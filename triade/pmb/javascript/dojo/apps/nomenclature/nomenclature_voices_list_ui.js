// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_voices_list_ui.js,v 1.7 2016/11/29 13:00:29 vtouchard Exp $

define(["dojo/_base/declare","dojo/dom-construct", "dojo/topic",  "dojo/on", "dojo/_base/lang", "dijit/_WidgetBase", "dijit/registry", "apps/nomenclature/nomenclature_voice_ui", "apps/nomenclature/nomenclature_voice", "dojo/dom"], function(declare, domConstruct, topic, on,lang, _WidgetBase,registry, Voice_ui, Voice, dom){
	/*
	 *Classe nomenclature_voices_list_ui. Classe gérant l'affichage d'une liste de voix
	 */
	  return declare("voices_list_ui", [_WidgetBase], {
			    
		  	voices_list:null, /** Instance du modèle lié **/
		  	nomenclature_voices_ui:null, /** Instance de l'ui parent **/
		  	voices_ui:null, /** Instance de l'ui gérée par cette classe **/
		  	id:0,
		  	events_handles: null,
		  	dom_node:null,
		  	total_voices:0,
		  	voices_node:null,
		  	textarea_note:null,
		  	hidden_note:null,

		    constructor: function(params){
		    	this.events_handles = new Array();
		    	this.voices_ui = new Array();
		    	this.events_handles.push(topic.subscribe('voice_ui', lang.hitch(this, this.handle_events)));
		    	this.events_handles.push(topic.subscribe('voices_list', lang.hitch(this, this.handle_events)));
		    	this.events_handles.push(topic.subscribe('nomenclature_voices', lang.hitch(this, this.handle_events)));
		    },
		    
		    buildRendering: function(){ 
		    	this.inherited(arguments);
		    	this.build_form();
		    },
		    
		    handle_events: function(evt_type, evt_args){
		    	switch(evt_type){
		    		case 'voice_ui_ready':
		    			if(evt_args.hash.indexOf(this.voices_list.get_hash()) != -1){
		    				/** TODO: increment total_voices **/
			    			this.total_voices++;
		    	 		}
		    			break;
		    		case 'voice_changed':
		    			if(evt_args.hash.indexOf(this.voices_list.get_hash()) != -1){
		    				/** TODO: Publish evt for update abbreviation **/
		    				topic.publish('voices_list_ui', 'voices_list_changed', {hash:this.voices_list.get_hash()});
		    	 		}
		    			break;
		    		case 'voice_delete':
		    			if(evt_args.hash.indexOf(this.voices_list.get_hash()) != -1){
		    				this.delete_voice_event(evt_args.order);
		    				topic.publish('voices_list_ui', 'voices_list_changed', {hash:this.voices_list.get_hash()});
		    	 		}
		    			break;
		    		case 'reorder_voices':
		    			if(evt_args.hash.indexOf(this.voices_list.get_hash()) != -1){
		    				this.reorder_voices_ui();
		    	 		}
		    			break;
		    		case 'end_analyze':
		    			if(this.voices_list.get_hash().indexOf(evt_args.hash) != -1){
		    				/** TODO: Publish evt for update abbreviation **/
		    				this.init_voices_ui();
		    	 		}
		    			break;
		    		case 'error_analyze':
		    			if(this.voices_list.get_hash().indexOf(evt_args.hash) != -1){
		    				this.voices_node.style.display = "none";
		    	 		}
		    			break;
		    	}
		    },
		    
		    build_form: function(){
		    	var h3_node = domConstruct.create('h3', {innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_voice_add')}, this.get_dom_node());
		    	var input_plus = domConstruct.create('input', {type:'button', value:'+', class:'bouton'}, h3_node);
		    	on(input_plus, 'click', lang.hitch(this, this.add_voice_to_list));
		    	
		    	var input_nb_voices = domConstruct.create('input', {
				    	type:'hidden', 
				    	id:this.get_dom_node().id+'_count_voices', 
				    	value:this.voices_list.get_effective()
				    }, this.get_dom_node());
		    	var display = "table";
		    	if(this.voices_list.voices.length == 0){
		    		display = "none";
		    	}
		    	this.set_voices_node(domConstruct.create('table', {id:this.get_dom_node().id+'_tab_voices', style:{display:display}}, this.get_dom_node()));
		    	
		    	var header_line = domConstruct.create('tr',null,this.get_voices_node());
		    	
    			var th_order = domConstruct.create('th', {innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_instruments_header_order'), style:{textAlign:'center'}}, header_line);
		    	var th_voice = domConstruct.create('th', {innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_instruments_header_voices'), style:{textAlign:'center'}}, header_line);
		    	var th_effective = domConstruct.create('th', {innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_instruments_header_effective'), style:{textAlign:'center'}}, header_line);
		    	var th_bouton_delete = domConstruct.create('th', {style:{textAlign:'center'}}, header_line);
		    	
		    	var lib_note = domConstruct.create('div', {"class":'row'}, this.get_dom_node());
		    	domConstruct.create('label', {
		    		"class":'etiquette',
		    		"for":this.get_dom_node().id+'_note',
		    		innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_voices_note')
		    	}, lib_note);
		    	var content_note = domConstruct.create('div', {"class":'row'}, this.get_dom_node());
		    	this.textarea_note = domConstruct.create('textarea', {
		    		id:this.get_dom_node().id+'_note',
		    		name:this.get_dom_node().id+'_note',
		    		"class":'saisie-80em',
		    		wrap:'virtual',
		    		rows:'3',
		    		innerHTML:this.voices_list.nomenclature_voices.record_formation.get_note()
		    	}, content_note);
		    	this.own(on(this.textarea_note, 'keyup', lang.hitch(this, this.update_note)));
		    	/** Création de l'input hidden de la note en vue de la sauvegarde **/
		    	this.hidden_note = domConstruct.create('input', {type:'hidden', name:this.voices_list.nomenclature_voices.record_formation.get_hidden_field_name('notes'), value:this.voices_list.nomenclature_voices.record_formation.get_note()}, content_note);
		    	
		    	this.init_voices_ui();
		    },
		    
		    init_voices_ui: function(){
		    	this.total_voices = 0;
		    	for(var i=0; i<this.voices_list.voices.length ; i++){
		    		this.init_display = true;
		    		/**
		    		 * TODO: Add voice ui here with good params
		    		 */
		    		this.voices_node.style.display = "table";
		    		var obj = {id:this.voices_list.voices[i].id, dom_node:this.get_voices_node(), indice:this.get_total_voices(), voice:this.voices_list.voices[i], voices_list_ui:this};
			    	var new_voice_ui = new Voice_ui(obj);
			    	this.voices_ui.push(new_voice_ui);
		    	}
		    	this.voices_list.calc_abbreviation();
		    	topic.publish('voices_list_ui', 'voices_list_reordered', {hash:this.voices_list.get_hash()});
		    },
		    
		    get_id: function() {
				return this.id;
			},
			
			set_id: function(id) {
				this.id = id;
			},
			
			get_voices_list: function() {
				return this.voices_list;
			},
			
			set_voices_list: function(voices_list) {
				this.voices_list = voices_list;
			},
			
			get_nomenclature_voices_ui: function() {
				return this.nomenclature_voices_ui;
			},
			
			set_nomenclature_voices_ui: function(nomenclature_voices_ui) {
				this.nomenclature_voices_ui = nomenclature_voices_ui;
			},
			
			get_voices_list_ui: function() {
				return this.voices_list_ui;
			},
			
			set_voices_list_ui: function(voices_list_ui) {
				this.voices_list_ui = voices_list_ui;
			},
			destroy: function(){
				for(var i=0 ; i<this.events_handles.length ; i++){
					this.events_handles[i].remove();
				}
				this.voices_list = null;
				this.inherited(arguments);
			},
			get_dom_node: function() {
				return this.dom_node;
			},
			
			set_dom_node: function(dom_node) {
				this.dom_node = dom_node;
			},
			
			add_voice_to_list:function(){
		    	if(this.voices_node.style.display == "none"){		
	    			this.init_display = true;
		    		this.voices_node.style.display = "table";
		    	}

		    	var voice = new Voice("","");
		    	voice.set_effective(1);
		    	voice.set_order(this.voices_list.get_max_order()+1);
		    	
		    	this.voices_list.add_voice(voice);
		    	//Null est passé en 2eme parametre, si l'on passe un noeud, le widget le prendra automatiquement
		    	var obj = {id:0, dom_node:this.get_voices_node(), indice:this.get_total_voices(), voice:voice, voices_list_ui:this};
		    	var new_voice_ui = new Voice_ui(obj);
		    	this.voices_ui.push(new_voice_ui);
		    	new_voice_ui.init_actions();
			},
			
			get_voices_node: function() {
				return this.voices_node;
			},
			
			set_voices_node: function(voices_node) {
				this.voices_node = voices_node;
			},
			
			get_total_voices: function() {
				return this.total_voices;
			},
			
			set_total_voices: function(total_voices) {
				this.total_voices = total_voices;
			},
			delete_voice_event: function(order){
		    	var index_voice_ui;
		    	var order = order;
		    	for(var i=0 ; i<this.voices_ui.length ; i++){
		    		if(this.voices_ui[i].voice.get_order() == order){
		    			index_voice_ui = i; 
		    		}
		    	}
		    	
		    	this.voices_ui[index_voice_ui].destroy();
		    	this.voices_ui.splice(index_voice_ui, 1);
		    	
		    	this.voices_list.delete_voice(order, true);
		    	
		    	var array_nodes = new Array();
		    	for(var i=0; i<this.voices_ui.length ; i++){
		    		array_nodes.push(this.voices_ui[i].domNode);
		    	}
		    	var newarr = array_nodes.sort(this.sort_nodes);
		    	for(var i=0; i<newarr.length ; i++){
		    		var ui_instance = registry.byId(newarr[i].id);
		    		ui_instance.set_order(i+1);
		    	}
		    	
		    	parse_drag(this.get_voices_node());
		    	
		    	topic.publish("voices_list_ui", "voices_list_changed", {hash:this.voices_list.get_hash()});
		    	if(this.voices_list.get_voices().length == 0)
		    		this.voices_node.style.display = "none";
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
			
			purge_voices : function(){
				for(var i=0 ; i<this.voices_ui.length ; i++){
					if(this.voices_ui[i]){
						this.voices_ui[i].destroy();
					}
				}
				this.voices_list.set_voices(new Array());
				this.voices_ui = new Array();
		    	this.set_total_voices(0);
				parse_drag(this.voices_node);
			},
			reorder_voices_ui: function(){
				for(var i=0 ; i<this.voices_ui.length ; i++){
					this.voices_ui[i].set_order(this.voices_ui[i].voice.get_order());
				}
				var array_nodes = new Array();
				for(var i=0; i<this.voices_ui.length ; i++){
		    		array_nodes.push(this.voices_ui[i].domNode);
		    	}
		    	var newarr = array_nodes.sort(this.sort_nodes);
		    	for(var i=newarr.length-1 ; i>0 ; i--){
		    			this.voices_node.insertBefore(newarr[i-1], newarr[i]);
		    	}
		    	parse_drag(this.voices_node);
			},
			postCreate: function(){
				topic.publish('voices_list_ui', 'ui_ready', {hash:this.voices_list.get_hash()});
			},
			update_note: function() {
		    	var note = this.textarea_note.value;
		    	this.voices_list.nomenclature_voices.record_formation.set_note(note);
		    	this.update_hidden_fields();
		    },
		    update_hidden_fields:function (){
				this.hidden_note.value = this.voices_list.nomenclature_voices.record_formation.get_note();
			},
		    
	    });
	});