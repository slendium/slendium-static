<?php

namespace Slendium\SlendiumStatic\Site;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class UriInfo {

	/**
	 * Returns the last segment of the path, if any.
	 *
	 * Essentially the equivalent of the basename / filename in path terminology.
	 *
	 * @since 1.0
	 */
	public static function getTail(Uri $uri): ?string {
		return $uri->path[\count($uri->path) - 1]
			?? null;
	}

	/**
	 * Returns the sequence of "directories" of the given URI.
	 *
	 * For example, `/static/scripts/copyPaste.js` would yield `[ 'static', 'scripts' ]`.
	 *
	 * @since 1.0
	 * @return list<non-empty-string>
	 */
	public static function getDirnames(Uri $uri): array {
		return \count($uri->path) > 1
			? \array_slice($uri->path, 0, -1)
			: [ ];
	}

	private function __construct() { }

}
