// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SvgContextMenu.js,v 1.11 2021/01/21 09:01:34 qvarin Exp $

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
        "dijit/MenuSeparator",  
        'dojo/request/xhr',
        ], function(declare,lang, topic, MenuItem, Menu, domAttr, win, dom, on, MenuSeparator, xhr){
	return declare(Menu, {
		areaList: [],
		clipboardId: false,
		postCreate: function(){
			this.inherited(arguments);
			
			// On récupère la liste des espaces
			this.getAreaList();
			
			// On récupère l'id du clipboard dans l'url
			var currentUrl = window.location;
			var regex = /id_clipboard=(\w+)&?/g.exec(currentUrl);
			if (regex) {
			    this.clipboardId = regex[1];
			}
		},
		bindDomNode: function(/*String|DomNode*/ node){
//		    var callbackFind = lang.hitch(this, this.findMovableElt);
		    var callbackBuildMenu = lang.hitch(this, this.buildMenu);
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
						callbackBuildMenu(evt.target);
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
		
		requestScenarioCreation: function(){
			topic.publish('SvgContextMenu', 'scenarioCreationRequested', {isStartScenario:true});
		},
		
		requestRemoveNode: function(nodeID){
			topic.publish('SvgContextMenu', 'nodeRemoveRequested',{nodeID:nodeID});
		},
		
		requestScenarioEdition: function(nodeID){
			topic.publish('SvgContextMenu', 'scenarioEditionRequested', {nodeID:nodeID});
		},
		
		requestFormEdition: function(nodeID){
			topic.publish('SvgContextMenu', 'formEditionRequested', {nodeID:nodeID});
		},
		
		requestAttachmentEdition: function(nodeID){
			topic.publish('SvgContextMenu', 'formAttachmentRequested', {nodeID:nodeID});
		},
		
		buildMenu : function(nodeClicked) {
			var nodeId = nodeClicked.getAttribute('id');
			if (!nodeId) {
				nodeId = nodeClicked.getAttribute('data-circle-id');
			}
			if (typeof this.contextType === 'undefined'){
				this.getMenuGraph(nodeClicked, nodeId);
			}else{
				this.getMenuList(nodeClicked, nodeId);
			}
		},
		
		getMenuGraph : function(nodeClicked, nodeId){
			
			if (this.clipboardId) {
				// On vérifie si le clipboard est toujour valide 
				var request = xhr.post("./ajax.php?module=modelling&categ=contribution_area&sub=area&action=clipboard_valid&id_clipboard="+this.clipboardId);
			    request.then(lang.hitch(this, function(response) {
			    	response = JSON.parse(response)
			    	if (response == false) {
			    		this.clipboardId = false;
			    	}
			    }))
			} else {
				// On n'a pas de clipboard on interdit 
	    		this.clipboardId = false;
			}

			switch (nodeClicked.getAttribute('data-type')) {
			
				case 'scenario':
					this.addChild(
						new MenuItem({
							label : graphStore.get(nodeId).name,
							'class' : 'authorityGridTitleItem'
						})
					);
					this.addChild(
						new MenuItem(
						{
							label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_edit_scenario'),
							onClick : lang.hitch(this, this.requestScenarioEdition, nodeId)
						})
					);
					this.addChild(
						new MenuItem({
							label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_copy'),
							onClick : lang.hitch(this, this.copyNode, nodeId),
							disabled : !graphStore.canCopy(nodeId)
						})
					);

					if (this.areaList.total > 1) {
						this.addChild(
							new MenuItem({
								label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_paste_to_other_contribution_area'),
								onClick : lang.hitch(this, this.pasteToOtherArea, nodeId),
							})
						);
					}
					this.addChild(
						new MenuItem({
							label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_paste'),
							onClick : lang.hitch(this, this.pasteNode, nodeId),
							disabled : !graphStore.canPaste(nodeId)
						})
					);
					this.addChild(
						new MenuItem({
							label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_paste_and_adapt'),
							onClick : lang.hitch(this, this.pasteAndAdaptNode, nodeId),
							disabled : !graphStore.canPaste(nodeId)
						})
					);
					this.addChild(
						new MenuItem(
						{
							label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_remove'),
							onClick : lang.hitch(this, this.requestRemoveNode, nodeId),
							disabled : graphStore.hasChildren(nodeId)
						})
					);

					if (this.areaList.total > 1 && (this.clipboardId && !graphStore.clipboardAlreadyPaste)) {
					    this.addChild(
					        new MenuSeparator()
					    );
					    this.addChild(
					        new MenuItem({
					            label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_paste_clipboard'),
					            onClick : lang.hitch(this, this.pasteNodeClipboard, {"nodeId": nodeId, "clipboardId":this.clipboardId}),
					        })
					    );
					}
					break;
				case 'form':
					this.addChild(
						new MenuItem({
							label : graphStore.get(nodeId).name,
							'class' : 'authorityGridTitleItem'
						})
					);
					this.addChild(
						new MenuItem({
							label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_edit_form'),
							onClick : lang.hitch(this, this.requestFormEdition,nodeId)
						})
					);

					if (this.areaList.total > 1) {
						this.addChild(
							new MenuItem({
								label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_paste_to_other_contribution_area'),
								onClick : lang.hitch(this, this.pasteToOtherArea, nodeId),
							})
						);
					}
					this.addChild(
						new MenuItem({
							label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_copy'),
							onClick : lang.hitch(this, this.copyNode, nodeId),
							disabled : !graphStore.canCopy(nodeId)
						})
					);
					this.addChild(
						new MenuItem({
							label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_paste'),
							onClick : lang.hitch(this, this.pasteNode, nodeId),
							disabled : !graphStore.canPaste(nodeId)
						})
					);
					this.addChild(
						new MenuItem({
							label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_paste_and_adapt'),
							onClick : lang.hitch(this, this.pasteAndAdaptNode, nodeId),
							disabled : !graphStore.canPaste(nodeId)
						})
					);
					this.addChild(
						new MenuItem(
						{
							label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_remove'),
							onClick : lang.hitch(this, this.requestRemoveNode, nodeId),
							disabled : graphStore.hasChildren(nodeId)
						})
					);
					
					if (this.areaList.total > 1 && (this.clipboardId && !graphStore.clipboardAlreadyPaste) ) {
					    this.addChild(
					        new MenuSeparator()
					    );
					    this.addChild(
					        new MenuItem({
					            label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_paste_clipboard'),
					            onClick : lang.hitch(this, this.pasteNodeClipboard, {"nodeId": nodeId, "clipboardId":this.clipboardId}),
					        })
					    );
					}
					break;
				case "attachment" :
					this.addChild(
						new MenuItem({
							label : graphStore.get(nodeId).name,
							'class' : 'authorityGridTitleItem'
						})
					);
					this.addChild(
						new MenuItem({
							label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_editing_attachment'),
							onClick : lang.hitch(this, this.requestAttachmentEdition,nodeId)
						})
					);
					this.addChild(
							new MenuItem({
								label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_paste'),
								onClick : lang.hitch(this, this.pasteNode, nodeId),
								disabled : !graphStore.canPaste(nodeId)
							})
					);
					this.addChild(
							new MenuItem({
								label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_paste_and_adapt'),
								onClick : lang.hitch(this, this.pasteAndAdaptNode, nodeId),
								disabled : !graphStore.canPaste(nodeId)
							})
					);
					
					if (this.areaList.total > 1 && (this.clipboardId && !graphStore.clipboardAlreadyPaste) ) {
						this.addChild(
								new MenuSeparator()
						);
						this.addChild(
								new MenuItem({
									label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_paste_clipboard'),
									onClick : lang.hitch(this, this.pasteNodeClipboard, {"nodeId": nodeId, "clipboardId":this.clipboardId}),
								})
						);
					}
					break;
				default:
					this.addChild(
						new MenuItem(
						{
							label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_create_scenario'),
							onClick : lang.hitch(this, this.requestScenarioCreation,nodeId)
						})
					);
					this.addChild(
						new MenuItem({
							label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_paste'),
							onClick : lang.hitch(this, this.pasteNode, nodeId),
							disabled : !graphStore.canPaste(nodeId)
						})
					);
					this.addChild(
						new MenuItem({
							label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_paste_and_adapt'),
							onClick : lang.hitch(this, this.pasteAndAdaptNode, nodeId),
							disabled : !graphStore.canPaste(nodeId)
						})
					);
					
					if (this.areaList.total > 1 && (this.clipboardId && !graphStore.clipboardAlreadyPaste) ) {
					    this.addChild(
					        new MenuSeparator()
					    );
					    this.addChild(
					        new MenuItem({
					            label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_paste_clipboard'),
					            onClick : lang.hitch(this, this.pasteNodeClipboard, {"nodeId": nodeId, "clipboardId":this.clipboardId}),
					        })
					    );
					}
					break;
			}
		},
		
		getMenuList : function(nodeClicked, nodeId){
			let nodeIdSplit = nodeId.split('_')[1];
			switch (this.contextType) {
				case 'scenarioList':
					this.addChild(
						new MenuItem({
							label : nodeClicked.textContent,
							'class' : 'authorityGridTitleItem'
						})
					);
					
					this.addChild(
						new MenuItem(
						{
							label : pmbDojo.messages.getMessage('contribution_area', 'contribution_area_delete'),
							onClick : lang.hitch(this, this.deleteScenario, nodeIdSplit),
							disabled : !graphStore.canDeleteScenario(nodeIdSplit)
						})
					);
				
					break;
				default:
					this.getMenuGraph(nodeClicked, nodeId);
					break;
			}
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
		
		deleteScenario: function(nodeId) {
			topic.publish('SvgContextMenu', 'deleteScenario', nodeId);
		},
		
		copyNode : function(nodeId) {
			topic.publish('SvgContextMenu', 'setSelectedNode', nodeId);
		},
		
		pasteNode : function(nodeId) {
			graphStore.isPasteAndApdate = false;
			topic.publish('SvgContextMenu', 'pasteSelectedNode', nodeId);
		},
		
		pasteAndAdaptNode : function(nodeId) {
			graphStore.isPasteAndApdate = true;
			topic.publish('SvgContextMenu', 'pasteSelectedNode', nodeId);
		},
		
		pasteToOtherArea : function(nodeId) {
			topic.publish('SvgContextMenu', 'pasteToArea', nodeId);
		},

		pasteNodeClipboard : function(params) {
			topic.publish('SvgContextMenu', 'pasteNodeClipboard', params);
		},

		getAreaList : function() {
			if (!window.areaList) {
	 			var request = xhr.post("./ajax.php?module=modelling&categ=contribution_area&sub=area&action=list");
				request.then(lang.hitch(this, function(response) {
					this.areaList = JSON.parse(response)
					window.areaList = this.areaList;
				}))
			} else {
				this.areaList = window.areaList;
			}
			return this.areaList;
		}
	});
});