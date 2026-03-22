<?php

namespace Slendium\SlendiumStatic\Source;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class Path {

	/** @since 1.0 */
	const UNIX_SEPARATOR = '/';

	/** @since 1.0 */
	const WINDOWS_SEPARATOR = '\\';

	/** @since 1.0 */
	public static function fromUnix(string $path): self {
		return self::fromString($path, self::UNIX_SEPARATOR);
	}

	/** @since 1.0 */
	public static function fromWindows(string $path): self {
		return self::fromString($path, self::WINDOWS_SEPARATOR);
	}

	/** @param non-empty-string $separator */
	private static function fromString(string $path, string $separator): self {
		$parts = \trim($path, $separator)
			|> (fn($x) => $x === '' ? [ ] : \explode($separator, $x))
			|> (fn($x) => \array_filter($x, static fn($v) => $v !== ''));

		return new self(\array_values($parts));
	}

	/** @since 1.0 */
	public function __construct(

		/**
		 * @since 1.0
		 * @var list<non-empty-string>
		 */
		public readonly array $parts,

	) {
		if (\array_find($parts, static fn($v) => $v === '.' || $v === '..') !== null) {
			throw new SourceException('Path contained unexpected relative part');
		}
	}

	/** @return non-empty-string */
	public function __toString() {
		return \DIRECTORY_SEPARATOR.\implode(\DIRECTORY_SEPARATOR, $this->parts);
	}

}
