<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_authorities_indexer.class.php,v 1.7 2019-05-27 12:55:59 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once $class_path.'/sphinx/sphinx_indexer.class.php';

class sphinx_authorities_indexer extends sphinx_indexer {
	protected $type;
	
	public function __construct() {
		$this->object_id = 'id_authority';
		$this->object_key = 'id_authority';
		$this->object_index_table = 'authorities_fields_global_index';
		$this->object_table = 'authorities';
		$this->filters = array(
		    'status'
		);
		parent::__construct();
	}
	
	public function fillIndex($object_id=0)
	{
	    global $sphinx_indexes_prefix;
		//$options['size'] = 80;
		$this->parse_file();
		$object_id+=0;
		//Remplissage des indexs...
		$rq='select '.$this->object_key.' from '.$this->object_table.' where type_object = '.$this->type.' '.($object_id!= 0 ? 'and '.$this->object_key.'='.$object_id : '').' order by 1';
		$res=pmb_mysql_query($rq);
		if ($res) {
			pmb_mysql_query('set session group_concat_max_len = 16777216');
			if( $object_id == 0) print ProgressBar::start(pmb_mysql_num_rows($res),"Index ".$this->default_index,$options);
			while ($object=pmb_mysql_fetch_object($res)) {
				//purge...
				$langs = $this->getAvailableLanguages();
				for($i=0 ; $i<count($langs) ; $i++){
					foreach($this->indexes as $index_name => $infos){
						if(!pmb_mysql_query('delete from '.$sphinx_indexes_prefix.$index_name.($langs[$i] != '' ? '_'.$langs[$i] :'').' where id = '.$object->{$this->object_key},$this->getDBHandler())){
							print $table. ' : '.pmb_mysql_error($this->getDBHandler()). "(".$query.")\n";;
						}
					}
				}
				//Construction de l'index
				$rq='select code_champ,code_ss_champ,lang,group_concat(value SEPARATOR "'.$this->getSeparator().'") as value from '.$this->object_index_table.' where '.$this->object_id.'= '.$object->{$this->object_key}.' and lang in ("'.implode('","',$this->getAvailableLanguages()).'") group by code_champ,code_ss_champ,lang';
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
						$keys.=$key;
						if(substr($key,0,2) !== "f_"){
						    $values.=$value;
						}else{
						    $values.='\''.$value.'\'';
						};
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
	
	protected function addSpecificsFilters($id,$filters=array()){
		$filters = parent::addSpecificsFilters($id,$filters);	
		return $filters;
	}
}