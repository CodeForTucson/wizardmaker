<?php
/*
Stores images and is called by image_element.php
check php manual for error codes.
*/
if ($_SERVER['REQUEST_METHOD'] == 'POST')  { // upload the file
	//print ' got to the post section <br>';
	// this is the folder on the server where I store the image file
	$target_dir = "images/";
	// the $_FILES super global variable here will get the original file name.
	// and name is the extention
	//together $target_file is the path on the server for the file
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	// The pathinfo() function returns an array that contains information about a path; dir, basename, ext.
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	// make sure size is not too large but see the php manual that can block the big file before it is uploaded
	// using file_size_max or some such
	if ($_FILES["fileToUpload"]["size"] > 900000000) { // limit 900 mb
		echo "Sorry, your file is too large.";
		$uploadOk = 0;
	}
	// search for a dot, sync2 was a cookie with P's sync number, for every dot in $target_file
	// comment out for now and later replace with a unique number for the number if image files
	//$targetSync_file = str_replace(".",$sync2 .  ".", $target_file);
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		echo "Sorry, your file was not uploaded.";
	} else {
		// $target_file it the location including the name 
		// tmp_name is the temporary name of the file -- it was stored in some scratchpad place
		// on the surver.
		// if the move worked must return true -- might print the error there.
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";			
		} else {
			echo "Sorry, there was an error uploading your file.";
		}
    }
    print "OK may have done it";
	exit("End of program");
} else { // Display the form to get the sync number
	print 'Did not get a POST';
	exit("End of program");	
   }
?>