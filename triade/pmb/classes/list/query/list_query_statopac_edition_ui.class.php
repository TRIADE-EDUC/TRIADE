<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_query_statopac_edition_ui.class.php,v 1.1.2.2 2024/09/14 10:15:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_query_statopac_edition_ui extends list_query_statopac_ui {
	
	protected function init_default_settings() {
	    parent::init_default_settings();
	    $this->set_setting_display('search_form', 'options', true);
	    $this->set_setting_display('search_form', 'add_filters', false);
	}
	
	public function get_error_message_empty_list() {
	    global $msg, $charset;
	    return htmlentities($msg["etatperso_aucuneligne"], ENT_QUOTES, $charset);
	}
	
	protected function get_title() {
	    global $charset;
	    
	    $proc = static::get_proc();
	    return "<h1>".htmlentities($proc->name, ENT_QUOTES, $charset)."</h1><h2>".htmlentities($proc->comment, ENT_QUOTES, $charset)."</h2>";
	}
	
	protected function get_html_title() {
	    $proc = static::get_proc();
	    return "<h1>".$proc->name."</h1><h2>".$proc->comment."</h2>".static::$SQL_query."<br />";;
	}
	
	protected function get_display_html_cell($object, $property) {
	    $value = strip_tags($this->get_cell_content($object, $property));
	    if(trim($value)=='') {
	        $value = "&nbsp;";
	    }
	    $display = "<td>".$value."</td>";
	    return $display;
	}
	
	public static function get_controller_url_base() {
	    global $force_exec;
	    
	    return parent::get_controller_url_base()."&action=execute&id_proc=".static::$id_proc."&form_type=gen_form&force_exec=".$force_exec;
	}
	
	public static function get_ajax_controller_url_base() {
	    global $force_exec;
	    
	    return parent::get_ajax_controller_url_base()."&id_proc=".static::$id_proc."&form_type=gen_form&force_exec=".$force_exec;
	}
}