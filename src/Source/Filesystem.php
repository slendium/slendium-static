<?php

namespace Slendium\SlendiumStatic\Source;

use Exception;

/**
 * Interface for mocking or virtualizing the filesystem.
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
interface Filesystem {

	/**
	 * Scans the directory and returns handles to its contents.
	 *
	 * This method doesn't return the current directory nor the one above it.
	 *
	 * An error is returned if the given path is not a directory or not readable.
	 *
	 * @since 1.0
	 * @return Exception|iterable<Directory|File> An error or the directory contents
	 */
	public function scanDirectory(Path $path): Exception|iterable;

	/** @since 1.0 */
	public function readFile(Path $path): Exception|string;

	/** @since 1.0 */
	public function writeFile(Path $path, string $contents): ?Exception;

	/** @since 1.0 */
	public function copyFile(Path $sourcePath, Path $targetPath): ?Exception;

}
