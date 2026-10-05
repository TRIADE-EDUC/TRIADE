// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_family.js,v 1.12 2016/03/16 15:16:58 vtouchard Exp $

define(["dojo/_base/declare", "apps/nomenclature/nomenclature_musicstand", "dijit/registry"], function(declare, Musicstand, registry){
	/*
	 *Classe nomenclature_family. Classe representant une famille d'instruments
	 */
	  return declare(null, {
			id:0,
		  	name:"",
		  	nomenclature: null,
		    musicstands:null,
		    valid:false,
		    abbreviation: "",
		    hash:null,
		    note:"",
		    
		    constructor: function(name){
		    	this.set_name(name);
		    },
		    
		    set_hash:function(hash){
		    	this.hash = hash+"_family_"+this.get_id();
		    },
		    
		    get_hash: function(){
		    	if(!this.hash){
		    		this.set_hash(this.nomenclature.get_hash());
		    	}
		    	return this.hash;
		    },
		    
		    get_nomenclature : function(){
		    	return this.nomenclature;
		    },
		    
		    set_nomenclature : function(nomenclature){
		    	this.nomenclature = nomenclature;
		    },
		    
		    get_name: function(){
				return this.name;
			},
			
			set_name: function(name){
				this.name = name;
			},
			
			set_id: function(id){
				this.id = id;
			},
			
			get_id: function(){
				return this.id;
			},
			
			get_musicstands: function() {
				return this.musicstands;
			},
			
			set_musicstands: function(musicstands) {
				this.musicstands = musicstands;
			},

			add_musicstand: function(musicstand){
				if(this.musicstands == null){
					this.musicstands = new Array();
				}
				musicstand.set_family(this);
				this.musicstands.push(musicstand);
			},
			
			get_musicstand: function (indice){
				return this.musicstands[indice];
			},
			
			set_abbreviation: function(abbreviation){
		    	this.abbreviation = abbreviation.trim();
		    },
		    
		    get_abbreviation: function(){
		    	return this.abbreviation;
		    },
		    
			calc_abbreviation: function(){
				var abbreviation= "";
				for(var i=0 ; i<this.musicstands.length ; i++){
					this.musicstands[i].calc_effective();
					this.musicstands[i].calc_abbreviation();
					abbreviation += this.musicstands[i].get_abbreviation();
					if(i<this.musicstands.length-1)
						abbreviation += ".";
				}
				this.set_abbreviation(abbreviation);
			},
			
			check: function(){
				this.valid = true;
				for(var i=0 ; i<this.musicstands.length ; i++){
					if(!this.musicstands[i].check()){
						this.valid = false;
						this.error_message = registry.byId('nomenclature_datastore').get_message('nomenclature_js_nomenclature_error_check_musicstand_incorrect')
						break;
					}
				}
				return this.valid;
			},
			
			set_note: function(note){
				this.note = note;
			},
			
			get_note: function(){
				return this.note;
			},
			
			get_hidden_field_name:function (name){
				if(name)
					return this.nomenclature.get_hidden_field_name()+'['+name+']['+this.id+']';
				else
					return this.nomenclature.get_hidden_field_name();
			},
			get_error_message : function(){
				return this.error_message;
			}

	    });
	});