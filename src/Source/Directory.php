<?php

namespace Slendium\SlendiumStatic\Source;

use Exception;
use NoDiscard;

use Slendium\SlendiumStatic\Common\Iteration;
use Slendium\SlendiumStatic\Site\Resource;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class Directory {

	public string $name {
		get => \basename($this->path);
	}

	/** Returns a path relative to the root source directory, ends with a '/' */
	public string $sourcePath {
		get => $this->get_sourcePath();
	}

	private readonly Filesystem $filesystem;

	/** @var Exception|list<File|self>|null */
	private Exception|array|null $contents = null;

	/** @param iterable<File> $files */
	private static function convertGroupedFilesToResource(iterable $files): Exception|Resource {
		$files = Iteration::toList($files);
		if (\count($files) < 1) {
			return new SourceException('Attempt to create resource from empty list of files');
		} else if (\count($files) > 1) {
			return SourceException::forAmbiguousResource($files);
		}

		if (\mb_strlen($files[0]->name) === \mb_strlen($files[0]->extension) + 1) {
			return SourceException::forUnnamedResource($files[0]);
		}

		return $files[0]->toResource();
	}

	public function __construct(

		private readonly string $path,

		public readonly ?self $ancestor = null,

		?Filesystem $filesystem = null,

	) {
		$this->filesystem = $filesystem ?? new RealFilesystem;
	}

	/** @return iterable<Resource|Exception> */
	#[NoDiscard]
	public function extractResources(): iterable {
		$files = [ ];
		foreach ($this->getFilesRecursively() as $file) {
			if ($file instanceof Exception) {
				yield $file;
			} else {
				$files[] = $file;
			}
		}

		yield from $files
			|> (fn($x) => Iteration::group($x, static fn($file) => $file->normalizedPath))
			|> (fn($x) => Iteration::map($x, self::convertGroupedFilesToResource(...)));
	}

	/** @return Exception|list<File|self> */
	public function getContents(): Exception|array {
		return $this->contents ??= $this->initializeContents();
	}

	/** @return iterable<Exception|File> */
	private function getFilesRecursively(): iterable {
		$contents = $this->getContents();
		if (!\is_array($contents)) {
			yield $contents;
			return;
		}

		yield from Iteration::flatten($contents, fn(File|self $item) => $item instanceof self
			? $item->getFilesRecursively()
			: [ $item ]
		);
	}

	/** @return Exception|list<File|self> */
	private function initializeContents(): Exception|array {
		$contents = $this->filesystem->scanDirectory($this->path);
		if ($contents instanceof Exception) {
			return $contents;
		}

		$resolved = [ ];
		foreach (Iteration::map($contents, $this->resolveSubPath(...)) as $resolvedItem) {
			if ($resolvedItem instanceof Exception) {
				return $resolvedItem;
			}
			$resolved[] = $resolvedItem;
		}
		return $resolved;
	}

	private function resolveSubPath(string $subPath): Exception|File|self {
		$path = "{$this->path}/$subPath";
		return match(true) {
			$this->filesystem->isDirectory($path) => new Directory($path, $this, $this->filesystem),
			$this->filesystem->isFile($path) => new File($path, directory: $this),
			default => new SourceException("Item `$subPath` requested from `{$this->path}` does not exist")
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
