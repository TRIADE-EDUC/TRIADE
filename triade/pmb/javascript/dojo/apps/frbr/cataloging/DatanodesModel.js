// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DatanodesModel.js,v 1.1 2018/01/17 15:01:13 dgoron Exp $


define(["dojo/_base/declare", "dijit/tree/ObjectStoreModel", "dojo/request/xhr", "dojo/_base/lang"], function(declare,ObjectStoreModel, xhr, lang){

	  return declare([ObjectStoreModel], {
		  labelAttr : "title"
	  });
});