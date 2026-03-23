<?php

namespace Slendium\SlendiumStaticTests\Base\Content\Fakes;

use Override;
use Dom\HTMLDocument;

use Slendium\SlendiumStatic\Base\Content\Summary;
use Slendium\SlendiumStatic\Content\Summarizer;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class FixedValueSummarizer implements Summarizer {

	public function __construct(

		/** @var non-empty-string */
		private readonly ?string $title,

		/** @var non-empty-string */
		private readonly ?string $description,

	) { }

	#[Override]
	public function summarize(HTMLDocument $document): Summary {
		return new Summary($this->title, $this->description);
	}

}
