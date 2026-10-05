<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_update.inc.php,v 1.31.4.1 2025/03/05 07:56:33 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

use Pmb\Digitalsignature\Models\DocnumCertifier;

global $acces_m, $gestion_acces_active, $gestion_acces_user_notice, $class_path, $PMBuserid, $f_notice, $f_explnum_id, $charset, $forcage;
global $ret_url, $retour, $nberrors, $msg, $pmb_explnum_controle_doublons, $base_path, $current_module, $signature, $nb_per_page_search;
global $record_link, $record_title, $conservervignette, $f_statut_chk, $book_lender_id, $f_bulletin, $f_nom, $f_url;
global $f_explnum_statut, $f_url_vignette;

//verification des droits de modification notice
$acces_m=1;
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	$acces_m = $dom_1->getRights($PMBuserid,$f_notice,8);
}

if ($acces_m==0) {

	if (!$f_explnum_id) {
		error_message('', htmlentities($dom_1->getComment('mod_noti_error'), ENT_QUOTES, $charset), 1, '');
	} else {
		error_message('', htmlentities($dom_1->getComment('mod_enum_error'), ENT_QUOTES, $charset), 1, '');
	}

} else {

	if(!isset($forcage)){
		$forcage = 0;
	}
	if ($forcage == 1) {
		$tab = unserialize(stripslashes($ret_url));
		if(!isset($tab->GET) || !is_array($tab->GET)) {
		    $tab->GET = [];
		}
		foreach($tab->GET as $key => $val){
			add_sl($val);
			$GLOBALS[$key] = $val;
		}
		if(!isset($tab->POST) || !is_array($tab->POST)) {
		    $tab->POST = [];
		}
		foreach($tab->POST as $key => $val){
			add_sl($val);
			$GLOBALS[$key] = $val;
		}
		if(!isset($tab->FILES) || !is_array($tab->FILES)) {
		    $tab->FILES = [];
		}
		foreach($tab->FILES as $key => $val){
			add_sl($val);
			$_FILES[$key] = $val;
		}
	}
	$retour = notice::get_permalink($f_notice);

	//Vérification des champs personalisés
	$p_perso=new parametres_perso("explnum");
	$nberrors=$p_perso->check_submited_fields();
	if ($nberrors) {
		error_message_history($msg["notice_champs_perso"],$p_perso->error_message,1);
		exit();
	}

	$explnum = new explnum($f_explnum_id);

    $docNumCertifier = new DocnumCertifier($explnum);
    $check = $docNumCertifier->checkSignExists();
    if ($check) {
        return print return_error_message($msg["540"], $msg["digital_signature_already_signed_docnum_del"], 1, "./catalog.php?categ=isbd&id=".$explnum->explnum_notice);
    }

	$explnum->set_p_perso($p_perso);
	if(!$forcage && $pmb_explnum_controle_doublons) {
		$doublons = $explnum->has_doublons($_FILES['f_fichier']['tmp_name']);
		if(!empty($doublons)) {
			$dbls = count($doublons);

			$new_name = $base_path.'/temp/explnum_doublon_'.$explnum->explnum_notice;
			move_uploaded_file($_FILES['f_fichier']['tmp_name'], $new_name);
			$_FILES['f_fichier']['tmp_name'] = $new_name;

			//affichage de l'erreur, en passant tous les param postes (serialise) pour l'eventuel forcage
			$tab = new stdClass();
			$tab->POST = $_POST;
			$tab->GET = $_GET;
			$tab->FILES = $_FILES;
			$ret_url = htmlentities(serialize($tab), ENT_QUOTES, $charset);

			print "
					<br /><div class='erreur'>".$msg[540]."</div>
					<script type='text/javascript' src='".$base_path."/javascript/tablist.js'></script>
					<div class='row'>
						<div class='colonne10'>
							<img src='".get_url_icon('error.gif')."' class='align_left'>
						</div>
						<div class='colonne80'>
							<strong>".$msg["gen_signature_docnum_erreur_similaire"]."</strong>
						</div>
					</div>
					<div class='row'>
						<form class='form-".$current_module."' name='dummy' enctype='multipart/form-data'  method='post' action='".$base_path."/catalog.php?categ=explnum_update&sub=create'>
							<input type='hidden' name='forcage' value='1'>
							<input type='hidden' name='signature' value='".$signature."'>
							<input type='hidden' name='ret_url' value='".$ret_url."'>
							<input type='button' name='ok' class='bouton' value=' ".$msg[76]." ' onClick='window.location = \"".$base_path."/catalog.php?categ=explnum_create&id=".$f_notice."\";'>
							<input type='submit' class='bouton' name='bt_forcage' value=' ".htmlentities($msg["gen_signature_forcage"], ENT_QUOTES, $charset)." '>
						</form>
					</div>
						";
			if ($dbls < $nb_per_page_search) {
				$maxAffiche = $dbls;
				echo "<div class='row'><strong>".sprintf($msg["gen_signature_erreur_similaire_nb"], $dbls, $dbls)."</strong></div>";
			}else{
				$maxAffiche = $nb_per_page_search;
				echo "<div class='row'><strong>".sprintf($msg["gen_signature_erreur_similaire_nb"], $maxAffiche, $dbls)."</strong></div>";
			}
			$enCours = 1;
			foreach ($doublons as $doublon) {
				if($enCours <= $maxAffiche) {
					$record_link = '#';
					$record_title = '';
					if ($doublon->explnum_notice) {
						require_once($class_path.'/notice.class.php');
						$record_link = notice::get_gestion_link($doublon->explnum_notice);
						$record_title = notice::get_notice_title($doublon->explnum_notice);
					} else if ($doublon->explnum_bulletin) {
					    $record_link = './catalog.php?categ=serials&sub=view&sub=bulletinage&action=view&bul_id=' . intval($doublon->explnum_bulletin);
					    $query = 'select bulletin_titre from bulletins where bulletin_id = ' . intval($doublon->explnum_bulletin);
						$record_title = pmb_mysql_result(pmb_mysql_query($query), 0, 0);
					}
					echo "
						<div class='row'>
							<a href='".$record_link."' target='_blank'>".$doublon->explnum_nom." (".$record_title.")</a>
						</div>";
					$enCours++;
				} else {
					break;
				}
			}
			echo "<script type='text/javascript'>document.forms['dummy'].elements['ok'].focus();</script>";
			exit();
		}
	} //fin du controle de dedoublonage

	if(!isset($conservervignette)){
		$conservervignette = 0;
	}
	if(!isset($f_statut_chk)){
		$f_statut_chk = 0;
	}
	if(!isset($book_lender_id)){
		$book_lender_id = array();
	}
	$explnum->mise_a_jour($f_notice, $f_bulletin, $f_nom, $f_url, $retour, $conservervignette, $f_statut_chk, $f_explnum_statut, $book_lender_id, $forcage, $f_url_vignette);
}
