<?php

namespace Slendium\SlendiumStatic\Base;

use Exception;
use Override;

use Slendium\SlendiumStatic\Pathed;
use Slendium\SlendiumStatic\Site as ISite;
use Slendium\SlendiumStatic\Base\Site\Resource;
use Slendium\SlendiumStatic\Site\KnownUris;
use Slendium\SlendiumStatic\Site\MapSearch;
use Slendium\SlendiumStatic\Site\Resource as IResource;
use Slendium\SlendiumStatic\Source\Copyable;
use Slendium\SlendiumStatic\Source\File;
use Slendium\SlendiumStatic\Source\Filesystem;
use Slendium\SlendiumStatic\Source\Path;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class Site implements ISite {

	#[Override]
	public readonly ISite\Map $map;

	/** @param iterable<IResource> $resources */
	public function __construct(

		/** @var list<Pathed<Exception>> */
		#[Override]
		public readonly array $errors,

		iterable $resources,

		private readonly Filesystem $filesystem,

	) {
		$this->map = new Site\Map($resources);
	}

	#[Override]
	public function save(string $path): Exception|true {
		$postProcessResult = $this->applyPostProcessing($this->map);
		if ($postProcessResult instanceof Exception) {
			return $postProcessResult;
		}

		foreach ($this->map as $resource) {
			$contents = $resource->generateContents();
			if (\is_string($contents)) {
				$this->filesystem->writeFile($path.(new Path($resource->uri->path)), $contents);
			} else if ($contents instanceof Copyable) {
				$contents->copyTo(Path::fromString($path.(new Path($resource->uri->path))));
			} else {
				return $contents;
			}
		}
		return true;
	}

	private function applyPostProcessing(Site\Map $map): Exception|true {
		$defaultStyles = new Resource\Stylesheet\DefaultStylesheet(KnownUris::MainStylesheet());
		if ($map->contains(KnownUris::MainStylesheet())) {
			$userStyles = MapSearch::getResourceOfType($map, KnownUris::MainStylesheet(), Resource\Stylesheet::class);
			if ($userStyles === null) {
				return new Exception('Unexpected resource type at URI `'.KnownUris::MainStylesheet().'`');
			}
			$defaultCss = $defaultStyles->generateContents();
			if ($defaultCss instanceof Exception) {
				return $defaultCss;
			}
			$userStyles->prepend($defaultCss);
		} else {
			$map->insert($defaultStyles);
		}
		return true;
	}

}
