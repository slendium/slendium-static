<?php

namespace Slendium\SlendiumStatic;

use ArrayAccess;
use Exception;

use Slendium\SlendiumStatic\Base\Pathed;
use Slendium\SlendiumStatic\Site as ISite;
use Slendium\SlendiumStatic\Base\Site;
use Slendium\SlendiumStatic\Site\Resource;
use Slendium\SlendiumStatic\Source\Directory;
use Slendium\SlendiumStatic\Source\Filesystem;

/**
 * @since 1.0
 * @phpstan-import-type ConfigsMap from Configs
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class SiteGenerator {

	private const IN_DIR = 'content';

	private const OUT_DIR = 'public';

	private readonly Filesystem $filesystem;

	/**
	 * Creates and saves a site using the given configs and outputs the results to the command line.
	 * @since 1.0
	 * @param ConfigsMap $configs
	 */
	public static function cli(ArrayAccess|array $configs): void {
		$site = new self($configs)->create(self::IN_DIR);

		if (\count($site->errors) > 0) {
			echo "Aborting, encountered ".\count($site->errors)." errors\n";
			foreach ($site->errors as $error) {
				echo "Error while processing {$error->path}:\n", "\t\e[31m{$error->value->getMessage()}\n\e[0m";
			}
		} else {
			$result = $site->save(self::OUT_DIR);
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

	) {
		$this->filesystem = Configs::getFilesystem($configs);
	}

	/** @since 1.0 */
	public function create(string $path): ISite {
		$errors = [ ];
		$resources = [ ];

		$sourceContents = new Directory($path, filesystem: $this->filesystem)
			->extractResources($this->configs);
		foreach ($sourceContents as $resourcePath => $resource) {
			if ($resource instanceof Resource) {
				$resources[] = $resource;
			} else {
				$errors[] = new Pathed($resourcePath, $resource);
			}
		}
		return new Site($errors, $resources, $this->filesystem);
	}

}
