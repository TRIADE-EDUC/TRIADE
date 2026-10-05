// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_instruments_list_ui.js,v 1.28 2016/11/29 13:00:29 vtouchard Exp $

define(["dojo/_base/declare", "apps/nomenclature/nomenclature_instrument_ui","apps/nomenclature/nomenclature_instrument", "dojo/on", "dojo/dom-construct","dojo/dom", "dojo/_base/lang", "dojo/topic", "dijit/registry", "dijit/_WidgetBase"], function(declare, Instrument_ui, Instrument, on, domConstruct, dom, lang, topic, registry, _WidgetBase){
	/*
	 *Classe nomenclature_instruments_list_ui. Classe générant la partie du formulaire liée a un pupitre
	 */
	  return declare("nomenclature_instruments_list_ui",[_WidgetBase], {
			
			id:null,    
		  	dom_node:null,
		  	mode:null,
		  	total_instruments:0,
		  	instruments_node:null,
		  	init_display:false,
		  	instruments_list:null,
		    inputs_array:null,
		    instruments:null,
		    workshop_ui:null,
		    textarea_note:null,
		    hidden_note:null,
		    
		    constructor: function(params){
		        this.inputs_array = new Array();
		    	this.instruments = new Array();
		    	this.own(topic.subscribe("instrument_ui",lang.hitch(this, this.handle_events)));
		    },
		    
		    postCreate:function(){
		    	this.own(topic.subscribe(this.dom_node.id+'_tab_delete', lang.hitch(this, this.delete_instrument_event)));
		    	this.init_instruments_ui();
			},
			
		    handle_events : function(evt_type, evt_args){
		    	switch(evt_type){
		    	case "dom_ready" :
					if(evt_args.hash.indexOf(this.instruments_list.get_hash()) != -1){
						this.instrument_ready();
						if(this.mode == "exotic_instruments"){
							this.generate_inputs();	
						}
						if(this.mode == "workshop"){
							this.generate_inputs();	
						}
		    		}
		    		break;
		    	case "instrument_delete":
		    		if(evt_args.hash.indexOf(this.instruments_list.get_hash()) != -1){
		    			this.delete_instrument_event(evt_args.order);
		    			this.generate_inputs();
		    		}
		    		break;
		    	case "instru_changed":
		    		if(evt_args.hash.indexOf(this.instruments_list.get_hash())!=-1){
		    			if(evt_args.mode == "exotic_instruments"){
		    				this.generate_inputs();
			    			topic.publish("instruments_list_ui", "instru_changed", {hash: this.instruments_list.get_hash()});
		    			}
		    			if(evt_args.mode == "workshop"){
		    				this.generate_inputs();
			    			topic.publish("instruments_list_ui", "instru_changed", {hash: this.instruments_list.get_hash()});
		    			}
		    		}
		    		break;
		    	}
		    },
		    
		    buildRendering: function(){
		    	this.inherited(arguments);
		    	this.build_form();
		    },
		    
		    build_form: function(){
		    
		    	var h3_node = domConstruct.create('h3', {innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_instrument_add')}, this.get_dom_node());
		    	var input_plus = domConstruct.create('input', {type:'button', value:'+', class:'bouton'}, h3_node);
		    	this.own(on(input_plus, 'click', lang.hitch(this, this.add_instrument)));
		    	
		    	var input_nb_instruments = domConstruct.create('input', {
				    	type:'hidden', 
				    	id:this.get_dom_node().id+'_count', 
				    	value:this.instruments_list.get_effective()
				    }, this.get_dom_node());
		    	var display = "table";
		    	if(this.instruments_list.instruments.length == 0){
		    		display = "none";
		    	}
		    	this.set_instruments_node(domConstruct.create('table', {id:this.get_dom_node().id+'_tab', style:{display:display}}, this.get_dom_node()));
		    	
		    	var header_line = domConstruct.create('tr',null,this.get_instruments_node());
				    	
		    	switch (this.mode) {
		    		case "exotic_instruments":
		    			var th_order = domConstruct.create('th', {rowspan:'2', innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_instruments_header_order'), style:{textAlign:'center'}}, header_line);
				    	var th_instrument = domConstruct.create('th', {colspan:'2', innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_instruments_header_instruments'), style:{textAlign:'center'}}, header_line);
				    	var th_effective = domConstruct.create('th', {rowspan:'2', innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_instruments_header_effective'), style:{textAlign:'center'}}, header_line);
				    	var th_bouton_delete = domConstruct.create('th', {rowspan:'2', style:{textAlign:'center'}}, header_line);
				    	var header_line_2 = domConstruct.create('tr', null, this.get_instruments_node());
				    	var th_main_instr = domConstruct.create('th', {innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_instruments_header_main'), style:{textAlign:'center'}}, header_line_2);
				    	var th_annexe_instr = domConstruct.create('th', {innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_instruments_header_other'), style:{textAlign:'center'}}, header_line_2);
				    	
				    	var lib_note = domConstruct.create('div', {class:'row'}, this.get_dom_node());
				    	domConstruct.create('label', {
				    		class:'etiquette',
				    		for:this.get_dom_node().id+'_note',
				    		innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_exotic_instruments_note')
				    	}, lib_note);
				    	
				    	var content_note = domConstruct.create('div', {class:'row'}, this.get_dom_node());
				    	this.textarea_note = domConstruct.create('textarea', {
				    		id:this.get_dom_node().id+'_note',
				    		name:this.get_dom_node().id+'_note',
				    		class:'saisie-80em',
				    		wrap:'virtual',
				    		rows:'3',
				    		innerHTML:this.instruments_list.nomenclature.record_formation.get_exotic_instruments_note()
				    	}, content_note);
				    	this.own(on(this.textarea_note, 'keyup', lang.hitch(this, this.update_exotic_instruments_note)));
				    	/** Création de l'input hidden de la note en vue de la sauvegarde **/
				    	this.hidden_note = domConstruct.create('input', {type:'hidden', name:this.instruments_list.nomenclature.record_formation.get_hidden_field_name('exotic_instruments_note'), value:this.instruments_list.nomenclature.record_formation.get_exotic_instruments_note()}, content_note);
				    	break;
		    		case "workshop":
		    			var th_order = domConstruct.create('th', {innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_instruments_header_order'), style:{textAlign:'center'}}, header_line);
				    	var th_instrument = domConstruct.create('th', {innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_instruments_header_instrument'), style:{textAlign:'center'}}, header_line);
				    	var th_effective = domConstruct.create('th', {innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_instruments_header_effective'), style:{textAlign:'center'}}, header_line);
				    	var th_bouton_delete = domConstruct.create('th', {style:{textAlign:'center'}}, header_line);
				    	break;
		    	}
		    },
		    init_instruments_ui: function(){
		    	this.total_instruments = 0;
		    	if (this.instruments_list.instruments.length) {
			    	for(var i=0; i<this.instruments_list.instruments.length ; i++){
			    		this.init_display = true;
			    		var new_instru_ui = new Instrument_ui({id:this.get_dom_node().id+'_'+this.instruments_list.instruments[i].get_code()+'_'+this.get_total_instruments(), indice: this.get_total_instruments(), dom_node: this.get_instruments_node(), instrument: this.instruments_list.instruments[i], mode: this.mode});
			    		this.instruments.push(new_instru_ui);
			    	}
		    	} else {
		    		topic.publish("instruments_list_ui",'instruments_list_ui_ready', {hash:this.instruments_list.get_hash()});
		    	}
		    },
		    add_instrument: function(){
		    	if(this.instruments_node.style.display == "none"){		
		    			this.init_display = true;
			    		this.instruments_node.style.display = "table";
		    	}
		    	
		    	var input_count = dom.byId(this.get_dom_node().id+'_count');
		    	input_count.value = parseInt(input_count.value)+1; 
		    	var new_inst = new Instrument('','');
		    	new_inst.set_effective(1);
		    	new_inst.set_part(0);
		    	new_inst.set_order(this.instruments_list.get_max_order()+1);
		    	
		    	this.instruments_list.add_instrument(new_inst);
		    	var new_inst_ui = new Instrument_ui({id:this.get_dom_node().id+'_'+this.get_total_instruments(), indice: this.total_instruments, dom_node: this.get_instruments_node(), instrument: new_inst, mode: this.mode});
		    	this.instruments.push(new_inst_ui);
		    	new_inst_ui.init_actions();

		    },
		    delete_instrument: function(instrument){
		    	var index_inst = this.instruments.indexOf(instrument);
		    	this.instruments[index_inst].destroy();
		    	this.instruments.slice(index_inst, 1);
		    	instrument = null;
		    },
		    delete_instrument_event: function(order){
		    	var index_instru_ui;
		    	var order = order;
		    	for(var i=0 ; i<this.instruments.length ; i++){
		    		if(this.instruments[i].instrument.get_order() == order){
		    			index_instru_ui = i; 
		    		}
		    	}
		    	this.instruments[index_instru_ui].destroy();
		    	this.instruments.splice(index_instru_ui, 1);
		    	this.instruments_list.delete_instrument(order, true);
		    	
		    	var array_nodes = new Array();
		    	for(var i=0; i<this.instruments.length ; i++){
		    		array_nodes.push(this.instruments[i].domNode);
		    	}
		    	var newarr = array_nodes.sort(this.sort_nodes);
		    	for(var i=0; i<newarr.length ; i++){
		    		var ui_instance = registry.byId(newarr[i].id);
		    		ui_instance.set_order(i+1);
		    	}
		    	parse_drag(this.instruments_node);
		    	topic.publish("intrument_ui","instru_changed",null);
		    	var input_count = dom.byId(this.dom_node.id+'_count');
		    	input_count.value = parseInt(input_count.value)-1;
		    	if(parseInt(input_count.value) == 0){
		    		this.instruments_node.style.display = "none";
		    	}
		    },
			sort_array: function(a, b){
				if(a.order < b.order){
					return -1;
				}
				if(a.order == b.order){
					return 0;
				}
				if(a.order > b.order){
					return 1;
				}
			},
			sort_nodes: function(a, b){
				if(parseInt(a.getAttribute('order')) < parseInt(b.getAttribute('order'))){
					return -1;
				}
				if(parseInt(a.getAttribute('order')) == parseInt(b.getAttribute('order'))){
					return 0;
				}
				if(parseInt(a.getAttribute('order')) > parseInt(b.getAttribute('order'))){
					return 1;
				}
			},
		    get_dom_node: function() {
				return this.dom_node;
			},
			set_dom_node: function(dom_node) {
				this.dom_node = dom_node;
			},
			get_mode: function() {
				return this.mode;
			},
			set_mode: function(mode) {
				this.mode = mode;
			},
			get_instruments_list: function() {
				return this.instruments_list;
			},
			set_instruments_list: function(instruments_list) {
				this.instruments_list = instruments_list;
			},
			get_instruments_node: function() {
				return this.instruments_node;
			},
			set_instruments_node: function(instruments_node) {
				this.instruments_node = instruments_node;
			},
			instrument_ready: function(){
				this.total_instruments++;
				var instruments = this.instruments_list.get_instruments();	
				if(this.total_instruments == instruments.length){
					topic.publish("instruments_list_ui",'instruments_list_ui_ready', {hash:this.instruments_list.get_hash()});
					//Cas des instruments non standards
					if(this.init_display == false){
						this.instruments_node.style.display = "none";
					}
				}
			},
			
			get_total_instruments: function() {
				return this.total_instruments;
			},
			
			set_total_instruments: function(total_instruments) {
				this.total_instruments = total_instruments;
			},
			get_object_instrument: function(){
				var array_instru = new Array();
				var actual_instruments = this.instruments_list.get_instruments();
				for(var i=0 ; i<actual_instruments.length ; i++){
					var obj = {};
					obj.code = actual_instruments[i].get_code();
					obj.id = actual_instruments[i].get_id();
					obj.name = actual_instruments[i].get_name();
					obj.order = actual_instruments[i].get_order();
					obj.effective = actual_instruments[i].get_effective();
					if(this.mode == "exotic_instruments"){
						obj.id_exotic_instrument = actual_instruments[i].get_id_exotic_instrument();
						obj.other = new Array();
						if(actual_instruments[i].others_instruments){
							for(var j=0 ; j<actual_instruments[i].others_instruments.length ; j++){
								var obj_other = {};
								obj_other.code = actual_instruments[i].others_instruments[j].get_code();
								obj_other.order = actual_instruments[i].others_instruments[j].get_order();
								obj_other.id = actual_instruments[i].others_instruments[j].get_id();
								obj_other.name = actual_instruments[i].others_instruments[j].get_name();
								obj_other.effective = actual_instruments[i].others_instruments[j].get_effective();
								obj_other.id_exotic_instrument = actual_instruments[i].others_instruments[j].get_id_exotic_instrument();
								obj.other.push(obj_other);
							}
						}	
					}else if(this.mode == "workshop"){
						obj.id_workshop_instrument = actual_instruments[i].get_id_workshop_instrument();	
					}
					
					array_instru.push(obj);
				}
				return array_instru;
			},
			generate_inputs:function(){
				for(var i=0 ; i<this.inputs_array.length ; i++){
					domConstruct.destroy(this.inputs_array[i]);
				}
				this.inputs_array = new Array();
				var json_data = this.generate_json_data();
				for(var i=0 ; i<json_data.length ; i++){
					var json_params = {
							type:'hidden', 
							name:json_data[i].name, 
							value:json_data[i].value,
						};
					if(json_data[i].id){
						json_params.id = json_data[i].id;
					}
					this.inputs_array.push(domConstruct.create('input',json_params, this.dom_node));
				}
			},
			generate_json_data: function(){
				var json_data = new Array();
				if(this.mode == "exotic_instruments"){
					var instruments_non_standards = this.get_object_instrument();
					for(var i=0 ; i<instruments_non_standards.length ; i++){
						json_data.push({name:this.instruments_list.get_hidden_field_name()+'[instruments]['+i+'][code]', value:instruments_non_standards[i].code});
						json_data.push({name:this.instruments_list.get_hidden_field_name()+'[instruments]['+i+'][name]', value:instruments_non_standards[i].name});
						json_data.push({name:this.instruments_list.get_hidden_field_name()+'[instruments]['+i+'][id]', value:instruments_non_standards[i].id});
						json_data.push({name:this.instruments_list.get_hidden_field_name()+'[instruments]['+i+'][order]', value:instruments_non_standards[i].order});
						json_data.push({name:this.instruments_list.get_hidden_field_name()+'[instruments]['+i+'][effective]', value:instruments_non_standards[i].effective});
						json_data.push({id:this.instruments_list.get_hidden_field_name()+'_instruments_'+instruments_non_standards[i].order+'_id_exotic_instrument', name:this.instruments_list.get_hidden_field_name()+'[instruments]['+i+'][id_exotic_instrument]', value:instruments_non_standards[i].id_exotic_instrument});
						
						for(var j=0; j<instruments_non_standards[i].other.length ; j++){
							json_data.push({name:this.instruments_list.get_hidden_field_name()+'[instruments]['+i+'][other]['+j+'][code]', value:instruments_non_standards[i].other[j].code});
							json_data.push({name:this.instruments_list.get_hidden_field_name()+'[instruments]['+i+'][other]['+j+'][name]', value:instruments_non_standards[i].other[j].name});
							json_data.push({name:this.instruments_list.get_hidden_field_name()+'[instruments]['+i+'][other]['+j+'][order]', value:instruments_non_standards[i].other[j].order});
							json_data.push({name:this.instruments_list.get_hidden_field_name()+'[instruments]['+i+'][other]['+j+'][id]', value:instruments_non_standards[i].other[j].id});
							json_data.push({id:this.instruments_list.get_hidden_field_name()+'_instruments_'+instruments_non_standards[i].order+'_other_'+instruments_non_standards[i].other[j].order+'_id_exotic_instrument', name:this.instruments_list.get_hidden_field_name()+'[instruments]['+i+'][other]['+j+'][id_exotic_instrument]', value:instruments_non_standards[i].other[j].id_exotic_instrument});
						}
					}
				}
				if(this.mode == "workshop"){
					var instruments = this.get_object_instrument();
					for(var i=0 ; i<instruments.length ; i++){
						json_data.push({name:this.instruments_list.get_hidden_field_name()+'[workshops]['+this.workshop_ui.get_indice()+'][instruments]['+i+'][code]', value:instruments[i].code});
						json_data.push({name:this.instruments_list.get_hidden_field_name()+'[workshops]['+this.workshop_ui.get_indice()+'][instruments]['+i+'][name]', value:instruments[i].name});
						json_data.push({name:this.instruments_list.get_hidden_field_name()+'[workshops]['+this.workshop_ui.get_indice()+'][instruments]['+i+'][id]', value:instruments[i].id});
						json_data.push({name:this.instruments_list.get_hidden_field_name()+'[workshops]['+this.workshop_ui.get_indice()+'][instruments]['+i+'][order]', value:instruments[i].order});
						json_data.push({name:this.instruments_list.get_hidden_field_name()+'[workshops]['+this.workshop_ui.get_indice()+'][instruments]['+i+'][effective]', value:instruments[i].effective});
						json_data.push({id:this.instruments_list.get_hidden_field_name()+'_workshops_'+this.workshop_ui.workshop.get_order()+'_instruments_'+instruments[i].order+'_id_workshop_instrument', name:this.instruments_list.get_hidden_field_name()+'[workshops]['+this.workshop_ui.get_indice()+'][instruments]['+i+'][id_workshop_instrument]', value:instruments[i].id_workshop_instrument});
					}
				}
				return json_data;
			},
			init_instruments_action: function(){
				for(var i=0 ; i<this.instruments.length ; i++){
					this.instruments[i].init_actions();
				}
			},
			update_exotic_instruments_note: function() {
		    	var exotic_instruments_note = this.textarea_note.value;
		    	this.instruments_list.nomenclature.record_formation.set_exotic_instruments_note(exotic_instruments_note);
		    	this.update_hidden_fields();
		    },
		    update_hidden_fields:function (){
				this.hidden_note.value = this.instruments_list.nomenclature.record_formation.get_exotic_instruments_note();
			},
	    });
	});