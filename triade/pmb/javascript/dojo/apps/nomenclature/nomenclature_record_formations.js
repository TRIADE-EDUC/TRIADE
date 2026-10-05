// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_record_formations.js,v 1.5 2016/06/03 12:48:58 dgoron Exp $

define(["dojo/_base/declare", "apps/nomenclature/nomenclature_musicstand_ui"], function(declare, Musicstand_ui){
	/*
	 *Classe nomenclature_record_formations. Classe permettant de représenter les formations d'une notice
	 */
	  return declare(null, {
			    
		  	record_id:0,
		  	record_formations:null,
		  	
		    constructor: function(id){
		    	this.set_record_id(id);
		    	this.record_formations = new Array();
		    },
		    
		    get_record_id: function() {
				return this.record_id;
			},
			
			set_record_id: function(record_id) {
				this.record_id = record_id;
			},
			
			get_record_formations: function() {
				return this.record_formations;
			},
			
			set_record_formations: function(record_formations) {
				this.record_formations = record_formations;
			},
			
			add_formation: function(record_formation){
				this.record_formations.push(record_formation);
			},
			delete_formation: function(hash){
				for(var i=0 ; i<this.record_formations.length ; i++){
					if(this.record_formations[i].get_hash() == hash){
						this.record_formations.splice(i, 1);
						break;
					}
				}
				for(var i=0 ; i<this.record_formations.length ; i++){
					this.record_formations[i].set_order(i);
				}
			}, 
			get_last_formation: function(){
				return this.record_formations[this.record_formations.length-1];
			}
					
	    });
	});