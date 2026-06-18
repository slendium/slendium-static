<?php

namespace Slendium\SlendiumStatic\Site;

use Override;

use Slendium\SlendiumStatic\Site\ContentBody;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class ReadOnlyContentBody implements ContentBody {

	public function __construct(private readonly string $bytes) { }

	#[Override]
	public function getBytes(): string {
		return $this->bytes;
	}

}
