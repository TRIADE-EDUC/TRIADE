<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_concepts_indexer.class.php,v 1.9 2019-05-28 12:38:12 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once $class_path.'/sphinx/sphinx_authorities_indexer.class.php';

class sphinx_concepts_indexer extends sphinx_authorities_indexer {
	
	public function __construct() {
		global $include_path;
		parent::__construct();
		$this->type = AUT_TABLE_CONCEPT;
		$this->default_index = "concepts";
		$this->object_id = 'id_item';
		$this->object_index_table = 'skos_fields_global_index';		
		$this->filters = ['status', 'scheme'];
// 		$this->setChampBaseFilepath($include_path."/indexation/authorities/authperso/champs_base.xml");
	}
	
	protected function addSpecificsFilters($id, $filters =array()){
		$filters = parent::addSpecificsFilters($id, $filters);

		//Récupération du statut
		$query = "select num_statut, if(authority_num is not null, authority_num, 0) as scheme_num from authorities left join skos_fields_global_index on num_object = id_item and code_champ = 4 and code_ss_champ = 1 where id_authority = ".$id." and type_object = ".$this->type;
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		$filters['status'] = $row->num_statut;
		$filters['scheme'] = $row->scheme_num;
		return $filters;
	}
	
	protected function parse_file()
	{
		global $base_path;
		if (is_array($this->indexes) && count($this->indexes)) {
			return;
		}
		$onto_store_config = array(
				/* db */
				'db_name' => DATA_BASE,
				'db_user' => USER_NAME,
				'db_pwd' => USER_PASS,
				'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'ontology',
				/* stop after 100 errors */
				'max_errors' => 100,
				'store_strip_mb_comp_str' => 0
		);
		$data_store_config = array(
				/* db */
				'db_name' => DATA_BASE,
				'db_user' => USER_NAME,
				'db_pwd' => USER_PASS,
				'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'rdfstore',
				/* stop after 100 errors */
				'max_errors' => 100,
				'store_strip_mb_comp_str' => 0
		);
		
		$tab_namespaces = array(
				"skos"	=> "http://www.w3.org/2004/02/skos/core#",
				"dc"	=> "http://purl.org/dc/elements/1.1",
				"dct"	=> "http://purl.org/dc/terms/",
				"owl"	=> "http://www.w3.org/2002/07/owl#",
				"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
				"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
				"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
				"pmb"	=> "http://www.pmbservices.fr/ontology#"
		);
		
		$onto_index = onto_index::get_instance("skos");	
		$onto_index->load_handler($base_path."/classes/rdf/skos_pmb.rdf", "arc2", $onto_store_config, "arc2", $data_store_config,$tab_namespaces,'http://www.w3.org/2004/02/skos/core#prefLabel');
		$onto_index->init();
		
		if (!count($onto_index->tab_code_champ)) {
			return ;
		}

		// On initialise le tableau
		if(!isset($this->indexes[$this->default_index])){
			$this->indexes[$this->default_index] = array(
					'fields' => array(),
					'attributes' => array('dummy')
			);
		}
		foreach ($onto_index->tab_code_champ as $index) {
			foreach ($index as $sub_index) {
				$field = 'f_'.str_pad($sub_index['champ'], 3, "0", STR_PAD_LEFT).'_'.str_pad($sub_index['ss_champ'], 2,"0",STR_PAD_LEFT);
				$this->insert_index[$field] = $this->default_index;
				$this->fields_pond[$field] = $sub_index['pond']*$this->multiple;
				$this->indexes[$this->default_index]['fields'][] = $field;
// 				$this->indexes[$this->default_index]['attributes'][] = $field;
			}
		}
		$this->indexes[$this->default_index]['attributes'] = $this->filters;
	}
	
	public function fillIndex($object_id=0)
	{
		global $sphinx_indexes_prefix;
		//$options['size'] = 80;
		$this->parse_file();
		$object_id+=0;
		//Remplissage des indexs...
		$rq='select '.$this->object_key.', num_object from '.$this->object_table.' where type_object = '.$this->type.' '.($object_id!= 0 ? 'and num_object = '.$object_id : '').' order by 1';
		$res=pmb_mysql_query($rq);
		if ($res) {
			pmb_mysql_query('set session group_concat_max_len = 16777216');
			if( $object_id == 0) print ProgressBar::start(pmb_mysql_num_rows($res), "Index ".$this->default_index, $options);
			$langs = $this->getAvailableLanguages();
			while ($object=pmb_mysql_fetch_object($res)) {
				for($i=0 ; $i<count($langs) ; $i++){
					foreach($this->indexes as $index_name => $infos){
					    if(!pmb_mysql_query('delete from '.$sphinx_indexes_prefix.$index_name.($langs[$i] != '' ? '_'.$langs[$i] :'').' where id = '.$object->{$this->object_key},$this->getDBHandler())){
							print $table. ' : '.pmb_mysql_error($this->getDBHandler()). "(".$query.")\n";;
						}
					}
				}
				//Construction de l'index
				$rq='select code_champ, code_ss_champ, lang, group_concat(value SEPARATOR "'.$this->getSeparator().'") as value from '.$this->object_index_table.' where code_champ < 100 and '.$this->object_id.'= '.$object->num_object.' and lang in ("'.implode('","',$this->getAvailableLanguages()).'") group by code_champ, code_ss_champ, lang';
				$inserts = array();
				$res_notice=pmb_mysql_query($rq);
				while ($champ=pmb_mysql_fetch_object($res_notice)) {
					
					if(in_array($champ->lang,$langs)){
						$code_champ=str_pad($champ->code_champ, 3,"0",STR_PAD_LEFT);
						$code_ss_champ=str_pad($champ->code_ss_champ, 2,"0",STR_PAD_LEFT);
						$field='f_'.$code_champ.'_'.$code_ss_champ;
	
						if($this->insert_index[$field]){
							$inserts[$this->insert_index[$field].($champ->lang ? '_'.$champ->lang : '')][$field] = addslashes($champ->value);
						}
	
					}
				}
				$inserts = $this->getSpecificsFiltersValues($object->{$this->object_key},$inserts);
				foreach($inserts as $table => $fields){
					$keys = $values =  "";
					foreach($fields as $key => $value){
						if($keys){
							$keys.=",";
							$values.=",";
						}
						$keys.= $key;
						if(substr($key,0,2) !== "f_"){
						    $values.=$value;
						}else{
						    $values.='\''.$value.'\'';
						}
					}
					$query = 'insert into '.$sphinx_indexes_prefix.$table.' (id,'.$keys.') values ('.$object->{$this->object_key}.','.$values.')';
					if(!pmb_mysql_query($query,$this->getDBHandler())){
						print $table. ' : '.pmb_mysql_error($this->getDBHandler()). "(".$query.")\n";
					}
				}
				if( $object_id == 0) print ProgressBar::next();
			}
			if( $object_id == 0) print ProgressBar::finish();
		}
	}
}