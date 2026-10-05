// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormContainer.js,v 1.3 2020/05/18 15:12:39 tsamson Exp $


define(["dojo/_base/declare", 
        "dijit/layout/ContentPane", 
        "dojo/store/Memory", 
        "dijit/tree/ObjectStoreModel", 
        "dojo/_base/lang",
        "dijit/form/Button",
        "dojo/dom-construct",
        "dojo/request/xhr",
        "dojo/_base/lang",
        "dojo/topic",
        "dojo/dom-form",
        "dojo/query",
        "dijit/registry",
        "dojox/widget/Standby"], 
        function(declare, ContentPane, Memory, ObjectStoreModel, lang, Button, domConstruct, xhr, lang, topic, domForm, query, registry, Standby){
	return declare([ContentPane], {
		standby: null,
		constructor: function(){
			this.set('executeScripts', true);
			this.own(	topic.subscribe('EntityTree', lang.hitch(this, this.handleEvents)),
						topic.subscribe('formButton', lang.hitch(this, this.handleEvents)),
						topic.subscribe('TreeContainer', lang.hitch(this, this.handleEvents)),						
						topic.subscribe('EntityForm', lang.hitch(this, this.handleEvents))						
			);
		},
		
		postCreate:function(){
			this.inherited(arguments);		
			this.standby = new Standby({target:this.containerNode.id});
			document.body.appendChild(this.standby.domNode);
			this.standby.startup();
		},
		
		clearContentPane: function(){
			this.set('content', '');
		},

		loadContent:function(data){ //Called by the xhr promise (then)
			var widgets = query('[widgetid]', this.domNode);
			widgets.forEach(function(widget){
				var widget = registry.byId(widget.getAttribute('id'));
				if(widget){
					widget.destroy();
				}
			});
			this.set("content", data);
			preLoadScripts(this.domNode);
			topic.publish('FormContainer', 'formLoaded');
		},
		showPatience:function(){
			this.standby.show();
		},
		hidePatience:function(){
			this.standby.hide();
		},
	});
});