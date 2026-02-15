<?php

namespace Slendium\SlendiumStatic\Base;

use Override;

use Slendium\SlendiumStatic\Error as IError;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class Error implements IError {

	/** @since 1.0 */
	public function __construct(

		#[Override]
		public readonly string $message,

		/** @var array<self> */
		#[Override]
		public readonly array $innerErrors = [ ],

	) { }

}
