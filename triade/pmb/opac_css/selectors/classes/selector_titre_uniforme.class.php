<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_titre_uniforme.class.php,v 1.11.2.1 2025/01/16 11:24:29 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once($base_path."/selectors/classes/selector_authorities.class.php");
require($base_path."/selectors/templates/sel_titre_uniforme.tpl.php");
require_once($class_path.'/searcher/searcher_factory.class.php');
require_once($class_path.'/titre_uniforme.class.php');
require_once($class_path."/authority.class.php");

class selector_titre_uniforme extends selector_authorities {

	public function __construct($user_input=''){
		parent::__construct($user_input);
		$this->objects_type = 'titres_uniformes';
	}

	protected function get_form() {
		global $charset;
		global $titre_uniforme_form;
		global $selector_titre_uniforme_form;

		$titre_uniforme_form = $selector_titre_uniforme_form;
		$titre_uniforme_form = str_replace("!!deb_saisie!!", htmlentities($this->user_input,ENT_QUOTES,$charset), $titre_uniforme_form);
		$titre_uniforme_form = str_replace("!!base_url!!",static::get_base_url(),$titre_uniforme_form);
		$titre_uniforme = new titre_uniforme(0);
		$titre_uniforme->show_form();
	}

	protected function get_search_form() {
		global $msg;
		global $sel_search_form;
		global $oeuvre_type_selector, $oeuvre_nature_selector;

		$sel_search_form = parent::get_search_form();
		$select_oeuvre_type = new marc_select( 'oeuvre_type', 'oeuvre_type_selector', ($oeuvre_type_selector ? $oeuvre_type_selector : '0'), '', '0', $msg['authorities_select_all']);
		$sel_search_form = str_replace( '!!oeuvre_type!!', $select_oeuvre_type->display, $sel_search_form);
		$select_oeuvre_nature = new marc_select( 'oeuvre_nature', 'oeuvre_nature_selector', ($oeuvre_nature_selector ? $oeuvre_nature_selector : '0'), '', '0', $msg['authorities_select_all']);
		$select_oeuvre_nature->first_item_at_last();
		$sel_search_form = str_replace( '!!oeuvre_nature!!', $select_oeuvre_nature->get_radio_selector(), $sel_search_form);
		return $sel_search_form;
	}

	protected function save() {
		global $name;
		global $oeuvre_nature;
		global $oeuvre_type;
		global $authority_statut;

		$value = array(
			'name' 			=> $name,
			'oeuvre_nature' => $oeuvre_nature,
			'oeuvre_type' 	=> $oeuvre_type,
			'statut'=> $authority_statut);

		$titre_uniforme = new titre_uniforme();
		$titre_uniforme->update($value);
		return $titre_uniforme->id;
	}

	protected function get_authority_instance($authority_id=0, $object_id=0) {
		//return new authority($authority_id, $object_id, AUT_TABLE_TITRES_UNIFORMES);
		return authorities_collection::get_authority('authority', $authority_id, ['num_object' => $object_id, 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
	}

	protected function get_display_object($id=0, $object_id=0) {
		global $charset;
		global $caller;
		global $callback;

		$display = '';
		$authority = $this->get_authority_instance($id, $object_id);
		$titre_uniforme = $authority->get_object_instance();

		$display .= "<div class='row'>";
		$display .= pmb_bidi($authority->get_display_statut_class_html()."<a href='#' onclick=\"set_parent('$caller', '".$authority->get_num_object()."', '".htmlentities(addslashes($titre_uniforme->get_header()),ENT_QUOTES, $charset)."','$callback')\">".$titre_uniforme->get_header()."</a>");
		$display .= "</div>";
		return $display;
	}

	protected function get_link_pagination() {
		global $rech_regexp;
		global $oeuvre_type_selector;
		global $oeuvre_nature_selector;

		$link = static::get_base_url()."&rech_regexp=$rech_regexp&user_input=".rawurlencode($this->user_input)."&oeuvre_type_selector=".$oeuvre_type_selector."&oeuvre_nature_selector=".$oeuvre_nature_selector."&page=!!page!!";
		return $link;
	}

	public function get_sel_search_form_template() {
		global $msg, $charset;

		$sel_search_form ="
			<form name='".$this->get_sel_search_form_name()."' method='post' action='".static::get_base_url()."'>
				<div class='row'>
					!!oeuvre_type!!
				</div>
				<div class='row'>
					!!oeuvre_nature!!
				</div>
				<input type='text' name='f_user_input' value=\"".htmlentities($this->user_input,ENT_QUOTES,$charset)."\">
				&nbsp;
				<input type='submit' class='bouton_small' value='".$msg[142]."' />
				!!bouton_ajouter!!
			</form>
			<script>
				<!--
				document.forms['".$this->get_sel_search_form_name()."'].elements['f_user_input'].focus();
				-->
			</script>
		";
		return $sel_search_form;
	}
}
?>