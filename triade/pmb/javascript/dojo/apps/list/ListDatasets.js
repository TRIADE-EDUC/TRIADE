// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ListDatasets.js,v 1.6 2019/11/27 12:58:50 dgoron Exp $

define([
        "dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/request",
        "dojo/query",
        "dojo/on",
        "dojo/dom-attr",
        "dojo/dom",
        "dojo/dom-style",
        "dojo/request/xhr",
        "dojo/ready"
], function(declare, lang, request, query, on, domAttr, dom, domStyle, xhr, ready){
	return declare(null, {
		dom_node_id:null,
		objects_type:null,
		which:null,
		controller_url_base:null,
		constructor: function(dom_node_id, objects_type, which, controller_url_base) {
			this.dom_node_id = dom_node_id;
			this.objects_type = objects_type;
			this.which = which;
			this.controller_url_base = controller_url_base;
			this.addEvents();
		},
		addEventOnDataset: function(node) {
			var id = domAttr.get(dom.byId(node), 'data-dataset-id');
			var action = domAttr.get(dom.byId(node), 'data-dataset-action');
			switch(action) {
				case 'apply':
					on(node, 'click', lang.hitch(this, this.applyDataset, id));
					break;
				case 'edit':
					on(node, 'click', lang.hitch(this, this.editDataset, id));
					break;
				case 'delete':
					on(node, 'click', lang.hitch(this, this.deleteDataset, id));
					break;
			}
		},
		addEvents: function() {
			on(dom.byId(this.objects_type+'_datasets_'+this.which+'_img'), 'click', lang.hitch(this, this.contentShow));
			
			var nodes = document.querySelectorAll("*[data-dataset-action]");
			if(nodes.length) {
				for(var i=0; i<nodes.length; i++) {
					this.addEventOnDataset(nodes[i]);
				}
			}
		},
		contentShow: function() {
			var domNode = dom.byId(this.objects_type+'_datasets_'+this.which+'_content');
			if(domStyle.get(domNode, 'display') == 'none') {
				domStyle.set(domNode, 'display', 'block');
				domAttr.set(dom.byId(this.objects_type+'_datasets_'+this.which+'_img'), 'src', pmbDojo.images.getImage('minus.gif'));
			} else {
				domStyle.set(domNode, 'display', 'none');
				domAttr.set(dom.byId(this.objects_type+'_datasets_'+this.which+'_img'), 'src', pmbDojo.images.getImage('plus.gif'));
			}
		},
		applyDataset: function(id) {
			window.location = this.controller_url_base+'&action=dataset_apply&id='+id;
		},
		editDataset: function(id) {
			window.location = this.controller_url_base+'&action=dataset_edit&id='+id;
		},
		deleteDataset: function(id) {
			if(confirm(pmbDojo.messages.getMessage('list', 'list_delete_confirm'))) {
				xhr('./ajax.php?module=ajax&categ=list&action=delete&id='+id+'&objects_type='+this.objects_type, {
					sync: false,
					handleAs: 'JSON',
				}).then(lang.hitch(this, 
						function(response){
							dom.byId(this.objects_type+'_dataset_'+id).innerHTML = ''; 
						})
				);
			}
		},
	});
});