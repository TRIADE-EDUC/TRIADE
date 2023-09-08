$(document).ready(function(){
	$("#footer").addClass("uk-grid uk-grid-collapse");
	//full width
		var fullW = new Array();
		fullW.push("#footer>div");	
		for (key in fullW){
			$(fullW[key]).addClass("uk-width-1-1 wl-width-custom");
		}	
	
});