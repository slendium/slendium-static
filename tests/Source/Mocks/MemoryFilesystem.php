<?php

namespace Slendium\SlendiumStaticTests\Source\Mocks;

use Exception;
use Override;

use Slendium\SlendiumStatic\Common\Iteration;
use Slendium\SlendiumStatic\Source\Filesystem;

/** Forces a `/` directory separator. */
class MemoryFilesystem implements Filesystem {

	/**
	 * @param array<string,mixed> $structure A recursive structure where each key is a path and each
	 *   value is either another recursive structure or a string to indicate a file
	 */
	public function __construct(private array $structure) { }

	#[Override]
	public function isDirectory(string $path): bool {
		return \is_array($this->findNode($path));
	}

	#[Override]
	public function isFile(string $path): bool {
		return \is_string($this->findNode($path));
	}

	#[Override]
	public function scanDirectory(string $path): Exception|iterable {
		$dir = $this->findNode($path);
		if (!\is_array($dir)) {
			return new Exception("Directory `$path` not found");
		}

		return \array_keys($dir);
	}

	#[Override]
	public function readFile(string $path): Exception|string {
		$file = $this->findNode($path);
		return \is_string($file)
			? $file
			: new Exception('File not found');
	}

	#[Override]
	public function writeFile(string $path, string $contents): Exception|true {
		return new Exception('Memory filesystem is not writeable');
	}

	#[Override]
	public function copyFile(string $sourcePath, string $targetPath): Exception|true {
		return new Exception('Memory filesystem does not support copying');
	}

	/** @param list<non-empty-string> $directories */
	public function addFileFromRoot(array $directories, string $filename, string $contents): void {
		$root = $this->structure;
		$current =& $root;
		foreach ($directories as $name) {
			if (!isset($current[$name]) || !\is_array($current[$name])) {
				$current[$name] = [ ];
			}
			$current =& $current[$name];
		}
		$current[$filename] = $contents;
		$this->structure = $root;
	}

	/** @return array<mixed>|string|null An array represents a directory, a string a file, `null` not found */
	private function findNode(string $path): array|string|null {
		$path = \trim($path, '/');
		if ($path === '') {
			return $this->structure;
		}

		$parts = \mb_split('\\/', $path); /** @var list<string> $parts */
		$current = $this->structure;
		foreach ($parts as $part) {
			$current = \is_array($current) && isset($current[$part])
				? $current[$part]
				: null;
		}
		return \is_array($current) || \is_string($current)
			? $current
			: null;
	}

}
