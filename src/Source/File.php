<?php

namespace Slendium\SlendiumStatic\Source;

use ArrayAccess;
use Exception;
use SplFileInfo;

use Slendium\SlendiumStatic\Configs;
use Slendium\SlendiumStatic\Site\Resource;

/**
 * @internal
 * @phpstan-import-type ConfigsMap from Configs
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class File {

	public string $path {
		get => $this->fileInfo->getRealPath();
	}

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

	/** @param ConfigsMap $configs */
	public function toResource(ArrayAccess|array $configs): Exception|Resource {
		return $this->resource ??= Resource::fromFile($configs, $this);
	}

	public function getContents(): Exception|string {
		return $this->directory->filesystem->readFile($this->path);
	}

	private function get_normalizedName(): string {
		return match($this->extension) {
			'htm' => $this->fileInfo->getBasename('.htm').'.html',
			'md' => $this->fileInfo->getBasename('.md').'.html',
			'jpeg' => $this->fileInfo->getBasename('.jpeg').'.jpg',
			default => $this->name,
		};
	}

}
