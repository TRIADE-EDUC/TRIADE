<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.21 2018-09-14 10:25:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/rec_history.inc.php");

//Classe de gestion de la recherche spécial "facette"

class facette_search {
	public $id;
	public $n_ligne;
	public $params;
	public $search;
	public $champ_base;
	public $xml_file;
	
	//Constructeur
    public function __construct($id,$n_ligne,$params,&$search) {
    	$this->id=$id;
    	$this->n_ligne=$n_ligne;
    	$this->params=$params;
    	$this->search=&$search;
    	
    	//les facettes sont désormais un tableau de tableaux
    	//il faut parfois les desérialiser quand on est passé par un formulaire
    	$field_name="field_".$this->n_ligne."_s_".$this->id;
    	global ${$field_name},$launch_search;
    	$valeur = ${$field_name};
    	if (!is_array($valeur[0])) {
    		$tmpValeur = unserialize(stripslashes($valeur[0]));
    		while (($tmpValeur !== false) && (!is_array($tmpValeur[0]))) {
    			$tmpValeur=unserialize(stripslashes($tmpValeur[0]));
    		}
    		if ($tmpValeur !== false) {
    			$valeur = $tmpValeur;
    			${$field_name} = $tmpValeur;
    		}
    	}
    }
    
	public function get_op() {
    	$operators = array();
    	if ($_SESSION["nb_queries"]!=0) {
    		$operators["EQ"]="=";
    	}
    	return $operators;
    }
    
    public function make_search(){
        global $dbh;
        global $mode;
        
        $prefix = '';
        
        switch($this->xml_file){
            case 'search_fields_authorities':
            case 'search_fields_authorities_subst':
                $plural_prefix = 'authorities';
                $prefix = 'authority';
                $tempo_key_name = 'id_authority';
                break;
            default:
                $plural_prefix = 'notices'; 
                $prefix = 'notice';
                $tempo_key_name = 'notice_id';
                break;
        }
        
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
        
        //TODO : c'est là qu(il faut tout faire
        $ids = '';
        if(is_array($filter_array)) {
	        foreach ($filter_array as $k=>$v) {
	            
	            $filter_value = $v[1];
	            $filter_field = $v[2];
	            $filter_subfield = $v[3];
	            
	            switch ($mode) {
	                case 'external':
	                    $qs = facettes_external::get_filter_query_by_facette($filter_field, $filter_subfield, $filter_value);
	                    if($ids) {
	                        $qs .= ' where recid IN ('.$ids.')';
	                    }
	                    break;
	                default:
	                    $qs = 'SELECT id_'.$prefix.' FROM '.$plural_prefix.'_fields_global_index WHERE code_champ = '.($filter_field+0).' AND code_ss_champ = '.($filter_subfield+0).' AND (';
	                    foreach ($filter_value as $k2=>$v2) {
	                        if ($k2) {
	                            $qs .= ' OR ';
	                        }
	                        $qs .= 'value ="'.addslashes($v2).'"';
	                    }
	                    $qs .= ')';
	                    if($ids) {
	                        $qs .= ' and id_'.$prefix.' in ('.$ids.')';
	                    }
	                    break;
	            }
	            $rs = pmb_mysql_query($qs, $dbh) or die (mysql_error());
	            
	            $t_ids=array();
	            
	            if(pmb_mysql_num_rows($rs)) {
	                $ids='';
	                while ($o=pmb_mysql_fetch_object($rs)) {
	                    $t_ids[]= $o->{'id_'.$prefix};
	                }
	                $ids = implode(',',$t_ids);
	            }else{
	                break;
	            }
	        }
        }
        unset($ids);
//         $t_ids = array_slice($t_ids, 0, 5);
        $last_table = 'table_facette_temp_'.$this->n_ligne.'_'.md5(microtime());
        $qc_last_table = 'create temporary table '.$last_table.' ('.$tempo_key_name.' int, index i_'.$prefix.'_id('.$tempo_key_name.'))';
        pmb_mysql_query($qc_last_table,$dbh) or die ();
        if(count($t_ids)) {
            $qi_last_table = 'insert ignore into '.$last_table.' values ('.implode('),(', $t_ids).')';
            pmb_mysql_query($qi_last_table,$dbh) or die ();
        }
        unset($t_ids);
        return $last_table;
    } 
    
    public function make_human_query(){
		global $dbh, $champ_base, $msg;
		global $mode;
		
		$literral_words = array();
    	
    	$valeur="field_".$this->n_ligne."_s_".$this->id;
    	global ${$valeur};
    	$valeur = ${$valeur};
    	$item_literal_words = array();
    	if(is_array($valeur)) {
	    	foreach ($valeur as $k=>$v) {
		    	$filter_value = $v[1];
		    	$filter_name = $v[0];
		    	$filter_field = $v[2];
		    	$filter_subfield = $v[3];
		    	
		    	$libValue = "";
		    	foreach ($filter_value as $value) {
		    		if ($libValue) $libValue .= ' '.$msg["search_or"].' ';
		    		switch ($mode) {
		    			case 'external':
		    				$libValue .= facettes_external::get_formatted_value($filter_field, $filter_subfield, $value);
		    				break;
		    			default:
		    				$libValue .= facettes::get_formatted_value($filter_field, $filter_subfield, $value);
		    				break;
		    		}
		    	}
				$item_literal_words[] = stripslashes($filter_name)." : '".stripslashes($libValue)."'";
	    	}
    	}
    	
    	$literral_words[] = implode(' '.$msg["search_and"].' ',$item_literal_words);
    	
    	return $literral_words;
    }
    
    public function make_unimarc_query(){
    	//Récupération de la valeur de saisie
    	$valeur_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$valeur_};
    	$valeur=${$valeur_};
    	return "";
    }
    
    public function get_input_box() {
    	global $charset, $dbh, $msg;
    	
    	$field_name="field_".$this->n_ligne."_s_".$this->id;
    	global ${$field_name},$launch_search;
    	$valeur = ${$field_name};

    	$item_literal_words = array();
    	
    	if(is_array($valeur)) {
	    	foreach ($valeur as $k=>$v) {
		    	$filter_value = $v[1];
		    	$filter_name = $v[0];
	
		    	if (count($filter_value)==1) {
		    		$libValue = $filter_value[0];
		    	} else {
		    		$libValue = implode(' '.$msg["search_or"].' ',$filter_value);
		    	}
				$item_literal_words[] = stripslashes($filter_name)." : '".stripslashes($libValue)."'";
	    	}
    	}
    	
    	$literral_words = implode(' '.$msg["search_and"].' ',$item_literal_words);
    	
    	$form=$literral_words;
    	$form.="<input type='hidden' name='".$field_name."[]' value=\"".htmlentities(serialize($valeur),ENT_QUOTES,$charset)."\"/>";
		
    	return $form;
    }
    
    public function set_xml_file($file){
        $this->xml_file = $file;
    }
    
}
?>