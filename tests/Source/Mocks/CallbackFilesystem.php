<?php

namespace Slendium\SlendiumStaticTests\Source\Mocks;

use Closure;
use Exception;
use Override;

use Slendium\SlendiumStatic\Source\Filesystem;

class CallbackFilesystem implements Filesystem {

	private const ALWAYS_FALSE = static function ($path) {
		return false;
	};

	private const SCAN_DIRECTORY_EMPTY = static function ($path) {
		return [ ];
	};

	private const READ_EXCEPTION = static function ($path) {
		return new Exception('Not implemented');
	};

	private const WRITE_EXCEPTION = static function ($path, $contents) {
		return new Exception('Not implemented');
	};

	public function __construct(

		/** @var Closure(string):bool */
		private readonly Closure $isDirectory = self::ALWAYS_FALSE,

		/** @var Closure(string):bool */
		private readonly Closure $isFile = self::ALWAYS_FALSE,

		/** @var Closure(string):Exception|iterable<string> */
		private readonly Closure $scanDirectory = self::SCAN_DIRECTORY_EMPTY,

		/** @var Closure(string):Exception|string */
		private readonly Closure $readFile = self::READ_EXCEPTION,

		/** @var Closure(string,string):Exception|true */
		private readonly Closure $writeFile = self::WRITE_EXCEPTION,

	) { }

	#[Override]
	public function isFile(string $path): bool {
		return ($this->isFile)($path);
	}

	#[Override]
	public function isDirectory(string $path): bool {
		return ($this->isDirectory)($path);
	}

	/** @return Exception|iterable<string> */
	#[Override]
	public function scanDirectory(string $path): Exception|iterable {
		return ($this->scanDirectory)($path);
	}

	#[Override]
	public function readFile(string $path): Exception|string {
		return ($this->readFile)($path);
	}

	#[Override]
	public function writeFile(string $path, string $contents): Exception|true {
		return ($this->writeFile)($path, $contents);
	}

}
