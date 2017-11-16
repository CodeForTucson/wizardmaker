<?php
include('evalmath.class.php');
$m = new EvalMath;
// basic evaluation:
$result = $m->evaluate('2+2');
 print 'Test of new evalmath functon <br>';
 print "result = " .$result;   
    
/*
just to test xml,  then discard 
pass in the index of the step, index of the element and file name
and get attribute and
name of the element and value as an array
$wfile = $_COOKIE['c_file']; // the wizard file is kept as a cookie
$sIndex = $_COOKIE['c_snum'] -1;  // step number minnus 1 is the xml index
$enum = $_COOKIE['c_sele'];  // element number
$doc2 = new DOMDocument();
$doc2->preserveWhiteSpace = false;
$doc2->formatOutput = true;  // so it will output nicely with indents
$doc2->load("wizards/wizText.xml"); // load the wizard xml file
//append the object
$sxe2 = simplexml_import_dom($doc2); // convert to simpleXML object
// Get the right piece of info -- name or description
// will try to get the data by using the childres functoin reading into an array

$temElems = $sxe2->step[0]->stepElems[0]->children(); // get present title of the step
print "test of xml function  "; 
print '<br>';
var_dump($temElems);
print '<br>';
print 'type= ' . $temElems[2]->type;
print '<br>';
print 'placeholder= ' . $temElems[2]->place;
print '<br>';
print 'text = ' . $temElems[2]->text;
print '<br>';
$temElems[2]->text = "now here are some good words";
print 'new text = ' . $temElems[2]->text;
print '<br>';
*/
?>