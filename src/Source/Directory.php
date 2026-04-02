<?php

namespace Slendium\SlendiumStatic\Source;

use Exception;

use Slendium\SlendiumStatic\Source\Path;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class Directory {

	/** @internal @deprecated */
	public ?self $ancestor {
		get => \count($this->path->parts) > 0
			? new self($this->filesystem, new Path(\array_slice($this->path->parts, 0, -1)))
			: null;
	}

	/** @var non-empty-string */
	public string $name {
		get => \basename($this->path); // @phpstan-ignore return.type
	}

	/** Returns a path relative to the root source directory, ends with a '/' */
	public string $sourcePath {
		get => $this->get_sourcePath();
	}

	/** @since 1.0 */
	public function __construct(

		/** @since 1.0 */
		public readonly Filesystem $filesystem,

		/** @since 1.0 */
		public readonly Path $path,

	) { }

	/**
	 * @since 1.0
	 * @return Exception|iterable<File|self>
	 */
	public function getContents(): Exception|iterable {
		return $this->filesystem->scanDirectory($this->path);
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
