<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_indexation_stack.class.php,v 1.3 2023/04/04 09:34:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/indexation_stack.class.php');

class scheduler_indexation_stack extends indexation_stack {
	
	/*protected static function is_indexation_needed() {
		global $pmb_scheduler_indexation_needed;
		return $pmb_scheduler_indexation_needed;
	}
	
	protected static function indexation_in_progress($in_progress) {
		global $pmb_scheduler_indexation_in_progress;

		$pmb_scheduler_indexation_in_progress = $in_progress;
		$query = "update parametres set valeur_param = '".$in_progress."' where type_param = 'pmb' and sstype_param = 'scheduler_indexation_in_progress' ";
		pmb_mysql_query($query);
	}
	
	protected static function indexation_needed($needed) {
		global $pmb_scheduler_indexation_needed;
		
		$pmb_scheduler_indexation_needed = $needed;
		$query = "update parametres set valeur_param = '".$needed."' where type_param = 'pmb' and sstype_param = 'scheduler_indexation_needed' ";
		pmb_mysql_query($query);
	}*/
	
	public static function push($entity_id, $entity_type, $datatype = 'all',$informations = '') {
		parent::push($entity_id, $entity_type, 'scheduler');
		if(count(static::$values) > 100) {
			//on pousse en base par lot de 100
			static::push_database();
		}
	}
	
	public static function push_database() {
		if (!empty(static::$values)) {
			$values = '';
			foreach (static::$values as $value) {
				if ($values) {
					$values.= ',';
				}
				$values.= '("'.$value['entity_id'].'", "'.$value['entity_type'].'", "'.$value['datatype'].'", "'.$value['timestamp'].'", "'.static::$parent_entity['id'].'", "'.static::$parent_entity['type'].'","'.$value['informations'].'")';
			}
			$query = 'insert ignore into indexation_stack (indexation_stack_entity_id, indexation_stack_entity_type, indexation_stack_datatype, indexation_stack_timestamp, indexation_stack_parent_id, indexation_stack_parent_type, indexation_stack_informations)
				values '.$values;
			pmb_mysql_query($query);
			static::$values = array();
		}
	}
	
	protected static function add_reciproc_entities($prefix, $id) {
		//do nothing	
	}
	
	public static function add_exclude_datatypes_with_index_records() {
		$exclude_datatypes = array(
				'new',
				'serial',
				'expl',
				'map',
				'custom_field',
				'explnum',
				'nomenclature'
		);
		static::$exclude_datatypes = array_merge(static::$exclude_datatypes, $exclude_datatypes);
	}
	
	public static function add_exclude_datatypes_with_index_authorities() {
		$exclude_datatypes = array(
				'author',
				'publisher',
				'indexint',
				'collection',
				'serie',
				'subcollection',
				'subject',
				'uniformtitle',
				'authperso',
				'author',
				'custom_field',
				'aut_link',
				'authperso_link',
				'tu_subdiv',
				'tu_ref',
				'tu_distrib',
				'tu_oeuvres_event',
				'oeuvre_link'
		);
		static::$exclude_datatypes = array_merge(static::$exclude_datatypes, $exclude_datatypes);
	}
	
	public static function add_exclude_datatypes_with_index_concept() {
		$exclude_datatypes = array(
				'concept',
		);
		static::$exclude_datatypes = array_merge(static::$exclude_datatypes, $exclude_datatypes);
	}
	
	public function __destruct() {
		//do nothing
	}
}