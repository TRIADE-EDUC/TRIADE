<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.13 2015-12-01 10:48:57 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once('./admin/param/param_func.inc.php');

$admin_layout = str_replace('!!menu_sous_rub!!', $msg[1600], $admin_layout);
print $admin_layout;
		
print "<div class='row'>";
echo window_title($database_window_title.$msg[1600].$msg[1003].$msg[1001]);
		
switch($action) {
	case 'modif':
		include("./admin/param/param_modif.inc.php");
		break;
	case 'update':
		$requete = "update parametres set "; 
		$requete .= "valeur_param='$form_valeur_param', ";
		$requete .= "comment_param='$comment_param' ";
		$requete .= "where id_param='$form_id_param' ";
		$res = @pmb_mysql_query($requete, $dbh);
		show_param($dbh);
		break;
	case 'add':
		param_form();
		break;
	default:
		show_param($dbh);
		break;
	}

print "</div>";
