// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: misc.js,v 1.31.2.1.2.1 2025/02/27 14:05:55 dgoron Exp $


function replace_texte(string,text,by) {
    var strLength = string.length, txtLength = text.length;
    if ((strLength == 0) || (txtLength == 0)) return string;

    var i = string.indexOf(text);
    if ((!i) && (text != string.substring(0,txtLength))) return string;
    if (i == -1) return string;

    var newstr = string.substring(0,i) + by;

    if (i+txtLength < strLength)
        newstr += replace_texte(string.substring(i+txtLength,strLength),text,by);

    return newstr;
}
		
function reverse_html_entities(text) {
    
    text = replace_texte(text,'&quot;',unescape('%22'));
	text = replace_texte(text,'&apos;',unescape('%27'));
	text = replace_texte(text,'&#039;',unescape('%27'));
    text = replace_texte(text,'&amp;',unescape('%26'));
    text = replace_texte(text,'&lt;',unescape('%3C'));
    text = replace_texte(text,'&gt;',unescape('%3E'));
    text = replace_texte(text,'&nbsp;',unescape('%A0'));
    text = replace_texte(text,'&iexcl;',unescape('%A1'));
    text = replace_texte(text,'&cent;',unescape('%A2'));
    text = replace_texte(text,'&pound;',unescape('%A3'));
    text = replace_texte(text,'&yen;',unescape('%A5'));
    text = replace_texte(text,'&brvbar;',unescape('%A6'));
    text = replace_texte(text,'&sect;',unescape('%A7'));
    text = replace_texte(text,'&uml;',unescape('%A8'));
    text = replace_texte(text,'&copy;',unescape('%A9'));
    text = replace_texte(text,'&ordf;',unescape('%AA'));
    text = replace_texte(text,'&laquo;',unescape('%AB'));
    text = replace_texte(text,'&not;',unescape('%AC'));
    text = replace_texte(text,'&shy;',unescape('%AD'));
    text = replace_texte(text,'&reg;',unescape('%AE'));
    text = replace_texte(text,'&macr;',unescape('%AF'));
    text = replace_texte(text,'&deg;',unescape('%B0'));
    text = replace_texte(text,'&plusmn;',unescape('%B1'));
    text = replace_texte(text,'&sup2;',unescape('%B2'));
    text = replace_texte(text,'&sup3;',unescape('%B3'));
    text = replace_texte(text,'&acute;',unescape('%B4'));
    text = replace_texte(text,'&micro;',unescape('%B5'));
    text = replace_texte(text,'&para;',unescape('%B6'));
    text = replace_texte(text,'&middot;',unescape('%B7'));
    text = replace_texte(text,'&cedil;',unescape('%B8'));
    text = replace_texte(text,'&sup1;',unescape('%B9'));
    text = replace_texte(text,'&ordm;',unescape('%BA'));
    text = replace_texte(text,'&raquo;',unescape('%BB'));
    text = replace_texte(text,'&frac14;',unescape('%BC'));
    text = replace_texte(text,'&frac12;',unescape('%BD'));
    text = replace_texte(text,'&frac34;',unescape('%BE'));
    text = replace_texte(text,'&iquest;',unescape('%BF'));
    text = replace_texte(text,'&Agrave;',unescape('%C0'));
    text = replace_texte(text,'&Aacute;',unescape('%C1'));
    text = replace_texte(text,'&Acirc;',unescape('%C2'));
    text = replace_texte(text,'&Atilde;',unescape('%C3'));
    text = replace_texte(text,'&Auml;',unescape('%C4'));
    text = replace_texte(text,'&Aring;',unescape('%C5'));
    text = replace_texte(text,'&AElig;',unescape('%C6'));
    text = replace_texte(text,'&Ccedil;',unescape('%C7'));
    text = replace_texte(text,'&Egrave;',unescape('%C8'));
    text = replace_texte(text,'&Eacute;',unescape('%C9'));
    text = replace_texte(text,'&Ecirc;',unescape('%CA'));
    text = replace_texte(text,'&Euml;',unescape('%CB'));
    text = replace_texte(text,'&Igrave;',unescape('%CC'));
    text = replace_texte(text,'&Iacute;',unescape('%CD'));
    text = replace_texte(text,'&Icirc;',unescape('%CE'));
    text = replace_texte(text,'&Iuml;',unescape('%CF'));
    text = replace_texte(text,'&ETH;',unescape('%D0'));
    text = replace_texte(text,'&Ntilde;',unescape('%D1'));
    text = replace_texte(text,'&Ograve;',unescape('%D2'));
    text = replace_texte(text,'&Oacute;',unescape('%D3'));
    text = replace_texte(text,'&Ocirc;',unescape('%D4'));
    text = replace_texte(text,'&Otilde;',unescape('%D5'));
    text = replace_texte(text,'&Ouml;',unescape('%D6'));
    text = replace_texte(text,'&times;',unescape('%D7'));
    text = replace_texte(text,'&Oslash;',unescape('%D8'));
    text = replace_texte(text,'&Ugrave;',unescape('%D9'));
    text = replace_texte(text,'&Uacute;',unescape('%DA'));
    text = replace_texte(text,'&Ucirc;',unescape('%DB'));
    text = replace_texte(text,'&Uuml;',unescape('%DC'));
    text = replace_texte(text,'&Yacute;',unescape('%DD'));
    text = replace_texte(text,'&THORN;',unescape('%DE'));
    text = replace_texte(text,'&szlig;',unescape('%DF'));
    text = replace_texte(text,'&agrave;',unescape('%E0'));
    text = replace_texte(text,'&aacute;',unescape('%E1'));
    text = replace_texte(text,'&acirc;',unescape('%E2'));
    text = replace_texte(text,'&atilde;',unescape('%E3'));
    text = replace_texte(text,'&auml;',unescape('%E4'));
    text = replace_texte(text,'&aring;',unescape('%E5'));
    text = replace_texte(text,'&aelig;',unescape('%E6'));
    text = replace_texte(text,'&ccedil;',unescape('%E7'));
    text = replace_texte(text,'&egrave;',unescape('%E8'));
    text = replace_texte(text,'&eacute;',unescape('%E9'));
    text = replace_texte(text,'&ecirc;',unescape('%EA'));
    text = replace_texte(text,'&euml;',unescape('%EB'));
    text = replace_texte(text,'&igrave;',unescape('%EC'));
    text = replace_texte(text,'&iacute;',unescape('%ED'));
    text = replace_texte(text,'&icirc;',unescape('%EE'));
    text = replace_texte(text,'&iuml;',unescape('%EF'));
    text = replace_texte(text,'&eth;',unescape('%F0'));
    text = replace_texte(text,'&ntilde;',unescape('%F1'));
    text = replace_texte(text,'&ograve;',unescape('%F2'));
    text = replace_texte(text,'&oacute;',unescape('%F3'));
    text = replace_texte(text,'&ocirc;',unescape('%F4'));
    text = replace_texte(text,'&otilde;',unescape('%F5'));
    text = replace_texte(text,'&ouml;',unescape('%F6'));
    text = replace_texte(text,'&divide;',unescape('%F7'));
    text = replace_texte(text,'&oslash;',unescape('%F8'));
    text = replace_texte(text,'&ugrave;',unescape('%F9'));
    text = replace_texte(text,'&uacute;',unescape('%FA'));
    text = replace_texte(text,'&ucirc;',unescape('%FB'));
    text = replace_texte(text,'&uuml;',unescape('%FC'));
    text = replace_texte(text,'&yacute;',unescape('%FD'));
    text = replace_texte(text,'&thorn;',unescape('%FE'));
    text = replace_texte(text,'&yuml;',unescape('%FF'));
    return text;

}

function html_entities(text) {

  text = replace_texte(text, unescape('%22'), '&quot;');
  text = replace_texte(text, unescape('%26'), '&amp;');
  text = replace_texte(text, unescape('%3C'), '&lt;');
  text = replace_texte(text, unescape('%3E'), '&gt;');
  text = replace_texte(text, unescape('%A0'), '&nbsp;');
  text = replace_texte(text, unescape('%A1'), '&iexcl;');
  text = replace_texte(text, unescape('%A2'), '&cent;');
  text = replace_texte(text, unescape('%A3'), '&pound;');
  text = replace_texte(text, unescape('%A5'), '&yen;');
  text = replace_texte(text, unescape('%A6'), '&brvbar;');
  text = replace_texte(text, unescape('%A7'), '&sect;');
  text = replace_texte(text, unescape('%A8'), '&uml;');
  text = replace_texte(text, unescape('%A9'), '&copy;');
  text = replace_texte(text, unescape('%AA'), '&ordf;');
  text = replace_texte(text, unescape('%AB'), '&laquo;');
  text = replace_texte(text, unescape('%AC'), '&not;');
  text = replace_texte(text, unescape('%AD'), '&shy;');
  text = replace_texte(text, unescape('%AE'), '&reg;');
  text = replace_texte(text, unescape('%AF'), '&macr;');
  text = replace_texte(text, unescape('%B0'), '&deg;');
  text = replace_texte(text, unescape('%B1'), '&plusmn;');
  text = replace_texte(text, unescape('%B2'), '&sup2;');
  text = replace_texte(text, unescape('%B3'), '&sup3;');
  text = replace_texte(text, unescape('%B4'), '&acute;');
  text = replace_texte(text, unescape('%B5'), '&micro;');
  text = replace_texte(text, unescape('%B6'), '&para;');
  text = replace_texte(text, unescape('%B7'), '&middot;');
  text = replace_texte(text, unescape('%B8'), '&cedil;');
  text = replace_texte(text, unescape('%B9'), '&sup1;');
  text = replace_texte(text, unescape('%BA'), '&ordm;');
  text = replace_texte(text, unescape('%BB'), '&raquo;');
  text = replace_texte(text, unescape('%BC'), '&frac14;');
  text = replace_texte(text, unescape('%BD'), '&frac12;');
  text = replace_texte(text, unescape('%BE'), '&frac34;');
  text = replace_texte(text, unescape('%BF'), '&iquest;');
  text = replace_texte(text, unescape('%C0'), '&Agrave;');
  text = replace_texte(text, unescape('%C1'), '&Aacute;');
  text = replace_texte(text, unescape('%C2'), '&Acirc;');
  text = replace_texte(text, unescape('%C3'), '&Atilde;');
  text = replace_texte(text, unescape('%C4'), '&Auml;');
  text = replace_texte(text, unescape('%C5'), '&Aring;');
  text = replace_texte(text, unescape('%C6'), '&AElig;');
  text = replace_texte(text, unescape('%C7'), '&Ccedil;');
  text = replace_texte(text, unescape('%C8'), '&Egrave;');
  text = replace_texte(text, unescape('%C9'), '&Eacute;');
  text = replace_texte(text, unescape('%CA'), '&Ecirc;');
  text = replace_texte(text, unescape('%CB'), '&Euml;');
  text = replace_texte(text, unescape('%CC'), '&Igrave;');
  text = replace_texte(text, unescape('%CD'), '&Iacute;');
  text = replace_texte(text, unescape('%CE'), '&Icirc;');
  text = replace_texte(text, unescape('%CF'), '&Iuml;');
  text = replace_texte(text, unescape('%D0'), '&ETH;');
  text = replace_texte(text, unescape('%D1'), '&Ntilde;');
  text = replace_texte(text, unescape('%D2'), '&Ograve;');
  text = replace_texte(text, unescape('%D3'), '&Oacute;');
  text = replace_texte(text, unescape('%D4'), '&Ocirc;');
  text = replace_texte(text, unescape('%D5'), '&Otilde;');
  text = replace_texte(text, unescape('%D6'), '&Ouml;');
  text = replace_texte(text, unescape('%D7'), '&times;');
  text = replace_texte(text, unescape('%D8'), '&Oslash;');
  text = replace_texte(text, unescape('%D9'), '&Ugrave;');
  text = replace_texte(text, unescape('%DA'), '&Uacute;');
  text = replace_texte(text, unescape('%DB'), '&Ucirc;');
  text = replace_texte(text, unescape('%DC'), '&Uuml;');
  text = replace_texte(text, unescape('%DD'), '&Yacute;');
  text = replace_texte(text, unescape('%DE'), '&THORN;');
  text = replace_texte(text, unescape('%DF'), '&szlig;');
  text = replace_texte(text, unescape('%E0'), '&agrave;');
  text = replace_texte(text, unescape('%E1'), '&aacute;');
  text = replace_texte(text, unescape('%E2'), '&acirc;');
  text = replace_texte(text, unescape('%E3'), '&atilde;');
  text = replace_texte(text, unescape('%E4'), '&auml;');
  text = replace_texte(text, unescape('%E5'), '&aring;');
  text = replace_texte(text, unescape('%E6'), '&aelig;');
  text = replace_texte(text, unescape('%E7'), '&ccedil;');
  text = replace_texte(text, unescape('%E8'), '&egrave;');
  text = replace_texte(text, unescape('%E9'), '&eacute;');
  text = replace_texte(text, unescape('%EA'), '&ecirc;');
  text = replace_texte(text, unescape('%EB'), '&euml;');
  text = replace_texte(text, unescape('%EC'), '&igrave;');
  text = replace_texte(text, unescape('%ED'), '&iacute;');
  text = replace_texte(text, unescape('%EE'), '&icirc;');
  text = replace_texte(text, unescape('%EF'), '&iuml;');
  text = replace_texte(text, unescape('%F0'), '&eth;');
  text = replace_texte(text, unescape('%F1'), '&ntilde;');
  text = replace_texte(text, unescape('%F2'), '&ograve;');
  text = replace_texte(text, unescape('%F3'), '&oacute;');
  text = replace_texte(text, unescape('%F4'), '&ocirc;');
  text = replace_texte(text, unescape('%F5'), '&otilde;');
  text = replace_texte(text, unescape('%F6'), '&ouml;');
  text = replace_texte(text, unescape('%F7'), '&divide;');
  text = replace_texte(text, unescape('%F8'), '&oslash;');
  text = replace_texte(text, unescape('%F9'), '&ugrave;');
  text = replace_texte(text, unescape('%FA'), '&uacute;');
  text = replace_texte(text, unescape('%FB'), '&ucirc;');
  text = replace_texte(text, unescape('%FC'), '&uuml;');
  text = replace_texte(text, unescape('%FD'), '&yacute;');
  text = replace_texte(text, unescape('%FE'), '&thorn;');
  text = replace_texte(text, unescape('%FF'), '&yuml;');
  return text;

}
	
// Fonction check_checkbox : Permet de changer l'etats d'une liste de checkbox.
// checkbox_list : Liste d'id des checkbox separee par |
// level: 1 (checked) ou 0;
function check_checkbox(checkbox_list,level) {
	var ids,id,state;
	if(level) state=true; else state=false;
	ids=checkbox_list.split('|');
	while (ids.length>0) {
		id=ids.shift();
		if(!document.getElementById(id).disabled) {
			document.getElementById(id).checked = state;
		}
	}
}


/* -------------------------------------------------------------------------------------
 *		Deroulement du menu vertical sur clic, enregistrement
 *		des preferences sur ctrl+clic avec ajax
 *
 *		menuHide - setMenu - menuSelectH3 - setMenuHide - menuAutoHide
 * ----------------------------------------------------------------------------------- */

/* -----------------------------------------------------------------------------------
 * Fonction menuHide
 * gestionnaire general pour masquer le menu, declenche sur onclick du <span>
 */
// si l'utilisateur n'enregistre pas de preferences,  on retracte/deplie le menu.
function menuHide(obj,event){
	var ctrl = event.ctrlKey || event.metaKey;
	if (ctrl){setMenu(event);}
	else {menuHideObject(obj);}
}

/* -----------------------------------------------------------------------------------
 * Fonction setMenu
 * sauve-restaure les preferences sur le deroulement par defaut du menu selectionne
 */
// Variables globales
var hlist=new Array();
var hclasses=new Array();

function setMenu(){
	var menu = document.getElementById("menu");
	var childs = menu.childNodes;
	var parseH3=0;
	
	//on releve l'etat du menu
	var values="";
	var j=1;
	for(i=0; i<childs.length; i++){
		if(childs[i].tagName=='H3'){
			hlist[j]=childs[i];
			hclasses[j]=hlist[j].className;
			parseH3=1;
			j++;
		} else if (childs[i].tagName=='UL' && parseH3==1){
			if(childs[i].style.display=='none'){values+='f,';}
			else{values+='t,';}
			parseH3=0;
		}
	}
	//requete ajax pour sauvegarder l'etat
	savehide = new http_request();
	var url= "./ajax.php?module=ajax&categ=menuhide&fname=setpref";
	url=encodeURI(url) 
	var page_name = document.getElementById("body_current_module").getAttribute('page_name');
	page_name=encodeURI(page_name)
	values=encodeURI(values)
	savehide.request(url,1,"&page_name="+page_name+"&values="+values);
	if(savehide.get_text()!=0){
		alert(savehide.get_text());
	} else {
		for(i=1; i<hlist.length; i++){
			setTimeout("hlist["+i+"].className=\"setpref\"",i*15);
			setTimeout("hlist["+i+"].className=hclasses["+i+"]",i*15+500);
		}
	}
}

/* -------------------------------------------------------------------------------------
 * Fonction menuHideObject
 * Masque ou affiche le menu sous le H3 selectionne
 */
function menuHideObject(obj,force) {
	var pointer=obj;
	do{
		pointer=pointer.nextSibling;
		if (pointer.tagName=='H3' || pointer.tagName=='DIV'){
			break;
		}
		if (pointer.tagName=='UL'){
			if (force==undefined){
				if (pointer.style.display=='none'){
					pointer.style.display='block';
					menuSelectH3(pointer,"");
				}
				else {
					pointer.style.display='none';
					menuSelectH3(pointer,"selected");
				}
			} else {
				if (force==0){
					pointer.style.display='block';
					menuSelectH3(pointer,"");
				}
				else {
					pointer.style.display='none';
					menuSelectH3(pointer,"selected");
				}
			}
		}
	}while(pointer.nextSibling);
}
/* -------------------------------------------------------------------------------------
 * Fonction menuSelectH3()
 * Attribue au menuH3 selectionne une nouvelle classe css (a priori purement esthetique)
 */
function menuSelectH3(ulChild,selectState){
	prec=ulChild.previousSibling;
	if(navigator.appName != "Microsoft Internet Explorer"){
		prec=prec.previousSibling;
	}
	if(prec && prec.tagName=='H3'){
		prec.className=selectState;
	}
}

/* --------------------------------------------------------------------------------------
 * Fonction menuGlobalHide
 * Force le depliement d'une liste de menus, masque tous les autres.
 */
function menuGlobalHide(boollist){
	var boollist=boollist.split(",");	
	var menu = document.getElementById("menu");
	var fils = menu.childNodes;
	var j=0;
	for(i=0; i<fils.length; i++){
		if(fils[i].tagName=='H3'){
			if(boollist[j]=='t'){
				menuHideObject(fils[i],0);
			} else {
				menuHideObject(fils[i],1);
			}
			j++;
		}
	}
}

/* --------------------------------------------------------------------------------------
 * Fonction menuAutoHide
 * Recuppere les preferences d'affichage de l'user, si != 0 elles sont definies
 * et on deplie/replie les menus avec l'appel e menuGlobalHide
 */
function menuAutoHide(){
	if (!trueids) {
		var getHide = new http_request();
		var url = "./ajax.php?module=ajax&categ=menuhide&fname=getpref";
		url=encodeURI(url)
		var page = document.getElementById("body_current_module").getAttribute('page_name');
		page=encodeURI(page)
		getHide.request(url,1,"&page="+page);	
		if(getHide.get_text()!=0){
			menuGlobalHide(getHide.get_text());	
		}
	} else if (trueids!="0") menuGlobalHide(trueids);	
}

/* --------------------------------------------------------------------------------------
 * Fonction addLoadEvent
 * Empile les differentes fonctions a appeler quand la page est chargee
 */
function addLoadEvent(func) {
  if (window.addEventListener)
    window.addEventListener("load", func, false);
  else if (window.attachEvent)
    window.attachEvent("onload", func);
  else { // fallback
    var old = window.onload;
    window.onload = function() {
      if (old) old();
      func();
    };
  }
}

var pmbForm = {
    fieldToObject: function fieldToObject(inputNode){

        var ret = null;
        if(inputNode){
            var _in = inputNode.name, type = (inputNode.type || "").toLowerCase();
            if(_in && type && !inputNode.disabled){
            	if(type == "textarea" && inputNode.id !="" && inputNode.value == ""){ //Test tinymce
            		if(typeof tinyMCE != 'undefined' && tinyMCE.get(inputNode.id)){
            			return tinyMCE.get(inputNode.id).getContent();
            		}
            	}
                if(type == "radio" || type == "checkbox"){
                    if(inputNode.checked){
                        ret = inputNode.value;
                    }
                }else if(inputNode.multiple){
                    ret = [];
                    var nodes = [inputNode.firstChild];
                    while(nodes.length){
                        for(var node = nodes.pop(); node; node = node.nextSibling){
                            if(node.nodeType == 1 && node.tagName.toLowerCase() == "option"){
                                if(node.selected){
                                    ret.push(node.value);
                                }
                            }else{
                                if(node.nextSibling){
                                    nodes.push(node.nextSibling);
                                }
                                if(node.firstChild){
                                    nodes.push(node.firstChild);
                                }
                                break;
                            }
                        }
                    }
                }else{
                    ret = inputNode.value;
                }
            }
        }
        
        if(!ret && pmbForm.include.indexOf(type)!= -1){
        	var form = inputNode.form;
        	var widgetNode = form.querySelector('[widgetid="'+inputNode.name+'"]');
        	if(widgetNode){
        		var widget = dijit.byId(widgetNode.getAttribute('widgetid'));
        	} else {
        		var widgetNode2 = form.querySelector('[widgetid="'+inputNode.name+'_form"]');
            	if(widgetNode2){
            		var widget = dijit.byId(widgetNode2.getAttribute('widgetid'));
            	}
        	}
        	if (widget) {
        		return widget.get('value') ? widget.get('value') : '';
        	}
        }
        return ret;
    },
    setValue: function(obj, name, value){
    	if(value === null){
    		return;
    	}
    	var val = obj[name];
    	if(typeof val == "string"){
    		obj[name] = [val, value];
    	}else if(Array.isArray(val)){
    		val.push(value);
    	}else{
    		obj[name] = value;
    	}
	},
	exclude: ["file", "submit", "image", "reset", "button"],
	include: ['text', 'hidden', 'textarea'],
    toObject: function formToObject(formNode){
        var ret = {}, elems = document.getElementById(formNode).elements;
        for(var i = 0, l = elems.length; i < l; ++i){
            var item = elems[i], _in = item.name, type = (item.type || "").toLowerCase();
            if(_in && type && pmbForm.exclude.indexOf(type) < 0 && !item.disabled){
                pmbForm.setValue(ret, _in, pmbForm.fieldToObject(item));
                if(type == "image"){
                    ret[_in + ".x"] = ret[_in + ".y"] = ret[_in].x = ret[_in].y = 0;
                }
            }
        }
        return ret; 
    },

    toQuery: function formToQuery(formNode){
        return ioq.objectToQuery(pmbForm.toObject(formNode));
    },

    toJson: function formToJson(formNode,prettyPrint){

        return JSON.stringify(pmbForm.toObject(formNode), null, prettyPrint ? 4 : 0);
    }
};

function preLoadScripts(domNode){
	if(domNode){
		var scripts = domNode.querySelectorAll('script');
		scripts = Array.prototype.slice.call(scripts);
		var tabScripts = new Array();
		scripts.forEach(function(script){
			var newScript = document.createElement('script');
			var scriptAttributes = Array.prototype.slice.call(script.attributes);
			scriptAttributes.forEach(function(attribute){
				newScript.setAttribute(attribute.name, attribute.value);
			});			
			if (script.innerHTML.trim() != '' ) {
				newScript.innerHTML = script.innerHTML;
			}			
			newScript.domNode = domNode;
			tabScripts.push(newScript);
			script.parentNode.removeChild(script);
		});
		loadScripts(tabScripts);		
		var nodes = document.querySelectorAll("[data-dojo-type]");
		var tabNodes = Array.prototype.slice.call(nodes);
		tabNodes.forEach(function(node){
			if (parentElement != node.parentElement) {
				if (!node.getAttribute('widgetid')) {
					dojo.parser.parse(node.parentElement);
				}
				var parentElement = node.parentElement;
			}				
		});
	}
}
function loadScripts(tabScripts){
	if(tabScripts.length){
		var currentScript = tabScripts.shift();
		if (currentScript.src) {
			//l'evenement onload ne fonctionne que sur des scripts avec l'attribut src
			currentScript.onload = currentScript.onreadystatechange =  function(){
				loadScripts(tabScripts);
			}
			currentScript.domNode.appendChild(currentScript);
		} else {
			currentScript.domNode.appendChild(currentScript);
			loadScripts(tabScripts);
		}
	}
};

function empty_dojo_calendar_by_id(id){
	require(["dijit/registry"], function(registry) {registry.byId(id).set('value',null);});
}

function aide_regex() {
	openPopUp('./help.php?whatis=regex', 'regex_howto');
}

function closeCurrentEnv(what){
	window.parent.require(["dojo/topic"],
		function(topic){
			var evtArgs = [];
			if(what) {
				evtArgs.what = what;
			}
			topic.publish("SelectorTab", "SelectorTab", "closeCurrentTab", evtArgs);
		}
	);
}

function get_input_date_time_inter_js(div, name, id, today, msg_date_begin, msg_date_end) {
	
	var date = new Date();
	if (today) {
		date = null;
	} else {
		date = date.toISOString().substr(0, 10);
	}    
	var label_begin = document.createElement('label');
	label_begin.innerHTML = pmbDojo.messages.getMessage('date', msg_date_begin);

	var date_begin = document.createElement('input');
    date_begin.setAttribute('type', 'date');
    date_begin.setAttribute('id', id + '_date_begin');
    date_begin.setAttribute('value', date);
	

	var time_begin = document.createElement('input');
	time_begin.setAttribute('type', 'time');
	time_begin.setAttribute('id', id + '_time_begin');
			
	var label_end = document.createElement('label');
	label_end.innerHTML = pmbDojo.messages.getMessage('date', msg_date_end);
			
	var date_end = document.createElement('input');
    date_end.setAttribute('type','date');
    date_end.setAttribute('id', id + '_date_end');
    date_end.value = date;
    
	var time_end = document.createElement('input');
	time_end.setAttribute('type','time');
	time_end.setAttribute('id', id + '_time_end');
	
	var del = document.createElement('input');
	del.setAttribute('type', 'button');
    del.setAttribute('class', 'bouton');
    del.setAttribute('value', 'X');
    
    var buttonId = id.split('_');
    buttonId.pop();
    buttonId = buttonId.join('_');
    var buttonAdd = document.getElementById('button_add_' + buttonId);
    
	if (use_dojo_calendar == 1) { 
		del.addEventListener('click', function() {
			require(['dijit/registry'], function(registry) {
				empty_dojo_calendar_by_id(id + '_date_begin');
				empty_dojo_calendar_by_id(id + '_time_begin');
				empty_dojo_calendar_by_id(id + '_date_end');
				empty_dojo_calendar_by_id(id + '_time_end');
			});
		}, false);
		
	} else {
	    date_begin.setAttribute('name', name + '[date_begin]');
		time_begin.setAttribute('name', name + '[time_begin]');
	    date_end.setAttribute('name', name + '[date_end]');
		time_end.setAttribute('name', name + '[time_end]');
		del.addEventListener('click', function() {
			document.getElementById(id + '_date_begin').value = '';
			document.getElementById(id + '_time_begin').value = '';
			document.getElementById(id + '_date_end').value = '';
			document.getElementById(id + '_time_end').value = '';
		}, false);
		
	}
	var br = document.createElement('br');
	div.appendChild(label_begin);
	div.appendChild(document.createTextNode(' '));
	div.appendChild(date_begin);
	div.appendChild(document.createTextNode(' '));
	div.appendChild(time_begin);
	div.appendChild(document.createTextNode(' '));
	div.appendChild(label_end);
	div.appendChild(document.createTextNode(' '));
	div.appendChild(date_end);
	div.appendChild(document.createTextNode(' '));
	div.appendChild(time_end);
	div.appendChild(document.createTextNode(' '));
	div.appendChild(del);
	if (buttonAdd) div.appendChild(buttonAdd);
	div.appendChild(br);
	
	if (use_dojo_calendar == 1) { 		
		require(['dijit/form/TimeTextBox', 'dijit/form/DateTextBox'], function(TimeTextBox, DateTextBox) {
			new DateTextBox({value : date, name : name + '[date_begin]'}, id + '_date_begin').startup();

			new TimeTextBox({value: null,
				name : name + '[time_begin]',
				constraints : {
					timePattern : 'HH:mm',
					clickableIncrement : 'T00:15:00',
					visibleIncrement : 'T01:00:00',
					visibleRange : 'T01:00:00'
				}
			}, id + '_time_begin').startup();

			new DateTextBox({value : date, name : name + '[date_end]'}, id + '_date_end').startup();

			new TimeTextBox({value : null,
				name : name + '[time_end]',
				constraints : {
					timePattern : 'HH:mm',
					clickableIncrement : 'T00:15:00',
					visibleIncrement : 'T01:00:00',
					visibleRange : 'T01:00:00'
				}
			}, id + '_time_end').startup();
		});
		
	} 
    return div;
}

function get_input_date_js(name, id, value, required, onchange) {
	
    var input_date = document.createElement('input');
    input_date.setAttribute('name', name);
    input_date.setAttribute('id', id);
    if (use_dojo_calendar == 1) { 
        input_date.setAttribute('data-dojo-type', 'dijit/form/DateTextBox');
        input_date.setAttribute('type', 'text');
    } else {
        input_date.setAttribute('type', 'date');
    }
    if (value) {
    	input_date.setAttribute('value', value);
    } else {
    	input_date.setAttribute('value', '');
    }
    return input_date;
}

function set_parent_value(f_caller, id, value){
	if (!f_caller || !id) return;
	if(typeof window.parent.document.forms[f_caller] != 'undefined') {
		window.parent.document.forms[f_caller].elements[id].value = value;
	} else if(typeof window.opener.document.forms[f_caller] != 'undefined') {
		window.opener.document.forms[f_caller].elements[id].value = value;
	} 
}

function get_parent_value(f_caller, id){
	if(typeof window.parent.document.forms[f_caller] != 'undefined') {
		return window.parent.document.forms[f_caller].elements[id].value;
	} else if(typeof window.opener.document.forms[f_caller] != 'undefined') {
		return window.opener.document.forms[f_caller].elements[id].value;
	}
	return '';
}

function set_parent_focus(f_caller, id){
	if(typeof window.parent.document.forms[f_caller] != 'undefined') {
		window.parent.document.forms[f_caller].elements[id].focus();
	} else if(typeof window.opener.document.forms[f_caller] != 'undefined') {
		window.opener.document.forms[f_caller].elements[id].focus();
	}
}

function is_valid_mail(mail){
	var regex = /(^(([^<>()\[\]\\.,;:\s@\"]+(\.[^<>()\[\]\\.,;:\s@\"]+)*)|(\"\.+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$)/;
	var result = mail.match(regex);
	if(null == result){
		return false;
	}
	return true;
}

function toggle_password(caller,id) {
	
	try {
		var i = document.getElementById(id);
		switch (i.type) {
			case 'text' :
				i.type='password';
				caller.setAttribute('class', 'fa fa-eye');
				break;;
			case 'password' :
				i.type='text';
				caller.setAttribute('class', 'fa fa-eye-slash');
				break;
		}
		
	} catch(e) {}
}

function show_elements(class_name) {
	try {
		var elements = document.querySelectorAll("*[class='"+class_name+"']");
		elements.forEach(function(element){
			element.style.display = "block"
		});
		
	} catch(e) {}
}

function hide_elements(class_name) {
	try {
		var elements = document.querySelectorAll("*[class='"+class_name+"']");
		elements.forEach(function(element){
			element.style.display = "none"
		});
		
	} catch(e) {}
}

/**
 * methode pour inclure des fichiers js et eviter les balises <script type='text/javascript' src='nom_fichier.js'></script>
 */
function pmb_include(file) {
    if (typeof window.filesList == 'undefined') {
        window['filesList'] = [];
    }
    if (window.filesList.includes(file)) {
        return;
    }
    let script = document.createElement('script');
    script.src = file;
    script.type = 'text/javascript';
    script.defer = true;
 
    document.getElementsByTagName('head').item(0).appendChild(script);
    window.filesList.push(file);
}

/**
 * Methode pour afficher un loader sur tout l'ecran
 */
function pmb_show_loader(id) {
    // Créez un élément div pour le conteneur principal avec les classes nécessaires
    const loader_container = document.createElement('div');
    loader_container.className = 'generic-loader';
    loader_container.setAttribute('id', id + '_generic_loader');
    loader_container.setAttribute('title', 'loading');
    
    // Créez un sous-conteneur div pour les éléments de chargement
    const loader_sub_container = document.createElement('div');
    loader_sub_container.className = 'generic-loader-container';
    
    // Créez l'icône de chargement
    const spinner_icon = document.createElement('i');
    spinner_icon.className = 'fa fa-spinner fa-spin fa-3x fa-fw';
    
    // Créez le texte 'loading' pour les lecteurs d'écran
    const sr_only_text = document.createElement('span');
    sr_only_text.className = 'sr-only';
    sr_only_text.textContent = 'loading';
    
    // Ajoutez l'icône de chargement et le texte 'loading' au sous-conteneur
    loader_sub_container.appendChild(spinner_icon);
    loader_sub_container.appendChild(sr_only_text);
    
    // Ajoutez le sous-conteneur au conteneur principal
    loader_container.appendChild(loader_sub_container);
    
    // Ajoutez le conteneur principal au corps du document
    document.body.appendChild(loader_container);
}
 
/**
 * Methode pour cacher un loader
 */
function pmb_hide_loader(id) {
  // Supprimez l'élément de chargement du DOM s'il existe
  const loader = document.getElementById(id + '_generic_loader');
  if (loader) {
    loader.remove();
  }
}

/*
 * Focus trap : permet de bloquer le focus dans un container spécifique (RGAA)
 */

function focus_trap(containerElement) {
	if (!containerElement || !containerElement.id) {
		return;
	}
	var container = document.getElementById(containerElement.id);
	if (container) {
		var focusableElements = get_focusable_elements(container);
		if (focusableElements.length == 0) {
			return;
		}
		// Le focus ne prend pas sans delay
		setTimeout(() => {
			if (focusableElements.first) {
				focusableElements.first.focus();
			}
		}, 0);

		container.addEventListener('keydown', function (event) {
			if (event.key === 'Tab' || event.key === 'ArrowDown') {
				if (event.shiftKey) {
					if (document.activeElement === focusableElements.first) {
						event.preventDefault();
						focusableElements.last.focus();
					}
				} else {
					if (document.activeElement === focusableElements.last) {
						event.preventDefault();
						focusableElements.first.focus();
					}
				}
			}
		})
		document.onmousedown = function(event) {
			if (!container.contains(event.target)) {
				event.preventDefault();

				setTimeout(() => {
					if (focusableElements.first) {
						focusableElements.first.focus();
					}
				}, 0);
			}
		}
	}
}

/*
 * Focus trap : permet de lister la liste des éléments focussables situés dans un élément spécifique
 * Retourne le premier et le dernier élément de la liste
 */

function get_focusable_elements(element) {
	var focusableElementsList = element.querySelectorAll('a[href], button, input, textarea, select, details,[tabindex]:not([tabindex="-1"])');
	var focusableElementsArray = Array.from(focusableElementsList);
	focusableElementsArray = focusableElementsArray.filter((el) => !el.hasAttribute('disabled') && !el.getAttribute('aria-hidden'));
	var focusableElementsObject = {
		first: focusableElementsArray[0],
		last:focusableElementsArray[focusableElementsArray.length - 1]
	}

	return focusableElementsObject;
}