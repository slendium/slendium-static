<?php

namespace Slendium\SlendiumStatic\Source;

use Exception;
use Override;

use Slendium\SlendiumStatic\Source\Path;
use Slendium\SlendiumStatic\Source\PathInfo;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class File {

	/** @since 1.0 */
	public Path $path {
		get => $this->directory->path->append($this->name);
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

	/** @since 1.0 */
	public function getContents(): Exception|string {
		return $this->directory->filesystem->readFile($this->path);
	}

}
