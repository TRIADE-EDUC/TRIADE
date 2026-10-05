// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchController.js,v 1.21 2021/05/18 09:35:16 arenou Exp $

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
        'dojo/parser'
], function(declare, ContentPane, Memory, lang, SearchFieldsTree, query, domConstruct, SearchDnd, domClass, domAttr, ObjectStoreModel, domStyle, on, Standby, request, domForm, parser) {
	return declare(null, {
		contentTree: null,
		contentForm: null,
		store: null,
		searchFieldsList: null,
		widgets: [],
		uniqueIdentifier: '',
		module: null,
		constructor: function(uniqueIdentifier, module) {
			this.uniqueIdentifier = uniqueIdentifier;
			this.module = (module ? module : "catalog");
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
			}).placeAt(this.uniqueIdentifier);
			this.contentForm = new ContentPane({
				splitter: true,
				region: 'center',
				style: 'height:100%;'
			}).placeAt(this.uniqueIdentifier);
		},
		
		parseSelector: function() {
			this.store = new Memory({data:[{id: 'root'}]});
			var children = dojo.byId('add_field').children;
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
								id: 'parent_' + i + '_children_' + children[i].children[j].value,
								value: children[i].children[j].value,
								label: children[i].children[j].label,
								authperso: (domAttr.get(children[i].children[j], 'data-authperso_id') ? domAttr.get(children[i].children[j], 'data-authperso_id') : ""),
								parent: 'parent_' + i,
								leaf: true
							});
						}
					}
				} else if ((children[i].nodeName == 'OPTION') && (children[i].value)) {
					this.store.put({
						id: 'root_' + i + "_children_" + children[i].value,
						label: children[i].label,
                        value: children[i].value,
						parent: 'root',
						leaf: true
					});
				}
			}
			this.store.getChildren = function(object) {
				return lang.hitch(this.store, this.query({parent: object.id}));
			};
			domStyle.set(dojo.byId('add_field').parentNode, 'display', 'none');
		},
		
		buildTree: function() {
			// Un titre pour l'arbre
			domConstruct.place('<h3>' + this.getTreeTitle() + '</h3>', this.contentTree.id);
			
			// Expand/Collapse all
			domConstruct.place('<span id="search_fields_tree_expandall" class="liLike"><img class="dijitTreeExpando dijitTreeExpandoClosed" data-dojo-attach-point="expandoNode" src="'+pmbDojo.images.getImage('expand_all.gif')+'"></span><span id="search_fields_tree_collapseall" class="liLike"><img class="dijitTreeExpando dijitTreeExpandoOpened" data-dojo-attach-point="expandoNode" src="'+pmbDojo.images.getImage('collapse_all.gif')+'"></span>', this.contentTree.id);
			
			// Filtre rapide
			this.input = domConstruct.create('input', {
				type : 'text',
				id : 'fast_filter_input',
				placeholder : pmbDojo.messages.getMessage('admin_parameters', 'admin_param_edit_input_placeholder')
			}, this.contentTree.id, 'last');
			on(this.input, 'keyup', lang.hitch(this, this.launchFilter));
			
			// Div row
			domConstruct.place('<div class="row"></div>', this.contentTree.id);
			
			var model = new ObjectStoreModel({
				store: this.store,
				query: { id: 'root'},
				mayHaveChildren: function(item) {
					return !item.leaf;
				}
			});
			this.tree = new SearchFieldsTree({model: model, searchController: this});
			this.tree.placeAt(this.contentTree);
			on(dojo.byId('search_fields_tree_expandall'), 'click', lang.hitch(this,function(){this.tree.expandAll()}));
			on(dojo.byId('search_fields_tree_collapseall'), 'click', lang.hitch(this,function(){this.tree.collapseAll()}));

			var search_perso = dojo.byId('search_perso');
			if(search_perso){
				domConstruct.place('<hr><h3>' + pmbDojo.messages.getMessage('search', 'search_perso_title') + '</h3>', this.contentTree.id);
				domConstruct.place(search_perso,this.contentTree.id);
				domStyle.set(search_perso, 'display', 'block');
			}
		},
		
		buildForm: function() {
			var form = query('form[name="search_form"]')[0];
			domConstruct.place(form,this.contentForm.id);
			this.widgets = parser.parse(form);
			on(form, 'submit', lang.hitch(this, this.createJsonDataInput));
			this.updateForm(form);
		},
		
		updateForm: function(form) {
			this.searchFieldsList = query('table tbody tr', form);
			if (this.searchFieldsList.length) {
				if (domStyle.get(form, 'display') == 'none') {
					domStyle.set(form, 'display', 'block');
				}
				if (dojo.byId("search_fields_no_selected_fields")) {
					domConstruct.destroy("search_fields_no_selected_fields");
				}
				this.initDnd();
				this.updateDeleteButtons();
				this.updateSelectorDate();
			} else {
				domStyle.set(form, 'display', 'none');
				domConstruct.place('<span class="saisie-contenu" id="search_fields_no_selected_fields">' + pmbDojo.messages.getMessage('search', 'search_fields_no_selected_fields') + '</span>',this.contentForm.id);
			}
		},
		
		getFormInfos: function() {
			var stand = new Standby({target: this.contentForm.get('id'), imageText: 'Chargement...', image: pmbDojo.images.getImage('patience.gif')});
			document.body.appendChild(stand.domNode);
			stand.startup();
			stand.show();
			enable_operators();
			form = query('form[name="search_form"]')[0];
			request.post("ajax.php?module="+this.module+"&categ=extended_search&sub=get_already_selected_fields",{
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
		
		initDnd: function() {
			if (this.searchFieldsList.length) {
				var dndForm = new SearchDnd(this.searchFieldsList[0].parentNode, {type: ['searchField'], searchController: this});
				this.searchFieldsList.forEach(this.declareItems, this);
				dndForm.sync();
			}
		},
		
		declareItems: function(node, index, nodeList) {
			domClass.add(node, 'dojoDndItem');
			// On met une poignée !
			domConstruct.place('<i class="fa fa-arrows"></i>', node.childNodes[0]);
			domStyle.set(node.childNodes[0], 'cursor', 'move');
			domAttr.set(node, 'search_field_index', index);
			domClass.add(node.childNodes[0], 'dojoDndHandle');
		},
		
		getTreeTitle: function() {
			return query('form[name="search_form"] div div label')[0].innerHTML;
		},
		
		updateDeleteButtons: function() {
			var delete_field = query('form[name="search_form"] input[name="delete_field"]')[0];
			this.searchFieldsList.forEach(function(node, index, nodeList){
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

		updateSelectorDate: function() {
			this.searchFieldsList.forEach(function(node, index, nodeList){
				var selector = query('select[name^="op_"]', node);
				if (selector.length && selector[0]) {
					on(selector[0], 'change', lang.hitch(this, function() {
						this.getFormInfos();
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
		},
		
		launchFilter : function() {
			// Les TreeNode ne sont présents dans l'arbre DOM que si tout est déplié
			this.tree.expandAll().then(lang.hitch(this,function(){
				// Du coup, quand c'est bon on récupère la saisie dans le filtre
				let inputValue = this.input.value.toLowerCase();
				// On cherche les items dans le sore
				let searchedItems = this.store.query({label : new RegExp(inputValue,"i")})
				let focused = [];
				// Pour chaque item, on va chercher l'objet associé et ses parents.
				for(let i=0 ; i<searchedItems.length ; i++){
					let treeNode = this.tree.getNodesByItem(searchedItems[i].id);
					focused = [].concat(focused,treeNode[0].getTreePath());
				}
				// Maintenant, on a tous les élements que l'on veut voir afficher...
				// Petit parcours récursif pour gérer ca
				this.showHideSearch(focused);
			}));
		},
		
		showHideSearch : function(focused,id='root'){
			// On récupère les enfants
			let children = this.store.getChildren({'id' : id});
			children.forEach(lang.hitch(this,function(element){
				let displayField = 'none';
				// Pour chaque, on regarde s'il faut l'afficher ou non
				for(let i=0 ; i<focused.length ; i++){
					if(element.id == focused[i].id || element.id == "root"){
						displayField = "block";
						break;
					}
				}
				// Dans tous les cas, il faut manipuler le DOM...
				let treeNodes = this.tree.getNodesByItem(element.id);
				let treeNode = treeNodes[0];
				treeNode.domNode.style.display = displayField;
				// Petite récursion pour être sur de son coup !
				this.showHideSearch(focused,element.id);
			}));
		}
	});
});