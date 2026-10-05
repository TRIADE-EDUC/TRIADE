<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_itemslist_datasource_items.class.php,v 1.9.6.1.2.1 2025/04/29 10:10:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
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
		//on commence par récupérer l'identifiant retourné par le sélecteur...
		$selector = $this->get_selected_selector();
		if ($selector) {
			$return = array();
			if (is_countable($selector->get_value()) && count($selector->get_value()) > 0) {
				foreach ($selector->get_value() as $value) {
					$return[] = intval($value);
				}
			}

			if (count($return)) {
				$items_ids = array();
				$query = "select id_item from docwatch_items where id_item in ('".implode("','",$return)."')";
				/**
				 * On ne veut garder que les items non-lu, lu ou restauré.
				 *
				 * les statuts :
				 * 0 -> UNREAD
				 * 1 -> READ OR RESTORE
				 * 2 -> DELETE
				 * 3 -> PURGED
				 */
				$query .= " AND item_status IN (0, 1)";
				if ($this->parameters["sort_by"] != "") {
					$query .= " order by ".addslashes($this->parameters["sort_by"]);
					if ($this->parameters["sort_order"] != "") $query .= " ".addslashes($this->parameters["sort_order"]);
				}
				$result = pmb_mysql_query($query);
				if ($result) {
					if (pmb_mysql_num_rows($result)) {
						while($row=pmb_mysql_fetch_object($result)){
							$items_ids[] = $row->id_item;
						}
					}
				}

				$items_ids = $this->filter_datas('items', $items_ids);
				if ($this->parameters["nb_max_elements"] > 0) {
					$items_ids = array_slice($items_ids, 0, $this->parameters["nb_max_elements"]);
				}

				$itemslist = array();
				foreach ($items_ids as $item_id) {
					$docwatch_item = new docwatch_item($item_id);
					$itemslist[] = $docwatch_item->get_normalized_item();
				}
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