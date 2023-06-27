// +-------------------------------------------------+
// � 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart.js,v 1.8 2017-12-01 13:46:45 dgoron Exp $

function getDomNodeBasketImg(img_src, img_title) {
	var basket_img = window.parent.document.createElement('img');
	basket_img.setAttribute('src', img_src);
	basket_img.setAttribute('alt',img_title);
	return basket_img;
}

function getIconDomNodeBasketRender(id_notice, action, header) {
	var basket_link = window.parent.document.createElement('a');
	if(window.parent.document.getElementById('baskets'+id_notice)) {
		basket_link.setAttribute('class','img_basket_exist');
		basket_link.setAttribute('title',msg_notice_title_basket_exist);
		switch(action) {
			case 'remove':
				var img_src = pmb_img_basket_small_20x20;
				var basket_img = getDomNodeBasketImg(img_src, msg_notice_title_basket);
				break;
			default:
				var img_src = pmb_img_basket_exist;
				var basket_img = getDomNodeBasketImg(img_src, msg_notice_title_basket_exist);
				break;
		}
		basket_link.appendChild(basket_img);
	}
	if(window.parent.document.getElementById('record_container_'+id_notice+'_cart')) {
		basket_link.setAttribute('class','img_basketNot');
		basket_link.setAttribute('target','cart_info');
		switch(action) {
			case 'remove':
				basket_link.setAttribute('href', 'cart_info.php?id='+id_notice+'&header='+header);
				basket_link.setAttribute('title',msg_record_display_add_to_cart);
				var img_src = pmb_img_white_basket;
				var basket_img = getDomNodeBasketImg(img_src, msg_notice_title_basket);
				break;
			default:
				basket_link.setAttribute('href', 'cart_info.php?action=remove&id='+id_notice+'&header='+header);
				basket_link.setAttribute('title',msg_notice_basket_remove);
				var img_src = pmb_img_record_in_basket;
				var basket_img = getDomNodeBasketImg(img_src, msg_notice_basket_remove);
				break;
		}
		var basket_span = window.parent.document.createElement('span');
		basket_span.setAttribute('class','icon_basketNot');
		basket_span.appendChild(basket_img);
		basket_link.appendChild(basket_span);
	}
	return basket_link;
}

function getLabelDomNodeBasketRender(id_notice, action, header) {
	var basket_link = window.parent.document.createElement('a');
	basket_link.setAttribute('class','label_basketNot');
	
	var basket_span = window.parent.document.createElement('span');
	basket_span.setAttribute('class','label_basketNot');
	switch(action) {
		case 'remove':
			basket_link.setAttribute('href', 'cart_info.php?id='+id_notice+'&header='+header);
			basket_link.setAttribute('title',msg_record_display_add_to_cart);
			var basket_txt = document.createTextNode(msg_notice_title_basket);
			break;
		default:
			basket_link.setAttribute('href', './index.php?lvl=show_cart');
			basket_link.setAttribute('title',msg_notice_title_basket_exist);
			var basket_txt = document.createTextNode(msg_notice_title_basket_exist);
			break;
	}
	basket_span.appendChild(basket_txt);
	basket_link.appendChild(basket_span);
	return basket_link;
}

function changeBasketImage(id_notice, action, header) {
	var basket_node = '';
	if(window.parent.document.getElementById('baskets'+id_notice)) {
		//Affichage de notices via la classe notice_affichage
		basket_node = window.parent.document.getElementById('baskets'+id_notice);
	} else if(window.parent.document.getElementById('record_container_'+id_notice+'_cart')) {
		//Affichage de notices via les templates Django
		basket_node = window.parent.document.getElementById('record_container_'+id_notice+'_cart');
	}
	if(basket_node) {
		if (basket_node.hasChildNodes()) {
			while (basket_node.hasChildNodes()) {  
				basket_node.removeChild(basket_node.firstChild);
			}
		}
		var iconDomNode = getIconDomNodeBasketRender(id_notice, action, header);
		basket_node.appendChild(iconDomNode);
		//Affichage de notices via les templates Django
		if(window.parent.document.getElementById('record_container_'+id_notice+'_cart')) {
			var labelDomNode = getLabelDomNodeBasketRender(id_notice, action, header);
			basket_node.appendChild(labelDomNode);
		}
	}
}