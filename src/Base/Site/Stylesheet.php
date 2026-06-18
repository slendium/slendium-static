<?php

namespace Slendium\SlendiumStatic\Base\Site;

use Closure;
use Exception;
use Override;

use Slendium\SlendiumStatic\Source\File;
use Slendium\SlendiumStatic\Site\ContentBody;
use Slendium\SlendiumStatic\Site\ReadOnlyContentBody;
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
	public function generateContents(): ContentBody|File|Exception {
		return \count($this->prepends) > 0
			? $this->generateModifiedContents()
			: (\is_string($this->source)
			? new ReadOnlyContentBody($this->source)
			: $this->source); // $this->source is File
	}

	/** @param non-empty-string $css */
	public function prepend(string $css): void {
		$this->prepends[] = $css;
	}

	private function generateModifiedContents(): ContentBody|Exception {
		$stylesheet = '';
		foreach (\array_reverse($this->prepends) as $css) {
			$stylesheet .= "$css\n";
		}
		$mainBody = $this->source instanceof File
			? $this->source->getContents()
			: $this->source;
		return $mainBody instanceof Exception
			? $mainBody
			: new ReadOnlyContentBody($stylesheet.$mainBody);
	}

}
