<?php

namespace Slendium\SlendiumStaticTests\Source;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\SlendiumStatic\Common\Iteration;
use Slendium\SlendiumStatic\Site\Resource;
use Slendium\SlendiumStatic\Source\Directory;
use Slendium\SlendiumStatic\Source\Filesystem;
use Slendium\SlendiumStatic\Source\SourceException;

use Slendium\SlendiumStaticTests\Source\Mocks\CallbackFilesystem;
use Slendium\SlendiumStaticTests\Source\Mocks\MemoryFilesystem;

class DirectoryTest extends TestCase {

	public function test_extractResources_shouldNeverQueryDotPaths(): void {
		$called = false;
		$pathFlagged = false;
		$callback = static function ($path) use (&$called, &$pathFlagged) {
			$called = true;
			$pathFlagged = $pathFlagged || $path === '.' || $path === '..';
			return false;
		};
		$sut = new Directory('', filesystem: new CallbackFilesystem(
			isDirectory: $callback,
			isFile: $callback,
			scanDirectory: static fn() => [ '.', '..', 'test' ],
		));

		Iteration::toList($sut->extractResources());

		$this->assertTrue($called);
		$this->assertFalse($pathFlagged);
	}

	public static function sourceExceptionCases(): iterable {
		// ambiguous, both files should resolve to index.html
		yield [ new MemoryFilesystem([ 'tmp' => [ 'index.htm' => '', 'index.html' => '' ] ]) ];
		// page name cannot be empty
		yield [ new MemoryFilesystem([ 'tmp' => [ '.html' => '' ] ]) ];
	}

	#[DataProvider('sourceExceptionCases')]
	public function test_extractResources_shouldYieldErrors_whenSourceInvalid(Filesystem $filesystem): void {
		$sut = new Directory('/tmp', filesystem: $filesystem);

		$result = Iteration::toList($sut->extractResources());

		$this->assertTrue(Iteration::all($result, static fn($item) => $item instanceof SourceException));
	}

	public function test_extractResources_shouldYieldResources_whenSourceValid(): void {
		$filesystem = new MemoryFilesystem([ 'tmp' => [
			'index.php' => '',
			'blog.html' => '',
			'terms.pdf' => '',
			'blog' => [
				// ensure no failure on two files with the same name in different subfolders
				'2025' => [ 'article1.html' => '' ],
				'2026' => [ 'article1.html' => '' ],
				// ensure no failure on empty folders
				'2027' => [ ],
			],
		] ]);
		$sut = new Directory('/tmp', filesystem: $filesystem);

		$result = Iteration::toList($sut->extractResources());

		$this->assertTrue(\count($result) > 0);
		$this->assertTrue(Iteration::all($result, static fn($item) => $item instanceof Resource));
	}

}
