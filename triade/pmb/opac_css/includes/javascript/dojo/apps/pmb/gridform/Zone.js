// +-------------------------------------------------+
// é 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Zone.js,v 1.2 2017/11/30 10:53:34 dgoron Exp $


define([
        'dojo/_base/declare', 
        'dojo/_base/lang', 
        'dojo/topic', 
        'dojo/dom-attr', 
        'dojo/dom', 
        'dojo/dom-construct', 
        'dojo/dom-style', 
        'dojo/query', 
        'apps/pmb/gridform/Element', 
        'dijit/registry'], 
        function(declare, lang, topic, domAttr, dom, domConstruct, domStyle, query, Element, registry){
	  return declare(null, {
		  isExpandable:null,
		  showLabel:null,
		  label:null,
		  nodeId:null,
		  domNode: null,
		  elements:null,
		  visible:null,
		  parentNode: null,
		  labelNode: null,
		  parent:null,
		  context: null,
		  constructor:function(params, nodeId, parent, context){
			  this.context = context;
			  this.isExpandable = (params.isExpandable)?params.isExpandable:false;
			  this.showLabel = (params.showLabel)?params.showLabel:false;
			  this.label = params.label;
			  this.nodeId = nodeId;
			  this.visible = (params.visible ? params.visible : true);
			  this.parent = parent;
			  this.elements = new Array();
			  return this;
		  },
		  createNodes: function(){
			  if(this.isExpandable) {
				  this.parentNode = domConstruct.create('div', {id:this.nodeId+'Parent', class:'parent'}, query('#zone-container', this.context)[0], 'last');
				  this.labelNode = domConstruct.create('h3', {innerHTML:this.label, style:{'display':'inline'}}, this.parentNode, 'last');
				  domConstruct.create('img', {
					  src:pmbDojo.images.getImage('minus.gif'),
					  class:'img_plus',
					  align:'bottom',
					  name:'imEx',
					  id:this.nodeId+'Img',
					  title:'titre',
					  border:'0',
					  onClick:'expandBase("'+this.nodeId+'", true); return false;'
					  }, this.labelNode , 'before');
				  this.domNode = domConstruct.create('div', {id:this.nodeId+'Child', label: this.label, class:'child', style:'border: 1px solid black; min-height:100px; '}, query('#zone-container', this.context)[0], 'last');
			  } else {
				  if(this.showLabel){
					  this.parentNode = domConstruct.create('div', {id:this.nodeId+'Parent', class:'parent'}, query('#zone-container', this.context)[0], 'last');
					  this.labelNode  = domConstruct.create('h3', {innerHTML:this.label}, this.parentNode, 'last');
				  }else{
					  this.parentNode = domConstruct.create('div', {id:this.nodeId+'Parent', class:'parent', innerHTML:'&nbsp;'}, query('#zone-container', this.context)[0], 'last');
				  }
				  this.domNode = domConstruct.create('div', {id:this.nodeId+'Child', label: this.label, style:'border: 1px solid black; min-height:100px; '}, query('#zone-container', this.context)[0], 'last');
			  }
			  domAttr.set(this.domNode,'etirable', 'yes');
			  if(this.visible) {
				  domStyle.set(this.nodeId+'Parent', 'display', 'block');
				  domStyle.set(this.nodeId+'Child', 'display', 'block');
			  } else {
				  domStyle.set(this.nodeId+'Parent', 'display', 'none');
				  domStyle.set(this.nodeId+'Child', 'display', 'none');
			  }
		  },
		  addField: function(fieldNode, visible, disabled){
			  //console.log('addfield once')
			  var elt = new Element(this,fieldNode);
			  if(visible){
				  elt.setVisible(true);
			  } else {
				  elt.setVisible(false);
				  if(disabled){
					  elt.setDisabled(true);
				  }
			  }
			  this.elements.push(elt);
			  return elt;
		  },
		  removeField: function(id){
			  for(var i=0 ; i<this.elements.length ; i++){
				  if(this.elements[i].nodeId == id){
					  var tempElt = this.elements[i].domNode;
					  this.elements.splice(i, 1);
					  return tempElt;
				  }
			  }
		  },
		  setVisible: function(visible){
			  if(visible) {
				  domStyle.set(this.nodeId+'Parent', 'display', 'block');
				  domStyle.set(this.nodeId+'Child', 'display', 'inline-block');
				  domStyle.set(this.nodeId+'Child', 'width', '100%');
				  this.visible = true;
			  } else {
				  domStyle.set(this.nodeId+'Parent', 'display', 'none');
				  domStyle.set(this.nodeId+'Child', 'display', 'none');
				  this.visible = false;
			  }
		  },
		  getElementFromId: function(id){
			  for(var i=0 ; i<this.elements.length ; i++){
				  if(this.elements[i].nodeId == id){
					  return this.elements[i];
				  }
			  }
			  return false;
		  },
		  makeInvisibleElement: function(params){
			  var elementToMakeInvisible = this.getElementFromId(params.id);
			  if(elementToMakeInvisible){
				  elementToMakeInvisible.setVisible(false);  
			  }
		  },
		  makeVisibleElement: function(params){
			  var elementToMakeVisible = this.getElementFromId(params.id);
			  if(elementToMakeVisible){
				  elementToMakeVisible.setVisible(true);
			  }
		  },
		  getJSONInformations: function(){
			  
			  var JSONElements = [];
			  if(this.elements.length){
				  for(var i=0 ; i<this.elements.length ; i++){
					  JSONElements.push(this.elements[i].getJSONInformations());
				  }
			  }
			  
			  var JSONInformations = new Object();
			  JSONInformations =  
			  {
				  "nodeId" : this.nodeId,
				  "label" : this.label,
				  "isExpandable" : this.isExpandable,
				  "showLabel" : this.showLabel,
				  "visible" : this.visible,
				  "elements": JSONElements
			  }			
			  return JSONInformations;
		  },
		  getHiddenElements: function(){
			var hiddenElements = new Array();
			for(var i=0 ; i<this.elements.length ; i++){
				if(!this.elements[i].visible){
					hiddenElements.push(this.elements[i]);	
				}
			}
			return hiddenElements;
		  },
		  getElements: function(){
			return this.elements;
		  },
		  enableElement: function(params){
			  var elementToEnable = this.getElementFromId(params.id);
			  if(elementToEnable){
				  elementToEnable.setDisabled(false);
			  }
		  },
		  disableElement: function(params){
			  var elementToDisable = this.getElementFromId(params.id);
			  if(elementToDisable){
				  elementToDisable.setDisabled(true);
			  }
		  },
	  });
});