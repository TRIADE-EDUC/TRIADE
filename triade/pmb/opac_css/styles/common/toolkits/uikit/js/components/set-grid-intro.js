$(document).ready(function(){
	$("#intro").addClass("uk-grid uk-grid-collapse");
	//full width
		var fullW = new Array();
		fullW.push("#intro>div");
		for (key in fullW){
			$(fullW[key]).addClass("uk-width-1-1 wl-width-custom");
		}	
});