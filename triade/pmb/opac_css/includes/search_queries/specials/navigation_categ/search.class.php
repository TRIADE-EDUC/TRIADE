<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.6 2023/08/23 10:25:54 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Classe de gestion de la recherche spécial "combine"

class navigation_categ_search {
	public $id;
	public $n_ligne;
	public $params;
	public $search;

	//Constructeur
    public function __construct($id,$n_ligne,$params,&$search) {
    	$this->id = intval($id);
    	$this->n_ligne=$n_ligne;
    	$this->params=$params;
    	$this->search=&$search;
    }
    
    //fonction de récupération des opérateurs disponibles pour ce champ spécial (renvoie un tableau d'opérateurs)
    public function get_op() {
    }
    
    //fonction de récupération de l'affichage de la saisie du critère
    public function get_input_box() {
    }
    
    //fonction de conversion de la saisie en quelque chose de compatible avec l'environnement
    public function transform_input() {
    }
    
    //fonction de création de la requête (retourne une table temporaire)
    public function make_search() {
		global $opac_auto_postage_etendre_recherche,$opac_auto_postage_nb_descendant,$opac_auto_postage_nb_montant;
		global $opac_auto_postage_descendant,$opac_auto_postage_montant,$opac_auto_postage_etendre_recherche;
		global $gestion_acces_active,$gestion_acces_empr_notice,$class_path;
		global $opac_visionneuse_allow,$opac_photo_filtre_mimetype;
    	
    	$id=$_SESSION['last_module_search']['search_id'];
    	$nb_level_enfants=$_SESSION["last_module_search"]["search_nb_level_enfants"];
    	$nb_level_parents=$_SESSION["last_module_search"]["search_nb_level_parents"];
    	
    	//recuperation du thesaurus session
    	$id_thes = thesaurus::getSessionThesaurusId();
    	
    	$thes = new thesaurus($id_thes);
    	$id_top = $thes->num_noeud_racine;
    	
    	//FIL D'ARIANNE DANS LE THESAURUS
    	$ourCateg = new categorie($id);
    	
    	//LISTE DES NOTICES ASSOCIEES
    	//Lire le champ path du noeud pour étendre la recherche éventuellement au fils et aux père de la catégorie
    	// lien Etendre auto_postage
    	if (!$nb_level_enfants) {
    		// non defini, prise des valeurs par défaut
    		if (isset($_SESSION["nb_level_enfants"]) && $opac_auto_postage_etendre_recherche) $nb_level_descendant=$_SESSION["nb_level_enfants"];
    		else $nb_level_descendant=$opac_auto_postage_nb_descendant;
    	} else {
    		$nb_level_descendant=$nb_level_enfants;
    	}
    	
    	// lien Etendre auto_postage
    	if(!isset($nb_level_parents)) {
    		// non defini, prise des valeurs par défaut
    		if(isset($_SESSION["nb_level_parents"]) && $opac_auto_postage_etendre_recherche) $nb_level_montant=$_SESSION["nb_level_parents"];
    		else $nb_level_montant=$opac_auto_postage_nb_montant;
    	} else {
    		$nb_level_montant=$nb_level_parents;
    	}
    	
    	$q = "select path from noeuds where id_noeud = '".$id."' ";
    	$r = pmb_mysql_query($q);
    	if($r && pmb_mysql_num_rows($r)){
    		$path=pmb_mysql_result($r, 0, 0);
    		$nb_pere=substr_count($path,'/');
    	}else{
    		$path="";
    		$nb_pere=0;
    	}
    	
    	$acces_j='';
    	if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
    		require_once("$class_path/acces.class.php");
    		$ac= new acces();
    		$dom_2= $ac->setDomain(2);
    		$acces_j = $dom_2->getJoin($_SESSION['id_empr_session'],4,'notcateg_notice');
    	}
    	
    	if($acces_j) {
    		$statut_j='';
    		$statut_r='';
    	} else {
    		$statut_j=',notice_statut';
    		$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
    	}
    	
    	if($_SESSION["opac_view"] && $_SESSION["opac_view_query"] ){
    		$opac_view_restrict=" notice_id in (select opac_view_num_notice from  opac_view_notices_".$_SESSION["opac_view"].") ";
    		$statut_r.=" and ".$opac_view_restrict;
    	}
    	
    	// Si un path est renseigné et le paramètrage activé
    	if ($path && ($opac_auto_postage_descendant || $opac_auto_postage_montant || $opac_auto_postage_etendre_recherche) && ($nb_level_montant || $nb_level_descendant)){
    	
    		//Recherche des fils
    		if(($opac_auto_postage_descendant || $opac_auto_postage_etendre_recherche)&& $nb_level_descendant) {
    			if($nb_level_descendant != '*' && is_numeric($nb_level_descendant))
    				$liste_fils=" path regexp '^$path(\\/[0-9]*){0,$nb_level_descendant}$' ";
    			else
    				//$liste_fils=" path regexp '^$path(\\/[0-9]*)*' ";
    				$liste_fils=" path like '$path/%' or  path = '$path' ";
    		} else {
    			$liste_fils=" id_noeud='".$id."' ";
    		}
    	
    		// recherche des pères
    		if(($opac_auto_postage_montant || $opac_auto_postage_etendre_recherche) && $nb_level_montant ) {
    	
    			$id_list_pere=explode('/',$path);
    			$stop_pere=0;
    			if($nb_level_montant != '*' && is_numeric($nb_level_montant)) $stop_pere = $nb_pere - intval($nb_level_montant);
    			if($stop_pere<0) $stop_pere=0;
    			for($i=$nb_pere;$i>=$stop_pere; $i--) {
    				$liste_pere.= " or id_noeud='".$id_list_pere[$i]."' ";
    			}
    		}
    		$suite_req = " FROM noeuds STRAIGHT_JOIN notices_categories on id_noeud=num_noeud join notices on notcateg_notice=notice_id $acces_j $statut_j ";
    		$suite_req.= "WHERE ($liste_fils $liste_pere) $statut_r ";
    	
    	} else {
    		// cas normal d'avant
    		$suite_req = " FROM notices_categories join notices on notcateg_notice=notice_id $acces_j $statut_j ";
    		$suite_req.= "WHERE num_noeud='".$id."' $statut_r ";
    	}
    	
    	$requeteSource = "SELECT notice_id ".$suite_req;

   		pmb_mysql_query("create temporary table t_s_navigation_section (notice_id integer unsigned not null)");
   		$requete="insert into t_s_navigation_section ".$requeteSource;
   		pmb_mysql_query($requete);
		pmb_mysql_query("alter table t_s_navigation_section add primary key(notice_id)");

		return "t_s_navigation_section"; 
    }
    
    //fonction de traduction littérale de la requête effectuée (renvoie un tableau des termes saisis)
    public function make_human_query() {  
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
}
?>