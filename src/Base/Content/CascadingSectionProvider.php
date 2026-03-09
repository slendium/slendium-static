<?php

namespace Slendium\SlendiumStatic\Base\Content;

use Exception;
use Override;

use Slendium\SlendiumStatic\Content\Section;
use Slendium\SlendiumStatic\Content\SectionProvider;
use Slendium\SlendiumStatic\Common\Iteration;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class CascadingSectionProvider implements SectionProvider {

	/** @param iterable<SectionProvider> $providers Priority list of section providers to try, high to low */
	public function __construct(private readonly iterable $providers) { }

	#[Override]
	public function getSection(string $name): Exception|Section|null {
		foreach ($this->providers as $provider) {
			if (($section = $provider->getSection($name)) !== null) {
				return $section;
			}
		}
		return null;
	}

}
