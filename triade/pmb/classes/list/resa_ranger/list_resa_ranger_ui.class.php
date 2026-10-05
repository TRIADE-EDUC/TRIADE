<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_resa_ranger_ui.class.php,v 1.1.4.3 2025/04/16 15:18:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_resa_ranger_ui extends list_ui {

	protected function _get_query_base() {
		$query = "SELECT resa_cb, expl_id 
            FROM resa_ranger 
            LEFT JOIN exemplaires on resa_cb=expl_cb";
		return $query;
	}

	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'expl_location' => 'transferts_circ_resa_lib_localisation'
				)
		);
		$this->available_filters['custom_fields'] = array();
	}

	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
	    global $deflt_docs_location;
	    global $pmb_lecteurs_localises;

		$this->filters = array();
        if ($pmb_lecteurs_localises){
		        $this->filters['expl_location'] = $deflt_docs_location;
		}
		parent::init_filters($filters);
	}

	protected function init_default_selected_filters() {
	    global $pmb_lecteurs_localises;
	    
	    if ($pmb_lecteurs_localises) {
	        $this->add_selected_filter('expl_location');
	    }
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
	    $this->available_columns =
		array('main_fields' =>
				array(
						'isbd' => 'ISBD',
				)
		);
	}
	
	protected function init_default_columns() {
	    $this->add_column('isbd');
	}

	/**
	 * Initialisation des settings par défaut
	 */
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'unfoldable_filters', false);
		$this->set_setting_display('search_form', 'sorts', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_display('pager', 'visible', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->settings['objects']['default']['display_mode'] = 'expandable_div';
	}

	protected function init_default_pager() {
	    parent::init_default_pager();
	    $this->pager['all_on_page'] = true;
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
	    $this->set_filter_from_form('expl_location', 'integer');
		parent::set_filters_from_form();
	}

	protected function get_selection_query($type) {
	    $query = '';
	    switch ($type) {
	        case 'docs_location':
	            $query = 'SELECT idlocation AS id, location_libelle AS label FROM docs_location ORDER BY label';
	            break;
	    }
	    return $query;
	}

	protected function get_search_filter_expl_location() {
	    global $msg;
	    
	    return $this->get_search_filter_simple_selection($this->get_selection_query('docs_location'), 'expl_location', $msg["all_location"]);
	}

	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
	    global $base_path, $current_module, $msg;
	    global $pmb_lecteurs_localises;
	    
	    $search_form = '';
	    if ($pmb_lecteurs_localises) {
	        //la liste de sélection de la localisation
	        $search_form .= "<form class='form-$current_module' name='check_docranger' action='".$base_path."/circ.php?categ=listeresa&sub=docranger' method='post'>";
	        $search_form .= "<br />".$msg["transferts_circ_resa_lib_localisation"];
	        $search_form .= "<select name='".$this->objects_type."_expl_location' onchange='document.check_docranger.submit();'>";
	        $res = pmb_mysql_query($this->get_selection_query('docs_location'));
	        $search_form .= "<option value='0'>".$msg["all_location"]."</option>";
	        //on parcours la liste des options
	        while ($value = pmb_mysql_fetch_array($res)) {
	            //debut de l'option
	            $search_form .= "<option value='".$value[0]."'";
	            if ($value[0] == $this->filters['expl_location']) {
	                $search_form .= " selected"; //c'est l'option par défaut
	            }
	            $search_form .= ">".$value[1]."</option>";
	        }
	        $search_form .= "</select></form>";
	    }
	    return $search_form;
	}

	protected function _add_query_filters() {
		global $pmb_lecteurs_localises;
		
		if ($pmb_lecteurs_localises) {
    		$this->_add_query_filter_simple_restriction('expl_location', 'expl_location');
		}
	}
	
	public function get_error_message_empty_list() {
	    global $msg;
	    
	    return $msg['resa_liste_docranger_nodoc'];
	}
	
	protected function _get_object_property_isbd($object) {
	    global $msg;
	    
	    if ($object->expl_id) {
	        if($stuff = get_expl_info($object->expl_id)) {
	            $stuff = check_pret($stuff);
	            return print_info($stuff,0,0,0);
	        } else {
	            return $object->resa_cb."&nbsp;: {$msg[395]}";
	        }
	    } else {
	        return $object->resa_cb."&nbsp;: {$msg[395]}";
	    }
	}

	protected function get_cell_content($object, $property) {
		global $msg;

		$content = '';
		switch($property) {
			case 'isbd':
			    if ($object->expl_id) {
			        if($stuff = get_expl_info($object->expl_id)) {
			            $stuff = check_pret($stuff);
			            $content .=  print_info($stuff,0,0,0);
			        } else {
			            $content .=  "<strong>".$object->resa_cb."&nbsp;: {$msg[395]}</strong><br />";
			        }
			    } else {
			        $content .=  "<strong>".$object->resa_cb."&nbsp;: {$msg[395]}</strong><br />";
			    }
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
}