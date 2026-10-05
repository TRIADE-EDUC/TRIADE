<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.1.2.2 2024/12/20 08:08:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Classe de gestion de la recherche spécial "search_perso"

class search_perso_search {
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
        global $msg, $charset;
    	global $PMBuserid;
    	
    	//Récupération de la valeur de saisie
    	$valeur_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$valeur_};
    	$valeur=${$valeur_};
    	
    	//Affichage de la liste des recherches prédéfinies
    	$r="<select name='field_".$this->n_ligne."_s_".$this->id."[]'>";
    	$r.="<option value='0'>".htmlentities($msg['predefined_search_choice'],ENT_QUOTES,$charset)."</option>";
    	
    	$query = "SELECT * FROM search_perso WHERE search_type = 'RECORDS'";
    	if ($PMBuserid!=1) {
    	    $query .= " AND (autorisations='$PMBuserid' or autorisations like '$PMBuserid %' or autorisations like '% $PMBuserid %' or autorisations like '% $PMBuserid') ";
    	}
    	$query .= " order by search_order, search_name ";
    	$result = pmb_mysql_query($query);
    	while ($row=pmb_mysql_fetch_object($result)) {
    	    $r.="<option value='".$row->search_id."'".($valeur[0]==$row->search_id?" selected='selected'":"").">".htmlentities($row->search_name,ENT_QUOTES,$charset)."</option>";
    	}
    	$r.="</select>";
    	return $r;
    }
    
    //fonction de conversion de la saisie en quelque chose de compatible avec l'environnement
    public function transform_input() {
    }
    
    //fonction de création de la requête (retourne une table temporaire)
    public function make_search() {
        $table_tempo = '';
    	//Récupération de la valeur de saisie
    	$valeur_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$valeur_};
    	$valeur=${$valeur_};
    	if (!$this->is_empty($valeur)) {
    	    //enregistrement de l'environnement courant
    	    $this->search->push();
    	    
    	    //et on se met dans le contexte de la recherche prédéfnie
    	    $search_perso = new search_perso($valeur[0]);
    	    $es = new search(false);
    	    $es->unserialize_search($search_perso->query);
    	    //on cherche...
    	    $table_tempo=$es->make_search("tempo_".$this->n_ligne);
    	    
    		//restauration de l'environnement courant
    		$this->search->pull();
    	}
    	return $table_tempo;
    }
    
    //fonction de traduction littérale de la requête effectuée (renvoie un tableau des termes saisis)
    public function make_human_query() {
        //Récupération de la valeur de saisie
        $valeur_="field_".$this->n_ligne."_s_".$this->id;
        global ${$valeur_};
        $valeur=${$valeur_};
        
        $tit=array();
        if (!$this->is_empty($valeur)) {
            $search_perso = new search_perso($valeur[0]);
            $tit[0]=$search_perso->name;
        } else $tit[0]="[vide]";
        return $tit;
    }
    
    
    public function make_unimarc_query() {
    	return '';
    }
    
	/**
	 * Fonction de vérification du champ saisi ou sélectionné
	 * @param array $valeur
	 * @return boolean true si vide, false sinon
	 */
    public function is_empty($valeur) {
    	if (count($valeur)) {
    	    if ($valeur[0]=="-1") {
    	        return true;
    	    } else {
    	        return ($valeur[0] === false);
    	    }
    	} else {
    		return true;
    	}	
    }
}
