<?php

namespace Slendium\SlendiumStatic\Base\Site\Resource;

use Override;

use Slendium\SlendiumStatic\Base\Site\Resource;
use Slendium\SlendiumStatic\Source\File;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class BinaryResource extends Resource {

	#[Override]
	public function generateContents(): File {
		return $this->file;
	}

}
