// +-------------------------------------------------+
// � 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: track_clicks.js,v 1.8 2021/10/29 10:02:36 dgoron Exp $

function _trackClick(event){
	//event.button == 0 => clic gauche
	//event.button == 1 => clic molette
	//event.button == 2 => clic droite
	if(event.button == 2) { //Impossible de savoir si l'action suivante est : Ouvrir dans un nouvel onglet
		return;
	}
	var el = event.srcElement || event.target;
	while (el && (typeof el.tagName == 'undefined' || el.tagName.toLowerCase() != 'a' || !el.href)) {
		el = el.parentNode;
	}
	if (el && el.href) {
		var open_link = false;
		
		if (el.href.indexOf(location.host) == -1 && el.href.substring(0,10).toLowerCase()!='javascript') {
			var type = 'external_url_external';
			if (el.getAttribute('type')) {
				type = el.getAttribute('type');
			}
			log_click(el.getAttribute('href'),type);
			open_link = true;
		} else if(el.href.indexOf(location.host) != -1 && el.href.indexOf('.php') == -1 && el.href.indexOf('.js') == -1) {
			//URL interne HTML, PNG, etc.
			var type = 'external_url_internal';
			if (el.getAttribute('type')) {
				type = el.getAttribute('type');
			}
			log_click(el.getAttribute('href'),type);
		}
		//L'appel est synchrone et le comportement doit rester � l'identique
//		if (open_link) {
//			setTimeout(function() {
//				window.open(el.href, '_blank');
//			}.bind(el), 500);
//			event.preventDefault ? event.preventDefault() : event.returnValue = false;
//		}
	}
}

function log_click(url,type){
	
	var action = new http_request();
	var called_url = url;
	if(url.indexOf('?') != -1) {
		//encodons l'url si celle-ci ne l'est pas encore
		called_url = encodeURIComponent(url);
	}
	var ajax_url = './ajax.php?module=ajax&categ=log&type_url='+type+'&called_url='+called_url;
	
	action.request(ajax_url);
	
}

if (window.addEventListener) {
	window.addEventListener('load', function() {
		document.body.addEventListener('mousedown', _trackClick, false);
	}, false);
}
else {
	window.attachEvent && window.attachEvent('onload', function() {
		document.body.attachEvent('onmousedown', _trackClick);
	});
}