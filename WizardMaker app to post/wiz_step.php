<?php
/* This page lists the steps of the wizard plus the title and instructions.
8-27-17 
todo:
X Keep track of the step number as well 
- Issue, when + hit does not increment step number so redo it to use setandgo and up the cookie
*/
// get the cookie data
$wname = $_COOKIE['c_name'];
$wfile = $_COOKIE['c_file'];
define('WIZTITLE',$wname . ' All Steps');
// set the four buttons left to right Edit/back nav, Settings, Preview, Done/plus sign
define('BUTTON_1', '<a href="index.php" class="btn btn-primary" role="button">
         			<span class="glyphicon glyphicon-chevron-left"></span>All Wizards
         			</a>');
define('BUTTON_2', '<a href="" class="btn btn-primary" role="button">
         			Help
         			</a>');
define('BUTTON_3', '<a href="" class="btn btn-primary" role="button">
         			Edit
         			</a>');
define('BUTTON_4', '<a href="" class="btn btn-primary" role="button">
         			Preview
         			</a>');
define('BUTTON_5', '<a href="" class="btn btn-primary" role="button">
         			Settings
         			</a>');
define('BUTTON_6', '<a href="" class="btn btn-primary" role="button">
         			Export
         			</a>');
define('BUTTON_7', '<button  class="btn btn-primary"  
					onclick="setAndGo(0,\'add\',0)">
    				<span class="glyphicon glyphicon-plus"></span>
    				</button>');
    				// onclick="setAndGo(0,\'add\')">
// define('BUTTON_4', '<a href="add_step.php" class="btn btn-primary" role="button">
//     				<span class="glyphicon glyphicon-plus"></span>
//     				</a>');
// Include the header:
include 'templates/header_plus.html';
?>
<!-- leave php to add javascript for setting cookies, the  go to wiz_step -->
<script>
function setAndGo(title,file,count) {
	// if + was not selected then set step number to the step selected
	// otherwise leave the cookie at the number for the next step
	if (count > 0) {
	document.cookie = "c_snum=" + count;
	}
	// I don't need to change the filename and name so leave alone.
    //document.cookie = "c_file=" + file;
    document.cookie = "c_sname=" + title; //title of the step
    window.location.assign("http://betterstuffbetterlife.com/pttrot/WizardMakerApp/add_step.php");
}
</script>
<?php
print '<h3> Select a step to edit or select + to add a new step.</h3>';
// list all the steps
$xml=simplexml_load_file( $wfile) or die("Error: Cannot create object");
$scount = 0; // index for step number
foreach ($xml-> children() as $step) {
 // foreach ($xml->step[$scount]->element as $elem) 
	$stitle = $step->title;
	$instruct = $step->instruct;
//	$location = "wiz_step.php";
	$scount ++; // incrment step number
	$listing = "Step " . $scount . ": " . $stitle;
	print '<div class="row">
		<div class="col-xs-4">';
	print '<button class="btn-info" onclick="setAndGo(\'' . $stitle .'\',\'' . $wfile . '\',\'' . $scount . '\')">' . $listing . '</button><br>'; 
	print '</div>';
	print '<div class="col-xs-6">';
 	print  $instruct;
 	print '</div>';
	print '</div>';
	print '<br>';
   }
$newStepCount = $scount + 1; // this will be one plus the last step
setcookie('c_snum', $newStepCount); // set cookie to new step number
   
// print 'Final step count is = ' . $newStepCount;
include 'templates/footer.html'; // Include the footer.
?>