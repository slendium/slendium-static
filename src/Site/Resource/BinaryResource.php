<?php

namespace Slendium\SlendiumStatic\Site\Resource;

use Exception;
use Override;

use Slendium\SlendiumStatic\Site\Resource;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class BinaryResource extends Resource {

	#[Override]
	public function writeToFile(string $path): Exception {
		return new Exception('Not implemented');
	}

}
