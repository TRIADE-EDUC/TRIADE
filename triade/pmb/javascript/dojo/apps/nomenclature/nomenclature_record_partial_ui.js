// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_record_partial_ui.js,v 1.8 2016/11/29 13:00:29 vtouchard Exp $

define(["dojo/_base/declare", "dojo/dom-construct", "dojo/dom", "dojo/on", "dojo/_base/lang", "dojo/topic", "dijit/registry", "dijit/_WidgetBase", "apps/nomenclature/nomenclature_record_partial"], function(declare, domConstruct, dom, on, lang, topic, registry, _WidgetBase, record_partial){
	/*
	 *Classe nomenclature_record_formations_ui. Classe générant le formulaire permettant de représenter les formations d'une notice
	 */
	return declare("nomenclature_record_partial_ui",[_WidgetBase], {
		num_record:null,
		record_partial:null,
		parent_node:null,
		formation:null,
		musicstand:null,
		instrument:null,
		other_instruments:null,
		workshop:null,
		voice:null,
		order:null,
		effective:null,
		current_form: null,
		relation_code:null,
		
		constructor:function(params){
			this.own(topic.subscribe("record_partial_ui",lang.hitch(this,this.handle_events)));
			this.relation_code = registry.byId('nomenclature_datastore').get_relation_code()+"-up";
		},
		
		handle_events: function(evt_type,evt_args){
			switch(evt_type){
				case "relation_changed":
					this.init_display();
					break;
				case "possible_values_ready":
					this.set_form_values(evt_args.possible_values);
					break;
			}
		},
		
		buildRendering: function(){
			//noeud racine du Widget, il a déjà un ID, il lui faut une classe...
			this.domNode = domConstruct.create('div',{
				class:'notice-child'
			},this.parent_node);

			//on ajoute les éléments du formulaire....
			//la formation
			var label = domConstruct.create("div",{
				class:"row"
			},this.domNode);
			domConstruct.create("label",{
				innerHTML : registry.byId("nomenclature_datastore").get_message("nomenclature_js_partial_formation")
			},label);
			var value = domConstruct.create("div",{
				class:"row"
			},this.domNode);
			this.formation = domConstruct.create("select",{
				name:"nomenclature_record_partial_formation"
			},value);
			domConstruct.create("option",{
				value: "",
				innerHTML : registry.byId("nomenclature_datastore").get_message('nomenclature_js_partial_formation_choice')
			},this.formation);

			//le pupitre
			var label = domConstruct.create("div",{
				class:"row"
			},this.domNode);
			domConstruct.create("label",{
				innerHTML : registry.byId("nomenclature_datastore").get_message("nomenclature_js_partial_musicstand")
			},label);
			var value = domConstruct.create("div",{
				class:"row"
			},this.domNode);
			this.musicstand = domConstruct.create("select",{
				name:"nomenclature_record_partial_musicstand"
			},value);
			domConstruct.create("option",{
				value: "",
				innerHTML : registry.byId("nomenclature_datastore").get_message('nomenclature_js_partial_musicstand_choice')
			},this.musicstand);
			

			//l'atelier
			var label = domConstruct.create("div",{
				class:"row"
			},this.domNode);
			domConstruct.create("label",{
				innerHTML : registry.byId("nomenclature_datastore").get_message("nomenclature_js_partial_workshop")
			},label);
			var value = domConstruct.create("div",{
				class:"row"
			},this.domNode);
			this.workshop = domConstruct.create("select",{
				name:"nomenclature_record_partial_workshop"
			},value);
			domConstruct.create("option",{
				value: "",
				innerHTML : registry.byId("nomenclature_datastore").get_message('nomenclature_js_partial_workshop_choice')
			},this.workshop);
			
			
			//l'instrument
			var label = domConstruct.create("div",{
				class:"row"
			},this.domNode);
			domConstruct.create("label",{
				innerHTML : registry.byId("nomenclature_datastore").get_message("nomenclature_js_partial_instrument")
			},label);
			var value = domConstruct.create("div",{
				class:"row"
			},this.domNode);
			this.instrument = domConstruct.create('input', {
	    		name: "nomenclature_record_partial_instrument", 
	    		type:'text', 
	    		id: "nomenclature_record_partial_instrument", 
	    		value:"",
	    		autocomplete:'off',
	    		completion:'instruments',
	    		autfield: "nomenclature_record_partial_num_instrument"
	    	},value);
			domConstruct.create("input",{
				type: "hidden",
				name: "nomenclature_record_partial_num_instrument",
				id: "nomenclature_record_partial_num_instrument",
				
			},value);
			ajax_pack_element(this.instrument);

			//les instruments annexes
			var label = domConstruct.create("div",{
				class:"row"
			},this.domNode);
			domConstruct.create("label",{
				innerHTML : registry.byId("nomenclature_datastore").get_message("nomenclature_js_partial_other_instruments")
			},label);
			var value = domConstruct.create("div",{
				class:"row"
			},this.domNode);
			this.other_instruments = domConstruct.create('input', {
				name: "nomenclature_record_partial_other_instruments", 
	    		id: "nomenclature_record_partial_other_instruments", 
	    		type:'text', 
	    		value:"",
	    		autocomplete:'off',
	    		completion:'instruments',
	    		separator:'/',
	    		autfield: "nomenclature_record_partial_other_instruments", 
	    	}, value);
			ajax_pack_element(this.other_instruments);

			//la Voix
			var label = domConstruct.create("div",{
				class:"row"
			},this.domNode);
			domConstruct.create("label",{
				innerHTML : registry.byId("nomenclature_datastore").get_message("nomenclature_js_partial_voice")
			},label);
			var value = domConstruct.create("div",{
				class:"row"
			},this.domNode);
			this.voice = domConstruct.create('input', {
	    		name: "nomenclature_record_partial_voice", 
	    		type:'text', 
	    		id: "nomenclature_record_partial_voice", 
	    		value:"",
	    		autocomplete:'off',
	    		completion:'voices',
	    		autfield: "nomenclature_record_partial_num_voice"
	    	},value);
			domConstruct.create("input",{
				type: "hidden",
				name: "nomenclature_record_partial_num_voice",
				id: "nomenclature_record_partial_num_voice",
				
			},value);
			ajax_pack_element(this.voice);

			//l'ordre
			var label = domConstruct.create("div",{
				class:"row"
			},this.domNode);
			domConstruct.create("label",{
				innerHTML : registry.byId("nomenclature_datastore").get_message("nomenclature_js_partial_order")
			},label);
			var value = domConstruct.create("div",{
				class:"row"
			},this.domNode);
			this.order = domConstruct.create("input",{
				type: "text",
				class: "saisie-10em",
				name: "nomenclature_record_partial_order"
			},value);

			//l'effectif
			var label = domConstruct.create("div",{
				class:"row"
			},this.domNode);
			domConstruct.create("label",{
				innerHTML : registry.byId("nomenclature_datastore").get_message("nomenclature_js_partial_effective")
			},label);
			var value = domConstruct.create("div",{
				class:"row"
			},this.domNode);
			this.effective = domConstruct.create("input",{
				type: "text",
				class: "saisie-10em",
				name: "nomenclature_record_partial_effective"
			},value);
		},
		
		postCreate:function(){
			this.inherited(arguments);
			if(!this.record_partial){
				this.record_partial = new record_partial({
					num_record: this.num_record,
					detail: this.detail
				});
			}
			//on cherche le formulaire
			var currentNode = this.domNode;
			this.current_form = false;
			if(document.forms['notice']){
				this.current_form = document.forms['notice'];
				var zone = document.getElementById("el11Child");
				if(zone){
					var inputs = zone.getElementsByTagName("input");	
					for(var i=0; i<inputs.length ; i++){
						//on recherche le bouton d'ajouts de manière un peu sauvage...
						if(inputs[i].getAttribute("type") == "button" && inputs[i].getAttribute("onclick") == "add_rel();"){
							this.own(on(inputs[i], "click", lang.hitch(this,this.init_handlers)));
						}
					}
				}
				if(this.current_form.max_rel){
					for(var i=0 ; i<this.current_form.max_rel.value ; i++){
						//TODO handler sur le change d'une notice liée pas juste son type...
						this.own(on(this.current_form['f_rel_type_'+i], "change", function(){topic.publish("record_partial_ui", "relation_changed")}));
						//this.own(on(this.current_form['f_rel_'+(this.current_form.max_rel.value-1)], "blur", lang.hitch(this,this.check_if_is_a_child)));
					}
				}
			}
			
			this.init_display();
		},
		
		init_display: function(){
//			if(this.check_if_is_a_child()){
//				this.formation.disabled = false;
//				this.musicstand.disabled = false;
//				this.workshop.disabled = false;
//				this.order.disabled = false;
//				this.voice.disabled = false;
//				this.effective.disabled = false;
//				this.other_instruments.disabled = false;
//				this.instrument.disabled = false;
//				
//			}else{
//				this.formation.disabled = true;
//				this.musicstand.disabled = true;
//				this.workshop.disabled = true;
//				this.order.disabled = true;
//				this.voice.disabled = true;
//				this.effective.disabled = true;
//				this.other_instruments.disabled = true;
//				this.instrument.disabled = true;
//			}
			if(this.check_if_is_a_child()){
				this.formation.disabled = true;
				this.musicstand.disabled = true;
				this.workshop.disabled = true;
				this.order.disabled = true;
				this.voice.disabled = true;
				this.effective.disabled = true;
				this.other_instruments.disabled = true;
				this.instrument.disabled = true;
			}
		},

		check_if_is_a_child: function(){
			if(this.current_form !== false){
				if(this.current_form.max_rel && this.current_form.max_rel.value){
					for(var i=0 ; i<this.current_form.max_rel.value ; i++){
						//recherche si la notice est liée à quelque chose...
						if(this.current_form['f_rel_id_'+i].value!= ""){
							//oui? alors on regarde la liaison
							if(this.current_form['f_rel_type_'+i].options[this.current_form['f_rel_type_'+i].selectedIndex].value == this.relation_code){
								this.num_parent = this.current_form['f_rel_id_'+i].value;
								this.record_partial.set_num_parent(this.current_form['f_rel_id_'+i].value);
								return true;
							}
						}
					}
				}
			}
			this.record_partial.set_num_parent(0);
			return false;
		},

		init_handlers: function(){
			if(this.current_form !== false){
				if(this.current_form.max_rel){
					//TODO handler sur le change d'une notice liée pas juste son type...
					this.own(on(this.current_form['f_rel_type_'+(this.current_form.max_rel.value-1)], "change", topic.publish("record_partial_ui", "relation_changed")));
					//this.own(on(this.current_form['f_rel_'+(this.current_form.max_rel.value-1)], "blur", lang.hitch(this,this.check_if_is_a_child)));
				}
			}
		},
		
		set_form_values: function(values){
			if(values.formations){
				for(var i=0 ; i<this.formation.childNodes.length ; i++){
					if(this.formation.childNodes[i].value !== ""){
						this.formation.removeChild(this.formation.childNodes[i])
					}
				}
				for(var key in values.formations){
					domConstruct.create("option",{
						value : key,
						innerHTML : values.formations[key],
						selected : (this.record_partial.num_nomenclature == key ? "selected" : false)
					},this.formation);
					
				}
			}
			if(values.musicstands){
				for(var i=0 ; i<this.musicstand.childNodes.length ; i++){
					if(this.musicstand.childNodes[i].value !== ""){
						this.musicstand.removeChild(this.musicstand.childNodes[i])
					}
				}
				for(var key in values.musicstands){
					domConstruct.create("option",{
						value : key,
						innerHTML : values.musicstands[key],
						selected : (this.record_partial.num_musicstand == key ? "selected" : false)
					},this.musicstand);
					
				}
			}
			if(values.workshops){
				for(var i=0 ; i<this.workshop.childNodes.length ; i++){
					if(this.workshop.childNodes[i].value !== ""){
						this.workshop.removeChild(this.workshop.childNodes[i])
					}
				}
				for(var key in values.workshops){
					domConstruct.create("option",{
						value : key,
						innerHTML : values.workshops[key],
						selected : (this.record_partial.num_workshop == key ? "selected" : false)
					},this.workshop);
					
				}
			}
			this.init_display();
			//initialisation des valeurs...
			//effective
			this.effective.value = this.record_partial.get_effective();
			//order
			this.order.value = this.record_partial.get_order();
			//instrument
			this.instrument.value = registry.byId('nomenclature_datastore').get_code_from_id(this.record_partial.get_num_instrument());
			dom.byId("nomenclature_record_partial_num_instrument").value = this.record_partial.get_num_instrument();
			//instruments annexes
			var others = this.record_partial.get_other();
			var others_codes= new Array();
			for(var i=0; i<others.length; i++){
				others_codes.push(others[i]);
			}
			this.other_instruments.value = others_codes.join('/');
			//voice
			this.voice.value = registry.byId('nomenclature_datastore').get_voice_code_from_id(this.record_partial.get_num_voice());
			dom.byId("nomenclature_record_partial_num_voice").value = this.record_partial.get_num_voice();
			//musicstand
			for(var i=0; i<this.musicstand.options.length ; i++){
				if(this.musicstand.options[i].value == this.record_partial.get_num_musicstand()){
					this.musicstand.selectedIndex = i;
					break;
				}
			}
			//workshop
			for(var i=0; i<this.workshop.options.length ; i++){
				if(this.workshop.options[i].value == this.record_partial.get_num_workshop()){
					this.workshop.selectedIndex = i;
					break;
				}
			}
			//formation
			for(var i=0; i<this.formation.options.length ; i++){
				if(this.formation.options[i].value == (this.record_partial.get_num_formation()+"_"+this.record_partial.get_num_type())){
					this.formation.selectedIndex = i;
				}
			}
			this.add_hidden_inputs();
			
		},
		add_hidden_inputs: function(){
			if(!document.getElementById(this.formation.name+'_hidden')){
				domConstruct.create('input', {type:'hidden', name: this.formation.name,id:this.formation.name+'_hidden', value:this.formation.value}, this.domNode);	
			}		
			if(!document.getElementById(this.musicstand.name+'_hidden')){
				domConstruct.create('input', {type:'hidden', name: this.musicstand.name, id:this.musicstand.name+'_hidden', value:this.musicstand.value}, this.domNode);	
			}
			if(!document.getElementById(this.workshop.name+'_hidden')){
				domConstruct.create('input', {type:'hidden', name: this.workshop.name, id:this.workshop.name+'_hidden',value:this.workshop.value}, this.domNode);	
			}
			if(!document.getElementById(this.order.name+'_hidden')){
				domConstruct.create('input', {type:'hidden', name: this.order.name, id:this.order.name+'_hidden', value:this.order.value}, this.domNode);	
			}
			if(!document.getElementById(this.voice.name+'_hidden')){
				domConstruct.create('input', {type:'hidden', name: this.voice.name, id:this.voice.name+'_hidden', value:this.voice.value}, this.domNode);	
			}
			if(!document.getElementById(this.effective.name+'_hidden')){
				domConstruct.create('input', {type:'hidden', name: this.effective.name, id: this.effective.name+'_hidden',value:this.effective.value}, this.domNode);	
			}
			if(!document.getElementById(this.other_instruments.name+'_hidden')){
				domConstruct.create('input', {type:'hidden', name: this.other_instruments.name, id:this.other_instruments.name+'_hidden', value:this.other_instruments.value}, this.domNode);	
			}
			if(!document.getElementById(this.instrument.name+'_hidden')){
				domConstruct.create('input', {type:'hidden', name: this.instrument.name, id:this.instrument.name+'_hidden', value:this.instrument.value}, this.domNode);	
			}
			
		},
	});
});
			    