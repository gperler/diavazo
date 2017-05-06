<?php

declare(strict_types=1);

namespace Diavazo;

class HTMLDocument
{

    /**
     * @var \DOMDocument
     */
    private $domDocument;

    /**
     * HTMLParser constructor.
     */
    public function __construct()
    {
        $this->domDocument = new \DOMDocument();
    }

    /**
     * @param string $fileName
     */
    public function loadFile(string $fileName)
    {
        @$this->domDocument->loadHTMLFile($fileName);
    }

    /**
     * @param string $htmlString
     */
    public function loadString(string $htmlString)
    {
        @$this->domDocument->loadHTML($htmlString);
    }

    /**
     * @return HTMLElement
     */
    public function getRootElement(): HTMLElement
    {
        return new HTMLElement($this->domDocument, $this->domDocument->documentElement);
    }

    /**
     * @param string $elementId
     *
     * @return HTMLElement|null
     */
    public function getElementById(string $elementId)
    {
        $domElement = $this->domDocument->getElementById($elementId);
        if ($domElement === null) {
            return null;
        }
        return new HTMLElement($this->domDocument, $domElement);
    }

    /**
     * @param string $tagName
     *
     * @return HTMLElement[]
     */
    public function getElementByTagName(string $tagName)
    {
        $root = $this->getRootElement();
        return $root->getDescendantByName($tagName);
    }

    /**
     * @param string $queryString allows combination of "tagName .className tagName.className"
     *
     * @return HTMLElement[]
     */
    public function getElement(string $queryString): array
    {
        $root = $this->getRootElement();
        return $root->getElement($queryString);
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
        foreach ($this->domDocument->getElementsByTagName($elementName) as $node) {
            $htmlElement = new HTMLElement($this->domDocument, $node);
            if ($htmlElement->hasAttributeValue($attributeName, $attributeValue)) {
                $resultList[] = $htmlElement;
            }
        }
        return $resultList;
    }

    /**
     * @param string $xpath
     *
     * @return HTMLElement[]
     */
    public function query(string $xpath)
    {
        $domXpath = new \DOMXPath($this->domDocument);

        $resultList = [];
        foreach ($domXpath->query($xpath) as $childNode) {
            $resultList[] = new HTMLElement($this->domDocument, $childNode);
        }
        return $resultList;
    }

    /**
     * @param string $xpath
     *
     * @return mixed
     */
    public function evaluate(string $xpath)
    {
        $xpathObject = new \DOMXPath($this->domDocument);
        return $xpathObject->evaluate($xpath);
    }

    /**
     * @return \DOMDocument
     */
    public function getDOMDocument(): \DOMDocument
    {
        return $this->domDocument;
    }

}