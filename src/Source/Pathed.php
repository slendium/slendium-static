<?php

namespace Slendium\SlendiumStatic\Source;

/**
 * Container for values associated with a specific path on the filesystem.
 *
 * @since 1.0
 * @template T
 * @author C. Fahner
 * @copyright Slendium 2026
 */
interface Pathed {

	/** @since 1.0 */
	public string $path { get; }

	/**
	 * @since 1.0
	 * @var T
	 */
	public mixed $value { get; }

}
