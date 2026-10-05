// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contextUtil.js,v 1.2 2018/06/22 15:33:13 vtouchard Exp $

define([
        "dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/request",
        "dojo/query",
        "dojo/on",
        "dojo/dom-attr",
        "dojo/dom",
        "dojo/ready",
        "dojo/_base/xhr",
    	"dojo/_base/window",
], function(declare, lang, request, query, on, domAttr, dom, ready, xhrUtil, windowUtil){
	var obj = {
	//Code issu de la classe dojox ContentPane permettant d'instancier les scripts présents dans un contenu texte
		values: [],
		finalCodeString: '',
		eventCor: {
			'onclick': 'click',
			'onsubmit': 'submit',
			'onmouseout': 'mouseout',
			'onchange' : 'change'
		},
		/*
		 * A utiliser pour les pbs de document.location dans le contexte généré par l'objet 

(function () {

  var _window = window;
  var _document = document;  
  (function () {
    var window = {};
    var document = {};
    Object.defineProperty(window, 'location', {
      get: function () { return _window.location; },
      set: function (value) { console.log(value); }
    });
	Object.defineProperty(document, 'location', {
	  get: function () { return _document.location; },
      set: function (value) { console.log('doc', value); }
    });
    window.__proto__ = _window;
	document.__proto__ = _document;
    
    // At this point, 'window' is identical to normal window except 
    // we've overriden its location property to be read-only.
    
    // If you could manage to run your page's environment here, you could 
    // intercept attempts to change the page.
    window.location = "bonjour"
	document.location.href = "lkslk";    
  }());

}());

		 */
		snarfScripts: function(content){
			var byRef = {
					downloadRemote: true,
					code: ""
			}
	
			content = content.replace(/<[!][-][-](.|\s)*?[-][-]>/g,
				function(comment){
					return comment.replace(/<(\/?)script\b/ig,"&lt;$1Script");
				}
			);
			function download(src){
					if(byRef.downloadRemote){
					src = src.replace(/&([a-z0-9#]+);/g, function(m, name) {
						switch(name) {
							case "amp"	: return "&";
							case "gt"	: return ">";
							case "lt"	: return "<";
							default:
								return name.charAt(0)=="#" ? String.fromCharCode(name.substring(1)) : "&"+name+";";
						}
					});
					xhrUtil.get({
						url: src,
						sync: true,
						load: function(code){
							if(byRef.code !=="") {
							   code = "\n" + code;
							}
							byRef.code += code+";";
						},
						error: byRef.errBack
					});
					}
			}
			// match <script>, <script type="text/..., but not <script type="dojo(/method)...
			 content.replace(/<script\s*(?![^>]*type=['"]?(?:dojo\/|text\/html\b))[^>]*?(?:src=(['"]?)([^>]*?)\1[^>]*)?>([\s\S]*?)<\/script>/gi,
				function(ignore, delim, src, code){				
					if(src){
						download(src);
					}else{
						if(byRef.code !=="") {
						   code = "\n" + code;
						}
						byRef.code += code+";";
					}
					return "";
				}
			);
			return byRef.code.replace(/(<!--|(?:\/\/)?-->|<!\[CDATA\[|\]\]>)/g, '');;
		},
		//Take html string, extract 'on' (like onclick) events build a special context, inject it ; return cleaned code 
		buildCustomContext: function(content, appendNode){
			var appendNode = appendNode || windowUtil.doc.body;
			scripts = this.snarfScripts(content);
			content = this.rebuildEvents(content);

			if(this.finalCodeString != ''){
				var customEvents = "require(['dojo/on', 'dojo/query', 'dojo/_base/lang'], function(on, query, lang){"+this.finalCodeString+"}); \n"
			}
			
			
			scripts = scripts.replace("document.forms['explnum'].elements['f_nom'].focus()", '');
			scripts = scripts.replace("ajax_parse_dom();", '');

			/**
			 * TODO: append d'abord le contenu 
			 * Puis charger le script
			 */
			return {content: content, scripts: '(function(){\n'+(scripts ? scripts : '')+'\n'+(customEvents ? customEvents : '')+'\n}())'};
		},
		rebuildEvents: function(content){
			if(this.finalCodeString != ''){
				this.finalCodeString = '';
			}
			content = content.replace(/(?: (?:(on\w+)=)(?:"|'))([^'|^"]*)(?:" |' |">|'>|"\/>|'\/>)/g, lang.hitch(this, function(fullMatch, eventType, jsString){
				var randomVal = Math.random().toString(36).substring(7);
				while(this.values.indexOf(randomVal) !== -1){
					randomVal = Math.random().toString(36).substring(7);
				}
				this.values.push(randomVal);
				
				if(this.eventCor[eventType.toLowerCase()]){
					if(jsString){
						jsString = this.replaceKeywords(jsString, 'event', 'e');
						jsString = this.replaceKeywords(jsString, 'this', 'e.target');	
					}
					
					this.finalCodeString+= "\n if(document.querySelector('*[data-event-type=\""+randomVal+"\"]')){on(document.querySelector('*[data-event-type=\""+randomVal+"\"]'), '"+this.eventCor[eventType.toLowerCase()]+"', function(e){ lang.hitch(e.target, function(e){ "+jsString+" }, e)() })} ;"	
				}
				if(fullMatch && fullMatch[fullMatch.length-1] == ">"){
					return ' data-event-type="'+randomVal+'" >';
				}
				return  ' data-event-type="'+randomVal+'" ';
			}));
			return content;
		},
		replaceKeywords: function(string, search, replace){
			var regex = new RegExp("(\"|'|^|\\(|,| )("+search+")(\\)|,| |\\.)", 'g');
			var replace = replace;
			return string.replace(regex, (fullMatch, grp1, grp2, grp3) => {
				return grp1+replace+grp3;	
			});
		}
		
	}
	return obj;
});