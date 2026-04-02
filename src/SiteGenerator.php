<?php

namespace Slendium\SlendiumStatic;

use ArrayAccess;
use Exception;

use Slendium\SlendiumStatic\Base\SourceConverter;
use Slendium\SlendiumStatic\Site;
use Slendium\SlendiumStatic\Source\Path;

/**
 * @since 1.0
 * @phpstan-import-type ConfigsMap from Configs
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class SiteGenerator {

	private const IN_DIR = 'content';

	private const OUT_DIR = 'public';

	/**
	 * Creates and saves a site using the given configs and outputs the results to the command line.
	 * @since 1.0
	 * @param ConfigsMap $configs
	 */
	public static function cli(ArrayAccess|array $configs): void {
		$cwd = \getcwd();
		if ($cwd === false) {
			echo "No access to current working directory, aborting\n";
			exit;
		}
		$site = new self($configs)->create($cwd.\DIRECTORY_SEPARATOR.self::IN_DIR);

		if (\count($site->errors) > 0) {
			echo "Aborting, encountered ".\count($site->errors)." errors\n";
			foreach ($site->errors as $error) {
				echo "Error while processing {$error->path}:\n", "\t\e[31m{$error->value->getMessage()}\n\e[0m";
			}
		} else {
			$result = $site->save($cwd.\DIRECTORY_SEPARATOR.self::OUT_DIR);
			if ($result === true) {
				echo 'Site saved successfully at ./'.self::OUT_DIR."/\n";
			} else {
				echo "The site was created successfully, but an error occurred while saving: \n",
					"\t\e[31m{$result->getMessage()}\e[0m\n";
			}
		}
	}

	/** @since 1.0 */
	public function __construct(

		/** @var ConfigsMap */
		private readonly ArrayAccess|array $configs,

	) { }

	/** @since 1.0 */
	public function create(Path|string $path): Site {
		if (\is_string($path)) {
			$path = Path::fromString($path);
		}

		return new SourceConverter($this->configs)->convert($path);
	}

}
