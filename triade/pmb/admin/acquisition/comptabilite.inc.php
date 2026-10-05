<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: comptabilite.inc.php,v 1.27.4.1 2025/03/13 16:40:33 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $action, $ent, $id_entity, $id_bibli, $id, $libelle, $msg, $date_deb, $date_fin, $def;

// gestion des exercices comptables
require_once("$class_path/entites.class.php");
require_once("$class_path/exercices.class.php");
require_once("$include_path/templates/comptabilite.tpl.php");

function show_list_biblio() {
	global $msg;
	global $charset;

	//Récupération de l'utilisateur
  	$requete_user = "SELECT userid FROM users where username='".SESSlogin."' limit 1 ";
	$res_user = pmb_mysql_query($requete_user);
	$row_user=pmb_mysql_fetch_row($res_user);
	$user_userid=$row_user[0];

	//Affichage de la liste des etablissements auxquels a acces l'utilisateur
	$q = entites::list_biblio($user_userid);
	$res = pmb_mysql_query($q);
	$nbr = intval(pmb_mysql_num_rows($res));

	$error = false;
	if(!$nbr) {
		//Pas d'etablissements définis pour l'utilisateur
		$error = true;
		$error_msg = htmlentities($msg["acquisition_err_coord"],ENT_QUOTES, $charset)."<div class='row'></div>";
	}

	if ($error) {
		error_message($msg[321], $error_msg.htmlentities($msg["acquisition_err_par"],ENT_QUOTES, $charset), '1', './admin.php?categ=acquisition');
		die;
	}

	if ($nbr == '1') {
		$row = pmb_mysql_fetch_object($res);
		show_list_exer($row->id_entite);
	} else {
		print list_accounting_entites_ui::get_instance(array('num_user' => $user_userid))->get_display_list();
	}
}

function show_list_exer($id_entite) {
	global $msg;
	global $charset;

	$biblio = new entites($id_entite);
	print "<div class='row'><label class='etiquette'>".htmlentities($biblio->raison_sociale,ENT_QUOTES,$charset)."</label></div>";
	print list_configuration_acquisition_compta_ui::get_instance(array('num_entite' => $id_entite))->get_display_list();
}

if(empty($ent) && !empty($id_entity)) {
	$ent = $id_entity;
}
if(empty($ent) && !empty($id_bibli)) {
	$ent = $id_bibli;
}
switch($action) {
	case 'list':
		show_list_exer($ent);
		break;
	case 'add':
	    $exercice = new exercices();
	    print $exercice->get_form($ent);
		break;
	case 'modif':
		if (exercices::exists($id)) {
		    $exercice = new exercices($id);
		    print $exercice->get_form($ent);
		} else {
			show_list_exer($ent);
		}
		break;
	case 'save':
	case 'update':
		// vérification validité des données fournies.
		//Pas deux libelles d'exercices identiques pour la même entité
		$nbr = exercices::existsLibelle($ent, stripslashes($libelle), $id);
		if ( $nbr > 0 ) {
			error_form_message($libelle.$msg["acquisition_compta_already_used"]);
			break;
		}
		if ($date_deb && $date_fin) {	//Vérification des dates
			//Date fin > date début
			if($date_deb > $date_fin) {
				error_form_message($libelle.$msg["acquisition_compta_date_inf"]);
				break;
			}
		}
		$ex = new exercices($id);
		$ex->set_properties_from_form();
		$ex->save();
		if (isset($def) && $def) $ex->setDefault();
		show_list_exer($ent);
		break;
	case 'del':
	case 'delete':
		if($id) {
			$total1 = exercices::hasBudgetsActifs($id);
			$total2 = exercices::hasActesACtifs($id);
			if (($total1+$total2)==0) {
				exercices::delete($id);
				show_list_exer($ent);
			} else {
				$msg_suppr_err = $msg['acquisition_compta_used'] ;
				if ($total1) $msg_suppr_err .= "<br />- ".$msg['acquisition_compta_used_bud'] ;
				if ($total2) $msg_suppr_err .= "<br />- ".$msg['acquisition_compta_used_act'] ;

				error_message($msg[321], $msg_suppr_err, 1, 'admin.php?categ=acquisition&sub=compta&action=list&ent='.$ent);
			}

		} else {
			show_list_exer($ent);
		}
		break;
	case 'clot':
	case 'cloture':
		//On vérifie que tous les budgets sont cloturés et toutes les commandes archivées
		if($id) {
			$total1 = exercices::hasBudgetsActifs($id);
			$total2 = exercices::hasActesActifs($id);
			if (($total1+$total2)==0) {
				$ex = new exercices($id);
				$ex->statut='0';
				$ex->save();
				show_list_exer($ent);
			} else {
				$msg_suppr_err = $msg['acquisition_compta_actif'] ;
				if ($total1) $msg_suppr_err .= "<br />- ".$msg['acquisition_compta_used_bud'] ;
				if ($total2) $msg_suppr_err .= "<br />- ".$msg['acquisition_compta_used_act'] ;

				error_message($msg[321], $msg_suppr_err, 1, 'admin.php?categ=acquisition&sub=compta&action=list&ent='.$ent);
			}
		} else {
			show_list_exer($ent);
		}
		break;
	default:
		show_list_biblio();
		break;
}
