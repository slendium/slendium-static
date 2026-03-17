<?php

namespace Slendium\SlendiumStatic\Site;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class Uri {

	/** @since 1.0 */
	public static function fromString(string $value): self {
		$pathEnd = \strpos($value, '?');
		if ($pathEnd === false) {
			$pathEnd = \strpos($value, '#');
		}
		if ($pathEnd === false) {
			$pathEnd = null;
		}

		return \substr($value, 0, $pathEnd)
			|> (fn($x) => \trim($x, '/'))
			|> (fn($x) => $x === '' ? [ ] : \explode('/', $x))
			|> (fn($x) => new self($x));
	}

	/** @internal */
	public function __construct(

		/**
		 * @since 1.0
		 * @var list<string>
		 */
		public readonly array $path,

	) { }

	/** @return non-empty-string */
	public function __toString(): string {
		return '/'.\implode('/', $this->path);
	}

}
