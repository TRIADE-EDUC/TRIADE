<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authority_tabs_titre_uniforme.class.php,v 1.11 2021/12/28 08:46:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/authorities/tabs/authority_tabs.class.php');
require_once($class_path.'/vedette/vedette_composee.class.php');
require_once($class_path.'/onto/common/onto_common_uri.class.php');
require_once($class_path."/skos/skos_concept.class.php");

class authority_tabs_titre_uniforme extends authority_tabs {

	/**
	 * Méthode permettant de récupérer les autorités indexées avec ce concept
	 * @param elements_list_tab $tab
	 * @param authority_tabs $authority_tabs
	 */
	protected static function get_tab_titre_uniforme_execution_nomenclature_docnums($tab, $authority) {
		global $pmb_nomenclature_activate, $msg;
		global $pmb_nomenclature_music_concept_before, $pmb_nomenclature_music_concept_after;
		global $quoi;
		
		if (!$pmb_nomenclature_activate) {
			return false;
		}
		$concept_ids = array(
				'before' => array(),
				'after' => array()
		);
		
		if($pmb_nomenclature_music_concept_before){
			$before_id = onto_common_uri::get_id($pmb_nomenclature_music_concept_before);
			if($before_id){
				$vedette_composee_found = vedette_composee::get_vedettes_built_with_elements(array(
					array(
							'id' => $authority->get_num_object(),
							'type' => $authority->get_type_const()
					),
					array(
							'id' => $before_id,
							'type' => TYPE_CONCEPT
					)), 'music_explnum');
			
				if(count($vedette_composee_found)){
					foreach($vedette_composee_found as $vedette_id){
						$concept_ids['before'][] = vedette_composee::get_object_id_from_vedette_id($vedette_id, TYPE_CONCEPT_PREFLABEL);
					}
				}
			}
		}
		if($pmb_nomenclature_music_concept_after){
			$after_id = onto_common_uri::get_id($pmb_nomenclature_music_concept_after);
			if($after_id){
				$vedette_composee_found = vedette_composee::get_vedettes_built_with_elements(array(
					array(
							'id' => $authority->get_num_object(),
							'type' => $authority->get_type_const()
					),
					array(
							'id' => $after_id,
							'type' => TYPE_CONCEPT
					)), 'music_explnum');
				if(count($vedette_composee_found)){
					foreach($vedette_composee_found as $vedette_id){
						$concept_ids['after'][] = vedette_composee::get_object_id_from_vedette_id($vedette_id, TYPE_CONCEPT_PREFLABEL);
					}
				}
			}
		}
		$nb_results = 0;
		if (count($concept_ids['before']) || count($concept_ids['after'])) {
			$query = 'select count(distinct num_object) from index_concept 
					join explnum on index_concept.num_object = explnum.explnum_id and type_object = '.TYPE_EXPLNUM.' and explnum.explnum_bulletin = 0
					left join nomenclature_notices_nomenclatures on explnum.explnum_notice = nomenclature_notices_nomenclatures.notice_nomenclature_num_notice
					left join nomenclature_children_records on explnum.explnum_notice = nomenclature_children_records.child_record_num_record
					where (nomenclature_notices_nomenclatures.id_notice_nomenclature is not null or nomenclature_children_records.child_record_num_nomenclature is not null)';
		
			$query.= 'and num_concept in ('.implode(',', array_merge($concept_ids['before'], $concept_ids['after'])).')';
			$nb_results = pmb_mysql_result(pmb_mysql_query($query), 0, 0);
		}
		
		$tab->set_nb_results($nb_results);
		
		if (!$quoi && $nb_results) {
			// Si $quoi n'est pas valorisé et qu'on a des résultats, on valorise $quoi avec cet onglet
			$quoi = $tab->get_name();
		}
		$elements_ids = array();
		if ($nb_results && ($quoi == $tab->get_name())) {
			// On définit les filtres
			$filters = array(
					array(
							'name' => 'titre_uniforme_execution_nomenclature_docnums_by_temporality',
							'label' => $msg['authority_tabs_titre_uniforme_execution_nomenclature_docnums_by_temporality']
					),
					array(
							'name' => 'titre_uniforme_execution_nomenclature_docnums_by_formation',
							'label' => $msg['authority_tabs_titre_uniforme_execution_nomenclature_docnums_by_formation']
					),
					array(
							'name' => 'titre_uniforme_execution_nomenclature_docnums_by_family',
							'label' => $msg['authority_tabs_titre_uniforme_execution_nomenclature_docnums_by_family']
					),
					array(
							'name' => 'titre_uniforme_execution_nomenclature_docnums_by_musicstand',
							'label' => $msg['authority_tabs_titre_uniforme_execution_nomenclature_docnums_by_musicstand']
					),
					array(
							'name' => 'titre_uniforme_execution_nomenclature_docnums_by_workshop',
							'label' => $msg['authority_tabs_titre_uniforme_execution_nomenclature_docnums_by_workshop']
					),
					array(
							'name' => 'titre_uniforme_execution_nomenclature_docnums_by_instrument',
							'label' => $msg['authority_tabs_titre_uniforme_execution_nomenclature_docnums_by_instrument']
					),
					array(
							'name' => 'titre_uniforme_execution_nomenclature_docnums_by_voice',
							'label' => $msg['authority_tabs_titre_uniforme_execution_nomenclature_docnums_by_voice']
					)
			);
			$tab->set_filters($filters);
			$filtered_elements = self::get_titre_uniforme_execution_nomenclature_docnums_filters($tab, $concept_ids);
				
			if (!$tab->has_filters_values() || ($tab->has_filters_values() && count($filtered_elements))) {
				$query = 'select SQL_CALC_FOUND_ROWS num_object';
				$query.= ' from index_concept
						join explnum on index_concept.num_object = explnum.explnum_id and type_object = '.TYPE_EXPLNUM.'
						left join nomenclature_notices_nomenclatures as manifs on explnum.explnum_notice = manifs.notice_nomenclature_num_notice
						left join nomenclature_children_records on explnum.explnum_notice = nomenclature_children_records.child_record_num_record
						left join nomenclature_notices_nomenclatures as formations on nomenclature_children_records.child_record_num_nomenclature = formations.id_notice_nomenclature
						left join nomenclature_musicstands on nomenclature_children_records.child_record_num_musicstand = nomenclature_musicstands.id_musicstand
						left join nomenclature_families on nomenclature_musicstands.musicstand_famille_num = nomenclature_families.id_family
						left join nomenclature_voices on nomenclature_children_records.child_record_num_voice = nomenclature_voices.id_voice
						left join nomenclature_workshops on nomenclature_children_records.child_record_num_nomenclature = nomenclature_workshops.workshop_num_nomenclature and nomenclature_children_records.child_record_num_workshop = nomenclature_workshops.id_workshop
						left join nomenclature_exotic_instruments on nomenclature_children_records.child_record_num_nomenclature = nomenclature_exotic_instruments.exotic_instrument_num_nomenclature and nomenclature_children_records.child_record_num_instrument = nomenclature_exotic_instruments.exotic_instrument_num_instrument';
				$query .= '	where (manifs.id_notice_nomenclature is not null or nomenclature_children_records.child_record_num_nomenclature is not null)';
				if (count($filtered_elements)) {
					$query.= ' and num_object in ('.implode(',', $filtered_elements).')';
				}
				if (count($concept_ids['before']) || count($concept_ids['after'])) {
					$query.= ' and num_concept in ('.implode(',', array_merge($concept_ids['before'], $concept_ids['after'])).')';
				}
				$query.= ' order by formations.notice_nomenclature_order, exotic_instrument_order, workshop_order, family_order, musicstand_order, child_record_order, voice_order';
				// on lance la requête
				$result = pmb_mysql_query($query);
				if($result && pmb_mysql_num_rows($result)){
					while($row = pmb_mysql_fetch_object($result)){
						$elements_ids[] = $row->num_object;
					}
				}
				$nb_filtered_results = pmb_mysql_result(pmb_mysql_query('select FOUND_ROWS()'), 0, 0);
				$tab->set_nb_filtered_results($nb_filtered_results);
				$elements_ids = self::get_limit($elements_ids);
			}
			$tab->set_contents($elements_ids);
		}
	}
	
	/**
	 * 
	 * @param elements_list_tab $tab 
	 */
	protected static function get_titre_uniforme_execution_nomenclature_docnums_filters($tab, $concept_ids = array()) {
		$filters = $tab->get_filters();
		$elements_ids = array();
	
		foreach ($filters as $filter) {
			if ($filter['name'] == 'titre_uniforme_execution_nomenclature_docnums_by_temporality') {
				// On traite le cas particulier du filtre de temporalité
				$result_ids = self::get_titre_uniforme_execution_nomenclature_docnums_temporality_filter($tab, $concept_ids, $filter);
				if (count($elements_ids) && count($result_ids)) {
					$elements_ids = array_intersect($elements_ids, $result_ids);
				} else if (count($result_ids)) {
					$elements_ids = $result_ids;
				}
				continue;
			}
			// On n'a plus besoin de la distinction avant/après
			$result_ids = array();
			$groups = array();

			$query_clauses = self::get_titre_uniforme_execution_nomenclature_docnums_query_clauses($filter['name']);
			$query = 'select count(explnum.explnum_id) as nb, '.$query_clauses['select_group_id'].' as group_id, '.$query_clauses['select_label'].' as label 
					from index_concept 
					join explnum on index_concept.num_object = explnum.explnum_id and type_object = '.TYPE_EXPLNUM.' and explnum.explnum_bulletin = 0';
			$query.= ' '.$query_clauses['from'].($query_clauses['join_label'] ? ' '.$query_clauses['join_label'] : '');
			if (strpos($query, 'nomenclature_notices_nomenclatures') === false) {
				$query.= ' left join nomenclature_notices_nomenclatures as manifs on explnum.explnum_notice = manifs.notice_nomenclature_num_notice';
			}
			if (strpos($query, 'nomenclature_children_records') === false) {
				$query.= ' left join nomenclature_children_records on explnum.explnum_notice = nomenclature_children_records.child_record_num_record';
			}
			$query.= ' where (manifs.id_notice_nomenclature is not null or nomenclature_children_records.child_record_num_nomenclature is not null)';
			if (count($concept_ids['before']) || count($concept_ids['after'])) {
				$query.= ' and num_concept in ('.implode(',', array_merge($concept_ids['before'], $concept_ids['after'])).')';
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
					uasort($groups, array('authority_tabs', '_sort_groups_by_label'));
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
					if (strpos($query, 'nomenclature_notices_nomenclatures') === false) {
						$query.= ' left join nomenclature_notices_nomenclatures as manifs on explnum.explnum_notice = manifs.notice_nomenclature_num_notice';
					}
					if (strpos($query, 'nomenclature_children_records') === false) {
						$query.= ' left join nomenclature_children_records on explnum.explnum_notice = nomenclature_children_records.child_record_num_record';
					}
					$query.= ' where (manifs.id_notice_nomenclature is not null or nomenclature_children_records.child_record_num_nomenclature is not null)';
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
	
	protected static function get_titre_uniforme_execution_nomenclature_docnums_query_clauses($name) {
		$query_clauses = array();
		switch ($name) {
			case 'titre_uniforme_execution_nomenclature_docnums_by_formation':
				$query_clauses = array(
						'select_group_id' => 'if (manifs.id_notice_nomenclature, manifs.id_notice_nomenclature, nomenclature_children_records.child_record_num_nomenclature)',
						'select_label' => 'if (manifs.id_notice_nomenclature, manifs.notice_nomenclature_label, formations.notice_nomenclature_label)',
						'from' => ' left join nomenclature_notices_nomenclatures as manifs on explnum.explnum_notice = manifs.notice_nomenclature_num_notice
								left join nomenclature_children_records on explnum.explnum_notice = nomenclature_children_records.child_record_num_record',
						'join_label' => 'left join nomenclature_notices_nomenclatures as formations on nomenclature_children_records.child_record_num_nomenclature = formations.id_notice_nomenclature',
						'order' => 'formations.notice_nomenclature_order'
				);
				break;
			case 'titre_uniforme_execution_nomenclature_docnums_by_family':
				$query_clauses = array(
						'select_group_id' => 'nomenclature_musicstands.musicstand_famille_num',
						'select_label' => 'nomenclature_families.family_name',
						'from' => 'join nomenclature_children_records on explnum.explnum_notice = nomenclature_children_records.child_record_num_record'.
									' join nomenclature_musicstands on nomenclature_children_records.child_record_num_musicstand = nomenclature_musicstands.id_musicstand',
						'join_label' => 'join nomenclature_families on nomenclature_musicstands.musicstand_famille_num = nomenclature_families.id_family',
						'order' => 'family_order'
				);
				break;
			case 'titre_uniforme_execution_nomenclature_docnums_by_musicstand':
				$query_clauses = array(
						'select_group_id' => 'nomenclature_children_records.child_record_num_musicstand',
						'select_label' => 'nomenclature_musicstands.musicstand_name',
						'from' => 'join nomenclature_children_records on explnum.explnum_notice = nomenclature_children_records.child_record_num_record',
						'join_label' => 'join nomenclature_musicstands on nomenclature_children_records.child_record_num_musicstand = nomenclature_musicstands.id_musicstand'.
										' join nomenclature_families on nomenclature_musicstands.musicstand_famille_num = nomenclature_families.id_family',
						'order' => 'family_order, musicstand_order'
				);
				break;
			case 'titre_uniforme_execution_nomenclature_docnums_by_workshop':
				$query_clauses = array(
						'select_group_id' => 'nomenclature_children_records.child_record_num_workshop',
						'select_label' => 'nomenclature_workshops.workshop_label',
						'from' => 'join nomenclature_children_records on explnum.explnum_notice = nomenclature_children_records.child_record_num_record',
						'join_label' => 'join nomenclature_workshops on nomenclature_children_records.child_record_num_workshop = nomenclature_workshops.id_workshop',
						'order' => 'workshop_order'
				);
				break;
			case 'titre_uniforme_execution_nomenclature_docnums_by_instrument':
				$query_clauses = array(
						'select_group_id' => 'nomenclature_children_records.child_record_num_instrument',
						'select_label' => 'nomenclature_instruments.instrument_name',
						'from' => 'join nomenclature_children_records on explnum.explnum_notice = nomenclature_children_records.child_record_num_record',
						'join_label' => 'join nomenclature_instruments on nomenclature_children_records.child_record_num_instrument = nomenclature_instruments.id_instrument',
				);
				break;
			case 'titre_uniforme_execution_nomenclature_docnums_by_voice':
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
	
	protected static function get_titre_uniforme_execution_nomenclature_docnums_temporality_filter($tab, $concept_ids, $filter) {
		global $pmb_nomenclature_music_concept_before, $pmb_nomenclature_music_concept_after;
		
		$concept_before = new skos_concept(0, $pmb_nomenclature_music_concept_before);
		$concept_after = new skos_concept(0, $pmb_nomenclature_music_concept_after);
		$concepts = array(
				array(
						'group_id' => 1,
						'group_label' => $concept_before->get_display_label(),
						'concept_ids' => $concept_ids['before']
				),
				array(
						'group_id' => 2,
						'group_label' => $concept_after->get_display_label(),
						'concept_ids' => $concept_ids['after']
				)
		);
		$filter_values = $tab->get_filter_values($filter['name']);
		
		$result_ids = array();
		$groups = array();
		
		for ($i = 0; $i < count($concepts); $i++) {
			if (!count($concepts[$i]['concept_ids'])) {
				continue;
			}
			$query = 'select distinct explnum.explnum_id as element_id from index_concept
						join explnum on index_concept.num_object = explnum.explnum_id and type_object = '.TYPE_EXPLNUM.' and explnum.explnum_bulletin = 0';
			if (strpos($query, 'nomenclature_notices_nomenclatures') === false) {
				$query.= ' left join nomenclature_notices_nomenclatures as manifs on explnum.explnum_notice = manifs.notice_nomenclature_num_notice';
			}
			if (strpos($query, 'nomenclature_children_records') === false) {
				$query.= ' left join nomenclature_children_records on explnum.explnum_notice = nomenclature_children_records.child_record_num_record';
			}
			$query.= ' where (manifs.id_notice_nomenclature is not null or nomenclature_children_records.child_record_num_nomenclature is not null)';
			if (count($concepts[$i]['concept_ids'])) {
				$query.= ' and num_concept in ('.implode(',', $concepts[$i]['concept_ids']).')';
			}
			
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$groups[$concepts[$i]['group_id']] = array(
						'label' => $concepts[$i]['group_label'],
						'nb_results' => pmb_mysql_num_rows($result)
				);
				if (is_array($filter_values) && in_array($concepts[$i]['group_id'], $filter_values)) {
					while ($row = pmb_mysql_fetch_object($result)) {
						$result_ids[] = $row->element_id;
					}
				}
			}
		}
		
		if (count($groups)) {
			$tab->add_groups($filter['name'], array(
					'label' => $filter['label'],
					'elements' => $groups
			));
		}
		return $result_ids;
	}
}