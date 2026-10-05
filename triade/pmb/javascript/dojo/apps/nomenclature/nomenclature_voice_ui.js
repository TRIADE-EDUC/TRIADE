// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_voice_ui.js,v 1.17 2022/06/08 14:45:16 rtigero Exp $

define(["dojo/_base/declare","dojo/dom-construct", "dojo/topic", "dijit/_WidgetBase", "dojo/on", "dojo/_base/lang","dijit/registry", "dojo/request/xhr"], function(declare, domConstruct, topic, _WidgetBase, on, lang, registry, xhr){
	/*
	 *Classe nomenclature_voice_ui. Classe g�rant l'affichage d'une voix
	 */
	  return declare("voice_ui", [_WidgetBase],{
			    
		  	voice:null, /** instance du modele li� **/
		  	voices_list_ui:null, /** instance du parent ui **/
		  	indice:0,
		  	id:0,
		  	events_handles: null,
		  	table_node:null,
		  	td_suppression: null,
		  	record_id: 0,
		  	
		    constructor: function(params){
		    	this.set_id(params.id);
		    	this.set_voice(params.voice);
		    	this.set_table_node(params.dom_node);
		    	this.set_indice(params.indice);
		    	this.set_voices_list_ui(params.voices_list_ui);
		    	this.events_handles = new Array();
		    	this.events_handles.push(topic.subscribe("voice_ui",lang.hitch(this, this.handle_events)));
		    	this.events_handles.push(topic.subscribe("nomenclature_voices_ui",lang.hitch(this, this.handle_events)));
		    },
		    
		    buildRendering: function(){ 
		    	this.inherited(arguments);
		    	this.domNode = domConstruct.create('tr', null, this.get_table_node());
		    	this.domNode.setAttribute('order', this.voice.get_order());
		    	this.domNode.setAttribute("draggable", "yes");
		    	this.domNode.setAttribute("dragtype", "instru");
		    	this.domNode.setAttribute("id", this.voice.get_hash()+'_voice_'+this.get_indice());
		    	this.domNode.setAttribute("id_voice", this.voice.get_hash()+'_'+this.get_indice());
		    	this.domNode.setAttribute("recept", "yes");
		    	this.domNode.setAttribute("dragtext", registry.byId('nomenclature_datastore').get_message('nomenclature_js_instruments_header_voices'));
		    	this.domNode.setAttribute("highlight", "instru_highlight");
		    	this.domNode.setAttribute("recepttype", "instru");
		    	this.domNode.setAttribute("downlight", "instru_downlight");
		    	this.domNode.setAttribute("dragicon", pmbDojo.images.getImage('icone_drag_notice.png'));
		    	this.domNode.setAttribute("handler", this.get_table_node().id+'_handle_'+this.voice.get_hash()+'_'+this.get_indice());
		    	this.build_form();
		    },
		    
		    handle_events: function(evt_type, evt_args){
		    	switch(evt_type){
		    	 	case "input_change" :
		    	 		if(evt_args.hash == this.voice.get_hash()){
		    	 			if(this.input_changed()){
		    	 				this.update_record();
		    	 				this.publish_event('voice_changed');
		    	 			}
		    	 		}
		    			break;
		    	 	case "nomenclature_voices_expanded":
		    	 	case "nomenclature_voices_sync_from_abrege":
		    	 		if (evt_args.hash == this.voices_list_ui.nomenclature_voices_ui.nomenclature_voices.get_hash()) {
		    	 			this.init_actions();
		    	 		}
		    	 		break;
					case "sync_and_save_end":
						if (this.voice.get_hash().indexOf(evt_args.hash) != -1) {
							this.update_record();
						}
						break;
		    	 	
		    	}	
		    },
		    
		    build_form: function(){
		    	
		    	/** Cr�ation d'un callback appell� a chaque �dition d'un input de la voix **/
		    	var callback_change = lang.hitch(this, function(){
		    		topic.publish("voice_ui","input_change",{
		    			hash : this.voice.get_hash(),
		    		})
		    	});		    	
		    	
		    	
		    	/** Publication d'un event "input_changed" sur un choix dans l'autocompletion du champs instruments principal **/
		    	var object_value = this;
	    		window.nomenclature_input_callback = function(){
	    			var id = arguments[0];
	    			if(id.match('_input_voice')){
	    				var dijit_id = id.split('_input_voice')[0];
	    			}
	    			if(dijit_id != undefined){
	    				topic.publish("voice_ui","input_change",{
			    			hash : dijit.registry.byId(dijit_id).voice.get_hash(),
			    		})
	    			}
	    		}
	    		/** Cr�ation du td draggable modifiant l'ordre de la voix ondrag **/
	    		var td_order = domConstruct.create('td', null, this.domNode);
		    	var span_order = domConstruct.create('span', {style:{float:'left',paddingRight:'7px'}, id:this.get_table_node().id+'_handle_'+this.voice.get_hash()+'_'+this.get_indice()} , td_order);
		    	this.span_order = domConstruct.create('span', {style:{position:'relative',paddingRight:'7px'}, innerHTML:this.voice.get_order(), id:this.get_table_node().id+'_order_label_'+this.voice.get_hash()+'_'+this.get_indice()} , td_order);
		    	var img = domConstruct.create('img', {style:{width:"20px", verticalAlign:'middle'}, src:pmbDojo.images.getImage('sort.png')}, span_order);
		    	
		    	/** Cr�ation du champs voix, autocompl�t� **/
		    	var td_main_instr = domConstruct.create('td', null,this.domNode);
		    	this.input_main_voice = domConstruct.create('input', {
		    		name:this.get_id()+'_input_voice', 
		    		type:'text', 
		    		id:this.get_id()+'_input_voice', 
		    		value:this.voice.get_code(),
		    		autocomplete:'off',
		    		completion:'voices',
		    		callback:"nomenclature_input_callback",
		    		//autfield:this.get_id()+'_input_voice'
		    	},td_main_instr);
		    	this.own(on(this.input_main_voice, 'change', callback_change));
		    	
		    	/** Cr�ation du champs effectif **/
		    	var td_effective = domConstruct.create('td', null,this.domNode);
		    	this.input_effective = domConstruct.create('input', {
		    		name:this.get_id()+'_input_effective_voice', 
		    		id:this.get_id()+'_input_effective_voice', 
		    		type:'text', 
		    		value:this.voice.get_effective()||"~",
		    	}, td_effective);
		    	this.own(on(this.input_effective, 'change', callback_change));
		    	
		    	/** Cr�ation du bouton de suppression **/
		    	this.td_suppression = domConstruct.create('td', null, this.domNode);
		    	var bouton_delete = domConstruct.create('input', {type:'button', value:'X', class:'bouton'}, this.td_suppression);
		    	this.own(on(bouton_delete, "click", lang.hitch(this, function(){
		    		this.delete_record_child();
		    	})));
		    	this.ajax_parse();	
		    	
		    },
		    
		    get_indice: function() {
				return this.indice;
			},
			
			set_indice: function(indice) {
				this.indice = indice;
			},
			
			get_voice: function() {
				return this.voice;
			},
			
			set_voice: function(voice) {
				this.voice = voice;
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
				this.voice = null;
				this.inherited(arguments);
			},
			get_table_node: function() {
				return this.table_node;
			},
			
			set_table_node: function(table_node) {
				this.table_node = table_node;
			},

			get_id: function() {
				return this.id;
			},
			
			set_id: function(id) {
				this.id = id;
			},
			
			ajax_parse: function(){
				ajax_pack_element(this.input_main_voice);
			},
			
			postCreate:function(){
				this.inherited(arguments);
				parse_drag(this.table_node);
				this.publish_event('voice_ui_ready');
			},
			
			publish_event: function(evt_name){
				var event_args = {};
				event_args.hash = this.voice.get_hash();
				switch(evt_name){
					case "voice_delete":
							event_args.order = this.voice.get_order();	
						break;
				}
				topic.publish("voice_ui", evt_name, event_args);
			},
			input_changed:function(){
	    		var flag = false;
				if(this.input_main_voice.value != ""){
	    			this.voice.set_code(this.input_main_voice.value.trim());
	    			flag = true;
	    		}
		    	if((this.input_effective.value != null && !isNaN(this.input_effective.value) && this.input_effective.value != this.voice.get_effective()) || (this.input_effective.value == "~")){
		    		if(!isNaN(this.input_effective.value )){
		    			this.voice.set_effective(parseInt(this.input_effective.value));
		    			this.voice.set_indefinite_effective(false);
		    		}else{
		    			this.voice.set_indefinite_effective(true);	
		    		}
		    		flag = true;
		    	}
		    	return flag;
			},
			set_order: function(order){
				this.voice.set_order(order);
				this.domNode.setAttribute("order", order);
				this.span_order.innerHTML = order;
				this.publish_event('voice_changed');
			},
			init_actions: function (){
		    	if(this.voices_list_ui.nomenclature_voices_ui.nomenclature_voices.record_formation.get_record() != 0){
		    		xhr("./ajax.php?module=ajax&categ=nomenclature&sub=record_child&action=get_child&id_parent="+this.voices_list_ui.nomenclature_voices_ui.nomenclature_voices.record_formation.get_record(), {
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
		    	if(this.voices_list_ui.nomenclature_voices_ui.ajax_save()){
		    		this.create_record(lang.hitch(this, this.record_created), "create");
		    	}
		    },
		    create_record: function(callback, action){
	    		xhr("./ajax.php?module=ajax&categ=nomenclature&sub=record_child&action="+action+"&id_parent="+this.voices_list_ui.nomenclature_voices_ui.nomenclature_voices.record_formation.get_record(), {
					handleAs: "json",
					method:"POST",
					data:this.ajax_prepare_args()
				}).then(callback,function(err){console.log(err)});
		    },
		    
		    create_child: function(){
		    	if(!this.record_id){
			    	this.create_record(lang.hitch(this, this.create_children_callback), "create_children");
		    	} else {
					topic.publish("voice_ui", "submanifestation_created", {
						hash: this.voice.get_hash(),
						is_new: false
					});
		    	}
		    },
		    create_children_callback: function(data){
		    	if(data.new_record){
		    		this.record_created(data);
					topic.publish("voice_ui", "submanifestation_created", {
						hash: this.voice.get_hash(),
						is_new: true
					});
		    	}else{
		    		this.got_record(data.id);
					topic.publish("voice_ui", "submanifestation_created", {
						hash: this.voice.get_hash(),
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
		    		topic.publish('voice_ui',"partial_record_created",{
		    			hash: this.voice.get_hash(),
		    			record_id: record.id,
		    			record_title: record.title,
		    			record_reverse_id_notices_relations : record.reverse_id_notices_relations,
		    			record_reverse_num_reverse_link : record.reverse_num_reverse_link
		    		});
		    	}
		    },
		    ajax_prepare_args:function(){
				var args = "&record_child_data[num_formation]="+this.voices_list_ui.nomenclature_voices_ui.nomenclature_voices.record_formation.formation.get_id();
	    		args+="&record_child_data[num_type]="+(this.voices_list_ui.nomenclature_voices_ui.record_formation_ui.record_formation.get_type() ? this.voices_list_ui.nomenclature_voices_ui.record_formation_ui.record_formation.get_type().id : '0');
	    		args+="&record_child_data[num_musicstand]=0";
	    		args+="&record_child_data[num_instrument]=0";
	    		args+="&record_child_data[num_nomenclature]="+this.voices_list_ui.nomenclature_voices_ui.nomenclature_voices.record_formation.get_id();
	    		args+="&record_child_data[num_voice]="+this.voice.get_id();
	    		args+="&record_child_data[num_workshop]=0";
	    		args+="&record_child_data[effective]="+(this.voice.get_indefinite_effective() ? this.voices_list_ui.nomenclature_voices_ui.nomenclature_voices.indefinite_character : this.voice.get_effective());
	    		args+="&record_child_data[order]="+this.voice.get_order();
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
						}).then(() => this.publish_event('voice_delete'));
					});
				} else {
					this.publish_event('voice_delete');	
				}
			}
	    });
	});