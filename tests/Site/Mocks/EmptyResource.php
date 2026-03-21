<?php

namespace Slendium\SlendiumStaticTests\Site\Mocks;

use Override;

use Slendium\SlendiumStatic\Site\Resource;
use Slendium\SlendiumStatic\Site\Uri;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class EmptyResource implements Resource {

	#[Override]
	public readonly Uri $uri;

	public function __construct(Uri|string $uri) {
		$this->uri = \is_string($uri)
			? Uri::fromString($uri)
			: $uri;
	}

	#[Override]
	public function generateContents(): string {
		return '';
	}

}
