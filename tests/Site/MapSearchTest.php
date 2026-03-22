<?php

namespace Slendium\SlendiumStaticTests\Site;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\SlendiumStatic\Base\Site\Map;
use Slendium\SlendiumStatic\Common\Iteration;
use Slendium\SlendiumStatic\Site\MapSearch;
use Slendium\SlendiumStatic\Site\Uri;
use Slendium\SlendiumStaticTests\Site\Mocks\EmptyResource;
use Slendium\SlendiumStaticTests\Site\Mocks\PlainResource;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class MapSearchTest extends TestCase {

	private static function MapFixture(): Map {
		return new Map([
			new EmptyResource(Uri::fromString('/index.html')),
			new EmptyResource(Uri::fromString('/contact.html')),
			new EmptyResource(Uri::fromString('/blog.html')),
			new EmptyResource(Uri::fromString('/blog/2025.html')),
			new EmptyResource(Uri::fromString('/blog/2025/article1.html')),
			new EmptyResource(Uri::fromString('/blog/2026.html')),
			new EmptyResource(Uri::fromString('/blog/2026/article1.html')),
		]);
	}

	public static function findDescendantsCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ self::MapFixture(), Uri::fromString('/blog/2026.html'), [ ] ];
		yield [ self::MapFixture(), Uri::fromString('/blog/2026/--------'), [ ] ];
		yield [ self::MapFixture(), Uri::fromString('/blog/2026'), [ '/blog/2026/article1.html' ] ];
		yield [ self::MapFixture(), Uri::fromString('/blog'), [
			'/blog/2025.html',
			'/blog/2025/article1.html',
			'/blog/2026.html',
			'/blog/2026/article1.html',
		] ];
		yield [ self::MapFixture(), Uri::fromString('/'), [
			'/index.html',
			'/contact.html',
			'/blog.html',
			'/blog/2025.html',
			'/blog/2025/article1.html',
			'/blog/2026.html',
			'/blog/2026/article1.html',
		] ];
	}

	/** @param list<non-empty-string> $expectedResult */
	#[DataProvider('findDescendantsCases')]
	public function test_findDescendants_shouldReturnExpectedDescendants(Map $map, Uri $uri, array $expectedResult): void {
		$result = MapSearch::findDescendants($map, $uri)
			|> (fn($x) => Iteration::map($x, static fn($v) => (string)$v->uri))
			|> Iteration::toList(...);

		$this->assertEqualsCanonicalizing($expectedResult, $result);
	}

	public static function findSiblingsCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ self::MapFixture(), Uri::fromString('/index.html'), [ '/contact.html', '/blog.html' ] ];
		yield [ self::MapFixture(), Uri::fromString('/'), [ ] ];
		yield [ self::MapFixture(), Uri::fromString('/blog/2025.html'), [ '/blog/2026.html' ] ];
		yield [ self::MapFixture(), Uri::fromString('/blog/2025/article1.html'), [ ] ];
	}

	/** @param list<non-empty-string> $expectedResult */
	#[DataProvider('findSiblingsCases')]
	public function test_findSiblings_shouldReturnExpectedSiblings(Map $map, Uri $uri, array $expectedResult): void {
		$result = MapSearch::findSiblings($map, $uri)
			|> (fn($x) => Iteration::map($x, static fn($v) => (string)$v->uri))
			|> Iteration::toList(...);

		$this->assertEqualsCanonicalizing($expectedResult, $result);
	}

	public function test_getResourceOfType_shouldReturnValueDependingOnTypeMatch(): void {
		$sut = new Map([
			new EmptyResource(Uri::fromString('/a')),
			new PlainResource(Uri::fromString('/b'), ''),
		]);

		$expectValue = MapSearch::getResourceOfType($sut, Uri::fromString('/b'), PlainResource::class);
		$expectNoMatch = MapSearch::getResourceOfType($sut, Uri::fromString('/b'), EmptyResource::class);
		$expectNotFound = MapSearch::getResourceOfType($sut, Uri::fromString('/c'), PlainResource::class);

		$this->assertNotNull($expectValue);
		$this->assertNull($expectNoMatch);
		$this->assertNull($expectNotFound);
	}

}
