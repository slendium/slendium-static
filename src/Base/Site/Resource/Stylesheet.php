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
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class Stylesheet implements Resource {

	/** @var list<non-empty-string> */
	private array $prepends = [ ];

	/**
	 * @since 1.0
	 * @param non-empty-string $css
	 */
	public static function fromCss(Uri $uri, string $css): self {
		return new self($uri, $css);
	}

	/** @internal */
	public static function fromFile(File $file): self {
		return new self(self::getUriFromFile($file), $file);
	}

	private static function getUriFromFile(File $file): Uri {
		$current = $file->directory;
		$path = [ $file->normalizedName ];
		while ($current->ancestor !== null) {
			$path[] = $current->name;
			$current = $current->ancestor;
		}

		return new Uri(\array_reverse($path)); // @phpstan-ignore argument.type (wont contain empty strings)
	}

	private function __construct(

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

	/**
	 * @since 1.0
	 * @param non-empty-string $css
	 */
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
