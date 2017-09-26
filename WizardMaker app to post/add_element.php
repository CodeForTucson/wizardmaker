<?php
/* This page creates a new element.  
It uses templates to create the layout. */
// set the title
define('WIZTITLE', 'Element');
// set the four buttons left to right Edit/back nav, Settings, Preview, Done/plus sign
define('BUTTON_1', '<a href="add_step.php" class="btn btn-primary" role="button">
         			<span class="glyphicon glyphicon-chevron-left"></span>The Step
         			</a>');
define('BUTTON_2', '<a href="" class="btn btn-primary" role="button">
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
<!-- leave php to add javascript for setting cookies, the  go to wiz_step -->
<script>
function setAndGo(file) {
    // document.cookie = "c_name=" + name;
//     document.cookie = "c_file=" + file;
    window.location.assign("http://betterstuffbetterlife.com/pttrot/WizardMakerApp/" + file);
}
</script>

<h3> Select the element you want to add to this step. </h3>
<!--  create a row for each type of element and add a description -->
<div class="row">
	<div class="col-xs-4">
		<button class="btn-info" onclick="setAndGo(\'text_element.php\')">Text</button>
	</div> 
	<div class="col-xs-6">
		<p> Add a block of text. This may include calculations based on 
		information you get from the user by using Ask for Input.</p>
	</div>
</div>
<div class="row">
	<div class="col-xs-4">
		<button class="btn-info" onclick="setAndGo(\'image_element.php\')">Picture or Video</button>
	</div> 
	<div class="col-xs-6">
		<p>Add a picture or video clip.  You can take it from this device if it has a camera
		or upload it from your computer. </p>
	</div>
</div>
<div class="row">
	<div class="col-xs-4">
		<button class="btn-info" onclick="setAndGo(\'askInput_element.php\')">Ask for Input</button>
	</div> 
	<div class="col-xs-6">
		<p>Ask your user for a number that can be used with text to show calculations.</p>
	</div>
</div>
		
<?php
// footer commands and material
include 'templates/footer.html'; // Include the footer.
?>