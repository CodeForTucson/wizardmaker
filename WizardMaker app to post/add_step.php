<?php
/* This page creates or edits a new step and lists all elements.  8/25/17
todo's:
x look at xml file and figure out if this step is new or an edit.
x if an edit, reload the title and instruction fields 
x Load the xml as we move to next page with changes
x figure out how to get the instruction text.
X  finish the part that decides where to go, make it a switch, not an if
- handle more than one step
*/
// read the cookies
$wname = $_COOKIE['c_name'];
$wfile = $_COOKIE['c_file'];
$snum = $_COOKIE['c_snum'];  // step number
// set the title
define('WIZTITLE',$wname . ' Step ' . $snum );
// set the four buttons left to right Edit/back nav, Settings, Preview, Done/plus sign
define('BUTTON_1', '<button class="btn btn-primary" 
					onclick="setAndGo(0,\'back\')">
         			<span class="glyphicon glyphicon-chevron-left"></span>All Steps
         			</button>');
define('BUTTON_2', '');
define('BUTTON_3', '');
define('BUTTON_4', '<button  class="btn btn-primary"  
					onclick="setAndGo(0,\'add\')">
    				<span class="glyphicon glyphicon-plus"></span>
    				</button>');
// define('BUTTON_4', '<button  class="btn btn-primary"  
// 					onclick="setAndGo(\'' . $eNum . '\',\'add\')">
//     				<span class="glyphicon glyphicon-plus"></span>
//     				</button>');

// Include the header:
include 'templates/header_plus.html';
?>
<!-- leave php to add javascript for setting cookies, the  go to wiz_step 
	need to do submit here and let post processing code save changes
-->
<script>
// change this to get the element type and go   . to their page if it is an edit
// and bypass add_element.php
function setAndGo(ecount,subber) {
    document.cookie = "subBy=" + subber;  // sets the cookie subBy to subber
    document.cookie = "c_sele=" + ecount; // which element
    //submit the form -- when it comes back it will save the new data
    document.getElementById("settingsForm").submit();
    //window.location.assign("http://betterstuffbetterlife.com/pttrot/WizardMakerApp/add_element.php");
}
</script>
<?php
/* Load in the wizard xml file
Preload the title and instructions if they exist using Value parameter for this step
*/ 
// use dom because then the output is readable and not one long line
// first load the file
$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;  // so it will output nicely with indents
$doc->load($wfile); // load the wizard xml file
//append the object
$sxe = simplexml_import_dom($doc); // convert to simpleXML object
// start here -- test to see if this step exists, if so grab data.
if (isset($sxe->step[$snum - 1])) {  // if this step exists
$titleValue = $sxe->step[$snum - 1]->title; // get present title of the step
$instValue = $sxe->step[$snum - 1]->instruct; // get present instructions for step
} else {
$titleValue = ''; // blanked these out because when first creating the step was a pain to delete
$instValue = '';
}
//print 'the title is ' . $titleValue . '<b>';
//Ask user to enter the title of the step and instructions (optional)
print '<h3> Enter or edit the title and instructions (optional) </h3>';
// use the form to  get these data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Handle the form:
	if (!empty($_POST['stitle'])) {
		 // Correct! 
		 	$stitle = $_POST["stitle"]; // get user input
			$sinstr = $_POST["sinstr"];
		 	// wizFiles($stitle,$sinstr); //  save the data in xml 
		 	//Hmm, check out this -1 business
		 	if (isset($sxe->step[$snum - 1])) {  // if this step exists
				// change values of title and instruction
				// wrong below, must change the specific node
				$sxe->step[$snum - 1]->title = $stitle;
				$sxe->step[$snum - 1]->instruct = $sinstr;
		 	} else { // add a new step on the end`
				$sxe->addChild("step"); // add a new step to the end of all Steps
				// create an object that is the last instance of Step in the file
				$lastStep = $sxe->step[$sxe->step->count() - 1];
				$lastStep->addChild("title", $stitle); // add children to this last step
				$lastStep->addChild("instruct", $sinstr);
				$lastStep->addChild("selements"); // add location for the elements.
			}
			$doc->loadXML($sxe->asXML()); // convert back to DOM document
			$doc->save($wfile);
			// Redirect the user to the element selection page!
			ob_end_clean(); // Destroy the buffer!
			//$hstring = "Location: add_element.php" . "?name=" . $wname . "&file=" . $wfile2;
			
			// header($hstring);
			// start here -- check the who-submitted cookie and decide where to go	
			$tempCook = $_COOKIE['subBy'];
			switch ($tempCook) {
				case "back":
					header('Location: wiz_step.php');
					break;
				case "add":
					header('Location: add_element.php');
					break;
				case "Ask for Input":
					header('Location: askInput_element.php');
					break;
				case "Picture or Video":
					header('Location: image_element.php');
					break;										
				default:
				print 'This part not done yet';
				}
			exit();								 
	} else { // Forgot a field.
		print '<p class="text--error">Please make sure you enter a name 
		 and try again.</p>';
	}
} else { // Display the form.
	print '<form action="add_step.php" method="post" id="settingsForm">
				<div class="form-group">
				  <label for="idname">Title:</label>
				  <input type="text" class="form-control" id="idname" value="' . $titleValue . '" name="stitle"> 
				</div>
				<div class="form-group">
				  <label for="iddes">Instructions:</label>
				  <input type="text" class="form-control" id="iddes" value="' . $instValue . '"  name="sinstr">
				</div>
  			</form>';
}
// second set of instructions
print '<h3> Select an element to edit or select + to add an element </h3>';
/* Load and list all the elements of this step
Add subroutine to set cookies and link away as done in index.PHP
*/
//print 'ok, got to the listing part <br>';
// test of xpath
$allElems = $sxe->xpath('/wizard/step[' . $snum . ']/selements/selem');
//print_r ($allElems);
//print '<br>';
//var_dump($allElems);
//print '<br>';
//print $allElems[0];
print '<br>';
// print the list of elements but go directly to their page for edits, not add_element
foreach($allElems as $eNum => $eText) {
	// print '<button class="btn-info" onclick="setAndGo(\'' . $eNum . '\',\'elem\')">' . $eText . '</button><br>';
	print '<button class="btn-info" onclick="setAndGo(\'' . $eNum . '\',\''. $eText . '\')">' . $eText . '</button><br>';
	print '<br>';
}

include 'templates/footer.html'; // Include the footer.
?>