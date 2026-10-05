<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_reservations_circ_reader_ui.class.php,v 1.1.4.1 2024/07/17 14:21:39 pmallambic Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_opac_reservations_reader_ui extends list_opac_reservations_ui {
	
	protected function _get_query_base() {
	    global $msg;
	    
	    $query = "SELECT id_resa, resa_idempr, resa_idnotice, resa_idbulletin, resa_date, resa_date_fin, resa_cb, IF(resa_date_fin>=sysdate() or resa_date_fin='0000-00-00',0,1) as perimee, date_format(resa_date_fin, '".$msg["format_date_sql"]."') as aff_date_fin FROM resa";
	    return $query;
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        //On maintient le tri par objet dans ce contexte
	        //La requête SQL de base ne fournit pas assez d'info
	        case 'index_sew':
	        case 'record':
	            return '';
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
    protected function init_default_selected_filters() {
        $this->selected_filters = array();
    }
    
	protected function init_default_columns() {
		$this->add_column('record');
		$this->add_column('rank');
		$this->add_column('resa_delete', 'resa_liste_del');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_display('pager', 'visible', false);
		
		//Oublions le deffered pour l'instant
		$this->set_setting_display('objects_list', 'deffered_load', false);
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	public function get_display_header_list() {
	    global $msg;
	    //Pour garder la rétro-compatibilité avec le legacy
	    if((isset($msg["resa_liste_titre"]) || isset($msg["resa_liste_rank"]) || isset($msg["resa_liste_del"]))) {
	        return parent::get_display_header_list();
	    }
	    return '';
	}
	
	protected function get_class_odd_even($indice) {
	    return ($indice % 2 ? 'even' : 'odd');
	}
	
	protected function is_highlight_activated() {
	    return false;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		
		$content = '';
	    switch($property) {
// 	        case 'resa_confirmee':
// 	        	if($object->confirmee) {
// 	        		$content .= "<span style='color:red'>X</span>";
// 	        	}
// 	        	break;
	        case 'resa_delete':
	            if ($object->id_notice) {
	                if($opac_rgaa_active){
	                    $content .= "<button type='button' onclick='if(confirm(\"".$msg['empr_confirm_delete_resa']."\")){location.href=\"empr.php?tab=loan_reza&lvl=all&delete=1&id_notice=".$object->id_notice."#empr-resa\"}' class='bouton bouton-resa-delete'>".$msg['resa_effacer_resa']."</button>";
	                }else{
	                    $content .= "<a role='button' href='javascript:if(confirm(\"".$msg['empr_confirm_delete_resa']."\")){location.href=\"empr.php?tab=loan_reza&lvl=all&delete=1&id_notice=".$object->id_notice."#empr-resa\"}'>".$msg['resa_effacer_resa']."</a>";
	                }
	            } else {
	                if($opac_rgaa_active){
	                    $content .= "<button type='button' onclick='if(confirm(\"".$msg['empr_confirm_delete_resa']."\")){location.href=\"empr.php?tab=loan_reza&lvl=all&delete=1&id_bulletin=".$object->id_bulletin."#empr-resa\"}' class='bouton bouton-resa-delete'>".$msg['resa_effacer_resa']."</button>";
	                }else{
	                    $content .= "<a role='button' href='javascript:if(confirm(\"".$msg['empr_confirm_delete_resa']."\")){location.href=\"empr.php?tab=loan_reza&lvl=all&delete=1&id_bulletin=".$object->id_bulletin."#empr-resa\"}'>".$msg['resa_effacer_resa']."</a>";
	                }
	            }
	            break;
	        default :
	            $content .= parent::get_cell_content($object, $property);
	            break;
	    }
	    return $content;
	}
	
	public function get_display_search_form() {
		return '';
	}
	
	protected function init_default_selection_actions() {
		$this->selection_actions = array();
	}
	
	protected function _cell_is_sortable($name) {
		return false;
	}
	
	protected function get_uid_objects_list() {
		return $this->objects_type."_".$this->filters['id_empr']."_list";
	}
	
	protected function get_class_objects_list() {
		return parent::get_class_objects_list()." fiche-lecteur";
	}
}