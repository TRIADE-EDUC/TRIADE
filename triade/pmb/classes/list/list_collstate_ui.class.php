<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_collstate_ui.class.php,v 1.2 2018-11-09 14:45:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/list_ui.class.php");
require_once($class_path."/collstate.class.php");
require_once($class_path."/parametres_perso.class.php");

class list_collstate_ui extends list_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
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
	
	protected function add_object($row) {
		$this->objects[] = new collstate($row->collstate_id);
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
					eval ("\$colencours=\$msg['collstate_header_".$colonnesarray[$i]."'];");
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
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
		global $pmb_sur_location_activate;
		global $pmb_etat_collections_localise;
		
		if($this->applied_sort['by']) {
			$order = '';
			$sort_by = $this->applied_sort['by'];
			switch($sort_by) {
				default :
					$order .= parent::_get_query_order();
					break;
			}
			if($order) {
				$this->applied_sort_type = 'SQL';
				return " order by ".$order." ".$this->applied_sort['asc_desc'];
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
		
		$this->pager = array(
				'page' => 1,
				'nb_per_page' => $nb_per_page_a_search,
				'nb_results' => 0,
				'nb_page' => 1
		);
	}
	
	protected function get_form_title() {
		return '';	
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
// 		$this->is_displayed_options_block = true;
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
		
	public function get_export_icons() {
		return "";
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		global $pmb_droits_explr_localises;
		global $explr_invisible;
		
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();

		if (($pmb_droits_explr_localises)&&($explr_invisible)) {
			$filters[] = "location_id not in (".$explr_invisible.")";
		}
		if($this->filters['location']) {
			$filters[] = 'location_id = "'.$this->filters['location'].'"';
		}
		if($this->filters['bulletin_id']) {
			$filters[] = "collstate_bulletins_num_bulletin='".$this->filters['bulletin_id']."'";
		} else {
			$filters[] = "id_serial='".$this->filters['serial_id']."'";
		}
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);		
		}
		return $filter_query;
	}
	
	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'collections_state', $display);
		$display = str_replace('!!sub!!', '', $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		
		$content = '';
		switch($property) {
			case 'surloc':
			case 'location':
			case 'emplacement':
				$content .= $object->{$property."_libelle"};
				break;
			case 'support':
				$content .= $object->type_libelle;
				break;
			case 'statut':
				$content .= "<span class='".$object->statut_class_html."'  style='margin-right: 3px;'><img src='".get_url_icon('spacer.gif')."' width='10' height='10' /></span>".$object->statut_gestion_libelle;
				break;
			case 'collections':
				$content .= $object->state_collections;
				break;
			case 'linked_bulletins':
				$content .= "<input type='button' class='bouton' value='".$msg["collstate_linked_bulletins_list_link"]."' onclick=\"document.location='".static::get_controller_url_base()."&sub=collstate_bulletins_list&id=".$object->id."&serial_id=".$this->filters['serial_id']."&bulletin_id=".$this->filters['bulletin_id']."'\">";
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_display_cell($object, $property) {
		if($object->explr_acces_autorise=="MODIF" && $property != 'linked_bulletins') {
			$display = "<td class='".$property."' onclick=\"window.location='".static::get_controller_url_base()."&sub=collstate_form&id=".$object->id."&serial_id=".$this->filters['serial_id']."&bulletin_id=".$this->filters['bulletin_id']."'\" style='cursor:pointer;'>".$this->get_cell_content($object, $property)."</td>";
		} else {
			$display = "<td class='".$property."'>".$this->get_cell_content($object, $property)."</td>";
		}
		return $display;
	}
	
	/**
	 * Affiche la recherche + la liste
	 */
	public function get_display_list() {
		global $msg, $charset;
		global $base_path;
	
		// Affichage du formulaire de recherche
		// Conservé dans le DOM pour le tri Ajax
		$display = "<style>#collstate_ui_search_form {display:none;}</style>";
		$display .= $this->get_search_form();
		
		//Récupération du script JS de tris
		$display .= $this->get_js_sort_script_sort();
		$display .= "<form action='".static::get_controller_url_base()."&sub=view&serial_id=".$this->filters['serial_id']."&view=collstate' method='post' name='filter_form'>
			<input type='hidden' name='location' value='".$this->filters['location']."'/>";
		//Affichage de la liste des objets
		if(count($this->objects)) {
			$display .= "<table class='exemplaires' id='".$this->objects_type."_list'>";
			$display .= $this->get_display_header_list();
			$display .= $this->get_display_content_list();
			$display .= "</table>";
		} else {
			$display .= $this->get_display_no_results();
		}
		$display .= "</form>";
		return $display;
	}
	
	protected function get_display_no_results() {
		global $msg;

		return $msg["collstate_no_collstate"];
	}
	
	public function get_collstate_pagination() {
		return $this->pager();
	}
	
	public static function get_controller_url_base() {
		global $base_path;
	
		return $base_path.'/catalog.php?categ=serials';
	}
}