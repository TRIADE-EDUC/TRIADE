<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_loans_reader_ui.class.php,v 1.3.2.1 2024/12/30 15:15:45 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use PhpOffice\PhpSpreadsheet\Style\Fill;

class list_opac_loans_reader_ui extends list_opac_loans_ui {
	
	protected function get_title() {
		global $msg, $opac_rgaa_active;
		if($opac_rgaa_active) {
			return '<h2><span>' . $msg['empr_loans'] . '</span> <span id="empr_loans_number">('.count($this->objects).')</span></h2>';
		}
		return '<h3><span>' . $msg['empr_loans'] . '</span> <span id="empr_loans_number">('.count($this->objects).')</span></h3>';
	}
	
	protected function init_default_columns() {
		global $opac_pret_prolongation, $allow_prol, $lvl;
		
		$this->add_column('record');
		$this->add_column('author');
		$this->add_column('typdoc');
		$this->add_column('pret_date');
		$this->add_column('pret_retour');
		if($opac_pret_prolongation==1 && $allow_prol) {
			$this->add_column('nb_prolongation');
			$this->add_column('prolongation');
		}
		if ($lvl!="late") {
			$this->add_column('late');
		}
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	public function init_applied_group($applied_group=array()) {
		$this->applied_group = array(0 => 'expl_location_libelle');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('pager', 'visible', false);
	}
	
	protected function get_cell_group_label($group_label, $indice=0) {
		global $msg;
		
		$content = '';
		switch($this->applied_group[$indice]) {
			case 'expl_location_libelle':
				$content .= $msg["expl_header_location_libelle"]." : ".$group_label;
				break;
			default :
				$content .= parent::get_cell_group_label($group_label, $indice);
				break;
		}
		return $content;
	}
	
	protected function get_display_group_header_list($group_label, $level=1, $uid='') {
		$display = "
		<tr id='".$uid."_group_header' class='tb_pret_location_row'>
			<td class='list_ui_content_list_group list_ui_content_list_group_level_".$level." ".$this->objects_type."_content_list_group ".$this->objects_type."_content_list_group_level_".$level."' colspan='".count($this->columns)."'>
				".$this->get_cell_group_label($group_label, ($level-1))."
			</td>
		</tr>";
		return $display;
	}
	 
	public function get_display_spreadsheet_list() {
	    global $opac_show_group_checkout;
	    global $id_empr;
	    global $base_path, $worksheet, $line;
	    
	    $this->spreadsheet = new spreadsheetPMB();
	    $this->get_display_spreadsheet_title();
	    $this->get_display_spreadsheet_header_list();
	    if(count($this->objects)) {
	        $this->get_display_spreadsheet_content_list();
	    }
	    if($opac_show_group_checkout) {
	        $query = "SELECT * FROM groupe WHERE resp_groupe=$id_empr ORDER BY libelle_groupe";
	        $result = pmb_mysql_query($query);
	        while ($row = pmb_mysql_fetch_object($result)) {
	            list_opac_loans_groups_reader_ui::set_id_group($row->id_groupe);
	            $list_opac_loans_groups_reader_ui = list_opac_loans_groups_reader_ui::get_instance(array('groups' => array($row->id_groupe)));
	            
	            if(count($list_opac_loans_groups_reader_ui->get_objects())) {
	                $this->spreadsheet_line = $this->spreadsheet_line+2;
	                $list_opac_loans_groups_reader_ui->set_spreadsheet($this->spreadsheet);
	                $list_opac_loans_groups_reader_ui->set_spreadsheet_line($this->spreadsheet_line);
	                $list_opac_loans_groups_reader_ui->get_display_spreadsheet_title();
	                $list_opac_loans_groups_reader_ui->add_spreadsheet_line(2);
	                $list_opac_loans_groups_reader_ui->get_display_spreadsheet_header_list();
	                $list_opac_loans_groups_reader_ui->add_spreadsheet_line();
	                $list_opac_loans_groups_reader_ui->get_display_spreadsheet_content_list();
	                
	                $this->spreadsheet = $list_opac_loans_groups_reader_ui->get_spreadsheet();
	                $this->spreadsheet_line = $list_opac_loans_groups_reader_ui->get_spreadsheet_line();
	            }
	        }
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
	
	protected function get_spreadsheet_title() {
	    return "empr.xls";
	}
	
	protected function get_display_spreadsheet_title() {
	    global $msg, $lvl;
	    
	    $heading_blue = array(
	        'fill' => array(
	            'type' => Fill::FILL_SOLID,
	            'color' => array('rgb' => '00CCFF')
	        )
	    );
	    if ($lvl!="late"){
	        $this->spreadsheet->write_string($this->spreadsheet_line,0,$msg["empr_loans"],$heading_blue);
	    } else {
	        $this->spreadsheet->write_string($this->spreadsheet_line,0,$msg["empr_late"],$heading_blue);
	    }
	}
}