// responsive toggle user quick acces
$(document).ready(function(){
	if($( window ).width() < 960) {
		$("#connexion").addClass(function(){
			return ($("html").hasClass("uk-touch")) ? "uk-visible-large" : ''; 
		});	
	}
});