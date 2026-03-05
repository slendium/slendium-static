<?php

namespace Slendium\SlendiumStaticTests\Common;

use Dom\HTMLDocument;

use PHPUnit\Framework\TestCase;

use Slendium\SlendiumStatic\Common\HtmlParser;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class HtmlParserTest extends TestCase {

	public function test_parse_shouldReturnDocument_whenSyntaxCorrect(): void {
		$html = <<<HtmlDoc
<!DOCTYPE html>
<html>
	<head><title>Test</title></head>
	<body><main>Test <b>document</b>.</main></body>
</html>
HtmlDoc;

		$result = HtmlParser::parse($html);

		$this->assertInstanceOf(HTMLDocument::class, $result);
	}

	public function test_parse_shouldReturnErrorList_whenSyntaxIncorrect(): void {
		$html = <<<SyntaxError
<!DOCTYPE html>
<html>><</html>
SyntaxError;

		$result = HtmlParser::parse($html);

		$this->assertTrue(\is_array($result));
	}

}
