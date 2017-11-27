<?php
/* This page lists the steps of the wizard plus the title and instructions.
11-10--17 
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
define('BUTTON_2', '<button class="btn btn-primary" role="button" onclick="togEdit()">
         			Move/Del
         			</button>');
define('BUTTON_3', '<a href="" class="btn btn-primary" role="button">
         			Help
         			</a>');
define('BUTTON_4', '<a href="Preview.php?wFrom=allsteps" class="btn btn-primary" role="button" target="_blank">
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
		xmlhttp.open("GET", "cleanSteps.php?q=" + windGlob + "&a=" + eAct, true);
		xmlhttp.send();
		
		//alert(Res);
			 
		// delete the wizard node in wizardlist

	} else {				// no cancel this
		
	}
				
}


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
$subIndex = 0;  // used for move and delete
foreach ($xml-> children() as $step) {
 // foreach ($xml->step[$scount]->element as $elem) 
	$stitle = $step->title;
	$instruct = $step->instruct;
//	$location = "wiz_step.php";
	$scount ++; // incrment step number
	$listing = "Step " . $scount . ": " . $stitle;
	print '<div class="row">
			<div class="col-xs-1 edColumn" id="edDiv" style="display: none;">'; // take out 
	print '<img src="wizassets/editicon3.jpg" alt="edit button" class="pull-right movdel-button" onclick="moveDelete(' . $subIndex . ')" style="height:25px;width:43px">';
	// print '<img src="wizassets/editicon3.jpg" alt="edit button" class="pull-right movdel-button" onclick="moveDelete(' . $subIndex . ')" style="height:30px;width:52px">';
	print '</div>';
	print '<div class="col-xs-4">';
	print '<button class="btn-info get_label" onclick="setAndGo(\'' . $stitle .'\',\'' . $wfile . '\',\'' . $scount . '\')">' . $listing . '</button><br>'; 
	print '</div>';
	print '<div class="col-xs-6">';
 	print  $instruct;
 	print '</div>';
	print '</div>';
	print '<br>';
	$subIndex++;	    // increment idex used for move delete
   }
$newStepCount = $scount + 1; // this will be one plus the last step
setcookie('c_snum', $newStepCount); // set cookie to new step number
include 'templates/EditModel.html'; // Include the popup.   
// print 'Final step count is = ' . $newStepCount;
include 'templates/footer.html'; // Include the footer.
?>