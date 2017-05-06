<?php

declare(strict_types=1);

namespace DiavazoTest;

use Diavazo\HTMLDocument;
use PHPUnit\Framework\TestCase;

class ElementTest extends TestCase
{

    private function getDocument()
    {
        $document = new HTMLDocument();
        $document->loadFile(__DIR__ . "/assets/ElementTest.html");
        return $document;
    }

    public function testIdAndClass()
    {
        $document = $this->getDocument();
        $div = $document->getElementById('testIdAndClass');

        $this->assertNotNull($div);
        $this->assertSame("testIdAndClass", $div->getId());
        $this->assertSame("div", $div->getTagName());
        $this->assertTrue($div->isClass("myClass otherClass"));
        $this->assertTrue($div->hasClass("myClass"));
        $this->assertSame("myClass otherClass", $div->getClass());
    }

    //    <div id="testAttribute" style="text-align: center" hidden="19.08" about="7" disabled="" readonly="false"></div>

    public function testAttribute()
    {
        $document = $this->getDocument();

        $div = $document->getElementById("testAttribute");

        $this->assertNotNull($div);

        $this->assertSame(19.08, $div->getAttributeValueAsFloat("hidden"));
        $this->assertSame(7, $div->getAttributeValueAsInt("about"));
        $this->assertFalse($div->getAttributeValueAsBoolean("disabled"));
        $this->assertTrue($div->getAttributeValueAsBoolean("about"));
        $this->assertNull($div->getAttributeValue('notExisting'));
        $this->assertSame("", $div->getAttributeValue("disabled"));
        $this->assertFalse($div->getAttributeValueAsBoolean("readonly"));

        $this->assertTrue($div->hasAttribute("disabled"));
        $this->assertFalse($div->hasAttribute("notExisting"));
        $this->assertTrue($div->hasAttributeValue("hidden", "19.08"));

    }

    //     <div id="testData" data-int="8" data-float="19.08" data-bool="false" data-empty=""></div>

    public function testData()
    {
        $document = $this->getDocument();
        $div = $document->getElementById("testData");

        $this->assertNull($div->getData('not-existing'));
        $this->assertSame("", $div->getData("empty"));
        $this->assertSame(false, $div->getDataAsBoolean("bool"));
        $this->assertSame(8, $div->getDataAsInt("int"));
        $this->assertSame(19.08, $div->getDataAsFloat("float"));

    }

    public function testDescendants()
    {
        $document = $this->getDocument();
        $root = $document->getRootElement();
        $this->assertNotNull($root);

        $table = $root->getFirstDescendantByName("table");
        $this->assertNotNull($table);
        $tdList = $table->getDescendantByName("td");
        $this->assertCount(4, $tdList);

        foreach ($tdList as $index => $td) {
            $this->assertSame($index, $td->getInnerTextAsInt());
        }
    }

    public function testDescendantClass() {
        $document = $this->getDocument();
        $root = $document->getRootElement();
        $this->assertNotNull($root);

        $tdList = $root->getDescendantWithClassName("testClass");
        $this->assertCount(3, $tdList);

        $tdList = $root->getDescendantWithClassNameStrict("testClass");
        $this->assertCount(2, $tdList);

    }

    public function testMultipleTag() {
        $document = $this->getDocument();
        $elementList = $document->getElementByTagName("p span");
        $this->assertCount(4, $elementList);

    }


    public function testXPath() {
        $document = $this->getDocument();
        $root = $document->getRootElement();
        $this->assertNotNull($root);

        $table = $root->getFirstDescendantByName("table");
        $this->assertNotNull($table);


        $tdHeadList = $table->query("thead/tr/td");
        $this->assertCount(2, $tdHeadList);

    }



    public function testOuterHTML() {
        $document = $this->getDocument();
        $div = $document->getElementById("testOuterHTML");

        $this->assertNotNull($div);
        $this->assertSame('<div id="testOuterHTML" data-int="8" data-float="19.08" data-bool="false" data-empty=""><br/></div>', $div->getOuterHTML());
    }

}