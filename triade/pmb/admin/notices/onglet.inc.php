<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onglet.inc.php,v 1.5 2018-10-12 11:59:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/list/configuration/notices/list_configuration_notices_onglet_ui.class.php");

// gestion des codes statut exemplaires
?>
<script type="text/javascript">
function test_form(form)
{
	if(form.form_nom.value.length == 0)
	{
		alert("<?php echo $msg[98] ?>");
		return false;
	}
	return true;
}
</script>

<?php
function show_onglet() {
	print list_configuration_notices_onglet_ui::get_instance()->get_display_list();
}

function onglet_form($nom="", $id=0) {

	global $msg;
	global $admin_onglet_form;
	global $charset;

	$admin_onglet_form = str_replace('!!id!!', $id, $admin_onglet_form);

	if(!$id) $admin_onglet_form = str_replace('!!form_title!!', $msg['admin_noti_onglet_ajout'], $admin_onglet_form);
	else $admin_onglet_form = str_replace('!!form_title!!', $msg['admin_noti_onglet_modification'], $admin_onglet_form);

	$admin_onglet_form = str_replace('!!nom!!', htmlentities($nom,ENT_QUOTES, $charset), $admin_onglet_form);
	
	$admin_onglet_form = str_replace('!!nom_suppr!!', addslashes($nom), $admin_onglet_form);
	print confirmation_delete("./admin.php?categ=notices&sub=onglet&action=del&id=");
	print $admin_onglet_form;

	}

switch($action) {
	case 'update':
		if(!empty($form_nom)) {
			if($id) {
				$requete = "UPDATE notice_onglet SET onglet_name='$form_nom' WHERE id_onglet='$id' ";
				$res = pmb_mysql_query($requete, $dbh);
			} else {
				$requete = "SELECT count(1) FROM notice_onglet WHERE onglet_name='$form_nom' LIMIT 1 ";
				$res = pmb_mysql_query($requete, $dbh);
				$nbr = pmb_mysql_result($res, 0, 0);
				if($nbr == 0){
					$requete = "INSERT INTO notice_onglet (onglet_name) VALUES ('$form_nom') ";
					$res = pmb_mysql_query($requete, $dbh);
				}
			}
		}
		show_onglet();
		break;
	case 'add':
		if(empty($form_nom)) onglet_form();
			else show_onglet();
		break;
	case 'modif':
		if($id){
			$requete = "SELECT onglet_name FROM notice_onglet WHERE id_onglet='$id' ";
			$res = pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($res)) {
				$row=pmb_mysql_fetch_object($res);
				onglet_form($row->onglet_name, $id);
			} else {
				show_onglet();
			}
		} else {
			show_onglet();
		}
		break;
	case 'del':
		if ($id) {			

			$req="UPDATE authperso SET authperso_notice_onglet_num=0 where authperso_notice_onglet_num=".$id;
			pmb_mysql_query($req, $dbh);
				
			$requete = "DELETE FROM notice_onglet WHERE id_onglet='$id' ";
			$res = pmb_mysql_query($requete, $dbh);
			
			$requete = "OPTIMIZE TABLE origine_notice ";
			$res = pmb_mysql_query($requete, $dbh);
			show_onglet();
		}
		break;
	default:
		show_onglet();
		break;
	}
