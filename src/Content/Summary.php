<?php

namespace Slendium\SlendiumStatic\Content;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
interface Summary {

	/**
	 * @since 1.0
	 * @var ?non-empty-string
	 */
	public ?string $title { get; }

	/**
	 * @since 1.0
	 * @var ?non-empty-string
	 */
	public ?string $description { get; }

}
