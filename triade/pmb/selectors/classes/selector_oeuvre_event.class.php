<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_oeuvre_event.class.php,v 1.3.6.1 2025/01/16 10:24:12 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path;
require_once($base_path."/selectors/classes/selector_authperso.class.php");
require_once($base_path."/selectors/templates/sel_oeuvre_event.tpl.php");

class selector_oeuvre_event extends selector_authperso {

	public function __construct($user_input=''){
		parent::__construct($user_input);
	}

	public function get_sel_search_form_template() {
		global $msg, $charset;

		$authpersos= authpersos::get_oeuvre_event_authpersos();
		if(!$authperso_id)$authperso_id=$authpersos[0]['id'];
		$sel_authpersos = '';
		if (count($authpersos)>1) {
			$sel_authpersos = "<select class='saisie-20em' id='authperso_id' name='authperso_id' onchange = \"this.form.submit()\">";
			foreach($authpersos as $authperso) {
				$sel_authpersos.= "<option value='".$authperso['id']."' "; ;
				if ($authperso_id == $authperso['id']) $sel_authpersos.= " selected";
				$sel_authpersos.= ">".htmlentities($authperso['name'],ENT_QUOTES,$charset)."</option>";
			}
			$sel_authpersos.= "</select>&nbsp;";
		}

		$sel_search_form ="
			<form name='".$this->get_sel_search_form_name()."' method='post' action='".static::get_base_url()."'>
				".$sel_authpersos."
				<input type='text' name='f_user_input' value=\"".htmlentities($this->user_input,ENT_QUOTES,$charset)."\">
				&nbsp;
				<input type='submit' class='bouton_small' value='".$msg[142]."' />
				!!bouton_ajouter!!
			</form>
			<script type='text/javascript'>
				<!--
				document.forms['".$this->get_sel_search_form_name()."'].elements['f_user_input'].focus();
				-->
			</script>
		";
		return $sel_search_form;
	}

	public function proceed() {
		global $msg;
		global $action;
		global $authperso_id;
		global $param1;

		$authpersos = authpersos::get_oeuvre_event_authpersos();
		if (empty($authpersos)) {
			print $msg['oeuvre_event_sel_no'];
			exit;
		}
		if (!empty($param1)) {
		    $authperso_id = $param1;
		}
		if (empty($authperso_id)) {
		    $authperso_id = $authpersos[0]['id'];
		}

		switch($action) {
			default:
				parent::proceed();
				break;
		}
	}
}
?>