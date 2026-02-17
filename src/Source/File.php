<?php

namespace Slendium\SlendiumStatic\Source;

use SplFileInfo;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class File {

	public string $name {
		get => $this->fileInfo->getBasename();
	}

	public string $extension {
		get => $this->fileInfo->getExtension();
	}

	public string $normalizedName {
		get => $this->get_normalizedName();
	}

	public string $sourcePath {
		get => $this->get_sourcePath();
	}

	private readonly SplFileInfo $fileInfo;

	public function __construct(string $path, public readonly Directory $directory) {
		$this->fileInfo = new SplFileInfo($path);
	}

	private function get_normalizedName(): string {
		return match($this->extension) {
			'htm' => $this->fileInfo->getBasename('.htm').'.html',
			'md' => $this->fileInfo->getBasename('.md').'.html',
			'php' => $this->fileInfo->getBasename('.php').'.html',
			'jpeg' => $this->fileInfo->getBasename('.jpeg').'.jpg',
			default => $this->name,
		};
	}

	private function get_sourcePath(): string {
		$segments = [ ];
		$current = $this->directory;
		while ($current !== null) {
			$segments[] = $current->name;
			$current = $current->ancestor;
		}

		return [ ...\array_reverse($segments), $this->name ]
			|> (fn($x) => \implode('/', $x))
			|> (fn($x) => \trim($x, '/'));
	}

}
