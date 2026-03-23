<?php

namespace Slendium\SlendiumStaticTests\Base\Content;

use Exception;

use PHPUnit\Framework\TestCase;

use Slendium\SlendiumStatic\Base\Content\MarkdownFileSectionProvider;
use Slendium\SlendiumStatic\Content\Section;
use Slendium\SlendiumStatic\Content\SectionNames;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class MarkdownFileSectionProviderTest extends TestCase {

	public function test_getSection_shouldContainMain_whenFileIsJustMarkdown(): void {
		$markdown = 'This text is **bold**';
		$expectedOutput = '<p>This text is <strong>bold</strong></p>';
		$sut = new MarkdownFileSectionProvider($markdown);

		$section = $sut->getSection(SectionNames::MAIN);
		$this->assertInstanceOf(Section::class, $section);
		/** @var Section $section */
		$result = $section->getHtml();

		$this->assertSame($expectedOutput, $result);
	}

	public function test_getSection_shouldContainMeta_whenExplicitlyAdded(): void {
		$markdown = <<<Markdown
<sls-section name=meta>
	<meta name=title content="Hidden real title">
</sls-section>

# Main contents

This should be a paragraph with _emphasized_ text.

Markdown;
		$sut = new MarkdownFileSectionProvider($markdown);

		$result = [
			$sut->getSection(SectionNames::META),
			$sut->getSection(SectionNames::MAIN),
		];

		foreach ($result as $part) {
			$this->assertInstanceOf(Section::class, $part);
		}
	}

	public function test_getSection_shouldError_whenBothImplicitAndExplicitMainGiven(): void {
		$markdown = <<<Markdown
# Hello world

This is a paragraph.

<sls-section name=main>This is another main section</sls-section>
Markdown;
		$sut = new MarkdownFileSectionProvider($markdown);

		$result = $sut->getSection(SectionNames::MAIN);

		$this->assertInstanceOf(Exception::class, $result);
	}

}
