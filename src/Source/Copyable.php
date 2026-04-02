<?php

namespace Slendium\SlendiumStatic\Source;

use Exception;

/**
 * Interface for filesystem entities that support being copied.
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
interface Copyable {

	/** @since 1.0 */
	public function copyTo(Path $target): ?Exception;

}
