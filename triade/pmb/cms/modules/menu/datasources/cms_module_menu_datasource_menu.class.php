<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_menu_datasource_menu.class.php,v 1.10 2023/08/28 14:04:13 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_menu_datasource_menu extends cms_module_common_datasource{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_menu_selector_menu"
		);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		$selector = $this->get_selected_selector();
		if($selector){
			$query = "select managed_module_box from cms_managed_modules join cms_cadres on id_cadre = '".$this->cadre_parent."' and cadre_object = managed_module_name";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$box = pmb_mysql_result($result,0,0);
				$infos =unserialize($box);
				$menu = $infos['module']['menus'][$selector->get_value()]; 
			}
			//les données sont passées par json_encode donc obligatoirement en utf-8
			return encoding_normalize::utf8_decode($menu);
		}
		return false;
	}
	
	public function get_format_data_structure(){
		return array(
			array(
				'var' => "items",
				'desc'=> $this->msg['cms_module_menu_datasource_menu_items_desc'],
				'children' => array(
					array(
						'var' => "items[i].id",
						'desc'=> $this->msg['cms_module_menu_datasource_menu_item_id_desc']
					),
					array(
						'var' => "items[i].title",
						'desc'=> $this->msg['cms_module_menu_datasource_menu_item_title_desc']
					),
					array(
						'var' => "items[i].link",
						'desc'=> $this->msg['cms_module_menu_datasource_menu_item_link_desc']
					),
					array(
						'var' => "items[i].details",
						'desc'=> $this->msg['cms_module_menu_datasource_menu_item_details_desc'],
						'children' => cms_editorial::prefix_var_tree(cms_section::get_format_data_structure(false,false,false),"items[i].details")
					),
					array(
						'var' => "items[i].children",
						'desc'=> $this->msg['cms_module_menu_datasource_menu_item_children_desc']
					)
				)
			)
		);
	}
}