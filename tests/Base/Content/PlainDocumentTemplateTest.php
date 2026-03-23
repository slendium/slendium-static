<?php

namespace Slendium\SlendiumStaticTests\Base\Content;

use Exception;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\SlendiumStatic\Base\Builders\ConfigsBuilder;
use Slendium\SlendiumStatic\Base\Content\ArraySectionProvider;
use Slendium\SlendiumStatic\Base\Content\ContentException;
use Slendium\SlendiumStatic\Base\Content\HtmlSection;
use Slendium\SlendiumStatic\Base\Content\PlainDocumentTemplate;
use Slendium\SlendiumStatic\Content\SectionNames;
use Slendium\SlendiumStatic\Content\SectionProvider;
use Slendium\SlendiumStaticTests\Base\Content\Fakes\FixedValueSummarizer;
use Slendium\SlendiumStaticTests\Base\Content\Fixtures\SectionProviderFixtures;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class PlainDocumentTemplateTest extends TestCase {

	public static function missingSectionsCases(): iterable { // @phpstan-ignore missingType.iterableValue
		$header = new HtmlSection('HEADER');
		$main = new HtmlSection('MAIN');
		$footer = new HtmlSection('FOOTER');
		yield [ new ArraySectionProvider([ SectionNames::HEADER => $header, SectionNames::MAIN => $main ]) ];
		yield [ new ArraySectionProvider([ SectionNames::HEADER => $header, SectionNames::FOOTER => $footer ]) ];
		yield [ new ArraySectionProvider([ SectionNames::MAIN => $main, SectionNames::FOOTER => $footer ]) ];
	}

	#[DataProvider('missingSectionsCases')]
	public function test_createDocument_shouldError_whenMissingSections(SectionProvider $sections): void {
		$sut = new PlainDocumentTemplate([ ]);

		$result = $sut->createDocument($sections);

		$this->assertInstanceOf(Exception::class, $result);
	}

	public function test_createDocument_shouldConsiderConfiguredLocales(): void {
		$primaryLanguage = 'fy';
		$configs = new ConfigsBuilder()
			->setLocales([ $primaryLanguage, 'nl', 'en' ])
			->build();
		$sut = new PlainDocumentTemplate($configs);

		$doc = $sut->createDocument(SectionProviderFixtures::PlainWorkingProvider());
		/** @var \Dom\HTMLDocument $doc */
		$result = $doc->querySelector('html')?->getAttribute('lang');

		// this test could technically succeed by coincidence if the system default locale is 'fy'
		$this->assertSame($primaryLanguage, $result);
	}

	public function test_createDocument_shouldUpsertMetaTitleAndDescription(): void {
		$expectedTitle = '{79ab717f-9699-422f-8aec-e22b41b59114}';
		$expectedDescription = '{be1c4d79-5945-40c5-8e60-712843103396}';
		$configs = new ConfigsBuilder()
			->setSummarizer(new FixedValueSummarizer($expectedTitle, $expectedDescription))
			->build();
		$sut = new PlainDocumentTemplate($configs);

		$doc = $sut->createDocument(SectionProviderFixtures::PlainWorkingProvider());
		/** @var \Dom\HTMLDocument $doc */
		$resultTitle = $doc->querySelector('meta[name=title]')?->getAttribute('content');
		$resultDescription = $doc->querySelector('meta[name=description]')?->getAttribute('content');

		$this->assertSame($expectedTitle, $resultTitle);
		$this->assertSame($expectedDescription, $resultDescription);
	}

	public function test_createDocument_shouldConsiderConfiguredTitleTemplate(): void {
		$localTitle = '{59903fe5-9874-48dd-8aa6-1adbe60c5c1f}';
		$template = static fn(string $title) => "before $title after";
		$expectedTitle = $template($localTitle);
		$configs = new ConfigsBuilder()
			->setSummarizer(new FixedValueSummarizer($localTitle, null))
			->setTitleTemplate($template)
			->build();
		$sut = new PlainDocumentTemplate($configs);

		$doc = $sut->createDocument(SectionProviderFixtures::PlainWorkingProvider());
		/** @var \Dom\HTMLDocument $doc */
		$result = $doc->querySelector('title')?->textContent;

		$this->assertSame($expectedTitle, $result);
	}

	public function test_createDocument_shouldError_whenSectionHasSyntaxError(): void {
		$configs = new ConfigsBuilder()->build();
		$sections = new ArraySectionProvider([
			SectionNames::HEADER => new HtmlSection('OK'),
			SectionNames::MAIN => new HtmlSection('><SyntaxError'),
			SectionNames::FOOTER => new HtmlSection('OK'),
		]);
		$sut = new PlainDocumentTemplate($configs);

		$result = $sut->createDocument($sections);

		$this->assertInstanceOf(ContentException::class, $result);
	}

}
