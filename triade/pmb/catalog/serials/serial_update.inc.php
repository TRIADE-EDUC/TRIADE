<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serial_update.inc.php,v 1.86 2017-09-05 13:39:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($forcage)) $forcage = 0;

require_once($class_path."/entities/entities_serials_controller.class.php");

echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[346], $serial_header);

$entities_serials_controller = new entities_serials_controller($serial_id);
if($entities_serials_controller->has_rights()) {
	// On a besoin de récupérer le tit1 sur forcage
	if ($forcage == 1) {
		$tab= unserialize(stripslashes($ret_url));
		foreach($tab->GET as $key => $val){
			if (get_magic_quotes_gpc())
				$GLOBALS[$key] = $val;
				else {
					add_sl($val);
					$GLOBALS[$key] = $val;
				}
		}
		foreach($tab->POST as $key => $val){
			if (get_magic_quotes_gpc())
				$GLOBALS[$key] = $val;
				else {
					add_sl($val);
					$GLOBALS[$key] = $val;
				}
		}
	}
	$p_perso=new parametres_perso("notices");
	$nberrors=$p_perso->check_submited_fields();
	$tit1 = clean_string($f_tit1);
	if(trim($tit1)&&(!$nberrors)) {
		$updated = $entities_serials_controller->proceed_update();
		if($updated) {
			print "<div class='row'><div class='msg-perio'>".$msg['maj_encours']."</div></div>";
			$retour = "./catalog.php?categ=serials&sub=view&serial_id=".$entities_serials_controller->get_id();
			print "
			<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" style=\"display:none\">
			<input type=\"hidden\" name=\"id_form\" value=\"$id_form\">
			</form>
			<script type=\"text/javascript\">document.dummy.submit();</script>
			";
		} else {
			// echec de la requete
			error_message($msg[4004] , $msg['catalog_serie_impossible'], 1, './catalog.php?categ=serials');
		}
	} else {
		if (!trim($tit1)) {
			// erreur : le champ tit1 est vide
			if($id) {
				$notitle_message = $msg[280];
			} else {
				$notitle_message = $msg[279];
			}
			error_message('', $notitle_message, 1, './catalog.php?categ=serials');
		} else {
			error_message_history($msg["notice_champs_perso"],$p_perso->error_message,1);
		}
	}
}