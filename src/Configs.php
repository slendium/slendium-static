<?php

namespace Slendium\SlendiumStatic;

use ArrayAccess;

use Slendium\Localization\Base\Locale;
use Slendium\Localization\Base\LocaleList;
use Slendium\Localization\Locale as ILocale;
use Slendium\Localization\LocaleList as ILocaleList;

use Slendium\SlendiumStatic\Base\Content\ClosureTitleTemplate;
use Slendium\SlendiumStatic\Base\Content\DefaultSummarizer;
use Slendium\SlendiumStatic\Common\Iteration;
use Slendium\SlendiumStatic\Content\Summarizer;
use Slendium\SlendiumStatic\Content\TitleTemplate;

/**
 * @since 1.0
 * @phpstan-type ConfigsMap ArrayAccess<non-empty-string,mixed>|array<non-empty-string,mixed>
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class Configs {

	/** @since 1.0 */
	const KEY_TITLE_TEMPLATE = 'titleTemplate';

	/**
	 * @since 1.0
	 * @param ConfigsMap $configs
	 */
	public static function getTitleTemplate(ArrayAccess|array $configs): TitleTemplate {
		return isset($configs[self::KEY_TITLE_TEMPLATE]) && $configs[self::KEY_TITLE_TEMPLATE] instanceof TitleTemplate
			? $configs[self::KEY_TITLE_TEMPLATE]
			: new ClosureTitleTemplate(static fn($baseTitle) => $baseTitle);
	}

	private function __construct() { }

}
