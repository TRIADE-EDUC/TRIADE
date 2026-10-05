// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FilesModel.js,v 1.2 2018/11/28 14:21:53 dgoron Exp $


define(["dojo/_base/declare", "dijit/tree/ObjectStoreModel", "dojo/request/xhr", "dojo/_base/lang"], function(declare,ObjectStoreModel, xhr, lang){

	  return declare([ObjectStoreModel], {
		  labelAttr : "title",
		  mayHaveChildren: function(item) {
			  switch(item.type) {
				  case 'file':
					  if(!this.store.getChildren(item).length) {
						 return false; 
					  }
					  break;
				  case 'substFile':
					  return false;
					  break;
			  }
			  return true;
			}
	  });
});