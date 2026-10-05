<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_loans_ui.class.php,v 1.37.2.1 2024/05/03 12:47:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/emprunteur.class.php");
require_once($class_path."/pret.class.php");
require_once($class_path."/expl.class.php");

class list_loans_ui extends list_ui {
	
	protected $_query_filters_with_in_progress;
	
	protected function _get_query_base() {
		/* Conservation des anciens éléments du select
		 	date_format(pret_date, '".$msg['format_date']."') as aff_pret_date, ";
			$sql .= "date_format(pret_retour, '".$msg['format_date']."') as aff_pret_retour, ";
			$sql .= "IF(pret_retour>=CURDATE(),0,1) as retard, ";
			$sql .= "id_empr, empr_nom, empr_prenom, empr_mail, empr_cb, expl_cote, expl_cb, expl_notice, expl_bulletin, notices_m.notice_id as idnot, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, tdoc_libelle, ";
			$sql .= "short_loan_flag
		 */
	    $query = 'select pret_idempr, pret_idexpl';
	    //Cas particulier : on utilise le(s) groupe(s) associé(s) dans le tri
	    if(!empty($this->applied_sort[0]['by']) && $this->applied_sort[0]['by'] == 'groups') {
	        $query .= ', group_concat(libelle_groupe) as groups';
	    }
        $query .= '
			FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id )
				LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id)
				LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id)
				JOIN pret ON pret_idexpl = expl_id
				JOIN empr ON empr.id_empr = pret.pret_idempr
				JOIN docs_type ON expl_typdoc = idtyp_doc
		';
        //Cas particulier : on utilise le(s) groupe(s) associé(s) dans le tri
        if(!empty($this->applied_sort[0]['by']) && $this->applied_sort[0]['by'] == 'groups') {
            $query .= '
                JOIN empr_groupe ON empr_groupe.empr_id = empr.id_empr
                JOIN groupe ON groupe.id_groupe = empr_groupe.groupe_id
            ';
        }
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new pret($row->pret_idempr, $row->pret_idexpl);
	}
		
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		global $pmb_lecteurs_localises;
		
		$this->available_filters =
		array('main_fields' =>
				array(
						'doc_location' => 'editions_filter_docs_location',
						'empr_categorie' => 'editions_filter_empr_categ',
						'empr_codestat_one' => 'editions_filter_empr_codestat',
// 						'groups' => '907',
// 						'pret_date' => 'circ_date_emprunt',
// 						'pret_retour' => 'circ_date_retour'
				        'expl_type' => 'editions_datasource_expl_type'
				)
		);
		if($pmb_lecteurs_localises) {
			$this->available_filters['main_fields']['empr_location'] = 'editions_filter_empr_location';
		}
		if(dilicom::is_pnb_active()) {
			$this->available_filters['main_fields']['pnb_flag'] = 'admin_menu_pnb';
		}
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $pmb_lecteurs_localises;
		global $deflt2docs_location;
		
		$this->filters = array(
				'docs_location_id' => '',
				'empr_categ_filter' => '',
				'empr_codestat_filter' => '',
				'pret_date_start' => '',
				'pret_date_end' => '',
				'pret_retour_start' => '',
				'pret_retour_end' => '',
				'short_loan_flag' => '',
                'associated_group' => '',
                'empr_resp_group_location_id' => '',
				'groups' => array(),
                'expl_type' => '',
		);
		
		if (dilicom::is_pnb_active()) {
		    $this->filters['pnb_flag'] = 0;
		}
		
		if(is_array($this->selected_filters) && array_key_exists('empr_location', $this->selected_filters)) {
		    $this->filters['empr_location_id'] = ($pmb_lecteurs_localises ? $deflt2docs_location : 0);
		} else {
		    $this->filters['empr_location_id'] = 0;
		}
		parent::init_filters($filters);
	}
	
	protected function init_override_filters() {
		global $pmb_lecteurs_localises;
		global $deflt2docs_location;
		
		if(array_key_exists('empr_location', $this->selected_filters)) {
			$this->filters['empr_location_id'] = ($pmb_lecteurs_localises ? $deflt2docs_location : 0);
		} else {
			$this->filters['empr_location_id'] = 0;
		}
	}
	
	protected function init_default_selected_filters() {
		global $pmb_lecteurs_localises;
		
		if($pmb_lecteurs_localises) {
			$this->add_selected_filter('empr_location');
		}
		$this->add_selected_filter('doc_location');
		$this->add_selected_filter('expl_type');
		if(!$pmb_lecteurs_localises) {
            $this->add_empty_selected_filter();
		}
		$this->add_selected_filter('empr_categorie');
		$this->add_selected_filter('empr_codestat_one');
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('pret_retour');
	}
	
	protected function _get_query_order() {
	    //Cas particulier : on utilise le(s) groupe(s) associé(s) dans le tri
	    if(!empty($this->applied_sort[0]['by']) && $this->applied_sort[0]['by'] == 'groups') {
	        return ' GROUP BY pret_idempr, pret_idexpl '.parent::_get_query_order();
	    } else {
	        return parent::_get_query_order();
	    }
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'cb_expl' => '4014',
						'cote' => '4016',
						'typdoc' => '294',
						'section_libelle' => '295',
						'lender_libelle' => '651',
						'expl_location_libelle' => 'editions_datasource_expl_location',
						'expl_statut_libelle' => 'editions_datasource_expl_statut',
						'expl_codestat_libelle' => 'editions_datasource_expl_codestat',
						'record' => '233',
						'author' => '234',
						'empr' => 'empr_nom_prenom',
						'pret_date' => 'circ_date_emprunt',
						'pret_retour' => 'circ_date_retour',
						'late_letter' => '369',
				        'groups' => 'groupes_empr',
						'empr_cb' => '35',
						'empr_location_libelle' => 'editions_datasource_empr_location',
						'empr_statut_libelle' => 'editions_datasource_empr_statut',
						'empr_categ_libelle' => 'editions_datasource_empr_categ',
						'empr_codestat_libelle' => 'editions_datasource_empr_codestat',
				)
		);
		
		$this->available_columns['custom_fields'] = array();
// 		$this->add_custom_fields_available_columns('notices');
		$this->add_custom_fields_available_columns('expl', 'id_expl');
		$this->add_custom_fields_available_columns('empr', 'id_empr');
	}
	
	protected function init_no_sortable_columns() {
	    $this->no_sortable_columns = array(
	        'late_letter'
	    );
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'record' :
	            return '';
	        case 'author' :
	            return '';
	        case 'empr' :
	            return 'empr.empr_nom, empr.empr_prenom';
	        case 'pret_retour_empr' :
	            return 'pret_retour, empr.empr_nom, empr.empr_prenom';
	        case 'cote':
	            return 'expl_cote';
	        case 'typdoc':
	            return 'expl_typdoc';
	        case 'pret_date':
	            return 'pret_date';
	        case 'pret_retour':
	            return 'pret_retour';
	        case 'groups':
	            return 'groups, empr.empr_nom, empr.empr_prenom, pret_retour';
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	/**
	 * Initialisation des settings par défaut
	 */
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'unfolded_filters', true);
		$this->set_setting_column('cb_expl', 'text', array('bold' => true));
		$this->set_setting_column('record', 'text', array('bold' => true));
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		global $empr_location_id;
		global $docs_location_id;
		global $empr_categ_filter;
		global $empr_codestat_filter;
		global $empr_groupes_localises, $empr_resp_group_location_id;
		global $pnb_flag;

		if(isset($empr_location_id)) {
			$this->filters['empr_location_id'] = intval($empr_location_id);
		} else {
		    $this->set_filter_from_form('empr_location_id', 'integer');
		}
		if(isset($docs_location_id)) {
			$this->filters['docs_location_id'] = intval($docs_location_id);
		} else {
		    $this->set_filter_from_form('docs_location_id', 'integer');
		}
		if(isset($empr_categ_filter)) {
		    $this->filters['empr_categ_filter'] = intval($empr_categ_filter);
		} else {
			$this->set_filter_from_form('empr_categ_filter', 'integer');
		}
		if(isset($empr_codestat_filter)) {
		    $this->filters['empr_codestat_filter'] = intval($empr_codestat_filter);
		} else {
		    $this->set_filter_from_form('empr_codestat_filter', 'integer');
		}
		if(isset($empr_resp_group_location_id)) {
		    $this->filters['empr_resp_group_location_id'] = intval($empr_resp_group_location_id);
		} elseif(!$empr_groupes_localises) {
		    $this->filters['empr_resp_group_location_id'] = '';
		}
		$this->filters['pnb_flag'] = 0;
		if(isset($pnb_flag)) {
		    $this->filters['pnb_flag'] = 1;
		}
		$this->set_filter_from_form('pret_date_start');
		$this->set_filter_from_form('pret_date_end');
		$this->set_filter_from_form('pret_retour_start');
		$this->set_filter_from_form('pret_retour_end');
		$this->set_filter_from_form('groups', 'integer');
		$this->set_filter_from_form('expl_type', 'integer');
		parent::set_filters_from_form();
	}
	
	protected function get_selection_query($type) {
	    $query = '';
	    switch ($type) {
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
	        case 'docs_groups':
	            $query = 'select id_groupexpl as id, groupexpl_name as label from groupexpl order by label';
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
	
	/**
	 * Retourne l'identifiant du noeud HTML du filtre du formulaire de recherche
	 */
	protected function get_uid_search_filter($property) {
	    switch ($property) {
	        case 'doc_location':
	            return parent::get_uid_search_filter('docs_location_id');
	        case 'empr_location':
	            return parent::get_uid_search_filter('empr_location_id');
	        case 'empr_categorie':
	            return parent::get_uid_search_filter('empr_categ_filter');
	        case 'empr_codestat_one':
	            return parent::get_uid_search_filter('empr_codestat_filter');
	        default:
	            return parent::get_uid_search_filter($property);
	    }
	}
	
	protected function get_search_filter_empr_location() {
	    global $msg;
	    
		return $this->get_search_filter_simple_selection($this->get_selection_query('docs_location'), 'empr_location_id', $msg['all_location']);
		
	}
	
	protected function get_search_filter_doc_location() {
	    global $msg;
	    
	    return $this->get_search_filter_simple_selection($this->get_selection_query('docs_location'), 'docs_location_id', $msg['all_location']);
	}
	
	protected function get_search_filter_empr_categorie() {
	    global $msg;
	    
		return $this->get_search_filter_simple_selection($this->get_selection_query('empr_categ'), 'empr_categ_filter', $msg['categ_all']);
	}
	
	protected function get_search_filter_empr_codestat_one() {
	    global $msg;
	    
		return $this->get_search_filter_simple_selection($this->get_selection_query('empr_codestat'), 'empr_codestat_filter', $msg['codestat_all']);
	}
	
	protected function get_search_filter_pnb_flag() {
	    global $msg;
	    $checked_0 = ( $this->filters['pnb_flag'] == 0 ) ? ' checked="checked" ' : '';
	    $checked_1 = ( $this->filters['pnb_flag'] == 1 ) ? ' checked="checked" ' : '';
	    return '<input type="radio" id="'.$this->objects_type.'_pnb_flag_0" name="pnb_flag" value="0" '.$checked_0.'><label for="'.$this->objects_type.'_pnb_flag_0" >'. $msg[39] .'</label>
                <input type="radio" id="'.$this->objects_type.'_pnb_flag_1" name="pnb_flag" value="1" '.$checked_1.'><label for="'.$this->objects_type.'_pnb_flag_1" >'. $msg[40].'</label>';
	}
	
	protected function get_search_filter_empr_resp_group_location() {
	    global $msg;
	    
	    return docs_location::get_html_select(array($this->filters['empr_resp_group_location_id']),array('id'=> 0,'msg'=> $msg['all_location']),array('id'=>'empr_resp_group_location_id','name'=>'empr_resp_group_location_id'));
	}
	
	protected function get_search_filter_interval_date($name) {
		global $sub;
		
		$readonly_start = false;
		$readonly_end = false;
		switch ($name) {
			case 'pret_retour':
				if($sub == 'retard' || $sub == 'retard_par_date' || $sub == 'overdue_short_loans') {
					$readonly_end = true;
				}
				if($sub == 'unreturned_short_loans') {
					$readonly_start = true;
				}
				break;
			case 'pret_date':
				if($sub == 'unreturned_short_loans') {
					$readonly_end = true;
				}
				break;
		}
		return "<input type='date' name='".$this->objects_type."_".$name."_start' id='".$this->objects_type."_".$name."_start' value='".$this->filters[$name."_start"]."' ".($readonly_start ? "disabled='disabled'" : '')." />
			 - <input type='date' name='".$this->objects_type."_".$name."_end' id='".$this->objects_type."_".$name."_end' value='".$this->filters[$name."_end"]."' ".($readonly_end ? "disabled='disabled'" : '')." />";
	}
	
	protected function get_search_filter_pret_date() {
		return $this->get_search_filter_interval_date('pret_date');
	}
	
	protected function get_search_filter_pret_retour() {
		return $this->get_search_filter_interval_date('pret_retour');
	}
	
	protected function get_search_filter_groups() {
		//TODO : filtre de groupes avec auto-complétion
		return '';
	}
	
	protected function get_search_filter_expl_type() {
	    global $msg;
	    
	    return $this->get_search_filter_simple_selection($this->get_selection_query('docs_type'), 'expl_type', $msg['all']);
	}
	
	/**
	 * Jointure externes SQL pour les besoins des filtres
	 */
	protected function _get_query_join_filters() {
	    global $empr_groupes_localises;
	    
	    $filter_join_query = '';
	    if($empr_groupes_localises && $this->filters['empr_resp_group_location_id']) {
	        $filter_join_query .= " LEFT JOIN empr as coords_resp_group ON coords_resp_group.id_empr=groupe.resp_groupe";
	    }
	    return $filter_join_query;
	}
	
	protected function _add_query_filters() {
		global $empr_groupes_localises;
		
		$this->_add_query_filter_simple_restriction('empr_location_id', 'empr_location', 'integer');
		$this->_add_query_filter_simple_restriction('docs_location_id', 'expl_location', 'integer');
		$this->_add_query_filter_simple_restriction('empr_categ_filter', 'empr_categ', 'integer');
		$this->_add_query_filter_simple_restriction('empr_codestat_filter', 'empr_codestat', 'integer');
		
		$this->_add_query_filter_interval_restriction('pret_date', 'pret_date', 'date');
		
		if(empty($this->_query_filters_with_in_progress)) {
			$this->_add_query_filter_interval_restriction('pret_retour', 'pret_retour', 'date');
		}
		$this->_add_query_filter_simple_restriction('short_loan_flag', 'short_loan_flag', 'integer');
		if($this->filters['associated_group'] == 1) {
			$this->query_filters [] = 'groupe_id IS NOT NULL';
		}
		if($empr_groupes_localises && $this->filters['empr_resp_group_location_id']) {
			$this->query_filters [] = 'coords_resp_group.empr_location="'.$this->filters['empr_resp_group_location_id'].'"';
		}
		$this->_add_query_filter_simple_restriction('pnb_flag', 'pret_pnb_flag', 'integer');
		$this->_add_query_filter_multiple_restriction('groups', 'groupe_id', 'integer');
		
		$this->_add_query_filter_simple_restriction('expl_type', 'expl_typdoc', 'integer');
	}
	
	protected function _compare_objects($a, $b, $index=0) {
	    if($this->applied_sort[$index]['by']) {
	        $sort_by = $this->applied_sort[$index]['by'];
			switch($sort_by) {
				case 'record' :
					return $this->strcmp($a->get_exemplaire()->get_notice_title(), $b->get_exemplaire()->get_notice_title());
					break;
				case 'author':
					return $this->strcmp(gen_authors_header(get_notice_authors($a->get_exemplaire()->id_notice)), gen_authors_header(get_notice_authors($b->get_exemplaire()->id_notice)));
					break;
				case 'empr':
					return $this->strcmp(emprunteur::get_name($a->id_empr), emprunteur::get_name($b->id_empr));
					break;
				case 'late_letter':
					return '';
					break;
				default :
					return parent::_compare_objects($a, $b, $index);
					break;
			}
		}
	}
	
	protected function get_grouped_label($object, $property) {
		global $charset;
	    
	    $grouped_label = '';
	    switch($property) {
	        case 'groups':
	            $groupes = emprunteur::get_groupes($object->id_empr);
	            if(count($groupes)) {
	                $grouped_label = array();
	                foreach ($groupes as $groupe) {
	                	$grouped_label[] = html_entity_decode(strip_tags($groupe), ENT_QUOTES, $charset);
	                }
	            }
	            break;
	        default:
	            $grouped_label = parent::get_grouped_label($object, $property);
	            break;
	    }
	    return $grouped_label;
	}
	
	protected function _get_object_property_cote($object) {
		return $object->get_exemplaire()->cote;
	}
	
	protected function _get_object_property_typdoc($object) {
		return $object->get_exemplaire()->typdoc;
	}
	
	protected function _get_object_property_section_libelle($object) {
		return $object->get_exemplaire()->section;
	}
	
	protected function _get_object_property_lender_libelle($object) {
		return $object->owner;
	}
	
	protected function _get_object_property_expl_location_libelle($object) {
		return $object->get_exemplaire()->location;
	}
	
	protected function _get_object_property_expl_statut_libelle($object) {
		return $object->statut_doc;
	}
	
	protected function _get_object_property_expl_codestat_libelle($object) {
		$docs_codestat = new docs_codestat($object->get_exemplaire()->codestat_id);
		return $docs_codestat->libelle;
	}
	
	protected function _get_object_property_empr_cb($object) {
		return emprunteur::get_cb_empr($object->id_empr);
	}
	
	protected function _get_object_property_empr_location_libelle($object) {
		return $object->get_emprunteur()->empr_location_l;
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
	
	protected function _get_object_property_empr($object) {
		return emprunteur::get_name($object->id_empr);
	}
	
	protected function _get_object_property_groups($object) {
		return implode(', ', emprunteur::get_groupes($object->id_empr));
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset, $base_path;
		global $empr_show_caddie;
		global $pmb_short_loan_management;
		global $biblio_email;
		
		$content = '';
		switch($property) {
			case 'cb_expl':
				$content .= exemplaire::get_cb_link($object->cb_expl);
				break;
			case 'record':
				$content .= "";
				if (SESSrights & CATALOGAGE_AUTH) {
					if ($object->get_exemplaire()->id_notice) {
						$query = "select tit1 as title from notices where notice_id = ".$object->get_exemplaire()->id_notice;
						$result = pmb_mysql_query($query);
						$content .= "<a href='".notice::get_permalink($object->get_exemplaire()->id_notice)."' ".($object->retard ? "style='color:RED'" : "").">".pmb_mysql_result($result, 0, 'title')."</a>"; // notice de monographie
					} elseif ($object->get_exemplaire()->id_bulletin) {
						$query = "select notices_s.tit1 as title, bulletin_numero, mention_date from bulletins
								LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id
								where bulletin_id = ".$object->get_exemplaire()->id_bulletin;
						$result = pmb_mysql_query($query);
						$row = pmb_mysql_fetch_object($result);
						$record_title = $row->title;
						if($row->bulletin_numero) {
							$record_title .= ' '.$row->bulletin_numero;
						}
						if($row->mention_date) {
							$record_title .= ' ('.$row->mention_date.')';
						}
						$content .= "<a href='".bulletinage::get_permalink($object->get_exemplaire()->id_bulletin)."' ".($object->retard ? "style='color:RED'" : "").">".$record_title."</a>"; // notice de bulletin
					} else {
						$content .= $object->get_exemplaire()->get_notice_title();
					}
				} else {
					$content .= $object->get_exemplaire()->get_notice_title();
				}
				break;
			case 'author':
				$content .= "<span ".($object->retard ? "style='color:RED'" : "").">".gen_authors_header(get_notice_authors($object->get_exemplaire()->id_notice))."</span>";
				break;
			case 'empr':
				if ($empr_show_caddie) {
					$content .= "<img src='".get_url_icon('basket_empr.gif')."' class='align_middle' alt='basket' title=\"".$msg[400]."\" onClick=\"openPopUp('./cart.php?object_type=EMPR&item=".$object->id_empr."', 'cart')\">&nbsp;";
				}
				$content .= "<a href='".$base_path."/circ.php?categ=pret&form_cb=".rawurlencode(emprunteur::get_cb_empr($object->id_empr))."'>".emprunteur::get_name($object->id_empr)."</a>";
				break;
			case 'pret_date':
				$content .= $object->date_pret_display;
				if($pmb_short_loan_management && $this->filters['short_loan_flag']) {
					$content .= "&nbsp;<img src='".get_url_icon('chrono.png')."' alt='".$msg['short_loan']."' title='".$msg['short_loan']."'/>";
				}
				break;
			case 'pret_retour':
				$content .= "<span ".($object->retard ? "style='color:RED'" : "")."><b>".$object->date_retour_display."</b></span>";
				break;
			case 'late_letter':
				if ($object->retard) {
					$imprime_click = "onclick=\"openPopUp('./pdf.php?pdfdoc=lettre_retard&cb_doc=".$object->id_expl."&id_empr=".$object->id_empr."', 'lettre'); return(false) \"";
					$mail_click = "onclick=\"if (confirm('".$msg["mail_retard_confirm"]."')) {openPopUp('./mail.php?type_mail=mail_retard&cb_doc=".$object->id_expl."&id_empr=".$object->id_empr."', 'mail');} return(false) \"";
					$content .= "<span class='".$this->objects_type."_list_cell_content_late_letter_print'>";
					$content .= "<a href=\"#\" ".$imprime_click."><img src='".get_url_icon('new.gif')."' title='".htmlentities($msg["lettre_retard"], ENT_QUOTES, $charset)."' alt='".htmlentities($msg["lettre_retard"], ENT_QUOTES, $charset)."' style='border:0px' /></a>";
					$content .= "</span>";
					if ((emprunteur::get_mail_empr($object->id_empr))&&($biblio_email)) {
						$content .= "<span class='".$this->objects_type."_list_cell_content_late_letter_mail'>";
						$content .= "<a href=\"#\" ".$mail_click."><img src='".get_url_icon('mail.png')."' title='".htmlentities($msg['mail_retard'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['mail_retard'], ENT_QUOTES, $charset)."' style='border:0px' /></a>";
						$content .= "</span>";
					}
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_display_cell_html_value($object, $value) {
		$value = str_replace('!!id!!', $object->id_empr.'_'.$object->id_expl, $value);
		$display = $this->get_display_format_cell($value);
		return $display;
	}
	
	protected function _get_query_property_filter($property) {
	    switch ($property) {
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
	        case 'expl_group':
	            return "select groupexpl_name from groupexpl where id_groupexpl = ".$this->filters[$property];
	        case 'expl_groups':
	            return "select groupexpl_name from groupexpl where id_groupexpl IN (".implode(',', $this->filters[$property]).")";
	    }
	    return '';
	}
	
	protected function _get_query_human_empr_location() {
		if($this->filters['empr_location_id']) {
			$docs_location = new docs_location($this->filters['empr_location_id']);
			return $docs_location->libelle;
		}
		return '';
	}
	
	protected function _get_query_human_doc_location() {
		if($this->filters['docs_location_id']) {
			$docs_location = new docs_location($this->filters['docs_location_id']);
			return $docs_location->libelle;
		}
		return '';
	}
	
	protected function _get_query_human_pret_date() {
		return $this->_get_query_human_interval_date('pret_date');
	}
	
	protected function _get_query_human_pret_retour() {
		return $this->_get_query_human_interval_date('pret_retour');
	}
	
	protected function _get_query_human_groups() {
// 		return '';
	}
	
	protected function _get_query_human() {
		global $msg;
		
		$humans = $this->_get_query_human_main_fields();
		if($this->filters['empr_categ_filter']) {
			$query = "select libelle from empr_categ where id_categ_empr = ".$this->filters['empr_categ_filter'];
			$humans['empr_categorie'] = $this->_get_label_query_human_from_query($msg['editions_filter_empr_categ'], $query);
		}
		if($this->filters['empr_codestat_filter']) {
			$query = "select libelle from empr_codestat where idcode = ".$this->filters['empr_codestat_filter'];
			$humans['empr_codestat_one'] = $this->_get_label_query_human_from_query($msg['editions_filter_empr_codestat'], $query);
		}
		if($this->filters['empr_resp_group_location_id']) {
		    $docs_location = new docs_location($this->filters['empr_resp_group_location_id']);
		    $humans['empr_resp_group_location'] = $this->_get_label_query_human($msg['empr_resp_group_location'], $docs_location->libelle);
		}
		return $this->get_display_query_human($humans);
	}
	
	protected function get_infos() {
		$infos=array();
		$this->_query_filters_with_in_progress = true;
		$infos['in_progress'] = 0;
		$infos['late'] = 0;
		$query = "select IF(pret_retour>=CURDATE(),'0','1') as retard, count(pret_idexpl) as combien
			FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id )
			LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id)
			LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id)
			JOIN pret ON pret_idexpl = expl_id
			JOIN empr ON empr.id_empr = pret.pret_idempr
			JOIN docs_type ON expl_typdoc = idtyp_doc 	
		";
		$query .= $this->_get_query_filters();
		$query.= " group by retard ";
		$result = pmb_mysql_query($query);
		while($row = pmb_mysql_fetch_object($result)) {
			if($row->retard) {
				$infos['late'] += $row->combien;
			}
			$infos['in_progress'] += $row->combien;
		}
		$this->_query_filters_with_in_progress = false;
		return $infos;
	}
	
	public function get_display_late() {
		global $msg;
		// construction du message ## prêts en retard sur un total de ##
		$display = $msg['n_retards_sur_total_de'];
		$infos = $this->get_infos();
		$display = str_replace ("!!nb_retards!!", $infos['late'], $display);
		$display = str_replace ("!!nb_total!!", $infos['in_progress'], $display);
		return $display;
	}	
}