<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_query_proc_ui.class.php,v 1.1.2.5 2024/09/14 10:15:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_query_proc_ui extends list_query_ui {
	
    protected static $id_proc;
    
    protected static $proc;
    
    protected $notice_tpl_selector;
    
    public static function get_parameters() {
        if (!isset(static::$parameters)) {
            static::$parameters = new parameters(static::$id_proc,"procs");
        }
        return static::$parameters;
    }
	public static function set_id_proc($id_proc) {
	    static::$id_proc = intval($id_proc);
	    $hp=static::get_parameters();
	    $hp->get_final_query();
	    static::$SQL_query = $hp->final_query;
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
	    $this->set_filter_from_form('notice_tpl');
	    parent::set_filters_from_form();
	}

	protected static function get_proc() {
	    if (!isset(static::$proc)) {
    	    $query = "SELECT idproc, name, requete, comment, proc_notice_tpl, proc_notice_tpl_field FROM procs where idproc='".static::$id_proc."' ";
    	    $result = pmb_mysql_query($query);
    	    static::$proc = pmb_mysql_fetch_object($result);
	    }
	    return static::$proc;
	}
	
	protected function get_spreadsheet_title() {
	    return "Procedure_".static::$id_proc.".xls";
	}
	
	protected function get_display_spreadsheet_title() {
	    $proc = static::get_proc();
	    $this->spreadsheet->write_string(0,0,$proc->name);
	    $this->spreadsheet->write_string(0,1,$proc->comment);
	}
	
	protected function get_display_spreadsheet_cell($object, $property, $row, $col) {
	    $value = strip_tags($this->get_cell_content($object, $property));
	    if (is_numeric($col)) {
	        $this->spreadsheet->write($row,$col, $value);
	    } else {
	        $this->spreadsheet->write_string($row,$col, $value);
	    }
	}
	
	/**
	 * Header de la liste du tableau
	 */
	protected function get_display_html_header_list() {
	    $display = '<tr>';
	    foreach ($this->columns as $column) {
	        if(!empty($column['exportable']) && !empty($this->get_setting('columns', $column['property'], 'exportable'))) {
	            $display .= "<th class='align_left'>".$this->_get_label_cell_header($column['label'])."</th>";
	        }
	    }
	    $display .= '</tr>';
	    
	    return $display;
	}
	
	protected function get_notice_tpl_selector() {
	    if(!isset($this->notice_tpl_selector)) {
	        $proc = static::get_proc();
	        if ($proc->proc_notice_tpl_field) {
	            $this->notice_tpl_selector = notice_tpl_gen::gen_tpl_select($this->objects_type."_notice_tpl",$this->filters['notice_tpl'],'',0,1);
	        }
	    }
	    return $this->notice_tpl_selector;
	}
}