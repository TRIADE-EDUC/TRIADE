// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DnDZone.js,v 1.2 2015/10/27 11:28:57 vtouchard Exp $


define([
        'dojo/_base/declare', 
        'dojo/_base/lang', 
        'dojo/topic', 
        'dojo/dnd/Source'], 
        function(declare, lang, topic, dndSource){
	  return declare(dndSource, {
		  domNode:null,
		  constructor:function(){
			  
		  }, 
		  checkAcceptance:function(source, nodes){
			  return false;
		  },
		  
		  getItem: function(nodeId){
			  var item = {type:new Array('movable')};
			  return item;
		  },
		  onDndDrop: function(source,nodes,copy){
			  this.inherited(arguments);
			  var zone = (this.element)?this.element.zone: this.zone;
		  },
		  onDndStart: function(source,nodes,copy){
			  this.inherited(arguments);
			  var zone = (this.element)?this.element.zone: this.zone;
			  zone.createVirtualLines();
			  
		  },
		  onDndCancel: function(){
			  this.inherited(arguments);
			  setTimeout(lang.hitch(this, function(){
				  var zone = (this.element)?this.element.zone: this.zone;
				  zone.purgeVirtualLines();
			  }),100);
		  }
	  });
});