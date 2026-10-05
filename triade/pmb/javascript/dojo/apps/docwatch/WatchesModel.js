// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: WatchesModel.js,v 1.1 2015/02/17 10:55:09 arenou Exp $


define(["dojo/_base/declare", "dijit/tree/ObjectStoreModel", "dojo/request/xhr", "dojo/_base/lang"], function(declare,ObjectStoreModel, xhr, lang){

	  return declare([ObjectStoreModel], {
		  labelAttr : "title"
	  });
});