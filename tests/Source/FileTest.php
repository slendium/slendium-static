<?php

namespace Slendium\SlendiumStaticTests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\SlendiumStatic\Source\File;
use Slendium\SlendiumStatic\Source\Directory;

class FileTest extends TestCase {

	public static function pathWithNormalizedNameCases(): iterable {
		// SplFileInfo::getFilename() returns the initial '/' when a file is in the filesystem root
		// to ensure this bug never occurs again, both cases (root and non-root folder) are tested
		foreach ([ '/tmp/', '/' ] as $path) {
			yield [ "{$path}index.html", 'index.html' ];
			yield [ "{$path}index.htm", 'index.html' ];
			yield [ "{$path}index.md", 'index.html' ];
			yield [ "{$path}index.php", 'index.html' ];
			yield [ "{$path}image.jpeg", 'image.jpg' ];
			yield [ "{$path}image.jpg", 'image.jpg' ];
			yield [ "{$path}invoice.pdf", 'invoice.pdf' ];
		}
	}

	#[DataProvider('pathWithNormalizedNameCases')]
	public function test_normalizedName(string $path, string $expectedNormalizedName): void {
		$sut = new File($path, new Directory('/dummy'));

		$result = $sut->normalizedName;

		$this->assertSame($expectedNormalizedName, $result);
	}

	public static function fileWithSourcePathCases(): iterable {
		yield [ new File('root.html', new Directory('/')), 'root.html' ];
		yield [ new File('index.html', new Directory('/tmp/dummy', ancestor: new Directory('/tmp'))), 'tmp/dummy/index.html' ];
	}

	#[DataProvider('fileWithSourcePathCases')]
	public function test_sourcePath_shouldMatchRelativeDirectoryPath(File $sut, string $expectedSourcePath): void {
		$result = $sut->sourcePath;

		$this->assertSame($expectedSourcePath, $result);
	}

}
