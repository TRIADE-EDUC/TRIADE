<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: livraisons.inc.php,v 1.45 2023/05/04 10:35:19 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $msg, $charset, $id_bibli, $id, $id_liv;

if(!isset($id)) {
	$id = 0;
}
$id = intval($id);

// gestion des livraisons
require_once("$class_path/entites.class.php");
require_once("$class_path/actes.class.php");
include_once("$include_path/templates/actes.tpl.php");
require_once("$class_path/liens_actes.class.php");
include_once("$include_path/templates/livraisons.tpl.php");
require_once($class_path.'/accounting/accounting_livraisons_controller.class.php');

//Affiche la liste des livraisons pour un etablissement
function show_list_liv($id_bibli) {
	global $accounting_livraisons_ui_user_input;
	global $accounting_livraisons_ui_status;
	
	$filters = array();
	$filters['user_input'] = stripslashes($accounting_livraisons_ui_user_input ?? "");
	$filters['status'] = $accounting_livraisons_ui_status;
	
	$list_accounting_livraisons_ui = new list_accounting_livraisons_ui($filters);
	print $list_accounting_livraisons_ui->get_display_list();	
}

//Affiche le formulaire de création de livraison depuis une commande
function show_from_cde($id_bibli, $id_cde) {
	global $msg, $charset;
	global $livr_modif_form, $frame_show_from_cde, $form_search, $bt_enr;

	$cde = new actes($id_cde);
	$fou = new entites($cde->num_fournisseur);
	$bibli = new entites($id_bibli);
	$exer = new exercices($cde->num_exercice);

	$form = $livr_modif_form;
	$form = str_replace('<!-- frame_show -->', $frame_show_from_cde, $form);

	$form = str_replace('<!-- bouton_enr -->', $bt_enr, $form);
	$form = str_replace('<!-- form_search -->', $form_search, $form);
	$form = str_replace('!!form_title!!', htmlentities($msg['acquisition_liv_cre'], ENT_QUOTES, $charset), $form);
	$form = str_replace('!!id_bibli!!', $id_bibli, $form);
	$form = str_replace('!!lib_bibli!!', htmlentities($bibli->raison_sociale, ENT_QUOTES, $charset), $form);
	$form = str_replace('!!lib_exer!!', htmlentities($exer->libelle, ENT_QUOTES, $charset), $form);

	$form = str_replace('!!id_cde!!', $id_cde, $form);
	$lien_cde = "<a href=\"./acquisition.php?categ=ach&sub=cmde&action=modif&id_bibli=".$id_bibli."&id_cde=".$id_cde."\">".htmlentities($cde->numero, ENT_QUOTES, $charset)."</a>";
	$form = str_replace('!!num_cde!!', $lien_cde, $form);
	$form = str_replace('!!date_cre!!', formatdate(today()), $form);
	$form = str_replace('!!id_liv!!', 0, $form);
	$form = str_replace('!!numero!!', '', $form);
	$form = str_replace('!!id_fou!!', $cde->num_fournisseur, $form);
	$form = str_replace('!!lib_fou!!', htmlentities($fou->raison_sociale, ENT_QUOTES, $charset), $form);
	$form = str_replace('!!comment!!', '', $form);
	$form = str_replace('!!ref!!', '', $form);
	print $form;
}

//Affiche le formulaire de modification de livraison
function show_form_liv($id_bibli, $id_liv) {
	global $msg, $charset;
	global $livr_modif_form, $frame_show, $bt_sup, $bt_enr, $form_search;
	global $pmb_type_audit, $bt_audit;

	$liv = new actes($id_liv);
	$fou = new entites($liv->num_fournisseur);
	$id_cde = liens_actes::getParent($id_liv);
	$cde = new actes($id_cde);
	$bibli = new entites($id_bibli);
	$exer = new exercices($liv->num_exercice);

	$form = $livr_modif_form;
	$form = str_replace('<!-- frame_show -->', $frame_show, $form);
	if( ($cde->statut & STA_ACT_ARC) == STA_ACT_ARC ) { 	//La commande est archivée donc le bl non modifiable
	} else {	//Le bl est modifiable
		$form = str_replace('<!-- bouton_sup -->', $bt_sup, $form);
		$form = str_replace('<!-- bouton_enr -->', $bt_enr, $form);
		$form = str_replace('<!-- form_search -->', $form_search, $form);
	}
	$form = str_replace('!!form_title!!', htmlentities($msg['acquisition_liv_mod'], ENT_QUOTES, $charset), $form);
	$form = str_replace('!!id_bibli!!', $id_bibli, $form);
	$form = str_replace('!!lib_bibli!!', htmlentities($bibli->raison_sociale, ENT_QUOTES, $charset), $form);
	$form = str_replace('!!lib_exer!!', htmlentities($exer->libelle, ENT_QUOTES, $charset), $form);

	$form = str_replace('!!id_cde!!', $id_cde, $form);
	$lien_cde = "<a href=\"./acquisition.php?categ=ach&sub=cmde&action=modif&id_bibli=".$id_bibli."&id_cde=".$id_cde."\">".htmlentities($cde->numero, ENT_QUOTES, $charset)."</a>";
	$form = str_replace('!!num_cde!!', $lien_cde, $form);
	$form = str_replace('!!date_cre!!', formatdate($liv->date_acte), $form);
	$form = str_replace('!!id_liv!!', $id_liv, $form);
	$form = str_replace('!!numero!!', htmlentities($liv->numero, ENT_QUOTES, $charset), $form);
	$form = str_replace('!!id_fou!!', $liv->num_fournisseur, $form);
	$form = str_replace('!!lib_fou!!', htmlentities($fou->raison_sociale, ENT_QUOTES, $charset), $form);
	$form = str_replace('!!comment!!', htmlentities($liv->commentaires, ENT_QUOTES, $charset), $form);
	$form = str_replace('!!ref!!', htmlentities($liv->reference, ENT_QUOTES, $charset), $form);
	if ($id_liv && $pmb_type_audit) {
		$form = str_replace('<!-- bouton_audit -->', $bt_audit, $form);
	}
	print $form;
}

//Supprime la livraison
function sup_liv($id_liv, $id_cde) {
	$cde = new actes($id_cde);
	$cde->statut = ($cde->statut & (~STA_ACT_REC) | STA_ACT_ENC); //Statut commande = soldé->en cours
	$cde->update_statut();

	actes::delete($id_liv);
	liens_actes::delete($id_liv);
}

accounting_livraisons_controller::set_id_bibli($id_bibli);
accounting_livraisons_controller::set_id_acte($id_liv);
accounting_livraisons_controller::proceed($id);

