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
	const KEY_LOCALES = 'locales';

	/** @since 1.0 */
	const KEY_TITLE_TEMPLATE = 'titleTemplate';

	/** @since 1.0 */
	const KEY_SUMMARIZER = 'summarizer';

	/**
	 * @since 1.0
	 * @param ConfigsMap $configs
	 */
	public static function getLocales(ArrayAccess|array $configs): ILocaleList {
		if (isset($configs[self::KEY_LOCALES]) && $configs[self::KEY_LOCALES] instanceof LocaleList) {
			return $configs[self::KEY_LOCALES];
		}

		return (self::getIterable($configs, self::KEY_LOCALES) ?? [ ])
			|> (fn($x) => Iteration::map($x, self::parseLocale(...)))
			|> (fn($x) => Iteration::filterType($x, ILocale::class))
			|> (fn($x) => new LocaleList($x));
	}

	/**
	 * @since 1.0
	 * @param ConfigsMap $configs
	 */
	public static function getSummarizer(ArrayAccess|array $configs): Summarizer {
		return isset($configs[self::KEY_SUMMARIZER]) && $configs[self::KEY_SUMMARIZER] instanceof Summarizer
			? $configs[self::KEY_SUMMARIZER]
			: new DefaultSummarizer;
	}

	/**
	 * @since 1.0
	 * @param ConfigsMap $configs
	 */
	public static function getTitleTemplate(ArrayAccess|array $configs): TitleTemplate {
		return isset($configs[self::KEY_TITLE_TEMPLATE]) && $configs[self::KEY_TITLE_TEMPLATE] instanceof TitleTemplate
			? $configs[self::KEY_TITLE_TEMPLATE]
			: new ClosureTitleTemplate(static fn($baseTitle) => $baseTitle);
	}

	private static function parseLocale(mixed $value): ?ILocale {
		return $value instanceof ILocale
			? $value
			: (\is_string($value)
			? Locale::parse($value)
			: null);
	}

	/**
	 * @param ConfigsMap $configs
	 * @return iterable<mixed>
	 */
	private static function getIterable(ArrayAccess|array $configs, string $key): ?iterable {
		return isset($configs[$key]) && \is_iterable($configs[$key])
			? $configs[$key]
			: null;
	}

	private function __construct() { }

}
