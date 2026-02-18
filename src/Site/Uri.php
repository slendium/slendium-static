<?php

namespace Slendium\SlendiumStatic\Site;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class Uri {

	/** @internal */
	public function __construct(

		/**
		 * @since 1.0
		 * @var list<string>
		 */
		public readonly array $path,

	) { }

	public function __toString(): string {
		return '/'.\implode('/', $this->path);
	}

}
