// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchOntoController.js,v 1.2 2022/12/06 17:53:47 arenou Exp $

define(['dojo/_base/declare',
        'dijit/layout/ContentPane',
        'dojo/store/Memory',
        'dojo/_base/lang',
        'apps/search/SearchFieldsTree',
        'dojo/query!css3',
        'dojo/dom-construct',
        'apps/search/SearchDnd',
        'dojo/dom-class',
        'dojo/dom-attr',
        'dijit/tree/ObjectStoreModel',
        'dojo/dom-style',
        'dojo/on',
        'dojox/widget/Standby',
        'dojo/request',
        'dojo/dom-form',
        'dojo/parser',
        'apps/search/SearchController',
], function(declare, ContentPane, Memory, lang, SearchFieldsTree, query, domConstruct, SearchDnd, domClass, domAttr, ObjectStoreModel, domStyle, on, Standby, request, domForm, parser, SearchController) {
	return declare([SearchController], {
		contentTree: null,
		contentForm: null,
		store: null,
		searchFieldsList: null,
		widgets: [],
		uniqueIdentifier: '',
		module: null,
		ontology_id: null,
		constructor: function(uniqueIdentifier, module, ontology_id) {	
			if(ontology_id>0){
				this.ontology_id =ontology_id;
			}else{
				this.ontology_id = /ontology_id=(\d+)&?/g.exec(window.location)[1];
			}
		},
		
		getFormInfos: function() {
			var stand = new Standby({target: this.contentForm.get('id'), imageText: 'Chargement...', image: pmbDojo.images.getImage('patience.gif')});
			document.body.appendChild(stand.domNode);
			stand.startup();
			stand.show();
			enable_operators();
			form = query('form[name="search_form"]')[0];
			request.post("ajax.php?module="+this.module+"&ontology_id="+this.ontology_id+"&categ=extended_search&sub=get_already_selected_fields",{
				data : JSON.parse(domForm.toJson(form))
			}).then(lang.hitch(this, function(data) {
				for (var i = 0; i < this.widgets.length; i++) {
					this.widgets[i].destroy();
				}
				var table_container = query('form[name="search_form"] table')[0].parentNode;
				domConstruct.place(data, table_container, 'only');
				this.widgets = parser.parse(table_container);
				query('script', table_container).forEach(function(node) {
					domConstruct.create('script', {
						innerHTML: node.innerHTML,
						type: 'text/javascript'
					}, node, 'replace');
				});
				this.updateForm(form);
				ajax_parse_dom();
				stand.hide();
			}));
		},		
	});
});