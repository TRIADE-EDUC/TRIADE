// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_form_instrument.js,v 1.1 2016/01/06 15:05:58 dgoron Exp $


define(["dojo/_base/declare", "dojo/parser", "dojo/topic", "dojo/_base/lang", "dojo/dom", "dijit/form/Form", "dojo/dom-form", "dojo/text!pmbBase/ajax.php?module=admin&categ=nomenclature&sub=forms&action=get_form&form=nomenclature_instrument_form_tpl", "dojo/query", "dojo/request/xhr"], function(declare, parser, topic, lang, dom, Form, domForm, template, query, xhr){
	return declare([Form], {
		templateString: template,
		
		postCreate: function(){
			this.inherited(arguments);
			parser.parse(this.containerNode);
		},
		onSubmit: function(){
			if(this.isValid()){
				this.save_instrument();
			}
			return false;
		},
		save_instrument: function(){
			xhr("./ajax.php?module=admin&categ=nomenclature&sub=instrument&action=create", {
				handleAs: "json",
				method:"POST",
				data:domForm.toObject(this.containerNode)
			}).then(lang.hitch(this,this.instrument_created),function(err){console.log(err)})
		},
		instrument_created: function(response){
			if(response.state) {
				topic.publish("form_instrument","instrument_created", response);
			} else {
				dom.byId('nomenclature_instrument_save_error').innerHTML = response.error_message;
			}
		}
	});
});