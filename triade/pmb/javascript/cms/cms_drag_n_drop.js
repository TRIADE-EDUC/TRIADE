// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_drag_n_drop.js,v 1.9 2016/06/18 12:06:28 Alexandre Exp $


/*
 * Utilisation :
 *
 * Definition des elements pouvant être deplaces :
 *
 * Attribut draggable="yes" (obligatoire)
 * Attribut dragtype="$TXT" (obligatoire= Type d'element a deplacer)
 * Attribut callback_before="$FCT" (Nom de la fonction appelee sur clic avant deplacement)
 * Attribut callback_after="$FCT" (Nom de la fonction appelee sur relache apres deplaçement)
 * Attribut dragflash="yes"  (Affichage d'un symbole au survol d'un element deplaçable)
 * Attribut dragicon="$IMG" (Image affichee lors du deplacement)
 * Attribut dragtext="$TEXT" (Texte affiche lors du deplacement)
 * Attribut draghand="$ID" (ID de la poignee utilisee pour deplacer l'element)
 *
 * Definition des elements recepteurs :
 *
 * Attribut recept="yes" (obligatoire)
 * Attribut recepttype="$TXT" (obligatoire= Type d'element recepteur)
 * Attribut highlight=$FCT" (Nom de la fonction appelee au survol du recepteur)
 * Attribut downlight=$FCT" (Nom de la fonction apres au survol du recepteur)
 *
 * Appeller la fonction "init_drag()" pour rechercher tous les elements deplaçables de la page
 *
 * La fonction "dragtype_recepttype(dragged,target)" est appelee pour associer l'element deplace et l'element cible (si elle existe)
 *
 *
 */

 //TODO = a modifier pour prendre en compte la possibilite d'avoir un recepteur acceptant +sieurs types d elements deplaçables


var	draggable=new Array(); 	//Elements deplaçables
var recept=new Array();		//Elements recepteurs
var handler=new Array();	//Poignees
var is_down=false;
var dragup=true;
var posxdown=0;
var posydown=0;
var current_drag=null;
var dragged=null;
var shifton=false;

var allow_drag= new Array();

allow_drag['opacdrop']=new Array();
allow_drag['opacdrop']['opacdrop']=true;

var r_x=new Array();
var r_y=new Array();
var r_width=new Array();
var r_height=new Array();
var r_highlight="";

var d_x=new Array();
var d_y=new Array();
var d_width=new Array();
var d_height=new Array();
var d_highlight="";

var drag_icon="./images/drag_symbol.png";
var drag_empty_icon="./images/drag_symbol_empty.png";

//Trouve la position absolue d'un objet dans la page
function cms_findPos(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		curleft = obj.offsetLeft
		curtop = obj.offsetTop
		while (obj = obj.offsetParent) {
				curleft += obj.offsetLeft;
				curtop += obj.offsetTop;
		}
	}
	return [curleft,curtop];
}

//Recupere les coordonnees du click souris
function cms_getCoordinate(e) {
	var posx = 0;
	var posy = 0;
	if (!e) var e = window.event;
	if (e.pageX || e.pageY) {
		posx = e.pageX;
		posy = e.pageY;
	}
	else if (e.clientX || e.clientY) 	{
		posx = e.clientX + parent.frames['opac_frame'].document.body.scrollLeft
			+ parent.frames['opac_frame'].document.documentElement.scrollLeft;
		posy = e.clientY + parent.frames['opac_frame'].document.body.scrollTop
			+ parent.frames['opac_frame'].document.documentElement.scrollTop;
	}
	return [posx,posy];
}

//Handler : Click sur un element draggable
function cms_mouse_down_draggable(e) {
	//On annule tous les comportements par defaut du navigateur (ex : selection de texte)
	if (!e) var e=window.event;
	if (e.stopPropagation) {
		e.preventDefault();
		e.stopPropagation();
	} else {
		e.cancelBubble=true;
		e.returnValue=false;
	}

	//Recuperation de l'element d'origine qui a reçu l'evenement
	if (e.target) var targ=e.target; else var targ=e.srcElement;

	//On nettoie tout drag en cours
	posxdown=0;
	posydown=0;
	is_down=false;
	if (current_drag) current_drag.parentNode.removeChild(current_drag);
	current_drag=null;
	dragged=null;

	//Recherche du premier parent qui a draggable comme attribut
	while ((targ.getAttribute("draggable")!="yes")&&(targ.nodeName!="HTML")) {
		targ=targ.parentNode;
	}
	//On stocke l'element d'origine
	dragged=targ;
	cms_show_css_obj(targ.getAttribute("id"));
	//Stockage des coordonnees d'origine du click
	var pos=getCoordinate(e);
	posxdown=pos[0];
	posydown=pos[1];

	pos_init_absolute=cms_findPos(targ);
	//Il y a un element en cours de drag !
	is_down=true;

	var to_create = true;
	//Appel de la fonction callback before si elle existe
	if (targ.getAttribute("callback_before")) {
		 to_create = eval(targ.getAttribute("callback_before")+"(targ,e)");
	}

	//Creation du clone qui bougera
	if(to_create)
		cms_create_dragged(targ);
	else{
		//On nettoie tout drag en cours
		posxdown=0;
		posydown=0;
		is_down=false;
		if (current_drag) current_drag.parentNode.removeChild(current_drag);
		current_drag=null;
		dragged=null;
	}
}

//Evenement : passage au dessus d'un element draggable : on affiche un
// petit symbole pour signifier qu'il est draggable
function cms_mouse_over_draggable(e) {
	if (!e) var e=window.event;
	if (e.target) var targ=e.target; else var targ=e.srcElement;

	//Recherche du premier parent qui a draggable
	while ((targ.getAttribute("draggable")!="yes")&&(targ.nodeName!="HTML")) {
			targ=targ.parentNode;
	}
	//On met un petit symbole "drap"
	//Recherche de la position
	var pos=cms_findPos(targ);
	//Creation d'un <div><image/></div> au dessus de l'element
	var drag_symbol=parent.frames['opac_frame'].document.createElement("div");
	drag_symbol.setAttribute("id","drag_symbol_"+targ.getAttribute("id"));
	drag_symbol.style.position="absolute";
	drag_symbol.style.left=pos[0]+"px";
	drag_symbol.style.top=pos[1]+"px";
	drag_symbol.style.zIndex=1000;
	if (targ.getAttribute("dragflash")=="yes") {
		img_symbol=parent.frames['opac_frame'].document.createElement("img");
		img_symbol.setAttribute("src",drag_icon);
		drag_symbol.appendChild(img_symbol);
	}
	//Affichage a partir de l'ancre
	parent.frames['opac_frame'].document.getElementById("att").appendChild(drag_symbol);
}

//Evenement : on sort du survol d'un element "draggable"
function cms_mouse_out_draggable(e) {
	if (!e) var e=window.event;
	if (e.target) var targ=e.target; else var targ=e.srcElement;

	//Recherche du premier parent qui a draggable
	while ((targ.getAttribute("draggable")!="yes")&&(targ.nodeName!="HTML")) {
		targ=targ.parentNode;
	}
	//Suppression du petit symbole
	drag_symbol=parent.frames['opac_frame'].document.getElementById("drag_symbol_"+targ.getAttribute("id"));
	if (drag_symbol) drag_symbol.parentNode.removeChild(drag_symbol);
}

//Quand on relache le clone, y-a-t-il un element recepteur en dessous ? Si oui, on retourne l'id
function cms_is_on() {

	var i;
	if (current_drag!=null) {
		var current_drag_id=current_drag.getAttribute('id');


		var scrollbar_pos=0;
		var dragged_id=current_drag_id.substring(0, current_drag_id.length-5);
		var pos=cms_findPos(current_drag);

		for (i=0; i<recept.length; i++) {
			if( (allow_drag[parent.frames['opac_frame'].document.getElementById(recept[i]).getAttribute('recepttype')]['all']==true) ||
				(allow_drag[parent.frames['opac_frame'].document.getElementById(recept[i]).getAttribute('recepttype')][parent.frames['opac_frame'].document.getElementById(dragged_id).getAttribute('dragtype')]==true)  ) {

				if ( ((pos[0]>r_x[i])&&(pos[0]<parseFloat(r_x[i])+parseFloat(r_width[i]))) &&
					((pos[1]>r_y[i])&&(pos[1]<parseFloat(r_y[i])+parseFloat(r_height[i]))) )
					return recept[i];

			}
		}
	}
	return false;
}

//Si la souris est au dessus du document et qu'on est en cours de drag, on annule tous les
// comportements par defaut du navigateur
function cms_mouse_over(e) {
	if (!e) var e=window.event;
	if (is_down) {
		if (e.stopPropagation) {
			e.preventDefault();
			e.stopPropagation();
		} else {
			e.cancelBubble = true;
			e.returnValue=false;
		}
	}
}

//On relache le bouton en cours de drag
function cms_up_dragged(e) {
	if (!e) var e=window.event;
	//Si il y a un clone en cours de mouvement, on le supprime, on remet tout a zero et on
	// appelle la fonction qui gere le drag si elle existe et qu'il y a un recepteur en dessous
	if (current_drag!=null) {
		//Y-a-t-il un recepteur en dessous du lâche ?
		try{
			target=cms_is_on();
			if (target&&e.shiftKey) {
				if (parent.frames['opac_frame'].document.getElementById(r_highlight).getAttribute('downlight')) {
					eval(parent.frames['opac_frame'].document.getElementById(r_highlight).getAttribute('downlight')+"(parent.frames['opac_frame'].document.getElementById(r_highlight))");
					r_highlight="";
				}
			}
		} catch(e){
			target=null;
		}

		//Remise a zero
		var pos=getCoordinate(e);
		var coords_orig=cms_findPos(dragged);
		var encx=posxdown-coords_orig[0];
		var ency=posydown-coords_orig[1];


		var xdrop=pos[0]-encx;
		var ydrop=pos[1]-ency;
		posxdown=0;
		posydown=0;
		is_down=false;
		if (current_drag) current_drag.parentNode.removeChild(current_drag);
		current_drag=null;
		//Si il y a un recepteur : callback de la fonction d'association si elle existe
		if (target && !e.shiftKey) {

			if (eval("typeof "+dragged.getAttribute("dragtype")+"_"+parent.frames['opac_frame'].document.getElementById(target).getAttribute("recepttype")+"=='function'")) {
				eval(dragged.getAttribute("dragtype")+"_"+parent.frames['opac_frame'].document.getElementById(target).getAttribute("recepttype")+"(dragged,parent.frames['opac_frame'].document.getElementById(target),xdrop,ydrop,coords_orig[0],coords_orig[1])");
			}
		} else {
			if (eval("typeof "+dragged.getAttribute("dragtype")+"_moved=='function'")) {
				eval(dragged.getAttribute("dragtype")+"_moved(dragged,xdrop,ydrop,coords_orig[0],coords_orig[1])");
			}
		}
		//Appel de la fonction callback_after si elle existe
		if (dragged && dragged.getAttribute("callback_after")) {
			eval(dragged.getAttribute("callback_after")+"(dragged,e,'"+target+"')");
		}
		cms_show_css_obj(dragged.getAttribute("id"));
		//On nettoie la reference a l'element d'origine
		dragged=null;
	}
}

function show_cadre_depos(cadre,x,y){
	var childs=cadre.childNodes;
	var flag_found=false;

	var cadre_depos=parent.frames['opac_frame'].document.getElementById("cadre_depos");
	for (var i=0; i<childs.length; i++) {

		var child_block=childs[i];
		if((child_block.offsetWidth!=0)&&(child_block.offsetHeight!=0)) {
			left_coords=cms_findPos(child_block);
			//On a trouvé !
			if (((x>=left_coords[0])&&(x<=left_coords[0]+child_block.offsetWidth))&&((y>=left_coords[1])&&(y<=left_coords[1]+child_block.offsetHeight))) {
				cadre_depos.style.left=left_coords[0]+"px";
				cadre_depos.style.top=left_coords[1]+"px";
				cadre_depos.style.zIndex=2000;
				cadre_depos.style.width=100+"px";
				cadre_depos.style.visibility="visible";
				flag_found=true;
				break;
			}
		}
	}
	if (!flag_found) {


	}
}

//Evenement : Deplacement du clone (draggage)
function cms_move_dragged(e) {
	if (!e) var e=window.event;
	//Si il y a un drag en cours
	if (is_down) {
		//On annule tous les comportements par defaut du navigateur
		if (e.stopPropagation) {
			e.preventDefault();
			e.stopPropagation();
		} else {
			e.cancelBubble = true;
			e.returnValue=false;
		}
		//Deplacement
		var pos=getCoordinate(e);

		//Positionnement du clone à l'endroit de son original
		var coords_orig=cms_findPos(dragged);
		var encx=posxdown-coords_orig[0];
		var ency=posydown-coords_orig[1];
		current_drag.style.left=(pos[0]-encx)+"px";
		current_drag.style.top=(pos[1]-ency)+"px";

		try{
			var r=cms_is_on();
		} catch(e){
			var r=null;
		}
		if (r) {
			if ((r_highlight)&&(r_highlight!=r)) {
				if (parent.frames['opac_frame'].document.getElementById(r_highlight).getAttribute('downlight'))
					eval(parent.frames['opac_frame'].document.getElementById(r_highlight).getAttribute('downlight')+"(parent.frames['opac_frame'].document.getElementById(r_highlight))");
			}
			if (parent.frames['opac_frame'].document.getElementById(r).getAttribute('highlight'))
					eval(parent.frames['opac_frame'].document.getElementById(r).getAttribute('highlight')+"(parent.frames['opac_frame'].document.getElementById(r))");
			r_highlight=r;
			show_cadre_depos(parent.frames['opac_frame'].document.getElementById(r),(pos[0]-encx),(pos[1]-ency));


		} else if ((r_highlight)&&parent.frames['opac_frame'].document.getElementById(r_highlight)) {
			if (parent.frames['opac_frame'].document.getElementById(r_highlight).getAttribute('downlight')) {
					eval(parent.frames['opac_frame'].document.getElementById(r_highlight).getAttribute('downlight')+"(parent.frames['opac_frame'].document.getElementById(r_highlight))");
					r_highlight="";
			}
			parent.frames['opac_frame'].document.getElementById("cadre_depos").style.visibility="hidden";
		}
		cms_show_css_obj(current_drag.getAttribute("id"));
	}
}



/*function cms_key_down(e) {
	if (e.)
}*/

//Creation du clone
function cms_create_dragged(targ) {
	//Recherche de la position d'origine
	initpos=cms_findPos(targ);

	//Creation du clone si necessaire
	if (current_drag==null) {
		dragtext=targ.getAttribute("dragtext");
		dragicon=targ.getAttribute("dragicon");
		if (dragtext||dragicon) {
			clone=parent.frames['opac_frame'].document.createElement("span");
			clone.className="dragtext";
			if (dragicon) {
				var icon=parent.frames['opac_frame'].document.createElement("img");
				icon.src=dragicon;
				clone.appendChild(icon);
			}
			if (dragtext) {
				clone.appendChild(parent.frames['opac_frame'].document.createTextNode(dragtext));
			}
		} else {

			if (targ.nodeName=='TR') {	//Et c'est encore IE qui fait des siennes !!!
				fclone=targ.cloneNode(true);
				t=parent.frames['opac_frame'].document.createElement('TABLE');
				b=parent.frames['opac_frame'].document.createElement('TBODY');
				b.appendChild(fclone);
				t.appendChild(b);
				clone=t;
			} else {
				clone=targ.cloneNode(true);
				clone_style=getComputedStyle(clone);
				if ((clone_style.position=='relative')||(clone_style.position=='absolute')) clone.style.position='static';
			}
		}
		current_drag=parent.frames['opac_frame'].document.createElement("div");
		current_drag.setAttribute("id",targ.getAttribute("id")+"drag_");
		current_drag.setAttribute('handler',targ.getAttribute("handler"));
		current_drag.className="dragged";
		current_drag.appendChild(clone).classList.remove("cms_drag");
		current_drag.style.position="absolute";
		current_drag.style.visibility="hidden";
		current_drag=parent.frames['opac_frame'].document.getElementById("att").appendChild(current_drag);
		/*var encx=current_drag.offsetWidth;
		var ency=current_drag.offsetHeight;*/
		var coords_orig=cms_findPos(targ);
		current_drag.className='cms_drag';
		current_drag.style.left=coords_orig[0]+"px";
		current_drag.style.top=coords_orig[1]+"px";
		current_drag.style.zIndex=2000;
		current_drag.style.visibility="visible";
		current_drag.style.cursor="move";
	//	current_drag.style.border="3px dashed red";
	//	current_drag.style.outline="2px solid red";

		if(!parent.frames['opac_frame'].document.getElementById("cadre_depos")){
			cadre_depos=parent.frames['opac_frame'].document.createElement("div");
			cadre_depos.setAttribute("id","cadre_depos");
			cadre_depos.style.position="absolute";
			cadre_depos.style.width="100%";
			cadre_depos.style.height="10px";
			cadre_depos.style.visibility="hidden";
			cadre_depos.style.backgroundColor="#000088";
			cadre_depos=parent.frames['opac_frame'].document.getElementById("att").appendChild(cadre_depos);
		}
	}
}

//Parcours de l'arbre HTML pour trouver les elements qui ont les attributs draggable ou recept
function cms_parse_drag(n) {
	var i;
	var c;
	var l;
	var idh;
	var tmp;

	//Pour le noeud passe, si c'est un noeud de type element (1), alors on regarde ses attributs
	if(n.nodeType==1){
		//C'est un recepteur
		if (n.getAttribute("recept")=="yes") {
			if(n.getAttribute("id")!="iframe_resume_panier"){
				l=recept.length;
				recept[l]=n.getAttribute("id");
				cms_calc_recept(l);
			}

		}
		//C'est un element deplaçable
		if (n.getAttribute("draggable")=="yes") {

			draggable[draggable.length]=n.getAttribute("id");

			//Avec une poignee
			if (n.getAttribute("handler")) {
				idh=n.getAttribute("handler");
				tmp=parent.frames['opac_frame'].document.getElementById(idh);
				alert(idh)
				handler[handler.length]=idh;
			} else {
				tmp=n;
			}
			//Implementation des gestionnaires d'evenement pour les elements deplaçables
			tmp.onmousedown=function(e) {
				cms_mouse_down_draggable(e);
			}
			tmp.onmouseover=function(e) {
				cms_mouse_over_draggable(e);
			}
			tmp.onmouseout=function(e) {
				cms_mouse_out_draggable(e);
			}
		} else if (n.getAttribute("draggable")=="no"){
			tmp=n;
			tmp.onmousedown=function(e) {}
			tmp.onmouseover=function(e) {}
			tmp.onmouseout=function(e) {}
		}
	}
	//Si il a des enfants, on parse ses enfants !
	if (n.hasChildNodes()) {
		for (i=0; i<n.childNodes.length; i++) {
			c=n.childNodes[i];
			cms_parse_drag(c);
		}
	}
}

//Recherche des recepteurs uniquement, a partir du noeud specifie
function cms_parse_drag_recept(n) {
	var i;
	var l;
	var c;
	//Pour le noeud passe, si c'est un noeud de type element (1), alors on regarde ses attributs
	if(n.nodeType==1){
		//C'est un recepteur
		if (n.getAttribute("recept")=="yes") {
			l=recept.length;
			recept[l]=n.getAttribute("id");
			cms_calc_recept(l);
		}
	}
	//Si il a des enfants, on parse ses enfants !
	if (n.hasChildNodes()) {
		for (i=0; i<n.childNodes.length; i++) {
			c=n.childNodes[i];
			cms_parse_drag_recept(c);
		}
	}
}

//Reinitialisation et recherche de tous les recepteurs
function cms_init_recept() {

	//Reinitialisation des tableaux recepteurs
	r_x=new Array();
	r_y=new Array();
	r_width=new Array();
	r_height=new Array();
	r_highlight='';
	recept=new Array();

	//Recherche de tous les recepteurs
	cms_parse_drag_recept(parent.frames['opac_frame'].document.body);

}

//Initialisation des fonctionnalites (a appeler a la fin du chargement de la page)
function cms_init_drag() {

	//Reinitialisation des tableaux et variables
	draggable=new Array(); 	//Elements deplaçables
	recept=new Array();		//Elements recepteurs
	handler=new Array();	//Poignees
	is_down=false;
	dragup=true;
	posxdown=0;
	posydown=0;
	current_drag=null;
	dragged=null;
	shifton=false;

	r_x=new Array();
	r_y=new Array();
	r_width=new Array();
	r_height=new Array();
	r_highlight="";

	d_x=new Array();
	d_y=new Array();
	d_width=new Array();
	d_height=new Array();
	d_highlight="";

	//Recherche de tous les elements deplaçables et des recepteurs
	cms_parse_drag(parent.frames['opac_frame'].document.body);
	// Si pas de draggable ou de recept
	if(!draggable.length || !recept.length){
		parent.frames['opac_frame'].document.onmousemove=function (e) {	}
		parent.frames['opac_frame'].document.onmouseup=function (e) {	}
		parent.frames['opac_frame'].document.onmouseover=function (e) {	}
	} else {
		//On surveille tout ce qui se passe dans le document au niveau de la souris (sauf click down !)
		parent.frames['opac_frame'].document.onmousemove=function (e) {
			cms_move_dragged(e);
		}
		parent.frames['opac_frame'].document.onmouseup=function (e) {
			cms_up_dragged(e);
		}
		parent.frames['opac_frame'].document.onmouseover=function (e) {
			cms_mouse_over(e);
		}

		//On capte les évênements clavier
		/*document.onkeydown=function (e) {
			cms_key_down(e);
		}
		document.onkeyup=function (e) {
			cms_key_up(e);
		}*/
	}
	cms_recalc_draggable();
}

//Calcul de l'encombrement de tous les recepteurs
function cms_recalc_recept() {

	for(var i=0;i<recept.length;i++) {
		cms_calc_recept(i);
	}
	cms_recalc_draggable();
}

//Calcul de l'encombrement d'un recepteur
function cms_calc_recept(i) {
	try {
		var r=parent.frames['opac_frame'].document.getElementById(recept[i]);
		var pos=cms_findPos(r);
		r_x[i]=pos[0];
		r_y[i]=pos[1];
		r_width[i]=r.offsetWidth;
		r_height[i]=r.offsetHeight;
		r_highlight="";
	} catch(err) {
		recept.splice(i,1);
		r_x.splice(i,1);
		r_y.splice(i,1);
		r_width.splice(i,1);;
		r_height.splice(i,1);
	}
}

function cms_recalc_draggable() {

	for(var i=0;i<draggable.length;i++) {
		cms_calc_draggable(i);
	}
}
//Calcul de l'encombrement d'un draggable
function cms_calc_draggable(i) {
	try {
		var r=parent.frames['opac_frame'].document.getElementById(draggable[i]);
		var pos=cms_findPos(r);
		d_x[i]=pos[0];
		d_y[i]=pos[1];
		d_width[i]=r.offsetWidth;
		d_height[i]=r.offsetHeight;
		d_highlight="";
	} catch(err) {
		draggable.splice(i,1);
		d_x.splice(i,1);
		d_y.splice(i,1);
		d_width.splice(i,1);;
		d_height.splice(i,1);
	}
}