<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module.class.php,v 1.8 2018-12-20 11:00:19 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/modules/module.tpl.php");

/**
 * class module
 * Un module
 */
class module {
	
	protected $object_id;
	
	protected $url_base = '';
	
	protected $sub_tabs;
	
	public function construct() {
		
	}
	
	public function proceed(){
		global $categ;
		global $module_layout_end;
		
		if($categ && method_exists($this, "proceed_".$categ)) {
			$method_name = "proceed_".$categ;
			$this->{$method_name}();
		} else {
			$layout_template = $this->get_layout_template();
			$layout_template = str_replace("!!menu_contextuel!!", "", $layout_template);
			print str_replace("!!menu_sous_rub!!","",$layout_template);
		}
		print $module_layout_end;
	}
	
	public function get_sub_tab($sub, $label, $url_extra='') {
		global $msg;
		
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
	
	public function get_layout_template() {
		global $module_layout;
	
		$layout_template = str_replace("!!left_menu!!", $this->get_left_menu(),$module_layout);
		return $layout_template;
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
	
} // end of module