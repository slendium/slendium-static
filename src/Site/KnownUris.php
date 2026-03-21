<?php

namespace Slendium\SlendiumStatic\Site;

/**
 * Contains definitions for {@see Uri}'s used by the static site generator.
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class KnownUris {

	private static Uri $staticDirectory; // /static

	private static Uri $mainStylesheet; // /static/styles.css

	/**
	 * Returns a URI to the /static directory.
	 * @since 1.0
	 */
	public static function StaticDirectory(): Uri {
		return self::$staticDirectory ??= new Uri([ 'static' ]);
	}

	/**
	 * Returns a URI to the main stylesheet (/static/styles.css) included with every page.
	 * @since 1.0
	 */
	public static function MainStylesheet(): Uri {
		return self::$mainStylesheet ??= new Uri([ 'static', 'styles.css' ]);
	}

	private function __construct() { }

}
