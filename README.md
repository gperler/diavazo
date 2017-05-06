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


# HTMLElement methods
````php
$document = new HTMLDocument();
$document->loadFile(__DIR__ . "/assets/TableToArrayTest.html");

// get root (<html>)
$root = $document->getRootElement();

$root->

````