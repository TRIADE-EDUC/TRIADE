/* +--------------------------------------------------------------------------+
// 2017 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: set-grid-main-uncolored.js,v 1.24.8.1 2024/09/12 09:49:49 pmallambic Exp $ */
// grid test
$(document).ready(function () {
	$("#main_hors_footer").addClass(function () {
		return ($('#grid-init').length) ? "uk-grid uk-grid-medium" : '';
	});
	$("#main_hors_footer").attr("data-uk-grid-margin", "");
	$("#intro").addClass("uk-width-1-1");
	$("#footer").addClass("uk-width-1-1");
	$("#container").addClass("uk-grid uk-grid-collapse uncolored");
	$("#container").attr("data-uk-grid-margin", "");
	Array.prototype.slice.call(document.querySelectorAll('div[class^="cms_module"]>script')).forEach(function (script) {
		if (script.parentNode.children.length == 1) {
			script.parentNode.className = "cmsNoStyles";
		}
	});
	$("#bandeau div[class^='cms_module'],#bandeau>#facette").each(function () {
		if ($(this).children().length == 0) {
			$(this).removeAttr("class").addClass("cmsNoStyles");
		}
	});

	var bandeauHasChilds = function () {
		var bandeau = document.getElementById('bandeau');
		if (bandeau) {
			var bandeauChilds = bandeau.children;
			for (var i = 0; i < bandeauChilds.length; i++) {
				//console.log(bandeauChilds[i])
				if (
					bandeauChilds[i].getAttribute('id') != 'accueil' &&
					bandeauChilds[i].getAttribute('id') != 'adresse' &&
					bandeauChilds[i].getAttribute('class') != 'cmsNoStyles' &&
					bandeauChilds[i].tagName != 'SCRIPT'){
					return true;
				}
			}
			return false;
		}
		return false;
	}
	if (bandeauHasChilds() == false) {
		//Soit bandeau pas present dans la page
		//Soit bandeau est present mais n as pas d'autres enfants que #accueil et #adresse
		$("#main").removeAttr("class").addClass("uk-width-1-1");
		$("#bandeau").removeAttr("class").addClass("uk-width-1-1 ready");
		$("#bandeau").css({ "margin": "0", "padding": "0px", "border": "none" });
		$("#accueil, #adresse").addClass("uk-hidden");
		

	}
	else if (bandeauHasChilds() == true) {
		$("#bandeau").addClass("uk-width-large-1-4 uk-width-medium-1-3 uk-width-small-1-1 ready");
		$("#main").addClass("uk-width-large-3-4 uk-width-medium-2-3 uk-width-small-1-1");
		$("#container>#bandeau:nth-child(3)").addClass("is-on-right-side");
		$("#container>#bandeau:nth-child(2)").addClass("is-on-left-side");
	}
	//full width
	var fullW = new Array();
	fullW.push("#main_hors_footer>div");
	for (key in fullW) {
		$(fullW[key]).addClass("uk-width-1-1 wl-width-custom");
	}
	$("#main").addClass(function () {
		return ($('#home-tracker').length) ? "on-home" : 'not-home';
	});
	$(".notice_corps").addClass(function () {
		return ($('div#cart_action').length) ? "no-right-content" : '';
	});
	if( $("#search p.p1>*").length == 0){
		$("#search p.p1").addClass("ui-disable-item");
	}
	// Pixel blanc
	imgEvents();
	$("div:empty").not(".cms_module_agenda *").not("div[class*='dijit']").not("div[id^='add_div_cms_module']").not("div[data-dojo-type]").not("div[data-dojo-type] :empty").not("#att").addClass("ui-empty-item");
	$("body").addClass("ready");
});

var counterImage = 0;
function imgEvents(){
	
	var imagesSlider = Array.prototype.slice.call(document.querySelectorAll('.imgSize'));
	var imagesNoti = Array.prototype.slice.call(document.querySelectorAll('.vignetteNot'));
	
	imagesSlider = imagesSlider.concat(imagesNoti);
	imagesSlider.forEach(function(image){
		image.addEventListener('load', incrementCounterImage);	
	});
	imagesSlider.forEach(function(image){
        if(image.getAttribute('src').indexOf == -1){
            image.setAttribute('src', image.getAttribute('src')+'&timestamp='+Date.now());	
        }
	});
}

function incrementCounterImage(){
	var imagesSlider = Array.prototype.slice.call(document.querySelectorAll('.imgSize'));
	var imagesNoti = Array.prototype.slice.call(document.querySelectorAll('.vignetteNot'));
	imagesSlider = imagesSlider.concat(imagesNoti);
	
	counterImage++;
	if(counterImage == imagesSlider.length){
		for(var i=0 ; i<imagesSlider.length ; i++){
			if((imagesSlider[i].naturalWidth < 3) && (imagesSlider[i].naturalHeight < 3)){
				var img = document.createElement('img');
				img.setAttribute('src', './styles/'+opac_style+'/images/no_image.jpg');
				img.setAttribute('class', 'no-img-added');
				imagesSlider[i].parentElement.appendChild(img);
				img.parentElement.removeChild(imagesSlider[i]);
			}   
		
		}		
	}

}