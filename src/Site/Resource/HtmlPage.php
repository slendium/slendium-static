<?php

namespace Slendium\SlendiumStatic\Site\Resource;

use Exception;
use Override;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class HtmlPage extends Page {

	#[Override]
	public function writeToFile(string $path): Exception {
		return new Exception('Not implemented');
	}

}
