<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: statut.inc.php,v 1.7 2019-06-07 13:36:26 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// gestion des codes statut documents numériques
?>
<script type="text/javascript">
function test_form(form) {
	if(form.form_gestion_libelle.value.length == 0) {
		alert("<?php echo $msg[98] ?>");
		return false;
	}
	return true;
}
</script>

<?php
function show_statut($dbh) {
	global $msg;

	print "<table>
	<tr>
		<th colspan=1>".$msg["docnum_statut_gestion"]."</th>
		<th colspan=4>".$msg["docnum_statut_opac"]."</th>
	</tr><tr>
		<th>".$msg["docnum_statut_libelle"]."</th>
		<th>".$msg["docnum_statut_libelle"]."</th>
		<th>".$msg["docnum_statut_visu_opac"]."</th>
		<th>".$msg["docnum_statut_cons_opac"]."</th>
		<th>".$msg["docnum_statut_down_opac"]."</th>
	</tr>";

	// affichage du tableau des statuts
	$requete = "SELECT id_explnum_statut, gestion_libelle, opac_libelle, ";
	$requete .= "explnum_visible_opac, explnum_visible_opac_abon,";
	$requete .= "explnum_consult_opac, explnum_consult_opac_abon,";
	$requete .= "explnum_download_opac, explnum_download_opac_abon, ";
	$requete .= "class_html FROM explnum_statut ORDER BY gestion_libelle ";
	$res = pmb_mysql_query($requete, $dbh);
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
		$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./admin.php?categ=docnum&sub=statut&action=modif&id=$row->id_explnum_statut';\" ";
		print pmb_bidi("<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>");
		print pmb_bidi("<td><span class='$row->class_html'  style='margin-right: 3px;'><img src='".get_url_icon('spacer.gif')."' width='10' height='10' /></span>") ;
		if ($row->id_explnum_statut<2) print pmb_bidi("<strong>$row->gestion_libelle</strong></td>"); 
		else print pmb_bidi("$row->gestion_libelle</td>");
		print "<td>$row->opac_libelle</td>"; 
		if($row->explnum_visible_opac) print "<td>X</td>";
			else print "<td>&nbsp;</td>";
		if($row->explnum_consult_opac) print "<td>X</td>";
			else print "<td>&nbsp;</td>";
		if($row->explnum_download_opac) print "<td>X</td>";
			else print "<td>&nbsp;</td>";
		print "</tr>";
	}
	print "</table>
		<input class='bouton' type='button' value=' $msg[115] ' onClick=\"document.location='./admin.php?categ=docnum&sub=statut&action=add'\" />";
}

function statut_form($id=0, $gestion_libelle="", $opac_libelle="", $visible_opac=1, $consult_opac=1, $download_opac=1, $class_html='', $visible_opac_abon=0, $consult_opac_abon=0, $download_opac_abon=0, $thumbnail_visible_opac_override=0) {

	global $msg;
	global $admin_docnum_statut_form;
	global $charset;

	if (!$id) {
		$admin_docnum_statut_form = str_replace('!!form_title!!', $msg[115], $admin_docnum_statut_form);
		$admin_docnum_statut_form = str_replace("!!bouton_supprimer!!","",$admin_docnum_statut_form) ;
	} else {
		$admin_docnum_statut_form = str_replace("!!bouton_supprimer!!","<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id!!,'!!libelle_suppr!!')\" />",$admin_docnum_statut_form) ;
		$admin_docnum_statut_form = str_replace('!!form_title!!', $msg[118], $admin_docnum_statut_form);
	}
	$admin_docnum_statut_form = str_replace('!!id!!', $id, $admin_docnum_statut_form);

	$admin_docnum_statut_form = str_replace('!!gestion_libelle!!', htmlentities($gestion_libelle,ENT_QUOTES, $charset), $admin_docnum_statut_form);
	$admin_docnum_statut_form = str_replace('!!libelle_suppr!!', addslashes($gestion_libelle), $admin_docnum_statut_form);
	
	$admin_docnum_statut_form = str_replace('!!opac_libelle!!', htmlentities($opac_libelle,ENT_QUOTES, $charset), $admin_docnum_statut_form);
	if ($visible_opac) $checkbox="checked"; else $checkbox="";
	$admin_docnum_statut_form = str_replace('!!checkbox_visible_opac!!', $checkbox, $admin_docnum_statut_form);
	
	if ($consult_opac) $checkbox="checked"; else $checkbox="";
	$admin_docnum_statut_form = str_replace('!!checkbox_consult_opac!!', $checkbox, $admin_docnum_statut_form);
	
	if ($download_opac) $checkbox="checked"; else $checkbox="";
	$admin_docnum_statut_form = str_replace('!!checkbox_download_opac!!', $checkbox, $admin_docnum_statut_form);
		
	if ($visible_opac_abon) $checkbox="checked"; else $checkbox="";
	$admin_docnum_statut_form = str_replace('!!checkbox_visible_opac_abon!!', $checkbox, $admin_docnum_statut_form);
	
	if ($consult_opac_abon) $checkbox="checked"; else $checkbox="";
	$admin_docnum_statut_form = str_replace('!!checkbox_consult_opac_abon!!', $checkbox, $admin_docnum_statut_form);
	
	if ($download_opac_abon) $checkbox="checked"; else $checkbox="";
	$admin_docnum_statut_form = str_replace('!!checkbox_download_opac_abon!!', $checkbox, $admin_docnum_statut_form);
	
	if ($thumbnail_visible_opac_override) $checkbox="checked"; else $checkbox="";
	$admin_docnum_statut_form = str_replace('!!checkbox_thumbnail_visible_opac_override!!', $checkbox, $admin_docnum_statut_form);
	
	for ($i=1;$i<=20; $i++) {
		if ($class_html=="statutnot".$i) $checked = "checked";
			else $checked = "";
		$couleur[$i]="<span for='statutnot".$i."' class='statutnot".$i."' style='margin: 7px;'><img src='".get_url_icon('spacer.gif')."' width='10' height='10' />
					<input id='statutnot".$i."' type=radio name='form_class_html' value='statutnot".$i."' $checked class='checkbox' /></span>";
		if ($i==10) $couleur[10].="<br />";
		elseif ($i!=20) $couleur[$i].="<b>|</b>";
		}
	
	$couleurs=implode("",$couleur);
	$admin_docnum_statut_form = str_replace('!!class_html!!', $couleurs, $admin_docnum_statut_form);

	print confirmation_delete("./admin.php?categ=docnum&sub=statut&action=del&id=");
	print $admin_docnum_statut_form;

}

$id = intval($id);
switch($action) {
    case 'update':
        if(empty($form_visible_opac)) $form_visible_opac = 0;
        if(empty($form_gestion_libelle)) $form_gestion_libelle = 0;
        if(empty($form_opac_libelle)) $form_opac_libelle = 0;
        if(empty($form_class_html)) $form_class_html = 0;
        if(empty($form_consult_opac)) $form_consult_opac = 0;
        if(empty($form_download_opac)) $form_download_opac = 0;
        if(empty($form_visible_opac_abon)) $form_visible_opac_abon = 0;
        if(empty($form_consult_opac_abon)) $form_consult_opac_abon = 0;
        if(empty($form_download_opac_abon)) $form_download_opac_abon = 0;
        if(empty($form_thumbnail_visible_opac_override)) $form_thumbnail_visible_opac_override = 0;
		if ($id) {
			$requete = 'UPDATE explnum_statut SET 
						gestion_libelle="'.$form_gestion_libelle.'", 
						opac_libelle="'.$form_opac_libelle.'", 
						class_html="'.$form_class_html.'",
						explnum_visible_opac="'.$form_visible_opac.'", 
						explnum_consult_opac="'.$form_consult_opac.'", 
						explnum_download_opac="'.$form_download_opac.'",
						explnum_visible_opac_abon="'.$form_visible_opac_abon.'", 
						explnum_consult_opac_abon="'.$form_consult_opac_abon.'", 
						explnum_download_opac_abon="'.$form_download_opac_abon.'",
						explnum_thumbnail_visible_opac_override="'.$form_thumbnail_visible_opac_override.'"
			 			WHERE id_explnum_statut="'.$id.'" ';
			$res = pmb_mysql_query($requete, $dbh);
		} else {
			$requete = 'INSERT INTO explnum_statut SET
						gestion_libelle="'.$form_gestion_libelle.'", 
						opac_libelle="'.$form_opac_libelle.'",
						class_html="'.$form_class_html.'",
						explnum_visible_opac="'.$form_visible_opac.'", 
						explnum_consult_opac="'.$form_consult_opac.'", 
						explnum_download_opac="'.$form_download_opac.'",
						explnum_visible_opac_abon="'.$form_visible_opac_abon.'", 
						explnum_consult_opac_abon="'.$form_consult_opac_abon.'", 
						explnum_download_opac_abon="'.$form_download_opac_abon.'",
						explnum_thumbnail_visible_opac_override="'.$form_thumbnail_visible_opac_override.'" ';
			$res = pmb_mysql_query($requete, $dbh);
		}
		show_statut($dbh);
		break;
	case 'add':
		if (empty($form_gestion_libelle)) statut_form();
			else show_statut($dbh);
		break;
	case 'modif':
		if ($id) {
			$requete = "SELECT id_explnum_statut, gestion_libelle, opac_libelle, explnum_visible_opac, explnum_consult_opac, explnum_download_opac, class_html, explnum_visible_opac_abon, explnum_consult_opac_abon, explnum_download_opac_abon, explnum_thumbnail_visible_opac_override FROM explnum_statut WHERE id_explnum_statut='$id'";
			$res = pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($res)) {
				$row=pmb_mysql_fetch_object($res);
				statut_form($row->id_explnum_statut, $row->gestion_libelle, $row->opac_libelle, $row->explnum_visible_opac, $row->explnum_consult_opac, $row->explnum_download_opac, $row->class_html, $row->explnum_visible_opac_abon, $row->explnum_consult_opac_abon, $row->explnum_download_opac_abon, $row->explnum_thumbnail_visible_opac_override );
			} else {
				show_statut($dbh);
			}
		} else {
			show_statut($dbh);
		}
		break;
	case 'del':
		if ($id && $id!=1) {
			$total = 0;
			$total = pmb_mysql_result(pmb_mysql_query("select count(1) from explnum where explnum_docnum_statut ='".$id."' ", $dbh), 0, 0);
			if ($total==0) {
				$requete = "DELETE FROM explnum_statut WHERE id_explnum_statut='$id' ";
				$res = pmb_mysql_query($requete, $dbh);
				$requete = "OPTIMIZE TABLE explnum_statut ";
				$res = pmb_mysql_query($requete, $dbh);
				show_statut($dbh);
			} else {
				error_message(	$msg["docnum_statut_docnum"], $msg["docnum_statut_used"], 1, 'admin.php?categ=docnum&sub=statut&action=');
			}
		} else show_statut($dbh);
		break;
	default:
		show_statut($dbh);
		break;
	}
