<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: calendrier.inc.php,v 1.2 2019/05/29 12:03:09 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $action, $pmb_utiliser_calendrier, $id_value, $loc_id;

switch($action){
	case "test_ouverture" :
		$retour = array();
		if ($pmb_utiliser_calendrier) {
			$req_date_calendrier = "select date_ouverture from ouvertures where date_ouverture='".$id_value."' and ouvert=1 and num_location='".$loc_id."'";
			$res_date_calendrier = pmb_mysql_query($req_date_calendrier);
			if (!pmb_mysql_num_rows($res_date_calendrier)) {
				//le jour sélectionné n'est pas un jour d'ouverture, on va chercher le prochain
				$req_date_calendrier = "select date_ouverture from ouvertures where date_ouverture>'".$id_value."' and ouvert=1 and num_location='".$loc_id."' LIMIT 1";
				$res_date_calendrier = pmb_mysql_query($req_date_calendrier);
				if (pmb_mysql_num_rows($res_date_calendrier)) {
					$row = pmb_mysql_fetch_object($res_date_calendrier);
					$retour = array(0=>$row->date_ouverture, 1=>formatdate($row->date_ouverture));
				}
			}
		}
		echo encoding_normalize::json_encode($retour);
		break;
}
