<?php

namespace Slendium\SlendiumStatic\Base\Content;

use Override;

use Slendium\SlendiumStatic\Content\Section;
use Slendium\SlendiumStatic\Content\SectionProvider;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class ArraySectionProvider implements SectionProvider {

	/** @since 1.0 */
	public function __construct(

		/** @var array<string,Section> */
		private readonly array $sections,

	) { }

	#[Override]
	public function getSection(string $name): ?Section {
		return $this->sections[$name]
			?? null;
	}

}
