<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_collstate_ui.class.php,v 1.2.2.1.2.1 2025/02/20 09:18:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_opac_collstate_ui extends list_opac_ui {
		
	protected function _get_query_base() {
		global $pmb_sur_location_activate;
		global $opac_view_filter_class;
		
		$query = 'SELECT collstate_id FROM collections_state 
            JOIN arch_statut ON archstatut_id = collstate_statut
        ';
		if($this->filters['bulletin_id']) {
			$query .= "JOIN collstate_bulletins ON collstate_bulletins_num_collstate = collstate_id ";
		}
		if($opac_view_filter_class){
		    if(!$opac_view_filter_class->params["nav_collections"]){
		        $opac_view_filter_class->params["nav_collections"][0]="0";
		    }
		    $query .= "JOIN docs_location ON location_id=idlocation and idlocation in (". implode(",",$opac_view_filter_class->params["nav_collections"]).") ";
		} else {
		    $query .= "LEFT JOIN docs_location ON location_id = idlocation ";
		}
		if ($pmb_sur_location_activate) {
			$query .= "LEFT JOIN sur_location on docs_location.surloc_num=sur_location.surloc_id ";
		}
		$query .= " LEFT JOIN arch_emplacement ON collstate_emplacement=archempla_id ";
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new collstate($row->collstate_id);
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
	
		$this->filters = array(
			'location' => 0,
			'serial_id' => 0,
			'bulletin_id' => 0
				
		);
		parent::init_filters($filters);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		global $pmb_sur_location_activate;
		global $pmb_collstate_advanced;
		
		$this->available_columns = 
		array('main_fields' =>
			array(
					'location_libelle' => 'collstate_form_localisation',
					'emplacement_libelle' => 'collstate_form_emplacement',
					'cote' => 'collstate_form_cote',
					'type_libelle' => 'collstate_form_support',
					'statut_opac_libelle' => 'collstate_form_statut',
					'origine' => 'collstate_form_origine',
					'state_collections' => 'collstate_form_collections',
					'archive' => 'collstate_form_archive',
					'lacune' => 'collstate_form_lacune'
			)
		);
		if ($pmb_sur_location_activate) {
			$this->available_columns['main_fields']['surloc_libelle'] = 'collstate_form_surloc';
		}
		if($pmb_collstate_advanced) {
			$this->available_columns['main_fields']['linked_bulletins'] = 'collstate_linked_bulletins_list';
		}
		$this->available_columns['custom_fields'] = array();
		$this->add_custom_fields_available_columns('collstate', 'id');
	}
	
	protected function init_default_columns() {
		global $msg;
		global $opac_collstate_data;
		global $pmb_sur_location_activate;
		global $pmb_etat_collections_localise;
		global $pmb_collstate_advanced;
		
		if($opac_collstate_data) {
		    if (strstr($opac_collstate_data, "#")) {
				$cp=new parametres_perso("collstate");
			}
			$colonnesarray=explode(",",$opac_collstate_data);
			for ($i=0; $i<count($colonnesarray); $i++) {
				if (substr($colonnesarray[$i],0,1)=="#") {
					//champs personnalisés
					$id=substr($colonnesarray[$i],1);
					if (!$cp->no_special_fields) {
						$this->add_column($cp->t_fields[substr($colonnesarray[$i],1)]['NAME'], $cp->t_fields[$id]["TITRE"]);
					}
				}else{
				    $colencours = isset($msg['collstate_header_' . $colonnesarray[$i]]) ? $msg['collstate_header_' . $colonnesarray[$i]] : '';
				    $this->add_column($colonnesarray[$i], $colencours);
				}
			}
		} else {
			if ($pmb_sur_location_activate) {
				$this->add_column('surloc_libelle');
			}
			if($pmb_etat_collections_localise && $this->filters['location']==0) {
				$this->add_column('location_libelle');
			}
			$this->add_column('emplacement_libelle');
			$this->add_column('cote');
			$this->add_column('type_libelle');
			$this->add_column('statut_opac_libelle');
			$this->add_column('origine');
			$this->add_column('state_collections');
			$this->add_column('archive');
			$this->add_column('lacune');
		}
		if($pmb_collstate_advanced) {
			$this->add_column('linked_bulletins');
		}
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_display('pager', 'visible', false);
		$this->set_setting_column('default', 'align', 'left');
	}
	
	protected function init_default_applied_sort() {
		global $pmb_sur_location_activate, $pmb_etat_collections_localise;
		
		if ($pmb_sur_location_activate) {
			$this->add_applied_sort('surloc_libelle');
		}
		if($pmb_etat_collections_localise) {
			$this->add_applied_sort('location_libelle');
		}
		$this->add_applied_sort('emplacement_libelle');
		$this->add_applied_sort('cote');
	}
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
		global $pmb_sur_location_activate;
		global $pmb_etat_collections_localise;
		
		if(!empty($this->applied_sort[0]['by'])) {
			$order = '';
			$sort_by = $this->applied_sort[0]['by'];
			switch($sort_by) {
			    case 'cote' :
			    case 'origine' :
			    case 'archive' :
			    case 'lacune' :
			        $order = 'collstate_' . $sort_by;
			        break;
				default :
					$order .= parent::_get_query_order();
					break;
			}
			if($order) {
				return $this->_get_query_order_sql_build($order);
			} else {
				//Tri SQL par défaut
				$this->applied_sort_type = 'SQL';
				$query = " ORDER BY ";
				if ($pmb_sur_location_activate) {
					$query .= "surloc_libelle, ";
				}
				if($pmb_etat_collections_localise) {
					$query .= "location_libelle, ";
				}
				$query .= "archempla_libelle, collstate_cote ";
				return $query;
			}
		}
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	public function get_display_search_form() {
		return '';
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
	    $this->set_filter_from_form('location', 'integer');
		parent::set_filters_from_form();
	}
	
	protected function _add_query_filters() {
	    
	    $this->query_filters [] = "((archstatut_visible_opac=1 and archstatut_visible_opac_abon=0)".( $_SESSION["user_code"]? " or (archstatut_visible_opac_abon=1 and archstatut_visible_opac=1)" : "").") ";

		$this->_add_query_filter_simple_restriction('location', 'location_id', 'integer');
		if($this->filters['bulletin_id']) {
			$this->query_filters [] = "collstate_bulletins_num_bulletin='".$this->filters['bulletin_id']."'";
		} else {
			$this->query_filters [] = "id_serial='".$this->filters['serial_id']."'";
		}
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		global $opac_url_base;
		
		$content = '';
		switch($property) {
		    case 'lacune':
		        $content .= str_replace("\n","<br />", $object->lacune);
		        break;
		    case 'state_collections':
		        $content .= str_replace("\n","<br />", $object->state_collections);
		        break;
			case 'location_libelle':
			    if ($object->num_infopage) {
			        $link = $opac_url_base."index.php?lvl=infopages&pagesid=".$object->num_infopage."&location=".$object->location_id;
			        if ($object->surloc_id) {
			            $link .= "&surloc=".$object->surloc_id;
			        }
			        $content .= "<a href=\"".$link."\" title=\"".$msg['location_more_info']."\">".$object->location_libelle."</a>";
			    } else {
			        $content .= $object->location_libelle;
			    }
				break;
			case 'linked_bulletins':
			    $link = $opac_url_base.'index.php?lvl=collstate_bulletins_display'.($object->id ? '&id='.$object->id : '');
			    if ($this->filters['serial_id']) {
			        $link .= '&serial_id='.$this->filters['serial_id'];
			    }
			    if ($this->filters['bulletin_id']) {
			        $link .= '&bulletin_id='.$this->filters['bulletin_id'];
			    }
			    $content .= $this->get_interface_button($msg["collstate_linked_bulletins_list_link"], ['location' => $link]);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_class_objects_list() {
	    return "exemplaires etatcoll ".parent::get_class_objects_list();
	}
	
	protected function _get_class_cell_header($name) {
	    return parent::_get_class_cell_header($name)." collstate_header_".$name;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		$attributes = array(
				'class' => $property
		);
		return $attributes;
	}
	
	protected function _cell_is_sortable($name) {
	    return false;
	}
	
	protected function get_js_sort_script_sort() {
	    return '';
	}
	
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg["collstate_no_collstate"], ENT_QUOTES, $charset);
	}
	
	public static function get_controller_url_base() {
		global $base_path;
	
		return $base_path.'/index.php?lvl=collstate_bulletins_display';
	}
	
	public static function get_ajax_controller_url_base() {
	    global $base_path;
	    return $base_path.'/ajax.php?module=ajax&categ=collstate';
	}
}