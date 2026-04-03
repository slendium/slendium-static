<?php

namespace Slendium\SlendiumStatic\Base\Site;

use ArrayAccess;
use Exception;
use Override;

use Slendium\SlendiumStatic\Site\Resource;
use Slendium\SlendiumStatic\Site\Uri;
use Slendium\SlendiumStatic\Source\File;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class FileResource implements Resource {

	public function __construct(

		#[Override]
		public readonly Uri $uri,

		protected readonly File $file,

	) { }

	#[Override]
	public function generateContents(): File|Exception|string {
		return $this->file;
	}

}
