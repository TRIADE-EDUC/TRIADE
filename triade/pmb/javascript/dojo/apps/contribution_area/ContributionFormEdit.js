// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ContributionFormEdit.js,v 1.4 2017-07-10 14:37:03 apetithomme Exp $

define(	[
	        'dojo/_base/declare', 
	        'dojo/_base/lang', 
	        'dojo/topic',
	        'apps/pmb/gridform/FormEdit',
	        'dojo/dom',
	        'dojo/dom-attr',
	        'dojo/query',
	        'dojo/dom-style',
	        'dojo/dom-construct'
    	], function(declare,lang, topic, FormEdit, dom, domAttr, query, domStyle, domConstruct){
			return declare(FormEdit, {
				initialized: null,
				constructor: function(){
					this.initialized = false;
					domConstruct.destroy(this.btnEdit);
				},
				saveCallback: function(response){
					if(response.status == true){
						if (this.btnSave) {
							var returnURL = dom.byId('return_url').value;
							if (returnURL) {
								window.location = returnURL; 
							}
						}
					}else{
						alert(pmbDojo.messages.getMessage('grid', 'grid_js_move_saved_error'));
					}
				},
				buildGrid: function(datas){
					this.inherited(arguments);
					if(!this.initialized){
						this.initializationCallback();	
					}
				},
				initializationCallback : function() {
					this.btnEditCallback();	
					this.initialized = true;
				},
				btnEditCallback: function(evt){
					switch(this.state){
						case 'std':
							this.state = 'inedit';
							var disableButtonsForm = query('#form-contenu input[type=button]');
							if(disableButtonsForm.length){
								for(var i=0; i<disableButtonsForm.length; i++){
									if(domAttr.get(disableButtonsForm[i], 'onclick')){
										domAttr.remove(disableButtonsForm[i],'onclick');										
									}
									domStyle.set(disableButtonsForm[i],'color','#aaa');
								}
							}
							this.parseDom();
							domAttr.set(this.btnEdit, 'value', pmbDojo.messages.getMessage('grid', 'grid_js_move_back'));
							break;
						case 'inedit':
							this.state = 'std';
							this.unparseDom();
							this.getDatas();
							domAttr.set(this.btnEdit, 'value', pmbDojo.messages.getMessage('grid', 'grid_js_move_edit_format'));
							break;
					}
				},
				removeJsDom: function(){
					  var cleanElts = query('#zone-container > div');
					  for(var i=0; i<this.originalFormat.length ; i++){
						  domConstruct.place(dom.byId(this.originalFormat[i].id), dom.byId('zone-container'), 'last');
						  dom.byId(this.originalFormat[i].id).className = this.originalFormat[i].class;
					  }

					  for(var i=0; i<cleanElts.length ; i++){
					  	if(cleanElts[i].getAttribute('movable') == null){
					  		domConstruct.destroy(cleanElts[i]);	
					  	}
					  }
					  this.zones = new Array();
					  if(this.btnEdit) this.signalEditFormat = on(this.btnEdit,'click', lang.hitch(this, this.btnEditCallback));
				}
			});
		});
	