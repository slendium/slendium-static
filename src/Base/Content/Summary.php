<?php

namespace Slendium\SlendiumStatic\Base\Content;

use Override;

use Slendium\SlendiumStatic\Content\Summary as ISummary;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class Summary implements ISummary {

	/** @since 1.0 */
	public function __construct(

		/** @var ?non-empty-string */
		#[Override]
		public readonly ?string $title,

		/** @var ?non-empty-string */
		#[Override]
		public readonly ?string $description,

	) { }

}
