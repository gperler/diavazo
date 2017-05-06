<?php

declare(strict_types=1);

namespace DiavazoTest;

use Diavazo\HTMLDocument;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    public function testFromFile()
    {

        $document = new HTMLDocument();
        $document->loadFile(__DIR__ . "/assets/DocumentTest.html");

        $elementList = $document->getElementByTagName("body");
        $this->assertCount(1, $elementList);

        $body = $elementList[0];
        $this->assertTrue($body->isClass('bodyClass'));

        $div = $document->getElementById("divId");
        $this->assertSame("inner", $div->getInnerHTML());

        $title = $document->query("/html/head/title");
        $this->assertCount(1, $title);

        $meta = $document->getElementWithAttributeValue("meta", "charset", "UTF-8");
        $this->assertCount(1, $meta);

        $elementList = $document->getElement("p .spanClass b.bClass");
        $this->assertCount(3, $elementList);

    }
}