<?php

namespace Slendium\SlendiumStatic\Content\Meta;

use DateTime;
use DateTimeInterface;
use DateTimeImmutable;
use Stringable;

/**
 * Type conversions for front matter values.
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class FrontMatterTypes {

	/**
	 * @since 1.0
	 * @param array<mixed> $values
	 * @param non-empty-string $key
	 */
	public static function getBool(array $values, string $key): ?bool {
		return isset($values[$key]) && \is_bool($values[$key])
			? $values[$key]
			: null;
	}

	/**
	 * @since 1.0
	 * @param array<mixed> $values
	 * @param non-empty-string $key
	 */
	public static function getString(array $values, string $key): ?string {
		return isset($values[$key]) && self::isStringifiable($values[$key])
			? (string)$values[$key]
			: null;
	}

	/**
	 * @since 1.0
	 * @param array<mixed> $values
	 * @param non-empty-string $key
	 */
	public static function getDate(array $values, string $key): ?DateTimeInterface {
		if (!isset($values[$key])) {
			return null;
		}

		if ($values[$key] instanceof DateTimeInterface) {
			return $values[$key];
		}

		if (self::isStringifiable($values[$key])) {
			$ymd = DateTime::createFromFormat('Y-m-d', (string)$values[$key]);
			if ($ymd !== false) {
				return DateTimeImmutable::createFromMutable($ymd->setTime(12, 0, 0, 0));
			}

			$atom = DateTimeImmutable::createFromFormat(DateTimeImmutable::ATOM, (string)$values[$key]);
			if ($atom !== false) {
				return $atom;
			}
		}

		return null;
	}

	/** @phpstan-assert-if-true Stringable|string|float|int $value */
	private static function isStringifiable(mixed $value): bool {
		return \is_string($value) || \is_numeric($value) || $value instanceof Stringable;
	}

	private function __construct() { }

}
