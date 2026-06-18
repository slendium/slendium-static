<?php

namespace Slendium\SlendiumStatic\Content\Meta;

use Attribute;

/**
 * Describes alternative keys for a front matter property.
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class AlternativeKeys {

	/**
	 * @since 1.0
	 * @var non-empty-list<non-empty-string>
	 */
	public array $names;

	/**
	 * @since 1.0
	 * @param non-empty-string $name
	 * @param non-empty-string ...$names
	 */
	public function __construct(string $name, string ...$names) {
		$this->names = [ $name, ...\array_values($names) ];
	}

}
