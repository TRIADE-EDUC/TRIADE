<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: statut.inc.php,v 1.13 2018-10-12 12:18:37 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/list/configuration/empr/list_configuration_empr_statut_ui.class.php");

// gestion des statut d'emprunteur
?>
<script type="text/javascript">
function test_form(form)
{
	if(form.statut_libelle.value.length == 0)
	{
		alert("<?php echo $msg[98] ?>");
		return false;
	}
	return true;
}
</script>

<?php

function show_statut($dbh) {
	print list_configuration_empr_statut_ui::get_instance()->get_display_list();
}

function statut_form($statut_libelle="", $allow_loan=1, $allow_loan_hist=0, $allow_book=1, $allow_opac=1, $allow_dsi=1, $allow_dsi_priv=1, $allow_sugg=1, $allow_liste_lecture=1, $allow_dema=1, $allow_prol=1, $allow_avis=1, $allow_tag=1, $allow_pwd=1, $allow_self_checkout=0, $allow_self_checkin=0, $allow_serialcirc=0, $allow_scan_request=0, $allow_contribution=0, $id=0) {

	global $msg;
	global $admin_empr_statut_form;
	global $charset;

	$admin_empr_statut_form = str_replace('!!id!!', $id, $admin_empr_statut_form);

	if(!$id) $admin_empr_statut_form = str_replace('!!form_title!!', $msg['empr_statut_create'], $admin_empr_statut_form);
		else $admin_empr_statut_form = str_replace('!!form_title!!', $msg['empr_statut_modif'], $admin_empr_statut_form);

	$admin_empr_statut_form = str_replace('!!libelle!!', htmlentities($statut_libelle,ENT_QUOTES, $charset), $admin_empr_statut_form);
	$admin_empr_statut_form = str_replace('!!libelle_suppr!!', addslashes($statut_libelle), $admin_empr_statut_form);

	if ($allow_loan) $checkbox="checked"; else $checkbox="";
	$admin_empr_statut_form = str_replace('!!checkbox_loan!!', $checkbox, $admin_empr_statut_form);

	if ($allow_loan_hist) $checkbox="checked"; else $checkbox="";
	$admin_empr_statut_form = str_replace('!!checkbox_loan_hist!!', $checkbox, $admin_empr_statut_form);

	if ($allow_book) $checkbox="checked"; else $checkbox="";
	$admin_empr_statut_form = str_replace('!!checkbox_book!!', $checkbox, $admin_empr_statut_form);

	if ($allow_opac) $checkbox="checked"; else $checkbox="";
	$admin_empr_statut_form = str_replace('!!checkbox_opac!!', $checkbox, $admin_empr_statut_form);

	if ($allow_dsi) $checkbox="checked"; else $checkbox="";
	$admin_empr_statut_form = str_replace('!!checkbox_dsi!!', $checkbox, $admin_empr_statut_form);

	if ($allow_dsi_priv) $checkbox="checked"; else $checkbox="";
	$admin_empr_statut_form = str_replace('!!checkbox_dsi_priv!!', $checkbox, $admin_empr_statut_form);

	if ($allow_sugg) $checkbox="checked"; else $checkbox="";
	$admin_empr_statut_form = str_replace('!!checkbox_sugg!!', $checkbox, $admin_empr_statut_form);

	if($allow_liste_lecture) $checkbox="checked"; else $checkbox="";
	$admin_empr_statut_form = str_replace('!!checkbox_liste_lecture!!', $checkbox, $admin_empr_statut_form);
	
	if ($allow_dema) $checkbox="checked"; else $checkbox="";
	$admin_empr_statut_form = str_replace('!!checkbox_dema!!', $checkbox, $admin_empr_statut_form);

	if ($allow_prol) $checkbox="checked"; else $checkbox="";
	$admin_empr_statut_form = str_replace('!!checkbox_prol!!', $checkbox, $admin_empr_statut_form);

	if ($allow_avis) $checkbox="checked"; else $checkbox="";
	$admin_empr_statut_form = str_replace('!!checkbox_avis!!', $checkbox, $admin_empr_statut_form);

	if ($allow_tag) $checkbox="checked"; else $checkbox="";
	$admin_empr_statut_form = str_replace('!!checkbox_tag!!', $checkbox, $admin_empr_statut_form);

	if ($allow_pwd) $checkbox="checked"; else $checkbox="";
	$admin_empr_statut_form = str_replace('!!checkbox_pwd!!', $checkbox, $admin_empr_statut_form);

	if ($allow_self_checkout) $checkbox="checked"; else $checkbox="";
	$admin_empr_statut_form = str_replace('!!allow_self_checkout!!', $checkbox, $admin_empr_statut_form);
	if ($allow_self_checkin) $checkbox="checked"; else $checkbox="";
	$admin_empr_statut_form = str_replace('!!allow_self_checkin!!', $checkbox, $admin_empr_statut_form);
	
	if ($allow_serialcirc) $checkbox="checked"; else $checkbox="";
	$admin_empr_statut_form = str_replace('!!allow_serialcirc!!', $checkbox, $admin_empr_statut_form);
	
	if ($allow_scan_request) $checkbox="checked"; else $checkbox="";
	$admin_empr_statut_form = str_replace('!!allow_scan_request!!', $checkbox, $admin_empr_statut_form);
	
	if ($allow_contribution) $checkbox="checked"; else $checkbox="";
	$admin_empr_statut_form = str_replace('!!allow_contribution!!', $checkbox, $admin_empr_statut_form);
	
	print confirmation_delete("./admin.php?categ=empr&sub=statut&action=del&id=");
	print $admin_empr_statut_form;

	}

switch($action) {
	case 'update':
		// vérification validité des données fournies.
		$requete = " SELECT count(1) FROM empr_statut WHERE (statut_libelle='$statut_libelle' AND idstatut!='$id' ) ";
		$res = pmb_mysql_query($requete, $dbh);
		$nbr = pmb_mysql_result($res, 0, 0);
		if ($nbr > 0) {
			error_form_message($statut_libelle.$msg["empr_statut_label_already_used"]);
		} else {
			if (!isset($allow_loan)) $allow_loan = 0;
			if (!isset($allow_loan_hist)) $allow_loan_hist = 0;
			if (!isset($allow_book)) $allow_book = 0;
			if (!isset($allow_opac)) $allow_opac = 0;
			if (!isset($allow_dsi)) $allow_dsi = 0;
			if (!isset($allow_dsi_priv)) $allow_dsi_priv = 0;
			if (!isset($allow_sugg)) $allow_sugg = 0;
			if (!isset($allow_dema)) $allow_dema = 0;
			if (!isset($allow_prol)) $allow_prol = 0;
			if (!isset($allow_avis)) $allow_avis = 0;
			if (!isset($allow_tag)) $allow_tag = 0;
			if (!isset($allow_pwd)) $allow_pwd = 0;
			if (!isset($allow_liste_lecture)) $allow_liste_lecture = 0;
			if (!isset($allow_self_checkout)) $allow_self_checkout = 0;
			if (!isset($allow_self_checkin)) $allow_self_checkin = 0;
			if (!isset($allow_serialcirc)) $allow_serialcirc = 0;
			if (!isset($allow_scan_request)) $allow_scan_request = 0;
			if (!isset($allow_contribution)) $allow_contribution = 0;
			// O.K.,  now if item already exists UPDATE else INSERT
			if($id) {
				$requete = "UPDATE empr_statut SET statut_libelle='".$statut_libelle."', allow_loan='".$allow_loan."', allow_loan_hist='".$allow_loan_hist."', allow_book='".$allow_book."', allow_opac='".$allow_opac."', allow_dsi='".$allow_dsi."', allow_dsi_priv='".$allow_dsi_priv."', allow_sugg='".$allow_sugg."', allow_dema='".$allow_dema."', allow_prol='".$allow_prol."', allow_avis='".$allow_avis."', allow_tag='".$allow_tag."', allow_pwd='".$allow_pwd."', allow_liste_lecture='".$allow_liste_lecture."', allow_self_checkout='".$allow_self_checkout."', allow_self_checkin='".$allow_self_checkin."', allow_serialcirc='".$allow_serialcirc."', allow_scan_request='".$allow_scan_request."', allow_contribution = '".$allow_contribution."' WHERE idstatut=".$id;
				$res = pmb_mysql_query($requete, $dbh);
			}else {
				$requete = "INSERT INTO empr_statut set idstatut=0, statut_libelle='".$statut_libelle."', allow_loan='".$allow_loan."', allow_loan_hist='".$allow_loan_hist."', allow_book='".$allow_book."', allow_opac='".$allow_opac."', allow_dsi='".$allow_dsi."', allow_dsi_priv='".$allow_dsi_priv."', allow_sugg='".$allow_sugg."', allow_dema='".$allow_dema."', allow_prol='".$allow_prol."', allow_avis='".$allow_avis."', allow_tag='".$allow_tag."', allow_pwd='".$allow_pwd."', allow_liste_lecture='".$allow_liste_lecture."', allow_self_checkout='".$allow_self_checkout."', allow_self_checkin='".$allow_self_checkin."', allow_serialcirc='".$allow_serialcirc."', allow_scan_request='".$allow_scan_request."', allow_contribution = '".$allow_contribution."' ";
				$res = pmb_mysql_query($requete, $dbh);
			}
		}
		show_statut($dbh);
		break;
	case 'add':
		if (empty($form_libelle)) statut_form();
			else show_statut($dbh);
		break;
	case 'modif':
		if ($id){
			$requete = "SELECT idstatut, statut_libelle, allow_loan, allow_loan_hist, allow_book, allow_opac, allow_dsi, allow_dsi_priv, allow_sugg, allow_liste_lecture, allow_dema, allow_prol, allow_avis, allow_tag, allow_pwd, allow_self_checkout, allow_self_checkin, allow_serialcirc, allow_scan_request, allow_contribution FROM empr_statut WHERE idstatut=$id ";
			$res = pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($res)) {
				$row=pmb_mysql_fetch_object($res);
				statut_form($row->statut_libelle, $row->allow_loan, $row->allow_loan_hist, $row->allow_book, $row->allow_opac, $row->allow_dsi, $row->allow_dsi_priv, $row->allow_sugg, $row->allow_liste_lecture, $row->allow_dema, $row->allow_prol, $row->allow_avis, $row->allow_tag, $row->allow_pwd, $row->allow_self_checkout, $row->allow_self_checkin, $row->allow_serialcirc, $row->allow_scan_request, $row->allow_contribution, $id);
			} else {
				show_statut($dbh);
			}
		} else {
			show_statut($dbh);
		}
		break;
	case 'del':
		if ($id>2) {
			$total = 0;
			$total = pmb_mysql_result(pmb_mysql_query("select count(1) from empr where empr_statut ='".$id."' ", $dbh), 0, 0);
			if ($total==0) {
				$requete = "DELETE FROM empr_statut WHERE idstatut=$id ";
				$res = pmb_mysql_query($requete, $dbh);
				show_statut($dbh);
				} else {
					error_message(	"", $msg['empr_statut_del_impossible'], 1, 'admin.php?categ=empr&sub=statut&action=');
					}
			} else {
				error_message(	"", $msg['empr_statut_del_1_2_impossible'], 1, 'admin.php?categ=empr&sub=statut&action=');
				}
		break;
	default:
		show_statut($dbh);
		break;
	}
