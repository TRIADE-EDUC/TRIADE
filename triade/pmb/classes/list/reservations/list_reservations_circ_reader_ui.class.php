<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_reservations_circ_reader_ui.class.php,v 1.16.4.1 2025/02/20 09:18:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_reservations_circ_reader_ui extends list_reservations_circ_ui {
	
	protected $flag_resa_confirme = false;
	
	protected static $info_gestion = LECTEUR_INFO_GESTION;
	
	protected function get_object_instance($row) {
		$resa = new reservation($row->resa_idempr, $row->resa_idnotice, $row->resa_idbulletin, $row->resa_cb);
		$resa->set_on_empr_fiche(true);
		$resa->get_resa_cb();
		if($resa->confirmee) {
			$this->flag_resa_confirme = true;
		}
		return $resa;
	}
	
	protected function add_object($row) {
		$no_aff=0;
		if(!($this->filters['id_notice'] || $this->filters['id_bulletin']))
			if($this->filters['removal_location'] && !$this->filters['id_empr'] && $row->resa_cb && $row->resa_confirmee){
				// Dans la liste des résa à traiter, on n'affiche pas la résa qui a été affecté par un autre site
				$query = "SELECT expl_location FROM exemplaires WHERE expl_cb='".$row->resa_cb."' ";
				$res = @pmb_mysql_query($query);
				if(($data_expl = pmb_mysql_fetch_array($res))){
					if($data_expl['expl_location']!=$this->filters['removal_location']) {
						$no_aff=1;
					}
				}
		}
		if(!$no_aff || ($this->filters['id_notice'] || $this->filters['id_bulletin'])) {
			if($this->filters['id_empr']) {
				$this->filters['removal_location']=0;
			}
			$empr_location = emprunteur::get_location($row->resa_idempr)->id;
			if($this->is_visible_object($empr_location, $row)) {
				$this->objects[] = $this->get_object_instance($row);
				$this->location_reservations[$row->id_resa] = $empr_location;
			}
		}
	}
	
    protected function init_default_selected_filters() {
        $this->selected_filters = array();
    }
    
	protected function init_default_columns() {
		global $pmb_resa_planning;
		global $pmb_transferts_actif;
		
		$this->add_column('record');
		$this->add_column('expl_cote');
		$this->add_column('rank');
		$this->add_column('resa_date');
		$this->add_column('resa_condition');
		if ($pmb_resa_planning) {
			$this->add_column('resa_date_debut');
		}
		$this->add_column('resa_date_fin');
		$this->add_column('resa_confirmee');
		if ($pmb_transferts_actif=="1") {
			$this->add_column('resa_loc_retrait');
		}
		$this->add_column('resa_delete', 'resa_suppr_th');
		$this->add_column_selection(); //Selection resa_confirmee
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
	
	protected function get_cell_content($object, $property) {
		global $msg;
		global $transferts_choix_lieu_opac;
		
		$content = '';
	    switch($property) {
	        case 'resa_loc_retrait':
	        	if (($transferts_choix_lieu_opac=="1")&&($object->date_fin == "")) {
	        		//choix du lieu de retrait
	        		$rqt = "SELECT idlocation, location_libelle FROM docs_location ORDER BY location_libelle";
	        		$res_loc = pmb_mysql_query($rqt);
	        		$liste_loc = "";
	        		while($value = pmb_mysql_fetch_object($res_loc)) {
	        			$liste_loc .= "<option value='".$value->idlocation."'";
	        			if ($value->idlocation == $object->loc_retrait)
	        				$liste_loc .= " selected";
	        				$liste_loc .= ">" . $value->location_libelle . "</option>";
	        		}
	        		$content .= str_replace("!!liste_loc!!",$liste_loc,"<select onchange=\"chgLocRetrait(" . $object->id . ", this.options[this.selectedIndex].value)\">!!liste_loc!!</select>");
	        	} else {
	        		//on affiche le lieu de retrait
	        		$content .= parent::get_cell_content($object, $property);
	        	}
	        	break;
	        case 'resa_confirmee':
	        	if($object->confirmee) {
	        		$content .= "<span style='color:red'>X</span>";
	        	}
	        	break;
	        case 'resa_delete':
                $location = "./circ.php?categ=pret&sub=suppr_resa_from_fiche&action=suppr_resa&suppr_id_resa[]=".$object->id."&id_empr=".$object->id_empr;
                $content .= $this->get_interface_button($msg['raz'], ['location' => $location], ['id' => 'suppr_resa', 'name' => 'suppr_resa']);
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
	
	protected function get_name_selection_objects() {
	    return "ids_resa";
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		
		$this->selection_actions = array();
		if($this->flag_resa_confirme) {
			$do_pret_link = array(
					'href' => static::get_controller_url_base()."&sub=do_pret_resa&id_empr=".$this->filters['id_empr'],
					'confirm' => ''
			);
			$this->add_selection_action('do_pret', $msg['empr_do_pret_resa'], '', $do_pret_link);
		}
	}
	
	protected function get_name_selected_objects() {
		return "ids_resa";
	}
	
	protected static function get_name_selected_objects_from_form() {
		return "ids_resa";
	}
	
	protected function get_js_sort_script_sort() {
		return "<script type='text/javascript' src='./javascript/sorttable.js'></script>";	
	}
	
	protected function _cell_is_sortable($name) {
		return false;
	}
	
	protected function get_uid_objects_list() {
		return $this->objects_type."_".$this->filters['id_empr']."_list";
	}
	
	protected function get_class_objects_list() {
		return parent::get_class_objects_list()." sortable";
	}
	
	public static function get_controller_url_base() {
		global $base_path;
	
		return $base_path.'/circ.php?categ=pret';
	}
}