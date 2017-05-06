<?php

declare(strict_types=1);

namespace DiavazoTest;

use Diavazo\HTMLDocument;
use Diavazo\HTMLElement;
use Diavazo\TableToArrayConverter;
use PHPUnit\Framework\TestCase;

class TableToArrayTest extends TestCase
{
    private function getDocument()
    {
        $document = new HTMLDocument();
        $document->loadFile(__DIR__ . "/assets/TableToArrayTest.html");
        return $document;
    }

    public function testAssociativeArray()
    {
        $document = $this->getDocument();
        $table = $document->getElementById("associateArrayTest");

        $arrayConverter = new TableToArrayConverter($table);
        $array = $arrayConverter->getAsAssociativeArray();

        $this->assertCount(2, $array);

        foreach ($array as $rowIndex => $row) {
            foreach ($row as $columnIndex => $cell) {
                $this->assertSame($columnIndex . $rowIndex, $cell);
            }
        }
    }

    public function testArray()
    {
        $document = $this->getDocument();
        $table = $document->getElementById("arrayTest");

        $arrayConverter = new TableToArrayConverter($table);
        $array = $arrayConverter->getAsArray();

        $this->assertCount(3, $array);

        foreach ($array as $rowIndex => $row) {

            foreach ($row as $columnIndex => $cell) {
                $this->assertSame($rowIndex . "." . $columnIndex, $cell);
            }
        }
    }

    public function testExtractorArray()
    {
        $document = $this->getDocument();
        $table = $document->getElementById("extractorTest");

        $arrayConverter = new TableToArrayConverter($table);
        $arrayConverter->registerExtractor("a", function (HTMLElement $td) {
            $a = $td->getFirstDescendantByName("a");
            return $a->getAttributeValue("href");
        });
        $array = $arrayConverter->getAsAssociativeArray();

        $this->assertCount(2, $array);

        foreach ($array as $rowIndex => $row) {
            foreach ($row as $columnIndex => $cell) {
                $this->assertSame($columnIndex . $rowIndex, $cell);
            }
        }
    }

}