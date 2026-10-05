<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_query_proc_edition_ui.class.php,v 1.1.2.3 2024/09/14 08:09:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_query_proc_edition_ui extends list_query_proc_ui {
	
	/**
	 * Affichage du formulaire d'options
	 */
	public function get_options_content_form() {
	    global $msg;
	    
	    $options_content_form = parent::get_options_content_form();
	    if(!isset($this->filters['notice_tpl'])) {
	        $this->filters['notice_tpl'] = 0;
	    }
        if ($this->get_notice_tpl_selector()) {
	        $sel_notice_tpl= "
				<div class='".$this->objects_type."_notice_tpl_content'>
					<div class='colonne3'>
						<div class='row'>
							<label>".$msg['etatperso_export_notice']."</label>
						</div>
						<div class='row'>
							".$this->get_notice_tpl_selector()."
						</div>
					</div>
				</div>";
            $options_content_form .= $sel_notice_tpl;
	    }
	    return $options_content_form;
	}
	
	protected function init_default_settings() {
	    parent::init_default_settings();
	    $this->set_setting_display('search_form', 'options', true);
	    $this->set_setting_display('search_form', 'add_filters', false);
	}

	public function get_export_icons() {
	    global $msg;
	    
	    if($this->get_setting('display', 'search_form', 'export_icons')) {
	        $export_icons = parent::get_export_icons();
	        if ($this->get_notice_tpl_selector()) {
	           $export_icons .= "&nbsp;&nbsp;<img  src='".get_url_icon('texte_ico.gif')."' style='border:0px' class='align_top' onMouseOver ='survol(this);' onclick=\"start_export('EXPORT_NOTI');\" alt='".$msg['etatperso_export_notice']."' title='".$msg['etatperso_export_notice']."'/>";
	        }
	        return $export_icons;
	    }
	    return "";
	}
	
	public function get_error_message_empty_list() {
	    global $msg, $charset;
	    return htmlentities($msg["etatperso_aucuneligne"], ENT_QUOTES, $charset);
	    return '';
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
	    if (is_numeric($value)){
	        $value = "'".$value ;
	    }
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