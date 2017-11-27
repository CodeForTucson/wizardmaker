 // when the placeholder button is clicked, the form shows and upload goes away.
$(document).ready(function(){
	// $("#edButton").hide(); // hide edit button when loading
	$("#dhide").click(function(){ // hide all and show placeholder input
		$("#plButton").remove();
		$("#imUpButtonDiv").remove();
/* 
		$("#plButton").fadeOut();
		$("#imUpButtonDiv").fadeOut();
 */
		$("#psForm").fadeIn("slow");
	});
	$("#imButton").click(function(){ //hide all and show upload form
		$("#plButton").remove();
		$("#imUpButtonDiv").remove();
		$("#imUpload").fadeIn("slow");
	});
});
