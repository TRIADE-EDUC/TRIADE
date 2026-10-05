// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormsList.js,v 1.9 2020/07/21 14:35:16 gneveu Exp $


define([
        "dojo/_base/declare", 
        "dojo/topic", 
        "dojo/_base/lang", 
        "dijit/layout/ContentPane", 
        "dojo/dom", 
        "dojo/dom-construct", 
        "dojo/on",
        "dojo/query", 
        "dojo/dom-class",
        "dojo/dnd/Moveable",
        "dojo/dom-construct",
        "dojo/dom-class",
        "dojo/dom-style"
    ], function(declare, topic, lang, ContentPane, dom, domConstruct, on, query, domClass, Moveable, domConstruct, domClass, domStyle){
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
				topic.subscribe('GraphStore', lang.hitch(this, this.handleEvents)),
				topic.subscribe('Form', lang.hitch(this, this.handleEvents)),
				topic.subscribe('Node', lang.hitch(this,this.handleEvents))
			);
			this.buildList();			
		},
		handleEvents: function(evtType,evtArgs){	
			//console.log('HandleEvents FormList.js: ', 'evtType: ', evtType, ' evtArgs: ', evtArgs);
			switch(evtType){
				case "formSaved" :			
					this.currentParent = evtArgs.form.parent;
					this.currentParentType = evtArgs.form.parent_type;
					this.buildList(evtArgs.form.form_id);
					break;
				case 'formDeleted' :
				case 'nodeUnselected':
					this.buildList();
					break;
				case 'nodeSelected':
					this.buildList('', evtArgs.node);
					break;
				case 'refreshNodes' :
					this.cleanEvents()
					this.buildList();
					break;
			}
		},
		generateFormLink: function (formDetail, node){
			this.formsListHandler.push(on(node, 'click', lang.hitch(this, this.formClicked, formDetail)));
			this.formsListHandler.push(on(node, 'dragstart', lang.hitch(this, this.dragStartHandler, formDetail)));
			this.formsListHandler.push(on(node, 'dragend', lang.hitch(this, this.dragEnd)));
		},
		formClicked: function(form, event){
			if(domClass.contains('form_'+form.form_id, "selected")){
				domClass.remove('form_'+form.form_id,'selected');
				topic.publish('FormsList', 'formEltUnselected', {});
			}else{
				query('#forms_list .form_line').forEach(function(node){domClass.remove(node,'selected')});
				domClass.add('form_'+form.form_id,'selected');
				topic.publish('FormsList', 'formEltClicked', {formElt: form});
			}
		},
		cleanEvents: function(){
			this.formsListHandler.forEach(function(item){item.remove()});
			this.formsListHandler = new Array();
		},
		buildList: function(selected, nodeFilter){			
			var entities = availableEntities.query({type:'entity'});
			var entitiesContainer = domConstruct.create("div", {id:'forms_list'});
			var divRowTitle = domConstruct.create("div", {"class":'row'}, entitiesContainer);
			var titleFormsList = domConstruct.create("h3", {innerHTML:pmbDojo.messages.getMessage('contribution_area', 'contribution_area_forms_list')}, divRowTitle);
			var divRow = domConstruct.create("div", {"class":"row"}, divRowTitle);
			var spanExpand = domConstruct.create("span", {"class":"item-expand"}, divRow);			
			var aExpand = domConstruct.create("a", {}, spanExpand);			
			var imgExpand = domConstruct.create("img",{id:"expandall", src:pmbDojo.images.getImage('expand_all.gif'),border:"0"}, aExpand);
			var divRowContext = domConstruct.create("div", {"class":'row'}, entitiesContainer);
			var context = divRowContext;

			on(imgExpand, "click",lang.hitch(this, function(context){
				expandAll(context);  
				  return false;
			}, context))
			
			var aCollapse = domConstruct.create("a", {}, spanExpand);
			var imgCollapse = domConstruct.create("img",{id:"collapseall", src:pmbDojo.images.getImage('collapse_all.gif'),border:"0"}, aCollapse);
			
			on(imgCollapse, "click",lang.hitch(this, function(context){
				collapseAll(context);  
				  return false;
			}, context))
			
			for(var i=0 ; i<entities.length ; i++){
				var divHeader = domConstruct.create('div', {id:entities[i].pmb_name+'_div', "class":'notice-parent contribution_area_form'}, divRowContext);
				var divRowParent = domConstruct.create("div", {"class":"row item-expandable"}, divHeader);
				var img = domConstruct.create('img', {'class':'img_plus', id:entities[i].pmb_name+'_divImg', src:pmbDojo.images.getImage('plus.gif'), name:'imEx', border:0, hspace:3}, divRowParent);
				var span = domConstruct.create('span', {'class':'notice-heada', innerHTML: entities[i].name+' ('+availableEntities.query({type:'form', parent_type:entities[i].pmb_name}).length+')'}, divRowParent);
				var entityName = entities[i].pmb_name+'_div';
				
				on(img, 'click', lang.hitch(this, this.expendBaseEntity, entityName))
			
				
				var divContent = domConstruct.create('div', {id:entities[i].pmb_name+'_divChild', 'class':'notice-child contribution_area_form', style:{marginBottom:'6px', display:'none'}}, divHeader);
				this.buildFormsListByEntity(entities[i].pmb_name, divContent);
				
				var divRowButton = domConstruct.create("div", {"class":"row"}, divContent);
				var divLeft = domConstruct.create("div", {"class":"left"}, divRowButton);
				var addButton = domConstruct.create("input", {type:"button","class":"bouton", name:"add_form_"+entities[i].name, id : "add_form_"+entities[i].name, value: pmbDojo.messages.getMessage('contribution_area', 'contribution_area_add')}, divLeft);
				
				
				on(addButton, 'click', lang.hitch(this, this.addFormOpener, entities[i].pmb_name));
				var divRowButtonAfter = domConstruct.create("div", {"class":"row"}, divContent);
				var divNbsp = domConstruct.create("div", {innerHTML:"&nbsp;&nbsp;"}, divRowButton);
			}			
			this.setContent(entitiesContainer);
			
			if (nodeFilter && nodeFilter.type == "scenario") {
				expandBase(nodeFilter.entityType + '_div', true);
			}
		},
		buildFormsListByEntity: function(entityType, node){
			var forms = availableEntities.query({type:'form', parent_type:entityType});
			if(forms.length){
				for(var i=0 ; i<forms.length ; i++){
					var formNode = domConstruct.create('div', {draggable: true, id: 'form_'+forms[i].form_id, innerHTML: forms[i].name, 'class':'form_line'}, node);
					this.generateFormLink(forms[i],formNode);
				}
				return true;
			}
			return false;
		},
		dragStartHandler: function(formElt, e){
			topic.publish('FormsList', 'formEltDragStart', {formElt: formElt});
			e.dataTransfer.effectAllowed = 'move';
			e.dataTransfer.setData('form', JSON.stringify(formElt));
			window.draggedContributionElt = formElt;
		},
		dragEnd: function(){
			topic.publish('FormsList', 'formDragEnd', {});
			query('#forms_list .form_line').forEach(function(node){domClass.remove(node,'selected')});
			window.draggedContributionElt = null;
		},
		toggleFormsList: function(elementClicked){
			var alias = elementClicked.nextElementSibling;
			if(alias != null){
				if(domClass.contains(alias, 'form_entity_displayed')){
					domClass.remove(alias, 'form_entity_displayed');
					domClass.add(alias, 'form_entity_undisplayed');
				}else{
					domClass.remove(alias, 'form_entity_undisplayed'); 
					domClass.add(alias, 'form_entity_displayed');
				}
			}
		},
		addFormOpener: function(type){
			var url = "./modelling.php?categ=contribution_area&sub=form&type=" + type + "&action=edit&form_id=0&area="+graphStore.area_id;
			window.open(url, '_self');
		},
		expendBaseEntity: function(entityName){
			expandBase(entityName, true);
			return false;
		}
	});
});