// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DnDVirtualLine.js,v 1.4 2015/12/10 10:04:11 vtouchard Exp $


define(['dojo/_base/declare', 
        'dojo/_base/lang', 
        'dojo/topic', 
        'dojo/dnd/Source',
        'dojo/query',
        'dojo/dom-construct',
        'dojo/dom-class',
        'dojo/_base/array',
        'apps/pmb/gridform/WidgetSource',
        'dojo/_base/lang',
        'apps/pmb/gridform/DnDElement',
        'dijit/registry'], 
        function(declare, lang, topic, dndSource, query, domConstruct, domClass, array, WidgetBase, lang, DnDElement, registry){
	  return declare(WidgetBase, {	
		  dnd : null,
		  constructor: function(params,srcNodeRef){
			  this.dndParams = params.dndParams;
		  },
		  getItem: function(nodeId){
			  var item = {type:new Array('movable','virtual')};
			  return item;
		  },
		  onDrop: function(source,nodes,copy){
			  var newContainer = domConstruct.create('div', {class: 'container-div row'}, this.node, 'after');
			  var droppedElt = this.line.zone.parent.getElementFromId(nodes[0].id);
			  var dndElt = new DnDElement({
				  dndParams:{
					  copyOnly: false,
					  singular:true,
					  isSource: true,
					  accept:['movable','virtual'],
					  element: droppedElt
				  },
				  id:registry.getUniqueId("dijit._WidgetBase")
			  },newContainer);
			  dndElt.startup();
			  dndElt.dnd.onDrop(source, nodes, copy);
		  },
		  copyState:function(){
			  return false;
		  },
		  postCreate:function(){
			  this.dnd = new dndSource(this.domNode,this.dndParams);
			  this.dnd.onDrop = lang.hitch(this.dnd, this.onDrop);
			  this.dnd.getItem = lang.hitch(this.dnd, this.getItem);
			  this.dnd.copyState = lang.hitch(this.dnd, this.copyState);
		  },
		  destroy:function(){
			  this.dnd.destroy();
			  this.inherited(arguments);
		  }

	  });
});