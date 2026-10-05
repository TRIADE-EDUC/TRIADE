// +-------------------------------------------------+
// é 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FileUI.js,v 1.8 2023/07/26 06:23:12 dgoron Exp $

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
        "dojo/query",
        "dojo/dom-style",
        "dijit/form/DropDownButton",
        "dijit/DropDownMenu",
        "dijit/MenuItem",
        "dijit/form/Select",
        "dojo/dom-attr",
        "dojo/io-query",
        "dojo/request/xhr",
        "dojo/dom-form",
        "apps/misc/files/FileDnd",
        "dojo/dom-class",
        "dojo/dom-style",
        "apps/misc/files/PopupPond"
        ], function(declare, ExpandoPane, ContentPane, domConstruct, dom, on, topic, lang, registry, Button, query, domStyle, DropDownButton, DropDownMenu, MenuItem, Select, domAttr, ioQuery, xhr, domForm, FileDnd, domClass, domStyle, PopupPond){
	
	return declare([ContentPane], {
		path:null,
		filename:null,
		popupPond:null,
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
					this.filename = evtArgs.filename;
					this.loadForm();
					break;
				case "substFileTreeSelected":
					this.path = evtArgs.path;
					this.filename = evtArgs.filename.replace('_subst.xml', '.xml');
					this.loadForm();
					break;
			}
		},
		loadForm: function(){
			if(this.path && this.filename){
				this.destroyDescendants();
				this.set('href', './ajax.php?module=admin&categ=misc&sub=file&action=get_form&path='+this.path+'&filename='+this.filename);
				this.set("onDownloadEnd", lang.hitch(this, function(){
				    this.addEvents();
				    this.initDnd();
				}));
				return true;
			}
			return true;
		},
		addEventOnCode: function(node) {
			var code = domAttr.get(dom.byId(node), 'data-file-code');
			var action = domAttr.get(dom.byId(node), 'data-file-action');
			if(domAttr.get(dom.byId(node), 'data-file-type')) {
				var type = domAttr.get(dom.byId(node), 'data-file-type'); 
			} else {
				var type = null;
			}
			switch(action) {
				case 'add_substitution':
					this.own(on(node, 'click', lang.hitch(this, this.addSubstitution, code, type)));
					break;
				case 'edit_pond':
					if(domAttr.get(dom.byId(node), 'data-file-label')) {
						var label = domAttr.get(dom.byId(node), 'data-file-label'); 
					} else {
						var label = null;
					}
					if(domAttr.get(dom.byId(node), 'data-file-pond')) {
						var pond = domAttr.get(dom.byId(node), 'data-file-pond'); 
					} else {
						var pond = null;
					}
					this.own(on(node, 'click', lang.hitch(this, this.editPopupPond, code, label, pond)));
					break;
			}
		},
		addEvents: function() {
			this.own(
					on(dom.byId('misc_file_cancel'), 'click', lang.hitch(this, this.cancelAction)),
					on(dom.byId('misc_file_save'), 'click', lang.hitch(this, this.saveAction))
			);
			if(dom.byId('misc_file_initialization')) {
				this.own(
						on(dom.byId('misc_file_initialization'), 'click', lang.hitch(this, this.initializationAction))
				);
			}
			var nodes = document.querySelectorAll("*[data-file-code]");
			if(nodes.length) {
				for(var i=0; i<nodes.length; i++) {
					this.addEventOnCode(nodes[i]);
				}
			}
		},
		initDnd: function() {
			var nodes = document.querySelectorAll("*[data-file-element]");
			if(nodes.length) {
				var dndList = new FileDnd(nodes[0].parentNode, {type: ['fileField'], fileController: this});
				for(var i=0; i<nodes.length; i++) {
					this.addDndOnCode(nodes[i], i);
				}
				dndList.sync();
			}
		},
		addDndOnCode: function(node, index) {
			domClass.add(node, 'dojoDndItem');
			domStyle.set(node, 'cursor', 'move');
			domClass.add(node, 'dojoDndHandle');
		},
		cancelAction: function() {
			this.loadForm();
		},
		saveAction: function() {
			//TODO : Form to JSON or object pour envoyer données POST et enregistrer en base
			xhr.post("./ajax.php?module=admin&categ=misc&sub=file&action=save&path="+this.path+"&filename="+this.filename,{
				 handleAs: "json",
				 method: 'post',
				 data: domForm.toObject('misc_file_form')
		  }).then(lang.hitch(this, this.saveCallback));
		},
		saveCallback: function(data) {
			if(data.status) {
				topic.publish("dGrowl", pmbDojo.messages.getMessage('misc', 'misc_file_save_success'), {});
				this.loadForm();
			} else {
				topic.publish("dGrowl", pmbDojo.messages.getMessage('misc', 'misc_file_save_error'), {});
			}
		},
		initializationAction: function() {
			xhr.post("./ajax.php?module=admin&categ=misc&sub=file&action=initialization&path="+this.path+"&filename="+this.filename,{
				 handleAs: "json",
				 method: 'get'
		  }).then(lang.hitch(this, this.initializationCallback));
		},
		initializationCallback: function(data) {
			if(data.status) {
				topic.publish("dGrowl", pmbDojo.messages.getMessage('misc', 'misc_file_initialization_success'), {});
				this.loadForm();
			} else {
				topic.publish("dGrowl", pmbDojo.messages.getMessage('misc', 'misc_file_initialization_error'), {});
			}
			
		},
		addSubstitution: function(code, type) {
			var misc_subst_file_content = pmbDojo.aceManager.getEditor('misc_subst_file_content');
			misc_subst_file_content.insert('<entry code="'+code+'"></entry>\r\n');
			if(misc_subst_file_content.getCursorPosition()) {
				misc_subst_file_content.moveCursorTo(misc_subst_file_content.getCursorPosition().row+1);
			}
		},
		editPopupPond: function(code, label, pond){
			this.popupPond = new PopupPond({code: code, label:label, pond:pond});
			this.popupPond.show();
		},
	});
});

