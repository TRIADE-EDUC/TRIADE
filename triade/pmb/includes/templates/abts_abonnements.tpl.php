<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abts_abonnements.tpl.php,v 1.44.4.1 2025/04/18 13:07:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $abonnement_view,$abonnement_list,$abonnement_form;
global $antivol_form, $msg, $current_module, $tpl_calendrier, $abonnement_serialcirc_empr_list_empr, $abonnement_serialcirc_empr_list_group, $abonnement_serialcirc_empr_list_group_elt;
global $pmb_serialcirc_active;
global $creation_abonnement_js_form, $edition_abonnement_js_form;
global $creation_abonnement_content_form, $edition_abonnement_content_form;
global $edition_abonnement_expl_content_form;

$abonnement_view = "
<div id='abts_abonnement!!id_abonnement!!' class='notice-parent'>
    ".get_expandBase_button('abts_abonnement!!id_abonnement!!')."
	<span class='notice-heada'>
    	!!statut!!
    	<a href='!!view_id_abonnement!!'>!!abonnement_header!!</a>
    </span>
    <br />
</div>
<div id='abts_abonnement!!id_abonnement!!Child' class='notice-child' style='margin-bottom:6px;display:none;'>
	<table width='100%'>
		<tr>
			<td>
				".$msg["abonnements_modele_lie"].": !!modele_lie!!
			</td>
			<td>
			</td>
		</tr>
		<tr>
			<td>
				".$msg["abonnements_duree_abonnement"].": !!duree_abonnement!!
			</td>
			<td>
				!!fournisseur!!
			</td>
		</tr>
		<tr>
			<td>
				".$msg["abonnements_date_debut"].": !!date_debut!!
			</td>
			<td>
				".$msg["abonnements_date_fin"].": !!date_fin!!
			</td>
		</tr>
		<tr>
			<td>
				".$msg["abonnements_nombre_de_series"].": !!nombre_de_series!!
			</td>
			<td>
				".$msg["abonnements_nombre_de_horsseries"].": !!nombre_de_horsseries!!
			</td>
		</tr>
		<tr>
			<td>
				".$msg["4050"].": !!prix!!
			</td>
		</tr>
		!!commentaire!!
		!!serialcirc_empr_list!!
		
	</table>
	".($pmb_serialcirc_active ? "<input type='button' class='bouton' value='".$msg["serialcirc_diffusion_gestion_button"]."' onClick=\"document.location='./catalog.php?categ=serialcirc_diff&sub=view&num_abt=!!id_abonnement!!';\"/>&nbsp;" : "")."
	!!serialcirc_export_list_bt!!
</div>
";

$abonnement_list ="
<script type='text/javascript' src='./javascript/tablist.js'></script>
<div class='form-contenu' id='abonnement_list_content'>
<a href='javascript:expandAll(document.getElementById(\"abonnement_list_content\"))'><img src='".get_url_icon('expand_all.gif')."' border='0' id='expandall'></a>
<a href='javascript:collapseAll(document.getElementById(\"abonnement_list_content\"))'><img src='".get_url_icon('collapse_all.gif')."' border='0' id='collapseall'></a>
!!abonnement_list!!
</div>
<div class='row'>
   !!abts_abonnements_add_button!!
</div>";

$abonnement_script1 = "
<script type='text/javascript'>
function test_form(form)
{
	if(form.abt_name.value.replace(/^\s+|\s+$/g, '').length == 0)
	{
		alert(\"$msg[326]\");
		form.abt_name.focus();
		return false;
	}
	!!test_liste_modele!!
	return true;
}
</script>
";

$creation_abonnement_js_form = "
<script type='text/javascript' src='./javascript/tablist.js'></script>
$abonnement_script1
";

$creation_abonnement_content_form = "
<div class='colonne2'>
	<div class='row'>
		<label for='abonnement_name' class='etiquette'>".$msg["abonnements_nom_abonnement"]."</label>
	</div>
	<div class='row'>
		<input type='text' size='40' name='abt_name' id='abt_name' value='!!abt_name!!'/>
	</div>
</div>
<input type='hidden' name='num_notice' id='num_notice' value='!!num_notice!!'/>
<div class='row'></div>
<div class='colonne2'>
	<div class='row'>
		<label for='abonnement_name_opac' class='etiquette'>".$msg["abonnements_nom_opac_abonnement"]."</label>
	</div>
	<div class='row'>
		<input type='text' size='40' name='abt_name_opac' id='abt_name_opac' data-translation-fieldname='abt_name_opac' value='!!abt_name_opac!!'/>
	</div>
</div>
<div class='row'></div>
<div class='colonne2'>
	<div class='row'>
		<label for='abonnement_name' class='etiquette'>".$msg["abonnements_liste_modele"]."</label>
	</div>
	<div class='row'>
		!!liste_modele!!
	</div>
</div>
<div class='row'></div>
!!abonnement_form1!!
";

$edition_abonnement_js_form = "
<script type='text/javascript' src='./javascript/tablist.js'></script>
<script type='text/javascript'>
<!--

	function calcule_section(selectBox) {
		for (i=0; i<selectBox.options.length; i++) {
			id=selectBox.options[i].value;
		    list=document.getElementById(\"docloc_section\"+id);
		    list.style.display=\"none\";
			}
			
		id=selectBox.options[selectBox.selectedIndex].value;
		list=document.getElementById(\"docloc_section\"+id);
		list.style.display=\"block\";
		}
		
		function gere_statut(obj) {
			var obj_check=document.getElementById(obj+'_check');
			
			if(obj_check.checked == true){
				document.getElementById(obj).disabled = false;
			}else{
				document.getElementById(obj).disabled = true;
			}
		}
		
		function expl_part_display() {
			if(document.getElementById('abt_numeric').checked){
				document.getElementById('abt_numeric').checked = 'checked';
				document.getElementById('expl_part').style.display='none';
			} else {
				document.getElementById('abt_numeric').checked = '';
				document.getElementById('expl_part').style.display='';
			}
		}
-->
</script>
$abonnement_script1
";

$edition_abonnement_expl_content_form = "
<div class='row'>
	<div class='colonne3'>
		<!-- cote -->
			<label class='etiquette' for='cote'>$msg[296]</label>
		<div class='row'>
			<input type='text' class='saisie-20em' id=\"cote\" name='cote' value='!!cote!!' />
		</div>
	</div>
	<div class='colonne3'>
		<!-- type document -->
		<label class='etiquette' for='f_ex_typdoc'>$msg[294]</label>
		<div class='row'>
			!!type_doc!!
		</div>
	</div>
	<div class='colonne3'>
		<!-- type document -->
		<label class='etiquette' for='exemp_auto'>$msg[exemplarisation_automatique]</label>
		<div class='row'>
			!!exemplarisation_automatique!!
		</div>
	</div>
</div>
<div class='row'>
	<div class='colonne3'>
		<!-- localisation -->
		<label class='etiquette' for='f_ex_location'>$msg[298]</label>
		<div class='row'>
			!!localisation!!
			</div>
		</div>
	<div class='colonne3'>
		<!-- section -->
		<label class='etiquette' for='f_ex_section'>$msg[295]</label>
		<div class='row'>
			!!section!!
			</div>
		</div>
	<div class='colonne3'>
		<!-- propri?taire -->
		<label class='etiquette' for='f_ex_owner'>$msg[651]</label>
		<div class='row'>
			!!owner!!
			</div>
		</div>
	</div>
<div class='row'>
	<div class='colonne3'>
		<!-- statut -->
		<label class='etiquette' for='f_ex_statut'>$msg[297]</label>
		<div class='row'>
			!!statut!!
		</div>
	</div>
	<div class='colonne3'>
		<!-- code stat -->
		<label class='etiquette' for='f_ex_cstat'>$msg[299]</label>
		<div class='row'>
			!!codestat!!
		</div>
	</div>
	<div class='colonne3'>
		<!-- prix -->
		<label class='etiquette' for='prix'>$msg[4050]</label>
		<div class='row'>
			<input type='text' class='saisie-20em' id=\"prix\" name='prix' value='!!prix!!' />
		</div>
	</div>
	".$antivol_form."
</div>
";

$edition_abonnement_content_form="
<div class='colonne2'>
	<div class='row'>
		<label for='abonnement_name' class='etiquette'>".$msg["abonnements_nom_abonnement"]."</label>
	</div>
	<div class='row'>
		<input type='text' size='40' name='abt_name' id='abt_name' value='!!abt_name!!'/>
	</div>
</div>
<input type='hidden' name='num_notice' id='num_notice' value='!!num_notice!!'/>
<div class='colonne2'>
	<div class='row'>
		<label for='duree_abonnement' class='etiquette'>".$msg["abonnements_duree_abonnement"]."</label>
	</div>
	<div class='row'>
		<input type='text' size='5' name='duree_abonnement' id='duree_abonnement' value='!!duree_abonnement!!'/>
	</div>
</div>
<div class='row'></div>
<div class='colonne2'>
	<div class='row'>
		<label for='abonnement_name_opac' class='etiquette'>".$msg["abonnements_nom_opac_abonnement"]."</label>
	</div>
	<div class='row'>
		<input type='text' size='40' name='abt_name_opac' id='abt_name_opac' data-translation-fieldname='abt_name_opac' value='!!abt_name_opac!!'/>
	</div>
</div>
<div class='row'></div>
<div class='colonne2'>
	<div class='row'>
		<label class='etiquette'>".$msg["abonnements_date_debut"]."</label>
	</div>
	<div class='row'>
		<input type='date' name='date_debut' value='!!date_debut!!' />
	</div>
</div>
<div class='colonne_suite'>
	<div class='row'>
		<label class='etiquette'>".$msg["abonnements_date_fin"]."</label>
	</div>
	<div class='row'>
		<input type='date' name='date_fin' value='!!date_fin!!' />
	</div>
</div>
<div class='colonne2'>
	<div class='row'>
		<label for='fournisseur' class='etiquette'>".$msg["abonnements_fournisseur"]."</label>
	</div>
	<div class='row'>
		<input id='id_fou' name='id_fou' value='!!id_fou!!' type='hidden'>
		<input id='lib_fou' name='lib_fou' tabindex='1' value='!!lib_fou!!' class='saisie-30emr' onchange=\"openPopUp('./select.php?what=fournisseur&caller=form_abonnement&param1=id_fou&param2=lib_fou&id_bibli=0&deb_rech='+".pmb_escape()."(this.form.lib_fou.value), 'selector'); \" type='text'>
		<input type='button' name='fournisseur' class='bouton' value='...'
		onClick=\"openPopUp('./select.php?what=fournisseur&caller=form_abonnement&param1=id_fou&param2=lib_fou&id_bibli=0&deb_rech='+".pmb_escape()."(this.form.lib_fou.value), 'selector');\"   />
		<input type='button' tabindex='1' class='bouton' value='".$msg['raz']."' onclick=\"document.getElementById('id_fou').value='0';document.getElementById('lib_fou').value='';\" />
	</div>
</div>
<div class='colonne_suite'>
	<div class='row'>
		<label for='destinataire' class='etiquette'>".$msg["abonnements_destinataire"]."</label>
	</div>
	<div class='row'>
		<TEXTAREA name='destinataire' rows='6' cols='50'>!!destinataire!!</TEXTAREA>
	</div>
</div>
<div class='row'>
	<input type='checkbox' !!abt_numeric_checked!! value='1' name='abt_numeric' id='abt_numeric' onclick=\"expl_part_display();\"/><label for='abt_numeric' class='etiquette'>".$msg['abt_numeric_checkbox']."</label>
</div>
<div class='row'>
	<div id='expl_part'>
		!!expl_content_form!!
	</div><!-- expl_part end -->
</div>
<div class='row'>
	!!modele_list!!
</div>
";

$tpl_calendrier = "
<form class='form-$current_module' id='form_abonnement' name='form_abonnement' method='post' action='!!action!!'>
	<h3>!!libelle_form!!</h3>
	<div class='form-contenu'>
	<input type='hidden' name='abonnement_id' value='!!abonnement_id!!'/>
	!!calendrier!!
	</div> <!-- Fin du contenu -->
	<div class='row'>
		<input type='hidden' id='act' name='act' value='' />
		<div class='left'><input type=\"submit\" class='bouton' value='".$msg["77"]."' onClick=\"document.getElementById('act').value='update';this.form.submit();\"/>&nbsp;<input type='button' class='bouton' value='".$msg["76"]."' onClick=\"document.location='./catalog.php?categ=serials&sub=view&serial_id=!!serial_id!!&view=abonnement';\"/>&nbsp;<input type='button' class='bouton' value='".$msg["abts_abonnements_copy_abonnement"]."'/></div><div class='right'>!!del_button!!</div>
	</div>
	<div class='row'></div>
</form>
";

$abonnement_serialcirc_empr_list_empr = "
<br />
<div class='row'><a href='!!empr_view_link!!'>!!empr_name!!</a></div>
";

$abonnement_serialcirc_empr_list_group = "
<br />
<div class='row' >
	<div id='group_circ!!id_diff!!' >
    	<img src='".get_url_icon('plus.gif')."' class='img_plus' name='imEx' id='group_circ!!id_diff!!Img' title='".addslashes($msg['plus_detail'])."' onClick=\"expandBase('group_circ!!id_diff!!', true); recalc_recept();return false;\" />
	    <a href='#' >!!empr_name!!</a>
	</div>
	<div id='group_circ!!id_diff!!Child' class='notice-child' style='margin-bottom:6px;display:none;'>
		!!empr_list!!
	</div>
</div>
";

$abonnement_serialcirc_empr_list_group_elt="
<br />
<div class='row'>!!empr_libelle!!</div>
";

