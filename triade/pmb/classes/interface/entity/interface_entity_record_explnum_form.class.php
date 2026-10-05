<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_entity_record_explnum_form.class.php,v 1.2.2.1.2.3 2025/04/24 14:45:33 qvarin Exp $

use Pmb\Common\Library\CSRF\CollectionCSRF;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/entity/interface_entity_record_form.class.php');

class interface_entity_record_explnum_form extends interface_entity_record_form {

	protected $record_id;

	public $explnum_id;

	protected function get_js_script_check_fields() {
		global $msg, $charset;

		$js_script = "
		if((form.f_nom.value.length == 0) && (form.f_fichier.value.length == 0) && (form.f_url.value.length == 0)) {
			alert(\"".htmlentities($msg['explnum_error_creation'], ENT_QUOTES, $charset)."\");
			return false;
		}
		if((form.f_fichier.value.length != 0) && (form.f_url.value.length == 0) && (document.getElementById('upload').checked==true) && (document.getElementById('id_rep').value==0)) {
			alert(\"".htmlentities($msg['explnum_error_rep_upload'], ENT_QUOTES, $charset)."\");
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
			     	new FormEdit('catalog', 'explnum');
			     });
			});
		</script>";
	}

	protected function get_js_script() {
		global $msg, $charset;

		$js_script = jscript_unload_question();

		$collectionCSRF = new CollectionCSRF();

		$js_script .= "
			<script type='text/javascript'>
				".$this->get_js_function_test_form()."
			</script>
			<script src='javascript/ajax.js'></script>
			".$this->get_js_gridform()."
			<script type='text/javascript'>
				//Test si le fichier est déjà uploadé au meme endroit
				function ecraser_fichier(filename){

					var res = confirm(\"".htmlentities($msg['docnum_ecrase_file'], ENT_QUOTES, $charset)." \"+filename+\".\\n".htmlentities($msg['agree_question'], ENT_QUOTES, $charset)."\");
					if(res) {
						document.getElementById('f_new_name').value = filename;
						return true;
					}
					document.getElementById('f_new_name').value = '';
					return false;

				}
				const tabToken_explnum = " . json_encode($collectionCSRF->getArrayTokens()) . ";
				function chklnk_f_url(element){
					if(element.value != ''){
						var wait = document.createElement('img');
						wait.setAttribute('src','".get_url_icon('patience.gif')."');
						wait.setAttribute('align','top');
						while(document.getElementById('f_url_check').firstChild){
							document.getElementById('f_url_check').removeChild(document.getElementById('f_url_check').firstChild);
						}
						var csrf_token = tabTokens_explnum[0];
						tabTokens_explnum.splice(0, 1);
						document.getElementById('f_url_check').appendChild(wait);
						var testlink = encodeURIComponent(element.value);
				 		var check = new http_request();
						if(check.request('./ajax.php?module=ajax&categ=chklnk',true,'&timeout=0&link='+testlink+'&csrf_token='+csrf_token)){
							alert(check.get_text());
						}else{
							var result = check.get_text();
							var type_status=result.substr(0,1);
							var img = document.createElement('img');
							var src='';
							if(type_status == '2' || type_status == '3'){
								if((element.value.substr(0,7) != 'http://') && (element.value.substr(0,8) != 'https://')) element.value = 'http://'+element.value;
								//impec, on print un petit message de confirmation
								src = '".get_url_icon('tick.gif')."';
							}else{
								//problème...
								src = '".get_url_icon('error.png')."';
								img.setAttribute('style','height:1.5em;');
							}
							img.setAttribute('src',src);
							img.setAttribute('align','top');
							while(document.getElementById('f_url_check').firstChild){
								document.getElementById('f_url_check').removeChild(document.getElementById('f_url_check').firstChild);
							}
							document.getElementById('f_url_check').appendChild(img);
						}
					}
				}
			</script>
			<script src=\"./javascript/http_request.js\" type='text/javascript'></script>
			<script src=\"./javascript/ajax.js\" type='text/javascript'></script>
			<script src=\"./javascript/select.js\" type='text/javascript'></script>
			<script src=\"./javascript/upload.js\" type='text/javascript'></script>";
		return $js_script;
	}

	protected function get_editables_buttons() {
		global $msg, $charset, $PMBuserid, $pmb_form_explnum_editables;

		$display = '';
		if ($PMBuserid==1 && $pmb_form_explnum_editables==1){
			$display .= "<input type='button' class='bouton_small' value='".htmlentities($msg["catal_edit_format"], ENT_QUOTES, $charset)."' id=\"bt_inedit\"/>";
		}
		if ($pmb_form_explnum_editables==1) {
			$display .= "<input type='button' class='bouton_small' value=\"".htmlentities($msg["catal_origin_format"], ENT_QUOTES, $charset)."\" id=\"bt_origin_format\"/>";
		}
		return $display;
	}

	protected function get_submit_action() {
// 		return $this->get_url_base()."&categ=update".(!empty($this->object_id) ? "&id=".$this->object_id : "");
	}

	protected function get_display_hidden_fields() {
		return "
		<input type='hidden' name='f_explnum_id' value='!!explnum_id!!' />
		<input type='hidden' name='f_bulletin' value='!!bulletin!!' />
		<input type='hidden' name='f_notice' value='!!notice!!' />
		<input type='hidden' name='f_new_name' id='f_new_name' value='' />";
	}

	protected function get_display_actions() {
		global $pmb_type_audit;

		$display = "
		<div class='left'>
			".$this->get_display_cancel_action()."
			".$this->get_display_submit_action()."
			!!associate_speakers!!
			".($pmb_type_audit && $this->object_id ? $this->get_display_audit_action() : "")."
			".$this->get_display_hidden_fields()."
		</div>
		<div class='right'>
			".($this->object_id ? $this->get_display_delete_action() : "")."
		</div>";
		return $display;
	}

// 	protected function get_display_cancel_action() {
// 	    return $this->get_display_action('btcancel', $this->get_action_cancel_label(), ['function' => 'history.go(-1);']);
// 	}

	protected function get_display_audit_action() {
		return audit::get_dialog_button($this->explnum_id, AUDIT_EXPLNUM);
	}

	protected function get_display_delete_action() {
		return $this->get_display_action('delete_button', $this->get_action_delete_label(), ['function' => 'confirm_delete();']);
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
		<form class='form-".$current_module."' id='".$this->name."' name='".$this->name."'  method='post' action=\"".$this->get_submit_action()."\" onSubmit=\"!!submit_action!!\" ".(!empty($this->enctype) ? "enctype='".$this->enctype."'" : "").">
			".$this->get_display_label()."
			<div class='form-contenu'>
				".$this->get_display_content_form()."
			</div>
			<div class='row'>
				".$this->get_display_actions()."
			</div>
		</form>";
		$display .= "
		<script type=\"text/javascript\">
			ajax_parse_dom();
		</script>";
		return $display;
	}
}