// +-------------------------------------------------+
// é 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubstFileContentUI.js,v 1.11 2019/11/07 12:52:36 dgoron Exp $

define(["dojo/_base/declare", 
        "dojox/layout/ExpandoPane", 
        "dojox/layout/ContentPane", 
        "dojo/dom-construct", 
        "dojo/dom", 
        "dojo/on", 
        "dojo/topic",
        "dojo/_base/lang",
        "dijit/registry",
        "dijit/form/Button",
        "dojo/request",
        "dojo/dom-style",
        "dijit/form/DropDownButton",
        "dijit/DropDownMenu",
        "dijit/MenuItem",
        "dijit/form/Select",
        "dojo/dom-attr",
        "dojo/io-query"], function(declare, ExpandoPane, ContentPane, domConstruct, dom, on, topic, lang, registry, Button, request, domStyle, DropDownButton, DropDownMenu, MenuItem, Select, domAttr, ioQuery){
	
	return declare([ContentPane], {
		path:null,
		filename:null,
		textareaNode:null,
		hasSubstFile:null,
		isWritableDir:null,
		cancelButton:null,
		createButton:null,
		saveButton:null,
		deleteButton:null,
		constructor: function(){

		},
		postCreate:function(){
			this.inherited(arguments);
			this.own(
				topic.subscribe('Tree', lang.hitch(this, this.handleEvents)),
				topic.subscribe('FileUI', lang.hitch(this, this.handleEvents))
			);
		},
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case "fileTreeSelected":
					this.path = evtArgs.path;
					this.filename = evtArgs.filename.replace('.xml', '_subst.xml');
					if(evtArgs.hasSubstFile != this.hasSubstFile) {
						this.destroyContent();
						this.hasSubstFile = evtArgs.hasSubstFile;
					}
					this.loadContent();
					break;
				case "substFileTreeSelected":
					this.path = evtArgs.path;
					this.filename = evtArgs.filename;
					this.destroyContent();
					this.hasSubstFile = true;
					this.loadContent();
					break;
			}
		},
		loadButtons: function() {
			if(this.path && this.filename){
				if(!this.cancelButton) {
					this.cancelButton = domConstruct.create('input', {"type": "button", "class": "bouton", "id" : "misc_subst_file_cancel", "name" : "misc_subst_file_cancel", "value" : pmbDojo.messages.getMessage('misc', 'misc_subst_file_cancel')}, this.containerNode);
					domConstruct.place(this.cancelButton, this.containerNode);
					this.own(on(dom.byId('misc_subst_file_cancel'), 'click', lang.hitch(this, this.cancelFile)));
				}
				if(this.hasSubstFile) {
					if(!this.saveButton) {
						this.saveButton = domConstruct.create('input', {"type": "button", "class": "bouton", "id" : "misc_subst_file_save", "name" : "misc_subst_file_save", "value" : pmbDojo.messages.getMessage('misc', 'misc_subst_file_save')}, this.containerNode);
						domConstruct.place(this.saveButton, this.containerNode);
						this.own(on(dom.byId('misc_subst_file_save'), 'click', lang.hitch(this, this.saveFile)));
					}
					if(!this.deleteButton) {
						this.deleteButton = domConstruct.create('input', {"type": "button", "class": "bouton", "id" : "misc_subst_file_delete", "name" : "misc_subst_file_delete", "value" : pmbDojo.messages.getMessage('misc', 'misc_subst_file_delete')}, this.containerNode);
						domConstruct.place(this.deleteButton, this.containerNode);
						this.own(on(dom.byId('misc_subst_file_delete'), 'click', lang.hitch(this, this.deleteFile)));
					}
				} else {
					if(!this.createButton) {
						this.createButton = domConstruct.create('input', {"type": "button", "class": "bouton", "id" : "misc_subst_file_create", "name" : "misc_subst_file_create", "value" : pmbDojo.messages.getMessage('misc', 'misc_subst_file_create')}, this.containerNode);
						domConstruct.place(this.createButton, this.containerNode);
						this.own(on(dom.byId('misc_subst_file_create'), 'click', lang.hitch(this, this.createFile)));
					}
				}
				return true;
			}
			return false;
		},
		loadContent: function(){
			if(this.path && this.filename){
				if(!this.textareaNode) {
					this.textareaNode = domConstruct.create("textarea", {"id" : "misc_subst_file_content", "style" : {"width":"100%", "height" : "30px", "text-align" : "center"}}, this.containerNode);
					pmbDojo.aceManager.initEditor('misc_subst_file_content');
				}
				request('./ajax.php?module=admin&categ=misc&sub=file&action=get_contents&path='+this.path+'&filename='+this.filename).then(lang.hitch(this, function(data) {
					var data = JSON.parse(data);
					if(data.is_writable_dir != this.isWritableDir) {
						this.destroyContent();
						this.textareaNode = domConstruct.create("textarea", {"id" : "misc_subst_file_content", "style" : {"width":"100%", "height" : "30px", "text-align" : "center"}}, this.containerNode);
						pmbDojo.aceManager.initEditor('misc_subst_file_content');
						this.isWritableDir = data.is_writable_dir;
					}
					var misc_subst_file_content = pmbDojo.aceManager.getEditor('misc_subst_file_content');
					misc_subst_file_content.setReadOnly(false);
					misc_subst_file_content.selectAll();
					misc_subst_file_content.removeLines();
					misc_subst_file_content.insert(data.contents);
					if(parseInt(this.isWritableDir)) {
						if(misc_subst_file_content.getCursorPosition()) {
							misc_subst_file_content.moveCursorTo(misc_subst_file_content.getCursorPosition().row-1);
						}
//						this.loadButtons();
					} else {
						misc_subst_file_content.setReadOnly(true);
					}
				}));
				return true;
			}
			return false;
		},
		destroyContent: function() {
			this.destroyDescendants();
			this.textareaNode = null;
			this.cancelButton = null;
			this.createButton = null;
			this.saveButton = null;
			this.deleteButton = null;
		},
		cancelFile: function() {
			this.loadContent();
		},
		createFile: function() {
			this.saveFile();
			this.hasSubstFile = true;
		},
		saveFile: function() {
			request.post('./ajax.php?module=admin&categ=misc&sub=file&action=save_contents&path='+this.path+'&filename='+this.filename, {
				data: {
					contents : pmbDojo.aceManager.getEditor('misc_subst_file_content').getValue()
				}
			}).then(lang.hitch(this, function(data) {
				var data = JSON.parse(data);
				if(data.status) {
					topic.publish("dGrowl", pmbDojo.messages.getMessage('misc', 'misc_subst_file_save_success') + '<b><br />' + this.filename + '</b>', {});
					this.destroyContent();
					this.loadContent();
					topic.publish("SubstFileContentUI","savedFile",data);
				} else {
					topic.publish("dGrowl", pmbDojo.messages.getMessage('misc', 'misc_subst_file_save_error') + '<b><br />' + this.filename + '</b>', {});
				}
			}));
		},
		deleteFile: function() {
			if(confirm(pmbDojo.messages.getMessage('misc', 'misc_subst_file_confirm_delete'))) {
				request('./ajax.php?module=admin&categ=misc&sub=file&action=delete&path='+this.path+'&filename='+this.filename).then(lang.hitch(this, function(data) {
					var data = JSON.parse(data);
					if(data.status) {
						topic.publish("dGrowl", pmbDojo.messages.getMessage('misc', 'misc_subst_file_delete_success') + '<b><br />' + this.filename + '</b>', {});
						this.hasSubstFile = false;
						this.destroyContent();
						this.loadContent();
						topic.publish("SubstFileContentUI","deletedFile",data);
					} else {
						topic.publish("dGrowl", pmbDojo.messages.getMessage('misc', 'misc_subst_file_delete_error') + '<b><br />' + this.filename + '</b>', {});
					}
				}));
			}
		},
	});
});

