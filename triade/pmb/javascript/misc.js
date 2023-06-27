// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: misc.js,v 1.22 2019-03-28 21:47:25 ccraig Exp $


// Fonction check_checkbox : Permet de changer l'etats d'une liste de checkbox.
// checkbox_list : Liste d'id des checkbox separee par |
// level: 1 (checked) ou 0;
function check_checkbox(checkbox_list,level) {
	var ids,id,state;
	if(level) state=true; else state=false;
	ids=checkbox_list.split('|');
	while (ids.length>0) {
		id=ids.shift();
		document.getElementById(id).checked = state;
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
	var page = document.getElementById("body_current_module").getAttribute('page_name');
	page=encodeURI(page)
	values=encodeURI(values)
	savehide.request(url,1,"&page="+page+"&values="+values);
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
	if(prec.tagName=='H3'){
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

function closeCurrentEnv(){
	window.parent.require(["dojo/topic"],
		function(topic){
			topic.publish("SelectorTab", "SelectorTab", "closeCurrentTab");
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