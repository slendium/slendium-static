<?php

namespace Slendium\SlendiumStaticTests\Source;

use Exception;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\SlendiumStatic\Source\Path;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class PathTest extends TestCase {

	public static function fromStringCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ '/', [ ] ];
		yield [ '/tmp', [ 'tmp' ] ];
		yield [ '/tmp/inner', [ 'tmp', 'inner' ] ];
		yield [ 'tmp', [ 'tmp' ] ];
		yield [ '/tmp/test.html', [ 'tmp', 'test.html' ] ];
	}

	/** @param list<string> $expectedResult */
	#[DataProvider('fromStringCases')]
	public function test_fromString_shouldProduceValidPath(string $path, array $expectedResult): void {
		$result = Path::fromString($path)->parts;

		$this->assertSame($expectedResult, $result);
	}

	public static function fromStringThrowCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ '.' ];
		yield [ '..' ];
		yield [ '/tmp/.' ];
		yield [ '/tmp/../inner' ];
	}

	#[DataProvider('fromStringThrowCases')]
	public function test_fromString_shouldThrow_whenContainingRelativeParts(string $case): void {
		// Assert
		$this->expectException(Exception::class);

		// Act
		Path::fromString($case);
	}

	public static function toStringCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ [ ], '/' ];
		yield [ [ 'tmp' ], '/tmp' ];
		yield [ [ 'tmp', 'inner' ], '/tmp/inner' ];
		yield [ [ 'tmp', 'test.html' ], '/tmp/test.html' ];
	}

	/** @param list<non-empty-string> $parts */
	#[DataProvider('toStringCases')]
	public function test_toString_shouldProduceExpectedResult(array $parts, string $expectedResult): void {
		$sut = new Path($parts);

		$result = (string)$sut;

		$this->assertSame($expectedResult, $result);
	}

}
