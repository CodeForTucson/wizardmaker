<?php
/*
This program uses opensource code under GNU General Public License.  See the file beolw for more information.
      File: widgEdit.js
      Created by: Cameron Adams (http://www.themaninblue.com/)
      Created on: 2005-01-16
It uses templates to create the layout. */
// set the title
define('WIZTITLE', 'Add or Edit Text');
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
//check to see if POST data received, if so set cookies and go to wiz_step.php
if ($_COOKIE['subBy'] == "add") {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
		if (isset($_POST['noise'])) { // store place holder here
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
					<p> Enter more detailed instructions for your step.  To perform a calculation bracket the equation with "cal#" at the front and "#" at the end. 
						For example, if you named a user input "Width" using Ask for Input you could create cal#(Width/2)+2# to divide it in half and add 2.
						and add 2 to it.</p>
					<form action="text_element.php" method="post">
						<fieldset>
							<label for="noise">Enter text here:</label>
							<textarea id="noise" name="noise" class="widgEditor nothing">'
							. $wizText .
							'</textarea>
						</fieldset>
						<fieldset class="submit">
							<input type="submit" value="Submit" />
						</fieldset>
					</form>
					<br>
				</div> 
				<div class="col-xs-6">
		
				</div>
			</div>';
			// get and list all variables in the wizard so far
			// load the wizard file
			$xmlv=simplexml_load_file($_COOKIE['c_file']) or die("Error: Cannot create object");
			// need xpath to find all the variables in the wizard
			$wizVars = $xmlv->xpath('/wizard/step/stepElems/sElem[type = "Ask for Input"]/text');
			$wizLabls = $xmlv->xpath('/wizard/step/stepElems/sElem[type = "Ask for Input"]/label');
			
			if ($wizLabls[0] != "") {
				print '<div class="row">
						<div class="col-xs-6">
							<p> Here are all the labels the input boxes you created (using the Ask for Input element) followed by the name
							of the number you are collecting from the user.  If you are adding calculations, use these names (in bold) in the calculations
							you enter into the text above.</p>';
				for ($vCnt = 0; $vCnt < count($wizVars); $vCnt++) {
					print $wizLabls[$vCnt] . '   <b>' . $wizVars[$vCnt] . '</b> <br>';
			
				}
				print '</div> 
								<div class="col-xs-6">

								</div>
						</div>';
			}
				
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
						<p> Edit the instructions for your step.  To perform a calculation bracket the equation with "cal#" at the front and "#" at the end. 
						For example, if you named a user input "Width" using Ask for Input you could create cal#(Width/2)+2# to divide it in half and add 2.
						and add 2 to it.</p>
						<form action="text_element.php" method="post">
							<fieldset>
								<label for="noise">Enter text here:</label>
								<textarea id="noise" name="noise" class="widgEditor nothing">'
								. $wizText .
								'</textarea>
							</fieldset>
							<fieldset class="submit">
								<input type="submit" value="Submit" />
							</fieldset>
						</form>
						<br>
					</div> 
					<div class="col-xs-6">

					</div>
				</div>';
			// get and list all variables in the wizard so far
			// load the wizard file
			$xmlv=simplexml_load_file($_COOKIE['c_file']) or die("Error: Cannot create object");
			// need xpath to find all the variables in the wizard
			$wizVars = $xmlv->xpath('/wizard/step/stepElems/sElem[type = "Ask for Input"]/text');
			$wizLabls = $xmlv->xpath('/wizard/step/stepElems/sElem[type = "Ask for Input"]/label');
			
			if ($wizLabls[0] != "") {
				print '<div class="row">
						<div class="col-xs-6">
							<p> Here are all the labels the input boxes you created (using the Ask for Input element) followed by the name
							of the number you are collecting from the user.  If you are adding calculations, use these names (in bold) in the calculations
							you enter into the text above.</p>';
				for ($vCnt = 0; $vCnt < count($wizVars); $vCnt++) {
					print $wizLabls[$vCnt] . '   <b>' . $wizVars[$vCnt] . '</b> <br>';
			
				}
				print '</div> 
								<div class="col-xs-6">

								</div>
						</div>';
			}

				
	}
}
function storeData($elType,$place, $value,$addrep) {
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
		$sxe->step[$sIndex]->stepElems->sElem[$imIndex]->addChild("place",$place);
		// store  text Content
		$sxe->step[$sIndex]->stepElems->sElem[$imIndex]->addChild("text",$value);
	} else {
		//just replace the edited element's data
		$temElems = $sxe->step[$sIndex]->stepElems[0]->children(); // get present tit
		$temElems[$enum3]->type = $elType;
		$temElems[$enum3]->place = $place;
		$temElems[$enum3]->text = $value;
	}
	// now add an attribute to this element.
	//$sxe->step[$sIndex]->stepElems[0]->image[$imIndex]->addAttribute("sElem");
	$doc->loadXML($sxe->asXML()); // convert back to DOM document
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