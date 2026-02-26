<?php

namespace Slendium\SlendiumStatic\Common;

use Closure;
use Stringable;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class Iteration {

	private const ALWAYS_TRUE = static function() {
		return true;
	};

	/**
	 * @template TSourceKey
	 * @template TSourceValue
	 * @param iterable<TSourceKey,TSourceValue> $source
	 * @param Closure(TSourceValue,TSourceKey):bool $predicate
	 * @return ?TSourceValue
	 */
	public static function firstOrNull(iterable $source, Closure $predicate = self::ALWAYS_TRUE): mixed {
		foreach ($source as $key => $value) {
			if ($predicate($value, $key)) {
				return $value;
			}
		}
		return null;
	}

	/**
	 * @template TSourceKey
	 * @template TSourceValue
	 * @param iterable<TSourceKey,TSourceValue> $source
	 * @param Closure(TSourceValue,TSourceKey):bool $predicate
	 */
	public static function all(iterable $source, Closure $predicate): bool {
		foreach ($source as $key => $value) {
			if (!$predicate($value, $key)) {
				return false;
			}
		}
		return true;
	}

	/**
	 * @template TSourceKey
	 * @template TSourceValue
	 * @param iterable<TSourceKey,TSourceValue> $source
	 * @param Closure(TSourceValue,TSourceKey):bool $predicate
	 */
	public static function any(iterable $source, Closure $predicate = self::ALWAYS_TRUE): bool {
		foreach ($source as $key => $value) {
			if ($predicate($value, $key)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @template TSourceKey
	 * @template TSourceValue
	 * @param iterable<TSourceKey,TSourceValue> $source
	 * @param Closure(TSourceValue,TSourceKey):bool $predicate
	 * @return iterable<TSourceKey,TSourceValue>
	 */
	public static function filter(iterable $source, Closure $predicate): iterable {
		foreach ($source as $key => $value) {
			if ($predicate($value, $key)) {
				yield $key => $value;
			}
		}
	}

	/**
	 * @template TSourceKey
	 * @template TResult
	 * @param iterable<TSourceKey,mixed> $source
	 * @param class-string<TResult> $className
	 * @return iterable<TSourceKey,TResult>
	 */
	public static function filterType(iterable $source, string $className): iterable {
		foreach ($source as $key => $value) {
			if (\is_object($value) && \is_a($value, $className)) {
				yield $key => $value;
			}
		}
	}

	/**
	 * @template TSourceKey
	 * @template TSourceValue
	 * @template TTargetValue
	 * @param iterable<TSourceKey,TSourceValue> $source
	 * @param Closure(TSourceValue,TSourceKey):TTargetValue $transform
	 * @return iterable<TSourceKey,TTargetValue>
	 */
	public static function map(iterable $source, Closure $transform): iterable {
		foreach ($source as $sourceKey => $sourceValue) {
			yield $sourceKey => $transform($sourceValue, $sourceKey);
		}
	}

	/**
	 * @template TSourceKey
	 * @template TSourceValue
	 * @template TCarry
	 * @param iterable<TSourceKey,TSourceValue> $source
	 * @param Closure(TCarry,TSourceValue,TSourceKey):TCarry $reducer
	 * @param TCarry $initial
	 * @return TCarry
	 */
	public static function reduce(iterable $source, Closure $reducer, mixed $initial): mixed {
		$carry = $initial;
		foreach ($source as $key => $value) {
			$carry = $reducer($carry, $value, $key);
		}
		return $carry;
	}

	/** @param iterable<Stringable|string|float|int> $source */
	public static function implode(iterable $source, string $separator): string {
		$out = '';
		$first = true;
		foreach ($source as $value) {
			if (!$first) {
				$out .= $separator;
			}
			$out .= $value;
			$first = false;
		}
		return $out;
	}

	/**
	 * @template TSourceKey
	 * @template TSourceValue
	 * @template TResult
	 * @param iterable<TSourceKey,TSourceValue> $source
	 * @param Closure(TSourceValue,TSourceKey):iterable<TResult> $transform
	 * @return iterable<TResult>
	 */
	public static function flatten(iterable $source, Closure $transform): iterable {
		foreach ($source as $key => $value) {
			yield from $transform($value, $key);
		}
	}

	/**
	 * Groups values of the iterable according to a key selector.
	 *
	 * This grouping uses the same rules as PHP's native array, where a numeric string key will be
	 * cast to an int key.
	 *
	 * @template TSourceKey
	 * @template TSourceValue
	 * @template TGroupKey of string|int
	 * @param iterable<TSourceKey,TSourceValue> $source
	 * @param Closure(TSourceValue,TSourceKey):TGroupKey $getKey
	 * @return iterable<TGroupKey,iterable<TSourceValue>>
	 */
	public static function group(iterable $source, Closure $getKey): iterable {
		$grouped = [ ];
		foreach ($source as $sourceKey => $value) {
			$groupKey = $getKey($value, $sourceKey);
			if (!isset($grouped[$groupKey])) {
				$grouped[$groupKey] = [ ];
			}
			$grouped[$groupKey][] = $value;
		}

		yield from $grouped;
	}

	/**
	 * @template TKey
	 * @template TValue
	 * @param iterable<TKey,TValue> $source
	 * @param iterable<TKey,TValue> $addition
	 * @return iterable<TKey,TValue>
	 */
	public static function merge(iterable $source, iterable $addition): iterable {
		yield from $source;
		yield from $addition;
	}

	/**
	 * @template TSource
	 * @param iterable<TSource> $source
	 * @return list<TSource>
	 */
	public static function toList(iterable $source): array {
		$list = [ ];
		foreach ($source as $value) {
			$list[] = $value;
		}
		return $list;
	}

	/**
	 * @template TSourceKey of array-key
	 * @template TSourceValue
	 * @param iterable<TSourceKey,TSourceValue> $source
	 * @return array<TSourceKey,TSourceValue>
	 */
	public static function toArray(iterable $source): array {
		$array = [ ];
		foreach ($source as $key => $value) {
			$array[$key] = $value;
		}
		return $array;
	}

	private function __construct() { }

}
