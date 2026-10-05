<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: calendar.class.php,v 1.19.16.1 2025/03/27 10:03:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class calendar {

    public function __construct() {
    }
    
    public static function get_open_days($dd,$md,$yd,$df,$mf,$yf,$loc_calendar = 0) {
    	global $pmb_utiliser_calendrier;
    	global $deflt2docs_location ;
    	global $pmb_pret_calcul_retard_date_debut_incluse;
    	
    	//on stocke la timezone actuelle pour se baser sur l'utc
    	//sinon, avec une date en heure d'été et une autre en heure d'hiver, les soustractions de mktime renvoient un float
    	$tmp_timezone = date_default_timezone_get();
    	date_default_timezone_set('UTC'); //pour les calculs
    	
    	$ndays=0;    	
	   	if ($pmb_utiliser_calendrier==1) {
	   		if (!$loc_calendar) {
	   			$loc_calendar = $deflt2docs_location;
	   		}
	   		if ($pmb_pret_calcul_retard_date_debut_incluse) {
	   			$requete="select count(date_ouverture) from ouvertures where ouvert=1 and num_location=".$loc_calendar." and date_ouverture>='".$yd."-".$md."-".$dd."' and date_ouverture<='".$yf."-".$mf."-".$df."'";
	   		} else {
	   			$requete="select count(date_ouverture) from ouvertures where ouvert=1 and num_location=".$loc_calendar." and date_ouverture>'".$yd."-".$md."-".$dd."' and date_ouverture<='".$yf."-".$mf."-".$df."'";
	   		}
	   		$resultat=pmb_mysql_query($requete);	   		
	   		if (pmb_mysql_result($resultat,0,0)) {
	   			$ndays=pmb_mysql_result($resultat,0,0);
	   		} else {
	   			//on regarde si un jour d'ouverture arrive prochainement..
	   			//si oui cela signifie que l'emprunteur n'est pas en retard et attend la prochaine ouverture
	   			$requete="select count(date_ouverture) from ouvertures where ouvert=1 and num_location=".$loc_calendar." and date_ouverture >'".$yf."-".$mf."-".$df."' limit 0,1";
	   			$result=pmb_mysql_query($requete);
	   			if (pmb_mysql_result($result,0,0)) {
	   				$ndays = 0;
	   			} else {
	   				if ($pmb_pret_calcul_retard_date_debut_incluse)
	   					$ndays=1+(mktime(0,0,0,$mf,$df,$yf)-mktime(0,0,0,$md,$dd,$yd))/86400;
	   				else
	   					$ndays=(mktime(0,0,0,$mf,$df,$yf)-mktime(0,0,0,$md,$dd,$yd))/86400;
	   			}
	   		}
    	} else {
    		if ($pmb_pret_calcul_retard_date_debut_incluse) {
   				$ndays=1+(mktime(0,0,0,$mf,$df,$yf)-mktime(0,0,0,$md,$dd,$yd))/86400;
    		} else {
    			$ndays=(mktime(0,0,0,$mf,$df,$yf)-mktime(0,0,0,$md,$dd,$yd))/86400;
    		}
    	}
    	
    	date_default_timezone_set($tmp_timezone); //on remet la timezone d'origine
    	
    	return $ndays;
    }
    
    public static function add_days($dd,$md,$yd,$days,$loc_calendar = 0, $for_amendes = false) {
    	global $pmb_utiliser_calendrier;
    	global $deflt2docs_location;
    	
    	$dd = intval($dd);
    	$md = intval($md);
    	$yd = intval($yd);
    	$days = intval($days);
    	if ($pmb_utiliser_calendrier) {
    		if (!$loc_calendar && !$for_amendes) { //Si flag for_amendes, la statique est appelée depuis les amendes où loc_calendar est déja calculé et peut être égal à 0
	   			$loc_calendar = $deflt2docs_location;
	   		}	
 		   	$requete="select min(date_ouverture) from ouvertures where ouvert=1 and num_location=".$loc_calendar." and date_ouverture>=adddate('".$yd."-".$md."-".$dd."', interval $days day)";
   		 	$resultat=pmb_mysql_query($requete);
   		 	if (!pmb_mysql_num_rows($resultat)) {
   		 		$requete="select adddate('".$yd."-".$md."-".$dd."', interval $days day)";
    			$resultat=pmb_mysql_query($requete);
   		 	}
   		 	if($date=pmb_mysql_result($resultat,0,0)){
   		 		return $date;
	    	} 
    	}
    	$requete="select adddate('".$yd."-".$md."-".$dd."', interval $days day)";
    	$resultat=pmb_mysql_query($requete);

    	$date=pmb_mysql_result($resultat,0,0);
    	return $date;	
    }
 
 	public static function maketime($mysql_date) {
 	    if (!empty($mysql_date)) {
            $t_date=explode("-",$mysql_date);
     		return mktime(0,0,0,$t_date[1],$t_date[2],$t_date[0]);
 	    }
 	    return false;
 	}
}