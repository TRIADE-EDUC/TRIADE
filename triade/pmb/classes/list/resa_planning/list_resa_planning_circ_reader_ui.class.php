<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_resa_planning_circ_reader_ui.class.php,v 1.8.4.1 2025/02/20 09:18:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_resa_planning_circ_reader_ui extends list_resa_planning_circ_ui {
	
    protected function init_default_selected_filters() {
        $this->selected_filters = array();
    }
    
	protected function init_default_columns() {
		$this->add_column('record');
		$this->add_column('resa_date');
		$this->add_column('resa_date_debut');
		$this->add_column('resa_date_fin');
		$this->add_column('resa_qty');
		$this->add_column('resa_validee');
		$this->add_column('resa_confirmee');
		if ($this->get_locations_number() > 1) {
		    $this->add_column('resa_loc_retrait');
		}
		$this->add_column('resa_delete', 'resa_suppr_th');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('record');
	    $this->add_applied_sort('resa_date');
	}
	
	protected function get_cell_content($object, $property) {
	    $content = '';
	    switch($property) {
	        case 'resa_delete':
                $location = "./circ.php?categ=pret&sub=suppr_resa_planning_from_fiche&action=suppr_resa&id_resa=".$object->id_resa."&id_empr=".$object->resa_idempr;
                $content .= $this->get_interface_button('X', ['location' => $location], ['id' => 'resa_supp', 'name' => 'resa_supp']);
	            break;
	        default :
	            $content .= parent::get_cell_content($object, $property);
	            break;
	    }
	    return $content;
	}
	
	/**
	 * Affiche la liste
	 */
	public function get_display_list() {
	    
	    //Récupération du script JS de tris
	    $display = $this->get_js_sort_script_sort();
	    
	    $display .= $this->get_js_func_callback();
	    
	    $display .= $this->pager_top();
	    //Affichage de la liste des objets
	    $display .= $this->get_display_objects_list();
	    $display .= $this->pager_bottom();
	    return $display;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
	
		return $base_path.'/circ.php?categ=pret';
	}
}