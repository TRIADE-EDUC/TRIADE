<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa_planning_ajax.inc.php,v 1.3 2020/11/05 10:02:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/resa_planning.class.php");

switch($sub){	
	// Mise à jour de la prévision
	case 'update_resa_planning':
		if ($id && $date) {
			//On vérifie la date
			$tresa_date = explode('-', extraitdate($date));
			if (strlen($tresa_date[2])==1) $tresa_date[2] = '0'.$tresa_date[2];
			if (strlen($tresa_date[1])==1) $tresa_date[1] = '0'.$tresa_date[1];
			$date_imp = implode('', $tresa_date);
			if ((@checkdate($tresa_date[1], $tresa_date[2], $tresa_date[0])) 
				&& (strlen($date_imp)==8)) {
				$r = new resa_planning($id);
				$d_resa = implode('-',$tresa_date);
				if (($param1 == "1") && ($d_resa < $r->resa_date_fin)) {
					$r->resa_date_debut=$d_resa;
					$r->save();
					$date_resa = formatdate($r->resa_date_debut);
				} else if (($param1 == "2") && ($r->resa_date_debut < $d_resa)) {
					$r->resa_date_fin=$d_resa;
					$r->save();
					$date_resa = formatdate($r->resa_date_fin);
				} else {
					if ($param1 == "1") $date_resa = $r->aff_resa_date_debut;
					elseif ($param1 == "2") $date_resa = $r->aff_resa_date_fin;
				}
			}
		}
		ajax_http_send_response($date_resa);
		break;
	default :
	    switch ($action) {
	        case "list":
	            lists_controller::proceed_ajax($object_type, 'resa_planning');
	            break;
	    }
	break;		
	
}



