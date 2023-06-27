<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: statut.inc.php,v 1.27 2019-06-07 12:57:50 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/list/configuration/docs/list_configuration_docs_statut_ui.class.php");

// gestion des codes statut exemplaires
?>
<script type="text/javascript">
function test_form(form){
	if(form.form_libelle.value.length == 0){
		alert("<?php echo $msg[98] ?>");
		return false;
	}
	return true;
}
function test_check(form){
	if(form.form_pret.value < 1)
		form.form_pret.value = '1';
	else
		form.form_pret.value = '0';
	return true;
}
function test_check_trans(form){
	if(form.form_trans.value < 1)
		form.form_trans.value = '1';
	else
		form.form_trans.value = '0';
	return true;
}
function test_check_visible_opac(form){
	if(form.form_visible_opac.value < 1)
		form.form_visible_opac.value = '1';
	else
		form.form_visible_opac.value = '0';
	return true;
}

</script>

<?php
function show_statut($dbh) {
	print list_configuration_docs_statut_ui::get_instance()->get_display_list();
}

function statut_form($libelle="", $pret=1, $trans=1, $statusdoc_codage_import="", $statusdoc_owner=0, $id=0, $libelle_opac="", $visible_opac=1,$allow_resa=1) {

	global $msg;
	global $admin_statut_form;
	global $charset;

	$admin_statut_form = str_replace('!!id!!', $id, $admin_statut_form);

	if(!$id) $admin_statut_form = str_replace('!!form_title!!', $msg[115], $admin_statut_form);
	else $admin_statut_form = str_replace('!!form_title!!', $msg[118], $admin_statut_form);

	$admin_statut_form = str_replace('!!libelle!!', htmlentities($libelle,ENT_QUOTES, $charset), $admin_statut_form);
	$admin_statut_form = str_replace('!!libelle_suppr!!', addslashes($libelle), $admin_statut_form);

	$admin_statut_form = str_replace('!!libelle_opac!!', htmlentities($libelle_opac,ENT_QUOTES, $charset), $admin_statut_form);
	
	if($pret) $checkbox="checked"; else $checkbox="";
	$admin_statut_form = str_replace('!!checkbox!!', $checkbox, $admin_statut_form);
	$admin_statut_form = str_replace('!!pret!!', $pret, $admin_statut_form);
	
	if($allow_resa) $checkbox="checked"; else $checkbox="";
	$admin_statut_form = str_replace('!!checkbox_allow_resa!!', $checkbox, $admin_statut_form);
	
	if($trans) $checkbox="checked"; else $checkbox="";
	$admin_statut_form = str_replace('!!checkbox_trans!!', $checkbox, $admin_statut_form);
	$admin_statut_form = str_replace('!!trans!!', $trans, $admin_statut_form);
	
	if($visible_opac) $checkbox="checked"; else $checkbox="";
	$admin_statut_form = str_replace('!!checkbox_visible_opac!!', $checkbox, $admin_statut_form);
	$admin_statut_form = str_replace('!!visible_opac!!', $visible_opac, $admin_statut_form);
	
	$admin_statut_form = str_replace('!!statusdoc_codage_import!!', $statusdoc_codage_import, $admin_statut_form);
	$combo_lender= gen_liste ("select idlender, lender_libelle from lenders order by lender_libelle ", "idlender", "lender_libelle", "form_statusdoc_owner", "", $statusdoc_owner, 0, $msg[556],0,$msg["proprio_generique_biblio"]) ;
	$admin_statut_form = str_replace('!!lender!!', $combo_lender, $admin_statut_form);

	print confirmation_delete("./admin.php?categ=docs&sub=statut&action=del&id=");
	print $admin_statut_form;

}
$id = intval($id);
switch($action) {
	case 'update':
	    if(empty($form_libelle)) $form_libelle = '';
	    if(empty($form_pret)) $form_pret = '';
	    if(empty($form_allow_resa)) $form_allow_resa = '';
	    if(empty($form_trans)) $form_trans = '';
	    if(empty($form_statusdoc_owner)) $form_statusdoc_owner = '';
	    if(empty($form_libelle_opac)) $form_libelle_opac = '';
	    if(empty($form_visible_opac)) $form_visible_opac = '';
		// vérification validité des données fournies.
		$requete = " SELECT count(1) FROM docs_statut WHERE (statut_libelle='$form_libelle' AND idstatut!='$id' )  LIMIT 1 ";
		$res = pmb_mysql_query($requete, $dbh);
		$nbr = pmb_mysql_result($res, 0, 0);
		if ($nbr > 0) {
			error_form_message($form_libelle.$msg["docs_label_already_used"]);
		} else {
			// O.K.,  now if item already exists UPDATE else INSERT
			if($id) {
				$requete = "UPDATE docs_statut SET statut_libelle='$form_libelle',pret_flag='$form_pret',statut_allow_resa='$form_allow_resa', transfert_flag='$form_trans',statusdoc_codage_import='$form_statusdoc_codage_import', statusdoc_owner='$form_statusdoc_owner', statut_libelle_opac='$form_libelle_opac', statut_visible_opac='$form_visible_opac' WHERE idstatut=$id ";
				$res = pmb_mysql_query($requete, $dbh);
			} else {
				$requete = "INSERT INTO docs_statut SET statut_libelle='$form_libelle',pret_flag='$form_pret',statut_allow_resa='$form_allow_resa', transfert_flag='$form_trans',statusdoc_codage_import='$form_statusdoc_codage_import', statusdoc_owner='$form_statusdoc_owner', statut_libelle_opac='$form_libelle_opac', statut_visible_opac='$form_visible_opac' ";
				$res = pmb_mysql_query($requete, $dbh);
			}
		}
		show_statut($dbh);
		break;
	case 'add':
		if(empty($form_libelle) && empty($form_pret)) statut_form();
		else show_statut($dbh);
		break;
	case 'modif':
		if($id){
			$requete = "SELECT * FROM docs_statut WHERE idstatut=$id LIMIT 1 ";
			$res = pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($res)) {
				$row=pmb_mysql_fetch_object($res);
				statut_form($row->statut_libelle, $row->pret_flag, $row->transfert_flag, $row->statusdoc_codage_import,$row->statusdoc_owner, $id, $row->statut_libelle_opac, $row->statut_visible_opac, $row->statut_allow_resa);
			} else {
				show_statut($dbh);
			}
		} else {
			show_statut($dbh);
		}
		break;
	case 'del':
		if($id) {
			$total_serialcirc = 0;
			$total_serialcirc = pmb_mysql_result(pmb_mysql_query("select count(1) from serialcirc where serialcirc_expl_statut_circ='".$id."' or serialcirc_expl_statut_circ_after='".$id."'", $dbh), 0, 0);
			if ($total_serialcirc > 0) {
				error_message(	$msg[294], $msg["admin_docs_statut_serialcirc_delete_forbidden"], 1, 'admin.php?categ=docs&sub=statut&action=');
			} else {
				$total = 0;
				$total = pmb_mysql_result(pmb_mysql_query("select count(1) from exemplaires where expl_statut ='".$id."' ", $dbh), 0, 0);
				if ($total > 0) {
					$msg_suppr_err = $admin_liste_jscript;
					$msg_suppr_err .= $msg[1703]." <a href='#' onclick=\"showListItems(this);return(false);\" what='statut_docs' item='".$id."' total='".$total."' alt=\"".$msg["admin_docs_list"]."\" title=\"".$msg["admin_docs_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
					error_message(	$msg[294], $msg_suppr_err, 1, 'admin.php?categ=docs&sub=statut&action=');
				} else {
					if ($finance_statut_perdu == '') $statut_perdu = 0;
					else $statut_perdu = $finance_statut_perdu; 
					if ($statut_perdu == $id) {
						error_message(	$msg[294], $msg["admin_docs_statut_gestion_financiere_delete_forbidden"], 1, 'admin.php?categ=docs&sub=statut&action=');
					} else {
						$requete = "DELETE FROM docs_statut WHERE idstatut=$id ";
						$res = pmb_mysql_query($requete, $dbh);
						show_statut($dbh);
					}
				}
			}
		} else show_statut($dbh);
		break;
	default:
		show_statut($dbh);
		break;
	}
