<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_records_ui.class.php,v 1.16.4.1 2025/03/04 16:43:11 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/analyse_query.class.php");

class list_records_ui extends list_ui {

	protected $aq_members;

	protected function get_keep_fields($tablename) {
		$keep_fields = array();
		switch ($tablename) {
			case 'collections':
				$keep_fields[] = 'collection_id';
				$keep_fields[] = 'collection_name';
				$keep_fields[] = 'collection_parent';
				$keep_fields[] = 'collection_issn';
				$keep_fields[] = 'collection_web';
				$keep_fields[] = 'collection_comment';
				break;
			case 'indexint':
				$keep_fields[] = 'indexint_id';
				$keep_fields[] = 'indexint_name';
				$keep_fields[] = 'indexint_comment';
				break;
			case 'publishers':
				$keep_fields[] = 'ed_id';
				$keep_fields[] = 'ed_name';
				$keep_fields[] = 'ed_adr1';
				$keep_fields[] = 'ed_adr2';
				$keep_fields[] = 'ed_cp';
				$keep_fields[] = 'ed_ville';
				$keep_fields[] = 'ed_pays';
				$keep_fields[] = 'ed_web';
				$keep_fields[] = 'ed_comment';
				break;
			case 'series':
				$keep_fields[] = 'serie_id';
				$keep_fields[] = 'serie_name';
				break;
			case 'sub_collections':
				$keep_fields[] = 'sub_coll_id';
				$keep_fields[] = 'sub_coll_name';
				$keep_fields[] = 'sub_coll_parent';
				$keep_fields[] = 'sub_coll_issn';
				$keep_fields[] = 'subcollection_web';
				$keep_fields[] = 'subcollection_comment';
				break;
		}
		return $keep_fields;
	}

	protected function _get_query_select_fields($tablename, $alias='') {
		$query_select_fields = '';
		$fields = $this->get_keep_fields($tablename);
		if($alias) {
			$builded_fields = array();
			foreach ($fields as $field) {
				$builded_fields[] = $field." as ".$alias."_".$field;
			}
			$query_select_fields .= $alias.".".implode(', '.$alias.'.', $builded_fields);
		} else {
			$query_select_fields .= $tablename.".".implode(', '.$tablename.'.', $fields);
		}
		return $query_select_fields;
	}

	protected function _get_query_base() {
		$aq_members = $this->get_aq_members();
		$query = "SELECT notices.notice_id as id, notices.*, ".$aq_members["select"]." as pert,
                    ".$this->_get_query_select_fields('collections').",
                    ".$this->_get_query_select_fields('publishers', 'p1').",
                    ".$this->_get_query_select_fields('publishers', 'p2').",
                    ".$this->_get_query_select_fields('series').",
                    ".$this->_get_query_select_fields('sub_collections').",
                    ".$this->_get_query_select_fields('indexint')."
					FROM notices
					left join series on serie_id=notices.tparent_id
					left join publishers p1 on p1.ed_id=notices.ed1_id
					left join publishers p2 on p2.ed_id=notices.ed2_id
					left join collections on notices.coll_id=collection_id
					left join sub_collections on notices.subcoll_id=sub_coll_id
					left join indexint on notices.indexint=indexint_id";
		return $query;
	}

	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('pert', 'desc');
	}

	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'global_search' => 'global_search',
						'tit1' => '233',
						'authors' => '234',
						'publishers' => 'searcher_publisher',
						'date_parution' => 'date_publication_or_annee_edition',
						'collections' => 'searcher_coll',
						'nocoll' => '253',
						'sub_collections' => 'searcher_subcoll',
						'series' => 'serie_query',
						'code' => 'isbn_query',
						'n_gen' => '265',
						'n_contenu' => '266',
						'n_resume' => 'note_resume_query',
						'indexint' => 'indexint_menu_title',
						'index_l' => '324',
						'langues' => 'langue_publication_query',
						'languesorg' => 'langue_originale_query',
						'niveau_biblio' => 'doc_perio_art_query',
						'typdoc' => 'type_doc_sort',
						'origine_catalogage' => 'origine_notice_query',
						'statuts' => 'statut_notice_sort',
						'create_date' => 'date_creation_query',
						'update_date' => 'date_update_query',
						'lien' => '274',
						'eformat' => 'recherche_format_electronique',
						'is_new' => 'notice_is_new_search',
						'is_numeric' => 'notice_is_numeric_search',
				)
		);
		$this->available_filters['custom_fields'] = array();
		$this->add_custom_fields_available_filters('notices', 'id_notice');
	}

	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'notice_id' => 'notice_id',
						'typdoc' => 'z3950_type_doc',
						'tit1' => '237',
						'tit2' => '238',
						'tit3' => '239',
						'tit4' => '240',
						'serie_name' => '241',
						'tnvol' => '242',
						'publisher_name' => 'serial_Editeur_group',
						'collection_name' => '250',
						'sub_coll_name' => '251',
						'year' => '252',
						'nocoll' => '253',
						'mention_edition' => 'mention_edition',
						'code' => '255',
						'npages' => '259',
						'ill' => '260',
						'size' => '261',
						'accomp' => '262',
						'n_gen' => '265',
						'n_contenu' => '266',
						'n_resume' => '267',
						'lien' => '275',
						'eformat' => '276',
						'index_l' => '324',
						'indexint_name' => 'indexint_menu_title',
						'niveau_biblio' => 'doc_perio_art_query',
// 						'niveau_hierar' =>
						'prix' => 'notice_prix',
						'statut_name' => 'statut_notice_sort',
						'create_date' => 'date_creation_query',
						'update_date' => 'date_update_query',
						'date_parution' => '4026',
						'indexation_lang' => 'indexation_lang_select',
						'is_new' => 'notice_is_new_gestion',
						'author_main' => '244',
// 						'authors_others' => '246',
						'authors_secondary' => '247',
						'categories' => '134',
						'langues' => '710',
						'languesorg' => '711',
						'opac_permalink' => 'opac_permalink'
				)
		);
		$this->available_columns['custom_fields'] = array();
		$this->add_custom_fields_available_columns('notices', 'notice_id');
	}

	protected function init_default_columns() {
		$this->add_column('notice_id');
		$this->add_column('niveau_biblio');
		$this->add_column('typdoc');
		$this->add_column('tit1');
		$this->add_column('tit4');
		$this->add_column('serie_name');
		$this->add_column('tnvol');
		$this->add_column('author_main');
		$this->add_column('authors_secondary');
		$this->add_column('publisher_name');
		$this->add_column('collection_name');
		$this->add_column('year');
		$this->add_column('date_parution');
		$this->add_column('code');
		$this->add_column('n_gen');
		$this->add_column('n_contenu');
		$this->add_column('n_resume');
		$this->add_column('indexint_name');
		$this->add_column('categories');
		$this->add_column('langues');
	}

	protected function init_default_settings() {
// 		global $gestion_acces_active, $gestion_acces_user_notice;

		parent::init_default_settings();
		$this->set_setting_display('search_form', 'options', true);

// 		$this->set_setting_display('grouped_objects', 'display_counter', true);
		$this->set_setting_filter('authors', 'selection_type', 'completion');
		$this->set_setting_filter('publishers', 'selection_type', 'completion');
		$this->set_setting_filter('collections', 'selection_type', 'completion');
		$this->set_setting_filter('sub_collections', 'selection_type', 'completion');
		$this->set_setting_filter('series', 'selection_type', 'completion');
		$this->set_setting_filter('indexint', 'selection_type', 'completion');
		$this->set_setting_column('create_date', 'datatype', 'date');
		$this->set_setting_column('update_date', 'datatype', 'date');
		$this->set_setting_column('date_parution', 'datatype', 'date');
		$this->set_setting_column('notice_is_new', 'datatype', 'boolean');
	}

	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'user_query' => '*',
				'niveau_biblio' => '',
				'niveau_hierar' => '',
				'global_search' => '',
				'tit1' => '',
				'authors' => array(),
				'publishers' => array(),
				'date_parution_start' => '',
				'date_parution_end' => '',
				'collections' => array(),
				'nocoll' => '',
				'sub_collections' => array(),
				'series' => array(),
				'code' => '',
				'n_gen' => '',
				'n_contenu' => '',
				'n_resume' => '',
				'indexint' => array(),
				'index_l' => '',
				'langues' => array(),
				'languesorg' => array(),
				'typdoc' => '',
				'origine_catalogage' => array(),
				'statuts' => array(),
				'create_date_start' => '',
				'create_date_end' => '',
				'update_date_start' => '',
				'update_date_end' => '',
				'lien' => '',
				'eformat' => '',
				'is_new' => '',
				'is_numeric' => '',
		);
		parent::init_filters($filters);
	}

	protected function init_default_selected_filters() {
		$this->add_selected_filter('global_search');
		$this->add_selected_filter('tit1');
		$this->add_selected_filter('authors');
		$this->add_selected_filter('publishers');
		$this->add_selected_filter('date_parution');
		$this->add_selected_filter('collections');
		$this->add_selected_filter('nocoll');
		$this->add_selected_filter('sub_collections');
		$this->add_selected_filter('series');
		$this->add_selected_filter('code');
		$this->add_selected_filter('n_gen');
		$this->add_selected_filter('n_contenu');
		$this->add_selected_filter('n_resume');
		$this->add_selected_filter('indexint');
		$this->add_selected_filter('index_l');
		$this->add_selected_filter('langues');
		$this->add_selected_filter('languesorg');
		$this->add_selected_filter('niveau_biblio');
		$this->add_selected_filter('typdoc');
		$this->add_selected_filter('origine_catalogage');
		$this->add_selected_filter('statuts');
		$this->add_selected_filter('create_date');
		$this->add_selected_filter('update_date');
		$this->add_selected_filter('lien');
		$this->add_selected_filter('eformat');
		$this->add_selected_filter('is_new');
		$this->add_selected_filter('is_numeric');
	}

	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$user_query = $this->objects_type.'_user_query';
		global ${$user_query};
		if(isset(${$user_query}) && ${$user_query} != '') {
			$this->filters['user_query'] = ${$user_query};
		}

		$this->set_filter_from_form('global_search');
		$this->set_filter_from_form('tit1');
		$this->set_filter_from_form('authors', 'integer');
		$this->set_filter_from_form('publishers', 'integer');
		$this->set_filter_from_form('date_parution_start');
		$this->set_filter_from_form('date_parution_end');
		$this->set_filter_from_form('collections', 'integer');
		$this->set_filter_from_form('nocoll');
		$this->set_filter_from_form('sub_collections', 'integer');
		$this->set_filter_from_form('series', 'integer');
		$this->set_filter_from_form('code');
		$this->set_filter_from_form('n_gen');
		$this->set_filter_from_form('n_contenu');
		$this->set_filter_from_form('n_resume');
		$this->set_filter_from_form('indexint', 'integer');
		$this->set_filter_from_form('index_l');
		$this->set_filter_from_form('langues');
		$this->set_filter_from_form('languesorg');
		$this->set_filter_from_form('niveau_biblio');
		$this->set_filter_from_form('typdoc');
		$this->set_filter_from_form('origine_catalogage', 'integer');
		$this->set_filter_from_form('statuts', 'integer');
		$this->set_filter_from_form('create_date_start');
		$this->set_filter_from_form('create_date_end');
		$this->set_filter_from_form('update_date_start');
		$this->set_filter_from_form('update_date_end');
		$this->set_filter_from_form('lien');
		$this->set_filter_from_form('eformat');
		$this->set_filter_from_form('is_new', 'integer');
		$this->set_filter_from_form('is_numeric', 'integer');

		parent::set_filters_from_form();
	}

	protected function get_aq_members() {
		global $msg;

		$user_query = $this->filters['user_query'];
		if(!isset($this->aq_members[$user_query])) {
			$aq=new analyse_query($this->filters['user_query']);
			if ($aq->error) {
				error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message));
				exit();
			}
			$this->aq_members[$user_query]=$aq->get_query_members("notices","index_wew","index_sew","notice_id");
		}
		return $this->aq_members[$user_query];
	}

	protected function _add_query_filters() {
		if($this->filters['user_query']) {
			$aq_members = $this->get_aq_members();
			$this->query_filters [] = $aq_members["where"];
		}
		$this->_add_query_filter_simple_restriction('niveau_biblio', 'niveau_biblio');
		$this->_add_query_filter_simple_restriction('niveau_hierar', 'niveau_hierar');

// 		$this->_add_query_filter_simple_restriction('global_search', 'global_search');
		$this->_add_query_filter_simple_restriction('tit1', 'tit1');
// 		$this->_add_query_filter_multiple_restriction('authors', 'authors', 'integer');
		$this->_add_query_filter_combine_restrictions(array(
				$this->_get_query_filter_simple_restriction('publishers', 'ed1_id', 'integer'),
				$this->_get_query_filter_simple_restriction('publishers', 'ed2_id', 'integer')
		));
		$this->_add_query_filter_interval_restriction('date_parution', 'date_parution', 'date');
		$this->_add_query_filter_multiple_restriction('collections', 'coll_id', 'integer');
		$this->_add_query_filter_simple_restriction('nocoll', 'nocoll');
		$this->_add_query_filter_multiple_restriction('sub_collections', 'subcoll_id', 'integer');
		$this->_add_query_filter_multiple_restriction('series', 'tparent_id', 'integer');
		$this->_add_query_filter_simple_restriction('code', 'code');
		$this->_add_query_filter_simple_restriction('n_gen', 'n_gen');
		$this->_add_query_filter_simple_restriction('n_contenu', 'n_contenu');
		$this->_add_query_filter_simple_restriction('n_resume', 'n_resume');
		$this->_add_query_filter_multiple_restriction('indexint', 'indexint', 'integer');
		$this->_add_query_filter_simple_restriction('index_l', 'index_l');
// 		$this->_add_query_filter_multiple_restriction('langues', 'langues');
// 		$this->_add_query_filter_multiple_restriction('languesorg', 'languesorg');
		$this->_add_query_filter_simple_restriction('typdoc', 'typdoc');
		$this->_add_query_filter_multiple_restriction('origine_catalogage', 'origine_catalogage', 'integer');
		$this->_add_query_filter_multiple_restriction('statuts', 'statut', 'integer');
		$this->_add_query_filter_interval_restriction('create_date', 'create_date', 'datetime');
		$this->_add_query_filter_interval_restriction('update_date', 'update_date', 'datetime');
		$this->_add_query_filter_simple_restriction('lien', 'lien');
		$this->_add_query_filter_simple_restriction('eformat', 'eformat');
		$this->_add_query_filter_simple_restriction('is_new', 'notice_is_new', 'integer');
		$this->_add_query_filter_simple_restriction('is_numeric', 'is_numeric', 'integer');
	}

	protected function get_selection_query_fields($type) {
		switch ($type) {
			case 'authors':
				return array('id' => 'author_id', 'label' => 'author_name');
			case 'publishers':
				return array('id' => 'ed_id', 'label' => 'ed_name');
			case 'collections':
				return array('id' => 'collection_id', 'label' => 'collection_name');
			case 'sub_collections':
				return array('id' => 'sub_coll_id', 'label' => 'sub_coll_name');
			case 'series':
				return array('id' => 'serie_id', 'label' => 'serie_name');
			case 'indexint':
				return array('id' => 'indexint_id', 'label' => 'indexint_name');
			case 'notice_statut':
				return array('id' => 'id_notice_statut', 'label' => 'gestion_libelle');
			case 'origine_notice':
				return array('id' => 'orinot_id', 'label' => 'orinot_nom');
			case 'docs_section':
				return array('id' => 'idsection', 'label' => 'section_libelle');
			case 'docs_statut':
				return array('id' => 'idstatut', 'label' => 'statut_libelle');
			case 'docs_type':
				return array('id' => 'idtyp_doc', 'label' => 'tdoc_libelle');
			case 'docs_location':
				return array('id' => 'idlocation', 'label' => 'location_libelle');
			case 'explnum_statut':
				return array('id' => 'id_explnum_statut', 'label' => 'gestion_libelle');
			case 'upload_repertoire':
				return array('id' => 'repertoire_id', 'label' => 'repertoire_nom');
			case 'pclassement':
				return array('id' => 'id_pclass', 'label' => 'name_pclass');
			case 'lenders':
				return array('id' => 'idlender', 'label' => 'lender_libelle');
		}
	}

	protected function get_selection_query($type) {
		$query = '';
		switch ($type) {
			case 'langues':
				$query = 'SELECT distinct code_langue as id, code_langue as label from notices_langues where type_langue=0';
				break;
			case 'languesorg':
				$query = 'SELECT distinct code_langue as id, code_langue as label from notices_langues where type_langue=1';
				break;
			case 'rights_users_records':
				$query = 'select prf_id as id, prf_name as label from acces_profiles where dom_num = "1" and prf_type="1" and prf_id = prf_used order by label';
				break;
			default:
				$query = parent::get_selection_query($type);
				break;
		}
		return $query;
	}

	protected function get_search_filter_global_search() {
		return $this->get_search_filter_simple_text('global_search');
	}

	protected function get_search_filter_authors() {
		global $msg;

		return $this->get_search_filter_multiple_selection($this->get_selection_query('authors'), 'authors', $msg["all"]);
	}

	protected function get_search_filter_publishers() {
		global $msg;

		return $this->get_search_filter_multiple_selection($this->get_selection_query('publishers'), 'publishers', $msg["all"]);
	}

	protected function get_search_filter_date_parution() {
		return $this->get_search_filter_interval_date('date_parution');
	}

	protected function get_search_filter_collections() {
		global $msg;

		return $this->get_search_filter_multiple_selection($this->get_selection_query('collections'), 'collections', $msg["all"]);
	}

	protected function get_search_filter_sub_collections() {
		global $msg;

		return $this->get_search_filter_multiple_selection($this->get_selection_query('sub_collections'), 'sub_collections', $msg["all"]);
	}

	protected function get_search_filter_series() {
		global $msg;

		return $this->get_search_filter_multiple_selection($this->get_selection_query('series'), 'series', $msg["all"]);
	}

	protected function get_search_filter_indexint() {
		global $msg;

		return $this->get_search_filter_multiple_selection($this->get_selection_query('indexint'), 'indexint', $msg["all"]);
	}

	protected function get_search_filter_langues() {
		return $this->get_search_filter_marclist_multiple_selection('lang', 'langues');
	}

	protected function get_search_filter_languesorg() {
		return $this->get_search_filter_marclist_multiple_selection('lang', 'languesorg');
	}

	protected function get_search_filter_niveau_biblio() {
		global $msg;

		$options = array(
				'm' => $msg['4057'],
				's' => $msg['4010'],
				'b' => $msg['bulletin_query'],
				'a' => $msg['articles_query']
		);
		return $this->get_search_filter_simple_selection('', 'niveau_biblio', $msg["all"], $options);
	}

	protected function get_search_filter_typdoc() {

		return $this->get_search_filter_marclist_simple_selection('doctype', 'typdoc');
	}

	protected function get_search_filter_origine_catalogage() {
		global $msg;

		return $this->get_search_filter_multiple_selection($this->get_selection_query('origine_notice'), 'origine_catalogage', $msg["all"]);
	}

	protected function get_search_filter_statuts() {
		global $msg;

		return $this->get_search_filter_multiple_selection($this->get_selection_query('notice_statut'), 'statuts', $msg["all"]);
	}

	protected function get_search_filter_create_date() {
		return $this->get_search_filter_interval_date('create_date');
	}

	protected function get_search_filter_update_date() {
		return $this->get_search_filter_interval_date('update_date');
	}

	protected function get_search_filter_is_new() {
		global $msg;

		return $this->get_search_filter_boolean_selection('is_new', $msg['all']);
	}

	protected function get_search_filter_is_numeric() {
		global $msg;

		return $this->get_search_filter_boolean_selection('is_numeric', $msg['all']);
	}

// 	protected function _get_query_field_order($sort_by) {
// 	    switch($sort_by) {
// 	        case 'author_main':
// 			case 'authors_others':
// 			case 'authors_secondary':
// 			case 'categories':
// 			case 'langues':
// 			case 'languesorg':
// 			case 'typdoc':
// 			case 'statut_name':
// 			case 'publisher_name':
// 				$this->applied_sort_type = 'OBJECTS';
// 				return '';
// 			case 'year':
// 				return $this->_get_query_order_sql_build('date_parution');
// 	        default :
// 	            return parent::_get_query_field_order($sort_by);
// 	    }
// 	}

	protected function _compare_objects($a, $b, $index=0) {
	    $sort_by = $this->applied_sort[$index]['by'];
		switch($sort_by) {
			case 'authors_others':
				//TODO
				break;
			case 'categories':
				$categories_a = strip_tags($this->get_cell_categories_content($a));
				$categories_b = strip_tags($this->get_cell_categories_content($b));
				return $this->strcmp($categories_a, $categories_b);
				break;
			case 'publisher_name':
				//@TODO
				break;
			default :
			    return parent::_compare_objects($a, $b, $index);
				break;
		}
	}

	protected function get_cell_categories_content($object) {
		global $thesaurus_mode_pmb;
		global $pmb_keyword_sep;

		$content = '';
		$record_datas = record_display::get_record_datas($object->notice_id);
		$categories = $record_datas->get_categories();
		foreach($categories as $thesaurus) {
			foreach ($thesaurus as $categorie) {
				$content .= ($content ? $pmb_keyword_sep : "");
				if($thesaurus_mode_pmb) {
					$content .= "[".$categorie['object']->thes->libelle_thesaurus."] ";
				}
				$content .= $categorie['object']->libelle;
			}
		}
		return $content;
	}

	protected function get_grouped_label($object, $property) {
		$grouped_label = '';
		switch($property) {
			case 'authors_others':
				//TODO
				break;
			case 'categories':
				$grouped_label = strip_tags($this->get_cell_categories_content($object));
				break;
			case 'publisher_name':
				//@TODO
				break;
			default:
				$grouped_label = parent::get_grouped_label($object, $property);
				break;
		}
		return $grouped_label;
	}

	protected function _get_object_property_author_main($object) {
		$record_datas = record_display::get_record_datas($object->notice_id);
		return $record_datas->get_auteurs_principaux();
	}

	protected function _get_object_property_authors_secondary($object) {
		$record_datas = record_display::get_record_datas($object->notice_id);
		return $record_datas->get_auteurs_secondaires();
	}

	protected function _get_object_property_langues($object) {
		$record_datas = record_display::get_record_datas($object->notice_id);
		$langues = $record_datas->get_langues();
		return record_display::get_lang_list($langues['langues']);
	}

	protected function _get_object_property_languesorg($object) {
		$record_datas = record_display::get_record_datas($object->notice_id);
		$langues = $record_datas->get_langues();
		return record_display::get_lang_list($langues['languesorg']);
	}

	protected function _get_object_property_statut_name($object) {
		$record_datas = record_display::get_record_datas($object->notice_id);
		return $record_datas->get_statut_notice();
	}

	protected function _get_object_property_typdoc($object) {
		$marc_list_instance = marc_list_collection::get_instance('doctype');
		if(!empty($marc_list_instance->table[$object->typdoc])) {
			return $marc_list_instance->table[$object->typdoc];
		}
		return '';
	}

	protected function _get_object_property_opac_permalink($object) {
		global $opac_url_base;

		return $opac_url_base."index.php?lvl=notice_display&id=".$object->notice_id;
	}

	protected function get_cell_content($object, $property) {
		global $msg, $charset;

		$content = '';
		switch($property) {
		    case 'record_header':
		    case 'record_isbd':
		        $method_name = 'get_'.$property;
		        $cart_click_noti = "onClick=\"openPopUp('./cart.php?object_type=NOTI&item=".$object->notice_id."', 'cart')\"";
		        $url = "./catalog.php?categ=serials&sub=view&serial_id=".$object->notice_id;

		        $content .= "<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title='".htmlentities($msg['400'], ENT_QUOTES, $charset)."' ".$cart_click_noti." />";
		        $content .= "<a href='".$url."'>".$object->{$method_name}()."</a>";
		        break;
		    case 'authors_others':
		    	//TODO
		    	break;
		    case 'categories':
		    	$content = $this->get_cell_categories_content($object);
		    	break;
		    case 'publisher_name' :
		    	$publishers_name = array();
		    	$record_datas = record_display::get_record_datas($object->notice_id);
		    	$publishers = $record_datas->get_publishers();
		    	if(count($publishers)) {
		    		foreach ($publishers as $publisher) {
		    			$publishers_name[] = $publisher->get_isbd();
		    		}
		    	}
		    	$content = implode(' / ',$publishers_name);
		    	break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}

	protected function _get_query_property_filter($property) {
		switch ($property) {
			case 'origine_catalogage':
				return "select orinot_nom from origine_notice where orinot_id IN (".implode(',', $this->filters[$property]).")";
		}
		return '';
	}

	protected function _get_query_human_authors() {
		if(!empty($this->filters['authors'])) {
			$labels = array();
			foreach ($this->filters['authors'] as $id) {
				$author = new auteur($id);
				$labels[] = $author->display;
			}
			return implode(', ', $labels);
		}
		return '';
	}

	protected function _get_query_human_publishers() {
		if(!empty($this->filters['publishers'])) {
			$labels = array();
			foreach ($this->filters['publishers'] as $id) {
				$publisher = new editeur($id);
				$labels[] = $publisher->display;
			}
			return implode(', ', $labels);
		}
		return '';
	}

	protected function _get_query_human_date_parution() {
		return $this->_get_query_human_interval_date('date_parution');
	}

	protected function _get_query_human_collections() {
		if(!empty($this->filters['collections'])) {
			$labels = array();
			foreach ($this->filters['collections'] as $id) {
				$collection = new collection($id);
				$labels[] = $collection->display;
			}
			return implode(', ', $labels);
		}
		return '';
	}

	protected function _get_query_human_sub_collections() {
		if(!empty($this->filters['sub_collections'])) {
			$labels = array();
			foreach ($this->filters['sub_collections'] as $id) {
				$subcollection = new subcollection($id);
				$labels[] = $subcollection->display;
			}
			return implode(', ', $labels);
		}
		return '';
	}

	protected function _get_query_human_series() {
		if(!empty($this->filters['series'])) {
			$labels = array();
			foreach ($this->filters['series'] as $id) {
				$serie = new serie($id);
				$labels[] = $serie->name;
			}
			return implode(', ', $labels);
		}
		return '';
	}

	protected function _get_query_human_indexint() {
		if(!empty($this->filters['indexint'])) {
			$labels = array();
			foreach ($this->filters['indexint'] as $id) {
				$indexint = new indexint($id);
				$labels[] = $indexint->display;
			}
			return implode(', ', $labels);
		}
		return '';
	}

	protected function _get_query_human_langues() {
		if(!empty($this->filters['langues'])) {
			$labels = array();
			$marc_list_collection = marc_list_collection::get_instance('lang');
			foreach ($this->filters['langues'] as $code) {
				$labels[] = $marc_list_collection->table[$code] ?? "";
			}
			return implode(', ', $labels);
		}
		return '';
	}

	protected function _get_query_human_languesorg() {
		if(!empty($this->filters['languesorg'])) {
			$labels = array();
			$marc_list_collection = marc_list_collection::get_instance('lang');
			foreach ($this->filters['languesorg'] as $code) {
				$labels[] = $marc_list_collection->table[$code] ?? "";
			}
			return implode(', ', $labels);
		}
		return '';
	}

	protected function _get_query_human_create_date() {
		return $this->_get_query_human_interval_date('create_date');
	}

	protected function _get_query_human_update_date() {
		return $this->_get_query_human_interval_date('update_date');
	}
}