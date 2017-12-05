
<?php
/*This is the settings page 
*/
// set the title
define('WIZTITLE', 'Settings');
// set the four buttons left to right Edit/back nav, Settings, Preview, Done/plus sign
define('BUTTON_1', '<a href="index.php" class="btn btn-primary" role="button">
         			Cancel
         			</a>');
define('BUTTON_2', '<a href="Help/Settings_help.html" class="btn btn-primary" role="button" target="_blank">
         			Help
         			</a>');
define('BUTTON_3', '');
define('BUTTON_4', '');
define('BUTTON_5', '');
define('BUTTON_6', '');
define('BUTTON_7', '<button type="submit" form="settingsForm" class="btn btn-primary">
    				Done
    				</button>');
// Include the header:
include 'templates/header_plus.html';
// Leave the PHP section to display lots of HTML:
print '<h3> Enter the name of your wizard and a short description. </h3>';
// Check if the form has been submitted:
$wizIndex = $_COOKIE['c_windex'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Handle the form:
	if ( (!empty($_POST['wname'])) && (!empty($_POST['wdesc'])) ) {
		$wname = $_POST["wname"]; // get user input
		$wdesc = $_POST["wdesc"];
		if($_COOKIE['c_from'] == "index") {
			$wstepnum = 1; // since new, creat first step
			$maxWiz = $_COOKIE['c_wmax'] + 1;
			wizFiles($wname,$wdesc,$maxWiz); //  save the data in xml 
			$hstring = "Location: add_step.php";
		} else { 
			wizUpdate($wizIndex,$wname,$wdesc); // just update the wizard listing
			$hstring = "Location: wiz_step.php";
		} 
		ob_end_clean(); // Destroy the buffer!
		header($hstring);
		exit(); 
	} else { // Forgot a field.
		print '<p class="text--error">Please make sure you enter both a name 
		and a description!<br>Go back and try again.</p>';
	}
} else { // Display the form. If coming from All Steps, show the present title and decription for editing.
// check the cookies and be prepared to go to All Steps if it came from there.
	if($_COOKIE['c_from'] == "wizstep") {
		$placeName = wizListGet($wizIndex,"name");
		$placeDesc = wizListGet($wizIndex,"desc");
	} else {
		$placeName = "";
		$placeDesc = "";
	}
	print '<div class="row">
			<div class="col-xs-6">
				<form action="settings.php" method="post" id="settingsForm">
					<div class="form-group">
					  <label for="idname">Name:</label>
					  <input type="text" class="form-control" id="idname" value="' . $placeName . '" name="wname">
					</div>
					<div class="form-group">
					  <label for="iddes">Description:</label>
					  <input type="text" class="form-control" id="iddes" value="' . $placeDesc . '" name="wdesc">
					</div>
				</form>
  			</div>';
}
function wizFiles($name,$desc,$index) {
	// create a file name from the wizard name -- put it in directory wizards
	$wfile = "wizards/" . clean($name) . ".xml";  // add .php to the name to make a file
	// save key data as cookies 
	setcookie('c_name', $name); // save the name of the wizard
	setcookie('c_file', $wfile); // save the file name of the wizard
	setcookie('c_windex', $index); // increment the index
	//setcookie('c_desc', $desc); // save the description of the wizard
	setcookie('c_snum', '1'); // save the name of the wizard
	// create the new xml file for the wizard
	$myfile = fopen($wfile, "w") or die("Unable to open file!");
	// write a few lines to create an xml file
	$txt = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	fwrite($myfile, $txt);
	$txt = "<wizard>\n";
	fwrite($myfile, $txt);
// 	$txt = "<wizVars>\n";
// 	fwrite($myfile,$txt);
// 	$txt = "</wizVars>\n";
// 	fwrite($myfile,$txt);
	$txt = "</wizard>";
	fwrite($myfile, $txt);
	fclose($myfile);
	// update wizardListing.xml with the name, desc and file 
	// use dom because then the output is readable and not one long line
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
	// Add an empty node to hold all global variables for the wizard -- to be filled later
	$lastWizard->addChild("wizVars", "");
	$doc->loadXML($sxe->asXML()); // convert back to DOM document
	$doc->save("wizardListing.xml");
}
// call this to get informaton from wizlisting.xml -- name or description
function wizListGet($index,$NorD) {
	$doc1 = new DOMDocument();
	$doc1->preserveWhiteSpace = false;
	$doc1->formatOutput = true;  // so it will output nicely with indents
	$doc1->load("wizardListing.xml"); // load the wizard xml file
	//append the object
	$sxe1 = simplexml_import_dom($doc1); // convert to simpleXML object
	// Get the right piece of info -- name or description
	if ($NorD == "name") {  // if I want the name
		$temp =  $sxe1->wizard[$index - 1]->wizname; // get present title of the step
	} else {
		$temp = $sxe1->wizard[$index - 1]->wizdesc; // get present title of the step
	}
	//print $temp;
	return $temp;
}
// call this to just change the name and description
// look up listin by old name then change the name and description
function wizUpdate($wizI,$na,$de) {
	// use dom because then the output is readable and not one long line
	// first load the file
	$doc2 = new DOMDocument();
	$doc2->preserveWhiteSpace = false;
	$doc2->formatOutput = true;  // so it will output nicely with indents
	$doc2->load("wizardListing.xml"); // load the wizard listing xml file
	$sxe2 = simplexml_import_dom($doc2); // convert to simpleXML object
	// I have the index so replace the name and description with the edited ones
	$sxe2->wizard[$wizI - 1]->wizname = $na;
	$sxe2->wizard[$wizI - 1]->wizdesc = $de;
	$doc2->loadXML($sxe2->asXML()); // convert back to DOM document
	$doc2->save("wizardListing.xml");
	setcookie('c_name', $na); // save the name of the wizard	
}
// for cleaning up the file name
function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}
include 'templates/footer.html'; // Include the footer.
?>
