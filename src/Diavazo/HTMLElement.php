<?php

declare(strict_types=1);

namespace Diavazo;

use Civis\Common\StringUtil;

class HTMLElement
{

    /**
     * @var \DOMElement
     */
    private $domElement;

    /**
     * HTMLElement constructor.
     *
     * @param \DOMElement $domElement
     */
    public function __construct(\DOMElement $domElement)
    {
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
     * @param string $attributeName
     *
     * @return null|string
     */
    public function getAttributeValue(string $attributeName)
    {
        $value = $this->domElement->getAttribute($attributeName);
        return StringUtil::trimToNull($value);
    }

    /**
     * @param string $attributeName
     *
     * @return bool
     */
    public function getAttributeValueAsBoolean(string $attributeName): bool
    {
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
        if ($value === null) {
            return null;
        }
        return intval($value);
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
        return StringUtil::trimToNull($this->domElement->textContent);
    }

    /**
     * @return int|null
     */
    public function getInnerTextAsInt()
    {
        $innerText = $this->getInnerText();
        if ($innerText === null) {
            return null;
        }
        return intval($innerText);
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
     * @return HTMLElement|null
     */
    public function getFirstChild()
    {
        if ($this->domElement->firstChild === null) {
            return null;
        }
        return new HTMLElement($this->domElement->firstChild);
    }

    /**
     * @return HTMLElement|null
     */
    public function getPreviousSibling()
    {
        if (!$this->domElement->previousSibling) {
            return null;
        }
        return new HTMLElement($this->domElement->previousSibling);
    }

    /**
     * @return HTMLElement|null
     */
    public function getNextSibling()
    {
        if (!$this->domElement->nextSibling) {
            return null;
        }
        return new HTMLElement($this->domElement->nextSibling);
    }

    /**
     * @return HTMLElement[]
     */
    public function getChildElementList(): array
    {
        $childElementList = [];
        foreach ($this->domElement->childNodes as $childNode) {
            if ($childNode->nodeType === XML_ELEMENT_NODE) {
                $childElementList[] = new HTMLElement($childNode);
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
     * @param string $tagName
     *
     * @return HTMLElement[]
     */
    public function getDescendantByName(string $tagName)
    {
        $descendantList = [];
        $elementList = $this->getChildElementList();
        foreach ($elementList as $child) {
            if ($child->getTagName() === $tagName) {
                $descendantList[] = $child;
            }
            $descendantList = array_merge($descendantList, $child->getDescendantByName($tagName));
        }
        return $descendantList;
    }

    /**
     * @param string $className
     *
     * @return HTMLElement[]
     */
    public function getDescendantWithClassName(string $className)
    {
        $descendantList = [];
        $elementList = $this->getChildElementList();
        foreach ($elementList as $child) {
            if ($child->hasClass($className)) {
                $descendantList[] = $child;
            }
            $descendantList = array_merge($descendantList, $child->getDescendantWithClassName($className));
        }
        return $descendantList;
    }

    /**
     * @param string $className
     *
     * @return array
     */
    public function getDescendantWithClassNameStrict(string $className)
    {
        $descendantList = [];
        $elementList = $this->getChildElementList();
        foreach ($elementList as $child) {
            if ($child->isClass($className)) {
                $descendantList[] = $child;
            }
            $descendantList = array_merge($descendantList, $child->getDescendantWithClassNameStrict($className));
        }
        return $descendantList;
    }


    public function getOuterHTML() {
        if ($this->domElement->childNodes->length === 0) {

        }


        $outerHTML = $this->getOpeningTag();
    }

    public function getInnerHTML() {

    }

    public function getOpeningTag() {
        $tag = '<' . $this->getTagName();
        foreach($this->domElement->attributes as $attribute) {
            $tag .= ' ' .$attribute->nodeName . '"' . $attribute->nodeValue . '"';
        }
        return $tag .= '>';
    }

    public function getClosingTag() {
        return '</' . $this->getTagName() . '>';
    }

    /**
     * @return \DOMElement
     */
    public function getDomElement() : \DOMElement{
        return $this->domElement;
    }

}
