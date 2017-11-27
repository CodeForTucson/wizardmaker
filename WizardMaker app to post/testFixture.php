<?php
// print 'Test regex searches <br>';
// $stnug2 = '<p>Adding calculations to test:</p><div><ol><li>Get wood for 2 boards for the long side; about cal#LengthVar*2# feet .</li><li>Get wood for 2 more boards for the short side; about Calculate(WidthVar * 2) feet.</li><li>And so on and so forth.</li></ol></div>';
// //preg_match('/cal\[.*\]/', $stnug2, $varSt);
// preg_match('/cal#.+#/', $stnug2, $varSt);
// // now replace each variable with its captured number from the sessions data
// //var_dump($varSt);
// //print "<br>";
// foreach ($varSt as $var) {
// 	print 'result was ' . $var . '<br>';
// 	$core = substr($var,4,strlen($var)-5);
// 	print 'expression part is ' . $core . '<br>';
// }
 
// print 'Test math part <br>';
// include('evalmath.class.php');
// $m = new EvalMath;
// // basic evaluation:
// $m->evaluate('dog = 20');
// //$m->evaluate('cat = 5');
// $result = $m->evaluate('(10/2.5)+(dog*2)');
// //$result = $m->evaluate($core);
// //print 'Test of new evalmath functon <br>';=
// print "result = " .$result; 
  
//print 'Test xml maniuplation <br>';    

// just to test xml,  then discard 
// pass in the index of the step, index of the element and file name
// and get attribute and
// name of the element and value as an array
// $wfile = $_COOKIE['c_file']; // the wizard file is kept as a cookie
$sIndex = $_COOKIE['c_snum'] -1;  // step number minnus 1 is the xml index
$enum = $_COOKIE['c_sele'];  // element number
$wfile = 'wizards/Wizard-number-1.xml';
$doc2 = new DOMDocument();
$doc2->preserveWhiteSpace = false;
$doc2->formatOutput = true;  // so it will output nicely with indents
$doc2->load($wfile); // load the wizard xml file
//append the object
$sxe2 = simplexml_import_dom($doc2); // convert to simpleXML object
// Get the right piece of info -- name or description
// will try to get the data by using the childres functoin reading into an array

$x = $doc2->getElementsByTagName("stepElems")->item(0);
// print 'variable x below ';
// print '<br>';
// echo "<pre>";
// print_r($x);
// echo "</pre>";
// print '<br>';
$temElems = $x->childNodes->item(0);
$elemLength = $x->childNodes->length;
//$temElems = $x->childNodes[0]);
//$temElems = $doc->step->item(0)->getElementsByTagName("sElem"); //create an array of elements
//$temElems = $sxe2->step[0]->stepElems[0]->children(); // get present title of the step

// xpath stuff

$xpath = new DOMXPath($doc2);
//$imFile = $xpath->query("//text");
$imFile = $xpath->query("//sElem/text");

print "test of xml function <br>"; 
// print 'index of step is ' . $sIndex;
// print '<br>';
// print 'number of elements is ' . $elemLength;
// print '<br>';
//print 'number of elements is ' . sizeof($temElems);
print '<br>';
//$elemToGo = $temElems->getElementsByTagName('sElem')->item(1);
//var_dump($temElems);
echo "<pre>";
print_r($imFile[1]->nodeValue);
echo "</pre>";
print '<br>';
// var_dump($temElems);
// print 'type= ' . $temElems[2]->type;
// print '<br>';
// print 'placeholder= ' . $temElems[2]->place;
// print '<br>';
// print 'text = ' . $temElems[2]->text;
// print '<br>';
//$temElems[2]->text = "now here are some good words";
//print 'new text = ' . $temElems[2]->text;
//print '<br>';

?>