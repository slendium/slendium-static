<?php

namespace Slendium\SlendiumStatic\Base\Source;

use Exception;
use Override;

use Slendium\SlendiumStatic\Source\Directory;
use Slendium\SlendiumStatic\Source\File;
use Slendium\SlendiumStatic\Source\Filesystem;
use Slendium\SlendiumStatic\Source\FilesystemException;
use Slendium\SlendiumStatic\Source\Path;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class NativeFilesystem implements Filesystem {

	#[Override]
	public function scanDirectory(Path $path): Exception|iterable {
		$contents = \scandir($path);
		/** @var list<non-empty-string>|false $contents */
		return $contents === false
			? FilesystemException::forScanDirectoryError($path)
			: $this->resolveScandirResults(new Directory($this, $path), $contents);
	}

	#[Override]
	public function readFile(Path $path): Exception|string {
		$contents = \file_get_contents($path);
		return $contents === false
			? FilesystemException::forReadFileError($path)
			: $contents;
	}

	#[Override]
	public function writeFile(Path $path, string $contents): ?Exception {
		$this->ensureDirExists($path);
		return \file_put_contents($path, $contents) === false
			? FilesystemException::forWriteFileError($path)
			: null;
	}

	#[Override]
	public function copyFile(Path $sourcePath, Path $targetPath): ?Exception {
		$this->ensureDirExists($targetPath);
		return \copy($sourcePath, $targetPath)
			? null
			: FilesystemException::forCopyFileError($sourcePath, $targetPath);
	}

	/**
	 * @param array<non-empty-string> $subPaths
	 * @return iterable<Directory|File>
	 */
	private function resolveScandirResults(Directory $current, array $subPaths): iterable {
		foreach ($subPaths as $subPath) {
			if ($subPath === '.' || $subPath === '..') {
				continue;
			}

			$absPath = $current->path->append($subPath);
			if (\is_dir($absPath)) {
				yield new Directory($this, $absPath);
			} else if (\is_file($absPath)) {
				yield new File($current, $subPath);
			}
		}
	}

	private function ensureDirExists(string $path): void {
		if (!\file_exists(\dirname($path))) {
			\mkdir(\dirname($path), recursive: true);
		}
	}

}
