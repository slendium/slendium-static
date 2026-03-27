<?php

namespace Slendium\SlendiumStaticTests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\SlendiumStatic\Source\File;
use Slendium\SlendiumStatic\Source\Directory;
use Slendium\SlendiumStatic\Source\Path;
use Slendium\SlendiumStatic\Source\PathInfo;

class FileTest extends TestCase {

	public static function fileWithSourcePathCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ new File(new Directory('/'), 'root.html'), 'root.html' ];
		yield [ new File(new Directory('/tmp/'), 'index.html'), 'index.html' ];
		// the upper ancestor is the root folder, which should not appear in the source path
		yield [ new File(new Directory('/tmp/dummy', ancestor: new Directory('/tmp')), 'index.html'), 'dummy/index.html' ];
	}

	#[DataProvider('fileWithSourcePathCases')]
	public function test_sourcePath_shouldMatchRelativeDirectoryPath(File $sut, string $expectedSourcePath): void {
		$result = $sut->sourcePath;

		$this->assertSame($expectedSourcePath, $result);
	}

}
