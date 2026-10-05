// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EntitySelector.js,v 1.6 2018/03/19 14:49:42 vtouchard Exp $

define(["dojo/_base/declare",  
        "dojo/dom-construct", 
        "dojo/_base/lang",
        "dijit/form/Select",
        "dojo/dom-attr",
        "dojo/dom",
        "dijit/layout/ContentPane",
        "dojo/dom-style",
        "dojo/request/xhr",
        ], function(declare, domConstruct, lang, Select, domAttr, dom, ContentPane, domStyle, xhr){	
	return declare(null, {
		menuCreated: 0,
		selectedValue: null,
		constructor: function(){

		},
		createMenu: function(callbackSelector, selectedValue){
			xhr("./ajax.php?module=autorites&categ=get_auth_persos", {
				handleAs: "json"
			}).then((data) => {
				var itemsList = [
				                 	{value: '', label : pmbDojo.messages.getMessage('frbr', 'frbr_cataloging_choice')},
			                    	{value: './ajax.php?module=selectors&what=notice&tab=frbr&action=add', label : pmbDojo.messages.getMessage('frbr', 'frbr_cataloging_record')}, 
			                    	{value: './ajax.php?module=selectors&what=auteur&tab=frbr&action=add' , label :  pmbDojo.messages.getMessage('frbr', 'frbr_cataloging_author')}, 
			                    	{value: './ajax.php?module=selectors&what=categorie&tab=frbr&action=add' , label :  pmbDojo.messages.getMessage('frbr', 'frbr_cataloging_category')}, 
		                    		{value: './ajax.php?module=selectors&what=editeur&tab=frbr&action=add' , label :  pmbDojo.messages.getMessage('frbr', 'frbr_cataloging_publisher')}, 
		                    		{value: './ajax.php?module=selectors&what=collection&tab=frbr&action=add' , label :  pmbDojo.messages.getMessage('frbr', 'frbr_cataloging_collection')}, 
	                    			{value: './ajax.php?module=selectors&what=subcollection&tab=frbr&action=add' , label :  pmbDojo.messages.getMessage('frbr', 'frbr_cataloging_subcollection')}, 
	                    			{value: './ajax.php?module=selectors&what=serie&tab=frbr&action=add' , label :  pmbDojo.messages.getMessage('frbr', 'frbr_cataloging_serie')}, 
	                				{value: './ajax.php?module=selectors&what=titre_uniforme&tab=frbr&action=add' , label :  pmbDojo.messages.getMessage('frbr', 'frbr_cataloging_work')}, 
	                				{value: './ajax.php?module=selectors&what=indexint&tab=frbr&action=add' , label :  pmbDojo.messages.getMessage('frbr', 'frbr_cataloging_indexint')},
	            					{value: './ajax.php?module=selectors&what=ontology&element=concept&action=add' , label :  pmbDojo.messages.getMessage('frbr', 'frbr_cataloging_concept')},
			                   	];
				if(data){
					for(var i in data){
						itemsList.push({value: './ajax.php?module=selectors&what=authperso&tab=frbr&dyn=4&perso_id='+data[i].id+'&authperso_id='+data[i].id+'&action=add', label:data[i].name});
					}
				}
				
				
				this.selector = new Select({
					name: 'entity_selector',
					options:itemsList,
					onChange: lang.hitch(this, callbackSelector),
					value: selectedValue ? selectedValue : '',
				});
				this.selectedValue = selectedValue;
				domConstruct.create('div', {id: "containerNode"}, this.containerNode);
				this.selector.placeAt(this.selectorContainer, 'first');
				
				if (selectedValue) {
					lang.hitch(this, callbackSelector);
				}	
			});
			
			
		},
		createSelector: function(callbackSelector, selectedValue){
			var selectorContainerDiv = domConstruct.create('div', {}, this.containerNode, 'first');
//			this.containerNode
			var selectorContainer = new ContentPane({
				style: 'height:5%; overflow: hidden;',
				id: this.id+'_selector_container',
				
			}, selectorContainerDiv);
			selectorContainer.startup();
			this.selectorContainer = selectorContainer;
			this.createMenu(callbackSelector, selectedValue);
		},
		getAuthPerso : function () {
			
		},

	});
});

