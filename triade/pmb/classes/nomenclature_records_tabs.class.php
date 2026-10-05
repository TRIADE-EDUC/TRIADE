<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_records_tabs.class.php,v 1.11 2023/05/05 13:38:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/elements_list_tab.class.php');
require_once($class_path.'/skos/skos_concept.class.php');
require_once($class_path.'/indexation.class.php');
require_once($class_path.'/onto/common/onto_common_uri.class.php');
require_once($class_path."/notice_relations_collection.class.php");

class nomenclature_records_tabs extends records_tabs {
	
	/**
	 * Tableau des ids de notice pour lesquelles il faut chercher les documents numériques
	 * @var array
	 */
	protected $manifs_ids;
	
	/**
	 * Constructeur
	 * @param notice $notice Instance de la classe notice associée
	 */
	public function __construct($record){
		$this->record = $record;
		$this->tabs = array();
		$this->get_manifs_ids();
		$this->add_tab($this->get_tab_records_indexed_with_concept());
		$this->add_tab($this->get_tab_authorities_indexed_with_concept());
		$this->add_tab($this->get_tab_submanifestations_docnums());
		$this->record->set_record_tabs($this);
		$this->record->get_records_list_ui();
	}
	
	protected function get_manifs_ids() {
		global $pmb_nomenclature_record_children_link;
		
		if (is_array($this->manifs_ids) && count($this->manifs_ids)) {
			return $this->manifs_ids;
		}
		$this->manifs_ids = array($this->record->id);
		$notice_relations = notice_relations_collection::get_object_instance($this->record->id);
		$childs = $notice_relations->get_childs();
		if(isset($childs[$pmb_nomenclature_record_children_link])) {
			foreach ($childs[$pmb_nomenclature_record_children_link] as $child) {
				$this->manifs_ids[] = $child->get_linked_notice();
			}
		}
		return $this->manifs_ids;
	}
	
	/**
	 * Méthode permettant de récupérer les documents numériques des sous-manifs de cette manifestation
	 * @return elements_list_tab Onglet
	 */
	protected function get_tab_submanifestations_docnums(){
		global $msg, $pmb_nomenclature_activate, $pmb_nomenclature_music_concept_blank, $quoi;

		if (!$pmb_nomenclature_activate || !$pmb_nomenclature_music_concept_blank || !$this->record->get_nomenclature_record_formations() || !count($this->record->get_nomenclature_record_formations()->get_record_formations())) {
			// Si on n'a pas de nomenclature associée, on s'arrète là
			return null;
		}
		if($pmb_nomenclature_music_concept_blank){
			$concept_blank_id = onto_common_uri::get_id($pmb_nomenclature_music_concept_blank);
			if($concept_blank_id){
				$concept_ids = array($concept_blank_id);
			}
		}
		
		$tab = new elements_list_tab('records_submanifestations_docnums', $msg['record_tabs_submanifestations_docnums'], 'docnums');

		$vedette_composee_found = vedette_composee::get_vedettes_built_with_element($concept_blank_id, TYPE_CONCEPT);
		if(count($vedette_composee_found)){
			foreach($vedette_composee_found as $vedette_id){
				$concept_ids[] = vedette_composee::get_object_id_from_vedette_id($vedette_id, TYPE_CONCEPT_PREFLABEL);
			}
		}
		$nb_results = 0;
		if(count($concept_ids)){
			$query = 'select count(distinct num_object, type_object) from index_concept join explnum on index_concept.num_object = explnum.explnum_id and type_object = '.TYPE_EXPLNUM.'
				where explnum.explnum_notice in ('.implode(',', $this->get_manifs_ids()).') and num_concept in ('.implode(',', $concept_ids).') and type_object = '.TYPE_EXPLNUM;
			$nb_results = pmb_mysql_result(pmb_mysql_query($query), 0, 0);
		}
		$tab->set_nb_results($nb_results);
		
		if (empty($quoi) && $nb_results) {
			// Si $quoi n'est pas valorisé et qu'on a des résultats, on valorise $quoi avec cet onglet
			$quoi = $tab->get_name();
		}
		$elements_ids = array();
		if ($nb_results && ($quoi == $tab->get_name())) {
			// On définit les filtres
			$filters = array(
					array(
							'name' => 'records_submanifestations_docnums_by_formation',
							'label' => $msg['records_tabs_submanifestations_docnums_by_formation']
					),
					array(
							'name' => 'records_submanifestations_docnums_by_family',
							'label' => $msg['records_tabs_submanifestations_docnums_by_family']
					),
					array(
							'name' => 'records_submanifestations_docnums_by_musicstand',
							'label' => $msg['records_tabs_submanifestations_docnums_by_musicstand']
					),
					array(
							'name' => 'records_submanifestations_docnums_by_workshop',
							'label' => $msg['records_tabs_submanifestations_docnums_by_workshop']
					),
					array(
							'name' => 'records_submanifestations_docnums_by_instrument',
							'label' => $msg['records_tabs_submanifestations_docnums_by_instrument']
					),
					array(
							'name' => 'records_submanifestations_docnums_by_voice',
							'label' => $msg['records_tabs_submanifestations_docnums_by_voice']
					)
			);
			$tab->set_filters($filters);
			$filtered_elements = $this->get_submanifestations_docnums_filters($tab, $concept_ids);
			
			if (!$tab->has_filters_values() || ($tab->has_filters_values() && count($filtered_elements))) {
				$query = 'select SQL_CALC_FOUND_ROWS num_object';
				$query.= ' from index_concept 
						join explnum on index_concept.num_object = explnum.explnum_id and type_object = '.TYPE_EXPLNUM.'
						left join nomenclature_children_records on explnum.explnum_notice = nomenclature_children_records.child_record_num_record
						left join nomenclature_notices_nomenclatures on nomenclature_children_records.child_record_num_nomenclature = nomenclature_notices_nomenclatures.id_notice_nomenclature
						left join nomenclature_musicstands on nomenclature_children_records.child_record_num_musicstand = nomenclature_musicstands.id_musicstand
						left join nomenclature_families on nomenclature_musicstands.musicstand_famille_num = nomenclature_families.id_family
						left join nomenclature_voices on nomenclature_children_records.child_record_num_voice = nomenclature_voices.id_voice
						left join nomenclature_workshops on nomenclature_children_records.child_record_num_nomenclature = nomenclature_workshops.workshop_num_nomenclature and nomenclature_children_records.child_record_num_workshop = nomenclature_workshops.id_workshop
						left join nomenclature_exotic_instruments on nomenclature_children_records.child_record_num_nomenclature = nomenclature_exotic_instruments.exotic_instrument_num_nomenclature and nomenclature_children_records.child_record_num_instrument = nomenclature_exotic_instruments.exotic_instrument_num_instrument';
				$query .= '	where explnum.explnum_notice in ('.implode(',', $this->get_manifs_ids()).')';
				if (count($filtered_elements)) {
					$query.= ' and num_object in ('.implode(',', $filtered_elements).')';
				}
				$query.= ' and num_concept in ('.implode(',', $concept_ids).') 
					and type_object = '.TYPE_EXPLNUM;
				$query.= ' order by notice_nomenclature_order, exotic_instrument_order, workshop_order, family_order, musicstand_order, child_record_order, voice_order';
				$query.= $this->get_limit();
				// on lance la requête
				$result = pmb_mysql_query($query);
				if($result && pmb_mysql_num_rows($result)){
					while($row = pmb_mysql_fetch_object($result)){
						$elements_ids[] = $row->num_object;
					}
				}
				$nb_filtered_results = pmb_mysql_result(pmb_mysql_query('select FOUND_ROWS()'), 0, 0);
				$tab->set_nb_filtered_results($nb_filtered_results);
			}
			$tab->set_contents($elements_ids);
		}
		return $tab;
	}
	
	/**
	 * 
	 * @param elements_list_tab $tab 
	 */
	protected function get_submanifestations_docnums_filters($tab, $concept_ids = array()) {
		$filters = $tab->get_filters();
		$elements_ids = array();
	
		foreach ($filters as $filter) {
			$result_ids = array();
			$groups = array();

			$query_clauses = $this->get_submanifestations_docnums_query_clauses($filter['name']);
			$query = 'select count(explnum.explnum_id) as nb, '.$query_clauses['select_group_id'].' as group_id, '.$query_clauses['select_label'].' as label 
					from index_concept join explnum on index_concept.num_object = explnum.explnum_id and type_object = '.TYPE_EXPLNUM;
			$query.= ' '.$query_clauses['from'].($query_clauses['join_label'] ? ' '.$query_clauses['join_label'] : '').
					' where explnum.explnum_notice in ('.implode(',', $this->get_manifs_ids()).')';
			if (count($concept_ids)) {
				$query.= ' and num_concept in ('.implode(',', $concept_ids).') and type_object = '.TYPE_EXPLNUM;
			}
			$query.= ' group by '.$query_clauses['select_group_id'].' having group_id != 0';
			if ($query_clauses['order']) {
				$query.= ' order by '.$query_clauses['order'];
			}
			
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					if(!isset($groups[$row->group_id])){
						$groups[$row->group_id] = array(
								'label' => $row->label,
								'nb_results' => $row->nb
						);
					}
				}
			}
			if (count($groups)) {
				if (!$query_clauses['order']) {
					// On trie le tableau uniquement si on n'a pas défini d'ordre dans la requête
					uasort($groups, array($this, '_sort_groups_by_label'));
				}
				$tab->add_groups($filter['name'], array(
						'label' => $filter['label'],
						'elements' => $groups
				));
				
				$filter_values = $tab->get_filter_values($filter['name']);

				//Si on a des résultats; on passe à la suite
				if($filter_values && count($filter_values)){
					$query = 'select distinct explnum.explnum_id as element_id';
					$query.= ' from explnum '.$query_clauses['from'];
					$query.= ' where explnum.explnum_notice in ('.implode(',', $this->get_manifs_ids()).')';
					$query.= ' and '.$query_clauses['select_group_id'].' in ("'.implode('","', $filter_values).'")';
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						while($row = pmb_mysql_fetch_object($result)){
							$result_ids[] = $row->element_id;
						}
					}
				}
				if (count($elements_ids) && count($result_ids)) {
					$elements_ids = array_intersect($elements_ids, $result_ids);
				} else if (count($result_ids)) {
					$elements_ids = $result_ids;
				}
			}
		}
		return $elements_ids;
	}
	
	protected function get_submanifestations_docnums_query_clauses($name) {
		$query_clauses = array();
		switch ($name) {
			case 'records_submanifestations_docnums_by_formation':
				$query_clauses = array(
						'select_group_id' => 'if (manifs.id_notice_nomenclature, manifs.id_notice_nomenclature, nomenclature_children_records.child_record_num_nomenclature)',
						'select_label' => 'if (manifs.id_notice_nomenclature, manifs.notice_nomenclature_label, formations.notice_nomenclature_label)',
						'from' => ' left join nomenclature_notices_nomenclatures as manifs on explnum.explnum_notice = manifs.notice_nomenclature_num_notice
								left join nomenclature_children_records on explnum.explnum_notice = nomenclature_children_records.child_record_num_record',
						'join_label' => 'left join nomenclature_notices_nomenclatures as formations on nomenclature_children_records.child_record_num_nomenclature = formations.id_notice_nomenclature',
						'order' => 'formations.notice_nomenclature_order'
				);
				break;
			case 'records_submanifestations_docnums_by_family':
				$query_clauses = array(
						'select_group_id' => 'nomenclature_musicstands.musicstand_famille_num',
						'select_label' => 'nomenclature_families.family_name',
						'from' => 'join nomenclature_children_records on explnum.explnum_notice = nomenclature_children_records.child_record_num_record'.
									' join nomenclature_musicstands on nomenclature_children_records.child_record_num_musicstand = nomenclature_musicstands.id_musicstand',
						'join_label' => 'join nomenclature_families on nomenclature_musicstands.musicstand_famille_num = nomenclature_families.id_family',
						'order' => 'family_order'
				);
				break;
			case 'records_submanifestations_docnums_by_musicstand':
				$query_clauses = array(
						'select_group_id' => 'nomenclature_children_records.child_record_num_musicstand',
						'select_label' => 'nomenclature_musicstands.musicstand_name',
						'from' => 'join nomenclature_children_records on explnum.explnum_notice = nomenclature_children_records.child_record_num_record',
						'join_label' => 'join nomenclature_musicstands on nomenclature_children_records.child_record_num_musicstand = nomenclature_musicstands.id_musicstand'.
										' join nomenclature_families on nomenclature_musicstands.musicstand_famille_num = nomenclature_families.id_family',
						'order' => 'family_order, musicstand_order'
				);
				break;
			case 'records_submanifestations_docnums_by_workshop':
				$query_clauses = array(
						'select_group_id' => 'nomenclature_children_records.child_record_num_workshop',
						'select_label' => 'nomenclature_workshops.workshop_label',
						'from' => 'join nomenclature_children_records on explnum.explnum_notice = nomenclature_children_records.child_record_num_record',
						'join_label' => 'join nomenclature_workshops on nomenclature_children_records.child_record_num_workshop = nomenclature_workshops.id_workshop',
						'order' => 'workshop_order'
				);
				break;
			case 'records_submanifestations_docnums_by_instrument':
				$query_clauses = array(
						'select_group_id' => 'nomenclature_children_records.child_record_num_instrument',
						'select_label' => 'nomenclature_instruments.instrument_name',
						'from' => 'join nomenclature_children_records on explnum.explnum_notice = nomenclature_children_records.child_record_num_record',
						'join_label' => 'join nomenclature_instruments on nomenclature_children_records.child_record_num_instrument = nomenclature_instruments.id_instrument',
				);
				break;
			case 'records_submanifestations_docnums_by_voice':
				$query_clauses = array(
						'select_group_id' => 'nomenclature_children_records.child_record_num_voice',
						'select_label' => 'nomenclature_voices.voice_name',
						'from' => 'join nomenclature_children_records on explnum.explnum_notice = nomenclature_children_records.child_record_num_record',
						'join_label' => 'join nomenclature_voices on nomenclature_children_records.child_record_num_voice = nomenclature_voices.id_voice',
						'order' => 'voice_order'
				);
				break;
		}
		return $query_clauses;
	}
}