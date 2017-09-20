<?php
/* This is the text element page 
It uses templates to create the layout. */
// set the title
define('WIZTITLE', 'Add or Edit Pictures and Video');
// set the four buttons left to right Edit/back nav, Settings, Preview, Done/plus sign
define('BUTTON_1', '<a href="add_step.php" class="btn btn-primary" role="button">
         			Cancel
         			</a>');
define('BUTTON_2', '');
define('BUTTON_3', '');
define('BUTTON_4', '<a href="" class="btn btn-primary" role="button">
    				Done
    				</a>');
// Include the header:
include 'templates/header_plus.html';
//check to see if GET data received, if so set cookies and go to wiz_step.php
?>
<!-- leave php to add javascript for setting cookies, the  go to wiz_step -->
<script>
function setAndGo(name,file) {
    document.cookie = "c_name=" + name;
    document.cookie = "c_file=" + file;
    window.location.assign("http://betterstuffbetterlife.com/pttrot/WizardMakerApp/wiz_step.php");
}
</script>
<?php
//form handling
// Leave the PHP section to display lots of HTML:
print '<h3> Enter text </h3>';
// put text entry form here
// function to set cookies and go to wizard steps page
function set_data($dname,$dfile) {
	// save key data as cookies
	setcookie('c_name', $name); // save the name of the wizard
	setcookie('c_file', $file); // save the file name of the wizard

}
include 'templates/footer.html'; // Include the footer.
?>