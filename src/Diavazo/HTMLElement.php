<?php

declare(strict_types=1);

namespace Diavazo;

use Civis\Common\StringUtil;

class HTMLElement
{

    /**
     * @var \DOMDocument
     */
    private $domDocument;

    /**
     * @var \DOMElement
     */
    private $domElement;

    /**
     * HTMLElement constructor.
     *
     * @param \DOMDocument $domDocument
     * @param \DOMElement $domElement
     */
    public function __construct(\DOMDocument $domDocument, \DOMElement $domElement)
    {
        $this->domDocument = $domDocument;
        $this->domElement = $domElement;
    }

    /**
     * @return null|string
     */
    public function getId()
    {
        return $this->getAttributeValue("id");
    }

    /**
     * @return null|string
     */
    public function getClass()
    {
        return $this->getAttributeValue("class");
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    public function hasClass(string $className): bool
    {
        $classNameOnElement = $this->getClass();
        if ($classNameOnElement === null) {
            return false;
        }
        return strpos($classNameOnElement, $className) !== false;
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    public function isClass(string $className): bool
    {
        return $this->hasAttributeValue('class', $className);
    }

    /**
     * @param string $dataName
     *
     * @return null|string
     */
    public function getData(string $dataName)
    {
        return $this->getAttributeValue("data-" . $dataName);
    }

    /**
     * @param string $dataName
     *
     * @return bool
     */
    public function getDataAsBoolean(string $dataName)
    {
        return $this->getAttributeValueAsBoolean("data-" . $dataName);
    }

    /**
     * @param $dataName
     *
     * @return int
     */
    public function getDataAsInt(string $dataName)
    {
        return $this->getAttributeValueAsInt("data-" . $dataName);
    }

    /**
     * @param string $dataName
     *
     * @return float
     */
    public function getDataAsFloat(string $dataName)
    {
        return $this->getAttributeValueAsFloat("data-" . $dataName);
    }

    /**
     * @param string $attributeName
     *
     * @return bool
     */
    public function hasAttribute(string $attributeName)
    {
        return $this->domElement->hasAttribute($attributeName);
    }

    /**
     * @param string $attributeName
     *
     * @return null|string
     */
    public function getAttributeValue(string $attributeName)
    {
        if (!$this->domElement->hasAttribute($attributeName)) {
            return null;
        }
        return $this->domElement->getAttribute($attributeName);
    }

    /**
     * @param string $attributeName
     *
     * @return bool
     */
    public function getAttributeValueAsBoolean(string $attributeName): bool
    {
        $value = $this->getAttributeValue($attributeName);
        if ($value === 'false' || $value = "0") {
            return false;
        }
        return !!$this->getAttributeValue($attributeName);
    }

    /**
     * @param string $attributeName
     *
     * @return int|null
     */
    public function getAttributeValueAsInt(string $attributeName)
    {
        $value = $this->getAttributeValue($attributeName);
        return $value === null ?: intval($value);
    }

    /**
     * @param string $attributeName
     *
     * @return float
     */
    public function getAttributeValueAsFloat(string $attributeName)
    {
        $value = $this->getAttributeValue($attributeName);
        if ($value === null) {
            return null;
        }
        $value = str_replace(',', '.', $value);

        return floatval($value);
    }

    /**
     * @param string $attributeName
     * @param string $value
     *
     * @return bool
     */
    public function hasAttributeValue(string $attributeName, string $value = null): bool
    {
        return $this->getAttributeValue($attributeName) === $value;
    }

    /**
     * @return string
     */
    public function getInnerText()
    {
        return $this->domElement->textContent;
    }

    /**
     * @return int|null
     */
    public function getInnerTextAsInt()
    {
        $innerText = $this->getInnerText();
        return $innerText === null ?: intval($innerText);
    }

    /**
     * @return float
     */
    public function getInnerTextAsFloat()
    {
        $innerText = $this->getInnerText();
        if ($innerText === null) {
            return null;
        }
        $innerText = str_replace(',', '.', $innerText);
        return floatval($innerText);
    }

    /**
     * @return string
     */
    public function getTagName(): string
    {
        return strtolower($this->domElement->tagName);
    }

    /**
     * @param string $tags
     *
     * @return bool
     */
    public function isOneOfTags(string $tags): bool
    {
        $tagList = explode(" ", $tags);
        foreach ($tagList as $tag) {
            if ($tag === $this->getTagName()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return HTMLElement|null
     */
    public function getFirstChild()
    {
        if ($this->domElement->firstChild === null) {
            return null;
        }
        return new HTMLElement($this->domDocument, $this->domElement->firstChild);
    }

    /**
     * @return HTMLElement|null
     */
    public function getPreviousSibling()
    {
        if (!$this->domElement->previousSibling) {
            return null;
        }
        return new HTMLElement($this->domDocument, $this->domElement->previousSibling);
    }

    /**
     * @return HTMLElement|null
     */
    public function getNextSibling()
    {
        if (!$this->domElement->nextSibling) {
            return null;
        }
        return new HTMLElement($this->domDocument, $this->domElement->nextSibling);
    }

    /**
     * @return HTMLElement[]
     */
    public function getChildElementList(): array
    {
        $childElementList = [];
        foreach ($this->domElement->childNodes as $childNode) {
            if ($childNode->nodeType === XML_ELEMENT_NODE) {
                $childElementList[] = new HTMLElement($this->domDocument, $childNode);
            }
        }
        return $childElementList;
    }

    /**
     * @param string $tagName
     *
     * @return HTMLElement|null
     */
    public function getFirstDescendantByName(string $tagName)
    {
        $elementList = $this->getChildElementList();

        while (sizeof($elementList) !== 0) {
            $current = array_shift($elementList);
            if ($current->getTagName() === $tagName) {
                return $current;
            }
            $elementList = array_merge($elementList, $current->getChildElementList());
        }
        return null;
    }

    /**
     * @param string $queryString allows combination of "tagName .className tagName.className"
     *
     * @return HTMLElement[]
     */
    public function getElement(string $queryString): array
    {
        $resultList = [];

        $queryList = explode(" ", $queryString);
        foreach ($queryList as $query) {
            if (StringUtil::startsWith($query, ".")) {
                $className = trim($query, ".");
                $resultList = array_merge($resultList, $this->getDescendantWithClassName($className));
                continue;
            }
            if (strpos($query, ".") === false) {
                $resultList = array_merge($resultList, $this->getDescendantByName($query));
                continue;
            }
            $elementClass = explode(".", $query);
            $resultList = array_merge($resultList, $this->getDescendantWithClassName($elementClass[1], $elementClass[0]));
        }
        return $resultList;
    }

    /**
     * @param string $tagName
     *
     * @return HTMLElement[]
     */
    public function getDescendantByName(string $tagName)
    {
        $descendantList = [];
        $elementList = $this->getChildElementList();
        foreach ($elementList as $child) {
            if ($child->isOneOfTags($tagName)) {
                $descendantList[] = $child;
            }
            $descendantList = array_merge($descendantList, $child->getDescendantByName($tagName));
        }
        return $descendantList;
    }

    /**
     * @param string $className
     * @param string|null $tagList
     *
     * @return array
     */
    public function getDescendantWithClassName(string $className, string $tagList = null)
    {
        $descendantList = [];
        $elementList = $this->getChildElementList();
        foreach ($elementList as $child) {
            if ($child->hasClass($className) && ($tagList === null || $child->isOneOfTags($tagList))) {
                $descendantList[] = $child;
            }
            $descendantList = array_merge($descendantList, $child->getDescendantWithClassName($className));
        }
        return $descendantList;
    }

    /**
     * @param string $className
     * @param string|null $tagList
     *
     * @return array
     */
    public function getDescendantWithClassNameStrict(string $className, string $tagList = null)
    {
        $descendantList = [];
        $elementList = $this->getChildElementList();
        foreach ($elementList as $child) {
            if ($child->isClass($className) && ($tagList === null || $child->isOneOfTags($tagList))) {
                $descendantList[] = $child;
            }
            $descendantList = array_merge($descendantList, $child->getDescendantWithClassNameStrict($className));
        }
        return $descendantList;
    }

    /**
     * @return string
     */
    public function getOuterHTML()
    {
        if ($this->domElement->childNodes->length === 0) {
            return $this->getOpeningTag(true);
        }
        $html = $this->getOpeningTag();
        $html .= $this->getInnerHTML();
        $html .= $this->getClosingTag();
        return $html;
    }

    /**
     * @return string
     */
    public function getInnerHTML()
    {
        $html = '';
        foreach ($this->domElement->childNodes as $childNode) {
            if ($childNode->nodeType === XML_ELEMENT_NODE) {
                $htmlElement = new HTMLElement($this->domDocument, $childNode);
                $html .= $htmlElement->getOuterHTML();
            }
            if ($childNode->nodeType === XML_TEXT_NODE) {
                $html .= $childNode->nodeValue;
            }
            if ($childNode->nodeType === XML_COMMENT_NODE) {
                $html .= '<!--' . $childNode->nodeValue . '-->';
            }
        }
        return $html;
    }

    /**
     * @param bool $immediateClose
     *
     * @return string
     */
    public function getOpeningTag($immediateClose = false)
    {
        $tag = '<' . $this->getTagName();
        foreach ($this->domElement->attributes as $attribute) {
            $tag .= ' ' . $attribute->nodeName . '="' . $attribute->nodeValue . '"';
        }
        return $tag .= ($immediateClose) ? '/>' : '>';
    }

    /**
     * @return string
     */
    public function getClosingTag()
    {
        return '</' . $this->getTagName() . '>';
    }

    /**
     * @return \DOMElement
     */
    public function getDomElement(): \DOMElement
    {
        return $this->domElement;
    }

    /**
     * @param string $xpath
     *
     * @return array
     */
    public function query(string $xpath)
    {
        $xpathObject = new \DOMXPath($this->domDocument);
        $resultList = [];
        foreach ($xpathObject->query($xpath, $this->domElement) as $childNode) {
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
        return $xpathObject->evaluate($xpath, $this->domElement);
    }

}
