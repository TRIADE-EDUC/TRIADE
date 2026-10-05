<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_suggestions_ui.class.php,v 1.3.2.1 2024/11/25 11:12:45 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/list/opac/list_opac_ui.class.php');

class list_opac_suggestions_ui extends list_opac_ui {

	protected $type_acte;

	protected $analyse_query;

	protected $suggestions_map;

	protected function _get_query_base() {
		$query = "select id_suggestion as id, suggestions.* from suggestions
				JOIN suggestions_origine ON id_suggestion=num_suggestion
				LEFT JOIN suggestions_categ ON id_categ=num_categ
				LEFT JOIN suggestions_source ON id_source=sugg_source";
		return $query;
	}

	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		global $acquisition_sugg_localises;
		global $acquisition_sugg_categ;

		$this->available_filters =
		array('main_fields' =>
				array(
						'user_input' => 'global_search',
						'state' => 'acquisition_sug_etat',
						'source' => 'acquisition_sugg_filtre_src',
						'origins' => 'acquisition_sugg_filtre_user',
						'date' => 'date_creation_query'

				)
		);
		if ($acquisition_sugg_localises) {
			$this->available_filters['main_fields']['location'] = 'acquisition_location';
		}
		if ($acquisition_sugg_categ) {
			$this->available_filters['main_fields']['category'] = 'acquisition_categ';
		}
		$this->available_filters['custom_fields'] = array();
	}

	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $acquisition_sugg_localises;
		global $deflt_docs_location;

		$this->filters = array(
				'user_input' => '',
				'entite' => '',
				'location' => ($acquisition_sugg_localises && $deflt_docs_location ? $deflt_docs_location : 0),
				'category' => -1,
				'source' => '',
				'state' => -1,
				'date_start' => '',
				'date_end' => '',
				'user_status' => '',
				'user_id' => ''
		);
		parent::init_filters($filters);
	}

	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'category':
	            return "libelle_categ";
	        case 'url':
	            return "url_suggestion";
	        case 'source':
	            return "libelle_source";
	        case 'etat':
	            $this->applied_sort_type = 'OBJECTS';
	            return '';
	        default :
	            return $sort_by;
	    }
	}

	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {

	    $user_input = $this->objects_type.'_user_input';
	    global ${$user_input};
	    if(isset(${$user_input})) {
	        $this->filters['user_input'] = ${$user_input};
	    }
	    $location = $this->objects_type.'_location';
	    global ${$location};
	    if(isset(${$location})) {
	        $this->filters['location'] = ${$location};
	    }
	    $category = $this->objects_type.'_category';
	    global ${$category};
	    if(isset(${$category})) {
	        $this->filters['category'] = ${$category};
	    }
	    $state = 'statut';
	    global ${$state};
	    if(isset(${$state})) {
	        $this->filters['state'] = ${$state};
	    }
	    $source = $this->objects_type.'_source';
	    global ${$source};
	    if(isset(${$source})) {
	        $this->filters['source'] = ${$source};
	    }
	    $date_start = $this->objects_type.'_date_start';
	    global ${$date_start};
	    if(isset(${$date_start})) {
	        $this->filters['date_start'] = ${$date_start};
	    }
	    $date_end = $this->objects_type.'_date_end';
	    global ${$date_end};
	    if(isset(${$date_end})) {
	        $this->filters['date_end'] = ${$date_end};
	    }
	    parent::set_filters_from_form();
	}

	protected function get_selection_query($type) {
		$query = '';
		switch ($type) {
			case 'locations':
				$query = 'select idlocation as id, location_libelle as label from docs_location order by label';
				break;
			case 'sources':
				$query = "select id_source as id, libelle_source as label from suggestions_source order by libelle_source";
				break;
		}
		return $query;
	}

	protected function get_search_filter_user_input() {
		return $this->get_search_filter_simple_text('user_input');
	}

	protected function get_search_filter_location() {
		global $msg;

		return $this->get_search_filter_simple_selection($this->get_selection_query('locations'), 'location', $msg['all_location']);
	}

	protected function get_search_filter_category() {
		global $msg, $charset;
		global $acquisition_sugg_categ;

		$selector = '';
		if ($acquisition_sugg_categ == '1') {
			$tab_categ = suggestions_categ::getCategList();
			$selector .= "<select class='saisie-25em' id='".$this->objects_type."_category' name='".$this->objects_type."_category'>";
			$selector .= "<option value='0'>".htmlentities($msg['acquisition_sug_tous'],ENT_QUOTES, $charset)."</option>";
			foreach($tab_categ as $id_categ=>$lib_categ){
				$selector .= "<option value='".$id_categ."' ".($this->filters['category'] == $id_categ ? "selected='selected'" : "")." > ".htmlentities($lib_categ,ENT_QUOTES, $charset)."</option>";
			}
			$selector.= "</select>";
		}
		return $selector;
	}

	protected function get_search_filter_state() {
		return $this->get_suggestions_map()->getStateSelector($this->filters['state']);
	}

	protected function get_search_filter_source() {
		global $msg;

		return $this->get_search_filter_simple_selection($this->get_selection_query('sources'), 'source', $msg['acquisition_sugg_all_sources']);
	}

	protected function get_search_filter_date() {
		return $this->get_search_filter_interval_date('date');
	}

	protected function _add_query_filters() {
		if($this->filters['location']) {
			$this->query_filters [] = 'sugg_location IN (0, '.intval($this->filters['location']).')';
		}
		if($this->filters['category'] && $this->filters['category'] != '-1') {
			$this->query_filters [] = 'num_categ = "'.$this->filters['category'].'"';
		}
		if($this->filters['state'] != '-1') {
			$mask = $this->get_suggestions_map()->getMask_FILED();
			if ($this->filters['state'] == $mask) {
				$this->query_filters [] = "(statut & '".$mask."') = '".$mask."' ";
			} else {
				$this->query_filters [] = "(statut & '".$mask."') = 0 and (statut & ".$this->filters['state'].") = '".$this->filters['state']."' ";
			}
		}
		$this->_add_query_filter_simple_restriction('source', 'sugg_source');
		$this->_add_query_filter_interval_restriction('date', 'date_creation', 'date');
		$tab_empr=array();
		$tab_user=array();
		$tab_visitor=array();
		if (is_array($this->filters['user_id']) && is_array($this->filters['user_status'])) {
			foreach ($this->filters['user_id'] as $k=>$id) {
				if ($this->filters['user_status'][$k] == "0") {
					$tab_user[] = $id;
				}
				if ($this->filters['user_status'][$k] == "1") {
					$tab_empr[] = $id;
				}
				if ($this->filters['user_status'][$k] == "2") {
					$tab_visitor[] = $id;
				}
			}
			$filters_origins = array();
			if(count($tab_empr)) {
				$filters_origins[] = 'suggestions_origine.origine IN ("'.implode('","', $tab_empr).'") AND type_origine="1"';
			}
			if(count($tab_user)) {
				$filters_origins[] = 'suggestions_origine.origine IN ("'.implode('","', $tab_user).'") AND type_origine="0"';
			}
			if(count($tab_visitor)) {
				$filters_origins[] = 'suggestions_origine.origine IN ("'.implode('","', $tab_visitor).'") AND type_origine="2"';
			}
			if(count($filters_origins)) {
				$this->query_filters [] = "(".implode(") or (", $filters_origins).")";
			}
		}
		if($this->filters['user_input']) {
			$aq = $this->get_analyse_query();
			$isbn = '';
			$t_codes = array();

			if (isEAN($this->filters['user_input'])) {
				// la saisie est un EAN -> on tente de le formater en ISBN
				$isbn = EANtoISBN($this->filters['user_input']);
				if($isbn) {
					$t_codes[] = $isbn;
					$t_codes[] = formatISBN($isbn,10);
				}
			} elseif (isISBN($this->filters['user_input'])) {
				// si la saisie est un ISBN
				$isbn = formatISBN($this->filters['user_input']);
				if($isbn) {
					$t_codes[] = $isbn ;
					$t_codes[] = formatISBN($isbn,13);
				}
			}
			if (count($t_codes)) {
				$q_codes = "(";
				foreach ($t_codes as $k=>$v) {
					if($k) {
						$q_codes.= "or code like '%".$v."%' ";
					} else {
						$q_codes.= "code like '%".$v."%' ";
					}
				}
				$q_codes.=") ";
				$this->query_filters [] = $q_codes;
			} else {
				$members=$aq->get_query_members("suggestions","concat(titre,' ',editeur,' ',auteur,' ',commentaires)","index_suggestion","id_suggestion");
				$this->query_filters [] = $members["where"];
			}
		}
	}

	protected function _get_object_property_etat($object) {
		return $this->get_suggestions_map()->getHtmlComment($object->statut);
	}

	protected function _get_object_property_source($object) {
		$source = new suggestion_source($object->sugg_source);
		return $source->libelle_source;
	}

	protected function _get_object_property_category($object) {
		$categ = new suggestions_categ($object->num_categ);
		return $categ->libelle_categ;
	}

	protected function get_cell_content($object, $property) {
		global $charset;
		global $base_path;

		$content = '';
		switch($property) {
		    case 'etat':
		        $content .= $this->get_suggestions_map()->getHtmlComment($object->statut);
		        break;
			case 'url':
				if($object->url_suggestion) {
					$content .= "<a href='".$object->url_suggestion."' target='_blank'><img src='".get_url_icon('globe.gif')."' title='".htmlentities($object->url_suggestion, ENT_QUOTES, $charset)."' alt='".htmlentities($object->url_suggestion, ENT_QUOTES, $charset)."' />";
				}
				break;
			case 'piece_jointe':
				$sug = new suggestions($object->id_suggestion);
				if($sug->get_explnum('id')){
				    $content .="<a href=\"$base_path/explnum_doc.php?explnumdoc_id=".$sug->get_explnum('id')."\" target=\"_blank\"><img src='".get_url_icon("globe_orange.png")."' alt='' style='border:0px'/></a>";
				}
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
			case 'url':
				break;
			default:
			    if($object->statut == 1){
			        //Si la suggestion n'est pas validée on peut la modifier
			        $attributes['onclick'] = "document.location=\"".$base_path."/empr.php?lvl=make_sugg&id_sug=".$object->id_suggestion."\"";
			    } else {
			        $attributes['onclick'] = "document.location=\"".$base_path."/empr.php?lvl=view_sugg&id_sug=".$object->id_suggestion."\"";
			    }
				break;
		}
		return $attributes;
	}

	public function get_error_message_empty_list() {
	    global $msg, $charset;
	    return htmlentities($msg['empr_view_no_sugg'], ENT_QUOTES, $charset);
	}

	protected function _get_query_human_user_input() {
		if($this->filters['user_input'] !== '*') {
			return $this->filters['user_input'];
		}
		return '';
	}

	protected function _get_query_human_location() {
		if($this->filters['location']) {
			$docs_location = new docs_location($this->filters['location']);
			return $docs_location->libelle;
		}
		return '';
	}

	protected function _get_query_human_category() {
		if($this->filters['category'] && $this->filters['category'] != '-1') {
			$categ = new suggestions_categ($this->filters['category']);
			return $categ->libelle_categ;
		}
		return '';
	}

	protected function _get_query_human_state() {
		if($this->filters['state'] && $this->filters['state'] != '-1') {
			$states = $this->get_suggestions_map()->getStateList();
			return $states[$this->filters['state']];
		}
		return '';
	}

	protected function _get_query_human_source() {
		$source = new suggestion_source($this->filters['source']);
		return $source->libelle_source;
	}

	protected function _get_query_human_date() {
		return $this->_get_query_human_interval_date('date');
	}

	public function get_analyse_query() {
		global $msg;

		if(!isset($this->analyse_query)) {
			$this->analyse_query = new analyse_query(stripslashes($this->filters['user_input']),0,0,0,0);
			if ($this->analyse_query->error) {
				error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$this->analyse_query->current_car,$this->analyse_query->input_html,$this->analyse_query->error_message));
				exit;
			}
		}
		return $this->analyse_query;
	}

	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		global $opac_sugg_categ;

		$this->available_columns =
		array('main_fields' =>
				array(
						'date_creation' => 'acquisition_sug_dat_cre',
						'titre' => 'acquisition_sug_tit',
						'editeur' => 'acquisition_sug_edi',
						'auteur' => 'acquisition_sug_aut',
						'etat' => 'acquisition_sug_etat',
						'url' => 'acquisition_sug_url',
						'source' => 'empr_sugg_src',
						'date_publication' => 'empr_sugg_datepubli',
						'piece_jointe' => 'empr_sugg_piece_jointe',
						'commentaires' => 'acquisition_sug_com',
						'nb' => 'acquisition_sug_qte',
						'code' => 'acquisition_sug_cod',
						'prix' => 'acquisition_sug_pri'
				)
		);
		if ($opac_sugg_categ) {
			$this->available_columns['main_fields']['category'] = 'acquisition_categ';
		}
	}

	protected function init_default_columns() {
		global $opac_sugg_categ;

		$this->add_column('date_creation');
		$this->add_column('titre');
		$this->add_column('editeur');
		$this->add_column('auteur');
		$this->add_column('etat');
		$this->add_column('date_publication');
		$this->add_column('source');
		if ($opac_sugg_categ) {
			$this->add_column('category');
		}
		$this->add_column('piece_jointe');
	}

	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('url', 'align', 'center');
		$this->set_setting_column('nb', 'align', 'center');
		$this->set_setting_column('prix', 'align', 'center');
		$this->set_setting_column('url', 'text', array('italic' => false));
		$this->set_setting_column('date_creation', 'datatype', 'date');
		$this->set_setting_column('nb', 'datatype', 'integer');
		$this->set_setting_column('piece_jointe', 'align', 'center');
		$this->set_setting_column('piece_jointe', 'text', array('italic' => true));
		$this->set_setting_display('pager', 'visible', false);
	}

	protected function init_default_pager() {
	    parent::init_default_pager();
	    $this->pager['all_on_page'] = true;
	}

	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'piece_jointe'
		);
	}

	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('date_creation');
	}

	public static function get_controller_url_base() {
		global $base_path;

		return $base_path.'/empr.php?tab=sugg';
	}

	public function get_suggestions_map() {
		if(!isset($this->suggestions_map)) {
			$this->suggestions_map = new suggestions_map();
		}
		return $this->suggestions_map;
	}
}