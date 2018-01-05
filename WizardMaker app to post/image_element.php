<?php
/* 
================================================================================

WizardMaker project - image_element.php.  Home page of the WizardMaker.
Copyright (C) 2018 Paul Tynan <http://www.betterstuffbetterlife.com/>

================================================================================
*/
define('WIZTITLE', 'Picture or Video');
// set the four buttons left to right Edit/back nav, Settings, Preview, Done/plus sign
// define('BUTTON_1', '<a href="add_step.php" class="btn btn-primary" role="button">
//          			<span class="glyphicon glyphicon-chevron-left"></span>The Step
//          			</a>');
define('BUTTON_1', '<a href="add_step.php" class="btn btn-primary" role="button">
         			<span class="glyphicon glyphicon-chevron-left"></span>The Step
         			</a>');
// define('BUTTON_1', '<button class="btn btn-primary" 
// 					onclick="submitAndGo()">
//          			<span class="glyphicon glyphicon-chevron-left"></span>The Step
//          			</button>');
define('BUTTON_2', '<a href="Help/Image_help.html" class="btn btn-primary" role="button" target="_blank">
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
?>
<script>
function validateForm() {
	//alert("OK, validating");
    var x = document.forms["phName"]["phTest"].value;
    if (x == "") {
        alert("Name must be filled out");
        window.location.assign("add_step.php")
        return false;
    }
}
function submitAndGo() {
// this is only used to execute the submit of the placeholder form the back BUTTON
// for upload we will use a button and get feedback that it was successful
// The validateForm routine above till just navigate back if no data has 
// been put in the placeholder form
    document.getElementById("phForm").submit();
    //window.location.assign("http://betterstuffbetterlife.com/pttrot/WizardMakerApp/add_element.php");
}
</script>
<?php
// get step, element index and file name
// see flowchart -- first check to see if this is an Add or an Edit operation 
// if data came in, store it, if not, show the two choices of forms
if ($_COOKIE['subBy'] == "add") {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
		if (isset($_POST["phTest"])) { // store place holder here
			$plHolder = $_POST["phTest"];
			//print "placeholder is " . $plHolder;
			storeData("Picture or Video","yes", $plHolder,"add"); // store the data in the xml file
			print "<h3>Your placeholder has been saved.</h3>";
			// have to save this as image element then exit back to Add Step
			//header('Location: add_step.php');
		} else { // must be file
			$imfileName = imageUpload(); // upload the image to the file
			//print "file name is  " . $imfileName;
			storeData("Picture or Video","no", $imfileName,"add"); // store the text in the xml file
		}
	} else { // must be no data -- no forms submitted
	  // no form submitted find out what is in xml and allow options to change
	include 'templates/Image_noPost.html';
	}	
} else {  //must be edit
	if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
		if (isset($_POST["phTest"])) { // user has edited the placeholder
			$plHolder = $_POST["phTest"];
			//print "placeholder is " . $plHolder;
			storeData("Picture or Video","yes", $plHolder,"replace"); // store the text in the xml file
			print "<h3>Your placeholder has been edited.</h3>";
			// have to save this as image element then exit back to Add Step
			//header('Location: add_step.php');
		} else { // must be file upload to replace the placeholder
			$imfileName = imageUpload(); // upload the image to the file
			//print "file name is  " . $imfileName;
			storeData("Picture or Video","no", $imfileName,"replace"); // store the text in the xml file
		}
	} else { // must be no forms submitted
		// get the attribute and value from xml
		$enum2 =intval($_COOKIE['c_sele']);  // element number
		$elchildren = getStepElement();
// 		print "enum is " .  $enum2;
// 		print '<br>';
// 		var_dump($elchildren);
// 		print '<br>';
		// if a placeholder then define the value to show in the form
		$tempPlace = $elchildren[$enum2]->place;
		// print "place = " . $tempPlace;
// 		print '<br>';
		if ($tempPlace == "yes") { // yes, a placeholder
			// print "yes, a placeholder and text is " . $elchildren[$enum2]->text;
			$defValue = $elchildren[$enum2]->text;
			// define a control for image_edit to show the placeholder
			define('SHOWPLACE', '<form action="image_element.php" method="post" onsubmit="return validateForm()" id="phForm" name ="phName">
									<div class="form-group">
									  <label for="idname">Placeholder Text:</label>
									  <input type="text" class="form-control" id="idname" value="' . $defValue . '" name="phTest">
									  <br>
									  <button type="submit" class="btn btn-primary">Save</button>
									</div>
								</form>');
			include 'templates/Image_Edit.html';
			// replaced this <input id="wSubmit" type="submit" value="Submit">
				
		} else {
			// define a line telling the user what file is uploaded and then include the html.
			$defValue = $elchildren[$enum2]->text;
			// put logic to show picture or video
			if (substr_compare($defValue,".mp4",-4,4,TRUE) == 0) {
				define('SHOWPLACE', '<form action="image_element.php" method="post" onsubmit="return validateForm()" id="phForm" name ="phName">
										<div class="form-group">
										  <h4> Your video file is named ' . $defValue . '</h4>
										 	 <video width="372" height="280" controls>
												<source src="images/'. $defValue . '" type="video/mp4">
											 	Your browser does not support the video tag.
											</video>
										</div>
									</form>');				
			} else {

				define('SHOWPLACE', '<form action="image_element.php" method="post" onsubmit="return validateForm()" id="phForm" name ="phName">
										<div class="form-group">
										  <h4> Your image file is named ' . $defValue . '</h4>
										  <img src="images/'. $defValue . '" style="width:200px;height:auto;" >
										</div>
									</form>');			
			}							
			// include the new buttons and controls					
			include 'templates/Image_Edit.html';
		}
		
		
		// and load the controls that provide a choice of editing the 
		// placeholder or replacing it with an image 
		// include 'templates/Image_edit.html';
	}
}
// php funcitons
function imageUpload() {
	// print "at image upload function.";
	$target_dir = "images/";
	// the $_FILES super global variable here will get the original file name.
	// and name is the extention
	//together $target_file is the path on the server for the file
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	// The pathinfo() function returns an array that contains information about a path; dir, basename, ext.
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	// make sure size is not too large but see the php manual that can block the big file before it is uploaded
	// using file_size_max or some such
	if ($_FILES["fileToUpload"]["size"] > 900000000) { // limit 900 mb
		echo "Sorry, your file is too large.";
		$uploadOk = 0;
	}
	// search for a dot, sync2 was a cookie with P's sync number, for every dot in $target_file
	// comment out for now and later replace with a unique number for the number if image files
	//$targetSync_file = str_replace(".",$sync2 .  ".", $target_file);
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		echo "Sorry, your file was not uploaded.";
	} else {
		// $target_file it the location including the name 
		// tmp_name is the temporary name of the file -- it was stored in some scratchpad place
		// on the surver.
		// if the move worked must return true -- might print the error there.
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			echo "<h3>The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.</h3>";			
		} else {
			echo "<h3>Sorry, there was an error uploading your file.</h3>";
		}
    }
    return basename( $_FILES["fileToUpload"]["name"]);
}
// function to store new image element
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
	// pass in the index of the step, index of the element and file name
	//and get attribute and
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
	// will try to get the data by using the childres functoin reading into an array
	
	// print "from getstepElement -- enum is  " . $enum;
// 	print '<br>';
	$tempElems = $sxe1->step[$sIndex - 1]->stepElems[0]->children(); // get children of element
	return $tempElems;
}

// footer commands and material
include 'templates/footer.html'; // Include the footer.

?>