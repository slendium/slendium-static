<?php

namespace Slendium\SlendiumStaticTests\Base\Site;

use Exception;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\SlendiumStatic\Base\Site\Map;
use Slendium\SlendiumStatic\Site\Resource;
use Slendium\SlendiumStatic\Site\Uri;
use Slendium\SlendiumStaticTests\Site\Mocks\EmptyResource;
use Slendium\SlendiumStaticTests\Site\Mocks\PlainResource;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class MapTest extends TestCase {

	public static function containsCases(): iterable { // @phpstan-ignore missingType.iterableValue
		$index = new EmptyResource('/index.html');
		$map = new Map([ $index ]);
		yield [ $map, $index, true ];
		yield [ $map, Uri::fromString('/index.html'), true ];
		yield [ $map, new EmptyResource('/not-found.html'), false ];
		yield [ $map, Uri::fromString('/not-found.html'), false ];
	}

	#[DataProvider('containsCases')]
	public function test_contains_shouldReturnExpectedValue(Map $sut, Resource|Uri $argument, bool $expectedResult): void {
		$result = $sut->contains($argument);

		$this->assertSame($expectedResult, $result);
	}

	public function test_get_shouldReturnExpectedValue(): void {
		$index = new EmptyResource('/index.html');
		$sut = new Map([ $index ]);

		$result = $sut->get(Uri::fromString('/index.html'));

		$this->assertSame($index, $result);
	}

	public function test_insert_shouldInsertNewResources(): void {
		$sut = new Map([ ]);

		$sut->insert(new EmptyResource('/index.html'));
		$result = $sut->contains(Uri::fromString('/index.html'));

		$this->assertTrue($result);
	}

	public function test_insert_shouldThrow_whenResourceAlreadyContained(): void {
		// Arrange
		$sut = new Map([ new EmptyResource('/index.html') ]);

		// Assert
		$this->expectException(Exception::class);

		// Act
		$sut->insert(new EmptyResource('/index.html'));
	}

	public function test_overwrite_shouldOverwriteExistingResource(): void {
		$overwrittenContents = 'Overwritten contents';
		$index = new PlainResource('/index.html', 'Initial contents');
		$sut = new Map([ $index ]);

		$sut->overwrite(new PlainResource('/index.html', $overwrittenContents));
		$result = $sut->get(Uri::fromString('/index.html'))->generateContents();

		$this->assertSame($overwrittenContents, $result);
	}

	public function test_overwrite_shouldThrow_whenNotOverwritingAnything(): void {
		// Arrange
		$sut = new Map([ ]);

		// Assert
		$this->expectException(Exception::class);

		// Act
		$sut->overwrite(new EmptyResource('/index.html'));
	}

	public function test_delete_shouldDeleteResource_whenUriGiven(): void {
		$sut = new Map([ new EmptyResource('/index.html') ]);

		$sut->delete(Uri::fromString('/index.html'));

		$this->assertFalse($sut->contains(Uri::fromString('/index.html')));
	}

	public function test_delete_shouldDeleteResource_whenResourceGiven(): void {
		$index = new EmptyResource('/index.html');
		$sut = new Map([ $index ]);

		$sut->delete($index);

		$this->assertFalse($sut->contains($index));
	}

}
