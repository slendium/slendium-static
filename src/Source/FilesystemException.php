<?php

namespace Slendium\SlendiumStatic\Source;

use Exception;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class FilesystemException extends Exception {

	/** @since 1.0 */
	public static function forScanDirectoryError(string $path): Exception {
		return new self("An error occurred while scanning directory `$path`");
	}

	/** @since 1.0 */
	public static function forReadFileError(string $path): Exception {
		return new self("An error occurred reading from file `$path`");
	}

	/** @since 1.0 */
	public static function forWriteFileError(string $path): Exception {
		return new self("An error occurred writing to file `$path`");
	}

	/** @since 1.0 */
	public static function forCopyFileError(string $sourcePath, string $targetPath): Exception {
		return new self("An error occurred copying `$sourcePath` to `$targetPath`");
	}

}
