// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_type_formation.js,v 1.1 2015/01/27 16:14:59 vtouchard Exp $

define(["dojo/_base/declare", "apps/nomenclature/nomenclature_musicstand_ui"], function(declare, Musicstand_ui){
	/*
	 *Classe nomenclature_type_formation. Classe representant un type de formation
	 */
	  return declare(null, {
			    
		  	id:0,
		  	name:null,
		  	formation:null,

		    constructor: function(id, name, formation){
		    	this.set_id(id);
		    	this.set_name(name);
		    	this.set_formation(formation);
		    },
		    get_id: function() {
				return this.id;
			},
			
			set_id: function(id) {
				this.id = id;
			},
			
			get_name: function() {
				return this.name;
			},
			
			set_name: function(name) {
				this.name = name;
			},
			
			get_formation: function() {
				return this.formation;
			},
			
			set_formation: function(formation) {
				this.formation = formation;
			},
			
	    });
	});