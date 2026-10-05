<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: factures.inc.php,v 1.48 2023/05/04 10:35:19 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $msg, $charset, $id_bibli, $id, $id_fac;

if(!isset($id)) {
	$id = 0; 
}
$id = intval($id);

// gestion des factures
require_once("$class_path/entites.class.php");
require_once("$class_path/actes.class.php");
require_once("$class_path/liens_actes.class.php");
require_once("$include_path/templates/actes.tpl.php");
require_once("$include_path/templates/factures.tpl.php");
require_once($class_path.'/accounting/accounting_invoices_controller.class.php');

//Affiche la liste des factures pour un etablissement
function show_list_fac($id_bibli, $id_exercice = 0) {
	global $accounting_invoices_ui_user_input;
	global $accounting_invoices_ui_status;
	
	$filters = array();
	$filters['user_input'] = stripslashes($accounting_invoices_ui_user_input ?? "");
	$filters['status'] = $accounting_invoices_ui_status;
	
	$list_accounting_invoices_ui = new list_accounting_invoices_ui($filters);
	print $list_accounting_invoices_ui->get_display_list();		
}

//Affiche le formulaire de création de facture depuis une commande
function show_from_cde($id_bibli, $id_cde) {
	global $msg, $charset;
	global $fact_modif_form, $frame_show_from_cde, $form_search, $bt_enr;

	$form = $fact_modif_form;

	$cde = new actes($id_cde);
	$num_cde = htmlentities($cde->numero, ENT_QUOTES, $charset);
	$id_fou = $cde->num_fournisseur;
	$fou = new entites($id_fou);

	$bibli = new entites($id_bibli);
	$exer = new exercices($cde->num_exercice);
		
	$form = str_replace('<!-- frame_show -->', $frame_show_from_cde, $form);

	$form = str_replace('<!-- bouton_enr -->', $bt_enr, $form);
	$form = str_replace('<!-- form_search -->', $form_search, $form);
	$form = str_replace('!!form_title!!', htmlentities($msg['acquisition_fac_cre'], ENT_QUOTES, $charset), $form);
	$form = str_replace('!!id_bibli!!', $id_bibli, $form);
	$form = str_replace('!!lib_bibli!!', htmlentities($bibli->raison_sociale, ENT_QUOTES, $charset), $form);
	$form = str_replace('!!id_exer!!', $exer->id_exercice, $form);
	$form = str_replace('!!lib_exer!!', htmlentities($exer->libelle, ENT_QUOTES, $charset), $form);

	$form = str_replace('!!id_cde!!', $id_cde, $form);
	$lien_cde = "<a href=\"./acquisition.php?categ=ach&sub=cmde&action=modif&id_bibli=".$id_bibli."&id_cde=".$id_cde."\">".$num_cde."</a>";
	$form = str_replace('!!num_cde!!', $lien_cde, $form);
	$form = str_replace('!!date_cre!!', formatdate(today()), $form);
	$form = str_replace('!!id_fac!!', 0, $form);
	$form = str_replace('!!numero!!', '', $form);
	$form = str_replace('!!date_pay!!', '', $form);
	$form = str_replace('!!num_pay!!', '', $form);
	if($cde->date_paiement != '0000-00-00')
		$form = str_replace('!!date_pay_cde!!', formatdate($cde->date_paiement), $form);
	else
		$form = str_replace('!!date_pay_cde!!', '', $form);
	if (!$cde->num_paiement)
		$form = str_replace('!!num_pay_cde!!', '', $form);
	else
		$form = str_replace('!!num_pay_cde!!', htmlentities($cde->num_paiement,ENT_QUOTES, $charset), $form);
	$form = str_replace('!!id_fou!!', $id_fou, $form);
	$form = str_replace('!!lib_fou!!', htmlentities($fou->raison_sociale, ENT_QUOTES, $charset), $form);
	$form = str_replace('!!comment!!', '', $form);
	$form = str_replace('!!ref!!', '', $form);
	$form = str_replace('!!devise!!', htmlentities($cde->devise, ENT_QUOTES, $charset), $form);
	print $form;
}

//Affiche le formulaire de modification de facture
function show_form_fac($id_bibli, $id_fac) {
	global $msg, $charset;
	global $fact_modif_form, $frame_show, $bt_sup, $bt_enr, $bt_pay, $form_search;
	global $pmb_type_audit, $bt_audit;

	$form = $fact_modif_form;

	$factu = new actes($id_fac);

	$id_cde = liens_actes::getParent($id_fac);
	$cde = new actes($id_cde);

	$fou = new entites($factu->num_fournisseur);

	$bibli = new entites($id_bibli);
	$exer = new exercices($factu->num_exercice);

	$form = str_replace('<!-- frame_show -->', $frame_show, $form);

	if( (($factu->statut & STA_ACT_PAY) == STA_ACT_PAY) || (($factu->statut & STA_ACT_ARC) == STA_ACT_ARC) )  {
		//La facture est payée ou archivée, donc non modifiable
	} else {
		$form = str_replace('<!-- bouton_pay -->', $bt_pay, $form);
		$form = str_replace('<!-- bouton_sup -->', $bt_sup, $form);
		$form = str_replace('<!-- bouton_enr -->', $bt_enr, $form);
		$form = str_replace('<!-- form_search -->', $form_search, $form);
	}

	$form = str_replace('!!form_title!!', htmlentities($msg['acquisition_fac_mod'], ENT_QUOTES, $charset), $form);
	$form = str_replace('!!id_bibli!!', $id_bibli, $form);
	$form = str_replace('!!lib_bibli!!', htmlentities($bibli->raison_sociale, ENT_QUOTES, $charset), $form);
	$form = str_replace('!!id_exer!!', $exer->id_exercice, $form);
	$form = str_replace('!!lib_exer!!', htmlentities($exer->libelle, ENT_QUOTES, $charset), $form);

	$form = str_replace('!!id_cde!!', $id_cde, $form);
	$lien_cde = "<a href=\"./acquisition.php?categ=ach&sub=cmde&action=modif&id_bibli=".$id_bibli."&id_cde=".$id_cde."\">".htmlentities($cde->numero, ENT_QUOTES, $charset)."</a>";
	$form = str_replace('!!num_cde!!', $lien_cde, $form);
	$form = str_replace('!!date_cre!!', formatdate($factu->date_acte), $form);
	$form = str_replace('!!id_fac!!', $id_fac, $form);
	$form = str_replace('!!numero!!', htmlentities($factu->numero, ENT_QUOTES, $charset), $form);
	if($factu->date_paiement != '0000-00-00') $form = str_replace('!!date_pay!!', formatdate($factu->date_paiement), $form);
	else $form = str_replace('!!date_pay!!', '', $form);
	if (!$factu->num_paiement) $form = str_replace('!!num_pay!!', '', $form);
	else $form = str_replace('!!num_pay!!', htmlentities($factu->num_paiement,ENT_QUOTES, $charset), $form);
	if($cde->date_paiement != '0000-00-00') $form = str_replace('!!date_pay_cde!!', formatdate($cde->date_paiement), $form);
	else $form = str_replace('!!date_pay_cde!!', '', $form);
	if (!$cde->num_paiement) $form = str_replace('!!num_pay_cde!!', '', $form);
	else $form = str_replace('!!num_pay_cde!!', htmlentities($cde->num_paiement, ENT_QUOTES, $charset) , $form);
	$form = str_replace('!!id_fou!!', $factu->num_fournisseur, $form);
	$form = str_replace('!!lib_fou!!', htmlentities($fou->raison_sociale, ENT_QUOTES, $charset), $form);
	$form = str_replace('!!comment!!', htmlentities($factu->commentaires, ENT_QUOTES, $charset), $form);
	$form = str_replace('!!ref!!', htmlentities($factu->reference, ENT_QUOTES, $charset), $form);
	$form = str_replace('!!devise!!', htmlentities($factu->devise, ENT_QUOTES, $charset), $form );

	if ($id_fac && $pmb_type_audit) {
		$form = str_replace('<!-- bouton_audit -->', $bt_audit, $form);
	}
	print $form;
}

//Supprime la facture
function sup_fac($id_fac, $id_cde) {
	$cde = new actes($id_cde);
	$cde->statut = ($cde->statut & (~STA_ACT_FAC)); //Statut commande = facturé->non facturé
	$cde->statut = ($cde->statut & (~STA_ACT_PAY)); //Statut commande = payé->non payé
	$cde->update_statut();

	actes::delete($id_fac);
	liens_actes::delete($id_fac);
}

accounting_invoices_controller::set_id_bibli($id_bibli);
accounting_invoices_controller::set_id_acte($id_fac);
accounting_invoices_controller::proceed($id);
