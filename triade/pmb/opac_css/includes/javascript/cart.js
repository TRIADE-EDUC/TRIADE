// +-------------------------------------------------+
// � 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart.js,v 1.13.4.1 2025/06/04 07:04:59 dgoron Exp $

function getDomNodeBasketImg(img_src, img_title) {
	var basket_img = window.parent.document.createElement('img');
	basket_img.setAttribute('src', img_src);
	basket_img.setAttribute('alt',img_title);
	return basket_img;
}

function getIconDomNodeBasketRender(id_notice, action, header) {
	var basket_link = window.parent.document.createElement('a');
	basket_link.setAttribute('role','button');
	basket_link.setAttribute('aria-hidden','true');
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

		var url = window.parent.location.href.toString();
		var pattern = /lvl=notice_display/;
		var is_notice_display = false;
		if (pattern.exec(url)) {
			is_notice_display = true;
		}
		switch(action) {
			case 'remove':
				basket_link.classList.add('removed_from_cart');
				basket_link.setAttribute('href', 'cart_info.php?id='+id_notice+'&header='+header);
				basket_link.setAttribute('title',msg_record_display_add_to_cart);
				basket_link.setAttribute('aria-label',msg_record_display_add_to_cart + ' : ' +  decodeURIComponent(header));
				if (is_notice_display) {
					var img_src = pmb_img_extended_record_white_basket;
				} else {
					var img_src = pmb_img_white_basket;
				}
				var basket_img = getDomNodeBasketImg(img_src, msg_notice_title_basket);
				break;
			default:
				basket_link.classList.add('added_in_cart');
				basket_link.setAttribute('href', 'cart_info.php?action=remove&id='+id_notice+'&header='+header);
				basket_link.setAttribute('title',msg_notice_basket_remove);
				basket_link.setAttribute('aria-label',msg_notice_basket_remove + ' : ' +  decodeURIComponent(header));
				if (is_notice_display) {
					var img_src = pmb_img_extended_record_in_basket;
				} else {
					var img_src = pmb_img_record_in_basket;
				}
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
	basket_link.setAttribute('role','button');

	var basket_span = window.parent.document.createElement('span');
	basket_span.setAttribute('class','label_basketNot');
	switch(action) {
		case 'remove':
			basket_link.classList.add('removed_from_cart');
			basket_link.setAttribute('target','cart_info');
			basket_link.setAttribute('href', 'cart_info.php?id='+id_notice+'&header='+header);
			basket_link.setAttribute('title',msg_record_display_add_to_cart);
			basket_link.setAttribute('aria-label',msg_record_display_add_to_cart + ' : ' +  decodeURIComponent(header));
			var basket_txt = document.createTextNode(msg_notice_title_basket);
			break;
		default:
			basket_link.classList.add('added_in_cart');
			basket_link.setAttribute('target','cart_info');
			basket_link.setAttribute('href', 'cart_info.php?action=remove&id='+id_notice+'&header='+header);
			basket_link.setAttribute('title',msg_notice_basket_remove);
			basket_link.setAttribute('aria-label',msg_notice_basket_remove + ' : ' +  decodeURIComponent(header));
			var basket_txt = document.createTextNode(msg_notice_basket_remove);
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

var cart_all_checked = false;

function check_uncheck_all_cart() {
	if (cart_all_checked) {
		setCheckboxes('cart_form', 'notice', false);
		cart_all_checked = false;
		if (document.getElementById('show_cart_checked_all').nodeName == 'BUTTON') {
			document.getElementById('show_cart_checked_all').innerHTML = pmbDojo.messages.getMessage('cart', 'show_cart_check_all');
		} else {
			document.getElementById('show_cart_checked_all').value = pmbDojo.messages.getMessage('cart', 'show_cart_check_all');
		}
		document.getElementById('show_cart_checked_all').title = pmbDojo.messages.getMessage('cart', 'show_cart_check_all');
	} else {
		setCheckboxes('cart_form', 'notice', true);
		cart_all_checked = true;
		if (document.getElementById('show_cart_checked_all').nodeName == 'BUTTON') {
			document.getElementById('show_cart_checked_all').innerHTML = pmbDojo.messages.getMessage('cart', 'show_cart_uncheck_all');
		} else {
			document.getElementById('show_cart_checked_all').value = pmbDojo.messages.getMessage('cart', 'show_cart_uncheck_all');
		}
		document.getElementById('show_cart_checked_all').title = pmbDojo.messages.getMessage('cart', 'show_cart_uncheck_all');
	}
	return false;
}

function setCheckboxes(the_form, the_objet, do_check) {
	 var elts = document.forms[the_form].elements[the_objet+'[]'] ;
	 var elts_cnt = (typeof(elts.length) != 'undefined') ? elts.length : 0;
	 if (elts_cnt) {
		for (var i = 0; i < elts_cnt; i++) {
	 		elts[i].checked = do_check;
	 	}
	 } else {
	 	elts.checked = do_check;
	 }
	 return true;
}

function confirm_transform(){
	var is_check=false;
	var elts = document.getElementsByName('notice[]') ;
	if (!elts) is_check = false ;
	var elts_cnt  = (typeof(elts.length) != 'undefined') ? elts.length : 0;
	if (elts_cnt) {
		for (var i = 0; i < elts_cnt; i++) {
			if (elts[i].checked) {
				return true;
			}
		}
	}
	if(!is_check){
		alert(pmbDojo.messages.getMessage('opac', 'list_lecture_no_ck'));
		return false;
	}
	return is_check;
}

function show_more_actions(){
    var more_actions = document.getElementById('show_more_actions');
    if(more_actions.style.display == 'none'){
        more_actions.style.display = 'block';
    }else{
        more_actions.style.display = 'none';
    }
}

function download_docnum() {
	var url='./ajax.php?module=ajax&categ=download_docnum&sub=gen_list';
	window.open(url);

}

function download_docnum_notice_checked() {
	var is_check=false;
	var elts = document.getElementsByName('notice[]') ;
	var elts_chk = '';
	var elts_cnt  = (typeof(elts.length) != 'undefined')
              ? elts.length
              : 0;
	if (elts_cnt) {
		for (var i = 0; i < elts_cnt; i++) {
			if (elts[i].checked) {
				if (elts_chk == '') {
					elts_chk += elts[i].value;
				} else {
					elts_chk += ','+elts[i].value;
				}
			}
		}
	}
	if(elts_chk != '') {
		is_check=true;
	}
	if(!is_check){
		alert(pmbDojo.messages.getMessage('opac', 'docnum_download_no_ck'));
		return false;
	}
	var url='./ajax.php?module=ajax&categ=download_docnum&sub=gen_list';
	window.open(url+'&select_noti='+elts_chk);
}

function notice_checked(){
	var is_check=false;
	var elts = document.getElementsByName('notice[]') ;
	if (!elts) is_check = false ;
	var elts_cnt  = (typeof(elts.length) != 'undefined')
              ? elts.length
              : 0;
	if (elts_cnt) {
		for (var i = 0; i < elts_cnt; i++) {
			if (elts[i].checked) {
				return true;
			}
		}
	}
	if(!is_check){
		alert(pmbDojo.messages.getMessage('opac', 'list_lecture_no_ck'));
		return false;
	}

	return is_check;
}

function resa_cart_checked(popup=false, has_resa_planning=false) {
	var notice='';
	var data=document.forms['cart_form'].elements['notice[]'];

	if(typeof(data.length) != 'undefined'){
		for (var key = 0; key < data.length; key++) {
			if(data[key].checked && data[key].value){
				notice+='&notice[]='+data[key].value;
			}
		}
	}else{
		if(data.checked && data.value){
			notice+='&notice[]='+data.value;
		}
	}
	if(notice!=''){
		if(has_resa_planning) {
			var sub = 'resa_planning_cart_checked';
		} else {
			var sub = 'resa_cart_checked';
		}
		if(popup) {

			w=window.open('./do_resa.php?lvl=resa_cart&sub='+sub+notice,'doresa','scrollbars=yes,width=900,height=300,menubar=0,resizable=yes');
			w.focus();
		} else {
			document.location='./do_resa.php?lvl=resa_cart&sub='+sub+notice;
		}
		return false;
	}else{
		alert(pmbDojo.messages.getMessage('opac', 'resa_no_doc_selected'))
		return false;
	}
}

function getNoticeSelected(){
	if(document.getElementById('radio_exp_sel').checked){
		var items = '&select_item=';
		var notices =  document.forms['cart_form'].elements;
		if(notices['notice[]']){
			notices = notices['notice[]'];
		}
		var hasSelected = false;
		for (var i = 0; i < notices.length; i++) {
		 	if(notices[i].checked) {
		 		items += notices[i].value+',';
				hasSelected = true;
			}
		}
		if(!hasSelected) {
			alert(pmbDojo.messages.getMessage('opac', 'list_lecture_no_ck'));
			return false;
		} else return items;
	}
	return true;
}