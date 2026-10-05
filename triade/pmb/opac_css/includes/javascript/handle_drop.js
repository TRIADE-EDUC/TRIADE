// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: handle_drop.js,v 1.4 2013/06/19 07:05:30 ngantier Exp $

function title_textfield(dragged,target) {
	target.value=dragged.firstChild.data;
}

function image_textfield(dragged,target) {
	target.value=dragged.firstChild.src;
}

function textfield_image(dragged,target) {
	var childs=dragged.parentNode.childNodes;
	var i;
	for (i=0; i<childs.length; i++) {
		if (childs[i].nodeName=="INPUT") {
			break;
		}
	}
	if (i<childs.length) target.firstChild.src=childs[i].value;
}

function strip_tags(html){
	 
	//PROCESS STRING
	if(arguments.length < 3) {
		html=html.replace(/<\/?(?!\!)[^>]*>/gi, '');
	} else {
		var allowed = arguments[1];
		var specified = eval("["+arguments[2]+"]");
		if(allowed){
			var regex='</?(?!(' + specified.join('|') + '))\b[^>]*>';
			html=html.replace(new RegExp(regex, 'gi'), '');
		} else{
			var regex='</?(' + specified.join('|') + ')\b[^>]*>';
			html=html.replace(new RegExp(regex, 'gi'), '');
		}
	}

	//CHANGE NAME TO CLEAN JUST BECAUSE 
	var clean_string = html;

	//RETURN THE CLEAN STRING
	return clean_string;
}

function notice_cart(dragged,target) {
	id=dragged.getAttribute("id");
	id=id.substring(10);
	target.src="cart_info.php?id="+id+"&header="+encode_URL(strip_tags(dragged.innerHTML));
}