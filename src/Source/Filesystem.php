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

	/** @since 1.0 */
	public function isFile(string $path): bool;

	/** @since 1.0 */
	public function isDirectory(string $path): bool;

	/**
	 * @since 1.0
	 * @return Exception|iterable<string> An error or a list of file/directory names
	 */
	public function scanDirectory(string $path): Exception|iterable;

	/** @since 1.0 */
	public function readFile(string $path): Exception|string;

	/** @since 1.0 */
	public function writeFile(string $path, string $contents): Exception|true;

	/** @since 1.0 */
	public function copyFile(string $sourcePath, string $targetPath): Exception|true;

}
