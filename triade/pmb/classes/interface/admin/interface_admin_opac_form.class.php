<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_admin_opac_form.class.php,v 1.3 2022/06/10 06:22:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/admin/interface_admin_form.class.php');

class interface_admin_opac_form extends interface_admin_form {
	
	protected $id_view;
	
	protected function get_action_save_label() {
		global $sub, $msg;
		
		switch ($sub) {
			case 'opac_view':
				return $msg['opac_view_form_save'];
			default:
				return parent::get_action_save_label();
		}
	}
	
	protected function get_submit_action() {
		global $sub, $section;
		
		switch ($sub) {
			case 'opac_view':
				return $this->get_url_base()."&section=list&action=save&opac_view_id=".$this->object_id;
			default:
				switch ($section) {
					case 'colonne':
						return $this->get_url_base()."&section=view_gestion&act=save_col&id_view=".$this->id_view."&id_col=".$this->object_id;
					case 'view_gestion':
						return $this->get_url_base()."&section=view_list&act=save_view&id_view=".$this->id_view;
					case 'query':
						return $this->get_url_base()."&section=view_list&act=save_request&id_view=".$this->id_view."&id_req=".$this->object_id;
					default:
						return $this->get_url_base();
				}
		}
	}
	
	protected function get_delete_action() {
		global $sub, $section;
		
		switch ($sub) {
			case 'opac_view':
				return $this->get_url_base()."&section=list&action=delete&opac_view_id=".$this->object_id;
			default:
				switch ($section) {
					case 'colonne':
						return $this->get_url_base()."&section=view_gestion&act=suppr_col&id_view=".$this->id_view."&id_col=".$this->object_id;
					case 'view_gestion':
						return $this->get_url_base()."&section=view_list&act=suppr_view&id_view=".$this->object_id;
					case 'query':
						return $this->get_url_base()."&section=view_list&act=suppr_request&id_view=".$this->id_view."&id_req=".$this->object_id;
					default:
						return $this->get_url_base();
				}
		}
	}
	
	protected function get_action_cancel_label() {
		global $sub, $msg;
		
		switch ($sub) {
			case 'opac_view':
				return $msg['opac_view_form_annuler'];
			default:
				return parent::get_action_cancel_label();
		}
	}
	
	protected function get_cancel_action() {
		global $sub, $section;
		
		switch ($sub) {
			case 'opac_view':
				return $this->get_url_base()."&section=list";
			default:
				switch ($section) {
					case 'colonne':
						return $this->get_url_base()."&section=view_gestion&act=update_view&id_view=".$this->id_view;
					case 'view_gestion':
						return $this->get_url_base()."&section=view_list";
					case 'query':
						return $this->get_url_base()."&section=view_list";
					default:
						return $this->get_url_base();
				}
		}
	}
	
	protected function get_js_script_error_label() {
		global $sub, $msg;
		
		switch ($sub) {
			case 'opac_view':
				return $msg['opac_view_form_name_empty'];
			default:
				return $msg['stat_field_not_filled'];
		}
	}
	
	protected function get_js_script() {
		global $sub, $section;
	
		if(isset($this->field_focus) && $this->field_focus) {
			switch ($sub) {
				case 'opac_view':
					return parent::get_js_script();
				default:
					switch ($section) {
						case 'colonne':
							return "
							<script type='text/javascript'>
								if(typeof test_form == 'undefined') {
									function test_form(form) {
										if(form.col_name.value.length == 0 || form.expr_col.value.length == 0){
											alert('".addslashes($this->get_js_script_error_label())."');
											document.forms['".$this->name."'].elements['".$this->field_focus."'].focus();
											return false;
										}
										return true;
									}
								}
								</script>
							";
						case 'view_gestion':
							return parent::get_js_script();
						case 'query':
							return "
							<script type='text/javascript'>
								if(typeof test_form == 'undefined') {
									function test_form(form) {
										if(form.f_request_name.value.length == 0 || form.f_request_code.value.length == 0){
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
			}
		}
		return "";
	}
	
	public function set_id_view($id_view) {
		$this->id_view = intval($id_view);
		return $this;
	}
}