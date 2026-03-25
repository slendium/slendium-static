<?php

namespace Slendium\SlendiumStatic\Base\Source;

use Override;

use Slendium\SlendiumStatic\Source\Path;
use Slendium\SlendiumStatic\Source\Pathed as IPathed;

/**
 * @internal
 * @template T
 * @implements IPathed<T>
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class Pathed implements IPathed {

	public function __construct(

		#[Override]
		public readonly Path $path,

		/** @var T */
		#[Override]
		public readonly mixed $value,

	) { }

}
