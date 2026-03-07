<?php

namespace Slendium\SlendiumStatic\Source;

use Exception;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class SourceException extends Exception {

	public static function forAmbiguousResource(File $file): self {
		return new self("Ambiguous resource, `{$file->normalizedName}` can be created from multiple files");
	}

	public static function forUnnamedResource(File $file): self {
		return new self("Resource at `{$file->sourcePath}` should have a non-empty name");
	}

	public static function forOrphanedResource(File $file): self {
		$ancestorPath = ($file->directory->ancestor->sourcePath ?? '')
			."{$file->directory->name}.{$file->extension}";

		return new self("Resource at `{$file->sourcePath}` should have an ancestor at `$ancestorPath`");
	}

}
