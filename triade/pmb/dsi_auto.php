<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dsi_auto.php,v 1.13.4.1 2025/01/30 09:08:07 tsamson Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "";  
$base_title = "\$msg[dsi_menu_title]";
$base_noheader=1;
$base_nosession=1;
$base_nocheck = 1 ;

require_once ("$base_path/includes/init.inc.php");  

use Pmb\Common\Helper\MySQL;

if (!$user) $user=$argv[1];
if (!$password) $password=$argv[2];
if (!$database) $database=$argv[3];

$include_path = $base_path."/includes" ;
require_once("$include_path/db_param.inc.php");
require_once("$include_path/mysql_connect.inc.php");
$dbh = connection_mysql();
// on checke si l'utilisateur existe et si le mot de passe est OK
$query = "SELECT count(1) FROM users WHERE username='$user' AND pwd='".MySQL::password($password)."'";
$result = pmb_mysql_query($query, $dbh);
$valid_user = pmb_mysql_result($result, 0, 0);
if (!$valid_user) die("Interdit : utilisateur invalide ");

if (!$dsi_auto) die("DSI Auto pas activée sur base $database (user=$user) Version noyau: $pmb_bdd_version ");


	/* param par défaut */	
	$requete_param = "SELECT * FROM users WHERE username='$user' LIMIT 1 ";
	$res_param = pmb_mysql_query($requete_param, $dbh);
	$field_values = pmb_mysql_fetch_row( $res_param );
	$array_values = pmb_mysql_fetch_array( $res_param );
	$i = 0;
	while ($i < pmb_mysql_num_fields($res_param)) {
		$field = pmb_mysql_field_name($res_param, $i) ;
		$field_deb = substr($field,0,6);
		switch ($field_deb) {
			case "deflt_" :
				global ${$field};
				${$field}=$field_values[$i];
				break;
			case "deflt2" :
				global ${$field};
				${$field}=$field_values[$i];
				break;
			case "param_" :
				global ${$field};
				${$field}=$field_values[$i];
				break ;
			case "value_" :
				global ${$field};
				${$field}=$field_values[$i];
				break ;
			default :
				break ;
			}
		$i++;
		}
	$requete_nom = "SELECT nom, prenom, user_email, userid, username, user_lang  FROM users WHERE username='$user' ";
	$res_nom = pmb_mysql_query($requete_nom, $dbh);
	@$param_nom = pmb_mysql_fetch_object( $res_nom );
	$lang = $param_nom->user_lang ;
	$PMBusernom=$param_nom->nom ;
	$PMBuserprenom=$param_nom->prenom ;
	$PMBuseremail=$param_nom->user_email ;	
	// pour que l'id user soit dispo partout
	define('SESSuserid'	, $param_nom->userid);
	$PMBuserid = $param_nom->userid;
	$PMBusername = $param_nom->username;
	
	$messages = new XMLlist("$include_path/messages/$lang.xml", 0);
	$messages->analyser();
	$msg = $messages->table;



include_once("$class_path/bannette.class.php");
include_once("$class_path/equation.class.php");
include_once("$class_path/classements.class.php");
require_once("$class_path/docs_location.class.php");
include_once("$class_path/rss_flux.class.php");
require_once("./dsi/func_pro.inc.php");
require_once("./dsi/func_common.inc.php");

$action_diff_aff = "<h1>".$msg['dsi_dif_auto_titre']."</h1>" ;

// récupérer les bannettes à diffuser


$requete = "SELECT id_bannette, proprio_bannette FROM bannettes ";
$requete .= " WHERE (DATE_ADD(date_last_envoi, INTERVAL periodicite DAY) <= sysdate()) and bannette_auto=1 " ;
$res = pmb_mysql_query($requete, $dbh);
$liste_bannette = [];
while(($bann=pmb_mysql_fetch_object($res))) {
	$liste_bannette[]=$bann->id_bannette ;
}
pmb_mysql_free_result($res);
for ($i=0 ; $i < sizeof($liste_bannette) ; $i++) {
	$bannette = new bannette($liste_bannette[$i]) ;

	$action_diff_aff .= $msg['dsi_dif_vidage'].": ".$bannette->nom_bannette."<br />" ; 
	if(!$bannette->limite_type) $action_diff_aff .= $bannette->vider();
	$action_diff_aff .= $msg['dsi_dif_remplissage'].": ".$bannette->nom_bannette ; 
	$action_diff_aff .= $bannette->remplir();
	$action_diff_aff .= $bannette->purger();
	$action_diff_aff .= "<strong>".$msg['dsi_dif_diffusion'].": ".$bannette->nom_bannette."</strong><br />" ; 
	$action_diff_aff .= $bannette->diffuser();
}

print $action_diff_aff ;
// deconnection MYSql
pmb_mysql_close($dbh);
