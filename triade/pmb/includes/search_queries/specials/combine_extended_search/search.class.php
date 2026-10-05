<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.3 2022/08/17 13:19:54 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Classe de gestion de la recherche spécial "combine_extended_search"

class combine_extended_search {
	public $id;
	public $n_ligne;
	public $params;
	
	/**
	 * 
	 * @var search
	 */
	public $search;
	public $champ_base;
	
	protected $serialized_search;
	
	//Constructeur
    public function __construct($id,$n_ligne,$params,&$search) {
    	$this->id=$id;
    	$this->n_ligne=$n_ligne;
    	$this->params=$params;
    	$this->search=&$search;
    }
    
	public function get_op() {
    	$operators = array();
		$operators["EQ"] = "=";
    	return $operators;
    }
    
    public function make_search(){
    	$this->get_serialized_search();
    	
    	//enregistrement de l'environnement courant
    	$this->search->push();
    	
    	$this->search->unserialize_search($this->serialized_search);
    	$table_tempo = $this->search->make_search("tempo_".$this->n_ligne);
    	
    	//restauration de l'environnement courant
    	$this->search->pull();
    	
    	return $table_tempo;
    }
    
    public function make_human_query(){
    	$litteral = array();
    	
    	$this->get_serialized_search();
    	
    	//enregistrement de l'environnement courant
    	$this->search->push();

    	$this->search->unserialize_search($this->serialized_search);
    	$litteral[0] = $this->search->make_human_query();

    	//restauration de l'environnement courant
    	$this->search->pull();
    	
    	return $litteral;
    }
    
    public function make_unimarc_query(){
    	//Récupération de la valeur de saisie
    	$valeur_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$valeur_};
    	$valeur=${$valeur_};
    	return "";
    }
    
    public function get_input_box() {
    	global $charset;
    	
		//enregistrement de l'environnement courant
		$this->search->push();
		
    	//on génère une human_query
		$this->search->unserialize_search($this->serialized_search);
    	$r = $this->search->make_human_query();
    	$r.="<span><input type='hidden' name='field_".$this->n_ligne."_s_".$this->id."[]' value='".htmlentities($valeur[0],ENT_QUOTES,$charset)."'/></span>";
    	
    	//restauration de l'environnement courant
    	$this->search->pull();
    	
    	return $r;
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
    
    public function get_serialized_search() {
    	if (isset($this->serialized_search)) {
    		return $this->serialized_search;
    	}
    	$value = "field_".$this->n_ligne."_s_".$this->id;
    	global ${$value};
    	
    	$this->serialized_search = ${$value}[0];
    	return $this->serialized_search;
    }
    
}
