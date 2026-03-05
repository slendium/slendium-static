<?php

namespace Slendium\SlendiumStatic\Content;

/**
 * Constants for known section names.
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class SectionNames {

	/**
	 * Section that is dedicated to page metadata, typically part of the `<head>` element.
	 * @since 1.0
	 */
	const META = 'meta';

	/**
	 * Section that contains the contents to go before the main section.
	 * @since 1.0
	 */
	const HEADER = 'header';

	/**
	 * Section that contains the main contents of a page, such as an article.
	 * @since 1.0
	 */
	const MAIN = 'main';

	/**
	 * Section that contains the contents to go after the main section.
	 * @since 1.0
	 */
	const FOOTER = 'footer';

	private function __construct() { }

}
