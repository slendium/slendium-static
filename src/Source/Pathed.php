<?php

namespace Slendium\SlendiumStatic\Source;

/**
 * Container for values associated with a path.
 *
 * @since 1.0
 * @template T
 * @author C. Fahner
 * @copyright Slendium 2026
 */
interface Pathed {

	/** @since 1.0 */
	public Path $path { get; }

	/**
	 * @since 1.0
	 * @var T
	 */
	public mixed $value { get; }

}
