// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: move_empr.js,v 1.11.6.1 2024/09/03 13:33:42 dgoron Exp $

grid_type = 'readers';

widths = ( typeof widths != 'undefined' && widths instanceof Array ) ? widths : new Array('12.5%','25%','37.5%','50%','62.5%','75%','82.5%','100%');


function set_width(field_name,width) {
	var field=document.getElementById(field_name);
	field.style.width=width;	
	if (document.getElementById("popup_onglet")) document.getElementById("popup_onglet").parentNode.removeChild(document.getElementById("popup_onglet"));	
	recalc_recept();
}

function move_empr_fields(domXML) {
	root=domXML.getElementsByTagName("formpage");
	var id=0;
	var movables=domXML.getElementsByTagName("movable");
	for (i=0; i<movables.length; i++) {
		id=movables[i].getAttribute("id");
		var parent_id=movables[i].getAttribute("parent");
		var mov=document.getElementById(id);
		if (mov != null) {
			if(document.getElementById(parent_id)) {
				var parent=document.getElementById(parent_id);
			} else {
				//Bidouille pour tenter la rétro-compatibilité
				var parent_parent_id = parent_id.split('_');
				if(parent_parent_id[0] && document.getElementById(parent_parent_id[0])) {
					var parent=document.getElementById(parent_parent_id[0]);
				}
			}
			var lchild=parent.lastChild;
			while(lchild.nodeType!='1') {
				if(lchild.previousSibling) {
					lchild=lchild.previousSibling;
				} else {
					break;
				}
			}
			parent.insertBefore(mov,lchild);
			//Positionnement en fonction de relative
			mov.style.position="";
			mov.style.left="";
			mov.style.top="";
			var w=movables[i].getAttribute("width");
			if (w){
				mov.style.width=w;
			} else {
				mov.style.width='';
			}
			if (movables[i].getAttribute("visible")=="no") {
				mov.style.display="none";
			} else {
				mov.style.display="block";
			}
		}
	}
}

function move_empr_parse_dom(rel) {
	relative=rel;
	
	var sc = document.getElementById('form_categ');
	// Grille fiche lecteur
	var sgc = document.getElementById('empr_grille_categ');
	if (sgc!=null) {
		sgc.onchange = function(e) {
			get_pos();
			init_movables(relative);
		}
		sgc.style.display = "block";
		sgc.value = sc.value;
	}
	var sl = document.getElementById('empr_location_id');
	var sgl = document.getElementById('empr_grille_location');
	if (sgl!=null) {
		sgl.onchange = function(e) {
			get_pos();
			init_movables(relative);
			if(typeof calculate_type_abts != 'undefined') {
				calculate_type_abts(sgl);
			}
		}
		sgl.style.display = "block";
		sgl.value = sl.value;
	}
}

function init_movables() {
	var movables=document.getElementsByTagName("div");
	for(i=0; i<movables.length; i++) {
		if(movables[i].getAttribute("etirable")=="yes") {
			movables[i].style.border="#000000 1px solid";
			movables[i].style.minHeight="20px";
		}
		if (movables[i].getAttribute("movable")=="yes") {
			movables[i].style.border="#999999 2px solid";
			movables[i].style.background="#DDDDDD";
			movables[i].style.margin="10px 5px 10px 5px";
			
			movables[i].onmousedown=function(e) {
				e.cancelBubble = true;
				if (e.stopPropagation) e.stopPropagation();
				down=true;
				child_move=e.currentTarget.getAttribute("id");
				posx=e.currentTarget.style.left;
				posy=e.currentTarget.style.top;
				if (posx.substr(-2,2)=="px") posx=posx.substr(0,posx.length-2);
				if (posy.substr(-2,2)=="px") posy=posy.substr(0,posy.length-2);
				decx=e.screenX;
				decy=e.screenY;
			}
			//movables[i].onmousemove=move;
//			movables[i].onmouseup=function(e) {
//				e.cancelBubble = true;
//				if (e.stopPropagation) e.stopPropagation();
//				down=false;
//			}

//			movables[i].onmouseover=function(e) {
//				e.currentTarget.style.cursor="pointer";
//				e.cancelBubble = true;
//				if (e.stopPropagation) e.stopPropagation();
//			}
			
			movables[i].onclick=function(e) {
				var i;
				if (e.ctrlKey || e.metaKey) {
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
					for (var j=0;j<widths.length;j++) {
						textHtml+="<div onmouseover='this.style.background=\"#666\"; this.style.color=\"#FFF\";' onmouseout='this.style.background=\"#CCC\"; this.style.color=\"#000\";' style='width:100%;background:#CCC' onClick='set_width(\""+e.currentTarget.getAttribute("id")+"\",\""+widths[j]+"\")'>"+msg_move_width+" "+widths[j]+"</div>";
					}
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
}

//Mise en evidence cellule survolee
function circcell_highlight(obj) {
	obj.style.background="#CCC";
}


//Extinction cellule survolee
function circcell_downlight(obj) {
	//console.log('circrow_downlight'+obj.getAttribute('id'));
	obj.style.background="#DDDDDD";
}

//Mise en evidence ligne survolee
function circrow_highlight(obj) {
	obj.style.background="#CCC";
}

//Extinction ligne survolee
function circrow_downlight(obj) {
	//console.log('circrow_downlight'+obj.getAttribute('id'));
	obj.style.background="";
}

//Insertion avant la cellule survolee
function circcell_circcell(dragged,targetted) {
	var tab=targetted.parentNode;
	tab.insertBefore(dragged,targetted);
	circcell_downlight(targetted);
	recalc_recept();
}

//Insertion a la fin de la ligne survolee
function circcell_circrow(dragged,targetted) {
	var tab=targetted;
	var lchild=tab.lastChild;
	while(lchild.nodeType!='1') {
		lchild=lchild.previousSibling;
	}
	tab.insertBefore(dragged,lchild);
	circrow_downlight(targetted);
	recalc_recept();
}
