// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_instrument.js,v 1.17 2016/03/01 15:47:48 apetithomme Exp $

define(["dojo/_base/declare", "apps/nomenclature/nomenclature_musicstand", "dijit/registry"], function(declare, Musicstand, registry){
	/*
	 *Classe nomenclature_instrument. Classe representant un instrument
	 */
	  return declare(null, {
		    
		    name:"",
		    code:"",
		    id:0,
		    effective:1,
		    standard:true,
		    others_instruments:null,/** Tableau d'instruments representant les instruments annexe **/
		    order:1,
		    valid:false,
		    musicstand:null,
		    workshop:null,
		    valid:false,
		    part:0,
		    abbreviation: "",
		    indefinite_effective:false,
		    hash:null,
		    instruments_list:null,
		    id_exotic_instrument: 0,
		    id_workshop_instrument: 0,
		    
		    constructor: function(code, name){
		    	this.set_code(code);
		    	this.set_name(name);
		    },
		    
		    set_hash: function(hash){
		    	this.hash = hash+"_instrument_"+this.get_order();
		    },
		    
		    get_hash: function(){
		    	if(!this.hash){
		    		if(this.musicstand != null){
		    			var parent_hash = this.musicstand.get_hash();
		    		}else if (this.instruments_list != null){
		    			var parent_hash = this.instruments_list.get_hash();
		    		}else{
		    			var parent_hash = this.workshop.get_hash();
		    		}
		    		this.set_hash(parent_hash);
		    	}
		    	return this.hash;
		    },

		    is_indefinite_effective: function(){
		    	return this.indefinite_effective;
		    },
		    
		    set_indefinite_effective: function (boolean){
		    	this.indefinite_effective = boolean;
		    },
		    
		    get_name: function() {
		    	if(!this.name){
					this.name = registry.byId("nomenclature_datastore").get_instrument_name(this.code);
				}
				return this.name;
			},
			
			set_name: function(name) {
				this.name = name;
			},
			
			get_code: function() {
				return this.code;
			},
			
			set_code: function(code) {
				this.code = code;
			},
			
			get_effective: function() {
				return this.effective;
			},
			
			set_effective: function(effective) {
				this.effective = parseInt(effective);
			},
			
			get_others_instruments: function() {
				return this.others_instruments;
			},
			
			set_others_instruments: function(others_instruments) {
				this.others_instruments = others_instruments;
			},
			
			get_order: function() {
				return this.order;
			},
			
			set_order: function(order) {
				this.order = parseInt(order);
			},
			
			get_musicstand: function() {
				return this.musicstand;
			},
			
			set_musicstand: function(musicstand) {
				this.musicstand = musicstand;
			},
			
			get_workshop: function() {
				return this.workshop;
			},
			
			set_workshop: function(workshop) {
				this.workshop = workshop;
			},
			
			get_instruments_list: function() {
				return this.instruments_list;
			},
			
			set_instruments_list: function(instruments_list) {
				this.instruments_list = instruments_list;
			},
			
			get_part: function() {
				return this.part;
			},
			
			set_part: function(part) {
				var part = part || 0;
				this.part = part;
				this.set_order(part);
			},

			is_standard: function(){
				return this.standard;
			},
			
			get_standard: function() {
				return this.standard;
			},
			
			set_standard: function(standard) {
				this.standard = standard;
			},
 
			add_other_instrument: function(other_instrument){
				if(this.others_instruments == null){
					this.others_instruments = new Array();
				}
				other_instrument.set_order(this.get_others_instruments().length+1);
				this.others_instruments.push(other_instrument);
			},
			
			delete_other_instrument: function(order){
				this.others_instruments.splice(order, 1);
			},
			
			check: function(){
				this.valid = true;
				//Un instrument sans effectif, pas possible !
				if((this.effective  == 0) && !this.indefinite_effective){
					this.error_message = registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_check_instrument_effective')
					this.valid = false;
				}
				return this.valid;
			},
			get_error_message : function(){
				return this.error_message;
			},

			set_abbreviation: function(abbreviation){
		    	this.abbreviation = abbreviation.trim();
		    },
		    
		    get_abbreviation: function(){
		    	this.calc_abbreviation();
		    	return this.abbreviation;
		    },
			sort_array: function(a, b){
				if(a.get_order() < b.get_order()){
					return -1;
				}
				if(a.get_order() == b.get_order()){
					return 0;
				}
				if(a.get_order() > b.get_order()){
					return 1;
				}
			},
			calc_abbreviation: function(){
				var abbreviation= "";
				if(this.musicstand){
					var tab_others_instruments = new Array();
					var str_others_instruments = "";
					if(this.others_instruments != null) {
						var other_instruments = this.get_others_instruments();
						other_instruments.sort(this.sort_array);
						for(var i=0; i<other_instruments.length ; i++){
							tab_others_instruments.push(other_instruments[i].get_code());
						}
						str_others_instruments = tab_others_instruments.join('/');
					}
					if (this.standard) {
						if (this.musicstand.get_divisable()) {
							if (!this.effective && this.indefinite_effective) {
								abbreviation += this.musicstand.family.nomenclature.indefinite_character;
							} else {
								abbreviation += this.effective;
							}
						}else{
							abbreviation += this.order;
						}
					} else {
						if (this.musicstand.get_divisable()) {
							if (!this.effective && this.indefinite_effective) {
								abbreviation += this.musicstand.family.nomenclature.indefinite_character;
								abbreviation += this.code;
							} else {
								if (this.effective != 1){
									abbreviation += this.effective;
								}
								if (this.effective) {
									abbreviation += this.code;
								}
							}
						} else {
							abbreviation += this.code;
						}
					}
					if(str_others_instruments != "") {
						abbreviation += '/'+str_others_instruments;
					}
					this.set_abbreviation(abbreviation);
				}else{
					abbreviation = this.effective+' '+this.name;
					this.set_abbreviation(abbreviation);
				}
			},
			get_id: function() {
				if(!this.id){
					this.id = registry.byId('nomenclature_datastore').get_id_from_code(this.get_code());
				}
				return this.id;
			},
			
			set_id: function(id) {
				this.id = id;
			},
			set_id_exotic_instrument: function(id){
				this.id_exotic_instrument = id;
			},
			get_id_exotic_instrument: function(){
				return this.id_exotic_instrument;
			},
			set_id_workshop_instrument: function(id){
				this.id_workshop_instrument = id;
			},
			get_id_workshop_instrument: function(){
				return this.id_workshop_instrument;
			}
	    });
});