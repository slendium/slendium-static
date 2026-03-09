<?php

namespace Slendium\SlendiumStatic\Site;

use ArrayAccess;
use Exception;

use Slendium\SlendiumStatic\Configs;
use Slendium\SlendiumStatic\Source\File;

/**
 * @internal
 * @phpstan-import-type ConfigsMap from Configs
 * @author C. Fahner
 * @copyright Slendium 2026
 */
abstract class Resource {

	/** @param ConfigsMap $configs */
	public static function fromFile(ArrayAccess|array $configs, File $file): Exception|self {
		return match($file->extension) {
			'html', 'htm', 'md' => Resource\Page::fromFile($configs, $file),
			default => new Resource\BinaryResource($configs, $file)
		};
	}

	public Uri $uri {
		get => $this->get_uri();
	}

	protected function __construct(

		/** @var ConfigsMap */
		public readonly ArrayAccess|array $configs,

		protected readonly File $file,

	) { }

	public abstract function generateContents(): File|Exception|string;

	private function get_uri(): Uri {
		$current = $this->file->directory;
		$path = [ $this->file->normalizedName ];
		while ($current->ancestor !== null) {
			$current = $current->ancestor;
			$path[] = $current->name;
		}

		return new Uri(\array_reverse($path));
	}

}
