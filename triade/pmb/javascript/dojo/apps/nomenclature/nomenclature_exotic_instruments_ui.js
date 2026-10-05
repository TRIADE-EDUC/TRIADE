// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_exotic_instruments_ui.js,v 1.9 2017/11/30 10:53:34 dgoron Exp $

define(["dojo/_base/declare", "apps/nomenclature/nomenclature_instruments_list_ui", "dojo/dom-construct","dojo/dom", "dijit/registry", "dojo/topic", "dojo/_base/lang", "dijit/_WidgetBase", "dojo/on"], function(declare, Instruments_list_ui, domConstruct, dom, registry, topic, lang, _WidgetBase, on){
	/*
	 *Classe nomenclature_exotic_instruments_ui. Classe classe g�n�rant le dom contenant la liste des instruments non standards
	 */
	  return declare("nomenclature_exotic_instruments_ui",[_WidgetBase], {
			
			id:null,
		  	dom_node:null,
		  	instruments_list_ui:null,
		  	instruments_list:null,
		  	instruments_list_node:null,
		  	ajax_dispatched: false,
	  
		  	constructor: function(params){
		  		this.own(topic.subscribe('instruments_list_ui', lang.hitch(this, this.handle_events)));
		    },
		    
		    handle_events : function(evt_type,evt_args){
		    	//pour le d�bug, on affiche tout ce que l'on voit passer
		    	//console.log("DEBUG",evt_type,evt_args);
		    	switch(evt_type){
		    		case "record_formation_ready" :
		    			this.increment_total_formation();
		    			break;
		    		case "instru_changed" :
		    			if(evt_args.hash.indexOf(this.instruments_list.get_hash())!=-1){
		    				//this.generate_inputs();
			    		}
		    			break;
		    		case "instruments_list_ui_ready" : 
		    			if(evt_args.hash.indexOf(this.instruments_list.get_hash())!=-1){
		    				topic.publish('exotic_instruments_ui', 'exotic_instruments_ready', {
				    			hash : this.instruments_list.get_hash(),
				    			nomenclature_hash: this.instruments_list.nomenclature.get_hash()
				    		});
			    		}
		    			break;
		    	}
		    },
		    
		    buildRendering: function(){
		    	this.inherited(arguments);
		    	this.build_form();
		    },
		    
		    build_form: function(){
		    
		    	domConstruct.create('div', {class:'row'}, this.get_dom_node());
	    		var noeud_princ = domConstruct.create('div', {
	    			id:this.get_dom_node().id+'_nomenclature_form_family_non_standard', 
	    			class:'notice-parent'}, this.get_dom_node());
	    		/*
	    		 * Cr�ation d'un code type "pmb" permettant de d�plier les familles en cliquant sur une image
	    		 */
	    		var img_plus = domConstruct.create('img', {
	    			id:this.get_dom_node().id+'_nomenclature_form_family_non_standardImg', 
	    			class:'img_plus', 
	    			hspace:'3', 
	    			border:'0', 
	    			onclick:'expandBase(\''+this.get_dom_node().id+'_nomenclature_form_family_non_standard\', true); return false;', 
	    			title:'d\351tail',
	    			src:pmbDojo.images.getImage('plus.gif')
	    				}, noeud_princ);
	    		
	    		this.own(on(img_plus, 'click', lang.hitch(this, this.ajax_dispatch)));
	    		
	    		var span = domConstruct.create('span', {class:'notice-heada',innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_exotic_instruments_label')}, noeud_princ);
	    		this.instruments_list_node = domConstruct.create('div', {
	    			id:this.get_dom_node().id+'_nomenclature_form_family_non_standardChild',
	    			startOpen:"Yes",
	    			class:'notice-child',
	    			callback : "recalc_recept",
	    			style:{
	    				marginBottom:'6px',
	    				display:'none',
	    				width:'94%',
	    			}
	    		}, this.get_dom_node());
	    		
		    	var row_div = domConstruct.create('div', {class:'row'}, this.instruments_list_node);
		    	
		    	this.init_instruments_list_ui();
		    },
		    init_instruments_list_ui: function(){
		    	var params = {
		    			id:this.instruments_list.get_hash(),
		    			instruments_list:this.instruments_list,
		    			dom_node:this.instruments_list_node,
		    			mode:"exotic_instruments"
		    	};
		    	this.instruments_list_ui = new Instruments_list_ui(params);
		    },
		    get_instruments_list: function() {
				return this.instruments_list;
			},
			
			set_instruments_list: function(instruments_list) {
				this.instruments_list = instruments_list;
			},
			get_dom_node: function() {
				return this.dom_node;
			},
			
			set_dom_node: function(dom_node) {
				this.dom_node = dom_node;
			},
			ajax_dispatch: function(){
				if (!this.ajax_dispatched) {
					this.ajax_dispatched = true;
					this.instruments_list_ui.init_instruments_action();
				}
			}
	  });
	});