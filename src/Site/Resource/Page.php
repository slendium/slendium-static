<?php

namespace Slendium\SlendiumStatic\Site\Resource;

use ArrayAccess;
use Exception;
use Override;

use Slendium\SlendiumStatic\Configs;
use Slendium\SlendiumStatic\Common\Iteration;
use Slendium\SlendiumStatic\Site\Resource;
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
		$ancestorDirectory = $file->directory->ancestor;
		if ($ancestorDirectory === null) {
			return self::findRootAncestor($configs, $file->directory);
		}

		$contents = $ancestorDirectory->getContents();
		if (!\is_array($contents)) {
			return null;
		}

		return Iteration::filterType($contents, File::class)
			|> (fn($x) => Iteration::firstOrNull($x, fn($f) => $f->normalizedName === "{$file->directory->name}.html"))
			|> (fn($x) => $x?->toResource($configs))
			|> (fn($x) => $x instanceof self ? $x : null);
	}

	/** @param ConfigsMap $configs */
	private static function findRootAncestor(ArrayAccess|array $configs, Directory $root): ?self {
		$contents = $root->getContents();
		if (!\is_array($contents)) {
			return null;
		}

		return Iteration::filterType($contents, File::class)
			|> (fn($x) => Iteration::firstOrNull($x, fn($f) => $f->normalizedName === 'index.html'))
			|> (fn($x) => $x?->toResource($configs))
			|> (fn($x) => $x instanceof self ? $x : null);
	}

	/** @param ConfigsMap $configs */
	protected function __construct(

		ArrayAccess|array $configs,

		File $file,

		public readonly ?self $ancestor = null,

	) {
		parent::__construct($configs, $file);
	}

	#[Override]
	public function writeToFile(string $path): Exception|true {
		return new Exception('Not implemented');
	}

}
