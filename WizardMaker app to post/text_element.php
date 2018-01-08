<?php
/*
================================================================================

WizardMaker project - text_element.php.  Adds a block of text to the wizard.
Copyright (C) 2018 Paul Tynan <http://www.betterstuffbetterlife.com/>

================================================================================

This program uses opensource code under GNU General Public License.  See the file beolw for more information.
      File: widgEdit.js
      Created by: Cameron Adams (http://www.themaninblue.com/)
      Created on: 2005-01-16

*/
// set the title
define('WIZTITLE', 'Add or Edit Text');
// set the four buttons left to right Edit/back nav, Settings, Preview, Done/plus sign
define('BUTTON_1', '<a href="add_step.php" class="btn btn-primary" role="button">
         			<span class="glyphicon glyphicon-chevron-left"></span>The Step
         			</a>');
define('BUTTON_2', '<a href="Help/Text_help.html" class="btn btn-primary" role="button" target="_blank">
         			Help
         			</a>');
define('BUTTON_3', '');
define('BUTTON_4', '');
define('BUTTON_5', '');
define('BUTTON_6', '');
define('BUTTON_7', '<a href="add_step.php" class="btn btn-primary" role="button">
    				Cancel</span>
    				</a>');
// Include the header:
include 'templates/header_plus.html';
/* !! add routine to step through the steps and for each one find all the variables
	and labels.  but only look in steps up to and including this step.
	Build up an array for variables and labels up to and including this step.
*/
$sIn = intval($_COOKIE['c_snum']);  // step number minnus 1 is the xml index
$xmlv=simplexml_load_file($_COOKIE['c_file']) or die("Error: Cannot create object");
//$wizVars = $xmlv->xpath('/wizard/step[' .$sIn . ']/stepElems/sElem[type = "Ask for Input"]/text');
//print 'Output of all the variables up to and including this step <br>';
//print 'step number is  ' . $sIn . '<br>';
//print '<br>';
$varMerge = array();
$labMerge = array();
for ($x = 1; $x <= $sIn; $x++) {
    $wizVars = $xmlv->xpath('/wizard/step[' . $x . ']/stepElems/sElem[type = "Ask for Input"]/text');
    $wizLabls = $xmlv->xpath('/wizard/step[' . $x . ']/stepElems/sElem[type = "Ask for Input"]/label');
    $varMerge = array_merge ($varMerge, $wizVars);
    $labMerge = array_merge ($labMerge, $wizLabls);
}
//var_dump($varMerge);
//foreach ($xmlv->step[i]->stepElems[0]->children() as $elems) {
//}
//check to see if POST data received
if ($_COOKIE['subBy'] == "add") { // adding a new bit of text
	if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
		if (isset($_POST['noise'])) { // store text here
			$wizText = $_POST["noise"];
			// store the text
			storeData("Text","na",$wizText,"add"); // store the data in the xml file
			print "<h3>Your text has been saved.</h3>";
			//print "$wizText";
			//sleep(10);
		} 
	} else { // must be no data -- no forms submitted so ask for text
		$wizText = '';
// 		print 'Got to add but no post';
// 		print '<br';
		//sleep(10);
		
	  print '<div class="row">
					<div class="col-xs-6">
					<p> Enter some text for this step. 
					To add a calculation type in a expresion using * to multiply, / 
					to divide, + to add and - to subtract. For example you might enter
					"numServings*3" where numServings is the name of a number entered by a user
					because you used an Ask for Input element. Use parentheses for more complex expressions.
					</p>
					<p>
					Next, select this expression and click or touch the Calculation icon 
					<img src="wizassets/CalculatorIcon.gif" alt="calc icon" align="bottom" style="height:22px;width:22px">.
					The expression will appear in italics. When the end user sees the final wizard
					only the calculated number will appear. Use Preview to check this.
					Also, see Help for more information.
					</p>';
			// get and list all variables in the wizard so far
			// load the wizard file
			//$xmlv=simplexml_load_file($_COOKIE['c_file']) or die("Error: Cannot create object");
			// need xpath to find all the variables in the wizard
			//$wizVars = $xmlv->xpath('/wizard/step/stepElems/sElem[type = "Ask for Input"]/text');
			//$wizLabls = $xmlv->xpath('/wizard/step/stepElems/sElem[type = "Ask for Input"]/label');				
			if ($varMerge[0] != "") {
				print '<p> Here are the names of the inputs you can use in your expressions.</p>';
				for ($vCnt = 0; $vCnt < count($varMerge); $vCnt++) {
					print '<b>' . $varMerge[$vCnt] . '</b> (' . $labMerge[$vCnt] .  ')<br>';
				}
			} else {	
				print '<p><b>No names of user input are yet available. You cannot add calculations to your
								text.</b></p>';
			}		
					
				Print '<br><form action="text_element.php" method="post">
						<fieldset>
							<label for="noise">Enter text here:</label>
							<textarea id="noise" name="noise" class="widgEditor nothing">'
							. $wizText .
							'</textarea>
						</fieldset>
						<fieldset class="submit">
							<button type="submit" class="btn btn-primary">Save</button>
						
						</fieldset>
					</form>
					<br>
				</div> 
				<div class="col-xs-6">
				</div>
			</div>';
				
	}
} else {  //must be edit
// 	print 'got to the edit section';
// 	print '<br>';
	if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
		if (isset($_POST["noise"])) { // user has edited the placeholder
			$wizText = $_POST["noise"];
// 			print 'got to the post data section';
// 			print '<br>';
// 			print $wizText;
// 			print '<br>';
			// store the data and overwrite what is there
			storeData("Text","na", $wizText,"replace"); // store the text in the xml file
			print "<h3>Your edited text has been saved.</h3>";
			 
		} else { // must be no forms submitted
			
			print "<h3>There was an error retrieving the data.</h3>";			
		}
	} else { // form not submitted yet
		// get the attribute and value from xml
		$elchildren = getStepElement();
		$enum2 =intval($_COOKIE['c_sele']);  // element number
		$wizText = $elchildren[$enum2]->text;
// 		print $wizText;
// 		print '<br>';
		  print '<div class="row">
					<div class="col-xs-6">
					<p> Edit the text for this step. 
					To add a calculation type in a expresion using * to multiply, / 
					to divide, + to add and - to subtract. For example you might enter
					"numServings*3" where numServings is the name of a number entered by a user
					because you used an Ask for Input element. Use parentheses for more complex expressions.
					</p>
					<p>
					Next, select this expression and click or touch the Calculation icon 
					<img src="wizassets/CalculatorIcon.gif" alt="calc icon" align="bottom" style="height:22px;width:22px">.
					The expression will appear in italics. When the end user sees the final wizard
					only the calculated number will appear. Use Preview to check this.
					Also, see Help for more information.
					</p>';
			// get and list all variables in the wizard so far
			// load the wizard file
			//$xmlv=simplexml_load_file($_COOKIE['c_file']) or die("Error: Cannot create object");
			// need xpath to find all the variables in the wizard
			//$wizVars = $xmlv->xpath('/wizard/step/stepElems/sElem[type = "Ask for Input"]/text');
			//$wizLabls = $xmlv->xpath('/wizard/step/stepElems/sElem[type = "Ask for Input"]/label');				
			if ($varMerge[0] != "") {
				print '<p> Here are the names of the inputs you can use in your expressions.</p>';
				for ($vCnt = 0; $vCnt < count($varMerge); $vCnt++) {
					print '<b>' . $varMerge[$vCnt] . '</b> (' . $labMerge[$vCnt] .  ')<br>';
				}
			} else {	
				print '<p><b>No names of user input are yet available. You cannot add calculations to your
								text.</b></p>';
			}		
					
				Print '<br><form action="text_element.php" method="post">
						<fieldset>
							<label for="noise">Enter text here:</label>
							<textarea id="noise" name="noise" class="widgEditor nothing">'
							. $wizText .
							'</textarea>
						</fieldset>
						<fieldset class="submit">
							<button type="submit" class="btn btn-primary">Save</button>
						
						</fieldset>
					</form>
					<br>
				</div> 
				<div class="col-xs-6">
				</div>
			</div>';
			// replaced above <input type="submit" value="Submit" />
									
	}
}
function storeData($elType,$place, $value,$addrep) {
	// asks for element type, if a placekeeper, the text to save, and to add or edit.
	//load in the xmp file for this wizard
	$wfile = $_COOKIE['c_file']; // the wizard file is kept as a cookie
	$sIndex = intval($_COOKIE['c_snum'] -1);  // step number minnus 1 is the xml index
	$enum3 =intval($_COOKIE['c_sele']);  // element number
	// use the DOM to load because it makes it human readable.
	$doc = new DOMDocument();
	$doc->preserveWhiteSpace = false;
	$doc->formatOutput = true;  // so it will output nicely with indents
	$doc->load($wfile); // load the wizard xml file
	//Convert to simplXML object for ease of manipulation
	//$sxe = simplexml_import_dom($doc); 
	// at a new element to the list of elements.
	if ($addrep == "add") {
		// use DOM rather than simplexml
		$selems = $doc->getElementsByTagName("stepElems"); //get the array of element parent node
		$newsElem = $doc->createElement("sElem");			// create a new wizard element node
		$selems->item($sIndex)->appendChild($newsElem); //append the wizard element node
		$newsElem->appendChild($doc->createElement("type",$elType)); //append child with type
		$newsElem->appendChild($doc->createElement("place",$place)); //append child with placeholder
		$cdNode = $doc->createElement("text");  // create the text element
		$newsElem->appendChild($cdNode);  // append it to the elements parent
		$cd = $doc->createCDATASection($value);  // create CDATA section out of text
		$cdNode->appendChild($cd);  // append CDATA to the node like it was a textnode.
		
		// add a new element as child
		// $sxe->step[$sIndex]->stepElems->addChild("sElem");
// 		// I have to find the last child (the one just added) and add more children
// 		$imIndex = $sxe->step[$sIndex]->stepElems[0]->count() -1;
// 		// store type of element
// 		$sxe->step[$sIndex]->stepElems->sElem[$imIndex]->addChild("type",$elType);
// 		// store yes or no -- is this a placeholder
// 		$sxe->step[$sIndex]->stepElems->sElem[$imIndex]->addChild("place",$place);
// 		// store  text Content
// 		$sxe->step[$sIndex]->stepElems->sElem[$imIndex]->addChild("text",$value);
	} else {
		//just replace the edited element's data
		// get old text node with CDATA
		//$textold = $doc->getElementsByTagName("text")->item($enum3);
		// new code -- get to the right text node
		$textold = $doc->getElementsByTagName("stepElems")->item($sIndex);
		$tarElem = $textold->getElementsByTagName("sElem")->item($enum3);
		$tarText = $tarElem->getElementsByTagName("text")->item(0);
		// create new text node and apppend it
		$textrep = $tarText->parentNode->appendChild($doc->createElement('text'));
		$cdr = $doc->createCDATASection($value);  // create CDATA section out of text
		$textrep->appendChild($cdr);		// append it to the new text node
		$tarText->parentNode->replaceChild($textrep,$tarText);  // replace the old with new
		
		// fixture code to compare
		// $textrep = $tarText->parentNode->appendChild($doc2->createElement('text'));
// 		$cdr = $doc2->createCDATASection('The new test text');  // create CDATA section out of text
// 		$textrep->appendChild($cdr);		// append it to the new text node
// 		$tarText->parentNode->replaceChild($textrep,$tarText);

	}
	
	$doc->save($wfile);
}
function getStepElement() {
	// name of the element and value as an array
	$wfile = $_COOKIE['c_file']; // the wizard file is kept as a cookie
	$sIndex =  intval($_COOKIE['c_snum']);  // step number minnus 1 is the xml index
	$doc1 = new DOMDocument();
	$doc1->preserveWhiteSpace = false;
	$doc1->formatOutput = true;  // so it will output nicely with indents
	$doc1->load($wfile); // load the wizard xml file
	//append the object
	$sxe1 = simplexml_import_dom($doc1); // convert to simpleXML object
	// Get the right piece of info -- name or description
	// will try to get the data by using the childres function reading into an array
	
	// print "from getstepElement -- enum is  " . $enum;
// 	print '<br>';
	$tempElems = $sxe1->step[$sIndex - 1]->stepElems[0]->children(); // get children of element
	return $tempElems;
}
include 'templates/footer.html'; // Include the footer.

 ?>