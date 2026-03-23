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

	public static function unixPathCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield from self::pathCases(Path::UNIX_SEPARATOR);
	}

	/** @param list<string> $expectedResult */
	#[DataProvider('unixPathCases')]
	public function test_fromUnix_shouldProduceValidPath(string $path, array $expectedResult): void {
		$result = Path::fromUnix($path)->parts;

		$this->assertSame($expectedResult, $result);
	}

	public static function windowsPathCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield from self::pathCases(Path::WINDOWS_SEPARATOR);
	}

	/** @param list<string> $expectedResult */
	#[DataProvider('windowsPathCases')]
	public function test_fromWindows_shouldProduceValidPath(string $path, array $expectedResult): void {
		$result = Path::fromWindows($path)->parts;

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

}
