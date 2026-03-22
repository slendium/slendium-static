<?php

namespace Slendium\SlendiumStaticTests\Site;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\SlendiumStatic\Site\Uri;
use Slendium\SlendiumStatic\Site\UriRelations;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class UriRelationsTest extends TestCase {

	public static function isDescendantOfCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ Uri::fromString('/'), Uri::fromString('/'), false ];
		yield [ Uri::fromString('/'), Uri::fromString('/tmp'), true ];
		yield [ Uri::fromString('/tmp'), Uri::fromString('/'), false ];
		yield [ Uri::fromString('/tmp'), Uri::fromString('/tmp/inner'), true ];
		yield [ Uri::fromString('/tmp'), Uri::fromString('/sibling'), false ];
		yield [ Uri::fromString('/tmp/inner'), Uri::fromString('/tmp/inner/test.html'), true ];
		yield [ Uri::fromString('/tmp/inner'), Uri::fromString('/tmp/test.html'), false ];
	}

	#[DataProvider('isDescendantOfCases')]
	public function test_isDescendantOf_shouldReturnExpectedResult(Uri $ancestor, Uri $subject, bool $expectedResult): void {
		$result = UriRelations::isDescendantOf($ancestor, $subject);

		$this->assertSame($expectedResult, $result);
	}

	public static function isSiblingOfCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ Uri::fromString('/'), Uri::fromString('/'), false ];
		yield [ Uri::fromString('/'), Uri::fromString('/tmp'), false ];
		yield [ Uri::fromString('/tmp'), Uri::fromString('/'), false ];
		yield [ Uri::fromString('/tmp'), Uri::fromString('/tmp'), false ];
		yield [ Uri::fromString('/tmp'), Uri::fromString('/tmp/inner'), false ];
		yield [ Uri::fromString('/tmp'), Uri::fromString('/sibling'), true ];
		yield [ Uri::fromString('/tmp/inner'), Uri::fromString('/tmp/inner/test.html'), false ];
		yield [ Uri::fromString('/tmp/inner'), Uri::fromString('/tmp/test.html'), true ];
	}

	#[DataProvider('isSiblingOfCases')]
	public function test_isSiblingOf_shouldReturnExpectedResult(Uri $reference, Uri $subject, bool $expectedResult): void {
		$result = UriRelations::isSiblingOf($reference, $subject);

		$this->assertSame($expectedResult, $result);
	}

}
