<?php

namespace Slendium\SlendiumStatic\Site;

use Slendium\SlendiumStatic\Common\Iteration;
use Slendium\SlendiumStatic\Site\Map;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class MapSearch {

	/**
	 * Yields logical descendants of a given {@see Uri}.
	 *
	 * For example, the URI `/tmp` matches `/tmp/index.html` and `/tmp/inner/index.html`, but not
	 * `/tmp.html`.
	 *
	 * @since 1.0
	 * @return iterable<non-empty-string,Resource> An iterable with URI-string keys and Resource values
	 */
	public static function findDescendants(Map $map, Uri $uri): iterable {
		yield from Iteration::filter($map, function ($resource) use ($uri) {
			return UriRelations::isDescendantOf(ancestor: $uri, subject: $resource->uri);
		});
	}

	/**
	 * Yields the siblings of a given {@see Uri}.
	 * @since 1.0
	 * @return iterable<non-empty-string,Resource> An iterable with URI-string keys and Resource values
	 */
	public static function findSiblings(Map $map, Uri $uri): iterable {
		yield from Iteration::filter($map, function ($resource) use ($uri) {
			return UriRelations::isSiblingOf(reference: $uri, subject: $resource->uri);
		});
	}

	/**
	 * @since 1.0
	 * @template T of Resource
	 * @param class-string<T> $class
	 * @return ?T
	 */
	public static function getResourceOfType(Map $map, Uri $uri, string $class): ?Resource {
		$resource = $map->contains($uri)
			? $map->get($uri)
			: null;

		return $resource !== null && \is_a($resource, $class)
			? $resource
			: null;
	}

	private function __construct() { }

}
