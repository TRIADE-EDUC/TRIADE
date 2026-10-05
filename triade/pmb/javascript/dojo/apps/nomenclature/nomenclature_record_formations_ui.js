// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_record_formations_ui.js,v 1.27 2017/07/13 12:22:06 dgoron Exp $

define(["dojo/_base/declare", "apps/nomenclature/nomenclature_record_formations","apps/nomenclature/nomenclature_record_formation", "apps/nomenclature/nomenclature_formation","apps/nomenclature/nomenclature_type_formation", "dojo/dom-construct", "dojo/on", "dojo/_base/lang", "apps/nomenclature/nomenclature_record_formation_ui", "dojo/topic", "dijit/registry", "dijit/_WidgetBase"], function(declare, RecordFormations, RecordFormation, Formation, TypeFormation, domConstruct, on, lang, RecordFormationUi, topic, registry, _WidgetBase){
	/*
	 *Classe nomenclature_record_formations_ui. Classe g�n�rant le formulaire permettant de repr�senter les formations d'une notice
	 */
	  return declare("nomenclature_record_formations_ui",[_WidgetBase], {
			    
		  	id:0,
		  	record_formations:null, /** Instance de record_formations **/
		  	record_formations_ui:null, /** Tableau des ui de record_formations **/
		  	name:null,
		  	params:null,
		  	dom_node:null,
		  	formations_parametre:null, /** Tableau des formations standards (d�finies en administration) **/
		  	selector:null, 
		  	main_div:null,
		  	total_formation:0,
		  	
		    constructor: function(params){
		    	this.own(topic.subscribe("record_formation_ui", lang.hitch(this, this.handle_events)));
		    	this.own(topic.subscribe("instrument_ui", lang.hitch(this, this.handle_events)));
		    	this.own(topic.subscribe("voice_ui", lang.hitch(this, this.handle_events)));

		    	
		    	//		    	Pour le d�bug des �v�nements, on s'amuse � tout capter pour voir
		    	/*topic.subscribe("record_formations_ui", lang.hitch(this, this.handle_events));
		    	topic.subscribe("record_formations", lang.hitch(this, this.handle_events));
		    	topic.subscribe("record_formation", lang.hitch(this, this.handle_events));
		    	topic.subscribe("nomenclature", lang.hitch(this, this.handle_events));
		    	topic.subscribe("nomenclature_ui", lang.hitch(this, this.handle_events));
		    	topic.subscribe("family", lang.hitch(this, this.handle_events));
		    	topic.subscribe("family_ui", lang.hitch(this, this.handle_events));
		    	topic.subscribe("musicstand", lang.hitch(this, this.handle_events));
		    	topic.subscribe("musicstand_ui", lang.hitch(this, this.handle_events));
		    	topic.subscribe("instrument", lang.hitch(this, this.handle_events));
		    	topic.subscribe("instrument_list", lang.hitch(this, this.handle_events));
		    	topic.subscribe("instrument_list_ui", lang.hitch(this, this.handle_events));
		    	topic.subscribe("exotic_instruments", lang.hitch(this, this.handle_events));
		    	topic.subscribe("exotic_instruments_ui", lang.hitch(this, this.handle_events));
		    	topic.subscribe("workshop_ui", lang.hitch(this, this.handle_events));
		    	topic.subscribe("workshops_ui", lang.hitch(this, this.handle_events));
		    	topic.subscribe("workshop", lang.hitch(this, this.handle_events));*/

		    	
		    	/**
		    	 * TODO: Get arguments pour instancier les formations correctes 
		    	 *           - Formations existantes et d�j� enregistr�es pour la notice
		    	 *           - Infos pour en instancier de nouvelles
		    	 */
		    	this.record_formations = new RecordFormations(arguments[0].num_record);
		    	this.set_params(arguments[0]);
		    	this.formations_parametre = new Array();
		    	this.instanciate_param_formations();
		    	this.record_formations_ui = new Array();
		    },
		    
		    handle_events : function(evt_type,evt_args){
		    	//pour le d�bug, on affiche tout ce que l'on voit passer
		    	//console.log("DEBUG",evt_type,evt_args);
		    	switch(evt_type){
		    		case "record_formation_ready" :
		    			this.increment_total_formation();
		    			break;
		    		case "record_formation_delete" :
			    		this.delete_formation_event(evt_args.record_formation_hash);
		    			break;
		    		case "partial_record_created" :
		    			//petit hack utile...
		    			if(document.forms["notice"]['f_rel_id_'+(document.forms["notice"].max_rel.value-1)].value*1>0){
		    				add_rel();
		    			}
		    			document.forms["notice"]['f_rel_id_'+(document.forms["notice"].max_rel.value-1)].value = evt_args.record_id;
		    			document.forms["notice"]['f_rel_'+(document.forms["notice"].max_rel.value-1)].value = evt_args.record_title;
		    			document.forms["notice"]['f_rel_id_notices_relations_'+(document.forms["notice"].max_rel.value-1)].value = evt_args.record_reverse_id_notices_relations;
		    			document.forms["notice"]['f_rel_num_reverse_link_'+(document.forms["notice"].max_rel.value-1)].value = evt_args.record_reverse_num_reverse_link;
		    			for(var i=0 ; i<document.forms["notice"]['f_rel_type_'+(document.forms["notice"].max_rel.value-1)].options.length ; i++){
		     				if(document.forms["notice"]['f_rel_type_'+(document.forms["notice"].max_rel.value-1)].options[i].value == (registry.byId('nomenclature_datastore').get_relation_code()+"-down")){
		    					document.forms["notice"]['f_rel_type_'+(document.forms["notice"].max_rel.value-1)].selectedIndex = i;
		    					update_rel_reverse_type(document.forms["notice"]['f_rel_type_'+(document.forms["notice"].max_rel.value-1)], (document.forms["notice"].max_rel.value-1));
		    					break;
		    				}
		    			}
		    			break;
		    	}
		    },
		    
		    buildRendering: function(){	
		    	this.domNode = domConstruct.create('div', {class:'notice-child', id:this.get_dom_node().id+'_main_div'}, this.get_dom_node());
		    	this.inherited(arguments)
		    	this.main_div = this.domNode;
		    	this.build_form();
		    },
		    
		    
		    build_form: function(){
		    	domConstruct.create('label', {innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_formation_add')}, this.main_div);
		    	domConstruct.create('br', null, this.main_div);
		    	this.create_selector(this.main_div);
		    	var button_add = domConstruct.create('input', {type:'button', value:'+', class:'bouton'}, this.main_div);
				this.own(on(button_add, 'click', lang.hitch(this, this.create_new_formation)));
				
				/**
				 * TODO: Initialisation du dom: ajt de formations, selecteur de type
				 * Appel de init_formations
				 */
			},
			
			init_record_formation: function(){
				/**
				 * TODO: Initialisation des formations (dom node & cie)
				 */
				var already_saved_formations = JSON.parse(this.get_params().formations);
				for(var i=0 ; i<already_saved_formations.length ; i++){
					var formation = this.get_formation_from_id(already_saved_formations[i].num_formation);
					/**
					 * TODO: set params with those given in parameters & instanciate elts
					 */
					//console.log('formation in formations ui: ', formation);
					var record_formation = new RecordFormation(formation, this.params.num_record, this.get_total_formation(), already_saved_formations[i]);
					this.record_formations.add_formation(record_formation);
					var params = {
							id:record_formation.get_hash(),
							record_formation:record_formation,
							dom_node:this.main_div,
							indice:this.get_total_formation()
					};
					var record_formation_ui = new RecordFormationUi(params);
					this.record_formations_ui.push(record_formation_ui);
					record_formation_ui.add_input_hidden();
				}
				
			},
			
			get_dom_node: function() {
				return this.dom_node;
			},
			
			set_dom_node: function(dom_node) {
				this.dom_node = dom_node;
			},
			
			get_record_formations: function() {
				return this.record_formations;
			},
			
			set_record_formations: function(record_formations) {
				this.record_formations = record_formations;
			},
			
			get_params: function() {
				return this.params;
			},
			
			set_params: function(params) {
				this.params = params;
			},
			
			create_selector: function(node){
				this.selector = domConstruct.create("select", null, node);
				for(var i=0; i<this.formations_parametre.length ; i++){
					var option = domConstruct.create('option', {
						value:this.formations_parametre[i].get_id(), 
						id:this.get_dom_node().id+'_formation_selector_'+i, 
						innerHTML:this.formations_parametre[i].get_name()
					}, this.selector);
				}
			},
			
			create_new_formation: function(){
				/**
				 * TODO: 
				 * -Recuperation de l'id selectionn�
				 * -Instanciation d'une nouvelle formation avec le type (d�fini par l'id)
				 */
				var id_formation = this.selector.options[this.selector.selectedIndex].value;
				this.instanciate_formation_ui_from_id(id_formation);
			},
			instanciate_param_formations: function(){
				var tree_forms = registry.byId('nomenclature_datastore').get_formations_datastore();
				for(var i=0 ; i<tree_forms.length ; i++){
					var formation_param = new Formation(tree_forms[i].id, tree_forms[i].nature, tree_forms[i].name);
					var array_types = new Array();
					this.formations_parametre.push(formation_param);
					if(tree_forms[i].types != null){
						for(var j=0 ; j<tree_forms[i].types.length ; j++){
								array_types.push(new TypeFormation(tree_forms[i].types[j].id,tree_forms[i].types[j].name, formation_param));
						}
						formation_param.set_types(array_types);
					}
				}
			},
			add_record_formation: function(formation){
				var record_formation = new RecordFormation(formation, this.params.num_record, this.get_total_formation());
				this.record_formations.add_formation(record_formation);
				var params = {
						id:record_formation.get_hash(),
						record_formation:record_formation,
						dom_node:this.main_div,
						indice:this.get_total_formation()
				};
				var record_formation_ui = new RecordFormationUi(params);
				this.record_formations_ui.push(record_formation_ui);
				record_formation_ui.add_input_hidden();
			},
			instanciate_formation_ui_from_id: function(id){
				var id = id;
				for(var i=0 ; i<this.formations_parametre.length ; i++){
					if(id == this.formations_parametre[i].get_id()){
						break;
					}
				}
				this.add_record_formation(this.formations_parametre[i]);
			},
			get_total_formation: function() {
				return this.total_formation;
			},
			
			set_total_formation: function(total_formation) {
				this.total_formation = total_formation;
			},
			increment_total_formation: function(){
				this.total_formation++;
			},
			get_formation_from_id: function(id){
				var id = parseInt(id);
				for(var i=0 ; i<this.formations_parametre.length ; i++){
					if(id == this.formations_parametre[i].get_id()){
						return this.formations_parametre[i];
					}
				}
			},
			delete_formation_event: function(hash){
				for(var i=0 ; i<this.record_formations.record_formations.length ; i++){
		    		if(this.record_formations.record_formations[i].get_hash() == hash){
	    				this.record_formations_ui[i].destroy();
				    	this.record_formations_ui.splice(i, 1);
				    	this.record_formations.delete_formation(hash);
				    	this.total_formation--;
				    	break;
		    		}
		    	}
		    	for(var i=0 ; i<this.record_formations.record_formations.length ; i++){
					this.record_formations_ui[i].set_order(i);
				}
		    },
		    
		    postCreate: function(){
		    	this.init_record_formation();
		    },
		    check_validate:function(){
		    	for(var i=0 ; i<this.record_formations_ui.length ; i++){
		    		if(!this.record_formations_ui[i].check_validate()){
		    			this.record_formations_ui[i].nomenclature_ui.focus_on_error();
		    			return false;
		    		}
		    	}
		    	return true;
		    }
	    });
	});