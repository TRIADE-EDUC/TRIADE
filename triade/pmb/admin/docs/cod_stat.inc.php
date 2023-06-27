<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cod_stat.inc.php,v 1.19 2018-10-12 11:59:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/list/configuration/docs/list_configuration_docs_codstat_ui.class.php");

// gestion des codes stat exemplaires
?>
<script type="text/javascript">
function test_form(form)
{
	if(form.form_libelle.value.length == 0)
	{
		alert("<?php echo $msg[98]; ?>");
		return false;
	}
	return true;
}

</script>
<?php
function show_codstat($dbh) {
	print list_configuration_docs_codstat_ui::get_instance()->get_display_list();
}

function codstat_form($libelle="", $statisdoc_codage_import="", $statisdoc_owner=0, $id=0) {
	global $msg;
	global $charset;
	global $admin_codstat_form;

	$admin_codstat_form = str_replace('!!id!!', $id, $admin_codstat_form);
	if(!$id) $admin_codstat_form = str_replace('!!form_title!!', $msg[101], $admin_codstat_form);
		else $admin_codstat_form = str_replace('!!form_title!!', $msg[102], $admin_codstat_form);

	$admin_codstat_form = str_replace('!!libelle!!', htmlentities($libelle,ENT_QUOTES, $charset), $admin_codstat_form);
	$admin_codstat_form = str_replace('!!libelle_suppr!!', addslashes($libelle), $admin_codstat_form);
	$admin_codstat_form = str_replace('!!statisdoc_codage_import!!', $statisdoc_codage_import, $admin_codstat_form);
	$combo_lender= gen_liste ("select idlender, lender_libelle from lenders order by lender_libelle ", "idlender", "lender_libelle", "form_statisdoc_owner", "", $statisdoc_owner, 0, $msg[556],0,$msg["proprio_generique_biblio"]) ;
	$admin_codstat_form = str_replace('!!lender!!', $combo_lender, $admin_codstat_form);

	print confirmation_delete("./admin.php?categ=docs&sub=codstat&action=del&id=");
	print $admin_codstat_form;

	}

switch($action) {
	case 'update':
	
		// vérification validité des données fournies.
		$requete = " SELECT count(1) FROM docs_codestat WHERE (codestat_libelle='$form_libelle' AND idcode!='$id' )  LIMIT 1 ";
		$res = pmb_mysql_query($requete, $dbh);
		$nbr = pmb_mysql_result($res, 0, 0);
		if ($nbr > 0) {
			error_form_message($form_libelle.$msg["docs_label_already_used"]);
		} else {
			// O.K.  if item already exists UPDATE else INSERT
			if($id) {
				$requete = "UPDATE docs_codestat SET codestat_libelle='$form_libelle', statisdoc_codage_import='$form_statisdoc_codage_import', statisdoc_owner='$form_statisdoc_owner' WHERE idcode=$id  ";
				$res = pmb_mysql_query($requete, $dbh);
			} else {
				$requete = "INSERT INTO docs_codestat (idcode,codestat_libelle,statisdoc_codage_import,statisdoc_owner) VALUES ('', '$form_libelle','$form_statisdoc_codage_import','$form_statisdoc_owner') ";
				$res = pmb_mysql_query($requete, $dbh);
			}
		}
		show_codstat($dbh);
		break;
	case 'add':
		if(empty($form_libelle) && empty($form_pret)) {
			codstat_form();
		} else {
			show_codstat($dbh);
		}
		break;
	case 'modif':
		if($id){
			$requete = "SELECT codestat_libelle, statisdoc_codage_import, statisdoc_owner FROM docs_codestat WHERE idcode=$id ";
			$res = pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($res)) {
				$row=pmb_mysql_fetch_object($res);
				codstat_form($row->codestat_libelle,$row->statisdoc_codage_import,$row->statisdoc_owner, $id);
			} else {
				show_codstat($dbh);
			}
		} else {
			show_codstat($dbh);
		}
		break;
	case 'del':
		if($id) {
			$total = pmb_mysql_result(pmb_mysql_query("select count(1) from exemplaires where expl_codestat ='".$id."' ", $dbh), 0, 0);
			if ($total==0) {
				$requete = "DELETE FROM docs_codestat WHERE idcode=$id ";
				$res = pmb_mysql_query($requete, $dbh);
				show_codstat($dbh);
			} else {
				$msg_suppr_err = $admin_liste_jscript;
				$msg_suppr_err .= $msg[1701]." <a href='#' onclick=\"showListItems(this);return(false);\" what='codestat_docs' item='".$id."' total='".$total."' alt=\"".$msg["admin_docs_list"]."\" title=\"".$msg["admin_docs_list"]."\"><img src='".get_url_icon('req_get.gif')."'></a>" ;
				error_message(	$msg[294], $msg_suppr_err, 1, 'admin.php?categ=docs&sub=codstat&action=');
			}
		} else show_codstat($dbh);
		break;
	default:
		show_codstat($dbh);
		break;
}
