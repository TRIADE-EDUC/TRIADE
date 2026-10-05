<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_serie.class.php,v 1.10.2.1 2025/01/16 10:24:11 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once($base_path."/selectors/classes/selector_authorities.class.php");
require($base_path."/selectors/templates/sel_serie.tpl.php");
require_once($class_path.'/searcher/searcher_factory.class.php');
require_once($class_path.'/serie.class.php');
require_once($class_path.'/authority.class.php');
require_once($class_path."/entities/entities_series_controller.class.php");

class selector_serie extends selector_authorities {

	public function __construct($user_input=''){
		parent::__construct($user_input);
		$this->objects_type = 'series';
	}

	protected function get_form() {
		global $charset;
		global $selector_serie_form;

		$form = $selector_serie_form;
		$form = str_replace("!!deb_saisie!!", htmlentities($this->user_input ?? "",ENT_QUOTES,$charset), $form ?? "");
		$form = str_replace("!!base_url!!",static::get_base_url(),$form);
		return $form;
	}

	protected function save() {
		global $serie_nom;

		$value = $serie_nom;
		$serie = new serie(0);
		$serie->update($value);
		return $serie->s_id;
	}

	protected function get_authority_instance($authority_id=0, $object_id=0) {
		return new authority($authority_id, $object_id, AUT_TABLE_SERIES);
	}

	protected function get_display_object($id=0, $object_id=0) {
		global $charset;
		global $caller;
		global $callback;

		$display = '';
		$authority = $this->get_authority_instance($id, $object_id);
		$serie = $authority->get_object_instance();
		$display .= pmb_bidi($authority->get_display_statut_class_html()."
			<a href='#' onclick=\"set_parent('$caller', '".$authority->get_num_object()."', '".htmlentities(addslashes($serie->get_header()),ENT_QUOTES,$charset)."','$callback')\">
				".$serie->get_header()."</a>");
		$display .= "<br />";
		return $display;
	}

	protected function get_entities_controller_instance($id=0) {
		return new entities_series_controller($id);
	}
}
?>