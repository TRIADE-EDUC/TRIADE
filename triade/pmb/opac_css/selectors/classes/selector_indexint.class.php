<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_indexint.class.php,v 1.11.2.1 2025/01/16 11:24:29 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once($base_path."/selectors/classes/selector_authorities.class.php");
require($base_path."/selectors/templates/sel_indexint.tpl.php");
require_once($class_path.'/searcher/searcher_factory.class.php');
require_once($class_path.'/indexint.class.php');
require_once($class_path."/authority.class.php");

class selector_indexint extends selector_authorities {

	public function __construct($user_input=''){
		parent::__construct($user_input);
		$this->objects_type = 'indexint';
	}

	protected function get_form() {
		global $charset;
		global $selector_indexint_form;

		$form = $selector_indexint_form;
		$form = str_replace("!!deb_saisie!!", htmlentities($this->user_input,ENT_QUOTES,$charset), $form);
		$form = str_replace("!!base_url!!",static::get_base_url(),$form);
		return $form;
	}

	protected function get_add_link() {
		global $pclass_url;

		$link = parent::get_add_link();
		$link .= $pclass_url;
		return $link;
	}

	protected function get_search_form() {
		global $thesaurus_classement_mode_pmb, $typdoc;
		global $id_pclass;
		global $exact;

		$sel_search_form = parent::get_search_form();

		$toprint_typdocfield = '';
		if ($thesaurus_classement_mode_pmb) { //classement indexation décimale autorisé en parametrage
			$query = "select id_pclass,name_pclass from pclassement where typedoc like '%$typdoc%' order by name_pclass";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result) == 1) {
				$row = pmb_mysql_fetch_object($result);
				$id_pclass=$row->id_pclass;
				$toprint_typdocfield .= "[".$row->name_pclass."]";
			} elseif(pmb_mysql_num_rows($result) > 1) {
				$toprint_typdocfield .= "<select id='id_pclass' name='id_pclass' ";
				$toprint_typdocfield .= "onchange = \"document.location = '".static::get_base_url()."&id_pclass='+document.getElementById('id_pclass').value; \">" ;
				while ($row = pmb_mysql_fetch_object($result)) {
					$toprint_typdocfield .= "<option value='$row->id_pclass'";
					if ($id_pclass==$row->id_pclass) {
						$toprint_typdocfield .=" selected";
					}
					$toprint_typdocfield .= ">".$row->name_pclass."</option>\n";
				}
				$toprint_typdocfield .= "</select>";
			}
		}
		$sel_search_form = str_replace("!!pclassement!!", $toprint_typdocfield, $sel_search_form);
		if ((string)$exact=="") $exact=1;
		if ($exact) {
			$sel_search_form = str_replace("!!check1!!", "checked", $sel_search_form);
			$sel_search_form = str_replace("!!check0!!", "", $sel_search_form);
		} else {
			$sel_search_form = str_replace("!!check1!!", "", $sel_search_form);
			$sel_search_form = str_replace("!!check0!!", "checked", $sel_search_form);
		}
		return $sel_search_form;
	}

	protected function save() {
		global $indexint_nom;
		global $indexint_comment;
		global $id_pclass;

		$value=	$indexint_nom;
		$indexint = new indexint(0);
		$indexint->update($value,$indexint_comment,$id_pclass);
		return $indexint->indexint_id;
	}

	protected function get_authority_instance($authority_id=0, $object_id=0) {
		//return new authority($authority_id, $object_id, AUT_TABLE_INDEXINT);
		return authorities_collection::get_authority('authority', $authority_id, ['num_object' => $object_id, 'type_object' => AUT_TABLE_INDEXINT]);
	}

	protected function get_display_object($id=0, $object_id=0) {
		global $charset;
		global $caller;
		global $callback;
		global $thesaurus_classement_mode_pmb;

		$display = '';
		$authority = $this->get_authority_instance($id, $object_id);
		$indexint = $authority->get_object_instance();

		if ($indexint->comment) {
			$entry = $indexint->name." - ".$indexint->comment;
		} else {
			$entry = $indexint->name ;
		}
		if ($thesaurus_classement_mode_pmb != 0) { //classement indexation décimale autorisé en parametrage
			$entry="[".$indexint->name_pclass."] ".$entry;
		}
		$display .= pmb_bidi($authority->get_display_statut_class_html()."
			<a href='#' onclick=\"set_parent('$caller', '".$authority->get_num_object()."', '".htmlentities(addslashes(str_replace("\r"," ",str_replace("\n"," ",$entry))),ENT_QUOTES,$charset)."','$callback')\">
				$entry</a>");
		$display .= "<br />";
		return $display;
	}

	public function get_sel_search_form_template() {
		global $msg, $charset;

		$sel_search_form ="
			<form name='".$this->get_sel_search_form_name()."' method='post' action='".static::get_base_url()."'>
				!!pclassement!!
				<input type='text' name='f_user_input' value=\"".htmlentities($this->user_input,ENT_QUOTES,$charset)."\">
				&nbsp;
				<input type='submit' class='bouton_small' value='".$msg[142]."' />
				<br />
				<input type='radio' name='exact' id='exact1' value='1' !!check1!!/>
				<label class='etiquette' for='exact1'>&nbsp;".$msg["indexint_search_index"]."</label>&nbsp;
				<input type='radio' name='exact' id='exact0' value='0' !!check0!!/>
				<label for='exact0' class='etiquette'>&nbsp;".$msg["indexint_search_comment"]."</label>
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

	public static function get_params_url() {
		global $typdoc, $id_pclass;
		global $thesaurus_classement_mode_pmb;
		global $thesaurus_classement_defaut;

		$params_url = parent::get_params_url();
		$params_url .= ($typdoc ? "&typdoc=".$typdoc : "");
		if ($thesaurus_classement_mode_pmb) {
			$query = "select id_pclass,name_pclass from pclassement where typedoc like '%$typdoc%' order by name_pclass";
			$result = pmb_mysql_query($query);
			while ($row = pmb_mysql_fetch_object($result)) {
				if ($id_pclass==$row->id_pclass) {
					$params_url .= "&id_pclass=".$id_pclass;
				}
			}
		} else {
			$params_url .= "&id_pclass=".$thesaurus_classement_defaut;
		}
		return $params_url;
	}
}
?>