<?php

namespace Slendium\SlendiumStatic\Content;

use NoDiscard;
use Dom\HTMLDocument;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
interface Summarizer {

	/**
	 * Creates a summary based on the contents of the given document.
	 *
	 * If the summary is based on `<meta name=title>` or `<meta name=description>`, it is recommended
	 * that those elements are removed from the document. They will be re-added by the site generator
	 * in the appropriate places. Never remove the `<title>` element.
	 *
	 * @since 1.0
	 */
	#[NoDiscard]
	public function summarize(HTMLDocument $document): Summary;

}
