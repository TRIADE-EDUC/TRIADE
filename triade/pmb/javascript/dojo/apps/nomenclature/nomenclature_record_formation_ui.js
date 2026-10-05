// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_record_formation_ui.js,v 1.28 2017/11/30 10:53:34 dgoron Exp $

define(["dojo/_base/declare", "dojo/dom-construct", "dojo/topic", "apps/nomenclature/nomenclature_nomenclature_ui","apps/nomenclature/nomenclature_nomenclature","dijit/registry", "dojo/on", "dojo/_base/lang", "apps/nomenclature/nomenclature_nomenclature_voices_ui", "dijit/_WidgetBase"], function(declare, domConstruct, topic , NomenclatureUi, Nomenclature, registry, on, lang, Nomenclature_voices_ui, _WidgetBase){
	/*
	 *Classe nomenclature_record_formation_ui. Classe g�n�rant le formulaire permettant de repr�senter une formation
	 */
	  return declare("nomenclature_record_formation_ui",[_WidgetBase], {
		  	
		  record_formation:null,
		  dom_node:null, 
		  nomenclature_node:null,
		  indice:0,
		  type_selector:null,
		  label:null,
		  nomenclature_ui:null,
		  input_name:null,
		  hidden_type:null,
		  hidden_label:null,
		  hidden_record_formations:null,
		  nomenclature_id:null,
		  nomenclature_order:null,
		  
		    constructor: function(params){
		    	this.own(topic.subscribe("nomenclature_ui",lang.hitch(this, this.handle_events)));
		    	this.own(topic.subscribe("nomenclature_voices_ui",lang.hitch(this, this.handle_events)));
		    },
		    
		    handle_events : function(evt_type,evt_args){
		    	//pour le d�bug, on affiche tout ce que l'on voit passer
		    	//console.log("DEBUG",evt_type,evt_args);
		    	switch(evt_type){
		    		case "error_on_submit" :
		    			if(evt_args.hash.indexOf(this.record_formation.get_hash())!=-1){
		    				this.expand_formation_node();
			    		}
		    			break;
		    	}
		    },
		    
		    buildRendering: function(){
		    	this.inherited(arguments);
		    	this.build_form();
		    },
		    
		    
			build_form: function(){
				//console.log(this.dom_node.id, 'id domnode formation_ui');
				/**
				 * ----------> Deux noeuds dom, le premier: parent avec le label de la formation, � stocker dans les attributs de la classe pour le modifier en temps r�el
				 * sur une frappe utilisateur dans le champs nom ou, un autre choix dans le select
				 * Le deuxieme noeud est le child
				 * 
				 * 
				 * V�rifier les diff�rentes propri�t�s de "record_formation" si elles sont a null -> nouvelle formation, sinon
				 * initialiser les diff�rents inputs avec les valeurs pr�sentes dans l'instance
				 * TODO: Initialisation du dom, avec selecteur de type, input de nom
				 */
				
				domConstruct.create('div', {class:'row'}, this.get_dom_node());
	    		var noeud_princ = domConstruct.create('div', {
	    			/**
	    			 * TODO: Ajouter un ID pour rendre la formation unique (totalFormation dans record_formations_ui)
	    			 */
	    			id:this.record_formation.get_hash()+'_formation_form_'+this.get_indice(), 
	    			class:'notice-parent'}, this.get_dom_node());
	    		/*
	    		 * Cr�ation d'un code type "pmb" permettant de d�plier les familles en cliquant sur une image
	    		 */
	    		var img_plus = domConstruct.create('img', {
	    			/**
	    			 * TODO: ajouter ici aussi id correct avec id formation
	    			 */
	    			id:this.record_formation.get_hash()+'_formation'+this.indice+'Img', 
	    			class:'img_plus', 
	    			hspace:'3', 
	    			border:'0', 
	    			onclick:'expandBase(\''+this.record_formation.get_hash()+'_formation'+this.indice+'\', true); return false;', 
	    			title:'d\351tail',
	    			src:pmbDojo.images.getImage('plus.gif')
	    				}, noeud_princ);
	    		
	    		this.label = domConstruct.create('span', {class:'notice-heada',innerHTML:this.record_formation.formation.get_name()}, noeud_princ);
	    		
	    		var link_delete = domConstruct.create('a', {onclick:''}, noeud_princ);
	    		domConstruct.create('img', {src:pmbDojo.images.getImage('trash.png'), alt:registry.byId('nomenclature_datastore').get_message('nomenclature_js_formation_delete'), title:registry.byId('nomenclature_datastore').get_message('nomenclature_js_formation_delete')}, link_delete);
		    	on(link_delete, "click", lang.hitch(this, this.delete_formation));
		    	
	    		//this.abbreviation_node = domConstruct.create('span', {innerHTML:' '+this.family.get_abbreviation()}, span);
	    		this.nomenclature_node = domConstruct.create('div', {
	    			id:this.record_formation.get_hash()+'_formation'+this.indice+'Child',
	    			startOpen:"Yes",
	    			class:'notice-child',
	    			callback : "recalc_recept",
	    			style:{
	    				marginBottom:'6px',
	    				display:'none',
	    				width:'94%',
	    			}
	    		}, this.get_dom_node());
	    		topic.publish("record_formation_ui","record_formation_ready", {
	    			hash : this.record_formation.get_hash()
	    		});

				/******
				 * 
				 * APPEL du selecteur des types
				 * 
				 *****/
				if(this.record_formation.get_types() && this.record_formation.get_types().length){
					domConstruct.create('label', {innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_formation_type_label')+" : "}, this.nomenclature_node);
					this.create_selector();
				}
				
				this.hidden_type = domConstruct.create('input', {type:'hidden', name:this.record_formation.get_hidden_field_name('num_type'), value:0}, this.nomenclature_node);
				domConstruct.create('label', {innerHTML:" "+registry.byId('nomenclature_datastore').get_message('nomenclature_js_notice_nomenclature_label')+" : "}, this.nomenclature_node);
				
				if(!this.get_record_formation().get_record()){
					this.record_formation.set_label(this.record_formation.formation.get_name()+(this.record_formation.get_type() ? ' '+this.record_formation.get_type().get_name() : ''));
				} else {
					var paramsRecordInEdit =  JSON.parse(registry.byId('nomenclature_record_ui_0').params.record_formations);
					if(!paramsRecordInEdit[this.get_indice()]) {
						this.record_formation.set_label(this.record_formation.formation.get_name()+(this.record_formation.get_type() ? ' '+this.record_formation.get_type().get_name() : ''));
					}
				}
				this.input_name = domConstruct.create('input', {type:'text', value:this.record_formation.get_label()}, this.nomenclature_node);
				this.own(on(this.input_name, 'keyup', lang.hitch(this, this.update_record_formation)));
				
				/** Cr�ation de l'input hidden du type de la formation en vue de la sauvegarde (en th�orie il est fixe car choisi via le selecteur avant d'appuyer sur +) **/
				domConstruct.create('input', {type:'hidden', name:this.record_formation.get_hidden_field_name('num_formation'), value:this.record_formation.get_formation_type_id()}, this.nomenclature_node);
				
				this.hidden_label = domConstruct.create('input', {type:'hidden', name:this.record_formation.get_hidden_field_name('label'), value:this.record_formation.get_label()}, this.nomenclature_node);
				
				this.init_formation();
			},
			
			init_formation: function(){
				/**
				 * TODO: Init des nomenclatures associ�s � la formation
				 */
				switch(this.record_formation.formation.get_nature()){
					case 0:
						var params = {
							id:this.record_formation.nomenclature.get_hash(),
							nomenclature:this.record_formation.nomenclature,
							dom_node:this.nomenclature_node,
								
						};
						this.nomenclature_ui = new NomenclatureUi(params);
						break;
					case 1:
						var obj = {nomenclature_voices:this.record_formation.nomenclature,record_formation_ui:this, dom_node: this.nomenclature_node};
						this.nomenclature_ui = new Nomenclature_voices_ui(obj);
						break;
				}
				this.update_formation_label();
				
			},
			
		    get_record_formation: function() {
				return this.record_formation;
			},
			
			set_record_formation: function(record_formation) {
				this.record_formation = record_formation;
			},
			
			get_dom_node: function() {
				return this.dom_node;
			},
			
			set_dom_node: function(dom_node) {
				this.dom_node = dom_node;
			},
			get_indice: function() {
				return this.indice;
			},
			
			set_indice: function(indice) {
				this.indice = parseInt(indice);
			},
			
			get_nomenclature_node: function() {
				return this.nomenclature_node;
			},
			
			set_nomenclature_node: function(nomenclature_node) {
				this.nomenclature_node = nomenclature_node;
			},			 
			update_record_formation: function(){
				var new_lbl = this.input_name.value;
				this.record_formation.set_label(new_lbl);
				this.update_formation_label();
			},
			create_selector: function(){
				var array_types = this.record_formation.get_types();
				this.type_selector = domConstruct.create('select', null, this.nomenclature_node);
				var option_vide = domConstruct.create('option', {value:0, innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_formation_type_select_default_value')}, this.type_selector);
				for(var i=0; i<array_types.length ; i++){
					var option = domConstruct.create('option', {value:array_types[i].get_id(), innerHTML:array_types[i].get_name()}, this.type_selector);
				}
				if(this.record_formation.type != null){
					for(var i=0 ; i<this.type_selector.options.length ; i++){
						if(parseInt(this.type_selector.options[i].value) == this.record_formation.type.get_id()){
							this.type_selector.selectedIndex = i;
						}
					}
				}
				this.own(on(this.type_selector, 'change', lang.hitch(this, this.update_record_formation_type)));
			},
			update_formation_label: function(){
				var new_lbl = "";
				new_lbl += this.record_formation.formation.get_name()+' ';
				if(this.record_formation.get_types() != null){
					if(this.record_formation.type != null){
						new_lbl += '/ '+this.record_formation.get_type().get_name()+' ';
					}
				}
				if(this.record_formation.get_label() != ""){
					new_lbl+= '- '+this.record_formation.get_label();
				}
				this.label.innerHTML = new_lbl;
				this.update_hidden_fields();
			},
			update_hidden_fields:function (){
				this.hidden_label.value = this.record_formation.get_label();
				if(this.record_formation.get_type()!=null)
					this.hidden_type.value = this.record_formation.get_type().get_id();
				else
					this.hidden_type.value = 0;
			},
			delete_formation: function(){
				topic.publish("record_formation_ui", "record_formation_delete",{record_formation_hash : this.record_formation.get_hash()});
			},
			add_input_hidden:function(){
				this.hidden_record_formations = domConstruct.create('input', {type:'hidden', name:'record_formations['+parseInt(this.get_indice())+']', value:this.record_formation.get_hash()}, this.dom_node);
				this.nomenclature_id = domConstruct.create('input', {type:'hidden', name:this.record_formation.get_hash()+'[nomenclature_id]', id:this.record_formation.get_hash()+'_nomenclature_id', value:this.record_formation.get_id()}, this.dom_node);
				this.nomenclature_order = domConstruct.create('input', {type:'hidden', name:this.record_formation.get_hash()+'[order]', id:this.record_formation.get_hash()+'_order', value:this.record_formation.get_order()}, this.dom_node);
			},
			destroy: function(){
				domConstruct.destroy(this.nomenclature_ui.get_dom_node());
				domConstruct.destroy(this.record_formation.get_hash()+'_formation_form_'+this.get_indice());
				domConstruct.destroy(this.hidden_record_formations);
				domConstruct.destroy(this.nomenclature_id);
				domConstruct.destroy(this.nomenclature_order);
				this.inherited(arguments);
			},
			check_validate: function(){
				return this.nomenclature_ui.check_validate();
			},
			expand_formation_node: function(){
				var idToExpand = this.nomenclature_node.id.replace('Child', '');
				expandBase(idToExpand);
			},
			update_record_formation_type: function(){
				if(this.record_formation.get_types() && this.record_formation.get_types().length){
					if(this.type_selector.options[this.type_selector.selectedIndex] == 0){
						this.record_formation.set_type(null);
					}
					else{
						var new_type = this.record_formation.get_type_from_id(this.type_selector.options[this.type_selector.selectedIndex].value);
						this.record_formation.set_type(new_type);
					}
				}
				this.update_input_name();
				this.update_record_formation();
			},
			update_input_name: function(){
				this.input_name.value = this.record_formation.formation.get_name()+(this.record_formation.get_type() ? ' '+this.record_formation.get_type().get_name() : '');
			},
			set_order: function(order){
				this.record_formation.set_order(order);
				this.nomenclature_order.setAttribute("value", order);
			},
			get_order:function(){
				return this.record_formation.get_order();
			}
	    });
	});