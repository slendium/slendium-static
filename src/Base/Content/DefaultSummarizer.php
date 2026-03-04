<?php

namespace Slendium\SlendiumStatic\Base\Content;

use Override;
use Dom\HTMLDocument;

use Slendium\SlendiumStatic\Content\Summarizer;
use Slendium\SlendiumStatic\Base\Content\Summary;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class DefaultSummarizer implements Summarizer {

	private const TITLE_SELECTORS = [ 'meta[name=title]', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ];

	/** @return ?non-empty-string */
	private static function findTitle(HTMLDocument $document): ?string {
		foreach (self::TITLE_SELECTORS as $selector) {
			if (($el = $document->querySelector($selector)) === null) {
				continue;
			}
			$title = $el->textContent;

			if ($el->localName === 'meta') {
				$title = $el->getAttribute('content');
				$el->remove();
			}

			if ($title !== '') {
				return $title;
			}
		}
		return null;
	}

	/** @return ?non-empty-string */
	private static function findDescription(HTMLDocument $document): ?string {
		$el = $document->querySelector('meta[name=description]');
		if ($el === null) {
			return null;
		}

		$description = $el->getAttribute('content');
		if ($description !== '') {
			$el->remove();
			return $description;
		}
		return null;
	}

	#[Override]
	public function summarize(HTMLDocument $document): Summary {
		return new Summary(self::findTitle($document), self::findDescription($document));
	}

}
