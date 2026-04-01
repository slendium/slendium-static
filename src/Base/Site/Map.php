<?php

namespace Slendium\SlendiumStatic\Base\Site;

use IteratorAggregate;
use LogicException;
use NoDiscard;
use Override;
use Traversable;

use Slendium\SlendiumStatic\Site\Map as IMap;
use Slendium\SlendiumStatic\Site\Resource;
use Slendium\SlendiumStatic\Site\Uri;

/**
 * @internal
 * @implements IteratorAggregate<non-empty-string,Resource>
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class Map implements IMap, IteratorAggregate {

	/** @var array<non-empty-string,Resource> */
	private array $values;

	/**
	 * @param iterable<Resource> $values
	 * @return array<non-empty-string,Resource>
	 */
	private static function init_values(iterable $values): array {
		$map = [ ];
		foreach ($values as $resource) {
			$uri = (string)$resource->uri;
			if (isset($map[$uri])) {
				throw new LogicException("Unexpected duplicate resource `$uri` in site map");
			}
			$map[$uri] = $resource;
		}
		return $map;
	}

	/** @param iterable<Resource> $values */
	public function __construct(iterable $values) {
		$this->values = self::init_values($values);
	}

	#[Override]
	public function count(): int {
		return \count($this->values);
	}

	#[Override]
	public function getIterator(): Traversable {
		foreach ($this->values as $uriString => $resource) {
			yield $uriString => $resource;
		}
	}

	#[Override]
	public function contains(Resource|Uri $value): bool {
		if ($value instanceof Resource) {
			$value = $value->uri;
		}

		return isset($this->values[(string)$value]);
	}

	#[Override, NoDiscard]
	public function get(Uri $uri): Resource {
		return $this->values[(string)$uri];
	}

	#[Override]
	public function insert(Resource $resource): void {
		$uri = (string)$resource->uri;
		if (isset($this->values[$uri])) {
			throw new LogicException("Cannot insert `$uri`, resource already exists");
		}
		$this->values[$uri] = $resource;
	}

	#[Override]
	public function overwrite(Resource $resource): void {
		$uri = (string)$resource->uri;
		if (!isset($this->values[$uri])) {
			throw new LogicException("Cannot overwrite `$uri`, no such resource");
		}
		$this->values[$uri] = $resource;
	}

	#[Override]
	public function delete(Resource|Uri $value): void {
		if ($value instanceof Resource) {
			$value = $value->uri;
		}

		unset($this->values[(string)$value]);
	}

}
