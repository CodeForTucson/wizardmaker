<?php
/* This is the home page for this site.
Just learning about Github so I will commit some minor change and see how it goes
It uses templates to create the layout. */
// set the title
define('WIZTITLE', 'All Wizards');
// set the four buttons left to right Edit/back nav, Settings, Preview, Done/plus sign
define('BUTTON_1', '<button class="btn btn-primary" role="button" onclick="togEdit()">
         			Move/Del
         			</button>');
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

// Include the header:
include 'templates/header_plus.html';
// include 'templates/Edit_include.js';  // include javascript to hide edit buttons
//check to see if GET data received, if so set cookies and go to wiz_step.php
?>
<!-- leave php to add javascript for setting cookies, the  go to wiz_step -->
<script>
// turn on edit buttons if cookie set to keep them on -- set by movdelDoit
$(document).ready(function(){
	if (getCookie("editbuttons") == "keepon") {
		togEdit();  // toggle buttons on
		document.cookie = "editbuttons=turnoff";
			/* 
			var x = document.getElementById("edDiv");
			x.style.display = "block";	
			 */	
	}

});
// A subroutine to just get the value of a specific cookie
function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

var windGlob = 4; // create a global variable for the index
// This makes the first column visible which contains the move/del buttons
// it is toggled visible -not visible by Move//del button
function togEdit() {
	var x = document.getElementsByClassName("col-xs-1");
	var i;
	for (i = 0; i < x.length; i++) {
		if (x[i].style.display === "none") {
			x[i].style.display = "block";
		} else {
			x[i].style.display = "none";
		}
	}	
}
// When a Move/del button is clicked comes here to put up pop-up dialoge
function moveDelete(eIndex) {
	//alert("Hello! I am an alert box!!");
	// get all the button values in an array and show the one clicked
	var x = document.getElementsByClassName("get_label");
	var t = $(x[eIndex]).text();
	windGlob = eIndex;  // set the global index variable to pass to php
	document.getElementById("modText").innerHTML = t;
	$("#editMod").modal();
}
function movdelDoit(eAct) {
	//alert("Hello! I am an alert box!!");
	
	// sent action to PHP program to delete files and node of wizard
	// if a delete, ask for confirmation first
	if (eAct == "del") {
		var r = confirm("Are you sure you want to delete?");
	} else {
		r = true; // if a move up or down -- don't confirm
	}
	// now send data o cleanup.php to do the moves and delete
	if (r == true) {         // user said ok to delete 
		// get the filename and request cleanup php file to perform actions
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
			/* 
			print '<br>';
			print 'output from cleanup is ' . this.responseText;
				 */
			//document.getElementById("clStat").innerHTML = this.responseText;
			// save reload state in cookie so that buttons stay open after reload
			document.cookie = "editbuttons=keepon";
			// refresh page using reload method
			location.reload();  // force reload
			}
		}
		xmlhttp.open("GET", "cleanup.php?q=" + windGlob + "&a=" + eAct, true);
		xmlhttp.send();
		
		//alert(Res);
			 
		// delete the wizard node in wizardlist

	} else {				// no cancel this
		
	}
				
}

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
$subIndex = 0;
foreach ($xml-> children() as $wizard) {
	$name = $wizard->wizname;
	$file = $wizard->wizfile;
	$desc = $wizard->wizdesc;
	$location = "wiz_step.php";
	// For each button and descrition, create a bootstrap row with three columns
	// the first colum holds he move/delete buttons and is hidden to start
	print '<div class="row">
			<div class="col-xs-1 edColumn" id="edDiv" style="display: none;">'; // take out 
	print '<img src="wizassets/editicon3.jpg" alt="edit button" class="pull-right movdel-button" onclick="moveDelete(' . $subIndex . ')" style="height:25px;width:43px">';
	// print '<img src="wizassets/editicon3.jpg" alt="edit button" class="pull-right movdel-button" onclick="moveDelete(' . $subIndex . ')" style="height:30px;width:52px">';
	print '</div>';
	print '<div class="col-xs-4">';
	print '<button class="btn-info get_label" onclick="setAndGo(\'' . $name . '\',\'' . $file . '\')">' . $name . '</button>';
	print '</div>';
	print '<div class="col-xs-6">';
 	print  $desc;
 	print '</div>';  // end of column
    print '</div>';  // end of row
	print '<br>';
	$subIndex++;	    // increment idex used for move delete
}
//print '<h4 id="clStat">Status of clean<h4>';
// end of row add blank column to right
//print '</div>';  // end of container in header_plus

include 'templates/EditModel.html'; // Include the popup.
// final template has the end div
include 'templates/footer.html'; // Include the footer.
?>