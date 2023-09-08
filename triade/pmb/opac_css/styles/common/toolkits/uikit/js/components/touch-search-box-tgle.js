// responsive toggle search 
$(document).ready(function(){
	if($( window ).width() < 960) {
		$("div[class^='cms_module_search']").addClass(function(){
			return ($("html").hasClass("uk-touch")) ? "uk-visible-large" : ''; 
		});	
	}
});