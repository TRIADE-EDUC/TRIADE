// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormsTree.js,v 1.1 2017/01/20 09:54:51 tsamson Exp $


define(["dojo/_base/declare", 
        "dojo/topic", 
        "dojo/_base/lang", 
        "dojo/request/xhr", 
        "dojo/json", 
        "dijit/_WidgetBase"
        ], 
        function(declare, topic, lang, xhr, json, WidgetBase){
	return declare('FormsTree',[WidgetBase], {
		constructor: function(){
			
		},
		postCreate: function(){
			this.inherited(arguments);
			
		}
	});
});