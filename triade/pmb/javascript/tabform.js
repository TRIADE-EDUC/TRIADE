// gestion des forms "collapsibles" en Javascript
// $Id: tabform.js,v 1.13 2017/12/15 11:39:01 dgoron Exp $

// tabCreate() : cr�e un objet form et affecte les m�thodes et propri�t�s

var imgOpened = new Image();
if(typeof pmb_img_minus != 'undefined') {
	imgOpened.src = pmb_img_minus;
} else {
	imgOpened.src = base_path+'/images/minus.gif';
}
var imgClosed = new Image();
if(typeof pmb_img_plus != 'undefined') {
	imgClosed.src = pmb_img_plus;
} else {
	imgOpened.src = base_path+'/images/plus.gif';
}
var expandedDb = 'el0Child';

// on regarde si le client est DOM-compliant

var isDOM = (typeof(document.getElementsByTagName) != 'undefined') ? 1 : 0;

//Konqueror (support DOM partiel) : on rejette
if(isDOM && typeof(navigator.userAgent) != 'undefined') {
	var browserName = ' ' + navigator.userAgent.toLowerCase();
	if(browserName.indexOf('konqueror') > 0) {
		isDOM = 0;
	}
}

function expandAll() {
  var tempCollNoticeChild = document.querySelectorAll('div[class~="notice-child"]');
  var tempCollChild = document.querySelectorAll('div[class~="child"]');
  var tempColl = Array.prototype.slice.call(tempCollNoticeChild).concat(Array.prototype.slice.call(tempCollChild));
  var tempCollCnt = tempColl.length;

  for (var i = 0; i < tempCollCnt; i++) {
     if(tempColl[i].getAttribute("hide")!="yes"){
    	 tempColl[i].style.display = 'block';
     }
     
     var callback = tempColl[i].getAttribute("callback");
     if(callback){
   	  window[callback]();
     }
     if(typeof ajax_resize_elements == "function"){
   	  ajax_resize_elements();
     }
  }
  tempColl    = document.querySelectorAll('img[name="imEx"]');
  tempCollCnt = tempColl.length;
  for (var i = 0; i < tempCollCnt; i++) {
	  tempColl[i].src = imgOpened.src;
  }
}

function collapseAll() { 
  var tempColl = document.querySelectorAll('div[class~="child"]');
  var tempCollCnt = tempColl.length;
  for (var i = 0; i < tempCollCnt; i++) {
     if(tempColl[i].getAttribute("hide")!="yes"){
    	 tempColl[i].style.display = 'none';	 
     }
     
  }
  tempColl    = document.querySelectorAll('img[name="imEx"]');
  tempCollCnt = tempColl.length;
  for (var i = 0; i < tempCollCnt; i++) {
	  tempColl[i].src = imgClosed.src;
  }
}


function initIt()
{
  if (!isDOM) {
//	alert("ce navigateur n'est pas compatible avec le DOM.");
    return;
  }
  var tempColl    = document.getElementsByTagName('DIV');
  var tempCollCnt = tempColl.length;
  for (var i = 0; i < tempCollCnt; i++) {
    if ((((tempColl[i].id == expandedDb)&&(document.getElementById("elbulChild")==null))||(tempColl[i].id=="elbulChild")) && !(tempColl[i].getAttribute("startOpen")=="yes" || tempColl[i].getAttribute("startOpen")=="no")){
    	tempColl[i].style.display = 'block';
    }else if (tempColl[i].className == 'child' && !(tempColl[i].getAttribute("startOpen")=="yes" || tempColl[i].getAttribute("startOpen")=="no") ){
    	 tempColl[i].style.display = 'none';
    	 
    	 //On recharge l'onglet on met plus dans l'image
    	 var chaine= new String(tempColl[i].id);
    	 chaine=chaine.replace('Child', 'Parent');
    	 var tempCollparent = document.getElementById(chaine);
    	 
    	 //On parcourt tous les fils de l'�l�ment parent
    	 if(tempCollparent!=null){
	    	 for(var j=0;j<tempCollparent.childNodes.length;j++){
	    		 if(tempCollparent.childNodes[j].nodeType == 1){
	    			 if(tempCollparent.childNodes[j].nodeName == 'H3'){
	    				 //on r�cup�re tous les fils de H3
	    				 var tab = tempCollparent.childNodes[j].childNodes;
	    			 }
	    		 }
	    	 } 
	     }
    	 
    	 if(tab!=null){
    		 for (var k=0;k<tab.length;k++){
    			 if(tab[k].nodeName == 'IMG' && tab[k].name == 'imEx'){
    				//si un fils de H3 est une image qui a pour nom imEx on le met � plus
    				tab[k].src = imgClosed.src;
    			 }
    		 }
    	 }
     }else if(tempColl[i].getAttribute("startOpen")=="yes"){
    	 tempColl[i].style.display = 'block';    	 
       	 //On recharge l'onglet on met - dans l'image
    	 var chaine= new String(tempColl[i].id);
    	 chaine=chaine.replace('Child', 'Parent');
    	 var tempCollparent = document.getElementById(chaine);
    	 
    	 //On parcourt tous les fils de l'�l�ment parent
    	 if(tempCollparent!=null){
	    	 for(var j=0;j<tempCollparent.childNodes.length;j++){
	    		 if(tempCollparent.childNodes[j].nodeType == 1){
	    			 if(tempCollparent.childNodes[j].nodeName == 'H3'){
	    				 //on r�cup�re tous les fils de H3
	    				 var tab = tempCollparent.childNodes[j].childNodes;
	    			 }
	    		 }
	    	 } 
	     }    	 
    	 if(tab!=null){
    		 for (var k=0;k<tab.length;k++){
    			 if(tab[k].nodeName == 'IMG' && tab[k].name == 'imEx'){
    				//si un fils de H3 est une image qui a pour nom imEx on le met � plus
    				tab[k].src = imgOpened.src;
    			 }
    		 }
    	 }
     }else if(tempColl[i].getAttribute("startOpen")=="no"){
    	 tempColl[i].style.display = 'none';    	 
       	 //On recharge l'onglet on met - dans l'image
    	 var chaine= new String(tempColl[i].id);
    	 chaine=chaine.replace('Child', 'Parent');
    	 var tempCollparent = document.getElementById(chaine);
    	 
    	 //On parcourt tous les fils de l'�l�ment parent
    	 if(tempCollparent!=null){
	    	 for(var j=0;j<tempCollparent.childNodes.length;j++){
	    		 if(tempCollparent.childNodes[j].nodeType == 1){
	    			 if(tempCollparent.childNodes[j].nodeName == 'H3'){
	    				 //on r�cup�re tous les fils de H3
	    				 var tab = tempCollparent.childNodes[j].childNodes;
	    			 }
	    		 }
	    	 } 
	     }    	 
    	 if(tab!=null){
    		 for (var k=0;k<tab.length;k++){
    			 if(tab[k].nodeName == 'IMG' && tab[k].name == 'imEx'){
    				//si un fils de H3 est une image qui a pour nom imEx on le met � plus
    				tab[k].src = imgClosed.src;
    			 }
    		 }
    	 }
     }
  }
} // end of the 'initIt()' function

function expandBase(el, unexpand)
{
  if (!isDOM)
    return;
  var whichEl = document.getElementById(el + 'Child');
  var whichIm = document.getElementById(el + 'Img');
  var callback = whichEl.getAttribute("callback");
  if (whichEl.style.display == 'none' && whichIm) {
    whichEl.style.display  = 'block';
    whichIm.src            = imgOpened.src;
    if(typeof(dojo) == "object"){
   	 var widgets = dijit.registry.findWidgets(whichEl);
   	 if(widgets.length > 0){
	   	 for(var i=0 ; i<widgets.length ; i++){
	   		 if(widgets[i].declaredClass == "map_controler"){
	   			widgets[i].map.olMap.updateSize();
	   		 }
	   	 }
   	 }
    }
    if(callback){
  	  window[callback]();
    }
    if(typeof ajax_resize_elements == "function"){
  	  ajax_resize_elements();
    }
  }
  else if (unexpand) {
    whichEl.style.display  = 'none';
    whichIm.src            = imgClosed.src;
  }
} // end of the 'expandBase()' function

onload = initIt;

/*	CSS functions
		emprunt�es de la DHTML Kitchen :
		http://dhtmlkitchen.com/js/utilities/setStyle/index.jsp	*/
function getRef(obj)
{
	if (typeof obj == "string")
	{
		obj = document.getElementById(obj);
	}
	return obj;
}

function setStyle(obj, style, value)
{
	getRef(obj).style[style] = value;
}

function getStyle(obj, style)
{
	if (!document.getElementById)
		return;

	var obj = getRef(obj);
	var value = obj.style[style];

	if (!value)
	{
		if (document.defaultView)
		{
			value = document.defaultView.getComputedStyle(obj, "").getPropertyValue(style);
		}
		else if (obj.currentStyle)
		{
			value = obj.currentStyle[style]
		}
	}
	return style;
}

function setClassName(obj, className)
{
	getRef(obj).className = className;
}
