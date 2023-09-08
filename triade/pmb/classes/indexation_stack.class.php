<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexation_stack.class.php,v 1.9 2019-03-06 09:31:15 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/notice.class.php');
require_once($class_path.'/indexation_record.class.php');
require_once($class_path.'/indexations_collection.class.php');
require_once($class_path.'/skos/skos_onto.class.php');
require_once($class_path.'/skos/skos_datastore.class.php');
require_once($class_path.'/curl.class.php');
require_once($class_path.'/onto/skos/onto_skos_index.class.php');
require_once($class_path.'/onto/skos/onto_skos_autoposting.class.php');

class indexation_stack {
	
	protected static $indexation_record;
	protected static $onto_index;
	protected static $self;
	protected static $values = array();
	protected static $parent_entity;
	
	public static function push($entity_id, $entity_type, $datatype = 'all') {
		global $base_path, $pmb_indexation_needed;
		if (!isset(self::$self)) {
			self::$self = new indexation_stack();
		}
		if (array_key_exists($entity_type.'_'.$entity_id.'_'.$datatype, self::$values) || array_key_exists($entity_type.'_'.$entity_id.'_all', self::$values)) {
			return;
		}
	
		if(!$pmb_indexation_needed){
			self::indexation_needed(1);
		}
	
		if (empty(self::$values)) {
			self::$parent_entity = array(
					'id' => $entity_id,
					'type' => $entity_type
			);
		}
		self::$values[$entity_type.'_'.$entity_id.'_'.$datatype] = array(
				'entity_id' => $entity_id,
				'entity_type' => $entity_type,
				'datatype' => $datatype,
				'timestamp' => microtime(true)*1000
		);
	}
	
	public static function init_indexation($token=0){
		global $pmb_indexation_needed, $pmb_indexation_in_progress, $pmb_url_internal;
		
		if (!$pmb_indexation_needed || ($token != $pmb_indexation_in_progress && $pmb_indexation_in_progress != 0)) {
			return;
		}
		$curl = new Curl();
		$curl->set_option('CURLOPT_TIMEOUT', '1');
		$curl->set_option('CURLOPT_SSL_VERIFYPEER',false);
		
		$curl->get($pmb_url_internal.'indexation_stack.php?token='.$token.'&database='.LOCATION);
// 		$error = $curl->error();
	}
	
	public static function launch_indexation($token=0){
		global $pmb_indexation_needed, $pmb_indexation_in_progress;
		
		if (!$pmb_indexation_needed || ($token != $pmb_indexation_in_progress && $pmb_indexation_in_progress != 0)) {
			return;
		}
		if($token == 0){
		    $token = md5(microtime(true).'_toindex_'.SESSid);
		    self::indexation_in_progress($token);
		}
			
		$limit = 100;
		
		$query = "select indexation_stack_entity_id, indexation_stack_entity_type, indexation_stack_datatype from indexation_stack order by indexation_stack_timestamp limit ".$limit;
		$result = pmb_mysql_query($query);
		$nb_results = pmb_mysql_num_rows($result);
		if ($nb_results < $limit) {
			self::indexation_needed(0);
		}
		
		if($nb_results){
			while($row = pmb_mysql_fetch_assoc($result)){
				self::index_entity($row['indexation_stack_entity_id'], $row['indexation_stack_entity_type'], $row['indexation_stack_datatype']);	
			}
		}
		if ($nb_results < $limit) {
		    self::indexation_in_progress(0);
		    return;
		}
		self::init_indexation($token);
	}
	
	public static function index_entity($entity_id, $entity_type, $datatype){
		$query = "delete from indexation_stack where indexation_stack_entity_type = '".$entity_type."'
				and indexation_stack_entity_id = '".$entity_id."' and indexation_stack_datatype = '".$datatype."' ";
		pmb_mysql_query($query);
		
		switch($entity_type){
			case TYPE_NOTICE:
				if($datatype == 'all'){
					notice::majNotices($entity_id);
				}
				notice::majNoticesGlobalIndex($entity_id);
				notice::majNoticesMotsGlobalIndex($entity_id, $datatype);
				break;
			case TYPE_AUTHOR:
				$indexation_authority = indexations_collection::get_indexation(AUT_TABLE_AUTHORS);
				$indexation_authority->maj($entity_id, $datatype);
				break;
			case TYPE_CATEGORY:
				$indexation_authority = indexations_collection::get_indexation(AUT_TABLE_CATEG);
				$indexation_authority->maj($entity_id, $datatype);
				break;
			case TYPE_PUBLISHER:
				$indexation_authority = indexations_collection::get_indexation(AUT_TABLE_PUBLISHERS);
				$indexation_authority->maj($entity_id, $datatype);
				break;
			case TYPE_COLLECTION:
				$indexation_authority = indexations_collection::get_indexation(AUT_TABLE_COLLECTIONS);
				$indexation_authority->maj($entity_id, $datatype);
				break;
			case TYPE_SUBCOLLECTION:
				$indexation_authority = indexations_collection::get_indexation(AUT_TABLE_SUB_COLLECTIONS);
				$indexation_authority->maj($entity_id, $datatype);
				break;
			case TYPE_SERIE:
				$indexation_authority = indexations_collection::get_indexation(AUT_TABLE_SERIES);
				$indexation_authority->maj($entity_id, $datatype);
				break;
			case TYPE_TITRE_UNIFORME:
				$indexation_authority = indexations_collection::get_indexation(AUT_TABLE_TITRES_UNIFORMES);
				$indexation_authority->maj($entity_id, $datatype);
				break;
			case TYPE_INDEXINT:
				$indexation_authority = indexations_collection::get_indexation(AUT_TABLE_INDEXINT);
				$indexation_authority->maj($entity_id, $datatype);
				break;
			case TYPE_EXPLNUM:
				//TODO: Regarder ou est faite l'indexation des documents numériques
				break;
			case TYPE_AUTHPERSO:
				//TODO
				break;
			case TYPE_CMS_SECTION:
				//TODO
				break;
			case TYPE_CMS_ARTICLE:
				//TODO
				break;
			case TYPE_CONCEPT:
				if(!isset(static::$onto_index)){
					static::$onto_index = new onto_skos_index();
					static::$onto_index->load_handler('', skos_onto::get_store(), array(), skos_datastore::get_store(), array(), array(), 'http://www.w3.org/2004/02/skos/core#prefLabel');
				}
				static::$onto_index->maj($entity_id, '', $datatype);
				break;
		}
	}
	
	public function __destruct() {
		global $dbh;
		
		if (!empty(self::$values)) {
			$dbh = connection_mysql();
			$values = '';
			foreach (self::$values as $value) {
				if ($values) {
					$values.= ',';
				}
				$values.= '("'.$value['entity_id'].'", "'.$value['entity_type'].'", "'.$value['datatype'].'", "'.$value['timestamp'].'", "'.self::$parent_entity['id'].'", "'.self::$parent_entity['type'].'")';
			}
			$query = 'insert into indexation_stack (indexation_stack_entity_id, indexation_stack_entity_type, indexation_stack_datatype, indexation_stack_timestamp, indexation_stack_parent_id, indexation_stack_parent_type)
				values '.$values;
			pmb_mysql_query($query);
		}
	}
	
	public static function get_indexation_state(){
		global $pmb_indexation_in_progress;
		self::init_indexation();
		if (!$pmb_indexation_in_progress) {
			return array();
		}
		$query = 'SELECT count(indexation_stack_entity_id) as nb_entity, indexation_stack_entity_type as entity_type, indexation_stack_parent_id as parent_id, indexation_stack_parent_type as parent_type from indexation_stack group by indexation_stack_parent_type, indexation_stack_parent_id, indexation_stack_entity_type order by indexation_stack_timestamp';
		$result = pmb_mysql_query($query);
		$data = array();
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_assoc($result)){
				
				if(!isset($data[$row['parent_type'].'_'.$row['parent_id']])){
					$data[$row['parent_type'].'_'.$row['parent_id']] = array(
						'label' => self::get_entity_isbd($row['parent_type'], $row['parent_id']),
						'children' => array()
					);					
				}
				$data[$row['parent_type'].'_'.$row['parent_id']]['children'][] = array(
					'entity_label' => self::get_label_from_type($row['entity_type']),
					'nb' => $row['nb_entity'],
				);
			}
		}
		return $data;
	}
	
	protected static function get_entity_isbd($entity_type, $entity_id) {
		global $msg;
		$label = '';
		switch ($entity_type) {
			case TYPE_NOTICE :
				$label = $msg['288'].' : '.notice::get_notice_title($entity_id);
				break;
			case TYPE_AUTHOR :
				$authority = new authority(0, $entity_id, AUT_TABLE_AUTHORS);
				break;
			case TYPE_CATEGORY :
				$authority = new authority(0, $entity_id, AUT_TABLE_CATEG);
				break;
			case TYPE_PUBLISHER :
				$authority = new authority(0, $entity_id, AUT_TABLE_PUBLISHERS);
				break;
			case TYPE_COLLECTION :
				$authority = new authority(0, $entity_id, AUT_TABLE_COLLECTIONS);
				break;
			case TYPE_SUBCOLLECTION :
				$authority = new authority(0, $entity_id, AUT_TABLE_SUB_COLLECTIONS);
				break;
			case TYPE_SERIE :
				$authority = new authority(0, $entity_id, AUT_TABLE_SERIES);
				break;
			case TYPE_TITRE_UNIFORME :
				$authority = new authority(0, $entity_id, AUT_TABLE_TITRES_UNIFORMES);
				break;
			case TYPE_INDEXINT :
				$authority = new authority(0, $entity_id, AUT_TABLE_INDEXINT);
				break;
			case TYPE_CONCEPT :
				$authority = new authority(0, $entity_id, AUT_TABLE_CONCEPT);
				break;
			case TYPE_EXPLNUM:
				//TODO
				break;
			case TYPE_AUTHPERSO:
				$authority = new authority(0, $entity_id, AUT_TABLE_AUTHPERSO);
				break;
			case TYPE_CMS_SECTION:
				//TODO
				break;
			case TYPE_CMS_ARTICLE:
				//TODO
				break;
			default :
				break;
		}
		if (!empty($authority)) {
			// On est dans le cas d'une autorité
			$label = $authority->get_type_label().' : '.$authority->get_isbd();
		}
		return $label;
	}
	
	public static function get_label_from_type($entity_type){
		global $msg;
		$label = " : ";
		switch ($entity_type) {
			case TYPE_NOTICE :
				$label = $msg['130'].$label;
				break;
			case TYPE_AUTHOR :
				$label = $msg['133'].$label;
				break;
			case TYPE_CATEGORY :
				$label = $msg['134'].$label;
				break;
			case TYPE_PUBLISHER :
				$label = $msg['135'].$label;
				break;
			case TYPE_COLLECTION :
				$label = $msg['136'].$label;
				break;
			case TYPE_SUBCOLLECTION :
				$label = $msg['137'].$label;
				break;
			case TYPE_SERIE :
				$label = $msg['search_extended_series'].$label;
				break;
			case TYPE_TITRE_UNIFORME :
				$label = $msg['search_extended_titres_uniformes'].$label;
				break;
			case TYPE_INDEXINT :
				$label = $msg['search_extended_indexint'].$label;
				break;
			case TYPE_CONCEPT :
				$label = $msg['skos_view_concepts_concepts'].$label;
				break;
			case TYPE_EXPLNUM:
				break;
			case TYPE_AUTHPERSO:
				break;
			case TYPE_CMS_SECTION:
				//TODO
				break;
			case TYPE_CMS_ARTICLE:
				//TODO
				break;
			default :
				break;
		}
		return $label;
	}
	
	protected static function indexation_in_progress($in_progress) {
		global $pmb_indexation_in_progress;
		
		$pmb_indexation_in_progress = $in_progress;
		$query = "update parametres set valeur_param = '".$in_progress."' where type_param = 'pmb' and sstype_param = 'indexation_in_progress' ";
		pmb_mysql_query($query);
	}
	
	protected static function indexation_needed($needed) {
		global $pmb_indexation_needed;
		
		$pmb_indexation_needed = $needed;
		$query = "update parametres set valeur_param = '".$needed."' where type_param = 'pmb' and sstype_param = 'indexation_needed' ";
		pmb_mysql_query($query);
	}
}