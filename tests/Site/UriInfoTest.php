<?php

namespace Slendium\SlendiumStaticTests\Site;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\SlendiumStatic\Site\Uri;
use Slendium\SlendiumStatic\Site\UriInfo;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class UriInfoTest extends TestCase {

	public static function getTailCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ '/', null ];
		yield [ '/index.html', 'index.html' ];
		yield [ '/tmp/index.html', 'index.html' ];
		yield [ '/tmp/inner/test.html', 'test.html' ];
	}

	#[DataProvider('getTailCases')]
	public function test_getTail_shouldReturnExpectedValue(string $uri, ?string $expectedResult): void {
		$sut = Uri::fromString($uri);

		$result = UriInfo::getTail($sut);

		$this->assertSame($expectedResult, $result);
	}

	public static function getDirnamesCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ '/', [ ] ];
		yield [ '/tmp', [ ] ];
		yield [ '/tmp/index.html', [ 'tmp' ] ];
		yield [ '/tmp/inner/test.html', [ 'tmp', 'inner' ] ];
	}

	/** @param list<non-empty-string> $expectedResult */
	#[DataProvider('getDirnamesCases')]
	public function test_getDirnames_shouldReturnExpectedValue(string $uri, array $expectedResult): void {
		$sut = Uri::fromString($uri);

		$result = UriInfo::getDirnames($sut);

		$this->assertSame($expectedResult, $result);
	}

}
