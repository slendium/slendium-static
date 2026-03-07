<?php

namespace Slendium\SlendiumStatic\Source;

use Exception;
use Override;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class RealFilesystem implements Filesystem {

	#[Override]
	public function isFile(string $path): bool {
		return \is_file($path);
	}

	#[Override]
	public function isDirectory(string $path): bool {
		return \is_dir($path);
	}

	#[Override]
	public function scanDirectory(string $path): Exception|iterable {
		$contents = \scandir($path);
		return $contents === false
			? new FilesystemException("An error occurred when trying to scan directory `$path`")
			: $contents;
	}

	#[Override]
	public function readFile(string $path): Exception|string {
		$contents = \file_get_contents($path);
		return $contents === false
			? new FilesystemException("An error occurred reading from file `$path`")
			: $contents;
	}

	#[Override]
	public function writeFile(string $path, string $contents): Exception|true {
		$this->ensureDirExists($path);
		return \file_put_contents($path, $contents) === false
			? new FilesystemException("An error occurred writing to file `$path`")
			: true;
	}

	#[Override]
	public function copyFile(string $sourcePath, string $targetPath): Exception|true {
		$this->ensureDirExists($targetPath);
		return \copy($sourcePath, $targetPath)
			? true
			: new FilesystemException("An error occurred copying `$sourcePath` to `$targetPath`");
	}

	private function ensureDirExists(string $path): void {
		if (!\file_exists(\dirname($path))) {
			\mkdir(\dirname($path), recursive: true);
		}
	}

}
