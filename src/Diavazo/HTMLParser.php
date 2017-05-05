<?php

declare(strict_types=1);

namespace Diavazo;

class HTMLParser
{

    /**
     * @var \DOMDocument
     */
    private $document;

    /**
     * @var HTMLElement[]
     */
    private $htmlElementList;

    /**
     * HTMLParser constructor.
     */
    public function __construct()
    {
        $this->document = new \DOMDocument();
        $this->htmlElementList = [];
    }

    /**
     * @param string $fileName
     */
    public function loadFile(string $fileName)
    {
        @$this->document->loadHTMLFile($fileName);
    }

    /**
     * @param string $htmlString
     */
    public function loadString(string $htmlString)
    {
        @$this->document->loadHTML($htmlString);
    }

    /**
     * @return HTMLElement
     */
    public function getRootElement(): HTMLElement
    {
        return new HTMLElement($this->document->documentElement);
    }

    /**
     * @param string $elementId
     *
     * @return HTMLElement|null
     */
    public function getElementById(string $elementId)
    {
        $domElement = $this->document->getElementById($elementId);
        if ($domElement === null) {
            return null;
        }
        return new HTMLElement($domElement);
    }

    /**
     * @param string $tagName
     *
     * @return array
     */
    public function getElementByTagName(string $tagName)
    {
        $elementList = [];
        foreach ($this->document->getElementsByTagName($tagName) as $childElement) {
            $elementList[] = new HTMLElement($childElement);
        }
        return $elementList;
    }

    /**
     * @param string $elementName
     * @param string $attributeName
     * @param string $attributeValue
     *
     * @return HTMLElement[]
     */
    public function getElementWithAttributeValue(string $elementName, string $attributeName, string $attributeValue)
    {
        $resultList = [];
        foreach ($this->document->getElementsByTagName($elementName) as $node) {
            $htmlElement = new HTMLElement($node);
            if ($htmlElement->hasAttributeValue($attributeName, $attributeValue)) {
                $resultList[] = $htmlElement;
            }
        }
        return $resultList;
    }

}