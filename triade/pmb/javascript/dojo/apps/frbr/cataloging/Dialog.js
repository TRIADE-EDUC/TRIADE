// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Dialog.js,v 1.1 2018/01/17 15:01:13 dgoron Exp $


define(["dojo/_base/declare", "dojo/topic", "dojo/_base/lang", "apps/pmb/PMBDialog", "apps/frbr/cataloging/form/Category", "apps/frbr/cataloging/form/Datanode"], function(declare, topic, lang, Dialog, CategoryForm, DatanodeForm){
	return declare([Dialog], {
		postCreate: function(){
			this.inherited(arguments);
			this.own(
				topic.subscribe("DatanodesUI",lang.hitch(this,this.handleEvents)),
				topic.subscribe("DatanodeStore",lang.hitch(this,this.handleEvents))
			);
		},
		
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case "showCategoryForm" :
					if(evtArgs.values.id){
						this.set("title", pmbDojo.messages.getMessage("frbr","frbr_cataloging_edit_category"));
					}else{
						this.set("title", pmbDojo.messages.getMessage("frbr","frbr_cataloging_add_category"));
					}
					this.destroyDescendants();
					this.addChild(new CategoryForm({
						categories: evtArgs.categories,
						values: evtArgs.values 
					}));
					this.show();
					break;
				case "showDatanodeForm" :
					if(evtArgs.values.id){
						this.set("title", pmbDojo.messages.getMessage("frbr","frbr_cataloging_edit_datanode"));
					}else{
						this.set("title", pmbDojo.messages.getMessage("frbr","frbr_cataloging_add_datanode"));
					}
					this.destroyDescendants();
					this.addChild(new DatanodeForm({
						categories: evtArgs.categories,
						values: evtArgs.values 
					}));
					this.show();
					break;
				case "categorySaved" :
				case "categoryDeleted" :
				case "datanodeSaved" :
				case "datanodeDeleted" :
					this.destroyDescendants();
					this.hide();
					break;
			}
		}
	});
});