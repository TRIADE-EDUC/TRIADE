<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexation_record.class.php,v 1.9 2018-01-09 13:52:14 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/indexation.class.php");
require_once($class_path."/sphinx/sphinx_records_indexer.class.php");

//classe de calcul d'indexation des notices...
class indexation_record extends indexation {
	private static $sphinx_indexer = null;
	
	public function __construct($xml_filepath, $table_prefix, $type = 0) {
		parent::__construct($xml_filepath, $table_prefix, $type);
	}
	
	protected function get_indexation_lang() {
		global $indexation_lang;
		return $indexation_lang;
	}
	
	public static function get_sphinx_indexer(){
		global $include_path;
		if(!self::$sphinx_indexer){
			self::$sphinx_indexer = new sphinx_records_indexer();
		}
		return self::$sphinx_indexer;
	}
	
	public function maj($object_id,$datatype = 'all'){
		global $sphinx_active;
		parent::maj($object_id,$datatype);
		//SPHINX
		if($sphinx_active){
			$si = self::get_sphinx_indexer();
			$si->fillIndex($object_id);
		}
	}

	protected function delete_index($object_id,$datatype="all"){
		global $dbh;
		//qu'est-ce qu'on efface?
		if($datatype=='all') {
			$req_del="delete from ".$this->table_prefix."_mots_global_index where id_notice='".$object_id."' ";
			pmb_mysql_query($req_del,$dbh);
			//la table pour les recherche exacte
			$req_del="delete from ".$this->table_prefix."_fields_global_index where id_notice='".$object_id."' ";
			pmb_mysql_query($req_del,$dbh);
		}else{
			foreach($this->datatypes as $xml_datatype=> $codes){
				if($xml_datatype == $datatype){
					foreach($codes as $code_champ){
						$req_del="delete from ".$this->table_prefix."_mots_global_index where id_notice='".$object_id."' and code_champ='".$code_champ."'";
						pmb_mysql_query($req_del,$dbh);
						//la table pour les recherche exacte
						$req_del="delete from ".$this->table_prefix."_fields_global_index where id_notice='".$object_id."' and code_champ='".$code_champ."'";
						pmb_mysql_query($req_del,$dbh);
					}
				}
			}
		}
	}
	
	public function delete_objects_index($objects_ids=array(),$datatype="all"){
		global $dbh;
		
		//on s'assure qu'on a lu le XML et initialisé ce qu'il faut...
		if(!$this->initialized) {
			$this->init();
		}
		
		$req_del="delete ".$this->table_prefix."_global_index from ".$this->table_prefix."_global_index ".gen_where_in('num_notice', $objects_ids);
		pmb_mysql_query($req_del,$dbh);
		//qu'est-ce qu'on efface?
		if($datatype=='all') {
			$join_temporary_table = gen_where_in('id_notice', $objects_ids);
			$req_del="delete ".$this->table_prefix."_mots_global_index from ".$this->table_prefix."_mots_global_index ".$join_temporary_table;
			pmb_mysql_query($req_del,$dbh);
			//la table pour les recherche exacte
			$req_del="delete ".$this->table_prefix."_fields_global_index from ".$this->table_prefix."_fields_global_index ".$join_temporary_table;
			pmb_mysql_query($req_del,$dbh);
		}else{
			foreach($this->datatypes as $xml_datatype=> $codes){
				if($xml_datatype == $datatype){
					$join_temporary_table = gen_where_in('id_notice', $objects_ids);
 					$req_del="delete ".$this->table_prefix."_mots_global_index from ".$this->table_prefix."_mots_global_index ".$join_temporary_table." where code_champ in (".implode(',', $codes).")";
 					pmb_mysql_query($req_del,$dbh);
 					//la table pour les recherche exacte
 					$req_del="delete ".$this->table_prefix."_fields_global_index from ".$this->table_prefix."_fields_global_index ".$join_temporary_table." where code_champ in (".implode(',', $codes).")";
 					pmb_mysql_query($req_del,$dbh);
				}
			}
		}
	}
	
	//compile les tableaux et lance les requetes
	protected function save_elements($tab_insert, $tab_field_insert){
		global $dbh;
		if($tab_insert && count($tab_insert)){
			$req_insert="insert into ".$this->table_prefix."_mots_global_index(id_notice,code_champ,code_ss_champ,num_word,pond,position,field_position) values ".implode(',',$tab_insert)." ON DUPLICATE KEY UPDATE num_word = num_word";
			pmb_mysql_query($req_insert,$dbh);
		}
		if($tab_field_insert && count($tab_field_insert)){
			//la table pour les recherche exacte
			$req_insert="insert into ".$this->table_prefix."_fields_global_index(id_notice,code_champ,code_ss_champ,ordre,value,lang,pond,authority_num) values ".implode(',',$tab_field_insert)." ON DUPLICATE KEY UPDATE value = value";
			pmb_mysql_query($req_insert,$dbh);
		}
	}
}