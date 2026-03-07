<?php

namespace Slendium\SlendiumStatic\Base\Content;

use Exception;
use Override;
use Dom\HTMLDocument;
use Dom\HTMLElement;
use Dom\Text as DomText;

use Slendium\SlendiumStatic\Base\Content\ContentException;
use Slendium\SlendiumStatic\Base\Content\HtmlSection;
use Slendium\SlendiumStatic\Common\HtmlParser;
use Slendium\SlendiumStatic\Common\Iteration;
use Slendium\SlendiumStatic\Content\Consumable;
use Slendium\SlendiumStatic\Content\Section;
use Slendium\SlendiumStatic\Content\SectionNames;
use Slendium\SlendiumStatic\Content\SectionProvider;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class HtmlFileSectionProvider implements SectionProvider {

	/** @var Exception|array<string,Exception|Section> */
	private Exception|array $sections;

	/** @return Exception|array<string,Exception|Section> */
	private static function parseHtmlFileIntoSections(string $html): Exception|array {
		$doc = HtmlParser::parse("<!DOCTYPE html>\n<html><body>$html</body></html>");
		if (\is_array($doc)) {
			return ContentException::forLibXmlErrors($doc);
		}

		if ($doc->querySelectorAll('sls-section sls-section')->length > 0) {
			return ContentException::forNestedSectionDefinitions();
		}

		return self::parseDocumentIntoSections($doc);
	}

	/** @return Exception|array<string,Exception|Section> */
	private static function parseDocumentIntoSections(HTMLDocument $doc): Exception|array {
		$sections = [ ];
		foreach ($doc->querySelectorAll('sls-section') as $sectionEl) {
			$name = $sectionEl->getAttribute('name');
			if ($name === null || $name === '') {
				return ContentException::forSectionWithoutName();
			}
			if (isset($sections[$name])) {
				return ContentException::forDuplicateSection($name);
			}
			if ($sectionEl instanceof HTMLElement) {
				$sections[$name] = new HtmlSection($sectionEl->innerHTML);
				self::removeSection($sectionEl);
			}
		}

		if (($mainSection = self::extractImplicitMainSection($doc)) !== null) {
			if (isset($sections[SectionNames::MAIN])) {
				return ContentException::forDuplicateSection(SectionNames::MAIN);
			}
			$sections[SectionNames::MAIN] = $mainSection;
		}
		return $sections;
	}

	private static function extractImplicitMainSection(HTMLDocument $doc): ?Section {
		$implicitMain = \trim($doc->querySelector('body')->innerHTML ?? '');
		return $implicitMain !== ''
			? new HtmlSection($implicitMain)
			: null;
	}

	private static function removeSection(HTMLElement $el): void {
		$before = $el->previousSibling;
		if ($before instanceof DomText && $before->textContent !== null) {
			$before->textContent = \rtrim($before->textContent);
		}
		$after = $el->nextSibling;
		if ($after instanceof DomText && $after->textContent !== null) {
			$after->textContent = \ltrim($after->textContent);
		}
		$el->remove();
	}

	public function __construct(string $html) {
		$this->sections = self::parseHtmlFileIntoSections($html);
	}

	#[Override]
	public final function getSection(string $name): Exception|Section|null {
		return \is_array($this->sections)
			? ($this->sections[$name] ?? null)
			: $this->sections;
	}

}
