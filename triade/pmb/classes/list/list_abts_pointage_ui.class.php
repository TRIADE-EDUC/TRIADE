<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_abts_pointage_ui.class.php,v 1.21 2023/12/27 08:13:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/abts_pointage.class.php');

class list_abts_pointage_ui extends list_ui {
	
	protected $cpt_prochain_numero = 0;
	protected $cpt_a_recevoir = 0;
	protected $cpt_en_retard = 0;
	protected $cpt_en_alerte = 0;
	
	protected function _get_query_base() {
		return 'SELECT id_bull,num_abt,abts_grille_abt.date_parution,modele_id,type,numero,nombre,ordre,state,fournisseur,abt_name,num_notice,location_id,tit1,index_sew,date_debut, date_fin,cote
			FROM abts_grille_abt
			JOIN abts_abts ON abt_id=num_abt
			JOIN notices ON notice_id=num_notice';
	}
	
	protected function fetch_data() {
		$this->objects = array();
		$query = $this->_get_query();
		$abts_pointage = new abts_pointage();
		$liste_bulletin = $abts_pointage->get_bulletinage_from_query($query);
		
		if($liste_bulletin){
			//Tri par type de retard
			asort($liste_bulletin);
			foreach($liste_bulletin as $retard => $bulletin_retard){
				foreach($bulletin_retard as $id_bull => $fiche){
					$fiche['retard'] = $retard;
					$fiche['id_bull'] = $id_bull;
					$this->add_object((object) $fiche);
					switch ($retard) {
						case 3:
							$this->cpt_prochain_numero++;
							break;
						case 1:
							$this->cpt_en_retard++;
							break;
						case 2:
							$this->cpt_en_alerte++;
							break;
						case 0:
							$this->cpt_a_recevoir++;
							break;
					}
				}
			}
		}
		$this->pager['nb_results'] = count($this->objects);
		$this->messages = "";
	}
	
	protected function get_form_title() {
		global $msg, $charset;
		
		return htmlentities($msg["4000"].":".$msg["pointage_libelle_form"], ENT_QUOTES, $charset);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'location' => 'pointage_label_localisation',
						'abts_statut' => 'abts_statut',
						'serials' => 'serials_query',
						'date_parution' => 'pointage_label_date'
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $deflt_bulletinage_location;
		
		$this->filters = array(
				'location' => $deflt_bulletinage_location,
				'abts_statut' => 0,
				'serials' => array(),
				'date_parution_start' => '',
				'date_parution_end' => ''
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('location');
		$this->add_selected_filter('abts_statut');
		$this->add_selected_filter('serials');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
					'date_parution' => 'pointage_label_date',
					'periodique' => 'pointage_label_notice',
					'libelle_numero' => 'pointage_label_numero',
					'libelle_abonnement' => 'pointage_label_abonnement',
					'a_recevoir' => 'pointage_label_a_recevoir',
					'recu' => 'pointage_label_recu',
					'supprimer_et_conserver' => 'pointage_label_supprimer_et_conserver',
					'bulletin_mention_periode' => 'bulletin_mention_periode',
					'bulletin_see' => 'pointage_voir_le_bulletin'
			)
		);
	}
	
	public function init_applied_group($applied_group=array()) {
		$this->applied_group = array(0 => 'retard');
	}
	
	protected function get_grouped_objects() {
		global $msg;
		
		$this->grouped_objects = parent::get_grouped_objects();
		//Conserver l'ordre logique
		$tempo_grouped_objects = array();
		$order_indexes_labels = array(
				$msg['pointage_label_a_recevoir'], //A recevoir ($retard==0)
				$msg['pointage_label_prochain_numero'], //Prochains numéros ($retard==3)
				$msg['pointage_label_en_retard'], //En retard ($retard==1)
				$msg['pointage_label_depasse'] //En alerte ($retard==2)
		);
		foreach ($order_indexes_labels as $index_label) {
			if(!empty($this->grouped_objects[$index_label])) {
				$tempo_grouped_objects[$index_label] = $this->grouped_objects[$index_label];
			}
		}
		$this->grouped_objects = $tempo_grouped_objects;
		return $this->grouped_objects;
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('date_parution');
	}
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
		
	    if($this->applied_sort[0]['by']) {
	    	//On force le tri SQL suivant pour le calcul des numéros
	    	$order = 'order by date_parution,tit1,ordre,abt_name';
			$sort_by = $this->applied_sort[0]['by'];
			switch($sort_by) {
				case 'date_parution':
					if($this->applied_sort[0]['asc_desc'] == 'desc') {
						$this->applied_sort_type = 'OBJECTS';
					} else {
						$this->applied_sort_type = 'SQL';
					}
					break;
				default :
					$this->applied_sort_type = 'OBJECTS';
					break;
			}
			return $order;
		}	
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('location', 'integer');
		$this->set_filter_from_form('abts_statut', 'integer');
		$this->set_filter_from_form('serials', 'integer');
		$this->set_filter_from_form('date_parution_start');
		$this->set_filter_from_form('date_parution_end');
		parent::set_filters_from_form();
	}
	
	protected function init_default_columns() {
		$this->add_column('date_parution');
		$this->add_column('periodique');
		$this->add_column('libelle_numero');
		$this->add_column('libelle_abonnement');
		$this->add_column('a_recevoir');
		$this->add_column('recu');
		$this->add_column('supprimer_et_conserver');
		$this->add_column('bulletin_see');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('search_form', 'unfolded_filters', true);
		$this->set_setting_display('grouped_objects', 'sort', false);
		$this->set_setting_display('grouped_objects', 'display_counter', true);
		$this->set_setting_display('pager', 'visible', false);
		$this->set_setting_filter('serials', 'selection_type', 'selector');
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('date_parution', 'datatype', 'date');
		$this->set_setting_column('date_parution', 'text', array('bold' => true));
		$this->settings['objects']['default']['display_mode'] = 'expandable_table';
		$this->settings['grouped_objects']['level_1']['display_mode'] = 'expandable_table';
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'libelle_numero', 
				'a_recevoir', 'recu', 'supprimer_et_conserver',
				'bulletin_mention_periode', 'bulletin_see'
		);
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	protected function get_selection_query($type) {
		$query = '';
		switch ($type) {
			case 'locations':
				$query = 'select idlocation as id, location_libelle as label from docs_location order by label';
				break;
			case 'abts_status':
				$statuses = abts_status::get_ids_bulletinage_active();
				$query = 'select abts_status_id as id, abts_status_gestion_libelle as label from abts_status where abts_status_id IN ('.implode(',', $statuses).') order by label';
				break;
			case 'serials':
				$query = 'SELECT distinct notice_id as id, tit1 as label FROM notices JOIN abts_abts ON num_notice = notice_id ORDER BY label';
				break;
		}
		return $query;
	}
	
	protected function get_search_filter_location() {
		global $msg;
		return $this->get_search_filter_simple_selection($this->get_selection_query('locations'), 'location', $msg["all_location"]);
	}
	
	protected function get_search_filter_abts_statut() {
		global $msg;
		return $this->get_search_filter_simple_selection($this->get_selection_query('abts_status'), 'abts_statut', $msg["all"]);
	}
	
	protected function get_search_filter_serials() {
		global $msg;
		return $this->get_search_filter_multiple_selection($this->get_selection_query('serials'), 'serials', $msg["all"]);
	}
	
	protected function get_search_filter_date_parution() {
		return $this->get_search_filter_interval_date('date_parution');
	}
	
	/**
	 * Affichage d'une colonne avec du HTML non calculé
	 * @param string $value
	 */
	protected function get_display_cell_html_value($object, $value) {
		$value = str_replace('!!id!!', $object->id_bull, $value);
		$display = $this->get_display_format_cell($value);
		return $display;
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('location', 'location_id', 'integer');
		if(!empty($this->filters['abts_statut'])) {
			$this->query_filters [] = 'abt_status IN ('.$this->filters['abts_statut'].')';
		} else {
			$abts_status_ids = abts_status::get_ids_bulletinage_active();
			if(count($abts_status_ids)) {
				$this->query_filters [] = 'abt_status IN ('.implode(',', $abts_status_ids).')';
			}
		}
		$this->_add_query_filter_multiple_restriction('serials', 'notice_id', 'integer');
		$this->_add_query_filter_interval_restriction('date_parution', 'abts_grille_abt.date_parution', 'date');
	}
	
	protected function _compare_objects($a, $b, $index=0) {
	    $sort_by = $this->applied_sort[$index]['by'];
		switch($sort_by) {
			case 'date_parution':
				return strcmp($a->date_parution, $b->date_parution);
			default :
			    return parent::_compare_objects($a, $b, $index);
		}
	}
	
	protected function _get_query_human_location() {
		$docs_location = new docs_location($this->filters['location']);
		return $docs_location->libelle;
	}
	
	protected function _get_query_human_abts_statut() {
		if($this->filters['abts_statut']) {
			$abts_status = new abts_status($this->filters['abts_statut']);
			return $abts_status->gestion_libelle;
		}
		return '';
	}
	
	protected function _get_query_human_serials() {
		if(!empty($this->filters['serials'])) {
			$labels = array();
			foreach ($this->filters['serials'] as $serial_id) {
				$labels[] = notice::get_notice_title($serial_id);
			}
			return implode(', ', $labels);
		}
	}
	
	protected function _get_query_human_date_parution() {
		return $this->_get_query_human_interval_date('date_parution');
	}
	
	protected function _get_object_property_retard($object) {
		global $msg;
		
		switch ($object->retard) {
			case 3:
				return $msg["pointage_label_prochain_numero"];
			case 1:
				return $msg["pointage_label_en_retard"];
			case 2:
				return $msg["pointage_label_depasse"];
			case 0:
				return $msg["pointage_label_a_recevoir"];
		}
	}
	
	protected function _get_object_property_bulletin_mention_periode($object) {
		//Préparation nouveau bulletin
		$requete = "SELECT * FROM abts_grille_abt WHERE id_bull='".$object->id_bull."'";
		$abtsQuery = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($abtsQuery)) {
			$abts = pmb_mysql_fetch_object($abtsQuery);
			$modele_id = $abts->modele_id;
			$abt_id = $abts->num_abt;
			$date_parution = $abts->date_parution;
		}
		$requete = "SELECT * FROM abts_abts WHERE abt_id='$abt_id'";
		$abtsQuery = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($abtsQuery)) {
			$abts = pmb_mysql_fetch_object($abtsQuery);
			$date_debut = $abts->date_debut;
			$date_fin = $abts->date_fin;
			
		}
		
		$requete = "SELECT num_notice,format_periode FROM abts_modeles WHERE modele_id='$modele_id'";
		$abtsQuery = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($abtsQuery)) {
			$abts = pmb_mysql_fetch_object($abtsQuery);
			$format_periode = $abts->format_periode;
		}
		
		//Genération du libellé de période
		$print_format=parse_format::get_instance();
		$print_format->var_format['DATE'] = $date_parution;
		$print_format->var_format['NUM'] = $object->NUM;
		$print_format->var_format['VOL'] = $object->VOL;
		$print_format->var_format['TOM'] = $object->TOM;
		$print_format->var_format['START_DATE'] = $date_debut;
		$print_format->var_format['END_DATE'] = $date_fin;
		
		$requete = "SELECT * FROM abts_abts_modeles WHERE modele_id='$modele_id' and abt_id='$abt_id' ";
		$abtsabtsQuery = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($abtsabtsQuery)) {
			$abtsabts = pmb_mysql_fetch_object($abtsabtsQuery);
			$print_format->var_format['START_NUM'] = $abtsabts->num;
			$print_format->var_format['START_VOL'] = $abtsabts->vol;
			$print_format->var_format['START_TOM'] = $abtsabts->tome;
		}
		
		$print_format->cmd = $format_periode;
		return $print_format->exec_cmd();
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		
		$content = '';
		switch($property) {
			case 'periodique':
				$content .= $object->periodique; //conservation de l'interprétation du HTML
				break;
			case 'a_recevoir':
				$content .= "<input type='radio' name='".$object->id_bull."' id='".$object->id_bull."_1' checked='checked'  value='1' title='".htmlentities($msg['pointage_label_a_recevoir'], ENT_QUOTES, $charset)."' />";
				break;
			case 'recu':
				$content .= "<input name='".$object->id_bull."' id='".$object->id_bull."_2' value='2' nume='".$object->NUM."' vol='".$object->VOL."' tom='".$object->TOM."' num='". htmlentities($object->libelle_numero,ENT_QUOTES, $charset)."'  type='radio' ".$object->link_recu." title='".htmlentities($msg['pointage_label_recu'], ENT_QUOTES, $charset)."' />";
				break;
			case 'supprimer_et_conserver':
				$content .= "<input name='".$object->id_bull."' id='".$object->id_bull."_3' value='3' type='radio' ".$object->link_non_recevable." title='".htmlentities($msg['pointage_label_supprimer_et_conserver'], ENT_QUOTES, $charset)."' /><span id='".$object->id_bull."_3_action_response'></span>";
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		$attributes = array();
		switch($property) {
			case 'bulletin_see':
				$attributes['id'] = $object->id_bull."_bul";
				break;
		}
		return $attributes;
	}
	
	public function get_cpt_prochain_numero() {
		return $this->cpt_prochain_numero;
	}
	
	public function get_cpt_a_recevoir() {
		return $this->cpt_a_recevoir;
	}
	
	public function get_cpt_en_retard() {
		return $this->cpt_en_retard;
	}
	
	public function get_cpt_en_alerte() {
		return $this->cpt_en_alerte;
	}
}