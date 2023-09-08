<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_echelle.inc.php,v 1.3 2019-06-05 13:13:19 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

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
function show_map_echelle() {
	global $msg,$dbh;
	global $charset ;

	print "<table>
	<tr>
		<th>".$msg['admin_nomap_echelle_name']."</th>
	</tr>";

	// affichage du tableau 
	$requete = "SELECT map_echelle_id, map_echelle_name FROM map_echelles ORDER BY map_echelle_name ";
	$res = pmb_mysql_query($requete, $dbh);
	$nbr = pmb_mysql_num_rows($res);

	$parity=1;
	for($i=0;$i<$nbr;$i++) {
		$row=pmb_mysql_fetch_object($res);
		if ($parity % 2) $pair_impair = "even";else $pair_impair = "odd";
		$parity += 1;
		$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./admin.php?categ=notices&sub=map_echelle&action=modif&id=$row->map_echelle_id';\" ";
        	
		print "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'><td>".htmlentities($row->map_echelle_name,ENT_QUOTES, $charset)."</td>";
		print "</tr>";
	}
	print "</table>
		<input class='bouton' type='button' value=' $msg[admin_noti_map_echelle_ajout] ' onClick=\"document.location='./admin.php?categ=notices&sub=map_echelle&action=add'\" />";
}

function map_echelle_form($nom="", $id=0) {
	global $msg;
	global $admin_map_echelle_form;
	global $charset;

	$admin_map_echelle_form = str_replace('!!id!!', $id, $admin_map_echelle_form);

	if(!$id) $admin_map_echelle_form = str_replace('!!form_title!!', $msg['admin_noti_map_echelle_ajout'], $admin_map_echelle_form);
	else $admin_map_echelle_form = str_replace('!!form_title!!', $msg['admin_noti_map_echelle_modification'], $admin_map_echelle_form);

	$admin_map_echelle_form = str_replace('!!nom!!', htmlentities($nom,ENT_QUOTES, $charset), $admin_map_echelle_form);
	
	$admin_map_echelle_form = str_replace('!!nom_suppr!!', addslashes($nom), $admin_map_echelle_form);
	print confirmation_delete("./admin.php?categ=notices&sub=map_echelle&action=del&id=");
	print $admin_map_echelle_form;

}

switch($action) {
	case 'update':
		if(!empty($form_nom)) {
			if($id) {
				$requete = "UPDATE map_echelles SET map_echelle_name='$form_nom' WHERE map_echelle_id='$id' ";
				$res = pmb_mysql_query($requete, $dbh);
			} else {
				$requete = "SELECT count(1) FROM map_echelles WHERE map_echelle_name='$form_nom' LIMIT 1 ";
				$res = pmb_mysql_query($requete, $dbh);
				$nbr = pmb_mysql_result($res, 0, 0);
				if($nbr == 0){
					$requete = "INSERT INTO map_echelles (map_echelle_name) VALUES ('$form_nom') ";
					$res = pmb_mysql_query($requete, $dbh);
				}
			}
		}
		show_map_echelle();
		break;
	case 'add':
		if(empty($form_nom)) map_echelle_form();
			else show_map_echelle();
		break;
	case 'modif':
		if($id){
			$requete = "SELECT map_echelle_name FROM map_echelles WHERE map_echelle_id='$id' ";
			$res = pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($res)) {
				$row=pmb_mysql_fetch_object($res);
				map_echelle_form($row->map_echelle_name, $id);
			} else {
				show_map_echelle();
			}
		} else {
			show_map_echelle();
		}
		break;
	case 'del':
		if ($id) {			
			$requete = "DELETE FROM map_echelles WHERE map_echelle_id='$id' ";
			$res = pmb_mysql_query($requete, $dbh);
			$requete = "OPTIMIZE TABLE map_echelles ";
			$res = pmb_mysql_query($requete, $dbh);
			show_map_echelle();
		}
		break;
	default:
		show_map_echelle();
		break;
	}
