<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: typ_doc.inc.php,v 1.24 2018-10-12 11:59:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/list/configuration/docs/list_configuration_docs_typdoc_ui.class.php");

function show_typdoc_list() {
	print list_configuration_docs_typdoc_ui::get_instance()->get_display_list();
}

function show_typdoc_form($libelle='', $pret='31', $short_loan_duration='1', $resa='15', $tdoc_codage_import='', $tdoc_owner=0, $id=0, $tarif='0.00') {
	global $dbh,$msg,$charset;
	global $admin_typdoc_form;
	global $pmb_quotas_avances;
	global $pmb_gestion_financiere,$pmb_gestion_tarif_prets;
	global $pmb_short_loan_management;
	
	$admin_typdoc_form = str_replace('!!id!!', $id, $admin_typdoc_form);

	if(!$id) {
		$admin_typdoc_form = str_replace('!!form_title!!', $msg[122], $admin_typdoc_form);
	} else {
		$admin_typdoc_form = str_replace('!!form_title!!', $msg[124], $admin_typdoc_form);
	}
	$admin_typdoc_form = str_replace('!!libelle!!', htmlentities($libelle,ENT_QUOTES, $charset), $admin_typdoc_form);
	$admin_typdoc_form = str_replace('!!libelle_suppr!!', addslashes($libelle), $admin_typdoc_form);
	
	$form_pret='';
	if (!$pmb_quotas_avances) {
		$form_pret = "
		<div class='row' >
			<label class='etiquette' for='form_pret'>".$msg[123]."</label>
		</div>
		<div class='row' >
			<input type='text' id='form_pret' name='form_pret' value='$pret' maxlength='10' class='saisie-10em' />
		</div>";
	}
	$admin_typdoc_form = str_replace('<!-- form_pret -->', $form_pret, $admin_typdoc_form);

	$form_short_loan_duration='';
	if (!$pmb_quotas_avances && $pmb_short_loan_management) {
		$form_short_loan_duration = "
		<div class='row' >
			<label class='etiquette' for='form_short_loan_duration'>".$msg['short_loan_duration_wdays']."</label>
		</div>
		<div class='row' >
			<input type='text' id='form_short_loan_duration' name='form_short_loan_duration' value='$short_loan_duration' maxlength='10' class='saisie-10em' />
		</div>";
	}
	$admin_typdoc_form = str_replace('<!-- form_short_loan_duration -->', $form_short_loan_duration, $admin_typdoc_form);
	
	$form_resa='';
	if (!$pmb_quotas_avances) {
		$form_resa = "
		<div class='row' >
			<label class='etiquette' for='form_resa'>".$msg['duree_resa']."</label>
		</div>
		<div class='row' >
			<input type='text' id='form_resa' name='form_resa' value='$resa' maxlength='10' class='saisie-10em' />
		</div>";
	}
	$admin_typdoc_form = str_replace('<!-- form_resa -->', $form_resa, $admin_typdoc_form);
	
	$admin_typdoc_form = str_replace('!!tdoc_codage_import!!', $tdoc_codage_import, $admin_typdoc_form);
	$combo_lender= gen_liste ("select idlender, lender_libelle from lenders order by lender_libelle ", "idlender", "lender_libelle", "form_tdoc_owner", "", $tdoc_owner, 0, $msg[556],0,$msg["proprio_generique_biblio"]) ;
	$admin_typdoc_form = str_replace('<!-- lender -->', $combo_lender, $admin_typdoc_form);
	
	$tarif_pret='';
	if (($pmb_gestion_financiere)&&($pmb_gestion_tarif_prets==1)) {
		$tarif_pret="
		<div class='row'>
			<label class='etiquette' for='form_tarif_pret'>".$msg['typ_doc_tarif']."</label>
		</div>
		<div class='row'>
			<input type='text' id='form_tarif_pret' name='form_tarif_pret' value='$tarif' maxlength='10' class='saisie-5em' />
		</div>";
	}
	$admin_typdoc_form = str_replace('<!-- tarif_pret -->', $tarif_pret, $admin_typdoc_form);
	
	print confirmation_delete("./admin.php?categ=docs&sub=typdoc&action=del&id=");
	print $admin_typdoc_form;
}

switch($action) {
	
	case 'update':
		// vérification validité des données fournies.
		$id+=0;
		if(isset($form_pret)) $form_pret+=0; else $form_pret=0;
		if(isset($form_resa)) $form_resa+=0; else $form_resa=0;
		if(isset($form_short_loan_duration)) $form_short_loan_duration+=0; else $form_short_loan_duration=0;
		if(isset($form_tarif_pret)) $form_tarif_pret+=0; else $form_tarif_pret=0;
		$q = "SELECT count(1) FROM docs_type WHERE (tdoc_libelle='$form_libelle' AND idtyp_doc!='$id' )  LIMIT 1 ";
		$res = pmb_mysql_query($q, $dbh);
		$nbr = pmb_mysql_result($res, 0, 0);
		if ($nbr > 0) {
			error_form_message($form_libelle.$msg['docs_label_already_used']);
		} else {
			// O.k., now if the id already exist UPDATE else INSERT
			$q =(($id)?"update ":"insert into ");
			$q.= "docs_type set tdoc_libelle='$form_libelle', ";
			$q.= ((!$pmb_quotas_avances)?"duree_pret='$form_pret', duree_resa='$form_resa', ":'');
			$q.= ((!$pmb_quotas_avances && $pmb_short_loan_management)?"short_loan_duration='$form_short_loan_duration', ":'');
			$q.= (($pmb_gestion_financiere && $pmb_gestion_tarif_prets==1)?"tarif_pret='$form_tarif_pret', ":'');
			$q.= "tdoc_codage_import='$form_tdoc_codage_import', tdoc_owner='$form_tdoc_owner' ";
			$q.= (($id)?"where idtyp_doc=$id ":'');
			$res = pmb_mysql_query($q, $dbh);
		}
		show_typdoc_list();
		break;
	
	case 'add':
		if(empty($form_libelle) && empty($form_pret) && empty($form_resa)) show_typdoc_form();
			else show_typdoc_list();
		break;
		
	case 'modif':
		$id+=0;
		if($id){
			$q = "SELECT tdoc_libelle,duree_pret,short_loan_duration,duree_resa,tdoc_codage_import,tdoc_owner,tarif_pret FROM docs_type WHERE idtyp_doc='$id' LIMIT 1 ";
			$res = pmb_mysql_query($q, $dbh);
			if(pmb_mysql_num_rows($res)) {
				$row=pmb_mysql_fetch_object($res);
				show_typdoc_form($row->tdoc_libelle, $row->duree_pret, $row->short_loan_duration, $row->duree_resa, $row->tdoc_codage_import, $row->tdoc_owner, $id, $row->tarif_pret);
			} else {
				show_typdoc_list();
			}
		} else {
			show_typdoc_list();
		}
		break;
		
	case 'del':
		$id+=0;
		if($id) {
			// requête sur 'exemplaires' pour voir si ce typdoc est encore utilisé
			$total = 0;
			$total = pmb_mysql_result(pmb_mysql_query("select count(1) from exemplaires where expl_typdoc ='".$id."' ", $dbh), 0, 0);
			if ($total==0) {
				$q = "DELETE FROM docs_type WHERE idtyp_doc=$id ";
				$res = pmb_mysql_query($q, $dbh);
				show_typdoc_list();
			} else {
				$msg_suppr_err = $admin_liste_jscript;
				$msg_suppr_err .= $msg[1700]." <a href='#' onclick=\"showListItems(this);return(false);\" what='typdoc_docs' item='".$id."' total='".$total."' alt=\"".$msg["admin_docs_list"]."\" title=\"".$msg["admin_docs_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
				error_message(	$msg[294], $msg_suppr_err, 1, 'admin.php?categ=docs&sub=typdoc&action=');
			}
		} else {
			show_typdoc_list();	
		}
		break;
		
	default:
		show_typdoc_list();
		break;
	}
