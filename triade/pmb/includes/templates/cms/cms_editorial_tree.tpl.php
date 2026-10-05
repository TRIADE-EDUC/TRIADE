<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_editorial_tree.tpl.php,v 1.26.2.1 2024/06/19 06:55:26 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");
global $base_path, $cms_editorial_tree_layout, $cms_editorial_tree_content, $cms_editorial_tree_selected_item;
		
$cms_editorial_tree_layout= "
		<script type='text/javascript' src='./javascript/misc.js'></script>
		<script type='text/javascript' src='./javascript/cms/cms_tree_dnd.js'></script>
		<script type='text/javascript'>
			dojo.require('dijit.layout.ContentPane');
			dojo.require('dijit.tree.ForestStoreModel');
			dojo.require('dojo.data.ItemFileWriteStore');
    		dojo.require('dijit.Tree');
    		dojo.require('dijit.tree.dndSource');  	
    		dojo.require('dojox.layout.ContentPane');	
		</script>
		<div data-dojo-type='dijit/layout/BorderContainer' data-dojo-props='gutters:true' style='min-height:400px;height:auto' id='treeBorderContainer'>
			<div data-dojo-type='dojox/layout/ContentPane' data-dojo-props='splitter:true, region:\"left\"' style='width:40%;' id='editorial_tree_container' href='./ajax.php?module=cms&categ=get_tree'></div>
			<div data-dojo-type='dojox/layout/ContentPane' data-dojo-props='region:\"center\"' style='height:auto;' id='content_infos'></div>
            <script type='text/javascript'>
                require(['dojo/dom','dojo/dom-style','dijit/registry', 'dojo/ready'], function(dom, domStyle, registry, ready) {
                    ready(function(){
                        var mh= Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
                        var off=0;
                        var obj = dom.byId('treeBorderContainer');  
                        // pour retrouver les top
                        do {
                            off+= obj.offsetTop;
                       	} while (obj = obj.offsetParent); 
                        var obj = dom.byId('treeBorderContainer');
                        // on retire également les margin-bottom des parents...
                        do {
                            if(obj.nodeType == 1){  
                                off+= domStyle.get(obj,'marginBottom');
                            }
                       	} while (obj = obj.parentNode);
                        // on n'a donc plus d'ascenseur vertical (sauf si le menu de gauche dépasse, mais là...)
                        registry.byId('treeBorderContainer').resize({h:(mh-off)});
                    });
                });
            </script>
		</div>";

$cms_editorial_tree_content ="
		<div id='cms_menu_editorial_tree' class='uk-flex-auto uk-flex uk-flex-row ss-nav-cms'>
			<span class='ss-nav-cms-item' class='liLike'>
				<img class='dijitTreeExpando dijitTreeExpandoClosed' onClick='dijit.byId(\"section_tree\").expandAll();' role='presentation' data-dojo-attach-point='expandoNode' alt='' src='".$base_path."/images/expand_all.gif'>
			</span>
			<span class='ss-nav-cms-item' class='liLike'>
				<img class='dijitTreeExpando dijitTreeExpandoOpened' onClick='dijit.byId(\"section_tree\").collapseAll();' role='presentation' data-dojo-attach-point='expandoNode' alt='' src='".$base_path."/images/collapse_all.gif'>
			</span>
			<span class='ss-nav-cms-item' id='add_buttons' class='liLike'>
				&nbsp;<a class='uk-button uk-button-default uk-button-small ui-button-Xsmall wyr-custom wyr-input' id='add_section_button' href='".$base_path."/cms.php?categ=section&sub=edit&id=new'>".$msg['cms_editorial_form_new_section_from_section']."</a>
				&nbsp;<a class='uk-button uk-button-default uk-button-small ui-button-Xsmall wyr-custom wyr-input' id='add_article_button' href='".$base_path."/cms.php?categ=article&sub=edit&id=new'>".$msg['cms_editorial_form_new_article_from_section']."</a>
			</span>
			<div id='cms_menu_editorial_tree_clear_cache_buttons'>
				<span class='ss-nav-cms-item' id='add_buttons_clear cache' class='cache liLike'>
					!!cms_editorial_clean_cache_button!!
				</span>
				<span class='ss-nav-cms-item' id='add_buttons_clear cache_img' class='cache liLike'>
					!!cms_editorial_clean_cache_img!!
				</span>
			</div>
			<div class='ss-nav-cms-item' class='clear'></div>
            <div>
				<select id='fast_filter_param'>
					<option value='title' selected>".$msg['cms_editorial_filter_by_title']."</option>
					<option value='id'>".$msg['cms_editorial_filter_by_id']."</option>
				</select>
                <input type='text' id='fast_filter_input' />
            </div>
		</div>
		<div id='section_tree'>
			<script type='text/javascript'>
                require([
                    'dojo/ready', 
                    'dojo/data/ItemFileWriteStore',
                    'dijit/tree/ForestStoreModel',
                    'dijit/Tree',
                    'dojo/_base/lang',
                    'apps/cms/CmsContextMenu',
                    'dojo/on',
                    'dojo/dom',
                    'dojo/_base/lang',
                ], 
                function(ready, ItemFileWriteStore, ForestStoreModel, Tree, lang, CmsContextMenu, on, dom, lang) {
                    ready(function(){
            			var store = new ItemFileWriteStore({
            	        	url: './ajax.php?module=cms&categ=list_sections'
                		});
                		var treeModel = new ForestStoreModel({
                    		store: store,
                    		query: {
            	                'type': 'root_section'
                        	},
            	        	rootId: 'root',
                	    	rootLabel: 'Racine',
                    		childrenAttrs: ['children'],
                		});

                        var showHideSearch = function(focused){                                                   
                            store._arrayOfAllItems.forEach(lang.hitch(this,function(element){
                				let displayField = 'none';
                				// Pour chaque, on regarde s'il faut l'afficher ou non
                				for(let i=0 ; i<focused.length ; i++){
                					if(element.id == focused[i].id || element.id == 'root'){
                						displayField = 'block';
                						break;
                					}
                				}
                				// Dans tous les cas, il faut manipuler le DOM...
                				let treeNodes = cms_editorial_tree.getNodesByItem(element.id[0]);
                				let treeNode = treeNodes[0];
                                if(treeNode) {
                					treeNode.domNode.style.display = displayField;
								}
                            }));
                        }
            	
            	    	var cms_editorial_tree = new Tree({
                    	        model: treeModel,
                				persist : true,
                    	        openOnDblClick : true,
                    	        betweenThreshold : '5',
                    	        getIconClass : get_icon_class,
                    	        getLabelClass : get_label_class,
                    	        getLabel : get_label,
                                add_context_menu: add_context_menu,
                                is_Load: false,
                   	            _createTreeNode: function(args) {
                                    var tnode = new dijit._TreeNode(args);
                                    tnode.labelNode.innerHTML = args.label;
                                    if (this.is_Load) {
                                        this.add_context_menu(this.tree.getChildren(), CmsContextMenu);
                                    }
                                    return tnode;
                                },
                            	dndController: 'dijit.tree.dndSource'
                		    },
                    		'section_tree'
            	    	);
            	    	cms_editorial_tree.dndController.checkItemAcceptance = cms_check_if_item_tree_can_drop_here;
            	    	cms_editorial_tree.dndController.checkAcceptance = cms_check_if_draggeable_item_tree;
                        
                        cms_editorial_tree.onLoadDeferred.then(lang.hitch(cms_editorial_tree, function() {
                            this.is_Load = true;
                            this.add_context_menu(this.tree.getChildren(), CmsContextMenu);
                        }))

                        dojo.connect(cms_editorial_tree,'onClick',cms_load_content_infos);
            			dojo.connect(treeModel, 'onAddToRoot', cms_section_add_to_root);
                		dojo.connect(treeModel, 'onLeaveRoot', cms_section_leave_root);
            			dojo.connect(treeModel, 'onChildrenChange', cms_child_change);

						const fast_filter = function(param, value) {
							// Les TreeNode ne sont présents dans l'arbre DOM que si tout est déplié
                            cms_editorial_tree.expandAll().then(lang.hitch(this,function(){
            				    // On cherche les items dans le store
                				let searchedItems = store.fetch({
                                    query: { [param]: '*' + value + '*' },
                                    queryOptions: { ignoreCase: true },
                                    onComplete: function(items){
                                        let focused = [];
                                        for(let i=0 ; i<items.length ; i++){
                        					let treeNode = cms_editorial_tree.getNodesByItem(items[i].id[0]);
                                            if(treeNode && treeNode[0]) {
                        						focused = [].concat(focused,treeNode[0].getTreePath());
                        					}
                        				}
                                        showHideSearch(focused);
                                    }
                                });
                            }))
						}
						
                        on(dom.byId('fast_filter_input'), 'keyup', function (evt) {
							fast_filter(dom.byId('fast_filter_param').value, evt.target.value);
                        });
						on(dom.byId('fast_filter_param'), 'change', function (evt) {
							const fast_filter_input = dom.byId('fast_filter_input');
							fast_filter_input.value = '';
							fast_filter(evt.target.value, fast_filter_input.value);
                        });
                    });
			     });
    		</script>
    	</div>";

$cms_editorial_tree_selected_item= "
        <script type='text/javascript'>
            require(['dojo/dom','dojo/dom-style','dijit/registry', 'dojo/ready'], function(dom, domStyle, registry, ready) {
                ready(function(){
                    setTimeout(function(){
                        if(dijit.byId('section_tree')) {
                            if('!!item_type!!' == 'article') {
                                dijit.byId('section_tree').set('selectedItem', 'article_!!item_id!!');
                            } else {
                                dijit.byId('section_tree').set('selectedItem', '!!item_id!!');
                            }
							setTimeout(function(){
                            	if(dijit.byId('section_tree').get('selectedItem')) {
                                	cms_load_content_infos(dijit.byId('section_tree').get('selectedItem'));
                            	}
                        	}, 1000);
                        }
                    }, 1000);
                });
            });
        </script>";
