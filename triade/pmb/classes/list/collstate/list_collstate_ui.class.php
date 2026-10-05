<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_collstate_ui.class.php,v 1.4.2.1.2.1 2025/02/20 09:18:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/collstate.class.php");
require_once($class_path."/parametres_perso.class.php");

class list_collstate_ui extends list_ui {
		
	protected function _get_query_base() {
		global $pmb_sur_location_activate;
		
		$query = 'SELECT  collstate_id , location_id FROM collections_state ';
		if($this->filters['bulletin_id']) {
			$query .= "JOIN collstate_bulletins ON collstate_bulletins_num_collstate = collstate_id ";
		}
		$query .= "LEFT JOIN docs_location ON location_id=idlocation ";
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
					'location' => 'collstate_form_localisation',
					'emplacement' => 'collstate_form_emplacement',
					'cote' => 'collstate_form_cote',
					'support' => 'collstate_form_support',
					'statut' => 'collstate_form_statut',
					'origine' => 'collstate_form_origine',
					'collections' => 'collstate_form_collections',
					'archive' => 'collstate_form_archive',
					'lacune' => 'collstate_form_lacune'
			)
		);
		if ($pmb_sur_location_activate) {
			$this->available_columns['main_fields']['surloc'] = 'collstate_surloc';
		}
		if($pmb_collstate_advanced) {
			$this->available_columns['main_fields']['linked_bulletins'] = 'collstate_linked_bulletins_list';
		}
		$this->available_columns['custom_fields'] = array();
		$this->add_custom_fields_available_columns('collstate', 'id');
	}
	
	protected function init_default_columns() {
		global $msg;
		global $pmb_collstate_data;
		global $pmb_sur_location_activate;
		global $pmb_etat_collections_localise;
		global $pmb_collstate_advanced;
		
		if($pmb_collstate_data) {
			if (strstr($pmb_collstate_data, "#")) {
				$cp=new parametres_perso("collstate");
			}
			$colonnesarray=explode(",",$pmb_collstate_data);
			for ($i=0; $i<count($colonnesarray); $i++) {
				if (substr($colonnesarray[$i],0,1)=="#") {
					//champs personnalisés
					$id=substr($colonnesarray[$i],1);
					if (!$cp->no_special_fields) {
						$this->add_column($cp->t_fields[substr($colonnesarray[$i],1)]['NAME'], $cp->t_fields[$id]["TITRE"]);
// 						$collstate_list_header_deb.="<th class='collstate_header_".$colonnesarray[$i]."'>".htmlentities($cp->t_fields[$id]["TITRE"],ENT_QUOTES, $charset)."</th>";
					}
				}else{
				    $colencours = isset($msg['collstate_header_' . $colonnesarray[$i]]) ? $msg['collstate_header_' . $colonnesarray[$i]] : '';
				    $this->add_column($colonnesarray[$i], $colencours);
// 					$collstate_list_header_deb.="<th class='collstate_header_".$colonnesarray[$i]."'>".htmlentities($colencours,ENT_QUOTES, $charset)."</th>";
				}
			}
		} else {
			if ($pmb_sur_location_activate) {
				$this->add_column('surloc');
			}
			if($pmb_etat_collections_localise && $this->filters['location']==0) {
				$this->add_column('location');
			}
			$this->add_column('emplacement');
			$this->add_column('cote');
			$this->add_column('support');
			$this->add_column('statut');
			$this->add_column('origine');
			$this->add_column('collections');
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
	}
	
	protected function init_default_applied_sort() {
		global $pmb_sur_location_activate, $pmb_etat_collections_localise;
		
		if ($pmb_sur_location_activate) {
			$this->add_applied_sort('surloc');
		}
		if($pmb_etat_collections_localise) {
			$this->add_applied_sort('location');
		}
		$this->add_applied_sort('emplacement');
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
			    case 'statut' :
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
		global $nb_per_page_a_search;
		
		parent::init_default_pager();
		$this->pager['nb_per_page'] = $nb_per_page_a_search;
	}
	
	/**
	 * Affichage des filtres du formulaire de recherche
	 */
	public function get_search_filters_form() {
		global $list_collstate_ui_search_filters_form_tpl;
	
		$search_filters_form = $list_collstate_ui_search_filters_form_tpl;
		$search_filters_form = str_replace('!!objects_type!!', $this->objects_type, $search_filters_form);
		return $search_filters_form;
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		$search_form = parent::get_search_form();
		$search_form = str_replace('!!action!!', static::get_controller_url_base()."&sub=view&serial_id=".$this->filters['serial_id']."&view=collstate", $search_form);
		return $search_form;
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$location = $this->objects_type.'_location';
		global ${$location};
		if(isset(${$location}) && ${$location} != '') {
			$this->filters['location'] = ${$location};
		}
		parent::set_filters_from_form();
	}
	
	protected function _add_query_filters() {
		global $pmb_droits_explr_localises;
		global $explr_invisible;
		
		if (($pmb_droits_explr_localises)&&($explr_invisible)) {
			$this->query_filters [] = "location_id not in (".$explr_invisible.")";
		}
		$this->_add_query_filter_simple_restriction('location', 'location_id', 'integer');
		if($this->filters['bulletin_id']) {
			$this->query_filters [] = "collstate_bulletins_num_bulletin='".$this->filters['bulletin_id']."'";
		} else {
			$this->query_filters [] = "id_serial='".$this->filters['serial_id']."'";
		}
	}
	
	protected function _get_object_property_surloc($object) {
		return $object->surloc_libelle;
	}
	
	protected function _get_object_property_location($object) {
		return $object->location_libelle;
	}
	
	protected function _get_object_property_emplacement($object) {
		return $object->emplacement_libelle;
	}
	
	protected function _get_object_property_support($object) {
		return $object->type_libelle;
	}
	
	protected function _get_object_property_collections($object) {
		return $object->state_collections;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		
		$content = '';
		switch($property) {
			case 'statut':
				$content .= "<span class='".$object->statut_class_html."'  style='margin-right: 3px;'><img src='".get_url_icon('spacer.gif')."' style='width:10px; height:10px' alt='' /></span>".$object->statut_gestion_libelle;
				break;
			case 'linked_bulletins':
			    $attrs = [
                    'location' => static::get_controller_url_base()."&sub=collstate_bulletins_list&id=".$object->id."&serial_id=".$this->filters['serial_id']."&bulletin_id=".$this->filters['bulletin_id']
			    ];
			    $content .= $this->get_interface_button($msg["collstate_linked_bulletins_list_link"], $attrs);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		$attributes = array(
				'class' => $property
		);
		if(($object->explr_acces_autorise=="" || $object->explr_acces_autorise=="MODIF") && $property != 'linked_bulletins') {
			$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&sub=collstate_form&id=".$object->id."&serial_id=".$this->filters['serial_id']."&bulletin_id=".$this->filters['bulletin_id']."\"";
		}
		return $attributes;
	}
	
	/**
	 * Affichage de la liste des objets
	 * @return string
	 */
	public function get_display_objects_list() {
		$display = "<form action='".static::get_controller_url_base()."&sub=view&serial_id=".$this->filters['serial_id']."&view=collstate' method='post' name='filter_form'>
			<input type='hidden' name='location' value='".$this->filters['location']."'/>";
		$display .= parent::get_display_objects_list();
		$display .= "</form>";
		return $display;
		
	}
	
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg["collstate_no_collstate"], ENT_QUOTES, $charset);
		return '';
	}
	
	public function get_collstate_pagination() {
		return $this->pager();
	}
	
	public static function get_controller_url_base() {
		global $base_path, $serial_id;
	
		return $base_path.'/catalog.php?categ=serials&sub=view&serial_id='.$serial_id.'&view=collstate';
	}
	
	public static function get_ajax_controller_url_base() {
		global $base_path, $current_module;
		return $base_path.'/ajax.php?module='.$current_module.'&categ=collections_state';
	}
}