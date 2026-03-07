<?php

namespace Slendium\SlendiumStatic\Base\Content;

use Exception;
use LibXMLError;

use Slendium\SlendiumStatic\Common\Iteration;
use Slendium\SlendiumStatic\Content\SectionNames;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class ContentException extends Exception {

	/** @since 1.0 */
	public static function forMissingSection(string $name): self {
		return new self("Expected a section with the name `$name`");
	}

	/** @since 1.0 */
	public static function forNestedSectionDefinitions(): self {
		return new self('Section definitions may not contain other section definitions');
	}

	/** @since 1.0 */
	public static function forSectionWithoutName(): self {
		return new self('Expected section to have a name');
	}

	/** @since 1.0 */
	public static function forDuplicateSection(string $name): self {
		return new self($name === SectionNames::MAIN
			? 'Unexpected duplicate section `main`, ensure that the implicit main content is empty or remove the duplicate explicit section(s)'
			: "Unexpected duplicate section `$name`"
		);
	}

	/**
	 * @since 1.0
	 * @param array<LibXMLError> $errors
	 */
	public static function forLibXmlErrors(array $errors): self {
		$errorMessages = $errors
			|> (fn($x) => Iteration::map($x, static fn($err) => "[{$err->code}] {$err->message}"))
			|> Iteration::toList(...)
			|> (fn($x) => Iteration::implode($x, ', '));

		$errorText = \count($errors) === 1
			? 'An error'
			: 'Multiple errors';
		return new self("$errorText occurred while parsing HTML: {$errorMessages}");
	}

	/** @since 1.0 */
	public static function forMissingTitle(): self {
		return new self('Expected document to have a title');
	}

}
