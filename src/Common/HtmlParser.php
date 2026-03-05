<?php

namespace Slendium\SlendiumStatic\Common;

use LibXMLError;
use Dom\HTMLDocument;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class HtmlParser {

	/** @return HTMLDocument|array<LibXMLError> */
	public static function parse(string $html): HTMLDocument|array {
		$previousUseErrors = \libxml_use_internal_errors(true);
		\libxml_clear_errors();
		$document = HTMLDocument::createFromString($html);
		$errors = \libxml_get_errors();
		\libxml_clear_errors();
		\libxml_use_internal_errors($previousUseErrors);
		return \count($errors) > 0
			? $errors
			: $document;
	}

	private function __construct() { }

}
