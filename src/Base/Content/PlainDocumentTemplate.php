<?php

namespace Slendium\SlendiumStatic\Base\Content;

use ArrayAccess;
use Exception;
use Override;
use Dom\ChildNode;
use Dom\HTMLDocument;

use Slendium\Localization\Base\Locale;
use Slendium\Localization\Locale as ILocale;

use Slendium\SlendiumStatic\Configs;
use Slendium\SlendiumStatic\Common\HtmlParser;
use Slendium\SlendiumStatic\Content\DocumentTemplate;
use Slendium\SlendiumStatic\Content\SectionNames;
use Slendium\SlendiumStatic\Content\SectionProvider;
use Slendium\SlendiumStatic\Content\Summarizer;
use Slendium\SlendiumStatic\Content\TitleTemplate;

/**
 * @since 1.0
 * @phpstan-import-type ConfigsMap from Configs
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class PlainDocumentTemplate implements DocumentTemplate {

	private readonly ILocale $audienceLocale;

	private readonly Summarizer $summarizer;

	private readonly TitleTemplate $titleTemplate;

	/**
	 * @since 1.0
	 * @param ConfigsMap $configs
	 */
	public function __construct(ArrayAccess|array $configs) {
		$this->audienceLocale = Configs::getLocales($configs)[0]
			?? Locale::defaultLocale();
		$this->summarizer = Configs::getSummarizer($configs);
		$this->titleTemplate = Configs::getTitleTemplate($configs);
	}

	#[Override]
	public function createDocument(SectionProvider $sections): Exception|HTMLDocument {
		$html = $this->createRawHtml($sections);
		if ($html instanceof Exception) {
			return $html;
		}

		$document = HtmlParser::parse($html);
		if (\is_array($document)) {
			return ContentException::forLibXmlErrors($document);
		}

		return $this->applyDocumentPostProcessing($document);
	}

	private function applyDocumentPostProcessing(HTMLDocument $document): Exception|HTMLDocument {
		$summary = $this->summarizer->summarize($document);
		$titleEl = $document->querySelector('title');
		if ($titleEl === null) {
			return new ContentException('Internal error: expected a `TITLE` element');
		}

		if ($summary->description !== null) {
			$this->upsertMetaElement($document, 'description', $summary->description);
		}

		if ($summary->title === null) {
			return ContentException::forMissingTitle();
		}
		$titleEl->textContent = $this->titleTemplate->createTitle($summary->title);
		$this->upsertMetaElement($document, 'title', $summary->title);
		return $document;
	}

	private function upsertMetaElement(HTMLDocument $document, string $name, string $content): void {
		$existingEl = $document->querySelector("meta[name=$name]");
		if ($existingEl !== null) {
			$existingEl->setAttribute('content', $content);
		} else {
			$metaEl = $document->createElement('meta');
			$metaEl->setAttribute('name', $name);
			$metaEl->setAttribute('content', $content);
			$title = $document->querySelector('meta[name=generator]');
			if ($title instanceof ChildNode) {
				$title->after($metaEl);
			}
		}
	}

	private function createRawHtml(SectionProvider $sections): Exception|string {
		$header = $sections->getSection(SectionNames::HEADER);
		if ($header === null || $header instanceof Exception) {
			return $header
				?? ContentException::forMissingSection(SectionNames::HEADER);
		}

		$main = $sections->getSection(SectionNames::MAIN);
		if ($main === null || $main instanceof Exception) {
			return $main
				?? ContentException::forMissingSection(SectionNames::MAIN);
		}

		$footer = $sections->getSection(SectionNames::FOOTER);
		if ($footer === null || $footer instanceof Exception) {
			return $footer
				?? ContentException::forMissingSection(SectionNames::FOOTER);
		}

		$meta = $sections->getSection(SectionNames::META);
		if ($meta instanceof Exception) {
			return $meta;
		}

		return "<!DOCTYPE html>\n"
			."<html lang=\"{$this->audienceLocale->language}\">"
			.'<head>'
				.'<meta charset=utf-8><meta name=viewport content="width=device-width,initial-scale=1">'
				.'<title></title>'
				.'<meta name=generator content="github.com/slendium/slendium-static">'
				.($meta !== null ? $meta->getHtml() : '')
			.'</head>'
			.'<body>'
				.'<header>'.$header->getHtml().'</header>'
				.'<main>'.$main->getHtml().'</main>'
				.'<footer>'.$footer->getHtml().'</footer>'
			.'</body></html>';
	}

}
