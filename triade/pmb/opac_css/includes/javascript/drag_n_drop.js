// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: drag_n_drop.js,v 1.6 2020/12/22 16:55:43 btafforeau Exp $

draggable=new Array();
recept=new Array();
//state="";
is_down=false;
dragup=true;
posxdown=0;
posydown=0;
current_drag=null;
dragged=null;
r_x=new Array();
r_y=new Array();
r_width=new Array();
r_height=new Array();

var r_highlight = "";
var drag_icon = "./images/drag_symbol.png";
var drag_empty_icon = "./images/drag_symbol_empty.png";
var handler = new Array();

//Trouve la position absolue d'un objet dans la page
function findPos(obj) {
	var curleft = curtop = 0
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

//R�cup�re les coordonn�es du click souris
function getCoordinate(e) {
	var posx = 0;
	var posy = 0;
	if (!e) var e = window.event;
	if (e.pageX || e.pageY) 	{
		posx = e.pageX;
		posy = e.pageY;
	}
	else if (e.clientX || e.clientY) 	{
		posx = e.clientX + document.body.scrollLeft
			+ document.documentElement.scrollLeft;
		posy = e.clientY + document.body.scrollTop
			+ document.documentElement.scrollTop;
	}
	return [posx,posy];
}

//Handler : Click sur un �l�ment draggable
function mouse_down_draggable(e) {
	//On annule tous les comportements par d�faut du navigateur (ex : s�lection de texte)
	if (!e) var e=window.event;
	if (e.stopPropagation) {
		e.preventDefault();
		e.stopPropagation();
	} else { 
		e.cancelBubble=true;
		e.returnValue=false;
	}
	//R�cup�ration de l'�l�ment d'origine qui a re�u l'�v�nement
	if (e.target) var targ=e.target; else var targ=e.srcElement;
	
	//On nettoie tout drag en cours
	posxdown=0;
	posydown=0;
	is_down=false;
	if (current_drag) current_drag.parentNode.removeChild(current_drag);
	current_drag=null;
	dragged=null;
	
	//Recherche du premier parent qui a draggable comme attribut
	while (targ.getAttribute("draggable")!="yes") {
		targ=targ.parentNode;
	}
	//On stocke l'�l�ment d'origine
	dragged=targ;
	//Stockage des coordonn�es d'origine du click
	var pos=getCoordinate(e);
	posxdown=pos[0];
	posydown=pos[1];
	//Il y a un �l�ment en cours de drag !
	is_down=true;
	//Appel de la fonction callback before si elle existe
	if (targ.getAttribute("callback_before")) {
		eval(targ.getAttribute("callback_before")+"(targ,e)");
	}
	//Cr�ation du clone qui bougera
	create_dragged(targ);
}

//Ev�nement : passage au dessus d'un �l�ment draggable : on affiche un 
// petit symbole pour signifier qu'il est draggable
function mouse_over_draggable(e) {
	if (!e) var e=window.event;
	if (e.target) var targ=e.target; else var targ=e.srcElement;
	
	//Recherche du premier parent qui a draggable
	while (targ.getAttribute("draggable")!="yes") {
		targ=targ.parentNode;
	}
		
	//On met un petit symbole "drap"
	//Recherche de la position
	var pos=findPos(targ);
	var posPere=findPos(targ.parentNode);
	//Cr�ation d'un <div><image/></div> au dessu de l'�lement
	var drag_symbol=document.createElement("div");
	drag_symbol.setAttribute("id","drag_symbol_"+targ.getAttribute("id"));
	drag_symbol.style.position="absolute";	
	drag_symbol.style.top=pos[1]+"px";
	drag_symbol.style.zIndex=1000;
	img_symbol=document.createElement("img");
	img_symbol.setAttribute("src","images/drag_symbol.png");
	drag_symbol.appendChild(img_symbol);
			
	var decalage=pos[0]-posPere[0];
	if((targ.offsetWidth+img_symbol.width+decalage)<targ.parentNode.offsetWidth)
		drag_symbol.style.left=(pos[0]+targ.offsetWidth)+"px";
	else { 
		drag_symbol.style.left= (pos[0]+(targ.parentNode.offsetWidth-img_symbol.width-decalage))+"px";
	}
	//Affichage � partir de l'ancre
	document.getElementById("att").appendChild(drag_symbol);
	
}

//Ev�nement : on sort du survol d'un �l�ment "draggable"
function mouse_out_draggable(e) {
	if (!e) var e=window.event;
	if (e.target) var targ=e.target; else var targ=e.srcElement;
	
	//Recherche du premier parent qui a draggable
	while (targ.getAttribute("draggable")!="yes") {
		targ=targ.parentNode;
	}
	//Suppression du petit symnbole
	drag_symbol=document.getElementById("drag_symbol_"+targ.getAttribute("id"));
	if (drag_symbol) drag_symbol.parentNode.removeChild(drag_symbol);
}

//Quand on relache le clone, y-a-t-il un �l�ment recepteur en dessous ? Si oui, on retourne l'id
function is_on() {
	var i;
	if (current_drag!=null) {
		var pos=findPos(current_drag);
		for (i=0; i<recept.length; i++) {
			var isx=0;
			var isy=0;
			if ((pos[0]>r_x[i])&&(pos[0]<parseFloat(r_x[i])+parseFloat(r_width[i]))) isx++; 
			if (((parseFloat(pos[0])+parseFloat(current_drag.offsetWidth))>r_x[i])&&((parseFloat(pos[0])+parseFloat(current_drag.offsetWidth))<parseFloat(r_x[i])+parseFloat(r_width[i]))) isx++;
			if ((pos[1]>r_y[i])&&(pos[1]<parseFloat(r_y[i])+parseFloat(r_height[i]))) isy++;
			if (((parseFloat(pos[1])+parseFloat(current_drag.offsetHeight))>r_y[i])&&((parseFloat(pos[1])+parseFloat(current_drag.offsetHeight))<parseFloat(r_y[i])+parseFloat(r_height[i]))) isy++;
			if (parseInt(isx)*parseInt(isy)) return recept[i];			 
		}
	}
	return false;
}

//Si la souris est au dessus du document et qu'on est en cours de drag, on annule tous les 
// comportements par d�faut du navigateur
function mouse_over(e) {
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
function up_dragged(e) {
	//Si il y a un clone en cours de mouvement, on le supprime, on remet tout � z�ro et on 
	// appelle la fonction qui g�re le drag si elle existe et qu'il y a un r�cepteur en dessous 
	if (current_drag!=null) {
		//Y-a-t-il un r�cepteur en dessous du l�ch� ?
		target=is_on();
		//Appel de la fonction callback_after si elle existe
		if (dragged.getAttribute("callback_after")) {
			eval(dragged.getAttribute("callback_after")+"(dragged,e,'"+target+"')");
		}
		//Remise � zero
		posxdown=0;
		posydown=0;
		is_down=false;
		if (current_drag) current_drag.parentNode.removeChild(current_drag);
		current_drag=null;
		//Si il y a un r�cepteur : callback de la fonction d'association si elle existe 
		if (target) {
			if (eval("typeof "+dragged.getAttribute("dragtype")+"_"+document.getElementById(target).getAttribute("recepttype")+"=='function'")) {
				eval(dragged.getAttribute("dragtype")+"_"+document.getElementById(target).getAttribute("recepttype")+"(dragged,document.getElementById(target))");
			}
		}
		//On nettoie la r�f�rence � l'�l�ment d'origine
		dragged=null;
	}
}

//Evenement : Deplacement du clone (draggage)
function move_dragged(e) {
	if (!e) {
		var e = window.event;
	}
	//Si il y a un drag en cours 
	if (is_down) {
		//On annule tous les comportements par defaut du navigateur
		if (e.stopPropagation) {
			e.preventDefault();
			e.stopPropagation();
		} else {
			e.cancelBubble = true;
			e.returnValue = false;
		}
		//Deplacement
		var pos = getCoordinate(e);

		//Positionnement du clone pour que le pointeur de la souris soit au milieu !
		// On gere le cas de la presence d'un handler
		var current_drag_handler = document.getElementById(current_drag.getAttribute("handler"));
		if (current_drag_handler) {
			var encx = current_drag_handler.offsetWidth;
			var ency = current_drag_handler.offsetHeight;
		} else {
			var encx = current_drag.offsetWidth;
			var ency = current_drag.offsetHeight;
		}
		current_drag.style.left = (pos[0]-(encx/2)) + "px";
		current_drag.style.top = (pos[1]-(ency/2)) + "px";

		try {
			var r = is_on();
		} catch(e) {
			var r = null;
		}
		
		if (r) {
			if ((r_highlight) && (r_highlight != r)) {
				if (document.getElementById(r_highlight).getAttribute('downlight')) {
					eval(document.getElementById(r_highlight).getAttribute('downlight') + "(document.getElementById(r_highlight))");
				}
			}
			if (document.getElementById(r).getAttribute('highlight')) {
				eval(document.getElementById(r).getAttribute('highlight') + "(document.getElementById(r))");
			}
			r_highlight = r;
		} else if (r_highlight && document.getElementById(r_highlight)) {
			if (document.getElementById(r_highlight).getAttribute('downlight')) {
				eval(document.getElementById(r_highlight).getAttribute('downlight') + "(document.getElementById(r_highlight))");
				r_highlight = "";
			}
		}
	}
}

//Creation du clone
function create_dragged(targ) {
	//Recherche de la position d'origine
	initpos = findPos(targ);
	
	//Creation du clone si necessaire
	if (current_drag == null) {
		dragtext = targ.getAttribute("dragtext");
		dragicon = targ.getAttribute("dragicon");
		if (dragtext || dragicon) {
			clone = document.createElement("span");
			clone.className = "dragtext";
			if (dragicon) {
				var icon = document.createElement("img");
				icon.src = dragicon;
				clone.appendChild(icon);
			}
			if (dragtext) {
				clone.appendChild(document.createTextNode(dragtext));
			}
		} else {
			if (targ.nodeName == 'TR') {	//Et c'est encore IE qui fait des siennes !!!
				fclone = targ.cloneNode(true);
				t = document.createElement('TABLE');
				b = document.createElement('TBODY');
				b.appendChild(fclone);
				t.appendChild(b);
				clone = t;
			} else {
				clone = targ.cloneNode(true);
			}
		}
		current_drag=document.createElement("div");
		current_drag.setAttribute("id",targ.getAttribute("id")+"drag_");
		current_drag.setAttribute('handler',targ.getAttribute("handler"));
		current_drag.className="dragged";
		current_drag.appendChild(clone);
		current_drag.style.position="absolute";
		current_drag.style.visibility="hidden";
		current_drag.style.width=targ.offsetWidth;
		current_drag=document.getElementById("att").appendChild(current_drag);

		// On gere le cas de la presence d'un handler
		var current_drag_handler = document.getElementById(current_drag.getAttribute("handler"));
		if (current_drag_handler) {
			var encx = current_drag_handler.offsetWidth;
			var ency = current_drag_handler.offsetHeight;
		} else {
			var encx = current_drag.offsetWidth;
			var ency = current_drag.offsetHeight;
		}
		current_drag.style.left = (posxdown-(encx/2)) + "px";
		current_drag.style.top = (posydown-(ency/2)) + "px";
		current_drag.style.zIndex = 2000;
		current_drag.style.visibility = "visible";
		current_drag.style.cursor = "move";
	}
}


//Parcours de l'arbre HTML pour trouver les elements qui ont les attributs draggable ou recept
function parse_drag(n) {
	var i;
	var c;
	var l;
	var idh;
	var tmp;
	
	//Pour le noeud passe, si c'est un noeud de type element (1), alors on regarde ses attributs
	if(n.nodeType==1){
		//C'est un recepteur
		if (n.getAttribute("recept")=="yes") {
			
			l=recept.length;
			recept[l]=n.getAttribute("id");
			calc_recept(l);
		} 
		//C'est un element depla�able
		if (n.getAttribute("draggable")=="yes") {

			draggable[draggable.length]=n.getAttribute("id");
			
			//Avec une poignee
			if (n.getAttribute("handler")) {
				idh=n.getAttribute("handler");
				tmp=document.getElementById(idh);
				handler[handler.length]=idh;
			} else {
				tmp=n;
			}
			//Implementation des gestionnaires d'evenement pour les elements depla�ables
			tmp.onmousedown=function(e) {
				mouse_down_draggable(e);
			}
			tmp.onmouseover=function(e) {
				mouse_over_draggable(e);
			}
			tmp.onmouseout=function(e) {
				mouse_out_draggable(e);
			}
		}
	}
	//Si il a des enfants, on parse ses enfants !
	if (n.hasChildNodes()) {
		for (i=0; i<n.childNodes.length; i++) {
			c=n.childNodes[i];
			parse_drag(c);
		}
	}	
}

//Initialisation des fonctionnalites (a appeler a la fin du chargement de la page)
function init_drag() {

	//Reinitialisation des tableaux et variables
	draggable=new Array(); 	//Elements depla�ables
	recept=new Array();		//Elements recepteurs
	handler=new Array();	//Poignees
	is_down=false;
	dragup=true;
	posxdown=0;
	posydown=0;
	current_drag=null;
	dragged=null;

	r_x=new Array();
	r_y=new Array();
	r_width=new Array();
	r_height=new Array();
	r_highlight="";

	//Recherche de tous les elements depla�ables et des recepteurs
	parse_drag(document.body);

	//On surveille tout ce qui se passe dans le document au niveau de la souris (sauf click down !)
	document.onmousemove=function (e) {
		move_dragged(e);
	}
	document.onmouseup=function (e) {
		up_dragged(e);
	}
	document.onmouseover=function (e) {
		mouse_over(e);
	}
}

//Calcul de l'encombrement de tous les recepteurs
function recalc_recept() {
	
	for(var i=0;i<recept.length;i++) {
		calc_recept(i);
	}
}

//Calcul de l'encombrement d'un recepteur
function calc_recept(i) {
	try {
		var r=document.getElementById(recept[i]);
		var pos=findPos(r);
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
