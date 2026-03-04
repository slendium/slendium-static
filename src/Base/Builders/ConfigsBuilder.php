<?php

namespace Slendium\SlendiumStatic\Base\Builders;

use Closure;

use Slendium\SlendiumStatic\Base\Content\ClosureTitleTemplate;
use Slendium\SlendiumStatic\Configs;
use Slendium\SlendiumStatic\Content\Summarizer;
use Slendium\SlendiumStatic\Content\TitleTemplate;

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

}
