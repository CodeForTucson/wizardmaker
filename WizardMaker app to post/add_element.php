<?php
/* This page creates a new element.  
It uses templates to create the layout. */
// set the title
define('WIZTITLE', 'Element');
// set the four buttons left to right Edit/back nav, Settings, Preview, Done/plus sign
define('BUTTON_1', '<a href="add_step.php" class="btn btn-primary" role="button">
         			<span class="glyphicon glyphicon-chevron-left"></span>The Step
         			</a>');
define('BUTTON_2', '');
define('BUTTON_3', '');
define('BUTTON_4', '<a href="add_step.php" class="btn btn-primary" role="button">
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
<?php

print '<h3> Select the element you want to add to this step. </h3>';

print '<button class="btn-info" onclick="setAndGo(\'text_element.php\')">Text</button><br>'; 
print '<br>';
print '<button class="btn-info" onclick="setAndGo(\'image_element.php\')">Picture or Video</button><br>';
print '<br>';
print '<button class="btn-info" onclick="setAndGo(\'askInput_element.php\')">Ask for Input</button><br>';
print '<br>';
// footer material
include 'templates/footer.html'; // Include the footer.
?>