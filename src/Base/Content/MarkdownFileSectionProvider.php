<?php

namespace Slendium\SlendiumStatic\Base\Content;

use Override;

use Parsedown;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class MarkdownFileSectionProvider extends HtmlFileSectionProvider {

	private static Parsedown $parserInstance;

	private static function getParserInstance(): Parsedown {
		return self::$parserInstance ??= new Parsedown;
	}

	public function __construct(string $markdown) {
		parent::__construct(self::getParserInstance()->text($markdown)); // @phpstan-ignore argument.type (Parsedown has no type hints)
	}

}
