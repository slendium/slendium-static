# Slendium Static

Static website generator.

## Installation

Requires **PHP >= 8.5**. Simply run `composer install slendium/slendium-static` to add it to your project.

## Usage

Create the website in the `content` folder. Each file will be converted to a full HTML page. Create
a configuration file called `slendium-static.php` in the root folder. Run the script at `./vendor/bin/generate-site`
to generate the site. The generated site will be located in the `public` folder.

## Roadmap

1. **[Done]** Static pages based on HTML or Markdown only
1. Include a global script and stylesheet if available
1. Integrate compositor library
1. Support for multi-locale sites
1. Page generation (for pagination of blogs, product pages, or similar)
1. Auto-optimized images with support for cropping based on screen size
1. Validating site integrity by checking all internal links
