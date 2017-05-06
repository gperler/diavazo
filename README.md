# Diavazo PHP7 HTML Parser

Diavazo is a wrapper arround \DOMDocument and \DOMElement. It adds some useful functionality to search 
within descendants or query by classes.


# Usage
````php
use Diavazo\HTMLDocument;
$document = new HTMLDocument();

// load file
$document->loadFile("local.html");
$document->loadFile("http://mypage.com/test.html");

// load from string
$document->loadString("<html></html>");

````

# HTMLDocument methods
````php
$document = new HTMLDocument();
$document->loadFile(__DIR__ . "/assets/TableToArrayTest.html");

// get element by id
$table = $document->getElementById("associateArrayTest");

// get element by tag name
$elementList = $document->getElementByTagName("div");

// find all <p> elements, all elements with the class 'spanClass' and all <b class="bClass">  
$elementList = $document->getElement("p .spanClass b.bClass");

// xpath query
$title = $document->query("/html/head/title");

// get root (<html>)
$root = $document->getRootElement();
````


# HTMLElement descendants methods
````php
$document = new HTMLDocument();
$document->loadFile(__DIR__ . "/assets/TableToArrayTest.html");

$table = $document->getElementById("table");

// will return the first tr (Breadth-first search)
$table->getFirstDescendantByName("tr");

// will return all td elements
$tdList = $table->getDescendantByName("td th");


$root = $document->getRootElement();

// will find all elements that have the class 'active'
$elementsWithClass = $root->getDescendantWithClassName("active");

// will find all elements that have the class 'myClass' and are td or th elements
$elementsWithClass = $root->getDescendantWithClassName("myClass", "td th");


// will find all elements having only the class 'testClass'
$elementsWithExactClass = $root->getDescendantWithClassNameStrict("testClass");

// will find all elements having only the class 'testClass' and are td or th elements
$elementsWithExactClass = $root->getDescendantWithClassNameStrict("testClass", "td th");



// find all <p> elements, all elements with the class 'spanClass' and all <b class="bClass"> that are descendants of #myId  
$anyElement = $document-getElementById("myId");
$elementList = $document->getElement("p .spanClass b.bClass");

````

# HTMLElement attribute methods
````php
$document = new HTMLDocument();
$document->loadFile("myFile.html");

$table = $document->getElementBy("myTable");

// will return null if the attribute does not exist otherwise string
$table->getAttributeValue("align");


````

# Table to Array Converter
Diavazo allows converting a table to an associative or index based array. Associative Array will
use the first row for the key attribute. 

````php
$document = new HTMLDocument();
$document->loadFile("tabletest.html");

$table = $document->getElementById("myTableID");

$arrayConverter = new TableToArrayConverter($table);
$array = $arrayConverter->getAsAssociativeArray();


<table id="myTableID">
    <tr>
        <td>Key1</td>
        <td>Key2</td>
    </tr>
    <tr>
        <td>Value 1</td>
        <td>Value 2</td>
    </tr>
    ...
</table>

$array = [
    [
       "Key1" => "Value 1",
       "Key2" => "Value 2"
    ],
    ...
]


````


# Table 2 Array using an extractor
The following examples show how to register an extractor. The closure will be invoked
with the table data cell (<td>) and is expected to return the value that will be added to the array.
The following example gets the first <a> element and extracts the href attribute


````php
$document = $this->getDocument();
$table = $document->getElementById("extractorTest");

$arrayConverter = new TableToArrayConverter($table);
$arrayConverter->registerExtractor("columnName", function (HTMLElement $td) {
    $a = $td->getFirstDescendantByName("a");
    return $a->getAttributeValue("href");
});
$array = $arrayConverter->getAsAssociativeArray();
````
