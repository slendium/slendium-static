<?php

namespace Slendium\SlendiumStatic\Source;

use Exception;

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

	/** @since 1.0 */
	public string $extension {
		get => PathInfo::getExtension($this->path);
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
