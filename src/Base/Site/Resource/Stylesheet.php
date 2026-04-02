<?php

namespace Slendium\SlendiumStatic\Base\Site\Resource;

use Closure;
use Exception;
use Override;

use Slendium\SlendiumStatic\Source\Copyable;
use Slendium\SlendiumStatic\Source\File;
use Slendium\SlendiumStatic\Site\Resource;
use Slendium\SlendiumStatic\Site\Uri;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class Stylesheet implements Resource {

	/** @var list<non-empty-string> */
	private array $prepends = [ ];

	public function __construct(

		#[Override]
		public readonly Uri $uri,

		/** @var File|non-empty-string */
		private readonly File|string $source,

	) { }

	#[Override]
	public function generateContents(): Copyable|Exception|string {
		return \count($this->prepends) > 0
			? $this->generateModifiedContents()
			: $this->source;
	}

	/** @param non-empty-string $css */
	public function prepend(string $css): void {
		$this->prepends[] = $css;
	}

	private function generateModifiedContents(): Exception|string {
		$stylesheet = '';
		foreach (\array_reverse($this->prepends) as $css) {
			$stylesheet .= "$css\n";
		}
		$mainBody = $this->source instanceof File
			? $this->source->getContents()
			: $this->source;
		return $mainBody instanceof Exception
			? $mainBody
			: ($stylesheet.$mainBody);
	}

}
