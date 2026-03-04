<?php

namespace Slendium\SlendiumStaticTests;

use PHPUnit\Framework\TestCase;

use Slendium\SlendiumStatic\Base\Builders\ConfigsBuilder;
use Slendium\SlendiumStatic\Base\Content\DefaultSummarizer;
use Slendium\SlendiumStatic\Configs;
use Slendium\SlendiumStatic\Content\Summarizer;
use Slendium\SlendiumStatic\Content\TitleTemplate;

/**
 * Tests both {@see Configs} and {@see ConfigsBuilder}.
 *
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class ConfigsTest extends TestCase {

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

}
