<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_editorial_tree.tpl.php,v 1.7 2023/08/17 09:47:55 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");
global $base_path, $cms_editorial_tree_layout, $cms_editorial_tree_content, $msg;
		
$cms_editorial_tree_layout= "
		<script src='./javascript/cms/cms_tree_dnd.js'></script>
		<script>
			dojo.require('dijit.layout.ContentPane');
			dojo.require('dijit.tree.ForestStoreModel');
			dojo.require('dojo.data.ItemFileWriteStore');
    		dojo.require('dijit.Tree');
    		dojo.require('dijit.tree.dndSource');  	
    		dojo.require('dojox.layout.ContentPane');	
		</script>
		<div class='colonne3'>
			<div id='editorial_tree_container' style='min-height:600px' href='./ajax.php?module=cms&categ=get_tree' dojoType='dojox.layout.ContentPane'></div>
		</div>	
		<div class='colonne-suite'>
			<div id='content_infos' dojoType='dojox.layout.ContentPane'></div>
		</div>";

$cms_editorial_tree_content ="
		<img class='dijitTreeExpando dijitTreeExpandoClosed' onClick='dijit.byId(\"section_tree\").expandAll();' role='presentation' data-dojo-attach-point='expandoNode' alt='' src='".get_url_icon("expand_all.gif")."'>
		<img class='dijitTreeExpando dijitTreeExpandoOpened' onClick='dijit.byId(\"section_tree\").collapseAll();' role='presentation' data-dojo-attach-point='expandoNode' alt='' src='".get_url_icon("collapse_all.gif")."'>
		<span id='add_buttons'>
			&nbsp;<a id='add_section_button' href='$base_path/cms.php?categ=section&sub=edit&id=new'>".$msg['cms_editorial_form_new_section_from_section']."</a>
			&nbsp;<a id='add_article_button' href='$base_path/cms.php?categ=article&sub=edit&id=new'>".$msg['cms_editorial_form_new_article_from_section']."</a>
		</span>
		<div id='section_tree'>
			<script>
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
