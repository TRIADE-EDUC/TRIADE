// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: move_empr.js,v 1.6 2015-10-15 07:52:32 jpermanne Exp $

down=false;
down_parent=false;
child_move='';
decx=0;
decy=0;
posx=0;
posy=0;

//pheight=0;
//parent_move="";
//parent_min=6;
//parent_last_h=0;
//formatpage="";
inedit=false;
widths = ( typeof widths != 'undefined' && widths instanceof Array ) ? widths : new Array('12.5%','25%','37.5%','50%','62.5%','75%','82.5%','100%');

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


function invisible(child_name) {
	child=document.getElementById(child_name);
	child.style.display="none";
	child.setAttribute("hide","yes");
	if (document.getElementById("popup_onglet")) document.getElementById("popup_onglet").parentNode.removeChild(document.getElementById("popup_onglet"));
	recalc_recept();
}


function visible(child_name) {
	child=document.getElementById(child_name);
	child.style.display="";
	child.setAttribute("hide","");
	recalc_recept();
}


function set_width(field_name,width) {
	var field=document.getElementById(field_name);
	field.style.width=width;	
	if (document.getElementById("popup_onglet")) document.getElementById("popup_onglet").parentNode.removeChild(document.getElementById("popup_onglet"));	
	recalc_recept();
}


function save_all(e) {
	var xml="<formpage relative='yes'>\n";
	var etn=0;
	var empr_grille_categ=document.getElementById('empr_grille_categ');
	var empr_grille_location=document.getElementById('empr_grille_location');
	
	if (document.getElementById("popup_onglet")) document.getElementById("popup_onglet").parentNode.removeChild(document.getElementById("popup_onglet"));
	movables=document.getElementsByTagName("div");
	var i=0;
	for (i=0; i<movables.length; i++) {
		if (movables[i].getAttribute("movable")=="yes") {
			xml+="<movable id='"+movables[i].getAttribute("id")+"' visible='"+(movables[i].style.display=="none"?"no":"yes")+"' parent='"+movables[i].parentNode.getAttribute("id")+"'";
			xml+=" width='"+movables[i].style.width+"' ";
			xml+="/>\n";
		}
	}
	xml+="</formpage>";
	
	var url= "./ajax.php?module=circ&categ=empr&sub=set_empr_grille";
	var req = new http_request();
	var post_params = "&empr_grille_categ="+empr_grille_categ.value+"&empr_grille_location=";
	if (empr_grille_location) post_params+=empr_grille_location.value;
	post_params+="&empr_grille_format="+encodeURIComponent(xml);
	if(req.request(url,1,post_params)){
		alert(msg_move_saved_error+" "+requete["sauve_notice"].responseText);
	} else {
		var resp = req.get_text();
		alert(msg_move_saved_ok);
	}
}


function move_fields(domXML) {
	root=domXML.getElementsByTagName("formpage");
	var id=0;
	var movables=domXML.getElementsByTagName("movable");
	
	for (i=0; i<movables.length; i++) {
		id=movables[i].getAttribute("id");
		var parent_id=movables[i].getAttribute("parent");
		var mov=document.getElementById(id);
		if (mov != null) {
			var parent=document.getElementById(parent_id);
			var lchild=parent.lastChild;
			while(lchild.nodeType!='1') {
				lchild=lchild.previousSibling;
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


function get_pos(def) {
	
	var post_params='';
	if (def) {
		var empr_grille_categ=document.getElementById('form_categ');
		var empr_grille_location=document.getElementById('empr_location_id');
		empr_grille_categ.setAttribute('onchange','get_pos();');
		empr_grille_location.setAttribute('onchange','get_pos();');
		var url= "./ajax.php?module=circ&categ=empr&sub=get_default_empr_grille";
	} else {
		if(inedit) {
			var empr_grille_categ=document.getElementById('empr_grille_categ');
			empr_grille_categ.setAttribute('onchange','get_pos();');
			var empr_grille_location=document.getElementById('empr_grille_location');
			empr_grille_location.setAttribute('onchange','get_pos();');
			post_params = "&empr_grille_categ="+empr_grille_categ.value+"&empr_grille_location="+empr_grille_location.value;
			var url= "./ajax.php?module=circ&categ=empr&sub=get_empr_grille";
		} else {
			var empr_grille_categ=document.getElementById('form_categ');
			var empr_grille_location=document.getElementById('empr_location_id');
			empr_grille_categ.setAttribute('onchange','get_pos();');
			empr_grille_location.setAttribute('onchange','get_pos();');
			post_params = "&empr_grille_categ="+empr_grille_categ.value+"&empr_grille_location="+empr_grille_location.value;
			var url= "./ajax.php?module=circ&categ=empr&sub=get_empr_grille";
		}
	}
	
	var req = new http_request();
	if(req.request(url,1,post_params)){
	} else {
		var xml = req.get_xml();
		move_fields(xml);
	}
}


function move_parse_dom() {

	inedit=true;
	var i=0;
	//Rendre visible les listes des categories et localisations et desactiver les selecteurs du formulaire
	var sc=document.getElementById('form_categ');
	//sc.disabled=true;
	var sgc=document.getElementById('empr_grille_categ');
	if (sgc!=null) {
		sgc.onchange=function(e) {
			get_pos();
		}
		sgc.style.display="block";
		sgc.value=sc.value;
	}
	var sl=document.getElementById('empr_location_id');
	//sl.disabled=true;
	var sgl=document.getElementById('empr_grille_location');
	if (sgl!=null) {
		sgl.onchange=function(e) {
			get_pos();
		}
		sgl.style.display="block";
		sgl.value=sl.value;
	}
	
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


document.onclick=function(e) {
	if (e) {
		if (e.target.nodeType==1)
			if  ((e.target.parentNode.getAttribute("id")!="popup_onglet")&&(e.target.getAttribute("id")!="popup_onglet"))
				if (document.getElementById("popup_onglet")) document.getElementById("popup_onglet").parentNode.removeChild(document.getElementById("popup_onglet"));
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
