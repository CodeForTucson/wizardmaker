<?php
/* This page creates a new step and lists all elements. 11-10
  You can enter an title and instructions for the step and edit them later.
*/
// read the cookies
$wname = $_COOKIE['c_name'];
$wfile = $_COOKIE['c_file'];
$snum = $_COOKIE['c_snum'];  // step number
// set the title
define('WIZTITLE', $wname . ': Step ' . $snum );
// set the four buttons left to right Edit/back nav, Settings, Preview, Done/plus sign
define('BUTTON_1', '<button class="btn btn-primary" 
					onclick="setAndGo(0,\'back\')">
         			<span class="glyphicon glyphicon-chevron-left"></span>All Steps
         			</button>');
define('BUTTON_2', '<a href="" class="btn btn-primary" role="button">
         			Help
         			</a>');
define('BUTTON_3', '<a href="" class="btn btn-primary" role="button">
         			Edit
         			</a>');
define('BUTTON_4', '<a href="Preview.php?wFrom=onestep" class="btn btn-primary" role="button" target="_blank">
         			Preview
         			</a>');
define('BUTTON_5', '');
define('BUTTON_6', '');
define('BUTTON_7', '<button  class="btn btn-primary"  
					onclick="setAndGo(0,\'add\')">
    				<span class="glyphicon glyphicon-plus"></span>
    				</button>');
// define('BUTTON_7', '<button  class="btn btn-primary"  
// 					onclick="setAndGo(\'' . $eNum . '\',\'add\')">
//     				<span class="glyphicon glyphicon-plus"></span>
//     				</button>');

// Include the header:
include 'templates/header_plus.html';
?>
<!-- leave php to add javascript for setting cookies.
	need to do submit here and let post processing code save changes
-->
<script>
// !!! change this to get the element type and go to their page if it is an edit
// and bypass add_element.php
function setAndGo(ecount,subber) {
    document.cookie = "subBy=" + subber;  // sets the cookie subBy to subber
    document.cookie = "c_sele=" + ecount; // which element
    //submit the form -- when it comes back it will save the new data
    document.getElementById("settingsForm").submit();
    //window.location.assign("http://betterstuffbetterlife.com/pttrot/WizardMakerApp/add_element.php");
}
function gotoElement(ecount,gotoElPage) {
    document.cookie = "subBy=" + "edit";  // sets the cookie subBy to edit because you clicked an existing one
    document.cookie = "c_sele=" + ecount; // which element
    // need to create a path
    window.location.assign("http://betterstuffbetterlife.com/pttrot/WizardMakerApp/" + gotoElPage);
}
</script>
<?php
// Load in the wizard xml file
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
				$sxe->step[$snum - 1]->title = $stitle;
				$sxe->step[$snum - 1]->instruct = $sinstr;
		 	} else { // add a new step on the end`
				$sxe->addChild("step"); // add a new step to the end of all Steps
				// create an object that is the last instance of Step in the file
				$lastStep = $sxe->step[$sxe->step->count() - 1];
				$lastStep->addChild("title", $stitle); // add children to this last step
				$lastStep->addChild("instruct", $sinstr);
				$lastStep->addChild("stepElems"); // add location for the elements.		
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
				case "text":
					header('Location: text_element.php');
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
// Load and list all the elements of this step
// !! next line is wrong -- come back and fix
//$allElems = $sxe->xpath('/wizard/step[' . $snum . ']/stepElems/*');
// print 'step is ' . $snum . '<br>';
// print_r ($allElems);
// print '<br>';
// var_dump($allElems);
// print '<br>';
// print $allElems[0];
// print '<br>';

// print the list of elements but go directly to their page for edits, not add_element
//Uh, oh, this is all wrong need to go to the right page.
//foreach($allElems as $eNum => $eText) {
$elIndex = 0; // set this up as the element index number
foreach ($sxe->step[$snum -1]->stepElems[0]->children() as $selm) {
	//Pass the name of the element handler to gotoElement
	// 	print 'name is ' . $selm->getName() . '<br>';
	// 	print ' value is ' . $selm . '<br>';
	//    $elemName = $selm->getName();
    $elemName = $selm->type[0];
    $eltext = $selm->text[0];
	//print 'index is '. $eNum . ' value is ' . $eText . '<br>';
	switch ($elemName) {
				case "Picture or Video":
					$elHandle = "image_element.php";
					break;
				case "Text":
					$elHandle = "text_element.php";
					break;
				case "Ask for Input":
					$elabel = $selm->label[0];
					$elHandle = "askInput_element.php";
					break;										
				default:
					//print 'This part not done yet';
					$elHandle = "Error";
				}
	print '<div class="row">
			<div class="col-xs-4">';
	print '<button class="btn-info" onclick="gotoElement(\'' . $elIndex . '\',\''. $elHandle . '\')">' . $elemName . '</button><br>';
	print '</div>';
	print '<div class="col-xs-6">';
	// in the future we will put an image, variable name or part of the text by each element
	// depending on the type of element
	if ($elemName == "Text") {
		print strip_tags(substr($eltext,0,25) . "...");  // clean out special characters
	} 
	
	if ($elemName == "Ask for Input") { 
		print $elabel . ',  Name of input is: ' . $eltext;
	}
	
	if ($elemName == "Picture or Video") { 
		if ($selm->place[0] == "no") {
			// logic determins if I need an image or video control
			// checks the last 4 characters to see if it is a video file
			// this uses the bootstrap image control to keep things neet
			print $eltext . '   ';
				if (substr_compare($eltext,".mp4",-4,4,TRUE) == 0) {
					print '<br>';
					print '<video width="93" height="70">
								<source src="images/'. $eltext . '" type="video/mp4">
								 Your browser does not support the video tag.
							</video>';
					// print '<br>';		
				} else {
					print '<br>';
					print '<img src="images/'. $eltext . '" class="img-thumbnail" alt="Picture missing" style="width:auto;height:70px;">';
					// print '<br>';
				} 
	
		} else if ($selm->place[0] == "yes") {
			print $eltext . '   ';
			print ' (Placeholder Text)';
		} else  {
		
		}	
			
	}
	
	
 	//print  "";       
 	print '</div>';
	print '</div>';
	print '<br>';
	$elIndex = $elIndex + 1; // index of elements
}

include 'templates/footer.html'; // Include the footer.
?>