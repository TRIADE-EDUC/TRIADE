<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_concepts_datasource_entities_used_in_composed_concept.class.php,v 1.2 2019/06/11 08:53:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_concepts_datasource_entities_used_in_composed_concept extends frbr_entity_common_datasource {
	
	protected $object_type;
	
	public function get_sub_datasources(){
	    if(static::class != 'frbr_entity_concepts_datasource_entities_used_in_composed_concept') {
			return array();
		} else {
			return array(
					"frbr_entity_concepts_datasource_authors_used_in_composed_concept",
					"frbr_entity_concepts_datasource_works_used_in_composed_concept"
			);
		}
	}
	
	public function get_form(){
		$form = parent::get_form();
		if(static::class != 'frbr_entity_concepts_datasource_entities_used_in_composed_concept' && !empty($this->object_type)) {
			$form .= "
			<input type='hidden' name='datanode_object_type' value='".$this->object_type."'/>";
		}
		return $form;
	}
	
	public function save_form(){
		global $datanode_object_type;
		$this->parameters->object_type = $datanode_object_type;
		return parent::save_form();
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
		$query = 'SELECT num_vedette, num_object FROM vedette_link WHERE num_object IN ('.implode(',', $datas).') AND type_object = '.TYPE_CONCEPT_PREFLABEL;
		$result = pmb_mysql_query($query);
		$num_vedettes = array();
		$num_objects = array();
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_assoc($result)) {
				$num_vedettes[] = $row['num_vedette'];
				$num_objects[$row['num_vedette']] = $row['num_object'];
			}
		}
		
		$datas = array();
		if (count($num_vedettes)) {
			$query = 'SELECT object_id AS id, num_vedette FROM vedette_object WHERE object_type = '.$this->parameters->object_type.' AND num_vedette IN ('.implode($num_vedettes).')';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_assoc($result)) {
					$datas[$num_objects[$row['num_vedette']]][] = $row['id'];
					$datas[0][] = $row['id'];
				}
			}
		}
		$datas = parent::get_datas($datas);
		return $datas;
	}
	
	
}