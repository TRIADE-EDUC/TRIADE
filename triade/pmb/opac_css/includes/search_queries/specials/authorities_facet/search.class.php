<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.4 2022/01/10 10:23:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/rec_history.inc.php");
require_once($include_path."/search_queries/specials/facette/search.class.php");

//Classe de gestion de la recherche spécial "facette"

class authorities_facet extends facette_search{
	public $id;
	public $n_ligne;
	public $params;
	public $search;
	public $champ_base;

    public function make_search(){
		global $mode;
    	$valeur = "field_".$this->n_ligne."_s_".$this->id;
    	global ${$valeur};
    	
    	$filter_array = ${$valeur};
    	if (!is_array($filter_array[0])) {
	   		$tmpValeur = unserialize(stripslashes($filter_array[0]));
	  		
	    	if ($tmpValeur !== false) {
	    		${$valeur} = $tmpValeur;
	    	}
    	}
    	$filter_array = ${$valeur};

    	$ids_authorities = '';
    	foreach ($filter_array as $v) {
    	
    		$filter_value = $v[1];
    		$filter_field = $v[2];
    		$filter_subfield = $v[3];
    		
    		switch ($mode) {
//     			case 'external':
//     				$qs = facettes_external::get_filter_query_by_facette($filter_field, $filter_subfield, $filter_value);
//     				if($ids_notices) {
//     					$qs .= ' where recid IN ('.$ids_notices.')';
//     				}
//     				break;
    			default:
		    		$qs = 'SELECT id_authority FROM authorities_fields_global_index WHERE code_champ = '.($filter_field+0).' AND code_ss_champ = '.($filter_subfield+0).' AND (';
		    		foreach ($filter_value as $k2=>$v2) {
		    			if ($k2) {
		    				$qs .= ' OR ';
		    			}
		    			$qs .= 'value ="'.addslashes($v2).'"';
		    		}
		    		$qs .= ')';    		
		    		if($ids_authorities) {
		    			$qs .= ' and id_authority in ('.$ids_authorities.')';
		    		}
    				break;
    		}
    		$rs = pmb_mysql_query($qs);
    		
    		$t_ids_authorities=array();
    		
    		if(pmb_mysql_num_rows($rs)) {
    			$ids_authorities='';
    			while ($o=pmb_mysql_fetch_object($rs)) {
    				$t_ids_authorities[]=$o->id_authority;
    			}
    			$ids_authorities = implode(',',$t_ids_authorities);
    		}else{
    			break;
    		}
    	}
    	
    	unset($ids_authorities);
    	$last_table = 'table_facette_temp_'.$this->n_ligne.'_'.md5(microtime());
    	$qc_last_table = 'create temporary table '.$last_table.' (id_authority int, index i_id_authority(id_authority))';
    	pmb_mysql_query($qc_last_table);
    	if(count($t_ids_authorities)) {
    		$qi_last_table = 'insert ignore into '.$last_table.' values ('.implode('),(', $t_ids_authorities).')';
    		pmb_mysql_query($qi_last_table);
    	}
    	unset($t_ids_authorities);
    	return $last_table;
    }
}
?>