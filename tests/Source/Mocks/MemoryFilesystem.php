<?php

namespace Slendium\SlendiumStaticTests\Source\Mocks;

use Exception;
use Override;

use Slendium\SlendiumStatic\Common\Iteration;
use Slendium\SlendiumStatic\Source\Directory;
use Slendium\SlendiumStatic\Source\File;
use Slendium\SlendiumStatic\Source\Filesystem;
use Slendium\SlendiumStatic\Source\FilesystemException;
use Slendium\SlendiumStatic\Source\Path;

/** Forces a `/` directory separator. */
class MemoryFilesystem implements Filesystem {

	/**
	 * @param array<string,mixed> $structure A recursive structure where each key is a path and each
	 *   value is either another recursive structure or a string to indicate a file
	 */
	public function __construct(private array $structure) { }

	#[Override]
	public function scanDirectory(Path $path): Exception|iterable {
		$node = $this->findNode($path);
		if (!\is_array($node)) {
			return new FilesystemException("Directory `$path` not found");
		}

		$directory = new Directory($this, $path);
		return Iteration::map($node, fn($value, $key) => match(true) {
			\is_string($value) => new File($directory, $key),
			\is_array($value) => new Directory($this, $path->append($key)),
			default => throw new Exception('Unexpected value type in internal structure')
		}) |> Iteration::toList(...);
	}

	#[Override]
	public function readFile(Path $path): Exception|string {
		$file = $this->findNode($path);
		return \is_string($file)
			? $file
			: new Exception('File not found');
	}

	#[Override]
	public function writeFile(Path $path, string $contents): ?Exception {
		return new Exception('Memory filesystem is not writeable');
	}

	#[Override]
	public function copyFile(Path $sourcePath, Path $targetPath): ?Exception {
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

	/** @return array<non-empty-string,mixed>|string|null An array represents a directory, a string a file, `null` not found */
	private function findNode(Path $path): array|string|null {
		$current = $this->structure;
		foreach ($path->parts as $part) {
			$current = \is_array($current) && isset($current[$part])
				? $current[$part]
				: null;
		}
		return match(true) {
			\is_array($current) => self::ensureDirectoryArray($current),
			\is_string($current) => $current,
			default => null
		};
	}

	/**
	 * @param array<mixed> $array
	 * @return array<non-empty-string,mixed>
	 */
	private static function ensureDirectoryArray(array $array): array {
		$out = [ ];
		foreach ($array as $k => $v) {
			if ($k !== '') {
				$out["$k"] = $v;
			}
		}
		return $out;
	}

}
