<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: statut.inc.php,v 1.7 2019-06-07 13:40:36 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/list/configuration/collstate/list_configuration_collstate_statut_ui.class.php");

// gestion des codes statut exemplaires
?>
<script type="text/javascript">
function test_form(form)
{
	if(form.form_gestion_libelle.value.length == 0)
	{
		alert("<?php echo $msg[98] ?>");
		return false;
	}
	return true;
}
</script>

<?php
function show_statut($dbh) {
	print list_configuration_collstate_statut_ui::get_instance()->get_display_list();
}

function statut_form($id=0, $gestion_libelle="", $opac_libelle="", $visible_opac=1, $visible_gestion=1, $class_html='', $visible_opac_abon=0) {

	global $msg;
	global $admin_collstate_statut_form;
	global $charset;

	if (!$id) {
		$admin_collstate_statut_form = str_replace('!!form_title!!', $msg[115], $admin_collstate_statut_form);
		$admin_collstate_statut_form = str_replace("!!bouton_supprimer!!","",$admin_collstate_statut_form) ;
	} else {
		$admin_collstate_statut_form = str_replace("!!bouton_supprimer!!","<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id!!,'!!libelle_suppr!!')\" />",$admin_collstate_statut_form) ;
		$admin_collstate_statut_form = str_replace('!!form_title!!', $msg[118], $admin_collstate_statut_form);
	}
	$admin_collstate_statut_form = str_replace('!!id!!', $id, $admin_collstate_statut_form);

	$admin_collstate_statut_form = str_replace('!!gestion_libelle!!', htmlentities($gestion_libelle,ENT_QUOTES, $charset), $admin_collstate_statut_form);
	$admin_collstate_statut_form = str_replace('!!libelle_suppr!!', addslashes($gestion_libelle), $admin_collstate_statut_form);
	//if ($visible_gestion) $checkbox="checked"; else $checkbox="";
	//$admin_collstate_statut_form = str_replace('!!checkbox_visible_gestion!!', $checkbox, $admin_collstate_statut_form);
	
	$admin_collstate_statut_form = str_replace('!!opac_libelle!!', htmlentities($opac_libelle,ENT_QUOTES, $charset), $admin_collstate_statut_form);
	if ($visible_opac) $checkbox="checked"; else $checkbox="";
	$admin_collstate_statut_form = str_replace('!!checkbox_visible_opac!!', $checkbox, $admin_collstate_statut_form);
		
	if ($visible_opac_abon) $checkbox="checked"; else $checkbox="";
	$admin_collstate_statut_form = str_replace('!!checkbox_visu_abon!!', $checkbox, $admin_collstate_statut_form);
	
	for ($i=1;$i<=20; $i++) {
		if ($class_html=="statutnot".$i) $checked = "checked";
		else $checked = "";
		$couleur[$i]="<span for='statutnot".$i."' class='statutnot".$i."' style='margin: 7px;'><img src='".get_url_icon('spacer.gif')."' width='10' height='10' />
					<input id='statutnot".$i."' type=radio name='form_class_html' value='statutnot".$i."' $checked class='checkbox' /></span>";
		if ($i==10) $couleur[10].="<br />";
		elseif ($i!=20) $couleur[$i].="<b>|</b>";
	}
	
	$couleurs=implode("",$couleur);
	$admin_collstate_statut_form = str_replace('!!class_html!!', $couleurs, $admin_collstate_statut_form);

	print confirmation_delete("./admin.php?categ=collstate&sub=statut&action=del&id=");
	print $admin_collstate_statut_form;

}

$id = intval($id);
switch($action) {
    case 'update':
        if(empty($form_visible_opac)) $form_visible_opac = '';
        if(empty($form_visu_abon)) $form_visu_abon = '';
        if(empty($form_visible_gestion)) $form_visible_gestion = '';
        if(empty($form_gestion_libelle)) $form_gestion_libelle = '';
        if(empty($form_opac_libelle)) $form_opac_libelle = '';
        if(empty($form_class_html)) $form_class_html = '';
		if ($id) {
			if ($id==1) $visu=", archstatut_visible_gestion=1, archstatut_visible_opac='$form_visible_opac', archstatut_visible_opac_abon='$form_visu_abon' ";
				else $visu=", archstatut_visible_gestion='$form_visible_gestion', archstatut_visible_opac='$form_visible_opac', archstatut_visible_opac_abon='$form_visu_abon' "; 
			$requete = "UPDATE arch_statut SET archstatut_gestion_libelle='$form_gestion_libelle', archstatut_opac_libelle='$form_opac_libelle', archstatut_class_html='$form_class_html' $visu WHERE archstatut_id='$id' ";
			$res = pmb_mysql_query($requete, $dbh);
		} else {
			$requete = "INSERT INTO arch_statut SET archstatut_gestion_libelle='$form_gestion_libelle',archstatut_visible_gestion='$form_visible_gestion',archstatut_opac_libelle='$form_opac_libelle', archstatut_visible_opac='$form_visible_opac', archstatut_class_html='$form_class_html', archstatut_visible_opac_abon='$form_visu_abon' ";
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
			$requete = "SELECT * FROM arch_statut WHERE archstatut_id='$id'";
			$res = pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($res)) {
				$row=pmb_mysql_fetch_object($res);
				statut_form($row->archstatut_id, $row->archstatut_gestion_libelle, $row->archstatut_opac_libelle, $row->archstatut_visible_opac, $row->archstatut_visible_gestion, $row->archstatut_class_html, $row->archstatut_visible_opac_abon );
			} else {
				show_statut($dbh);
			}
		} else {
			show_statut($dbh);
		}
		break;
	case 'del':
		if ($id) {
			$total = 0;
			$total = pmb_mysql_result(pmb_mysql_query("select count(1) from collections_state where collstate_statut ='".$id."' ", $dbh), 0, 0);
			if ($total==0) {
				$requete = "DELETE FROM arch_statut WHERE archstatut_id='$id' ";
				$res = pmb_mysql_query($requete, $dbh);
				$requete = "OPTIMIZE TABLE arch_statut ";
				$res = pmb_mysql_query($requete, $dbh);
				show_statut($dbh);
				} else {
					error_message(	$msg[294], $msg["collstate_statut_used"], 1, 'admin.php?categ=collstate&sub=statut&action=');
				}
			} else show_statut($dbh);
		break;
	default:
		show_statut($dbh);
		break;
	}
