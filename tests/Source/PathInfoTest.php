<?php

namespace Slendium\SlendiumStaticTests\Source;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\SlendiumStatic\Source\Path;
use Slendium\SlendiumStatic\Source\PathInfo;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class PathInfoTest extends TestCase {

	public static function getExtensionCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ 'test.txt', 'txt' ];
		yield [ '/', '' ];
		yield [ '/tmp/index.html', 'html' ];
		yield [ '/tmp/inner/index.test.html', 'html' ];
		yield [ '/test.', '' ];
		yield [ '/.test', 'test' ];
	}

	#[DataProvider('getExtensionCases')]
	public function test_getExtension_shouldReturnExpectedResult(string $path, string $expectedResult): void {
		$path = Path::fromString($path);

		$result = PathInfo::getExtension($path);

		$this->assertSame($expectedResult, $result);
	}

	public static function getBasenameCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ '/', '', '' ];
		yield [ '/', '.txt', '' ];
		yield [ '/tmp', '', 'tmp' ];
		yield [ '/tmp', '.txt', 'tmp' ];
		yield [ '/tmp', 'tmp', '' ];
		yield [ '/tmp.txt', '', 'tmp.txt' ];
		yield [ '/tmp.txt', '.html', 'tmp.txt' ];
		yield [ '/tmp.txt', '.txt', 'tmp' ];
		yield [ '/tmp/inner/test.txt', '', 'test.txt' ];
		yield [ '/tmp/inner/test.txt', '.txt', 'test' ];
		yield [ '/tmp/inner/test.txt', 'test.txt', '' ];
		yield [ '/tmp/inner/test.txt', 'inner/test.txt', 'test.txt' ];
		yield [ '/tmp/inner/test.txt', 'random', 'test.txt' ];
	}

	#[DataProvider('getBasenameCases')]
	public function test_getBasename_shouldReturnExpectedResult(string $path, string $suffix, string $expectedResult): void {
		$path = Path::fromString($path);

		$result = PathInfo::getBasename($path, $suffix);

		$this->assertSame($expectedResult, $result);
	}

	public static function getNormalizedNameCases(): iterable { // @phpstan-ignore missingType.iterableValue
		// SplFileInfo::getFilename() returns the initial '/' when a file is in the filesystem root
		// to ensure this bug never occurs again, both cases (root and non-root folder) are tested
		foreach ([ '/tmp/', '/' ] as $path) {
			yield [ "{$path}index.html", 'index.html' ];
			yield [ "{$path}index.htm", 'index.html' ];
			yield [ "{$path}index.md", 'index.html' ];
			yield [ "{$path}image.jpeg", 'image.jpg' ];
			yield [ "{$path}image.jpg", 'image.jpg' ];
			yield [ "{$path}invoice.pdf", 'invoice.pdf' ];
		}
	}

	#[DataProvider('getNormalizedNameCases')]
	public function test_getNormalizedName(string $path, string $expectedResult): void {
		$path = Path::fromString($path);

		$result = PathInfo::getNormalizedName($path);

		$this->assertSame($expectedResult, $result);
	}

}
