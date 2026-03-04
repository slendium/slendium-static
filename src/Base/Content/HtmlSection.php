<?php

namespace Slendium\SlendiumStatic\Base\Content;

use Override;

use Slendium\SlendiumStatic\Content\Section;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class HtmlSection implements Section {

	/** @since 1.0 */
	public function __construct(private readonly string $html) { }

	#[Override]
	public function getHtml(): string {
		return $this->html;
	}

}
