<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.6 2019/05/16 13:40:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $msg,$lang,$charset,$base_path,$class_path,$include_path;

require_once($class_path.'/map/map_hold_circle.class.php');

class map_circle{
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
    	global $get_input_box_id;
    	global $base_path;

    	//$this->s = new search(false,"search_simple_fields.xml");
    	
    	//Récupération de la valeur de saisie
    	$valeur_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$valeur_};
    	$valeur=${$valeur_};
    	
    	$radius_="fieldvar_".$this->n_ligne."_s_".$this->id;
    	global ${$radius_};
    	$radius=${$radius_}[0][0];
    	
    	$r ="
 	    " . $msg['search_extended_map_circle_center'] . "  
    	<input type='text' id='center_" . $valeur_ . "' name='" . $valeur_ . "[]' value='" . $valeur[0] . "'><br>
 	    " . $msg['search_extended_map_circle_radius'] . "  
    	<input type='text' id='radius_" . $valeur_ . "' name='" . str_replace('field','fieldvar',$valeur_) . "[][]' value='" . $radius . "'><br>
    	";
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
    	$center_="field_".$this->n_ligne."_s_".$this->id;
    	$radius_="fieldvar_".$this->n_ligne."_s_".$this->id;
    	global ${$center_};
    	global ${$radius_};
    	$center=${$center_};
    	$radius = ${$radius_}[0][0];
		    	
    	if( count($center) && $radius) {
    	    $center_coords = explode(',', $center[0]);
    	    $x = $center_coords[0];
    	    $y = $center_coords[1];
    	    if ($x && $y && $radius) {
    	    //['x'=> -74.00597,'y'=>40.71427]
    	        $wkt = map_hold_circle::getWKT(map_hold_circle::createRegularPolygon(['x'=> $x,'y'=> $y], $radius, 32),true);
        	    if($wkt) {
        	       $query = "select distinct map_emprise_obj_num as notice_id from map_emprises where map_emprise_type=11 and intersects(geomfromtext('!!p!!'),map_emprise_data) = 1 union select distinct notcateg_notice as notice_id from notices_categories join map_emprises on num_noeud = map_emprises.map_emprise_obj_num where map_emprise_type = 2 and intersects(geomfromtext('!!p!!'),map_emprise_data)";
        	               	       
        	       $query = str_replace('!!p!!', $wkt, $query);
        	       
        	       pmb_mysql_query("create temporary table t_s_map_circle (notice_id integer unsigned not null)");
        	       $requete="insert into t_s_map_circle " . $query;
        	       pmb_mysql_query($requete);
        	       pmb_mysql_query("alter table t_s_map_circle add primary key(notice_id)");        	       
        	       return "t_s_map_circle"; 
        	    }
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
    	$center_ = "field_".$this->n_ligne."_s_".$this->id;
    	$radius_ = "fieldvar_".$this->n_ligne."_s_".$this->id;
    	global ${$center_};
    	global ${$radius_};
    	$center = ${$center_};
    	$radius = ${$radius_}[0][0];
    	$litteral = array();
    	if( count($center) && $radius) {    	    
    	    $center_coords = explode(',', $center[0]);
    	    $x = $center_coords[0];
    	    $y = $center_coords[1];
    	    if ($x && $y && $radius) {
    	        $litteral[0] = $msg['search_extended_map_circle_center'] . ' ' . $center[0] . ', ' . $msg['search_extended_map_circle_radius'] . ' ' . $radius; 
        	}
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