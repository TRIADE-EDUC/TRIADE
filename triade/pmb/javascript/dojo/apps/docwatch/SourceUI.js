// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SourceUI.js,v 1.19 2019/03/13 14:48:22 dgoron Exp $


define(["dojo/_base/declare", "dijit/layout/ContentPane", "dojo/dom-construct", "dojo/dom", "dojo/on", "dojo/topic","dojo/_base/lang","dijit/registry","dijit/form/Button","dijit/form/CheckBox","dijit/form/ValidationTextBox"], function(declare, ContentPane, domConstruct, dom, on, topic, lang, registry, Button, CheckBox, ValidationTextBox){
	
	return declare([ContentPane], {
		sourceId:0,
		watchId:0,
		className:0,
		sourceTitle:null,
		postCreate:function(){
			this.own(
				topic.subscribe("sourcesListUI", lang.hitch(this,this.handleEvents)),
				topic.subscribe("itemsListUI", lang.hitch(this,this.handleEvents)),
				topic.subscribe("sourceUI", lang.hitch(this,this.handleEvents)),
				topic.subscribe("sourcesStore", lang.hitch(this,this.handleEvents)),
				topic.subscribe("watchStore", lang.hitch(this, this.handleEvents))
			);
		},		
		handleEvents: function(evtType,evtArgs){
			//console.log('sourceUI', evtType, evtArgs);
			switch(evtType){
				case "sourcesListRefreshed":
					this.sourceId=evtArgs.source.id;
					this.showForm(evtArgs);
					break;
				case "addSource":
					this.destroyDescendants();
					this.sourceId=0;
					this.className = evtArgs.className;
					this.watchId = evtArgs.watchId;
					this.showForm(evtArgs);
					break;	
				case "sourceSelected":
					if(this.sourceId!=evtArgs.id){
						this.sourceId=evtArgs.id;
						this.watchId = evtArgs.watchId;
						this.className = evtArgs.className;
						this.showForm(evtArgs);
					}
					break;
				case "itemsListRefreshed":
					this.display_raz();
					break;			
				case "sourceDeleted":
					this.display_raz();
					break;
				case "showSourcesList":
					this.show();
					break;
				case "showItemsList":
					this.hide();
					break;
				case "displayRaz":
					this.display_raz();
					break;
				case "watchDeleted":
					this.display_raz();
					break;
			}			
		},		
		buildRendering: function(){
			this.inherited(arguments);
		},
		destroy: function(){
			this.inherited(arguments);
		},
		
		showForm: function(data){	
			this.destroyDescendants();
			if(this.sourceId){
				this.href = "./ajax.php?module=dsi&categ=docwatch&sub=sources&action=get_form&id="+this.sourceId;
			}else{
				this.href = "./ajax.php?module=dsi&categ=docwatch&sub=sources&action=get_form&class="+data.className;
			}
			this.set("onDownloadEnd", function(){
			    var scripts = this.containerNode.getElementsByTagName("script");
				for(var i=0; i<scripts.length; i++) {
					if (window.execScript)
						window.execScript(scripts[i].text.replace('<!--',''));
					else
						window.eval(scripts[i].text);
				}
				this.sourceTitle = dom.byId("docwatch_datasource_title").value;
			});
			this.refresh();
		},			
		saveForm: function(){	
			var checkBoxInteresting=0;
			if(registry.byId("checkBoxInteresting").get("checked"))checkBoxInteresting=1;
			
			var data={
				source : {
					id : this.sourceId,
				 	title: dom.byId("inputTitle").value,		
				 	default_interesting: checkBoxInteresting				 	 	
				}
			};			
			if(!data.source.title){
				topic.publish('sourceUI',"sourceUIError",{error:{name:"saveFormNoTitle",msg:this.getMsg("dsi_js_source_save_form_no_title")}});
				return;
			}
			topic.publish('sourceUI',"sourceSave",data);
		},			
		sourceDelete: function(){	
			topic.publish('sourceUI',"sourceDelete",{sourceId:this.sourceId});
		},			
		display_raz: function(){		
			this.destroyDescendants();
			this.sourceId = 0;
		},
		getMsg: function(key){			 
			return pmbDojo.messages.getMessage("dsi",key);
		},
		show:function(){
			if(this.domNode.style.display == "none"){
				this.domNode.style.display = "block";
			}
		},
		hide:function(){
			if(this.domNode.style.display != "none"){
				this.domNode.style.display = "none";
			}
		}
	});
});