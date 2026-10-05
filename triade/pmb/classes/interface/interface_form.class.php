<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_form.class.php,v 1.30.2.2.2.2 2025/03/06 08:40:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/translation.class.php');

class interface_form {
	
	protected $name;
	
	protected $object_id;
	
	protected $label;
	
	protected $url_base;
	
	protected $confirm_delete_msg;
	
	protected $content_form;
	
	protected $table_name;
	
	protected $field_focus;
	
	protected $duplicable;
	
	protected $actions_extension;
	
	protected $no_deletable;
	
	protected $no_deletable_msg;
	
	protected $deletable_on_auth;
	
	protected $enctype;
	
	protected $grid_editables = false;
	
	public function __construct($name = ''){
		$this->name = $name;
	}
	
	public function get_display_field_text($label, $value) {
	    global $charset;
	    $display = "
        <div class='row'>
            <div class='row'>
                <label class='etiquette'>".htmlentities($label, ENT_QUOTES, $charset)."</label>
            </div>
            <div class='row'>
                ".htmlentities($value, ENT_QUOTES, $charset)."
            </div>
        </div>";
	    return $display;
	}
	
	public function get_display_field_url($label, $value) {
	    global $charset;
	    $display = "
        <div class='row'>
            <div class='row'>
                <label class='etiquette'>".htmlentities($label, ENT_QUOTES, $charset)."</label>
            </div>
            <div class='row'>
                <a href='".$value."' target='_blank'>".htmlentities($value, ENT_QUOTES, $charset)."</a>
            </div>
        </div>";
	    return $display;
	}
	
	protected function get_action_cancel_label() {
		global $msg;
		return $msg['76'];
	}
	
	protected function get_action_save_label() {
		global $msg;
		return $msg['77'];
	}
	
	protected function get_action_replace_label() {
		global $msg;
		return $msg['159'];
	}
	
	protected function get_action_import_label() {
	    global $msg;
	    return $msg['import'];
	}
	
	protected function get_action_delete_label() {
		global $msg;
		
		switch ($this->table_name) {
			case 'lists':
			case 'modules':
			case 'selectors':
			case 'tabs':
			case 'forms':
			case 'mails_settings':
			case 'mails_configuration':
				return $msg['initialize'];
			default:
				return $msg['63'];
		}
	}
	
	protected function get_action_duplicate_label() {
		global $msg;
		return $msg["duplicate"];
	}
	
	protected function get_display_action($name, $label, $event=[], $attrs=[]) {
	    global $charset;
	    
	    $formatted_attrs = [];
	    foreach ($attrs as $name=>$value) {
	        $formatted_attrs[] = $name."='".$value."'";
	    }
	    $display_event = '';
	    if (!empty($event['location'])) {
	        if (!empty($event['confirm_msg'])) {
	            $display_event .= "onClick=\"if(confirm('".htmlentities(addslashes($event['confirm_msg']), ENT_QUOTES, $charset)."')) {document.location='".$event['location']."'}\"";
	        } else {
	            $display_event .= "onClick=\"document.location='".$event['location']."'\"";
	        }
	    } elseif (!empty($event['function'])) {
	        $display_event .= "onClick=\"".$event['function']."\"";
	    }
	    return "<input type='button' class='bouton' name='".$name."' id='".$name."' ".implode(' ', $formatted_attrs)." value=\"".htmlentities($label, ENT_QUOTES, $charset)."\" ".$display_event." />";
	}
	
	protected function get_display_actions() {
		$display = "
		<div class='left'>
			".$this->get_display_cancel_action()."
			".$this->get_display_submit_action()."
			".($this->object_id && !empty($this->duplicable) ? $this->get_display_duplicate_action() : "")."
			".($this->object_id && !empty($this->actions_extension) ? $this->get_display_actions_extension() : "")."
		</div>
		<div class='right'>
			".($this->object_id ? $this->get_display_delete_action() : "")."
		</div>";
		return $display;
	}
	
	protected function get_cancel_action() {
		return $this->get_url_base();
	}
	
	protected function get_display_cancel_action() {
		$where_are_we = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], "/")+1);
		//inutile d'afficher le bouton si l'action sera au même endroit
		if($where_are_we != substr($this->get_cancel_action(), strrpos($this->get_cancel_action(), "/")+1)) {
			return $this->get_display_action('cancel_button', $this->get_action_cancel_label(), ['location' => $this->get_cancel_action()]);
		} 
		return "";
	}
	
	protected function get_submit_action() {
		return $this->get_url_base()."&action=save".(!empty($this->object_id) ? "&id=".$this->object_id : "");
	}
	
	protected function get_display_submit_action() {
		if(isset($this->field_focus) && $this->field_focus) {
			return "<input type='submit' class='bouton' name='save_button' id='save_button' value='".$this->get_action_save_label()."' onClick=\"return test_form(this.form)\" />";
		} else {
			return "<input type='submit' class='bouton' name='save_button' id='save_button' value='".$this->get_action_save_label()."' />";
		}
	}
	
	protected function get_duplicate_action() {
		return $this->get_url_base()."&action=duplicate&id=".$this->object_id;
	}
	
	protected function get_display_duplicate_action() {
		return $this->get_display_action('duplicate_button', $this->get_action_duplicate_label(), ['location' => $this->get_duplicate_action()]);
	}
	
	protected function get_delete_action() {
		global $name;
		
		switch ($this->table_name) {
			case 'mails_configuration':
			case 'modules':
			case 'selectors':
				return $this->get_url_base()."&action=delete&name=".$name;
			default:
				return $this->get_url_base()."&action=delete&id=".$this->object_id;
		}
	}
	
	protected function get_display_delete_action() {
		global $charset;
		
		if(!empty($this->no_deletable)) {
			if(!empty($this->no_deletable_msg)) {
				return "<input type='button' class='bouton' name='delete_button' id='delete_button' value='".htmlentities($this->get_action_delete_label(), ENT_QUOTES, $charset)."' onclick=\"javascript:alert('".htmlentities(addslashes($this->no_deletable_msg), ENT_QUOTES, $charset)."')\" />";
			} else {
				return "";
			}	
		} else {
			if(!empty($this->deletable_on_auth)) {
				return "<input type='button' class='bouton' name='delete_button' id='delete_button' value='".htmlentities($this->get_action_delete_label(), ENT_QUOTES, $charset)."' onclick=\"if(promptAuthenticityCheck()) {if(confirm('".htmlentities(addslashes($this->confirm_delete_msg), ENT_QUOTES, $charset)."')){document.location='".$this->get_delete_action()."';}}\" />";
			} else {
				return "<input type='button' class='bouton' name='delete_button' id='delete_button' value='".htmlentities($this->get_action_delete_label(), ENT_QUOTES, $charset)."' onclick=\"if(confirm('".htmlentities(addslashes($this->confirm_delete_msg), ENT_QUOTES, $charset)."')){document.location='".$this->get_delete_action()."';}\" />";
			}			
		}
	}
	
	protected function get_display_actions_extension() {
		$display = "";
		foreach ($this->actions_extension as $action_extension) {
		    $display .= $this->get_display_action($action_extension['name'], $action_extension['value'], ['location' => $action_extension['destination']]);
		}
		return $display;
	}
	
	protected function get_js_script_error_label() {
		global $msg;
		$error_label = '';
		switch ($this->table_name) {
			case 'arch_emplacement':
			case 'arch_type':
			case 'lenders':
			case 'users_groups':
				$error_label = $msg[559];
				break;
			case 'harvest_profil':
				$error_label = $msg['admin_harvest_build_name_error'];
				break;
			case 'harvest_profil_import':
				$error_label = $msg['admin_harvest_profil_name_error'];
				break;
			case 'bannette_tpl':
			case 'serialcirc_tpl':
				$error_label = $msg['template_nom_erreur'];
				break;
			case 'notice_tpl':
				$error_label = $msg['notice_tpl_nom_erreur'];
				break;
			case 'transactype':
				$error_label = $msg['transactype_form_name_no'];
				break;
			case 'transaction_payment_methods':
				$error_label = $msg['transaction_payment_method_form_name_no'];
				break;
			case 'connectors_out_setcategs':
				$error_label = $msg['admin_connecteurs_setcateg_emptyfield'];
				break;
			case 'connectors_out_sets':
				$error_label = $msg['admin_connecteurs_set_emptyfield'];
				break;
			case 'cashdesk':
				$error_label = $msg["cashdesk_form_name_no"];
				break;
			case 'groupexpl':
				$error_label = $msg['groupexpl_form_name_error'];
				break;
			case 'classements':
				$error_label = $msg['dsi_clas_nom_oblig'];
				break;
			case 'bannettes':
			case 'equations':
				$error_label = $msg['dsi_ban_nom_oblig'];
				break;
			case 'rss_flux':
				$error_label = $msg['dsi_flux_nom_oblig'];
				break;
			case 'pclassement':
				$error_label = $msg['pclassement_libelle_manquant'];
				break;
			case 'thesaurus':
				$error_label = $msg['thes_libelle_manquant'];
				break;
			case 'etagere':
				$error_label = $msg['etagere_name_oblig'];
				break;
			case 'coordonnees':
				$error_label = $msg['acquisition_raison_soc_vide'];
				break;
			case 'contact_form_objects':
				$error_label = $msg["admin_opac_contact_form_object_form_label_error"];
				break;
			case 'demandes_notes':
				$error_label = $msg['demandes_note_create_ko'];
				break;
			case 'authorities_caddie':
			case 'caddie':
			case 'empr_caddie':
				$error_label = $msg['caddie_name_oblig'];
				break;
			case 'connectors_sources':
				$error_label = $msg["connecteurs_check_source_name"];
				break;
			case 'classementGen':
				$error_label = $msg["classementGen_list_form_no_empty"];
				break;
			default :
				$error_label = $msg[98];
				break;
		}
		return $error_label;
	}
	
	protected function get_js_gridform() {
	    global $current_module;
	    return "
		<script type='text/javascript'>
			require(['dojo/ready', 'apps/pmb/gridform/FormEdit'], function(ready, FormEdit){
			     ready(function(){
			     	new FormEdit('".$current_module."', '".$this->name."');
			     });
			});
		</script>";
	}
	
	protected function get_js_script() {
	    $js_script = "";
		if(isset($this->field_focus) && $this->field_focus) {
		    $js_script .= "
			<script type='text/javascript'>
				if(typeof test_form == 'undefined') {
					function test_form(form) {
						if(form.".$this->field_focus.".value.replace(/^\s+|\s+$/g, '').length == 0) {
							alert('".addslashes($this->get_js_script_error_label())."');
							document.forms['".$this->name."'].elements['".$this->field_focus."'].focus();
							return false;
						}
						return true;
					}
				}
			</script>
			";
		}
		if ($this->grid_editables) {
		    $js_script .= $this->get_js_gridform();
		}
	    return $js_script;
	}
	
	protected function get_editables_buttons() {
	    global $msg, $PMBuserid;
	    
	    $display = '';
	    if ($PMBuserid==1) {
	        $display.="<input type='button' class='bouton_small' value='".$msg["catal_edit_format"]."' id=\"bt_inedit\"/>";
	    }
        $display.="<input type='button' class='bouton_small' value=\"".$msg["catal_origin_format"]."\" id=\"bt_origin_format\"/>";
	    return $display;
	}
	
	protected function get_display_label() {
		global $charset;
		
		if ($this->grid_editables) {
		    return "
            <div class='row'>
				<div class='left'>
					<h3>".htmlentities($this->label ?? "", ENT_QUOTES, $charset)."</h3>
				</div>
				<div class='right'>
					".$this->get_editables_buttons()."
				</div>
			</div>";
		} else {
		    return "<h3>".htmlentities($this->label ?? "", ENT_QUOTES, $charset)."</h3>";
		}
	}
	
	protected function get_display_content_form() {
	    if ($this->grid_editables) {
	        return "
            <div id='zone-container'>
				".$this->content_form."
			</div>";
	    } else {
	        return $this->content_form;
	    }
	}
	
	public function get_display($ajax = false) {
		global $current_module;
		
		$display = "
		<form class='form-".$current_module."' id='".$this->name."' name='".$this->name."'  method='post' action=\"".$this->get_submit_action()."\" ".(!empty($this->enctype) ? "enctype='".$this->enctype."'" : "").">
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
		$display .= $this->get_js_script();
		if(isset($this->field_focus) && $this->field_focus) {
			$display .= "<script type='text/javascript'>document.forms['".$this->name."'].elements['".$this->field_focus."'].focus();</script>";
		}
		return $display;
	}
	
	public function get_display_ajax() {
		global $charset;
		global $current_module;
		
		$display = "
		<form class='form-".$current_module."' id='".$this->name."' name='".$this->name."'  method='post' action=\"".$this->get_url_base()."&action=save&id=".$this->object_id."\" >
			".$this->get_display_label()."	
			<div class='form-contenu'>
				".$this->content_form."
			</div>	
			<div class='row'>	
				<div class='left'>
                    ".$this->get_display_action('cancel_button', $this->get_action_cancel_label())."
					<input type='submit' class='bouton' name='save_button' id='save_button' value='".$this->get_action_save_label()."' />
				</div>
				<div class='right'>
					".($this->object_id ? $this->get_display_action('delete_button', $this->get_action_delete_label()) : "")."
				</div>
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
		return $display;
	}
	
	public function branch_translations($table_name) {
		$translation = new translation($this->object_id, $table_name);
		return $translation->connect($this->name);
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_object_id() {
		return $this->object_id;
	}
	
	public function get_label() {
		return $this->label;
	}
	
	public function get_url_base() {
		global $base_path, $current_module, $categ, $sub;
		if(empty($this->url_base)) {
			$this->url_base = $base_path.'/'.$current_module.'.php?categ='.$categ. (!empty($sub) ? '&sub='.$sub : '');
		}
		return $this->url_base;
	}
	
	public function add_url_base($url_extra) {
		$this->get_url_base();
		$this->url_base .= $url_extra;
	}
	
	public function get_confirm_delete_msg() {
		global $msg;
		
		if(!isset($this->confirm_delete_msg)) {
			if(isset($msg[$this->name.'_confirm_delete'])) {
				$this->confirm_delete_msg = $msg[$this->name.'_confirm_delete'];
			}
		}
		return $this->confirm_delete_msg;
	}
	
	public function set_name($name) {
		$this->name = $name;
		return $this;
	}
	
	public function set_object_id($object_id) {
		$this->object_id = intval($object_id);
		return $this;
	}
	
	public function set_label($label) {
		$this->label = $label;
		return $this;
	}
	
	public function set_url_base($url_base) {
		$this->url_base = $url_base;
		return $this;
	}
	
	public function set_confirm_delete_msg($confirm_delete_msg) {
		$this->confirm_delete_msg = $confirm_delete_msg;
		return $this;
	}
	
	public function set_content_form($content_form) {
		$this->content_form = $content_form;
		return $this;
	}
	
	public function set_table_name($table_name) {
		$this->table_name = $table_name;
		return $this;
	}
	
	public function set_field_focus($field_focus) {
		$this->field_focus = $field_focus;
		return $this;
	}
	
	public function set_duplicable($duplicable) {
		$this->duplicable = intval($duplicable);
		return $this;
	}
	
	public function add_action_extension($name, $value, $destination) {
		if(empty($this->actions_extension)) {
			$this->actions_extension = array();
		}
		$this->actions_extension[] = array(
				'name' => $name,
				'value' => $value,
				'destination' => $destination
		);
		return $this;
	}

	public function set_no_deletable($no_deletable) {
		$this->no_deletable = $no_deletable;
		return $this;
	}
	
	public function set_no_deletable_msg($no_deletable_msg) {
		$this->no_deletable_msg = $no_deletable_msg;
		return $this;
	}
	
	public function set_deletable_on_auth($deletable_on_auth) {
		$this->deletable_on_auth = $deletable_on_auth;
		return $this;
	}
	
	public function set_enctype($enctype) {
		$this->enctype = $enctype;
		return $this;
	}
	
	public function set_grid_editables($grid_editables) {
	    $this->grid_editables = $grid_editables;
	    return $this;
	}
}