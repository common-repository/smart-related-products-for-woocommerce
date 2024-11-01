jQuery(document).ready(function(){
	jQuery('#attributes').multiSelect({
		selectableHeader: "<div class='custom-header'>" +  smart.non_active_title + "</div>",
		selectionHeader: "<div class='custom-header'>" +  smart.active_title + "</div>"
	});
});