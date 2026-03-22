<?php

namespace Slendium\SlendiumStatic\Base\Site\Resource\Stylesheet;

use Exception;
use Override;

use Slendium\SlendiumStatic\Site\KnownUris;
use Slendium\SlendiumStatic\Site\Resource;
use Slendium\SlendiumStatic\Site\Uri;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class DefaultStylesheet implements Resource {

	public function __construct(

		#[Override]
		public readonly Uri $uri,

	) { }

	/** @return Exception|non-empty-string */
	#[Override]
	public function generateContents(): Exception|string {
		$dir = __DIR__.\DIRECTORY_SEPARATOR.'DefaultStylesheet'.\DIRECTORY_SEPARATOR;
		$scanned = \scandir($dir);
		if ($scanned === false) {
			return new Exception('Could not scan directory that contains the default stylesheet');
		}
		$defaultStyleParts = \array_filter($scanned, static fn($v) => $v !== '.' && $v !== '..');
		\natsort($defaultStyleParts);

		$contents = '';
		foreach ($defaultStyleParts as $path) {
			$fileContents = \file_get_contents($dir.$path);
			if ($fileContents === false) {
				return new Exception("Could not open file `$path`");
			}
			$contents .= $fileContents;
		}
		return $contents === ''
			? new Exception('Unexpected empty default stylesheet')
			: $contents;
	}

}
