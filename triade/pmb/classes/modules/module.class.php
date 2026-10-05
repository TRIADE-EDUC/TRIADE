<?php
use Pmb\Users\Controller\RolesController;

// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module.class.php,v 1.21.2.3.2.1 2025/01/28 07:44:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/tabs/tabs.class.php");
require_once($include_path."/templates/modules/module.tpl.php");

/**
 * class module
 * Un module
 */
class module {
	
	protected $name;
	
	protected $object_id;
	
	protected $url_base = '';
		
	protected $sub_tabs;
	
	protected static $instance;
	
	public function __construct() {
		$this->name = str_replace("module_", "", static::class);
	}
	
	public function proceed_header(){
		global $dest, $include_path, $use_shortcuts, $msg, $charset;
		global $database_window_title, $current_module;
		global $menu_bar, $extra, $extra2, $extra_info;
		global $raclavier;
		global $PMBuserid;
		
		switch($dest) {
			case "TABLEAU":
				break;
			case "TABLEAUHTML":
				header("Content-Type: application/download\n");
				header("Content-Disposition: atttachement; filename=\"tableau.html\"");
				print "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body>";
				break;
			case "TABLEAUCSV":
				// header ("Content-Type: text/html; charset=".$charset);
				header("Content-Type: application/download\n");
				header("Content-Disposition: atachement; filename=\"tableau.csv\"");
				break;
			case "EXPORT_NOTI":
				// header ("Content-Type: text/html; charset=".$charset);
				header("Content-Type: application/download\n");
				header("Content-Disposition: atachement; filename=\"notices.doc\"");
				break;
			case "PLUGIN_FILE": // utiliser pour les plugins
				break;
			default:
				print "<div id='att' style='z-Index:1000'></div>";
				print $menu_bar;
				print $extra;
				print $extra2;
				print $extra_info;
				if($use_shortcuts) {
					if(empty($raclavier)) $raclavier = array();
					$shortcuts = tabs::get_shortcuts();
					if(!empty($shortcuts)) {
						foreach ($shortcuts as $shortcut) {
							$raclavier[] = $shortcut;
						}
					}
					include("$include_path/shortcuts/circ.sht");
				}
				$list_modules_ui = list_modules_ui::get_instance();
				$objects = $list_modules_ui->get_objects();
				foreach ($objects as $object) {
					if($object->get_name() == $current_module) {
						echo window_title($database_window_title.$object->get_label().$msg[1003].$msg[1001]);
						break;
					}
				}
				$layout_template = $this->get_layout_template();
				print str_replace('!!menu_contextuel!!', $this->get_display_subtabs() ?? "", $layout_template);
				break;
		}
		$data = [
		    'module' => $this->name,
		    'categ' => $_GET['categ'] ?? '',
		    'sub' => $_GET['sub'] ?? '',
		    'urlExtra' => '',
		    'userId' => $PMBuserid
		];
		$rolesController = new RolesController();
		if (!$rolesController->checkUserAccess($data)) {
		    error_message($msg['roles'], $msg['role_permission_denied']);
		    die;
		}
		
	}
	
	public function proceed(){
		global $categ;
		
		if($categ && method_exists($this, "proceed_".$categ)) {
			$method_name = "proceed_".$categ;
			$this->{$method_name}();
		}
	}
	
	public function proceed_footer(){
		global $dest, $footer, $module_layout_end;
		switch($dest) {
			case "TABLEAU":
				break;
			case "TABLEAUHTML":
				print $footer;
				break;
			case "TABLEAUCSV":
				break;
			default:
				print $module_layout_end;
				// pied de page
				print $footer;
				break;
		}
	}
	
	public function get_display_tabs() {
		$display = "<div id='menu'>";
		$list_tabs_ui_class_name = "list_tabs_".$this->name."_ui";
		$list_tabs_ui_class_name::set_module_name($this->name);
		$list_tabs_ui = new $list_tabs_ui_class_name();
		$display .= $list_tabs_ui->get_display();
		$plugins = plugins::get_instance();
		$display .= $plugins->get_menu($this->name)."
			<div id='div_alert' class='erreur'></div>
		</div>";
		return $display;
	}
	
	public function get_sub_tab($sub, $label, $url_extra='') {
		return "<span".ongletSelect(substr($this->url_base, strpos($this->url_base, '?')+1)."&sub=".$sub.$url_extra).">
			<a title='".$label."' href='".$this->url_base."&sub=".$sub.$url_extra."'>
				".$label."
			</a>
		</span>";
	}
	
	public function add_sub_tab($sub, $label, $url_extra='') {
		if(!isset($this->sub_tabs)) {
			$this->sub_tabs = array();
		}
		$this->sub_tabs[] = $this->get_sub_tab($sub, $label, $url_extra);
	}
	
	public function get_sub_tabs() {
		global $module_sub_tabs;
		
		$template = '';
		if(isset($this->sub_tabs)) {
			$sub_tabs = '';
			foreach ($this->sub_tabs as $sub_tab) {
				$sub_tabs .= $sub_tab;
			}
			$template .= str_replace('!!sub_tabs!!', $sub_tabs, $module_sub_tabs);
		}
		return $template;	
	}
	
	public function get_left_menu() {
		return $this->get_display_tabs();
	}
	
	public function get_layout_template() {
		global $module_layout;
	
		$layout_template = str_replace("!!left_menu!!", $this->get_left_menu(),$module_layout);
		return $layout_template;
	}
	
	public function get_display_subtabs() {
		global $categ, $sub, $database_window_title, $msg;
		
		if(empty($categ)) {
			return '';
		}
		$display = "";
		$list_subtabs_ui_class_name = "list_subtabs_".$this->name."_ui";
		$list_subtabs_ui_class_name::set_module_name($this->name);
		$list_subtabs_ui_class_name::set_categ($categ);
		$list_subtabs_ui_class_name::set_sub($sub ?? '');
		$list_subtabs_ui = new $list_subtabs_ui_class_name();
		$display .= $list_subtabs_ui->get_display();
		if(!empty($list_subtabs_ui->get_selected_subtab())) {
			echo window_title($database_window_title.$list_subtabs_ui->get_selected_subtab()->get_label().$msg["1003"].$msg["1001"]);
		} else {
			echo window_title($database_window_title.$list_subtabs_ui->get_title().$msg["1003"].$msg["1001"]);
		}
		return $display;
	}
	
	public function set_object_id($object_id) {
		$object_id = intval($object_id);
		$this->object_id = $object_id;
	}
	
	public function set_url_base($url_base) {
		$this->url_base = $url_base;
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
	
	public static function get_instance() {
		$class_name = static::class;
		if(!isset(static::$instance[$class_name])) {
			static::$instance[$class_name] = new $class_name();
		}
		return static::$instance[$class_name];
	}
} // end of module