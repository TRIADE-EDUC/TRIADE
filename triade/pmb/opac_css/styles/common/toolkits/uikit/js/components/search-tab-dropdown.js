$(document).ready(function() {
	$(".search_tabs").addClass("uk-nav uk-nav-dropdown");
	$(".search_tabs").wrap("<ul class='uk-tab'><li  class='insert-a uk-active' data-uk-dropdown='{mode:'click'}' '><div class='uk-dropdown uk-dropdown-small'></div></li></ul>");
	$(".insert-a").prepend("<a href='#'>clic-me</a>");
	UIkit.dropdown('.insert-a', {
			mode:'click'
	
	});
});