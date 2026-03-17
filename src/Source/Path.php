<?php

namespace Slendium\SlendiumStatic\Source;

use Slendium\SlendiumStatic\Common\Iteration;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class Path {

	/** @since 1.0 */
	public static function fromString(string $path): self {
		$parts = \trim($path, '/')
			|> (fn($x) => $x === '' ? [ ] : \explode('/', $x))
			|> (fn($x) => \array_filter($x, static fn($v) => $v !== ''));

		return \array_find($parts, static fn($v) => $v === '.' || $v === '..') !== null
			? throw new SourceException('Path contained unexpected relative part')
			: new self(\array_values($parts));
	}

	/** @since 1.0 */
	public function __construct(

		/**
		 * @since 1.0
		 * @var list<non-empty-string>
		 */
		public readonly array $parts,

	) { }

	/** @return non-empty-string */
	public function __toString() {
		return '/'.\implode('/', $this->parts);
	}

}
