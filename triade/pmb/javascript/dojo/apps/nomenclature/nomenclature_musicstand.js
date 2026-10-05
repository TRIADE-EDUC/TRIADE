// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_musicstand.js,v 1.27 2016/03/02 10:44:50 vtouchard Exp $

define(["dojo/_base/declare",  "apps/nomenclature/nomenclature_instrument", "apps/nomenclature/nomenclature_family", "dijit/registry"], function(declare, Instrument, Family, registry){
	/*
	 *Classe nomenclature_musicstand. Classe representant un pupitre
	 */
	  return declare(null, {
		    
		  	family:null,
		    name:"",
		    effective:0,
		    standard_instrument:null,
		    instruments:null, /** Tableau d'instruments **/
		    valid:false,
		    divisable:false,
		    id:0,
		    abbreviation: "",
		    indefinite_effective:false,
		    used_by_workshops : false,
		    hash:null,
		    
		    constructor: function(name, family, std, divisable){
		    	var divisable = divisable || false;
		    	this.set_divisable(divisable);
		    	this.set_name(name);
		    	this.set_family(family);
		    	this.set_standard_instrument(std);
		    	this.instruments = new Array();
		    }, 
		    
		    set_hash:function(hash){
		    	this.hash = hash+"_musicstand_"+this.get_id();
		    },
		    
		    get_hash: function(){
		    	if(!this.hash){
		    		this.set_hash(this.family.get_hash());
		    	}
		    	return this.hash;
		    },
		    
		    is_indefinite_effective: function(){
		    	return this.indefinite_effective;
		    },
		    
		    set_indefinite_effective: function(boolean){
		    	this.indefinite_effective = boolean;
		    },
		    
		    get_family: function() {
				return this.family;
			},
			
			set_family: function(family) {
				this.family = family;
			},
			
			get_name: function() {
				return this.name;
			},
			
			set_name: function(name) {
				this.name = name;
			},
			
			get_effective: function() {
				this.calc_effective();
				return this.effective;
			},
			
			set_effective: function(effective) {
				this.effective = parseInt(effective);
			},
			
			get_standard_instrument: function() {
				return this.standard_instrument;
			},
			
			set_standard_instrument: function(standard_instrument) {
				this.standard_instrument = standard_instrument;
			},
			get_divisable: function() {
				return this.divisable;
			},
			
			set_divisable: function(divisable) {
				this.divisable = divisable;
			}, 
			
			set_used_by_workshops: function(used_by_workshops){
		    	this.used_by_workshops = used_by_workshops;
		    },
		    
		    get_used_by_workshops: function(){
		    	return this.used_by_workshops;
		    },
		    	

 
			add_instrument: function(instrument, reorder){
				var reorder = reorder || false;
				if(this.instruments == null){
					this.instruments = new Array();
				}
				instrument.set_musicstand(this);
				this.instruments.push(instrument);
				if(reorder){
					for(var i=0 ; i<this.instruments.length; i++){
						this.instruments[i].set_order(i+1);
					}
				}
			},

			calc_effective: function(){
				this.set_indefinite_effective(false);
				if(!this.get_used_by_workshops()){
					var effective = 0;
					for(var i=0; i<this.instruments.length ; i++){
						if (this.instruments[i].is_indefinite_effective()) {
							effective = 0;
							this.set_indefinite_effective(true);
							break;
						}
						effective+=this.instruments[i].get_effective();
					}
					this.set_effective(effective);
				}else{
    				for(var i=0 ; i<this.family.nomenclature.workshops.length ; i++){
    					if(!this.family.nomenclature.workshops[i].get_defined()){
    						this.set_indefinite_effective(true);
    						break;
    					}
    				}
    				if(!this.is_indefinite_effective()){
    					this.set_effective(this.family.nomenclature.workshops.length);
    				}else{
    					this.set_effective(0);
    				}
				}
			},
			
			set_instruments: function(instruments){
				this.instruments = instruments;
			}, 
			get_instruments: function(){
				return this.instruments;
			}, 
			
			get_max_order: function(){
				var max=0;
				for(var i=0 ; i<this.instruments.length ; i++){
					if(this.instruments[i].get_order()>max){
						max = this.instruments[i].get_order();
					}
				}
				return max;
			},
			get_id: function(){
				return this.id;
			},
			set_id: function(id){
				this.id = id;
			},
			delete_instrument: function(order, reorder){
				var reorder = reorder || false;
				for(var i=0 ; i<this.instruments.length ; i++){
					if(this.instruments[i].get_order() == order){
						this.instruments.splice(i, 1);
						break;
					}
				}
				if(reorder){
					for(var i=0 ; i<this.instruments.length; i++){
						if(this.instruments[i].get_order() > order){
							this.instruments[i].set_order(this.instruments[i].get_order()-1);
						}
					}
				}
			},
			
			set_abbreviation: function(abbreviation){
		    	this.abbreviation = abbreviation.trim();
		    },
		    
		    get_abbreviation: function(){
		    	return this.abbreviation;
		    },
		    
			calc_abbreviation: function(){
				var abbreviation= "";
				var tab_instruments = new Array();
				var instruments_reordered = this.get_instruments();
				instruments_reordered.sort(this.sort_array);
				if((!this.get_divisable() && !this.get_used_by_workshops())){
					//Flag indique si dans le pupitre, des crochets seront nécessaire.
					var flag = false;
					for(var i=0; i<instruments_reordered.length ; i++){
						//Si l'instrument n'est pas standard, ou si ils comprend des instruments annexe ou si sa partie est != de 0 (pupitre des cordes)
						if(!instruments_reordered[i].is_standard() || instruments_reordered[i].get_others_instruments()!=null || instruments_reordered[i].get_part()){
							flag = true;
						}
						if (instruments_reordered[i].get_part()) {
							tab_instruments.push(instruments_reordered[i].get_abbreviation());
						} else {
							tab_instruments.push(instruments_reordered[i].get_abbreviation());
						}
					}
					abbreviation += this.get_effective();
					if (flag) {
						abbreviation += "[";
						abbreviation += tab_instruments.join('.');
						abbreviation += "]";
					}
				}else if(this.get_used_by_workshops()){
					if(this.is_indefinite_effective()){
						abbreviation += this.family.nomenclature.indefinite_character;
					}else{
						abbreviation += this.get_effective();
					}
				}else{
					if(this.is_indefinite_effective()){
						abbreviation += this.family.nomenclature.indefinite_character;
					}else{
						abbreviation += this.get_effective();
					}
					if(this.get_max_order()>1){
						abbreviation += "[";
						for(var i=0 ; i<instruments_reordered.length ; i++){
							abbreviation+= instruments_reordered[i].get_abbreviation();
							if(i<instruments_reordered.length -1){
								abbreviation += ".";
							}
						}
						abbreviation += "]";
					}	
				}
				this.set_abbreviation(abbreviation);
			},
			/**
			 * Tri tableau des instrument selon l'order défini en property
			 */
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
			check : function(){
				var total_effective = 0;
				this.valid = true;
				var undefined_musicstand = false;
				//on commence par vérifier les instruments...
				for(var i=0 ; i<this.instruments.length ; i++){
					if(!this.instruments[i].check()){
						this.valid = false;
						this.error_message = registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_check_instrument_incorrect')
						break;
					}else if(this.instruments[i].is_indefinite_effective()){
						undefined_musicstand = true;
					}else{
						total_effective+=this.instruments[i].get_effective();
					}
				}
				if(undefined_musicstand){
					total_effective = 0;
				}
				
				if(this.valid && this.get_used_by_workshops()){
					//nothing to do
				}else if(this.valid && total_effective != this.effective){
					this.error_message = registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_check_instrument_sum_effective_incorrect')
					this.valid = false;
				}
				return this.valid;
			},
			get_error_message : function(){
				return this.error_message;
			},
			
	    });
	});
