// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_voice.js,v 1.5 2015/02/05 13:03:02 vtouchard Exp $

// Ajout du dijit registry pour récupérer le nom des voices
define(["dojo/_base/declare", "dijit/registry"], function(declare, registry){
	/*
	 *Classe nomenclature_voice. Classe representant une voix
	 */
	  return declare(null, {
			    
		  	abbreviation:null,
		  	voices_list:null, /** Lien au niveau supérieur, instance de voice list **/
		  	hash:null,
		  	code:"",
		  	name:"",
		  	effective:1,
		  	id:0,
		  	order:1,
		  	indefinite_effective:false,

		    constructor: function(code,name){
		    	this.set_name(name);
		    	this.set_code(code);
		    },
		   
		    get_id: function() {
				if(!this.id){
					this.set_id(registry.byId('nomenclature_datastore').get_voice_id_from_code(this.get_code()));
				}
				return this.id;
			},
			
			set_id: function(id) {
				this.id = parseInt(id);
			},		
			
			get_abbreviation: function() {
				this.calc_abbreviation();
				return this.abbreviation;
			},
			
			set_abbreviation: function(abbreviation) {
				this.abbreviation = abbreviation;
			},
			
			get_voices_list: function() {
				return this.voices_list;
			},
			
			set_voices_list: function(voices_list) {
				this.voices_list = voices_list;
			},
			
		    set_hash: function(hash){
		    	this.hash = hash+"_voice_"+this.get_order();
		    },
		    
		    get_hash: function(){
		    	if(!this.hash){
		    		var parent_hash = this.voices_list.get_hash();
		    		this.set_hash(parent_hash);
		    	}
		    	return this.hash;
		    },
		   
			get_code: function() {
				return this.code;
			},
			
			set_code: function(code) {
				this.code = code;
			},
			
		    get_name: function() {
		    	if(!this.name){
					this.name = registry.byId("nomenclature_datastore").get_voice_name(this.code);
				}
				return this.name;
			},
			
			set_name: function(name) {
				this.name = name;
			},
			
			get_order: function() {
				return this.order;
			},
			
			set_order: function(order) {
				this.order = order;
			},
			
			get_effective: function() {
				if(this.indefinite_effective == false){
					return this.effective;
				}
				return 0;
			},
			
			set_effective: function(effective) {
				this.effective = parseInt(effective);
			},

			get_voices_list: function() {
				return this.voices_list;
			},
			
			set_voices_list: function(voices_list) {
				this.voices_list = voices_list;
			},
			
			calc_abbreviation: function(){
				var abbreviation = "";
				if(!this.get_indefinite_effective()){
					abbreviation+=this.get_effective();
				}else{
					abbreviation+="~";
				}
				this.set_abbreviation(abbreviation);
			},
			
			get_indefinite_effective: function() {
				return this.indefinite_effective;
			},
			
			set_indefinite_effective: function(indefinite_effective) {
				this.indefinite_effective = indefinite_effective;
			},
			
	    });
	});