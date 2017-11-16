<?php
/* This is the ask for input module, modeled after text_element
It uses templates to create the layout. */
// set the title
define('WIZTITLE', 'Add or Edit a Request for Input');
// set the four buttons left to right Edit/back nav, Settings, Preview, Done/plus sign
define('BUTTON_1', '<a href="add_step.php" class="btn btn-primary" role="button">
         			<span class="glyphicon glyphicon-chevron-left"></span>The Step
         			</a>');
define('BUTTON_2', '');
define('BUTTON_3', '');
define('BUTTON_4', '');
define('BUTTON_5', '');
define('BUTTON_6', '');
define('BUTTON_7', '<a href="add_step.php" class="btn btn-primary" role="button">
    				Cancel</span>
    				</a>');
// Include the header:
include 'templates/header_plus.html';
//see if this is adding a new element - add - or editing an old one
if ($_COOKIE['subBy'] == "add") {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
		if (isset($_POST['askInput'])) { // see if data is there
			$wizText = $_POST["askInput"];
			$wizInLabel = $_POST["askLabel"];
			// store the text
			storeData("Ask for Input",$wizInLabel,$wizText,"add"); // store the data in the xml file
			print "<h3>Your request has been saved.</h3>";
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
					<p> When you use the Calculate feature of Text elements, the wizard performs a calculation based 
					on a number that the user has already given the wizard. We don\'t know what that number will be so
					we have to give it a name to refer to it when it comes.  That is the name you will enter here.
					Just make up a word that starts with a letter and is composed of only letters and numbers (no dashes, underlines, etc.).
					For more information select Help.</p>
					<form action="askInput_element.php" method="post">
						Label for the input box:<br>
						<input type="text" name="askLabel" value="'. $wizInLabel . '">
						<br>
						<br>
						Name of the number provided:<br>
					   <input type="text" name="askInput" value="' . $wizText . '">
					   <br>
					   <br>
					   <input type="submit" value="Submit">
					</form>
			     </div>';
	}
} else {  //must be edit
// 	print 'got to the edit section';
// 	print '<br>';
	if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
		if (isset($_POST["askInput"])) { // user has edited the placeholder
			$wizText = $_POST["askInput"];
			$wizInLabel = $_POST["askLabel"];
// 			print 'got to the post data section';
// 			print '<br>';
// 			print $wizText;
// 			print '<br>';
			// store the data and overwrite what is there
			storeData("Ask for Input",$wizInLabel, $wizText,"replace"); // store the text in the xml file
			print "<h3>Your edited request has been saved.</h3>";
			 
		} else { // must be no forms submitted
			
			print "<h3>There was an error retrieving the data.</h3>";			
		}
	} else { // form not submitted yet
		// get the attribute and value from xml
		$elchildren = getStepElement();
		$enum2 =intval($_COOKIE['c_sele']);  // element number
		$wizText = $elchildren[$enum2]->text;
		$wizInLabel = $elchildren[$enum2]->label;
// 		print $wizText;
// 		print '<br>';
		  print '<div class="row">
					<div class="col-xs-6">
						<p> Edit the name that will refer to the number the user provides to the wizard.
						Be sure the word that starts with a letter and is composed of only letters and numbers (no dashes, underlines, etc.).
						For more information select Help.</p>
						<form action="askInput_element.php" method="post">
							Label for the input box:<br>
							<input type="text" name="askLabel" value="'. $wizInLabel . '">
							<br>
							Name of the number provided:<br>
							<input type="text" name="askInput" value="' . $wizText . '">
							<br>
							<br>
							<input type="submit" value="Submit">
						</form>
					</div> 
				</div>';
	}
}
function storeData($elType,$wLabel,$value,$addrep) {
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
	$sxe = simplexml_import_dom($doc); 
	// at a new element to the list of elements.
	if ($addrep == "add") {
		// add a new element as child
		$sxe->step[$sIndex]->stepElems->addChild("sElem");
		// I have to find the last child (the one just added) and add more children
		$imIndex = $sxe->step[$sIndex]->stepElems[0]->count() -1;
		// store type of element
		$sxe->step[$sIndex]->stepElems->sElem[$imIndex]->addChild("type",$elType);
		// store yes or no -- is this a placeholder
		$sxe->step[$sIndex]->stepElems->sElem[$imIndex]->addChild("label",$wLabel);
		// store  text Content
		$sxe->step[$sIndex]->stepElems->sElem[$imIndex]->addChild("text",$value);
	} else {
		//just replace the edited element's data
		$temElems = $sxe->step[$sIndex]->stepElems[0]->children(); // get present tit
		$temElems[$enum3]->type = $elType;
		$temElems[$enum3]->label = $wLabel;
		$temElems[$enum3]->text = $value;
	}
	// now add an attribute to this element.
	//$sxe->step[$sIndex]->stepElems[0]->image[$imIndex]->addAttribute("sElem");
	$doc->loadXML($sxe->asXML()); // convert back to DOM document
	$doc->save($wfile);
}
function getStepElement() {
	// Label of the input and text for the variable as an array
	$wfile = $_COOKIE['c_file']; // the wizard file is kept as a cookie
	$sIndex =  intval($_COOKIE['c_snum']);  // step number minnus 1 is the xml index
	$doc1 = new DOMDocument();
	$doc1->preserveWhiteSpace = false;
	$doc1->formatOutput = true;  // so it will output nicely with indents
	$doc1->load($wfile); // load the wizard xml file
	//append the object
	$sxe1 = simplexml_import_dom($doc1); // convert to simpleXML object
	// Get the right piece of info -- name or description
	// will try to get the data by using the childres functoin reading into an array
	
	// print "from getstepElement -- enum is  " . $enum;
// 	print '<br>';
	$tempElems = $sxe1->step[$sIndex - 1]->stepElems[0]->children(); // get children of element
	return $tempElems;
}
include 'templates/footer.html'; // Include the footer.

 ?>