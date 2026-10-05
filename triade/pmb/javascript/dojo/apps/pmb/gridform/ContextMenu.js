// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ContextMenu.js,v 1.7 2021/06/02 07:44:47 dgoron Exp $


define(['dojo/_base/declare', 
        'dojo/_base/lang', 
        'dojo/topic', 
        'dijit/Menu', 
        'dijit/MenuItem', 
        'dojo/dom-attr', 
        'dojo/_base/window', 
        'dojo/dom', 
        'dojo/on', 
        'apps/pmb/gridform/PopupZone', 
        'dojo/query', 
        'dijit/PopupMenuItem'], 
        function(declare, lang, topic, menu, menuItem, domAttr, win, dom, on, PopupZone, query, popupMenuItem){

	  return declare([menu], {
		  popupZone:null,
		  zone:null,
		  elementIdClicked:null,
		  elementLabelClicked:null,
		  constructor:function(params){
			  this.leftClickToOpen = true;
			  this.zone = params.zone;
			  //console.log('createdContextMenu', this);
		  },
		  handleEvents: function(evtClass, evtType, evtArgs){
			  switch(evtClass){
			  case 'FormEdit':
				  switch(evtType){
				  
				  }
				  break;
			  case 'Zone':
				  switch(evtType){
				  
				  }
				  break;
			  }
		  },
		  postCreate: function(){
			  this.inherited(arguments);
		  },
		  bindDomNode: function(/*String|DomNode*/ node){
			    var callbackFind = lang.hitch(this, this.findMovableElt);
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
							if(!evt.ctrlKey && !evt.metaKey){
								return false;
							}
							callbackFind(evt.target);
							callbackBuildMenu();
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

			unBindDomNode: function(/*String|DomNode*/ nodeName){
				var node;
				try{
					node = dom.byId(nodeName, this.ownerDocument);
				}catch(e){

					return;
				}

				var attrName = "_dijitMenu" + this.id;
				if(node && domAttr.has(node, attrName)){
					var bid = domAttr.get(node, attrName) - 1, b = this._bindings[bid], h;
					while((h = b.connects.pop())){
						h.remove();
					}
					var iframe = b.iframe;
					if(iframe){
						if(iframe.removeEventListener){
							iframe.removeEventListener("load", b.onloadHandler, false);
						}else{
							iframe.detachEvent("onload", b.onloadHandler);
						}
					}

					domAttr.remove(node, attrName);
					delete this._bindings[bid];
				}
			},
			findMovableElt: function(elt){
				var element = elt;
				do{
					if(element.getAttribute('movable') || element.getAttribute('etirable')){
						break;
					}
					element = element.parentNode;
				}while(element.parentNode);
				if(element.getAttribute('movable')) {
					this.elementIdClicked = element.getAttribute('id');
					this.elementLabelClicked = element.getAttribute('title') ? element.getAttribute('title') : 'Sans Titre';
				}
			},
			buildMenu: function(){
				this.removeChilds(); //Purge avant creation
				if(this.elementIdClicked) {
					if(this.zone.getElements().length){
						this.buildEltMenu();
						this.buildCurrentZoneMenu(false);
					}else{
						this.buildCurrentZoneMenu(true);
					}
					
				}else{
					this.buildCurrentZoneMenu(true);
				}
				
				this.buildZoneMenu();
			},
			buildZoneMenu: function(){
					var generalZoneMenu = new menu();
					generalZoneMenu.addChild(new menuItem({
						label: pmbDojo.messages.getMessage('grid', 'grid_js_move_create_zone'),
				        onClick: lang.hitch(this, this.createPopupZone),
					}));
					var hiddenZones = this.zone.parent.getHiddenZones();
					if(hiddenZones.length){
						var tabHiddenZones = new menu();
						generalZoneMenu.addChild(
							new popupMenuItem({
								popup: tabHiddenZones,
								label: pmbDojo.messages.getMessage('grid', 'grid_js_move_visible_zone'),
								'class':'authorityGridMainItem'
							})
						);
		  				for(var i=0 ; i<hiddenZones.length ; i++){
		  					tabHiddenZones.addChild(
	  							new menuItem({
	  								label: hiddenZones[i].label,
	  								onClick: lang.hitch(this, this.makeVisibleZone,hiddenZones[i].nodeId),
	  							})	
		  					);
		  				}
					}
					this.addChild(
						new popupMenuItem({
							popup: generalZoneMenu,
							label: pmbDojo.messages.getMessage('grid', 'grid_js_move_zones'),
							'class':'authorityGridMainItem',
						})
					);
					this.addChild(new menuItem({
				        label: pmbDojo.messages.getMessage('grid', 'grid_js_general_menu'),
				        'class':'authorityGridTitleItem'
					}));
					
					
					
					/***
					 * If pivots ajouter sauver pour tout les pivots
					 */
					if(query('select[backbone="yes"]').length){
						var saveMenu = new menu();
						saveMenu.addChild(new menuItem({
					        label: pmbDojo.messages.getMessage('grid', 'grid_js_move_save'),
					        onClick: lang.hitch(this, this.saveAll)
						}));
						saveMenu.addChild(new menuItem({
					        label: pmbDojo.messages.getMessage('grid', 'grid_js_save_backbone'),
					        onClick: lang.hitch(this, this.saveAllBackbones)
						}));
						this.addChild(
							new popupMenuItem({
								popup: saveMenu,
								label: pmbDojo.messages.getMessage('grid', 'grid_js_move_save'),
								'class':'authorityGridMainItem',
							})
						);
					}else{
						this.addChild(new menuItem({
					        label: pmbDojo.messages.getMessage('grid', 'grid_js_move_save'),
					        onClick: lang.hitch(this, this.saveAll)
						}));
					}
			},
			buildCurrentZoneMenu: function(isMain){
				var isMain = (isMain)?isMain:false;
				var bindMenu = (isMain)?this:(new menu());
				if(!isMain){
					this.addChild(
						new popupMenuItem({
							popup: bindMenu,
							label: this.zone.label,
							'class':'authorityGridMainItem'
						})
					);
				}else{
					this.addChild(new menuItem({
				        label: this.zone.label,
				        'class':'authorityGridMainItem'
					}));
				}
				var zones = this.zone.parent.getZones();
				var indexZone = zones.indexOf(this.zone);
				bindMenu.addChild(new menuItem({
			        label: pmbDojo.messages.getMessage('grid', 'grid_js_move_up_zone'),
			        onClick: lang.hitch(this, this.upZone),
			        disabled:(indexZone == 0 ? true : false)
				}));
				bindMenu.addChild(new menuItem({
			        label: pmbDojo.messages.getMessage('grid', 'grid_js_move_down_zone'),
			        onClick: lang.hitch(this, this.downZone),
			        disabled:(indexZone == (zones.length-1) ? true : false)
				}));
				bindMenu.addChild(new menuItem({
					label: pmbDojo.messages.getMessage('grid', 'grid_js_move_invisible_zone'),
			        onClick: lang.hitch(this, this.makeInvisibleZone)
				}));
				bindMenu.addChild(new menuItem({
					label: pmbDojo.messages.getMessage('grid', 'grid_js_move_edit_zone'),
			        onClick: lang.hitch(this, this.editPopupZone),
				}));
				
				bindMenu.addChild(new menuItem({
			        label: pmbDojo.messages.getMessage('grid', 'grid_js_move_delete_zone'),
			        onClick: lang.hitch(this, this.deleteZone),
			        disabled:(this.zone.elements.length ? true : false)
				}));
				
				
				var hiddenElements = this.zone.getHiddenElements();
				if(hiddenElements.length){
					var tabMakeElementsVisible = new menu();
					bindMenu.addChild(
						new popupMenuItem({
							popup: tabMakeElementsVisible,
							label: pmbDojo.messages.getMessage('grid', 'grid_js_move_visible'),
							'class':'authorityGridMainItem'
						})
					);
	  				for(var i=0 ; i<hiddenElements.length ; i++){
	  					tabMakeElementsVisible.addChild(
  							new menuItem({
  								label: hiddenElements[i].nodeLabel,
  								onClick: lang.hitch(this, this.makeVisibleElement,hiddenElements[i].nodeId),
  							})	
	  					);
	  				}
	  				
	  				var tabHiddenElements = new menu();
	  				bindMenu.addChild(
						new popupMenuItem({
							popup: tabHiddenElements,
							label: pmbDojo.messages.getMessage('grid', 'grid_js_move_context_menu_hidden_fields'),
							'class':'authorityGridMainItem'
						})
					);
					var arrayDisabled = new Array();
  					var arrayEnabled = new Array();
	  				for(var i=0 ; i<hiddenElements.length ; i++){
	  					if(hiddenElements[i].isDisabled){
	  						arrayDisabled.push(hiddenElements[i]);
	  					}else{
	  						arrayEnabled.push(hiddenElements[i]);
	  					}
	  				}
	  				/**
	  				 * TODO: 
	  				 * Tester la length du tableau des �l�ments actifs
	  				 * Si > 0  --> S�parateur "Actif"
	  				 * Puis for sur les elts pour les ajouter avec le truc "d�sactiver" en sous menu
	  				 * 
	  				 * Tester la length du tableau des �l�ments inactifs
	  				 * Si > 0 --> S�parateur "Inactif"
	  				 * Puis for sur les elts pour les ajouter avec le truc "activer" en sous menu
	  				 */
	  				if(arrayEnabled.length){
	  					tabHiddenElements.addChild(
	  						new menuItem({
								label:  pmbDojo.messages.getMessage('grid', 'grid_js_move_context_menu_active_fields'),
								'class':'authorityGridMainItem'
							})
	  					);
	  					for(var i=0 ; i<arrayEnabled.length ; i++){
	  						var disablePopup = new menu();
	  						disablePopup.addChild(new menuItem({
								label: pmbDojo.messages.getMessage('grid', 'grid_js_move_context_menu_deactivate_field'),
								onClick: lang.hitch(this, this.disableElement,arrayEnabled[i].nodeId),
							}));
	  						var menuItemEnabled = new popupMenuItem({
	  							label: arrayEnabled[i].nodeLabel,
	  							popup:disablePopup
							});
	  						tabHiddenElements.addChild(
  		  						menuItemEnabled
  		  					);
	  					}
	  				}
	  				if(arrayDisabled.length){
	  					tabHiddenElements.addChild(
	  						new menuItem({
	  							label:  pmbDojo.messages.getMessage('grid', 'grid_js_move_context_menu_inactive_fields'),
								'class':'authorityGridMainItem'
							})
	  					);
	  					for(var i=0 ; i<arrayDisabled.length ; i++){
	  						var enablePopup = new menu();
	  						enablePopup.addChild(new menuItem({
								label: pmbDojo.messages.getMessage('grid', 'grid_js_move_context_menu_activate_field'),
								onClick: lang.hitch(this, this.enableElement, arrayDisabled[i].nodeId),
							}));
	  						
	  						var menuItemDisabled = new popupMenuItem({
	  							label: arrayDisabled[i].nodeLabel,
	  							popup:enablePopup
							});
	  						
	  						tabHiddenElements.addChild(
  								menuItemDisabled
  		  					);
	  					}
	  				}
				}
				
				
			},
			buildEltMenu: function(){
				var elements = this.zone.getElements();
				var indexElement =0;
				for(var i=0; i<elements.length; i++){
					if(elements[i].nodeId == this.elementIdClicked) {
						indexElement = i;
						break;
					}
				}
				var nbEltsContained = elements[indexElement].domNode.parentNode.querySelectorAll('div[movable="yes"]').length;
				
				this.addChild(
					new menuItem({
						label: this.elementLabelClicked,
						'class':'authorityGridTitleItem'
					})
				);
				/**
				 * TODO ajouter les conditions sur les containers 
				 */
				this.addChild(new menuItem({
			        label: pmbDojo.messages.getMessage('grid', 'grid_js_move_first_plan'),
			        onClick: lang.hitch(this, this.goFirstElement,elements[indexElement]),
			        disabled:((indexElement>0||nbEltsContained>1) == 0 ? true : false)
				}));
				this.addChild(new menuItem({
					label: pmbDojo.messages.getMessage('grid', 'grid_js_move_up'),
			        onClick: lang.hitch(this, this.upElement),
			        disabled:((indexElement>0||nbEltsContained>1) == 0 ? true : false)
				}));
				this.addChild(new menuItem({
			        label: pmbDojo.messages.getMessage('grid', 'grid_js_move_down'),
			        onClick: lang.hitch(this, this.downElement),
			        disabled:(((indexElement == (elements.length-1))&&(nbEltsContained==1)) ? true : false)
				}));
				this.addChild(new menuItem({
			        label: pmbDojo.messages.getMessage('grid', 'grid_js_move_last'),
			        onClick: lang.hitch(this, this.goLastElement),
			        disabled:(((indexElement == (elements.length-1))&&(nbEltsContained==1)) ? true : false)
				}));
				this.addChild(new menuItem({
					label: pmbDojo.messages.getMessage('grid', 'grid_js_move_invisible'),
					onClick: lang.hitch(this, this.makeInvisibleElement)
					
				}));
				
				var tabSubMenu = new menu();

				var zones = query('div[etirable="yes"]');
				for(var i=0; i < zones.length; i++) {
					if(zones[i].getAttribute('label') == null) {
						if(zones[i].getAttribute('title') != null) {
							zones[i].setAttribute('label', zones[i].getAttribute('title'));
						}
					}
					if(this.zone.label != zones[i].getAttribute('label')){
						tabSubMenu.addChild(new menuItem({
							label: zones[i].getAttribute('label'),
							onClick: lang.hitch(this, this.changeZone,this.zone.nodeId,zones[i].getAttribute('id').replace('Child', '')),
						}));  
					}
				}
				if(zones.length > 1){
					this.addChild(
						new popupMenuItem({
							popup: tabSubMenu,
							label: pmbDojo.messages.getMessage('grid', 'grid_js_move_inside_zone')
						})
					);
				}
			},
			onBlur: function() {
				this.removeChilds();
			},
			removeChilds: function(){
				var childs = this.getChildren();
				for(var i=0 ; i<childs.length ; i++){
					this.removeChild(childs[i]);
				}
			},
			createPopupZone: function(){
				this.popupZone = new PopupZone({zoneId:this.zone.nodeId,mode: 'create'});
				this.popupZone.show();
			},
			editPopupZone: function(){
				this.popupZone = new PopupZone({label:this.zone.label, zoneId:this.zone.nodeId, mode:'edit', isExpandable:this.zone.isExpandable, showLabel:this.zone.showLabel});
				this.popupZone.show();
			},
			deleteZone: function(){
				if(confirm(pmbDojo.messages.getMessage('grid', 'grid_js_move_context_menu_confirm_delete'))) {
					topic.publish('ContextMenu', 'deleteZone', {
						nodeId: this.zone.nodeId
					});
				}
			},
			upZone: function(){
				topic.publish('ContextMenu', 'upZone', {
					  nodeId: this.zone.nodeId
				  });
			},
			downZone: function(){
				topic.publish('ContextMenu', 'downZone', {
					nodeId: this.zone.nodeId
				  });
			},
			makeInvisibleZone: function(){
				topic.publish('ContextMenu', 'makeInvisibleZone', {
					nodeId: this.zone.nodeId
				  });
			},
			/**
			 * TODO *****************
			 * Rappeler le ondrop d'un elt g�n�r� � la vol�e
			 */
			goFirstElement: function(){
				topic.publish('ContextMenu', 'goFirstElement', {
					id: this.elementIdClicked
				});
			},
			upElement: function(){
				topic.publish('ContextMenu', 'upElement', {
					  id: this.elementIdClicked
				  });
			},
			downElement: function(){
				topic.publish('ContextMenu', 'downElement', {
					id: this.elementIdClicked
				  });
			},
			goLastElement: function(){
				topic.publish('ContextMenu', 'goLastElement', {
					id: this.elementIdClicked
				  });
			},
			makeInvisibleElement: function(){
				topic.publish('ContextMenu', 'makeInvisibleElement', {
					id: this.elementIdClicked
				  });
			},
			makeVisibleElement: function(nodeId){
				topic.publish('ContextMenu', 'makeVisibleElement', {
					id: nodeId
				  });
			},
			changeZone: function(zoneId, moveToZoneId){
				topic.publish('ContextMenu', 'changeZone', {
					id: this.elementIdClicked,
					zoneId:zoneId,
					moveToZoneId:moveToZoneId
				  });
			},
			makeVisibleZone: function(nodeId){
				topic.publish('ContextMenu', 'makeVisibleZone', {
					nodeId: nodeId
				  });
			},
			saveAll: function(){
				if(confirm(pmbDojo.messages.getMessage('grid', 'grid_js_move_context_menu_confirm_save'))) {
					topic.publish('ContextMenu', 'saveAll', {});
				}
			},
			saveAllBackbones: function(){
				if(confirm(pmbDojo.messages.getMessage('grid', 'grid_js_move_context_menu_confirm_save')+'\n'+pmbDojo.messages.getMessage('grid', 'grid_js_save_backbone_warning_confirm'))) {
					topic.publish('ContextMenu', 'saveAllBackbones', {});
				}
			},
			disableElement: function(nodeId){
				topic.publish('ContextMenu', 'disableElement', {
					id: nodeId
				});
			},
			enableElement: function(nodeId){
				topic.publish('ContextMenu', 'enableElement', {
					id: nodeId
				});
			},
	  });
});