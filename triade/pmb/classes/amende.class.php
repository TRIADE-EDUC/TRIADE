<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: amende.class.php,v 1.28.6.2.2.2 2025/03/25 11:34:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/calendar.class.php");
require_once($class_path."/quotas.class.php");
require_once($class_path."/expl.class.php");

class amende {

	public $id_empr; 	//Id de l'emprunteur
	public	$t_id_expl; //Tableau des exemplaires en retard
	public $nb_amendes=0;	//Nombre d'exemplaires ayant une amende
	
    public function __construct($id_empr, $noreadcache=false) {
    	global $progress_bar;
    	$this->id_empr=intval($id_empr);
    	if (!$noreadcache) {
    		// lire en cache
    		$req="select data_amendes from cache_amendes where id_empr=$id_empr and cache_date=CURDATE()";
	    	$resultat=pmb_mysql_query($req);
	    	if (pmb_mysql_num_rows($resultat)) {
	    		$r=pmb_mysql_fetch_object($resultat); 
	    		$this->t_id_expl=unserialize($r->data_amendes);	
	    	} else {
	    		$this->t_id_expl=$this->get_list_of_id_expl();
    		
	    		// on fait le ménage des anciens caches
	    		$req="delete from cache_amendes where cache_date<CURDATE() ";
	    		pmb_mysql_query($req);
	    		$req="insert into cache_amendes set id_empr=$id_empr, cache_date=CURDATE(), data_amendes='".addslashes( serialize($this->t_id_expl))."'";
	    		pmb_mysql_query($req);
    		}
    	} else {
    		$this->t_id_expl=$this->get_list_of_id_expl();
    		
    		// on fait le ménage du cache de l'emprunteur
    		$req="delete from cache_amendes where cache_date<=CURDATE() and id_empr=$id_empr ";
    		pmb_mysql_query($req);
    		$req="insert into cache_amendes set id_empr=$id_empr, cache_date=CURDATE(), data_amendes='".addslashes( serialize($this->t_id_expl))."'";
    		pmb_mysql_query($req);
    	}
    	//progress bar utilisé pour le long calcul des relances (relance.inc.php)
    	if($progress_bar)$progress_bar->progress();    	
    }
    
    public function get_parameters($id_expl) {
    	global $pmb_gestion_amende,$lang,$include_path;
    	global $finance_amende_jour,$finance_delai_avant_amende,$finance_delai_recouvrement,$finance_amende_maximum,$finance_delai_1_2,$finance_delai_2_3, $finance_frais_recouvrement;
    	global $tbclasses;
    	
    	$id_expl = intval($id_expl);
		$param=array();
    	
    	if ($pmb_gestion_amende==1) {
    		//Gestion simple des amendes
    		$param["delai_avant_amende"]=$finance_delai_avant_amende;
    		$param["amende_jour"]=$finance_amende_jour;
    		$param["delai_recouvrement"]=$finance_delai_recouvrement;
    		$param["frais_recouvrement"]=$finance_frais_recouvrement;
    		$param["amende_maximum"]=$finance_amende_maximum;
    		$param["delai_1_2"]=$finance_delai_1_2;
    		$param["delai_2_3"]=$finance_delai_2_3;
    	} else {
    		//Gestion des quotas
    	    $struct = array();
 			$struct["READER"]=$this->id_empr;
			$struct["EXPL"]=$id_expl;
			$struct["NOTI"] = exemplaire::get_expl_notice_from_id($id_expl);
			$struct["BULL"] = exemplaire::get_expl_bulletin_from_id($id_expl);
			
			$qt_delai_avant_amende=new quota("AMENDE_DELAI","$include_path/quotas/own/$lang/finances.xml");
			$param["delai_avant_amende"]=$qt_delai_avant_amende->get_quota_value($struct);
			
			if (empty($tbclasses["QUOTAS_ELEMENTS"])) {
    			$tbclasses["QUOTAS_ELEMENTS"] = quota::$_quotas_[$qt_delai_avant_amende->descriptor]['_elements_'];
    			$tbclasses["QUOTAS_TYPES"] = quota::$_quotas_[$qt_delai_avant_amende->descriptor]['_types_'];
    			$tbclasses["QUOTAS_TABLE"] = quota::$_quotas_[$qt_delai_avant_amende->descriptor]['_table_'];
			}
			
			$qt_amende_jour=new quota("AMENDE_BY_DAY","$include_path/quotas/own/$lang/finances.xml");
			$param["amende_jour"]=$qt_amende_jour->get_quota_value($struct);
			if ($param["amende_jour"]==-1) $param["amende_jour"]=0;
			
			$qt_delai_recouvrement=new quota("AMENDE_DELAI_RECOUVREMENT","$include_path/quotas/own/$lang/finances.xml");
			$param["delai_recouvrement"]=$qt_delai_recouvrement->get_quota_value($struct);
			
			$qt_frais_recouvrement=new quota("AMENDE_FRAIS_RECOUVREMENT","$include_path/quotas/own/$lang/finances.xml");
			$param["frais_recouvrement"]=$qt_frais_recouvrement->get_quota_value($struct);
			
			$qt_amende_maximum=new quota("AMENDE_MAXIMUM","$include_path/quotas/own/$lang/finances.xml");
			$param["amende_maximum"]=$qt_amende_maximum->get_quota_value($struct);
			
			$qt_amende_maximum=new quota("AMENDE_1_2","$include_path/quotas/own/$lang/finances.xml");
			$param["delai_1_2"]=$qt_amende_maximum->get_quota_value($struct);
			
			$qt_amende_maximum=new quota("AMENDE_2_3","$include_path/quotas/own/$lang/finances.xml");
			$param["delai_2_3"]=$qt_amende_maximum->get_quota_value($struct);
    	}
    	
    	return $param;
    }
    
    public function get_list_of_id_expl() {
    	//Recherche des livres en retard
    	$t_id_expl=array();
    	
    	$requete="select pret_idexpl, printed from pret where pret_idempr=".$this->id_empr." and CURDATE()>pret_retour";
    	$resultat=pmb_mysql_query($requete);
    	if (@pmb_mysql_num_rows($resultat)) {
    		while ($r=pmb_mysql_fetch_object($resultat)) {
    			$t=array();
    			$t["id_expl"]=$r->pret_idexpl;
    			$t["printed"]=$r->printed;
    			//Calcul de l'amende
    			$amende=$this->get_amende($r->pret_idexpl);
    			
    			$t["amende"]=$amende;
    			if ($amende["njours"]) $t_id_expl[]=$t;
    		}
    	}
     	return $t_id_expl;
    }
    
    public function make_lost($id_expl) {
    }
    
    public function get_list_of_expl() {
    }
    
    public function get_total_amendes() {
    	$total=0;
    	$ta=$this->t_id_expl;
    	for ($i=0; $i<count($ta); $i++) {
    		$t=$ta[$i];
    		$total += floatval($t["amende"]["valeur"]);
    		if (floatval($t["amende"]["valeur"])) {
    		    $this->nb_amendes++;
    		}
    	}
    	return $total;
    }
    
    public function get_amende($id_expl) {
        global $pmb_amende_comptabilisation, $pmb_utiliser_calendrier, $pmb_utiliser_calendrier_location;
    	
        $id_expl = intval($id_expl);
    	
    	//Paramètre permettant de calculer l'amende sur le calendrier d'ouverture de la localisation de l'exemplaire
    	$loc_calendar = 0;
    	if (($pmb_utiliser_calendrier==1) && $pmb_utiliser_calendrier_location) {
    		$res=pmb_mysql_query("select expl_location from exemplaires where expl_id=".$id_expl);
    		if (pmb_mysql_num_rows($res)) {
    			$row = pmb_mysql_fetch_object($res);
    			$loc_calendar = $row->expl_location;
    		}
    	}
    	 
    	$requete="select pret_date, pret_retour, niveau_relance, date_relance from pret where pret_idexpl=$id_expl";
     	$resultat=pmb_mysql_query($requete);
    	$amende=array();
    	
    	$amende["valeur"]=0;
    	$amende["recouvrement"]=false;
    	
    	if (@pmb_mysql_num_rows($resultat)) {
    		$r=pmb_mysql_fetch_object($resultat);
    		$dr=explode("-",$r->pret_retour);
 		   	$njours=calendar::get_open_days($dr[2],$dr[1],$dr[0],date("d"),date("m"),date("Y"),$loc_calendar);
 		   	$amende_param=$this->get_parameters($id_expl);

 		   	if ($njours > 0) {
				global $pmb_gestion_financiere_periode, $pmb_gestion_financiere_periode_amende;

				$pmb_gestion_financiere_periode = intval($pmb_gestion_financiere_periode);
				// Calcul du nombre de periodes
				if($pmb_gestion_financiere_periode > 1) {
					$nbperiode = intval($njours / $pmb_gestion_financiere_periode);
			
					if(($pmb_gestion_financiere_periode_amende == 1) && ($njours % $pmb_gestion_financiere_periode != 0)){
						$nbperiode++;
					}

					// Remplacement du nombre de jours par le nombre de périodes.
					$njours = $nbperiode;
				}

 		   		$amende["njours"]=$njours;
	 		   	if ($njours>$amende_param["delai_avant_amende"]) {
 			   		//En recouvrement ?
 			   		if ($r->niveau_relance==3){
 			   			$drel=explode("-",$r->date_relance);
 			   			$njours_recouvrement=calendar::get_open_days($drel[2],$drel[1],$drel[0],date("d"),date("m"),date("Y"),$loc_calendar);
 			   			if ($njours_recouvrement>$amende_param["delai_recouvrement"]) {
 			   				$amende["recouvrement"]=true;
 			   				$njours=calendar::get_open_days($dr[2],$dr[1],$dr[0],$drel[2],$drel[1],$drel[0],$loc_calendar);
 			   			}
 			   		}
 			   		//Calcul de l'amende à partir du délai de grâce ?
 			   		if ($pmb_amende_comptabilisation) {
 			   			$amende["njours"] = $njours - $amende_param["delai_avant_amende"];
 			   		}
 			   		//Montant maximum dépassé ?
 			   		$amende["valeur"] = floatval($amende["njours"])*floatval($amende_param["amende_jour"]);
 			   		if ($amende["recouvrement"]) {
 			   		    $amende["valeur"] += floatval($amende_param["frais_recouvrement"]);
 			   		}
 			   		if (($amende["valeur"]>$amende_param["amende_maximum"])&&($amende_param["amende_maximum"]>0)) {
 			   			$amende["valeur"]=$amende_param["amende_maximum"];
 			   		}
 			   	}
    		}
    	
    		//Calcul du niveau théorique de l'exemplaire
    		//calcul de Date retour+delai_avant_amende
    		$date_1=calendar::add_days($dr[2],$dr[1],$dr[0],$amende_param["delai_avant_amende"],$loc_calendar,true);
    		//calcul de Date retour+delai_avant_amende+delai_1_2
    		$dr1=explode("-",$date_1);
    		$date_2=calendar::add_days($dr1[2],$dr1[1],$dr1[0],$amende_param["delai_1_2"],$loc_calendar,true);
    		//calcul de Date retour+delai_avant_amende+delai_1_2+delai_2_3
    		$dr2=explode("-",$date_2);
    		$date_3=calendar::add_days($dr2[2],$dr2[1],$dr2[0],$amende_param["delai_2_3"],$loc_calendar,true);
    		//calcul de Date retour+delai_avant_amende+delai_1_2+delai_2_3+delai_recouvrement
    		$dr3=explode("-",$date_3);
    		$date_recouvrement=calendar::add_days($dr3[2],$dr3[1],$dr3[0],$amende_param["delai_recouvrement"],$loc_calendar,true);
    		$time=mktime(0,0,0,date("m"),date("d"),date("Y"));
    		$niveau=0;
    		if (($time>calendar::maketime($date_1))&&($time<=calendar::maketime($date_2))) 
    			$niveau=1;
    		else if (($time>calendar::maketime($date_2))&&($time<=calendar::maketime($date_3)))
				$niveau=2;
			else if (($time>calendar::maketime($date_3))&&($time<=calendar::maketime($date_recouvrement)))
				$niveau=3;
			else if ($time>calendar::maketime($date_recouvrement)) $niveau=4;
			
			$amende["niveau"]=$niveau;		    		
   		 	$amende["date_pret"]=$r->pret_date;
   	 		$amende["date_retour"]=$r->pret_retour;
   	 		$amende["niveau_relance"]=$r->niveau_relance;
   	 		$amende["date_relance"]=$r->date_relance;
    	}	
    	return $amende;
    }
    
    public function get_max_level() {
    	$level=0;
    	$level_normal=0;
    	$level_min=0;
    	$printed=0;
    	$level_min_id_expl=0;
    	$date_relance='';
    	$t=array("level"=>0, "level_normal"=>0);
    	$max=-1;
    	$min=-1;
		if (is_countable($this->t_id_expl)) {
			for ($i=0; $i<count($this->t_id_expl); $i++) {
				if ($this->t_id_expl[$i]["amende"]["niveau"]>$level_normal) { 
					$level_normal=$this->t_id_expl[$i]["amende"]["niveau"]; 
					$max=$i; 
				}
				if ($this->t_id_expl[$i]["amende"]["niveau_relance"]>$level_min) {
					$level_min=$this->t_id_expl[$i]["amende"]["niveau_relance"];
					$min=$i;
				}
    		}
    	}
    	if ($max>=0) {
    		$level=$this->t_id_expl[$max]["amende"]["niveau_relance"];
    	}
    	if ($min>=0) {
    		$printed=$this->t_id_expl[$min]["printed"];
    		$level_min_id_expl=$this->t_id_expl[$min]["id_expl"];
    		$date_relance=$this->t_id_expl[$min]["amende"]["date_relance"];
    	}
    	
     	$t["level"]=$level;
    	$t["level_normal"]=$level_normal;
    	$t["level_min"]=$level_min;
    	$t["printed"]=$printed;
    	$t["level_min_id_expl"]=$level_min_id_expl;
    	$t["level_min_date_relance"]=$date_relance;
    	return $t;
    }
}
?>