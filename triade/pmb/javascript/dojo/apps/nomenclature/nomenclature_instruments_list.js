// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_instruments_list.js,v 1.7 2015/02/02 16:33:32 dgoron Exp $

define(["dojo/_base/declare"], function(declare){
	/*
	 *Classe nomenclature_instrument_list. Classe representant une liste d'instruments
	 */
	  return declare(null, {
		    
		    effective:0,
		    nomenclature:null,
		    instruments:null, /** Tableau d'instruments **/
		    hash: null,
		    workshop: null,
		    
		    constructor: function(){
		    	this.instruments = new Array();
		    },
		    
		    set_nomenclature : function (nomenclature){
		    	this.nomenclature = nomenclature;
		    },
		    
		    get_nomenclature : function (){
		    	return this.nomenclature;
		    },
		    
		    set_workshop : function (workshop){
		    	this.workshop = workshop;
		    },
		    
		    get_workshop : function (){
		    	return this.workshop;
		    },
			
		    set_hash:function(hash){
		    	this.hash = hash+"_instrument_list";
		    },
		    
		    get_hash: function(){
		    	if(!this.hash){
		    		if(this.nomenclature != null ){
		    			var parent_hash = this.nomenclature.get_hash()+"_exotics";
		    		}else if(this.workshop != null){
		    			var parent_hash = this.workshop.get_hash();
		    		}
		    		this.set_hash(parent_hash);
		    	}
		    	return this.hash;
		    },
		    
			get_effective: function() {
				this.calc_effective();
				return this.effective;
			},
			
			set_effective: function(effective) {
				this.effective = parseInt(effective);
			},
			 
			add_instrument: function(instrument, reorder){
				var reorder = reorder || false;
				if(this.instruments == null){
					this.instruments = new Array();
				}
				instrument.set_musicstand(null);
				instrument.set_instruments_list(this);
				this.instruments.push(instrument);
				if(reorder){
					for(var i=0 ; i<this.instruments.length; i++){
						this.instruments[i].set_order(i+1);
					}
				}
			},

			calc_effective: function(){
				var effective = 0;
				for(var i=0; i<this.instruments.length ; i++){
					effective=effective+this.instruments[i].get_effective();
				}
				this.set_effective(effective);	
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
			get_hidden_field_name:function (name){
				if (this.nomenclature != null) {
					if(name)
						return this.nomenclature.get_hidden_field_name()+'['+name+']';
					else
						return this.nomenclature.get_hidden_field_name();
				}
				if (this.workshop != null) {
					if(name)
						return this.workshop.get_hidden_field_name()+'['+name+']';
					else
						return this.workshop.get_hidden_field_name();
				}
			},
	    });
	});
