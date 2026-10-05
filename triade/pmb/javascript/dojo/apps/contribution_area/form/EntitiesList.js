// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EntitiesList.js,v 1.2 2017/01/20 09:54:51 tsamson Exp $


define([
        "dojo/_base/declare", 
        "dojo/topic", 
        "dojo/_base/lang", 
        "dijit/layout/ContentPane", 
        "dojo/dom", 
        "dojo/dom-construct", 
        "dojo/on",
        "dojo/query", 
        "dojo/dom-class"
    ], function(declare, topic, lang, ContentPane, dom, domConstruct, on, query, domClass){
	return declare(ContentPane, {
		constructor: function(){
			
		},
		postCreate: function(){
			this.inherited(arguments);
			this.own(
					topic.subscribe('EntitiesList', lang.hitch(this, this.handleEvents)),
					topic.subscribe('Form', lang.hitch(this, this.handleEvents))
					
			);
			var divContainer = domConstruct.create('ul', {id:'entitiesContainer', class: 'contribution_list'});
			this.setContent(divContainer);
			availableEntities.query({type:'entity'}).forEach(lang.hitch(this, this.generateEntityLink));
		},
		handleEvents: function(evtType,evtArgs){
//			console.log('HandleEvents EntitiesList.js: ', 'evtType: ', evtType, ' evtArgs: ', evtArgs);
			switch(evtType){
				case "formSaved" :
					//this.itemClicked(evtArgs.form.parent, evtArgs.form.parent_type);
					break;
			}
		},
		generateEntityLink: function(entityDetail){
			var entityLink = domConstruct.create('li', {id: 'entity_'+entityDetail.pmb_name, innerHTML: entityDetail.name}, dom.byId('entitiesContainer'), 'last');
			this.own(on(entityLink, 'click', lang.hitch(this, this.itemClicked, entityDetail.id, entityDetail.pmb_name)));
		},
		itemClicked: function(parent, pmb_name, event){
			query('#entitiesContainer li').forEach(function(node){domClass.remove(node,'selected')});
			query('#entity_'+pmb_name).forEach(function(node){domClass.add(node,'selected')});
			topic.publish('EntitiesList', 'entityClicked', {parent: parent});
		}
	});
});