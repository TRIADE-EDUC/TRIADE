<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_entity_authority_form.class.php,v 1.3.8.1.2.2 2025/04/24 14:45:33 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/entity/interface_entity_form.class.php');

class interface_entity_authority_form extends interface_entity_form {

	protected $num_statut;

	protected $page;

	protected $nbr_lignes;

	protected $user_input;

	protected $grid_editables = true;

	protected $id_authperso;

	protected function get_function_name_check_perso() {
		return '';
	}

	protected function get_js_script_check_perso() {
	    if ($this->get_function_name_check_perso()) {
    		return "
    			if(typeof ".$this->get_function_name_check_perso()." == 'function'){
    				var check = ".$this->get_function_name_check_perso()."(form);
    				if (check == false) return false;
    			}
    		";
	    }
	}

	protected function get_js_script_check_fields() {
		if(isset($this->field_focus) && $this->field_focus) {
			return "
			if(form.".$this->field_focus.".value.length == 0) {
				alert('".addslashes($this->get_js_script_error_label())."');
				document.forms['".$this->name."'].elements['".$this->field_focus."'].focus();
				return false;
			}
			";
		}
		return "";
	}

	protected function get_js_function_test_form() {
		global $pmb_autorites_verif_js;

		$js_function = "
		function test_form(form) {
			if (typeof check_form == 'function') {
				if (!check_form()) {
					return false;
				}
			}";
			if ($pmb_autorites_verif_js != "") {
				$js_function .= $this->get_js_script_check_perso();
			}
			$js_function .= $this->get_js_script_check_fields();
			$js_function .= "
			unload_off();
			return true;
		}";
		return $js_function;
	}

	protected function get_js_gridform() {
		return "
		<script type='text/javascript'>
			require(['dojo/ready', 'apps/pmb/gridform/FormEdit'], function(ready, FormEdit){
			     ready(function(){
			     	new FormEdit();
			     });
			});
		</script>";
	}

	protected function get_js_script() {
		global $pmb_autorites_verif_js, $base_path, $msg;

		$js_script = jscript_unload_question()."
			".($pmb_autorites_verif_js!= "" ? "<script type='text/javascript' src='$base_path/javascript/$pmb_autorites_verif_js'></script>":"")."
			<script type='text/javascript'>
				".$this->get_js_function_test_form()."

				function confirm_delete() {
			        result = confirm(\"".$msg['confirm_suppr']."\");
			        if(result) {
			        	unload_off();
			            document.location='".$this->get_delete_action()."';
					} else {
			            document.forms['".$this->get_name()."'].elements['".$this->field_focus."'].focus();
					}
			    }
				function check_link(id) {
					w=window.open(document.getElementById(id).value);
					w.focus();
				}
			</script>
			<script src='javascript/ajax.js'></script>
			".$this->get_js_gridform()."
			<script type='text/javascript'>
				document.title='".addslashes($this->document_title)."';
			</script>";
		return $js_script;
	}

	protected function get_editables_buttons() {
		global $msg, $PMBuserid, $pmb_form_authorities_editables;

		$display ='
			<!-- Selecteur de statut -->
			<label class="etiquette" for="authority_statut">'.$msg['authorities_statut_label'].'</label>
			'.$this->get_statuses_selector().'
		';
		if ($PMBuserid==1 && $pmb_form_authorities_editables==1){
			$display .= "<input type='button' class='bouton_small' value='".$msg["authorities_edit_format"]."' id=\"bt_inedit\"/>";
		}
		if ($pmb_form_authorities_editables==1) {
			$display .= "<input type='button' class='bouton_small' value=\"".$msg["authorities_origin_format"]."\" id=\"bt_origin_format\"/>";
		}
		return $display;
	}

	protected function get_submit_action() {
		return $this->get_url_base()."&sub=update".(!empty($this->object_id) ? "&id=".$this->object_id : "");
	}

	protected function get_display_hidden_fields() {
		global $charset;

		return "
		<input type='hidden' name='page' value='".$this->page."' />
		<input type='hidden' name='nbr_lignes' value='".$this->nbr_lignes."' />
		<input type='hidden' name='user_input' value=\"".htmlentities($this->user_input, ENT_QUOTES, $charset)."\" />";
	}

	protected function get_display_actions() {
		global $msg;
		global $pmb_type_audit;

		$display = "
		<div class='left'>
			".$this->get_display_cancel_action()."
			".$this->get_display_submit_action()."
			<input type='hidden' name='save_and_continue' id='save_and_continue' value='' />
	        <input type='button' id='update_continue' class='bouton' value='" . $msg['save_and_continue'] . "' onClick=\"document.getElementById('save_and_continue').value=1;if (test_form(this.form)) this.form.submit();\" />
			".($this->object_id ? $this->get_display_replace_action() : "")."
			".($this->object_id ? $this->get_display_see_records_action() : "")."
			".($this->object_id && !empty($this->duplicable) ? $this->get_display_duplicate_action() : "")."
			".($pmb_type_audit && $this->object_id ? $this->get_display_audit_action() : "")."
			".$this->get_display_hidden_fields()."
		</div>
		<div class='right'>
			".($this->object_id ? $this->get_display_delete_action() : "")."
		</div>";
		return $display;
	}

	protected function get_display_cancel_action() {
		return "<input type='button' class='bouton' value='".$this->get_action_cancel_label()."' id='btcancel' onClick=\"unload_off();document.location='".$this->get_cancel_action()."';\" />";
	}

	protected function get_replace_action() {
		return $this->get_url_base()."&sub=replace&id=".$this->object_id;
	}

	protected function get_action_see_records_label() {
		global $msg;
		return $msg['voir_notices_assoc'];
	}

	protected function get_see_records_action() {
		global $base_path;
		$action = $base_path."/catalog.php?categ=search&etat=aut_search";
		switch ($this->table_name) {
			case 'authors':
				$action .= "&mode=0";
				break;
			case 'publishers':
				$action .= "&mode=2&aut_type=publisher";
				break;
			case 'collections':
				$action .= "&mode=2&aut_type=collection";
				break;
			case 'indexint':
				$action .= "&mode=1&aut_type=indexint";
				break;
			case 'series':
				$action .= "&mode=10&aut_type=tit_serie";
				break;
			case 'sub_collections':
				$action .= "&mode=2&aut_type=subcoll";
				break;
			case 'titres_uniformes':
				$action .= "&mode=9&aut_type=titre_uniforme";
				break;
			case 'authperso_authorities':
				$action .= "&mode=".($this->id_authperso + 1000)."&etat=aut_search&aut_type=authperso";
				break;
			default:
				break;
		}
		$action .= "&aut_id=".$this->object_id;
		return $action;
	}

	protected function get_display_see_records_action() {
		return "<input type='button' value='".$this->get_action_see_records_label()."' class='bouton' id='btseerecords' onClick=\"unload_off();document.location='".$this->get_see_records_action()."';\" />";
	}

	protected function get_action_duplicate_label() {
		global $msg;
		return $msg['aut_duplicate'];
	}

	protected function get_duplicate_action() {
		return $this->get_url_base()."&sub=duplicate&id=".$this->object_id;
	}

	protected function get_display_content_form() {
	    return "
	    <div class='row'>
    	    <a onclick='expandAll();return false;' href='#'><img border='0' id='expandall' src='".get_url_icon('expand_all.gif')."'></a>
    	    <a onclick='collapseAll();return false;' href='#'><img border='0' id='collapseall' src='".get_url_icon('collapse_all.gif')."'></a>
	    </div>
	    <div id='zone-container'>
	       ".$this->content_form."
	    </div>";
	}

	public function get_display($ajax = false) {
		global $current_module;

		$display = $this->get_js_script();
		$display .= "
		<form class='form-".$current_module."' id='".$this->name."' name='".$this->name."'  method='post' action=\"".$this->get_submit_action()."\" onSubmit=\"return false\" ".(!empty($this->enctype) ? "enctype='".$this->enctype."'" : "").">
			".$this->get_display_label()."
			<div class='form-contenu'>
				".$this->get_display_content_form()."
			</div>
			<div class='row'>
				".$this->get_display_actions()."
			</div>
		<div class='row'></div>
		</form>";
		if(isset($this->table_name) && $this->table_name) {
			$translation = new translation($this->object_id, $this->table_name);
			$display .= $translation->connect($this->name);
		}

		if(isset($this->field_focus) && $this->field_focus) {
			$display .= "<script type='text/javascript'>document.forms['".$this->name."'].elements['".$this->field_focus."'].focus();</script>";
		}
		$display .= "<script type='text/javascript'>
			ajax_parse_dom();
		</script>";
		return $display;
	}

	public function set_num_statut($num_statut) {
		$this->num_statut = intval($num_statut);
		return $this;
	}

	public function set_page($page) {
		$this->page = intval($page);
		return $this;
	}

	public function set_nbr_lignes($nbr_lignes) {
		$this->nbr_lignes = intval($nbr_lignes);
		return $this;
	}

	public function set_user_input($user_input) {
		$this->user_input = $user_input;
		return $this;
	}

	protected function get_statuses_selector() {
		return '';
	}
}