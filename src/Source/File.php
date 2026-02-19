<?php

namespace Slendium\SlendiumStatic\Source;

use Exception;
use SplFileInfo;

use Slendium\SlendiumStatic\Site\Resource;

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

	public string $normalizedPath {
		get => $this->fileInfo->getPath().$this->normalizedName;
	}

	public string $sourcePath {
		get => \trim("{$this->directory->sourcePath}{$this->name}", '/');
	}

	private readonly SplFileInfo $fileInfo;

	/** Keeps a cache of the results of `toResource()` */
	private Exception|Resource|null $resource = null;

	public function __construct(string $path, public readonly Directory $directory) {
		$this->fileInfo = new SplFileInfo($path);
	}

	public function toResource(): Exception|Resource {
		return $this->resource ??= Resource::fromFile($this);
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

}
