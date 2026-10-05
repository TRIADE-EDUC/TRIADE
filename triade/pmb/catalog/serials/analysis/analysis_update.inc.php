<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: analysis_update.inc.php,v 1.76 2023/09/06 06:55:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $charset, $current_module, $pmb_notice_controle_doublons;
global $analysis_id, $serial_id, $bul_id, $nb_per_page_search;
global $gestion_acces_active, $gestion_acces_user_notice, $PMBuserid, $pmb_synchro_rdf;
global $serial_header, $ret_url, $forcage, $id_form, $id, $f_tit1, $f_year;

if(!isset($forcage)) $forcage = 0;

require_once($class_path."/notice_doublon.class.php");
require_once($class_path."/serials.class.php");

// valorisation du champ f_year à partir de la date de parution du bulletin
// on le fait ici car il peut être nécessaire pour le calcul de dédoublonnage
if($bul_id && empty($f_year)) {
	$f_year = substr(bulletinage::get_date_date_from_id($bul_id),0,4);
}

$sign = new notice_doublon();
$signature = $sign->gen_signature();
if ($forcage == 1) {
	$tab= unserialize( urldecode($ret_url) );
	foreach($tab->GET as $key => $val){
		$GLOBALS[$key] = $val;	    
	}	
	foreach($tab->POST as $key => $val){
		$GLOBALS[$key] = $val;
	}
} elseif ($pmb_notice_controle_doublons != 0 && !$analysis_id) {	
	//Si control de dédoublonnage activé	
	$requete="select signature, niveau_biblio ,notice_id from notices where signature='$signature'";
	if($serial_id)	$requete.= " and notice_id != '$analysis_id' ";
	//$requete.= " limit 1 ";
		
	$result=pmb_mysql_query($requete);	
	if ($dbls=pmb_mysql_num_rows($result)) {
		//affichage de l'erreur, en passant tous les param postés (serialise) pour l'éventuel forcage 
		$tab = new stdClass();
		$tab->POST = addslashes_array($_POST);
		$tab->GET = addslashes_array($_GET);
		$ret_url= urlencode(serialize($tab));
		require_once("$class_path/mono_display.class.php");
		require_once("$class_path/serial_display.class.php");
	   
		print "
			<br /><div class='erreur'>$msg[540]</div>
			<script type='text/javascript' src='./javascript/tablist.js'></script>
			<div class='row'>
				<div class='colonne10'>
					<img src='".get_url_icon('error.gif')."' class='align_left'>
				</div>
				<div class='colonne80'>
					<strong>".$msg["gen_signature_erreur_similaire"]."</strong>
				</div>
			</div>
			<div class='row'>
				<form class='form-$current_module' name='dummy'  method='post' action='./catalog.php?categ=serials&sub=analysis&action=update&bul_id=$bul_id&analysis_id=$analysis_id'>
					<input type='hidden' name='forcage' value='1'>
					<input type='hidden' name='signature' value='$signature'>
					<input type='hidden' name='ret_url' value='$ret_url'>
					<input type='button' name='ok' class='bouton' value=' $msg[76] ' onClick='history.go(-1);'>
					<input type='submit' class='bouton' name='bt_forcage' value=' ".htmlentities($msg["gen_signature_forcage"], ENT_QUOTES,$charset)." '>
				</form>
				
			</div>
			";
		if($dbls<$nb_per_page_search){
			$maxAffiche=$dbls;
			echo "<div class='row'><strong>".sprintf($msg["gen_signature_erreur_similaire_nb"],$dbls,$dbls)."</strong></div>";
		}else{
			$maxAffiche=$nb_per_page_search;
			echo "<div class='row'><strong>".sprintf($msg["gen_signature_erreur_similaire_nb"],$maxAffiche,$dbls)."</strong></div>";
		}
		$enCours=1;
		while($enCours<=$maxAffiche){
			$r=pmb_mysql_fetch_object($result);
			if($r->niveau_biblio != 's' && $r->niveau_biblio != 'a') {
				// notice de monographie
				$nt = new mono_display($r->notice_id,1,notice::get_permalink($r->notice_id));
			} elseif($r->niveau_biblio == 's'){
				// on a affaire à un périodique
				$nt = new serial_display($r->notice_id,1,serial::get_permalink($r->notice_id));
			}else{
				// on a affaire à un article
				$bulletin_id = analysis::getBulletinIdFromAnalysisId($r->notice_id);
				$nt = new serial_display($r->notice_id,1,'',bulletinage::get_permalink($bulletin_id));
			}
			echo "
				<div class='row'>
				$nt->result
		 	    </div>
				<script>document.getElementById('el".$r->notice_id."Child').setAttribute('startOpen','Yes');</script>
				<script type='text/javascript'>document.forms['dummy'].elements['ok'].focus();</script>";	
			$enCours++;
		}
		exit();
	}
}

	
//verification des droits de modification notice
//droits d'acces
if ($gestion_acces_active==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
}

$acces_m=1;
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	$dom_1= $ac->setDomain(1);
	$acces_m = $dom_1->getRights($PMBuserid,$serial_id,8);
}

if ($acces_m==0) {

	if (!$analysis_id) {
		error_message('', htmlentities($dom_1->getComment('mod_bull_error'), ENT_QUOTES, $charset), 1, '');
	} else {
		error_message('', htmlentities($dom_1->getComment('mod_depo_error'), ENT_QUOTES, $charset), 1, '');
	}

} else {

	// mise à jour de l'entête de page
	echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[4023], $serial_header);
	
	$p_perso=new parametres_perso("notices");
	$nberrors=$p_perso->check_submited_fields();
	$tit1 = clean_string($f_tit1);
	if(trim($tit1)&&(!$nberrors)) {
		//Traitement des périos et bulletins
		global $perio_type, $bull_type;
		global  $f_perio_new, $f_perio_new_issn;
		global  $f_bull_new_num, $f_bull_new_date, $f_bull_new_mention, $f_bull_new_titre;
		//Perios
		if($perio_type == 'insert_new' && !$serial_id){
			$new_serial = new serial();
			$values = array();
			$values['tit1'] = $f_perio_new;
			$values['code'] = $f_perio_new_issn;
			$values['niveau_biblio'] = "s";
			$values['niveau_hierar'] = "1";
			$serial_id =  $new_serial->update($values);
			if($pmb_synchro_rdf){
				$synchro_rdf->addRdf($serial_id,0);
			}
		}
		//Bulletin
		if($bull_type == 'insert_new' && !$bul_id) {
			$req = "insert into bulletins set bulletin_numero='".$f_bull_new_num."',
			  mention_date='".$f_bull_new_mention."',
			  date_date='".$f_bull_new_date."',
			  bulletin_titre='".$f_bull_new_titre."',
			  bulletin_notice='".$serial_id."'";
			pmb_mysql_query($req);
			$bul_id = pmb_mysql_insert_id();
			if($pmb_synchro_rdf){
				$synchro_rdf->addRdf(0,$bul_id);
			}
		}
		if($analysis_id && $bul_id) {
		    //Assurons-nous que la relation dans analysis existe
		    //Traité pour le cas des articles de périodique orphelins
		    import_records::insert_relation_analysis_bulletin($analysis_id, $bul_id);
		}
		$myAnalysis = new analysis($analysis_id, $bul_id);
		$myAnalysis->signature = $signature;
		$myAnalysis->set_properties_from_form();
		$saved = $myAnalysis->save();
		if($saved) {
			print "<div class='row'><div class='msg-perio'>".$msg['maj_encours']."</div></div>";
			$retour = bulletinage::get_permalink($myAnalysis->get_bulletinage()->bulletin_id);
			print "
			<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" style=\"display:none\">
			<input type=\"hidden\" name=\"id_form\" value=\"$id_form\">
			</form>
			<script type=\"text/javascript\">document.dummy.submit();</script>
			";
		} else {
			error_message(	$msg[4023] ,$msg['catalog_serie_modif_depouill_imp'] ,1,"./catalog.php?categ=serials&sub=bulletinage&action=view&serial_id=$serial_id&bul_id=$bul_id");
		}
	} else {
		if (!trim($tit1)) {
			// erreur : le champ tit1 est vide
			if($id) {
				$notitle_message = $msg[280];
			} else {
				$notitle_message = $msg[279];
			}
			error_message('', $notitle_message, 1, "./catalog.php");
		} else {
			error_message_history($msg["notice_champs_perso"],$p_perso->error_message,1);
		}
	}
}

