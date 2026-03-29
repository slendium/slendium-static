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

	public static function pathCases(string $separator): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ $separator, [ ] ];
		yield [ "{$separator}tmp", [ 'tmp' ] ];
		yield [ "{$separator}tmp{$separator}inner", [ 'tmp', 'inner' ] ];
		yield [ 'tmp', [ 'tmp' ] ];
		yield [ "{$separator}tmp{$separator}test.html", [ 'tmp', 'test.html' ] ];
	}

	public static function fromStringCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield from self::pathCases(Path::UNIX_SEPARATOR);
		yield from self::pathCases(Path::WINDOWS_SEPARATOR);
	}

	/** @param list<string> $expectedResult */
	#[DataProvider('fromStringCases')]
	public function test_fromString_shouldReturnExpectedResult(string $path, array $expectedResult): void {
		$result = Path::fromString($path)->parts;

		$this->assertSame($expectedResult, $result);
	}

	public static function constructThrowCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ [ '.' ] ];
		yield [ [ '..' ] ];
		yield [ [ 'tmp', '.' ] ];
		yield [ [ 'tmp', '..', 'inner' ] ];
	}

	/** @param list<non-empty-string> $parts */
	#[DataProvider('constructThrowCases')]
	public function test_construct_shouldThrow_whenContainingRelativeParts(array $parts): void {
		// Assert
		$this->expectException(Exception::class);

		// Act
		new Path($parts);
	}

	public static function toStringCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ [ ], \DIRECTORY_SEPARATOR ];
		yield [ [ 'tmp' ], \DIRECTORY_SEPARATOR.'tmp' ];
		yield [ [ 'tmp', 'inner' ], \DIRECTORY_SEPARATOR.'tmp'.\DIRECTORY_SEPARATOR.'inner' ];
		yield [ [ 'tmp', 'test.html' ], \DIRECTORY_SEPARATOR.'tmp'.\DIRECTORY_SEPARATOR.'test.html' ];
	}

	/** @param list<non-empty-string> $parts */
	#[DataProvider('toStringCases')]
	public function test_toString_shouldProduceExpectedResult(array $parts, string $expectedResult): void {
		$sut = new Path($parts);

		$result = (string)$sut;

		$this->assertSame($expectedResult, $result);
	}

	/** @return iterable<array{0: Path, 1: list<non-empty-string>, 2: list<non-empty-string> }> */
	public static function appendCases(): iterable {
		yield [ new Path([ ]), [ ], [ ] ];
		yield [ new Path([ ]), [ 'tmp' ], [ 'tmp' ] ];
		yield [ new path([ ]), [ 'tmp', 'inner' ], [ 'tmp', 'inner' ] ];
		yield [ new Path([ 'tmp' ]), [ ], [ 'tmp' ] ];
		yield [ new Path([ 'tmp' ]), [ 'inner' ], [ 'tmp', 'inner' ] ];
		yield [ new Path([ 'tmp', 'inner' ]), [ ], [ 'tmp', 'inner' ] ];
	}

	/**
	 * @param list<non-empty-string> $append
	 * @param list<non-empty-string> $expectedResult
	 */
	#[DataProvider('appendCases')]
	public function test_append_shouldProduceExpectedResult(Path $sut, array $append, array $expectedResult): void {
		$result = $sut->append($append)->parts;

		$this->assertSame($expectedResult, $result);
	}

}
