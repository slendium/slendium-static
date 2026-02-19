<?php

namespace Slendium\SlendiumStatic\Source;

use Exception;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class SourceException extends Exception {

	/** @param non-empty-list<File> $files */
	public static function forAmbiguousResource(array $files): self {
		return new self(
			"Ambiguous resource, `{$files[0]->normalizedName}` could be created from multiple source files: "
				.\implode(', ', \array_map(fn($file) => $file->sourcePath, $files))
		);
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
