<?php

namespace Slendium\SlendiumStatic\Base\Site;

use ArrayAccess;
use Exception;
use Override;

use Slendium\SlendiumStatic\Site\Resource as IResource;
use Slendium\SlendiumStatic\Site\Uri;
use Slendium\SlendiumStatic\Source\File;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
abstract class Resource implements IResource {

	public function __construct(

		#[Override]
		public readonly Uri $uri,

		protected readonly File $file,

	) { }

	#[Override]
	public abstract function generateContents(): File|Exception|string;

}
