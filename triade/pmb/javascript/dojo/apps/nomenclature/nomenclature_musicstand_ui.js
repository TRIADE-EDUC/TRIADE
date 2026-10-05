// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_musicstand_ui.js,v 1.51 2016/11/29 13:00:29 vtouchard Exp $

define(["dojo/_base/declare", "apps/nomenclature/nomenclature_instrument_ui","apps/nomenclature/nomenclature_instrument", "dojo/on", "dojo/dom-construct","dojo/dom", "dojo/_base/lang", "dojo/topic", "dijit/registry", "dijit/_WidgetBase"], function(declare, Instrument_ui, Instrument, on, domConstruct, dom, lang, topic, registry, _WidgetBase){
	/*
	 *Classe nomenclature_musicstand_ui. Classe générant la partie du formulaire liée a un pupitre
	 */
	  return declare("nomenclature_musicstand_ui",[_WidgetBase], {
			    
		  	musicstand:null,
		  	dom_node:null,
		  	instruments:null,
		  	total_instruments:0,
		  	instruments_node:null,
		  	init_display:false,
		  	displayed_effective:'',
		  	
		    constructor: function(params){
		    	this.instruments = new Array();
		    	this.own(topic.subscribe("nomenclature", lang.hitch(this, this.handle_events)));
		    	this.own(topic.subscribe("instrument_ui", lang.hitch(this, this.handle_events)));
		    	this.own(topic.subscribe("family_ui", lang.hitch(this, this.handle_events)));
		    	this.own(topic.subscribe("workshops_ui", lang.hitch(this, this.handle_events)));
		    },
			
		    handle_events : function(evt_type,evt_args){
		    	switch(evt_type){
		    		case "end_analyze" :
		    			if(evt_args.hash == this.musicstand.family.nomenclature.get_hash()){
		    				this.init_instruments_ui();
		    			}
		    			break;
		    		case "dom_ready" :
		    			if(evt_args.mode == "musicstand" && evt_args.musicstand_hash == this.musicstand.get_hash()){
		    				this.instrument_ready();
		    			}
		    			break;
		    		case "instru_changed" :
		    			if(evt_args.mode == "musicstand" && evt_args.musicstand_hash == this.musicstand.get_hash()){		
		    				if(this.musicstand.get_divisable()){
		    					this.update_input_effective();
		    				}
		    				topic.publish("musicstand_ui","musicstand_changed", {
		    		    		hash : this.musicstand.get_hash(),
		    		    		family_hash : this.musicstand.family.get_hash()
		    				});
		    				
		    			}
		    		case "workshop_deleted" :
		    		case "new_workshop" :
		    		case "workshop_state_changed":
		    			if(this.musicstand.get_hash().indexOf(evt_args.nomenclature_hash)!=-1 && this.musicstand.get_used_by_workshops()){
		    				this.musicstand.calc_effective();
		    				this.update_input_effective();
		    				//console.log('musicstand effective & is_defined', this, this.musicstand.is_indefinite_effective(), this.musicstand.get_effective());
		    				topic.publish("musicstand_ui","musicstand_changed", {
		    		    		hash : this.musicstand.get_hash(),
		    		    		family_hash : this.musicstand.family.get_hash()
			    			});
		    			}
		    			break;
		    		case 'instrument_delete':
		    			if(evt_args.mode == "musicstand" && evt_args.musicstand_hash == this.musicstand.get_hash()){
		    				this.delete_instrument_event(evt_args.order);
		    			}
		    			break;
		    		case 'family_expanded': 
		    			if(this.musicstand.get_hash().indexOf(evt_args.hash)!=-1){
		    				this.ajax_dispatch();
		    			}
		    			break;
		    	}
		    },
		    
		    buildRendering: function(){
		    	this.inherited(arguments);
		    	this.build_form();
		    },
		    
		    build_form: function(){
		    	var row_div = domConstruct.create('div', {class:'row'}, this.get_dom_node());
		    	//var div_colonne1 = domConstruct.create('div', {class:'colonne5'}, row_div);
		    	var h3_node = domConstruct.create('h3', {class:"colonne5",innerHTML:(!this.musicstand.get_divisable()?registry.byId('nomenclature_datastore').get_message('nomenclature_js_musicstand_label')+' ' : '')+this.musicstand.get_name()}, row_div);
		    	//var div_colonne2 = domConstruct.create('div', {class:'colonne5'}, row_div);
		    	var suite = domConstruct.create('div', {class:'colonne_suite'}, row_div);
		    	var input_effective = domConstruct.create('input', {
		    		id:this.dom_node.id+'_'+this.musicstand.get_id()+'_input_effective', 
		    		type:'text',
		    		style:{
		    			width:'30px',
		    		}, 
		    		value:0
		    	}, suite);
		    	if(!this.musicstand.get_used_by_workshops()){
		    		on(input_effective, 'keyup', lang.hitch(this, this.effective_changed));
			    	//var div_colonne3 = domConstruct.create('div', {class:'colonne5'}, row_div);
			    	var input_plus = domConstruct.create('input', {type:'button', value:'+', class:'bouton'}, suite);
			    	var div_colonne4 = domConstruct.create('div', {class:'row'}, this.get_dom_node());
			    	on(input_plus, 'click', lang.hitch(this, this.add_instrument));
			    	var input_nb_instruments = domConstruct.create('input', {
			    		type:'hidden', 
			    		id:this.get_dom_node().id+'_'+this.musicstand.get_id()+'_count', 
			    	}, this.get_dom_node());
			    	this.set_instruments_node(domConstruct.create('table', {
			    		id:this.get_dom_node().id+'_'+this.musicstand.get_id()+'_'+this.musicstand.get_id(),
			    		style:"display:none;"
			    	}, this.get_dom_node()));
			    	if(!this.musicstand.get_divisable()){
				    	var header_line = domConstruct.create('tr',null,this.get_instruments_node());
				    	var th_order = domConstruct.create('th', {rowspan:'2', innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_instruments_header_order'), style:{textAlign:'center'}}, header_line);
				    	var th_instrument = domConstruct.create('th', {colspan:'2', innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_instruments_header_instruments'), style:{textAlign:'center'}}, header_line);
	
				    	var th_bouton_delete = domConstruct.create('th', {rowspan:'2', style:{textAlign:'center'}}, header_line);
				    	var header_line_2 = domConstruct.create('tr', null, this.get_instruments_node());
				    	var th_main_instr = domConstruct.create('th', {innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_instruments_header_main'), style:{textAlign:'center'}}, header_line_2);
				    	var th_annexe_instr = domConstruct.create('th', {innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_instruments_header_other'), style:{textAlign:'center'}}, header_line_2);
			    	}else{
			    		var header_line = domConstruct.create('tr',null,this.get_instruments_node());
				    	var th_order = domConstruct.create('th', {innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_instruments_header_part'), style:{textAlign:'center'}}, header_line);
				    	var th_effective = domConstruct.create('th', {innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_instruments_header_effective'), style:{textAlign:'center'}}, header_line);
				    	var th_instrument = domConstruct.create('th', {innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_instruments_header_instruments'), style:{textAlign:'center'}}, header_line);
				    	var th_bouton_delete = domConstruct.create('th', {style:{textAlign:'center'}}, header_line);		    		
			    	}
		    	}else{
		    		input_effective.disabled = "disabled";
		    	}
		    	this.init_instruments_ui();
		    },
		    init_instruments_ui: function(){
		    	if(!this.musicstand.get_used_by_workshops()){
			    	this.total_instruments = 0;
			    	this.init_display = false;
		    		this.get_input_effective().value = this.get_displayed_effective();
		    		if(this.musicstand.get_effective()>0 || this.musicstand.is_indefinite_effective()){
				    	for(var i=0; i<this.musicstand.instruments.length ; i++){
				    		if(!this.musicstand.instruments[i].is_standard() || (this.musicstand.instruments[i].others_instruments != null && this.musicstand.instruments[i].others_instruments.length > 0 ) || (this.musicstand.get_divisable() && ((this.musicstand.instruments.length > 1)))){
				    			this.init_display = true;
				    		}
				    		this.instruments.push(new Instrument_ui({id:this.get_dom_node().id+'_'+this.musicstand.get_id()+'_'+this.musicstand.instruments[i].get_id()+'_'+this.get_total_instruments(), indice: this.get_total_instruments(), dom_node: this.get_instruments_node(), instrument: this.musicstand.instruments[i]}));
				    	}
			    	}else{
						topic.publish('musicstand_ui', 'musicstand_ready', {
				    		hash : this.musicstand.get_hash(),
				    		family_hash : this.musicstand.family.get_hash()
						});
			    	}
		    	} else {
		    		this.get_input_effective().value = this.get_displayed_effective();
		    		topic.publish('musicstand_ui', 'musicstand_ready', {
			    		hash : this.musicstand.get_hash(),
			    		family_hash : this.musicstand.family.get_hash()
					});
		    	}
	    		this.display_instruments_node();
		    },
		    add_instrument: function(){
		    	if(this.instruments_node.style.display == "none"){
    				var std_inst = this.musicstand.get_standard_instrument();
		    		if(!isNaN(this.get_input_effective().value) && parseInt(this.get_input_effective().value) > 0){
		    			this.init_display = true;
			    		this.display_instruments_node();
			    		this.purge_instruments();
			    		var nb_new_inst = 0;
		    			nb_new_inst = parseInt(this.get_input_effective().value);
		    			if(!this.musicstand.get_divisable()){
			    			for(var i=0 ; i<nb_new_inst ; i++){
			    		    	var new_inst = new Instrument(std_inst.get_code(),std_inst.get_name());
			    		    	new_inst.set_effective(1);
			    		    	new_inst.set_part(0);
			    		    	new_inst.set_order(this.musicstand.get_max_order()+1);
	
			    		    	this.musicstand.add_instrument(new_inst);
			    		    	var new_inst_ui = new Instrument_ui({id:this.get_dom_node().id+'_'+this.musicstand.get_id()+'_'+new_inst.get_id()+'_'+this.get_total_instruments(), indice: this.total_instruments, dom_node: this.get_instruments_node(), instrument: new_inst});
			    		    	new_inst_ui.init_actions();
			    		    	this.instruments.push(new_inst_ui);
			    			}
		    			}else{
		    		    	var new_inst = new Instrument(std_inst.get_code(),std_inst.get_name());
		    		    	new_inst.set_effective(nb_new_inst);
		    		    	new_inst.set_order(1);

		    		    	this.musicstand.add_instrument(new_inst);
		    		    	var new_inst_ui = new Instrument_ui({id:this.get_dom_node().id+'_'+this.musicstand.get_id()+'_'+new_inst.get_id()+'_'+this.get_total_instruments(), indice: this.total_instruments, dom_node: this.get_instruments_node(), instrument: new_inst});
		    		    	new_inst_ui.init_actions();
		    		    	this.instruments.push(new_inst_ui);
	    				
		    			}
		    		} else if (isNaN(parseInt(this.get_input_effective().value)) && this.musicstand.get_divisable()) {
		    			this.init_display = true;
		    			this.purge_instruments();
	    		    	var new_inst = new Instrument(std_inst.get_code(),std_inst.get_name());
	    		    	new_inst.set_effective(0);
	    		    	new_inst.set_indefinite_effective(true);
	    		    	new_inst.set_order(1);
	    		    	this.musicstand.add_instrument(new_inst);
	    		    	var new_inst_ui = new Instrument_ui({id:this.get_dom_node().id+'_'+this.musicstand.get_id()+'_'+new_inst.get_id()+'_'+this.get_total_instruments(), indice: this.total_instruments, dom_node: this.get_instruments_node(), instrument: new_inst});
	    		    	new_inst_ui.init_actions();
	    		    	this.instruments.push(new_inst_ui);
	    		    	this.get_input_effective().value = this.musicstand.family.nomenclature.indefinite_character;
		    		}
		    		ajax_resize_elements();
		    		
		    	}else{
			    	var input_count = dom.byId(this.get_dom_node().id+'_'+this.musicstand.get_id()+'_count');
			    	input_count.value = parseInt(input_count.value)+1; 
			    	var std_inst = this.musicstand.get_standard_instrument();
			    	
			    	var new_inst = new Instrument(std_inst.get_code(),std_inst.get_name());
			    	new_inst.set_effective(1);
			    	new_inst.set_part(0);
    		    	new_inst.set_order(this.musicstand.get_max_order()+1);
    		    	
			    	this.musicstand.add_instrument(new_inst);
			    	var new_inst_ui = new Instrument_ui({id:this.get_dom_node().id+'_'+this.musicstand.get_id()+'_'+new_inst.get_id()+'_'+this.get_total_instruments(), indice: this.total_instruments, dom_node: this.get_instruments_node(), instrument: new_inst});
			    	this.instruments.push(new_inst_ui)
			    	new_inst_ui.init_actions();
			    	this.musicstand.calc_effective();
			    	if (this.musicstand.is_indefinite_effective()) {
			    		this.get_input_effective().value = this.musicstand.family.nomenclature.indefinite_character;
			    	} else {
			    		this.get_input_effective().value = parseInt(this.musicstand.get_effective());
			    	}
			    	if(this.musicstand.instruments.length) {
				    	topic.publish("musicstand_ui","musicstand_changed", {
				    		hash : this.musicstand.get_hash(),
				    		family_hash : this.musicstand.family.get_hash()
				    	});
			    	}
		    	}
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
		    	this.musicstand.delete_instrument(order, true);
		    	
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
		    	topic.publish("musicstand_ui","musicstand_changed", {
		    		hash : this.musicstand.get_hash(),
		    		family_hash : this.musicstand.family.get_hash()
		    	});
		    	
		    	var input_count = dom.byId(this.dom_node.id+'_'+this.musicstand.get_id()+'_count');
		    	input_count.value = parseInt(input_count.value)-1;
		    	this.musicstand.calc_effective();
		    	if (this.musicstand.is_indefinite_effective()) {
		    		this.get_input_effective().value = this.musicstand.family.nomenclature.indefinite_character;
		    	} else {
		    		this.get_input_effective().value = parseInt(this.musicstand.get_effective());
		    	}
		    	
		    	if(!this.instruments.length){
		    		this.init_display = false;
		    		this.display_instruments_node();
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
			get_musicstand: function() {
				return this.musicstand;
			},
			set_musicstand: function(musicstand) {
				this.musicstand = musicstand;
			},
			get_instruments_node: function() {
				return this.instruments_node;
			},
			set_instruments_node: function(instruments_node) {
				this.instruments_node = instruments_node;
			},
			instrument_ready: function(){
				this.total_instruments++;
				var instruments = this.musicstand.get_instruments();
				
				if(this.total_instruments == instruments.length){
					topic.publish('musicstand_ui', 'musicstand_ready', {
			    		hash : this.musicstand.get_hash(),
			    		family_hash : this.musicstand.family.get_hash()
					});
					this.display_instruments_node();
				}
			},
			
			purge_instruments : function(){
				for(var i in this.musicstand.get_instruments()){
					if(this.instruments[i]){
						this.instruments[i].destroy();
					}
				}
				this.musicstand.set_instruments(new Array());
				var input_count = dom.byId(this.dom_node.id+'_'+this.musicstand.get_id()+'_count');
				this.instruments = new Array();
		    	this.set_total_instruments(0);
				if(!this.musicstand.get_used_by_workshops()){
					input_count.value = 0;
					parse_drag(this.instruments_node);
				}
			},
			
			get_total_instruments: function() {
				return this.total_instruments;
			},
			
			set_total_instruments: function(total_instruments) {
				this.total_instruments = total_instruments;
			},
			get_input_effective: function(){
				return dom.byId(this.dom_node.id+'_'+this.musicstand.get_id()+'_input_effective');
			},
			effective_changed: function(){
				if(!isNaN(this.get_input_effective().value) && parseInt(this.get_input_effective().value) > 0){
					//on a saisie un nombre
					this.musicstand.set_indefinite_effective(false);
					this.purge_instruments();
		    		var nb_new_inst = 0;
		    		nb_new_inst = parseInt(this.get_input_effective().value);
		    		if(!this.musicstand.get_divisable()){
		    			for(var i=0 ; i<nb_new_inst ; i++){
		    				var std_inst = this.musicstand.get_standard_instrument();
		    		    	var new_inst = new Instrument(std_inst.get_code(),std_inst.get_name());
		    		    	new_inst.set_effective(1);
		    		    	new_inst.set_part(0);
		    		    	new_inst.set_order(this.musicstand.get_max_order()+1);
		    		    	this.musicstand.add_instrument(new_inst);
		    		    	var new_inst_ui = new Instrument_ui({id:this.get_dom_node().id+'_'+this.musicstand.get_id()+'_'+new_inst.get_id()+'_'+this.get_total_instruments(), indice: this.total_instruments, dom_node: this.get_instruments_node(), instrument: new_inst});
		    		    	this.instruments.push(new_inst_ui);
		    			}	
		    		}else{
		    			var std_inst = this.musicstand.get_standard_instrument();
		    			var new_inst = new Instrument(std_inst.get_code(),std_inst.get_name());
		    			new_inst.set_effective(nb_new_inst);
	    		    	new_inst.set_part(0);
	    		    	new_inst.set_order(this.musicstand.get_max_order()+1);
	    		    	this.musicstand.add_instrument(new_inst);
	    		    	var new_inst_ui = new Instrument_ui({id:this.get_dom_node().id+'_'+this.musicstand.get_id()+'_'+new_inst.get_id()+'_'+this.get_total_instruments(), indice: this.total_instruments, dom_node: this.get_instruments_node(), instrument: new_inst});
	    		    	this.instruments.push(new_inst_ui);
		    		}
	    			ajax_resize_elements();
				}else {
					this.purge_instruments();
					if(isNaN(this.get_input_effective().value)){
						var std_inst = this.musicstand.get_standard_instrument();
	    		    	var new_inst = new Instrument(std_inst.get_code(),std_inst.get_name());
	    		    	new_inst.set_effective(0);
	    		    	new_inst.set_indefinite_effective(true);
	    		    	new_inst.set_part(0);
	    		    	new_inst.set_order(1);
	    		    	this.musicstand.add_instrument(new_inst);
	    		    	var new_inst_ui = new Instrument_ui({id:this.get_dom_node().id+'_'+this.musicstand.get_id()+'_'+new_inst.get_id()+'_'+this.get_total_instruments(), indice: this.total_instruments, dom_node: this.get_instruments_node(), instrument: new_inst});
	    		    	this.instruments.push(new_inst_ui);
		    			ajax_resize_elements();
					}
				}
				topic.publish("musicstand_ui","musicstand_changed", {
		    		hash : this.musicstand.get_hash(),
		    		family_hash : this.musicstand.family.get_hash()
    			});
			},
			update_input_effective: function(){
				this.get_input_effective().value = this.get_displayed_effective();
			},
			get_displayed_effective: function(){
				/**
				 * TODO: (Traitement des différents cas de workshops à voir plus tard)
				 */
				this.displayed_effective = 0;
				if(!this.musicstand.get_used_by_workshops()){
					for(var i=0; i<this.musicstand.instruments.length ; i++){
						if(this.musicstand.instruments[i].is_indefinite_effective()){
							this.displayed_effective = this.musicstand.family.nomenclature.indefinite_character;
							break;
						}
						this.displayed_effective+= this.musicstand.instruments[i].get_effective();
					}	
				}else{
					if(this.musicstand.is_indefinite_effective()){
						this.displayed_effective = this.musicstand.family.nomenclature.indefinite_character;
					}else{
						this.displayed_effective = this.musicstand.get_effective();	
					}
				}
				return this.displayed_effective;
			},
			ajax_dispatch: function(){
				for(var i=0 ; i<this.instruments.length ; i++){
					this.instruments[i].init_actions();
				}
			},
			display_instruments_node: function(){
				if(this.instruments_node){
					if(this.init_display == false){
						this.instruments_node.style.display = "none";
						if(this.get_input_effective().getAttribute('readonly')){
							this.get_input_effective().removeAttribute('readonly');	
						}
						if(this.get_input_effective().getAttribute('disabled')){
							this.get_input_effective().removeAttribute('disabled');	
						}
						if(this.instruments_node.style.display == "table"){
							this.instruments_node.style.display = "none";
						}
					}else{
						this.get_input_effective().setAttribute('readonly', 'true');
						this.get_input_effective().setAttribute('disabled', 'disabled');
						this.instruments_node.style.display = "table";
					}
				}
			}
	    });
	});