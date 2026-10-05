// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Element.js,v 1.2 2021/11/16 15:48:32 dgoron Exp $


define(['dojo/_base/declare', 
        'dojo/_base/lang', 
        'dojo/topic', 
        'dojo/dom-attr', 
        'dojo/dom-construct', 
        'dojo/dom-style', 
        'dojo/dom-class',
        'dijit/registry'], 
        function(declare, lang, topic, domAttr, domConstruct, domStyle, domClass, registry){
	  return declare(null, {
		  domNode:null,
		  nodeId:null,
		  nodeLabel:null,
		  visible:null,
		  className:null,
		  zone:null,
		  isDisabled: false,
		  constructor:function(zone, domNode){
			  this.zone = zone;
			  this.domNode = domNode;
			  this.nodeId = this.domNode.getAttribute('id');
			  this.nodeLabel = this.domNode.getAttribute('title');
			  this.className = this.domNode.getAttribute('class');
			  this.visible = true;
		  },
		  handleEvents: function(evtClass, evtType, evtArgs){
			  switch(evtClass){
			  }
		  },
		  setVisible: function(visible){
			  if(visible) {
				  domStyle.set(this.domNode,'display','');
				  this.setDisabled(false);
				  this.visible = true;
			  } else {
				  domStyle.set(this.domNode,'display','none');
				  this.visible = false;
			  }
			  this.scrollToElementParent();
			  this.resize();
		  },
		  getJSONInformations: function(){
			  var JSONInformations = new Object();
			  JSONInformations =
			  {
				  "nodeId" : this.nodeId,
				  "visible" : this.visible,
				  "className" : this.className,
				  "disabled" : this.isDisabled
			  }
			  return JSONInformations;
		  },
		  switchClass: function(newClass){
			  this.domNode.className = this.domNode.className.replace(this.className, newClass);
			  this.className = newClass;
		  },
		  switchWidth: function(nb_columns) {
			  nb_columns = parseInt(nb_columns);
			  if(nb_columns) {
				  domStyle.set(this.domNode, 'width', (100/nb_columns)+'%');
			  }
		  },
		  scrollToElementParent: function(){
			  this.domNode.parentNode.scrollIntoView();
		  },
		  setDisabled: function(bool){
			  this.isDisabled = bool;
			  if(bool){
				  this.zone.parent.disableNodes(this.domNode);
			  }else{
				  this.zone.parent.enableNodes(this.domNode);
			  }
		  }
	  });
});