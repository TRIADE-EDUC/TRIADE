// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Form.js,v 1.3 2018/01/09 10:48:40 vtouchard Exp $


define([
        "dojo/_base/declare", 
        "dijit/layout/ContentPane", 
        "dojo/_base/lang", 
        "dojo/topic", 
        "dojox/grid/DataGrid", 
        "dojo/data/ObjectStore", 
        "dojo/store/Memory", 
        "dojo/ready", 
        "apps/docwatch/ItemsStore", 
        "dojo/date/locale", 
        "dojo/dom-construct", 
        "dojo/on", 
        "dijit/form/Button",  
        "dojox/widget/Standby", 
        "dojo/dom", 
        "dojo/request/xhr", 
        "dijit/registry", 
        "dojo/dom-form",
        "dojo/query"
    ], function(declare,ContentPane,lang,topic,DataGrid,ObjectStore,Memory,ready,ItemsStore,locale, domConstruct, on, Button, standby, dom, xhr, registry, domForm, query){
	return declare([ContentPane], {
		currentForm: null,
		constructor: function(){
		},
		postCreate: function(){
			this.inherited(arguments);
			this.own(
				topic.subscribe('FormsList', lang.hitch(this, this.handleEvents)),
				topic.subscribe('Form', lang.hitch(this, this.handleEvents))
			);
			
		},
		handleEvents: function(evtType,evtArgs){	
//			console.log('HandleEvents Form.js: ', 'evtType: ', evtType, ' evtArgs: ', evtArgs);
			switch(evtType){
				case 'formClicked' :
					this.setAttribute('href','./ajax.php?module=modelling&categ=contribution_area&sub=form&type='+evtArgs.form.parent_type+'&form_id='+evtArgs.form.form_id);
					this.currentForm = evtArgs.form;
					break;
				case 'submit':
					this.treatForm();
					break;
				case 'delete':
					this.deleteForm();
					break;
				case 'formSaved':
					this.setAttribute('href','./ajax.php?module=modelling&categ=contribution_area&sub=form&type='+evtArgs.form.parent_type+'&form_id='+evtArgs.form.form_id);
					this.currentForm = evtArgs.form;
					break;
				case 'formDeleted':
					this.currentForm = null;
					this.setAttribute('href','./ajax.php?module=modelling&categ=contribution_area&sub=form');
					break;
				case 'gridEdit':
					window.open('./modelling.php?categ=contribution_area&sub=form&action=grid&form_id='+this.currentForm.form_id);
					break;
			}
		},
		
		treatForm: function(){
			dojo.xhrPost({
				url : './ajax.php?module=modelling&categ=contribution_area&sub=form&action=save&form_id='+this.currentForm.form_id+'&type='+this.currentForm.parent_type,
				form: dom.byId('contribution_area_form'),
				handleAs: 'json'
			}).then(lang.hitch(this, this.formSaved));			
		},
		
		serializeForm: function(){
			var formWidget = registry.byId('contribution_area_form');
			var nodesToFetch = formWidget.containerNode.elements; //Made by dojo, contient tout les �l�ments post�s (inputs; textarea & cie)
			var data = {};
			for(var i=0 ; i<nodesToFetch.length ; i++){				
				if(nodesToFetch[i].getAttribute('name')){
					if((nodesToFetch[i].getAttribute('type') == 'checkbox' || nodesToFetch[i].getAttribute('type') == 'radio') && !nodesToFetch[i].checked){
						continue;
					}
					if(nodesToFetch[i].getAttribute('name').split('[').length > 1){
						var keyName = nodesToFetch[i].getAttribute('name').split('[')[0];
						var arrayIndice = nodesToFetch[i].getAttribute('name').split('[')[1].split(']')[0];
						if(!data[keyName]){
							data[keyName] = [];
						}
						if(typeof data[keyName][arrayIndice] != 'object'){
							data[keyName][arrayIndice] = {};
						}
						data[keyName][arrayIndice][nodesToFetch[i].getAttribute('name').split('[')[2].replace(']', '')] = nodesToFetch[i].value;	
					}else{ //Common value
						data[nodesToFetch[i].getAttribute('name')] = nodesToFetch[i].value; 
					}
				}
			}
			var enabledElements = query('input[name^="switch_"]');
			var enabledElementsArray = new Array();
			for(var i=0 ; i<enabledElements.length ; i++){
				if(enabledElements[i].checked){
					enabledElementsArray.push(enabledElements[i].name.replace('switch_', ''));	
				}
			}					
			data['switch'] = enabledElementsArray;
			return JSON.stringify(data);
		},
		
		deleteForm: function(){
			xhr.post('./ajax.php?module=modelling&categ=contribution_area&sub=form&action=delete', {
				data: {
					form_id: this.currentForm.form_id,
				},
				handleAs: 'json'
			}).then(lang.hitch(this, this.formDeleted));
		},
		
		formSaved: function(data){
			var existingForm = availableEntities.query({form_id:data.form_id});
			if(existingForm.total == 0){
				data['id'] = this.getMaxStoreId() + 1;
				data['parent'] = availableEntities.query({pmb_name: data.parent_type, type: 'entity'})[0].id;
				availableEntities.put(data);
			}else{
				var exist = existingForm[0];
				if(data.name != exist.name){
//					console.log("difference : ",data.name,exist.name);
					exist.name = data.name;
					availableEntities.remove(existingForm[0].id);
					availableEntities.put(exist);
				}
				data = exist;
			}			
			topic.publish('Form', 'formSaved', {form: data});
		},
		
		formDeleted: function(data){
			if(data.success){
				if(availableEntities.query({form_id: data.form_id}).total > 0){
					availableEntities.remove(availableEntities.getIdentity(availableEntities.query({form_id: data.form_id})[0]))	
				}
			}
			topic.publish('Form', 'formDeleted', {});
		},
		getMaxStoreId:function(){
			var max = 0;
			for(var key in availableEntities.index){
				if(key > max){
					max = key;
				}
			}
			return max;
		},
		
	});
});