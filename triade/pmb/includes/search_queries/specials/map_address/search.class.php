<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.3 2019/05/16 13:40:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $msg,$lang,$charset,$base_path,$class_path,$include_path;

class map_address {
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
    	global $msg;
    	$operators = array();
    	$operators["CONTAINS"]=$msg['284'];
    	$operators["INTERSECTS"]=$msg['map_multisearch_intersects'];
    	return $operators;
    }
    
    //fonction de récupération de l'affichage de la saisie du critère
    public function get_input_box() {
    	global $msg;
    	global $charset;
    	global $get_input_box_id;
    	global $base_path;

    	//$this->s = new search(false,"search_simple_fields.xml");
    	
    	//Récupération de la valeur de saisie
    	$valeur_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$valeur_};
    	$valeur=${$valeur_};
    	
    	$r ="<input type='text' id='map_address_" . $valeur_ . "' name='" . $valeur_ . "[]' value='" . $valeur[0] . "'><br>";
    	return $r;
    }
    
    //fonction de conversion de la saisie en quelque chose de compatible avec l'environnement
    public function transform_input() {
    }
    
    //fonction de création de la requête (retourne une table temporaire)
    public function make_search() {
    	global $search;
    	global $base_path;
    	
    	//Récupération de la valeur de saisie
    	$address_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$address_};
    	$address=${$address_};
    	$op_ = "op_".$this->n_ligne."_s_".$this->id;
    	global ${$op_};
    	$op=${$op_};
    	if( count($address)) {    		
    		
     		$url = "https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=1&polygon_text=1&q=".urlencode($address[0]);
    		$curl = new Curl();
    		$response = $curl->get($url);
    		
    		$body = encoding_normalize::json_decode($response->body, true);
    		
    		if (!empty($body)) {
    		    switch($op) {
    		        case "CONTAINS" :
    		            $query = "
                                SELECT DISTINCT map_emprise_obj_num AS notice_id 
                                FROM map_emprises 
                                WHERE map_emprise_type=11 
                                AND CONTAINS(GEOMFROMTEXT('".$body[0]["geotext"]."'),map_emprise_data) = 1 
                                UNION 
                                SELECT DISTINCT notcateg_notice AS notice_id 
                                FROM notices_categories 
                                JOIN map_emprises ON num_noeud = map_emprises.map_emprise_obj_num 
                                WHERE map_emprise_type = 2 
                                AND CONTAINS(GEOMFROMTEXT('".$body[0]["geotext"]."'),map_emprise_data) = 1";
    		            break;
    		        case "INTERSECTS" :
    		            $query = "
                                SELECT DISTINCT map_emprise_obj_num AS notice_id
                                FROM map_emprises
                                WHERE map_emprise_type=11
                                AND INTERSECTS(GEOMFROMTEXT('".$body[0]["geotext"]."'),map_emprise_data) = 1
                                UNION
                                SELECT DISTINCT notcateg_notice AS notice_id
                                FROM notices_categories
                                JOIN map_emprises ON num_noeud = map_emprises.map_emprise_obj_num
                                WHERE map_emprise_type = 2
                                AND INTERSECTS(GEOMFROMTEXT('".$body[0]["geotext"]."'),map_emprise_data) = 1";
    		            break;
    		    }
    		    
    		    pmb_mysql_query("create temporary table t_s_map_address (notice_id integer unsigned not null)");
    		    $requete="insert into t_s_map_address " . $query;
    		    pmb_mysql_query($requete);
    		    pmb_mysql_query("alter table t_s_map_address add primary key(notice_id)");
    		    return "t_s_map_address";
    		}    	    
    	} 
    	return '';
    }
    
    public function make_unimarc_query() {
		return array();
    }
    	    
    //fonction de traduction littérale de la requête effectuée (renvoie un tableau des termes saisis)
    public function make_human_query() {
		global $search;
		global $base_path,$charset;
		global $msg;
    	
    	//Récupération de la valeur de saisie
		$address_="field_".$this->n_ligne."_s_".$this->id;
		global ${$address_};
		$address=${$address_};
		
		
    	$litteral = array();
    	if( count($address) ) {    	    
    	    $litteral[0] = $address[0]; 
    	}
    	return $litteral;
    }
    
    //fonction de vérification du champ saisi ou sélectionné
    public function is_empty($valeur) {
    	
    }
    
     //fonction de découpage d'une chaine trop longue
    public function cutlongwords($valeur,$size=50) {
    	if (strlen($valeur)>=$size) {
    		$pos=strrpos(substr($valeur,0,$size)," ");
    		if ($pos) {
    			$valeur=substr($valeur,0,$pos+1)."...";
    		} 
    	}
    	return $valeur;		
    }
    
    public static function check_visibility() {
    	global $pmb_map_activate;
    	if($pmb_map_activate) {
    		return true;
    	} else {
    		return false;
    	}
    }
}