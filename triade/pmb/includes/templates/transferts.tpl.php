<?php
// +-------------------------------------------------+
// Â© 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transferts.tpl.php,v 1.45 2021/02/25 13:39:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $transferts_reception_lot, $transferts_retour_lot, $transferts_validation_actif, $transferts_envoi_lot, $msg;
global $transferts_popup_global, $base_path, $charset, $transferts_popup_expl_fantome_radio, $transferts_popup_table_expl_fantomes, $transferts_popup_ligne_tableau;
global $transfert_popup_ligne_groupe_tableau, $transfert_popup_groups_checkbox, $transferts_popup_ligne_tableau_ex_fantome, $transferts_popup_enregistre_demande;
global $transferts_validation_acceptation_erreur, $transferts_validation_acceptation_OK;
global $transferts_envoi_erreur;
global $transferts_envoi_OK;
global $transferts_reception_erreur, $transferts_reception_OK;
global $transferts_reception_avertissement_retour;
global $javascript_path;
global $transferts_retour_acceptation_erreur, $transferts_retour_acceptation_OK, $transferts_reset_OK;
global $transferts_refus_redemande_global;
global $transferts_admin_modif_ordre_loc, $transferts_admin_modif_ordre_loc_ligne;
global $transferts_admin_modif_ordre_loc_ligne_flBas, $transferts_admin_modif_ordre_loc_ligne_flHaut;
global $transferts_admin_statuts_loc_modif, $transferts_admin_purge_defaut, $transferts_admin_purge_message_ok;

if(!isset($transferts_reception_lot)) $transferts_reception_lot = 0;
if(!isset($transferts_retour_lot)) $transferts_retour_lot = 0;
if(!isset($transferts_validation_actif)) $transferts_validation_actif = 0;
if(!isset($transferts_envoi_lot)) $transferts_envoi_lot = 0;

//*******************************************************************
// Définition des templates pour le popup de demande de transfert
//*******************************************************************

$transferts_popup_global = "
		<script type='text/javascript' src='./javascript/sorttable.js'></script>
		<form class='form-catalog' name='transferts' method='post' action='".$base_path."/catalog/transferts/transferts_popup.php?action=enregistre'>
		<h3>".$msg["transferts_popup_lib_titre"]."</h3>
		!!expl_fantome_checkbox!!				
		<div class='form-contenu'>
			".$msg["transferts_popup_lib_exemplaire"]."	
			<table border='0' class='sortable'>		
			<tr>
				<th class='align_left'>".$msg[293]."</th>
				<th class='align_left'>".$msg[296]."</th>
				<th class='align_left'>".$msg[298]."</th>
				<th class='align_left'>".$msg[295]."</th>
				<th class='align_left'>".$msg[294]."</th>
				<th class='align_left'>".$msg[651]."</th>
			</tr>
			!!liste_exemplaires!!
			</table>
			<div class='row'>
				<label class='etiquette'>".$msg["transferts_popup_lib_destination"]."</label> <b>!!dest_localisation!!</b>
				<input type='hidden' name='dest_id' value='!!loc_id!!'>
			</div>
			<div class='row'>&nbsp;</div>		
			<div class='row'>		
				<label class='etiquette'>".$msg["transferts_popup_motif"]."</label><br />
				<textarea name='motif' cols=40 rows=5></textarea>
			</div>
			<div class='row'>&nbsp;</div>		
			<div class='row'>
				<label class='etiquette' for='transferts_popup_ask_date'>".htmlentities($msg['transferts_popup_ask_date'],ENT_QUOTES,$charset)."</label>
				<input type='date' id='transferts_popup_ask_date' name='transferts_popup_ask_date' value='" . date("Y-m-d") . "' required />
			</div>
			<div class='row'>		
				<label class='etiquette'>".$msg["transferts_popup_date_retour"]."</label>
				<input type='date' id='date_retour' name='date_retour' value='!!date_retour_mysql!!' required />
			</div>
			!!table_exemplaire_fantome!!
			<!--!!hook_tansfert_popup_result!!-->
		</div>
		<input type='submit' class='bouton_small' name='".$msg["transferts_popup_btValider"]."' value='".$msg["transferts_popup_btValider"]."'>
		&nbsp;
		<input type='button' class='bouton_small' name='".$msg["transferts_popup_btAnnuler"]."' value='".$msg["transferts_popup_btAnnuler"]."' onclick='window.close();'>
		<input type='hidden' name='expl_ids' value='!!expl_ids!!'>
		</form>
		
		";
		
$transferts_popup_expl_fantome_radio = "
		<input type='radio' autocomplete='off' checked='checked' id='transfert_expl' name='transfert_type' value='0'/>
		<label>$msg[transfert_expls]</label><br/>
		
		<input type='radio' autocomplete='off' id='transfert_ghost' name='transfert_type' value='1'/>
		<label>$msg[transfert_ghost_expl]</label>
		
		<script type='text/javascript'>
			var transfertTypeRadio = document.getElementsByName('transfert_type');
			window.onload = function(){spanGroups = document.querySelectorAll('span[is_group=\"true\"]');}
			var switchCheckboxes = function(display){
				for(var i=0 ; i<spanGroups.length ; i++){
					if(display){
						spanGroups[i].style.display = '';
						document.getElementById(spanGroups[i].id+'_checkbox').disabled = false;
					}else{
						spanGroups[i].style.display = 'none';
						document.getElementById(spanGroups[i].id+'_checkbox').disabled = true;
					}
				}
			}
			var switchTransfertType = function(evt){
				var clickedRadio = evt.target.getAttribute('id');
				switch(clickedRadio){
					case 'transfert_expl':
						var tableVirtual = document.getElementById('ghost_table');
						if(tableVirtual.style.display != 'none'){
							tableVirtual.style.display = 'none';
						}
						switchCheckboxes(true);
						break;
					case 'transfert_ghost':
						var tableVirtual = document.getElementById('ghost_table');
						if(tableVirtual.style.display == 'none'){
							tableVirtual.style.display = '';
						}
						if(document.body.offsetWidth < tableVirtual.offsetWidth){
							window.resizeTo(tableVirtual.offsetWidth+50, document.body.offsetHeight);	
						}
						switchCheckboxes();
						break;
				}
			}
			for(var i=0 ; i<transfertTypeRadio.length ; i++){
				transfertTypeRadio[i].addEventListener('click', switchTransfertType, false);
			}
		</script>
		
		";

$transferts_popup_table_expl_fantomes = "
		<div class='row' id='virtual_ex_div' >
			<table style='display:none' border='0' id='ghost_table' class='sortable'>		
				<tr>
					<th class='align_left'>".$msg["transfert_ghost_expl_from"]."</th>
					<th class='align_left'>".$msg[293]."</th>
					<th class='align_left'>".$msg[296]."</th>
					<th class='align_left'>".$msg["extexpl_statut"]."</th>
					<th class='align_left'>".$msg["groupexpl_form_comment"]."</th> 
				</tr>
				!!liste_exemplaires_fantomes!!
			</table>		
		</div>";

$transferts_popup_ligne_tableau = "
		<tr class='!!class_ligne!!'>
			<td>!!expl_cb!!</td>
			<td>!!expl_cote!!</td>
			<td>!!location_libelle!!</td>
			<td>!!section_libelle!!</td>
			<td>!!tdoc_libelle!!</td>
			<td>!!lender_libelle!!</td>
		</tr>
		";
		
$transfert_popup_ligne_groupe_tableau = "
		<th class='align_left'>!!group_libelle!!</th>
		<th class='align_left' colspan='5'>!!group_expl_checkbox!!</th>
		";
		
$transfert_popup_groups_checkbox = "
		<span is_group='true' id='transfert_all_group_!!group_id!!'>
			<input type='checkbox' autocomplete='off' value='!!group_id!!' name='transfert_all_group[]' id='transfert_all_group_!!group_id!!_checkbox'/>
			!!group_expl_libelle!!
		</span>
		";


$transferts_popup_ligne_tableau_ex_fantome = "
		<tr class='!!class_ligne!!' id='ghost_line'>
		 	<td>!!cb_ghost_from!!</td>
			<td><input type='text' name='expl_virtual_cb' value='!!new_expl_cb!!' readonly/></td>
			<td><input type='text' name='expl_virtual_cote' value='!!expl_cote!!'/></td>
			<td>!!expl_status!!</td>
			<td><textarea rows='2' name='expl_virtual_comment'></textarea></td>
			<input type='hidden' name='from_codestat' value='!!expl_codestat!!'/>
			<input type='hidden' name='from_location' value='!!expl_location!!'/>
			<input type='hidden' name='from_owner' value='!!expl_owner!!'/>
			<input type='hidden' name='from_section' value='!!expl_section!!'/>
			<input type='hidden' name='from_typdoc' value='!!expl_typdoc!!'/>
			<input type='hidden' name='from_!!parent_type!!' value='!!parent_num!!'/>
			<input type='hidden' name='from_expl_parent_id' value='!!expl_parent_id!!'/>
		</tr>
		";

$transferts_popup_enregistre_demande = "
		<script>
			window.close();
		</script>
		";

//*******************************************************************
// Définition des templates pour le parcours des listes de transfert
// en circulation
//*******************************************************************


//*******************************************************************
// Définition des templates pour l'interface de validation
//*******************************************************************

$transferts_validation_acceptation_erreur = "
		<div class='center erreur'>
			<img src='".get_url_icon('warning.gif')."'><b>&nbsp;".$msg["transferts_circ_validation_erreur_acceptation"]."</b>
		</div>
		";

$transferts_validation_acceptation_OK = "
		<div class='center'>
			<b>".$msg["transferts_circ_validation_accepte"]."</b>
		</div>
		";

//*******************************************************************
// Définition des templates pour l'interface d'envoi
//*******************************************************************

$transferts_envoi_erreur = "
		<div class='center erreur'>
			<img src='".get_url_icon('warning.gif')."'><b>&nbsp;".$msg["transferts_circ_envoi_erreur"]."</b>
		</div>
		";

$transferts_envoi_OK = "
		<div class='center'>
			<b>".$msg["transferts_circ_envoi_accepte"]."</b>
		</div>
		";

//*******************************************************************
// Définition des templates pour l'interface de reception
//*******************************************************************

$transferts_reception_erreur = "
		<div class='center erreur'>
			<img src='".get_url_icon('warning.gif')."'><b>&nbsp;".$msg["transferts_circ_reception_erreur"]."</b>
		</div>
		";

$transferts_reception_OK = "
		<div class='center row'>
			<b>".$msg["transferts_circ_reception_accepte"]."</b>
		</div>
		";

$transferts_reception_avertissement_retour = "
		<img src='".get_url_icon('warning.gif')."' border=0> ".$msg["transfert_reception_avertissement_retour"]."<select>!!liste_statut_origine!!</select>
		<br />";

//*******************************************************************
// Définition des templates pour l'interface de retour
//*******************************************************************

$transferts_retour_acceptation_erreur = "
		<div class='center erreur'>
			<img src='".get_url_icon('warning.gif')."'><b>&nbsp;".$msg["transferts_circ_validation_erreur_acceptation"]."</b>
		</div>
		";

$transferts_retour_acceptation_OK = "
		<div class='center'>
			<b>".$msg["transferts_circ_retour_accepte"]."</b>
		</div>
		";

$transferts_reset_OK = "
		<div class='center'>
			<b>".$msg["transferts_circ_reset"]."</b>
		</div>
		";

//*******************************************************************
// Définition des templates pour l'interface des refus
//*******************************************************************

$transferts_refus_redemande_global = "
		<br />
		<form name='form_circ_trans_redemande' class='form-circ' method='post' action='!!action_formulaire!!&action=redem'>
		<h3>".$msg["transferts_circ_refus_relance"]."</h3>
		<div class='form-contenu' >
			!!detail_notice!!
			<div class='row'>&nbsp;</div>		
			<div class='row'>
				<label class='etiquette'>".$msg["transferts_circ_refus_relance_apartir"]."</label>
				<select name='source'>!!liste_sites!!</select></div>
			<div class='row'>&nbsp;</div>		
			<div class='row'>		
				<label class='etiquette'>".$msg["transferts_circ_refus_relance_motif"]."</label><br />
				<textarea name='motif' cols=40 rows=5></textarea>
			</div>
			<div class='row'>&nbsp;</div>		
			<div class='row'>		
				<label class='etiquette'>".$msg["transferts_circ_refus_relance_retour"]."</label>
				<input type='date' name='date_retour' value='!!date_retour_mysql!!'>
			</div>
		</div>
		<input type='submit' class='bouton' name='".$msg["89"]."' value='".$msg["89"]."'>
		&nbsp;
		<input type='button' class='bouton' name='".$msg["76"]."' value='".$msg["76"]."' onclick='document.location=\"!!action_formulaire!!\"'>
		<input type='hidden' name='transid' value='!!trans_id!!'>
		</form>
		";

//*******************************************************************
// Définition des templates pour l'administration des transferts
//*******************************************************************

$transferts_admin_modif_ordre_loc = "
		<form class='form-admin' name='modifOrdre' method='post' action='./admin.php?categ=transferts&sub=ordreloc&action=enregistre'>
		<h3>".$msg["admin_tranferts_ordre_localisation"]."</h3>
		<div class='form-contenu'>
		<table>
			!!liste_sites!!
		</table>
		</div>
		<input type='hidden' name='sens'>
		<input type='hidden' name='idLoc'>
		<div class='row'></div>
		</form>
		<script language='javascript'>
				function chgOrdre(id,dir) {
					document.modifOrdre.sens.value=dir;
					document.modifOrdre.idLoc.value=id;
					document.modifOrdre.submit();
				}
		</script>
		";

$transferts_admin_modif_ordre_loc_ligne = "
		<tr class='!!class_ligne!!'  onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='!!class_ligne!!'\">
			<td><i>!!lib_site!!</i></td>
			<td>!!fl_haut!!</td>
			<td>!!fl_bas!!</td>
			</tr>
		";
		
$transferts_admin_modif_ordre_loc_ligne_flBas = "<a href='javascript:chgOrdre(!!idSite!!,1);' style='cursor:hand'><img src=\"".get_url_icon('arrow_down.png')."\"  alt=\"".$msg["admin_transferts_lib_descend"]."\"></a>";

$transferts_admin_modif_ordre_loc_ligne_flHaut = "<a href='javascript:chgOrdre(!!idSite!!,-1);' style='cursor:hand'><img src='".get_url_icon('arrow_up.png')."' alt=\"".$msg["admin_transferts_lib_monte"]."\"'></a>";

$transferts_admin_statuts_loc_modif = "
		<form class='form-admin' name='modifStatutDef' method='post' action='./admin.php?categ=transferts&sub=statutsdef&action=enregistre'>
		<h3>!!nom_site!!</h3>
		<div class='form-contenu'>
		".$msg["admin_transferts_statutsDef_statuts"]." : <select name='statutDef'>!!liste_statuts!!</select>
		</div>
		<div class='left'>
			<input type='button' class='bouton' value='".$msg["admin_transferts_annuler"]."' onClick=\"document.location='./admin.php?categ=transferts&sub=statutsdef';\">&nbsp;&nbsp;&nbsp;
			<input type='submit' class='bouton' value='".$msg["admin_transferts_enregistrer"]."'>
		</div>
		<div class='row'></div>
		<input type='hidden' name='id' value='!!id_site!!'>
		</form>
		<script language='javascript'>
			selOpt(document.modifStatutDef.statutDef,'!!selStatut!!');
			
			//function qui selectionne l'option dans une liste en cherchant la bonne valeur
			function selOpt(objSel,valueOpt) {
				for(i=0;i<(objSel.length);i++) {
					if (objSel[i].value==valueOpt)
							objSel.selectedIndex = i;
							objSel[i].selected == true;
				}
			}

		</script>
		";

$transferts_admin_purge_defaut = "
		<div class='row'>		
			<label class='etiquette'>!!message_purge!!</label>
		</div>
		<form class='form-admin' name='transferts' method='post' action='".$base_path."/admin.php?categ=transferts&sub=purge&action=purge'>
		<h3>".$msg["admin_transferts_titre_purge"]."</h3>
		<div class='form-contenu'>
			<div class='row'>&nbsp;</div>
			<div class='erreur'>
				".$msg["admin_transferts_avertissement_purge"]."
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>		
				<label class='etiquette'>".$msg["admin_transferts_date_purge"]."</label>
				<input type='date' name='date_purge' value='!!date_purge_mysql!!'>
			</div>
			<div class='row'>&nbsp;</div>
		</div>
		<input type='button' class='bouton' name='".$msg["admin_transferts_purger"]."' value='".$msg["admin_transferts_purger"]."' onclick=\"if (confirm('".$msg["admin_transferts_avertissement_purge_confirm"]."'+new Date(transferts.date_purge.value).toLocaleDateString()+'".$msg["admin_transferts_avertissement_purge_confirm_suite"]."')) transferts.submit();\">
		</form>
		";
		
$transferts_admin_purge_message_ok = "
		<div class='row'>".$msg["admin_transferts_message_purge"]."</div>
		";