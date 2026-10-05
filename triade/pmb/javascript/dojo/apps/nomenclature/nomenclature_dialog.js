// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_dialog.js,v 1.4 2017/09/05 08:37:29 vtouchard Exp $


define(["dojo/_base/declare", "dojo/topic", "dojo/_base/lang","dojo/dom","dojo/on","dojo/dom-attr", "apps/pmb/PMBDialog", "dijit/registry", "apps/nomenclature/form/nomenclature_form_instrument"], function(declare, topic, lang, dom, on, domAttr, Dialog, registry, Instrument_form){
	return declare([Dialog], {
		sourceNode:null,
		
		postCreate: function(){
			this.inherited(arguments);
			this.destroyDescendants();
			this.own(topic.subscribe("form_instrument",lang.hitch(this,this.handleEvents)));
			this.set("title", registry.byId('nomenclature_datastore').get_message('nomenclature_js_dialog_add_instrument'));
			this.addChild(new Instrument_form());
			this.own(on(dom.byId('nomenclature_instrument_form_exit'), 'click', lang.hitch(this, this.hide)));
		},
		
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case "instrument_created" :
					this.destroyDescendants();
					registry.byId('nomenclature_datastore').add_instrument_datastore(evtArgs);
					domAttr.set(this.sourceNode,'value',evtArgs.code);
					if(this.sourceNode.id.match('_input_instr')){
	    				var dijit_id = this.sourceNode.id.split('_input_instr')[0];
	    			}else if(this.sourceNode.id.match('_input_other_inst')){
	    				var dijit_id = this.sourceNode.id.split('_input_other_inst')[0];
	    			}
	    			if(dijit_id != undefined){
	    				topic.publish("form_instrument","input_change",{
			    			hash : dijit.registry.byId(dijit_id).instrument.get_hash(),
			    		})
	    			}
					this.hide();
					break;
			}
		},
		set_code: function(){
			domAttr.set(dom.byId('code'),'value',this.sourceNode.value);
		},
		onHide: function() {
			this.inherited(arguments);
			  this.destroyDescendants();
			  this.destroy();
			  delete this;
		}
	});
});