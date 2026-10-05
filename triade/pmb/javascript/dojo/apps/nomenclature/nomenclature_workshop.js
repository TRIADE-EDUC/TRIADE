// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_workshop.js,v 1.12 2016/02/23 14:07:00 apetithomme Exp $

define(["dojo/_base/declare","apps/nomenclature/nomenclature_instruments_list", "dijit/registry"], function(declare, Instruments_list, registry){
	/*
	 *Classe nomenclature_workshop. Classe representant un atelier
	 */
	  return declare(null, {
			id:0,
		  	label:"",
		  	effective:0,
		  	instruments_list:null,
		  	nomenclature : null,
		    valid:false,
		    abbreviation: "",
		    hash:null,
		    order: 0,
		    defined: 0,
		    indice: 0,
		    
		    constructor: function(indice,order){
		    	this.set_order(order);
		    	this.set_indice(indice);
		    	this.instruments_list = new Instruments_list();
		    	this.instruments_list.set_workshop(this);
		    },
		    
		    set_hash : function(hash){
		    	this.hash = hash+"_workshop_"+this.indice;
		    },
		    
		    get_hash: function(){
		    	if(!this.hash){
		    		this.set_hash(this.nomenclature.get_hash());
		    	}
		    	return this.hash;
		    },
		    
		    set_nomenclature : function(nomenclature){
		    	this.nomenclature = nomenclature;
		    },
		    
		    get_nomenclature : function(){
		    	return this.nomenclature;
		    },
		    
		    get_label: function(){
				return this.label;
			},
			
			set_label: function(label){
				this.label = label;
			},
			
			get_effective: function() {
				this.calc_effective();
				return this.effective;
			},
			
			set_effective: function(effective) {
				this.effective = parseInt(effective);
			},
			
			get_order: function() {
				return this.order;
			},
			
			set_order: function(order) {
				this.order = parseInt(order);
			},
			
			get_defined: function() {
				return parseInt(this.defined);
			},
			
			set_defined: function(defined) {
				this.defined = defined;
			},
			
			get_id: function(){
				return this.id;
			},
				
			set_id: function(id){
				this.id = id;
			},
			
			get_indice: function(){
				return this.indice;
			},
			
			set_indice: function(indice){
				this.indice = indice;
			},
			
			get_instruments_list: function() {
				return this.instruments_list;
			},
			
			set_instruments_list: function(instruments_list) {
				this.instruments_list = instruments_list;
			},
			
			set_abbreviation: function(abbreviation){
		    	this.abbreviation = abbreviation.trim();
		    },
		    
		    get_abbreviation: function(){
		    	return this.abbreviation;
		    },
		    
			calc_abbreviation: function(){
				var abbreviation= "";
				for(var i=0 ; i<this.instruments_list.instruments.length ; i++){
					if (this.instruments_list.instruments[i].get_name() != "") {
						abbreviation += this.instruments_list.instruments[i].get_abbreviation();
						if(i<this.instruments_list.instruments.length-1)
							abbreviation += " / ";
					}
				}
				this.set_abbreviation(abbreviation);
			},
			
			check: function(){
				this.valid = true;
				for(var i=0 ; i<this.instruments_list.instruments.length ; i++){
					if(!this.instruments_list.instruments[i].check()){
						this.valid = false;
						this.error_message = registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_check_instrument_incorrect');
						break;
					}
				}
				return this.valid;
			},
			
			get_hidden_field_name:function (name){
				if(name)
					return this.nomenclature.get_hidden_field_name()+'['+name+']';
				else
					return this.nomenclature.get_hidden_field_name();
			},
			
			get_error_message : function(){
				return this.error_message;
			}

	    });
	});