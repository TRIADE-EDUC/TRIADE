<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dynamic_search_q_txt_i18n.class.php,v 1.6 2022/11/03 09:56:04 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($include_path."/search_queries/dynamics/dynamic_search.class.php");

class dynamic_search_q_txt_i18n extends dynamic_search {
    
	public function make_human_query($field = array()) {
		$field_aff = ($field['txt'] != '' ? $field['txt'] : '*')."|||".$field['lang']."|||".(isset($field['qualification']) ? $field['qualification'] : '');
		return $this->search->pp[$this->xml_prefix]->get_formatted_output(array(0=>$field_aff),$this->id);
	}
	
	public function get_query($field = array()) {
		//Recuperation de l'operateur
    	$op="op_".$this->n_ligne."_".$this->xml_prefix."_".$this->id;
		global ${$op};
		$q_index=$this->params["QUERIES_INDEX"];
		$q=$this->params["QUERIES"][$q_index[${$op}]];
		
		$main = "select distinct ".$this->search->keyName." from ".$this->search->tableName." ";
		$main .= $this->get_join_query();
		
		$restricts = array();
		if($field['txt'] != '') {
			if($q["KEEP_EMPTYWORD"])	$field['txt']=strip_empty_chars($field['txt']);
			elseif ($q["REGDIACRIT"]) $field['txt']=strip_empty_words($field['txt']);
			$restrict_query = $this->prefix."_custom_champ = ".$this->id;
			$restrict_query .= " and ".$this->get_restrict_query_with_operator("SUBSTR(".$this->prefix."_custom_".$this->params['DATATYPE'].", 1, INSTR(".$this->prefix."_custom_".$this->params['DATATYPE'].", '|||')-1)", ${$op});
			if ($q["MULTIPLE_WORDS"]) {
				$terms=explode(" ", $field['txt']);
				//Pour chaque terme
				$multiple_terms=array();
				for ($k=0; $k<count($terms); $k++) {
					$mt=str_replace("!!p!!",addslashes($terms[$k]), $restrict_query);
					$multiple_terms[]="(".$mt.")";
				}
				$restricts[] = implode(" ".$q["MULTIPLE_OPERATOR"]." ",$multiple_terms);
			} else {
				$restricts[] = str_replace("!!p!!", addslashes($field['txt']), $restrict_query);
			}
		}
		if(isset($field['qualification']) && $field['qualification'] != '') {
			$restricts[] = $this->prefix."_custom_champ = ".$this->id." and SUBSTR(".$this->prefix."_custom_".$this->params['DATATYPE'].", INSTR(INSTR(".$this->prefix."_custom_".$this->params['DATATYPE'].", '|||'),'|||')-1) like '%".addslashes($field['qualification'])."%'";
		}
		if($field['lang'] != '') {
			$restricts[] = $this->prefix."_custom_champ = ".$this->id." and SUBSTR(".$this->prefix."_custom_".$this->params['DATATYPE'].", IF(INSTR(".$this->prefix."_custom_".$this->params['DATATYPE'].", '|||'), INSTR(".$this->prefix."_custom_".$this->params['DATATYPE'].", '|||')+3, LENGTH(".$this->prefix."_custom_".$this->params['DATATYPE'].")),3) like '%".addslashes($field['lang'])."%'";
		}
		if(count($restricts)) {
			$main .= " where (".implode(') and (', $restricts).")";
		}
// 		if ($q["WORD"]){
// 			//recherche par terme...
// 			$searcher = new $q['CLASS']($field[$j],$s[1]);
// 			$main = $searcher->get_full_query();
// 		}
		return $main;
	}
}