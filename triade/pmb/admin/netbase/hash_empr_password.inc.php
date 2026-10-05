<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: hash_empr_password.inc.php,v 1.9 2024/04/17 13:55:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $msg, $charset;
global $start, $v_state, $spec, $count;

require_once($class_path."/emprunteur.class.php");

// la taille d'un paquet de lecteurs
$lot = EMPR_PAQUET_SIZE*10; // defini dans ./params.inc.php

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

if(!$count) {
	$empr = pmb_mysql_query("SELECT count(1) FROM empr where empr_password_is_encrypted=0");
	$count = pmb_mysql_result($empr, 0, 0);
}

print netbase::get_display_progress_title($msg["hash_empr_password"]);

$query = pmb_mysql_query("SELECT id_empr, empr_password, empr_login FROM empr where empr_password_is_encrypted=0 LIMIT $lot");
// start <= count : test supplémentaire pour s'assurer de ne pas boucler à l'infini
// problème rencontré : login vide et 2 login identiques (en théorie impossible)
if(pmb_mysql_num_rows($query) && ($start <= $count)) {

	if (!$start) {
		$requete = "CREATE TABLE if not exists empr_passwords (
			id_empr INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			empr_password VARCHAR( 255 ) NOT NULL default '')";
		pmb_mysql_query($requete);
		$requete = "INSERT IGNORE INTO empr_passwords SELECT id_empr, empr_password FROM empr where empr_password_is_encrypted=0";
		pmb_mysql_query($requete);
	}
	
	print netbase::get_display_progress($start, $count);
   	while ($row = pmb_mysql_fetch_object($query) )  {
   		emprunteur::update_digest($row->empr_login,$row->empr_password);
   		emprunteur::hash_password($row->empr_login,$row->empr_password);
   	}
   	pmb_mysql_free_result($query);
	$next = $start + $lot;
	print netbase::get_current_state_form($v_state, $spec, '', $next, $count);
} else {
	$spec = $spec - HASH_EMPR_PASSWORD;
	$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["hash_empr_password_status"], ENT_QUOTES, $charset);
	$v_state .= $count." ".htmlentities($msg["hash_empr_password_status_end"], ENT_QUOTES, $charset);
	
	$requete = "show tables like 'empr_passwords'";
	if (pmb_mysql_num_rows(pmb_mysql_query($requete))) {
		$v_state .=  "<br><a href='".$base_path."/admin.php?categ=netbase' target='_parent'>".htmlentities($msg["need_to_clean_empr_passwords"], ENT_QUOTES, $charset)."</a>";
	}
	
	pmb_mysql_query('OPTIMIZE TABLE empr');
	// mise à jour de l'affichage de la jauge
	print netbase::get_display_final_progress();

	print netbase::get_process_state_form($v_state, $spec);
}	