// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_record_partial.js,v 1.5 2016/03/22 17:16:46 apetithomme Exp $

define(["dojo/_base/declare", "dojo/_base/lang", "dojo/topic", "dijit/registry", "dojo/request/xhr"], function(declare, lang, topic, registry, xhr){
	/*
	 *Classe nomenclature_record_formations_ui. Classe générant le formulaire permettant de représenter les formations d'une notice
	 */
	return declare("nomenclature_record_partial_ui",null, {
		num_record: null,
		num_parent:null,
		possible_values: null,
		relation_code:null,
		
		num_formation : null,
		num_musicstand: null,
		num_workshop: null,
		effective: null,
		order: null,
		num_type: null,
		num_voice: null,
		num_instrument: null,
		other: null,
		num_nomenclature: null,
		
		constructor: function(params){
			this.num_record = params.num_record;
			this.relation_code = registry.byId("nomenclature_datastore").get_relation_code();
			if(params.detail){
				this.set_informations(JSON.parse(params.detail));
			}
		},
		
		set_informations: function(detail){
			this.effective = detail.effective;
			this.order = parseInt(detail.order);
			this.num_musicstand = parseInt(detail.num_musicstand);
			this.num_formation = parseInt(detail.num_formation);
			this.num_workshop = parseInt(detail.num_workshop);
			this.num_type = parseInt(detail.num_type);
			this.num_instrument = parseInt(detail.num_instrument);
			this.num_voice = parseInt(detail.num_voice);
			this.num_nomenclature = parseInt(detail.num_nomenclature);
			this.other = new Array();
			if(detail.other){
				var others = detail.other.split("/");
				for(var i=0; i<others.length; i++){
					this.other.push(others[i]);
				}
			}
		},
		
		set_num_parent: function (num_parent){
			if(this.num_parent != parseInt(num_parent)){
				this.num_parent = parseInt(num_parent);
				this.get_possible_values();
			}
		},
		
		get_possible_values: function(){
			if(this.num_parent){
				xhr("./ajax.php?module=ajax&categ=nomenclature&sub=record_child&action=get_possible_values&id="+this.num_record+"&id_parent="+this.num_parent, {
					handleAs: "json"
				}).then(lang.hitch(this,this.got_possible_values),lang.hitch(this,this.error))
			}
			return false;
		},
		
		got_possible_values:function(response){
			this.possible_values = response;
			topic.publish("record_partial_ui","possible_values_ready",{possible_values: this.possible_values});
		},
		
		error: function(err){
			console.log(err)
		},
		
		get_effective: function(){
			return this.effective;
		},
		
		get_order: function(){
			return this.order;
		},
		
		get_num_voice: function(){
			return this.num_voice;
		},
		
		get_num_instrument: function(){
			return this.num_instrument;
		},
		
		get_num_workshop: function(){
			return this.num_workshop;
		},
		
		get_num_musicstand: function(){
			return this.num_musicstand;
		},
		
		get_num_formation: function(){
			return this.num_formation;
		},
		
		get_num_type: function(){
			return this.num_type;
		},
		
		get_num_nomenclature: function(){
			return this.num_nomenclature;
		},
		
		get_other: function(){
			return this.other;
		},
	});
});