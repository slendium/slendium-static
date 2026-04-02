<?php

namespace Slendium\SlendiumStatic\Site;

use Exception;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class SiteException extends Exception {

	/** @param list<string> $paths */
	public static function forAmbiguousResource(string $uri, array $paths): Exception {
		return new self("Ambiguous resource, `$uri` can be created from multiple files: ".\implode(', ', $paths));
	}

	public static function forUnnamedResource(string $path): Exception {
		return new self("Resource at `$path` should have a non-empty name");
	}

}
