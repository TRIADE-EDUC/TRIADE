// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_record_formation.js,v 1.18 2016/06/23 15:18:55 dgoron Exp $

define(["dojo/_base/declare", "apps/nomenclature/nomenclature_nomenclature", "dijit/registry", "apps/nomenclature/nomenclature_nomenclature_voices"], function(declare, Nomenclature, registry, Nomenclature_voices){
	/*
	 *Classe nomenclature_record_formation. Classe representant une formation d'une notice
	 */
	  return declare(null, {
			
		  	formation:null,
		  	record:null,
		  	nomenclature:null,
		  	label:null,
		  	type:null,
		  	hash:null,
		  	id: 0,
		  	order: 0,
		  	note:null,
		  	exotic_instruments:null,
		  	
		    constructor: function(formation, num_record, order, params){
		    	this.set_formation(formation);
		    	if(params){
		    		this.set_id(params.id);
		    		this.set_record(params.num_record);
		    		this.init_hash(order);
		    		this.set_label(params.label);
		    		this.set_type(this.formation.get_type_from_id(params.num_type));
		    		this.set_order(order);
		    		this.set_note(params.notes);
		    		this.set_exotic_instruments_note(params.exotic_instruments_note);
		    		switch(formation.get_nature()){
		    		case 0:
		    			var nomenclature_param = new Nomenclature(params.abbreviation, registry.byId('nomenclature_datastore').get_families_datastore(), null, params.workshops, params.instruments, this, params.families_notes);
		    			this.set_nomenclature(nomenclature_param);
		    			break;
		    		case 1:
		    			/** 
		    			 * Instanciation d'une nomenclature de type voix
		    			 */
		    			var nomenclature_param = new Nomenclature_voices(params.abbreviation, null, this);
		    			this.set_nomenclature(nomenclature_param);
		    			break;
		    		}		    		
		    	}else{
		    		this.set_label("");
		    		this.set_record(num_record);
		    		this.init_hash(order);
		    		this.set_order(order);
		    		this.set_note("");
		    		this.set_exotic_instruments_note("");
		    		switch(formation.get_nature()){
		    		case 0:
		    			var nomenclature_std = new Nomenclature(this.get_empty_abbr_instruments(), registry.byId('nomenclature_datastore').get_families_datastore(), null, [], [] ,this);
			    		this.set_nomenclature(nomenclature_std);
		    			break;
		    		case 1:
		    			var nomenclature_voices_std = new Nomenclature_voices(this.get_empty_abbr_voices(), null, this);
		    			this.set_nomenclature(nomenclature_voices_std);
		    			break;
		    		}
		    	}
		    },
		    
		    init_hash : function(order){
		    	this.hash = this.record+"_"+order+"_"+(new Date()).getTime();
		    },
		    
		    get_hash : function (){
		    	return this.hash;
		    },
		    
		    get_formation: function() {
				return this.formation;
			},
			
			set_formation: function(formation) {
				this.formation = formation;
			},
			
			get_record: function() {
				return this.record;
			},
			
			set_record: function(record) {
				this.record = record;
			},
			
			get_nomenclature: function() {
				return this.nomenclature;
			},
			
			set_nomenclature: function(nomenclature) {
				this.nomenclature = nomenclature;
			},
			
			get_label: function() {
				return this.label;
			},
			
			set_label: function(label) {
				this.label = label;
			},
			
			get_type: function() {
				return this.type;
			},
			
			set_type: function(type) {
				this.type = type;
			},
			get_record: function() {
				return this.record;
			},
			
			set_record: function(record) {
				this.record = record;
			},
			get_types: function(){
				return this.formation.get_types();
			},
			get_type_from_id:function(id){
				return this.formation.get_type_from_id(id);
			},
			get_empty_abbr_instruments: function(){
				var families_tree = registry.byId('nomenclature_datastore').get_families_datastore();
				var empty_abbr = "";
				for(var i=0 ; i<families_tree.length ; i++){
					for(var j=0 ; j<families_tree[i].musicstands.length ; j++){
						empty_abbr+="0";
						if(j<(families_tree[i].musicstands.length-1))
							empty_abbr+=".";
					}
					if(i<(families_tree.length-1))
						empty_abbr+=" - ";
				}
				return empty_abbr;
			},
			get_empty_abbr_voices: function(){
				return "";
			},
			get_formation_type_id: function(){
				return this.formation.get_id();
			},
			get_hidden_field_name:function (name){
				if(name)
					return this.get_hash()+'['+name+']';
				else
					return this.get_hash();
			},
			set_id: function(id){
				this.id = id;
			},
			get_id: function(){
				return this.id;
			},
			set_order: function(order){
				this.order = order;
			},
			get_order: function(){
				return this.order;
			},
			set_note: function(note){
				this.note = note;
			},
			get_note: function(){
				return this.note;
			},
			set_exotic_instruments_note: function(exotic_instruments_note){
				this.exotic_instruments_note = exotic_instruments_note;
			},
			get_exotic_instruments_note: function(){
				return this.exotic_instruments_note;
			},
	    });
	});