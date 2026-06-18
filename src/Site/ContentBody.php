<?php

namespace Slendium\SlendiumStatic\Site;

/**
 * Succesfully generated content produced by a site resource.
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
interface ContentBody {

	/** @since 1.0 */
	public function getBytes(): string;

}
