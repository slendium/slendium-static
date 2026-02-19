<?php

namespace Slendium\SlendiumStatic\Site\Resource;

use Exception;

use Slendium\SlendiumStatic\Common\Iteration;
use Slendium\SlendiumStatic\Site\Resource;
use Slendium\SlendiumStatic\Source\Directory;
use Slendium\SlendiumStatic\Source\File;
use Slendium\SlendiumStatic\Source\SourceException;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
abstract class Page extends Resource {

	public static function fromFile(File $file): Exception|self {
		if ($file->directory->sourcePath === '/' && $file->normalizedName === 'index.html') {
			return new HtmlPage($file);
		}

		$ancestor = self::findAncestor($file);
		if ($ancestor === null && $file->normalizedName !== 'index.html') {
			return SourceException::forOrphanedResource($file);
		}

		return match($file->extension) {
			'html', 'htm' => new HtmlPage($file, $ancestor),
			default => new SourceException('Attempt to create page from unsupported file type')
		};
	}

	private static function findAncestor(File $file): ?self {
		$ancestorDirectory = $file->directory->ancestor;
		if ($ancestorDirectory === null) {
			return self::findRootAncestor($file->directory);
		}

		$contents = $ancestorDirectory->getContents();
		if (!\is_array($contents)) {
			return null;
		}

		return Iteration::filterType($contents, File::class)
			|> (fn($x) => Iteration::firstOrNull($x, fn($f) => $f->normalizedName === "{$file->directory->name}.html"))
			|> (fn($x) => $x?->toResource())
			|> (fn($x) => $x instanceof self ? $x : null);
	}

	private static function findRootAncestor(Directory $root): ?self {
		$contents = $root->getContents();
		if (!\is_array($contents)) {
			return null;
		}

		return Iteration::filterType($contents, File::class)
			|> (fn($x) => Iteration::firstOrNull($x, fn($f) => $f->normalizedName === 'index.html'))
			|> (fn($x) => $x?->toResource())
			|> (fn($x) => $x instanceof self ? $x : null);
	}

	protected function __construct(

		File $file,

		public readonly ?self $ancestor = null,

	) {
		parent::__construct($file);
	}

}
