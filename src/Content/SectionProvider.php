<?php

namespace Slendium\SlendiumStatic\Content;

use Exception;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
interface SectionProvider {

	/**
	 * Returns a section by name if it exists in the source data.
	 *
	 * Can return an exception if an error occurred while instantiating the section.
	 *
	 * @since 1.0
	 */
	public function getSection(string $name): Exception|Section|null;

}
