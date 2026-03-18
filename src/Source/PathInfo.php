<?php

namespace Slendium\SlendiumStatic\Source;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class PathInfo {

	public static function getExtension(Path $path): string {
		$basename = self::getBasename($path);
		$dotPos = \strrpos($basename, '.');
		return $dotPos !== false
			? \substr($basename, $dotPos + 1)
			: '';
	}

	public static function getBasename(Path $path, string $suffix = ''): string {
		$basename = $path->parts[\count($path->parts) - 1]
			?? '';

		return $suffix !== '' && \str_ends_with($basename, $suffix)
			? \substr($basename, 0, -\strlen($suffix))
			: $basename;
	}

	public static function getNormalizedName(Path $path): string {
		return match(self::getExtension($path)) {
			'htm' => self::getBasename($path, '.htm').'.html',
			'md' => self::getBasename($path, '.md').'.html',
			'jpeg' => self::getBasename($path, '.jpeg').'.jpg',
			default => self::getBasename($path)
		};
	}

	private function __construct() { }

}
