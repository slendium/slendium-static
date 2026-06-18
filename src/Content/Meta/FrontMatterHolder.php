<?php

namespace Slendium\SlendiumStatic\Content\Meta;

/**
 * A piece of content that contains front matter.
 *
 * If the underlying data did not explicitly declare any front matter, the holder should pretend that
 * an empty front matter section was declared.
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
interface FrontMatterHolder {

	/** @since 1.0 */
	public FrontMatter $frontMatter { get; }

}
