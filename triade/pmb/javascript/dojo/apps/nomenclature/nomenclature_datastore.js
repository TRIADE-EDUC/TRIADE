// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_datastore.js,v 1.15 2016/02/26 14:52:26 dgoron Exp $


define(["dojo/_base/declare", "dijit/_WidgetBase"], function(declare, _WidgetBase){
	/*
	 *Classe nomenclature_datastore. Classe sotckant les différentes propriétés définies en administration pour les nomenclatures
	 */
	  return declare("nomenclature_datastore",[_WidgetBase], {
		  	formations_datastore:null,
		  	families_datastore:null,
		  	instruments_datastore:null,
		  	messages_datastore:null,
		  	voices_datastore:null,
		  	relation_code: null,
		  	
		    constructor: function(){
		    },
		    
		    buildRendering: function(){ 
		    
		    },
		    
		    get_families_datastore: function() {
				return this.families_datastore;
			},
			
			set_families_datastore: function(families_datastore) {
				this.families_datastore = families_datastore;
			},
			
			get_formations_datastore: function() {
				return this.formations_datastore;
			},
			
			set_formations_datastore: function(formations_datastore) {
				this.formations_datastore = formations_datastore;
			},
			
			get_instruments_datastore: function() {
				return this.instruments_datastore;
			},
			
			set_instruments_datastore: function(instruments_datastore) {
				this.instruments_datastore = instruments_datastore;
			},
			
			add_instrument_datastore: function(instrument) {
				this.instruments_datastore.push(instrument);
			},
			
			get_messages_datastore: function() {
				return this.messages_datastore;
			},
			
			set_messages_datastore: function(messages_datastore) {
				this.messages_datastore = messages_datastore;
			},
			
			get_message: function(code) {
				return this.messages_datastore[code];
			},
			
			get_voices_datastore: function() {
				return this.voices_datastore;
			},
			
			set_voices_datastore: function(voices_datastore) {
				this.voices_datastore = voices_datastore;
			},
			
			get_instrument_name: function (code){
				var instruments = this.instruments_datastore;
				for(var i=0 ; i<instruments.length ; i++){
					if(instruments[i].code == code){
						return instruments[i].name;
					}
				}
				return "";
			},
			get_id_from_code: function(code){
				var instruments = this.instruments_datastore;
				for(var i=0 ; i<instruments.length ; i++){
					if(instruments[i].code == code){
						return instruments[i].id;
					}
				}
				return 0;
			},
			
			get_code_from_id: function(id){
				var instruments = this.instruments_datastore;
				for(var i=0 ; i<instruments.length ; i++){
					if(instruments[i].id== id){
						return instruments[i].code;
					}
				}
				return "";
			},
			postCreate:function(){
				this.families_datastore = JSON.parse(this.families_datastore);
		    	this.instruments_datastore = JSON.parse(this.instruments_datastore);
		    	this.messages_datastore = JSON.parse(this.messages_datastore);
		    	this.formations_datastore = JSON.parse(this.formations_datastore);
		    	this.voices_datastore = JSON.parse(this.voices_datastore);
			},
			get_voice_name:function(code){
				var voices = this.voices_datastore;
				for(var i=0 ; i<voices.length ; i++){
					if(voices[i].code == code){
						return voices[i].name;
					}
				}
				return "";
			},
			get_voice_id_from_code:function(code){
				var voices = this.voices_datastore;
				for(var i=0 ; i<voices.length ; i++){
					if(voices[i].code == code){
						return voices[i].id;
					}
				}
				return 0;
			},	
			
			get_voice_code_from_id:function(id){
				var voices = this.voices_datastore;
				for(var i=0 ; i<voices.length ; i++){
					if(voices[i].id == id){
						return voices[i].code;
					}
				}
				return 0;
			},
			get_voice_order_from_code:function(code){
				var voices = this.voices_datastore;
				for(var i=0 ; i<voices.length ; i++){
					if(voices[i].code == code){
						return voices[i].order;
					}
				}
				return 0;
			},
			get_relation_code:function(){
				return this.relation_code;
			}
	  });
});