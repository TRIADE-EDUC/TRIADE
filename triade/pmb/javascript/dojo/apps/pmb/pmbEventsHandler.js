// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbEventsHandler.js,v 1.4 2018-04-18 09:12:34 tsamson Exp $


define(["dojo/_base/lang",
        "dojo/on",
        "dojo/query",
        "dojo/dom-attr",
        "dojo/dom-construct",
        ], 
		function(lang, on, query, domAttr, domConstruct){

	var pmbEventsHandler = {
		
		signals : [],
		
		initEvents: function(object, context) {
			if (!context) {
				context = document;
			}
			query('[data-pmb-evt]', context).forEach((node)=>{
				this.addEvent(node, object);
			});
		},
		
		addEvent: function(node, object) {
			var data_pmb_evt = JSON.parse(domAttr.get(node, 'data-pmb-evt'));
			if (object.className ==  data_pmb_evt.class) {
				if(typeof object[data_pmb_evt.method] == "function"){
					on(node, data_pmb_evt.type, lang.hitch(object, object[data_pmb_evt.method], data_pmb_evt.parameters));
				}
			}
		},
		
		formToAjax : function(context) {
			if (!context) {
				context = document;
			}
			query('form', context).forEach((form)=>{
				on(form, 'submit', (e)=>{
					e.preventDefault();
					return false;
				});
			});			
		},
	};

	return pmbEventsHandler;
});
