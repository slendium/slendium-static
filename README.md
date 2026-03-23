# Slendium Static

Static website generator.

## Installation

Requires **PHP >= 8.5**, [https://getcomposer.org](Composer) and [https://www.npmjs.com](npm). Simply
run `composer install slendium/slendium-static` to add it to your project.

## Usage

Create a `content/` directory and a `slendium-static.php` configuration file in the root of your project.
Each file in the `content/` directory corresponds to a page of the generated site. Pages can be created
using either HTML or Markdown. Running the script at `vendor/bin/generate-site` will generate the site
in the `public/` directory.

## Examples

### Default configuration file

The configuration file lives at `slendium-static.php` in the root directory of your project. It should
return an `ArrayAccess|array` where each key is a configuration name. Use the `ConfigsBuilder` for
type-safe configuration, as in the example below.

```php
return new ConfigsBuilder()
	->setTitleTemplate(static fn($localTitle) => "$localTitle - My Website")
	->setBaseSectionProvider(new ArraySectionProvider([
		SectionNames::HEADER => new HtmlSection('Enter your header-HTML here'),
		SectionNames::FOOTER => new HtmlSection('Enter your footer-HTML here'),
	]))
	->build();
```

## Roadmap

1. **[Done]** Static pages based on HTML or Markdown only
1. **[Done]** Include a global stylesheet
1. Integrate compositor library
1. Support for multi-locale sites
1. Page generation (for pagination of blogs, product pages, or similar)
1. Integrate JS/CSS minification strategies
1. Auto-optimized images with support for cropping based on screen size
1. Validating site integrity by checking all internal links
