<?php

namespace Slendium\SlendiumStatic\Source;

use Exception;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class SourceException extends Exception {

	/** @param list<string> $paths */
	public static function forAmbiguousResource(string $uri, array $paths): Exception {
		return new self("Ambiguous resource, `$uri` can be created from multiple files: ".\implode(', ', $paths));
	}

	public static function forUnnamedResource(File $file): Exception {
		return new self("Resource at `{$file->sourcePath}` should have a non-empty name");
	}

	//~ public static function forOrphanedResource(File $file): self {
		//~ $ancestorPath = ($file->directory->ancestor->sourcePath ?? '')
			//~ ."{$file->directory->name}.{$file->extension}";

		//~ return new self("Resource at `{$file->sourcePath}` should have an ancestor at `$ancestorPath`");
	//~ }

}
