<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.7 2017-07-12 15:15:02 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Classe de gestion de la recherche spécial "avis"

class avis_search {
	public $id;
	public $n_ligne;
	public $params;
	public $search;

	//Constructeur
    public function __construct($id,$n_ligne,$params,&$search) {
    	$this->id=$id;
    	$this->n_ligne=$n_ligne;
    	$this->params=$params;
    	$this->search=&$search;
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

    	$user_query="<span class='search_value'><input type='text' name='field_".$this->n_ligne."_s_".$this->id."[]' value='".htmlentities($valeur[0],ENT_QUOTES,$charset)."' /></span>";
    	return $select.$user_query;
    }
    
    //fonction de conversion de la saisie en quelque chose de compatible avec l'environnement
    public function transform_input() {
    }
    
    //fonction de création de la requête (retourne une table temporaire)
    public function make_search() {
    	//Récupération de la valeur de saisie
    	$valeur_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$valeur_};
    	$valeur=${$valeur_};

    	if (!$this->is_empty($valeur)) {
			$req = "select distinct num_notice as notice_id from avis where valide=1 and type_object=1 and (sujet like '%".$valeur[0]."%' or commentaire like '%".$valeur[0]."%') ";		
			if($_SESSION['id_empr_session']) {
				$req .= "
				and (
					avis_private = 0
					or (avis_private = 1 and num_empr='".$_SESSION['id_empr_session']."')
					or (avis_private = 1 and avis_num_liste_lecture <> 0
							and avis_num_liste_lecture in (
							select num_liste from abo_liste_lecture
							where abo_liste_lecture.num_empr='".$_SESSION['id_empr_session']."' and abo_liste_lecture.etat=2
							)
						)
					)";
			} else {
				$req .= " and avis_private = 0";
			}
			pmb_mysql_query("create temporary table t_s_avis (notice_id integer unsigned not null)");
    		$requete="insert into t_s_avis ".$req;
	    	$res = pmb_mysql_query($requete);    		
	 		pmb_mysql_query("alter table t_s_avis add primary key(notice_id)");
    	}
		return "t_s_avis"; 
    }
    
    //fonction de traduction littérale de la requête effectuée (renvoie un tableau des termes saisis)
    public function make_human_query() {
    	global $msg;
    	global $include_path;
    			
    	//Récupération de la valeur de saisie 
    	$valeur_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$valeur_};
    	$valeur=${$valeur_};
    	$tit[0]=$valeur[0];
		return $tit;    
    }
    
    public function make_unimarc_query() {
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
    	global $opac_avis_allow;
    	if($opac_avis_allow) {
    		return true;
    	} else {
    		return false;
    	}
    }
}