<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_loans_groups_reader_ui.class.php,v 1.3.2.2 2024/12/30 15:29:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use PhpOffice\PhpSpreadsheet\Style\Fill;

class list_opac_loans_groups_reader_ui extends list_opac_loans_groups_ui {
    
	protected static $id_group = 0;
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
	    if(empty($this->objects_type)) {
	        $this->objects_type = str_replace('list_', '', get_class($this)).'_'.static::$id_group;
	    }
	    parent::__construct($filters, $pager, $applied_sort);
	}
	
    protected function get_title() {
    	global $msg, $lvl;
    	
    	if(!empty(static::$id_group)) {
    		$query = 'SELECT libelle_groupe FROM groupe WHERE id_groupe = '.static::$id_group;
    		$libelle_groupe = pmb_mysql_result(pmb_mysql_query($query), 0, 'libelle_groupe');
    		if ($lvl == 'late'){
    			return "<h3><span>".sprintf($msg['empr_group_late'], $libelle_groupe)."</h3></span>";
    		} else {
    			return "<h3><span>".sprintf($msg['empr_group_loans'], $libelle_groupe)."</h3></span>";
    		}
    	} else {
    		return parent::get_title();
    	}
    }
    
    protected function init_default_pager() {
        parent::init_default_pager();
        $this->pager['all_on_page'] = true;
    }
    
    protected function init_default_settings() {
    	parent::init_default_settings();
    	$this->set_setting_display('pager', 'visible', false);
    }
    
    protected function get_spreadsheet_title() {
        return "empr.xls";
    }
    
    protected function get_display_spreadsheet_title() {
        global $msg, $lvl;
        
        if(!empty(static::$id_group)) {
            $heading_blue = array(
                'fill' => array(
                    'type' => Fill::FILL_SOLID,
                    'color' => array('rgb' => '00CCFF')
                )
            );
            $query = 'SELECT libelle_groupe FROM groupe WHERE id_groupe = '.static::$id_group;
            $libelle_groupe = pmb_mysql_result(pmb_mysql_query($query), 0, 'libelle_groupe');
            if ($lvl == 'late'){
                $this->spreadsheet->write_string($this->spreadsheet_line,0,sprintf($msg['empr_group_late'], $libelle_groupe),$heading_blue);
            } else {
                $this->spreadsheet->write_string($this->spreadsheet_line,0,sprintf($msg['empr_group_loans'], $libelle_groupe),$heading_blue);
            }
        } else {
            parent::get_display_spreadsheet_title();
        }
    }
    
    public function get_display_spreadsheet_list() {
        global $base_path, $worksheet, $line;
        
        if (empty($this->spreadsheet)) {
            $this->spreadsheet = new spreadsheetPMB();
        }
        $this->get_display_spreadsheet_title();
        if(!empty($this->spreadsheet_line) && $this->spreadsheet_line == 2) {
            $this->spreadsheet_line = 4;
        }
        $this->get_display_spreadsheet_header_list();
        if(count($this->objects)) {
            $uniqid = PHP_log::prepare_time($this->objects_type);
            $this->get_display_spreadsheet_content_list();
            PHP_log::register($uniqid);
        }
        if(file_exists($base_path."/empr/all_extended.inc.php")) {
            $worksheet = $this->spreadsheet;
            $line = $this->spreadsheet_line;
            require_once($base_path."/empr/all_extended.inc.php");
            $this->spreadsheet_line = $line;
            $this->spreadsheet = $worksheet;
        }
        $this->spreadsheet->download($this->get_spreadsheet_title());
    }
    
    public static function set_id_group($id_group) {
    	static::$id_group = intval($id_group);
    }
    
    public static function get_controller_url_base() {
    	global $base_path;
    	
    	return $base_path.'/empr.php?tab=loan_reza&lvl=all&id_groupe=' .static::$id_group;
    	
	}
}