<?php

namespace Slendium\SlendiumStatic\Content;

/**
 * Converts the base title of a document into the full website title.
 *
 * For example, `"Homepage"` could be converted to `"Homepage - MyCompany, Amsterdam"`.
 *
 * @since 1.0
 * @see \Slendium\SlendiumStatic\Base\Content\ClosureTitleTemplate	For a basic implementation
 * @author C. Fahner
 * @copyright Slendium 2026
 */
interface TitleTemplate {

	/** @since 1.0 */
	public function createTitle(string $baseTitle): string;

}
