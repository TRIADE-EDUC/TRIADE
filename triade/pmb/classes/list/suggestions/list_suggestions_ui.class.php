<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_suggestions_ui.class.php,v 1.6 2019-05-11 15:09:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/list_ui.class.php");
require_once($class_path."/entites.class.php");
require_once($class_path."/docs_location.class.php");
require_once($class_path."/suggestions.class.php");
require_once($class_path."/analyse_query.class.php");

class list_suggestions_ui extends list_ui {
		
	protected $type_acte;
	
	protected $analyse_query;
	
	protected $suggestions_map;
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	public function get_form_title() {
		global $msg, $charset;
		
		return htmlentities($msg['recherche'].' : '.$msg['acquisition_sug'], ENT_QUOTES, $charset);
	}
	
	protected function _get_query_base() {
		$query = "select id_suggestion as id, suggestions.* from suggestions 
				JOIN suggestions_origine ON id_suggestion=num_suggestion
				LEFT JOIN suggestions_categ ON id_categ=num_categ";
		return $query;
	}
	
	protected function add_object($row) {
		$this->objects[] = $row;
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
	
	protected function init_default_selected_filters() {
		global $acquisition_sugg_localises;
		global $acquisition_sugg_categ;
		
		$this->add_selected_filter('user_input');
		if ($acquisition_sugg_localises) {
			$this->add_selected_filter('location');
		} else {
			$this->add_empty_selected_filter();
		}
		if ($acquisition_sugg_categ) {
			$this->add_selected_filter('category');
		} else {
			$this->add_empty_selected_filter();
		}
		$this->add_selected_filter('state');
		$this->add_selected_filter('source');
		$this->add_selected_filter('origins');
		$this->add_selected_filter('date');
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
		global $nb_per_page_search;
		$this->pager = array(
				'page' => 1,
				'nb_per_page' => $nb_per_page_search,
				'nb_results' => 0,
				'nb_page' => 1
		);
	}
		
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
	
		if($this->applied_sort['by']) {
			$order = '';
			$sort_by = $this->applied_sort['by'];
			switch($sort_by) {
				case 'category':
					$order .= "libelle_categ";
					break;
				default :
					$order .= $sort_by;
					break;
			}
			if($order) {
				$this->applied_sort_type = 'SQL';
				return " order by ".$order." ".$this->applied_sort['asc_desc'];
			} else {
				return "";
			}
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
		$user_id = $this->objects_type.'_user_id';
		global ${$user_id};
		if(isset(${$user_id}) && is_array(${$user_id})) {
			$this->filters['user_id'] = ${$user_id};
		}
		$user_status = $this->objects_type.'_user_status';
		global ${$user_status};
		if(isset(${$user_status}) && is_array(${$user_status})) {
			$this->filters['user_status'] = ${$user_status};
		}
		parent::set_filters_from_form();
	}
	
	protected function get_selector_query($type) {
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
		global $charset;
		
		return "<input type='text' class='saisie-30em' id='".$this->objects_type."_user_input' name='".$this->objects_type."_user_input' value='".htmlentities($this->filters['user_input'], ENT_QUOTES, $charset)."' />";
	}
	
	protected function get_search_filter_location() {
		global $msg;
	
		return $this->get_simple_selector($this->get_selector_query('locations'), 'location', $msg['all_location']);
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
		return $this->get_suggestions_map()->getStateSelector();
	}
	
	protected function get_search_filter_source() {
		global $msg, $charset;
	
		return $this->get_simple_selector($this->get_selector_query('sources'), 'source', $msg['acquisition_sugg_all_sources']);
	}
	
	protected function get_search_filter_date() {
		return $this->get_search_filter_interval_date('date');
	}
	
	protected function get_search_filter_origins_others($indice) {
		global $msg;
		
		return "
		<div class='row'>
			<input type='hidden' id='".$this->objects_type."_user_id".$indice."' name='".$this->objects_type."_user_id[".$indice."]' value='!!user_id!!'/>
			<input type='hidden' id='".$this->objects_type."_user_status".$indice."' name='".$this->objects_type."_user_status[".$indice."]' value='!!user_status!!' />
			<input type='text' id='".$this->objects_type."_user_txt".$indice."' name='".$this->objects_type."_user_txt[".$indice."]' class='saisie-20emr' value='!!user_txt!!'/>
			<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=origine&caller=".$this->get_form_name()."&param1=".$this->objects_type."_user_id".$indice."&param2=".$this->objects_type."_user_txt".$indice."&param3=".$this->objects_type."_user_statut".$indice."&deb_rech='+".pmb_escape()."(this.form.".$this->objects_type."_user_txt".$indice.".value), 'selector')\" />
			<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.".$this->objects_type."_user_id".$indice.".value=0;this.form.".$this->objects_type."_user_statut".$indice.".value=0;this.form.".$this->objects_type."_user_txt".$indice.".value=''\"/>
		</div>
		";
	}
	protected function get_search_filter_origins() {
		global $msg, $charset;
		
		$origins_inputs_form = "
			<input type='hidden' id='".$this->objects_type."_user_id0' name='".$this->objects_type."_user_id[0]' value='!!user_id!!'/>
			<input type='hidden' id='".$this->objects_type."_user_status0' name='".$this->objects_type."_user_status[0]' value='!!user_status!!' />
			<input type='text' id='".$this->objects_type."_user_txt0' name='".$this->objects_type."_user_txt[0]' class='saisie-20emr' value='!!user_txt!!'/>
			<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=origine&caller=".$this->get_form_name()."&param1=".$this->objects_type."_user_id0&param2=".$this->objects_type."_user_txt0&param3=".$this->objects_type."_user_status0&deb_rech='+".pmb_escape()."(this.form.".$this->objects_type."_user_txt0.value), 'selector')\" />
			<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.".$this->objects_type."_user_id0.value='';this.form.".$this->objects_type."_user_status0.value='';this.form.".$this->objects_type."_user_txt0.value=''\"/>
			<input type='button' onclick='add_orig();' value='+' class='bouton_small' />
			<input type='hidden' id='max_orig' value='!!max_orig!!' />
			<div id='add_orig' ><!-- sel_orig --></div>";
		
		//Affichage origine
		$i=0;
		if (is_array($this->filters['user_id']) && count($this->filters['user_id']) && is_array($this->filters['user_status']) && count($this->filters['user_status'])) {
			foreach($this->filters['user_id'] as $k=>$v) {
				if ($this->filters['user_id'][$k]) {
					$user_name = $user_txt[$k];
					if(!$user_txt[$k]){
						if ($this->filters['user_status'][$k]==='0') {
							$req = "select nom, prenom from users where userid='".$this->filters['user_id'][$k]."'";
							$res = pmb_mysql_query($req);
							$user = pmb_mysql_fetch_object($res);
							$user_name = $user->nom.($user->prenom ? ", ".$user->prenom : "");
						} else {
							$req = "select concat(empr_nom,', ',empr_prenom) as nom from empr where id_empr='".$this->filters['user_id'][$k]."'";
							$res = pmb_mysql_query($req);
							$empr = pmb_mysql_fetch_object($res);
							$user_name = $empr->nom;
						}
					}
					if($i>0) {
						$origins_inputs_form=str_replace('<!-- sel_orig -->',$this->get_search_filter_origins_others($k).'<!-- sel_orig -->',$origins_inputs_form);
					}
					$origins_inputs_form = str_replace('!!user_txt!!',htmlentities($user_name,ENT_QUOTES,$charset), $origins_inputs_form);
					$origins_inputs_form = str_replace('!!user_id!!',htmlentities($this->filters['user_id'][$k],ENT_QUOTES,$charset), $origins_inputs_form);
					$origins_inputs_form = str_replace('!!user_status!!',htmlentities($this->filters['user_status'][$k],ENT_QUOTES,$charset), $origins_inputs_form);
					$i++;
				}
			}
			$origins_inputs_form=str_replace('!!max_orig!!',$i,$origins_inputs_form);
		}
		if (!$i) {
			$origins_inputs_form=str_replace('!!user_id!!','',$origins_inputs_form);
			$origins_inputs_form=str_replace('!!user_status!!','',$origins_inputs_form);
			$origins_inputs_form=str_replace('!!user_txt!!','',$origins_inputs_form);
			$origins_inputs_form=str_replace('!!max_orig!!','1',$origins_inputs_form);
		}
		return $origins_inputs_form;
	}
	
	public function get_search_filters_form() {
		global $msg;
		
		$search_filters_form = "
		<script type='text/javascript' src='javascript/ajax.js'></script>
		<script type='text/javascript' src='javascript/suggestion.js'></script>
		<script type='text/javascript' >
			var msg_parcourir='".addslashes($msg['parcourir'])."'; 
			var msg_raz='".addslashes($msg['raz'])."'; 
		</script>";
		$search_filters_form .= parent::get_search_filters_form();
		return $search_filters_form;
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		$search_form = parent::get_search_form();
		$search_form = str_replace('!!action!!', static::get_controller_url_base(), $search_form);
		return $search_form;
	}
	
	/**
	 * Jointure externes SQL pour les besoins des filtres
	 */
	protected function _get_query_join_filters() {
	
		$filter_join_query = '';
		if($this->filters['category'] && $this->filters['category'] != '-1') {
			$filter_join_query .= " LEFT JOIN suggestions_categ ON suggestions.num_categ=suggestions_categ.id_categ";
		}
		return $filter_join_query;
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if($this->filters['location']) {
			$filters[] = 'sugg_location = "'.$this->filters['location'].'"';
		}
		if($this->filters['category'] && $this->filters['category'] != '-1') {
			$filters[] = 'num_categ = "'.$this->filters['category'].'"';
		}
		if($this->filters['state'] != '-1') {
			$mask = $this->get_suggestions_map()->getMask_FILED();
			if ($this->filters['state'] == $mask) {
				$filters[] = "(statut & '".$mask."') = '".$mask."' ";
			} else {
				$filters[] = "(statut & '".$mask."') = 0 and (statut & ".$this->filters['state'].") = '".$this->filters['state']."' ";
			}
		}
		if($this->filters['source']) {
			$filters[] = 'sugg_source = "'.$this->filters['source'].'"';
		}
		if($this->filters['date_start']) {
			$filters[] = 'date_creation >= "'.$this->filters['date_start'].'"';
		}
		if($this->filters['date_end']) {
			$filters[] = 'date_creation <= "'.$this->filters['date_end'].'"';
		}
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
				$filters[] = "(".implode(") or (", $filters_origins).")";
			}
		}
		if(count($filters)) {
			$filter_query .= $this->_get_query_join_filters();
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
		
	protected function get_link_action($action, $act) {
		global $msg;
		global $base_path;
		global $acquisition_sugg_to_cde;
		
		$href = '';
		switch ($action) {
			case 'MERGE':
				$href = $base_path."/acquisition.php?categ=sug&action=fusChk";
				break;
			case 'ESTIMATED':
				if ($acquisition_sugg_to_cde) {
					$href = $base_path."/acquisition.php?categ=ach&sub=devi&action=from_sug";
				}
				break;
			case 'ORDERED':
				if ($acquisition_sugg_to_cde) {
					$href = $base_path."/acquisition.php?categ=ach&sub=cde&action=from_sug";
				}
				break;
			case 'TODO':
				$href = $base_path."/acquisition.php?categ=sug&transition=TODO";
				break;
			case 'TO_CATEG':
				$href = $base_path."/acquisition.php?categ=sug&action=to_categ";
				break;
		}
		if(!$href) {
			$href = static::get_controller_url_base()."&transition=".$action."&action=list";
		}
		return array(
				'href' => $href,
				'confirm' => $msg['acquisition_sug_msg_'.$act]
		);
	}
	
	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'sugg', $display);
		$display = str_replace('!!sub!!', '', $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		global $base_path;
		
		$content = '';
		switch($property) {
			case 'etat':
				$content .= $this->get_suggestions_map()->getHtmlComment($object->statut);
				break;
			case 'catalog':
				if($object->num_notice) {
					$req_ana = "select analysis_bulletin as bull , analysis_notice as noti from analysis where analysis_notice ='".$object->num_notice."'";
					$res_ana = pmb_mysql_query($req_ana);
					$num_rows_ana = pmb_mysql_num_rows($res_ana);
					if($num_rows_ana){
						$ana = pmb_mysql_fetch_object($res_ana);
						$url_view = "catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=$ana->bull&art_to_show=".$ana->noti;
					} else $url_view = "./catalog.php?categ=isbd&id=".$object->num_notice;
					$content .= "<a href=\"".$url_view."\"><img border=\"0\" class='align_middle' title=\"".$msg['acquisition_sug_view_not']."\" alt=\"".$msg['acquisition_sug_view_not']."\" src=\"".get_url_icon('notice.gif')."\" /></a>";
				}
				break;
			case 'url':
				if($object->url_suggestion) {
					$content .= "<a href='".$object->url_suggestion."' target='_blank'><img src='".get_url_icon('globe.gif')."' title='".htmlentities($object->url_suggestion, ENT_QUOTES, $charset)."' alt='".htmlentities($object->url_suggestion, ENT_QUOTES, $charset)."' hspace='3' />";
				}
				break;
			case 'date_creation':
				$content .= formatdate($object->date_creation);
				break;
			case 'source':
				$source = new suggestion_source($object->sugg_source);
				$content .= $source->libelle_source;
				break;
			case 'piece_jointe':
				$sug = new suggestions($object->id_suggestion);
				$img_pj = "<a href=\"".$base_path."/explnum_doc.php?explnumdoc_id=".$sug->get_explnum('id')."\" target=\"_blank\"><img src='".get_url_icon('globe_orange.png')."' /></a>";
				$img_import = "<a href=\"".$base_path."/acquisition.php?categ=sug&sub=import&explnumdoc_id=".$sug->get_explnum('id')." \"><img src='".get_url_icon('upload.gif')."' /></a>";
				$content .= ($sug->get_explnum('id') ? "$img_pj&nbsp;$img_import" : '' );
				break;
			case 'category':
				$categ = new suggestions_categ($object->num_categ);
				$content .= $categ->libelle_categ;
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_name_selected_objects() {
		return "chk";
	}
	
	protected function get_display_cell($object, $property) {
		switch($property) {
			case 'catalog':
				$display = "<td class='center'><i>".$this->get_cell_content($object, $property)."</i></td>";
				break;
			case 'url':
				$display = "<td class='center'>".$this->get_cell_content($object, $property)."</td>";
				break;
			default:
				$display = "<td onclick=\"window.location='".static::get_controller_url_base()."&action=modif&id_bibli=".$this->filters['entite']."&id_sug=".$object->id_suggestion."'\" style='cursor:pointer;'><i>".$this->get_cell_content($object, $property)."</i></td>";
				break;
		}	
		return $display;
	}
	
	protected function _get_query_human() {
		global $msg, $charset;
		
		$humans = array();
		if($this->filters['entite']) {
			$entites = new entites($this->filters['entite']);
			$humans[] = $this->_get_label_query_human($msg['acquisition_coord_lib'], $entites->raison_sociale);
		}
		if($this->filters['user_input'] !== '*') {
			$humans[] = $this->_get_label_query_human($msg['global_search'], $this->filters['user_input']);
		}
		if($this->filters['location']) {
			$docs_location = new docs_location($this->filters['location']);
			$humans[] = $this->_get_label_query_human($msg['acquisition_location'], $docs_location->libelle);
		}
		if($this->filters['category'] && $this->filters['category'] != '-1') {
			$categ = new suggestions_categ($this->filters['category']);
			$humans[] = $this->_get_label_query_human($msg['acquisition_categ'], $categ->libelle_categ);
		}
		if($this->filters['state'] && $this->filters['state'] != '-1') {
			$states = $this->get_suggestions_map()->getStateList();
			$humans[] = $this->_get_label_query_human($msg['acquisition_sug_etat'], $states[$this->filters['state']]);
		}
		if($this->filters['source']) {
			$source = new suggestion_source($this->filters['source']);
			$humans[] = $this->_get_label_query_human($msg['acquisition_sugg_filtre_src'], $source->libelle_source);
		}
		
		if(is_array($this->filters['user_id']) && count($this->filters['user_id'])) {
			$labels = array();
			foreach ($this->filters['user_id'] as $k=>$user_id) {
				if($this->filters['user_status'][$k]) {
					$labels[] = emprunteur::get_name($user_id);
				} else {
					$labels[] = user::get_name($user_id);
				}
			}
			$humans[] = $this->_get_label_query_human($msg['acquisition_sugg_filtre_user'], implode(',', $labels));
		}
		if($this->filters['date_start']) {
			$humans[] = $this->_get_label_query_human($msg['date_creation_query']." - ".$msg['list_ui_filter_date_start'], formatdate($this->filters['date_start']));
		}
		if($this->filters['date_end']) {
			$humans[] = $this->_get_label_query_human($msg['date_creation_query']." - ".$msg['list_ui_filter_date_end'], formatdate($this->filters['date_end']));
		}
		return $this->get_display_query_human($humans);
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
	
	protected function get_button_add() {
		global $msg;
	
		return "<input class='bouton' type='button' value='".$msg['acquisition_ajout_sug']."' onClick=\"document.location='./acquisition.php?categ=sug&sub=todo&action=modif&id_bibli=".$this->filters['entite']."&sugg_location_id=".$this->filters['location']."';\" />";
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		global $acquisition_sugg_categ;
		
		$this->available_columns =
		array('main_fields' =>
				array(
						'date_creation' => 'acquisition_sug_dat_cre',
						'titre' => 'acquisition_sug_tit',
						'editeur' => 'acquisition_sug_edi',
						'auteur' => 'acquisition_sug_aut',
						'etat' => 'acquisition_sug_etat',
						'catalog' => 'acquisition_sug_iscat',
						'url' => 'acquisition_sug_url',
						'source' => 'acquisition_sugg_src',
						'date_publication' => 'acquisition_sugg_date_publication',
						'piece_jointe' => 'acquisition_sugg_piece_jointe'
				)
		);
		if ($acquisition_sugg_categ) {
			$this->available_columns['main_fields']['category'] = 'acquisition_categ';
		}
	}
	
	protected function init_default_columns() {
		global $acquisition_sugg_categ;

		$this->add_column_selection();
		$this->add_column('date_creation');
		$this->add_column('titre');
		$this->add_column('editeur');
		$this->add_column('auteur');
		$this->add_column('etat');
		$this->add_column('catalog');
		$this->add_column('url');
		if ($acquisition_sugg_categ) {
			$this->add_column('category');
		}
		$this->add_column('source');
		$this->add_column('date_publication');
		$this->add_column('piece_jointe');
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'statut',
				'asc_desc' => 'asc'
		);
	}
	
	public function get_export_icons() {
		return '';
	}
	
	public static function get_controller_url_base() {
		global $base_path;
	
		return $base_path.'/acquisition.php?categ=sug';
	}
	
	protected function get_selection_actions() {
		global $msg;
	
		if(!isset($this->selection_actions)) {
			$this->selection_actions = array();
			$this->selection_actions[] = $this->get_selection_action('merge', $msg['acquisition_sug_bt_fus'], '', $this->get_link_action('MERGE', 'fus'));
			$this->selection_actions[] = $this->get_selection_action('validated', $msg['acquisition_sug_bt_val'], '', $this->get_link_action('VALIDATED', 'val'));
			$this->selection_actions[] = $this->get_selection_action('rejected', $msg['acquisition_sug_bt_rej'], '', $this->get_link_action('REJECTED', 'rej'));
			$this->selection_actions[] = $this->get_selection_action('confirmed', $msg['acquisition_sug_bt_con'], '', $this->get_link_action('CONFIRMED', 'con'));
			$this->selection_actions[] = $this->get_selection_action('givenup', $msg['acquisition_sug_bt_aba'], '', $this->get_link_action('GIVENUP', 'aba'));
			$this->selection_actions[] = $this->get_selection_action('ordered', $msg['acquisition_sug_bt_cde'], '', $this->get_link_action('ORDERED', 'cde'));
			$this->selection_actions[] = $this->get_selection_action('estimated', $msg['acquisition_sug_bt_dev'], '', $this->get_link_action('ESTIMATED', 'dev'));
			$this->selection_actions[] = $this->get_selection_action('received', $msg['acquisition_sug_bt_rec'], '', $this->get_link_action('RECEIVED', 'rec'));
			$this->selection_actions[] = $this->get_selection_action('filed', $msg['acquisition_sug_bt_arc'], '', $this->get_link_action('FILED', 'arc'));
			if ($this->filters['state'] == '-1') { //Tous états possibles
				$this->selection_actions[] = $this->get_selection_action('deleted', $msg['63'], '', $this->get_link_action('DELETED', 'sup'));
			} else {
				$state_name=$this->get_suggestions_map()->id_to_name[$this->filters['state']];
				$tostates=$this->get_suggestions_map()->transitions[$state_name];
				if (in_array('DELETED', $tostates)) {
					$this->selection_actions[] = $this->get_selection_action('deleted', $msg['63'], '', $this->get_link_action('DELETED', 'sup'));
				}
			}
		}
		return $this->selection_actions;
	}
	
	protected function get_selection_mode() {
		return 'button';
	}
		
	protected function add_event_on_selection_other_action($action=array()) {
		$display = "<script type='text/javascript'>
		require([
				'dojo/on',
				'dojo/dom',
				'dojo/query',
				'dojo/dom-construct',
		], function(on, dom, query, domConstruct){";
		$display .= $this->add_event_on_selection_action($action);
		$display .= "});
		</script>";
		return $display;
	}
	
	protected function get_display_other_action_print() {
		global $base_path;
		global $msg, $charset;
		
		$url_print="./pdf.php?pdfdoc=listsug&user_input=".urlencode($this->filters['user_input'])."&statut=".$this->filters['state']."&num_categ=".$this->filters['category']."&sugg_location_id=".$this->filters['location']."&date_inf=".$this->filters['date_start']."&date_sup=".$this->filters['date_end']."&filtre_src=".$this->filters['source'];
		if (is_array($this->filters['user_id']) && count($this->filters['user_id']) && is_array($this->filters['user_status']) && count($this->filters['user_status'])) {
			foreach($this->filters['user_id'] as $k=>$v) {
				if ($this->filters['user_id'][$k] && (isset($this->filters['user_status'][$k]))) {
					$url_print.="&origine_id[]=".$this->filters['user_id'][$k]."&type_origine[]=".$this->filters['user_status'][$k];
				}
			}
		}else{
			$url_print.="&origine_id=".$this->filters['user_id']."&type_origine=".$this->filters['user_status'];
		}
	
		$display = "<input type='button' class='bouton_small' value='$msg[imprimer]' onClick=\"openPopUp('".$url_print."' ,'print_PDF');\" />";
	
		return $display;
	}
	
	protected function get_display_spreadsheet_cell($object, $property, $row, $col) {
		switch ($property) {
			case 'url':
				$this->spreadsheet->write_string($row,$col, $object->url_suggestion);
				break;
			default:
				$this->spreadsheet->write_string($row,$col, strip_tags($this->get_cell_content($object, $property)));
				break;
		}
	}
	
	protected function get_display_other_action_export() {
		global $base_path;
		global $msg, $charset;
		
		
				
		$display = "<input type='button' id='".$this->objects_type."_selection_action_export_link'class='bouton_small' value='".$msg['admin_Expvers']."' />";
		$action = array(
				'name' => 'export', 
				'link' => array(
						'href' => $base_path.'/acquisition.php?categ=sug&sub=export'
				)
		);
		$display .= $this->add_event_on_selection_other_action($action);
		
		//Génération de la liste des conversions possibles
		$catalog=_parser_text_no_function_(file_get_contents($base_path."/admin/convert/imports/catalog.xml"),"CATALOG");
		$display .= "<select name='export_list'>";
		for ($i=0; $i<count($catalog["ITEM"]); $i++) {
			$item=$catalog["ITEM"][$i];
			if (isset($item["EXPORT"]) && $item["EXPORT"]=="yes") {
				$display .= "<option value='".$i."'>".htmlentities($item["EXPORTNAME"],ENT_QUOTES,$charset)."</option>\n";
			}
		}
		$display .= "</select>";
		return $display;
	}
	
	protected function get_display_other_action_export_tableau() {
		global $base_path;
		global $msg, $charset;
		
		$display = "<input type='button' id='".$this->objects_type."_selection_action_export_tableau_link' class='bouton_small' value='".$msg['sugg_export_tableau']."' />";
		$action = array(
				'name' => 'export_tableau',
				'link' => array(
						'href' => $base_path.'/acquisition.php?categ=sug&sub=export_tableau'
				)
		);
		$display .= $this->add_event_on_selection_other_action($action);
		return $display;
	}
	
	protected function get_display_other_action_todo() {
		global $base_path;
		global $msg, $charset;
		
		$display = "<input type='button' id='".$this->objects_type."_selection_action_todo_link' class='bouton_small' value='".$msg['acquisition_sug_bt_todo']."' />";
		$action = array('name' => 'todo' ,'link' => $this->get_link_action('TODO', 'todo'));
		if (!$this->filters['state']) $this->filters['state']='-1';
		if ($this->filters['state'] == '-1') { //Tous états possibles
			$display .= $this->add_event_on_selection_other_action($action);
			return $display;
		} else {
			$state_name=$this->get_suggestions_map()->id_to_name[$this->filters['state']];
			$tostates=$this->get_suggestions_map()->transitions[$state_name];
			if (in_array('TODO', $tostates)) {
				$display .= $this->add_event_on_selection_other_action($action);
				return $display;
			}
		}
		return "";
	}
	
	protected function get_display_other_action_to_categ() {
		global $msg, $charset;
		
		$display = "<label class='etiquette' >".htmlentities($msg['acquisition_sug_sel_categ'],ENT_QUOTES, $charset)."</label>&nbsp;";
		$display.= "<select class='saisie-25em' id='".$this->objects_type."_selection_action_to_categ_link' name='".$this->objects_type."_selection_action_to_categ_link' onChange=\"chk_CATEG(); \">";
		$display.= "<option value= '0'>".htmlentities($msg['acquisition_sug_sel_no_categ'], ENT_QUOTES, $charset)."</option>";
		$tab_categ = suggestions_categ::getCategList();
		foreach ($tab_categ as $id_categ=>$lib_categ) {
			$display.= "<option value='".$id_categ."' >".htmlentities($lib_categ, ENT_QUOTES, $charset)."</option>";
		}
		$display.= "</select>";
		$action = array('name' => 'to_categ' ,'link' => $this->get_link_action('TO_CATEG', 'tocateg'));
		if ($this->filters['state'] == '-1') { //Tous états possibles
			$display .= $this->add_event_on_selection_other_action($action);
			return $display;
		} else {
			$state_name=$this->get_suggestions_map()->id_to_name[$this->filters['state']];
			if ($this->get_suggestions_map()->getState_CATEG($state_name) == 'YES') {
				$display .= $this->add_event_on_selection_other_action($action);
				return $display;
			}
		}
		return "";
	}
	
	protected function get_display_others_actions() {
		global $msg, $charset;
		global $acquisition_sugg_categ;
		
		return "
		<div class='row'>&nbsp;</div>
		<div id='list_ui_others_actions' class='list_ui_others_actions ".$this->objects_type."_others_actions'>
			<div class='left'>
				".$this->get_display_other_action_print()."&nbsp;
				".$this->get_display_other_action_export()."&nbsp;
				".$this->get_display_other_action_export_tableau()."&nbsp;
				".$this->get_display_other_action_todo()."&nbsp;
				<span class='child' >".($acquisition_sugg_categ == '1' ? $this->get_display_other_action_to_categ() : '')."</span>
			</div>
		</div>
		<div class='row'>&nbsp;</div>
		";
	}
	
	public function get_suggestions_map() {
		if(!isset($this->suggestions_map)) {
			$this->suggestions_map = new suggestions_map();
		}
		return $this->suggestions_map;
	}
}