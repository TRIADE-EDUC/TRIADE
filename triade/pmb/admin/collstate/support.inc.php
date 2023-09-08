<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: support.inc.php,v 1.3 2018-10-12 11:59:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/list/configuration/collstate/list_configuration_collstate_support_ui.class.php");

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
function show_support($dbh) {
	print list_configuration_collstate_support_ui::get_instance()->get_display_list();
}

function support_form($libelle="", $id=0) {
	global $msg;
	global $admin_support_form;
	global $charset;
	
	$admin_support_form = str_replace('!!id!!', $id, $admin_support_form);

	if(!$id) {
		$admin_support_form = str_replace('!!form_title!!', $msg["admin_collstate_add_support"], $admin_support_form);
		$admin_support_form = str_replace('!!supprimer!!', "", $admin_support_form);	
	} else {
		$admin_support_form = str_replace('!!form_title!!', $msg["admin_collstate_edit_support"], $admin_support_form);
		print confirmation_delete("./admin.php?categ=collstate&sub=support&action=del&id=");
		$admin_support_form = str_replace('!!supprimer!!', "<input class='bouton' type='button' value=' ".$msg["supprimer"]." ' onClick=\"javascript:confirmation_delete($id,'".addslashes($libelle)."')\" />", $admin_support_form);		
	}
	$admin_support_form = str_replace('!!libelle!!', htmlentities($libelle,ENT_QUOTES, $charset), $admin_support_form);
	print $admin_support_form;
}

switch($action) {
	case 'update':
		// vérification validité des données fournies.
		$requete = " SELECT count(1) FROM arch_type WHERE (archtype_libelle='$form_libelle' AND archtype_id!='$id' )  LIMIT 1 ";
		$res = pmb_mysql_query($requete, $dbh);
		$nbr = pmb_mysql_result($res, 0, 0);
		if ($nbr > 0) {
			error_form_message($form_libelle.$msg["support_label_already_used"]);
		} else {
			// O.K.,  now if item already exists UPDATE else INSERT
			if($id != 0) {
				$requete = "UPDATE arch_type SET archtype_libelle='$form_libelle' WHERE archtype_id=$id ";
				$res = pmb_mysql_query($requete, $dbh);
			} else {
				$requete = "INSERT INTO arch_type (archtype_id,archtype_libelle) VALUES (0, '$form_libelle') ";
				$res = pmb_mysql_query($requete, $dbh);
			}
		}
		show_support($dbh);
		break;
	case 'add':
		if(empty($form_libelle) && empty($form_pret)) {
			support_form();
		} else {
			show_support($dbh);
		}
		break;
	case 'modif':
		if($id!=""){
			$requete = "SELECT archtype_libelle FROM arch_type WHERE archtype_id=$id ";
			$res = pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($res)) {
				$row=pmb_mysql_fetch_object($res);
				support_form($row->archtype_libelle, $id);
			} else {
				show_support($dbh);
			}
		} else {
			show_support($dbh);
		}
		break;
	case 'del':
		if($id!="") {			
			$total = pmb_mysql_num_rows(pmb_mysql_query("select 1 from collections_state where collstate_type='".$id."' limit 0,1", $dbh));
			if ($total==0) {
				$requete = "DELETE FROM arch_type WHERE archtype_id=$id ";
				$res = pmb_mysql_query($requete, $dbh);
				show_support($dbh);
			} else {
				error_message(	$msg[294], $msg["collstate_support_used"], 1, 'admin.php?categ=support&sub=support&action=');
			}
		}
		break;
	default:
		show_support($dbh);
		break;
	}
