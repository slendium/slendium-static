<?php

namespace Slendium\SlendiumStatic\Base\Builders;

use Closure;

use Slendium\Localization\Locale;

use Slendium\SlendiumStatic\Base\Content\ClosureTitleTemplate;
use Slendium\SlendiumStatic\Configs;
use Slendium\SlendiumStatic\Content\SectionProvider;
use Slendium\SlendiumStatic\Content\Summarizer;
use Slendium\SlendiumStatic\Content\TitleTemplate;
use Slendium\SlendiumStatic\Site\Uri;
use Slendium\SlendiumStatic\Source\Filesystem;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class ConfigsBuilder {

	/** @var array<non-empty-string,mixed> */
	private array $values = [ ];

	/** @since 1.0 */
	public function __construct() { }

	/**
	 * @since 1.0
	 * @return array<non-empty-string,mixed>
	 */
	public function build(): array {
		return $this->values;
	}

	/**
	 * @since 1.0
	 * @return $this
	 */
	public function setFilesystem(Filesystem $filesystem): self {
		$this->values[Configs::KEY_FILESYSTEM] = $filesystem;
		return $this;
	}

	/**
	 * @since 1.0
	 * @return $this
	 */
	public function setBaseSectionProvider(SectionProvider $provider): self {
		$this->values[Configs::KEY_BASE_SECTIONS] = $provider;
		return $this;
	}

	/**
	 * @since 1.0
	 * @param iterable<Locale|string> $locales
	 * @return $this
	 */
	public function setLocales(iterable $locales): self {
		$this->values[Configs::KEY_LOCALES] = $locales;
		return $this;
	}

	/**
	 * @since 1.0
	 * @return $this
	 */
	public function setSummarizer(Summarizer $summarizer): self {
		$this->values[Configs::KEY_SUMMARIZER] = $summarizer;
		return $this;
	}

	/**
	 * @since 1.0
	 * @return $this
	 */
	public function setTitleTemplate(TitleTemplate|Closure $template): self {
		$this->values[Configs::KEY_TITLE_TEMPLATE] = $template instanceof Closure
			? new ClosureTitleTemplate($template)
			: $template;
		return $this;
	}

	/**
	 * @since 1.0
	 * @return $this
	 */
	public function setBaseUri(Uri|string $uri): self {
		$this->values[Configs::KEY_BASE_URI] = \is_string($uri)
			? Uri::fromString($uri)
			: $uri;
		return $this;
	}

}
