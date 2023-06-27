<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestions_origine.class.php,v 1.16 2017-07-10 13:55:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class suggestions_origine{
	
	
	public $origine = 0;						//Auteur de la suggestion (email ou identifiant utilisateur ou identifiant abonné) 
	public $num_suggestion = 0;				//Numéro de la suggestion
	public $type_origine = 0;					//Identifie le type de l'auteur (0 = utilisateur, 1 = abonné, 2 = visiteur, ..)
	public $date_suggestion = NULL;			//Date et heure de la suggestion	
	 
	//Constructeur.	 
	public function __construct($origine= 0, $num_suggestion= 0,$type=0) {
		$this->origine = $origine+0;
		$this->num_suggestion = $num_suggestion+0;
		$this->type_origine = $type;
		$q = "select count(1) from suggestions_origine where origine = '".$this->origine."' and num_suggestion = '".$this->num_suggestion."' and type_origine='".$this->type_origine."'";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_result($r, 0, 0) != 0) {
			$this->load();
		}
	}	


	public static function exists($origine= 0, $num_suggestion= 0,$type=0) {
		$q = "select count(1) from suggestions_origine where origine = '".$origine."' and num_suggestion = '".$num_suggestion."' and type_origine='".$type."'";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0) != 0;
	}
	
	// charge un auteur et une de ses suggestions à partir de la base.
	public function load(){
		$q = "select * from suggestions_origine where origine = '".$this->origine."' and num_suggestion = '".$this->num_suggestion."' and type_origine='".$this->type_origine."'";
		$r = pmb_mysql_query($q);
		$obj = pmb_mysql_fetch_object($r);
		$this->type_origine = $obj->type_origine;
		$this->date_suggestion = $obj->date_suggestion;
	}
	
	// enregistre un auteur et une de ses suggestions en base.
	public function save(){
		if (!$this->origine && !$this->num_suggestion) die("Erreur de création suggestions_origine");

		$q = "select count(1) from suggestions_origine where origine = '".$this->origine."' and num_suggestion = '".$this->num_suggestion."' and type_origine='".$this->type_origine."'";	
		$r = pmb_mysql_query($q);
		if (pmb_mysql_result($r, 0, 0) != 0) {
			$q = "update suggestions_origine set type_origine = '".$this->type_origine."' ";
			$q.= "where origine = '".$this->origine."' and num_suggestion = '".$this->num_suggestion."' ";
			$r = pmb_mysql_query($q);
		} else {
			$q = "insert into suggestions_origine set origine = '".$this->origine."', num_suggestion = '".$this->num_suggestion."', ";
			$q.= "type_origine =  '".$this->type_origine."', date_suggestion = now() ";
			$r = pmb_mysql_query($q);
		}
	}

	//supprime la suggestion d'un auteur de la base
	public static function delete($num_suggestion, $origine=0,$type=0 ) {
		$q = "delete from suggestions_origine where num_suggestion = '".$num_suggestion."' ";
		if($origine) $q.= "and origine = '".$origine."' ";
		 if($type) $q.= "and type_origine = '".$type."' ";
		$r = pmb_mysql_query($q);
	}
	
	//optimization de la table suggestions_origine
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE suggestions_origine');
		return $opt;
	}
	
	//recherche les occurences d'une suggestion triées par date
	static public function listOccurences($num_suggestion, $limit=0){
		$num_suggestion += 0;
		$q = "Select origine, type_origine, date_suggestion from suggestions_origine where num_suggestion = '".$num_suggestion."' order by date_suggestion asc ";
		if ($limit) $q.= "limit ".$limit;
		return $q;
	}

	//fusion des suggestions
	public static function fusionne($origine, $from_sug, $to_sug){
		//On commence par supprimer les suggestions pour lesquelles l'origine est identique à celle de destination
		$q = "Delete from suggestions_origine where origine = '".$origine."' and num_suggestion = '".$from_sug."' ";
		$r = pmb_mysql_query($q);
		
		//On met à jour les suggestions à fusionner
		$q = "Update suggestions_origine set num_suggestion = '".$to_sug."' where num_suggestion = '".$from_sug."' ";
		$r = pmb_mysql_query($q);
	}
}
?>