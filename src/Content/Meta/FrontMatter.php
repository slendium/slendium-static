<?php

namespace Slendium\SlendiumStatic\Content\Meta;

use Closure;
use DateTimeInterface;
use ReflectionClass;
use ReflectionProperty;

/**
 * Contains all recognized front matter properties.
 *
 * Front matter is considered the most explicit form of document metadata. It overrides metadata found
 * in HTML `<meta>` elements or anything in the `<sls-section name=meta>` element.
 *
 * Front matter can be declared using YAML (delimited by `---`) or JSON (delimited by `;;;`). The names
 * of the public properties of this class are the supported keys. For compatibility with other static
 * site generators, some properties can be declared using multiple keys. They will be annotated with
 * the {@see AlternativeKeys} attribute. Alternative keys are tried in order only if the main property
 * name fails.
 *
 * Only files that start with a line containing only a delimiter are considered. If the delimiter is
 * preceeded by anything, including whitespace, the file is assumed to be without front matter.
 *
 * @since 1.0
 * @see https://www.markdownlang.com/advanced/frontmatter.html
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class FrontMatter {

	private const DELIMITER_YAML = '---';

	private const DELIMITER_JSON = ';;;';

	private const LINE_ENDS = [ "\n", "\r\n" ];

	/** @since 1.0 */
	public ?string $title {
		get => FrontMatterTypes::getString($this->values, __PROPERTY__);
	}

	/** @since 1.0 */
	#[AlternativeKeys('summary')]
	public ?string $description {
		get => self::getReflectedValue(__PROPERTY__, fn($k) => FrontMatterTypes::getString($this->values, $k));
	}

	/** @since 1.0 */
	#[AlternativeKeys('draft')]
	public ?bool $isDraft {
		get => self::getReflectedValue(__PROPERTY__, fn($k) => FrontMatterTypes::getBool($this->values, $k));
	}

	/** @since 1.0 */
	#[AlternativeKeys('created', 'date')]
	public ?DateTimeInterface $createdAt {
		get => self::getReflectedValue(__PROPERTY__, fn($k) => FrontMatterTypes::getDate($this->values, $k));
	}

	/** @since 1.0 */
	#[AlternativeKeys('published')]
	public ?DateTimeInterface $publishedAt {
		get => self::getReflectedValue(__PROPERTY__, fn($k) => FrontMatterTypes::getDate($this->values, $k));
	}

	/** @since 1.0 */
	#[AlternativeKeys('edited', 'updated')]
	public ?DateTimeInterface $editedAt {
		get => self::getReflectedValue(__PROPERTY__, fn($k) => FrontMatterTypes::getDate($this->values, $k));
	}

	/**
	 * Extracts the front matter from a text document.
	 * @since 1.0
	 * @return object{ bytes: int<0,max>, frontMatter: self }
	 */
	public static function fromDocument(string $contents): object {
		$yaml = self::getFrontMatterContents($contents, self::DELIMITER_YAML);
		if ($yaml !== null) {
			return (object)[ 'bytes' => \strlen($yaml), 'frontMatter' => self::fromYamlDocument($yaml) ];
		}

		$json = self::getFrontMatterContents($contents, self::DELIMITER_JSON);
		if ($json !== null) {
			return (object)[ 'bytes' => \strlen($json), 'frontMatter' => self::fromJsonDocument($json) ];
		}

		return (object)[ 'bytes' => 0, 'frontMatter' => new self([ ]) ];
	}

	private static function fromYamlDocument(string $contents): self {
		$data = \yaml_parse($contents, 0, $_, [ '!php/object' => fn() => null ]);
		if (!\is_array($data)) {
			$data = [ ];
		}

		return new self($data);
	}

	private static function fromJsonDocument(string $contents): self {
		$jsonStart = \strpos($contents, '{');
		$jsonEnd = \strrpos($contents, '}');
		if ($jsonStart === false || $jsonEnd === false) {
			return new self([ ]);
		}
		$jsonContents = \substr($contents, $jsonStart, $jsonEnd - $jsonStart + 1);

		$data = \json_decode($jsonContents, associative: true);
		return \is_array($data)
			? new self($data)
			: new self([ ]);
	}

	/** @param self::DELIMITER_* $delimiter */
	private static function getFrontMatterContents(string $contents, string $delimiter): ?string {
		$docStart = self::getNextDelimiter($contents, $delimiter, 0);
		if ($docStart?->offset !== 0) {
			return null;
		}

		$docEnd = self::getNextDelimiter($contents, $delimiter, $docStart->length);
		return $docEnd !== null
			? \substr($contents, 0, $docEnd->offset + $docEnd->length)
			: $contents;
	}

	/**
	 * @param self::DELIMITER_* $delimiter
	 * @param int<0,max> $offset
	 * @return ?object{ offset: int<0,max>, length: int<0,max> }
	 */
	private static function getNextDelimiter(string $contents, string $delimiter, int $offset): ?object {
		foreach (self::LINE_ENDS as $eol) {
			$variant = $delimiter.$eol;
			$index = \strpos($contents, $variant, $offset);
			if ($index !== false) {
				return (object)[ 'offset' => $index, 'length' => \strlen($variant) ];
			}
		}
		return null;
	}

	/**
	 * @template T
	 * @param non-empty-string $propertyName
	 * @param Closure(non-empty-string):?T $cast
	 * @return ?T
	 */
	private static function getReflectedValue(string $propertyName, Closure $cast): mixed {
		foreach (self::getKeysForProperty($propertyName) as $key) {
			$value = $cast($key);
			if ($value !== null) {
				return $value;
			}
		}
		return null;
	}

	/**
	 * @param non-empty-string $name
	 * @return iterable<non-empty-string>
	 */
	private static function getKeysForProperty(string $name): iterable {
		yield $name;
		foreach (new ReflectionClass(__CLASS__)->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
			foreach ($property->getAttributes(AlternativeKeys::class) as $attr) {
				yield from $attr->newInstance()->names;
			}
		}
	}

	/** @since 1.0 */
	public function __construct(

		/**
		 * @since 1.0
		 * @var array<mixed>
		 */
		public readonly array $values,

	) { }

}
