<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: origine_notice.inc.php,v 1.11 2019-06-07 13:03:04 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/list/configuration/notices/list_configuration_notices_orinot_ui.class.php");

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
function show_orinot($dbh) {
	print list_configuration_notices_orinot_ui::get_instance()->get_display_list();
}

function orinot_form($nom="", $pays="FR", $diffusion=1, $id=0) {

	global $msg;
	global $admin_orinot_form;
	global $charset;

	$admin_orinot_form = str_replace('!!id!!', $id, $admin_orinot_form);

	if(!$id) $admin_orinot_form = str_replace('!!form_title!!', $msg['orinot_ajout'], $admin_orinot_form);
		else $admin_orinot_form = str_replace('!!form_title!!', $msg['orinot_modification'], $admin_orinot_form);

	$admin_orinot_form = str_replace('!!nom!!', htmlentities($nom,ENT_QUOTES, $charset), $admin_orinot_form);
	$admin_orinot_form = str_replace('!!nom_suppr!!', addslashes($nom), $admin_orinot_form);
	$admin_orinot_form = str_replace('!!pays!!', htmlentities($pays,ENT_QUOTES, $charset), $admin_orinot_form);

	if($diffusion) $checkbox="checked"; else $checkbox="";
	$admin_orinot_form = str_replace('!!checkbox!!', $checkbox, $admin_orinot_form);
	$admin_orinot_form = str_replace('!!diffusion!!', $diffusion, $admin_orinot_form);


	print confirmation_delete("./admin.php?categ=notices&sub=orinot&action=del&id=");
	print $admin_orinot_form;

}

$id = intval($id);
switch($action) {
    case 'update':
        if(empty($form_nom)) $form_nom = '';
        if(empty($form_pays)) $form_pays = '';
        if(empty($form_diffusion)) $form_diffusion = '';        
		if(!empty($form_nom)) {
			if($id) {
				$requete = "UPDATE origine_notice SET orinot_nom='$form_nom',orinot_pays='$form_pays',orinot_diffusion='$form_diffusion' WHERE orinot_id='$id' ";
				$res = pmb_mysql_query($requete, $dbh);
			} else {
				$requete = "SELECT count(1) FROM origine_notice WHERE orinot_nom='$form_nom' LIMIT 1 ";
				$res = pmb_mysql_query($requete, $dbh);
				$nbr = pmb_mysql_result($res, 0, 0);
				if($nbr == 0){
					$requete = "INSERT INTO origine_notice (orinot_nom,orinot_pays,orinot_diffusion) VALUES ('$form_nom','$form_pays','$form_diffusion') ";
					$res = pmb_mysql_query($requete, $dbh);
				}
			}
		}
		show_orinot($dbh);
		break;
	case 'add':
		if(empty($form_nom) && empty($form_pays)) orinot_form();
			else show_orinot($dbh);
		break;
	case 'modif':
		if($id){
			$requete = "SELECT orinot_nom, orinot_pays, orinot_diffusion FROM origine_notice WHERE orinot_id='$id' ";
			$res = pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($res)) {
				$row=pmb_mysql_fetch_object($res);
				orinot_form($row->orinot_nom, $row->orinot_pays, $row->orinot_diffusion, $id);
				} else {
					show_orinot($dbh);
					}
			} else {
				show_orinot($dbh);
				}
		break;
	case 'del':
		if (($id) && ($id!=1)) {
			$total = 0;
			$total = pmb_mysql_num_rows(pmb_mysql_query("select origine_catalogage from notices where origine_catalogage ='".$id."' ", $dbh));
			if ($total==0) {
				$requete = "DELETE FROM origine_notice WHERE orinot_id='$id' ";
				$res = pmb_mysql_query($requete, $dbh);
				$requete = "OPTIMIZE TABLE origine_notice ";
				$res = pmb_mysql_query($requete, $dbh);
				show_orinot($dbh);
				} else {
					error_message(	"", $msg['orinot_used'], 1, 'admin.php?categ=notices&sub=orinot&action=');
					}
			} else show_orinot($dbh);
		break;
	default:
		show_orinot($dbh);
		break;
	}
