// +-------------------------------------------------+
// é 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Zone.js,v 1.16 2021/02/10 14:01:44 gneveu Exp $


define([
        'dojo/_base/declare', 
        'dojo/_base/lang', 
        'dojo/topic', 
        'dojo/dom-attr', 
        'dojo/dom', 
        'dojo/dom-construct', 
        'dojo/dom-style', 
        'apps/pmb/gridform/ContextMenu', 
        'dojo/query', 
        'apps/pmb/gridform/Element', 
        'apps/pmb/gridform/DnDZone',
        'apps/pmb/gridform/VirtualLine',
        'dijit/registry',
        'apps/pmb/gridform/DnDElement',], 
        function(declare, lang, topic, domAttr, dom, domConstruct, domStyle, ContextMenu, query, Element, DnDZone, VirtualLine, registry, DnDElement){
	  return declare(null, {
		  isExpandable:null,
		  showLabel:null,
		  label:null,
		  nodeId:null,
		  domNode: null,
		  elements:null,
		  contextMenu: null,
		  visible:null,
		  parentNode: null,
		  labelNode: null,
		  parent:null,
//		  nodesCreated:null,
		  dnd:null,
		  virtualLines: null,
		  constructor:function(params, nodeId, parent){
			  this.isExpandable = (params.isExpandable)?params.isExpandable:false;
			  this.showLabel = (params.showLabel)?params.showLabel:false;
			  this.label = params.label;
			  this.nodeId = nodeId;
			  this.visible = (params.visible ? params.visible : true);
			  this.parent = parent;
			  this.elements = new Array();
			  topic.subscribe('ContextMenu', lang.hitch(this, this.handleEvents, 'ContextMenu'));
			  //console.log(this, 'zoneCreated');
			  this.virtualLines = new Array();
			  return this;
		  },
		  handleEvents: function(evtClass, evtType, evtArgs){
			  switch(evtClass){
			  case 'ContextMenu':
				  switch(evtType){
				  	case 'goFirstElement':
			  			this.goFirstElement(evtArgs);
			  			break;
				  	case 'upElement':
			  			this.upElement(evtArgs);
			  			break;
			  		case 'downElement':
			  			this.downElement(evtArgs);
			  			break;
			  		case 'goLastElement':
			  			this.goLastElement(evtArgs);
			  			break;
			  		case 'makeInvisibleElement':
			  			this.makeInvisibleElement(evtArgs);
			  			break;
			  		case 'makeVisibleElement':
			  			this.makeVisibleElement(evtArgs);
			  			break;
			  		case 'enableElement':
			  			this.enableElement(evtArgs);
			  			break;
			  		case 'disableElement':
			  			this.disableElement(evtArgs);
			  			break;
				  }
				  break;
			  case 'FormEdit':
				  switch(evtType){
				  
				  }
				  break;
			  }
		  },
		  createNodes: function(){
			  if(this.isExpandable) {
				  this.parentNode = domConstruct.create('div', {id:this.nodeId+'Parent', class:'parent'}, dom.byId('zone-container'), 'last');
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
				  this.domNode = domConstruct.create('div', {id:this.nodeId+'Child', label: this.label, class:'child', style:'border: 1px solid black; min-height:100px; '}, dom.byId('zone-container'), 'last');
			  } else {
				  if(this.showLabel){
					  this.parentNode = domConstruct.create('div', {id:this.nodeId+'Parent', class:'parent'}, dom.byId('zone-container'), 'last');
					  this.labelNode  = domConstruct.create('h3', {innerHTML:this.label}, this.parentNode, 'last');
				  }else{
					  this.parentNode = domConstruct.create('div', {id:this.nodeId+'Parent', class:'parent', innerHTML:'&nbsp;'}, dom.byId('zone-container'), 'last');
				  }
				  this.domNode = domConstruct.create('div', {id:this.nodeId+'Child', label: this.label, style:'border: 1px solid black; min-height:100px; '}, dom.byId('zone-container'), 'last');
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
		  setDomNode: function(domNode) {
			  this.domNode = domNode;
		  },
		  addConnectStyle: function(){
			  this.parentNode = dom.byId(this.nodeId+'Parent');
			  this.labelNode = query('h3', this.parentNode)[0];				 
			  this.domNode = dom.byId(this.nodeId+'Child');
			  domStyle.set(this.nodeId+'Child', 'border', '1px solid black');
			  domStyle.set(this.nodeId+'Child', 'minHeight','100px');
			  this.dnd = new DnDZone(this.domNode, {
				  copyOnly: false,
				  isSource: true,
				  zone: this,
				  accept:['movable','virtual']
			  });
			  this.contextMenu = new ContextMenu({
				  	zone:this,
			        targetNodeIds: [this.nodeId+'Child']
			    });
			  this.contextMenu.startup();
			  for(var i=0 ; i<this.elements.length ; i++){
				  this.elements[i].resize();
			  }
		  },
		  edit: function(params){
			  this.label = params.label;
			  this.isExpandable = params.isExpandable;
			  this.showLabel = params.showLabel;
			  domConstruct.empty(this.parentNode);
			  if(this.isExpandable) {
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
			  } else {
				  if(this.showLabel){
					  this.labelNode  = domConstruct.create('h3', {innerHTML:this.label}, this.parentNode, 'last');
				  }
			  }
			  domAttr.set(this.domNode, 'label', this.label);
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
		  destroy: function(){
			  if(!this.elements.length){
				  domConstruct.destroy(this.nodeId+'Parent');
				  domConstruct.destroy(this.nodeId+'Child');
				  this.contextMenu.destroyDescendants();
				  this.contextMenu.destroy();
				  return true;
			  }
			  return false;
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
		  goFirstElement: function(params){
			  var elementToGoFirst = this.getElementFromId(params.id);
			  if(elementToGoFirst){
				var newContainer = domConstruct.create('div', {class: 'container-div row'}, this.domNode, 'first');
				var dndElt = new DnDElement({
					dndParams:{
					 	copyOnly: false,
				  		singular:true,
				  		isSource: true,
				  		accept:['movable','virtual'],
				  		element: elementToGoFirst
				 	},
					id:registry.getUniqueId("dijit._WidgetBase")
				},newContainer);
				dndElt.startup();
				dndElt.dnd.onDrop(elementToGoFirst.dnd.dnd, [elementToGoFirst.domNode], false);
			  }
		  },
		  upElement: function(params){
			  var elementToUp = this.getElementFromId(params.id);
			  var indexElement = this.elements.indexOf(elementToUp);
			  if((elementToUp) && (indexElement > 0 || elementToUp.dnd.domNode.children.length > 1)){
				  if(indexElement == 0 && query('div[movable="yes"]',elementToUp.dnd.domNode).length > 1){ //élément placé en premier mais contenu  
					  var elementUp = elementToUp;
				  }else{
					  var elementUp = this.elements[indexElement-1];  
				  }
				var newContainer = domConstruct.create('div', {class: 'container-div row'}, elementUp.dnd.domNode, 'before');
				var dndElt = new DnDElement({
					dndParams:{
					 	copyOnly: false,
				  		singular:true,
				  		isSource: true,
				  		accept:['movable','virtual'],
				  		element: elementToUp
				 	},
					id:registry.getUniqueId("dijit._WidgetBase")
				},newContainer);
				dndElt.startup();
				dndElt.dnd.onDrop(elementToUp.dnd.dnd, [elementToUp.domNode], false);
			  }
		  },
		  downElement: function(params){
			  var elementToDown = this.getElementFromId(params.id);
			  var indexElement = this.elements.indexOf(elementToDown);
			  if((elementToDown) && (indexElement < this.elements.length -1 || elementToDown.dnd.domNode.children.length > 1))
			  {
				  if(elementToDown.dnd.domNode.children.length == 1){ //On passe l'élément en dessous de l'élément suivant
					  var newContainer = domConstruct.create('div', {class: 'container-div row'}, this.elements[(indexElement+1)].dnd.domNode, 'after');
				  }else{ //On passe l'élément dans une interligne crée à la volée en dessous du container de l'élément courant 
					  var newContainer = domConstruct.create('div', {class: 'container-div row'}, elementToDown.dnd.domNode, 'after');
				  }
				var dndElt = new DnDElement({
					dndParams:{
					 	copyOnly: false,
				  		singular:true,
				  		isSource: true,
				  		accept:['movable','virtual'],
				  		element: elementToDown
				 	},
					id:registry.getUniqueId("dijit._WidgetBase")
				},newContainer);
				dndElt.startup();
				dndElt.dnd.onDrop(elementToDown.dnd.dnd, [elementToDown.domNode], false);
			  }
		  },
		  goLastElement: function(params){
			  var elementToGoLast = this.getElementFromId(params.id);
			  var indexElement = this.elements.indexOf(elementToGoLast);
			  if(elementToGoLast){
				var newContainer = domConstruct.create('div', {class: 'container-div row'}, this.domNode, 'last');
				var dndElt = new DnDElement({
					dndParams:{
					 	copyOnly: false,
				  		singular:true,
				  		isSource: true,
				  		accept:['movable','virtual'],
				  		element: elementToGoLast
				 	},
					id:registry.getUniqueId("dijit._WidgetBase")
				},newContainer);
				dndElt.startup();
				dndElt.dnd.onDrop(elementToGoLast.dnd.dnd, [elementToGoLast.domNode], false);
			  }
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
			  var computedElements = []; 
			  if(this.elements.length){
				  for(var i=0 ; i<this.elements.length ; i++){
					if(computedElements.indexOf(this.elements[i].nodeId) != -1){
						continue;
					}
					computedElements.push(this.elements[i].nodeId);
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
		  createVirtualLines: function(){
			  var containerDiv = query('.container-div', this.domNode);
			  if(containerDiv && containerDiv.length){
				  this.virtualLines.push(new VirtualLine(this, domConstruct.create('div',{class:'row container-div dojoDndItem', virtual:'yes', style:'min-height:5px'}, containerDiv[0], 'before')));
				  for(var i=0 ; i<containerDiv.length ; i++){
					  this.virtualLines.push(new VirtualLine(this, domConstruct.create('div',{class:'row container-div dojoDndItem', virtual:'yes', style:'min-height:5px'}, containerDiv[i], 'after')));
				  }
			  } else {
				  this.virtualLines.push(new VirtualLine(this, domConstruct.create('div',{class:'row container-div dojoDndItem',virtual:'yes', style:'height:60px'}, this.domNode, 'first')));
			  }
			  // var scrollY = window.scrollY;
			  /**
			   * 5 étant la valeur de la min height définie juste au dessus (a remplacer au besoin ou a calculer via une fct dojo)
			   */
			  //window.scrollTo(0, (scrollY+(5*this.virtualLines.length)));
			  
		  },
		  purgeVirtualLines: function(){
			  var scrollY = window.scrollY;
			  scrollY = scrollY - (5*(this.virtualLines.length));
			  for(var i=0 ; i<this.virtualLines.length; i++){
				  this.virtualLines[i].destroy();
				  this.virtualLines[i] = null;
			  }
			  this.virtualLines = new Array();
			  window.scrollTo(0, scrollY);
		  },
		  refreshZoneLines: function(){
			  for(var i=0 ; i<this.elements.length ; i++){
				  this.elements[i].dnd.resize();
			  }
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