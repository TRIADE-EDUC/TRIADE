<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_usage.inc.php,v 1.2 2019-01-24 16:46:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/translation.class.php");

// gestion des droits d'usage
?>
<script type="text/javascript">
function test_form(form)
{
	if(form.usage_libelle.value.length == 0)
	{
		alert("<?php echo $msg['98'] ?>");
		return false;
	}
	return true;
}
</script>

<?php
function show_notice_usage() {
	global $msg;
	global $charset ;

	print "<table>
	<tr>
		<th>".$msg['notice_usage_libelle']."</th>
	</tr>";

	$requete = "SELECT * FROM notice_usage ORDER BY usage_libelle ";
	$res = pmb_mysql_query($requete);
	$nbr = pmb_mysql_num_rows($res);

	$parity=1;
	for($i=0;$i<$nbr;$i++) {
		$row=pmb_mysql_fetch_object($res);
		if ($parity % 2) {
			$pair_impair = "even";
		} else {
			$pair_impair = "odd";
		}
		$parity += 1;
		$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./admin.php?categ=notices&sub=notice_usage&action=modif&id_usage=$row->id_usage';\" ";
        	
		print pmb_bidi("<tr class='$pair_impair' $tr_javascript style='cursor: pointer'><td>".htmlentities($row->usage_libelle,ENT_QUOTES, $charset)."</td></tr>");
	}
	print "</table>
		<input class='bouton' type='button' value=' ".htmlentities($msg['notice_usage_ajout'],ENT_QUOTES, $charset)." ' onClick=\"document.location='./admin.php?categ=notices&sub=notice_usage&action=add'\" />";
}

function notice_usage_form($id_usage=0, $usage_libelle='') {

	global $msg;
	global $admin_notice_usage_form;
	global $charset;

	if(!$id_usage){
		$admin_notice_usage_form = str_replace('!!form_title!!', $msg['notice_usage_ajout'], $admin_notice_usage_form);
		$admin_notice_usage_form = str_replace("!!bouton_supprimer!!","",$admin_notice_usage_form) ;
	} else {
		$admin_notice_usage_form = str_replace('!!form_title!!', $msg['notice_usage_modification'], $admin_notice_usage_form);
		$admin_notice_usage_form = str_replace("!!bouton_supprimer!!","<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id_usage!!,'!!usage_libelle_suppr!!')\" />",$admin_notice_usage_form) ;
	}

	$admin_notice_usage_form = str_replace('!!id_usage!!', $id_usage, $admin_notice_usage_form);
	$admin_notice_usage_form = str_replace('!!usage_libelle!!', htmlentities($usage_libelle,ENT_QUOTES, $charset), $admin_notice_usage_form);
	$admin_notice_usage_form = str_replace('!!usage_libelle_suppr!!', addslashes($usage_libelle), $admin_notice_usage_form);

	print confirmation_delete("./admin.php?categ=notices&sub=notice_usage&action=del&id_usage=");
	print $admin_notice_usage_form;
	$translation = new translation($id_usage, 'notice_usage');
	print $translation->connect('notice_usageform');

}

function get_translated_usage_libelle($id_usage=0, $usage_libelle='') {
	return translation::get_translated_text($id_usage, 'notice_usage', 'usage_libelle',  $usage_libelle);
}

switch($action) {
	case 'update':
		if(!empty($usage_libelle)) {
			if($id_usage) {
				$requete = "UPDATE notice_usage SET usage_libelle='".$usage_libelle."' WHERE id_usage='".$id_usage."' ";
				$res = pmb_mysql_query($requete);
			} else {
				$requete = "SELECT count(1) FROM notice_usage WHERE usage_libelle='".$usage_libelle."' LIMIT 1 ";
				$res = pmb_mysql_query($requete);
				$nbr = pmb_mysql_result($res, 0, 0);
				if($nbr == 0){
					$requete = "INSERT INTO notice_usage (usage_libelle) VALUES ('".$usage_libelle."') ";
					$res = pmb_mysql_query($requete);
					$id_usage = pmb_mysql_insert_id();
				}
			}
			if($id_usage) {
				$translation = new translation($id_usage, "notice_usage");
				$translation->update("usage_libelle");
			}
		}
		show_notice_usage();
		break;
	case 'add':
		notice_usage_form();
		break;
	case 'modif':
		if($id_usage){
			$requete = "SELECT * FROM notice_usage WHERE id_usage='".$id_usage."' ";
			$res = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res)) {
				$row = pmb_mysql_fetch_object($res);
				notice_usage_form($row->id_usage,$row->usage_libelle);
			} else {
				show_notice_usage();
			}
		} else {
			show_notice_usage();
		}
		break;
	case 'del':
		if ($id_usage) {
			$total = 0;
			$total = pmb_mysql_num_rows(pmb_mysql_query("select num_notice_usage from notices where num_notice_usage ='".$id_usage."' "));
			if ($total==0) {
				$requete = "DELETE FROM notice_usage WHERE id_usage='".$id_usage."' ";
				$res = pmb_mysql_query($requete);
				$requete = "OPTIMIZE TABLE notice_usage ";
				$res = pmb_mysql_query($requete);
				translation::delete($id_usage, "notice_usage");
				show_notice_usage();
			} else {
				error_message(	"", $msg['notice_usage_used'], 1, 'admin.php?categ=notices&sub=notice_usage&action=');
			}
		} else {
			show_notice_usage();
		}
		break;
	default:
		show_notice_usage();
		break;
}