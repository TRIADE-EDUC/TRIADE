<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_resa_planning_reader_ui.class.php,v 1.1.4.1 2024/07/17 14:21:09 pmallambic Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_opac_resa_planning_reader_ui extends list_opac_resa_planning_ui {
	
	protected function get_form_title() {
		global $msg;
		
		return $msg['empr_resa_planning'];
	}
	
    protected function init_default_selected_filters() {
        $this->selected_filters = array();
    }
    
	protected function init_default_columns() {
		$this->add_column('record');
		$this->add_column('resa_dates');
		$this->add_column('resa_qty');
		if ($this->get_locations_number() > 1) {
		    $this->add_column('resa_loc_retrait');
		}
		$this->add_column('resa_delete', 'resa_suppr_th');
	}
	
	protected function init_default_pager() {
	    parent::init_default_pager();
	    $this->pager['all_on_page'] = true;
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('resa_date_debut');
	    $this->add_applied_sort('resa_date_fin');
	}
	
	protected function init_default_settings() {
	    parent::init_default_settings();
	    $this->set_setting_display('pager', 'visible', false);
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		global $opac_rgaa_active;
		
	    $content = '';
	    switch($property) {
	        case 'resa_delete':
	            if($opac_rgaa_active){
	                $content .= '<button type="button" onclick="if(confirm(\''.$msg['empr_confirm_delete_resa_planning'].'\')){location.href=\'empr.php?tab=loan_reza&lvl=resa_planning&delete=1&id_resa_planning='.$object->id_resa.'\'}" class="bouton" >'.$msg['resa_effacer_resa'].'</button>';
	            }else{
	                $content .= '<a role="button" href="javascript:if(confirm(\''.$msg['empr_confirm_delete_resa_planning'].'\')){location.href=\'empr.php?tab=loan_reza&lvl=resa_planning&delete=1&id_resa_planning='.$object->id_resa.'\'}">'.$msg['resa_effacer_resa'].'</a>';
	            }
	            break;
	        default :
	            $content .= parent::get_cell_content($object, $property);
	            break;
	    }
	    return $content;
	}
}