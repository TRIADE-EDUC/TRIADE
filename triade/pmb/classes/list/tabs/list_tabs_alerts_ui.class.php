<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_tabs_alerts_ui.class.php,v 1.4 2023/01/19 14:57:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_tabs_alerts_ui extends list_tabs_ui {
	
	protected function _init_tabs() {
		global $current_alert;
		global $pmb_scan_request_activate, $pmb_transferts_actif;
		global $sphinx_active, $pmb_pnb_param_login, $pmb_contribution_area_activate;
		
// 		$this->check_module('message');
		switch ($current_alert) {
			case 'circ':
				$this->check_module('resa');
				$this->check_module('expl_todo');
				$this->check_module('empr');
				$this->check_module('empr_categ');
				if($pmb_scan_request_activate) {
					$this->check_module('scan_request');
				}
				//pour les alertes de transferts
				if ($pmb_transferts_actif && (SESSrights & TRANSFERTS_AUTH)) {
					$this->check_module('transferts');
				}
				break;
			case 'catalog':
				$this->check_module('tag');
				$this->check_module('sugg');
				$this->check_module('serialcirc');
				$this->check_module('bulletinage');
				if ($pmb_contribution_area_activate){
    				$this->check_module('contribution');
				}
				if ($sphinx_active) {
					$this->check_module('sphinx');
				}
				if($pmb_pnb_param_login) {
					$this->check_module('pnb');
				}
				break;
			case 'acquisition':
				$this->check_module('sugg');
				break;
			case 'demandes':
				$this->check_module('demandes');
				break;
			case 'animations':
			    $this->check_module('registrations');
			    break;
			case 'admin':
				$this->check_module('mails_configuration');
				break;
		}
	}
	
	protected function check_module($name) {
		
		$classname = 'alerts_'.$name;
		$this->load_class('/alerts/'.$classname.'.class.php');
		$instance = new $classname();
		$data = $instance->get_data();
		if(!empty($data)) {
			foreach ($data as $tab) {
				$this->set_module_name($tab['module']);
				$this->add_tab($tab['section'], $tab['categ'], $tab['label_code'], $tab['sub'], $tab['url_extra'], $tab['number']);
			}
		}
	}
	
	public function get_display_tab($object) {
		return "<li>
			<a href='".($object->get_section() == 'param_sphinx' ? '#' : $object->get_destination_link())."' target='_parent'>
				".$object->get_label().($object->get_number() ? " (".$object->get_number().")" : "")."
			</a>
		</li>";
	}
	
	public function get_display() {
		$display = '';
		$grouped_objects = $this->get_grouped_objects();
		foreach($grouped_objects as $group_label=>$objects) {
			$display .= "<ul>".$group_label;
			foreach ($objects as $object) {
				$display .= $this->get_display_tab($object);
			}
			$display .= "</ul>";
		}
		return $display;
	}
	
	protected function load_class($file){
		global $base_path;
		global $class_path;
		global $include_path;
		global $javascript_path;
		global $styles_path;
		global $msg,$charset;
		global $current_module;
		
		if(file_exists($class_path.$file)){
			require_once($class_path.$file);
		}else{
			return false;
		}
		return true;
	}
}