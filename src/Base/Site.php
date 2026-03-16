<?php

namespace Slendium\SlendiumStatic\Base;

use Exception;
use Override;

use Slendium\SlendiumStatic\Pathed;
use Slendium\SlendiumStatic\Site as ISite;
use Slendium\SlendiumStatic\Base\Site\Resource;
use Slendium\SlendiumStatic\Source\File;
use Slendium\SlendiumStatic\Source\Filesystem;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class Site implements ISite {

	public function __construct(

		/** @var list<Pathed<Exception>> */
		#[Override]
		public readonly array $errors,

		/** @var list<Resource> */
		private readonly array $resources,

		private readonly Filesystem $filesystem,

	) { }

	#[Override]
	public function save(string $path): Exception|true {
		$path = \rtrim($path, '/');

		foreach ($this->resources as $resource) {
			$contents = $resource->generateContents();
			if (\is_string($contents)) {
				$this->filesystem->writeFile($path.$resource->uri, $contents);
			} else if ($contents instanceof File) {
				$this->filesystem->copyFile($contents->path, $path.$resource->uri);
			} else {
				return $contents;
			}
		}
		return true;
	}

}
