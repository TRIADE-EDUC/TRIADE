// +-------------------------------------------------+
// é 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FileContentUI.js,v 1.5 2018/12/19 14:49:39 dgoron Exp $

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
		constructor: function(){

		},
		postCreate:function(){
			this.inherited(arguments);
			this.own(
				topic.subscribe('Tree', lang.hitch(this, this.handleEvents)),
				topic.subscribe('FilesUI', lang.hitch(this, this.handleEvents))
			);
		},
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case "fileTreeSelected":
					this.path = evtArgs.path;
					this.filename = evtArgs.filename;
					this.loadContent();
					break;
				case "substFileTreeSelected":
					this.path = evtArgs.path;
					this.filename = evtArgs.filename.replace('_subst.xml', '.xml');
					this.loadContent();
					break;
			}
		},
		loadContent: function(){
			if(this.path && this.filename){
				if(!this.textareaNode) {
					this.textareaNode = domConstruct.create("textarea", {"id" : "misc_file_content", "style" : {"width":"100%", "height" : "30px", "text-align" : "center"}}, this.containerNode);
					pmbDojo.aceManager.initEditor('misc_file_content');
				}
				request('./ajax.php?module=admin&categ=misc&sub=file&action=get_contents&path='+this.path+'&filename='+this.filename).then(lang.hitch(this, function(data) {
					var data = JSON.parse(data);
					var misc_file_content = pmbDojo.aceManager.getEditor('misc_file_content');
					misc_file_content.setReadOnly(false);
					misc_file_content.selectAll();
					misc_file_content.removeLines();
					misc_file_content.insert(data.contents);
					misc_file_content.setReadOnly(true);
					misc_file_content.focus();
				}));
				return true;
			}
			return true;
		},
	});
});

