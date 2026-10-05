<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexation_authority.class.php,v 1.28.4.1 2024/11/20 13:22:52 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/indexation.class.php");
require_once($class_path."/authority.class.php");

//classe de calcul d'indexation des autorités...
class indexation_authority extends indexation {

	private static $sphinx_indexer = null;
	protected static $authorities_instance = array();
	
	public function __construct($xml_filepath, $table_prefix, $type = 0) {
		parent::__construct($xml_filepath, $table_prefix, $type);
	}
	
	// compile les tableaux et lance les requetes
	protected function save_elements($tab_insert, $tab_field_insert){
		if(!$this->type) return false;
		
		if($tab_insert && count($tab_insert)){
			$req_insert="insert into ".$this->table_prefix."_words_global_index(id_authority,type,code_champ,code_ss_champ,num_word,pond,position,field_position) values ".implode(',',$tab_insert)." ON DUPLICATE KEY UPDATE num_word = num_word";
			pmb_mysql_query($req_insert);
		}
		if($tab_field_insert && count($tab_field_insert)){
			//la table pour les recherche exacte
			$req_insert="insert into ".$this->table_prefix."_fields_global_index(id_authority,type,code_champ,code_ss_champ,ordre,value,lang,pond,authority_num) values ".implode(',',$tab_field_insert)." ON DUPLICATE KEY UPDATE value = value";
			pmb_mysql_query($req_insert);	
		}
	}
	
	protected function delete_index($object_id,$datatype="all"){
		if(!$this->type) return false;
				
		$authority = static::get_authority_instance($object_id, $this->type);
		$id_authority = $authority->get_id();
		//qu'est-ce qu'on efface?
		if($datatype=='all') {
			$req_del="delete from ".$this->table_prefix."_words_global_index where id_authority = '".$id_authority."' ";
			pmb_mysql_query($req_del);
			//la table pour les recherche exacte
			$req_del="delete from ".$this->table_prefix."_fields_global_index where id_authority = '".$id_authority."' ";
			pmb_mysql_query($req_del);
		}else{
			foreach($this->datatypes as $xml_datatype=> $codes){
				if($xml_datatype == $datatype){
					foreach($codes as $code_champ){
						$req_del = "delete from ".$this->table_prefix."_words_global_index where id_authority = '".$id_authority."' and code_champ = '".$code_champ."'";
						pmb_mysql_query($req_del);
						//la table pour les recherche exacte
						$req_del = "delete from ".$this->table_prefix."_fields_global_index where id_authority = '".$id_authority."' and code_champ = '".$code_champ."'";
						pmb_mysql_query($req_del);
					}
				}
			}
		}
	}
	
	protected function get_tab_field_insert($object_id, $infos, $order_fields, $isbd, $lang = '', $autorite = 0) {
		$authority = static::get_authority_instance($object_id, $this->type);
		return "(".$authority->get_id().", ".$this->type.", ".$infos["champ"].", ".$infos["ss_champ"].", ".$order_fields.", '".addslashes(trim($isbd))."', '".addslashes(trim($lang))."', ".$infos["pond"].", ".(intval($autorite)).")";
	}
	
	protected function get_tab_insert($object_id, $infos, $num_word, $order_fields, $pos) {
		$authority = static::get_authority_instance($object_id, $this->type);
		return "(".$authority->get_id().", ".$this->type.", ".$infos["champ"].", ".$infos["ss_champ"].", ".$num_word.", ".$infos["pond"].", ".$order_fields.", ".$pos.")";
	}
	
	public static function delete_all_index($object_id, $table_prefix, $reference_key, $type = ""){
		global $sphinx_active;
		
		if(!$type) return false;
		
		$authority = static::get_authority_instance($object_id, $type);
		$id_authority = $authority->get_id();
		$req_del="delete from ".$table_prefix."_words_global_index where ".$reference_key."='".$id_authority."'";
		pmb_mysql_query($req_del);
		//la table pour les recherche exacte
		$req_del="delete from ".$table_prefix."_fields_global_index where ".$reference_key."='".$id_authority."'";
		pmb_mysql_query($req_del);

		if ($sphinx_active){
			$si = self::get_sphinx_indexer($type);
			if(is_object($si)) {
				$si->deleteIndex($id_authority);
			}
		}
	}
	
	public function delete_objects_index($objects_ids=array(),$datatype="all"){
		if(!$this->type) return false;
			
		//on s'assure qu'on a lu le XML et initialisé ce qu'il faut...
		if(!$this->initialized) {
			$this->init();
		}
		
		$authorities_ids = array();
		foreach ($objects_ids as $object_id) {
			$authority = static::get_authority_instance($object_id, $this->type);
			$authorities_ids[] = $authority->get_id();
		}
		
		//qu'est-ce qu'on efface?
		if($datatype=='all') {
			$join_temporary_table = gen_where_in('id_authority', $authorities_ids);
			$req_del="delete ".$this->table_prefix."_words_global_index from ".$this->table_prefix."_words_global_index ".$join_temporary_table;
			pmb_mysql_query($req_del);
			//la table pour les recherche exacte
			$req_del="delete ".$this->table_prefix."_fields_global_index from ".$this->table_prefix."_fields_global_index ".$join_temporary_table;
			pmb_mysql_query($req_del);
		}else{
			foreach($this->datatypes as $xml_datatype=> $codes){
				if($xml_datatype == $datatype){
					$join_temporary_table = gen_where_in('id_authority', $authorities_ids);
 					$req_del = "delete ".$this->table_prefix."_words_global_index from ".$this->table_prefix."_words_global_index ".$join_temporary_table." where code_champ in (".implode(',', $codes).")";
 					pmb_mysql_query($req_del);
 					//la table pour les recherche exacte
 					$req_del = "delete ".$this->table_prefix."_fields_global_index from ".$this->table_prefix."_fields_global_index ".$join_temporary_table." where code_champ in (".implode(',', $codes).")";
 					pmb_mysql_query($req_del);
				}
			}
		}
	}
	
	public static function get_sphinx_indexer($type){
		// Pour des raisons historiques, on n'a pas tjrs la constante pour les autorités persos...
		// On s'assure qu'un résidu n'altère pas le bon fonctionnement
		if($type > 1000 && $type <1999){
		    $type = AUT_TABLE_AUTHPERSO;
		}
		if(!isset(self::$sphinx_indexer[$type])){
			switch ($type){
				case AUT_TABLE_AUTHORS :
					$classname = 'sphinx_authors_indexer';
					break;
				case AUT_TABLE_CATEG :
					$classname = 'sphinx_categories_indexer';
					break;
				case AUT_TABLE_PUBLISHERS :
					$classname = 'sphinx_publishers_indexer';
					break;
				case AUT_TABLE_COLLECTIONS :
					$classname = 'sphinx_collections_indexer';
					break;
				case AUT_TABLE_SUB_COLLECTIONS :
					$classname = 'sphinx_subcollections_indexer';
					break;
				case AUT_TABLE_SERIES : 
					$classname = 'sphinx_series_indexer';
					break;
				case AUT_TABLE_TITRES_UNIFORMES : // 7
					$classname = 'sphinx_titres_uniformes_indexer';
					break;
				case AUT_TABLE_INDEXINT : 
					$classname = 'sphinx_indexint_indexer';
					break;
				case AUT_TABLE_AUTHPERSO :
				    $classname = 'sphinx_authperso_indexer';
				    break;
				case AUT_TABLE_CONCEPT :
				    $classname = 'sphinx_concepts_indexer';
				    break;
			}
			if(!empty($classname) && class_exists($classname)){
				self::$sphinx_indexer[$type] = new $classname();
			}else{
				self::$sphinx_indexer[$type] = null;
			}
		}
		return self::$sphinx_indexer[$type];
	}
	
	public function maj($object_id,$datatype = 'all'){
	    global $sphinx_active;
	    
	    $authority = static::get_authority_instance($object_id, $this->type);
	    if ('all' == $datatype) {
	        $vedette_composee_found = vedette_composee::get_vedettes_built_with_element($authority->get_num_object(), $authority->get_type_const());
	        foreach ($vedette_composee_found as $vedette_id) {
	            $vedette = new vedette_composee($vedette_id);
	            $vedette->update_label();
	            $vedette->save();
	            vedette_link::update_objects_linked_with_vedette($vedette);
	        }
	    }
	    parent::maj($object_id,$datatype);
	    
	    //SPHINX
	    if($sphinx_active){
	        $si = self::get_sphinx_indexer($this->type);
	        if(is_object($si)) {
	            $si->fillIndex($authority->get_id());
	        }
	    }
	}
	
	protected static function get_authority_instance($object_id, $object_type) {
		if(!isset(static::$authorities_instance[$object_type][$object_id])) {
			static::$authorities_instance[$object_type][$object_id] = new authority(0, $object_id, $object_type);
		}
		return static::$authorities_instance[$object_type][$object_id];
	}
}