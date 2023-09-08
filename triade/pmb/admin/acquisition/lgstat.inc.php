<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lgstat.inc.php,v 1.2 2011-08-11 15:39:30 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// gestion des statuts de lignes d'actes
require_once("$class_path/lignes_actes_statuts.class.php");


function show_lgstat_list() {
	
	global $dbh;
	global $msg;
	global $charset;

	print "<table>
	<tr>
		<th>".htmlentities($msg[103], ENT_QUOTES, $charset)."</th>
		<th>".htmlentities($msg['acquisition_lgstat_arelancer'], ENT_QUOTES, $charset)."</th>
	</tr>";

	$tab_lgstat = lgstat::getList();

	$parity=1;
	foreach($tab_lgstat as $id_statut=>$lgstat) {
			if ($parity % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			$parity += 1;
			$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./admin.php?categ=acquisition&sub=lgstat&action=modif&id=$id_statut';\" ";
	        print "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>";
	        if ($id_statut==1) print "<td><i><strong>".htmlentities($lgstat[0], ENT_QUOTES, $charset)."</strong></i></td>";
	        	else print "<td><i>".htmlentities($lgstat[0], ENT_QUOTES, $charset)."</i></td>";
			$arelancer=$msg[39];
	        if($lgstat[1]=='1')	{     	
	        	$arelancer=$msg[40];
	        }
	        print "<td><i>".htmlentities($arelancer, ENT_QUOTES, $charset)."</i></td>";
	        print "</tr>";
	}
	print "</table>
		<input class='bouton' type='button' value='".htmlentities($msg['acquisition_lgstat_add'],ENT_QUOTES,$charset)."' onClick=\"document.location='./admin.php?categ=acquisition&sub=lgstat&action=add'\" />";

}


function show_lgstat_form($id=0) {
		
	global $msg;
	global $charset;
	global $lgstat_form;
	global $ptab;
	
	$lgstat_form = str_replace('!!id!!', $id, $lgstat_form);
	$sel_relance_option_0= "";
	$sel_relance_option_1= " selected='selected' ";
	
	if(!$id) {
		
		$lgstat_form = str_replace('!!form_title!!', htmlentities($msg['acquisition_lgstat_add'],ENT_QUOTES,$charset), $lgstat_form);
		$lgstat_form = str_replace('!!libelle!!', '', $lgstat_form);

	} else {
		
		$lgstat = new lgstat($id);
		if($lgstat->relance==0) {
			$sel_relance_option_0=" selected='selected' ";
			$sel_relance_option_1="";
		}
		$lgstat_form = str_replace('!!form_title!!', htmlentities($msg['acquisition_lgstat_mod'],ENT_QUOTES,$charset), $lgstat_form);
		$lgstat_form = str_replace('!!libelle!!', htmlentities($lgstat->libelle,ENT_QUOTES,$charset), $lgstat_form);
		
		$ptab = str_replace('!!id!!', $id, $ptab);
		$ptab = str_replace('!!libelle_suppr!!', addslashes($lgstat->libelle), $ptab);
		if ($lgstat->id_statut!='1') $lgstat_form = str_replace('<!-- bouton_sup -->', $ptab, $lgstat_form);
	}

	$sel_relance = "<select id='relance' name ='relance' >";
	$sel_relance.= "<option value='1'".$sel_relance_option_1.">".htmlentities($msg[40],ENT_QUOTES,$charset)."</option>";
	$sel_relance.= "<option value='0'".$sel_relance_option_0.">".htmlentities($msg[39],ENT_QUOTES,$charset)."</option>";
	$sel_relance.= "</select>";
	$lgstat_form = str_replace('!!sel_relance!!', $sel_relance, $lgstat_form);
	
	print confirmation_delete("./admin.php?categ=acquisition&sub=lgstat&action=del&id=");
	print $lgstat_form;
	
}


$lgstat_form = "
<form class='form-".$current_module."' id='lgstatform' name='lgstatform' method='post' action=\"./admin.php?categ=acquisition&sub=lgstat&action=update&id=!!id!!\">
<h3>!!form_title!!</h3>
<!--    Contenu du form    -->
<div class='form-contenu'>

	<div class='row'>
		<label class='etiquette' for='libelle'>".htmlentities($msg[103],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='row'>
		<input type=text id='libelle' name='libelle' value=\"!!libelle!!\" class='saisie-60em' />
	</div>
	<div class='row'>
		<label class='etiquette'>".htmlentities($msg['acquisition_lgstat_arelancer'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='row'>
		!!sel_relance!!
	</div>
	<div class='row'></div>
</div>

<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='./admin.php?categ=acquisition&sub=lgstat' \" />&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\" />
	</div>
	<div class='right'>
		<!-- bouton_sup -->
	</div>
</div>
<div class='row'>
</div>
</form>
<script type='text/javascript'>
	document.forms['lgstatform'].elements['libelle'].focus();
</script>

";

$ptab = "<input class='bouton' type='button' value=' ".$msg['supprimer']." ' onClick=\"javascript:confirmation_delete('!!id!!', '!!libelle_suppr!!')\" />";

?>

<script type='text/javascript'>
function test_form(form)
{
	if(form.libelle.value.length == 0)
	{
		alert("<?php echo $msg[98]; ?>");
		document.forms['lgstatform'].elements['libelle'].focus();
		return false;	
	}
	return true;
}
</script>

<?php

//Traitement des actions
switch($action) {
	
	case 'add':
		show_lgstat_form();
		break;		
	
	case 'modif':
		if (lgstat::exists($id)) {
			show_lgstat_form($id);
		} else {
			show_lgstat_list();
		}
		break;
		
	case 'update':
		// vérification validité des données fournies.
		//Pas deux statuts de lignes d'actes identiques
		$nbr = lgstat::existsLibelle($libelle, $id);
		if ( $nbr > 0 ) {
			error_form_message($libelle.$msg['acquisition_lgstat_already_used']);
			break;
		}
		$lgstat = new lgstat($id);
		$lgstat->libelle = $libelle;
		$lgstat->relance = $relance;
		$lgstat->save();
		show_lgstat_list();
		break;
		
	case 'del':
	
		if($id) {
			if ($id=='1') {	//statut de ligne d'acte avec id=1 non supprimable
				$msg_suppr_err = $msg['acquisition_lgstat_used'] ;
				error_message($msg[321], $msg_suppr_err, 1, 'admin.php?categ=acquisition&sub=lgstat');
			} else {
				$total1 = lgstat::isUsed($id);
				if ($total1==0) {
					lgstat::delete($id);
				} else {
					$msg_suppr_err = $msg['acquisition_lgstat_used'] ;
					if ($total1) $msg_suppr_err .= "<br />- ".$msg['acquisition_lgstat_used_lgact'] ;
					error_message($msg[321], $msg_suppr_err, 1, 'admin.php?categ=acquisition&sub=lgstat');
				}
			}
		}
		show_lgstat_list();
		break;

	default:
		show_lgstat_list();
		break;
}