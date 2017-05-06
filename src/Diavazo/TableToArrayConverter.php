<?php

declare(strict_types=1);

namespace Diavazo;

class TableToArrayConverter
{
    /**
     * @var HTMLElement
     */
    protected $tableElement;

    /**
     * @var \Closure[]
     */
    protected $tdExtractorList;

    /**
     * TableToJSONConverter constructor.
     *
     * @param HTMLElement $element
     *
     * @throws \Exception
     */
    public function __construct(HTMLElement $element)
    {
        if ($element->getTagName() !== 'table') {
            throw new \Exception('Can only be applied on table elements');
        }
        $this->tableElement = $element;
        $this->tdExtractorList = [];
    }

    /**
     * @param string|int $columnName
     * @param \Closure $closure
     */
    public function registerExtractor($columnName, \Closure $closure)
    {
        $this->tdExtractorList[$columnName] = $closure;
    }

    /**
     * @param string|int $columnName
     *
     * @return \Closure|mixed
     */
    public function getExtractor($columnName)
    {
        if (isset($this->tdExtractorList[$columnName])) {
            return $this->tdExtractorList[$columnName];
        }
        return function (HTMLElement $td) {
            return $td->getInnerText();
        };
    }

    /**
     * @return string[]
     */
    public function getHeaderArray(): array
    {
        $keyList = [];

        $headerTr = $this->tableElement->getFirstDescendantByName('tr');
        foreach ($headerTr->getChildElementList() as $th) {
            $keyList[] = $th->getInnerText();
        }
        return $keyList;
    }

    /**
     * @return array
     */
    public function getAsAssociativeArray(): array
    {
        $keyList = $this->getHeaderArray();
        $tableArray = [];

        foreach ($this->tableElement->getDescendantByName('tr') as $rowIndex => $tr) {
            if ($rowIndex === 0) {
                continue;
            }

            $rowArray = [];
            foreach ($tr->getDescendantByName('td') as $columnIndex => $td) {
                $columnName = $keyList[$columnIndex];
                $extractor = $this->getExtractor($columnName);
                $rowArray[$columnName] = $extractor($td);
            }
            $tableArray[] = $rowArray;
        }
        return $tableArray;
    }

    /**
     *
     */
    public function getAsArray()
    {
        $tableArray = [];

        foreach ($this->tableElement->getDescendantByName('tr') as $tr) {
            $rowArray = [];
            foreach ($tr->getDescendantByName('td th') as $index => $td) {
                $extractor = $this->getExtractor($index);
                $rowArray[] = $extractor($td);
            }
            $tableArray[] = $rowArray;
        }
        return $tableArray;
    }

}