<?php

namespace Slendium\SlendiumStatic\Base\Site\FileResource;

use Dom\HTMLDocument;
use Override;

use Slendium\SlendiumStatic\Content\Meta\FrontMatter;
use Slendium\SlendiumStatic\Content\Meta\FrontMatterHolder;
use Slendium\SlendiumStatic\Site\ContentBody;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class PageContentBody implements ContentBody, FrontMatterHolder {

	public function __construct(

		private readonly HTMLDocument $document,

		#[Override]
		public readonly FrontMatter $frontMatter,

	) { }

	#[Override]
	public function getBytes(): string {
		return $this->document->saveHtml();
	}

}
