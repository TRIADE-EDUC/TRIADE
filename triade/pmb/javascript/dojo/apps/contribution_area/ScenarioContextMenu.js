// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ScenarioContextMenu.js,v 1.2 2017/08/29 14:16:51 tsamson Exp $

define([
        "dojo/_base/declare", 
        "dojo/_base/lang", 
        "dojo/topic", 
        "dijit/MenuItem", 
        "dijit/Menu",
        "dojo/dom-attr", 
        "dojo/_base/window", 
        "dojo/dom", 
        "dojo/on", 
    ], function(declare,lang, topic, MenuItem, Menu, domAttr, win, dom, on){
	return declare(Menu, {
		postCreate: function(){
			this.inherited(arguments);
//			this.addChild(
//				new MenuItem(
//					{
//						label:pmbDojo.messages.getMessage('contribution_area', 'contribution_area_edit_scenario'),
//						onClick: lang.hitch(this, this.requestScenarioEdition)
//					})
//			);
//			this.addChild(
//				new MenuItem(
//					{
//						label:pmbDojo.messages.getMessage('contribution_area', 'contribution_area_remove_scenario'),
//						onClick: lang.hitch(this, this.requestScenarionRemove)
//					})
//			);
		},
		bindDomNode: function(/*String|DomNode*/ node){
//		    var callbackFind = lang.hitch(this, this.findMovableElt);
//		    var callbackBuildMenu = lang.hitch(this, this.buildMenu);
			node = dom.byId(node, this.ownerDocument);
			var cn;	
			if(node.tagName.toLowerCase() == "iframe"){
				var iframe = node,
					window = this._iframeContentWindow(iframe);
				cn = win.body(window.document);
			}else{
				cn = (node == win.body(this.ownerDocument) ? this.ownerDocument.documentElement : node);
			}
			var binding = {
				node: node,
				iframe: iframe
			};
			domAttr.set(node, "_dijitMenu" + this.id, this._bindings.push(binding));
			var doConnects = lang.hitch(this, function(cn){
				var selector = this.selector,
					delegatedEvent = selector ?
						function(eventType){
							return on.selector(selector, eventType);
						} :
						function(eventType){
							return eventType;
						},
					self = this;
				return [
					on(cn, delegatedEvent(this.leftClickToOpen ? "click" : "contextmenu"), function(evt){
//						callbackFind(evt.target);
//						callbackBuildMenu();
						evt.stopPropagation();
						evt.preventDefault();
						if((new Date()).getTime() < this._lastKeyDown + 500){
							return;
						}
						self._scheduleOpen(this, iframe, {x: evt.pageX, y: evt.pageY}, evt.target);
					}),
					on(cn, delegatedEvent("keydown"), function(evt){
						if(evt.keyCode == 93 ||									// context menu key
							(evt.shiftKey && evt.keyCode == keys.F10) ||		// shift-F10
							(this.leftClickToOpen && evt.keyCode == keys.SPACE)	// space key
						){
							evt.stopPropagation();
							evt.preventDefault();
							self._scheduleOpen(this, iframe, null, evt.target);	// no coords - open near evt.target
							this._lastKeyDown = (new Date()).getTime();
						}
					})
				];
			});
			binding.connects = cn ? doConnects(cn) : [];

			if(iframe){
		
				binding.onloadHandler = lang.hitch(this, function(){
					var window = this._iframeContentWindow(iframe),
						cn = win.body(window.document);
					binding.connects = doConnects(cn);
				});
				if(iframe.addEventListener){
					iframe.addEventListener("load", binding.onloadHandler, false);
				}else{
					iframe.attachEvent("onload", binding.onloadHandler);
				}
			}
		},
		requestScenarioEdition: function(){
			topic.publish('SvgContextMenu', 'scenarioEditionRequested', {isStartScenario:true});
		},
		requestScenarioRemove: function(){
			topic.publish('SvgContextMenu', 'scenarioRemoveRequested', {isStartScenario:true});
		}
	});
});