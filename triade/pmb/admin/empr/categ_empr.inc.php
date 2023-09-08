<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: categ_empr.inc.php,v 1.16 2018-10-12 11:59:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/list/configuration/empr/list_configuration_empr_categ_ui.class.php");

?>
<script type="text/javascript">
function test_form(form)
{
	
	if(form.form_libelle.value.length == 0)
	{
		alert("<?php echo $msg['98']; ?>");
		return false;
	}
	return true;
}

</script>
<?php
function show_section($dbh) {
	print list_configuration_empr_categ_ui::get_instance()->get_display_list();
}

function categempr_form($libelle="", $id=0, $duree_adhesion=365, $tarif="0.00", $age_min="0", $age_max="0") {
	global $msg;
	global $admin_categlec_form ;
	global $charset;
	global $pmb_gestion_financiere,$pmb_gestion_abonnement;
	
	$admin_categlec_form = str_replace('!!id!!', $id, $admin_categlec_form);
	if(!$id) $admin_categlec_form = str_replace('!!form_title!!', $msg['524'], $admin_categlec_form);
		else $admin_categlec_form = str_replace('!!form_title!!', $msg['525'], $admin_categlec_form);

	$admin_categlec_form = str_replace('!!libelle!!', htmlentities($libelle,ENT_QUOTES, $charset), $admin_categlec_form);
	$admin_categlec_form = str_replace('!!libelle_suppr!!', htmlentities(addslashes($libelle),ENT_QUOTES,$charset), $admin_categlec_form);
	$admin_categlec_form = str_replace('!!duree_adhesion!!', htmlentities($duree_adhesion,ENT_QUOTES, $charset), $admin_categlec_form);	
	
	if (($pmb_gestion_financiere)&&($pmb_gestion_abonnement==1)) {
		$tarif_adhesion="
		<div class='row'>
			<label class='etiquette' for='form_tarif_adhesion'>".$msg["empr_categ_tarif"]."</label>
		</div>
		<div class='row'>
			<input type=text name='form_tarif_adhesion' id='form_tarif_adhesion' value='".htmlentities($tarif,ENT_QUOTES,$charset)."' maxlength='10' class='saisie-5em' />
			</div>
		";
	} else $tarif_adhesion="";
	$admin_categlec_form = str_replace('!!tarif_adhesion!!', $tarif_adhesion, $admin_categlec_form);	
	$admin_categlec_form = str_replace('!!age_min!!', htmlentities($age_min,ENT_QUOTES, $charset), $admin_categlec_form);
	$admin_categlec_form = str_replace('!!age_max!!', htmlentities($age_max,ENT_QUOTES, $charset), $admin_categlec_form);
	
	print confirmation_delete("./admin.php?categ=empr&sub=categ&action=del&id=");
	print $admin_categlec_form;

	}

switch($action) {
	case 'update':
		// no duplication
		$requete = " SELECT count(1) FROM empr_categ WHERE (libelle='$form_libelle' AND id_categ_empr!='$id' )  LIMIT 1 ";
		$res = pmb_mysql_query($requete, $dbh);
		$nbr = pmb_mysql_result($res, 0, 0);
		if ($nbr > 0) {
				error_form_message($form_libelle.$msg["docs_label_already_used"]);
		} else {
			// O.k., now if the id already exist UPDATE else INSERT
			if(!empty($form_libelle)) {
				if($id) {
					$requete = "UPDATE empr_categ SET libelle='$form_libelle', duree_adhesion='$form_duree_adhesion', tarif_abt='".$form_tarif_adhesion."', age_min='".$form_age_min."', age_max='".$form_age_max."' WHERE id_categ_empr=$id ";
					$res = pmb_mysql_query($requete, $dbh);
				} else {
					$requete = "SELECT count(1) FROM empr_categ WHERE libelle='$form_libelle' LIMIT 1 ";
					$res = pmb_mysql_query($requete, $dbh);
					$nbr = pmb_mysql_result($res, 0, 0);
					if($nbr == 0) {
						$requete = "INSERT INTO empr_categ (id_categ_empr,libelle,duree_adhesion,tarif_abt,age_min, age_max) VALUES ('', '$form_libelle','$form_duree_adhesion','".$form_tarif_adhesion."','".$form_age_min."','".$form_age_max."') ";
						$res = pmb_mysql_query($requete, $dbh);
					}
				}
			}
		}
		show_section($dbh);
		break;
	case 'add':
		if(empty($form_libelle) && empty($form_pret)) {
			categempr_form();
		} else {
			show_section($dbh);
		}
		break;
	case 'modif':
		if($id){
			$requete = "SELECT libelle, duree_adhesion, tarif_abt, age_min, age_max FROM empr_categ WHERE id_categ_empr=$id LIMIT 1 ";
			$res = pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($res)) {
				$row=pmb_mysql_fetch_row($res);
				categempr_form($row[0], $id, $row[1],$row[2],$row[3],$row[4]);
			} else {
				show_section($dbh);
			}
		} else {
			show_section($dbh);
		}
		break;
	case 'del':
		if($id) {
			$total = 0;
			$total = pmb_mysql_result(pmb_mysql_query("select count(1) from empr where empr_categ ='".$id."' ", $dbh), 0, 0);
			if ($total==0) {
				$test = pmb_mysql_result(pmb_mysql_query("select count(1) from search_persopac_empr_categ where id_categ_empr ='".$id."' ", $dbh), 0, 0);
				if($test == 0){
					$requete = "DELETE FROM empr_categ WHERE id_categ_empr='$id' ";
					$res = pmb_mysql_query($requete, $dbh);
					$requete = "OPTIMIZE TABLE empr_categ ";
					$res = pmb_mysql_query($requete, $dbh);
					$requete = "delete from search_persopac_empr_categ where id_categ_empr = $id";
					$res = pmb_mysql_query($requete, $dbh);
					show_section($dbh);
				}else{
					error_message(	$msg['294'], $msg['empr_categ_cant_delete_search_perso'], 1, 'admin.php?categ=empr&sub=categ&action=');
				}
			} else {
				error_message(	$msg['294'], $msg['1708'], 1, 'admin.php?categ=empr&sub=categ&action=');
				}
		} else show_section($dbh);
		break;
	default:
		show_section($dbh);
		break;
}
