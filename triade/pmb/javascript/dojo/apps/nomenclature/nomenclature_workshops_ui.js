// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_workshops_ui.js,v 1.23 2017/11/30 10:53:34 dgoron Exp $


define(["dojo/_base/declare", "apps/nomenclature/nomenclature_workshop", "apps/nomenclature/nomenclature_workshop_ui", "dojo/on", "dojo/dom-construct", "dojo/topic", "dojo/_base/lang", "dijit/registry", "dijit/_WidgetBase"], function(declare, Workshop, Workshop_ui, on, domConstruct, topic, lang, registry, _WidgetBase){
	/*
	 *Classe nomenclature_workshops_ui. Classe g�n�rant la partie du formulaire li�e a un atelier
	 */
	  return declare("nomenclature_workshops_ui",[_WidgetBase], {
			
		  	nomenclature:null,
		  	workshops:null,
		  	total_workshops:0,
		  	label_total_workshops:null,
		  	dom_node:null,
		  	abbreviation_node:null,
		  	workshop_node:null,
		  	
		    constructor: function(params){
		    	this.workshops = new Array();
		    	this.own(topic.subscribe("instrument_ui", lang.hitch(this, this.handle_events)));
		    	this.own(topic.subscribe("workshop_ui", lang.hitch(this, this.handle_events)));
		    },
			
		    handle_events : function(evt_type,evt_args){
		    	switch(evt_type){
		    		case "instru_changed" :
		    			if(evt_args.hash.indexOf(this.nomenclature.get_hash()) != -1){
		    				this.maj_abbreviation();
		    			}
		    			break;
		    		case "workshop_delete" :
			    		if(evt_args.hash.indexOf(this.nomenclature.get_hash()) != -1){
			    			this.delete_workshop_event(evt_args.order);
			    			this.maj_abbreviation();
			    			this.generate_inputs();
			    		}
		    			break;
		    		case "workshop_state_changed": 
		    			if(evt_args.hash.indexOf(this.nomenclature.get_hash()) != -1){
//		    				if(this.workshops.length == 1){
		    					topic.publish("workshops_ui", "workshop_state_changed",{nomenclature_hash : this.nomenclature.get_hash(), workshop_undefined:(!this.workshops[0].workshop.defined)?true:false});
//		    				}
		    			}
		    			break;
		    		case "workshop_ready":
		    			if(evt_args.hash.indexOf(this.nomenclature.get_hash()) != -1){
		    				this.workshops_ready();
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
	    			id:this.get_dom_node().id+'_nomenclature_form_workshops', 
	    			class:'notice-parent'}, this.get_dom_node());
	    		
	    		var img_plus = domConstruct.create('img', {
	    			id:this.get_dom_node().id+'_nomenclature_form_workshopsImg', 
	    			class:'img_plus', 
	    			hspace:'3', 
	    			border:'0', 
	    			onclick:'expandBase(\''+this.get_dom_node().id+'_nomenclature_form_workshops\', true); return false;', 
	    			title:'d\351tail',
	    			src:pmbDojo.images.getImage('plus.gif')
	    				}, noeud_princ);
	    		
	    		this.label_total_workshops = domConstruct.create('label', {innerHTML:''}, noeud_princ);
	    		domConstruct.create('label', {innerHTML:' '+registry.byId('nomenclature_datastore').get_message('nomenclature_js_workshops_label')+' : '}, noeud_princ);
	    		
	    		if(this.nomenclature.get_workshops_abbreviation() == ""){
	    			this.nomenclature.calc_workshops_abbreviation();
	    		}
	    		this.abbreviation_node = domConstruct.create('span', {innerHTML:this.nomenclature.get_workshops_abbreviation()}, noeud_princ);
	    		
	    		var link_delete = domConstruct.create('a', {onclick:''}, noeud_princ);
	    		domConstruct.create('img', {src:pmbDojo.images.getImage('trash.png'), alt:registry.byId('nomenclature_datastore').get_message('nomenclature_js_workshops_delete'), title:registry.byId('nomenclature_datastore').get_message('nomenclature_js_workshops_delete')}, link_delete);
		    	this.own(on(link_delete, "click", lang.hitch(this, this.delete_workshops)));
		    	
	    		this.set_workshop_node(domConstruct.create('div', {
	    			id:this.get_dom_node().id+'_nomenclature_form_workshopsChild',
	    			startOpen:"Yes",
	    			class:'notice-child',
	    			callback : "recalc_recept",
	    			style:{
	    				marginBottom:'6px',
	    				display:'none',
	    				width:'94%',
	    			}
	    		}, this.get_dom_node()));
	    		
	    		var h3_node = domConstruct.create('h3', {innerHTML:registry.byId('nomenclature_datastore').get_message('nomenclature_js_workshop_add')}, this.get_workshop_node());
		    	var input_plus = domConstruct.create('input', {type:'button', value:'+', class:'bouton'}, h3_node);
		    	this.own(on(input_plus, 'click', lang.hitch(this, this.add_workshop)));
		    	this.create_part();
		    	this.total_workshops = this.workshops.length;
		    	this.label_total_workshops.innerHTML = this.total_workshops;
		    },

		    set_workshops: function(workshops){
		    	this.workshops = workshops;
		    },
		    get_workshops: function(){
		    	return this.workshops;
		    },
		    set_dom_node: function(dom_node){
		    	this.dom_node = dom_node;
		    },
		    get_dom_node: function(){
		    	return this.dom_node;
		    },
		    set_workshop_node: function(workshop_node){
		    	this.workshop_node = workshop_node;
		    },
		    get_workshop_node: function(){
		    	return this.workshop_node;
		    },
		    get_total_instruments: function() {
				return this.total_instruments;
			},
					    
			add_workshop: function(is_undefined){
				var workshop = new Workshop(this.get_max_indice()+1, this.workshops.length+1);
				is_undefined && typeof is_undefined == "boolean" ? workshop.set_defined(0) : workshop.set_defined(1);
				workshop.set_nomenclature(this.nomenclature);
				this.nomenclature.add_workshop(workshop);
				var params = {
						id:workshop.get_hash(),
						workshop:workshop,
						dom_node:this.workshop_node,
						indice:this.get_max_indice()+1
				};
				this.workshops.push(new Workshop_ui(params));
				this.generate_inputs();
				this.total_workshops++;
				this.label_total_workshops.innerHTML = this.total_workshops;
				topic.publish("workshops_ui", "new_workshop",{nomenclature_hash : this.nomenclature.get_hash(), workshop_undefined:parseInt(workshop.get_defined())});
		    },
		    
		    delete_workshop_event: function(order){
		    	var index_workshop_ui;
		    	for(var i=0 ; i<this.nomenclature.workshops.length ; i++){
		    		if(this.nomenclature.workshops[i].get_order() == order){
		    			index_workshop_ui = i; 
		    		}
		    	}
		    	this.workshops[index_workshop_ui].destroy();
		    	this.workshops.splice(index_workshop_ui, 1);
		    	this.nomenclature.delete_workshop(order, true);
		    	
		    	var array_nodes = new Array();
		    	for(var i=0; i<this.workshops.length ; i++){
		    		array_nodes.push(this.workshops[i].domNode);
		    	}
		    	var newarr = array_nodes.sort(this.sort_nodes);
		    	for(var i=0; i<newarr.length ; i++){
		    		var ui_instance = registry.byId(newarr[i].id);
		    		//ui_instance.set_indice(i);
		    		ui_instance.set_order(i+1);
		    	}
		    	
		    	this.total_workshops--;
		    	this.label_total_workshops.innerHTML = this.total_workshops;
		    	topic.publish("workshops_ui", "workshop_deleted",{nomenclature_hash : this.nomenclature.get_hash(), is_undefined: (this.total_workshops == 1 && !parseInt(this.workshops[0].workshop.defined))?true:false});
		    },
		    
		    create_part: function(){
		    	if (this.nomenclature.workshops.length) {
			    	for(var i=0 ; i<this.nomenclature.workshops.length ; i++){
			    		var params = {
			    				id:this.nomenclature.workshops[i].get_hash(),
			    				workshop:this.nomenclature.workshops[i],
			    				dom_node:this.workshop_node,
			    				indice:i
			    		};
			    		this.workshops.push(new Workshop_ui(params));
//			    		if(this.nomenclature.workshops.length == 1 && !parseInt(this.nomenclature.workshops[i].get_defined())){
//			    			topic.publish("workshops_ui", "new_workshop",{nomenclature_hash : this.nomenclature.get_hash(), workshop_undefined:parseInt(this.nomenclature.workshops[i].get_defined())});
//			    		}
			    	}
		    	} else {
		    		topic.publish('workshops_ui', 'workshops_ready', {
			    		hash : this.nomenclature.get_hash(),
			    		nomenclature_hash : this.nomenclature.get_hash()
					});
		    	}
		    	this.generate_inputs();
		    },
		    		    
		    maj_abbreviation: function(){
		    	this.nomenclature.calc_workshops_abbreviation();
		    	this.abbreviation_node.innerHTML = this.nomenclature.get_workshops_abbreviation();
		    },
		    
		    update_label_workshops: function(){
				this.workshops.set_label(this.input_name.value);
		    	this.label.innerHTML = ' / '+this.workshop.get_label();
			},
		    
			sort_nodes: function(a, b){
				if(parseInt(a.getAttribute('order')) < parseInt(b.getAttribute('order'))){
					return -1;
				}
				if(parseInt(a.getAttribute('order')) == parseInt(b.getAttribute('order'))){
					return 0;
				}
				if(parseInt(a.getAttribute('order')) > parseInt(b.getAttribute('order'))){
					return 1;
				}
			},
		    
			get_max_indice: function(){
				var max_indice=-1;
		    	for(var i=0 ; i<this.workshops.length ; i++){
		    		if(this.workshops[i].get_indice()>max_indice){
		    			max_indice = this.workshops[i].get_indice();
					}
		    	}
		    	return max_indice;
		    },
			
		    generate_inputs:function(){
			    for(var i=0 ; i<this.workshops.length ; i++){
			    	this.workshops[i].generate_inputs();
			    }
			},
			
			delete_workshops: function(){
				topic.publish("workshops_ui", "workshops_delete",{nomenclature_hash : this.nomenclature.get_hash()})
			},
			workshops_ready: function(){
				if (this.total_workshops == this.workshops.length){
					topic.publish('workshops_ui', 'workshops_ready', {
			    		hash : this.nomenclature.get_hash(),
			    		nomenclature_hash : this.nomenclature.get_hash()
					});
				}
			}
	    });
	});