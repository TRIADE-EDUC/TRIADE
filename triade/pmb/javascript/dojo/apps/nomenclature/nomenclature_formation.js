// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_formation.js,v 1.1 2015/01/27 16:14:59 vtouchard Exp $

define(["dojo/_base/declare", "apps/nomenclature/nomenclature_musicstand_ui"], function(declare, Musicstand_ui){
	/*
	 *Classe nomenclature_formation. Classe representant une formation
	 */
	  return declare(null, {
			    
		  	id:0,
		  	nature:null,
		  	name:null,
		  	types:null,
		  	
		    constructor: function(id, nature, name){
		    	this.set_id(id);
		    	this.set_nature(nature);
		    	this.set_name(name);
		    },
		    get_id: function() {
				return this.id;
			},
			
			set_id: function(id) {
				this.id = parseInt(id);
			},

			get_nature: function() {
				return this.nature;
			},
			
			set_nature: function(nature) {
				this.nature = parseInt(nature);
			},

			get_name: function() {
				return this.name;
			},
			
			set_name: function(name) {
				this.name = name;
			},			
			get_types: function() {
				return this.types;
			},
			
			set_types: function(types) {
				this.types = types;
			},
			
			get_type_from_id: function(id){
				for(var i=0 ; i<this.types.length ; i++){
					if(this.types[i].get_id() == id){
						return this.types[i];
					}
				}
			},
			
			 
			
			
	    });
	});