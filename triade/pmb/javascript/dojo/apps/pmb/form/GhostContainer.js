// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: GhostContainer.js,v 1.3 2017-10-11 15:18:56 vtouchard Exp $


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
        'dijit/layout/BorderContainer',
        'dojox/layout/ContentPane',
        'apps/pmb/form/FormSelector',
        'dojo/io-query'
        ], function(declare, dom, on, lang, xhr, domForm, TabContainer, ContentPane, query, ready, topic, registry, domAttr, 
        		geometry, domConstruct, domStyle, LayoutContainer, BorderContainer, ContentPaneDojox, FormSelector, ioQuery){
		return declare([ContentPane], {
			
			constructor: function(data) {
				this.parameters = data;
				this.own(topic.subscribe('SubTabAdd', lang.hitch(this, this.handleEvents)));
			},
			handleEvents: function(evtClass, evtType, evtArgs){
			  switch(evtClass){
			  	case 'SubTabAdd':
					switch(evtType){
						case 'elementAdded':
							if(evtArgs.ghostContainerId == this.id){
								this.fillField(evtArgs);	
							}
							break;
					}
					break;
			  }
			},
			postCreate: function() {
				this.inherited(arguments);
				this.createGhost();
				
			},
			getFormNode: function(){
				var input = document.querySelector('input');
				return input.form;
				
			},
			createGhost: function(){
				var elementSize = geometry.getContentBox(this.parameters.field);
				
				this.ghost = domConstruct.create('div', {
					innerHTML: '<span>&nbsp;</span>',
					id: "fieldContainer",
					style: {
						width: elementSize.w+'px',
						height: elementSize.h+'px',
						backgroundColor: 'grey',
					},
				}, this.parameters.field, 'after');
				domConstruct.place(this.parameters.field, this.containerNode, 'last');
			},
			destroy: function(){
				var form = this.getFormNode();
				domConstruct.place(this.parameters.field, this.ghost, 'after');
				domConstruct.destroy(this.ghost);
				this.inherited(arguments);
			},
			fillField: function(evtData){
				var selectorURL = this.parameters.selectorURL.substring(this.parameters.selectorURL.indexOf("?") + 1, this.parameters.selectorURL.length);
			  	var queryObject = ioQuery.queryToObject(selectorURL);
			  	
			  	if(queryObject.param1 && queryObject.param2){
			  		var idContainer = query('input[name="'+queryObject.param1+'"]')[0];
			  		var labelContainer = query('input[name="'+queryObject.param2+'"]')[0];
			  	}else if(queryObject.p1 && queryObject.p2){
			  		var idContainer = query('input[name="'+queryObject.p1+'"]')[0];
			  		var labelContainer = query('input[name="'+queryObject.p2+'"]')[0];
			  	}

			  	domAttr.set(idContainer, 'value', evtData.id);
			  	domAttr.set(labelContainer, 'value', evtData.isbd);
			},
		})
});