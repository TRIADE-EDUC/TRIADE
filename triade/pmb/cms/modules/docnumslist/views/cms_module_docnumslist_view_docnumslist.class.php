<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_docnumslist_view_docnumslist.class.php,v 1.3 2023/08/28 14:01:12 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_docnumslist_view_docnumslist extends cms_module_common_view {
	protected static $nb_row=0;
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->use_dojo = true;
	}
	
	public static function format_datas_from_source($datas){
		$datastore = array();
		foreach($datas as $data){
			$datastore = array_merge($datastore,self::recurse_datas($data));
		}
		return encoding_normalize::utf8_normalize($datastore);
	}
	
	public function get_headers($datas=array()){
		$headers = parent::get_headers($datas);

			$headers[] = '<script type="text/javascript">
		require(["dojo/_base/window", "dojo/store/Memory", "dojo/store/JsonRest", "dijit/tree/ObjectStoreModel", "dijit/Tree", "dijit/registry", "dojo/domReady!"], function(win, Memory, JsonRest, ObjectStoreModel, Tree, registry){

		var myStore = new JsonRest({
			target: "'.$datas['jsonstore'].'",
			getChildren: function (object){
 				return this.query({parent: object.id});
 			},
			
		});
		
		// Create the model
	    var myModel = new ObjectStoreModel({
	        store: myStore,
	        query: {id: "root"},
			mayHaveChildren: function(object){
				return object.children;
			}
	    });
	
	    // Create the Tree.
	    var tree = new Tree({
	        model: myModel,
			showRoot: false,
			onClick : function (item,node,evt){
				if(item.explnum_id){
					var url = "./visionneuse.php?lvl=afficheur&explnum="+item.explnum_id;
					registry.byId("'.$this->get_module_dom_id().'_visualizer").set("content","<iframe src=\'"+url+"\' style=\'border:none;height:99%;width:100%\'></iframe>");
				}
			},
			getIconClass : function (item, opened){
				if(!item.explnum_id){
					return (!item || this.model.mayHaveChildren(item)) ? (opened ? "dijitFolderOpened" : "dijitFolderClosed") : "dijitLeaf"
				}else{
					return "dijitLeaf"
				}
			}
	    },"'.$this->get_module_dom_id().'_tree");
	});	
</script>';

		return $headers;
	}
	
	public function render($datas){
		$html  = '';
		$html  = '
		<div data-dojo-type="dijit/layout/LayoutContainer" style="height:500px;width:100%">		
			<div data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:\'left\'" style="width:200px;">
				<div id="'.$this->get_module_dom_id().'_tree"></div>
			</div>	
			 <div id="'.$this->get_module_dom_id().'_visualizer" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region: \'center\'"></div>	
		</div>';
		return $html;
	}
}