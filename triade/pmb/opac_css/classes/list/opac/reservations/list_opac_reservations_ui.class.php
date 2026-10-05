<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_reservations_ui.class.php,v 1.1 2024/01/03 14:47:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_opac_reservations_ui extends list_opac_ui {
	
	protected $location_reservations;
	
	protected $no_aff;
	
	protected $resa_loc;
	
	protected $on_expl_location;
	
	protected function _get_query_base() {
		$query = "SELECT id_resa, resa_idempr, resa_idnotice, resa_idbulletin, resa_cb, resa_confirmee, resa_loc_retrait,
			ifnull(expl_cote,'') as expl_cote, expl_cb,
			trim(concat(if(series_m.serie_name <>'', if(notices_m.tnvol <>'', concat(series_m.serie_name,', ',notices_m.tnvol,'. '), concat(series_m.serie_name,'. ')), if(notices_m.tnvol <>'', concat(notices_m.tnvol,'. '),'')),
			if(series_s.serie_name <>'', if(notices_s.tnvol <>'', concat(series_s.serie_name,', ',notices_s.tnvol,'. '), series_s.serie_name), if(notices_s.tnvol <>'', concat(notices_s.tnvol,'. '),'')),
			ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit
			FROM ((((resa LEFT JOIN notices AS notices_m ON resa_idnotice = notices_m.notice_id LEFT JOIN series AS series_m ON notices_m.tparent_id = series_m.serie_id )
			LEFT JOIN bulletins ON resa_idbulletin = bulletins.bulletin_id)
			LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id
			LEFT JOIN series AS series_s ON notices_s.tparent_id = series_s.serie_id)
			LEFT JOIN exemplaires ON resa_cb = exemplaires.expl_cb) ";
		return $query;
	}
	
	protected function is_filter_exemplaire() {
	    if($this->filters['expl_codestat']
	        || count($this->filters['expl_codestats'])
	        || $this->filters['expl_section']
	        || count($this->filters['expl_sections'])
	        || $this->filters['expl_statut']
	        || count($this->filters['expl_statuts'])
	        || $this->filters['expl_type']
	        || count($this->filters['expl_types'])
	        || $this->filters['expl_cote']
	        || $this->filters['expl_location']
	        || count($this->filters['expl_locations'])
	        ) {
	        return true;
	    }
	    return false;
	}
	    
	/**
	 * @param exemplaire $exemplaire
	 * @return boolean
	 */
	protected function is_visible_exemplaire($exemplaire) {
	    if($this->filters['expl_codestat'] && ($exemplaire->codestat_id != $this->filters['expl_codestat'])) {
	        return false;
	    }
	    if(count($this->filters['expl_codestats']) && (!in_array($exemplaire->codestat_id, $this->filters['expl_codestats']))) {
	        return false;
	    }
	    if($this->filters['expl_section'] && ($exemplaire->section_id != $this->filters['expl_section'])) {
	        return false;
	    }
	    if(count($this->filters['expl_sections']) && (!in_array($exemplaire->section_id, $this->filters['expl_sections']))) {
	        return false;
	    }
	    if($this->filters['expl_statut'] && ($exemplaire->statut_id != $this->filters['expl_statut'])) {
	        return false;
	    }
	    if(count($this->filters['expl_statuts']) && (!in_array($exemplaire->statut_id, $this->filters['expl_statuts']))) {
	        return false;
	    }
	    if($this->filters['expl_type'] && ($exemplaire->typdoc_id != $this->filters['expl_type'])) {
	        return false;
	    }
	    if(count($this->filters['expl_types']) && (!in_array($exemplaire->typdoc_id, $this->filters['expl_types']))) {
	        return false;
	    }
	    if($this->filters['expl_cote'] && ($exemplaire->cote != $this->filters['expl_cote'])) {
	        return false;
	    }
	    if($this->filters['expl_location'] && ($exemplaire->location_id != $this->filters['expl_location'])) {
	        return false;
	    }
	    if(count($this->filters['expl_locations']) && (!in_array($exemplaire->location_id, $this->filters['expl_locations']))) {
	        return false;
	    }
	    return true;
	}
	
	protected function is_filter_emprunteur() {
		if($this->filters['empr_statut']
				|| $this->filters['empr_categ']
				|| $this->filters['empr_codestat']
				) {
					return true;
				}
				return false;
	}
	
	/**
	 * @param emprunteur $emprunteur
	 * @return boolean
	 */
	protected function is_visible_emprunteur($emprunteur) {
		if($this->filters['empr_statut'] && ($emprunteur->empr_statut != $this->filters['empr_statut'])) {
			return false;
		}
		if($this->filters['empr_categ'] && ($emprunteur->categ != $this->filters['empr_categ'])) {
			return false;
		}
		if($this->filters['empr_codestat'] && ($emprunteur->cstat != $this->filters['empr_codestat'])) {
			return false;
		}
		return true;
	}
	
	protected function get_object_instance($row) {
		$resa = new reservation($row->resa_idempr, $row->resa_idnotice, $row->resa_idbulletin, $row->resa_cb);
// 		$resa->get_resa_cb();
		return $resa;
	}
	
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'index_sew':
	            return 'notices_m.index_sew, resa_date';
	        case 'record':
	            return 'tit, resa_date';
	        case 'resa_validee' :
	            return 'resa_cb';
	        case 'expl_cote' :
	        case 'resa_date' :
	        case 'resa_date_debut' :
	        case 'resa_date_fin' :
	        case 'resa_confirmee' :
	        case 'expl_cb' :
	            return $sort_by;
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
	    return " group by resa_idnotice, resa_idbulletin, resa_idempr ".parent::_get_query_order();
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		global $pmb_lecteurs_localises, $pmb_transferts_actif, $pmb_location_reservation;
		
		$this->available_filters =
		array('main_fields' =>
				array(
						'expl_codestat' => 'editions_datasource_expl_codestat',
						'expl_codestats' => 'editions_datasource_expl_codestats',
						'expl_section' => 'editions_datasource_expl_section',
						'expl_sections' => 'editions_datasource_expl_sections',
						'expl_statut' => 'editions_datasource_expl_statut',
						'expl_statuts' => 'editions_datasource_expl_statuts',
						'expl_type' => 'editions_datasource_expl_type',
						'expl_types' => 'editions_datasource_expl_types',
						'expl_cote' => '296',
						'groups' => '903',
// 						'empr_statut' => 'editions_filter_empr_statut',
//						'empr_categ' => 'editions_filter_empr_categ',
//						'empr_codestat' => 'editions_filter_empr_codestat',
				)
		);
		if($pmb_lecteurs_localises) {
			$this->available_filters['main_fields']['empr_location'] = 'editions_filter_empr_location';
		}
		if ($pmb_transferts_actif=="1" || $pmb_location_reservation) {
			$this->available_filters['main_fields']['removal_location'] = 'transferts_circ_resa_lib_localisation';
			$this->available_filters['main_fields']['available_location'] = 'edit_resa_expl_available_filter';
			$this->available_filters['main_fields']['resa_loc_retrait'] = 'edit_resa_expl_location_filter';
		}
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
    		    'id_notice' => 0,
    		    'id_bulletin' => 0,
				'id_empr' => (!empty($filters['id_empr']) ? $filters['id_empr'] : 0),
				'empr_location' => '',
				'removal_location' => '',
				'available_location' => '',
				'resa_state' => '',
				'expl_codestat' => '',
				'expl_codestats' => array(),
				'expl_section' => '',
				'expl_sections' => array(),
				'expl_statut' => '',
				'expl_statuts' => array(),
				'expl_type' => '',
				'expl_types' => array(),
				'expl_cote' => '',
    		    'expl_location' => '',
    		    'expl_locations' => array(),
				'groups' => array()
		);
		parent::init_filters($filters);
	}
	
	protected function init_no_sortable_columns() {
	    $this->no_sortable_columns = array(
	        'resa_delete'
	    );
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		global $pmb_transferts_actif;
		
		$this->available_columns =
		array('main_fields' =>
				array(
						'record' => 'resa_liste_titre',
// 						'expl_cote' => '296',
// 						'empr' => 'empr_nom_prenom',
// 						'empr_cb' => '35',
// 						'empr_location' => 'editions_datasource_empr_location',
						'rank' => 'resa_liste_rank',
// 						'resa_date' => '374',
// 						'resa_date_debut' => 'resa_date_debut_td',
// 						'resa_date_fin' => 'resa_date_fin_td',
// 						'resa_validee' => 'resa_validee',
// 						'resa_confirmee' => 'resa_confirmee',
// 						'expl_location' => 'edit_resa_expl_location',
// 						'section' => '295',
// 						'statut' => '297',
// 						'support' => '294',
// 						'expl_cb' => '232',
// 						'codestat' => '299',
// 						'groups' => 'groupe_empr',
				    
				    
//						'empr_statut_libelle' => 'editions_datasource_empr_statut',
//						'empr_categ_libelle' => 'editions_datasource_empr_categ',
//						'empr_codestat_libelle' => 'editions_datasource_empr_codestat',
				)
		);
// 		if ($pmb_transferts_actif) {
// 			$this->available_columns['main_fields']['resa_loc_retrait'] = 'resa_loc_retrait';
// 			$this->available_columns['main_fields']['transfert_location_source'] = 'transfert_location_source';
// 		}
	}
	
	/**
	 * Initialisation des settings par défaut
	 */
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'unfolded_filters', true);
		$this->set_setting_display('objects_list', 'deffered_load', true);
		$this->set_setting_filter('expl_codestats', 'visible', false);
		$this->set_setting_filter('expl_sections', 'visible', false);
		$this->set_setting_filter('expl_statuts', 'visible', false);
		$this->set_setting_filter('expl_types', 'visible', false);
		$this->set_setting_column('expl_cb', 'text', array('bold' => true));
		$this->set_setting_column('record', 'align', 'left');
		//$this->set_setting_column('record', 'text', array('bold' => true));
		$this->set_setting_column('rank', 'datatype', 'integer');
		$this->set_setting_column('resa_date', 'datatype', 'date');
		$this->set_setting_column('resa_date_debut', 'datatype', 'date');
		$this->set_setting_column('resa_date_fin', 'datatype', 'date');
		$this->set_setting_column('resa_validee', 'datatype', 'boolean');
		$this->set_setting_column('resa_validee', 'text', array('strong' => true));
		$this->set_setting_column('resa_validee', 'text_color', 'red');
		$this->set_setting_column('resa_confirmee', 'datatype', 'boolean');
		$this->set_setting_column('resa_confirmee', 'text', array('strong' => true));
		$this->set_setting_column('resa_confirmee', 'text_color', 'red');
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('record');
	    $this->add_applied_sort('resa_date');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('empr_location');
		$this->set_filter_from_form('removal_location');
		$this->set_filter_from_form('available_location');
		$this->set_filter_from_form('resa_loc_retrait');
		
		$this->set_filter_from_form('expl_codestat');
		$this->set_filter_from_form('expl_codestats');
		$this->set_filter_from_form('expl_section');
		$this->set_filter_from_form('expl_sections');
		$this->set_filter_from_form('expl_statut');
		$this->set_filter_from_form('expl_statuts');
		$this->set_filter_from_form('expl_type');
		$this->set_filter_from_form('expl_types');
		$this->set_filter_from_form('expl_cote');
		$this->set_filter_from_form('expl_location');
		$this->set_filter_from_form('expl_locations');
		$this->set_filter_from_form('groups');
//		$this->set_filter_from_form('empr_statut');
//		$this->set_filter_from_form('empr_categ');
//		$this->set_filter_from_form('empr_codestat');
		parent::set_filters_from_form();
	}
	
	protected function get_selection_query($type) {
		$query = '';
		switch ($type) {
			case 'empr_location':
				$query = 'select idlocation as id, location_libelle as label from docs_location order by label';
				break;
			case 'docs_codestat':
				$query = 'select idcode as id, codestat_libelle as label from docs_codestat order by label';
				break;
			case 'docs_section':
				$query = 'select idsection as id, section_libelle as label from docs_section order by label';
				break;
			case 'docs_statut':
				$query = 'select idstatut as id, statut_libelle as label from docs_statut order by label';
				break;
			case 'docs_type':
				$query = 'select idtyp_doc as id, tdoc_libelle as label from docs_type order by label';
				break;
			case 'docs_location':
			    $query = 'select idlocation as id, location_libelle as label from docs_location order by label';
			    break;
			case 'groups':
				$query = 'select id_groupe as id, libelle_groupe as label from groupe order by label';
				break;
			case 'empr_statut':
				$query = 'select idstatut as id, statut_libelle as label from empr_statut order by label';
				break;
			case 'empr_categ':
				$query = 'select id_categ_empr as id, libelle as label from empr_categ order by label';
				break;
			case 'empr_codestat':
				$query = 'select idcode as id, libelle as label from empr_codestat order by label';
				break;
		}
		return $query;
	}
	
	protected function get_search_filter_empr_location() {
		global $msg;
		
		return $this->get_search_filter_simple_selection($this->get_selection_query('empr_location'), 'empr_location', $msg['all_location']);
	}
	
	protected function get_search_filter_removal_location() {
		global $msg, $charset;
		
		$query = 'select idlocation, location_libelle FROM docs_location order by location_libelle';
		$result = pmb_mysql_query($query);
		$search_filter = '<select name="'.$this->objects_type.'_removal_location">';
		$search_filter.='<option value="0"'.((!$this->filters['removal_location'])?' selected="selected"':'').'>'.htmlentities($msg["all_location"], ENT_QUOTES, $charset).'</option>';
		if(pmb_mysql_num_rows($result)) {
			while($o=pmb_mysql_fetch_object($result)) {
				$search_filter.= '<option value="'.$o->idlocation.'"'.(($this->filters['removal_location'] == $o->idlocation)?' selected="selected"':'').'>'.htmlentities($o->location_libelle,ENT_QUOTES,$charset).'</option>';
			}
		}
		$search_filter.= '</select>';
		return $search_filter;
	}
	
	protected function get_search_filter_available_location() {
		global $msg, $charset;
		
		$query = 'select idlocation, location_libelle FROM docs_location order by location_libelle';
		$result = pmb_mysql_query($query);
		$search_filter = '<select name="'.$this->objects_type.'_available_location">';
		$search_filter.='<option value="0"'.((!$this->filters['available_location'])?' selected="selected"':'').'>'.$msg['all_location'].'</option>';
		if(pmb_mysql_num_rows($result)) {
			while($o=pmb_mysql_fetch_object($result)) {
				$search_filter.= '<option value="'.$o->idlocation.'"'.(($this->filters['available_location'] == $o->idlocation)?' selected="selected"':'').'>'.htmlentities($o->location_libelle,ENT_QUOTES,$charset).'</option>';
			}
		}
		$search_filter.= '</select>';
		return $search_filter;
	}
	
	protected function get_search_filter_resa_loc_retrait() {
		global $msg, $charset;
		
		$query = 'select idlocation, location_libelle FROM docs_location order by location_libelle';
		$result = pmb_mysql_query($query);
		$search_filter = '<select name="'.$this->objects_type.'_resa_loc_retrait">';
		$search_filter.='<option value="0"'.((!$this->filters['resa_loc_retrait'])?' selected="selected"':'').'>'.$msg['all_location'].'</option>';
		if(pmb_mysql_num_rows($result)) {
			while($o=pmb_mysql_fetch_object($result)) {
				$search_filter.= '<option value="'.$o->idlocation.'"'.(($this->filters['resa_loc_retrait'] == $o->idlocation)?' selected="selected"':'').'>'.htmlentities($o->location_libelle,ENT_QUOTES,$charset).'</option>';
			}
		}
		$search_filter.= '</select>';
		return $search_filter;
	}
	
	protected function get_search_filter_expl_cote() {
		return $this->get_search_filter_simple_text('expl_cote', 20);
	}
	
	protected function get_search_filter_expl_codestat() {
		global $msg;
		
		return $this->get_search_filter_simple_selection($this->get_selection_query('docs_codestat'), 'expl_codestat', $msg['all']);
	}
	
	protected function get_search_filter_expl_codestats() {
		global $msg;
		
		return $this->get_search_filter_multiple_selection($this->get_selection_query('docs_codestat'), 'expl_codestats', $msg['all']);
	}
	
	protected function get_search_filter_expl_section() {
		global $msg;
		
		return $this->get_search_filter_simple_selection($this->get_selection_query('docs_section'), 'expl_section', $msg['all']);
	}
	
	protected function get_search_filter_expl_sections() {
		global $msg;
		
		return $this->get_search_filter_multiple_selection($this->get_selection_query('docs_section'), 'expl_sections', $msg['all']);
	}
	
	protected function get_search_filter_expl_statut() {
		global $msg;
		
		return $this->get_search_filter_simple_selection($this->get_selection_query('docs_statut'), 'expl_statut', $msg['all']);
	}
	
	protected function get_search_filter_expl_statuts() {
		global $msg;
		
		return $this->get_search_filter_multiple_selection($this->get_selection_query('docs_statut'), 'expl_statuts', $msg['all']);
	}
	
	protected function get_search_filter_expl_type() {
		global $msg;
		
		return $this->get_search_filter_simple_selection($this->get_selection_query('docs_type'), 'expl_type', $msg['all']);
	}
	
	protected function get_search_filter_expl_types() {
		global $msg;
		
		return $this->get_search_filter_multiple_selection($this->get_selection_query('docs_type'), 'expl_types', $msg['all']);
	}
	
	protected function get_search_filter_expl_location() {
	    global $msg;
	    
	    return $this->get_search_filter_simple_selection($this->get_selection_query('docs_location'), 'expl_location', $msg['all']);
	}
	
	protected function get_search_filter_expl_locations() {
	    global $msg;
	    
	    return $this->get_search_filter_multiple_selection($this->get_selection_query('docs_location'), 'expl_locations', $msg['all']);
	}
	
	protected function get_search_filter_groups() {
		global $msg;
		
		return $this->get_search_filter_multiple_selection($this->get_selection_query('groups'), 'groups', $msg['dsi_all_groups']);
	}
	
	protected function get_search_filter_empr_statut() {
		global $msg;
		
		return $this->get_search_filter_simple_selection($this->get_selection_query('empr_statut'), 'empr_statut', $msg['all_statuts_empr']);
	}
	
	protected function get_search_filter_empr_categ() {
		global $msg;
		
		return $this->get_search_filter_simple_selection($this->get_selection_query('empr_categ'), 'empr_categ', $msg['categ_all']);
	}
	
	protected function get_search_filter_empr_codestat() {
		global $msg;
		
		return $this->get_search_filter_simple_selection($this->get_selection_query('empr_codestat'), 'empr_codestat', $msg['codestat_all']);
	}
	
	protected function _add_query_filters() {
		if(get_called_class() == 'list_reservations_edition_treat_ui') {
			$this->query_filters [] = '(resa_cb="" or resa_cb is null)';
		}
		$this->_add_query_filter_simple_restriction('id_notice', 'resa.resa_idnotice', 'integer');
		$this->_add_query_filter_simple_restriction('id_bulletin', 'resa.resa_idbulletin', 'integer');
		$this->_add_query_filter_simple_restriction('id_empr', 'resa.resa_idempr', 'integer');
		if($this->filters['resa_state']) {
			switch ($this->filters['resa_state']) {
				case 'depassee':
					$this->query_filters [] = '(resa_date_fin < CURDATE() and resa_date_fin<>"0000-00-00")';
					break;
				case 'encours':
				default:
					$this->query_filters [] = '(resa_date_fin >= CURDATE() or resa_date_fin="0000-00-00")';
					break;
			}
// 			$this->query_filters [] = $this->filters['resa_dates_restrict'];
		}
		$this->_add_query_filter_multiple_restriction('groups', 'groupe_id', 'integer');
		if($this->filters['ids']) {
			$this->query_filters [] = 'id_resa IN ('.$this->filters['ids'].')';
		}
	}
	
	/**
	 * Fonction de callback
	 * @param object $a
	 * @param object $b
	 * * @param number $index
	 */
	protected function _compare_objects($a, $b, $index=0) {
	    if($this->applied_sort[$index]['by']) {
	        $sort_by = $this->applied_sort[$index]['by'];
			switch($sort_by) {
// 				case 'record' :
// 					$a_notice_title = reservation::get_notice_title($a->id_notice, $a->id_bulletin);
// 					$b_notice_title = reservation::get_notice_title($b->id_notice, $b->id_bulletin);
// 					return strcmp(strip_tags($a_notice_title), strip_tags($b_notice_title));
// 					break;
// 				case 'empr':
// 					return strcmp(emprunteur::get_name($a->id_empr), emprunteur::get_name($b->id_empr));
// 					break;
// 				case 'resa_date':
// 					return strcmp($a->date, $b->date);
// 					break;
// 				case 'resa_date_debut':
// 					return strcmp($a->date_debut, $b->date_debut);
// 					break;
// 				case 'resa_date_fin':
// 					return strcmp($a->date_fin, $b->date_fin);
// 					break;
				default :
					return parent::_compare_objects($a, $b, $index);
					break;
			}
		}
	}
	
	protected function get_grouped_label($object, $property) {
		global $msg;
		
		$grouped_label = '';
		switch($property) {
// 			case 'resa_date':
// 				$grouped_label = $object->formatted_date;
// 				break;
// 			case 'resa_date_debut':
// 				if($object->date_debut != '0000-00-00') {
// 					$grouped_label = $object->formatted_date_debut;
// 				}
// 				break;
// 			case 'resa_date_fin':
// 				if($object->date_fin != '0000-00-00') {
// 					$grouped_label = $object->formatted_date_fin;
// 				}
// 				break;
// 			case 'resa_validee':
// 				if($object->expl_cb) {
// 					$grouped_label = $msg["40"];
// 				} else {
// 					$grouped_label = $msg["39"];
// 				}
// 				break;
// 			case 'resa_confirmee':
// 				if($object->confirmee) {
// 					$grouped_label = $msg["40"];
// 				} else {
// 					$grouped_label = $msg["39"];
// 				}
// 				break;
			default:
				$grouped_label = parent::get_grouped_label($object, $property);
				break;
		}
		return $grouped_label;
	}
	
	protected function _get_object_property_expl_cote($object) {
		return $object->get_exemplaire()->cote;
	}
	
	protected function _get_object_property_empr($object) {
		return emprunteur::get_name($object->id_empr);
	}
	
	protected function _get_object_property_empr_cb($object) {
		return emprunteur::get_cb_empr($object->id_empr);
	}
	
	protected function _get_object_property_empr_location($object) {
		return emprunteur::get_location($object->id_empr)->libelle;
	}
	
	protected function _get_object_property_rank($object) {
		return recupere_rang($object->id_empr, $object->id_notice, $object->id_bulletin, $this->filters['removal_location']) ;;
	}
	
	protected function _get_object_property_expl_location($object) {
		return $object->get_exemplaire()->location;
	}
	
	protected function _get_object_property_support($object) {
		return $object->get_exemplaire()->typdoc;
	}
	
	protected function _get_object_property_statut($object) {
		$docs_statut = new docs_statut($object->get_exemplaire()->statut_id);
		return $docs_statut->libelle;
	}
	
	protected function _get_object_property_section($object) {
		return $object->get_exemplaire()->section;
	}
	
	protected function _get_object_property_codestat($object) {
		$docs_codestat = new docs_codestat($object->get_exemplaire()->codestat_id);
		return $docs_codestat->libelle;
	}
	
	protected function _get_object_property_groups($object) {
		return implode(',', emprunteur::get_groupes($object->id_empr));
	}
	
	protected function _get_object_property_empr_statut_libelle($object) {
		return $object->get_emprunteur()->empr_statut_libelle;
	}
	
	protected function _get_object_property_empr_categ_libelle($object) {
		return $object->get_emprunteur()->cat_l;
	}
	
	protected function _get_object_property_empr_codestat_libelle($object) {
		return $object->get_emprunteur()->cstat_l;
	}
	
	protected function _get_object_property_resa_date($object) {
		return $object->formatted_date;
	}
	
	protected function _get_object_property_resa_date_debut($object) {
		if($object->date_debut != '0000-00-00') {
			return $object->formatted_date_debut;
		}
		return '';
	}
	
	protected function _get_object_property_resa_date_fin($object) {
		if($object->date_fin != '0000-00-00') {
			return $object->formatted_date_fin;
		}
		return '';
	}
	
	protected function _get_object_property_resa_validee($object) {
		if($object->expl_cb) {
			return "X";
		}
		return '';
	}
	
	protected function _get_object_property_resa_confirmee($object) {
		if($object->confirmee) {
			return "X";
		}
		return '';
	}
	
	protected function _get_object_property_resa_loc_retrait($object) {
		$loc_retrait = resa_loc_retrait($object->id);
		$docs_location = new docs_location($loc_retrait);
		return $docs_location->libelle;
	}
	
	protected function _get_object_property_transfert_location_source($object) {
		$query = "SELECT num_location_source FROM transferts_demande WHERE resa_trans=".$object->id." AND num_expl=".$object->expl_id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$docs_location = new docs_location(pmb_mysql_result($result, 0, 'num_location_source'));
			return $docs_location->libelle;
		}
		return '';
	}
		
	protected function get_cell_content($object, $property) {
	    global $msg, $css;
	    
		$content = '';
		switch($property) {
			case 'record':
			    if ($object->id_notice) {
			        // affiche la notice correspondant à la réservation
			        $notice = new notice($object->id_notice);
			        $content .= pmb_bidi($notice->print_resume(1,$css));
			    } else {
			        // c'est un bulletin donc j'affiche le nom de périodique et le nom du bulletin (date ou n°)
			        $requete = "SELECT bulletin_id, bulletin_numero, bulletin_notice, mention_date, date_date, date_format(date_date, '".$msg["format_date_sql"]."') as aff_date_date FROM bulletins WHERE bulletin_id='".$object->id_bulletin."'";
			        $res = pmb_mysql_query($requete);
			        $obj = pmb_mysql_fetch_object($res) ;
			        $notice3 = new notice($obj->bulletin_notice);
			        $content .= pmb_bidi($notice3->print_resume(1,$css));
			        
			        // affichage de la mention de date utile : mention_date si existe, sinon date_date
			        if ($obj->mention_date) {
			            $content .= pmb_bidi("(".$obj->mention_date.")\n");
			        } elseif ($obj->date_date) {
			            $content .= pmb_bidi("(".$obj->aff_date_date.")\n");
			        }
			    }
				break;
			case 'rank':
			    $content .= sprintf($msg['rank'], $this->_get_object_property_rank($object));
			    if (($object->date_fin >= date('Y-m-d') || $object->date_fin == '0000-00-00')) {
			        if (reservation::get_cb_from_id($object->id)) {
			            $content.= " ".sprintf($msg["expl_reserved_til"],$object->formatted_date_fin)." " ;
			        } else {
			            $content.= " ".$msg["resa_attente_validation"];
			        }
			    } else  {
			        $content .= " ".$msg["resa_overtime"];
			    }
			    break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function _get_query_property_filter($property) {
		switch ($property) {
			case 'empr_location':
				return "select location_libelle from docs_location where idlocation = ".$this->filters[$property];
			case 'expl_codestat':
				return "select codestat_libelle from docs_codestat where idcode = ".$this->filters[$property];
			case 'expl_codestats':
				return "select codestat_libelle from docs_codestat where idcode IN (".implode(',', $this->filters[$property]).")";
			case 'expl_section':
				return "select section_libelle from docs_section where idsection = ".$this->filters[$property];
			case 'expl_sections':
				return "select section_libelle from docs_section where idsection IN (".implode(',', $this->filters[$property]).")";
			case 'expl_statut':
				return "select statut_libelle from docs_statut where idstatut = ".$this->filters[$property];
			case 'expl_statuts':
				return "select statut_libelle from docs_statut where idstatut IN (".implode(',', $this->filters[$property]).")";
			case 'expl_type':
				return "select tdoc_libelle from docs_type where idtyp_doc = ".$this->filters[$property];
			case 'expl_types':
				return "select tdoc_libelle from docs_type where idtyp_doc IN (".implode(',', $this->filters[$property]).")";
			case 'groups':
				return "select libelle_groupe from groupe where id_groupe IN (".implode(',', $this->filters[$property]).")";
			case 'empr_statut':
				return "select statut_libelle from empr_statut where idstatut = ".$this->filters[$property];
			case 'empr_categ':
				return "select libelle from empr_categ where id_categ_empr = ".$this->filters[$property];
			case 'empr_codestat':
				return "select libelle from empr_codestat where idcode = ".$this->filters[$property];
		}
		return '';
	}
	
	protected function _get_query_human_empr_location() {
		if($this->filters['empr_location']) {
			$docs_location = new docs_location($this->filters['empr_location']);
			return $docs_location->libelle;
		}
		return '';
	}
	
	protected function _get_query_human_removal_location() {
		if($this->filters['removal_location']) {
			$docs_location = new docs_location($this->filters['removal_location']);
			return $docs_location->libelle;
		}
		return '';
	}
	
	protected function _get_query_human_available_location() {
		$docs_location = new docs_location($this->filters['available_location']);
		return $docs_location->libelle;
	}
	
	protected function _get_query_human_resa_loc_retrait() {
		$docs_location = new docs_location($this->filters['resa_loc_retrait']);
		return $docs_location->libelle;
	}
	
	public function get_resa_loc() {
		if(!isset($this->resa_loc)) {
			$this->resa_loc = new resa_loc();
		}
		return $this->resa_loc;
	}
}