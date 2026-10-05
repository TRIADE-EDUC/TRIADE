<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pubmed_analyse_query.class.php,v 1.5.6.1 2024/12/02 15:20:23 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/analyse_query.class.php");

class pubmed_analyse_query extends analyse_query{
	public $pubmed_stopwords = array();
	public $field = '';

    public function __construct($input,$debut=0,$parenthesis=0,$search_linked_words=1,$keep_empty=0,$field='',$pubmed_stopwords=array()) {
    	$this->pubmed_stopwords = $pubmed_stopwords;
    	$this->field = $field;
    	$this->operator = strtoupper($this->operator);
    	parent::__construct($input,0,0,1,0);
    }

	public function nettoyage_mot_vide($string) {
		//Supression des espaces avant et après le terme
		$string = trim($string);
		//Décomposition en mots du mot nettoyé (ex : l'arbre devient l arbre qui donne deux mots : l et arbre)
		$words=explode(" ",$string);
		//Variable de stockage des mots restants après supression des mots vides
		$words_empty_free=array();
		//Pour chaque mot
		for ($i=0; $i<count($words); $i++) {
			$words[$i]=trim($words[$i]);
			//Vérification que ce n'est pas un mot vide
			if (($this->keep_empty)||(in_array($words[$i],$this->pubmed_stopwords)===false)) {
				//Si ce n'est pas un mot vide, on stoque
				$words_empty_free[]=$words[$i];
			}
		}
		return $words_empty_free;
	}

	//Affichage sous forme mathématique logique du résultat de l'analyse
	public function show_analyse($tree="") {
		$r ="";
		if ($tree=="") $tree=$this->tree;
		foreach($tree as $elem){
			if($elem->start_with == 0){
				//PubMed veut ses opérateurs en MAJ
				if ($elem->operator) $r.=" ".strtoupper($elem->operator)." ";
				$r.="(";
				if ($elem->not) $r.="not";
				if ($elem->sub==null) {
					if ($elem->literal) $r.="\"";
					$r.=$elem->word;
					if ($elem->literal) $r.="\"";
					if ($elem->not) $r.=")";
					$r.=$this->field;
				} else {
					$r.="( ".$this->show_analyse($elem->sub).") ";
				}
				$r.=")";
			}
		}
		return $r;
	}
}
?>