<?php

namespace Slendium\SlendiumStaticTests\Source\Mocks;

use Closure;
use Exception;
use Override;

use Slendium\SlendiumStatic\Source\Directory;
use Slendium\SlendiumStatic\Source\File;
use Slendium\SlendiumStatic\Source\Filesystem;
use Slendium\SlendiumStatic\Source\Path;

class CallbackFilesystem implements Filesystem {

	private const SCAN_DIRECTORY_EMPTY = static function ($path) {
		return [ ];
	};

	private const READ_EXCEPTION = static function ($path) {
		return new Exception('Not implemented');
	};

	private const WRITE_EXCEPTION = static function ($path, $contents) {
		return new Exception('Not implemented');
	};

	private const COPY_EXCEPTION = static function ($from, $to) {
		return new Exception('Not implemented');
	};

	public function __construct(

		/** @var Closure(Path):(Exception|iterable<Directory|File>) */
		private readonly Closure $scanDirectory = self::SCAN_DIRECTORY_EMPTY,

		/** @var Closure(Path):(Exception|string) */
		private readonly Closure $readFile = self::READ_EXCEPTION,

		/** @var Closure(Path,string):?Exception */
		private readonly Closure $writeFile = self::WRITE_EXCEPTION,

		/** @var Closure(Path,Path):?Exception */
		private readonly Closure $copyFile = self::COPY_EXCEPTION,

	) { }

	/** @return Exception|iterable<Directory|File> */
	#[Override]
	public function scanDirectory(Path $path): Exception|iterable {
		return ($this->scanDirectory)($path);
	}

	#[Override]
	public function readFile(Path $path): Exception|string {
		return ($this->readFile)($path);
	}

	#[Override]
	public function writeFile(Path $path, string $contents): ?Exception {
		return ($this->writeFile)($path, $contents);
	}

	#[Override]
	public function copyFile(Path $sourcePath, Path $targetPath): ?Exception {
		return ($this->copyFile)($sourcePath, $targetPath);
	}

}
