<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.2 2017-05-18 15:14:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Classe de gestion de la recherche spécial "doublons isbn depuis import"

class last_import_isbn_doublons_search {
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
    	
    	if ((!isset($_SESSION['last_import_isbn_doublons'])) || (!trim($_SESSION['last_import_isbn_doublons'])) || ($_SESSION['last_import_isbn_doublons']=='""')) {
    		return $msg['last_import_isbn_doublons_msg_search_no_import'];
    	} else {
    		return $msg['last_import_isbn_doublons_msg_search'].formatdate($_SESSION["last_import_isbn_doublons_datetime"],1);
    	}
    }
    
    //fonction de création de la requête (retourne une table temporaire)
    public function make_search() {
    	$table_tempo = 'last_import_isbn_doublons_'.md5(microtime(true));
    	$requete="create temporary table ".$table_tempo." ENGINE=MyISAM SELECT notice_id FROM notices WHERE code IN (".json_decode($_SESSION['last_import_isbn_doublons']).")";	
		pmb_mysql_query($requete);
    	    	    	
    	return $table_tempo;
    }
    
    //fonction de traduction littérale de la requête effectuée (renvoie un tableau des termes saisis)
    public function make_human_query() {
    	global $msg;

    	$litteral=array();
    	$litteral[]=formatdate($_SESSION["last_import_isbn_doublons_datetime"],1);
    			
		return $litteral;    
    }
    
    public function is_empty($valeur) {
    	if ((!isset($_SESSION['last_import_isbn_doublons'])) || (!trim($_SESSION['last_import_isbn_doublons'])) || ($_SESSION['last_import_isbn_doublons']=='""')) {
    		return true;
    	} else {
    		return false;
    	}
    }

}
?>