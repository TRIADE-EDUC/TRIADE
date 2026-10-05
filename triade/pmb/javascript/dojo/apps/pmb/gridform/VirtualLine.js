// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: VirtualLine.js,v 1.4 2015/12/10 10:04:11 vtouchard Exp $


define(['dojo/_base/declare', 
        'dojo/_base/lang', 
        'dojo/topic', 
        'apps/pmb/gridform/DnDVirtualLine',
        'dojo/dom-construct',
        'dijit/registry'], 
        function(declare, lang, topic, DnDVirtualLine, domConstruct,registry){
	  return declare(null, {
		  domNode:null,
		  nodeId:null,
		  dnd:null,
		  zone:null,
		  constructor:function(zone, domNode){
			  //console.log('VirtualLine', arguments);
			  this.zone = zone;
			  this.domNode = domNode;
			  this.dnd = new DnDVirtualLine(
			   {
				   dndParams:{
					  copyOnly: false,
					  isSource: true,
					  singular:true,
					  accept:['movable','virtual'],
					  line: this
				   },
				   id: registry.getUniqueId("dijit._WidgetBase"),
				   'class': 'virtualLine'
			  },this.domNode);
			  this.dnd.startup();
		  },
		  handleEvents: function(evtClass, evtType, evtArgs){
			  switch(evtClass){
			  }
		  },
		  destroy:function(){
			  this.dnd.destroy();
		  }
	  });
});