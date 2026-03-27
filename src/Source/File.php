<?php

namespace Slendium\SlendiumStatic\Source;

use ArrayAccess;
use Exception;
use Override;
use SplFileInfo;

use Slendium\SlendiumStatic\Base\Site\Resource;
use Slendium\SlendiumStatic\Configs;
use Slendium\SlendiumStatic\Site\Resource as IResource;
use Slendium\SlendiumStatic\Source\Path;
use Slendium\SlendiumStatic\Source\PathInfo;

/**
 * @since 1.0
 * @phpstan-import-type ConfigsMap from Configs
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class File implements Copyable {

	/** @since 1.0 */
	public Path $path {
		get => $this->path ??= $this->init_path();
	}

	/** @internal @deprecated */
	public string $extension {
		get => PathInfo::getExtension($this->path);
	}

	/** @internal @deprecated */
	public string $normalizedName {
		get => PathInfo::getNormalizedName($this->path);
	}

	/** @internal @deprecated */
	public string $normalizedPath {
		get => \dirname($this->path).'/'.$this->normalizedName;
	}

	/** @internal @deprecated */
	public string $sourcePath {
		get => \trim("{$this->directory->sourcePath}{$this->name}", '/');
	}

	/** Keeps a cache of the results of `toResource()` */
	private Exception|IResource|null $resource = null;

	/** @since 1.0 */
	public function __construct(

		/** @since 1.0 */
		public readonly Directory $directory,

		/**
		 * @since 1.0
		 * @var non-empty-string
		 */
		public readonly string $name,

	) { }

	public function __debugInfo(): array {
		return [ 'path' => (string)$this->path ];
	}

	#[Override]
	public function copyTo(Path $target): Exception|true {
		return $this->directory->filesystem->copyFile($this->path, $target);
	}

	/**
	 * @internal
	 * @param ConfigsMap $configs
	 */
	public function toResource(ArrayAccess|array $configs): Exception|IResource {
		return $this->resource ??= Resource::fromFile($configs, $this);
	}

	/** @internal */
	public function getContents(): Exception|string {
		return $this->directory->filesystem->readFile($this->path);
	}

	private function init_path(): Path {
		$parts = [ $this->name ];

		$current = $this->directory;
		while ($current !== null) {
			$parts[] = $current->name;
			$current = $current->ancestor;
		}

		return new Path(\array_reverse($parts));
	}

}
