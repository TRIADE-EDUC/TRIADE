// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Category.js,v 1.6 2015/02/20 16:14:58 vtouchard Exp $


define(["dojo/_base/declare", "dojo/parser", "dojo/topic", "dojo/_base/lang", "dojo/dom", "dijit/form/Form", "dojo/dom-form", "dojo/text!pmbBase/ajax.php?module=dsi&categ=docwatch&sub=forms&action=get_form&form=docwatch_category_form_tpl", "dojo/dom-construct"], function(declare, parser, topic, lang, dom, Form, domForm, template, domConstruct){
	return declare([Form], {
		templateString: template,
		categories : [],
		values: {},
		
		postCreate: function(){
			this.own(topic.subscribe('watchStore', lang.hitch(this, this.handleEvents)));
			this.inherited(arguments);
			parser.parse(this.containerNode);
			var children = this.getChildren();
			
			for(var i=0 ; i<children.length ; i++){
				switch(children[i].get("id")){
					case "parent":
						for(var j=0 ; j<this.categories.length ; j++){
							if(this.categories[j].value == this.values.parent_category){
								this.categories[j].selected = true;
								break;
							}
						}
						children[i].addOption(this.categories);
						break;
					case "title":
						if(this.values.title){
							children[i].set("value",this.values.title);
						}
						break;
					case "docwatch_form_delete":
						if(this.values.id){
							children[i].on("click",lang.hitch(this,this.deleteCategory));
						}else{
							children[i].destroy();	
						}
						break;
				}
			}
		},
		
		handleEvents:function(evtType, evtArgs){
			switch(evtType){
			case 'deleteCategoryError':
				var node = domConstruct.create('div', {
					innerHTML:evtArgs.message, 
					style:{
						fontWeight:'bold', 
						color:'red'
					}
				});
				domConstruct.place(node, this.domNode, "before");
				break;
			}
		},
		
		deleteCategory: function(){
			topic.publish("category","deleteCategory",{
				categoryId: this.values.id}
			);
		},
		
		onSubmit: function(){
			//on met l'id si défini...
			if(this.values.id){
				dom.byId("id").value = this.values.id;
			}
			if(this.isValid()){
				topic.publish("category","saveCategory",domForm.toObject(this.containerNode));
			}
			return false;
		},
	});
});