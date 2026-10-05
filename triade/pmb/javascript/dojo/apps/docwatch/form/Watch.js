// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Watch.js,v 1.13 2018/08/21 15:38:44 plmrozowski Exp $


define(["dojo/_base/declare", "dojo/parser", "dojo/topic", "dojo/_base/lang", "dojo/dom", "dijit/form/Form", "dojo/dom-form", "dojo/text!pmbBase/ajax.php?module=dsi&categ=docwatch&sub=forms&action=get_form&form=docwatch_watch_form_tpl", "dojo/query", "dojo/request/xhr"], function(declare, parser, topic, lang, dom, Form, domForm, template, query, xhr){
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
				case "ttl":
					if(this.values.ttl){
						children[i].set("value",this.values.ttl);
					}
					break;
				case "desc":
					if(this.values.desc){
						children[i].set("value",this.values.desc);
					}
					break;
				case "logo_url":
					if(this.values.logo_url){
						children[i].set("value",this.values.logo_url);
					}
					break;
				case "docwatch_form_delete":
					if(this.values.id){
						children[i].on("click",lang.hitch(this,this.deleteWatch));
					}else{
						children[i].destroy();	
					}
					break;
				case "record_types":
					for(var j=0 ; j<children[i].options.length ; j++){
						if(children[i].options[j].value == this.values.record_default_type){
							children[i].setValue(children[i].options[j].value);
						}
					}
					break;
				case "record_status":
					for(var j=0 ; j<children[i].options.length ; j++){
						if(children[i].options[j].value == this.values.record_default_status){
							children[i].setValue(children[i].options[j].value);
						}
					}
					break;
                case "indexation_lang":
                    for(var j=0 ; j<children[i].options.length ; j++){
						if(children[i].options[j].value == this.values.record_default_index_lang){
							children[i].setValue(children[i].options[j].value);
						}
                    }
					break;
                case "record_default_lang":
					if(this.values.record_default_lang){
						children[i].setValue(this.values.record_default_lang);
					}else{
						children[i].setValue("");
					}
					break;
                case "watch_record_lang_libelle":
					if(this.values.record_default_lang){
						children[i].set("value",this.values.record_default_lang_libelle);
					}else{
                        children[i].set("value", "");
                    }
					break;
                case "watch_record_is_new":
                    for(var j=0 ; j<children[i].options.length ; j++){
						if(children[i].options[j].value == this.values.record_default_is_new){
							children[i].setValue(children[i].options[j].value);
						}
					}
					break;
				case "article_type":
					for(var j=0 ; j<children[i].options.length ; j++){
						if(children[i].options[j].value == this.values.article_default_content_type){
							children[i].setValue(children[i].options[j].value);
						}
					}
					break;
				case "article_status":
					for(var j=0 ; j<children[i].options.length ; j++){
						if(children[i].options[j].value == this.values.article_default_publication_status){
							children[i].setValue(children[i].options[j].value);
						}
					}
					break;
				case "article_parent":
					for(var j=0 ; j<children[i].options.length ; j++){
						if(children[i].options[j].value == this.values.article_default_parent){
							children[i].setValue(children[i].options[j].value);
						}
					}
					break;
				case "section_type":
					for(var j=0 ; j<children[i].options.length ; j++){
						if(children[i].options[j].value == this.values.section_default_content_type){
							children[i].setValue(children[i].options[j].value);
						}
					}
					break;
				case "section_status":
					for(var j=0 ; j<children[i].options.length ; j++){
						if(children[i].options[j].value == this.values.section_default_publication_status){
							children[i].setValue(children[i].options[j].value);
						}
					}
					break;
				case "section_parent":
					for(var j=0 ; j<children[i].options.length ; j++){
						if(children[i].options[j].value == this.values.section_default_parent){
							children[i].setValue(children[i].options[j].value);
						}
					}
					break;
				case "watch_rss_link":
					if(this.values.watch_rss_link){
						children[i].set("value",this.values.watch_rss_link);
					}
					break;
				case "watch_rss_lang":
					if(this.values.watch_rss_lang){
						children[i].set("value",this.values.watch_rss_lang);
					}
					break;
				case "watch_rss_copyright":
					if(this.values.watch_rss_copyright){
						children[i].set("value",this.values.watch_rss_copyright);
					}
					break;
				case "watch_rss_editor":
					if(this.values.watch_rss_editor){
						children[i].set("value",this.values.watch_rss_editor);
					}
					break;
				case "watch_rss_webmaster":
					if(this.values.watch_rss_webmaster){
						children[i].set("value",this.values.watch_rss_webmaster);
					}
					break;
				case "watch_rss_image_title":
					if(this.values.watch_rss_image_title){
						children[i].set("value",this.values.watch_rss_image_title);
					}
					break;
				case "watch_rss_image_website":
					if(this.values.watch_rss_image_website){
						children[i].set("value",this.values.watch_rss_image_website);
					}
					break;
				case "boolean_expression":
					if(this.values.boolean_expression){
						children[i].set("value",this.values.boolean_expression);
					}
					break;
				}
			}
			//�dition d'une veille. En cr�ation on ne passeras pas dans le if
			if(this.values.allowed_users){
				for(var i=0 ; i<this.values.allowed_users.length ; i++){
					this.values.allowed_users[i] = this.values.allowed_users[i].toString();
				}
				var checkboxes = query('input[type="checkbox"]', this.containerNode);
				for(var i=0 ; i<checkboxes.length ; i++){
					if(checkboxes[i].name == "allowed_users[]" && this.values.allowed_users.indexOf(checkboxes[i].value)!=-1){
						checkboxes[i].checked = true;
					}
				}	
			}
			//Edition du logo de la veille
			if(this.values.id){
				var xhr_logo = './ajax.php?module=dsi&categ=docwatch&sub=watches&&action=get_logo_form&id='+this.values.id;
			} else {
				var xhr_logo = './ajax.php?module=dsi&categ=docwatch&sub=watches&&action=get_logo_form';
			}
			xhr(xhr_logo, {
				handleAs:'json',
			}).then(lang.hitch(this, this.gotLogoForm));
			
		},
		onSubmit: function(){
			//on met l'id si d�fini...
			if(this.values.id){
				dom.byId("id").value = this.values.id;
			}
			if(this.isValid()){
				topic.publish("watch","saveWatch",domForm.toObject(this.containerNode));
			}else{
				//Pour afficher les message de validation
				this.validate();
			}
			return false;
		},
		deleteWatch:function(){
			if(confirm(pmbDojo.messages.getMessage("dsi","docwatch_confirm_watch_delete"))){
				topic.publish("watch","deleteWatch",{
					watchId: this.values.id}
				);
			}
		},
		gotLogoForm:function(formLogo){
			dom.byId('docwatch_logo').innerHTML = formLogo;
		},
	});
});