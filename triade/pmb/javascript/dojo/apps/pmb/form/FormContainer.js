// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormContainer.js,v 1.6 2021/01/05 14:45:21 gneveu Exp $


define([
        'dojo/_base/declare',
        'dojo/dom',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/request/xhr',
        'dojo/dom-form',
        'dijit/layout/TabContainer',
        'dijit/layout/ContentPane',
        'dojo/query',
        'dojo/ready',
        'dojo/topic',
        'dijit/registry',
        'dojo/dom-attr',
        'dojo/dom-geometry',
        'dojo/dom-construct',
        'dojo/dom-style',
        'dijit/layout/LayoutContainer',
        'apps/pmb/form/FormTab'
        ], function(declare, dom, on, lang, xhr, domForm, TabContainer, ContentPane, query, ready, topic, registry, domAttr, geometry, domConstruct, domStyle, LayoutContainer, FormTab){
		return declare([TabContainer], {
			standby : null,
			overlayDiv: null,
			constructor: function() {
				this.own(
					topic.subscribe('SelectorTab', lang.hitch(this, this.handleEvents)),
					topic.subscribe('GhostContainer', lang.hitch(this, this.handleEvents))
				);
			},
			handleEvents: function(evtClass, evtType, evtArgs){
				switch(evtClass){
					case 'SelectorTab':
						switch(evtType){
							case 'closeCurrentTab':
								this.closeChild(this.selectedChildWidget);
								break;
						}
						break;
					case 'GhostContainer':
						switch(evtType){
							case 'validatedSelection':
								this.closeChild(this.selectedChildWidget);
								break;
						}
						break;
				}
			},
			postCreate: function() {
				this.inherited(arguments);
			},
			
			formClicked:function(widget){

				/*var newTab = new ContentPane({title:formTitle, href:formURL, closable:true, nodeClickedId: this.fillIdFinder(widget.get('id')), preload : true});
				newTab.set({onDownloadEnd : lang.hitch(this, this.parseTab, newTab.id, formType)});
				this.addChild(newTab);
				this.selectChild(newTab);
				
				this.setClosableTab();*/				
			},
			removeChild : function(page, id) {
				this.inherited(arguments);
				if (this.getChildren().length == 1) {
					topic.publish('FormContainer', 'FormContainer', 'noMoreForms');
				}else{
					this.setClosableTab();	
				}
				
			},
			
			setClosableTab : function() {
				this.getChildren().forEach(lang.hitch(this, function(tab){
					//tab.set('title', tab.get('title').replace(this.rightArrow, ''));
					
					if (this.getIndexOfChild(tab) != (this.getChildren().length -1)) {
						tab.set({closable : false});
						if(this.getChildren().length > 1){
							this.applyOverlay(tab);
							//tab.set('title', tab.get('title') + this.rightArrow);
						}
					}else if(this.getIndexOfChild(tab) != 0){
						tab.set({closable : true});
					}else{
						this.removeOverlay();	
					}
				}));
				
			},
			
			selectChild : function(page,animate) {
				this.inherited(arguments);
				if(this.getIndexOfChild(page) != (this.getChildren().length -1)){
					this.applyOverlay(page);
				}else{
					this.removeOverlay();
				}
			},
			applyOverlay: function(widget) {
				var position = geometry.position(widget.domNode, true);
				if(!this.overlayDiv){
					this.overlayDiv = domConstruct.create('div', {
						id: 'overlayDiv', 
						style:{
							position: 'absolute',
							backgroundColor: 'grey',
							opacity: 0.2,
							zIndex : 1000,
							top: position.y+'px',
							left: position.x+'px',
							width: position.w+'px',
							height: position.h+'px',
							cursor: 'not-allowed'
						},
						innerHTML : '<span></span>'
					});
					document.body.appendChild(this.overlayDiv);
				}else{
					domStyle.set(this.overlayDiv, 'top', position.y+'px');
					domStyle.set(this.overlayDiv, 'left', position.x+'px');
					domStyle.set(this.overlayDiv, 'width', position.w+'px');
					domStyle.set(this.overlayDiv, 'height', position.h+'px');
				}
			},
			removeOverlay: function(){
				domConstruct.destroy('overlayDiv');
				this.overlayDiv = null;
			},
			addTab: function(evtData){
				//On récupére le champs complet (el*Child)
				var field = this.findFieldClicked(evtData.button);
				
				var tab = new FormTab({field: field, doLayout: false,style: 'width:100%; height:100%;', selectorURL: evtData.url});
				
				// On désactive le bouton pour éviter d'ouvrir plusieurs fois la même page
				evtData.button.setAttribute('disabled', 'true')
				evtData.button.classList.add("disabled")
				evtData.button.classList.add(tab.id)
				
				this.addChild(tab);
				this.setClosableTab();
				//Sélection du dernier onglet ajouté
				this.selectChild((this.getChildren()[this.getChildren().length-1]), true);
			},
			/**
			 * A remodifier selon la structure de levenement fournie
			 * 
			 * Le but ici est de récupérer le noeud sur lequel on a cliqué !
			 */
			findFieldClicked: function(node){
				while(!node.getAttribute('movable') || !node.getAttribute('title')){
					node = node.parentNode;
				}
				return node;
			},
			destroy: function(){
				this.removeOverlay();
				this.inherited(arguments);
			}
		})
});