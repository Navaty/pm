<?php
$text = "<group><id>1</id><name>Patterns of Enterprise Application Architecture</name><text>Text1</text></group>";

$xml1 = <<< XML
<?xml version="1.0" encoding="utf-8"?>
$text
XML;

$dom = new DOMDocument;
$dom->loadXML($xml1);
$books = $dom->getElementsByTagName('name');
foreach ($books as $book) {
    echo $book->nodeValue, PHP_EOL;
}
?>
