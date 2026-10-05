<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_entity_record_expl_form.class.php,v 1.3.4.3 2025/04/24 14:45:33 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/entity/interface_entity_record_form.class.php');

class interface_entity_record_expl_form extends interface_entity_record_form {

    protected $cb;

	protected $record_id;

	protected function get_js_script_check_fields() {
		global $msg;

		$js_script = "
		!!questionrfid!!
		if((form.f_ex_cb.value.replace(/^\s+|\s+$/g, '').length == 0) || (form.f_ex_cote.value.replace(/^\s+/g, '').replace(/\s+$/g,'').length == 0)) {
			alert(\"$msg[304]\");
			return false;
		}
		if (typeof(form.f_ex_typdoc) == 'undefined') {
			alert(\"".$msg["expl_typdoc_mandatory"]."\");
			return false;
		}
		if(!form.f_ex_location.value) {
			alert(\"".$msg["expl_location_mandatory"]."\");
			return false;
		}
		if (typeof(form.f_ex_cstat) == 'undefined') {
			alert(\"".$msg["expl_codestat_mandatory"]."\");
			return false;
		}
		";
		return $js_script;
	}

	protected function get_js_gridform() {
		return "
		<script type='text/javascript'>
			require(['dojo/ready', 'apps/pmb/gridform/FormEdit'], function(ready, FormEdit){
			     ready(function(){
			     	new FormEdit('catalog', 'expl');
			     });
			});
		</script>";
	}

	protected function get_js_script() {
		global $pmb_rfid_activate, $pmb_rfid_serveur_url, $pmb_rfid_driver, $rfid_js_header;
		global $msg, $base_path, $pmb_expl_verif_js;

		$js_script = jscript_unload_question();
		if ($pmb_rfid_activate==1 && $pmb_rfid_serveur_url ) {

			if($pmb_rfid_driver=="ident")  $script_erase="init_rfid_erase(rfid_ack_erase);";
			else $script_erase="rfid_ack_erase(1);";

			$rfid_script_catalog="
				$rfid_js_header
				<script type='text/javascript'>
					var flag_cb_rfid=0;
					flag_program_rfid_ask=0;
					setTimeout(\"init_rfid_read_cb(0,f_expl);\",0);;
					nb_part_readed=0;

					var msg_rfid_programmation_confirmation = '".addslashes($msg['rfid_programmation_confirmation'])."';
					var msg_rfid_etiquette_programmee_message = '".addslashes($msg['rfid_etiquette_programmee_message'])."';

					function program_rfid() {
						flag_semaphore_rfid=1;
						flag_program_rfid_ask=0;
						var nbparts = 0;
						if(document.getElementById('f_ex_nbparts')) {
							nbparts = document.getElementById('f_ex_nbparts').value;
							if(nb_part_readed!= nbparts) {
								flag_semaphore_rfid=0;
								alert(\"".addslashes($msg['rfid_programmation_nbpart_error'])."\");
								return;
							}
						} else {
							nbparts = 1;
						}
						$script_erase
					}
				</script>
				<script type='text/javascript' src='".$base_path."/javascript/rfid.js'></script>";
		}else {
			$rfid_script_catalog="";
		}
		if (!empty($pmb_expl_verif_js)) {
		    $js_script .= "<script type='text/javascript' src='$base_path/javascript/$pmb_expl_verif_js'></script>";
		}
		$js_script .= $rfid_script_catalog;
		$js_script .= "
			<script type='text/javascript'>
				".$this->get_js_function_test_form()."
				function calcule_section(selectBox) {
					for (i=0; i<selectBox.options.length; i++) {
						id=selectBox.options[i].value;
					    list=document.getElementById(\"docloc_section\"+id);
					    list.style.display=\"none\";
					}

					id=selectBox.options[selectBox.selectedIndex].value;
					list=document.getElementById(\"docloc_section\"+id);
					list.style.display=\"block\";
				}
			</script>
			<script src='javascript/ajax.js'></script>
			".$this->get_js_gridform();
		return $js_script;
	}

	protected function get_editables_buttons() {
		global $msg, $PMBuserid, $pmb_form_expl_editables;

		$display = '';
		if ($PMBuserid==1 && $pmb_form_expl_editables==1){
			$display .= "<input type='button' class='bouton_small' value='".$msg["catal_edit_format"]."' id=\"bt_inedit\"/>";
		}
		if ($pmb_form_expl_editables==1) {
			$display .= "<input type='button' class='bouton_small' value=\"".$msg["catal_origin_format"]."\" id=\"bt_origin_format\"/>";
		}
		return $display;
	}

	protected function get_submit_action() {
// 		return $this->get_url_base()."&categ=update".(!empty($this->object_id) ? "&id=".$this->object_id : "");
	}

	protected function get_display_hidden_fields() {
		return "
		<input type=\"hidden\" name=\"id_form\" value=\"".md5(microtime())."\">";
	}

	protected function get_display_actions() {
		global $pmb_type_audit, $pmb_rfid_activate, $pmb_rfid_serveur_url;

		$display = "
		<div class='left'>
			".$this->get_display_cancel_action()."
			".$this->get_display_submit_action()."
			".($pmb_rfid_activate && $pmb_rfid_serveur_url ? $this->get_display_rfid_action() : "")."
			".($this->object_id && !empty($this->duplicable) ? $this->get_display_duplicate_action() : "")."
			".($pmb_type_audit && $this->object_id ? $this->get_display_audit_action() : "")."
			".$this->get_display_hidden_fields()."
		</div>
		<div class='right'>
			".($this->object_id ? $this->get_display_delete_action() : "")."
		</div>";
		return $display;
	}

	protected function get_action_duplicate_label() {
		global $msg;
		return $msg['dupl_expl_bt'];
	}

	protected function get_duplicate_action() {
		return $this->get_url_base()."&categ=dupl_expl&id=".$this->record_id."&cb=".urlencode($this->cb)."&expl_id=".$this->object_id;
	}

	protected function get_display_audit_action() {
		return audit::get_dialog_button($this->object_id, 2);
	}

	protected function get_action_rfid_label() {
		global $msg;
		return $msg['rfid_configure_etiquette_button'];
	}

	protected function get_display_rfid_action() {
		return $this->get_display_action('btrfid', $this->get_action_rfid_label(), ['function' => 'program_rfid_ask();']);
	}

	protected function get_display_content_form() {
	    return "
        <div id='zone-container'>
            ".$this->content_form."
        </div>";
	}

	public function get_display($ajax = false) {
		global $current_module;

		$display = $this->get_js_script();
		$display .= "
		<form class='form-".$current_module."' id='".$this->name."' name='".$this->name."'  method='post' action=\"".$this->get_submit_action()."\" ".(!empty($this->enctype) ? "enctype='".$this->enctype."'" : "").">
			".$this->get_display_label()."
			<div class='form-contenu'>
				".$this->get_display_content_form()."
				<div class='row'>&nbsp;</div>
			</div>
			<div class='row'>
				".$this->get_display_actions()."
			</div>
			<div class='row'></div>
		</form>";

		$display .= "
		<script type=\"text/javascript\">
		    var dom_node_f_ex_cote = document.forms['expl'].elements['f_ex_cote'];
			dom_node_f_ex_cote.focus();
		    if(dom_node_f_ex_cote.value.length) {
		        dom_node_f_ex_cote.setSelectionRange(dom_node_f_ex_cote.value.length, dom_node_f_ex_cote.value.length);
		    }
			ajax_parse_dom();
		</script>";

		return $display;
	}

	protected function get_js_function_test_form() {
	    global $pmb_expl_verif_js;

	    $js_function = "
		function test_form(form) {
			if (typeof check_form == 'function') {
				if (!check_form()) {
					return false;
				}
			}";
	    if ($pmb_expl_verif_js != "") {
	        $js_function .= $this->get_js_script_check_perso();
	    }
	    $js_function .= $this->get_js_script_check_fields();
	    $js_function .= "
			return check_form();
		}";
	    return $js_function;
	}
}