<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_editorial_tree.tpl.php,v 1.20 2019-05-27 11:56:11 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");
global $base_path, $cms_editorial_tree_layout, $cms_editorial_tree_content;
		
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
		</div>
		<div id='section_tree'>
			<script type='text/javascript'>
			var store = new dojo.data.ItemFileWriteStore({
    	        	url: './ajax.php?module=cms&categ=list_sections'
        		});
        		var treeModel = new dijit.tree.ForestStoreModel({
            		store: store,
            		query: {
		                'type': 'root_section'
	            	},
    	        	rootId: 'root',
        	    	rootLabel: 'Racine',
            		childrenAttrs: ['children']
        		});
	
    	    	var cms_editorial_tree = new dijit.Tree({
	    	        model: treeModel,
					persist : true,
	    	        openOnDblClick : true,
	    	        betweenThreshold : '5',
	    	        getIconClass : get_icon_class,
	    	        getLabelClass : get_label_class,
	    	        getLabel : get_label,
	   	            _createTreeNode: function(args) {
                        var tnode = new dijit._TreeNode(args);
                        tnode.labelNode.innerHTML = args.label;
                        return tnode;
                    },
	    	        
	            	dndController: 'dijit.tree.dndSource'
	    		    },
	        		'section_tree'
    	    	);
    	    	cms_editorial_tree.dndController.checkItemAcceptance = cms_check_if_item_tree_can_drop_here;
    	    	cms_editorial_tree.dndController.checkAcceptance = cms_check_if_draggeable_item_tree;
				dojo.connect(cms_editorial_tree,'onClick',cms_load_content_infos);	
				dojo.connect(treeModel, 'onAddToRoot', cms_section_add_to_root);
        		dojo.connect(treeModel, 'onLeaveRoot', cms_section_leave_root);
				dojo.connect(treeModel, 'onChildrenChange', cms_child_change);
			</script>
		</div>";
