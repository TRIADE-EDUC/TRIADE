<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bul_expl_update.inc.php,v 1.40 2023/10/24 09:57:08 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $charset;
global $gestion_acces_active, $gestion_acces_user_notice, $PMBuserid, $current_module;
global $expl_id, $org_cb, $f_ex_cb, $f_ex_location, $f_ex_nbparts, $expl_bulletin, $abt_id, $serial_circ_add;

require_once("$class_path/serialcirc_diff.class.php");
require_once($class_path."/serialcirc.class.php");
require_once($class_path.'/audit.class.php');

// mise a jour de l'entete de page
if(!$expl_id) {
	// pas d'id, c'est une creation
	echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[4007], $serial_header);
} else {
	echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[4008], $serial_header);
}


//verification des droits de modification notice
$acces_m=1;
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	$acces_j = $dom_1->getJoin($PMBuserid,8,'bulletin_notice');
	$q = "select count(1) from bulletins $acces_j where bulletin_id=".$expl_bulletin;
	$r = pmb_mysql_query($q);
	if(pmb_mysql_result($r,0,0)==0) {
		$acces_m=0;
	}
}

if ($acces_m==0) {

	if (!$expl_id) {
		error_message('', htmlentities($dom_1->getComment('mod_bull_error'), ENT_QUOTES, $charset), 1, '');
	} else {
		error_message('', htmlentities($dom_1->getComment('mod_expl_error'), ENT_QUOTES, $charset), 1, '');
	}

} else {
		
	//Verification des champs personalises
	$p_perso=new parametres_perso("expl");
	$nberrors=$p_perso->check_submited_fields();
	if ($nberrors) {
		error_message_history($msg["notice_champs_perso"],$p_perso->error_message,1);
		exit();
	}
	// controle sur le nouveau code barre si applicable :
	if($org_cb != $f_ex_cb) {
		// si le nouveau code-barre est deja utilise, on reste sur l'ancien
		$requete = "SELECT expl_id FROM exemplaires WHERE expl_cb='$f_ex_cb'";
		
		$myQuery = pmb_mysql_query($requete);
		if(!($result=pmb_mysql_result($myQuery, 0, 0))) {
			$expl_cb = $f_ex_cb;
		} else {
			// Verif si expl_id est celui poste
			if($expl_id == $result[0]) {
				$expl_cb = $org_cb;
			} else {
				//Erreur: code barre deja existant
				error_message_history($msg[301],$msg[303],1);
				exit();
			}
		}	
	} else {
		$expl_cb = $f_ex_cb;
	}
		
	// on recupere les valeurs 
	$formlocid="f_ex_section".$f_ex_location ;
	$f_ex_section=${$formlocid} ;
	
	if(!is_numeric($f_ex_nbparts) || !$f_ex_nbparts) $f_ex_nbparts=1;
	
	$exemplaire = new exemplaire($expl_cb, $expl_id, 0, $expl_bulletin);
	$exemplaire->set_properties_from_form();
	$exemplaire->save();
	if(!$expl_id) {
		$expl_id = $exemplaire->expl_id;
	}
	if(isset($abt_id) && $abt_id && isset($serial_circ_add) && $serial_circ_add) {		
		$serialcirc_diff=new serialcirc_diff(0,$abt_id);
			// Si c'est à faire circuler
		if($serialcirc_diff->id){ 
			$serialcirc_diff->add_circ_expl($expl_id);
		}
	}
		
	// Mise a jour de la table notices_mots_global_index pour toutes les notices en relation avec l'exemplaire
	exemplaire::majNoticesMotsGlobalIndex($expl_id);
	
	$id_form = md5(microtime());
	print "<div class='row'><div class='msg-perio'>".$msg['maj_encours']."</div></div>";
	$retour = "./catalog.php?categ=serials&sub=view&sub=bulletinage&action=view&bul_id=" . intval($expl_bulletin);
	
	if (isset($pointage) && $pointage) {
		$templates="<script type='text/javascript'>
	
			function Fermer(obj,type_doc) {		
				var obj_1=obj+\"_1\";	
				var obj_2=obj+\"_2\";	
				var obj_3=obj+\"_3\";		
				parent.document.getElementById(obj_1).disabled = true;
				parent.document.getElementById(obj_2).disabled = true;
				parent.document.getElementById(obj_3).disabled = true;								
			 	parent.kill_frame_periodique();
			}	
	
		</script>
		<script type='text/javascript'>Fermer('$id_bull','$type_doc');</script>
		";
	} else {
		print "<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" style=\"display:none\">
		<input type=\"hidden\" name=\"id_form\" value=\"$id_form\">
		</form>
		<script type=\"text/javascript\">document.dummy.submit();</script>";
	}
}
?>