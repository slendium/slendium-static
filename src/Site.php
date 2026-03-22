<?php

namespace Slendium\SlendiumStatic;

use Exception;

/**
 * A completed collection of site {@see Resource}'s that can be saved to a directory.
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
interface Site {

	/**
	 * Contains all site resources as an editable map to allow post-processing.
	 * @since 1.0
	 */
	public Site\Map $map { get; }

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
