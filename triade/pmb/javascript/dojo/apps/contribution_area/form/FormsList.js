// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormsList.js,v 1.2 2017/01/20 09:54:51 tsamson Exp $


define(["dojo/_base/declare", "dojo/topic", "dojo/_base/lang", "dijit/layout/ContentPane", "dojo/dom", "dojo/dom-construct", "dojo/on","dojo/query", 'dojo/dom-class'], function(declare, topic, lang, ContentPane, dom, domConstruct, on, query, domClass){
	return declare(ContentPane, {
		formsListHandler:null,
		currentParentType:null,
		currentParent:null,
		
		constructor: function(){
			this.formsListHandler = new Array();
		},
		postCreate: function(){
			this.inherited(arguments);
			this.own(
				topic.subscribe('EntitiesList', lang.hitch(this, this.handleEvents)),
				topic.subscribe('Form', lang.hitch(this, this.handleEvents))
			);
			
		},
		handleEvents: function(evtType,evtArgs){	
			switch(evtType){
				case "entityClicked" :
					var entity = availableEntities.query({type:'entity',id:evtArgs.parent});
					this.currentParent = evtArgs.parent;
					this.currentParentType = entity[0].pmb_name;
					this.buildList();
					break;
				case "formSaved" :			
					this.currentParent = evtArgs.form.parent;
					this.currentParentType = evtArgs.form.parent_type;
					this.buildList(evtArgs.form.form_id);
					break;
				case 'formDeleted' :
					this.buildList();
					break;
			}
		},
		generateFormLink: function (formDetail){
			var formLink = domConstruct.create('li', {id: 'form_'+formDetail.form_id, innerHTML: formDetail.name}, 'formsContainer', 'last');
			this.formsListHandler.push(on(formLink, 'click', lang.hitch(this, this.formClicked, formDetail)));
		},
		formClicked: function(form, event){
			query('#formsContainer li').forEach(function(node){domClass.remove(node,'selected')});
			query('#form_'+form.form_id).forEach(function(node){domClass.add(node,'selected')});
			topic.publish('FormsList', 'formClicked', {form: form});
		},
		cleanEvents: function(){
			this.formsListHandler.forEach(function(item){item.remove()});
			this.formsListHandler = new Array();
		},
		
		buildList: function(selected){
			this.cleanEvents();
			var divContainer = domConstruct.create('ul', {id:'formsContainer', class: 'contribution_list'});
			this.setContent(divContainer);
			var newButton = domConstruct.create('li', {id:'creationButton', class: 'newItem',innerHTML: 'Nouveau'}, 'formsContainer', 'last');					
			this.own(
				on(newButton,'click', lang.hitch(this, this.formClicked, {parent_type:this.currentParentType, form_id:0}))
			);
			availableEntities.query({type:'form',parent:this.currentParent},{sort:[{attribute: "name"}]}).forEach(lang.hitch(this, this.generateFormLink));
			query('#formsContainer li').forEach(function(node){domClass.remove(node,'selected')});
			if(selected){
				query('#form_'+selected).forEach(function(node){domClass.add(node,'selected')});	
			}
		} 
		
	});
});