<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_cataloging_items.class.php,v 1.7.8.1 2025/04/24 12:37:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/frbr/cataloging/frbr_cataloging_item.class.php");
require_once($class_path."/authperso.class.php");
require_once($class_path."/elements_list/elements_list_ui.class.php");

class frbr_cataloging_items {

	protected $num_datanode;
	/**
	 * Liste des éléments en cours de catalogage
	 */
	protected $cataloging_items;

	/**
	 * Liste des types éléments
	 */
	protected static $items_types;

	/**
	 * Constructeur
	 */
	public function __construct($num_datanode=0) {
		$this->num_datanode = intval($num_datanode);
		$this->fetch_data();
	}

	protected function fetch_data() {
		global $PMBuserid;

		$this->cataloging_items = array();
		$query = "select num_cataloging_item, type_cataloging_item from frbr_cataloging_items where cataloging_item_num_user =".$PMBuserid." and cataloging_item_num_datanode =".$this->num_datanode;
		$query .= " order by cataloging_item_added_date DESC";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$this->cataloging_items[] = new frbr_cataloging_item($row->num_cataloging_item, $row->type_cataloging_item);
			}
		}
	}

	public function get_display_list() {
		$display = '';
		if(count($this->cataloging_items)) {
			foreach ($this->cataloging_items as $item) {
				$display .= $item->get_display();
			}
		}
		return $display;
	}

	/**
	 * Sélecteur des types élément
	 */
	public static function get_selector($name, $selected = '', $onchange = '') {
		global $charset;

		$selector = "<select name='".$name."' onchange=\"".$onchange."\">";
		foreach(static::get_items_types() as $item_key=>$item_value){
			$selector .= "<option value='".$item_key."' ".($selected == $item_key ? "selected='selected'" : "").">".htmlentities($item_value, ENT_QUOTES, $charset)."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}

	public static function get_items_types() {
		global $msg;
		global $pmb_use_uniform_title;
		global $thesaurus_concepts_active;

		if(!isset(static::$items_types)) {
			static::$items_types['auteur'] = $msg['133'];
			if (SESSrights & THESAURUS_AUTH) {
				static::$items_types['categorie'] = $msg['134'];
			}
			static::$items_types['editeur'] = $msg['135'];
			static::$items_types['collection'] = $msg['136'];
			static::$items_types['subcollection'] = $msg['137'];
			static::$items_types['serie'] = $msg['333'];
			if ($pmb_use_uniform_title) {
				static::$items_types['titre_uniforme'] = $msg['aut_menu_titre_uniforme'];
			}
			static::$items_types['indexint'] = $msg['indexint_menu'];
			if ($thesaurus_concepts_active==true && (SESSrights & CONCEPTS_AUTH)) {
				static::$items_types['ontology'] = $msg['ontology_skos_menu'];
			}
			$authpersos=new authpersos();
			if(isset($authpersos->info) && is_array($authpersos->info)) {
				foreach ($authpersos->info as $elt){
					static::$items_types['authperso'][$elt['id']] = $elt['name'];
				}
			}
		}
		return static::$items_types;
	}

	public static function get_type_from_what($what){
		switch($what){
			case 'auteur':
			case 'categorie':
			case 'editeur':
			case 'collection':
			case 'subcollection':
			case 'serie':
			case 'titre_uniforme':
			case 'indexint':
			case 'ontology':
			case 'authperso':
				return 'authorities';
				break;
			case 'notice':
				return 'records';
				break;
		}
	}

	public function get_list(){
		global $base_path;

		$list = array();
		if(count($this->cataloging_items)) {
			foreach ($this->cataloging_items as $item) {
				$list[] = array('id' => $item->get_id(), 'type' => $item->get_type()."_selectors");
			}
		}
		$elements_list_ui = new elements_list_ui($list, count($list), 1);
		$elements_list_ui->set_current_url($base_path.'/ajax.php?module=frbr&categ=cataloging&sub=items&action=get_list');

		$display = $elements_list_ui->get_elements_list();
//     		print $begin_result_liste;
//     		search_authorities::get_caddie_link();
		$display .= $elements_list_ui->get_elements_list_nav();
//     		print $end_result_liste;

		return $display;
	}
}