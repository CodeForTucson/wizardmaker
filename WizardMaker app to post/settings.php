<?php
/*This is the settings page  8-28-17
todo:
x- fixred filer name business to add directory and got rid of GET data 8-28
*/
// set the title
define('WIZTITLE', 'Settings');
// set the four buttons left to right Edit/back nav, Settings, Preview, Done/plus sign
define('BUTTON_1', '<a href="index.php" class="btn btn-primary" role="button">
         			Cancel
         			</a>');
define('BUTTON_2', '<a href="" class="btn btn-primary" role="button">
         			Help
         			</a>');
define('BUTTON_3', '');
define('BUTTON_4', '');
define('BUTTON_5', '');
define('BUTTON_6', '<button type="submit" form="settingsForm" class="btn btn-primary">
    				Done
    				</button>');
// Include the header:
include 'templates/header_plus.html';
// Leave the PHP section to display lots of HTML:
print '<h3> Enter the name of your wizard and a short description. </h3>';
// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Handle the form:
	if ( (!empty($_POST['wname'])) && (!empty($_POST['wdesc'])) ) {
		 // Correct! 
		 	$wname = $_POST["wname"]; // get user input
			$wdesc = $_POST["wdesc"];
			//$wfile2 = $wname . ".xml";  // .php to the name to make a file
			$wstepnum = 1; // since new, creat first step
		 	wizFiles($wname,$wdesc); //  save the data in xml 
		 	// Redirect the user to the welcome page!
			ob_end_clean(); // Destroy the buffer!
			// don't need GET data anymore
			// $hstring = "Location: add_step.php" . "?name=" . $wname . "&file=" . $wfile2 . "&snum=" . $wstepnum;
			$hstring = "Location: add_step.php";
			header($hstring);
			// header('Location: wiz_step.php');
			exit(); 
	} else { // Forgot a field.
		print '<p class="text--error">Please make sure you enter both a name 
		and a description!<br>Go back and try again.</p>';
	}
} else { // Display the form.
	print '<form action="settings.php" method="post" id="settingsForm">
				<div class="form-group">
				  <label for="idname">Name:</label>
				  <input type="text" class="form-control" id="idname" placeholder="Enter name of the wizard" name="wname">
				</div>
				<div class="form-group">
				  <label for="iddes">Description:</label>
				  <input type="text" class="form-control" id="iddes" placeholder="Enter a short description" name="wdesc">
				</div>
  			</form>';
}
function wizFiles($name,$desc) {
	// create a file name from the wizard name -- put it in directory wizards
	$wfile = "wizards/" . $name . ".xml";  // add .php to the name to make a file
	// save key data as cookies
	setcookie('c_name', $name); // save the name of the wizard
	setcookie('c_file', $wfile); // save the file name of the wizard
	setcookie('c_snum', '1'); // save the name of the wizard
	// create the new xml file for the wizard
	$myfile = fopen($wfile, "w") or die("Unable to open file!");
	// write a few lines to create an xml file
	$txt = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	fwrite($myfile, $txt);
	$txt = "<wizard>\n";
	fwrite($myfile, $txt);
	$txt = "</wizard>";
	fwrite($myfile, $txt);
	fclose($myfile);
	// update wizardListing.xml with the name, desc an file 
	// use dom because then the output is readable and not one long line
	// first load the file
	//$xml=simplexml_load_file("wizardListing.xml") or die("Error: Cannot create object");
	$doc = new DOMDocument();
	$doc->preserveWhiteSpace = false;
	$doc->formatOutput = true;  // so it will output nicely with indents
	$doc->load('wizardListing.xml');
	//append the object
	$sxe = simplexml_import_dom( $doc ); // convert to simpleXML object
	$sxe->addChild("wizard"); // add a new wizard to the end of all wizards
	// create an object that is the last instance of wizard in the file
	$lastWizard = $sxe->wizard[$sxe->wizard->count() - 1];
	$lastWizard->addChild("wizname", $name); // add children to this last wizard
	$lastWizard->addChild("wizdesc", $desc);
	$lastWizard->addChild("wizfile", $wfile);

	$doc->loadXML($sxe->asXML()); // convert back to DOM document
	$doc->save("wizardListing.xml");
}

include 'templates/footer.html'; // Include the footer.
?>