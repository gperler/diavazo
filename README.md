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

````

# HTMLElement attribute methods
````php
$document = new HTMLDocument();
$document->loadFile(__DIR__ . "/assets/TableToArrayTest.html");

$table = $document-getElementBy("myTable");

// will return null if the attribute does not exist otherwise string
$table->getAttributeValue("align");


````