<?php

namespace Slendium\SlendiumStaticTests\Site;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\SlendiumStatic\Site\Uri;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class UriTest extends TestCase {

	public static function fromStringCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ '/', [ ] ];
		yield [ '', [ ] ];
		yield [ 'tmp', [ 'tmp' ] ];
		yield [ '/tmp', [ 'tmp' ] ];
		yield [ '/tmp/tmp', [ 'tmp', 'tmp' ] ];
		yield [ '/tmp?a=b', [ 'tmp' ] ];
		yield [ 'tmp#tmp', [ 'tmp' ] ];
		yield [ 'tmp?a=b#tmp', [ 'tmp' ] ];
		yield [ '?a=c', [ ] ];
		yield [ '#test', [ ] ];
	}

	/** @param list<string> $expectedPath */
	#[DataProvider('fromStringCases')]
	public function test_fromString_shouldParseAnything(string $uri, array $expectedPath): void {
		$result = Uri::fromString($uri)->path;

		$this->assertSame($expectedPath, $result);
	}

	public static function toStringCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ [ ], '/' ];
		yield [ [ 'tmp' ], '/tmp' ];
		yield [ [ 'tmp', 'tmp' ], '/tmp/tmp' ];
		yield [ [ '' ], '/' ];
	}

	#[DataProvider('toStringCases')]
	public function test_toString_shouldPrefixWithSlash(array $parts, string $expectedResult): void {
		$sut = new Uri($parts);

		$result = (string)$sut;

		$this->assertSame($expectedResult, $result);
	}

}
