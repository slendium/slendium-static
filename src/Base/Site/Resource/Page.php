<?php

namespace Slendium\SlendiumStatic\Base\Site\Resource;

use ArrayAccess;
use Exception;
use Override;
use Dom\HTMLDocument;

use Slendium\SlendiumStatic\Configs;
use Slendium\SlendiumStatic\Base\Content\CascadingSectionProvider;
use Slendium\SlendiumStatic\Base\Content\ContentException;
use Slendium\SlendiumStatic\Base\Content\HtmlFileSectionProvider;
use Slendium\SlendiumStatic\Base\Content\MarkdownFileSectionProvider;
use Slendium\SlendiumStatic\Base\Content\PlainDocumentTemplate;
use Slendium\SlendiumStatic\Base\Site\Resource;
use Slendium\SlendiumStatic\Common\Iteration;
use Slendium\SlendiumStatic\Content\SectionProvider;
use Slendium\SlendiumStatic\Site\Uri;
use Slendium\SlendiumStatic\Source\Directory;
use Slendium\SlendiumStatic\Source\File;
use Slendium\SlendiumStatic\Source\SourceException;

/**
 * @internal
 * @phpstan-import-type ConfigsMap from Configs
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class Page extends Resource {

	public function __construct(

		Uri $uri,

		File $file,

		private readonly SectionProvider $baseSectionProvider,

		/** @var ConfigsMap */
		private readonly ArrayAccess|array $configs,

	) {
		parent::__construct($uri, $file);
	}

	#[Override]
	public function generateContents(): Exception|string {
		$localSectionProvider = $this->getLocalSectionProvider();
		if ($localSectionProvider instanceof Exception) {
			return $localSectionProvider;
		}

		$sectionProvider = new CascadingSectionProvider([ $localSectionProvider, $this->baseSectionProvider ]);
		$document = new PlainDocumentTemplate($this->configs)
			->createDocument($sectionProvider);

		return $document instanceof HTMLDocument
			? $document->saveHtml()
			: $document;
	}

	private function getLocalSectionProvider(): Exception|SectionProvider {
		$fileContents = $this->file->getContents();
		if ($fileContents instanceof Exception) {
			return $fileContents;
		}

		return match($this->file->extension) {
			'html', 'htm' => new HtmlFileSectionProvider($fileContents),
			'md' => new MarkdownFileSectionProvider($fileContents),
			default => new ContentException("Unexpected page type `.{$this->file->extension}`")
		};
	}

}
