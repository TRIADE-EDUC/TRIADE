// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PMBSelectorDialog.js,v 1.3 2020/07/09 09:27:00 dgoron Exp $


define(["dojo/_base/declare", 
        "dijit/Dialog", 
        "dojo/_base/lang", 
        "dojo/dom-class", 
        "apps/pmb/PMBMainDialog", 
        "dojo/topic"], function(declare, Dialog, lang, domClass, PMBMainDialog, topic){

	  return declare([Dialog, PMBMainDialog], {
		  postCreate: function(){
				this.inherited(arguments);  
				this.own(topic.subscribe('SelectorTab', lang.hitch(this, this.handleEvents)));
		  },
		  handleEvents: function(evtClass, evtType, evtArgs){
			switch(evtClass){
				case 'SelectorTab':
					switch(evtType){
						case 'closeCurrentTab':
							if(!evtArgs.what || (this.params && evtArgs.what == this.params.what)) {
								this.hide();
							}
							break;
					}
					break;
			}  
		  },
	  });
});