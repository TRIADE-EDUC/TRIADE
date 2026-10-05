<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_loans_ui.class.php,v 1.7.2.2 2024/12/31 10:46:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_opac_loans_ui extends list_opac_ui {
	
// 	protected $_query_filters_with_in_progress;
	
	protected function _get_query_base() {
		/* Conservation des anciens éléments du select
		 	date_format(pret_date, '".$msg['format_date']."') as aff_pret_date, ";
			$sql .= "date_format(pret_retour, '".$msg['format_date']."') as aff_pret_retour, ";
			$sql .= "IF(pret_retour>=CURDATE(),0,1) as retard, ";
			$sql .= "id_empr, empr_nom, empr_prenom, empr_mail, empr_cb, expl_cote, expl_cb, expl_notice, expl_bulletin, notices_m.notice_id as idnot, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, tdoc_libelle, ";
			$sql .= "short_loan_flag
		 */
		$query = 'select pret_idempr, pret_idexpl 
			FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id )
				LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id)
				LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id)
				JOIN pret ON pret_idexpl = expl_id
				JOIN empr ON empr.id_empr = pret.pret_idempr
				JOIN docs_type ON expl_typdoc = idtyp_doc 	
				';
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
				'empr_login' => '',
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
				'groups' => array()
		);
		
		if (dilicom::is_pnb_active()) {
			$this->filters['pnb_flag'] = 0;
		}
		
		if(array_key_exists('empr_location', $this->selected_filters)) {
			$this->filters['empr_location_id'] = ($pmb_lecteurs_localises ? $deflt2docs_location : 0);
		} else {
			$this->filters['empr_location_id'] = 0;
		}
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('pret_retour');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		global $opac_pret_prolongation, $allow_prol;
		
		$this->available_columns =
		array('main_fields' =>
				array(
// 						'cb_expl' => '4014',
// 						'cote' => '4016',
						'typdoc' => 'typdoc_support',
// 						'section_libelle' => '295',
// 						'lender_libelle' => '651',
// 						'expl_location_libelle' => 'editions_datasource_expl_location',
// 						'expl_statut_libelle' => 'editions_datasource_expl_statut',
// 						'expl_codestat_libelle' => 'editions_datasource_expl_codestat',
						'record' => 'title',
						'author' => 'author',
						'empr' => 'extexpl_emprunteur',
						'pret_date' => 'date_loan',
						'pret_retour' => 'date_back',
						'late' => 'empr_late',
// 				        'groups' => 'groupes_empr',
// 						'empr_cb' => '35',
						'empr_location_libelle' => 'editions_datasource_empr_location',
// 						'empr_statut_libelle' => 'editions_datasource_empr_statut',
// 						'empr_categ_libelle' => 'editions_datasource_empr_categ',
// 						'empr_codestat_libelle' => 'editions_datasource_empr_codestat',
				)
		);
		if($opac_pret_prolongation && $allow_prol) {
			$this->available_columns['main_fields']['nb_prolongation'] = 'opac_titre_champ_nb_prolongation';
			$this->available_columns['main_fields']['prolongation'] = 'opac_titre_champ_prolongation';
		}
		$this->available_columns['custom_fields'] = array();
// 		$this->add_custom_fields_available_columns('notices');
		$this->add_custom_fields_available_columns('expl', 'id_expl');
		$this->add_custom_fields_available_columns('empr', 'id_empr');
	}
	
// 	protected function init_default_columns() {
// 		global $opac_pret_prolongation, $allow_prol, $lvl;
		
// 		$this->add_column('record');
// 		$this->add_column('author');
// 		$this->add_column('typdoc');
// 		$this->add_column('pret_date');
// 		$this->add_column('pret_retour');
// 		if($opac_pret_prolongation==1 && $allow_prol) {
// 			$this->add_column('nb_prolongation');
// 			$this->add_column('prolongation');
// 		}
// 		if ($lvl!="late") {
// 			$this->add_column('late');
// 		}
// 	}
	
// 	public function init_applied_group($applied_group=array()) {
// 		$this->applied_group = array(0 => 'expl_location_libelle');
// 	}
	
	protected function init_no_sortable_columns() {
	    $this->no_sortable_columns = array(
	        'prolongation'
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
		$this->set_setting_column('typdoc', 'align', 'left');
		$this->set_setting_column('record', 'align', 'left');
		$this->set_setting_column('author', 'align', 'left');
		$this->set_setting_column('pret_date', 'datatype', 'date');
		$this->set_setting_column('pret_retour', 'datatype', 'date');
		$this->set_setting_column('prolongation', 'exportable', 0);
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('pret_date_start');
		$this->set_filter_from_form('pret_date_end');
		$this->set_filter_from_form('pret_retour_start');
		$this->set_filter_from_form('pret_retour_end');
		$this->set_filter_from_form('groups', 'integer');
		parent::set_filters_from_form();
	}
	
	protected function get_search_filter_empr_location() {
		return docs_location::gen_combo_box_empr($this->filters['empr_location_id']);
	}
	
	protected function get_search_filter_doc_location() {
		return docs_location::gen_combo_box_docs($this->filters['docs_location_id']);
	}
	
	protected function get_search_filter_empr_categorie() {
		return emprunteur::gen_combo_box_categ($this->filters['empr_categ_filter']);
	}
	
	protected function get_search_filter_empr_codestat_one() {
		return emprunteur::gen_combo_box_codestat($this->filters['empr_codestat_filter']);
	}
	
	protected function get_search_filter_pnb_flag() {
		global $msg;
		$checked_0 = ( $this->filters['pnb_flag'] == 0 ) ? ' checked="checked" ' : '';
		$checked_1 = ( $this->filters['pnb_flag'] == 1 ) ? ' checked="checked" ' : '';
		return '<input type="radio" id="'.$this->objects_type.'_pnb_flag_0" name="pnb_flag" value="0" '.$checked_0.'><label for="'.$this->objects_type.'_pnb_flag_0" >'. $msg[39] .'</label>
                <input type="radio" id="'.$this->objects_type.'_pnb_flag_1" name="pnb_flag" value="1" '.$checked_1.'><label for="'.$this->objects_type.'_pnb_flag_1" >'. $msg[40].'</label>';
	}
	
	protected function get_search_filter_groups() {
		//TODO : filtre de groupes avec auto-complétion
		return '';
	}
	
	protected function _add_query_filters() {
		global $empr_groupes_localises;
		
		$this->_add_query_filter_simple_restriction('empr_login', 'empr_login');
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
	}
	
	/**
	 * Fonction de callback
	 * @param object $a
	 * @param object $b
	 * @param number $index
	 */
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
				default :
					return parent::_compare_objects($a, $b, $index);
					break;
			}
		}
	}
	
	protected function _get_object_property_cote($object) {
		return $object->get_exemplaire()->cote;
	}
	
	protected function _get_object_property_typdoc($object) {
		return $object->get_exemplaire()->typdoc;
	}
	
	protected function _get_object_property_record($object) {
	    global $msg;
	    
	    if ($object->get_exemplaire()->id_notice) {
	        // affiche la notice correspondant à la réservation
	        return notice::get_notice_title($object->get_exemplaire()->id_notice);
	    }  else {
	        // c'est un bulletin donc j'affiche le nom de périodique et le nom du bulletin (date ou n°)
	        $requete = "SELECT bulletin_id, bulletin_numero, bulletin_notice, mention_date, date_date, date_format(date_date, '".$msg['format_date_sql']."') as aff_date_date FROM bulletins WHERE bulletin_id='".$object->get_exemplaire()->id_bulletin."'";
	        $res = pmb_mysql_query($requete);
	        $obj = pmb_mysql_fetch_object($res);
	        return $obj->bulletin_titre;
	    }
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
		$docs_location = new docs_location($object->get_emprunteur()->location);
		return $docs_location->libelle;
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
	
// 	protected function _get_object_property_groups($object) {
// 		return implode(', ', emprunteur::get_groupes($object->id_empr));
// 	}

	protected function _get_object_property_nb_prolongation($object) {
		$object->is_extendable();
		return $object->nb_prolongation."/".$object->pret_nombre_prolongation;
	}
	
	protected function _get_object_property_prolongation($object) {
		return $object->is_extendable();
	}
	
	protected function _get_object_property_late($object) {
		global $msg, $charset;

		if ($object->retard) {
			return "<span style='font-weight: 600' aria-label='".htmlentities($msg['empr_late_desc'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['empr_late_desc'],ENT_QUOTES,$charset)."'>&times;</span>";
		} else {
			return "&nbsp;";
		}
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		global $opac_rgaa_active;
	    if(!empty($this->selected_columns[$property])) {
			if($opac_rgaa_active){
				$attributes = array(
    				'data_column_name' => $this->_get_label_cell_header($this->selected_columns[$property])
				);
			}else{
				$attributes = array(
    				'column_name' => $this->_get_label_cell_header($this->selected_columns[$property])
    			);
			}
    	    
	    } else {
	        $attributes = array();
	    }
		switch($property) {
			case 'prolongation':
			case 'date_prolongation':
				break;
			default:
				if(!$opac_rgaa_active){
					if ($object->get_exemplaire()->id_notice) {
						$attributes['onclick'] = "window.location=\"./index.php?lvl=notice_display&id=".$object->get_exemplaire()->id_notice."&seule=1\"";
					} else {
						$attributes['onclick'] = "window.location=\"./index.php?lvl=bulletin_display&id=".$object->get_exemplaire()->id_bulletin."\"";
					}
				}
				break;
		}
		if($property == 'late' && $object->retard) {
	        $attributes['class'] = 'expl-empr-retard';
		}
		return $attributes;
	}
	
	protected function get_cell_content($object, $property) {
	    global $msg, $charset, $lvl;
		global $opac_rgaa_active;
		
		switch($property) {
		    case 'record':
		        $content = '';
		        if ($object->get_exemplaire()->id_notice) {
		            // affiche la notice correspondant à la réservation
		            $notice = new notice($object->get_exemplaire()->id_notice);
					if($opac_rgaa_active){
						$content .= "<a href='./index.php?lvl=notice_display&id=".$object->get_exemplaire()->id_notice."&seule=1' >".$notice->tit1."</a>";
					}else{
						$content .= $notice->tit1;
					}
		        }  else {
		            // c'est un bulletin donc j'affiche le nom de périodique et le nom du bulletin (date ou n°)
		            $requete = "SELECT bulletin_id, bulletin_numero, bulletin_notice, mention_date, date_date, date_format(date_date, '".$msg['format_date_sql']."') as aff_date_date FROM bulletins WHERE bulletin_id='".$object->get_exemplaire()->id_bulletin."'";
		            $res = pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($res)) {
			            $obj = pmb_mysql_fetch_object($res) ;
			            $notice = new notice($obj->bulletin_notice);
			            $content = pmb_bidi($notice->tit1);
			            if ($obj->bulletin_numero) {
			                $content .= ' '.$obj->bulletin_numero;
			            }
		        	    // affichage de la mention de date utile : mention_date si existe, sinon date_date
			            if ($obj->mention_date) {
			                $content .= pmb_bidi(" (".$obj->mention_date.")");
			            } elseif ($obj->date_date) {
		    	            $content.= pmb_bidi(" (".$obj->aff_date_date.")");
		        	    }
					}
		        }
		        // récupération du titre de série
		        $titre_serie="";
				if (!empty($notice)) {
			        if ($notice->tparent_id) {
			            $parent = new serie($notice->tparent_id);
			            $titre_serie = $parent->name;
		    	        if($notice->tnvol)
		        	        $titre_serie .= ', '.$notice->tnvol;
		        	}
				}
		        if($titre_serie) {
		            $content = $titre_serie.'. '.$content;
		        }
			
		        return $content;
			case 'author':
				$responsab = array("responsabilites" => array(),"auteurs" => array());  // les auteurs
				$responsab = get_notice_authors($object->get_exemplaire()->id_notice) ;
				
				//$this->responsabilites
				$as = array_search ("0", $responsab["responsabilites"]) ;
				if ($as!== FALSE && $as!== NULL) {
					$auteur_0 = $responsab["auteurs"][$as] ;
					$auteur = new auteur($auteur_0["id"]);
					$mention_resp = $auteur->get_isbd();
				} else {
					$aut1_libelle = array();
					$as = array_keys ($responsab["responsabilites"], "1" ) ;
					for ($i = 0 ; $i < count($as) ; $i++) {
						$indice = $as[$i] ;
						$auteur_1 = $responsab["auteurs"][$indice] ;
						$auteur = new auteur($auteur_1["id"]);
						$aut1_libelle[]= $auteur->get_isbd();
					}
					$mention_resp = implode (", ",$aut1_libelle) ;
				}
				
				$mention_resp ? $auteur = $mention_resp : $auteur="";
				
				return $auteur;
			case 'late':
				return $this->_get_object_property_late($object); //conserve le HTML
			case 'prolongation':
				$prolongation = $object->is_extendable();
				if ($prolongation) {
					return "<a href='./empr.php?prolongation=".$object->aff_date_prolongation."&prolonge_id=".$object->id_expl."&tab=loan_reza&lvl=$lvl#empr-loan'>".$object->aff_date_prolongation."</a>";
				} else {
					return "<img src='".get_url_icon("no_prolongation.png")."' style='border:0px' title='".htmlentities($object->no_prolong_explanation,ENT_QUOTES,$charset)."' alt=''/>";
				}
			default :
				return parent::get_cell_content($object, $property);
		}
	}
	
	protected function get_class_objects_list() {
		global $lvl;
		
		if ($lvl == 'late') {
			return "liste-expl-empr-late ".parent::get_class_objects_list();
		} else {
			return "liste-expl-empr-all ".parent::get_class_objects_list();
		}
	}
	
// 	protected function get_display_cell_html_value($object, $value) {
// 		$value = str_replace('!!id!!', $object->id_empr.'_'.$object->id_expl, $value);
// 		$display = $this->get_display_format_cell($value);
// 		return $display;
// 	}
	
// 	protected function get_infos() {
// 		$infos=array();
// 		$this->_query_filters_with_in_progress = true;
// 		$infos['in_progress'] = 0;
// 		$infos['late'] = 0;
// 		$query = "select IF(pret_retour>=CURDATE(),'0','1') as retard, count(pret_idexpl) as combien
// 			FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id )
// 			LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id)
// 			LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id)
// 			JOIN pret ON pret_idexpl = expl_id
// 			JOIN empr ON empr.id_empr = pret.pret_idempr
// 			JOIN docs_type ON expl_typdoc = idtyp_doc 	
// 		";
// 		$query .= $this->_get_query_filters();
// 		$query.= " group by retard ";
// 		$result = pmb_mysql_query($query);
// 		while($row = pmb_mysql_fetch_object($result)) {
// 			if($row->retard) {
// 				$infos['late'] += $row->combien;
// 			}
// 			$infos['in_progress'] += $row->combien;
// 		}
// 		$this->_query_filters_with_in_progress = false;
// 		return $infos;
// 	}	
}