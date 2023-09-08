<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_readers_ui.class.php,v 1.6 2019-06-11 08:53:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/list_ui.class.php");

class list_readers_ui extends list_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function _get_query_base() {
		$query = 'SELECT id_empr FROM empr
				JOIN empr_statut ON empr.empr_statut=empr_statut.idstatut
				JOIN empr_categ ON empr.empr_categ=empr_categ.id_categ_empr';
		return $query;
	}
	
	protected function add_object($row) {
		$this->objects[] = new emprunteur($row->id_empr);
	}
		
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		global $pmb_lecteurs_localises;
		
		$this->available_filters =
		array('main_fields' =>
				array(
						'categorie' => 'editions_filter_empr_categ',
						'categories' => 'dsi_ban_form_categ_lect',
						'groups' => 'dsi_ban_form_groupe_lect',
						'name' => 'dsi_ban_abo_empr_nom',
						'has_mail' => 'dsi_ban_abo_mail',
						'has_affected' => 'dsi_ban_lecteurs_affectes',
						'mail' => 'email',
						'codestat_one' => 'editions_filter_empr_codestat',
						'codestat' => '24',
						'status' => 'statut_empr',
						'date_adhesion' => 'empr_date_adhesion',
						'date_expiration' => 'readerlist_dateexpiration',
						'date_creation' => 'date_creation_query'
				)
		);
		if($pmb_lecteurs_localises) {
			$this->available_filters['main_fields']['location'] = 'editions_filter_empr_location';
			$this->available_filters['main_fields']['locations'] = '21'; 
		}
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $pmb_lecteurs_localises, $deflt2docs_location;
		
		$this->filters = array(
				'empr_statut_edit' => '',
				'empr_categ_filter' => '',
				'empr_codestat_filter' => '',
				'status' => array(),
				'categories' => array(),
				'codestat' => array(),
				'groups' => array(),
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
				'change_categ' => ''
		);
		if(static::class == 'list_readers_bannette_ui') {
			$this->filters['locations'] = ($pmb_lecteurs_localises ? array($deflt2docs_location) : array());
		} else {
			$this->filters['empr_location_id'] = ($pmb_lecteurs_localises ? $deflt2docs_location : '');
		}
		parent::init_filters($filters);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'cb' => 'code_barre_empr',
						'empr_name' => 'nom_prenom_empr',
						'adr1' => 'adresse_empr',
						'ville' => 'ville_empr',
						'birth' => 'year_empr',
						'mail' => 'email',
						'aff_date_expiration' => 'readerlist_dateexpiration',
						'empr_statut_libelle' => 'statut_empr',
						'categ_libelle' => 'categ_empr',
						'categ_change' => 'empr_categ_change_prochain',
						'relance' => 'relance_imprime'
				)
		);
		
		$this->available_columns['custom_fields'] = array();
		$this->add_custom_fields_available_columns('empr', 'id');
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
		global $nb_per_page_empr;
		$this->pager = array(
				'page' => 1,
				'nb_per_page' => ($nb_per_page_empr ? $nb_per_page_empr : 10),
				'nb_results' => 0,
				'nb_page' => 1
		);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'empr_name',
				'asc_desc' => 'asc'
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
				case 'id':
					$order .= 'id_empr';
					break;
				case 'empr_name' :
					$order .= 'empr_nom, empr_prenom';
					break;
				case 'cb':
					$order .= 'empr_cb';
					break;
				case 'adr1':
					$order .= 'empr_adr1';
					break;
				case 'ville':
					$order .= 'empr_ville';
					break;
				case 'birth':
					$order .= 'empr_year';
					break;
				case 'mail':
					$order .= 'empr_mail';
					break;
				case 'aff_date_expiration':
					$order .= 'empr_date_expiration';
					break;
				case 'empr_statut_libelle':
					$order .= 'empr_statut.statut_libelle';
					break;
				case 'categ_libelle':
					$order .= 'empr_categ.libelle';
					break;
				default :
					$order .= parent::_get_query_order();
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
		global $empr_location_id;
		global $empr_statut_edit;
		global $empr_categ_filter;
		global $empr_codestat_filter;
		
		if(isset($empr_location_id)) {
			$this->filters['empr_location_id'] = $empr_location_id*1;
		}
		if(isset($empr_statut_edit)) {
			$this->filters['empr_statut_edit'] = $empr_statut_edit*1;
		}
		if(isset($empr_categ_filter)) {
			$this->filters['empr_categ_filter'] = $empr_categ_filter*1;
		}
		if(isset($empr_codestat_filter)) {
			$this->filters['empr_codestat_filter'] = $empr_codestat_filter*1;
		}
		$locations = $this->objects_type.'_locations';
		global ${$locations};
		if(isset(${$locations})) {
			$this->filters['locations'] = array();
			if(${$locations}[0] != '') {
				$this->filters['locations'] = stripslashes_array(${$locations});
			}
		}
		$categories = $this->objects_type.'_categories';
		global ${$categories};
		if(isset(${$categories})) {
			$this->filters['categories'] = array();
			if(${$categories}[0] != '') {
				$this->filters['categories'] = stripslashes_array(${$categories});
			}
		}
		$codestat = $this->objects_type.'_codestat';
		global ${$codestat};
		if(isset(${$codestat})) {
			$this->filters['codestat'] = array();
			if(${$codestat}[0] != '') {
				$this->filters['codestat'] = stripslashes_array(${$codestat});
			}
		}
		$groups = $this->objects_type.'_groups';
		global ${$groups};
		if(isset(${$groups})) {
			$this->filters['groups'] = array();
			if(${$groups}[0] != '') {
				$this->filters['groups'] = stripslashes_array(${$groups});
			}
		}
		$name = $this->objects_type.'_name';
		global ${$name};
		if(isset(${$name})) {
			$this->filters['name'] = stripslashes(${$name});
		}
		$mail = $this->objects_type.'_mail';
		global ${$mail};
		if(isset(${$mail})) {
			$this->filters['mail'] = stripslashes(${$mail});
		}
		$has_mail = $this->objects_type.'_has_mail';
		global ${$has_mail};
		if(isset(${$has_mail})) {
			$this->filters['has_mail'] = stripslashes(${$has_mail});
		}
		$has_affected = $this->objects_type.'_has_affected';
		global ${$has_affected};
		if(isset(${$has_affected})) {
			$this->filters['has_affected'] = stripslashes(${$has_affected});
		}
		$date_creation_start = $this->objects_type.'_date_creation_start';
		global ${$date_creation_start};
		if(isset(${$date_creation_start})) {
			$this->filters['date_creation_start'] = ${$date_creation_start};
		}
		$date_creation_end = $this->objects_type.'_date_creation_end';
		global ${$date_creation_end};
		if(isset(${$date_creation_end})) {
			$this->filters['date_creation_end'] = ${$date_creation_end};
		}
		$date_adhesion_start = $this->objects_type.'_date_adhesion_start';
		global ${$date_adhesion_start};
		if(isset(${$date_adhesion_start})) {
			$this->filters['date_adhesion_start'] = ${$date_adhesion_start};
		}
		$date_adhesion_end = $this->objects_type.'_date_adhesion_end';
		global ${$date_adhesion_end};
		if(isset(${$date_adhesion_end})) {
			$this->filters['date_adhesion_end'] = ${$date_adhesion_end};
		}
		$date_expiration_start = $this->objects_type.'_date_expiration_start';
		global ${$date_expiration_start};
		if(isset(${$date_expiration_start})) {
			$this->filters['date_expiration_start'] = ${$date_expiration_start};
		}
		$date_expiration_end = $this->objects_type.'_date_expiration_end';
		global ${$date_expiration_end};
		if(isset(${$date_expiration_end})) {
			$this->filters['date_expiration_end'] = ${$date_expiration_end};
		}
		parent::set_filters_from_form();
	}
	
	protected function get_selector_query($type) {
		$query = '';
		switch ($type) {
			case 'categories':
				$query = 'select id_categ_empr as id, libelle as label from empr_categ order by label';
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
		}
		return $query;
	}
	
	protected function get_search_filter_categorie() {
		return emprunteur::gen_combo_box_categ($this->filters['empr_categ_filter']);
	}
	
	protected function get_search_filter_categories() {
		global $msg;
		
		return $this->get_multiple_selector($this->get_selector_query('categories'), 'categories', $msg['dsi_all_categories']);
	}
	
	protected function get_search_filter_groups() {
		global $msg;
	
		return $this->get_multiple_selector($this->get_selector_query('groups'), 'groups', $msg['dsi_all_groups']);
	}
	
	protected function get_search_filter_location() {
		return docs_location::gen_combo_box_empr($this->filters['empr_location_id']);
	}
	
	protected function get_search_filter_locations() {
		global $msg;
	
		return $this->get_multiple_selector($this->get_selector_query('locations'), 'locations', $msg['all_location']);
	}
	
	protected function get_search_filter_codestat_one() {
		global $msg;
	
		return emprunteur::gen_combo_box_codestat($this->filters['empr_codestat_filter']);
	}
	
	protected function get_search_filter_codestat() {
		global $msg;
	
		return $this->get_multiple_selector($this->get_selector_query('codestat'), 'codestat', $msg['all_codestat_empr']);
	}
	
	protected function get_search_filter_status() {
		global $msg;
		
		return gen_liste("select idstatut, statut_libelle from empr_statut","idstatut","statut_libelle","empr_statut_edit","",$this->filters['empr_statut_edit'],-1,"",0,$msg["all_statuts_empr"]);
	}
	
	protected function get_search_filter_name() {
		global $charset;
		
		return "<input type='text' class='10em' name='".$this->objects_type."_name' value=\"".htmlentities($this->filters['name'], ENT_QUOTES, $charset)."\" onchange=\"this.form.submit();\" />";
	}
	
	protected function get_search_filter_has_mail() {
		global $msg, $charset;
	
		return "
			<input type='radio' id='".$this->objects_type."_has_mail_no' name='".$this->objects_type."_has_mail' value='0' ".(!$this->filters['has_mail'] ? "checked='checked'" : "")." onchange=\"this.form.submit();\" />
			<label for='".$this->objects_type."_has_mail_no'>".$msg['39']."</label>
			<input type='radio' id='".$this->objects_type."_has_mail_yes' name='".$this->objects_type."_has_mail' value='1' ".($this->filters['has_mail'] ? "checked='checked'" : "")." onchange=\"this.form.submit();\" />
			<label for='".$this->objects_type."_has_mail_yes'>".$msg['40']."</label>";
	}
	
	protected function get_search_filter_mail() {
		global $charset;
	
		return "<input type='text' class='30em' name='".$this->objects_type."_mail' value=\"".$this->filters['mail']."\" onchange=\"this.form.submit();\" />";
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
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		$this->is_displayed_options_block = true;
		$search_form = parent::get_search_form();
		$search_form = str_replace('!!action!!', static::get_controller_url_base(), $search_form);
		return $search_form;
	}
	
	protected function get_selection_actions() {
		global $msg;
		global $base_path;
		global $empr_show_caddie;
		global $sub;
	
		if(!isset($this->selection_actions)) {
			$this->selection_actions = array();
			if ($empr_show_caddie) {
				$link = array();
				$link['openPopUp'] = $base_path."/cart.php?object_type=EMPR&action=add_empr_".$sub;
				$link['openPopUpTitle'] = 'cart';
				$this->selection_actions[] = $this->get_selection_action('caddie', $msg['add_empr_cart'], 'basket_20x20.gif', $link);
			}
		}
		return $this->selection_actions;
	}
	
	/**
	 * Jointure externes SQL pour les besoins des filtres
	 */
	protected function _get_query_join_filters() {
		
		$filter_join_query = '';
		if(is_array($this->filters['groups']) && count($this->filters['groups'])) {
			$filter_join_query .= " LEFT JOIN empr_groupe ON empr.id_empr=empr_groupe.empr_id";
		}
		if($this->filters['has_affected']) {
			$filter_join_query .= $this->_get_query_join_filter_affected();
		}
		return $filter_join_query;
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		global $pmb_lecteurs_localises;
		
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if($this->filters['empr_location_id']) {
			$filters [] = 'empr_location = "'.$this->filters['empr_location_id'].'"';
		}
		if($this->filters['empr_statut_edit']) {
			$filters [] = 'empr_statut = "'.$this->filters['empr_statut_edit'].'"';
		}
		if($this->filters['empr_categ_filter']) {
			$filters [] = 'empr_categ = "'.$this->filters['empr_categ_filter'].'"';
		}
		if($this->filters['empr_codestat_filter']) {
			$filters [] = 'empr_codestat = "'.$this->filters['empr_codestat_filter'].'"';
		}
		if($pmb_lecteurs_localises && array_key_exists('locations', $this->filters) && is_array($this->filters['locations']) && count($this->filters['locations'])) {
			$filters [] = 'empr_location IN ('.implode(',', $this->filters['locations']).')';
		}
		if(is_array($this->filters['categories']) && count($this->filters['categories'])) {
			$filters [] = 'empr_categ IN ('.implode(',', $this->filters['categories']).')';
		}
		if(is_array($this->filters['codestat']) && count($this->filters['codestat'])) {
			$filters [] = 'empr_codestat IN ('.implode(',', $this->filters['codestat']).')';
		}
		if(is_array($this->filters['groups']) && count($this->filters['groups'])) {
			$filters [] = 'groupe_id IN ('.implode(',', $this->filters['groups']).')';
		}
		if($this->filters['name']) {
			$filters [] = 'empr_nom like "%'.str_replace('*', '%', $this->filters['name']).'%"';
		}
		if($this->filters['mail']) {
			$filters [] = 'empr_mail like "%'.str_replace('*', '%', $this->filters['mail']).'%"';
		}
		if($this->filters['has_mail']) {
			$filters [] = 'empr_mail <> ""';
		}
		if($this->filters['has_affected']) {
			$query_affected = $this->_get_query_filter_affected();
			if($query_affected) {
				$filters [] = $this->_get_query_filter_affected();
			}
		}
		if($this->filters['date_creation_start']) {
			$filters [] = 'empr_creation >= "'.$this->filters['date_creation_start'].'"';
		}
		if($this->filters['date_creation_end']) {
			$filters [] = 'empr_creation < "'.$this->filters['date_creation_end'].'"';
		}
		if($this->filters['date_adhesion_start']) {
			$filters [] = 'empr_date_adhesion >= "'.$this->filters['date_adhesion_start'].'"';
		}
		if($this->filters['date_adhesion_end']) {
			$filters [] = 'empr_date_adhesion < "'.$this->filters['date_adhesion_end'].'"';
		}
		if($this->filters['date_expiration_start']) {
			$filters [] = 'empr_date_expiration >= "'.$this->filters['date_expiration_start'].'"';
		}
		if($this->filters['date_expiration_end']) {
			$filters [] = 'empr_date_expiration < "'.$this->filters['date_expiration_end'].'"';
		}
		if($this->filters['date_expiration_limit']) {
			$filters [] = $this->filters['date_expiration_limit'];
		}
		if($this->filters['change_categ']) {
			$filters [] = $this->filters['change_categ'];
		}
		if(count($filters)) {
			$filter_query .= $this->_get_query_join_filters();
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
		
	/**
	 * Fonction de callback
	 * @param account $a
	 * @param account $b
	 */
	protected function _compare_objects($a, $b) {
		if($this->applied_sort['by']) {
			$sort_by = $this->applied_sort['by'];
			switch($sort_by) {
				case 'empr_name':
					return strcmp(emprunteur::get_name($a->id), emprunteur::get_name($b->id));
					break;
				case 'aff_date_expiration':
					return strcmp($a->date_expiration, $b->date_expiration);
					break;
				default :
					return parent::_compare_objects($a, $b);
					break;
			}
		}
	}
	
	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'empr', $display);
		$display = str_replace('!!sub!!', '', $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		
		$content = '';
		switch($property) {
			case 'cb':
				$content .= "<b>".$object->{$property}."</b>";
				break;
			case 'empr_name':
				$content .= $object->nom." ".$object->prenom;
				break;
			case 'categ_libelle':
				$content.= $object->cat_l;
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
				$content .= "<a href=\"#\" ".$action_relance_courrier."><img src=\"".get_url_icon('new.gif')."\" title=\"".$msg["param_pdflettreadhesion"]."\" alt=\"".$msg["param_pdflettreadhesion"]."\" border=\"0\"></a>";
				if ($object->mail) {
					$mail_click = "onclick=\"if (confirm('".$msg["mail_retard_confirm"]."')) {openPopUp('./mail.php?type_mail=mail_relance_adhesion&id_empr=".$object->id."', 'mail');} return(false) \"";
					$content .= "&nbsp;<a href=\"#\" ".$mail_click."><img src=\"".get_url_icon('mail.png')."\" title=\"".$msg["param_mailrelanceadhesion"]."\" alt=\"".$msg["param_mailrelanceadhesion"]."\" border=\"0\"></a>";
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function _get_query_human() {
		global $msg, $charset;
		global $pmb_lecteurs_localises;
		
		$humans = array();
		if($this->filters['empr_location_id']) {
			$docs_location = new docs_location($this->filters['empr_location_id']);
			$humans[] = $this->_get_label_query_human($msg['editions_filter_empr_location'], $docs_location->libelle);
		}
		if($this->filters['empr_statut_edit']) {
			$query = "select statut_libelle from empr_statut where idstatut = ".$this->filters['empr_statut_edit'];
			$humans[] = $this->_get_label_query_human_from_query($msg['statut_empr'], $query);
		}
		if($this->filters['empr_categ_filter']) {
			$query = "select libelle from empr_categ where id_categ_empr = ".$this->filters['empr_categ_filter'];
			$humans[] = $this->_get_label_query_human_from_query($msg['editions_filter_empr_categ'], $query);
		}
		if($this->filters['empr_codestat_filter']) {
			$query = "select libelle from empr_codestat where idcode = ".$this->filters['empr_codestat_filter'];
			$humans[] = $this->_get_label_query_human_from_query($msg['editions_filter_empr_codestat'], $query);
		}
		if($pmb_lecteurs_localises && array_key_exists('locations', $this->filters) && is_array($this->filters['locations']) && count($this->filters['locations'])) {
			$query = "select location_libelle from docs_location where idlocation IN (".implode(',', $this->filters['locations']).")";
			$humans[] = $this->_get_label_query_human_from_query($msg['editions_filter_empr_location'], $query);
		}
		if(is_array($this->filters['categories']) && count($this->filters['categories'])) {
			$query = "select libelle from empr_categ where id_categ_empr IN (".implode(',', $this->filters['categories']).")";
			$humans[] = $this->_get_label_query_human_from_query($msg['editions_filter_empr_categ'], $query);
		}
		if(is_array($this->filters['codestat']) && count($this->filters['codestat'])) {
			$query = "select libelle from empr_codestat where idcode IN (".implode(',', $this->filters['codestat']).")";
			$humans[] = $this->_get_label_query_human_from_query($msg['editions_filter_empr_codestat'], $query);
		}
		if(is_array($this->filters['groups']) && count($this->filters['groups'])) {
			$query = "select libelle_groupe from groupe where id_groupe IN (".implode(',', $this->filters['groups']).")";
			$humans[] = $this->_get_label_query_human_from_query($msg['903'], $query);
		}
		if($this->filters['name']) {
			$humans[] = $this->_get_label_query_human($msg['dsi_ban_abo_empr_nom'], $this->filters['name']);
		}
		if($this->filters['mail']) {
			$humans[] = $this->_get_label_query_human($msg['email'], $this->filters['mail']);
		}
		if($this->filters['has_mail']) {
			$humans[] = $this->_get_label_query_human($msg['dsi_ban_abo_mail'], $msg['40']);
		}
		if($this->filters['has_affected']) {
			$humans[] = $this->_get_label_query_human($msg['dsi_ban_lecteurs_affectes'], $msg['40']);
		}
		if($this->filters['date_creation_start']) {
			$humans[] = $this->_get_label_query_human($msg['date_creation_query']." - ".$msg['list_ui_filter_date_start'], formatdate($this->filters['date_creation_start']));
		}
		if($this->filters['date_creation_end']) {
			$humans[] = $this->_get_label_query_human($msg['date_creation_query']." - ".$msg['list_ui_filter_date_end'], formatdate($this->filters['date_creation_end']));
		}
		if($this->filters['date_adhesion_start']) {
			$humans[] = $this->_get_label_query_human($msg['empr_date_adhesion']." - ".$msg['list_ui_filter_date_start'], formatdate($this->filters['date_adhesion_start']));
		}
		if($this->filters['date_adhesion_end']) {
			$humans[] = $this->_get_label_query_human($msg['empr_date_adhesion']." - ".$msg['list_ui_filter_date_end'], formatdate($this->filters['date_adhesion_end']));
		}
		if($this->filters['date_expiration_start']) {
			$humans[] = $this->_get_label_query_human($msg['readerlist_dateexpiration']." - ".$msg['list_ui_filter_date_start'], formatdate($this->filters['date_expiration_start']));
		}
		if($this->filters['date_expiration_end']) {
			$humans[] = $this->_get_label_query_human($msg['readerlist_dateexpiration']." - ".$msg['list_ui_filter_date_end'], formatdate($this->filters['date_expiration_end']));
		}
		return $this->get_display_query_human($humans);
	}
}