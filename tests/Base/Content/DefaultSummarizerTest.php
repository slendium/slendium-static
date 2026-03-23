<?php

namespace Slendium\SlendiumStaticTests\Base\Content;

use Dom\HTMLDocument;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\SlendiumStatic\Base\Content\DefaultSummarizer;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class DefaultSummarizerTest extends TestCase {

	public static function summarizeCases(): iterable { // @phpstan-ignore missingType.iterableValue

		yield [ HTMLDocument::createFromString(<<<WithMetaTitleAndDescription
<!DOCTYPE html>
<html>
	<head>
		<meta charset=utf-8>
		<title>Document title ≠ content summary title</title>
		<meta name=title content="Test title">
		<meta name=description content="Test description">
	</head>
	<body><h1>Meta title should override</h1></body>
</html>
WithMetaTitleAndDescription), 'Test title', 'Test description' ];

		yield [ HTMLDocument::createFromString(<<<WithH1TitleAndDescription
<!DOCTYPE html>
<html>
	<head><meta name=description content="Article description"></head>
	<body>
		<main>
			<article>
				<h1>Article title</h1>
				<h2>Subtitle</h2>
				<h3>Subsubttitle</h3>
			</article>
		</main>
	</body>
</html>
WithH1TitleAndDescription), 'Article title', 'Article description' ];

		yield [ HTMLDocument::createFromString(<<<NeitherTitleNorDescription
<!DOCTYPE html>
<html>
	<head><title>Empty document</title></head>
	<body>
		<header>These don't count</header>
		<b>These neither</b>
	</body>
</html>
NeitherTitleNorDescription), null, null ];

	}

	#[DataProvider('summarizeCases')]
	public function test_summarize(HTMLDocument $document, ?string $expectedTitle, ?string $expectedDescription): void {
		$sut = new DefaultSummarizer;

		$result = $sut->summarize($document);

		$this->assertSame($expectedTitle, $result->title);
		$this->assertSame($expectedDescription, $result->description);
	}

	public function test_summarize_shouldRemoveMetaElements(): void {
		$document = HTMLDocument::createFromString(<<<WithMetaElements
<!DOCTYPE html>
<html>
	<head>
		<meta name=title content="Test title">
		<meta name=description content="Test description">
	</head>
</html>
WithMetaElements);
		$sut = new DefaultSummarizer;

		(void)$sut->summarize($document);

		$this->assertNull($document->querySelector('meta[name=title]'));
		$this->assertNull($document->querySelector('meta[name=description]'));
	}

}
