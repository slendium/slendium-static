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
use Slendium\SlendiumStatic\Content\SectionProvider;
use Slendium\SlendiumStatic\Content\Summarizer;
use Slendium\SlendiumStatic\Content\TitleTemplate;
use Slendium\SlendiumStatic\Source\Filesystem;
use Slendium\SlendiumStatic\Source\RealFilesystem;

/**
 * @since 1.0
 * @phpstan-type ConfigsMap ArrayAccess<non-empty-string,mixed>|array<non-empty-string,mixed>
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class Configs {

	/** @since 1.0 */
	const KEY_FILESYSTEM = 'filesystem';

	/** @since 1.0 */
	const KEY_BASE_SECTIONS = 'baseSections';

	/** @since 1.0 */
	const KEY_LOCALES = 'locales';

	/** @since 1.0 */
	const KEY_TITLE_TEMPLATE = 'titleTemplate';

	/** @since 1.0 */
	const KEY_SUMMARIZER = 'summarizer';

	/**
	 * @since 1.0
	 * @see self::KEY_FILESYSTEM
	 * @param ConfigsMap $configs
	 */
	public static function getFilesystem(ArrayAccess|array $configs): Filesystem {
		return isset($configs[self::KEY_FILESYSTEM]) && $configs[self::KEY_FILESYSTEM] instanceof Filesystem
			? $configs[self::KEY_FILESYSTEM]
			: new RealFilesystem;
	}

	/**
	 * @since 1.0
	 * @see self::KEY_BASE_SECTIONS
	 * @param ConfigsMap $configs
	 */
	public static function getBaseSectionProvider(ArrayAccess|array $configs): ?SectionProvider {
		return isset($configs[self::KEY_BASE_SECTIONS]) && $configs[self::KEY_BASE_SECTIONS] instanceof SectionProvider
			? $configs[self::KEY_BASE_SECTIONS]
			: null;
	}

	/**
	 * @since 1.0
	 * @param ConfigsMap $configs
	 */
	public static function getLocales(ArrayAccess|array $configs): ILocaleList {
		if (isset($configs[self::KEY_LOCALES]) && $configs[self::KEY_LOCALES] instanceof ILocaleList) {
			return $configs[self::KEY_LOCALES];
		}

		return (self::getIterable($configs, self::KEY_LOCALES) ?? [ ])
			|> (fn($x) => Iteration::map($x, Locale::fromMixed(...)))
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
