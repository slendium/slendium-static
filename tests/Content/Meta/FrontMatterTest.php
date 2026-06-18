<?php

namespace Slendium\SlendiumStaticTests\Content\Meta;

use DateTime;
use ReflectionClass;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use Slendium\SlendiumStatic\Content\Meta\AlternativeKeys;
use Slendium\SlendiumStatic\Content\Meta\FrontMatter;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class FrontMatterTest extends TestCase {

	private const YAML = <<<EOF
---
title: "63adcd82-7241-47f1-9c03-2e7276aa1c7c"
description: "6a028a7b-d308-42ae-a167-939db7f467f3"
isDraft: true
createdAt: 2026-04-13
publishedAt: 2026-05-13
editedAt: 2026-06-13
---

EOF;

	private const JSON = <<<EOF
;;;
{
	"title": "b3ad7488-6794-4d74-8db1-521c6b0d96e5",
	"description": "e3c481de-cbd2-435c-b734-9181333b2202",
	"isDraft": true,
	"createdAt": "2026-04-15",
	"publishedAt": "2026-05-15",
	"editedAt": "2026-06-15"
}
;;;

EOF;

	private const MARKDOWN = <<<EOF
# Title

Lorem ipsum.
EOF;

	public function test_fromDocument_shouldReturnExpectedResult_whenFormattedAsYaml(): void {
		$result = FrontMatter::fromDocument(self::YAML.self::MARKDOWN);

		$this->assertSame(\strlen(self::YAML), $result->bytes);
		$frontMatter = $result->frontMatter;
		$this->assertSame('63adcd82-7241-47f1-9c03-2e7276aa1c7c', $frontMatter->title);
		$this->assertSame('6a028a7b-d308-42ae-a167-939db7f467f3', $frontMatter->description);
		$this->assertTrue($frontMatter->isDraft);
		$this->assertNotNull($frontMatter->createdAt);
		$this->assertSame('2026-04-13', $frontMatter->createdAt->format('Y-m-d'));
		$this->assertNotNull($frontMatter->publishedAt);
		$this->assertSame('2026-05-13', $frontMatter->publishedAt->format('Y-m-d'));
		$this->assertNotNull($frontMatter->editedAt);
		$this->assertSame('2026-06-13', $frontMatter->editedAt->format('Y-m-d'));
	}

	public function test_fromDocument_shouldReturnExpectedResult_whenFormattedAsJson(): void {
		$result = FrontMatter::fromDocument(self::JSON.self::MARKDOWN);

		$this->assertSame(\strlen(self::JSON), $result->bytes);
		$frontMatter = $result->frontMatter;
		$this->assertSame('b3ad7488-6794-4d74-8db1-521c6b0d96e5', $frontMatter->title);
		$this->assertSame('e3c481de-cbd2-435c-b734-9181333b2202', $frontMatter->description);
		$this->assertTrue($frontMatter->isDraft);
		$this->assertNotNull($frontMatter->createdAt);
		$this->assertSame('2026-04-15', $frontMatter->createdAt->format('Y-m-d'));
		$this->assertNotNull($frontMatter->publishedAt);
		$this->assertSame('2026-05-15', $frontMatter->publishedAt->format('Y-m-d'));
		$this->assertNotNull($frontMatter->editedAt);
		$this->assertSame('2026-06-15', $frontMatter->editedAt->format('Y-m-d'));
	}

	public static function descriptionKeys(): iterable { // @phpstan-ignore missingType.iterableValue
		foreach (self::getAltKeysForFrontMatterProperty('description') as $key) {
			yield $key => [ $key ];
		}
	}

	#[DataProvider('descriptionKeys')]
	public function test_description_shouldReturnExpectedResult_whenAlternativeKeyUsed(string $key): void {
		$expectedResult = '9dbc68b0-1cfc-4749-9527-f5e6b4980b04';
		$sut = new FrontMatter([ $key => $expectedResult ]);

		$result = $sut->description;

		$this->assertSame($expectedResult, $result);
	}

	public static function draftKeys(): iterable { // @phpstan-ignore missingType.iterableValue
		foreach (self::getAltKeysForFrontMatterProperty('isDraft') as $key) {
			yield $key => [ $key ];
		}
	}

	#[DataProvider('draftKeys')]
	public function test_draft_shouldReturnExpectedResult_whenAlternativeKeyUsed(string $key): void {
		$expectedResult = true;
		$sut = new FrontMatter([ $key => $expectedResult ]);

		$result = $sut->isDraft;

		$this->assertSame($expectedResult, $result);
	}

	public static function createdAtKeys(): iterable { // @phpstan-ignore missingType.iterableValue
		foreach (self::getAltKeysForFrontMatterProperty('createdAt') as $key) {
			yield $key => [ $key ];
		}
	}

	#[DataProvider('createdAtKeys')]
	public function test_createdAt_shouldReturnExpectedResult_whenAlternativeKeyUsed(string $key): void {
		$ymdString = '2026-06-15';
		$expectedResult = DateTime::createFromFormat('Y-m-d', $ymdString); /** @var DateTime $expectedResult */
		$expectedResult->setTime(12, 0, 0, 0);
		$sut = new FrontMatter([ $key => $ymdString ]);

		$result = $sut->createdAt;

		$this->assertEquals($expectedResult, $result);
	}

	public static function publishedAtKeys(): iterable { // @phpstan-ignore missingType.iterableValue
		foreach (self::getAltKeysForFrontMatterProperty('publishedAt') as $key) {
			yield $key => [ $key ];
		}
	}

	#[DataProvider('publishedAtKeys')]
	public function test_publishedAt_shouldReturnExpectedResult_whenAlternativeKeyUsed(string $key): void {
		$ymdString = '2026-06-15';
		$expectedResult = DateTime::createFromFormat('Y-m-d', $ymdString); /** @var DateTime $expectedResult */
		$expectedResult->setTime(12, 0, 0, 0);
		$sut = new FrontMatter([ $key => $ymdString ]);

		$result = $sut->publishedAt;

		$this->assertEquals($expectedResult, $result);
	}

	public static function editedAtKeys(): iterable { // @phpstan-ignore missingType.iterableValue
		foreach (self::getAltKeysForFrontMatterProperty('editedAt') as $key) {
			yield $key => [ $key ];
		}
	}

	#[DataProvider('editedAtKeys')]
	public function test_editedAt_shouldReturnExpectedResult_whenAlternativeKeyUsed(string $key): void {
		$ymdString = '2026-06-15';
		$expectedResult = DateTime::createFromFormat('Y-m-d', $ymdString); /** @var DateTime $expectedResult */
		$expectedResult->setTime(12, 0, 0, 0);
		$sut = new FrontMatter([ $key => $ymdString ]);

		$result = $sut->editedAt;

		$this->assertEquals($expectedResult, $result);
	}

	/** @return iterable<string> */
	private static function getAltKeysForFrontMatterProperty(string $property): iterable {
		$reflector = new ReflectionClass(FrontMatter::class)->getProperty($property);
		foreach ($reflector->getAttributes(AlternativeKeys::class) as $attr) {
			yield from $attr->newInstance()->names;
		}
	}

}
