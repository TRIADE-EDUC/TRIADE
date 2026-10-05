<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_loans_archives_reader_ui.class.php,v 1.2 2023/08/31 08:34:55 dgoron Exp $

use PhpOffice\PhpSpreadsheet\Style\Fill;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_opac_loans_archives_reader_ui extends list_opac_loans_archives_ui {
	
    protected function get_title() {
        global $msg, $charset;
        
        return '<h3><span>' . htmlentities($msg['empr_loans_old'], ENT_QUOTES, $charset) . '</span> <span id="empr_loans_old_number">('.count($this->objects).')</span></h3>';
    }
    
	protected function init_default_pager() {
		global $opac_empr_hist_nb_max;
		
		parent::init_default_pager();
		if ($opac_empr_hist_nb_max) {
			$this->pager['nb_per_page'] = $opac_empr_hist_nb_max;
		} else {
			$this->pager['all_on_page'] = true;
		}
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('pager', 'visible', false);
	}
	
	public function get_error_message_empty_list() {
	    global $msg, $charset;
	    return htmlentities($msg['empr_no_loan_old'], ENT_QUOTES, $charset);
	}
	
	protected function get_spreadsheet_title() {
	    return "empr.xls";
	}
	
	protected function get_display_spreadsheet_title() {
	    global $msg;
	    
	    $heading_blue = array(
	        'fill' => array(
	            'type' => Fill::FILL_SOLID,
	            'color' => array('rgb' => '00CCFF')
	        )
	    );
	    $this->spreadsheet->write_string($this->spreadsheet_line,0,$msg["empr_loans_old"],$heading_blue);
	}
}