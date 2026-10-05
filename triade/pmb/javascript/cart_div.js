/* +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart_div.js,v 1.7 2020/06/24 09:21:03 dgoron Exp $ */

var info_div_show = 0;
var flag_mouseover_info_div = false;

window.addEventListener('click',function(e) {
	hide_carts_div('','','');
	e.stopPropagation;
	}
,false);

function set_flag_info_div(val) {
	flag_mouseover_info_div = val;
}

function show_info_div(id) {
	var cadre;
	if (requete[id].readyState==4) {
		cadre=document.getElementById(id);
		if (requete[id].status=="200") {
			cadre.innerHTML=requete[id].responseText;
			document.getElementById('close_cart_div').onclick=function(){
				hide_carts_div('','','');
				return false;
			};
			init_recept();
		} else {
			cadre.innerHTML="Impossible d'obtenir la page !";
		}
	}
}

function get_info_div(id,url,datas) {
	creerRequete(id);
	requete[id].open("POST",url,true);
	requete[id].onreadystatechange=function() { show_info_div(id); };
	requete[id].setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		
	requete[id].send(datas);
}

function show_div_access_carts(event,id_object,type_object,show_on_left) {
	if (info_div_show==id_object) {
		return true;
	}
	set_flag_info_div(true);
	setTimeout(function(){ show_div_access_carts_suite(event,id_object,type_object,show_on_left); }, 1000);
}

function show_div_access_carts_suite(event,id_object,type_object,show_on_left) {
	if (flag_mouseover_info_div == false) {
		return true;
	}
	if(document.getElementById("div_access_carts")){
		hide_carts_div('','','');
	}
	var pos=getCoordinate(event);
	posxdown=pos[0];
	posydown=pos[1];
	if (show_on_left != null) {
		posxdown=posxdown-300;
	} else if (posxdown > (document.body.clientWidth-300)) {
		posxdown=posxdown-300;
	}
	var pannel=document.createElement("div");
	pannel.setAttribute("id","div_access_carts");
	pannel.style.width="300px";
	pannel.style.heigth="300px";
	pannel.style.top=posydown+"px";
	pannel.style.left=posxdown+"px";
	pannel.style.border="#000000 solid 1px";
	pannel.style.position="absolute";
	pannel.style.background="#FFFFFF";
	
	pannel.style.zIndex=1500;
	document.body.appendChild(pannel);
	if(!type_object) type_object = 'NOTI';
	get_info_div("div_access_carts","cart_list.php","id_object="+id_object+"&type_object="+type_object);
	
	info_div_show = id_object;
	
	return true;
}


function hide_carts_div(t,e,r) {
	var pannel_carts=document.getElementById("div_access_carts");
	if (pannel_carts) {
		pannel_carts.parentNode.removeChild(pannel_carts);
		info_div_show = 0;
	}
}

function object_div_caddie(idobject,typeobject,idcaddie) {
	var module = '';
	switch(typeobject) {
	    case 'EMPR':
	        module = 'circ';
	        break;
	    case 'NOTI':
	    case 'BULL':
	    case 'EXPL':
	    	module = 'catalog';
	    	break;
	    default:
	    	module = 'autorites';
	}
	var url= "./ajax.php?module="+module+"&categ=caddie&caddie="+typeobject+"_"+idcaddie+"&object="+typeobject+"_DRAG_"+idobject;
	var ajout_caddie = new http_request();	
	retour_ajout = ajout_caddie.request(url);
	message_ajout=ajout_caddie.get_text();
	if ((retour_ajout) || (isNaN(message_ajout))) { 
		alert (message_ajout) ;
	}
	else{
		hide_carts_div('','','');
	}
}

function object_delete_caddie(idobject,typeobject,idcaddie) {
	var module = '';
	switch(typeobject) {
	    case 'EMPR':
	        module = 'circ';
	        break;
	    case 'NOTI':
	    case 'BULL':
	    case 'EXPL':
	    	module = 'catalog';
	    	break;
	    default:
	    	module = 'autorites';
	}
	var url= "./ajax.php?module="+module+"&categ=caddie&action=delete&caddie="+typeobject+"_"+idcaddie+"&object="+typeobject+"_DRAG_"+idobject;
	var request = new http_request();	
	var retour_delete = request.request(url);
	var message_delete = request.get_text();
	if ((retour_delete) || (isNaN(message_delete))) { 
		alert (message_delete) ;
	}
	else{
		hide_carts_div('','','');
	}
}