<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fichier_saisie.inc.php,v 1.4 2017-04-20 16:25:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($idfiche)) $idfiche = 0;
if(!isset($act)) $act = '';

require_once($class_path."/fiche.class.php");

$fiche = new fiche($idfiche);
switch($act){
	
	case 'save_and_new':
		$p_perso=new parametres_perso('gestfic0');
		$nberrors=$p_perso->check_submited_fields();
		if ($nberrors) {
			error_message_history($msg['notice_champs_perso'],$p_perso->error_message,1);
		} else {
			$fiche->save();
			print $fiche->show_edit_form();
		}
		break;
		
	case 'update':
		$p_perso=new parametres_perso('gestfic0');
		$nberrors=$p_perso->check_submited_fields();
		if ($nberrors) {
			error_message_history($msg['notice_champs_perso'],$p_perso->error_message,1);
		} else {
			$fiche->save();
		}
		break;
		
	default:
		print $fiche->show_edit_form();
		break;
}