// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: WidgetSource.js,v 1.1 2015/12/10 10:04:11 vtouchard Exp $


define(['dojo/_base/declare', 
        'dojo/_base/lang', 
        'dojo/topic', 
        'dojo/dom-construct',
        'dijit/registry',
        'dojo/dom-style',
        'dojo/dom-attr',
        'dojo/query',
        'dijit/_WidgetBase'], 
        function(declare, lang, topic, domConstruct, registry, domStyle, domAttr, query, WidgetBase){
	  return declare(WidgetBase, {
		  resize:function(){
			  var maxHeight = 0;
			  var childs =  query('div[movable="yes"]', this.domNode);
			  for(var i=0 ; i<childs.length ; i++){
				  if(maxHeight < parseInt(domStyle.getComputedStyle(childs[i]).height)){
					  maxHeight = parseInt(domStyle.getComputedStyle(childs[i]).height) + (parseInt(dojo.getPadBorderExtents(childs[i]).h)/2);
				  }
			  }
			  domStyle.set(this.domNode, 'height', maxHeight+'px');
		  },
	  });
});