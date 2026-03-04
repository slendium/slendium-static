<?php

namespace Slendium\SlendiumStaticTests\Base\Content;

use Closure;

use PHPUnit\Framework\TestCase;

use Slendium\SlendiumStatic\Base\Content\ClosureTitleTemplate;

class ClosureTitleTemplateTest extends TestCase {

	public function test_createTitle(): void {
		$localTitle = 'test';
		$expectedResult = 'test | MyWebsite';
		$sut = new ClosureTitleTemplate(fn($title) => "$title | MyWebsite");

		$result = $sut->createTitle($localTitle);

		$this->assertSame($expectedResult, $result);
	}

}
