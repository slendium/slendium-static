<?php

namespace Slendium\SlendiumStaticTests\Base\Content;

use PHPUnit\Framework\TestCase;

use Slendium\SlendiumStatic\Base\Content\ArraySectionProvider;
use Slendium\SlendiumStatic\Base\Content\HtmlSection;
use Slendium\SlendiumStatic\Content\Section;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class ArraySectionProviderTest extends TestCase {

	/** Indirectly also tests {@see HtmlSection} */
	public function test_getSection(): void {
		$content = '<b>Main</b>';
		$section = new HtmlSection($content);
		$sut = new ArraySectionProvider([ 'main' => $section ]);

		$result = $sut->getSection('main');

		$this->assertSame($section, $result);
		$this->assertSame($content, $result->getHtml());
	}

}
