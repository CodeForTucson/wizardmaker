<?php
/* This is the home page for this site.
Just learning about Github so I will commit some minor change and see how it goes
It uses templates to create the layout. */
// set the title
define('WIZTITLE', 'All Wizards');
// set the four buttons left to right Edit/back nav, Settings, Preview, Done/plus sign
define('BUTTON_1', '<a href="" class="btn btn-primary" role="button">
         			Edit
         			</a>');
define('BUTTON_2', '<a href="" class="btn btn-primary" role="button">
         			Help
         			</a>');
define('BUTTON_3', '');
define('BUTTON_4', '');
define('BUTTON_5', '');
define('BUTTON_6', '');
define('BUTTON_7', '<button class="btn btn-primary" role="button" onclick="goSettings()">
					<span class="glyphicon glyphicon-plus"></span>
					</button>');

// define('BUTTON_7', '<onclick="goSettings()" class="btn btn-primary" role="button">
//     				<span class="glyphicon glyphicon-plus"></span>
//     				</a>');
// Include the header:
include 'templates/header_plus.html';
//check to see if GET data received, if so set cookies and go to wiz_step.php
?>
<!-- leave php to add javascript for setting cookies, the  go to wiz_step -->
<script>
// set cookies and then link to the next screen
function setAndGo(name,file) {
    document.cookie = "c_name=" + name;
    document.cookie = "c_file=" + file;
    window.location.assign("http://betterstuffbetterlife.com/pttrot/WizardMakerApp/wiz_step.php");
}
// set cookie so Settings knows this is a new wizard.
function goSettings() {
    document.cookie = "c_from=" + "index";
    window.location.assign("http://betterstuffbetterlife.com/pttrot/WizardMakerApp/settings.php");
}
</script>
<?php
// Leave the PHP section to display lots of HTML:
print '<h3> Select a wizard or select + to add a new wizard</h3>';
//find out how many wizards there are in wizlisting.xmlEncoding

// for each listing, print the name and description 
// and call wizLaunch to link to wizstep.php
$xml=simplexml_load_file("wizardListing.xml") or die("Error: Cannot create object");
// create a bootstrap list group
// set up columns to limit the size of the buttons

foreach ($xml-> children() as $wizard) {
	$name = $wizard->wizname;
	$file = $wizard->wizfile;
	$desc = $wizard->wizdesc;
	$location = "wiz_step.php";
	// For each button and descrition, create a bootstrap row with two columns
	print '<div class="row">
		<div class="col-xs-4">';
	print '<button class="btn-info" onclick="setAndGo(\'' . $name . '\',\'' . $file . '\')">' . $name . '</button>';
	// print "<button class='btn-info' onclick='setAndGo('" . $name . "','" . $file . "')'>" . $name . "</button>";
	// print "<button class='btn-info' onclick=setAndGo('" . $name . "','" . $file . "')>" . $name . "</button>";
	print '</div>';
	print '<div class="col-xs-6">';
 	print  $desc;
 	print '</div>';
    print '</div>';
	print '<br>';
		    
}
// end of row add blank column to right

// final template has the end div
include 'templates/footer.html'; // Include the footer.
?>