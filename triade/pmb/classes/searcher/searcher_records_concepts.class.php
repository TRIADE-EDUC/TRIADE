<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_records_concepts.class.php,v 1.6.16.1 2025/02/12 12:34:08 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class searcher_records_concepts extends searcher_records {

	public function __construct($user_query){
		global $thesaurus_concepts_autopostage, $concepts_autopostage_query;

		parent::__construct($user_query);

		$this->field_restrict[] = array(
				'field' => "code_champ",
				'values' => array(36),
				'op' => "and",
				'not' => false
		);
		//ajout de l'autopostage dans les recherches
		if (!empty($thesaurus_concepts_autopostage) && !empty($concepts_autopostage_query)) {
			$this->field_restrict[] = array(
				'field' => "code_champ",
				'values' => array(129),
				'op' => "or",
				'not' => false
			);
		}
	}

	protected function _get_search_type(){
		return parent::_get_search_type()."_concept";
	}

	public static function get_full_query_from_authority($id) {
		global $thesaurus_concepts_autopostage, $concepts_autopostage_query;
		$id = intval($id);
		$sub_query = " notice_id IN (
				SELECT DISTINCT num_object AS notice_id
				FROM index_concept
				WHERE type_object = 1
				AND num_concept = '".$id."'";
		//TODO : requete a modifier quand on aura stocker l'authority num dans notices_fields_global_index
		if (!empty($thesaurus_concepts_autopostage) && !empty($concepts_autopostage_query)) {
			$sub_query.= " UNION
				SELECT DISTINCT num_object AS notice_id
				FROM index_concept
				JOIN skos_fields_global_index ON authority_num = num_concept
				WHERE type_object = 1
				AND id_item = '".$id."'
				AND code_champ IN (5,6)";
		}
		$sub_query.=")";
		$query = parent::get_full_query_from_authority($id).$sub_query;
		return $query;
	}
}