<?php
/* Preview. 
	Looks at a wizard xml file and generates the wizard based on those data
	It is called with a GET url from either the wiz_step page, all_step or from anywhere
	when the wizard is done and it is being presented on a web site.
	The GET variable wFrom is either outside (from a web site), allsteps (show from beginning and
	return to wiz_step), or onestep (just show a step and return to that step)
	or cyup and cydown which means Preview called it self from the next and previous buttons
	The GET call also has to pass the xml file name using the variable wfile.
	Here is our test GET url
	http://betterstuffbetterlife.com/pttrot/WizardMakerApp/Preview.php?wFrom=outside&wFile=Wizard-to-test-preview.xml
*/
// buffer the output so the code can change cookie and session data.
ob_start();
// start session to store data
session_start();            // starts the data storing session
include('evalmath.class.php'); // needed for the number crunching if user is calculating in text block
//$_SESSION["stepNum"] = 0; // set the step number to 0
if ($_SERVER['REQUEST_METHOD'] == 'GET') {


// if from outside, save the filename  for the rest of preview
	if (!empty($_GET['wFrom'])) {
		$wFrom = $_GET['wFrom'];           // get this first to where we are coming from
		$_SESSION["wFrom"] = $wFrom;       // save data for after the POST call
		switch ($wFrom) {  			// get data depending on where this came from
			case "outside": // call from any website
				$wFile = $_GET['wFile']; 
				$_SESSION["wFile"] = $wFile; // save data for the rest of the preview
				$wStep = 1; // assume we start with the first step
				$_SESSION["wStep"] = $wStep; // save data for after the POST call
				$xml=simplexml_load_file($wFile) or die("Error: Cannot create object"); 
				$wStepCount = $xml->step->count(); // get the number of steps
				// set these in session data
				//$_SESSION["preStep"] = $wStep;  //?? do I need this?
				$_SESSION["preStepCnt"] = $wStepCount;
			break;
			case "allsteps": // call the all steps page -- get data from cookies
				// get the file name from the cookie
				$wFile = $_COOKIE['c_file'];
				$_SESSION["wFile"] = $wFile; // save data for the rest of the preview
				$wStep = 1; // assume we start with the first step
				$_SESSION["wStep"] = $wStep; // save data for after the POST call
				$xml=simplexml_load_file($wFile) or die("Error: Cannot create object"); 
				$wStepCount = $xml->step->count(); // get the number of steps
				// set these in session data
				//$_SESSION["preStep"] = $wStep;  //?? do I need this?
				$_SESSION["preStepCnt"] = $wStepCount;
			
			break;
			case "onestep": // called from one of the step pages
				// get the file name from the cookie
				$wFile = $_COOKIE['c_file'];
				$_SESSION["wFile"] = $wFile; // save data for the rest of the preview
				// now get the step from the cookies
				$wStep = $_COOKIE['c_snum'];  // step number
				$_SESSION["wStep"] = $wStep; // save data for after the POST call
				$xml=simplexml_load_file($wFile) or die("Error: Cannot create object"); 
				$wStepCount = $xml->step->count(); // get the number of steps
				// set these in session data
				//$_SESSION["preStep"] = $wStep;  //?? do I need this?
				$_SESSION["preStepCnt"] = $wStepCount;
			break;
			case "cyup": // call the all steps page
				$wFile = $_SESSION["wFile"];  //get the file name back
				$wStep= $_SESSION["wStep"];
				$wStep++;
				$_SESSION["wStep"] = $wStep;
				$wStepCount = $_SESSION["preStepCnt"];
			break;
			case "cydown": // call the all steps page
				$wFile = $_SESSION["wFile"];  //get the file name back
				$wStep = $_SESSION["wStep"];
				$wStep--;
				$_SESSION["wStep"] = $wStep;
				$wStepCount = $_SESSION["preStepCnt"];
			break;
			
		}
	
		// if wFrom is from either the all steps page or a step, we know the cookie data is OK
		// !! make sure all data needed after the post is saved as session data
	} else {
		print 'Error getting data from call';
	}
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') { // ask for input submitted
	// get former GET data from session data, also the step number
	$wFrom = $_SESSION["wFrom"];
	$wFile = $_SESSION["wFile"];
	$wStep= $_SESSION["wStep"];
	$wStepCount = $_SESSION["preStepCnt"];
	//print "$wFrom after POST is. " . $wFrom . '<br>';
	// get the variable names we need to look for in POST data
	$xml3=simplexml_load_file($wFile) or die("Error: Cannot create object");

	$allvars = $xml3->xpath('/wizard/step[' . $wStep . ']/stepElems/sElem[type="Ask for Input"]/text');

	foreach ($allvars as $eVar) {
		// print '$eVar is ' . $eVar . '<br>'; // get user input
		//if (!empty($_POST["$eVar"])) {
			$_SESSION["$eVar"] = $_POST["$eVar"]; // get user input
			// print 'Session data for ' . $eVar . ' = ' . $_SESSION["$eVar"];
			//print '<br>';
		//}
	}


} else { // has to be either get or post
  print "Error: neither GET nor POST";
  $wFrom = $_SESSION["wFrom"];
}

// Now set up navitation, etc.
createPage($wStep,$wStepCount,$wFile,$wFrom); // send get|post, step, count of steps, filename and who called


// Now generate the page from XML
function createPage ($step,$scount,$filename,$from) { // passed GET|POST, step, count of steps, filename and who called
	//function needs to know if the call was GET or POST, the filename, and the origin of the original call
	// get the name of the wizard
	$xml=simplexml_load_file("wizardListing.xml") or die("Error: Cannot create object");
	// need xpath to find the name from the filename
	// finds just the title of the wizard element that matches the file name.
	$wizTitle = $xml->xpath('/wizardList/wizard[wizfile="' . $filename . '"]/wizname');
	// get the steps and the step title
	$xmlStep=simplexml_load_file($filename) or die("Error: Cannot create object");
	$sName = $xmlStep->step[$step -1]->title;    // get the name of the step
	$sInstruct = $xmlStep->step[$step -1]->instruct;    // get the instructions or the step
	$sElements = $xmlStep->step[$step -1]->stepElems[0]->children();    // put all elements in an array

	// define the title for the top matter
	define('WIZTITLE', $wizTitle[0]);  // define wizard title
	// get data on the appropriate step
	define('STEPTITLE','Step ' . $step . ': ' . $sName); // defind step title
	define('SINSTRUCT',$sInstruct);    // define instsructions
	// include top matter                     
	include 'templates/genWizTop.html';
	// set the Previous button
	if ($step == 1) { // hid the previous button if first step
	  define('STEPPREV'," ");   // remove Previous button on wizard
	} else {
	  define('STEPPREV','
	  					<li class="previous"><a href="Preview.php?wFrom=cydown">Previous</a></li>
						');   // allow Previous button on wizard
	}
	// set the Next button
	if ($step == $scount) { // hid the next button if last step
	  define('STEPNEXT'," ");   // remove Next button on wizard
	} else {
	  define('STEPNEXT','
	  					<li class="next"><a href="Preview.php?wFrom=cyup">Next</a></li>
						');   // allow Next button on wizard
	}
	// set the exit button BUT maybe just open in new tab instead.
	if ($from =="allsteps" or $from == "onestep" ) { // show the exit button if you are editing the wizard
	  define('STEPEXIT'," ");   // remove Next button on wizard
	} else {
	  define('STEPEXIT','
	  					<li class="next"><a href="Preview.php?wFrom=cyup">Next</a></li>
						');   // allow Next button on wizard
	}
	
	// now for we display the elements
	// cycle through and display all elements
	$wasAsk = "no";  // use to create form
	foreach ($sElements as $elem) {
		$elSwitch = $elem->type;
 		// print 'elememnt type is ' . $elem->type . "<br>";
		//  logic to group inputs into one form and one submit button
		if ($wasAsk == "yes" AND $elSwitch != "Ask for Input") {
			print '<br>
			  <input type="submit" value="Submit">
			  </form>';
			$wasAsk = "no";
		}
		switch ($elSwitch) {
			
			case "Text":       // for a text element
				// check to see if it has calculations
				$texttemp = $elem->text;
				if (strpos($texttemp, "cal#", 0) == false) {
					print "<br>";
					print $texttemp;  // 1 is true 0 is false -- no calculations so print
				} else if (addCalcs($texttemp) == "error") {
					print "<br>";
					print "<br>";
					break;
				} else {
					// transform text to add the calculated numbers
					//print 'result from addCalcs <br>';
					print "<br>";
					print  addCalcs($texttemp);  
				}
				break;
			case "Ask for Input": // for an element asking for input -- complex we group them - one submit button
				// collect all inputs for this page. how many?
				if ($wasAsk == "no") {
	
					print '<form action="http://betterstuffbetterlife.com/pttrot/WizardMakerApp/Preview.php" method="POST">'; // top part of form
	
					$wasAsk = "yes";  
				}
				$inLabel = $elem->label;    // label for form
				$inVar = $elem->text;     // variable name
				// show the values gathered from POST if they have been gathered
				if(!empty($_SESSION["$inVar"])) {
  					 $value = $_SESSION["$inVar"];
				} else {
					$value = "";
				}
			
				print $inLabel . '<br>
				  <input type="text" name="' . $inVar . '" value="' . $value .'">
				  <br>';
			
				// cycle and print all input forms
				// print the submit button 
				break;
			case "Picture or Video": // to show a picture or video
				$texttemp = $elem->text;
				if (substr_compare($texttemp,".mp4",-4,4,TRUE) == 0) {
				print '<video width="300" height="225" controls>
							<source src="images/'. $texttemp . '" type="video/mp4">
							 Your browser does not support the video tag.
						</video>';
			// print '<br>';		
				} else {
					print '<img src="images/'. $texttemp . '" alt="Picture missing" style="width:auto;height:300px;">';
					// print '<br>';
				} 
			
			break;
		}
	}
	include 'templates/genWizBottom.html'; // print the template for upper matter
	// flush the buffer and show the page
	ob_end_flush();	
}
// funcitons
function addCalcs($inputText) {
	// Find the Cal#xxx# string and replace it with the calculated value
	// if no more calculations, return the modified text
	// if variables have no values, return the word error.
	$matchYN = 1;
	while ($matchYN == 1) {
		$matchYN = preg_match('/cal#.+?#/', $inputText,$varSt);
		//var_dump($varSt);
		if ($matchYN == 0) {
			break;
		}
		$varLong = $varSt[0]; // xxx
		//print 'regex result was ' . $varLong . '<br>';
		$core = html_entity_decode(substr($varLong,4,strlen($varLong)-5));  // trim off cal# and # and special characters
		//print 'expression part is ' . $core . '<br>';
		// $varMatch = 1;
		$varText = "xx";
		while ($varText != "") {   // find all the variables and replace with values
			preg_match('/[A-Za-z]+[A-Za-z0-9]+/', $core, $uVar);
			//var_dump($uVar);
			//print '<br>';
			// preg_match('/^[A-Za-z][A-Za-z0-9]*/', $core,$uVar);
			
			$varText = $uVar[0];
			
			//print '$varText is ' . $varText . '<br>';
			if ($varText == "") { // if no more variables, break out of loop
			// if ($varMatch == 0) { // if no more variables, break out of loop
				break;
			} else {             // else find the value and replace the variable
				//$varText = $uVar[0];  // this is the variable
				//print 'variable is ' . $varText . '<br>';
				// if (isset($_SESSION["$varText"]) AND $_SESSION["$varText"] != NULL ) {       // if GET variable will not be set
				if (isset($_SESSION["$varText"]) && !empty($_SESSION["$varText"])) {      // if GET variable will not be set
					 $varValue = $_SESSION["$varText"];  			// get the data we stored after POST          
				} else {
					return "error";
					break;
				}	
			}
			// replace variable with number
			$core = str_replace($varText, $varValue, $core);	// replace variable with value
			
			//print 'expression replaced with input is ' . $core . '<br>';
		}
		$coreString = preg_replace("/[^A-Za-z0-9+*.()-\/]/", "", $core);  //convert to string and strip out spaces
		//print 'expression sent to evalmath is ' . $coreString . '<br>';
		$m = new EvalMath;
		$result = $m->evaluate($coreString);
		//print 'result of math crunching is ' . $result . '<br>';
		// replace original string with the math result
		// can't use preg_replace because of the special characters uses as operators.
	
		$inputText = str_replace($varLong, $result, $inputText);
		//print 'new text for user is  ' . $stnug2 . '<br>';
		
	}
	return $inputText; // send it back and done
}	

?>