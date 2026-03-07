<?php

namespace Slendium\SlendiumStatic;

use Exception;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
interface Site {

	/**
	 * Contains any errors that occurred while generating the site.
	 * @since 1.0
	 * @var list<Pathed<Exception>>
	 */
	public array $errors { get; }

	/**
	 * Saves the website at the specified location.
	 * @since 1.0
	 * @return Exception|true `true` on success, an exception on failure
	 */
	public function save(string $path): Exception|true;

}
