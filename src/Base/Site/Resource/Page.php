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

	private readonly ?SectionProvider $baseSectionProvider;

	/** @param ConfigsMap $configs */
	public static function fromFile(ArrayAccess|array $configs, File $file): Exception|self {
		if ($file->directory->sourcePath === '/' && $file->normalizedName === 'index.html') {
			return new self($configs, $file);
		}

		$ancestor = self::findAncestor($configs, $file);
		if ($ancestor === null && $file->normalizedName !== 'index.html') {
			return SourceException::forOrphanedResource($file);
		}
		return new self($configs, $file, $ancestor);
	}

	/** @param ConfigsMap $configs */
	private static function findAncestor(ArrayAccess|array $configs, File $file): ?self {
		$ancestorName = $file->directory->ancestor !== null
			? "{$file->directory->name}.html"
			: 'index.html';

		$ancestor = ($file->directory->ancestor ?? $file->directory)->getContents()
			|> (fn($x) => Iteration::map($x, fn($resolved) => $resolved->value))
			|> (fn($x) => Iteration::filterType($x, File::class))
			|> (fn($x) => Iteration::firstOrNull($x, fn($f) => $f->normalizedName === $ancestorName))
			|> (fn($x) => $x?->toResource($configs));

		return $ancestor instanceof self
			? $ancestor
			: null;
	}

	/** @param ConfigsMap $configs */
	protected function __construct(

		ArrayAccess|array $configs,

		File $file,

		public readonly ?self $ancestor = null,

	) {
		parent::__construct($configs, $file);
		$this->baseSectionProvider = Configs::getBaseSectionProvider($configs);
	}

	#[Override]
	public function generateContents(): Exception|string {
		if ($this->baseSectionProvider === null) {
			return new Exception('No base section provider configured');
		}

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
