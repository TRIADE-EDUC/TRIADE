<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: constitution.inc.php,v 1.11 2019-06-05 09:04:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $idetagere, $msg, $etagere_constitution_form, $idcaddie;

require_once($class_path."/etagere.class.php");

if(!isset($action)) $action = '';
if(!isset($idetagere)) $idetagere = 0;
switch ($action) {
	case 'edit_etagere':
		if (etagere::check_rights($idetagere)) {
			$myEtagere = new etagere($idetagere);
			$etagere_constitution_form = str_replace('!!formulaire_titre!!', $msg['etagere_constitution_de']." ".$myEtagere->name, $etagere_constitution_form);
			$etagere_constitution_form = str_replace('!!idetagere!!', $idetagere, $etagere_constitution_form);
			$etagere_constitution_form = str_replace('!!constitution!!', $myEtagere->constitution(1), $etagere_constitution_form);
			print pmb_bidi($etagere_constitution_form) ;
		}
		break;
	case 'save_etagere':
		if (etagere::check_rights($idetagere)) {
			$myEtagere = new etagere($idetagere);
			// suppression
			$rqt = "delete from etagere_caddie where etagere_id='".$idetagere."' ";
			$res = pmb_mysql_query($rqt) ;
			for ($i=0 ; $i < sizeof($idcaddie) ; $i++) {
				if (caddie::check_rights($idcaddie[$i])) $myEtagere->add_panier($idcaddie[$i]) ;
			}
		}
		aff_etagere("constitution",0);
		break;
	default:
		aff_etagere("constitution",0);
	}
