<?php

namespace Slendium\SlendiumStaticTests;

use PHPUnit\Framework\TestCase;
use Slendium\Localization\Base\Locale;
use Slendium\Localization\Base\LocaleList;
use Slendium\Localization\LocaleList as ILocaleList;

use Slendium\SlendiumStatic\Base\Builders\ConfigsBuilder;
use Slendium\SlendiumStatic\Base\Content\ArraySectionProvider;
use Slendium\SlendiumStatic\Base\Content\DefaultSummarizer;
use Slendium\SlendiumStatic\Configs;
use Slendium\SlendiumStatic\Content\SectionProvider;
use Slendium\SlendiumStatic\Content\Summarizer;
use Slendium\SlendiumStatic\Content\TitleTemplate;
use Slendium\SlendiumStatic\Site\Uri;
use Slendium\SlendiumStatic\Source\Filesystem;
use Slendium\SlendiumStaticTests\Source\Mocks\MemoryFilesystem;

/**
 * Tests both {@see Configs} and {@see ConfigsBuilder}.
 *
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class ConfigsTest extends TestCase {

	public function test_getFilesystem_shouldReturnInstance_whenGiven(): void {
		$fs = new MemoryFilesystem([ ]);
		$configs = new ConfigsBuilder()
			->setFilesystem($fs)
			->build();

		$result = Configs::getFilesystem($configs);

		$this->assertSame($fs, $result);
	}

	public function test_getFilesystem_shouldReturnInstance_whenOmitted(): void {
		$configs = new ConfigsBuilder()->build();

		$result = Configs::getFilesystem($configs);

		$this->assertInstanceOf(Filesystem::class, $result);
	}

	public function test_getBaseSectionProvider_shouldReturnInstance_whenGiven(): void {
		$expectedSectionProvider = new ArraySectionProvider([ ]);
		$configs = new ConfigsBuilder()
			->setBaseSectionProvider($expectedSectionProvider)
			->build();

		$result = Configs::getBaseSectionProvider($configs);

		$this->assertSame($expectedSectionProvider, $result);
	}

	public function test_getBaseSectionProvider_shouldReturnSomething_whenOmitted(): void {
		$configs = new ConfigsBuilder()->build();

		$result = Configs::getBaseSectionProvider($configs);

		$this->assertNotNull($result);
	}

	public function test_getLocales_shouldReturnLocaleListInstance_whenLocaleListGiven(): void {
		$localeList = new LocaleList([ new Locale('nl'), new Locale('en') ]);
		$configs = new ConfigsBuilder()
			->setLocales($localeList)
			->build();

		$result = Configs::getLocales($configs);

		$this->assertSame($localeList, $result);
	}

	public function test_getLocales_shouldParseStringLocales(): void {
		$configs = new ConfigsBuilder()
			->setLocales([ new Locale('fy'), 'nl', 'en' ])
			->build();

		$result = Configs::getLocales($configs);

		$this->assertInstanceOf(ILocaleList::class, $result);
		$this->assertSame('fy', $result[0]?->language);
		$this->assertSame('nl', $result[1]?->language);
		$this->assertSame('en', $result[2]?->language);
	}

	public function test_getSummarizer_shouldReturnGivenInstance_whenGiven(): void {
		$summarizer = new DefaultSummarizer;
		$configs = new ConfigsBuilder()
			->setSummarizer($summarizer)
			->build();

		$result = Configs::getSummarizer($configs);

		$this->assertSame($summarizer, $result);
	}

	public function test_getSummarizer_shouldReturnSomething_whenOmitted(): void {
		$configs = new ConfigsBuilder()->build();

		$result = Configs::getSummarizer($configs);

		$this->assertInstanceOf(Summarizer::class, $result);
	}

	public function test_getTitleTemplate_shouldReturnTemplate_whenClosureGiven(): void {
		$localTitle = 'test';
		$expectedTitle = "$localTitle | MyWebsite";
		$configs = new ConfigsBuilder()
			->setTitleTemplate(static fn($title) => "$title | MyWebsite")
			->build();

		$result = Configs::getTitleTemplate($configs);

		$this->assertInstanceOf(TitleTemplate::class, $result);
		$this->assertSame($expectedTitle, $result->createTitle($localTitle));
	}

	public function test_getTitleTemplate_shouldReturnTemplateThatDoesNotModifyTitle_whenConfigOmitted(): void {
		$expectedTitle = 'test';
		$configs = new ConfigsBuilder()->build();

		$result = Configs::getTitleTemplate($configs);

		$this->assertInstanceOf(TitleTemplate::class, $result);
		$this->assertSame($expectedTitle, $result->createTitle($expectedTitle));
	}

	public function test_getBaseUri_shouldReturnSameInstance_whenConfigured(): void {
		$uri = new Uri([ 'static', 'stylesheet.css' ]);
		$configs = new ConfigsBuilder()
			->setBaseUri($uri)
			->build();

		$result = Configs::getBaseUri($configs);

		$this->assertSame($uri, $result);
	}

	public function test_getBaseUri_shouldReturnParsedInstance_whenConfiguredAsString(): void {
		$uri = '/static/stylesheet.css';
		$configs = new ConfigsBuilder()
			->setBaseUri($uri)
			->build();

		$result = Configs::getBaseUri($configs);

		$this->assertSame($uri, (string)$result);
	}

	public function test_getBaseUri_shouldReturnRootInstance_whenOmitted(): void {
		$configs = new ConfigsBuilder()->build();

		$result = Configs::getBaseUri($configs);

		$this->assertSame('/', (string)$result);
	}

}
