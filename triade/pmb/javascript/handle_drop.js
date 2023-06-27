/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: handle_drop.js,v 1.9 2017-10-04 14:52:33 jpermanne Exp $ */

requete=new Array();

function creerRequete(id) {
	try {
		requete[id]=new XMLHttpRequest();
	} catch (essaimicrosoft) {
		try {
			requete[id]=new ActiveXObject("Msxml2.XMLHTTP");
		} catch (autremicrosoft) {
			try {
				requete[id]=new ActiveXObject("Microsoft.XMLHTTP");
			} catch (echec) {
				requete[id]=null;
			}
		}
	}
}

function show_info(id) {
	var cadre;
	if (requete[id].readyState==4) {
		cadre=document.getElementById(id);
		if (requete[id].status=="200") {
			cadre.innerHTML=requete[id].responseText;
			document.getElementById('close_cart_pannel').onclick=function(){
				hide_carts('','','');
				return false;
			}
			init_recept();
		} else {
			cadre.innerHTML="Impossible d'obtenir la page !";
		}
	}
}

function get_info(id,url,datas) {
	creerRequete(id);
	requete[id].open("POST",url,true);
	requete[id].onreadystatechange=function() { show_info(id) };
	requete[id].setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		
	requete[id].send(datas);
}

function notice_cart(dragged,target) {
	id=dragged.getAttribute("id");
	id=id.substring(10);
	target.src="cart_info.php?id="+id+"&header="+encode_URL(dragged.innerHTML);
}

function cart_highlight(obj) {
	obj.style.background="#DDD";
}
function cart_downlight(obj) {
	obj.style.background="";
}

function show_carts(t,e) {
	if(!document.getElementById("pannel_carts")){
		var pannel=document.createElement("div");
		pannel.setAttribute("id","pannel_carts");
		pannel.style.width="300px";
		pannel.style.border="#000000 solid 1px";
		pannel.style.position="absolute";
		//pannel.style.float="right";
		pannel.style.background="#FFFFFF";
		pannel.style.overflow="auto";
		//pannel.className="main";
						
		if (window.innerWidth) {
			pannel.style.top=window.pageYOffset+"px";
			pannel.style.left=(parseFloat(window.innerWidth)+parseFloat(window.pageXOffset)-322)+"px";
			pannel.style.height=(window.innerHeight-3)+"px";
		} else {
			if (document.documentElement) {
				pannel.style.top=document.documentElement.scrollTop+"px";
				pannel.style.height=(document.documentElement.clientHeight-3)+"px";
				pannel.style.left=(parseFloat(document.documentElement.clientWidth)+parseFloat(document.documentElement.scrollLeft)-300)+"px";
			} else {
				pannel.style.top=document.body.scrollTop+"px";
				pannel.style.height=(document.body.clientHeight-3)+"px";
				pannel.style.left=(parseFloat(document.body.clientWidth)+parseFloat(document.body.scrollLeft)-300)+"px";
			}
		}
		pannel.style.zIndex=1500;
		document.getElementById("att").appendChild(pannel);
		get_info("pannel_carts","cart_list.php","");
	}
	return true;
}

function hide_carts(t,e,r) {
	var pannel_carts=document.getElementById("pannel_carts");
	if (pannel_carts) {
		pannel_carts.parentNode.removeChild(pannel_carts);
	}
}

function notice_caddie(dragged,target) {
	// alert(dragged.getAttribute("id"));
	var url= "./ajax.php?module=catalog&categ=caddie&caddie="+target.getAttribute("id")+"&object="+dragged.getAttribute("id");
	var ajout_caddie = new http_request();	
	retour_ajout = ajout_caddie.request(url);
	message_ajout=ajout_caddie.get_text();
	if ((retour_ajout) || (isNaN(message_ajout))) { 
		alert (message_ajout) ;
	}
	else{
		var id_nbitem=target.getAttribute("id").replace("_","_nbitem_");		
		document.getElementById(id_nbitem).innerHTML= " ("+message_ajout+") ";
		affichage_clignotant(target,0);
	}
}

var recepteurListe;

function affichage_clignotant(objet,cpt){
	
	recepteurListe = objet;
			
	if(cpt%2 == 0){
		cart_highlight(recepteurListe);
	} else{
		cart_downlight(recepteurListe);
	}	
	cpt++;
		
	if(cpt<8){
		setTimeout("affichage_clignotant(recepteurListe,"+cpt+")", 200);
	}
}


/**********************************
 *								  *				
 *      Tri des notices liées     *
 *                                * 
 **********************************/
/*
 * Fonction pour trier les filles
 */
function link_link(dragged,target){
	
	var pere_id = dragged.getAttribute("pere");
	var pere_cible_id = target.getAttribute("pere");
	
	var pere_type = dragged.getAttribute("type_rel");
	var pere_cible_type = target.getAttribute("type_rel");
	
	if(pere_id != pere_cible_id || pere_type != pere_cible_type) {
		recalc_recept();
		noti_downlight(target);
		alert('Vous ne pouvez changer le type de relation par le tri');
		return;
	}
	
	var pere=target.parentNode;
	pere.insertBefore(dragged,target);
	
	noti_downlight(target);
	
	recalc_recept();
	update_order(dragged,target);
}

function parents_parents(dragged,target) {
	link_link(dragged,target);
}

function pairs_pairs(dragged,target) {
	link_link(dragged,target);
}

function childs_childs(dragged,target) {
	link_link(dragged,target);
}

/*
 * Mis à jour de l'ordre
 */
function update_order(source,cible){
	var pere = source.parentNode.parentNode;
	
	var index = 0;
	var notices_relations_ids = new Array();
	for(var i=0;i<pere.childNodes.length;i++){
		if(pere.childNodes[i].nodeType == 1){
			pere.childNodes[i].setAttribute("order",index);
			if(pere.childNodes[i].getAttribute("notice_relation_id")){
				notices_relations_ids[index] = pere.childNodes[i].getAttribute("notice_relation_id");
			}
			index++;
		}
		if (pere.childNodes[i].childNodes.length) {
			for(var j=0;j<pere.childNodes[i].childNodes.length;j++){
				if(pere.childNodes[i].childNodes[j].nodeType == 1){
					pere.childNodes[i].childNodes[j].setAttribute("order",index);
					if(pere.childNodes[i].childNodes[j].getAttribute("notice_relation_id")){
						notices_relations_ids[index] = pere.childNodes[i].childNodes[j].getAttribute("notice_relation_id");
					}
					index++;
				}
			}
		}
	}
	
	var url= "./ajax.php?module=ajax&categ=tri&quoifaire=up_order";
	var action = new http_request();
	action.request(url,true,"notices_relations_ids="+notices_relations_ids.join(","));
}


function noti_highlight(obj) {
	obj.style.background="#DDD";
}
function noti_downlight(obj) {
	obj.style.background="";
}

function is_expandable(cible,e){
	
	var el = e.target || e.srcElement;
	var id_cible = (cible.id).substring(5);
	var drag = true;
	if(el.id == "el"+id_cible+"Img"){		
		drag = false;
	}	
	return drag;
}
