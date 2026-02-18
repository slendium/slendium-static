<?php

namespace Slendium\SlendiumStatic\Source;

use Exception;

/**
 * Interface for mocking or virtualizing the filesystem.
 *
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
interface Filesystem {

	public function isFile(string $path): bool;

	public function isDirectory(string $path): bool;

	/** @return Exception|iterable<string> An error or a list of file/directory names */
	public function scanDirectory(string $path): Exception|iterable;

	public function readFile(string $path): Exception|string;

	public function writeFile(string $path, string $contents): Exception|true;

}
