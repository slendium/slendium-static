<?php

namespace Slendium\SlendiumStatic\Site;

use Slendium\SlendiumStatic\Common\Iteration;

/**
 * Methods for checking the relationships between {@see Uri}'s.
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class UriRelations {

	/** @since 1.0 */
	public static function isDescendantOf(Uri $ancestor, Uri $subject): bool {
		return \count($ancestor->path) < \count($subject->path)
			? Iteration::all($ancestor->path, fn($part, $i) => $subject->path[$i] === $part)
			: false;
	}

	/** @since 1.0 */
	public static function isSiblingOf(Uri $reference, Uri $subject): bool {
		if (\count($reference->path) !== \count($subject->path)
			|| (string)$reference === (string)$subject
		) {
			return false;
		}

		foreach ($reference->path as $i => $part) {
			if ($subject->path[$i] !== $part && $i !== \count($reference->path) -1) {
				return false;
			}
		}
		return true;
	}

	private function __construct() { }

}
