<?php

namespace Slendium\SlendiumStatic\Base;

use ArrayAccess;
use Exception;

use Slendium\SlendiumStatic\Configs;
use Slendium\SlendiumStatic\Base\Site\Resource;
use Slendium\SlendiumStatic\Base\Source\Pathed;
use Slendium\SlendiumStatic\Content\SectionProvider;
use Slendium\SlendiumStatic\Site\Resource as IResource;
use Slendium\SlendiumStatic\Site\SiteException;
use Slendium\SlendiumStatic\Site\Uri;
use Slendium\SlendiumStatic\Source\Directory;
use Slendium\SlendiumStatic\Source\File;
use Slendium\SlendiumStatic\Source\Filesystem;
use Slendium\SlendiumStatic\Source\Pathed as IPathed;
use Slendium\SlendiumStatic\Source\Path;
use Slendium\SlendiumStatic\Source\PathInfo;

/**
 * @internal
 * @phpstan-import-type ConfigsMap from Configs
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class SourceConverter {

	private readonly Filesystem $filesystem;

	private readonly SectionProvider $baseSectionProvider;

	public function __construct(

		/** @var ConfigsMap */
		private readonly ArrayAccess|array $configs,

	) {
		$this->baseSectionProvider = Configs::getBaseSectionProvider($configs);
		$this->filesystem = Configs::getFilesystem($configs);
	}

	public function convert(Path $path): Site {
		$errors = [ ];
		$unique = [ ];

		$directory = new Directory($this->filesystem, $path);
		foreach ($this->convertDirectory($directory) as $path => $converted) {
			if ($converted instanceof IResource) {
				if (isset($unique[(string)$converted->uri])) {
					$unique[(string)$converted->uri][] = [ $path, $converted ];
				} else {
					$unique[(string)$converted->uri] = [ [ $path, $converted ] ];
				}
			} else {
				$errors[] = new Pathed($path, $converted);
			}
		}

		$resources = [ ];
		foreach ($unique as $uri => $entries) {
			if (\count($entries) > 1) {
				$paths = \array_map(static fn($entry) => $entry[0], $entries);
				foreach ($entries as $entry) {
					$errors[] = new Pathed($entry[0], SiteException::forAmbiguousResource($uri, $paths));
				}
			} else {
				$resources[] = $entries[0][1];
			}
		}
		return new Site($errors, $resources, $this->filesystem);
	}

	/**
	 * @param list<non-empty-string> $uriPath
	 * @return iterable<Path,IResource|Exception>
	 */
	public function convertDirectory(Directory $directory, array $uriPath = [ ]): iterable {
		$contents = $directory->getContents();
		if ($contents instanceof Exception) {
			yield $directory->path => $contents;
			return;
		}

		foreach ($contents as $i => $item) {
			if ($item instanceof File) {
				yield $item->path => $this->convertFile($item, $uriPath);
			} else {
				yield from $this->convertDirectory($item, [ ...$uriPath, $item->name ]);
			}
		}
	}

	/** @param list<non-empty-string> $uriPath */
	private function convertFile(File $file, array $uriPath): IResource|Exception {
		$name = PathInfo::getNormalizedName($file->path);
		$extension = PathInfo::getExtension($file->path);
		if ($name === '' || \mb_strlen($name) === \mb_strlen($extension) + 1) {
			return SiteException::forUnnamedResource($file->path);
		}

		$uri = new Uri([ ...$uriPath, $name ]);
		return match($extension) {
			'html', 'htm', 'md' => new Resource\Page($uri, $file, $this->baseSectionProvider, $this->configs),
			'css' => new Resource\Stylesheet($uri, $file),
			default => new Resource\BinaryResource($uri, $file)
		};
	}

}
