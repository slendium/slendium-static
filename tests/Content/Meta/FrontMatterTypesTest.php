<?php

namespace Slendium\SlendiumStaticTests\Content\Meta;

use DateTime;
use DateTimeInterface;
use DateTimeImmutable;
use Stringable;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use Slendium\SlendiumStatic\Content\Meta\FrontMatterTypes;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class FrontMatterTypesTest extends TestCase {

	public static function getBoolCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield 'bool(true)' => [ [ 'b' => true ], 'b', true ];
		yield 'bool(false)' => [ [ 'b' => false ], 'b', false ];
		yield 'no values' => [ [ ], 'b', null ];
		// for now, other truthy/falsy values are considered non-boolean as all supported front matter
		// formats have explicit boolean notations that must be used
		yield 'string(true)' => [ [ 'b' => 'true' ], 'b', null ];
		yield 'string(false)' => [ [ 'b' => 'false' ], 'b', null ];
		yield 'int(1)' => [ [ 'b' => 1 ], 'b', null ];
		yield 'int(0)' => [ [ 'b' => 0 ], 'b', null ];
		yield 'string(1)' => [ [ 'b' => '1' ], 'b', null ];
		yield 'string(0)' => [ [ 'b' => '0' ], 'b', null ];
		yield 'null' => [ [ 'b' => null ], 'b', null ];
	}

	/**
	 * @param non-empty-string $key
	 * @param array<mixed> $values
	 */
	#[DataProvider('getBoolCases')]
	public function test_getBool_shouldReturnExpectedResult(array $values, string $key, ?bool $expectedResult): void {
		$result = FrontMatterTypes::getBool($values, $key);

		$this->assertSame($expectedResult, $result);
	}

	public static function getStringCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield 'empty string' => [ [ 's' => '' ], 's', '' ];
		yield 'random guid' => [ [ 's' => '0c88a41f-929a-4a1c-94c4-e5c0ffc58a0f' ], 's', '0c88a41f-929a-4a1c-94c4-e5c0ffc58a0f' ];
		yield 'int(1)' => [ [ 's' => 1 ], 's', '1' ];
		yield 'negative-float' => [ [ 's' => -1.25 ], 's', '-1.25' ];
		yield 'int(0)' => [ [ 's' => 0 ], 's', '0' ];
		yield 'string(1)' => [ [ 's' => '1' ], 's', '1' ];
		yield 'no values' => [ [ ], 's', null ];
		yield 'null' => [ [ 's' => null ], 's', null ];
		yield 'Stringable' => [ [ 's' => self::asStringable('01eb6232-3338-476c-93ae-511eccbe6acd') ], 's', '01eb6232-3338-476c-93ae-511eccbe6acd' ];
		yield 'bool(true)' => [ [ 's' => true ], 's', null ];
		yield 'bool(false)' => [ [ 's' => false ], 's', null ];
	}

	private static function asStringable(string $input): Stringable {
		return new class($input) {
			public function __construct(private readonly string $value) { }
			public function __toString(): string { return $this->value; }
		};
	}

	/**
	 * @param non-empty-string $key
	 * @param array<mixed> $values
	 */
	#[DataProvider('getStringCases')]
	public function test_getString_shouldReturnExpectedResult(array $values, string $key, ?string $expectedResult): void {
		$result = FrontMatterTypes::getString($values, $key);

		$this->assertSame($expectedResult, $result);
	}

	public static function getDateCases(): iterable { // @phpstan-ignore missingType.iterableValue
		$now = new DateTimeImmutable;
		yield 'DateTimeInterface' => [ [ 'd' => $now ], 'd', $now ];
		yield 'random guid' => [ [ 'd' => '780b98d9-ba8c-4089-a3b1-44d49a533d36' ], 'd', null ];
		yield 'timestamp' => [ [ 'd' => 1781360771 ], 'd', null ];
		$isoString = '2025-01-01T12:00:00Z';
		$date1 = DateTimeImmutable::createFromFormat(DateTimeImmutable::ATOM, $isoString);
		yield 'iso-date' => [ [ 'd' => $isoString ], 'd', $date1 ];
		$ymdString = '2026-01-01';
		$date2 = DateTime::createFromFormat('Y-m-d', $ymdString); /** @var DateTime $date2 */
		$date2->setTime(12, 0, 0, 0);
		yield 'ymd-date' => [ [ 'd' => $ymdString ], 'd', $date2 ];
		yield 'no values' => [ [ ], 'd', null ];
	}

	/**
	 * @param non-empty-string $key
	 * @param array<mixed> $values
	 */
	#[DataProvider('getDateCases')]
	public function test_getDate_shouldReturnExpectedResult(array $values, string $key, ?DateTimeInterface $expectedResult): void {
		$result = FrontMatterTypes::getDate($values, $key);

		$this->assertEquals($expectedResult, $result);
	}

}
