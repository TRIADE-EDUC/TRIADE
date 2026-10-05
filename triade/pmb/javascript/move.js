// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: move.js,v 1.45.2.1 2024/09/03 13:33:42 dgoron Exp $

if (typeof(grid_type) == "undefined") {
	grid_type = 'records';
}
down=false;
down_parent=false;
child_move="";
decx=0;
decy=0;
posx=0;
posy=0;
pheight=0;
parent_move="";
parent_min=6;
parent_last_h=0;
relative=true;
formatpage="";
inedit=false;


function move(e) {
	e.cancelBubble = true;
	if (e.stopPropagation) e.stopPropagation();
	if (e.currentTarget.getAttribute("id")==child_move) {
		if (down==true) {
			var zx=posx*1+(e.screenX-decx)*1;
			if (zx<0) zx=0;
			var px=e.currentTarget.offsetParent.clientWidth;
			if (zx+e.currentTarget.clientWidth>px) zx=px-e.currentTarget.clientWidth;
			zx=zx+"px";
			var zy=posy*1+(e.screenY-decy)*1;
			if (zy<0) zy=0;
			var py=e.currentTarget.offsetParent.clientHeight;
			if (zy+e.currentTarget.clientHeight>py-6) {
				var nheight=zy+e.currentTarget.clientHeight+6;
				e.currentTarget.offsetParent.style.height=nheight+"px";
			}
			zy=zy+"px";
			e.currentTarget.style.left=zx;
			e.currentTarget.style.top=zy;
		}
	}
}

function move_parent(e) {
	if (!parent_move) parent_move="";
	var bord=e.currentTarget.clientHeight;
	if ((e.layerY>=bord-2)&&(e.layerY<=bord+2)) {
		e.currentTarget.style.cursor="n-resize";
	} else e.currentTarget.style.cursor="";
	if (e.currentTarget.getAttribute("id")==parent_move) {
		if (down_parent==true) {
			var nheight=pheight*1+(e.screenY-decy)*1;
			if ((nheight>parent_min)&&(nheight<parent_last_h)) {
				e.target.style.height=nheight+"px";
				parent_last_h=nheight;
			}
		}
	} else {
		down_parent=false;
		parent_move="";
	}
}

function malert(message) {
	me=document.getElementById("message");
	me.firstChild.nodeValue=message;
}

function change_onglet(onglet,champ) {
	var relp=relative;
	champ=document.getElementById(champ);
	onglet=document.getElementById(onglet);
	onglet.appendChild(champ);
	if (onglet.getAttribute("invert")=="yes") relp=(!relative); else relp=relative;
	if (!relp) {
		champ.style.position="absolute";
		champ.style.border="#999 2px solid";
		champ.style.background="#DDD"; 
	} else {
		champ.style.position="";
		champ.style.border="";
		champ.style.background=""; 
	}
	if (!relp) resize_onglet(onglet,true);
}

function resize_onglet(onglet,force) {
		var i=0;
		if ((!onglet.style.height)||(onglet.style.height=="0px")||(force)) {
			//Recherche de la hauteur maximale
			var childs=onglet.childNodes;
			var max=0;
			for (i=0; i<childs.length; i++) {
				var fromTop=childs[i].offsetTop;
				var cheight=childs[i].clientHeight;
				if (fromTop*1+cheight*1>max) max=fromTop*1+cheight*1;
			}
			var nheight=max*1+6*1
			onglet.style.height=nheight+"px";
		}
}

function place_fields(onglet) {
	//placement de tous les champs
	var j=0;
	var py=0;
	var childs=onglet.childNodes;
	for (j=0; j<childs.length; j++) {
		if (childs[j].nodeType==1) {
			if (childs[j].getAttribute("movable")=="yes") {
				childs[j].style.top=py+"px";
				py+=childs[j].clientHeight;
			}
		}
	}
}

function go_before(child_name) {
	child=document.getElementById(child_name);
	var childs=child.parentNode.childNodes;
	for (i=0; i<childs.length; i++) {
		if (childs[i].nodeType==1) {
			if (childs[i].getAttribute("id")==child_name) {
				break;
			}
		}
	}
	if (i<childs.length) {
		//recherche du suivant
		for (i=i+1;i<childs.length;i++) {
			if (childs[i].nodeType==1) {
				if (childs[i].getAttribute("movable")=="yes") {
					break;
				}
			}
		} 
		if (i<childs.length) {
			swap=child.parentNode.replaceChild(child,childs[i]);
			child.parentNode.insertBefore(swap,child);
		}
	}
	if (document.getElementById("popup_onglet")) document.getElementById("popup_onglet").parentNode.removeChild(document.getElementById("popup_onglet"));
}

function go_after(child_name) {
	child=document.getElementById(child_name);
	var childs=child.parentNode.childNodes;
	for (i=0; i<childs.length; i++) {
		if (childs[i].nodeType==1) {
			if (childs[i].getAttribute("id")==child_name) {
				break;
			}
		}
	}
	if ((i<childs.length)&&(i>0)) {
		//recherche du pr�c�dent
		for (i=i-1;i>=0;i--) {
			if (childs[i].nodeType==1) {
				if (childs[i].getAttribute("movable")=="yes") {
					break;
				}
			}
		} 
		if (i>=0) {
			child_i=childs[i];
			swap=child.parentNode.replaceChild(child_i,child);
			child_i.parentNode.insertBefore(swap,child_i);
		}
	}
	if (document.getElementById("popup_onglet")) document.getElementById("popup_onglet").parentNode.removeChild(document.getElementById("popup_onglet"));
}

function get_onglet_title(onglet) {
	var stop=false;
	var previous=onglet;
	while (!stop) {
		previous=previous.previousSibling;
		if (previous) {
			if (previous.nodeType==1) stop=true; 
		} else stop=true;
	}
	return previous;
}

function descendre_onglet(onglet_name) {
	child=document.getElementById(onglet_name);
	child_title=get_onglet_title(child);

	var childs=child.parentNode.childNodes;
	for (i=0; i<childs.length; i++) {
		if (childs[i].nodeType==1) {
			if (childs[i].getAttribute("id")==onglet_name) {
				break;
			}
		}
	}
	if (i<childs.length) {
		//recherche du suivant
		for (i=i+1;i<childs.length;i++) {
			if (childs[i].nodeType==1) {
				if (childs[i].getAttribute("etirable")=="yes") {
					break;
				}
			}
		} 
		if (i<childs.length) {
			//Recherche du titre de l'onglet
			new_title=get_onglet_title(childs[i]);
			swap=child.parentNode.replaceChild(child,childs[i]);
			swap_title=child_title.parentNode.replaceChild(child_title,new_title);
			child.parentNode.insertBefore(swap,child_title);
			child.parentNode.insertBefore(swap_title,swap);
		}
	}
	if (document.getElementById("popup_onglet")) document.getElementById("popup_onglet").parentNode.removeChild(document.getElementById("popup_onglet"));
}

function monter_onglet(onglet_name) {
	onglet=document.getElementById(onglet_name);
	onglet_title=get_onglet_title(onglet);
	var childs=onglet.parentNode.childNodes;
	for (i=0; i<childs.length; i++) {
		if (childs[i].nodeType==1) {
			if (childs[i].getAttribute("id")==onglet_name) {
				break;
			}
		}
	}
	if ((i<childs.length)&&(i>0)) {
		//recherche du pr�c�dent
		for (i=i-1;i>=0;i--) {
			if (childs[i].nodeType==1) {
				if (childs[i].getAttribute("etirable")=="yes") {
					break;
				}
			}
		} 
		if (i>=0) {
			child_i=childs[i];
			child_i_title=get_onglet_title(child_i);
			child_i.parentNode.insertBefore(onglet,child_i_title);
			child_i.parentNode.insertBefore(onglet_title,onglet);
		}
	}
	if (document.getElementById("popup_onglet")) document.getElementById("popup_onglet").parentNode.removeChild(document.getElementById("popup_onglet"));
}

function go_first(child_name) {
	child=document.getElementById(child_name);
	child.parentNode.appendChild(child);
	if (document.getElementById("popup_onglet")) document.getElementById("popup_onglet").parentNode.removeChild(document.getElementById("popup_onglet"));
}

function go_last(child_name) {
	child=document.getElementById(child_name);
	var childs=child.parentNode.childNodes;
	//Recherche du premier movable !!
	for (i=0;i<childs.length;i++) {
		if (childs[i].nodeType==1) {
			if (childs[i].getAttribute("movable")=="yes") {
				break;
			}
		}
	} 
	if (i<childs.length) {
		child.parentNode.insertBefore(child,childs[i]);
	}
	if (document.getElementById("popup_onglet")) document.getElementById("popup_onglet").parentNode.removeChild(document.getElementById("popup_onglet"));
}

function invisible(child_name) {
	child=document.getElementById(child_name);
	child.style.display="none";
	child.setAttribute("hide","yes");
	if (document.getElementById("popup_onglet")) document.getElementById("popup_onglet").parentNode.removeChild(document.getElementById("popup_onglet"));
	if (grid_type == 'readers') {
		recalc_recept();
	}
}

function invisible_onglet(onglet_name) {
	child=document.getElementById(onglet_name);
	child.style.display="none";
	child.setAttribute("hide","yes");
	var stop=false;
	var previous=child;
	while (!stop) {
		previous=previous.previousSibling;
		if (previous) {
			if (previous.nodeType==1) { previous.style.display="none"; stop=true; }
		} else stop=true;
	}
	if (document.getElementById("popup_onglet")) document.getElementById("popup_onglet").parentNode.removeChild(document.getElementById("popup_onglet"));
}

function visible(child_name) {
	child=document.getElementById(child_name);
	child.style.display="";
	child.setAttribute("hide","");
	if(grid_type == 'readers') {
		recalc_recept();
	}
}

function visible_onglet(onglet_name) {
	child=document.getElementById(onglet_name);
	child.style.display="";
	child.setAttribute("hide","");
	var stop=false;
	var previous=child;
	while (!stop) {
		previous=previous.previousSibling;
		if (previous) {
			if (previous.nodeType==1) { previous.style.display=""; stop=true; }
		} else stop=true;
	}
	place_fields(child)
	resize_onglet(child,true);
}

function open_onglet(onglet_name) {
	var child=document.getElementById(onglet_name);
	child.setAttribute("startOpen","yes");
	place_fields(child);
	if (document.getElementById("popup_onglet")) document.getElementById("popup_onglet").parentNode.removeChild(document.getElementById("popup_onglet"));
}

function close_onglet(onglet_name) {
	var child=document.getElementById(onglet_name);
	child.setAttribute("startOpen","no");
	if (document.getElementById("popup_onglet")) document.getElementById("popup_onglet").parentNode.removeChild(document.getElementById("popup_onglet"));

}

function save_all(e, backbones) {
	var xml="<formpage relative='"+(relative?"yes":"no")+"'>\n";
	var etn=0;
	var relp=relative;
	var onglet_state= new Array();
		
	if (document.getElementById("popup_onglet")) document.getElementById("popup_onglet").parentNode.removeChild(document.getElementById("popup_onglet"));
	etirables=document.getElementsByTagName("div");
	var i=0;
	for (i=0; i<etirables.length; i++) {
		if (etirables[i].getAttribute("etirable")=="yes") {	
			if (etirables[i].getAttribute("invert")=="yes") relp=(!relative); else relp=relative;
			etn++;
			xml+="  <etirable id='"+etirables[i].getAttribute("id")+"' visible='"+(etirables[i].getAttribute("hide")=="yes"?"no":"yes")+"' "+(etirables[i].getAttribute("invert")=="yes"?"invert='yes' ":"")+" order='"+etn+"' ";
			if (!relp) xml+=" width='"+etirables[i].clientWidth+"' height='"+etirables[i].clientHeight+"' ";			
			xml+= "startOpen='" + (etirables[i].getAttribute("startOpen")=="yes"?"yes":"no") + "'";		
			xml+="/>\n";
		}
	}
	for (i=0; i<etirables.length; i++) {
		if (etirables[i].getAttribute("movable")=="yes") {
			if (etirables[i].parentNode.getAttribute("invert")=="yes") relp=(!relative); else relp=relative;
			xml+="  <movable id='"+etirables[i].getAttribute("id")+"' visible='"+(etirables[i].style.display=="none"?"no":"yes")+"' parent='"+etirables[i].parentNode.getAttribute("id")+"'";
			if (!relp) xml+=" left='"+etirables[i].offsetLeft+"' top='"+etirables[i].offsetTop+"'";
			xml+="/>\n";
		}
	}
	xml+="</formpage>";
	
	var empr_grille_categ = document.getElementById('empr_grille_categ');
	if(empr_grille_categ) {
		// Grille fiche lecteur
		var empr_grille_location = document.getElementById('empr_grille_location');	
		var url = "./ajax.php?module=circ&categ=empr&sub=set_empr_grille";
		var post_params = "&empr_grille_categ="+empr_grille_categ.value+"&empr_grille_location=";
		if (empr_grille_location) post_params+= empr_grille_location.value;
		post_params+= "&empr_grille_format="+encodeURIComponent(xml);
		if(backbones) {
			post_params+= "&backbones=1";
		}
		var req = new http_request();
		if(req.request(url,1,post_params)){
			alert(msg_move_saved_error);
		} else {
			var resp = req.get_text();
			alert(msg_move_saved_ok);
		}
		return;
	}
	
	// Grille notice
	var grille_typdoc='a';
	var grille_niveau_biblio='m';
	var grille_loc=0;
	ajax_creerRequete("sauve_notice");
	requete["sauve_notice"].open("POST","save_notice_pos.php",true);
	requete["sauve_notice"].onreadystatechange=function() { move_saved() };
	requete["sauve_notice"].setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	grille_typdoc=document.notice.typdoc.value;
	grille_niveau_biblio=document.notice.b_level.value;
	if (document.notice.grille_location) grille_loc=document.notice.grille_location.value;
	requete["sauve_notice"].send("datas="+encode_URL(xml)+"&grille_typdoc="+encode_URL(grille_typdoc)+"&grille_niveau_biblio="+encode_URL(grille_niveau_biblio)+"&grille_location="+encode_URL(grille_loc));
}

function addTinyEditor(text_areas_with_tinymce) {
	  if (text_areas_with_tinymce.length >0) {
		  for (var j=0; j<text_areas_with_tinymce.length; j++) {
			  tinyMCE_execCommand('mceAddControl', true, text_areas_with_tinymce[j]);
		  }
	  }
}

function addDateboxes(dateboxes) {
	if(dateboxes.length){ //on a des dates à recréer
		require(['dojo/dom', 'dojo/dom-construct', 'dojo/dom-attr'], function(dom, domConstruct, domAttr){
			dateboxes.forEach(dateObj => {
				if(dateObj.value){
					var val = new Date(dateObj.value);
					var day = val.getDate();
					var month = val.getMonth()+1;

					var year = val.getFullYear();
					if(day<10){
					    day='0'+day;
					} 
					if(month<10){
					    month='0'+month;
					} 
					day = year+'-'+month+'-'+day;
					dateObj.params.value = day;	
				}
				
				delete dateObj.params.constraints;
				dateObj.params['data-dojo-type']='dijit/form/DateTextBox';
				
				var input = domConstruct.create('input', dateObj.params, dom.byId(dateObj.divId), 'replace');
				input.setAttribute('value', dateObj.params.value);
			});
		});
	}
}

function move_fields(domXML) {
	var need_parse = false;
	var dateboxes = [];
	if(typeof(dojo) == "object"){
		var widgets = dijit.registry.toArray();
		for(var i=0 ; i<widgets.length ; i++){
			if (widgets[i].declaredClass != 'dGrowl') {
				/**
				 * Cas spécial pour les dates dojo
				 */
				if(widgets[i].declaredClass == 'dijit.form.DateTextBox'){
					require(['dojo/dom-construct'], function(domConstruct){
						domConstruct.create('div', {id: widgets[i].domNode.id+'_save'}, widgets[i].domNode, 'after');	
					});
					dateboxes.push({
						params: widgets[i].params,
						value: widgets[i].get('value'),
						divId: widgets[i].domNode.id+'_save'
					});
					widgets[i].destroy();
				}else{
					widgets[i].destroy(true);
				}
				need_parse = true;
			}
		}
  	}
	var text_areas = document.getElementsByTagName('textarea');
	text_areas_with_tinymce = new Array();
	if (text_areas.length >0) {
		for (var j=0; j<text_areas.length; j++) {
			if(typeof(tinyMCE) != 'undefined' && typeof(tinyMCE_getInstance) != 'undefined') {
				var test = tinyMCE_getInstance(text_areas[j].getAttribute("id"));
				if (test != null) {
					tinyMCE_execCommand('mceRemoveControl', true, text_areas[j].getAttribute("id"));
					text_areas_with_tinymce.push(text_areas[j].getAttribute("id"));
				}
			}
		}
	}
	
	if(grid_type == 'readers') {
		move_empr_fields(domXML);
	} else {
		root=domXML.getElementsByTagName("formpage");
		relative=root[0].getAttribute("relative");
		if (relative=="yes") relative=true; else relative=false;
		
		var relp=relative;
		
		var etirables=domXML.getElementsByTagName("etirable");
		var parent_onglet=false;
		for (i=0; i<etirables.length; i++) {
			if(document.getElementById(etirables[i].getAttribute("id"))) {
				parent_onglet=document.getElementById(etirables[i].getAttribute("id")).parentNode;
				break;
			}
		}
		if(!parent_onglet){
			if(dateboxes.length){ //on a des dates à recréer
				this.addDateboxes(dateboxes);
			}
	      	//Ajout des noeuds contenant l'editeur HTML
			this.addTinyEditor(text_areas_with_tinymce);
			return;
		}
		var onglet=new Array();
		var onglet_titre=Array();
		var fields= new Array();
		var id=0;
		
		for (i=0; i<etirables.length; i++) {
			//Onglets flottants
			id=etirables[i].getAttribute("id");
			if(!document.getElementById(id)) continue;
			//on reg�n�re le dom des textarea, le navigateur se contente d'affecter la propri�t� value... 
			var text_areas = document.getElementById(id).getElementsByTagName('textarea');
			for(var x=0 ; x<text_areas.length ; x++){
				if(!text_areas[x].firstChild){
					text_areas[x].appendChild(document.createTextNode(text_areas[x].value));
				}
			}
			//on reg�n�re le dom des select, le navigateur se contente d'affecter la propri�t� selected sans recr�er l'attribut... 
			var selects = document.getElementById(id).getElementsByTagName('select');
			for(var x=0 ; x<selects.length ; x++){
				for(var y=0 ; y<selects[x].options.length ; y++){
					if(selects[x].options[y].selected){
						selects[x].options[y].setAttribute('selected','selected');
					} else {
						selects[x].options[y].removeAttribute('selected');
					}
				}
			}
			onglet[i]=document.getElementById(id).cloneNode(true);
			if (etirables[i].getAttribute("invert")=="yes") onglet[i].setAttribute("invert","yes"); else onglet[i].setAttribute("invert","");
			var onglet_tit=get_onglet_title(document.getElementById(id));
			onglet_titre[i]=onglet_tit.cloneNode(true);
			parent_onglet.removeChild(document.getElementById(id));
			parent_onglet.removeChild(onglet_tit);
		}
		for (i=0; i<etirables.length; i++) {
			//Remise en ordre
			if(!onglet_titre[i]) continue;
			parent_onglet.appendChild(onglet_titre[i]);
			parent_onglet.appendChild(onglet[i]);
			if (onglet[i].getAttribute("invert")=="yes") 
				relp=(!relative)
			else relp=relative;
			onglet[i].style.position=relp?"":"relative";
			if (!relp) onglet[i].style.height=etirables[i].getAttribute("height")+"px"; else onglet[i].style.height="";
			if (etirables[i].getAttribute("visible")=="no") {
				onglet_titre[i].style.display="none";
				onglet[i].style.display="none";
				onglet[i].setAttribute("hide","yes");
			} else {
				onglet_titre[i].style.display="block";
				onglet[i].style.display="block";
				onglet[i].setAttribute("hide","");
			}	

			if(etirables[i].getAttribute("startOpen")=="yes"){
				onglet[i].setAttribute("startOpen","yes");
			}
			if(etirables[i].getAttribute("startOpen")=="no"){
				onglet[i].setAttribute("startOpen","no");
			}
			if(etirables[i].getAttribute("visible")=="no"){
				onglet[i].setAttribute("startOpen","no");
			}
			if(etirables[i].getAttribute("startOpen")!="no" && onglet[i].id=='el0Child'){
				onglet[i].setAttribute("startOpen","yes");
			}
		}

		var movables=domXML.getElementsByTagName("movable");

		for (i=0; i<movables.length; i++) {
			id=movables[i].getAttribute("id");
			var parent_id=movables[i].getAttribute("parent");
			var mov=document.getElementById(id);
			if (mov != null && document.getElementById(parent_id)) {
				var new_mov=mov.cloneNode(true);
				mov.parentNode.removeChild(mov);
				document.getElementById(parent_id).appendChild(new_mov);
				//Positionnement en fonction de relative
				if (document.getElementById(parent_id).getAttribute("invert")=="yes") 
					relp=(!relative) 
				else relp=relative;
				new_mov.style.position=relp?"":"absolute";
				if (!relp) {
					new_mov.style.left=movables[i].getAttribute("left")+"px";
					new_mov.style.top=movables[i].getAttribute("top")+"px";
				} else {
					new_mov.style.left="";
					new_mov.style.top="";
				} 
				if (movables[i].getAttribute("visible")=="no") {
					new_mov.style.display="none";
				} else {
					new_mov.style.display="block";
				}
			}
		}
		parent_onglet.style.visibility="visible";
	}
	
	if(need_parse){
		if(dateboxes.length){ //on a des dates à recréer
			this.addDateboxes(dateboxes);
		}
		if(grid_type == 'readers') {
			dojo.parser.parse(document.getElementById('empr_form'));
		} else {
			dojo.parser.parse(document.getElementById('notice'));
		}
	}
	//Ajout des noeuds contenant l'editeur HTML
	this.addTinyEditor(text_areas_with_tinymce);

}

function move_getted_pos() {
	// Grille notice
	if (requete["get_notice"].readyState==4) {
		if (requete["get_notice"].status=="200") {
			var formatpage=requete["get_notice"].responseXML;
			if (formatpage) {
				var startOfMove=new Event('movestart');
				document.body.dispatchEvent(startOfMove);
				move_fields(formatpage);
				var endOfMove=new Event('moveend');
				document.body.dispatchEvent(endOfMove);
			}
		} else formatpage=null;
	}
}

function get_pos(def) {
	var empr_grille_categ = document.getElementById('empr_grille_categ');
	if(empr_grille_categ) {
		// Grille fiche lecteur
		if (def) {
			var empr_grille_location = document.getElementById('empr_grille_location');
			var url = "./ajax.php?module=circ&categ=empr&sub=get_default_empr_grille";
		} else {
			var empr_grille_categ=document.getElementById('form_categ');
			empr_grille_categ.setAttribute('onchange','get_pos();');
			var empr_grille_location=document.getElementById('empr_location_id');
			if(typeof calculate_type_abts != 'undefined') {
				empr_grille_location.setAttribute('onchange','get_pos();calculate_type_abts(this);');
			} else {
				empr_grille_location.setAttribute('onchange','get_pos();');
			}
			var url = "./ajax.php?module=circ&categ=empr&sub=get_empr_grille";
		}
		var post_params = "&empr_grille_categ="+empr_grille_categ.value+"&empr_grille_location="+empr_grille_location.value;
		var req = new http_request();
		if(req.request(url,1,post_params)){
		} else {
			if(def) {
				document.body.dispatchEvent(new Event('movestart'));
				move_fields(req.get_xml());
				document.body.dispatchEvent(new Event('moveend'));
			} else {
				//Sauvegarde tempo des valeurs
				var selected_location = empr_grille_location.value;
				var selected_categ = empr_grille_categ.value;
				//Traitement de la grille
				document.body.dispatchEvent(new Event('movestart'));
				move_fields(req.get_xml());
				document.body.dispatchEvent(new Event('moveend'));
				//Restitution des valeurs sélectionnées
				document.getElementById('form_categ').value = selected_categ;
				document.getElementById('empr_location_id').value = selected_location;
			}
		}
		return;
	}
	// Grille notice
	var grille_typdoc='a';
	var grille_niveau_biblio='m';

	ajax_creerRequete("get_notice");
	requete["get_notice"].open("POST","load_notice_pos.php",false);
	requete["get_notice"].setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	grille_typdoc=document.notice.typdoc.value;
	grille_niveau_biblio=document.notice.b_level.value;
	if(def) {
		requete["get_notice"].send("grille_typdoc=default&grille_niveau_biblio="+encode_URL(grille_niveau_biblio));
	}else {
		requete["get_notice"].send("grille_typdoc="+encode_URL(grille_typdoc)+"&grille_niveau_biblio="+encode_URL(grille_niveau_biblio));
	}
	move_getted_pos();
}

function get_default_pos() {
	get_pos(1);
}

function move_saved() {
	// Grille notice
	if (requete["sauve_notice"].readyState==4) {
		if (requete["sauve_notice"].status=="200") {
			if (requete["sauve_notice"].responseText=="OK") {
				alert(msg_move_saved_ok);
			} else alert(msg_move_saved_error+" "+requete["sauve_notice"].responseText);
		} else alert(msg_move_saved_error+" "+requete["sauve_notice"].responseText);
	}
}

function invert_onglet(id) {
	var relp=relative;
	var onglet=document.getElementById(id);
	
	if (document.getElementById("popup_onglet")) document.getElementById("popup_onglet").parentNode.removeChild(document.getElementById("popup_onglet"));
	
	if (onglet.getAttribute("invert")=="yes") {
		onglet.setAttribute("invert","");
		relp=relative;
	} else {
		onglet.setAttribute("invert","yes");
		relp=(!relative);
	}
	
	if (relp) onglet.style.position=""; else onglet.style.position="relative";
	
	movables=document.getElementsByTagName("div");
	for(i=0; i<movables.length; i++) {
		if ((movables[i].getAttribute("movable")=="yes")&&(movables[i].parentNode.getAttribute("id")==id)) {			
			if (!relp) {
				movables[i].style.border="#999 2px solid";
				movables[i].style.background="#DDD";
			} else {
				movables[i].style.border="";
				movables[i].style.background="";
			}
			movables[i].style.position=relp?"":"absolute";
		}
	}
	if (!relp) resize_onglet(onglet,true); else onglet.style.height="";
}

function move_parse_dom(rel) {
	relative=rel;
	inedit=true;
	var i=0;
	
	//Rendre visible les listes des categories et localisations et desactiver les selecteurs du formulaire
	var sc = document.getElementById('form_categ');
	if(sc) {
		move_empr_parse_dom(rel);	
	} else {	
		// Grille notice
		//Rendre visible la liste des localisations
		if (document.getElementById("grille_location"))
			document.getElementById("grille_location").style.display="block";
	}
	//Rendre invisible le bouton d'�dition et visible le bouton de switch
	if(document.getElementById("bt_swap_relative")) {
		if (!relative) 
			document.getElementById("bt_swap_relative").value=msg_move_to_relative_pos;
		else
			document.getElementById("bt_swap_relative").value=msg_move_to_absolute_pos;
		document.getElementById("bt_swap_relative").style.display="";
	}
	document.getElementById("bt_inedit").style.display="none";
	init_movables(relative);
}

function init_movables(rel) {
	relative=rel;
	movables=document.getElementsByTagName("div");
	for(i=0; i<movables.length; i++) {
		if (movables[i].getAttribute("movable")=="yes") {
			if (movables[i].parentNode.getAttribute("invert")=="yes") relp=(!relative); else relp=relative;
			if (!relp) {
				movables[i].style.border="#999 2px solid";
				movables[i].style.background="#DDD";
				movables[i].style.width='100%';
			} else {
				movables[i].style.border="";
				movables[i].style.background="";
			}
			movables[i].style.position=relp?"":"absolute";
			movables[i].onmousedown=function(e) {
				e.cancelBubble = true;
				if (e.stopPropagation) e.stopPropagation();
				down=true;
				child_move=e.currentTarget.getAttribute("id");
				sx=document.getElementById("sx");
				sy=document.getElementById("sy");
				sid=document.getElementById("sid");
				posx=e.currentTarget.style.left;
				posy=e.currentTarget.style.top;
				if (posx.substr(-2,2)=="px") posx=posx.substr(0,posx.length-2);
				if (posy.substr(-2,2)=="px") posy=posy.substr(0,posy.length-2);
				decx=e.screenX;
				decy=e.screenY;
			}
			movables[i].onmousemove=move;
			movables[i].onmouseup=function(e) {
				e.cancelBubble = true;
				if (e.stopPropagation) e.stopPropagation();
				down=false;
			}
			movables[i].onmouseover=function(e) {
				e.currentTarget.style.cursor="pointer";
			}
			movables[i].onclick=function(e) {
				var i;
				var relp=relative;
				if (e.ctrlKey || e.metaKey) {
					if (e.currentTarget.parentNode.getAttribute("invert")=="yes") relp=(!relative); else relp=relative;
					if (document.getElementById("popup_onglet")) document.getElementById("popup_onglet").parentNode.removeChild(document.getElementById("popup_onglet"));
					e.cancelBubble = true;
					if (e.stopPropagation) e.stopPropagation();
					popup=document.createElement("div");
					popup.setAttribute("id","popup_onglet");
					popup.style.border="#000 1px solid";
					popup.style.background="#EEE";
					popup.style.position="absolute";
					popup.style.zIndex=10;
					popup.style.left=e.pageX+"px";
					popup.style.top=e.pageY+"px";
					var etirables=document.getElementsByTagName("div");
					var textHtml="<div style='width:100%;background:#FFF;border-bottom:#000 2px solid;text-align:center'><b>"+(e.currentTarget.getAttribute("title")?e.currentTarget.getAttribute("title"):e.currentTarget.getAttribute("id"))+"</b></div>";
					textHtml+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#CCC\"; this.style.color=\"#000\";' style='width:100%;background:#CCC' onClick='go_last(\""+e.currentTarget.getAttribute("id")+"\")'>"+(!relp?msg_move_last_plan:msg_move_first_plan)+"</div>";
					textHtml+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#CCC\"; this.style.color=\"#000\";' style='width:100%;background:#CCC' onClick='go_after(\""+e.currentTarget.getAttribute("id")+"\")'>"+(!relp?msg_move_behind:msg_move_up)+"</div>";
					textHtml+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#CCC\"; this.style.color=\"#000\";' style='width:100%;background:#CCC' onClick='go_before(\""+e.currentTarget.getAttribute("id")+"\")'>"+(!relp?msg_move_infront:msg_move_down)+"</div>";
					textHtml+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#CCC\"; this.style.color=\"#000\";' style='width:100%;background:#CCC' onClick='go_first(\""+e.currentTarget.getAttribute("id")+"\")'>"+(!relp?msg_move_first_plan:msg_move_last)+"</div>";
					textHtml+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#CCC\"; this.style.color=\"#000\";' style='width:100%;background:#CCC' onClick='invisible(\""+e.currentTarget.getAttribute("id")+"\")'>"+msg_move_invisible+"</div>";
					var textHtml_visible="";
					for(i=0; i<etirables.length; i++) {
						if ((etirables[i].getAttribute("movable")=="yes")&&(etirables[i].style.display=="none")) {
							textHtml_visible+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#EEE\"; this.style.color=\"#000\";' style='width:100%' onclick='visible(\""+etirables[i].getAttribute("id")+"\"); this.parentNode.parentNode.removeChild(this.parentNode);'>&nbsp;&nbsp;"+(etirables[i].getAttribute("title")?etirables[i].getAttribute("title"):etirables[i].getAttribute("id"))+"</div>";
						}
					}
					if (textHtml_visible) {
						textHtml+="<div style='width:100%;background:#CCC;color:#333;'>"+msg_move_visible+"</div>";
						textHtml+=textHtml_visible;
					}
					textHtml+="<div style='width:100%;background:#CCC;color:#333;'>"+msg_move_inside_tab+"</div>";
					for(i=0; i<etirables.length; i++) {
						if (etirables[i].getAttribute("etirable")=="yes") {
							textHtml+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#EEE\"; this.style.color=\"#000\";' style='width:100%' onclick='change_onglet(\""+etirables[i].getAttribute("id")+"\",\""+e.currentTarget.getAttribute("id")+"\"); this.parentNode.parentNode.removeChild(this.parentNode);'>&nbsp;&nbsp;"+(etirables[i].getAttribute("title")?etirables[i].getAttribute("title"):etirables[i].getAttribute("id"))+"</div>";
						}
					}
					textHtml+="<div style='width:100%;background:#FFF;border-bottom:#000 2px solid;border-top:#000 1px solid;text-align:center'><b>"+(e.currentTarget.parentNode.getAttribute("title")?e.currentTarget.parentNode.getAttribute("title"):e.currentTarget.parentNode.getAttribute("id"))+"</b></div>";
					textHtml+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#CCC\"; this.style.color=\"#000\";' style='width:100%;background:#CCC' onClick='monter_onglet(\""+e.currentTarget.parentNode.getAttribute("id")+"\")'>"+msg_move_up_tab+"</div>";
					textHtml+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#CCC\"; this.style.color=\"#000\";' style='width:100%;background:#CCC' onClick='descendre_onglet(\""+e.currentTarget.parentNode.getAttribute("id")+"\")'>"+msg_move_down_tab+"</div>";
					textHtml+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#CCC\"; this.style.color=\"#000\";' style='width:100%;background:#CCC' onClick='invert_onglet(\""+e.currentTarget.parentNode.getAttribute("id")+"\")'>"+msg_move_position_tab+" "+(relp?msg_move_position_absolute_tab:msg_move_position_relative_tab)+"</div>";
					textHtml+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#CCC\"; this.style.color=\"#000\";' style='width:100%;background:#CCC' onClick='invisible_onglet(\""+e.currentTarget.parentNode.getAttribute("id")+"\")'>"+msg_move_invisible_tab+"</div>";
					textHtml+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#CCC\"; this.style.color=\"#000\";' style='width:100%;background:#CCC' onClick='open_onglet(\""+e.currentTarget.parentNode.getAttribute("id")+"\")'>"+msg_move_open_tab+"</div>";
					textHtml_visible="";
					for(i=0; i<etirables.length; i++) {
						if ((etirables[i].getAttribute("etirable")=="yes")&&(etirables[i].getAttribute("hide")=="yes")) {
							textHtml_visible+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#EEE\"; this.style.color=\"#000\";' style='width:100%' onclick='visible_onglet(\""+etirables[i].getAttribute("id")+"\"); this.parentNode.parentNode.removeChild(this.parentNode);'>&nbsp;&nbsp;"+(etirables[i].getAttribute("title")?etirables[i].getAttribute("title"):etirables[i].getAttribute("id"))+"</div>";
						}
					}
					if (textHtml_visible) {
						textHtml+="<div style='width:100%;background:#CCC;color:#333;'>"+msg_move_visible_tab+"</div>";
						textHtml+=textHtml_visible;
					}
					var textHtml_open="";
					for(i=0; i<etirables.length; i++) {
						if ((etirables[i].getAttribute("etirable")=="yes")&&(etirables[i].getAttribute("startOpen")=="yes")) {
							textHtml_open+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#EEE\"; this.style.color=\"#000\";' style='width:100%' onclick='close_onglet(\""+etirables[i].getAttribute("id")+"\"); this.parentNode.parentNode.removeChild(this.parentNode);'>&nbsp;&nbsp;"+(etirables[i].getAttribute("title")?etirables[i].getAttribute("title"):etirables[i].getAttribute("id"))+"</div>";
						}
					}
					if (textHtml_open) {
						textHtml+="<div style='width:100%;background:#CCC'>"+msg_move_close_tab+"</div>";
						textHtml+=textHtml_open;
					}
					textHtml+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#CCC\"; this.style.color=\"#000\";' style='width:100%;background:#CCC' onClick='save_all(event);'>"+msg_move_save+"</div>";
					
					// Sauver et appliquer a toutes les grilles
					var empr_grille_categ = document.getElementById('empr_grille_categ');
					if(empr_grille_categ) {
						// Grille fiche lecteur
						var empr_grille_location = document.getElementById('empr_grille_location');
						if(!parseInt(empr_grille_categ.value) || !parseInt(empr_grille_location.value)) {
							textHtml+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#CCC\"; this.style.color=\"#000\";' style='width:100%;background:#CCC;' onClick='save_all(event, true);'>"+msg_move_save_backbones+"</div>";
						}
					}
					
					popup.innerHTML=textHtml;
					document.body.appendChild(popup);
					popup.onmouseover=function(e) {
						e.currentTarget.style.cursor="default";
					}
				}
			}
		}
		if (movables[i].getAttribute("etirable")=="yes") {
			if (movables[i].getAttribute("invert")=="yes") relp=(!relative); else relp=relative;
			movables[i].style.border="#000 1px solid";
			movables[i].style.position=relp?"":"relative";
			movables[i].onmousedown=function(e) {
				var bord=e.currentTarget.clientHeight;
				if ((e.layerY>=bord-2)&&(e.layerY<=bord+2)) {
					down_parent=true;
					decy=e.screenY;
					pheight=e.currentTarget.clientHeight
					parent_last_h=pheight;
					parent_move=e.currentTarget.getAttribute("id");
					//Recherche de la hauteur maximale
					var childs=e.currentTarget.childNodes;
					var max=0;
					for (i=0; i<childs.length; i++) {
						var fromTop=childs[i].offsetTop;
						var cheight=childs[i].clientHeight;
						if (fromTop*1+cheight*1>max) max=fromTop*1+cheight*1;
					}
					parent_min=max*1+6*1;
				}
			}
			movables[i].ondblclick=function(e) {
				if (relative) return;
				var bord=e.currentTarget.clientHeight;
				if ((e.layerY>=bord-2)&&(e.layerY<=bord+2)) {
					down_parent=false;
					//Recherche de la hauteur maximale
					var childs=e.currentTarget.childNodes;
					var max=0;
					for (i=0; i<childs.length; i++) {
						var fromTop=childs[i].offsetTop;
						var cheight=childs[i].clientHeight;
						if (fromTop*1+cheight*1>max) max=fromTop*1+cheight*1;
					}
					var nheight=max*1+6*1
					e.currentTarget.style.height=nheight+"px";
				}
			}
			movables[i].onmousemove=move_parent;
			movables[i].onmouseout=function(e) {
				down_parent=false;
				parent_move="";
				e.currentTarget.style.cursor="";
			}
			movables[i].onmouseup=function(e) {
				down_parent=false;
				parent_move="";
				e.currentTarget.style.cursor="";
				//e.target.zIndex=0;
			}
			movables[i].onclick=function(e) {
				var i;
				var relp;
				if (e.ctrlKey || e.metaKey) {
					if (e.currentTarget.getAttribute("invert")=="yes") relp=(!relative); else relp=relative;
					if (document.getElementById("popup_onglet")) document.getElementById("popup_onglet").parentNode.removeChild(document.getElementById("popup_onglet"));
					e.cancelBubble = true;
					if (e.stopPropagation) e.stopPropagation();
					popup=document.createElement("div");
					popup.setAttribute("id","popup_onglet");
					popup.style.border="#000 1px solid";
					popup.style.background="#EEE";
					popup.style.position="absolute";
					popup.style.zIndex=10;
					popup.style.left=e.pageX+"px";
					popup.style.top=e.pageY+"px";
					var etirables=document.getElementsByTagName("div");
					var textHtml="<div style='width:100%;background:#FFF;border-bottom:#000 2px solid;text-align:center'><b>"+(e.currentTarget.getAttribute("title")?e.currentTarget.getAttribute("title"):e.currentTarget.getAttribute("id"))+"</b></div>";
					textHtml+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#CCC\"; this.style.color=\"#000\";' style='width:100%;background:#CCC' onClick='monter_onglet(\""+e.currentTarget.getAttribute("id")+"\")'>"+msg_move_up_tab+"</div>";
					textHtml+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#CCC\"; this.style.color=\"#000\";' style='width:100%;background:#CCC' onClick='descendre_onglet(\""+e.currentTarget.getAttribute("id")+"\")'>"+msg_move_down_tab+"</div>";
					textHtml+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#CCC\"; this.style.color=\"#000\";' style='width:100%;background:#CCC' onClick='invert_onglet(\""+e.currentTarget.getAttribute("id")+"\")'>"+msg_move_position_tab+" "+(relp?msg_move_position_absolute_tab:msg_move_position_relative_tab)+"</div>";
					textHtml+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#CCC\"; this.style.color=\"#000\";' style='width:100%;background:#CCC' onClick='invisible_onglet(\""+e.currentTarget.getAttribute("id")+"\")'>"+msg_move_invisible_tab+"</div>";
					var textHtml_visible="";
					for(i=0; i<etirables.length; i++) {
						if ((etirables[i].getAttribute("etirable")=="yes")&&(etirables[i].getAttribute("hide")=="yes")) {
							textHtml_visible+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#EEE\"; this.style.color=\"#000\";' style='width:100%' onclick='visible_onglet(\""+etirables[i].getAttribute("id")+"\"); this.parentNode.parentNode.removeChild(this.parentNode);'>&nbsp;&nbsp;"+(etirables[i].getAttribute("title")?etirables[i].getAttribute("title"):etirables[i].getAttribute("id"))+"</div>";
						}
					}
					if (textHtml_visible) {
						textHtml+="<div style='width:100%;background:#CCC'>"+msg_move_visible_tab+"</div>";
						textHtml+=textHtml_visible;
					}
					textHtml_visible="";
					for(i=0; i<etirables.length; i++) {
						if ((etirables[i].getAttribute("movable")=="yes")&&(etirables[i].style.display=="none")) {
							textHtml_visible+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#EEE\"; this.style.color=\"#000\";' style='width:100%' onclick='visible(\""+etirables[i].getAttribute("id")+"\"); this.parentNode.parentNode.removeChild(this.parentNode);'>&nbsp;&nbsp;"+(etirables[i].getAttribute("title")?etirables[i].getAttribute("title"):etirables[i].getAttribute("id"))+"</div>";
						}
					}
					if (textHtml_visible) {
						textHtml+="<div style='width:100%;background:#FFF;border-bottom:#000 2px solid;border-top:#000 1px solid;text-align:center'><b>Champs</b></div>";
						textHtml+="<div style='width:100%;background:#CCC'>"+msg_move_visible+"</div>";
						textHtml+=textHtml_visible;
					}		
					var textHtml_open="";
					for(i=0; i<etirables.length; i++) {
						if ((etirables[i].getAttribute("etirable")=="yes")&&(etirables[i].getAttribute("startOpen")=="yes")) {
							textHtml_open+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#EEE\"; this.style.color=\"#000\";' style='width:100%' onclick='close_onglet(\""+etirables[i].getAttribute("id")+"\"); this.parentNode.parentNode.removeChild(this.parentNode);'>&nbsp;&nbsp;"+(etirables[i].getAttribute("title")?etirables[i].getAttribute("title"):etirables[i].getAttribute("id"))+"</div>";
						}
					}
					if (textHtml_open) {
						textHtml+="<div style='width:100%;background:#CCC'>"+msg_move_close_tab+"</div>";
						textHtml+=textHtml_open;
					}			
					textHtml+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#CCC\"; this.style.color=\"#000\";' style='width:100%;background:#CCC' onClick='save_all(event);'>"+msg_move_save+"</div>";
					
					// Sauver et appliquer a toutes les grilles
					var empr_grille_categ = document.getElementById('empr_grille_categ');
					if(empr_grille_categ) {
						// Grille fiche lecteur
						var empr_grille_location = document.getElementById('empr_grille_location');
						if(!parseInt(empr_grille_categ.value) || !parseInt(empr_grille_location.value)) {
							textHtml+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#CCC\"; this.style.color=\"#000\";' style='width:100%;background:#CCC;' onClick='save_all(event, true);'>"+msg_move_save_backbones+"</div>";
						}
					}
					
					popup.innerHTML=textHtml;
					document.body.appendChild(popup);
					popup.onmouseover=function(e) {
						e.currentTarget.style.cursor="default";
					}
				}
			}
		}
	}
	for(i=0; i<movables.length; i++) {
		if (movables[i].getAttribute("etirable")=="yes") {
			if (movables[i].getAttribute("invert")=="yes") relp=(!relative); else relp=relative;
			if (!relp) {
				//placement de tous les champs
				var j=0;
				var py=0;
				if (!movables[i].style.height) {
					var childs=movables[i].childNodes;
					for (j=0; j<childs.length; j++) {
						if (childs[j].nodeType==1) {
							if (childs[j].getAttribute("movable")=="yes") {
								childs[j].style.top=py+"px";
								py+=childs[j].clientHeight;
							}
						}
					}
				}
				resize_onglet(movables[i],false);
			} else {
				movables[i].style.height="";
			}
		}
	}
}

document.onclick=function(e) {
	if (e) {
		if (e.target.nodeType==1)
			if  ((e.target.parentNode)&&(e.target.parentNode.getAttribute("id")!="popup_onglet")&&(e.target.getAttribute("id")!="popup_onglet"))
				if (document.getElementById("popup_onglet")) document.getElementById("popup_onglet").parentNode.removeChild(document.getElementById("popup_onglet"));
	}
}