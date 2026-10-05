<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_entity_reader_form.class.php,v 1.11.2.2.2.2 2025/03/06 08:40:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/entity/interface_entity_form.class.php');

class interface_entity_reader_form extends interface_entity_form {
    
    protected $cb;
    
    protected $num_statut;
    
    protected function get_js_script_error_label() {
        global $msg;
        
        return $msg[65];
    }
    
    protected function get_js_script_check_fields() {
        global $msg, $empr_birthdate_optional;
        
        $js_script = '';
        if(isset($this->field_focus) && $this->field_focus) {
            $js_script.= "
			if(form.".$this->field_focus.".value.replace(/^\s+|\s+$/g, '').length == 0) {
				alert('".addslashes($this->get_js_script_error_label())."');
				document.forms['".$this->name."'].elements['".$this->field_focus."'].focus();
				return false;
			}
			";
        }
        if ($empr_birthdate_optional == 0) {
            $js_script.= "
			if(form.form_year.value.replace(/^\s+|\s+$/g,'').length == 0) {
				alert(\"$msg[762]\");
				form.form_year.focus();
				return false;
			}";
        }
        $js_script .= "
		if(false == check_new_password() ) {
			alert('".$msg['circ_empr_password_rules_error']."');
			form.form_empr_password.focus();
			return false;
		}
		";
        
        return $js_script;
    }
    
    protected function get_js_rfid_encode() {
        global $pmb_rfid_activate, $pmb_rfid_serveur_url;
        
        if($this->object_id) {
            if ($pmb_rfid_activate==1 && $pmb_rfid_serveur_url) {
                return "if(script_rfid_encode()==false) return false;";
            }
        }
        return '';
    }
    
    protected function get_js_create_script_call() {
        global $base_path;
        
        if(!$this->object_id) {
            if (file_exists($base_path.'/javascript/empr_create_script.js')) {
                return 'empr_create_script();';
            }
        }
        return '';
    }
    
    protected function get_js_create_script_loader() {
        global $base_path;
        
        if(!$this->object_id) {
            if (file_exists($base_path.'/javascript/empr_create_script.js')) {
                return '<script type="text/javascript" src="javascript/empr_create_script.js"></script>';
            }
        }
        return '';
    }
    
    protected function get_js_function_test_form() {
        $js_function = "
		function test_form(form) {
			".$this->get_js_rfid_encode()."
			";
        $js_function .= $this->get_js_script_check_fields();
        $js_function .= "
			".$this->get_js_create_script_call()."
			unload_off();
			return check_form();
		}";
        return $js_function;
    }
    
    protected function get_js_gridform() {
        global $msg, $pmb_form_empr_editables;
        
        if($pmb_form_empr_editables == 1) {
            return "
			<script type='text/javascript' src='javascript/move_empr.js'></script>
			<script type='text/javascript' src='javascript/move.js'></script>
			<script type='text/javascript'>
				widths=new Array(".$msg['empr_field_widths'].");
				var msg_move_to_absolute_pos='".addslashes($msg['move_to_absolute_pos'])."';
				var msg_move_to_relative_pos='".addslashes($msg['move_to_relative_pos'])."';
				var msg_move_saved_ok='".addslashes($msg['move_saved_ok'])."';
				var msg_move_saved_error='".addslashes($msg['move_saved_error'])."';
				var msg_move_up_tab='".addslashes($msg['move_up_tab'])."';
				var msg_move_down_tab='".addslashes($msg['move_down_tab'])."';
				var msg_move_position_tab='".addslashes($msg['move_position_tab'])."';
				var msg_move_position_absolute_tab='".addslashes($msg['move_position_absolute_tab'])."';
				var msg_move_position_relative_tab='".addslashes($msg['move_position_relative_tab'])."';
				var msg_move_invisible_tab='".addslashes($msg['move_invisible_tab'])."';
				var msg_move_visible_tab='".addslashes($msg['move_visible_tab'])."';
				var msg_move_inside_tab='".addslashes($msg['move_inside_tab'])."';
				var msg_move_save='".addslashes($msg['move_save'])."';
				var msg_move_first_plan='".addslashes($msg['move_first_plan'])."';
				var msg_move_last_plan='".addslashes($msg['move_last_plan'])."';
				var msg_move_first='".addslashes($msg['move_first'])."';
				var msg_move_last='".addslashes($msg['move_last'])."';
				var msg_move_infront='".addslashes($msg['move_infront'])."';
				var msg_move_behind='".addslashes($msg['move_behind'])."';
				var msg_move_up='".addslashes($msg['move_up'])."';
				var msg_move_down='".addslashes($msg['move_down'])."';
				var msg_move_invisible='".addslashes($msg['move_invisible'])."';
				var msg_move_visible='".addslashes($msg['move_visible'])."';
				var msg_move_saved_onglet_state='".addslashes($msg['move_saved_onglet_state'])."';
				var msg_move_open_tab='".addslashes($msg['move_open_tab'])."';
				var msg_move_close_tab='".addslashes($msg['move_close_tab'])."';
                var msg_move_save_backbones='".addslashes($msg['move_save_backbones'])."';
			</script>";
        } elseif($pmb_form_empr_editables == 2) {
            $grid_type = str_replace(array('interface_entity_', '_form'), '', static::class);
            return "
			<script type='text/javascript'>
				require(['dojo/ready', 'apps/pmb/gridform/FormEdit'], function(ready, FormEdit){
				     ready(function(){
				     	new FormEdit('circ', '".$grid_type."');
				     });
				});
			</script>";
        }
    }
    
    protected function get_js_script() {
        global $rfid_script_empr;
        
        $js_script = jscript_unload_question();
        $js_script .= $rfid_script_empr;
        $js_script .= "
			<script type='text/javascript'>
				".$this->get_js_function_test_form()."
				    
			</script>
			<script type='text/javascript'>
				function calculate_type_abts(selectBox) {
					var location_id=selectBox.options[selectBox.selectedIndex].value;
					if(document.getElementById('type_abt')) {
						var options = document.getElementById('type_abt').options;
						if(options.length) {
							for(var i=0; i<options.length; i++) {
								var option = options[i];
								if(option.getAttribute('data-localisations')) {
									var localisations = option.getAttribute('data-localisations').split(',');
									if(parseInt(localisations.indexOf(location_id)) != -1) {
										option.removeAttribute('disabled');
									} else {
										option.setAttribute('disabled', 'disabled');
									}
								} else {
									option.removeAttribute('disabled');
								}
							}
						}
					}
				}
			</script>
			".$this->get_js_create_script_loader()."
			<script type='text/javascript' src='javascript/tablist.js'></script>
			<script type='text/javascript' src='javascript/ajax.js'></script>
			".$this->get_js_gridform();
        return $js_script;
    }
    
    protected function get_editables_buttons() {
        global $msg, $PMBuserid, $pmb_form_empr_editables;
        
        $display = '';
        if ($PMBuserid==1 && $pmb_form_empr_editables==1) {
            $display.="<input type='button' class='bouton_small' value='".$msg["empr_edit_format"]."' onClick=\"expandAll(); move_parse_dom(relative);\" id=\"bt_inedit\"/>";
        } elseif ($PMBuserid==1 && $pmb_form_empr_editables==2) {
            $display.="<input type='button' class='bouton_small' value='".$msg["empr_edit_format"]."' id=\"bt_inedit\"/>";
        }
        if ($pmb_form_empr_editables==1) {
            $display.="<input type='button' class='bouton_small' value=\"".$msg["empr_origin_format"]."\" onClick=\"get_default_pos(); expandAll();  ajax_parse_dom(); if (inedit) move_parse_dom(relative); else initIt();\"/>";
        } elseif ($pmb_form_empr_editables==2) {
            $display.="<input type='button' class='bouton_small' value=\"".$msg["empr_origin_format"]."\" id=\"bt_origin_format\"/>";
        }
        return $display;
    }
    
    protected function get_submit_action() {
        return $this->get_url_base()."?categ=empr_update".(!empty($this->object_id) ? "&id=".$this->object_id : "");
    }
    
    protected function get_display_hidden_fields() {
        global $charset;
        global $groupID, $group_id;
        
        return "
		<input type='hidden' name='form_cb' value='".htmlentities($this->cb, ENT_QUOTES, $charset)."' />
		<input type='hidden' name='groupID' value='$groupID' />
		<input type='hidden' name='group_id' value='$group_id' />";
    }
    
    protected function get_display_actions() {
        global $rfid_program_button;
        
        $display = "
			".$this->get_display_cancel_action()."
			".$this->get_display_submit_action()."
			$rfid_program_button
			".$this->get_display_hidden_fields()."
		";
			return $display;
    }
    
    protected function get_cancel_action() {
        return $this->get_url_base()."?categ=empr_create";
    }
    
    protected function get_display_cancel_action() {
        return $this->get_display_action('btcancel', $this->get_action_cancel_label(), ['function' => 'unload_off();history.go(-1);']);
    }
    
    protected function get_display_submit_action() {
        global $msg;
        
        $html = $this->get_display_action('btsubmit', $this->get_action_save_label(), ['function' => 'empr_form_submit(this);']);
        $html .= "
            <script type='text/javascript'>
    			function empr_form_submit(node) {
                    let empr_password_mail_checked = document.getElementById('form_empr_password_mail')
                    let empr_password_mail_disabled = document.getElementById('form_empr_password_mail')
                    if(empr_password_mail_checked && empr_password_mail_disabled){
                        if(empr_password_mail_checked.checked && !empr_password_mail_disabled.disabled){
                            let isNotValid = confirm('".$msg['circ_empr_send_pwd_validation']."');
                            if(!isNotValid){
                                return false;
                            }
                        }
                    }
                    if (test_form(node.form)) {
                       unload_off();
                       node.form.submit();
                    }
                }
            </script>
        ";
        return $html;
    }
    
    protected function get_categories_selector() {
        global $msg, $pmb_form_empr_editables;
        
        // on récupère le select catégorie
        $requete = "SELECT id_categ_empr, libelle, duree_adhesion FROM empr_categ ORDER BY libelle ";
        $res = pmb_mysql_query($requete);
        $nbr_lignes = pmb_mysql_num_rows($res);
        if($pmb_form_empr_editables==2) {
            $empr_grille_categ="<select id='empr_grille_categ' style='display:none;' backbone='yes'><option value='0' selected='selected' >".$msg['all_categories_empr']."</option>";
        } else {
            $empr_grille_categ="<select id='empr_grille_categ' style='display:none;' onChange=\"get_pos(); expandAll(); if (inedit) move_parse_dom(relative); else initIt();\"><option value='0' selected='selected' >".$msg['all_categories_empr']."</option>";
        }
        for($i=0; $i < $nbr_lignes; $i++) {
            $row = pmb_mysql_fetch_row($res);
            $empr_grille_categ.="<option value='$row[0]'>$row[1]</option>";
        }
        $empr_grille_categ.='</select>';
        return $empr_grille_categ;
    }
    
    protected function get_locations_selector() {
        global $msg, $pmb_lecteurs_localises, $pmb_form_empr_editables;
        
        if ($pmb_lecteurs_localises) {
            if($pmb_form_empr_editables==2) {
				return docs_location::get_html_select(array(0),array('id'=>0,'msg'=>$msg['all_locations_empr']),array('id'=>'empr_grille_location','class'=>'saisie-20em','style'=>'display:none;', 'onChange'=>'if(typeof calculate_type_abts != "undefined") {calculate_type_abts(this);}', 'backbone'=>'yes'));
            } else {
				return docs_location::get_html_select(array(0),array('id'=>0,'msg'=>$msg['all_locations_empr']),array('id'=>'empr_grille_location','class'=>'saisie-20em','style'=>'display:none;', 'onChange'=>'get_pos(); expandAll(); if (inedit) move_parse_dom(relative); else initIt(); if(typeof calculate_type_abts != "undefined") {calculate_type_abts(this);}'));
            }
        } else {
            return "<input type='hidden' id='empr_grille_location' value='0' />";
        }
    }
    
    protected function get_statuses_content_selector() {
        $requete = "SELECT idstatut, statut_libelle FROM empr_statut ORDER BY statut_libelle ";
        $res = pmb_mysql_query($requete);
        $nbr_lignes = pmb_mysql_num_rows($res);
        $statut_content = "";
        for($i=0; $i < $nbr_lignes; $i++) {
            $row = pmb_mysql_fetch_row($res);
            $statut_content .= "<option value='$row[0]'";
            if($row[0] == $this->num_statut) $statut_content .= " selected='selected'";
            $statut_content .= ">$row[1]</option>";
        }
        return $statut_content;
    }
    
    protected function get_display_title() {
        global $msg;
        
        if($this->object_id) {
            return $msg['55'];
        } else {
            // 			si from duplicate alors $msg["empr_duplicate"]
            
            return $msg['15'];
        }
    }
    
    protected function get_display_label() {
        global $msg, $charset;
        
        return "
        <div class='row'>
            <div class='left'>
                <h3>".htmlentities($this->label ?? "", ENT_QUOTES, $charset)."</h3>
            </div>
            <div class='right'>
                <label for='form_statut' class='etiquette'>".htmlentities($msg['empr_statut_menu'], ENT_QUOTES, $charset)."</label>&nbsp;<select id='form_statut' name='form_statut'>".$this->get_statuses_content_selector()."</select>&nbsp;
                ".$this->get_editables_buttons()."
            </div>
        </div>";
    }
    
    protected function get_display_content_form() {
        global $pmb_form_empr_editables;
        
        $display = "
        <div class='row'>
            ".$this->get_categories_selector()."
        </div>
        <div class='row'>
            ".$this->get_locations_selector()."
        </div>
        <div class='row'></div>";
        if ($pmb_form_empr_editables == 2) {
            $display .= "
            <div id='zone-container'>
                ".$this->content_form."
            </div>
            ";
        } else {
            $display .= $this->content_form;
        }
        return $display;
    }
    
    public function get_display($ajax = false) {
        global $current_module;
        global $pmb_form_empr_editables;
        
        $display = $this->get_js_script();
        $display .= "
		<h1>".$this->get_display_title()."</h1>
		<form class='form-".$current_module."' id='".$this->name."' name='".$this->name."'  method='post' action=\"".$this->get_submit_action()."\" onSubmit=\"return false\" ".(!empty($this->enctype) ? "enctype='".$this->enctype."'" : "").">
			".$this->get_display_label()."
			<div class='row'></div>
			<div class='form-contenu'>
                ".$this->get_display_content_form()."
			</div>
			<div class='row'>
				".$this->get_display_actions()."
			</div>
		</form>";
        if(isset($this->table_name) && $this->table_name) {
            $translation = new translation($this->object_id, $this->table_name);
            $display .= $translation->connect($this->name);
        }
        
        if(isset($this->field_focus) && $this->field_focus) {
            $display .= "<script type='text/javascript'>document.forms['".$this->name."'].elements['".$this->field_focus."'].focus();</script>";
        }
        $display .= "<script type='text/javascript'>
			".($pmb_form_empr_editables == 1 ?"get_pos(); ":"")."
		</script>";
        return $display;
    }
    
    public function set_cb($cb) {
        $this->cb = $cb;
        return $this;
    }
    
    public function set_num_statut($num_statut) {
        $this->num_statut = $num_statut;
        return $this;
    }
}