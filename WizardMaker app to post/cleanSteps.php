<?php
// get the wizard index re wizardlist
$step = $_REQUEST["q"];  // index of step to be processed
$act = $_REQUEST["a"];  // action: move up, move down or delete
$wfile = $_COOKIE['c_file'];             // get wizard xml file name from cookie.
//get file name
$index = (int)$step;
// create the dom for moving or deleting the wizardlisting node
$doc = new DOMDocument;
$doc->load($wfile);
$stepArray = $doc->documentElement; //create an array of step nodes
$numSteps = $stepArray->getElementsByTagName('step')->length;
// just get the node to remove or move
$stepToGo = $stepArray->getElementsByTagName('step')->item($index);  // item is to access by index number
// now either delete or move the node
switch ($act) {
    case del:
    	// using simplexml to clean up files and remove files
		$xml2=simplexml_load_file($wfile) or die("Error: Cannot create object");
		// need xpath to find all the variables in the wizard
		$stepImagess = $xml2->xpath('/wizard/step[$index]/stepElems/sElem[type = "Picture or Video"]/text');
		//var_dump($stepImagess);
		// delete all images and movies
		foreach ($stepImagess as $imText) { 
			// if this is an image file erase it -- last three characters are mp4,jpg or png
			if (preg_match('/(mp4|jpg|png)$/', $imText)) {
				unlink('images/' . $imText);  // delete that image file
				//echo $imText;					
			}
		}
		
		// removeChild is a method of the parent node so have to have parennode in the expression
		//$wparent = $stepToGo->parentNode;
		$stepToGo->parentNode->removeChild($stepToGo); // removes the node from the parent which is the root
		break;
	case movup:
        if ($index == 0) {
        	break;
        }
        $stepBefore = $stepArray->getElementsByTagName('step')->item($index-1); // preveous node
		$stepToGo->parentNode->insertBefore($stepToGo,$stepBefore);
		
        break;
    case movdown:
        if ($index == ($numSteps -1)) {
        	break;
        }
        // duh, there is no insertAfter so just switch nodes and use insertBefore 
        $stepAfter = $stepArray->getElementsByTagName('step')->item($index+1); // preveous node
		$stepToGo->parentNode->insertbefore($stepAfter,$stepToGo);
        break;
    
    default:
        
}
$doc->save($wfile);  // save the wizardlist with the node gone			
$cStatus = "Success";
// echo "<pre>";
// print_r($stepArray);
// echo "</pre>";
//var_dump($stepToGo);
//echo $numSteps;
?>