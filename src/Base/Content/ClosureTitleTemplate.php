<?php

namespace Slendium\SlendiumStatic\Base\Content;

use Closure;
use Override;

use Slendium\SlendiumStatic\Content\TitleTemplate;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class ClosureTitleTemplate implements TitleTemplate {

	/** @since 1.0 */
	public function __construct(

		/** @var Closure(string):string */
		private readonly Closure $createTitle,

	) { }

	#[Override]
	public function createTitle(string $baseTitle): string {
		return ($this->createTitle)($baseTitle);
	}

}
