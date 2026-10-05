<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexation_authorities.class.php,v 1.4 2024/04/12 09:41:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/indexation/indexation_entities.class.php");

//classe de calcul d'indexation des notices...
class indexation_authorities extends indexation_entities {
	
	protected static $authorities_instance = array();
	
	public function __construct($xml_filepath, $table_prefix, $type = 0){
		$this->fields_prefix = 'authorities_fields';
		$this->words_prefix = 'authorities_words';
		parent::__construct($xml_filepath, $table_prefix, $type);
	}
	
	public function raz_fields_table() {
		//remise a zero de la table au début
	    $this->type = intval($this->type);
	    if($this->type) {
	        pmb_mysql_query("DELETE FROM ".$this->fields_prefix."_global_index WHERE type = ".$this->type);
	    } else {
	        parent::raz_fields_table();
	    }
	}
	
	public function raz_words_table() {
		//remise a zero de la table au début
	    $this->type = intval($this->type);
	    if($this->type) {
	        pmb_mysql_query("DELETE FROM ".$this->words_prefix."_global_index WHERE type = ".$this->type);
	    } else {
	        parent::raz_words_table();
	    }
	}
	
	protected function clean_temporary_files() {
	    $indexation_directory = authority::get_indexation_directory($this->type);
	    netbase_entities::clean_files($this->directory_files, $indexation_directory);
	}
	
	protected function get_prefix_temporary_file() {
	    if(empty($this->prefix_temporary_file)) {
	        $indexation_directory = authority::get_indexation_directory($this->type);
	        $this->prefix_temporary_file = "indexation_".$indexation_directory."_".LOCATION;
	    }
	    return $this->prefix_temporary_file;
	    
	}
	
	protected function push_elements($tab_insert, $tab_field_insert){
		if($tab_insert && count($tab_insert)){
			$req_insert="insert into ".$this->table_prefix."_words_global_index(id_authority,type,code_champ,code_ss_champ,num_word,pond,position,field_position) values ".implode(',',$tab_insert)." ON DUPLICATE KEY UPDATE num_word = num_word";
			file_put_contents($this->directory_files.$this->words_prefix.'_global_index.sql', $req_insert."\r\n", FILE_APPEND);
		}
		if($tab_field_insert && count($tab_field_insert)){
			//la table pour les recherche exacte
			$req_insert="insert into ".$this->table_prefix."_fields_global_index(id_authority,type,code_champ,code_ss_champ,ordre,value,lang,pond,authority_num) values ".implode(',',$tab_field_insert)." ON DUPLICATE KEY UPDATE value = value";
			file_put_contents($this->directory_files.$this->fields_prefix.'_global_index.sql', $req_insert."\r\n", FILE_APPEND);
		}
	}
	
	protected function save_elements($tab_insert, $tab_field_insert){
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
	
	protected function get_tab_field_insert($object_id, $infos, $order_fields, $isbd, $lang = '', $autorite = 0) {
		$authority = static::get_authority_instance($object_id, $this->type);
		return "(".$authority->get_id().", ".$this->type.", ".$infos["champ"].", ".$infos["ss_champ"].", ".$order_fields.", '".addslashes(trim($isbd))."', '".addslashes(trim($lang))."', ".$infos["pond"].", ".(intval($autorite)).")";
	}
	
	protected function get_tab_insert($object_id, $infos, $num_word, $order_fields, $pos) {
		$authority = static::get_authority_instance($object_id, $this->type);
		return "(".$authority->get_id().", ".$this->type.", ".$infos["champ"].", ".$infos["ss_champ"].", ".$num_word.", ".$infos["pond"].", ".$order_fields.", ".$pos.")";
	}
	
	public function get_label() {
		global $msg;
		
		$code = authority::get_indexation_directory($this->type);
		if(!empty($code)) {
			switch ($code) {
				case 'subcollections':
					return $msg['nettoyage_reindex_sub_collections'];
				default:
					return $msg['nettoyage_reindex_'.$code];
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