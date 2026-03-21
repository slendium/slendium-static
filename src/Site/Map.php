<?php

namespace Slendium\SlendiumStatic\Site;

use ArrayAccess;
use Traversable;

/**
 * @since 1.0
 * @extends Traversable<non-empty-string,Resource>
 * @author C. Fahner
 * @copyright Slendium 2026
 */
interface Map extends Traversable {

	/** @since 1.0 */
	public function contains(Resource|Uri $value): bool;

	/** @since 1.0 */
	public function get(Uri $uri): Resource;

	/** @since 1.0 */
	public function insert(Resource $resource): void;

	/** @since 1.0 */
	public function overwrite(Resource $resource): void;

	/** @since 1.0 */
	public function delete(Resource|Uri $value): void;

}
