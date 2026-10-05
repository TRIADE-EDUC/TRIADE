// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_family_ui.js,v 1.32 2017/11/30 10:53:34 dgoron Exp $


define(["dojo/_base/declare", "apps/nomenclature/nomenclature_musicstand_ui", "dojo/on", "dojo/dom-construct", "dojo/topic", "dojo/_base/lang", "dijit/_WidgetBase", "dijit/registry"], function(declare, Musicstand_ui, on, domConstruct, topic, lang, _WidgetBase, registry){
	/*
	 *Classe nomenclature_family_ui. Classe g�n�rant la partie du formulaire li�e a une famille
	 */
	  return declare("nomenclature_family_ui",[_WidgetBase], {
			
		  	id:null,
		  	family:null,
		  	dom_node:null,
		  	musicstands:null,
		  	total_musicstands:0,
		  	abbreviation_node:null,
		  	ajax_dispatched:false,
		  	
		  	
	    	constructor: function(params){
		    	this.musicstands = new Array();
		    	this.own(topic.subscribe("musicstand_ui", lang.hitch(this, this.handle_events)));
		    },

		    handle_events : function(evt_type,evt_args){
		    	switch(evt_type){
		    		case "musicstand_changed" :
		    			if(evt_args.family_hash == this.family.get_hash()){
		    				this.maj_abbreviation();	
		    				topic.publish('family_ui', 'family_changed', {
		    	    			hash : this.family.get_hash(),
		    	    			nomenclature_hash: this.family.nomenclature.get_hash()
		    	    		});
		    			}
		    			break;
		    		case "musicstand_ready" :
		    			if(evt_args.family_hash == this.family.get_hash()){
		    				this.musicstand_ready();
		    			}
		    			break;
		    	}
		    },
   
		    buildRendering: function(){
		    	this.inherited(arguments);
		    	this.build_form();
		    },
		    
		    build_form: function(){
		    	
		    	domConstruct.create('div', {class:'row'}, this.get_dom_node());
	    		var noeud_princ = domConstruct.create('div', {
	    			id:this.get_dom_node().id+'_nomenclature_form_family_'+this.family.get_id(), 
	    			class:'notice-parent'}, this.get_dom_node());
	    		/*
	    		 * Cr�ation d'un code type "pmb" permettant de d�plier les familles en cliquant sur une image
	    		 */
	    		var img_plus = domConstruct.create('img', {
	    			id:this.get_dom_node().id+'_nomenclature_form_family_'+this.family.get_id()+'Img', 
	    			class:'img_plus', 
	    			hspace:'3', 
	    			border:'0', 
	    			onclick:'expandBase(\''+this.get_dom_node().id+'_nomenclature_form_family_'+this.family.get_id()+'\', true); return false;', 
	    			title:'d\351tail',
	    			src:pmbDojo.images.getImage('plus.gif')
	    		}, noeud_princ);
	    		this.own(on(img_plus, 'click', lang.hitch(this, this.ajax_dispatch)));
	    		
	    		var span = domConstruct.create('span', {class:'notice-heada',innerHTML:this.family.get_name()}, noeud_princ);
	    		if(this.family.get_abbreviation() == ""){
	    			this.family.calc_abbreviation();
	    		}
	    		this.abbreviation_node = domConstruct.create('span', {innerHTML:' '+this.family.get_abbreviation()}, span);
	    		var noeud_enfant = domConstruct.create('div', {
	    			id:this.get_dom_node().id+'_nomenclature_form_family_'+this.family.get_id()+'Child',
	    			startOpen:"Yes",
	    			class:'notice-child',
	    			callback : "recalc_recept",
	    			style:{
	    				marginBottom:'6px',
	    				display:'none',
	    				width:'94%',
	    			}
	    		}, this.get_dom_node());
		    	for(var i=0 ; i<this.family.musicstands.length ; i++){
		    		var params = {
		    				id:this.family.musicstands[i].get_hash(),
		    				musicstand:this.family.musicstands[i],
		    				dom_node:noeud_enfant
		    		};
		    		this.musicstands.push(new Musicstand_ui(params));
		    	}
		    	var lib_note = domConstruct.create('div', {class:'row'}, noeud_enfant);
		    	domConstruct.create('label', {
		    		class:'etiquette',
		    		"for":this.get_dom_node().id+'_nomenclature_form_family_'+this.family.get_id()+'Child_note',
		    		innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_family_note')
		    	}, lib_note);
		    	var content_note = domConstruct.create('div', {class:'row'}, noeud_enfant);
		    	this.textarea_note = domConstruct.create('textarea', {
		    		id:this.get_dom_node().id+'_nomenclature_form_family_'+this.family.get_id()+'Child_note',
		    		name:this.get_dom_node().id+'_nomenclature_form_family_'+this.family.get_id()+'Child_note',
		    		class:'saisie-80em',
		    		wrap:'virtual',
		    		rows:'3',
		    		innerHTML:this.family.get_note()
		    	}, content_note);
		    	this.own(on(this.textarea_note, 'keyup', lang.hitch(this, this.update_note)));
		    	/** Cr�ation de l'input hidden de la note en vue de la sauvegarde **/
		    	this.hidden_note = domConstruct.create('input', {type:'hidden', name:this.family.get_hidden_field_name('families_notes'), value:this.family.get_note()}, content_note);
		    },
		    set_family: function(family){
		    	this.family = family;
		    },
		    get_family: function(){
		    	return this.family;
		    },
		    set_dom_node: function(dom_node){
		    	this.dom_node = dom_node;
		    },
		    get_dom_node: function(){
		    	return this.dom_node;
		    },
		    musicstand_ready:function(){
		    	this.total_musicstands++;
		    	if(this.total_musicstands == this.family.get_musicstands().length){
		    		this.maj_abbreviation();
		    		topic.publish('family_ui', 'family_ready', {
		    			hash : this.family.get_hash(),
		    			nomenclature_hash: this.family.nomenclature.get_hash()
		    		});
		    	}
		    },
		    purge_instruments : function(){
		    	for(var i=0 ; i<this.musicstands.length ; i++){
		    		this.musicstands[i].purge_instruments();
		    	}
		    	this.total_musicstands = 0;
		    },
		    maj_abbreviation: function(){
		    	this.family.calc_abbreviation();
		    	this.abbreviation_node.innerHTML = ' '+this.family.get_abbreviation();
		    },
		    update_note: function() {
		    	var note = this.textarea_note.value;
		    	this.family.set_note(note);
		    	this.update_hidden_fields();
		    },
		    update_hidden_fields:function (){
				this.hidden_note.value = this.family.get_note();
			},
			ajax_dispatch: function() {
				if (!this.ajax_dispatched) {
					this.ajax_dispatched = true;
					topic.publish('family_ui', 'family_expanded', {
		    			hash : this.family.get_hash(),
		    			nomenclature_hash: this.family.nomenclature.get_hash()
		    		});
				}
			},
			set_ajax_dispatched: function(ajax_dispatched) {
				this.ajax_dispatched = ajax_dispatched;
			},
			get_ajax_dispatched: function() {
				return this.ajax_dispatched;
			}
		    
	    });
	});