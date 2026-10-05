<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_entity_record_form.class.php,v 1.8.4.3 2025/05/27 13:57:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/entity/interface_entity_form.class.php');

class interface_entity_record_form extends interface_entity_form {
	
	protected $biblio_level = 'm';
	
	protected $hierar_level = '';
	
	protected $code = '';
	
	protected $type_doc = '';
	
	protected $id_bibli = 0;
	protected $id_sug = 0;
	protected $id_demande = 0;

	/**
	 * Permet de savoir si on est en duplication de notice
	 */
	protected $is_duplication = 0;
	
	protected $grid_editables = true;
	
	protected function get_function_name_check_perso() {
		return 'check_perso_form';
	}
	
	protected function get_js_script_check_perso() {
		return "
			if(typeof ".$this->get_function_name_check_perso()." == 'function'){
				var check = ".$this->get_function_name_check_perso()."(form);
				if (check == false) return false;
			}
		";
	}
	
	protected function get_js_script_check_fields() {
		global $pmb_nomenclature_activate;
		
		$js_script = '';
		if ($pmb_nomenclature_activate){
			$js_script.= "
			if(dijit.byId('nomenclature_record_ui_0')) {
				if(!dijit.byId('nomenclature_record_ui_0').check_validate())
					return false;
			}
			";
		}
		if(isset($this->field_focus) && $this->field_focus) {
			$js_script.= "
			if(form.".$this->field_focus.".value.replace(/^\s+|\s+$/g, '').length == 0) {
				alert('".addslashes($this->get_js_script_error_label())."');
				document.forms['".$this->name."'].elements['".$this->field_focus."'].focus();
				return false;
			}
			";
		}
		return $js_script;
	}
	
	protected function get_js_function_test_form() {
	    global $pmb_catalog_verif_js;
		
		$js_function = "
		function test_form(form) {
			if (typeof check_form == 'function') {
				if (!check_form()) {
					return false;
				}
			}";
	       if ($pmb_catalog_verif_js != "") {
				$js_function .= $this->get_js_script_check_perso();
			}
			$js_function .= $this->get_js_script_check_fields();
			$js_function .= "
			return check_form();
		}";
		return $js_function;
	}
	
	protected function get_js_gridform() {
		global $msg, $pmb_form_editables;
		
		if($pmb_form_editables == 1) {
			return "
			<script src='javascript/ajax.js'></script>
			<script src='javascript/move.js'></script>
			<script type='text/javascript'>
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
			</script>";
		} elseif($pmb_form_editables == 2) {
			$grid_type = str_replace(array('interface_entity_', '_form'), '', static::class);
			return "
			<script type='text/javascript'>
				require(['dojo/ready', 'apps/pmb/gridform/FormEdit'], function(ready, FormEdit){
				     ready(function(){
				     	new FormEdit('catalog', '".$grid_type."');
				     });
				});
			</script>";
		}
	}
	
	protected function get_js_form_mapper() {
		if(form_mapper::isMapped('notice')){
			return "
			<!-- dojo manif from expression -->
			<script type='text/javascript'>
				require(['dojo/ready', 'apps/form_mapper/FormMapper', 'dojo/_base/lang'], function(ready, FormMapper, lang){
				     ready(function(){
				     	var formMapper = new FormMapper('notice', 'notice');
				     	window['formMapperCallback'] = lang.hitch(formMapper, formMapper.selectorCallback, 'tu');
				     });
				});
			</script>";
		}
		return "";
	}
	
	protected function get_js_script() {
	    global $pmb_catalog_verif_js, $pmb_use_uniform_title, $base_path, $msg;
		
		$js_script = jscript_unload_question();
		if ($pmb_use_uniform_title) {
			$js_script .= $this->get_js_form_mapper();
		}
		$js_script .= "
			<!-- script de gestion des onglets -->
			<script type='text/javascript' src='./javascript/tabform.js'></script>
			".($pmb_catalog_verif_js!= "" ? "<script type='text/javascript' src='$base_path/javascript/$pmb_catalog_verif_js'></script>":"")."
			<script type='text/javascript'>
				".$this->get_js_function_test_form()."
				function confirm_delete() {
					result = confirm(\"{$msg['confirm_suppr_notice']}\");
		       		if(result) {
		       			unload_off();
		           		document.location = '".$this->get_delete_action()."'
					} 
				}
			</script>
			<script src='javascript/ajax.js'></script>
			".$this->get_js_gridform()."
			<script type='text/javascript'>
				function focus_tit1(){
					var f_tit1 = document.getElementById('f_tit1');
					if (f_tit1) {
						f_tit1.focus();
					}
				}
			</script>
			<script type='text/javascript'>
				document.title='".addslashes($this->document_title)."';
			</script>
			<script type='text/javascript'>
				require(['dojo/ready', 'apps/pmb/form/FormController'], function(ready, FormController){
				     ready(function(){
				     	new FormController();
				     });
				});
			</script>";
		return $js_script;
	}
	
	protected function get_editables_buttons() {
		global $msg, $PMBuserid, $pmb_form_editables;
		
		$display = '';
		if ($PMBuserid==1 && $pmb_form_editables==1) {
			$display.="<input type='button' class='bouton_small' value='".$msg["catal_edit_format"]."' onClick=\"expandAll(); move_parse_dom(relative)\" id=\"bt_inedit\"/><input type='button' class='bouton_small' value='Relatif' onClick=\"expandAll(); move_parse_dom((!relative))\" style=\"display:none\" id=\"bt_swap_relative\"/>";
		} elseif ($PMBuserid==1 && $pmb_form_editables==2) {
			$display.="<input type='button' class='bouton_small' value='".$msg["catal_edit_format"]."' id=\"bt_inedit\"/>";
		}
		if ($pmb_form_editables==1) {
			$display.="<input type='button' class='bouton_small' value=\"".$msg["catal_origin_format"]."\" onClick=\"get_default_pos(); expandAll();  ajax_parse_dom(); if (inedit) move_parse_dom(relative); else initIt();\"/>";
		} elseif ($pmb_form_editables==2) {
			$display.="<input type='button' class='bouton_small' value=\"".$msg["catal_origin_format"]."\" id=\"bt_origin_format\"/>";
		}
		return $display;
	}
	
	protected function get_submit_action() {
		global $current_module,$base_path;
		switch ($current_module) {
			case 'acquisition':
				return $base_path."/acquisition.php?categ=sug&action=upd_notice&id_bibli=".$this->id_bibli."&id_sug=".$this->id_sug.(!empty($this->object_id) ? "&id=".$this->object_id : "");
			case 'demandes':
				return $base_path."/demandes.php?categ=gestion&act=upd_notice&iddemande=".$this->id_demande.(!empty($this->object_id) ? "&id=".$this->object_id : "");
			default:
				return $this->get_url_base()."&categ=update".(!empty($this->object_id) ? "&id=".$this->object_id : "");
		}
				
	}
	
	protected function get_display_hidden_fields() {
		return "
		<input type='hidden' name='b_level' value='".$this->biblio_level."' />
		<input type='hidden' name='h_level' value='".$this->hierar_level."' />
		<input type='hidden' name='is_duplication' value='".$this->is_duplication."' />";
	}
	
	protected function get_display_actions() {
		global $pmb_type_audit, $z3950_accessible;
		
		$display = "
		<div class='left'>
			".$this->get_display_cancel_action()."
			".$this->get_display_submit_action()."
			".($this->object_id ? $this->get_display_replace_action() : "")."
			".($this->object_id && !empty($this->duplicable) ? $this->get_display_duplicate_action() : "")."
			".($pmb_type_audit && $this->object_id ? $this->get_display_audit_action() : "")."
			".($z3950_accessible && $this->object_id ? $this->get_display_z3950_action() : "")."
			".$this->get_display_hidden_fields()."
		</div>
		<div class='right'>
			".($this->object_id ? $this->get_display_delete_action() : "")."
		</div>";
		return $display;
	}
	
	protected function get_cancel_action() {
		global $current_module,$base_path;
		global $notice_parent, $source_type, $source_id;
		
		switch ($current_module) {
			case 'acquisition':
				return $base_path."/acquisition.php?categ=sug&action=modif&id_bibli=".$this->id_bibli."&id_sug=".$this->id_sug;
			case 'demandes':
				return $base_path."/demandes.php?categ=gestion&act=see_dmde&iddemande=".$this->id_demande;
			default:
				if ($this->object_id) {
					return '';
				} else {
					if (!empty($notice_parent) && empty($source_type) && empty($source_id)) {
						return '';
					}
				}
				return parent::get_cancel_action();
		}
	}
	
	protected function get_display_cancel_action() {
		if($this->get_cancel_action()) {
			return "<input type='button' class='bouton' value='".$this->get_action_cancel_label()."' id='btcancel' onClick=\"unload_off();document.location='".$this->get_cancel_action()."'\" />";
		} else {
			return $this->get_display_action('btcancel', $this->get_action_cancel_label(), ['function' => 'unload_off();history.go(-1);']);
		}
	}
	
	protected function get_display_submit_action() {
		return "<input type='button' value='".$this->get_action_save_label()."' class='bouton' id='btsubmit' onClick=\"if (test_form(this.form)) {unload_off(); this.form.submit();}\" />";
	}
	
	protected function get_replace_action() {
		return $this->get_url_base()."&categ=replace&id=".$this->object_id;
	}
	
	protected function get_action_duplicate_label() {
		global $msg;
		return $msg['notice_duplicate_bouton'];
	}
	
	protected function get_duplicate_action() {
		return $this->get_url_base()."&categ=duplicate&id=".$this->object_id;
	}
	
	protected function get_display_audit_action() {
		return audit::get_dialog_button($this->object_id, 1);
	}
	
	protected function get_action_z3950_label() {
		global $msg;
		return $msg['notice_z3950_update_bouton'];
	}
	
	protected function get_z3950_action() {
		return $this->get_url_base()."&categ=z3950&id_notice=".$this->object_id."&isbn=".$this->code;
	}
	
	protected function get_display_z3950_action() {
		return "<input type='button' class='bouton' value='".$this->get_action_z3950_label()."' id='btz3950' onclick='unload_off();document.location=\"".$this->get_z3950_action()."\"' />";
	}
	
	protected function get_delete_action() {
		return $this->get_url_base()."&categ=delete&id=".$this->object_id;
	}
	
	protected function get_options_locations() {
	    $locations = array();
	    $req_loc="select idlocation,location_libelle from docs_location";
	    $res_loc=pmb_mysql_query($req_loc);
	    if (pmb_mysql_num_rows($res_loc)>1) {
	        while (($r=pmb_mysql_fetch_object($res_loc))) {
	            $locations[$r->idlocation] = $r->location_libelle;
	        }
	    }
	    return $locations;
	}
	
	protected function get_selector_location() {
	    global $PMBuserid, $pmb_form_editables, $deflt_docs_location;
		global $msg;
		
		$select_loc="";
		if (($PMBuserid==1 && $pmb_form_editables==1) || ($pmb_form_editables==2)) {
		    $options_locations = $this->get_options_locations();
		    if (!empty($options_locations)) {
		        if($pmb_form_editables==2) {
		            $select_loc .= "<select name='grille_location' id='grille_location' style='display:none' backbone='yes'>\n";
		            $selected = $deflt_docs_location;
		        } else {
		            $select_loc .= "<select name='grille_location' id='grille_location' style='display:none' onChange=\"get_pos(); expandAll(); if (inedit) move_parse_dom(relative); else initIt();\">\n";
		            $selected = 0;
		        }
		        $select_loc .= "<option value='0'>".$msg['all_location']."</option>\n";
		        foreach ($options_locations as $idlocation=>$location_libelle) {
		            
		            $select_loc.="<option value='".$idlocation."' ".($selected == $idlocation ? "selected='selected'" : "").">".$location_libelle."</option>\n";
		        }
		        $select_loc.="</select>\n";
		    }
		}
		return $select_loc;
	}
	
	protected function get_display_content_form() {
	    global $pmb_form_editables;
	    
	    if($pmb_form_editables==2) {
	        $select_doc = new marc_select('doctype', 'typdoc', $this->type_doc, '', '', '', array(array("name"=> "data-form-name", "value"=>"doctype"), array("name"=> "backbone", "value"=>"yes")));
	    } else {
	        $select_doc = new marc_select('doctype', 'typdoc', $this->type_doc, "get_pos(); expandAll(); ajax_parse_dom(); if (inedit) move_parse_dom(relative); else initIt();", '', '', array(array("name"=> "data-form-name", "value"=>"doctype")));
	    }
	    $display = "
        <div class='row'>
	    	".$select_doc->display." ".$this->get_selector_location()."
	    </div>
		<div class='row'>
			<a onclick='expandAll();return false;' href='#'><img border='0' id='expandall' src='".get_url_icon('expand_all.gif')."'></a>
			<a onclick='collapseAll();return false;' href='#'><img border='0' id='collapseall' src='".get_url_icon('collapse_all.gif')."'></a>
		</div>";
	    if($pmb_form_editables == 2) {
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
		global $pmb_form_editables;
		
		$display = $this->get_js_script();
		$display .= "
		<form data-advanced-form='true' class='form-".$current_module."' id='".$this->name."' name='".$this->name."'  method='post' action=\"".$this->get_submit_action()."\" onSubmit=\"return false\" ".(!empty($this->enctype) ? "enctype='".$this->enctype."'" : "").">
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
			".($pmb_form_editables == 1 ?"get_pos(); ":"")."ajax_parse_dom(); focus_tit1(); 
		</script>";
		return $display;
	}
	
	public function set_biblio_level($biblio_level) {
		$this->biblio_level = $biblio_level;
		return $this;
	}
	
	public function set_hierar_level($hierar_level) {
		$this->hierar_level = $hierar_level;
		return $this;
	}
	
	public function set_code($code) {
		$this->code = $code;
		return $this;
	}
	
	public function set_type_doc($type_doc) {
		$this->type_doc = $type_doc;
		return $this;
	}
	
	public function set_id_bibli($id_bibli) {
		$this->id_bibli = $id_bibli;
		return $this;
	}
	
	public function set_id_sug($id_sug) {
		$this->id_sug = $id_sug;
		return $this;
	}
	
	public function set_id_demande($id_demande) {
		$this->id_demande = $id_demande;
		return $this;
	}

	public function set_is_duplication($is_duplication) {
		$this->is_duplication = $is_duplication;
		return $this;
	}
}