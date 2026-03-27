<?php

namespace Slendium\SlendiumStatic\Source;

use ArrayAccess;
use Exception;
use NoDiscard;

use Slendium\SlendiumStatic\Configs;
use Slendium\SlendiumStatic\Base\Source\Pathed;
use Slendium\SlendiumStatic\Common\Iteration;
use Slendium\SlendiumStatic\Site\Resource;
use Slendium\SlendiumStatic\Source\Path;

/**
 * @internal
 * @phpstan-import-type ConfigsMap from Configs
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class Directory {

	/** @var non-empty-string */
	public string $name {
		get => \basename($this->path); // @phpstan-ignore return.type
	}

	/** Returns a path relative to the root source directory, ends with a '/' */
	public string $sourcePath {
		get => $this->get_sourcePath();
	}

	public readonly Filesystem $filesystem;

	/** @var ?list<Pathed<Exception|File|self>> */
	private ?array $contents = null;

	/**
	 * @param iterable<string,File> $files Map of paths to files (the paths are for logging consistency)
	 * @param ConfigsMap $configs
	 * @return iterable<string,Exception|Resource>
	 */
	private static function convertFilesToResources(iterable $files, ArrayAccess|array $configs): iterable {
		/** @var array<string,array{0:string,1:?File}> */
		$unique = [ ];

		foreach ($files as $path => $file) {
			if (\mb_strlen($file->name) === \mb_strlen($file->extension) + 1) {
				yield $path => SourceException::forUnnamedResource($file);
			} else if (isset($unique[$file->normalizedPath])) {
				yield $unique[$file->normalizedPath][0] => SourceException::forAmbiguousResource($file);
				// do not convert the original either, no way to tell which one is preferred
				$unique[$file->normalizedPath][1] = null;
				yield $path => SourceException::forAmbiguousResource($file);
			} else {
				$unique[$file->normalizedPath] = [ $path, $file ];
			}
		}

		foreach ($unique as $entry) {
			if ($entry[1] !== null) {
				yield $entry[0] => $entry[1]->toResource($configs);
			}
		}
	}

	public function __construct(

		private readonly string $path,

		public readonly ?self $ancestor = null,

		?Filesystem $filesystem = null,

	) {
		$this->filesystem = $filesystem ?? new RealFilesystem;
	}

	/**
	 * @param ConfigsMap $configs
	 * @return iterable<string,Resource|Exception> A path mapped to either a resource or an error
	 */
	#[NoDiscard]
	public function extractResources(ArrayAccess|array $configs): iterable {
		$files = [ ];
		foreach ($this->getFilesRecursively() as $path => $errorOrFile) {
			if ($errorOrFile instanceof Exception) {
				yield $path => $errorOrFile;
			} else {
				$files[(string)$path] = $errorOrFile;
			}
		}
		yield from self::convertFilesToResources($files, $configs);
	}

	/** @return list<Pathed<Exception|File|self>> */
	public function getContents(): array {
		return $this->contents ??= $this->initializeContents();
	}

	/** @return iterable<string,Exception|File> A path mapped to either a file or an error */
	private function getFilesRecursively(): iterable {
		foreach ($this->getContents() as $resolved) {
			if ($resolved->value instanceof self) {
				yield from $resolved->value->getFilesRecursively();
			} else {
				yield $resolved->path => $resolved->value;
			}
		}
	}

	/** @return list<Pathed<Exception|File|self>> */
	private function initializeContents(): array {
		$contents = $this->filesystem->scanDirectory($this->path);
		if ($contents instanceof Exception) {
			return [ new Pathed(Path::fromString($this->path), $contents) ]; // @phpstan-ignore return.type (Pathed<Exception> is technically not covariant but it doesnt matter here)
		}

		return $contents
			|> (fn($x) => Iteration::filter($x, static fn($path) => $path !== '.' && $path !== '..'))
			|> (fn($x) => Iteration::map($x, $this->resolveSubPath(...)))
			|> Iteration::toList(...);
	}

	/** @return Pathed<Exception|File|self> */
	private function resolveSubPath(string $subPath): Pathed {
		$path = "{$this->path}/$subPath";
		return new Pathed(Path::fromString($path), $this->resolvePath($path));
	}

	private function resolvePath(string $path): Exception|File|self {
		return match(true) {
			$this->filesystem->isDirectory($path) => new Directory($path, $this, $this->filesystem),
			$this->filesystem->isFile($path) => new File($this, \basename($path)), // @phpstan-ignore argument.type
			default => new SourceException("Item `$path` requested from `{$this->path}` does not exist")
		};
	}

	private function get_sourcePath(): string {
		$segments = [ ];
		$current = $this;
		while ($current->ancestor !== null) {
			$segments[] = $current->name;
			$current = $current->ancestor;
		}
		return \implode('/', \array_reverse($segments))
			|> (fn($x) => \trim($x, '/'))
			|> (fn($x) => "$x/");
	}

}
