// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_record_ui.js,v 1.7 2016/11/29 13:00:29 vtouchard Exp $


define(["dojo/_base/declare", "dijit/_WidgetBase", "dojo/dom-construct", "dojo/dom", "dojo/on", "dojo/_base/lang", "apps/nomenclature/nomenclature_record_formations_ui", "apps/nomenclature/nomenclature_record_formations", "apps/nomenclature/nomenclature_record_partial_ui", "dijit/registry", "dojo/topic"], function(declare, _WidgetBase, domConstruct, dom, on, lang, record_formations_ui, record_formations, record_partial_ui, registry, topic){
	/*
	* Classe nomenclature_record_ui. Aiguilleur général pour le formulaire (général ou celui d'une fille
	*/
	return declare("nomenclature_record_ui",[_WidgetBase], {
		current_form:null,
		mode:null,
		is_a_child:null,
		num_record:null,
		record_formations:null,
		child_detail:null,
		myparams: null,
		selector:null,
		
		constructor: function(params){
			this.myparams = params;
			this.num_record = this.myparams.num_record;
			this.record_formations = this.myparams.record_formations;
			if(!this.record_formations){
				this.record_formations = "[]";
			}
			this.child_detail = this.myparams.child_detail;
			this.own(topic.subscribe('nomenclature_ui', lang.hitch(this, this.handle_events)));
			this.own(topic.subscribe('nomenclature_voices_ui', lang.hitch(this, this.handle_events)));
		},

	    handle_events : function(evt_type,evt_args){
	    	//pour le débug, on affiche tout ce que l'on voit passer
	    	//console.log("DEBUG",evt_type,evt_args);
	    	switch(evt_type){
	    		case "error_on_submit" :
    				this.expand_nomenclature_node();
	    			break;
	    	}
	    },
		
		buildRendering: function(){
			this.inherited(arguments);
			if(this.child_detail){
				this.mode = "child";
			}else{
				this.mode = "general";
			}
			var label = domConstruct.create("div",{
				class:'row'
			},this.domNode);
			var div = domConstruct.create("div",{
				class:'row'
			},this.domNode);
			domConstruct.create("label",{
				innerHTML: registry.byId('nomenclature_datastore').get_message("nomenclature_js_selector")+' '+ (this.mode == "child" ? registry.byId("nomenclature_datastore").get_message("nomenclature_js_child_selector").toLowerCase() : registry.byId('nomenclature_datastore').get_message("nomenclature_js_general_selector").toLowerCase())
			},label);
//			this.selector = domConstruct.create("select",{},div);
//			domConstruct.create("option",{
//				value: "general",
//				selected: this.mode == "general" ? "selected" : false,
//				innerHTML: registry.byId('nomenclature_datastore').get_message("nomenclature_js_general_selector")
//			},this.selector);
//			domConstruct.create("option",{
//				value:"child",
//				selected: this.mode == "child" ? "selected" : false,
//				innerHTML: registry.byId("nomenclature_datastore").get_message("nomenclature_js_child_selector")
//			},this.selector);
//			
			
			//this.own(on(this.selector,"change",lang.hitch(this,this.refresh_child)));
			this.refresh_child();
		},
		refresh_child: function(){
//			this.mode = this.selector.options[this.selector.selectedIndex].value;
//			if(this.child){
//				this.child.destroy();
//			}
			var rootChild = domConstruct.create("div",{
				id: this.id+"_child"
			},this.domNode);
			if(this.mode == "general"){
				this.child = new record_formations_ui({
					num_record: this.num_record,
					formations: this.record_formations,
					dom_node: rootChild
				});
			}else{
				this.child = new record_partial_ui({
					num_record: this.num_record,
					detail: this.child_detail,
					parent_node: rootChild
				});
			}
		},
		check_validate: function(){
			if(this.mode == "general"){
				return this.child.check_validate();
			}
			return true;
		},
		expand_nomenclature_node: function(){
			var id_nomenclature_node = this.domNode.parentNode.parentNode.id;
			var find_id = /el(\d+)Child/g;
			var result = find_id.exec(id_nomenclature_node);
			expandBase('el'+result[1]);
		},
		destroy: function(){
			this.domNode.innerHTML = "";
			this.inherited(arguments);
		}
	});
});