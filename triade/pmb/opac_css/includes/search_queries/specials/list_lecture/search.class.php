<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.7 2017/07/13 12:14:17 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Classe de gestion de la recherche spécial "combine"

global $class_path;
require_once ($class_path."/listes_lecture.class.php") ;
require_once ($class_path."/liste_lecture.class.php") ;

class list_lecture_search {
	public $id;
	public $n_ligne;
	public $params;
	public $search;

	//Constructeur
    public function __construct($id,$n_ligne,$params,&$search) {
    	$this->id = $id;
    	$this->n_ligne = $n_ligne;
    	$this->params = $params;
    	$this->search = &$search;
    }
    
    //fonction de récupération des opérateurs disponibles pour ce champ spécial (renvoie un tableau d'opérateurs)
    public function get_op() {
    	$operators = array();
   		$operators["EQ"]="=";
    	return $operators;
    }
    
    //fonction de récupération de l'affichage de la saisie du critère
    public function get_input_box() {
    	global $msg;
    	global $charset;

    	//Récupération de la valeur de saisie
    	$valeur_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$valeur_};
    	$valeur=${$valeur_};
    	
    	$listes_lecture = new listes_lecture('private_reading_lists');    	
    	//Affichage de la liste des listes de lecture
    	$select="<select name='field_".$this->n_ligne."_s_".$this->id."[]'>
    		<option value='0'".($valeur[0]==0?" selected='selected' ":"").">".htmlentities($msg['search_list_lecture_all'],ENT_QUOTES,$charset)."</option>";;
    	foreach ($listes_lecture->get_listes_lecture() as $tag=>$listes) {
    		$select.="<optgroup label='".htmlentities($tag,ENT_QUOTES,$charset)."'>";
    		foreach ($listes as $liste_lecture) {
    			$select.="<option value='".$liste_lecture->id_liste."'".($valeur[0]==$liste_lecture->id_liste?" selected='selected'":"").">".htmlentities($liste_lecture->nom_liste,ENT_QUOTES,$charset)."</option>";
    		}
    		$select.="</optgroup>";
    	}
    	$select.="</select>";
    	return $select;
    }
    
    //fonction de conversion de la saisie en quelque chose de compatible avec l'environnement
    public function transform_input() {
    }
    
    //fonction de création de la requête (retourne une table temporaire)
    public function make_search() {
    	global $opac_indexation_docnum_allfields;
    	
    	//Récupération de la valeur de saisie
    	$valeur_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$valeur_};
    	$valeur=${$valeur_};
    	if (!$this->is_empty($valeur)) {
    		$notices = array();
	    	pmb_mysql_query("create temporary table t_s_list_lecture (notice_id integer unsigned not null)");
	    	if($valeur[0]) {
	    		$liste_lecture = new liste_lecture($valeur[0]);
	    		$notices = $liste_lecture->notices;
	    	} else {
	    		$listes_lecture = new listes_lecture('private_reading_lists');
	    		foreach ($listes_lecture->get_listes_lecture() as $tag=>$listes) {
	    			foreach ($listes as $liste) {
	    				if($liste->notices_associees) {
	    					$notices = array_merge($notices, explode(',', $liste->notices_associees));
	    				}
	    			}
	    		}
	    	}
			if(count($notices)) {
				$query = "insert into t_s_list_lecture VALUES ";
				foreach($notices as $i=>$notice_id){
					if($i > 0) $query .= ", ";
					$query .= "(".$notice_id.")";
				}
				pmb_mysql_query($query);
			}
	 		pmb_mysql_query("alter table t_s_list_lecture add primary key(notice_id)");
    	}
		return "t_s_list_lecture"; 
    }
    
    //fonction de traduction littérale de la requête effectuée (renvoie un tableau des termes saisis)
    public function make_human_query() {
    	global $msg;
    	global $include_path;
    			
    	//Récupération de la valeur de saisie 
    	$valeur_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$valeur_};
    	$valeur=${$valeur_};
    	
    	$tit=array();
    	if (!$this->is_empty($valeur)) {
    		$req = "select nom_liste from opac_liste_lecture where id_liste='".$valeur[0]."'";
    		$res=pmb_mysql_query($req);
    		if(pmb_mysql_num_rows($res)){
    			$tit[0]=pmb_mysql_result($res,0,0);
    		}
    	} 
    	if(!$tit[0])$tit[0]=$msg['search_list_lecture_all'];
		return $tit;    
    }
    
    public function make_unimarc_query() {
    	//Récupération de la valeur de saisie
    	$valeur_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$valeur_};
    	$valeur=${$valeur_};
    	return "";
    }    
    
	//fonction de vérification du champ saisi ou sélectionné
    public function is_empty($valeur) {
    	if (count($valeur)) {
    		if ($valeur[0]=="") return true;
    			else return ($valeur[0] === false);
    	} else {
    		return true;
    	}	
    }
    
    public static function check_visibility() {
    	global $opac_shared_lists;
    	if($opac_shared_lists) {
    		return true;
    	} else {
    		return false;
    	}
    }
}