<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authority_tabs_concept.class.php,v 1.9 2018/01/24 15:53:00 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/authorities/tabs/authority_tabs.class.php');
require_once($class_path.'/skos/skos_concept.class.php');
require_once($class_path.'/onto/common/onto_common_uri.class.php');

class authority_tabs_concept extends authority_tabs {

	/**
	 * Méthode permettant de récupérer les autorités indexées avec ce concept
	 * @param elements_list_tab $tab
	 * @param authority_tabs $authority_tabs
	 */
	protected static function get_tab_concept_authorities($tab, $authority){		
		self::get_tab_authorities_indexed_with_concepts($tab, $authority, array($authority->get_num_object()));
	}
	
	/**
	 * retourne les ids des concepts autopostés
	 * @param int $concept_id
	 * @return array :
	 */
	protected static function get_autoposted_concepts_id($concept_id) {		
		$paths = "";
		$broad_paths = skos_concept::get_broad_paths(onto_common_uri::get_uri($concept_id));
		$paths.= implode('', $broad_paths); 
		$narrow_paths = skos_concept::get_narrow_paths(onto_common_uri::get_uri($concept_id));
		$paths.= implode('', $narrow_paths); 
		$paths = substr($paths, 0, -1);
		$concepts_ids = explode('/', $paths);
		if ($concepts_ids[count($concepts_ids) - 1] == "") {
			array_pop($concepts_ids);
		}
		$concepts_ids = array_unique($concepts_ids);
		return $concepts_ids;
	}
	
	/**
	 * Méthode permettant de récupérer les notices indexées avec ce concept
	 * @param elements_list_tab $tab
	 * @param authority $authority
	 */
	protected static function get_tab_concept_records($tab, $authority) {
		global $quoi, $thesaurus_concepts_autopostage;
		
		$concepts_ids = array($authority->get_object_instance()->get_id());
		if ($thesaurus_concepts_autopostage) {
			$concepts_ids = array_merge($concepts_ids, self::get_autoposted_concepts_id($authority->get_object_instance()->get_id()));
		}
		$nb_result = self::get_nb_records_indexed_with_concepts($concepts_ids);		
		$tab->set_nb_results($nb_result);
		
		if (!$quoi && $nb_result) {
			// Si $quoi n'est pas valorisé et qu'on a des résultats, on valorise $quoi avec cet onglet
			$quoi = $tab->get_name();
		}
			
		$elements_ids = array();
		if ($nb_result && ($quoi == $tab->get_name())) {
			$filtered_elements = self::get_filtered_records_by_concepts_autoposted($tab, $authority->get_object_instance()->get_id());
			if (count($filtered_elements)) {
				$elements_ids = array_merge($elements_ids, $filtered_elements);
			}
			$elements_ids = self::get_limit(self::get_sorted_results($elements_ids, $tab));
		}
		$tab->set_contents($elements_ids);
	}	
	
	/**
	 * Ajout des notices indéxés par les concepts autopostés
	 * @param elements_records_list_ui $tab
	 * @param int $concept_id
	 * @return array:
	 */
	protected static function get_filtered_records_by_concepts_autoposted($tab, $concept_id) {
		global $msg, $thesaurus_concepts_autopostage;
		pmb_mysql_query('set session group_concat_max_len = 16777216');
		//Récupération du nombre de notice liées
		$groups = array();
		$elements_ids = array();
		$records_ids = '';
		
		$concepts_ids = array($concept_id);
		$records_ids = self::get_records_ids_from_concepts_ids($concepts_ids);
		
		if ($records_ids) {
			$elements_ids = explode(',', self::get_filtered_results($records_ids, $tab));		
		}		
		
		if ($thesaurus_concepts_autopostage) {
			$concepts_ids = array_merge($concepts_ids, self::get_autoposted_concepts_id($concept_id));
			$records_ids = self::get_records_ids_from_concepts_ids($concepts_ids);
			if ($records_ids) {
				$filtered_results = self::get_filtered_results($records_ids, $tab);
				if($filtered_results){
					if(!isset($groups[1])){
						$groups[1] = array(
								'label' => $msg['40'],
								'nb_results' => (substr_count($filtered_results,",") + 1)
						);
					}
					//$tab->set_nb_results(substr_count($filtered_results,",") + 1);	
				}
			}
		
			if (count($groups)) {
				// On trie le tableau
				uasort($groups, array('authority_tabs', '_sort_groups_by_label'));
				$tab->add_groups("concept_records_with_autoposting", array(
						'label' => $msg['authority_tabs_records_indexed_with_autoposted_concepts'],
						'elements' => $groups
				));
				
				$filter_values = $tab->get_filter_values("concept_records_with_autoposting");
				//Si on a des résultats; on passe à la suite
				if($filter_values && count($filter_values)){
					$elements_ids = explode(',', $filtered_results);
				}
			}
		}
		$nb = count($elements_ids);
		if ($nb == 0) {
			$nb = 1;
		}
		$tab->set_nb_results($nb);
		$tab->set_nb_filtered_results(count($elements_ids));
		
		return $elements_ids;	
	}
	
	protected static function get_records_ids_from_concepts_ids($concepts_ids) {
		$records_ids = "";
		if (is_array($concepts_ids) && count($concepts_ids)) {
			$query = '
					SELECT GROUP_CONCAT(DISTINCT num_object SEPARATOR ",") 
					FROM index_concept 
					WHERE num_concept in ('.implode(',', $concepts_ids).') 
					AND type_object = "'.TYPE_NOTICE.'"';
			$result = pmb_mysql_query($query);
			$records_ids = pmb_mysql_result(pmb_mysql_query($query), 0, 0);
		}
		return $records_ids;
	} 
	
	protected static function get_nb_records_indexed_with_concepts($concepts_ids){
		if (count($concepts_ids)) {
			$query = '
					SELECT COUNT(DISTINCT num_object, type_object) 
					FROM index_concept 
					WHERE num_concept IN ('.implode(',', $concepts_ids).') 
					AND type_object = "'.TYPE_NOTICE.'"';
			return pmb_mysql_result(pmb_mysql_query($query), 0, 0);
		}
		return 0;
	}
}