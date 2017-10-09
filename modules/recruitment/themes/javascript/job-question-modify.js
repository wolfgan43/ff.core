jQuery(function(){
    jQuery("#SelectedQuestionModify_type").change(function() {
		 if(jQuery(this).children("option:selected").val() == 2) {
			 jQuery(".row .multiple_choice").fadeIn();
		 } else { 
			 jQuery(".row .multiple_choice").hide();
		 }
	});
    if(jQuery("#SelectedQuestionModify_type").length) {
	 	jQuery("#SelectedQuestionModify_type").change();
	 }
    
});