<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_collection.class.php,v 1.10.4.1 2025/01/16 10:24:11 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once($base_path."/selectors/classes/selector_authorities.class.php");
require($base_path."/selectors/templates/sel_collection.tpl.php");
require_once($class_path.'/searcher/searcher_factory.class.php');
require_once("$class_path/collection.class.php");
require_once($class_path."/authority.class.php");
require_once($class_path."/entities/entities_collections_controller.class.php");

class selector_collection extends selector_authorities {

	public function __construct($user_input=''){
		parent::__construct($user_input);
		$this->objects_type = 'collections';
	}

	protected function get_form() {
		global $charset;
		global $selector_collection_form;

		$form = $selector_collection_form;
		$form = str_replace("!!deb_saisie!!", htmlentities($this->user_input,ENT_QUOTES,$charset), $form);
		$form = str_replace("!!base_url!!",static::get_base_url(),$form);
		return $form;
	}

	protected function save() {
		global $collection_nom;
		global $ed_id;
		global $issn;

		$value = array();
		$value['name'] = $collection_nom;
		$value['parent'] = $ed_id;
		$value['issn'] = $issn;
		$collection = new collection();
		$collection->update($value);
		return $collection->id;
	}

	protected function get_authority_instance($authority_id=0, $object_id=0) {
		return new authority($authority_id, $object_id, AUT_TABLE_COLLECTIONS);
	}

	protected function get_display_object($id=0, $object_id=0) {
		global $charset;
		global $caller;
		global $callback;

		$display = '';
		$authority = $this->get_authority_instance($id, $object_id);
		$collection = $authority->get_object_instance();

		$display .= pmb_bidi($authority->get_display_statut_class_html()."
 			<a href='#' onclick=\"set_parent('$caller', ".$authority->get_num_object().", '".htmlentities(addslashes($collection->name),ENT_QUOTES, $charset)."', '$callback', $collection->parent, '".htmlentities(addslashes($collection->editor_isbd),ENT_QUOTES,$charset)."')\">
				".$collection->name."</a>");
		$display .= pmb_bidi(".&nbsp;".$collection->editeur."<br />");
		return $display;
	}

	protected function get_entities_controller_instance($id=0) {
		return new entities_collections_controller($id);
	}

	public static function get_params_url() {
		global $p3, $p4, $p5, $p6, $mode;

		$params_url = parent::get_params_url();
		$params_url .= ($p3 ? "&p3=".$p3 : "").($p4 ? "&p4=".$p4 : "").($p5 ? "&p5=".$p5 : "").($p6 ? "&p6=".$p6 : "").($mode ? "&mode=".$mode : "");
		return $params_url;
	}
}
?>