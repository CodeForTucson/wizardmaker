<?php
// get the wizard index re wizardlist
$elem = $_REQUEST["q"];  // index of element to be processed
$act = $_REQUEST["a"];  // action: move up, move down or delete
$wfile = $_COOKIE['c_file'];     // get wizard xml file name from cookie.
$sIndex =$_COOKIE['c_snum'];	// get the step index
//get file name
$index = (int)$elem;  // index of element to be processed
// create the dom for moving or deleting the wizardlisting node
$doc = new DOMDocument;
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;  // so it will output nicely with indents
$doc->load($wfile);

// get the DOM parent node to the elements
$elemParnt = $doc->getElementsByTagName("stepElems")->item(0);
// get the element that is to be processed
$elemToGo = $elemParnt->childNodes->item($elem);
// get the number of elements
$numElems = $elemParnt->childNodes->length;

switch ($act) {
    case "del":
    	// find image file to remove using xpath
		$xpath = new DOMXPath($doc);
		$imgObj = $xpath->query("//sElem/text");
		$imgFile = $imgObj[$index]->nodeValue;
		
		// if this is an image file erase it -- last three characters are mp4,jpg or png
		if (preg_match('/(mp4|jpg|png)$/', $imgFile)) {
			unlink('images/' . $imgFile);  // delete that image file					
		}

		// removeChild is a method of the parent node so have to have parentnode in the expression
		$elemToGo->parentNode->removeChild($elemToGo); // removes the node from the parent which is the root
		break;
	case "movup":
        if ($index == 0) {
        	break;
        }
        $elemBefore = $elemParnt->childNodes->item($elem - 1); // preveous node
		// $elemParnt->insertBefore($elemToGo,$elemBefore);
		$elemToGo->parentNode->insertBefore($elemToGo,$elemBefore);
        break;
    case "movdown":
        if ($index == ($numElems -1)) {
        	break;
        }
        // duh, there is no insertAfter so just switch nodes and use insertBefore 
        $elemAfter = $elemParnt->childNodes->item($elem + 1);  // preveous node
		$elemToGo->parentNode->insertbefore($elemAfter,$elemToGo);
        break;
    
    default:
        
}
$doc->save($wfile);  // save the wizardlist with the node gone			
$cStatus = "Success";
//print 'node value for image is '. $elemText . '<br>';

print 'looking for text <br>';
echo "<pre>";
print_r($imgFile);
echo "</pre>";
// print 'parent element <br>';
// echo "<pre>";
// print_r($elemParnt);
// echo "</pre>";
//var_dump($elemToGo);
//echo 'command was ' . $act;
?>