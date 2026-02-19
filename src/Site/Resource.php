<?php

namespace Slendium\SlendiumStatic\Site;

use Exception;

use Slendium\SlendiumStatic\Source\File;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
abstract class Resource {

	public static function fromFile(File $file): Exception|self {
		return match($file->extension) {
			'html', 'htm', 'md', 'php' => Resource\Page::fromFile($file),
			default => new Resource\BinaryResource($file)
		};
	}

	public Uri $uri {
		get => $this->get_uri();
	}

	protected function __construct(private readonly File $file) { }

	public abstract function writeToFile(string $path): Exception|true;

	private function get_uri(): Uri {
		$current = $this->file->directory;
		$path = [ $this->file->normalizedName, $current->name ];
		while ($current->ancestor !== null) {
			$current = $current->ancestor;
			$path[] = $current->name;
		}

		return new Uri(\array_reverse($path));
	}

}
