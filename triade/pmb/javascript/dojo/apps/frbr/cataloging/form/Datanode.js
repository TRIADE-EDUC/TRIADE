// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Datanode.js,v 1.2 2018/01/24 10:54:46 vtouchard Exp $


define(["dojo/_base/declare", "dojo/parser", "dojo/topic", "dojo/_base/lang", "dojo/dom", "dijit/form/Form", "dojo/dom-form", "dojo/text!pmbBase/ajax.php?module=frbr&categ=cataloging&sub=forms&action=get_form&form=datanode_form_tpl", "dojo/query", "dojo/request/xhr"], function(declare, parser, topic, lang, dom, Form, domForm, template, query, xhr){
	return declare([Form], {
		templateString: template,
		categories : [],
		values: {},
		
		postCreate: function(){
			this.inherited(arguments);
			parser.parse(this.containerNode);
			var children = this.getChildren();
			for(var i=0 ; i<children.length ; i++){
				switch(children[i].get("id")){
					case "datanode_num_category":
						for(var j=0 ; j<this.categories.length ; j++){
							if(this.categories[j].value == this.values.parent_category){
								this.categories[j].selected = true;
								break;
							}
						}
						children[i].addOption(this.categories);
						break;
					case "datanode_title":
						if(this.values.title){
							children[i].set("value",this.values.title);
						}
						break;
					case "datanode_comment":
						if(this.values.comment){
							children[i].set("value",this.values.comment);
						}
						break;
					case "datanode_button_delete":
						if(this.values.id){
							children[i].on("click",lang.hitch(this,this.deleteDatanode));
						}else{
							children[i].destroy();	
						}
						break;
				}
			}
			//édition d'une veille. En création on ne passeras pas dans le if
			if(this.values.allowed_users){
				for(var i=0 ; i<this.values.allowed_users.length ; i++){
					this.values.allowed_users[i] = this.values.allowed_users[i].toString();
				}
				var checkboxes = query('input[type="checkbox"]', this.containerNode);
				for(var i=0 ; i<checkboxes.length ; i++){
					if(checkboxes[i].name == "datanode_allowed_users[]" && this.values.allowed_users.indexOf(checkboxes[i].value)!=-1){
						checkboxes[i].checked = true;
					}
				}	
			}
		},
		onSubmit: function(){
			//on met l'id si d�fini...
			if(this.values.id){
				dom.byId("id").value = this.values.id;
			}
			if(this.isValid()){
				topic.publish("Datanode","saveDatanode",domForm.toObject(this.containerNode));
			}else{
				//Pour afficher les message de validation
				this.validate();
			}
			return false;
		},
		deleteDatanode:function(){
			if(confirm(pmbDojo.messages.getMessage("frbr","frbr_cataloging_confirm_datanode_delete"))){
				topic.publish("Datanode","deleteDatanode",{
					datanodeId: this.values.id}
				);
			}
		},
	});
});