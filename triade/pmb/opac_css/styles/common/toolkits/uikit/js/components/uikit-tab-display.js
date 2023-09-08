$(document).ready(function(){
	//Current
		var active = new Array();
		active.push(".search_tabs #current");
		active.push(".empr_tabs .subTabCurrent");
		for (key in active){
			$(active[key]).addClass("uk-active");
		}
	//tabset
		var tabset = new Array();
		tabset.push(".search_tabs");
		tabset.push(".empr_tabs");
		for (key in tabset){
			$(tabset[key]).addClass("uk-tab");
		}	
});