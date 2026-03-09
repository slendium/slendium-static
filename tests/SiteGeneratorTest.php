<?php

namespace Slendium\SlendiumStaticTests;

use Exception;
use Override;

use PHPUnit\Framework\TestCase;

use Slendium\SlendiumStatic\SiteGenerator;
use Slendium\SlendiumStatic\Base\Builders\ConfigsBuilder;
use Slendium\SlendiumStatic\Base\Content\ArraySectionProvider;
use Slendium\SlendiumStatic\Base\Content\HtmlSection;
use Slendium\SlendiumStatic\Common\HtmlParser;
use Slendium\SlendiumStatic\Content\SectionNames;
use Slendium\SlendiumStaticTests\Base\Content\Fixtures\SectionProviderFixtures;
use Slendium\SlendiumStaticTests\Source\Mocks\MemoryFilesystem;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class SiteGeneratorTest extends TestCase {

	public function test_create_save_shouldReadAndWriteExpectedAmountsAndContents(): void {
		// Arrange
		$files = [ 'index.html' => '', 'posts.html' => '', 'posts' => [ '0.html' => '', '1.html' => '' ] ];
		$fs = new class($files) extends MemoryFilesystem {

			public int $readCount = 0;

			public array $writes = [ ];

			#[Override]
			public function readFile(string $path): string {
				$this->readCount += 1;
				return 'Main contents.';
			}

			#[Override]
			public function writeFile(string $path, string $contents): true {
				$this->writes[] = $contents;
				return true;
			}

		};
		$metaGuid = '48b9187b-cac9-447a-8a16-7f2aeea41a82';
		$headerGuid = '5c94d5b3-fd6f-45e3-b62e-d0487dfe32ac';
		$footerGuid = 'e19f2dd1-91a0-4a02-a797-1510b5827990';
		$sections = new ArraySectionProvider([
			SectionNames::META => new HtmlSection("<meta name=title content=\"$metaGuid\">"),
			SectionNames::HEADER => new HtmlSection("<span class=header>$headerGuid</span>"),
			sectionNames::FOOTER => new HtmlSection("<span class=footer>$footerGuid</span>"),
		]);
		$configs = new ConfigsBuilder()
			->setBaseSectionProvider($sections)
			->setFilesystem($fs)
			->build();
		$sut = new SiteGenerator($configs)->create('/');

		// Act
		$result = $sut->save('out');

		// Assert
		$this->assertTrue($result);
		$this->assertSame(4, $fs->readCount);
		$this->assertSame(4, \count($fs->writes));
		$document = HtmlParser::parse($fs->writes[0]); // all files are the same
		$this->assertSame($metaGuid, $document->querySelector('meta[name=title]')->getAttribute('content'));
		$this->assertSame($headerGuid, $document->querySelector('span.header')->textContent);
		$this->assertSame($footerGuid, $document->querySelector('span.footer')->textContent);
	}

	public function test_create_save_shouldTriggerCopyFile_whenEncounteringBinaryFile(): void {
		// Arrange
		$files = [ 'tmp' => [ 'index.html' => '', 'invoice.pdf' => '' ] ];
		$fs = new class($files) extends MemoryFilesystem {

			public bool $copied = false;

			#[Override]
			public function readFile(string $path): string {
				return '<h1>Title</h1>';
			}

			#[Override]
			public function copyFile(string $sourcePath, string $targetPath): Exception {
				$this->copied = true;
				return parent::copyFile($sourcePath, $targetPath);
			}
		};
		$configs = new ConfigsBuilder()
			->setBaseSectionProvider(SectionProviderFixtures::PlainWorkingProvider())
			->setFilesystem($fs)
			->build();
		$sut = new SiteGenerator($configs)->create('tmp');

		// Act
		$result = $sut->save('out');

		// Assert
		$this->assertTrue($result);
		$this->assertTrue($fs->copied);
	}

}
