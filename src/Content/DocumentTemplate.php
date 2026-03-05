<?php

namespace Slendium\SlendiumStatic\Content;

use Exception;

use Dom\HTMLDocument;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
interface DocumentTemplate {

	/** @since 1.0 */
	public function createDocument(SectionProvider $sections): Exception|HTMLDocument;

}
