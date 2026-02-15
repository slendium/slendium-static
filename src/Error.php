<?php

namespace Slendium\SlendiumStatic;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
interface Error {

	/** @since 1.0 */
	public string $message { get; }

	/**
	 * @since 1.0
	 * @var iterable<self>
	 */
	public iterable $innerErrors { get; }

}
