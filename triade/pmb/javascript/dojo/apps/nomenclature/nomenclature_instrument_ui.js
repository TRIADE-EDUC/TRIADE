// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_instrument_ui.js,v 1.54 2022/06/08 14:45:16 rtigero Exp $


define(["dojo/_base/declare", "dojo/on","dojo/dom-construct","dojo/dom", "dojo/dom-attr", "dojo/_base/lang", "dojo/topic", "apps/nomenclature/nomenclature_instrument", "dijit/_WidgetBase", "dijit/registry", "dojo/request/xhr", "apps/nomenclature/nomenclature_dialog"], function(declare, on, domConstruct, dom, domAttr, lang, topic, Instrument, _WidgetBase, registry, xhr, Dialog){
	/*
	 *Classe nomenclature_instrument_ui. Classe g�n�rant la partie du formulaire li�e a un instrument
	 */
	  return declare("instrument_ui",[_WidgetBase], {
			    
		  	instrument:null,
		  	dom_node:null,
		  	self_node:null,
		  	input_order:null,
		  	input_main_instr:null,
		  	input_annexe_instr:null,
		  	input_effective:null,
		  	span_order:null,
		  	indice:0,
		  	id:null,
		  	mode:null,
		  	dialog:null,
		  	create_record_button: null,
		  	show_record_button: null,
		  	record_id: 0,
		  	
		    constructor: function(params){
		    	if (!this.mode) {
		    		this.mode = "musicstand";
		    	}
		    	this.own(
		    		topic.subscribe("instrument_ui",lang.hitch(this, this.handle_events)),
	    			topic.subscribe("form_instrument",lang.hitch(this, this.handle_events)),
	    			topic.subscribe("nomenclature_ui",lang.hitch(this, this.handle_events))
		    	);
		    },
		    
		    handle_events : function(evt_type,evt_args){
		    	switch(evt_type){
		    	 	case "input_change" :
		    	 		if(evt_args.hash == this.instrument.get_hash()){
		    	 			this.input_changed();
		    	 		}
		    			break;
					case "sync_and_save_end":
						if (this.instrument.get_hash().indexOf(evt_args.hash) != -1) {
							this.update_record();
						}
						break;
				}
		    },
		    
		    buildRendering: function(){ 
		    	this.inherited(arguments);
		    	this.domNode = domConstruct.create('tr', null, this.get_dom_node());
		    	this.domNode.setAttribute('order', this.instrument.get_order());
		    	this.domNode.setAttribute("draggable", "yes");
		    	this.domNode.setAttribute("dragtype", "instru");
		    	if (this.instrument.musicstand != null) {
		    		this.domNode.setAttribute("musicstand", this.instrument.musicstand.get_name());
		    	}
		    	this.domNode.setAttribute("id_instru", this.instrument.get_id()+'_'+this.get_indice());
		    	this.domNode.setAttribute("recept", "yes");
		    	this.domNode.setAttribute("dragtext", this.instrument.get_code());
		    	this.domNode.setAttribute("highlight", "instru_highlight");
		    	this.domNode.setAttribute("recepttype", "instru");
		    	this.domNode.setAttribute("downlight", "instru_downlight");
		    	this.domNode.setAttribute("dragicon", pmbDojo.images.getImage('icone_drag_notice.png'));
		    	this.domNode.setAttribute("handler", this.get_dom_node().id+'_handle_'+this.instrument.get_id()+'_'+this.get_indice());
//		    	this.srcNodeRef = this.dom_node;
		    	this.build_form();
		    },
		    
		    build_form: function(){
		    	var callback_change = lang.hitch(this, function(){
		    		topic.publish("instrument_ui","input_change",{
		    			hash : this.instrument.get_hash(),
		    		})
		    	});
		    	var object_value = this;
	    		window.nomenclature_input_callback = function(){
	    			var id = arguments[0];
	    			var call_dialog = arguments[1];
	    			if(call_dialog){
	    				this.dialog = new Dialog();
	    				this.dialog.sourceNode = dom.byId(id);
	    				this.dialog.set_code();
	    				this.dialog.show();
	    			} else {
	    				if(id.match('_input_instr')){
		    				var dijit_id = id.split('_input_instr')[0];
		    			}else if(id.match('_input_other_inst')){
		    				var dijit_id = id.split('_input_other_inst')[0];
		    			}
		    			if(dijit_id != undefined){
		    				topic.publish("instrument_ui","input_change",{
				    			hash : dijit.registry.byId(dijit_id).instrument.get_hash(),
				    		});
		    			}
	    			}
	    		}
	    		if((this.instrument.get_musicstand()!=undefined && !this.instrument.get_musicstand().get_divisable()) || this.mode == "exotic_instruments" || this.mode == "workshop"){
		    		var td_order = domConstruct.create('td', null, this.domNode);
			    	var span_order = domConstruct.create('span', {style:{float:'left',paddingRight:'7px'}, id:this.get_dom_node().id+'_handle_'+this.instrument.get_id()+'_'+this.get_indice()} , td_order);
			    	this.span_order = domConstruct.create('span', {style:{position:'relative',paddingRight:'7px'}, innerHTML:this.instrument.get_order(), id:this.get_dom_node().id+'_order_label_'+this.instrument.get_id()+'_'+this.get_indice()} , td_order);
			    	var img = domConstruct.create('img', {style:{width:"20px", verticalAlign:'middle'}, src:pmbDojo.images.getImage('sort.png')}, span_order);
	
			    	var td_main_instr = domConstruct.create('td', null,this.domNode);
			    	this.input_main_instr = domConstruct.create('input', {
			    		name:this.get_id()+'_input_instr', 
			    		type:'text', 
			    		id:this.get_id()+'_input_instr', 
			    		value:this.instrument.get_code(),
			    		autocomplete:'off',
			    		completion:'instruments',
			    		callback:"nomenclature_input_callback",
			    		//autfield:this.get_id()+'_input_instr'
			    	},td_main_instr);
					if (this.mode == "musicstand") {
						domAttr.set(this.input_main_instr, 'param1', this.instrument.get_musicstand().get_id());
						domAttr.set(this.input_main_instr, 'param2', 1);
					}
					if (this.mode == "workshop") {
						domAttr.set(this.input_main_instr, 'param1', 'workshop');
						domAttr.set(this.input_main_instr, 'param2', 1);
					}
			    	this.own(on(this.input_main_instr, 'change', callback_change));
			    	
			    	switch (this.mode) {
				    	case "exotic_instruments":
				    		var others_inst = ""; 
					    	if(this.instrument.others_instruments!=null){
					    		var others_inst_array = this.instrument.get_others_instruments();
					    		for(var i=0 ; i<others_inst_array.length ; i++){
					    			others_inst+=others_inst_array[i].get_code();
					    			if(i<others_inst_array.length-1){
					    				others_inst+='/';
					    			}
					    		}
					    	}
					    	
					    	var td_annexe_instr = domConstruct.create('td', null,this.domNode);
					    	this.input_annexe_instr = domConstruct.create('input', {
					    		name:this.get_id()+'_input_other_inst', 
					    		id:this.get_id()+'_input_other_inst', 
					    		type:'text', 
					    		value:others_inst,
					    		autocomplete:'off',
					    		completion:'instruments',
					    		callback:"nomenclature_input_callback",
					    		separator:'/',
					    		//autfield:this.get_id()+'_input_other_inst'
					    	}, td_annexe_instr);
					    	this.own(on(this.input_annexe_instr, 'change', callback_change));
					    	var td_effective = domConstruct.create('td', null,this.domNode);
					    	this.input_effective = domConstruct.create('input', {
					    		name:this.get_id()+'_input_effective', 
					    		id:this.get_id()+'_input_effective', 
					    		type:'text', 
					    		value:this.instrument.get_effective(),
					    	}, td_effective);
					    	this.own(on(this.input_effective, 'change', callback_change));
					    	break;
				    	case "workshop":
				    		var td_effective = domConstruct.create('td', null,this.domNode);
					    	this.input_effective = domConstruct.create('input', {
					    		name:this.get_id()+'_input_effective', 
					    		id:this.get_id()+'_input_effective', 
					    		type:'text', 
					    		value:this.instrument.get_effective(),
					    	}, td_effective);
					    	this.own(on(this.input_effective, 'change', callback_change));
					    	break;
				    	case "musicstand":
				    		var others_inst = ""; 
					    	if(this.instrument.others_instruments!=null){
					    		var others_inst_array = this.instrument.get_others_instruments();
					    		for(var i=0 ; i<others_inst_array.length ; i++){
					    			others_inst+=others_inst_array[i].get_code();
					    			if(i<others_inst_array.length-1){
					    				others_inst+='/';
					    			}
					    		}
					    	}
					    	var td_annexe_instr = domConstruct.create('td', null,this.domNode);
					    	this.input_annexe_instr = domConstruct.create('input', {
					    		name:this.get_id()+'_input_other_inst', 
					    		id:this.get_id()+'_input_other_inst', 
					    		type:'text', 
					    		value:others_inst,
					    		autocomplete:'off',
					    		completion:'instruments',
					    		callback:"nomenclature_input_callback",
					    		separator:'/',
					    		//autfield:this.get_id()+'_input_other_inst',
					    		param1:this.instrument.get_musicstand().get_id(),
					    		param2:1
					    	}, td_annexe_instr);
					    	this.own(on(this.input_annexe_instr, 'change', callback_change));
					    	break;
			    	}
	    		}else{
		    		var td_order = domConstruct.create('td', null, this.domNode);
			    	var span_order = domConstruct.create('span', {style:{float:'left',paddingRight:'7px'}, id:this.get_dom_node().id+'_handle_'+this.instrument.get_id()+'_'+this.get_indice()} , td_order);
			    	this.span_order = domConstruct.create('span', {style:{position:'relative',paddingRight:'7px'}, innerHTML:this.instrument.get_order(), id:this.get_dom_node().id+'_part_label_'+this.instrument.get_id()+'_'+this.get_indice()} , td_order);
			    	var img = domConstruct.create('img', {style:{width:"20px", verticalAlign:'middle'}, src:pmbDojo.images.getImage('sort.png')}, span_order);
			    	
			    	var td_effective = domConstruct.create('td', null,this.domNode);
			    	this.input_effective = domConstruct.create('input', {
			    		name:this.get_id()+'_input_effective', 
			    		id:this.get_id()+'_input_effective', 
			    		type:'text', 
			    		value:(this.instrument.is_indefinite_effective()?this.get_nomenclature().indefinite_character:this.instrument.get_effective()),
			    	}, td_effective);
			    	this.own(on(this.input_effective, 'change', callback_change));
			    	
			    	var td_main_instr = domConstruct.create('td', null,this.domNode);
			    	this.input_main_instr = domConstruct.create('input', {
			    		name:this.get_id()+'_input_instr', 
			    		type:'text', 
			    		id:this.get_id()+'_input_instr', 
			    		value:this.instrument.get_code(),
			    		autocomplete:'off',
			    		completion:'instruments',
			    		callback:"nomenclature_input_callback",
			    		separator:'/',
			    		//autfield:this.get_id()+'_input_instr'
			    	},td_main_instr);
			    	this.own(on(this.input_main_instr, 'change', callback_change));
	    		}
		    	this.td_suppression = domConstruct.create('td', null, this.domNode);
		    	var bouton_delete = domConstruct.create('input', {
		    		type:'button', 
		    		value:'X',
		    		class:" bouton"
		    	}, this.td_suppression);
		    	
		    	this.own(on(bouton_delete, "click", lang.hitch(this, function(){
		    		this.delete_record_child();
		    	})));
		    	this.ajax_parse();
		    },
		    
		    init_actions: function (){
		    	if(this.get_nomenclature().record_formation.get_record() != 0){
		    		xhr("./ajax.php?module=ajax&categ=nomenclature&sub=record_child&action=get_child&id_parent="+this.get_nomenclature().record_formation.get_record(), {
						handleAs: "json",
						method:"POST",
						data:this.ajax_prepare_args()
					}).then(lang.hitch(this,this.got_record),function(err){console.log(err)})
		    	}
		    },
		    
		    got_record : function(record_id) {
		    	if(record_id!= 0){
		    		this.record_id = record_id;
		    		if(!this.show_record_button){
			    		this.show_record_button = domConstruct.create("input",{
			    			type: "button",
			    			class: "bouton",
			    			value: registry.byId("nomenclature_datastore").get_message("nomenclature_js_see_record")
			    		},this.td_suppression);
			    		this.own(on(this.show_record_button,"click",function(){
			    			window.open("./catalog.php?categ=modif&id="+record_id);
			    		}));
		    		}
		    	}else{
		    		if(!this.create_record_button){
			    		this.create_record_button = domConstruct.create("input",{
			    			type: "button",
			    			class: "bouton",
			    			value: registry.byId("nomenclature_datastore").get_message("nomenclature_js_create_record")
			    		},this.td_suppression);
			    		this.own(on(this.create_record_button,"click",lang.hitch(this,this.create_record_button_callback)));
		    		}
		    	}
		    },
		    create_record_button_callback: function(){
		    	if(this.get_nomenclature_ui().ajax_save()){
		    		this.create_record(lang.hitch(this, this.record_created), "create");
		    	}
		    },
		    create_record: function(callback, action){
	    		xhr("./ajax.php?module=ajax&categ=nomenclature&sub=record_child&action="+action+"&id_parent="+this.get_nomenclature().record_formation.get_record(), {
					handleAs: "json",
					method:"POST",
					data:this.ajax_prepare_args()
				}).then(callback,function(err){console.log(err)});
		    },
		    
		    create_child: function(){
		    	if(!this.record_id){
			    	this.create_record(lang.hitch(this, this.create_children_callback), "create_children");
		    	} else {
					topic.publish("instrument_ui", "submanifestation_created", {
						hash: this.instrument.get_hash(),
						is_new: false
					});
		    	}
		    },
		    create_children_callback: function(data){
		    	if(data.new_record){
		    		this.record_created(data);
					topic.publish("instrument_ui", "submanifestation_created", {
						hash: this.instrument.get_hash(),
						is_new: true
					});
		    	}else{
		    		this.got_record(data.id);
					topic.publish("instrument_ui", "submanifestation_created", {
						hash: this.instrument.get_hash(),
						is_new: false
					});
		    	}
		    },
		    record_created: function(record){
		    	if(record){
		    		this.record_id = record.id;
		    		if(!this.show_record_button){
			    		this.show_record_button = domConstruct.create("input",{
			    			type: "button",
			    			class: "bouton",
			    			value: registry.byId("nomenclature_datastore").get_message("nomenclature_js_see_record")
			    		},this.td_suppression);
			    		this.own(on(this.show_record_button,"click",function(){
			    			window.open("./catalog.php?categ=modif&id="+record.id);
			    		}));
		    		}
		    		if(this.create_record_button){
		    			this.td_suppression.removeChild(this.create_record_button);	
		    		}
		    		topic.publish('instrument_ui',"partial_record_created",{
		    			hash: this.instrument.get_hash(),
		    			record_id: record.id,
		    			record_title: record.title,
		    			record_reverse_id_notices_relations : record.reverse_id_notices_relations,
		    			record_reverse_num_reverse_link : record.reverse_num_reverse_link
		    		});
		    	}
		    },
		    
		    reorder: function(){
		    	this.input_order.value = this.instrument.get_order();
		    },
		    input_change_order: function(){
		    	if(!isNaN(this.input_order.value)){
		    		this.instrument.set_order(parseInt(this.input_order.value));	
		    	}
		    },
		    input_changed: function(instrument_changed){
		    	if(this.input_main_instr.value != ""){
	    			this.instrument.set_code(this.input_main_instr.value.trim());
	    			var tree_instruments_datastore = registry.byId('nomenclature_datastore').get_instruments_datastore();
	    			for(var i=0 ; i<tree_instruments_datastore.length ; i++){
	    				if (tree_instruments_datastore[i]['code'] == this.instrument.get_code()) {
	    					this.instrument.set_name(tree_instruments_datastore[i]['name']);
	    					this.instrument.set_id(tree_instruments_datastore[i]['id']);
	    					break;
	    				}
					}
	    			if (this.instrument.musicstand != null) {
		    			if(typeof this.instrument.musicstand.get_standard_instrument == "function"){
		    				if(this.input_main_instr.value.trim() != this.instrument.musicstand.get_standard_instrument().get_code()){
		    					this.instrument.set_standard(false);
		    				}else{
		    					this.instrument.set_standard(true);
		    				}
	    				}
		    			if(this.input_effective != null){
				    		if(this.instrument.musicstand.get_divisable()){
				    			if(isNaN(parseInt(this.input_effective.value))){
					    			this.instrument.set_effective(0);
					    			this.instrument.set_indefinite_effective(true);
					    			this.input_effective.value = this.get_nomenclature().indefinite_character;
					    		}else{
					    			this.instrument.set_indefinite_effective(false);
					    			this.instrument.set_effective(this.input_effective.value);	
					    		}	
				    		}
				    	}
	    			} else if(this.input_effective != null){
	    		    	this.instrument.set_effective(parseInt(this.input_effective.value));
	    			}
	    		}
	    		if (this.input_annexe_instr) {
	    			var new_values_other_inst = this.input_annexe_instr.value;
			    	var new_values_other_inst = new_values_other_inst.split('/');
			    	
			    	this.instrument.others_instruments = null;
			    	
			    	for(var i=0 ; i<new_values_other_inst.length ; i++){
			    		new_values_other_inst[i] = new_values_other_inst[i].trim(); 
			    		if(new_values_other_inst[i].length>0){
			    			var new_other_inst = new Instrument(new_values_other_inst[i])
			    			if(this.instrument.others_instruments == null){
			    				this.instrument.others_instruments = new Array();
			    			}
			    			this.instrument.add_other_instrument(new_other_inst);
			    		}
			    	}
	    		}
	    		this.update_record();
		    	this.publish_event("instru_changed");
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
			get_instrument: function() {
				return this.instrument;
			},
			set_instrument: function(instrument) {
				this.instrument = instrument;
			},
			get_indice: function() {
				return this.indice;
			},
			
			set_indice: function(indice) {
				this.indice = indice;
			},
			set_order: function(order){
				this.instrument.set_order(order);
				this.domNode.setAttribute("order", order);
				this.span_order.innerHTML = order;
				this.update_record();
				this.publish_event('instru_changed');
			},
			get_order:function(){
				return this.instrument.get_order();
			},
			get_part: function() {
				return this.instrument.get_part();
			},
			
			postCreate:function(){
				this.inherited(arguments);
				parse_drag(this.dom_node);

				this.publish_event("dom_ready");
			},
			destroy: function(){
				this.instrument = null;
				this.inherited(arguments);
			},
			set_id: function(id){
				this.id = id;
			},
			get_id: function(){
				return this.id;
			},
			ajax_parse: function(){
				if (this.input_main_instr) ajax_pack_element(this.input_main_instr);
				if (this.input_annexe_instr) ajax_pack_element(this.input_annexe_instr);
			},
			publish_event: function(event_name){
				var event_args = {};
				event_args.mode = this.mode;
				event_args.hash = this.instrument.get_hash();
				switch(this.mode){
					case "musicstand":
						event_args.musicstand_hash = this.instrument.musicstand.get_hash();
						break;
					case "workshop" :
						//event_args.musicstand_hash = this.instrument.musicstand.get_hash();
						break;
					case "exotic_instruments":
						//event_args.musicstand_hash = this.instrument.musicstand.get_hash();
						break;
				}
				switch(event_name){
					case "instrument_delete":
							event_args.order = this.instrument.get_order();	
						break;
				}
				/**
				 * For debug
				 * console.log(' publish evt: ', event_name, 'evt args;', event_args);
				 */
				topic.publish("instrument_ui", event_name, event_args);
			},
			get_nomenclature:function(){
				switch(this.mode){
					case "musicstand":
						return this.instrument.musicstand.family.nomenclature;
					case "workshop" :
						return this.instrument.instruments_list.workshop.nomenclature;
					case "exotic_instruments":
						return this.instrument.instruments_list.nomenclature;
				}
			},
			get_nomenclature_ui: function(){
				return registry.byId(this.get_nomenclature().get_hash());
			},
			ajax_prepare_args:function(){
				var args = "&record_child_data[num_formation]="+this.get_nomenclature().record_formation.formation.get_id();
		    	args+="&record_child_data[num_type]="+(this.get_nomenclature().record_formation.get_type() ? this.get_nomenclature().record_formation.get_type().id : '0');
		    	switch(this.mode){
		    		case "exotic_instruments":
			    		args+="&record_child_data[num_musicstand]=0";
			    		args+="&record_child_data[num_instrument]="+this.instrument.get_id();
			    		args+="&record_child_data[num_nomenclature]="+this.get_nomenclature().record_formation.get_id();
			    		args+="&record_child_data[num_voice]=0";
			    		args+="&record_child_data[num_workshop]=0";
				    	break;
			    	case "workshop":
			    		args+="&record_child_data[num_musicstand]=0";
			    		args+="&record_child_data[num_instrument]="+this.instrument.get_id();
			    		args+="&record_child_data[num_nomenclature]="+this.get_nomenclature().record_formation.get_id();
			    		args+="&record_child_data[num_voice]=0";
			    		args+="&record_child_data[num_workshop]="+this.instrument.instruments_list.workshop.get_id();
				    	break;
			    	case "musicstand":
			    		args+="&record_child_data[num_musicstand]="+this.instrument.musicstand.get_id();
			    		args+="&record_child_data[num_instrument]="+this.instrument.get_id();
			    		args+="&record_child_data[num_nomenclature]="+this.get_nomenclature().record_formation.get_id();
			    		args+="&record_child_data[num_voice]=0";
			    		args+="&record_child_data[num_workshop]=0";
				    	break;	
		    	}
	    		var others = new Array();
	    		var others_instruments = this.instrument.get_others_instruments();
	    		if(others_instruments){
	    			for(var i=0; i<others_instruments.length; i++){
		    			if(typeof others_instruments[i] == "object"){
		    				others.push(others_instruments[i].get_code());
		    			}
		    		}
	    		}
	    		if(others.length){
	    			args+="&record_child_data[other]="+others.join('/');
	    		}
	    		args+="&record_child_data[effective]="+(this.instrument.is_indefinite_effective() ? this.get_nomenclature().indefinite_character : this.instrument.get_effective());
	    		args+="&record_child_data[order]="+this.instrument.get_order();
	    		return args;
			},
			update_record: function(){
				if(this.record_id){
					xhr("./ajax.php?module=ajax&categ=nomenclature&sub=record_child&action=update_record&id="+this.record_id, {
						handleAs: "json",
						method:"POST",
						data:this.ajax_prepare_args()
					});
				}
			},
			delete_record_child: function(){
				if(this.record_id){
					xhr("./ajax.php?module=ajax&categ=nomenclature&sub=record_child&action=get_explnums&id="+this.record_id, {
						handleAs: "json",
						method:"GET",
					}).then((explnums)=>{
						if(explnums && explnums.length) {
							var message = registry.byId('nomenclature_datastore').get_message('nomenclature_js_explnum_confirm');
							if(!confirm(message)){
								return;
							}
						}
						xhr("./ajax.php?module=ajax&categ=nomenclature&sub=record_child&action=delete_record&id="+this.record_id, {
							handleAs: "json",
							method:"POST",
							data:this.ajax_prepare_args()
						}).then(() => this.publish_event('instrument_delete'));
					});
				} else {
					this.publish_event('instrument_delete');
				}
			}
	    });
	});