<?php

namespace Slendium\SlendiumStaticTests\Base\Content\Fixtures;

use Slendium\SlendiumStatic\Base\Content\ArraySectionProvider;
use Slendium\SlendiumStatic\Base\Content\HtmlSection;
use Slendium\SlendiumStatic\Content\SectionNames;
use Slendium\SlendiumStatic\Content\SectionProvider;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class SectionProviderFixtures {

	public const META_CONTENTS = '<meta name=title content=Title>';

	public const HEADER_CONTENTS = 'HEADER';

	public const MAIN_CONTENTS = 'MAIN';

	public const FOOTER_CONTENTS = 'FOOTER';

	public static function PlainWorkingProvider(): SectionProvider {
		return new ArraySectionProvider([
			SectionNames::META => new HtmlSection(self::META_CONTENTS),
			SectionNames::HEADER => new HtmlSection(self::HEADER_CONTENTS),
			SectionNames::MAIN => new HtmlSection(self::MAIN_CONTENTS),
			SectionNames::FOOTER => new HtmlSection(self::FOOTER_CONTENTS),
		]);
	}

}
