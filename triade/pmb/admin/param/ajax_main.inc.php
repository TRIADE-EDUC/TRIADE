<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.10 2024/01/22 14:54:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $charset, $action, $form_valeur_param, $comment_param, $form_id_param;
global $type_param, $sstype_param, $valeur_param;

require_once('./admin/param/param_func.inc.php');
require_once ($class_path.'/translation.class.php');

switch($action) {
	case 'modif':
		include("./admin/param/param_modif.inc.php");
		break;
	case 'update':
	    $form_id_param = intval($form_id_param);
		$requete = "update parametres set "; 
		$requete .= "valeur_param='$form_valeur_param', ";
		$requete .= "comment_param='$comment_param' ";
		$requete .= "where id_param='$form_id_param' ";
		pmb_mysql_query($requete);
		
		if(parameter::is_translated('', '', $form_id_param)) {
    		$translation = new translation($form_id_param, "parametres");
    		$translation->update("valeur_param", "form_valeur_param", "text");
		} else {
		    translation::delete($form_id_param, 'parametres');
		}
		$valeur_param = $form_valeur_param;
		// Si $form_valeur_param contient un balise html... on formate la valeur pour l'affichage
		if (preg_match("/<.+>/", $form_valeur_param)) {
		    $valeur_param = "<pre class='params_pre'>"
		        .htmlentities($form_valeur_param, ENT_QUOTES, $charset);
		        "</pre>";
		}
		print encoding_normalize::json_encode(array('param_id'=> $form_id_param, 'param_value' => stripslashes($valeur_param), 'param_comment' => stripslashes($comment_param)));	
		break;
	case 'add':
		param_form();
		break;
	case 'update_value':
		$valeur_param = trim(stripslashes($valeur_param));
		if($type_param && $sstype_param && $valeur_param != '') {
			$query = "UPDATE parametres set
				valeur_param = '".addslashes($valeur_param)."'
				WHERE type_param = '".addslashes($type_param)."'
				AND sstype_param = '".addslashes($sstype_param)."'
			";
			pmb_mysql_query($query);
			print encoding_normalize::json_encode(array('type_param'=> $type_param, 'sstype_param' => $sstype_param, 'valeur_param' => $valeur_param));
		} else {
			$valeur_param = $type_param."_".$sstype_param;
			global ${$valeur_param};
			print encoding_normalize::json_encode(array('type_param'=> $type_param, 'sstype_param' => $sstype_param, 'valeur_param' => ${$valeur_param}));
		}
		break;
	default:
//		show_param();
		$list_parameters_ui = new list_parameters_ui();
		print $list_parameters_ui->get_display_list();
		break;
	}
