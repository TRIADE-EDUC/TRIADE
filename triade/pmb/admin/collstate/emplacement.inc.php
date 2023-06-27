<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: emplacement.inc.php,v 1.3 2018-10-12 11:59:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/list/configuration/collstate/list_configuration_collstate_emplacement_ui.class.php");

// gestion des prêteurs de documents
?>
<script type="text/javascript">
function test_form(form)
{
	if(form.form_libelle.value.length == 0)
	{
		alert("<?php echo $msg[559]; ?>");
		return false;
	}
	return true;
}

</script>
<?php
function show_emplacement($dbh) {
	print list_configuration_collstate_emplacement_ui::get_instance()->get_display_list();
}

function emplacement_form($libelle="", $id=0) {
	global $msg;
	global $admin_emplacement_form;
	global $charset;
	
	$admin_emplacement_form = str_replace('!!id!!', $id, $admin_emplacement_form);

	if(!$id) {
		$admin_emplacement_form = str_replace('!!form_title!!', $msg["admin_collstate_add_emplacement"], $admin_emplacement_form);
		$admin_emplacement_form = str_replace('!!supprimer!!', "", $admin_emplacement_form);
	}else {
		$admin_emplacement_form = str_replace('!!form_title!!', $msg["admin_collstate_edit_emplacement"], $admin_emplacement_form);	
		print confirmation_delete("./admin.php?categ=collstate&sub=emplacement&action=del&id=");
		$admin_emplacement_form = str_replace('!!supprimer!!', "<input class='bouton' type='button' value=' ".$msg["supprimer"]." ' onClick=\"javascript:confirmation_delete($id,'".addslashes($libelle)."')\" />", $admin_emplacement_form);
	}

	$admin_emplacement_form = str_replace('!!libelle!!', htmlentities($libelle,ENT_QUOTES, $charset), $admin_emplacement_form);
	print $admin_emplacement_form;

}

switch($action) {
	case 'update':
		// vérification validité des données fournies.
		$requete = " SELECT count(1) FROM arch_emplacement WHERE (archempla_libelle='$form_libelle' AND archempla_id!='$id' )  LIMIT 1 ";
		$res = pmb_mysql_query($requete, $dbh);
		$nbr = pmb_mysql_result($res, 0, 0);
		if ($nbr > 0) {
			error_form_message($form_libelle.$msg["emplacement_label_already_used"]);
		} else {
			// O.K.,  now if item already exists UPDATE else INSERT
			if($id != 0) {
				$requete = "UPDATE arch_emplacement SET archempla_libelle='$form_libelle' WHERE archempla_id=$id ";
				$res = pmb_mysql_query($requete, $dbh);
			} else {
				$requete = "INSERT INTO arch_emplacement (archempla_id,archempla_libelle) VALUES (0, '$form_libelle') ";
				$res = pmb_mysql_query($requete, $dbh);
			}
		}
		show_emplacement($dbh);
		break;
	case 'add':
		if(empty($form_libelle) && empty($form_pret)) {
			emplacement_form();
		} else {
			show_emplacement($dbh);
		}
		break;
	case 'modif':
		if($id!=""){
			$requete = "SELECT archempla_libelle FROM arch_emplacement WHERE archempla_id=$id ";
			$res = pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($res)) {
				$row=pmb_mysql_fetch_object($res);
				emplacement_form($row->archempla_libelle, $id);
			} else {
					show_emplacement($dbh);
			}
		} else {
			show_emplacement($dbh);
		}
		break;
	case 'del':
		if($id!="") {
			$total = 0;
			$total = pmb_mysql_num_rows(pmb_mysql_query("select 1 from collections_state where collstate_emplacement='".$id."' limit 0,1", $dbh));
			if ($total==0) {
				$requete = "DELETE FROM arch_emplacement WHERE archempla_id=$id ";
				$res = pmb_mysql_query($requete, $dbh);
				show_emplacement($dbh);
			} else {
				error_message(	$msg[294], $msg["collstate_emplacement_used"], 1, 'admin.php?categ=collstate&sub=emplacement&action=');
			}
		}
		break;
	default:
		show_emplacement($dbh);
		break;
	}
