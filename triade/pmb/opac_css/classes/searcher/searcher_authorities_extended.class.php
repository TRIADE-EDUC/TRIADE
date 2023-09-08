<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_authorities_extended.class.php,v 1.6 2019-06-13 15:33:03 ccraig Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_autorities.class.php');
require_once($class_path.'/search_authorities.class.php');


//un jour ca sera utile
class searcher_authorities_extended extends searcher_autorities {
	
	protected $authority_type = 0;
	
	protected $serialized_query;	// recherche sérialisée
	protected $with_make_search; //Savoir si on peut avoir la pertinance ou non
	public $table;				// table tempo de la multi

	public function __construct($serialized_query=""){
		$this->with_make_search = false;
		$this->serialized_query = $serialized_query;
		parent::__construct("");
	}

	public function _get_search_type(){
		return "extended_authorities";
	}

	private function get_instance() {
	    global $es;
	    if(is_object($es) && get_class($es) != "search_authorities"){
	        $es = new search_authorities("search_fields_authorities");
	        return $es;
	    }
	    $es = new search_authorities("search_fields_authorities");
	    return $es;
	}
	
	protected function _get_user_query(){
		$es = $this->get_instance();
		return $es->serialize_search();
	}

	protected function _get_search_query(){
		global $msg;
		$es = $this->get_instance();
		if($this->serialized_query){
			$es->unserialize_search($this->serialized_query);
		}else{
			
			global $search;
    		//Vérification des champs vides
    		for ($i=0; $i<count($search); $i++) {
    			if($i==0){//On supprime le premier opérateur inter (il est renseigné pour les recherches prédéfinies avec plusieurs champs et une recherche avec le premier champ vide
    				$inter="inter_".$i."_".$search[$i];
    				global ${$inter};
    				${$inter}="";
    			}
	    		$op="op_".$i."_".$search[$i];
    			global ${$op};
    			$field_="field_".$i."_".$search[$i];
	   			global ${$field_};
	   			$field=${$field_};
	   			$s=explode("_",$search[$i]);
	   			if ($s[0]=="f") {
		    		$champ=$es->fixedfields[$s[1]]["TITLE"];
	   			} elseif ($s[0]=="s") {
		    		$champ=$es->specialfields[$s[1]]["TITLE"];
	   			} else {
		    		$champ=$es->pp->t_fields[$s[1]]["TITRE"];
	   			}
	   			if (((string)$field[0]=="") && (!$es->op_empty[${$op}])) {
		    		$search_error_message=sprintf($msg["extended_empty_field"],$champ);
	   				$flag=true;
					break;
	   			}
	   		}
    	}
    	$this->with_make_search=true;
    	$this->table = $es->make_search($this->get_temporary_table_name("_".rand(0,10)."_"));   

        $query = "describe $this->table";
        $result = pmb_mysql_query($query);
        $columns = pmb_mysql_fetch_assoc($result);

        // Détection si concept
        if (in_array('id_item', $columns)) {
            return "select id_item as ".$this->object_key.", pert from ".$this->table;
        }

    	return "select ".$this->object_index_key." as ".$this->object_key.", pert from ".$this->table;
	}

	protected function _get_pert($with_explnum=false, $query=false){
		global $dbh;
		if(!$this->objects_ids) return;
		if($this->with_make_search){
			$this->table_tempo = $this->get_temporary_table_name('get_pert');
			
			$rqt = "create temporary table ".$this->table_tempo." select * from ".$this->table." where ".$this->object_index_key." in(".$this->objects_ids.")";
			$res = pmb_mysql_query($rqt,$dbh);
			pmb_mysql_query("alter table ".$this->table_tempo." add index i_id(".$this->object_index_key.")",$dbh);
			
			$this->_add_pert($this->table_tempo);
		}else{
			$this->table_tempo = $this->get_temporary_table_name('get_pert_2');
			$rqt = "create temporary table ".$this->table_tempo." select ".$this->object_index_key.",100 as pert from authorities where ".$this->object_index_key." in(".$this->objects_ids.")";
			$res = pmb_mysql_query($rqt,$dbh);
			pmb_mysql_query("alter table ".$this->table_tempo." add index i_id(".$this->object_index_key.")",$dbh);
		}
	}
	
	protected function _add_pert($table_name){
		if((!pmb_mysql_num_rows(pmb_mysql_query('show columns from '.$table_name.' like "pert"')))) {
			$query = "alter table ".$table_name." add pert decimal(16,1) default 1";
			@pmb_mysql_query($query);
		}
	}
	
	public function get_result(){
		global $dbh;
		$cache_result = $this->_get_in_cache();
		if($cache_result===false){
			$this->_get_objects_ids();
			$this->_filter_results();
			$this->_set_in_cache();
			if($this->objects_ids){
				$_SESSION['tab_result'] = $this->objects_ids;
			}
		}else{
			$this->objects_ids = $cache_result;
			if(!$this->objects_ids) return array();
			$this->table = $this->get_temporary_table_name('get_result');
			$rqt = "create temporary table ".$this->table." engine=memory select ".$this->object_index_key." from authorities where ".$this->object_index_key." in(".$this->objects_ids.")";
			$res = pmb_mysql_query($rqt,$dbh);
			pmb_mysql_query("alter table ".$this->table." add index i_id(".$this->object_index_key.")",$dbh);			
		}
		return $this->objects_ids;
	}
}