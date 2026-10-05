// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SegmentForm.js,v 1.15 2022/12/06 17:53:47 arenou Exp $


define([
        "dojo/_base/declare", 
        "dojo/parser", 
        "dijit/form/Button",
        "dojo/topic",
        "dojo/_base/lang",
        "dojo/on",
        "dojo/dom",
        "dojo/query",
        "dojo/dom-attr",
        "dojo/request",
        "dijit/registry",
        "dojo/dom-construct",
        "apps/pmb/PMBDojoxDialogSimple",
        "dojo/dom-form",
        "apps/pmb/pmbEventsHandler",
        "apps/search_universes/EntityForm",
        "dojo/request/xhr",
        "dojo/dom-style",        
        ], 
		function(declare,parser, Button, topic, lang, on, dom, query, domAttr, request, registry, domConstruct, DialogSimple, domForm, pmbEventsHandler, EntityForm, xhr, domStyle){
	return declare(EntityForm, {
		
		loadSetDialog : function(params, evt) {
			var search_data = dom.byId('segment_set_data_set').value;			
			var path = './ajax.php?module='+params.module+'&what='+params.what+'&action='+params.action+'&entity_type='+params.entity_type+'&entity_id='+params.entity_id+'&no_search=1&class_name='+this.className+'&search_data='+search_data+'&method=saveAdvancedSearch';
			if (params.what == "ontology") {
				path += '&element=concept'; 
			}
			if (params.authperso_id > 0) {
				path += '&authperso_id=';
				path += params.authperso_id;
			}
			if (params.class_id > 0) {
				path += '&class_id=';
				path += params.class_id;
				path += '&ontology_id=';
				path += params.ontology_id;
			}
			if (path) {
				this.loadDialog(params, evt, path, true);
			}
		},
		
		loadSortDialog : function(params, evt) {
			var sort_data = dom.byId('segment_sort').value;			
			var path = './ajax.php?module='+params.module+'&what='+params.what+'&action='+params.action+'&entity='+params.entity+'&entity_type='+params.sort_type+'&entity_id='+params.entity_id+'&no_search=1&class_name='+this.className+'&sort_data='+sort_data+'&method=saveSort';
			if (params.what == "ontology") {
				path += '&element=concept'; 
			}
			if (params.authperso_id > 0) {
				path += '&authperso_id=';
				path += params.authperso_id;
			}
			if (path) {
				this.loadDialog(params, evt, path, true);
			}
		},
		
		loadSearchPersoDialog : function(params, evt) {
			var path = './ajax.php?module=admin&categ=search_universes&sub='+params.sub+'&action='+params.action+'&id='+params.entity_id+'&segment_id='+params.segment_id+'&no_search=1&class_name='+this.className+'&entity_type='+params.entity_type+'&entity_id='+params.entity_id+'&method=saveSearch';
//			var path = './admin.php?categ=opac&sub=search_persopac&section=liste&action=add';
			if (path) {
				var dialog = this.loadDialog(params, evt, path, true);
				on(dialog,'load', ()=>{
					var form = query('form', dialog.containerNode)[0];
					if (form) {
						if (domAttr.get(form, 'name') == 'search_form') {
							domAttr.set(form, 'action', './ajax.php?module=admin&categ=search_universes&sub='+params.sub+'&action=edit&id='+params.entity_id+'&segment_id='+params.segment_id);
						} else {
							domAttr.set(form, 'action', './ajax.php?module=admin&categ=search_universes&sub='+params.sub+'&action=save&id='+params.entity_id+'&segment_id='+params.segment_id);
						}
						on(form, 'submit', (e)=>{
							e.preventDefault();
							params.form = form;
							this.saveSearch(params);
							return false;
						});
					}
				});
			}
		},
		
		loadFacetDialog : function(params, evt) {
			var path = './ajax.php?module=admin&categ=search_universes&sub='+params.sub+'&segment_type='+params.segment_type+'&action=edit&id='+params.entity_id;
			if (path) {
				params.entity_id = registry.getUniqueId('facet_dialog');
				var dialog = this.loadDialog(params, evt, path, true);
				on(dialog,'load', ()=>{
					var form = query('form', dialog.containerNode)[0];
					if (form) {
						domAttr.set(form, 'action', './ajax.php?module=admin&categ=search_universes&sub='+params.sub+'&segment_type='+params.segment_type+'&action=save&id='+params.entity_id+'&segment_id='+params.segment_id);
						on(form, 'submit', (e)=>{
							e.preventDefault();
							params.form = form;
							this.saveFacet(params);
							return false;
						});
						domStyle.set(dom.byId('btexit'),'display','none');
					}
				});
			}
		},
		
		saveAdvancedSearch : function(params) {
			if (params.formId) {
				enable_operators();
				var myForm = dom.byId(params.formId);
				myForm.action = "./ajax.php?module=admin&categ=search_universes&sub="+params.entity_type+"&action=update_set&id="+params.entity_id;
				var formData = JSON.parse(domForm.toJson(myForm));
				request.post(myForm.action, {
					data : formData,
					handleAs : 'json'
				}).then(lang.hitch(this, function(data) {	
					if(data.status == '1'){
						this.hideDialog(params);
						this.updateSetDom(data);
						//topic.publish("EntityTree","updateTree",{tree_data : null, entity : {id : params.entity_id, type : params.entity_type}});
						this.destroy();
						topic.publish("EntityTree","selectTreeNodeById",{id : params.entity_id, type : params.entity_type});
					}else{
						alert(data.message);
					}
				}),function(err){
					alert(pmbDojo.messages.getMessage('search_universes', 'search_segment_set_not_save'));
				});
			}
		},
		
		saveSort : function(params) {
			if (params.formId) {
				var myForm = dom.byId(params.formId);
				myForm.action = "./ajax.php?module=admin&categ=search_universes&sub=segment&action=update_sort&id="+params.entity_id;
				var formData = JSON.parse(domForm.toJson(myForm));
				request.post(myForm.action, {
					data : formData,
					handleAs : 'json'
				}).then(lang.hitch(this, function(data) {					
					if(data.status == '1'){
						this.hideDialog(params);
						this.updateSortDom(data);
					}else{
						alert(data.message);
					}
				}),function(err){
					alert(pmbDojo.messages.getMessage('search_universes', 'search_segment_set_not_save'));
				});
			}
		},
		
		updateSetDom : function(data) {
			if (typeof data.human_query !== 'undefined') {
				dom.byId('segment_set_human_query').innerHTML = data.human_query;
			}
			if (typeof data.set !== 'undefined') {
				dom.byId('segment_set_data_set').value = data.set;
			}
		},
		
		updateSortDom : function(data) {
			if (typeof data.human_query !== 'undefined') {
				dom.byId('segment_sort_human_query').innerHTML = data.human_query;
			}
			if (typeof data.sort !== 'undefined') {
				dom.byId('segment_sort').value = data.sort;
			}
		},
		
		saveFacet : function(params) {
			if (params.form) {
				var formData = JSON.parse(domForm.toJson(params.form));
				request.post(params.form.action, {
					data : formData,
					handleAs : 'json'
				}).then(lang.hitch(this, function(response) {					
					if(response){
						this.hideDialog(params);
						topic.publish('FormContainer', 'updateTree', response);
					}
				}),function(err){
					alert(pmbDojo.messages.getMessage('search_universes', 'search_segment_facet_not_save'));
				});
			}
		},
		
		saveSearch : function(params) {
			if (params.formId) {
				var myForm = dom.byId(params.formId);
				var formData = JSON.parse(domForm.toJson(myForm));
				request.post(myForm.action, {
					data : formData,
					handleAs : 'html'
				}).then(lang.hitch(this, function(response) {
					if(response){						
						//this.hideDialog(params);
						var dijitId = params.entity_type+"_"+params.entity_id+"_dialog";
						if(this.dijits[dijitId]) {
							this.dijits[dijitId].set('content', response);
							var form = query('form', this.dijits[dijitId].containerNode)[0];
							if (form) {
								var buttonSubmit = dom.byId('btsubmit');
								domAttr.set(buttonSubmit, 'onclick', '');
								domAttr.set(buttonSubmit, 'type', 'submit');
								on(form, 'submit', (e)=>{
									e.preventDefault();
									params.form = form;
									this.saveSearchPerso(params);
									return false;
								});
								domStyle.set(dom.byId('btexit'),'display','none');								
							}
						}
						//topic.publish('FormContainer', 'updateTree', response);
					}
				}),function(err){
					alert(pmbDojo.messages.getMessage('search_universes', 'search_segment_facet_not_save'));
				});
			}
		},
		
		saveSearchPerso : function(params) {
			var myForm = dom.byId(params.form);
			var formData = JSON.parse(domForm.toJson(myForm));
			request.post(myForm.action, {
				data : formData,
				handleAs : 'json'
			}).then(lang.hitch(this, function(response) {
				if(response){				
					/**
					 * TODO: the dialog must be removed
					 */
					this.hideDialog(params);
					this.removeDialog(params);
					topic.publish('FormContainer', 'updateTree', response);
				}
			}),function(err){
				alert(pmbDojo.messages.getMessage('search_universes', 'search_segment_facet_not_save'));
			});
		},
		
		deleteSet : function(params) {
			var data_set = dom.byId('segment_set_data_set');
			if (data_set.value) {
				request.post("./ajax.php?module=admin&categ=search_universes&sub="+params.entity_type+"&action=delete_data_set&id="+params.entity_id, {
					data : {},
					handleAs : 'json'
				}).then(lang.hitch(this, function(data) {					
					if(data.status == '1'){
						this.updateSetDom(data);
					}else{
						alert(data.message);
					}
				}),function(err){
					alert(pmbDojo.messages.getMessage('search_universes', 'search_segment_set_not_save'));
				});
			}
		}
	});
});