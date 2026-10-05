<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_reservations_ui.class.php,v 1.61.4.2 2025/05/30 07:58:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/emprunteur.class.php");
require_once($class_path."/resa.class.php");
require_once($class_path."/resa_situation.class.php");
require_once($class_path."/notice.class.php");
require_once($class_path."/expl.class.php");
require_once($class_path."/resa_loc.class.php");

class list_reservations_ui extends list_ui {
	
	protected $location_reservations;
	
	protected $no_aff;
	
	protected $resa_situation;
	
	protected static $info_gestion = NO_INFO_GESTION;
	
	protected $lien_deja_affiche;
	
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
	
	protected function is_visible_object($empr_location, $resa_loc_retrait) {
		global $pmb_transferts_actif, $transferts_choix_lieu_opac, $transferts_site_fixe;
		global $pmb_location_reservation, $pmb_lecteurs_localises;
		
		if($this->filters['available_location']) {
		    if ($pmb_lecteurs_localises && !$this->filters['id_empr']){
		        $this->on_expl_location = true;
		    }
		}
		if(!empty($this->selected_filters['empr_location']) && $this->filters['empr_location']) {
			if($this->filters['empr_location'] != $empr_location) {
				return false;
			}
		}
		if(!empty($this->selected_filters['removal_location'])/* && $this->filters['removal_location']*/) {
		    $filter_location_retrait = $this->filters['removal_location'];
		} else {
		    $filter_location_retrait = $this->filters['resa_loc_retrait'];
		}
		if($filter_location_retrait) {
			if ($pmb_transferts_actif=="1" && $filter_location_retrait) {
				switch ($transferts_choix_lieu_opac) {
					case "1":
						//retrait de la resa sur lieu choisi par le lecteur
						if($resa_loc_retrait != $filter_location_retrait) {
							return false;
						}
						break;
					case "2":
						//retrait de la resa sur lieu fixé
						if ($filter_location_retrait != $transferts_site_fixe) {
							return false;
						}
						break;
					case "3":
						//retrait de la resa sur lieu exemplaire
						// On affiche les résa que peut satisfaire la loc
						// respecter les droits de réservation du lecteur
						if($pmb_location_reservation) {
							$resa_loc = $this->get_resa_loc();
							$data = $resa_loc->get_data();
							if(!in_array($filter_location_retrait, $data[$empr_location])) {
								return false;
							}
						}
						if(!$this->filters['id_empr']) {
						    $this->on_expl_location = true;
						}
						break;
					default:
						//retrait de la resa sur lieu lecteur
						if($empr_location != $filter_location_retrait) {
							return false;
						}
						if(!$this->filters['id_empr']) {
						    $this->on_expl_location = true;
						}
						break;
				}
			}elseif($pmb_location_reservation && $filter_location_retrait) {
				$resa_loc = $this->get_resa_loc();
				$data = $resa_loc->get_data();
				if(empty($data[$empr_location]) || !in_array($filter_location_retrait, $data[$empr_location])) {
					return false;
				}
			}
		}
		return true;
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
	
	protected function is_visible_exemplaire_from_resa($resa_idnotice=0, $resa_idbulletin=0) {
		$resa_idnotice = intval($resa_idnotice);
		$resa_idbulletin = intval($resa_idbulletin);
		
		$is_visible = false;
		$query = "SELECT expl_codestat as codestat_id, expl_section as section_id, expl_statut as statut_id, expl_typdoc as typdoc_id
			, expl_cote as type, expl_location as location_id
			FROM exemplaires
			WHERE expl_notice='$resa_idnotice' and expl_bulletin='$resa_idbulletin'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$flag = $this->is_visible_exemplaire($row);
				if($flag) {
					$is_visible = true;
					break;
				}
			}
		}
		return $is_visible;
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
		$resa->get_resa_cb();
		return $resa;
	}
	
	protected function add_object($row) {
		global $f_loc;
		
		$no_aff=0;
		if(!($this->filters['id_notice'] || $this->filters['id_bulletin']))
			if($this->filters['f_loc'] && !$this->filters['id_empr'] && $row->resa_cb && $row->resa_confirmee){
				// Dans la liste des résa à traiter, on n'affiche pas la résa qui a été affecté par un autre site
				$query = "SELECT expl_location FROM exemplaires WHERE expl_cb='".$row->resa_cb."' ";
				$res = @pmb_mysql_query($query);
				if(($data_expl = pmb_mysql_fetch_array($res))){
					if($data_expl['expl_location']!=$this->filters['f_loc']) {
						$no_aff=1;
					}
				}
		}
		if(!$no_aff || ($this->filters['id_notice'] || $this->filters['id_bulletin'])) {
			if($this->filters['id_empr']) {
				$this->filters['f_loc']=0;
				$f_loc = 0;
				$this->filters['removal_location']=0;
			}
			$empr_location = emprunteur::get_location($row->resa_idempr)->id;
			if($this->is_visible_object($empr_location, $row->resa_loc_retrait)) {
			    $is_filter_exemplaire = $this->is_filter_exemplaire();
			    //$is_filter_emprunteur = $this->is_filter_emprunteur();
			    $is_filter_emprunteur = false;
			    if($is_filter_exemplaire || $is_filter_emprunteur || !empty($this->on_expl_location)) {
			    	$object_instance = $this->get_object_instance($row);
			    	$exemplaire = $object_instance->get_exemplaire();
			    	if($is_filter_exemplaire && !empty($exemplaire->expl_id) && !$this->is_visible_exemplaire($exemplaire)) {
			            return false;
			    	}
			    	if($is_filter_exemplaire && empty($exemplaire->expl_id) && !$this->is_visible_exemplaire_from_resa($row->resa_idnotice, $row->resa_idbulletin)) {
			        	return false;
			        }
			        /*$emprunteur = $object_instance->get_emprunteur();
			        if($is_filter_emprunteur && !empty($emprunteur->id) && !$this->is_visible_emprunteur($emprunteur)) {
			        	return false;
			        }*/
			        if(!empty($this->on_expl_location)) {
			            if(!$this->filters['id_notice'] && !$this->filters['id_bulletin']) {
			                $resa_situation = $this->get_resa_situation($object_instance);
			                if($this->is_deffered_load()) {
			                    $resa_situation->initialize_no_aff();
			                } else {
			                    $resa_situation->get_display();
			                }
			                $no_aff = $resa_situation->get_no_aff();
			            } else {
			                $no_aff = 0;
			            }
			            if($no_aff) {
			                return false;
			            }
			        }
			        $this->objects[] = $object_instance;
			    } else {
			        parent::add_object($row);
			    }
				$this->location_reservations[$row->id_resa] = $empr_location;
			}
		}
	}
	
	/**
	 * On ne limite pas la requête SQL du fait des restrictions dans add_object
	 */
	protected function _get_query() {
		$query = $this->_get_query_base();
		$query .= $this->_get_query_filters();
		$query .= $this->_get_query_order();
		return $query;
	}
	
	protected function fetch_data() {
		parent::fetch_data();
		$this->pager['nb_results'] = count($this->objects);
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
	 * limite SQL retirée dans get_query
	 * On l'applique ici
	 */
	protected function _limit() {
		$this->applied_sort_type = 'OBJECTS';
		parent::_limit();
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		global $pmb_lecteurs_localises, $pmb_transferts_actif, $pmb_location_reservation;
		
		$this->available_filters =
		array('main_fields' =>
				array(
						'montrerquoi' => 'empr_etat_resa_query',
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
//						'resa_condition' => 'resa_condition'
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
	
	protected function init_location_filters() {
		global $pmb_transferts_actif, $transferts_choix_lieu_opac, $pmb_location_reservation;
		global $f_loc;
		
		$this->filters['resa_loc_retrait'] = '';
		$this->filters['resa_loc'] = 0;
		if ($pmb_transferts_actif=="1" && $this->filters['f_loc'] && empty($this->filters['id_empr'])) {
			switch ($transferts_choix_lieu_opac) {
				case "1":
					//retrait de la resa sur lieu choisi par le lecteur
					$this->filters['resa_loc_retrait'] = $this->filters['f_loc'];
					break;
				case "2":
					//retrait de la resa sur lieu fixé
					break;
				case "3":
					//retrait de la resa sur lieu exemplaire
					// On affiche les résa que peut satisfaire la loc
					// respecter les droits de réservation du lecteur
					if($pmb_location_reservation) {
						$this->filters['resa_loc'] = $this->filters['f_loc'];
					}
					break;
				default:
					//retrait de la resa sur lieu lecteur
					break;
			}
		}elseif($pmb_location_reservation && $this->filters['f_loc'] && empty($this->filters['id_empr'])) {
			$this->filters['resa_loc'] = $this->filters['f_loc'];
		}
		if($this->filters['id_notice'] || $this->filters['id_bulletin']) {
			$this->filters['f_loc'] = 0;
			$f_loc = 0;
			$this->filters['removal_location'] = 0;
		} else {
			$this->filters['removal_location'] = $this->filters['f_loc'];
		}
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $deflt_resas_location;
		global $f_loc;
		
		$this->filters = array(
    		    'id_notice' => 0,
    		    'id_bulletin' => 0,
				'id_empr' => (!empty($filters['id_empr']) ? $filters['id_empr'] : 0),
    		    'montrerquoi' => 'all',
				'my_home_location' => $deflt_resas_location,
				'f_loc' => ($f_loc == '' ? $deflt_resas_location : $f_loc),
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
				'groups' => array(),
                'resa_condition' => ''
		);
		$this->init_location_filters();
		$filters['my_home_location'] = $deflt_resas_location;
		parent::init_filters($filters);
	}
	
	protected function init_override_filters() {
		global $deflt_resas_location, $f_loc;
		
		$this->filters['f_loc'] = ($f_loc == '' ? $deflt_resas_location : $f_loc);
		$this->init_location_filters();
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
						'record' => '233',
						'expl_cote' => '296',
						'empr' => 'empr_nom_prenom',
						'empr_cb' => '35',
						'empr_location' => 'editions_datasource_empr_location',
						'rank' => '366',
						'resa_date' => '374',
						'resa_condition' => 'resa_condition',
						'resa_date_debut' => 'resa_date_debut_td',
						'resa_date_fin' => 'resa_date_fin_td',
						'resa_validee' => 'resa_validee',
						'resa_confirmee' => 'resa_confirmee',
						'expl_location' => 'edit_resa_expl_location',
						'section' => '295',
						'statut' => '297',
						'support' => '294',
						'expl_cb' => '232',
						'codestat' => '299',
						'groups' => 'groupe_empr',
//						'empr_statut_libelle' => 'editions_datasource_empr_statut',
//						'empr_categ_libelle' => 'editions_datasource_empr_categ',
//						'empr_codestat_libelle' => 'editions_datasource_empr_codestat',
				)
		);
		if ($pmb_transferts_actif) {
			$this->available_columns['main_fields']['resa_loc_retrait'] = 'resa_loc_retrait';
			$this->available_columns['main_fields']['transfert_location_source'] = 'transfert_location_source';
		}
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
		$this->set_setting_column('record', 'text', array('bold' => true));
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
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['nb_per_page'] = 40;
		$this->pager['allow_force_all_on_page'] = true;
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('montrerquoi');
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
		$this->set_filter_from_form('resa_condition');
		parent::set_filters_from_form();
	}
	
	/**
	 * Sauvegarde de la pagination en session
	 */
	public function set_pager_in_session() {
	    parent::set_pager_in_session();
	    if($this->pager['nb_results'] >= ($this->pager['page']*$this->pager['nb_per_page'])) {
	        $_SESSION['list_'.$this->objects_type.'_pager']['page'] = $this->pager['page'];
	    } else {
	        unset($_SESSION['list_'.$this->objects_type.'_pager']['page']);
	    }
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
	
	protected function get_search_filter_montrerquoi() {
		global $msg, $charset;
		
		//Selecteur réservations validees/confirmees
		$search_filter = "
            <span class='list_ui_montrerquoi_checkbox ".$this->objects_type."_montrerquoi_checkbox'>
                <input type='radio' name='".$this->objects_type."_montrerquoi' value='all' id='all' ".($this->filters['montrerquoi'] == 'all' ? "checked='checked'" : "")." />
                <label for='all'>".htmlentities($msg['resa_show_all'], ENT_QUOTES, $charset)."</label>
            </span>&nbsp;
            <span class='list_ui_montrerquoi_checkbox ".$this->objects_type."_montrerquoi_checkbox'>
                <input type='radio' name='".$this->objects_type."_montrerquoi' value='validees' id='validees' ".($this->filters['montrerquoi'] == 'validees' ? "checked='checked'" : "")." />
                <label for='validees'>".htmlentities($msg['resa_show_validees'], ENT_QUOTES, $charset)."</label>
            </span>&nbsp;
            <span class='list_ui_montrerquoi_checkbox ".$this->objects_type."_montrerquoi_checkbox'>
                <input type='radio' name='".$this->objects_type."_montrerquoi' value='invalidees' id='invalidees' ".($this->filters['montrerquoi'] == 'invalidees' ? "checked='checked'" : "")." />
                <label for='invalidees'>".htmlentities($msg['resa_show_invalidees'], ENT_QUOTES, $charset)."</label>
            </span>&nbsp;
            <span class='list_ui_montrerquoi_checkbox ".$this->objects_type."_montrerquoi_checkbox'>
                <input type='radio' name='".$this->objects_type."_montrerquoi' value='valid_noconf' id='valid_noconf' ".($this->filters['montrerquoi'] == 'valid_noconf' ? "checked='checked'" : "")." />
                <label for='valid_noconf'>".htmlentities($msg['resa_show_non_confirmees'], ENT_QUOTES, $charset)."</label>
            </span>";
		return $search_filter;
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
	
	protected function get_search_filter_resa_condition() {
	    global $msg, $charset;
	    
	    $options = resa_situation::get_conditions();
	    $selector = "<select name='".$this->objects_type."_resa_condition'>";
	    $selector .= "<option value='' ".(empty($this->filters['resa_condition']) ? "selected='selected'" : "").">".htmlentities($msg['all'], ENT_QUOTES, $charset)."</option>";
	    foreach ($options as $value=>$label) {
	        $selector .= "<option value='".htmlentities($value, ENT_QUOTES, $charset)."' ".(in_array($value, $this->filters['resa_condition']) ? "selected='selected'" : "").">".$label."</option>";
	    }
	    $selector .= "</select>";
	    return $selector;
	}
	
	protected function _add_query_filters() {
		if(get_called_class() == 'list_reservations_edition_treat_ui') {
			$this->query_filters [] = '(resa_cb="" or resa_cb is null)';
		}
		$this->_add_query_filter_simple_restriction('id_notice', 'resa.resa_idnotice', 'integer');
		$this->_add_query_filter_simple_restriction('id_bulletin', 'resa.resa_idbulletin', 'integer');
		$this->_add_query_filter_simple_restriction('id_empr', 'resa.resa_idempr', 'integer');
		if($this->filters['montrerquoi']) {
			switch ($this->filters['montrerquoi']) {
				case 'validees':
					$this->query_filters [] = 'resa_cb<>""';
					break;
				case 'invalidees':
					$this->query_filters [] = 'resa_cb=""';
					break;
				case 'valid_noconf':
					$this->query_filters [] = 'resa_cb<>""';
					$this->query_filters [] = 'resa_confirmee="0"';
					break;
				case 'all':
				default:
					break;
			}
		}
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
				case 'record' :
					$a_notice_title = reservation::get_notice_title($a->id_notice, $a->id_bulletin);
					$b_notice_title = reservation::get_notice_title($b->id_notice, $b->id_bulletin);
					return strcmp(strip_tags($a_notice_title), strip_tags($b_notice_title));
					break;
				case 'empr':
					return strcmp(emprunteur::get_name($a->id_empr), emprunteur::get_name($b->id_empr));
					break;
				case 'resa_date':
					return strcmp($a->date, $b->date);
					break;
				case 'resa_date_debut':
					return strcmp($a->date_debut, $b->date_debut);
					break;
				case 'resa_date_fin':
					return strcmp($a->date_fin, $b->date_fin);
					break;
				case 'resa_condition': //Situation
					$cmp_a = strip_tags($this->get_cell_content_resa_condition($a));
					$cmp_a_date = extraitdate($cmp_a);
					if($cmp_a_date) {
						$cmp_a = $cmp_a_date;
					}
					$cmp_b = strip_tags($this->get_cell_content_resa_condition($b));
					$cmp_b_date = extraitdate($cmp_b);
					if($cmp_b_date) {
						$cmp_b = $cmp_b_date;
					}
					return strcmp($cmp_a, $cmp_b);
					break;
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
			case 'resa_date':
				$grouped_label = $object->formatted_date;
				break;
			case 'resa_date_debut':
				if($object->date_debut != '0000-00-00') {
					$grouped_label = $object->formatted_date_debut;
				}
				break;
			case 'resa_date_fin':
				if($object->date_fin != '0000-00-00') {
					$grouped_label = $object->formatted_date_fin;
				}
				break;
			case 'resa_validee':
				if($object->expl_cb) {
					$grouped_label = $msg["40"];
				} else {
					$grouped_label = $msg["39"];
				}
				break;
			case 'resa_confirmee':
				if($object->confirmee) {
					$grouped_label = $msg["40"];
				} else {
					$grouped_label = $msg["39"];
				}
				break;
			case 'resa_condition': //Situation
				$grouped_label = $this->get_cell_content_resa_condition($object);
				break;
			default:
				$grouped_label = parent::get_grouped_label($object, $property);
				break;
		}
		return $grouped_label;
	}
	
	protected function get_resa_situation($object) {
		if(empty($this->resa_situation)) {
			$this->resa_situation = array();
		}
		if(!isset($this->resa_situation[$object->id])) {
			$rank = recupere_rang($object->id_empr, $object->id_notice, $object->id_bulletin, $this->filters['removal_location']);
			
			$resa_situation = new resa_situation($object->id);
			$resa_situation->set_resa($object)
			->set_resa_cb($object->expl_cb)
			->set_my_home_location($this->filters['my_home_location'])
			->set_rank($rank)
			->set_no_aff($this->no_aff)
			->set_lien_deja_affiche($this->lien_deja_affiche);
			if (isset($this->location_reservations[$object->id])) {
			    $resa_situation->set_idlocation($this->location_reservations[$object->id]);
			}
			$this->resa_situation[$object->id] = $resa_situation;
		}
		return $this->resa_situation[$object->id];
	}
	
	protected function get_cell_content_resa_condition($object) {
		if(!isset($this->no_aff)) $this->no_aff = 0;
		if(!isset($this->no_aff)) $this->lien_deja_affiche = 0;
		
		$resa_situation = $this->get_resa_situation($object);
		$situation = $resa_situation->get_display(static::$info_gestion);
		
		$this->no_aff = $resa_situation->get_no_aff();
		$this->lien_deja_affiche = $resa_situation->get_lien_deja_affiche();
		
		return $situation;
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
		$content = '';
		switch($property) {
			case 'expl_cb':
				$content .= exemplaire::get_cb_link($object->{$property});
				break;
			case 'record':
				if ($object->id_bulletin) {
					$typdoc = "";
				} else {
					$typdoc = notice::get_typdoc($object->id_notice);
				}
				$content .= resa_list_get_column_title($object->id_notice, $object->id_bulletin, $typdoc);
				break;
			case 'resa_condition': //Situation
				$content .= $this->get_cell_content_resa_condition($object);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		global $base_path;
		
		$attributes = array();
		switch($property) {
			case 'empr':
				if (SESSrights & CIRCULATION_AUTH) {
					$attributes['href'] = $base_path."/circ.php?categ=pret&form_cb=".rawurlencode(emprunteur::get_cb_empr($object->id_empr));
				}
				break;
			default:
				break;
		}
		return $attributes;
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
	
	protected function _get_query_human_montrerquoi() {
		global $msg;
		
		switch ($this->filters['montrerquoi']) {
			case 'validees':
				return $msg['resa_show_validees'];
			case 'invalidees':
				return $msg['resa_show_invalidees'];
			case 'valid_noconf':
				return $msg['resa_show_non_confirmees'];
		}
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
	
	protected function get_display_query_human_home() {
		global $msg, $charset;
		
		$display = '';
		if(!empty($this->filters['my_home_location'])) {
			$docs_location = new docs_location($this->filters['my_home_location']);
			$display .= "<div class='align_left'><img src='".get_url_icon('home.png')."' title='".htmlentities($msg['my_home_location'], ENT_QUOTES, $charset)."' /> <i>".htmlentities($docs_location->libelle, ENT_QUOTES, $charset)."</i><br /><br /></div>";
		}
		return $display;
	}
	
	protected function get_display_query_human($humans) {
		$display = parent::get_display_query_human($humans);
		$display .= $this->get_display_query_human_home();
		return $display;
	}
	
	public function get_resa_loc() {
		if(!isset($this->resa_loc)) {
			$this->resa_loc = new resa_loc();
		}
		return $this->resa_loc;
	}
}