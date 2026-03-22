<?php

namespace Slendium\SlendiumStatic\Base\Site;

use ArrayAccess;
use Exception;
use Override;

use Slendium\SlendiumStatic\Configs;
use Slendium\SlendiumStatic\Site\Resource as IResource;
use Slendium\SlendiumStatic\Site\Uri;
use Slendium\SlendiumStatic\Source\Copyable;
use Slendium\SlendiumStatic\Source\File;

/**
 * @internal
 * @phpstan-import-type ConfigsMap from Configs
 * @author C. Fahner
 * @copyright Slendium 2026
 */
abstract class Resource implements IResource {

	/**
	 * @internal
	 * @param ConfigsMap $configs
	 */
	public static function fromFile(ArrayAccess|array $configs, File $file): Exception|IResource {
		return match($file->extension) {
			'html', 'htm', 'md' => Resource\Page::fromFile($configs, $file),
			'css' => Resource\Stylesheet::fromFile($file),
			default => new Resource\BinaryResource($configs, $file)
		};
	}

	#[Override]
	public Uri $uri {
		get => $this->get_uri();
	}

	protected function __construct(

		/** @var ConfigsMap */
		public readonly ArrayAccess|array $configs,

		protected readonly File $file,

	) { }

	#[Override]
	public abstract function generateContents(): Copyable|Exception|string;

	private function get_uri(): Uri {
		$current = $this->file->directory;
		$path = [ $this->file->normalizedName ];
		while ($current->ancestor !== null) {
			$current = $current->ancestor;
			$path[] = $current->name;
		}

		return new Uri(\array_reverse($path)); // @phpstan-ignore argument.type (wont contain empty strings)
	}

}
