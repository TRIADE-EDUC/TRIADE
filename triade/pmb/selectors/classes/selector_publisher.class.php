<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_publisher.class.php,v 1.9.4.1 2025/01/16 10:24:12 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once($base_path."/selectors/classes/selector_authorities.class.php");
require($base_path."/selectors/templates/sel_editeur.tpl.php");
require_once($class_path.'/searcher/searcher_factory.class.php');
require_once($class_path.'/editor.class.php');
require_once($class_path."/authority.class.php");
require_once($class_path."/entities/entities_publishers_controller.class.php");

class selector_publisher extends selector_authorities {

	public function __construct($user_input=''){
		parent::__construct($user_input);
		$this->objects_type = 'publishers';
	}

	protected function get_form() {
		global $charset;
		global $selector_publisher_form;

		$form = $selector_publisher_form;
		$form = str_replace("!!deb_saisie!!", htmlentities($this->user_input,ENT_QUOTES,$charset), $form);
		$form = str_replace("!!base_url!!",static::get_base_url(),$form);
		return $form;
	}

	protected function save() {
		global $ed_nom;
		global $ed_adr1, $ed_adr2;
		global $ed_cp, $ed_ville, $ed_pays;
		global $ed_web;

		$value = array();
		$value['name']	=	$ed_nom;
		$value['adr1']	=	$ed_adr1;
		$value['adr2']	=	$ed_adr2;
		$value['cp']	=	$ed_cp;
		$value['ville']	=	$ed_ville;
		$value['pays']	=	$ed_pays;
		$value['web']	=	$ed_web;

		$editeur = new editeur();
		$editeur->update($value);
		return $editeur->id;
	}

	protected function get_authority_instance($authority_id=0, $object_id=0) {
		return new authority($authority_id, $object_id, AUT_TABLE_PUBLISHERS);
	}

	protected function get_display_object($id=0, $object_id=0) {
		global $charset;
		global $caller;
		global $callback;

		$display = '';
		$authority = $this->get_authority_instance($id, $object_id);
		$editeur = $authority->get_object_instance();

		print pmb_bidi($authority->get_display_statut_class_html(). "
		<a href='#' onclick=\"set_parent('$caller', '".$authority->get_num_object()."', '".htmlentities(addslashes($editeur->get_header()),ENT_QUOTES, $charset)."','$callback')\">".
		htmlentities($editeur->get_header(),ENT_QUOTES, $charset)."</a><br />");
		return $display;
	}

	protected function get_entities_controller_instance($id=0) {
		return new entities_publishers_controller($id);
	}

	public static function get_params_url() {
		global $p3, $p4, $p5, $p6;

		$params_url = parent::get_params_url();
		$params_url .= ($p3 ? "&p3=".$p3 : "").($p4 ? "&p4=".$p4 : "").($p5 ? "&p5=".$p5 : "").($p6 ? "&p6=".$p6 : "");
		return $params_url;
	}
}
?>