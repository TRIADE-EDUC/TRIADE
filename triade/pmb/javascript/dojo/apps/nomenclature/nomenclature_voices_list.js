// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_voices_list.js,v 1.7 2022/10/14 08:02:10 jparis Exp $

define(["dojo/_base/declare", "dojo/_base/lang", "dojo/topic", "dijit/registry"], function(declare, lang, topic, registry){
	/*
	 *Classe nomenclature_voices_list. Classe representant une liste de voix
	 */
	  return declare(null, {
			    
		  	abbreviation:null,
		  	voices:null, /** Tableau de voix **/
		  	nomenclature_voices:null,
		  	hash:null,
		  	id:0,
		  	effective: 0,

		    constructor: function(){
		    	this.voices = new Array();
		    },
		    get_id: function() {
				return this.id;
			},
			
			set_id: function(id) {
				this.id = id;
			},
			
			get_abbreviation: function() {
				return this.abbreviation;
			},
			
			set_abbreviation: function(abbreviation) {
				this.abbreviation = abbreviation;
			},
			
			get_voices: function() {
				return this.voices;
			},
			
			set_voices: function(voices) {
				this.voices = voices;
			},
			
			get_nomenclature_voices: function() {
				return this.nomenclature_voices;
			},
			
			set_nomenclature_voices: function(nomenclature_voices) {
				this.nomenclature_voices = nomenclature_voices;
			},
			
			set_hash:function(hash){
		    	this.hash = hash+"_voices_list_"+this.get_id();
		    },
		    
		    get_hash: function(){
		    	if(!this.hash){
		    		this.set_hash(this.nomenclature_voices.get_hash());
		    	}
		    	return this.hash;
		    },
			get_effective:function(){
				return this.effective;
			},
			add_voice: function(voice, reorder){
				var reorder = reorder || false;
				if(this.voices == null){
					this.voices = new Array();
				}
				voice.set_voices_list(this);
				this.voices.push(voice);
				if(reorder){
					for(var i=0 ; i<this.voices.length; i++){
						this.voices[i].set_order(i+1);
					}
				}
			},
			get_max_order: function(){
				var max=0;
				for(var i=0 ; i<this.voices.length ; i++){
					if(this.voices[i].get_order()>max){
						max = this.voices[i].get_order();
					}
				}
				return max;
			},
			delete_voice: function(order, reorder){
				var reorder = reorder || false;
				for(var i=0 ; i<this.voices.length ; i++){
					if(this.voices[i].get_order() == order){
						this.voices[i] = null;
						this.voices.splice(i, 1);
						break;
					}
				}
				if(reorder){
					for(var i=0 ; i<this.voices.length; i++){
						if(this.voices[i].get_order() > order){
							this.voices[i].set_order(this.voices[i].get_order()-1);
						}
					}
				}
			},
			calc_abbreviation: function(){
				var array_voices = new Array();
				/** Cr�ation d'un array d'objets json repr�sentant chacune des voix **/
				for(var i=0 ; i<this.voices.length ; i++){
					array_voices.push({code:this.voices[i].get_code(), indice:i, effective:this.voices[i].get_effective(), order:this.voices[i].get_order(), abbreviation:this.voices[i].get_abbreviation()});
				}
				
				var array_types = new Array();
				/** Cr�ation d'un array d'array regroupant les voix par type **/
				/** TODO: reorder sur sub array **/
				for(var i=0 ; i<array_voices.length ; i++){
					var types = new Array();
					for(var j=0 ; j<array_voices.length ; j++){
						if((array_voices[i].code == array_voices[j].code && types.indexOf(array_voices[j]) == -1) && (!array_voices[j].already_flagged)){
							array_voices[j].already_flagged = true;
							types.push(array_voices[j]);
						}
					}
					if(types.length>0)
						array_types.push(types);
				}
				array_types.sort(this.sort_tessiture);
				var abbr = "";
				var indice = 1;
				var flag_part_undef = false;
				/** Parcours de l'array d'array et g�n�ration de l'abbreviation **/
				for(var i=0 ; i<array_types.length ; i++){
					var increment_effective=0;
					var flag_part_undef = false;
					array_types[i].sort(this.sort_array);
					for(var j=0 ; j<array_types[i].length ; j++){
						this.voices[array_types[i][j].indice].set_order(indice);
						if(array_types[i][j].effective == 0)
							flag_part_undef = true;
						if(array_types[i].length == 1){
							if(array_types[i][j].abbreviation == this.nomenclature_voices.indefinite_character){
								abbr+=array_types[i][j].code;
							}else{
								abbr+=array_types[i][j].abbreviation+array_types[i][j].code;
							}
						}else{
							increment_effective = increment_effective+parseInt(array_types[i][j].effective);
							if(j == array_types[i].length-1){
								if(flag_part_undef)
									abbr+=array_types[i][j].code+"[";
								else
									abbr+=increment_effective+array_types[i][j].code+"[";
								for(var h=0 ; h<array_types[i].length ; h++){
									if(h>0)
										abbr+='.';
									abbr+=array_types[i][h].abbreviation;
								}
								abbr+="]";
							}
						}
						indice++;
					}
					if(i<array_types.length-1){
						abbr+="."	
					}
				}
				this.set_abbreviation(abbr);
				/** Les nouveaux ordres ont �t� appliqu�s, on peut maintenant demander aux ui de se mettre � jour **/
				topic.publish("voices_list", "reorder_voices", {hash:this.get_hash()});
			},
			sort_tessiture: function(a, b){
				var a_code = parseInt(registry.byId("nomenclature_datastore").get_voice_order_from_code(a[0].code))
				var b_code = parseInt(registry.byId("nomenclature_datastore").get_voice_order_from_code(b[0].code))
				
				if(a_code < b_code){
					return -1;
				}
				if(a_code == b_code){
					return 0;
				}
				if(a_code > b_code){
					return 1;
				}
			},
			sort_array: function(a, b){
				if(a.order < b.order){
					return -1;
				}
				if(a.order == b.order){
					return 0;
				}
				if(a.order > b.order){
					return 1;
				}
			},
	    });
	});