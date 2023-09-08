<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cod_stat.inc.php,v 1.13 2018-10-12 11:59:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/list/configuration/empr/list_configuration_empr_codstat_ui.class.php");

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
	print list_configuration_empr_codstat_ui::get_instance()->get_display_list();
}

function codstat_form($libelle="", $id=0)
{
	global $msg;
	global $admin_statlec_form ;
	global $charset;
	
	$admin_statlec_form = str_replace('!!id!!', $id, $admin_statlec_form);
	if(!$id) $admin_statlec_form = str_replace('!!form_title!!', $msg[101], $admin_statlec_form);
		else $admin_statlec_form = str_replace('!!form_title!!', $msg[102], $admin_statlec_form);

	$admin_statlec_form = str_replace('!!libelle!!', htmlentities($libelle,ENT_QUOTES, $charset), $admin_statlec_form);
	$admin_statlec_form = str_replace('!!libelle_suppr!!', addslashes($libelle), $admin_statlec_form);
	
	print confirmation_delete("./admin.php?categ=empr&sub=codstat&action=del&id=");
	print $admin_statlec_form;
	}

switch($action) {
	case 'update':
		// no duplication
		$requete = " SELECT count(1) FROM empr_codestat WHERE (libelle='$form_libelle' AND idcode!='$id' )  LIMIT 1 ";
		$res = pmb_mysql_query($requete, $dbh);
		$nbr = pmb_mysql_result($res, 0, 0);
		if ($nbr > 0) {
				error_form_message($form_libelle.$msg["docs_label_already_used"]);
		} else {
			// O.k., now if the id already exist UPDATE else INSERT
			if(!empty($form_libelle)) {
				if($id) {
					$requete = "UPDATE empr_codestat SET libelle='$form_libelle' WHERE idcode=$id ";
					$res = pmb_mysql_query($requete, $dbh);
				} else {
					$requete = "SELECT count(1) FROM empr_codestat WHERE libelle='$form_libelle' LIMIT 1 ";
					$res = pmb_mysql_query($requete, $dbh);
					$nbr = pmb_mysql_result($res, 0, 0);
					if($nbr == 0) {
						$requete = "INSERT INTO empr_codestat (idcode,libelle) VALUES ('', '$form_libelle') ";
						$res = pmb_mysql_query($requete, $dbh);
					}
				}
			}
		}
		show_codstat($dbh);
		break;
	case 'add':
		if(empty($form_libelle)) {
			codstat_form();
		} else {
			show_codstat($dbh);
		}
		break;
	case 'modif':
		if($id){
			$requete = "SELECT libelle FROM empr_codestat WHERE idcode=$id LIMIT 1;";
			$res = pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($res)) {
				$row=pmb_mysql_fetch_row($res);
				codstat_form($row[0], $id);
			} else {
				show_codstat($dbh);
			}
		} else {
			show_codstat($dbh);
		}
		break;
	case 'del':
		if($id) {
			$total = 0;
			$total = pmb_mysql_result(pmb_mysql_query("select count(1) from empr where empr_codestat ='".$id."' ", $dbh), 0, 0);
			if ($total==0) {
				$requete = "DELETE FROM empr_codestat WHERE idcode=$id ;";
				$res = pmb_mysql_query($requete, $dbh);
				$requete = "OPTIMIZE TABLE empr_codestat ";
				$res = pmb_mysql_query($requete, $dbh);
				show_codstat($dbh);
				} else {
					error_message(	$msg[294], $msg[1707], 1, 'admin.php?categ=empr&sub=codstat&action=');
					}
			} else show_codstat($dbh);
		break;
	default:
		show_codstat($dbh);
		break;
}
