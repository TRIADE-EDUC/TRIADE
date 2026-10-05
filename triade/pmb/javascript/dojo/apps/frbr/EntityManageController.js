// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EntityManageController.js,v 1.5 2017/11/30 10:53:34 dgoron Exp $

define(['dojo/_base/declare',
        'dijit/layout/ContentPane',
        'dojo/store/Memory',
        'dojo/_base/lang',
        'apps/frbr/FieldsTree',
        'dojo/query!css3',
        'dojo/dom-construct',
        'apps/frbr/ManageDnd',
        'dojo/dom-class',
        'dojo/dom-attr',
        'dijit/tree/ObjectStoreModel',
        'dojo/dom-style',
        'dojo/on',
        'dojox/widget/Standby',
        'dojo/request',
        'dojo/dom-form',
        'dojo/parser'
], function(declare, ContentPane, Memory, lang, FieldsTree, query, domConstruct, ManageDnd, domClass, domAttr, ObjectStoreModel, domStyle, on, Standby, request, domForm, parser) {
	return declare(null, {
		id:null,
		elem: null,
		type:null,
		manage_id:null,
		domNodeId:null,
		contentTree: null,
		contentForm: null,
		store: null,
		fieldsList: null,
		widgets: [],
		
		constructor: function(params) {
			this.id = params.id;
			this.elem = params.elem;
			this.type = params.type;
			this.manage_id = params.manage_id;
			this.domNodeId = this.elem+'_'+this.type+'_'+this.manage_id;
			this.generateDom();
			this.parseSelector();
			this.buildTree();
			this.buildForm();
		},
		
		generateDom: function() {
			this.contentTree = new ContentPane({
				splitter: true,
				region: 'left',
				style: 'height:100%;width:250px;'
			}).placeAt(this.domNodeId+'_manage_dnd_container');
			this.contentForm = new ContentPane({
				id: this.domNodeId+'_manage_dnd_content_form',
				splitter: true,
				region: 'center',
				style: 'height:100%;'
			}).placeAt(this.domNodeId+'_manage_dnd_container');
		},
		
		parseSelector: function() {
			this.store = new Memory({data:[{id: 'root'}]});
			var children = dojo.byId(this.domNodeId+'_add_field').children;
			for (var i in children) {
				if ((children[i].nodeName == 'OPTGROUP') && (children[i].children.length)) {
					this.store.put({
						id: 'parent_' + i,
						label: children[i].label,
						parent: 'root'
					});
					for (var j in children[i].children) {
						if (children[i].children[j].nodeName == 'OPTION') {
							this.store.put({
								id: children[i].children[j].value,
								label: children[i].children[j].label,
								parent: 'parent_' + i,
								leaf: true
							});
						}
					}
				}
			}
			this.store.getChildren = function(object) {
				return lang.hitch(this.store, this.query({parent: object.id}));
			};
			domStyle.set(dojo.byId(this.domNodeId+'_add_field').parentNode, 'display', 'none');
		},
		
		buildTree: function() {
			// Un titre pour l'arbre
			domConstruct.place('<h3>' + this.getTreeTitle() + '</h3>', this.contentTree.id);
			
			// Expand/Collapse all
			domConstruct.place('<span id="'+this.domNodeId+'_manage_fields_tree_expandall" class="liLike"><img class="dijitTreeExpando dijitTreeExpandoClosed" data-dojo-attach-point="expandoNode" src="'+pmbDojo.images.getImage('expand_all.gif')+'"></span><span id="'+this.domNodeId+'_manage_fields_tree_collapseall" class="liLike"><img class="dijitTreeExpando dijitTreeExpandoOpened" data-dojo-attach-point="expandoNode" src="'+pmbDojo.images.getImage('collapse_all.gif')+'"></span><div class="row"></div>', this.contentTree.id);
			var model = new ObjectStoreModel({
				store: this.store,
				query: { id: 'root'},
				mayHaveChildren: function(item) {
					return !item.leaf;
				}
			});
			var tree = new FieldsTree({id: this.domNodeId+'_fields_tree', model: model, entityManageController: this});
			tree.placeAt(this.contentTree);
			on(dojo.byId(this.domNodeId+'_manage_fields_tree_expandall'), 'click', function() {tree.expandAll();});
			on(dojo.byId(this.domNodeId+'_manage_fields_tree_collapseall'), 'click', function() {tree.collapseAll();});
		},
		
		buildForm: function() {
			var form = query('form[name="'+this.domNodeId+'_manage_form"]')[0];
			domConstruct.place(form,this.contentForm.id);
			on(form, 'submit', lang.hitch(this, this.createJsonDataInput));
			this.updateForm(form);
		},
		
		updateForm: function(form) {
			this.fieldsList = query('table tbody tr', form);
			if (this.fieldsList.length) {
				if (domStyle.get(form, 'display') == 'none') {
					domStyle.set(form, 'display', 'block');
				}
				if (dojo.byId(this.domNodeId+"_manage_fields_no_selected_fields")) {
					domConstruct.destroy(this.domNodeId+"_manage_fields_no_selected_fields");
				}
				this.initDnd();
				this.updateDeleteButtons();
				ajax_parse_dom();
			} else {
				domStyle.set(form, 'display', 'none');
				domConstruct.place('<span class="saisie-contenu" id="'+this.domNodeId+'_manage_fields_no_selected_fields">' + pmbDojo.messages.getMessage('frbr', 'manage_fields_no_selected_fields') + '</span>',this.contentForm.id);
			}
		},
		
		getFormInfos: function() {
			var stand = new Standby({target: this.domNodeId+'_manage_dnd_content_form', imageText: 'Chargement...', image: pmbDojo.images.getImage('patience.gif')});
			document.body.appendChild(stand.domNode);
			stand.startup();
			stand.show();
			form = query('form[name="'+this.domNodeId+'_manage_form"]')[0];
			request.post(this.getRequestUrl(),{
				data : JSON.parse(domForm.toJson(form))
			}).then(lang.hitch(this, function(data) {
				for (var i = 0; i < this.widgets.length; i++) {
					this.widgets[i].destroy();
				}
				var table_container = query('form[name="'+this.domNodeId+'_manage_form"] table')[0].parentNode;
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
		
		initDnd: function() {
			if (this.fieldsList.length) {
				var dndForm = new ManageDnd(this.fieldsList[0].parentNode, {type: ['manageField'], entityManageController: this});
				this.fieldsList.forEach(this.declareItems, this);
				dndForm.sync();
			}
		},
		
		declareItems: function(node, index, nodeList) {
			domClass.add(node, 'dojoDndItem');
			// On met une poign�e !
			domConstruct.place('<i class="fa fa-arrows"></i>', node.childNodes[0]);
			domStyle.set(node.childNodes[0], 'cursor', 'move');
			domAttr.set(node, 'search_field_index', index);
			domClass.add(node.childNodes[0], 'dojoDndHandle');
		},
		
		getTreeTitle: function() {
			return query('form[name="'+this.domNodeId+'_manage_form"] div div label')[0].innerHTML;
		},
		
		updateDeleteButtons: function() {
			var delete_field = query('form[name="'+this.domNodeId+'_manage_form"] input[name="delete_field"]')[0];
			this.fieldsList.forEach(function(node, index, nodeList){
				var search_field_index = domAttr.get(node, 'search_field_index');
				var button = dojo.byId('delete_field_button_' + search_field_index);
				if (button) {
					domAttr.set(button, 'onclick', '');
					on(button, 'click', lang.hitch(this, function() {
						domAttr.set(delete_field, 'value', search_field_index);
						this.getFormInfos();
						domAttr.set(delete_field, 'value', '');
					}));
				}
			}, this);
		},
		
		createJsonDataInput: function(e) {
			domConstruct.create('input', {
				type: 'hidden',
				name: 'form_json_data',
				value: domForm.toJson(e.target)
			}, e.target);
		}
	});
});