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
		yield [ Path::fromString('test.txt'), 'txt' ];
		yield [ Path::fromString('/'), '' ];
		yield [ Path::fromString('/tmp/index.html'), 'html' ];
		yield [ Path::fromString('/tmp/inner/index.test.html'), 'html' ];
		yield [ Path::fromString('/test.'), '' ];
		yield [ Path::fromString('/.test'), 'test' ];
	}

	#[DataProvider('getExtensionCases')]
	public function test_getExtension_shouldReturnExpectedResult(Path $path, string $expectedResult): void {
		$result = PathInfo::getExtension($path);

		$this->assertSame($expectedResult, $result);
	}

	public static function getBasenameCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ Path::fromString('/'), '', '' ];
		yield [ Path::fromString('/'), '.txt', '' ];
		yield [ Path::fromString('/tmp'), '', 'tmp' ];
		yield [ Path::fromString('/tmp'), '.txt', 'tmp' ];
		yield [ Path::fromString('/tmp'), 'tmp', '' ];
		yield [ Path::fromString('/tmp.txt'), '', 'tmp.txt' ];
		yield [ Path::fromString('/tmp.txt'), '.html', 'tmp.txt' ];
		yield [ Path::fromString('/tmp.txt'), '.txt', 'tmp' ];
		yield [ Path::fromString('/tmp/inner/test.txt'), '', 'test.txt' ];
		yield [ Path::fromString('/tmp/inner/test.txt'), '.txt', 'test' ];
		yield [ Path::fromString('/tmp/inner/test.txt'), 'test.txt', '' ];
		yield [ Path::fromString('/tmp/inner/test.txt'), 'inner/test.txt', 'test.txt' ];
		yield [ Path::fromString('/tmp/inner/test.txt'), 'random', 'test.txt' ];
	}

	#[DataProvider('getBasenameCases')]
	public function test_getBasename_shouldReturnExpectedResult(Path $path, string $suffix, string $expectedResult): void {
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
