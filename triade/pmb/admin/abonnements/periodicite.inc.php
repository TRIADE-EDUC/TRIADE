<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: periodicite.inc.php,v 1.10 2019-06-05 13:13:19 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/list/configuration/abonnements/list_configuration_abonnements_periodicite_ui.class.php");

// gestion des codes statut exemplaires
?>
<script type="text/javascript">
function test_form(form) {
	if(form.libelle.value.length == 0) {
		alert("<?php echo $msg[98] ?>");
		return false;
	}
	if((isNaN(form.duree.value)) || (form.duree.value == 0)) {
		alert("<?php echo $msg['abonnements_duree_erreur_saisie'] ?>");
		return false;
	}
	return true;
}
</script>

<?php
function show_statut($dbh) {
	print list_configuration_abonnements_periodicite_ui::get_instance()->get_display_list();
}

function statut_form($id=0, $libelle="", $duree=0, $unite=0, $seuil_periodicite=0, $retard_periodicite=0,$consultation_duration=0) {

	global $msg;
	global $admin_abonnements_periodicite_form;
	global $charset;

	if (!$id) {
		$admin_abonnements_periodicite_form = str_replace('!!form_title!!', $msg['abonnements_ajouter_une_periodicite'], $admin_abonnements_periodicite_form);
		$admin_abonnements_periodicite_form = str_replace("!!bouton_supprimer!!","",$admin_abonnements_periodicite_form) ;
	} else {
		$admin_abonnements_periodicite_form = str_replace("!!bouton_supprimer!!","<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id!!,'!!libelle_suppr!!')\" />",$admin_abonnements_periodicite_form) ;
		$admin_abonnements_periodicite_form = str_replace('!!form_title!!', $msg[118], $admin_abonnements_periodicite_form);
	}
	$admin_abonnements_periodicite_form = str_replace('!!id!!', $id, $admin_abonnements_periodicite_form);

	$admin_abonnements_periodicite_form = str_replace('!!libelle!!', htmlentities($libelle,ENT_QUOTES, $charset), $admin_abonnements_periodicite_form);
	$admin_abonnements_periodicite_form = str_replace('!!libelle_suppr!!', addslashes($libelle), $admin_abonnements_periodicite_form);

	$admin_abonnements_periodicite_form = str_replace('!!duree!!', htmlentities($duree,ENT_QUOTES, $charset), $admin_abonnements_periodicite_form);
	$selected[0]=$selected[1]=$selected[2]='';
	$selected[$unite]= "selected='selected'";
	$str_unite="
       <select id='unite' name='unite'>
        <option value='0'$selected[0]>".$msg['abonnements_periodicite_unite_jour']."</option>
        <option value='1'$selected[1]>".$msg['abonnements_periodicite_unite_mois']."</option>
        <option value='2'$selected[2]>".$msg['abonnements_periodicite_unite_annee']."</option>
        </select>";
	$admin_abonnements_periodicite_form = str_replace('!!unite!!', $str_unite, $admin_abonnements_periodicite_form);

	$admin_abonnements_periodicite_form = str_replace('!!seuil_periodicite!!', htmlentities($seuil_periodicite,ENT_QUOTES, $charset), $admin_abonnements_periodicite_form);
	
	$admin_abonnements_periodicite_form = str_replace('!!retard_periodicite!!', htmlentities($retard_periodicite,ENT_QUOTES, $charset), $admin_abonnements_periodicite_form);
	$admin_abonnements_periodicite_form = str_replace('!!consultation_duration!!', htmlentities($consultation_duration,ENT_QUOTES, $charset), $admin_abonnements_periodicite_form);
	
	print confirmation_delete("./admin.php?categ=abonnements&sub=periodicite&action=del&id=");
	print $admin_abonnements_periodicite_form;

	}

switch($action) {
	case 'update':
		if (($retard_periodicite>=$seuil_periodicite)||($retard_periodicite==0)) {
			if ($id) {
				$requete = "UPDATE abts_periodicites SET libelle='$libelle',duree='$duree',unite='$unite', seuil_periodicite='$seuil_periodicite', retard_periodicite='$retard_periodicite', retard_periodicite='$retard_periodicite' , consultation_duration='$consultation_duration' WHERE periodicite_id='$id' ";
				$res = pmb_mysql_query($requete, $dbh);
				show_statut($dbh);
			} else {
				$requete1=pmb_mysql_query("SELECT count(*) FROM abts_periodicites WHERE libelle='$libelle'");
				if ($requete1)
				{
					$result1=pmb_mysql_fetch_array($requete1);
					if ($result1[0]==0) {
						$requete = "INSERT INTO abts_periodicites SET libelle='$libelle',duree='$duree',unite='$unite', seuil_periodicite='$seuil_periodicite', retard_periodicite='$retard_periodicite' , consultation_duration='$consultation_duration' ";
						$res = pmb_mysql_query($requete, $dbh);
						show_statut($dbh);
					} else {
						error_message_history(	$msg['periodicite_existante'], $msg['periodicite_existante'], 1);	
					}	
					pmb_mysql_free_result($requete1);
				} else {
					print $msg['err_sql']."\n";
					print pmb_mysql_error();
				}
			}
		} else {
			error_message(	$msg['retard_rapport_seuil'], $msg['retard_rapport_seuil'], 1, 'admin.php?categ=abonnements&sub=periodicite&action=');		
		}
		break;
	case 'add':
		if (empty($libelle)) statut_form();
		else show_statut($dbh);
		break;
	case 'modif':
		if ($id) {
			$requete = "SELECT libelle, duree, unite, retard_periodicite, seuil_periodicite,consultation_duration FROM abts_periodicites WHERE periodicite_id='$id'";
			$res = pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($res)) {
				$row=pmb_mysql_fetch_object($res);
				statut_form($id, $row->libelle, $row->duree, $row->unite, $row->seuil_periodicite, $row->retard_periodicite, $row->consultation_duration);
			} 
		}else {
			show_statut($dbh);
		}
		break;
	case 'del':
		if ($id) {
			$total = 0;
			$total = pmb_mysql_result(pmb_mysql_query("select count(1) from abts_modeles where num_periodicite ='".$id."' ", $dbh), 0, 0);
			if ($total==0) {
				$requete = "DELETE FROM abts_periodicites WHERE periodicite_id='$id' ";
				$res = pmb_mysql_query($requete, $dbh);
				$requete = "OPTIMIZE TABLE abts_periodicites ";
				$res = pmb_mysql_query($requete, $dbh);
				show_statut($dbh);
				} else {
					error_message(	$msg['noti_statut_noti'], $msg['noti_statut_used'], 1, 'admin.php?categ=abonnements&sub=periodicite&action=');
				}
			} else show_statut($dbh);
		break;
	default:
		show_statut($dbh);
		break;
	}
