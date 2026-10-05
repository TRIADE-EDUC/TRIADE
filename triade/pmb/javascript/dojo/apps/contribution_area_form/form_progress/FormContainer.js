// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormContainer.js,v 1.2 2021/06/30 13:44:38 gneveu Exp $


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
        'apps/contribution_area_form/Contribution',
        'dojo/ready',
        'apps/contribution_area/ContributionFormEdit',
        'dojo/topic',
        'dijit/registry',
        'dojo/dom-attr',
        'dojo/dom-geometry',
        'dojo/dom-construct',
        'dojo/dom-style'
        ], function(declare, dom, on, lang, xhr, domForm, TabContainer, ContentPane, query, Contribution, ready, ContributionFormEdit, topic, registry, domAttr, geometry, domConstruct, domStyle){
		return declare([TabContainer], {
			standby : null,
			overlayDiv: null,
			rightArrow: ' <i class="fa fa-arrow-circle-right"></i>',
			'class': "contributionFormContainer",
			constructor: function() {
				topic.subscribe('Contribution', lang.hitch(this, this.handleEvents))
			},
			handleEvents: function(evtType,evtArgs){
				switch(evtType){
					case 'savedForm':
						this.fillField(evtArgs);
						this.closeTab(evtArgs.widgetId, evtArgs.response.id);
						break;
						
				}
			},
			postCreate: function() {
				this.inherited(arguments);
				ready(lang.hitch(this,this.parseTab));
			},
			
			formClicked:function(widget){
				var formURL = widget.get("data-form_url"); 
				var formTitle = widget.get("data-form_title");
				
				var formType = 'contribution_area_form_';
				var formId = /form_id=(\w+)&?/g.exec(formURL)[1];
				formType += formId;
				var newTab = new ContentPane({title:formTitle, href:formURL, closable:true, nodeClickedId: this.fillIdFinder(widget.get('id')), preload : true});
				newTab.set({onDownloadEnd : lang.hitch(this, this.parseTab, newTab.id, formType)});
				this.addChild(newTab);
				this.selectChild(newTab);
				
				this.setClosableTab();				
			},
			parseTab: function(tabId, formType){
				if (tabId) {
					var nodes = query("span[data-form_url][role='button']", tabId);
					new Contribution(tabId);
				}else{
					var nodes = query("span[data-form_url][role='button']", this.getChildren()[0].id);
					new Contribution(this.getChildren()[0].id);
				}
				
				nodes.forEach(lang.hitch(this,function(node){
					var myWidget = registry.byId(node.id);	
					if (myWidget) {
						on(myWidget,"click", lang.hitch(this, this.formClicked, myWidget));
					}	
				}));
				
				new ContributionFormEdit('catalog', formType, dom.byId(tabId));				
				if (tabId) {	
					var cancel_button = query(".cancel_part > *", dom.byId(tabId))[0];
					if(cancel_button) {
						cancel_button.onclick = '';
						on(cancel_button, "click", lang.hitch(this, this.closeTab, tabId));
					}
				}				
				this.resize();
			},
			closeTab: function(tabId, id){
				var child = registry.byId(tabId);
				this.removeChild(child, id);
			},
			fillIdFinder: function(originalOne){
				var baseId = originalOne.split('_sel')[0]; //Id du bouton (...)
				var nodeList = query('input[id*="'+baseId+'"][type="text"]'); //Noeuds dom correspondants aux champs texte associés au bouton
				var nodeToFill = nodeList[(nodeList.length)-1]; // On récupère le dernier créer (dernier dans la liste de résultat)
				var splittedId = nodeToFill.getAttribute('id').split('_display_label')[0];//On split cet id pour récupérer la chaine de base (à terme pour valoriser type / value)
				return splittedId;
			},
			fillField: function(data){
				//Contenu de data.data: array("uri" => $this->item->get_uri(), "displayLabel" => $display_label)
				var nodeToFill = registry.byId(data.widgetId).nodeClickedId;
				if (nodeToFill) {				
					var displayLabel = registry.byId(nodeToFill+'_display_label');
					if (displayLabel) {
						displayLabel.store.addData([{id : data.response.displayLabel, datas : data.response.displayLabel, value : data.response.uri}]);
						displayLabel.set("item", {id:data.response.displayLabel, datas:data.response.displayLabel, value:data.response.uri});
					}
					domAttr.set(nodeToFill+'_display_label', 'value', data.response.displayLabel);
					domAttr.set(nodeToFill+'_value', 'value', data.response.uri);
				}
			},
			
			removeChild : function(page, id) {
				this.inherited(arguments);
				this.setClosableTab();
				if (this.getChildren().length == 0) {
					window.location.href = "./catalog.php?categ=contribution_area&action=list";
				}
			},
			
			setClosableTab : function() {
				this.getChildren().forEach(lang.hitch(this, function(tab){
					tab.set('title', tab.get('title').replace(this.rightArrow, ''));
					if (this.getIndexOfChild(tab) != (this.getChildren().length -1)) {
						tab.set({closable : false});
						if(this.getChildren().length > 1){
							this.applyOverlay(tab);
							tab.set('title', tab.get('title') + this.rightArrow);
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
			}
			
		})
});