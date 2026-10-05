// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Element.js,v 1.11 2021/11/16 15:48:32 dgoron Exp $


define(['dojo/_base/declare', 
        'dojo/_base/lang', 
        'dojo/topic', 
        'dojo/dom-attr', 
        'dojo/dom-construct', 
        'dojo/dom-style', 
        'apps/pmb/gridform/DnDElement', 
        'dojo/dom-class',
        'dijit/registry'], 
        function(declare, lang, topic, domAttr, domConstruct, domStyle, DnDElement, domClass, registry){
	  return declare(null, {
		  domNode:null,
		  nodeId:null,
		  nodeLabel:null,
		  visible:null,
		  className:null,
		  dnd:null,
		  zone:null,
		  isDisabled: false,
//		  containerZone:null,
		  constructor:function(zone, domNode){
			  //console.log('Element', arguments);
			  this.zone = zone;
			  this.domNode = domNode;
			  this.nodeId = this.domNode.getAttribute('id');
			  this.nodeLabel = this.domNode.getAttribute('title');
			  this.className = this.domNode.getAttribute('class');
			  domClass.add(this.domNode, 'dojoDndItem movable');
			  domStyle.set(this.domNode,'width', (domStyle.get(this.domNode, "width")-5)+'px');
			  this.dnd = new DnDElement({
				  dndParams:{
					  copyOnly: false,
					  singular:true,
					  isSource: true,
					  accept:['movable','virtual'],
					  element: this
				  },
//				  style:{'min-height':'40px'},
				  id:registry.getUniqueId("dijit._WidgetBase")
			  },this.domNode.parentNode);
			  this.dnd.startup();
			  this.visible = true;
		  },
		  handleEvents: function(evtClass, evtType, evtArgs){
			  switch(evtClass){
			  }
		  },
		  setVisible: function(visible){
			  if(visible) {
				  domStyle.set(this.domNode,'display','');
//				  this.zone.parent.enableNodes(this.domNode);
				  this.setDisabled(false);
				  this.visible = true;
			  } else {
				  domStyle.set(this.domNode,'display','none');
//				  this.zone.parent.disableNodes(this.domNode);
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
			  //console.log('instance class=',this.className, '' this.domNode.className);
			  this.domNode.className = this.domNode.className.replace(this.className, newClass);
			  this.className = newClass;
		  },
		  switchWidth: function(nb_columns) {
			  nb_columns = parseInt(nb_columns);
			  if(nb_columns) {
				  domStyle.set(this.domNode, 'width', (100/nb_columns)+'%');
			  }
		  },
		  changeBehavior: function(oldDnd, newContainerZone){
//			  this.containerZone = newContainerZone;
//			  domAttr.remove(this.containerZone, 'virtual');
//			  domAttr.remove(this.containerZone, 'style'); 
//			  domClass.remove(this.containerZone, 'dojoDndItem');
			  
			  domAttr.remove(this.domNode.parentNode, 'virtual');
			  domAttr.remove(this.domNode.parentNode, 'style'); 
			  domClass.remove(this.domNode.parentNode, 'dojoDndItem');
			  /**
			   * TODO: destroywidget preserve dom remove id instanciate new widget
			   */
			  this.dnd = null;
			  this.dnd = new DnDElement({
				  copyOnly: false,
				  isSource: true,
				  accept:['movable','virtual'],
				  element: this
			  },this.domNode.parentNode);
			  this.dnd.startup();
		  },
		  resize:function(){
			  this.dnd.resize();
		  },
		  scrollToElementParent: function(){
			  this.domNode.parentNode.scrollIntoView();
		  },
		  destroy:function(){
			  this.dnd.destroy();
			  for(var key in this){
				  this[key] = null;
			  }
		  },
		  setDisabled: function(bool){
			  this.isDisabled = bool;
			  if(bool){
				  /**
				   * Set l'attribut correct sur l'�l�ment
				   * ->(qui sera pars� � la sauvegarde)
				   * ->(trait� par le buildGrid au chargement de la grille pour d�sactiver les �l�ments poss�dant cet attribut)
				   * ->(Supprime l'attribut si passage � false)
				   */
//				  domAttr.set(this.domNode, 'data-form-disabled', 'true');
				  this.zone.parent.disableNodes(this.domNode);
			  }else{
//				  domAttr.remove(this.domNode, 'data-form-disabled');
				  this.zone.parent.enableNodes(this.domNode);
			  }
		  }
	  });
});