<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_itemslist_datasource_items.class.php,v 1.5 2017-06-05 10:13:38 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/docwatch/docwatch_item.class.php");

class cms_module_itemslist_datasource_items extends cms_module_common_datasource_list{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->sortable = true;
		$this->limitable = true;
	}
	
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	*/
	public function get_available_selectors(){
		return array(
				"cms_module_itemslist_selector_items_generic"
		);
	}

	/*
	 * On défini les critères de tri utilisable pour cette source de donnée
	*/
	protected function get_sort_criterias() {
		return array (
				"item_publication_date",
				"id_item",
				"item_title"
		);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		global $dbh;
		//on commence par récupérer l'identifiant retourné par le sélecteur...
		$selector = $this->get_selected_selector();
		if($selector){
			$return = array();
			if (count($selector->get_value()) > 0) {
				foreach ($selector->get_value() as $value) {
					$return[] = $value*1;
				}
			}
			
			if(count($return)){
				$itemslist = array();
				$query = "select id_item from docwatch_items where id_item in ('".implode("','",$return)."')";
				if ($this->parameters["sort_by"] != "") {
					$query .= " order by ".addslashes($this->parameters["sort_by"]);
					if ($this->parameters["sort_order"] != "") $query .= " ".addslashes($this->parameters["sort_order"]);
				}
				$result = pmb_mysql_query($query,$dbh);
				if ($result) {
					if (pmb_mysql_num_rows($result)) {
						while($row=pmb_mysql_fetch_object($result)){
							$docwatch_item = new docwatch_item($row->id_item);
							$itemslist[] = $docwatch_item->get_normalized_item();
						}
					}
				}
				$itemslist = $this->filter_datas('items', $itemslist);
				if ($this->parameters["nb_max_elements"] > 0) $itemslist = array_slice($itemslist, 0, $this->parameters["nb_max_elements"]);
				return array('items' => $itemslist);
			}
		}
		return false;
	}
	
	public function get_format_data_structure(){
		
		$datasource_item = new cms_module_item_datasource_item();
		return array(
				array(
					'var' => "items",
					'desc' => $this->msg['cms_module_itemslist_datasource_items_desc'],
					'children' => $this->prefix_var_tree($datasource_item->get_format_data_structure(),"items[i]")
				)
		);
	}
}