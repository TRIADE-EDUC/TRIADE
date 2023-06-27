<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: aut_pass1.inc.php,v 1.19 2017-10-20 13:00:40 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/author.class.php");

// la taille d'un paquet de notices
$lot = AUTHOR_PAQUET_SIZE; // defini dans ./params.inc.php

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_suppr_auteurs"], ENT_QUOTES, $charset)."</h2>";

$res = pmb_mysql_query("SELECT author_id from authors left join responsability on responsability_author=author_id where responsability_author is null and author_see=0 ");
$affected=0;
if($affected = pmb_mysql_num_rows($res)){
	while ($ligne=pmb_mysql_fetch_object($res)) {
		$auteur=new auteur($ligne->author_id);
		$auteur->delete();
	}
}

//Nettoyage des informations d'autorités pour les sous collections
auteur::delete_autority_sources();

// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec, $affected, '1');
