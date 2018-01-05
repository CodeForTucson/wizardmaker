<?php
/* 
================================================================================

WizardMaker project - cleanup.php.  Moves or deletes elements of a step.
Called when you click the red button on the all steps page.
Copyright (C) 2018 Paul Tynan <http://www.betterstuffbetterlife.com/>

================================================================================

*/
// get the wizard index re wizardlist
$wiz = $_REQUEST["q"];
$act = $_REQUEST["a"];
//get file name
$index = (int)$wiz;
// create the dom for moving or deleting the wizardlisting node
$doc = new DOMDocument;
$doc->load("wizardListing.xml");
$wizArray = $doc->documentElement; //create an array of nodes
$numWiz = $wizArray->getElementsByTagName('wizard')->length;
// just get the node to remove or move
$wiztoGo = $wizArray->getElementsByTagName('wizard')->item($index);  // item is to access by index number
// now either delete or move the node
switch ($act) {
    case del:
    	// useing simplexml to clean up files and remove the wizard xml file
    	$xml = simplexml_load_file("wizardListing.xml") or die("Error: Cannot create object");
		$wfile = $xml->wizard[$index]->wizfile;    // get the path and file name of the wizard
		// get list of images and MovieScreenCapture
		$xml2=simplexml_load_file($wfile) or die("Error: Cannot create object");
		// need xpath to find all the variables in the wizard
		$wizImagess = $xml2->xpath('/wizard/step/stepElems/sElem[type = "Picture or Video"]/text');
		//var_dump($wizImagess);
		// delete all images and movies
		foreach ($wizImagess as $imText) { 
			// if this is an image file erase it -- last three characters are mp4,jpg or png
			if (preg_match('/(mp4|jpg|png)$/', $imText)) {
				unlink('images/' . $imText);  // delete that image file
				//echo $imText;					
			}
		}
		// delete the wizard xml file -- seems best done with DOM
		unlink($wfile);  // delete the wizardnxml file
		// removeChild is a method of the parent node so have to have parennode in the expression
		//$wparent = $wiztoGo->parentNode;
		$wiztoGo->parentNode->removeChild($wiztoGo); // removes the node from the parent which is the root
		break;
	case movup:
        if ($index == 0) {
        	break;
        }
        $wizBefore = $wizArray->getElementsByTagName('wizard')->item($index-1); // preveous node
		$wiztoGo->parentNode->insertBefore($wiztoGo,$wizBefore);
		
        break;
    case movdown:
        if ($index == ($numWiz -1)) {
        	break;
        }
        // duh, there is no insertAfter so just switch nodes and use insertBefore 
        $wizAfter = $wizArray->getElementsByTagName('wizard')->item($index+1); // preveous node
		$wiztoGo->parentNode->insertbefore($wizAfter,$wiztoGo);
        break;
    
    default:
        
}
$doc->save("wizardListing.xml");  // save the wizardlist with the node gone			
$cStatus = "Success";
// echo "<pre>";
// print_r($wizArray);
// echo "</pre>";
//var_dump($wiztoGo);
echo $numWiz;
?>