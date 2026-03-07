<?php

namespace Slendium\SlendiumStaticTests\Base\Content;

use Exception;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\SlendiumStatic\Base\Content\HtmlFileSectionProvider;
use Slendium\SlendiumStatic\Common\HtmlParser;
use Slendium\SlendiumStatic\Content\Section;
use Slendium\SlendiumStatic\Content\SectionNames;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class HtmlFileSectionProviderTest extends TestCase {

	public function test_getSection_shouldContainAllSections_whenFileContainsOnlyExplicitSections(): void {
		$html = <<<HtmlFile
<sls-section name=meta><meta name=title content=Test></sls-section>
<sls-section name=header>Header</sls-section>
<sls-section name=main>Main</sls-section>
<sls-section name=footer>Footer</sls-section>
<sls-section name=custom>Custom</sls-section>
HtmlFile;
		$sut = new HtmlFileSectionProvider($html);

		$result = [
			$sut->getSection('meta'),
			$sut->getSection('header'),
			$sut->getSection('main'),
			$sut->getSection('footer'),
			$sut->getSection('custom'),
		];

		foreach ($result as $part) {
			$this->assertInstanceOf(Section::class, $part);
		}
	}

	public function test_getSection_shouldContainMain_whenFileContainsOnlyImplicitMainSection(): void {
		$html = '<header><h1>Main contents</h1></header><p>A paragraph.</p>';
		$sut = new HtmlFileSectionProvider($html);

		$result = $sut->getSection(SectionNames::MAIN)->getHtml();

		$this->assertSame($html, $result);
	}

	public function test_getSection_shouldError_whenFileContainsExplicitAndImplicitMainSection(): void {
		$html = '<header>Implicit main content</header><sls-section name=main>Explicit main content</sls-section>';
		$sut = new HtmlFileSectionProvider($html);

		$result = $sut->getSection(SectionNames::MAIN);

		$this->assertInstanceOf(Exception::class, $result);
	}

	public function test_getSection_shouldNotContainMain_whenOnlyWhitespaceRemains(): void {
		$html = <<<Whitespace
<sls-section name=custom>Some section</sls-section>
            \t<sls-section name=footer>Footer</sls-section>
Whitespace;
		$sut = new HtmlFileSectionProvider($html);

		$result = $sut->getSection(SectionNames::MAIN);

		$this->assertNull($result);
	}

	public function test_getSection_shouldContainTrimmedMain_whenWrappedInWhitespace(): void {
		$expectedMainHtml = '<b>Main content</b>';
		$html = <<<Whitespace
<sls-section name=header>Header</sls-section>
		    $expectedMainHtml


<sls-section name=footer>Footer content preceded by newlines for formatting</sls-section>
Whitespace;
		$sut = new HtmlFileSectionProvider($html);

		$result = $sut->getSection(SectionNames::MAIN)->getHtml();

		$this->assertSame($expectedMainHtml, $result);
	}

	public function test_getSection_shouldCombineMultipleImplicitMainSectionParts(): void {
		$html = <<<SplitMain
<h1>Main title</h1>
<sls-section name=interstitial>Random stuff</sls-section>
<p>Main paragraph.</p>
<sls-section name=meta><meta name=title content="Put the title in the bag"></sls-section>
<p>Secondary paragraph.</p>
SplitMain;
		$sut = new HtmlFileSectionProvider($html);

		$result = HtmlParser::parse(
			"<!DOCTYPE html>\n<html><body>".$sut->getSection(SectionNames::MAIN)->getHtml().'</body></html>'
		);

		$this->assertSame(2, $result->querySelectorAll('p')->length);
		$this->assertSame(1, $result->querySelectorAll('h1')->length);
		$this->assertTrue($result->querySelector('h1')->nextSibling->tagName === 'P');
	}

	public static function getSectionInvalidElementCases(): iterable {
		yield [ '<html></html>' ];
		yield [ '<head></head>' ];
		yield [ '<body></body>' ];
		yield [ '<html>With content?</html>' ];
		yield [ '<head>With content?</head>' ];
		yield [ '<body>With content?</body>' ];
		yield [ '<html><head></head><body>?</body></html>' ];
		yield [ '<html><body>?</body></html>' ];
	}

	#[DataProvider('getSectionInvalidElementCases')]
	public function test_getSection_shouldError_whenFileContainsInvalidElement(string $html): void {
		$sut = new HtmlFileSectionProvider($html);

		$result = $sut->getSection(SectionNames::MAIN);

		$this->assertInstanceOf(Exception::class, $result);
	}

	public function test_getSection_shouldError_whenFileContainsUnnamedSection(): void {
		$html = '<sls-section>Unnamed</sls-section>';
		$sut = new HtmlFileSectionProvider($html);

		$result = $sut->getSection('');

		$this->assertInstanceOf(Exception::class, $result);
	}

	public function test_getSection_shouldError_whenFileContainsNestedSections(): void {
		$html = '<sls-section name=outer><sls-section name=nested>Nested</sls-section></sls-section>';
		$sut = new HtmlFileSectionProvider($html);

		$result = $sut->getSection('nested');

		$this->assertInstanceOf(Exception::class, $result);
	}

	public function test_getSection_shouldError_whenFileContainsSyntaxErrors(): void {
		$html = '><';
		$sut = new HtmlFileSectionProvider($html);

		$result = $sut->getSection(SectionNames::MAIN);

		$this->assertInstanceOf(Exception::class, $result);
	}

}
