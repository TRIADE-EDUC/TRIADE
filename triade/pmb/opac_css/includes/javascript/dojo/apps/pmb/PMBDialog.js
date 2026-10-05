// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PMBDialog.js,v 1.1 2017/09/05 08:37:29 vtouchard Exp $


define(["dojo/_base/declare", "dijit/Dialog", "dojo/_base/lang", "dojo/dom-class"], function(declare, Dialog, lang, domClass){

	  return declare([Dialog], {
		  show: function(){
			  this.inherited(arguments);
			  if (!domClass.contains(document.body, "dojoDialogOpened")){
				  domClass.add(document.body, "dojoDialogOpened");
			  }
		  },
		  hide: function(){
			  this.inherited(arguments);
			  if (domClass.contains(document.body, "dojoDialogOpened")){
				  domClass.remove(document.body, "dojoDialogOpened");
			  }
		  },
	  });
});