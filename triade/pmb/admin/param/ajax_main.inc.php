<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.2 2018-08-10 08:50:41 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($action) {
	case 'modif':
		require_once('./admin/param/param_func.inc.php');
		include("./admin/param/param_modif.inc.php");
		break;
	case 'update':
		$requete = "update parametres set "; 
		$requete .= "valeur_param='$form_valeur_param', ";
		$requete .= "comment_param='$comment_param' ";
		$requete .= "where id_param='$form_id_param' ";
		$res = @pmb_mysql_query($requete, $dbh);
		print encoding_normalize::json_encode(array('param_id'=> $form_id_param, 'param_value' => stripslashes($form_valeur_param), 'param_comment' => stripslashes($comment_param)));	
		break;
	case 'add':
		require_once('./admin/param/param_func.inc.php');
		param_form();
		break;
	default:
		require_once('./admin/param/param_func.inc.php');
		show_param($dbh);
		break;
	}
