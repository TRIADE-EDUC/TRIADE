// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CmsContextMenu.js,v 1.2 2021/01/22 09:46:29 qvarin Exp $


define([ 
        "dojo/_base/declare", 
        "dijit/Menu",
		"dijit/MenuItem", 
        "dojo/_base/lang",
        "dojo/dom-attr", 
        "dojo/on", 
        "dojo/_base/window",
        "dojo/dom", 
		"dijit/MenuSeparator",
		"dijit/registry",
        "dojo/dom-construct"
    ], function(declare, Menu, MenuItem, lang, domAttr, on, win, dom, MenuSeparator, registry, domConstruct){
	return declare(Menu, {
		path: "",
		widget: null,
		postCreate: function(){
			this.inherited(arguments);
			this.widget = registry.byId(this.widgetIdNode);
		},
		eventTypeSelector: function (eventType){
			return on.selector(selector, eventType);
		},
		eventType: function(eventType){
			return eventType;
		},
		bindDomNode: function(/*String|DomNode*/ node) { 
			
			// On récupère la fonction qui récréer le menu
		    var callbackBuildMenu = lang.hitch(this, this.buildMenu);

			// On récupère le noeud
			node = dom.byId(node, this.ownerDocument);
			if (!node) return;
			
			var cn = null;
			var iframe = null
			
			// On regarde si on est dans une iframe
			if (node.tagName.toLowerCase() == "iframe") {
				iframe = node;
				var window = this._iframeContentWindow(iframe);
				cn = win.body(window.document);
			} else {
				cn = (node == win.body(this.ownerDocument) ? this.ownerDocument.documentElement : node);
			}
			
			var binding = {
				node: node,
				iframe: iframe
			};
			
			domAttr.set(node, "_dijitMenu" + this.id, this._bindings.push(binding));
			
			var doConnects = lang.hitch(this, function(cn) {
				
				var selector = this.selector;
				var delegatedEvent = selector ? this.eventTypeSelector : this.eventType;
				var self = this;
				
				return [
					// Évènement lors du clique droit
					on(cn, delegatedEvent(this.leftClickToOpen ? "click" : "contextmenu"), function(evt) {
						
						callbackBuildMenu(evt);
						
						evt.stopPropagation();
						evt.preventDefault();
						
						if((new Date()).getTime() < this._lastKeyDown + 500){
							return;
						}
						
						self._scheduleOpen(this, iframe, {x: evt.pageX, y: evt.pageY}, evt.target);
					}),
				];
			});
			
			binding.connects = cn ? doConnects(cn) : [];

			if (iframe) {
				binding.onloadHandler = lang.hitch(this, function(){
					var window = this._iframeContentWindow(iframe),
						cn = win.body(window.document);
					binding.connects = doConnects(cn);
				});
				if (iframe.addEventListener) {
					iframe.addEventListener("load", binding.onloadHandler, false);
				} else {
					iframe.attachEvent("onload", binding.onloadHandler);
				}
			}
		},
		buildMenu : function(event) {
			this.getMenu(event);
		},
		onBlur : function() {
			this.removeChilds();
		},
		removeChilds : function() {
			var childs = this.getChildren();
			for (let i = 0; i < childs.length; i++) {
				this.removeChild(childs[i]);
			}
		},
		getTitle: function () {
			return this.widget.item.title[0];
		},
 		getMenu : function(event) {
			this.addChild(
				new MenuItem({
					label : this.getTitle(),
					'class' : 'authorityGridTitleItem',
					disabled : true
				})
			);
			this.addChild(new MenuSeparator());
			this.addChild(
				new MenuItem({
					label : pmbDojo.messages.getMessage('cms_editorrial', 'cms_editorrial_edit'),
					onClick : lang.hitch(this, cms_load_content_infos, this.widget.item, dom.byId(this.widgetIdNode), event),
				})
			);
			this.addChild(
				new MenuItem({
					label : pmbDojo.messages.getMessage('cms_editorrial', 'cms_editorrial_copy_path'),
					onClick : lang.hitch(this, this.copyPath),
				})
			);
		},
		copyPath: function () {
			// On construit le chemin du widget
			this.path = "";
			this.buildPath(this.widget);
			
			// On construit l'input pour copier le chemin
			var text = domConstruct.create("input", { 
				type: 'text',
				value: this.decodeHtml(this.path),
				styles: 'display:none;',
			}, document.body);
			text.select();
			
			// On copie le chemin
			document.execCommand('copy');
			text.remove();
			
			return true;
		},
		
		buildPath: function (widget) {
			if (widget.item.title) {
				this.path = "/" + widget.item.title[0] + this.path;
				var widgetParent = widget.getParent();
				if (widgetParent && widgetParent.item) {
					this.buildPath(widgetParent);
				}
			}
		},
		
		decodeHtml: function (html) {
			// On évite les encodages exemple: &eacute;
		    var txt = document.createElement("textarea");
			txt.innerHTML = html;
			return txt.value;
		}
		
	});
});