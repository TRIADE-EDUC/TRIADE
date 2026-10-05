<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: budgets.inc.php,v 1.48.4.1 2025/03/13 16:40:33 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $msg, $action;
global $id_bibli, $id_bud, $id_rub, $id_parent;
global $id, $id_entity;
global $libelle, $mnt;

// gestion des budgets
require_once("$class_path/entites.class.php");
require_once("$class_path/exercices.class.php");
require_once("$class_path/budgets.class.php");
require_once("$class_path/rubriques.class.php");
require_once("$include_path/templates/budgets.tpl.php");
require_once($class_path."/configuration/configuration_acquisition_controller.class.php");

if(empty($id_bibli) && !empty($id_entity)) {
	$id_bibli = $id_entity;
}
if(empty($id_bud) && !empty($id)) {
	$id_bud = $id;
}
configuration_acquisition_controller::set_model_class_name('budgets');
configuration_acquisition_controller::set_list_ui_class_name('list_configuration_acquisition_budget_ui');
configuration_acquisition_controller::set_id_entity($id_bibli);

//Affiche la liste des etablissements
function show_list_biblio() {
	global $msg, $charset;

	//Récupération de l'utilisateur
 	$requete_user = "SELECT userid FROM users where username='".SESSlogin."' limit 1 ";
	$res_user = pmb_mysql_query($requete_user);
	$row_user=pmb_mysql_fetch_row($res_user);
	$user_userid=$row_user[0];

	//Affichage de la liste des etablissements auxquelles a acces l'utilisateur
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
		//Rappel du nom de l'etablissement
		$biblio = new entites($row->id_entite);
		print "<div class='row'><label class='etiquette'>".htmlentities($biblio->raison_sociale,ENT_QUOTES,$charset)."</label></div>";
		print list_configuration_acquisition_budget_ui::get_instance(array('num_entite' => $row->id_entite))->get_display_list();
	} else {
		print list_accounting_entites_ui::get_instance(array('num_user' => $user_userid))->get_display_list();
	}
}

function redirect_display_rub_form($id_bud, $id_rub, $id_parent) {
	$url_base = configuration_acquisition_controller::get_url_base();
	if(headers_sent()) {
		print "
				<script type='text/javascript'>
					window.location.href='".$url_base."&action=modif_rub&id_bud=".$id_bud."&id_rub=".$id_rub."&id_parent=".$id_parent."';
				</script>";
	} else {
		header('Location: '.$url_base.'&action=modif_rub&id_bud='.$id_bud.'&id_rub='.$id_rub.'&id_parent='.$id_parent);
	}
}

function afficheSousRubriques($id_bud, $id_rub, &$form, $indent=0) {
	global $lig_rub, $lig_rub_img, $lig_indent;

	$bud = new budgets($id_bud);
	$q = budgets::listRubriques($id_bud, $id_rub);
	$list_n = pmb_mysql_query($q);

	while($row=pmb_mysql_fetch_object($list_n)){

		$form = str_replace('<!-- sous_rub'.$id_rub.' -->', $lig_rub[0].'<!-- sous_rub'.$id_rub.' -->', $form);
		$marge = '';
		for($i=0;$i<$indent;$i++){
			$marge.= $lig_indent;
		}
		$form = str_replace('<!-- marge -->', $marge, $form);

		if (rubriques::countChilds($row->id_rubrique)) {
			$form = str_replace('<!-- img_plus -->', $lig_rub_img, $form);
		} else {
			$form = str_replace('<!-- img_plus -->', '', $form);
		}
		$form = str_replace('<!-- sous_rub -->', '<!-- sous_rub'.$row->id_rubrique.' -->', $form);
		$form = str_replace('!!id_rub!!', $row->id_rubrique, $form);
		$form = str_replace('!!id_parent!!', $row->num_parent, $form);
		$form = str_replace('!!lib_rub!!', $row->libelle, $form);
		if ($bud->type_budget == TYP_BUD_RUB ) {
			$form = str_replace('!!mnt!!', $row->montant, $form);
		} else {
			$form = str_replace('!!mnt!!', '&nbsp;', $form);
		}
		$form = str_replace('!!ncp!!', $row->num_cp_compta, $form);

		afficheSousRubriques($id_bud, $row->id_rubrique, $form, $indent+1);
	}
}

//Traitement des actions
switch($action) {
	case 'add_rub' :
	case 'modif_rub' :
		$rub = new rubriques($id_rub);
		$rub->num_budget = $id_bud;
		$rub->num_parent = $id_parent;
		print $rub->get_form();
		break;
	case 'update_rub' :
		//vérification des éléments saisis
		if ($mnt && (!is_numeric($mnt) || $mnt < 0.00 || $mnt > 9999999999.99 )) {
			error_form_message($libelle." ".$msg["acquisition_rub_mnt_error"]);
			break;
		}

		$rub = new rubriques($id_rub);
		$rub->num_budget = $id_bud;
		$rub->set_properties_from_form();
		$rub->save();

		$bud = new budgets($id_bud);
		if ($bud->type_budget == TYP_BUD_RUB) {
			//màj des rubriques supérieures
			rubriques::maj($id_parent, TRUE);
			//recalcul du montant global de budget
			budgets::calcMontant($id_bud);
		} else {
			//màj des rubriques supérieures sans recalcul
			rubriques::maj($id_parent, FALSE);
		}

		if ($id_parent) {
			$rub_parent = new rubriques($id_parent);
			redirect_display_rub_form($id_bud, $id_parent, $rub_parent->num_parent);
		} else {
			configuration_acquisition_controller::redirect_display_form($id_bud);
		}
		break;
	case 'del_rub':
		if($id_rub) {
			$rub = new rubriques($id_rub);
			$total1 = rubriques::hasLignes($id_rub);
			$total2 = rubriques::countChilds($id_rub);
			if ( ($total1==0) && $total2==0 ) {
				rubriques::delete($id_rub);
				$bud = new budgets($id_bud);
				if ($bud->type_budget == TYP_BUD_RUB) {
					//màj des rubriques supérieures
					rubriques::maj($id_parent, TRUE);
					//recalcul du montant global de budget
					budgets::calcMontant($id_bud);
				} else {
					//màj des rubriques supérieures sans recalcul
					rubriques::maj($id_parent, FALSE);
				}

				if ($id_parent) {
					$rub_parent = new rubriques($id_parent);
					redirect_display_rub_form($id_bud, $id_parent, $rub_parent->num_parent);
				} else {
					configuration_acquisition_controller::redirect_display_form($id_bud);
				}
			} else {
				$msg_suppr_err = $msg['acquisition_rub_used'] ;
				if ($total1) $msg_suppr_err .= "<br />- ".$msg['acquisition_rub_used_lg'] ;
				if ($total2) $msg_suppr_err .= "<br />- ".$msg['acquisition_rub_used_childs'] ;
				error_message($msg[321],$msg_suppr_err, 1, configuration_acquisition_controller::get_url_base().'&action=modif_rub&id_bud='.$id_bud.'&id_rub='.$id_rub.'&id_parent='.$rub->num_parent);
			}
		} else {
			if ($id_parent) {
				$rub_parent = new rubriques($id_parent);
				redirect_display_rub_form($id_bud, $id_parent, $rub_parent->num_parent);
			}
			else {
				configuration_acquisition_controller::redirect_display_form($id_bud);
			}
		}
		break;
	default:
		configuration_acquisition_controller::proceed($id_bud);
		break;
}

