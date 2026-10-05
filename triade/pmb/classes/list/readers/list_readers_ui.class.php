<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_readers_ui.class.php,v 1.82.2.1.2.2 2025/05/13 13:28:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_readers_ui extends list_ui {
	
    protected static $used_filter_list_mode;
    
    protected static $filter_list;
    
    protected static $display_filters;
    
    protected static $correspondence_filters_fields;
    
    protected static $correspondence_columns_fields;
    
    protected static $empr_languages;
    
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
	    if(!empty(static::$used_filter_list_mode)) {
	        static::init_correspondence_filters_fields();
	        static::init_correspondence_columns_fields();
	    }
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function _get_query_base() {
		$query = 'SELECT DISTINCT id_empr, CONCAT(empr_nom, " ", empr_prenom) AS empr_nom_prenom FROM empr
				JOIN empr_statut ON empr.empr_statut=empr_statut.idstatut
				JOIN empr_categ ON empr.empr_categ=empr_categ.id_categ_empr
				JOIN empr_codestat ON empr.empr_codestat = empr_codestat.idcode
                JOIN docs_location ON empr.empr_location=docs_location.idlocation';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new emprunteur($row->id_empr);
	}
		
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		global $pmb_lecteurs_localises;
		
		if(!empty(static::$used_filter_list_mode)) {
		    $this->available_filters = array();
		    $this->available_filters['main_fields'] = array();
		    //Ajout des champs fixes en tant que filtres disponibles
		    foreach (static::$filter_list->fixedfields as $fixedfield) {
		        //est-ce que le champ est filtrable
		        if ($fixedfield["FILTERABLE"]=="yes") {
		            if(!empty(static::$correspondence_filters_fields['main_fields'][$fixedfield["VALUE"]])) {
		                $correspondence_filter = static::$correspondence_filters_fields['main_fields'][$fixedfield["VALUE"]];
		            } else {
		                $correspondence_filter = $fixedfield["VALUE"];
		            }
		            $this->available_filters['main_fields'][$correspondence_filter] = str_replace('msg:', '', $fixedfield["NAME"]);
		        }
		    }
		    //Ajout des champs spéciaux en tant que filtres disponibles
		    foreach (static::$filter_list->specialfields as $specialfield) {
		        if(!empty(static::$correspondence_filters_fields['main_fields'][$specialfield["ID"]])) {
		            $correspondence_filter = static::$correspondence_filters_fields['main_fields'][$specialfield["ID"]];
		        } else {
		            $correspondence_filter = $specialfield["ID"];
		        }
		        $this->available_filters['main_fields'][$correspondence_filter] = str_replace('msg:', '', $specialfield["NAME"]);
		    }
		} else {
		    $this->available_filters =
		    array('main_fields' =>
		        array(
	        		'simple_search' => '34',
		            'categorie' => 'editions_filter_empr_categ',
		            'categories' => 'dsi_ban_form_categ_lect',
		            'groups' => 'dsi_ban_form_groupe_lect',
	        		'id' => 'editions_datasource_id_empr',
	        		'name' => 'dsi_ban_abo_empr_nom',
		            'has_mail' => 'dsi_ban_abo_mail',
		            'has_affected' => 'dsi_ban_lecteurs_affectes',
		            'mail' => 'email',
		            'codestat_one' => 'editions_filter_empr_codestat',
		            'codestat' => '24',
		            'status' => 'statut_empr',
		            'date_adhesion' => 'empr_date_adhesion',
		            'date_expiration' => 'readerlist_dateexpiration',
		            'date_creation' => 'date_creation_query',
		            'cp' => 'acquisition_cp',
		            'villes' => 'ville_empr',
		            'birth_dates' => 'year_empr',
	        		'caddies' => 'caddie_de_EMPR',
		            'languages' => '537'
		        )
		    );
		    if($pmb_lecteurs_localises) {
		        $this->available_filters['main_fields']['location'] = 'editions_filter_empr_location';
		        $this->available_filters['main_fields']['locations'] = '21';
		    }
		}
		$this->available_filters['custom_fields'] = array();
		$this->add_custom_fields_available_filters('empr', 'id_empr');
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $pmb_lecteurs_localises, $deflt2docs_location;
		
		$this->filters = array(
                'simple_search' => '',
				'empr_statut_edit' => '',
				'empr_categ_filter' => '',
				'empr_codestat_filter' => '',
				'group' => '',
				'status' => array(),
				'categories' => array(),
				'codestat' => array(),
				'groups' => array(),
				'id' => 0,
				'name' => '',
				'mail' => '',
				'has_mail' => 0,
				'has_affected' => 0,
				'date_creation_start' => '',
				'date_creation_end' => '',
				'date_adhesion_start' => '',
				'date_adhesion_end' => '',
				'date_expiration_start' => '',
				'date_expiration_end' => '',
				'date_expiration_limit' => '',
				'change_categ' => '',
				'cp' => array(),
				'villes' => array(),
				'birth_dates' => array(),
				'supposed_level' => array(),
				'last_level_validated' => array(),
				'last_dates' => array(),
                'types_abts' => array(),
				'caddies' => array(),
                'languages' => array(),
                'empr_ids' => array(),
				'expl_locations' => array(),
				'id_diffusion' => 0
		);
		if(static::class == 'list_readers_bannette_ui' || static::class == 'list_readers_relances_ui') {
			$this->filters['empr_location_id'] = '';
			$this->filters['locations'] = ($pmb_lecteurs_localises ? array($deflt2docs_location) : array());
		} else if(static::class == 'list_readers_group_ui' || static::class == 'list_readers_recouvr_ui' || static::class == 'list_readers_bannette_diffusion_ui') {
            $this->filters['empr_location_id'] = '';
            $this->filters['locations'] = array();
		} else {
			$this->filters['empr_location_id'] = ($pmb_lecteurs_localises ? $deflt2docs_location : '');
			$this->filters['locations'] = array();
		}
		if(!empty($filters['empr_location_id'])) {
			if(empty($this->selected_filters['location']) && !empty($this->selected_filters['locations'])) {
				$filters['locations'] = array($filters['empr_location_id']);
				$filters['empr_location_id'] = '';
			}
		}
		parent::init_filters($filters);
		
		if(!empty(static::$used_filter_list_mode)) {
			$initialization = $this->objects_type.'_initialization';
			global ${$initialization};
			if(isset(${$initialization}) && ${$initialization} == 'reset') {
				$from_initialization = true;
			} else {
				$from_initialization = false;
			}
			static::set_filter_list_from_filters_ui($this->filters, $from_initialization);
			if (!static::$filter_list->error) {
				//Permet de regénérer la globale $all_level (relance::filter_niveau)
				static::$display_filters = static::$filter_list->display_filters();
			}
		}
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
	    global $empr_show_caddie, $sub;
	    
	    if(!empty(static::$used_filter_list_mode)) {
	        $this->available_columns = array();
	        $this->available_columns['main_fields'] = array();
	        //Ajout des champs fixes en tant que filtres disponibles
	        foreach (static::$filter_list->fixedfields as $fixedfield) {
	            if(!empty(static::$correspondence_columns_fields['main_fields'][$fixedfield["VALUE"]])) {
	                $correspondence_column = static::$correspondence_columns_fields['main_fields'][$fixedfield["VALUE"]];
	            } else {
	                $correspondence_column = $fixedfield["VALUE"];
	            }
	            $this->available_columns['main_fields'][$correspondence_column] = str_replace('msg:', '', $fixedfield["NAME"]);
	        }
	        //Ajout des champs spéciaux en tant que filtres disponibles
	        foreach (static::$filter_list->specialfields as $specialfield) {
	            if(!empty(static::$correspondence_columns_fields['main_fields'][$specialfield["ID"]])) {
	                $correspondence_column = static::$correspondence_columns_fields['main_fields'][$specialfield["ID"]];
	            } else {
	                $correspondence_column = $specialfield["ID"];
	            }
	            $this->available_columns['main_fields'][$correspondence_column] = str_replace('msg:', '', $specialfield["NAME"]);
	        }
	    } else {
	        $this->available_columns =
	        array('main_fields' =>
	            array(
	            	'id' => '1601',
	                'cb' => 'code_barre_empr',
	                'empr_name' => 'nom_prenom_empr',
	                'adr1' => 'adresse_empr',
	                'cp' => 'acquisition_cp',
	                'ville' => 'ville_empr',
	                'birth' => 'year_empr',
	                'mail' => 'email',
            		'aff_date_adhesion' => 'empr_date_adhesion',
            		'aff_date_expiration' => 'readerlist_dateexpiration',
            		'aff_date_prolong' => 'group_empr_date_prolong',
	                'empr_statut_libelle' => 'statut_empr',
	                'categ_libelle' => 'categ_empr',
	            	'codestat_libelle' => 'codestat_empr',
	                'relance' => 'relance_imprime',
	                'location' => 'localisation_sort',
	                'groups' => 'groupe_empr',
	                'nb_loans' => 'empr_nb_pret',
            		'nb_loans_late' => 'nb_loans_late',
	                'add_empr_cart' => 'add_empr_cart',
	                'type_abt' => 'type_abt_empr',
            		'tel1' => '73',
            		'tel2' => '73tel2',
            		'nb_resas_and_validated' => 'groupes_nb_resa_dont_valides',
            		'nb_loans_including_late' => 'nb_loans_including_late',
	                'nb_loans_including_resas' => 'nb_loans_including_resas',
            		'empr_msg' => 'empr_msg',
	                'empr_lang' => 'empr_langue_opac',
	            )
	        );
	    }
	    if ($empr_show_caddie) {
	        $this->available_columns['main_fields']['add_empr_cart'] = 'add_empr_cart';
	    }
	    if(get_called_class() == 'list_readers_edition_ui' && $sub == 'categ_change') {
	    	$this->available_columns['main_fields']['categ_change'] = 'empr_categ_change_prochain';
	    }
	    $this->available_columns['custom_fields'] = array();
	    $this->add_custom_fields_available_columns('empr', 'id');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('cb', 'text', array('bold' => true));
		$this->set_setting_column('nb_loans', 'datatype', 'integer');
		$this->set_setting_column('nb_loans_late', 'datatype', 'integer');
	}
	
	protected function init_default_selected_filters() {
	    if(!empty(static::$used_filter_list_mode)) {
	        if(empty($this->selected_filters)) {
	            $this->selected_filters = array();
	        }
	        $filtercolumns = explode(",",static::$filter_list->filtercolumns);
	        foreach ($filtercolumns as $filtercolumn) {
	            if(substr($filtercolumn,0,2) == "#e") {
	                $parametres_perso = $this->get_custom_parameters_instance('empr');
	                $custom_name = $parametres_perso->get_field_name_from_id(substr($filtercolumn,2));
	                $this->add_selected_filter($custom_name);
	            } elseif(substr($filtercolumn,0,2) == "#p") {
	                $parametres_perso = $this->get_custom_parameters_instance('pret');
	                $custom_name = $parametres_perso->get_field_name_from_id(substr($filtercolumn,2));
	                $this->add_selected_filter($custom_name);
	            } else {
	                $this->add_selected_filter(static::$correspondence_filters_fields['main_fields'][$filtercolumn]);
	            }
	        }
	    } else {
	        parent::init_default_selected_filters();
	    }
	}
	
	protected function init_no_sortable_columns() {
	    $this->no_sortable_columns = array(
	        'add_empr_cart',
	    );
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
		global $nb_per_page_empr;
		parent::init_default_pager();
		$this->pager['nb_per_page'] = ($nb_per_page_empr ? $nb_per_page_empr : 10);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    if(!empty(static::$used_filter_list_mode)) {
	        $sortablecolumns = explode(",", static::$filter_list->sortablecolumns);
	        foreach ($sortablecolumns as $sortcolumn) {
	            $fixedfield = static::$filter_list->fixedfields[$sortcolumn];
	            //est-ce que le champ est triable
	            if ($fixedfield["SORTABLE"]=="yes") {
	                if(!empty(static::$correspondence_columns_fields['main_fields'][$fixedfield["VALUE"]])) {
	                    $correspondence_column = static::$correspondence_columns_fields['main_fields'][$fixedfield["VALUE"]];
	                } else {
	                    $correspondence_column = $fixedfield["VALUE"];
	                }
	                $this->add_applied_sort($correspondence_column);
	            }
	        }
	    } else {
	        $this->add_applied_sort('empr_name');
	    }
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'id':
	            return 'id_empr';
	        case 'empr_name' :
	            return 'empr_nom_prenom';
	        case 'cb':
	            return 'empr_cb';
	        case 'adr1':
	            return 'empr_adr1';
	        case 'ville':
	            return 'empr_ville';
	        case 'birth':
	            return 'empr_year';
	        case 'mail':
	            return 'empr_mail';
	        case 'tel1':
	            return 'empr_tel1';
	        case 'tel2':
	            return 'empr_tel2';
	        case 'aff_date_adhesion':
	            return 'empr_date_adhesion';
	        case 'aff_date_expiration':
	            return 'empr_date_expiration';
	        case 'empr_statut_libelle':
	            return 'empr_statut.statut_libelle';
	        case 'categ_libelle':
	            return 'empr_categ.libelle';
	        case 'codestat_libelle':
	            return 'empr_codestat.libelle';
	        case 'location':
	            return 'docs_location.location_libelle';
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	protected function set_filters_from_filter_list() {
	    if(!empty(static::$filter_list)) {
	        $filtercolumns = explode(",",static::$filter_list->filtercolumns);
	        foreach ($filtercolumns as $filtercolumn) {
	            if(substr($filtercolumn,0,2) == "#e") {
	                $parametres_perso = $this->get_custom_parameters_instance('empr');
	                $custom_name = $parametres_perso->get_field_name_from_id(substr($filtercolumn,2));
	                $valeurs_post="f".$custom_name;
	                $v=array();
	                global ${$valeurs_post};
	                if (${$valeurs_post}) $v=${$valeurs_post};
	                $t=array();
	                if(!empty($parametres_perso->t_fields[substr($filtercolumn,2)]['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE'])) {
	                    $t[0]=$parametres_perso->t_fields[substr($filtercolumn,2)]['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE'];
	                }
	                $w=array_diff($v,$t);
	                $this->filters["#custom_field#".$custom_name] = array();
	                if (count($w)) {
	                    $this->filters["#custom_field#".$custom_name] = stripslashes_array($w);
	                }
	            } elseif(substr($filtercolumn,0,2) == "#p") {
	                $parametres_perso = $this->get_custom_parameters_instance('pret');
	                $custom_name = $parametres_perso->get_field_name_from_id(substr($filtercolumn,2));
	                $valeurs_post="f".$custom_name;
	                $v=array();
	                global ${$valeurs_post};
	                if (${$valeurs_post}) $v=${$valeurs_post};
	                $t=array();
	                if(!empty($parametres_perso->t_fields[substr($filtercolumn,2)]['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE'])) {
	                    $t[0]=$parametres_perso->t_fields[substr($filtercolumn,2)]['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE'];
	                }
	                $w=array_diff($v,$t);
	                $this->filters["#custom_field#".$custom_name] = array();
	                if (count($w)) {
	                    $this->filters["#custom_field#".$custom_name] = stripslashes_array($w);
	                }
	            } elseif (array_key_exists($filtercolumn, static::$filter_list->fixedfields)) {
	                $nom_valeurs_post="f".static::$filter_list->fixedfields[$filtercolumn]["ID"];
	                $valeurs_post=array();
	                global ${$nom_valeurs_post};
	                $valeurs_post=${$nom_valeurs_post};
	                $correspondence_filter = static::$correspondence_filters_fields['main_fields'][$filtercolumn];
	                $this->filters[$correspondence_filter] = array();
	                if (is_array($valeurs_post)) {
	                    $t=array();
	                    $t[0]=-1;
	                    $v=array_diff($valeurs_post,$t);
	                    if (count($v)) {
	                        $this->filters[$correspondence_filter] = stripslashes_array($v);
	                    }
	                }
	            } else {
	                $nom_valeurs_post="f".static::$filter_list->specialfields[$filtercolumn]["ID"];
	                $valeurs_post=array();
	                global ${$nom_valeurs_post};
	                $valeurs_post=${$nom_valeurs_post};
	                $correspondence_filter = static::$correspondence_filters_fields['main_fields'][$filtercolumn];
	                $this->filters[$correspondence_filter] = array();
	                if (is_array($valeurs_post)) {
	                    $t=array();
	                    $t[0]=-1;
	                    $v=array_diff($valeurs_post,$t);
	                    if (count($v)) {
	                        $this->filters[$correspondence_filter] = stripslashes_array($v);
	                    }
	                }
	            }
	        }
	    }
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		global $empr_location_id;
		global $empr_statut_edit;
		global $empr_categ_filter;
		global $empr_codestat_filter;
		global $sort_by, $sort_asc_desc;
		
		$initialization = $this->objects_type.'_initialization';
		global ${$initialization};
		if(isset(${$initialization}) && ${$initialization} == 'reset') {
			if(empty($this->selected_filters['location']) && empty($this->selected_filters['locations'])) {
				$empr_location_id = 0;
			}
		}
		if(isset($empr_location_id) && $empr_location_id !== '') {
			$empr_location_id = intval($empr_location_id);
			if($empr_location_id != -1) {
				if(!empty($this->selected_filters['locations'])) {
					if($empr_location_id) {
						$this->filters['locations'] = array($empr_location_id);
					} else {
						$this->filters['locations'] = array();
					}
					if(empty($this->selected_filters['location'])) {
						$this->filters['empr_location_id'] = '';
					}
				} else {
					$this->filters['empr_location_id'] = $empr_location_id;
				}
			}
		}
		if(isset($empr_statut_edit)) {
			$this->filters['empr_statut_edit'] = intval($empr_statut_edit);
		}
		if(isset($empr_categ_filter)) {
			$this->filters['empr_categ_filter'] = intval($empr_categ_filter);
		}
		if(isset($empr_codestat_filter)) {
			$this->filters['empr_codestat_filter'] = intval($empr_codestat_filter);
		}
		$this->set_filter_from_form('group', 'integer');
		$this->set_filter_from_form('locations');
		$this->set_filter_from_form('categories');
		$this->set_filter_from_form('status');
		$this->set_filter_from_form('codestat');
		$this->set_filter_from_form('groups');
		$this->set_filter_from_form('simple_search');
		$this->set_filter_from_form('id', 'integer');
		$this->set_filter_from_form('name');
		$this->set_filter_from_form('mail');
		$this->set_filter_from_form('has_mail');
		$this->set_filter_from_form('has_affected');
		$this->set_filter_from_form('date_creation_start');
		$this->set_filter_from_form('date_creation_end');
		$this->set_filter_from_form('date_adhesion_start');
		$this->set_filter_from_form('date_adhesion_end');
		$this->set_filter_from_form('date_expiration_start');
		$this->set_filter_from_form('date_expiration_end');
		$this->set_filter_from_form('cp');
		$this->set_filter_from_form('villes');
		$this->set_filter_from_form('birth_dates');
		$this->set_filter_from_form('types_abts');
		$this->set_filter_from_form('caddies');
		$this->set_filter_from_form('languages');
		
		//Filtres provenant de la classe filter_list
		if(!empty(static::$used_filter_list_mode) && empty($sort_by) && empty($sort_asc_desc)) {
		    $this->set_filters_from_filter_list();
		}
		
		parent::set_filters_from_form();
	}
	
	protected function get_selection_query($type) {
		$query = '';
		switch ($type) {
			case 'categories':
				$query = 'select id_categ_empr as id, libelle as label from empr_categ order by label';
				break;
			case 'status':
				$query = 'select idstatut as id, statut_libelle as label from empr_statut order by label';
				break;
			case 'groups':
				$query = 'select id_groupe as id, libelle_groupe as label from groupe order by label';
				break;
			case 'locations':
				$query = 'select idlocation as id, location_libelle as label from docs_location order by label';
				break;
			case 'codestat':
				$query = 'select idcode as id, libelle as label from empr_codestat order by label';
				break;
			case 'villes':
			    $query = 'select empr_ville as id, empr_ville as label from empr group by empr_ville order by empr_ville';
			    break;
			case 'cp':
			    $query = 'select empr_cp as id, empr_cp as label from empr group by empr_cp order by empr_cp';
			    break;
			case 'birth_dates':
			    $query = 'select empr_year as id, empr_year as label from empr group by empr_year order by empr_year';
			    break;
			case 'types_abts':
			    $query = 'select id_type_abt as id, type_abt_libelle as label from type_abts order by label';
			    break;
			case 'languages':
			    $query = 'select distinct empr_lang as id, empr_lang as label from empr order by empr_lang';
			    break;
		}
		return $query;
	}
	
	/**
	 * Retourne l'identifiant du noeud HTML du filtre du formulaire de recherche
	 */
	protected function get_uid_search_filter($property) {
	    switch ($property) {
	        case 'status':
	            return 'empr_statut_edit';
	        case 'categorie':
	            return 'empr_categ_filter';
	        case 'codestat_one':
	            return 'empr_codestat_filter';
	        case 'location':
	            return 'empr_location_id';
	        default:
	            return parent::get_uid_search_filter($property);
	    }
	}
	
	protected function get_search_filter_simple_search() {
		global $msg, $charset;
		
		return "<input type='text' class='saisie-30em' name='".$this->objects_type."_simple_search' value=\"".htmlentities($this->filters['simple_search'], ENT_QUOTES, $charset)."\" title='$msg[3000]'/>";
	}
	
	protected function get_search_filter_categorie() {
		return emprunteur::gen_combo_box_categ($this->filters['empr_categ_filter']);
	}
	
	protected function get_search_filter_categories() {
		global $msg;
		
		return $this->get_search_filter_multiple_selection($this->get_selection_query('categories'), 'categories', $msg['dsi_all_categories']);
	}
	
	protected function get_search_filter_groups() {
		global $msg;
	
		return $this->get_search_filter_multiple_selection($this->get_selection_query('groups'), 'groups', $msg['dsi_all_groups']);
	}
	
	protected function get_search_filter_location() {
		return docs_location::gen_combo_box_empr($this->filters['empr_location_id']);
	}
	
	protected function get_search_filter_locations() {
		global $msg;
	
		return $this->get_search_filter_multiple_selection($this->get_selection_query('locations'), 'locations', $msg['all_location']);
	}
	
	protected function get_search_filter_codestat_one() {
		return emprunteur::gen_combo_box_codestat($this->filters['empr_codestat_filter']);
	}
	
	protected function get_search_filter_codestat() {
		global $msg;
	
		return $this->get_search_filter_multiple_selection($this->get_selection_query('codestat'), 'codestat', $msg['all_codestat_empr']);
	}
	
	protected function get_search_filter_status() {
		global $msg;
		
		return gen_liste($this->get_selection_query('status'),"id","label","empr_statut_edit","",$this->filters['empr_statut_edit'],-1,"",0,$msg["all_statuts_empr"]);
	}
	
	protected function get_search_filter_id() {
		global $charset;
		
		return "<input type='text' class='saisie-30em' name='".$this->objects_type."_id' value=\"".htmlentities($this->filters['id'], ENT_QUOTES, $charset)."\" />";
	}
	
	protected function get_search_filter_name() {
		return $this->get_search_filter_simple_text('name');
	}
	
	protected function get_search_filter_has_mail() {
		global $msg, $charset;
	
		return "
			<input type='radio' id='".$this->objects_type."_has_mail_no' name='".$this->objects_type."_has_mail' value='0' ".(!$this->filters['has_mail'] ? "checked='checked'" : "")." />
			<label for='".$this->objects_type."_has_mail_no'>".htmlentities($msg['39'], ENT_QUOTES, $charset)."</label>
			<input type='radio' id='".$this->objects_type."_has_mail_yes' name='".$this->objects_type."_has_mail' value='1' ".($this->filters['has_mail'] ? "checked='checked'" : "")." />
			<label for='".$this->objects_type."_has_mail_yes'>".htmlentities($msg['40'], ENT_QUOTES, $charset)."</label>";
	}
	
	protected function get_search_filter_mail() {
		return $this->get_search_filter_simple_text('mail');
	}
	
	protected function get_search_filter_date_creation() {
		return $this->get_search_filter_interval_date('date_creation');
	}
	
	protected function get_search_filter_date_adhesion() {
		return $this->get_search_filter_interval_date('date_adhesion');
	}
	
	protected function get_search_filter_date_expiration() {
		return $this->get_search_filter_interval_date('date_expiration');
	}
	
	protected function get_search_filter_villes() {
	    global $msg;
	    
	    return $this->get_search_filter_multiple_selection($this->get_selection_query('villes'), 'villes', $msg['all_cities_empr']);
	}
	
	protected function get_search_filter_cp() {
	    global $msg;
	    
	    return $this->get_search_filter_multiple_selection($this->get_selection_query('cp'), 'cp', $msg['all_cp_empr']);
	}
	
	protected function get_search_filter_birth_dates() {
	    global $msg;
	    
	    return $this->get_search_filter_multiple_selection($this->get_selection_query('birth_dates'), 'birth_dates', $msg['all_years_empr']);
	}
	
	protected function get_search_filter_abts_types() {
	    global $msg;
	    
	    return $this->get_search_filter_multiple_selection($this->get_selection_query('abts_types'), 'abts_types', $msg['all_type_abt_empr']);
	}
	
	protected function get_search_filter_expl_locations() {
	    global $msg;
	    
	    return $this->get_search_filter_multiple_selection($this->get_selection_query('locations'), 'expl_locations', $msg['all_location']);
	}
	
	protected function get_search_filter_caddies() {
		global $msg, $PMBuserid;
		
		$query = "SELECT idemprcaddie, name, empr_caddie_classement FROM empr_caddie where 1 ";
		if ($PMBuserid!=1) $query.=" and (autorisations='$PMBuserid' or autorisations like '$PMBuserid %' or autorisations like '% $PMBuserid %' or autorisations like '% $PMBuserid') ";
		$query.=" ORDER BY empr_caddie_classement, name ";
		return gen_liste_multiple ($query, "idemprcaddie", "name", "", $this->objects_type.'_caddies[]', "", $this->filters['caddies'], 0, $msg['dsi_panier_aucun'], 0,'', 5, 'empr_caddie_classement');
	}
	
	protected function get_search_filter_languages() {
	    global $msg;
	    
	    $options = array();
	    
	    $empr_languages = static::get_empr_languages();
	    
	    $query = $this->get_selection_query('languages');
	    $result = pmb_mysql_query($query);
	    while ($row = pmb_mysql_fetch_object($result)) {
	        $options[$row->id] = $empr_languages[$row->id];
	    }
	    return $this->get_search_filter_multiple_selection('', 'languages', $msg['all'], $options);
	}
	
	/**
	 * Affichage des filtres du formulaire de recherche
	 */
	public function get_search_filters_form() {
	    $search_filters_form = '';
	    if(!empty(static::$used_filter_list_mode)) {
	    	$this->is_displayed_add_filters_block = false;
	    	if (!static::$filter_list->error) {
	    		if(empty(static::$display_filters)) {
	    			static::$display_filters = static::$filter_list->display_filters();
	    		}
	    		$search_filters_form .= static::$display_filters;
	    	}
	    } else {
	        $search_filters_form .= parent::get_search_filters_form();
	    }
	    return $search_filters_form;
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_display_search_form() {
	    if(get_called_class() != 'list_readers_circ_ui') {
	    	if(!isset($this->is_displayed_options_block)) {
	    		$this->is_displayed_options_block = true;
	    	}
	    }
	    if(get_called_class() == 'list_readers_bannette_ui' || get_called_class() == 'list_readers_bannettes_ui') {
	    	$this->is_displayed_datasets_block = true;
	    }
	    if(get_called_class() == 'list_readers_bannettes_ui') {
	    	$this->is_displayed_add_filters_block = true;
	    }
	    $display_search_form = parent::get_display_search_form();
	    return $display_search_form;
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		global $base_path;
		global $empr_show_caddie;
		global $sub;
		
		parent::init_default_selection_actions();
		if ($empr_show_caddie) {
			$link = array();
			$link['openPopUp'] = $base_path."/cart.php?object_type=EMPR&action=add_empr".($sub ? "_".$sub : "");
			$link['openPopUpTitle'] = 'cart';
			$this->add_selection_action('caddie', $msg['add_empr_cart'], 'basket_20x20.gif', $link);
		}
	}
	
	/**
	 * Jointure externes SQL pour les besoins des filtres
	 */
	protected function _get_query_join_filters() {
		
		$filter_join_query = '';
		if((is_array($this->filters['groups']) && count($this->filters['groups'])) || !empty($this->filters['group'])) {
			$filter_join_query .= " LEFT JOIN empr_groupe ON empr.id_empr=empr_groupe.empr_id";
		}
		if(is_array($this->filters['caddies']) && count($this->filters['caddies'])) {
			$filter_join_query .= " LEFT JOIN empr_caddie_content ON empr_caddie_content.object_id=empr.id_empr";
		}
		if($this->filters['has_affected']) {
			$filter_join_query .= $this->_get_query_join_filter_affected();
		}
		return $filter_join_query;
	}
	
	protected function _add_query_filters() {
		global $pmb_lecteurs_localises;
		
		if(!empty($this->filters['simple_search'])) {
			$elts = explode(' ', $this->filters['simple_search']);
			if(count($elts)>1) {
				$sql_elts = array();
				foreach ($elts as $elt) {
					$elt = str_replace("*", "%", trim($elt));
					if($elt) {
						$sql_elts[] = "(empr_nom like '".$elt."%' OR empr_nom like '% ".$elt."%' OR empr_nom like '%-".$elt."%' OR empr_prenom like '".$elt."%' OR empr_prenom like '% ".$elt."%' OR empr_prenom like '%-".$elt."%')";
					}
				}
				if(count($sql_elts)) {
					$this->query_filters [] = "((".implode(' AND ',$sql_elts).") OR empr_cb like '".str_replace("*", "%", $this->filters['simple_search'])."%')" ;
				}
			} else {
				$elt = str_replace("*", "%", $this->filters['simple_search']);
				$this->query_filters [] = "(empr_nom like '".$elt."%' OR empr_nom like '%-".$elt."%' OR empr_prenom like '".$elt."%' OR empr_prenom like '%-".$elt."%' OR empr_cb like '".$elt."%')" ;
			}
		}
		if(!empty($this->filters['empr_location_id']) && $this->filters['empr_location_id'] != -1) {
			$this->query_filters [] = 'empr_location = "'.$this->filters['empr_location_id'].'"';
		}
		$this->_add_query_filter_simple_restriction('empr_statut_edit', 'empr_statut', 'integer');
		$this->_add_query_filter_simple_restriction('empr_categ_filter', 'empr_categ', 'integer');
		$this->_add_query_filter_simple_restriction('empr_codestat_filter', 'empr_codestat', 'integer');
		$this->_add_query_filter_simple_restriction('group', 'groupe_id', 'integer');
		
		if($pmb_lecteurs_localises && array_key_exists('locations', $this->filters) && is_array($this->filters['locations']) && count($this->filters['locations'])) {
            if (!in_array(-1, $this->filters['locations'])) {
                $this->query_filters [] = 'empr_location IN ('.implode(',', $this->filters['locations']).')';
            }
		}
		$this->_add_query_filter_multiple_restriction('categories', 'empr_categ', 'integer');
		$this->_add_query_filter_multiple_restriction('status', 'empr_statut', 'integer');
		$this->_add_query_filter_multiple_restriction('codestat', 'empr_codestat', 'integer');
		$this->_add_query_filter_multiple_restriction('groups', 'groupe_id', 'integer');
		if($this->filters['id']) {
			$this->query_filters [] = 'id_empr = "'.$this->filters['id'].'"';
		}
		$this->_add_query_filter_combine_restrictions(array(
				$this->_get_query_filter_simple_restriction('name', 'empr_nom', 'boolean_search'),
				$this->_get_query_filter_simple_restriction('name', 'empr_prenom', 'boolean_search')
		));
		if($this->filters['mail']) {
			$this->query_filters [] = 'empr_mail like "%'.str_replace('*', '%', $this->filters['mail']).'%"';
		}
		if($this->filters['has_mail']) {
			$this->query_filters [] = 'empr_mail <> ""';
		}
		if($this->filters['has_affected']) {
			$query_affected = $this->_get_query_filter_affected();
			if($query_affected) {
				$this->query_filters [] = $this->_get_query_filter_affected();
			}
		}
		$this->_add_query_filter_interval_restriction('date_creation', 'empr_creation', 'date');
		$this->_add_query_filter_interval_restriction('date_adhesion', 'empr_date_adhesion', 'date');
		$this->_add_query_filter_interval_restriction('date_expiration', 'empr_date_expiration', 'date');
		
		if($this->filters['date_expiration_limit']) {
			$this->query_filters [] = $this->filters['date_expiration_limit'];
		}
		if($this->filters['change_categ']) {
			$this->query_filters [] = $this->filters['change_categ'];
		}
		
		$this->_add_query_filter_multiple_restriction('cp', 'empr_cp', 'integer');
		$this->_add_query_filter_multiple_restriction('villes', 'empr_ville');
		$this->_add_query_filter_multiple_restriction('birth_dates', 'empr_year');
		$this->_add_query_filter_multiple_restriction('last_level_validated', 'niveau_relance', 'integer');
		$this->_add_query_filter_multiple_restriction('last_dates', 'date_relance');
		$this->_add_query_filter_multiple_restriction('types_abts', 'type_abt', 'integer');
		
		if(is_array($this->filters['expl_locations']) && count($this->filters['expl_locations'])) {
			//Géré par la classe filter_list
		}
		$this->_add_query_filter_multiple_restriction('caddies', 'empr_caddie_id', 'integer');
		$this->_add_query_filter_multiple_restriction('languages', 'empr_lang');
		$this->_add_query_filter_multiple_restriction('empr_ids', 'id_empr', 'integer');
		$custom_fields_filters = $this->_get_query_custom_fields_filters();
		if(!empty($custom_fields_filters)) {
			foreach ($custom_fields_filters as $custom_field_filter) {
				$this->query_filters [] = $custom_field_filter;
			}
		}
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$this->query_filters = array();
		$this->_add_query_filters();
		if(count($this->query_filters)) {
			$filter_query .= $this->_get_query_join_filters();
			$filter_query .= $this->_get_query_join_custom_fields_filters('empr', 'id_empr');
			$filter_query .= ' where '.implode(' and ', $this->query_filters);
		}
		return $filter_query;
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
				case 'aff_date_adhesion':
					return strcmp($a->date_adhesion, $b->date_adhesion);
				case 'aff_date_expiration':
					return strcmp($a->date_expiration, $b->date_expiration);
				case 'location':
					return strcmp($a->empr_location_l, $b->empr_location_l);
				case 'nb_resas_and_validated':
					return $this->intcmp(emprunteur::get_nb_resas($a->id), emprunteur::get_nb_resas($b->id));
				case 'nb_loans_including_late':
				case 'nb_loans_including_resas':
					return $this->intcmp(emprunteur::get_nb_loans($a->id), emprunteur::get_nb_loans($b->id));
				default :
				    return parent::_compare_objects($a, $b, $index);
			}
		}
	}
	
	protected function iconepanier($id_emprunteur) {
	    global $msg, $empr_show_caddie;
	    
	    $img_ajout_empr_caddie="";
	    if ($empr_show_caddie) {
	        $img_ajout_empr_caddie = "\n<img src='".get_url_icon('basket_empr.gif')."' class='align_middle' alt='basket' title=\"".$msg[400]."\" ";
	        $img_ajout_empr_caddie .= "onclick=\"if (event) e=event; else e=window.event; if (e.target) elt=e.target; else elt=e.srcElement; e.cancelBubble = true; if (e.stopPropagation) e.stopPropagation();\" onmouseup=\"if (event) e=event; else e=window.event; if (e.target) elt=e.target; else elt=e.srcElement; if (elt.nodeName=='IMG') openPopUp('./cart.php?object_type=EMPR&item=".$id_emprunteur."', 'cart'); return false;\" ";
	        $img_ajout_empr_caddie .= "onMouseOver=\"show_div_access_carts(event,".$id_emprunteur.",'EMPR');\" onMouseOut=\"set_flag_info_div(false);\" ";
	        $img_ajout_empr_caddie .= "style=\"cursor: pointer\">\n";
	    }
	    return $img_ajout_empr_caddie;
	}
	
	protected function _get_object_property_categ_libelle($object) {
		return 	$object->cat_l;
	}
	
	protected function _get_object_property_codestat_libelle($object) {
		return 	$object->cstat_l;
	}
	
	protected function _get_object_property_nb_loans($object) {
		return emprunteur::get_nb_loans($object->id);
	}
	
	protected function _get_object_property_nb_loans_late($object) {
		return emprunteur::get_nb_loans_late($object->id);
	}
	
	protected function _get_object_property_nb_resas_and_validated($object) {
		return emprunteur::get_nb_resas_and_validated($object->id);
	}
	
	protected function _get_object_property_nb_loans_including_late($object) {
		return emprunteur::get_nb_loans_including_late($object->id);
	}
	
	protected function _get_object_property_nb_loans_including_resas($object) {
	    return emprunteur::get_nb_loans_including_resas($object->id);
	}
	
	protected function _get_object_property_type_abt($object) {
		$query = "select type_abt_libelle from type_abts where id_type_abt='".$object->type_abt."'";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			return pmb_mysql_result($result,0,0);
		}
		return '';
	}
	
	protected function _get_object_property_empr_name($object) {
		return $object->nom." ".$object->prenom;
	}
	
	protected function _get_object_property_groups($object) {
		return strip_tags(implode(',', emprunteur::get_groupes($object->id)));
	}
	
	protected function _get_object_property_location($object) {
		$docs_location = new docs_location($object->empr_location);
		return $docs_location->libelle;
	}
	
	protected function _get_object_property_empr_lang($object) {
	    return static::get_empr_languages()[$object->empr_lang];
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		
		$content = '';
		switch($property) {
			case 'groups':
				$content .= implode(',', emprunteur::get_groupes($object->id));
				break;
			case 'categ_change':
				$today = getdate();
				$age_lecteur = $today["year"] - $object->birth;
				// on construit le select catégorie
				$query = "SELECT id_categ_empr, libelle FROM empr_categ WHERE (".$age_lecteur." >= age_min or age_min=0)  and (".$age_lecteur." <= age_max or age_max=0) ORDER BY age_min ";
				$result = pmb_mysql_query($query);
				$nbr_rows = pmb_mysql_num_rows($result);
				$content .= "<select id='".$this->objects_type."_categ_change_".$object->id."' name='".$this->objects_type."_categ_change[".$object->id."]' class='saisie-20em ".$this->objects_type."_categ_change' data-empr-id='".$object->id."'>";
				$content .="<option value='0' selected='selected' >".$msg["change_categ_do_nothing"]."</option>";
				for($i=0; $i < $nbr_rows; $i++) {
					$row = pmb_mysql_fetch_row($result);
					$content .= "<option value='$row[0]'";
					if($i == 0) $content .= " selected='selected'";
					$content .= ">$row[1]</option>";
				}
				$content .= "</select>";
				break;
			case 'relance':
				$action_relance_courrier = "onclick=\"openPopUp('./pdf.php?pdfdoc=lettre_relance_adhesion&id_empr=".$object->id."', 'lettre'); return(false) \"";
				$content .= "<a href=\"#\" ".$action_relance_courrier."><img src=\"".get_url_icon('new.gif')."\" title='".htmlentities($msg["param_pdflettreadhesion"], ENT_QUOTES, $charset)."' alt='".htmlentities($msg["param_pdflettreadhesion"], ENT_QUOTES, $charset)."' style='border:0px' /></a>";
				if ($object->mail) {
					$mail_click = "onclick=\"if (confirm('".$msg["mail_retard_confirm"]."')) {openPopUp('./mail.php?type_mail=mail_relance_adhesion&id_empr=".$object->id."', 'mail');} return(false) \"";
					$content .= "&nbsp;<a href=\"#\" ".$mail_click."><img src=\"".get_url_icon('mail.png')."\" title='".htmlentities($msg["param_mailrelanceadhesion"], ENT_QUOTES, $charset)."' alt='".htmlentities($msg["param_mailrelanceadhesion"], ENT_QUOTES, $charset)."' style='border:0px' /></a>";
				}
				break;
			case 'add_empr_cart':
			    $content .= $this->iconepanier($object->id);
			    break;
			case 'empr_msg':
				$content .= nl2br($object->empr_msg);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function _get_query_property_filter($property) {
		switch ($property) {
			case 'empr_statut_edit':
				return "select statut_libelle from empr_statut where idstatut = ".$this->filters[$property];
			case 'empr_categ_filter':
				return "select libelle from empr_categ where id_categ_empr = ".$this->filters[$property];
			case 'empr_codestat_filter':
				return "select libelle from empr_codestat where idcode = ".$this->filters[$property];
			case 'group':
				return "select libelle_groupe from groupe where id_groupe = ".$this->filters[$property];
			case 'categories':
				return "select libelle from empr_categ where id_categ_empr IN (".implode(',', $this->filters[$property]).")";
			case 'status':
				return "select statut_libelle from empr_statut where idstatut IN (".implode(',', $this->filters[$property]).")";
			case 'codestat':
				return "select libelle from empr_codestat where idcode IN (".implode(',', $this->filters[$property]).")";
			case 'groups':
				return "select libelle_groupe from groupe where id_groupe IN (".implode(',', $this->filters[$property]).")";
			case 'types_abts':
				return "select type_abt_libelle from type_abts where id_type_abt IN (".implode(',', $this->filters[$property]).")";
			case 'expl_locations':
				return "select location_libelle from docs_location where idlocation IN (".implode(',', $this->filters[$property]).")";
			case 'caddies':
				return "select name from empr_caddie where idemprcaddie IN (".implode(',', $this->filters[$property]).")";
			case 'locations':
				return "select location_libelle from docs_location where idlocation IN (".implode(',', $this->filters[$property]).")";
		}
		return '';
	}
	
	protected function _get_query_human_location() {
		if(!empty($this->filters['empr_location_id']) && $this->filters['empr_location_id'] != -1) {
			$docs_location = new docs_location($this->filters['empr_location_id']);
			return $docs_location->libelle;
		}
		return '';
	}
	
	protected function _get_query_human_has_mail() {
		global $msg;
		if($this->filters['has_mail']) {
			return $msg['40'];
		}
	}
	
	protected function _get_query_human_has_affected() {
		global $msg;
		if($this->filters['has_affected']) {
			return $msg['40'];
		}
	}
	
	protected function _get_query_human_date_adhesion() {
		return $this->_get_query_human_interval_date('date_adhesion');
	}
	
	protected function _get_query_human_date_expiration() {
		return $this->_get_query_human_interval_date('date_expiration');
	}
	
	protected function _get_query_human_date_creation() {
		return $this->_get_query_human_interval_date('date_creation');
	}
	
	protected function _get_query_human_languages() {
	    $labels = [];
	    $empr_languages = static::get_empr_languages();
	    if(!empty($this->filters['languages'])) {
	        foreach ($this->filters['languages'] as $language) {
	            $labels[] = $empr_languages[$language];
	        }
	    }
	    return $labels;
	}
	
	protected function _get_query_human_last_dates() {
		$formatted_last_dates = array();
		if(!empty($this->filters['last_dates'])) {
			foreach ($this->filters['last_dates'] as $last_date) {
				$formatted_last_dates[] = format_date($last_date);
			}
		}
		return implode(',', $formatted_last_dates);
	}
	
	protected function _get_query_human() {
		global $msg;
		
		$humans = $this->_get_query_human_main_fields();
		if(empty($this->selected_filters['location']) && !empty($this->filters['empr_location_id']) && $this->filters['empr_location_id'] != -1) {
			$humans['location'] = $this->_get_query_human_main_field('location', $msg['location']);
		}
		if($this->filters['empr_statut_edit']) {
			$query = "select statut_libelle from empr_statut where idstatut = ".$this->filters['empr_statut_edit'];
			$humans['status'] = $this->_get_label_query_human_from_query($msg['statut_empr'], $query);
		}
		if($this->filters['empr_categ_filter']) {
			$query = "select libelle from empr_categ where id_categ_empr = ".$this->filters['empr_categ_filter'];
			$humans['categorie'] = $this->_get_label_query_human_from_query($msg['editions_filter_empr_categ'], $query);
		}
		if($this->filters['empr_codestat_filter']) {
			$query = "select libelle from empr_codestat where idcode = ".$this->filters['empr_codestat_filter'];
			$humans['codestat_one'] = $this->_get_label_query_human_from_query($msg['editions_filter_empr_codestat'], $query);
		}
		if(is_array($this->filters['last_level_validated']) && count($this->filters['last_level_validated'])) {
			$humans['last_level_validated'] = $this->_get_label_query_human($msg['relance_dernier_niveau'], implode(',', $this->filters['last_level_validated']));
		}
		if(is_array($this->filters['supposed_level']) && count($this->filters['supposed_level'])) {
			$humans['supposed_level'] = $this->_get_label_query_human($msg['relance_niveau_suppose'], implode(',', $this->filters['supposed_level']));
		}
		if(is_array($this->filters['types_abts']) && count($this->filters['types_abts'])) {
		    $query = "select type_abt_libelle from type_abts where id_type_abt IN (".implode(',', $this->filters['types_abts']).")";
		    $humans['types_abts'] = $this->_get_label_query_human_from_query($msg['type_abt_empr'], $query);
		}
		if(is_array($this->filters['expl_locations']) && count($this->filters['expl_locations'])) {
			$query = "select location_libelle from docs_location where idlocation IN (".implode(',', $this->filters['expl_locations']).")";
			$humans['expl_locations'] = $this->_get_label_query_human_from_query($msg['empr_filter_expl_loc'], $query);
		}
		if(is_array($this->filters['empr_ids']) && count($this->filters['empr_ids'])) {
		    global $human_requete; //générée dans empr_list.inc.php
		    $humans['empr_ids'] = $human_requete;
		}
		$custom_fields_humans = $this->_get_query_human_custom_fields();
		if(!empty($custom_fields_humans)) {
			foreach ($custom_fields_humans as $name=>$custom_field_human) {
				$humans[$name] = $custom_field_human;
			}
		}
		return $this->get_display_query_human($humans);
	}
	
	public static function get_ajax_controller_url_base() {
		global $base_path, $current_module, $sub;
		
		switch (get_called_class()) {
			case 'list_readers_relances_ui':
			case 'list_readers_recouvr_ui':
				$categ = 'relance';
				break;
			case 'list_readers_circ_ui':
				$categ = 'pret';
				break;
			case 'list_readers_group_ui':
				$categ = 'groups';
				break;
			default:
				$categ = 'empr';
				break;
		}
		return $base_path.'/ajax.php?module='.$current_module.'&categ='.$categ.'&sub='.$sub;
	}
	
	public static function set_used_filter_list_mode($used_filter_list_mode=false) {
		$objects_type = str_replace('list_', '', static::class);
		$dataset_id = list_model::get_num_dataset_common_list($objects_type);
		if($dataset_id) {
			static::$used_filter_list_mode = false;
		} else {
			static::$used_filter_list_mode = $used_filter_list_mode;
		}
	}
	
	public static function set_filter_list($filter_list, $filters_ui=array()) {
		if(!empty(static::$used_filter_list_mode)) {
		    static::$filter_list = $filter_list;
		    static::$filter_list->activate_filters();
		    if (!static::$filter_list->error) {
		        static::set_filter_list_from_filters_ui($filters_ui);
		    }
		}
	}
	
	protected static function init_correspondence_filters_fields() {
	    if(empty(static::$correspondence_filters_fields)) {
    	    static::$correspondence_filters_fields = array();
    	    static::$correspondence_filters_fields['main_fields'] = array(
    	        'n' => 'name',
    	        'v' => 'villes',
    	        'l' => 'locations',
    	        'c' => 'categories',
    	        's' => 'status',
    	        'g' => 'groups',
                'y' => 'birth_dates',
    	        'cp' => 'cp',
    	        'cs' => 'codestat',
                'ab' => 'types_abts',
    	        '13' => 'expl_locations',
    	        'lg' => 'languages',
    	    );
	    }
	}
	
	protected static function init_correspondence_columns_fields() {
	    if(empty(static::$correspondence_columns_fields)) {
	        static::$correspondence_columns_fields = array();
	        static::$correspondence_columns_fields['main_fields'] = array(
	            'n' => 'empr_name',
	            'b' => 'cb',
	            'a' => 'adr1',
	            'v' => 'ville',
	            'l' => 'location',
	            'c' => 'categ_libelle',
	            's' => 'empr_statut_libelle',
	            'g' => 'groups',
	            'y' => 'birth',
	            'cp' => 'cp',
	            'cs' => 'codestat_libelle',
	            'ab' => 'type_abt',
	            'm' => 'empr_msg',
	            '1' => 'add_empr_cart',
	            '4' => 'nb_loans',
        		'19' => 'nb_loans_late',
        		'i' => 'id',
        		'em' => 'mail',
        		't' => 'tel1',
        		'18' => 'nb_resas_and_validated',
        		'20' => 'nb_loans_including_late',
	            'lg' => 'empr_lang',
	        );
	    }
	}
	
	public function run_action_add_caddie() {
	    global $msg;
	    global $caddie;
	    
	    $message = '';
	    $nb_items_before = array();
	    $selected_objects = static::get_selected_objects();
	    if(is_array($selected_objects) && count($selected_objects)) {
	        foreach($caddie as $id_caddie => $coche) {
	            if($coche){
	                $myCart = new empr_caddie($id_caddie);
	                $myCart->compte_items();
	                $nb_items_before[$id_caddie]=$myCart->nb_item;
	                foreach ($selected_objects as $id) {
	                    $myCart->add_item($id);
	                }
	                $myCart->compte_items();
	                $message.=sprintf($msg["print_cart_n_added"]."\\n",($myCart->nb_item-$nb_items_before[$id_caddie]),$myCart->name);
	            }
	        }
	    }
	    return $message;
	}
	
	public function run_global_action_add_caddie() {
		$message = '';
		$query = $this->_get_query_base();
		$query .= $this->_get_query_filters();
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$objects_type = str_replace('list_', '', static::class);
			$selected_objects = $objects_type."_selected_objects";
			global ${$selected_objects};
			${$selected_objects} = array();
			while($row = pmb_mysql_fetch_object($result)) {
				${$selected_objects}[] = $row->id_empr;
			}
			if(count(${$selected_objects})) {
				$message.=$this->run_action_add_caddie();
			}
		}
		return $message;
	}
	
	protected static function set_filter_list_from_filter_ui($property, $values, $initialization=false) {
        $filtercolumns = explode(",",static::$filter_list->filtercolumns);
        foreach ($filtercolumns as $filtercolumn) {
            if($filtercolumn == $property) {
                if(substr($filtercolumn,0,2) == "#e") {
                    $parametres_perso = new parametres_perso('empr');
                    $custom_name = $parametres_perso->get_field_name_from_id(substr($filtercolumn,2));
                    $filter_name="f".$custom_name;
                } elseif(substr($filtercolumn,0,2) == "#p") {
                    $parametres_perso = new parametres_perso('pret');
                    $custom_name = $parametres_perso->get_field_name_from_id(substr($filtercolumn,2));
                    $filter_name="f".$custom_name;
                } elseif (array_key_exists($filtercolumn, static::$filter_list->fixedfields)) {
                    $filter_name="f".static::$filter_list->fixedfields[$filtercolumn]["ID"];
                } else {
                    $filter_name="f".static::$filter_list->specialfields[$filtercolumn]["ID"];
                    $name_function=static::$filter_list->specialfields[$filtercolumn]["FUNCTION"];
//                     $r="";
//                     $key=$result[$this->params["REFERENCEKEY"][0]['value']];
//                     eval("\$r=".$name_function."(\$key);");
                }
                global ${$filter_name};
                if(empty(${$filter_name}) || $initialization) {
                	if(!empty($values)) {
                		${$filter_name}=$values;
                	} else {
	                	unset(${$filter_name});
	                	unset($GLOBALS[$filter_name]);
                	}
                }
            }
        }
    }
    
    public static function set_filter_list_from_filters_ui($filters, $initialization=false) {
	    static::init_correspondence_filters_fields();
	    if(!empty($filters) && !empty(static::$filter_list)) {
	        foreach ($filters as $property=>$values) {
	            $correspondence_property = array_search($property, static::$correspondence_filters_fields['main_fields']);
	            static::set_filter_list_from_filter_ui($correspondence_property, $values, $initialization);
	        }
	    }
	}
	
	public static function get_empr_languages() {
	    global $include_path;
	    
	    if(!isset(static::$empr_languages)) {
	        $langues = new XMLlist("$include_path/messages/languages.xml");
	        $langues->analyser();
	        static::$empr_languages = $langues->table;
	        reset(static::$empr_languages);
	    }
	    return static::$empr_languages;
	}
}