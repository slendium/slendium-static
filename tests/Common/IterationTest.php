<?php

namespace Slendium\SlendiumStaticTests\Common;

use Closure;
use DateTime;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\SlendiumStatic\Common\Iteration;

class IterationTest extends TestCase {

	public static function firstOrNullCases(): iterable {
		yield [ [ 0, 1, 2, 3 ], 0 ];
		yield [ [ ], null ];
	}

	#[DataProvider('firstOrNullCases')]
	public function test_firstOrNull_returnsExpectedItem(iterable $source, mixed $expectedResult): void {
		$result = Iteration::firstOrNull($source);

		$this->assertSame($expectedResult, $result);
	}

	public static function firstOrNullPredicateCases(): iterable {
		yield [ [ 0, 1, 2, 3 ], fn($v) => $v === 1, 1 ];
		yield [ [ 0, 1, 2, 3 ], fn($v) => $v === 5, null ];
		yield [ [ ], fn($v) => $v === 2, null ];
	}

	#[DataProvider('firstOrNullPredicateCases')]
	public function test_firstOrNull_returnsExpectedItem_whenPredicateGiven(iterable $source, Closure $predicate, mixed $expectedResult): void {
		$result = Iteration::firstOrNull($source, $predicate);

		$this->assertSame($expectedResult, $result);
	}

	public static function allCases(): iterable {
		yield [ [ 0, 1, 2, 3, 4 ], fn($v) => \is_int($v), true ];
		yield [ [ ], fn($v) => \is_int($v), true ];
		yield [ [ 0, 1, '2', 3, 4 ], fn($v) => \is_int($v), false ];
	}

	#[DataProvider('allCases')]
	public function test_all_returnsExpectedValue(iterable $source, Closure $predicate, bool $expectedResult): void {
		$result = Iteration::all($source, $predicate);

		$this->assertSame($expectedResult, $result);
	}

	public static function anyCases(): iterable {
		yield [ [ 1, 2, 3 ], true ];
		yield [ [ ], false ];
	}

	#[DataProvider('anyCases')]
	public function test_any_returnsExpectedValue_whenPredicateNotGiven(iterable $source, bool $expectedResult): void {
		$result = Iteration::any($source);

		$this->assertSame($expectedResult, $result);
	}

	public static function anyPredicateCases(): iterable {
		yield [ [ null, 0, 1, 2 ], fn($v) => \is_int($v), true ];
		yield [ [ ], fn($v) => \is_int($v), false ];
		yield [ [ 0, 1, 2, 3, 4 ], fn($v) => \is_string($v), false ];
	}

	#[DataProvider('anyPredicateCases')]
	public function test_any_returnsExpectedValue_whenPredicateGiven(iterable $source, Closure $predicate, bool $expectedResult): void {
		$result = Iteration::any($source, $predicate);

		$this->assertSame($expectedResult, $result);
	}

	public function test_filter_returnsFilteredIterable(): void {
		$values = [ '0', 0, 1, 2, 3, 'test', null, true, 4, (object)[] ];
		$expectedResult = [ 0, 1, 2, 3, 4 ];

		$result = Iteration::filter($values, fn($v) => \is_int($v))
			|> Iteration::toList(...);

		$this->assertSame($expectedResult, $result);
	}

	public function test_filterType_returnsFilteredIterable(): void {
		$date = new DateTime;
		$stdClass1 = (object)[ ];
		$stdClass2 = (object)[ ];
		$values = [ $date, $stdClass1, $stdClass2 ];
		$expectedValues = [ $date ];

		$result = Iteration::filterType($values, DateTime::class)
			|> Iteration::toList(...);

		$this->assertSame($expectedValues, $result);
	}

	public function test_map_returnsMappedIterable(): void {
		$values = [ 0, 1, 2, 3 ];
		$expectedResult = [ 0, 2, 4, 6 ];

		$result = Iteration::map($values, fn($v) => $v*2)
			|> Iteration::toList(...);

		$this->assertSame($expectedResult, $result);
	}

	public function test_reduce_returnsReducedValue(): void {
		$values = [ 2, 3, 4 ];
		$expectedResult = 9;

		$result = Iteration::reduce($values, fn($carry, $value) => $carry + $value, 0);

		$this->assertSame($expectedResult, $result);
	}

	public function test_flatten_returnsFlattenedIterable(): void {
		$values = [ 1, 2, 3 ];
		$expectedResult = [ 1, 2, 2, 3, 3, 3 ];

		$result = Iteration::flatten($values, fn($v) => \array_pad([ ], $v, $v))
			|> Iteration::toList(...);

		$this->assertSame($expectedResult, $result);
	}

	public function test_group_returnsGroupedIterable(): void {
		$itemA1 = [ 'groupA', 1 ];
		$itemA2 = [ 'groupA', 2 ];
		$itemB1 = [ 'groupB', 3 ];
		$values = [ $itemA1, $itemA2, $itemB1 ];
		$expectedResult = [ 'groupA' => [ $itemA1, $itemA2 ], 'groupB' => [ $itemB1 ] ];

		$result = Iteration::group($values, fn($v) => $v[0])
			|> Iteration::toArray(...);

		$this->assertSame($expectedResult, $result);
	}

	public function test_merge_returnsMergedIterable(): void {
		$list1 = [ 0, 1, 2, 3 ];
		$list2 = [ 4, 5, 6, 7 ];

		$result = Iteration::merge($list1, $list2)
			|> Iteration::toList(...);

		$this->assertSame([ ...$list1, ...$list2 ], $result);
	}

}
