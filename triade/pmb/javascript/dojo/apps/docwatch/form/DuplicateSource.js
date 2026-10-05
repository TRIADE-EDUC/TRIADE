// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DuplicateSource.js,v 1.1 2019/03/13 14:48:22 dgoron Exp $

define(["dojo/_base/declare", "dojo/parser", "dojo/topic", "dojo/_base/lang", "dojo/dom", "dijit/form/Form", "dojo/dom-form", "dojo/text!pmbBase/ajax.php?module=dsi&categ=docwatch&sub=forms&action=get_form&form=docwatch_source_duplicate_form_tpl", "dojo/dom-construct"], function(declare, parser, topic, lang, dom, Form, domForm, template, domConstruct){
	return declare([Form], {
		templateString: template,
		watches : [],
		values: {},
		
		postCreate: function(){
//			this.own(topic.subscribe('watchStore', lang.hitch(this, this.handleEvents)));
			this.inherited(arguments);
			parser.parse(this.containerNode);
			var children = this.getChildren();
			for(var i=0 ; i<children.length ; i++){
				switch(children[i].get("id")){
					case "num_watch":
						for(var j=0 ; j<this.watches.length ; j++){
							if(this.watches[j].value == this.values.parent_watches){
								this.watches[j].selected = true;
								break;
							}
						}
						children[i].addOption(this.watches);
						break;
					case "title":
						if(this.values.title){
							children[i].set("value",this.values.title);
						}
						break;
				}
			}
		},
		
		onSubmit: function(){
			// On met l'ID dans le champ hidden
			if(this.values.id){
				dom.byId("id_duplicated_datasource").value = this.values.id;
			}
			if(this.values.className){
				dom.byId("className").value = this.values.className;
			}
			if(this.isValid()){
				topic.publish("duplicateSource","duplicateSource",domForm.toObject(this.containerNode));
			}
			return false;
		}
	});
});