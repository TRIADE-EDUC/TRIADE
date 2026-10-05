// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbEventsHandler.js,v 1.5 2023/03/10 15:19:19 qvarin Exp $


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

			let eventTypeParsed = {};
			if (node.hasAttribute('data-pmb-evt-parsed')) {
				eventTypeParsed = JSON.parse(node.getAttribute('data-pmb-evt-parsed')) || {};
			}

			let data_pmb_evt = JSON.parse(domAttr.get(node, 'data-pmb-evt'));
			if (eventTypeParsed[data_pmb_evt.type]) {
				return false;
			}

			if (object.className ==  data_pmb_evt.class) {
				if(typeof object[data_pmb_evt.method] == "function") {
					on(node, data_pmb_evt.type, lang.hitch(object, object[data_pmb_evt.method], data_pmb_evt.parameters));
					eventTypeParsed[data_pmb_evt.type] = true;
					node.setAttribute('data-pmb-evt-parsed', JSON.stringify(eventTypeParsed));
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
