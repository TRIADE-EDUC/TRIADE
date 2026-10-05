<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: records_tabs.class.php,v 1.13 2022/01/04 09:23:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/elements_list_tab.class.php');
require_once($class_path.'/skos/skos_concept.class.php');
require_once($class_path.'/indexation.class.php');
require_once($class_path.'/onto/common/onto_common_uri.class.php');
require_once($class_path.'/filter_results.class.php');
require_once($class_path.'/entity_graph.class.php');

class records_tabs {
	
	/**
	 * Instance de la classe notice associée
	 * @var notice
	 */
	protected $record;
	
	/**
	 * Tableau des onglets de la notice
	 * @var elements_list_tab Tableau des onglets
	 */
	protected $tabs;
	
	/**
	 * Constructeur
	 * @param notice $notice Instance de la classe notice associée
	 */
	public function __construct($record){
		$this->record = $record;
		$this->tabs = array();
		$this->add_tab($this->get_tab_records_indexed_with_concept());
		$this->add_tab($this->get_tab_authorities_indexed_with_concept());
		$this->add_tab($this->get_tab_entities_graphed());
		$this->record->set_record_tabs($this);
		$this->record->get_records_list_ui();
	}
	
	/**
	 * Retourne le tableau des onglets
	 * @return elements_list_tab Tableau d'elements_list_tab
	 */
	public function get_tabs(){
		return $this->tabs;
	}
	
	/**
	 * Ajoute un onglet au tableau
	 * @param elements_list_tab $tab Onglet à ajouter
	 */
	protected function add_tab($tab) {
		if ($tab) {
			$this->tabs[] = $tab;
		}
	}
	
	/**
	 * Retourne l'onglet contenant les notices associées à l'autorité
	 * (à dériver)
	 * @return elements_list_tab Onglet
	 */
	protected function get_tab_records(){
		return null;
	}
	
	/**
	 * Méthode permettant de récupérer les ids des notices indexées avec un concept utilisant cette notice
	 * @return elements_list_tab Onglet
	 */
	protected function get_tab_records_indexed_with_concept(){
		global $msg;
		
		$concept_ids = array();
		$tab = new elements_list_tab('records_records_indexed', $msg['record_tabs_records_indexed'], 'records');
		$vedette_composee_found = vedette_composee::get_vedettes_built_with_element($this->record->id, TYPE_NOTICE);
		pmb_mysql_query('set session group_concat_max_len = 16777216');
		if(count($vedette_composee_found)){
			foreach($vedette_composee_found as $vedette_id){
				$concept_ids[] = vedette_composee::get_object_id_from_vedette_id($vedette_id, TYPE_CONCEPT_PREFLABEL);
			}
			$nb_results = 0;
			$query = 'select group_concat(distinct notice_id) from notices join index_concept on index_concept.num_object = notices.notice_id and index_concept.type_object = "'.TYPE_NOTICE.'" where index_concept.num_concept in ('.implode(',', $concept_ids).') ';
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$notices_ids = pmb_mysql_result($result, 0, 0);
				if ($notices_ids) {
					$nb_results = (substr_count($this->get_filtered_results($notices_ids),",") + 1);
				}
			}
			
			$tab->set_nb_results($nb_results);
			
			if ($nb_results) {
				$query = 'select group_concat(distinct notice_id separator ",") from notices join index_concept on index_concept.num_object = notices.notice_id and index_concept.type_object = "'.TYPE_NOTICE.'" where index_concept.num_concept in ('.implode(',', $concept_ids).') ';
				$query.= $this->get_records_sort();
				// on lance la requête
				$result = pmb_mysql_query($query);
				$records_ids = array();
				if($result && pmb_mysql_num_rows($result)){
					$elements_ids = $this->get_filtered_results(pmb_mysql_result($result, 0,0));
					$records_ids = explode(',', $elements_ids);
					$records_ids = $this->get_limit_array($records_ids);
				}
				$tab->set_contents($records_ids);
			}
		}
		return $tab;
	}
	
	/**
	 * Méthode permettant de récupérer les autorités indexées avec un concept utilisant cette notice
	 * @return elements_list_tab Onglet
	 */
	protected function get_tab_authorities_indexed_with_concept(){
		global $msg;
		
		$concept_ids = array();
		$tab = new elements_list_tab('records_authorities_indexed', $msg['record_tabs_authorities_indexed'], 'authorities');
		$types_needed = array(TYPE_AUTHOR, TYPE_CATEGORY, TYPE_PUBLISHER, TYPE_COLLECTION, TYPE_SUBCOLLECTION, TYPE_SERIE, TYPE_TITRE_UNIFORME, TYPE_INDEXINT, TYPE_AUTHPERSO);
		$vedette_composee_found = vedette_composee::get_vedettes_built_with_element($this->record->id, TYPE_NOTICE);
		pmb_mysql_query('set session group_concat_max_len = 16777216');
		if(count($vedette_composee_found)){
			foreach($vedette_composee_found as $vedette_id){
				$concept_ids[] = vedette_composee::get_object_id_from_vedette_id($vedette_id, TYPE_CONCEPT_PREFLABEL);
			}
			$query = 'select count(distinct num_object, type_object) from index_concept where num_concept in ('.implode(',', $concept_ids).') and type_object in ('.implode(',', $types_needed).')';
			$nb_results = pmb_mysql_result(pmb_mysql_query($query), 0, 0);
			
			$tab->set_nb_results($nb_results);
			
			if ($nb_results) {
				// On définit les filtres
				$filter = array(
						'name' => 'records_authorities_indexed_by_types',
						'label' => $msg['authority_tabs_common_indexed_authorities_by_types']
				);
				$tab->set_filters(array($filter));
				$groups = array();
				$query = 'select count(distinct num_object) as nb, type_object, id_authperso, authperso_name from index_concept left join authperso_authorities on num_object = id_authperso_authority and type_object = '.TYPE_AUTHPERSO.' left join authperso on id_authperso = authperso_authority_authperso_num where num_concept in ('.implode(',', $concept_ids).') and type_object in ('.implode(',', $types_needed).') group by type_object, id_authperso';
				$result = pmb_mysql_query($query);
				if ($result && pmb_mysql_num_rows($result)) {
					while ($row = pmb_mysql_fetch_object($result)) {
						if (($row->type_object == TYPE_AUTHPERSO) && !isset($groups[1000 + $row->id_authperso])) {
							$groups[1000 + $row->id_authperso] = array(
									'label' => $row->authperso_name,
									'nb_results' => $row->nb
							);
						} else if (!isset($groups[$row->type_object])){
							$groups[$row->type_object] = array(
									'label' => authority::get_type_label_from_type_id(index_concept::get_aut_table_type_from_type($row->type_object)),
									'nb_results' => $row->nb
							);
						}
						$nb_results+= $row->nb;
					}
				}
				if (count($groups)) {
					// On trie le tableau
					uasort($groups, array($this, '_sort_groups_by_label'));
					$tab->add_groups($filter['name'], array(
							'label' => $filter['label'],
							'elements' => $groups
					));
					$filter_values = $tab->get_filter_values($filter['name']);

					$authpersos_needed = array();
					if ($filter_values && count($filter_values)) {
						$types_needed = array();
						foreach ($filter_values as $value) {
							if ($value > 1000) {
								if (!in_array(TYPE_AUTHPERSO, $types_needed)) {
									$types_needed[] = TYPE_AUTHPERSO;
								}
								$authpersos_needed[] = $value - 1000;
							} else {
								$types_needed[] = $value;
							}
						}
						
					}
					$query = 'select SQL_CALC_FOUND_ROWS num_object, type_object, authperso_authority_authperso_num';
					$query.= ' from index_concept left join authperso_authorities on num_object = id_authperso_authority and type_object = '.TYPE_AUTHPERSO;
					$query.= ' where num_concept in ('.implode(',', $concept_ids).') and type_object in ('.implode(',', $types_needed).')';
					// si on a des filtres sur des authorités persos
					if (count($authpersos_needed)) {
						$query.= ' and (authperso_authority_authperso_num is null or authperso_authority_authperso_num in ('.implode(',', $authpersos_needed).'))';
					}
					$query.= $this->get_authorities_sort();
					$query.= $this->get_limit();
					// on lance la requête
					$result = pmb_mysql_query($query);
					$records_ids = array();
					if($result && pmb_mysql_num_rows($result)){
						while($row = pmb_mysql_fetch_object($result)){
							$authority = new authority(0, $row->num_object, index_concept::get_aut_table_type_from_type($row->type_object));
							$records_ids[] = $authority->get_id();
						}
					}
					$nb_filtered_results = pmb_mysql_result(pmb_mysql_query('select FOUND_ROWS()'), 0, 0);
					$tab->set_nb_filtered_results($nb_filtered_results);
					$tab->set_contents($records_ids);
				}
			}
		}
		return $tab;
	}
	
	/**
	 * Retourne la portion de requête pour le tri des notices
	 * @param string $sort Tri à appliquer
	 * @return string Portion de la requête 
	 */
	protected function get_records_sort($sort=''){
		switch($sort){
			default:
				return ' order by notices.index_sew ';
		}
	}
	
	/**
	 * Retourne la portion de requête pour le tri des autorités
	 * @param string $sort Tri à appliquer
	 * @return string Portion de la requête
	 */
	protected function get_authorities_sort($sort=''){
		switch($sort){
			default:
				return '';
		}
	}
	
	/**
	 * Retourne la portion de requête pour la limite des résultats
	 * @return string Portion de la requête
	 */
	protected function get_limit(){
		global $pmb_nb_elems_per_tab;
		global $tab_nb_per_page;
		global $tab_page;
		if(!$tab_nb_per_page){
			$tab_nb_per_page = $pmb_nb_elems_per_tab;
		}
		if($tab_page){
			return ' limit '.(($tab_page-1) * ($tab_nb_per_page*1)).', '.($tab_nb_per_page*1).' ';
		}
		return ' limit '.($tab_nb_per_page*1).' ';
	}
	
	protected function _sort_groups_by_label($a, $b) {
		if (strtolower($a['label']) == strtolower($b['label'])) {
			return 0;
		}
		return (strtolower($a['label']) < strtolower($b['label'])) ? -1 : 1;
	}
	
	public function get_record(){
		return $this->record;
	}
	
	protected static function get_filtered_results($records_ids){
		$filter_results = new filter_results($records_ids);
		$filtered_result = $filter_results->get_results();
		if($filtered_result){
			return $filtered_result;
		}
		return "";
	}
	
	/**
	 * Retourne la portion de requête pour la limite des résultats
	 * @return string Portion de la requête
	 */
	protected static function get_limit_array($records_ids){
		global $pmb_nb_elems_per_tab;
		global $tab_page;
		global $tab_nb_per_page;
		
		if (!$tab_nb_per_page) {
			$tab_nb_per_page = $pmb_nb_elems_per_tab;
		}
		if($tab_page){
			return array_slice($records_ids, (($tab_page-1) * ($tab_nb_per_page*1)), ($tab_nb_per_page*1));
		}
		return array_slice($records_ids, 0,($tab_nb_per_page*1));
	}
	
	protected function get_tab_entities_graphed(){
	    global $msg, $quoi;
		global $pmb_entity_graph_recursion_lvl;
		global $pmb_entity_graph_activate;
		
		$tab = new elements_list_tab('records_entities_graphed', $msg['authority_tabs_entities_graphed'], 'graph');
		$tab->set_nb_results(0);

		if($pmb_entity_graph_activate && $quoi == $tab->get_name()) {
			$entity_graph = entity_graph::get_entity_graph($this->record, 'record');
			$entity_graph->get_recursive_graph($pmb_entity_graph_recursion_lvl);
			$tab->set_contents($entity_graph->get_json_entities_graphed());
		}
		return $tab;
	}
	
}