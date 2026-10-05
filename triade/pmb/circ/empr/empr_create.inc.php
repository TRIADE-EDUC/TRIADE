<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_create.inc.php,v 1.19.8.1 2025/04/03 14:54:25 dgoron Exp $

// récupération code barre en vue saisie d'un emprunteur (modifié F CEROVETTI 12/2007 pour marcher aussi avec ALPHANUMERIQUE )
// corrigé et augmenté par Eric ROBERT

// modifier ds administration, outils, paramètres, généraux :  "num_carte_auto" en 0 ,1 , 10 , 12, 13 ou autre selon le fonctionnement désiré:

// Explication 
// Numéro de carte de lecteur automatique et nombre de caratères du préfixe
// Numéro de carte de lecteur automatique ? 
//  0: Non (si utilisation de cartes pré-imprimées)
//  1: Oui, entièrement numérique
//  2,a,b,c: Oui avec préfixe: a=longueur du préfixe, b=nombre de chiffres de la partie numérique, c=préfixe fixé (facultatif)
  

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $msg, $database_window_title, $pmb_num_carte_auto;

// récupération code barre en vue saisie d'un emprunteur
echo window_title($database_window_title.$msg[42].$msg[1003].$msg[1001]);

$pmb_num_carte_auto_array=array();
$pmb_num_carte_auto_array=explode(",",$pmb_num_carte_auto);

$cb_a_creer = '';
if ($pmb_num_carte_auto_array[0] == "1" ) {
	$requete="DELETE from empr_temp where sess not in (select SESSID from sessions)";
	pmb_mysql_query($requete);
	$rqt = "select max(CAST(empr_cb AS UNSIGNED))+1 as max_cb FROM (select empr_cb from empr UNION select cb FROM empr_temp WHERE sess <>'".SESSid."') tmp";
	$res = pmb_mysql_query($rqt);
	$cb_initial = pmb_mysql_fetch_object($res);
	$cb_a_creer = (string)$cb_initial->max_cb;
	$requete="INSERT INTO empr_temp (cb ,sess) VALUES ('".addslashes($cb_a_creer)."','".SESSid."')";
	pmb_mysql_query($requete);	
} elseif ($pmb_num_carte_auto_array[0] == "2" ) {
	$requete="DELETE from empr_temp where sess not in (select SESSID from sessions)";
	pmb_mysql_query($requete);

	$long_prefixe = intval($pmb_num_carte_auto_array[1]);
	$nb_chiffres = intval($pmb_num_carte_auto_array[2]);
	$prefix = $pmb_num_carte_auto_array[3];
	
    $rqt =  "SELECT CAST(SUBSTRING(empr_cb,".($long_prefixe+1).") AS UNSIGNED) AS max_cb, SUBSTRING(empr_cb,1,".($long_prefixe*1).") AS prefixdb FROM (select empr_cb from empr".($long_prefixe?" WHERE empr_cb LIKE '".$prefix."%'":"")." UNION select cb FROM empr_temp WHERE sess <>'".SESSid."') tmp ORDER BY max_cb DESC limit 0,1" ; // modif f cerovetti pour sortir dernier code barre tri par ASCII
	$res = pmb_mysql_query($rqt);
	$cb_initial = pmb_mysql_fetch_object($res);
	$cb_a_creer = ($cb_initial->max_cb*1)+1;
	if (!$nb_chiffres) $nb_chiffres=strlen($cb_a_creer);
	if (!$prefix) $prefix = $cb_initial->prefixdb;
	
	$cb_a_creer = $prefix.substr((string)str_pad($cb_a_creer, $nb_chiffres, "0", STR_PAD_LEFT),-$nb_chiffres);
	$requete="INSERT INTO empr_temp (cb ,sess) VALUES ('".addslashes($cb_a_creer)."','".SESSid."')";
	pmb_mysql_query($requete);
} elseif ($pmb_num_carte_auto_array[0] == '3' ) {
	
	$num_carte_auto_filename = $base_path.'/circ/empr/'.trim($pmb_num_carte_auto_array[1]).'.inc.php';
	$num_carte_auto_fctname = trim($pmb_num_carte_auto_array[1]);
	if (file_exists($num_carte_auto_filename)){
		require_once($num_carte_auto_filename);
		if(function_exists($num_carte_auto_fctname)) {
			$cb_a_creer = $num_carte_auto_fctname();
		}
	}
}

get_cb($msg[42], "", $msg[43], './circ.php?categ=empr_saisie', 1, (string)$cb_a_creer, 1);
