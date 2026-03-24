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
	public static function fromString(string $path): self {
		return \trim($path)
			|> (static fn($x) => \trim($x, self::UNIX_SEPARATOR.self::WINDOWS_SEPARATOR))
			|> (static fn($x) => \str_replace(self::WINDOWS_SEPARATOR, self::UNIX_SEPARATOR, $x))
			|> (static fn($x) => $x === '' ? [ ] : \explode(self::UNIX_SEPARATOR, $x))
			|> (static fn($x) => \array_filter($x, static fn($v) => $v !== ''))
			|> (static fn($x) => new self(\array_values($x)));
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
