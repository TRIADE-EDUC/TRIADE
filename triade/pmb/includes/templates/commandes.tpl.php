<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: commandes.tpl.php,v 1.85 2020/11/04 11:32:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $id_bibli, $id_cde, $id_exercice, $cdelist_form, $current_module, $msg, $charset, $cde_help_jscript, $modif_cde_duplicate_transfer_jscript, $modif_cde_form, $acquisition_gestion_tva;
global $acquisition_budget, $acquisition_type_produit, $deflt3lgstatcde, $modif_cde_sel_typ_for_checked, $modif_cde_sel_rub_for_checked, $modif_cde_row_form, $first_applicant_line;
global $others_applicants_line, $sel_date_pay_mod, $sel_date_liv_mod, $sel_date_liv_fix, $bt_enr, $bt_val, $bt_dup, $bt_sup, $bt_arc, $bt_imp, $valid_cde_form, $bt_enr_valid;
global $bt_rec, $bt_fac, $bt_sol, $bt_audit, $valid_cde_row_form, $applicants_common_tpl;
global $acquisition_increase_rate_percent;

if(!isset($id_bibli)) $id_bibli = 0;
if(!isset($id_cde)) $id_cde = 0;
if(!isset($id_exercice)) $id_exercice = 0;

$cdelist_form = "
<script type='text/javascript'>
	function survol(obj){
		obj.style.cursor = 'pointer';
	}
	function sort_by_col(type){
		document.forms['search'].sortBy.value = type;
		document.forms['search'].submit();
	}
</script>
<form class='form-$current_module' id='act_list_form' name='act_list_form' method='post' action=\"\" >
	<div class='form-contenu'>
		<table style='width:100%' ><tbody>
			<tr>
			<th onMouseOver ='survol(this);' onClick='sort_by_col(\"!!sortBy_1_1!!\");'>".htmlentities($msg['38'], ENT_QUOTES, $charset)."!!sortBy_1_2!!</th>
			<th onMouseOver ='survol(this);' onClick='sort_by_col(\"!!sortBy_2_1!!\");'>".htmlentities($msg['acquisition_ach_fou2'], ENT_QUOTES, $charset)."!!sortBy_2_2!!</th>
			<th onMouseOver ='survol(this);' onClick='sort_by_col(\"!!sortBy_3_1!!\");'>".htmlentities($msg['acquisition_cde_date_cde'], ENT_QUOTES, $charset)."!!sortBy_3_2!!</th>
			<th onMouseOver ='survol(this);' onClick='sort_by_col(\"!!sortBy_4_1!!\");'>".htmlentities($msg['acquisition_cde_date_ech'], ENT_QUOTES, $charset)."!!sortBy_4_2!!</th>
			<th>".htmlentities($msg['acquisition_statut'], ENT_QUOTES, $charset)."</th>
			<th class='act_cell_chkbox'>&nbsp;</th>
			<!-- chk_th -->
			</tr>
			<!-- cde_list -->
		</tbody></table>
	</div>
	<div class='row'>
		<div class='left'></div>
		<div class='right'><!-- bt_chk --></div>
	</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='left'><!-- bt_list --></div>
		<div class='right'><!-- bt_sup --></div>
	</div>
	<div class='row'></div>
</form>
<!-- script -->
<br />
<div class='form' >
	<!-- nav_bar -->
</div>
";

$cde_help_jscript = "
	<script type='text/javascript' src='./javascript/ajax.js'></script>
	<script type='text/javascript'>
		function showHelp(obj) {


			kill_frame_help();

			var pos=findPos(obj);
			var whatis = 	obj.getAttribute('whatis');
			var helpdir = 	obj.getAttribute('helpdir');

			var url='./acquisition/achats/commandes/frame_help.php?whatis='+whatis+'&helpdir='+helpdir;
			var help_view=document.createElement('iframe');
			help_view.setAttribute('id','frame_help');
			help_view.setAttribute('name','help');
			help_view.src=url;

			var att=document.getElementById('att');
			help_view.style.visibility='hidden';
			help_view.style.display='block';
			help_view=att.appendChild(help_view);

			help_view.style.left=(pos[0])+'px';
			help_view.style.top=(pos[1])+'px';

			help_view.style.visibility='visible';
		}

		function kill_frame_help() {
			var help_view=document.getElementById('frame_help');
			if (help_view)
				help_view.parentNode.removeChild(help_view);
		}
	</script>
";
$modif_cde_duplicate_transfer_jscript = "
<script type='text/javascript'>

function act_line_action(type_action) {

	var checked_lines = 0;
	var unsaved_lines = false;

	for (var i=1; i<=act_curline; i++) {

		var c = document.getElementById('chk['+i+']');
		if(c && c.checked) {
				checked_lines++;
		}

		var id_lig = document.getElementById('id_lig['+i+']');
		if(id_lig && (0==id_lig.value)) {
			unsaved_lines = true;
       		}

	}

    if(0==checked_lines) {
        alert('".addslashes($msg['acquisition_action_no_checked_line'])."');
        return false;
    }

    if(true==unsaved_lines) {
        alert('".addslashes($msg['acquisition_action_unsaved_lines'])."');
        return false;
    }

    if ( (type_action == 'transfer') && (act_nblines<=checked_lines) ) {
        alert('".addslashes($msg['acquisition_action_transfer_not_all_lines'])."');
        return false;
    }

	if (type_action == 'transfer') {
		openPopUp('select.php?what=commande&action=transfer_lines&callback=act_transfer_checked_lines&id_bibli=!!id_bibli!!&id_exercice=!!id_exer!!&id_cde=!!id_cde!!', 'selector_commande');
        return true;
	}

    if (type_action == 'duplicate') {
		openPopUp('select.php?what=commande&action=duplicate_lines&callback=act_duplicate_checked_lines&id_bibli=!!id_bibli!!&id_exercice=!!id_exer!!&id_cde=!!id_cde!!', 'selector_commande');
        return true;
	}

}

function act_transfer_checked_lines(id_acte, id_bibli, id_exercice) {

    var ids_line = new Array();

	for (var i=1; i<=act_curline; i++) {
		c = document.getElementById('chk['+i+']');
		if(c) {
			if(c.checked) {
				ids_line.push(document.getElementById('id_lig['+i+']').value);
			}
		}
	}
	if (ids_line.length) {
		alert('".addslashes($msg['acquisition_action_confirm_toggle_acte'])."');
		var req = new http_request();
		req.request('./ajax.php?module=acquisition&categ=ach&sub=cmde&action=transfer_lines&ids_line=' + ids_line.join(',') + '&id_cde=' + id_acte, false, '', false);
		window.location = './acquisition.php?categ=ach&sub=cmde&action=modif&id_bibli='+id_bibli+'&id_exercice='+id_exercice+'&id_cde=' + id_acte;
	}
}

function act_duplicate_checked_lines (id_acte, id_bibli, id_exercice) {

	var ids_line = new Array();

	for (var i=1; i<=act_curline; i++) {
		c = document.getElementById('chk['+i+']');
		if(c) {
			if(c.checked) {
				ids_line.push(document.getElementById('id_lig['+i+']').value);
			}
		}
	}
	if (ids_line.length) {
		alert('".addslashes($msg['acquisition_action_confirm_toggle_acte'])."');
		var req = new http_request();
		req.request('./ajax.php?module=acquisition&categ=ach&sub=cmde&action=duplicate_lines&ids_line=' + ids_line.join(',') + '&id_cde=' + id_acte, false, '', false);
		window.location = './acquisition.php?categ=ach&sub=cmde&action=modif&id_bibli='+id_bibli+'&id_exercice='+id_exercice+'&id_cde=' + id_acte;
	}
}

</script>
";

$modif_cde_form = $cde_help_jscript.$modif_cde_duplicate_transfer_jscript."
<form class='form-".$current_module."' id='act_modif' name='act_modif' method='post' action=\"\">
	<h3>!!form_title!!</h3>
	<div class='row'></div>
	<!--    Contenu du form    -->
	<div class='form-contenu'>

		<div class='row'>
			<div class='colonne5'>
				<label class='etiquette'>".htmlentities($msg['acquisition_coord_lib'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='colonne_suite'>
				!!lib_bibli!!
				<input type='hidden' id='id_bibli' name='id_bibli' value='!!id_bibli!!' />
			</div>
		</div>

		<div class='row'>
			<div class='colonne5'>
				<label class='etiquette'>".htmlentities($msg['acquisition_budg_exer'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='colonne_suite'>
				!!lib_exer!!
				<input type='hidden' id='id_exer' name='id_exer' value='!!id_exer!!' />
			</div>
		</div>

		<div class='row'></div>
		<hr />

		<div class='row'>
			<div class='colonne60'>
				<div class='colonne3' >
					<label class='etiquette'>".htmlentities($msg['653'], ENT_QUOTES, $charset)."</label>
					&nbsp;!!date_cre!!
				</div>
				<div class='colonne3'>
					<label class='etiquette'>".htmlentities($msg['38'], ENT_QUOTES, $charset)."</label>
					&nbsp;
					<input type='text' id='num_cde' name='num_cde' value='!!numero!!' class='saisie-10em' />
				</div>
				<div class='colonne3'>
					<label class='etiquette'>".htmlentities($msg['acquisition_statut'], ENT_QUOTES, $charset)."</label>
					&nbsp;<!-- sel_statut -->
				</div>
			</div>
			<div class='colonne40'>
				<div class='colonne5'>
		    		<label class='etiquette'>".htmlentities($msg['acquisition_ach_fou2'], ENT_QUOTES, $charset)."</label>&nbsp;
				</div>
				<div class='colonne_suite'>
					<input type='text' id='lib_fou' name='lib_fou' tabindex='1' value='!!lib_fou!!' completion='fournisseurs' param1='!!id_bibli!!' autfield='id_fou' autocomplete='off' callback='callBackAdresseFournisseur' class='saisie-30emr' />
					<input type='button' class='bouton_small' style='width:20px;' tabindex='1' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=fournisseur&caller=act_modif&param1=id_fou&param2=lib_fou&param3=adr_fou&id_bibli=!!id_bibli!!&deb_rech='+".pmb_escape()."(this.form.lib_fou.value), 'selector'); \" />
					<input type='hidden' id='id_fou' name='id_fou' value='!!id_fou!!' />
				</div>
			</div>
		</div>

		<div class='row'>
			<div class='colonne60'>
				<label class='etiquette'>".htmlentities($msg['acquisition_cde_nom'], ENT_QUOTES, $charset)."</label>
				&nbsp;<input type='text' id='nom_acte' name='nom_acte' value='!!nom_acte!!' class='saisie-50em' />
			</div>
			<div class='colonne40'>
				<img id='adr_fou_Img' name='adr_fou_Img' src='".get_url_icon('plus.gif')."' class='img_plus' onclick=\"javascript:expandBase('adr_fou_', true);\"/>
		    	<label class='etiquette'>".htmlentities($msg['acquisition_adr_fou'], ENT_QUOTES, $charset)."</label>
			</div>
		</div>

		<div class='row' id='adr_fou_Child' style='display:none;'>
			<div class='colonne60'>&nbsp;</div>
			<div class='colonne_suite'  style='margin-left:30px'>
				<textarea id='adr_fou' name='adr_fou' class='saisie-30emd' readonly='readonly' cols='50' rows='4' wrap='virtual'>!!adr_fou!!</textarea>
				<input type='hidden' id='id_adr_fou' name='id_adr_fou' value='!!id_adr_fou!!' />
			</div>
		</div>

		<div class='row'></div>
		<hr />

		<div class='row'>
			<div class='colonne2'>
				<img id='adr_bib_Img' src='".get_url_icon('plus.gif')."' class='img_plus' onclick=\"javascript:expandBase('adr_bib_', true);\" />
	    		<label class='etiquette'>".htmlentities($msg['acquisition_adr_liv'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='colonne2'>
				<label class='etiquette'>".htmlentities($msg['acquisition_adr_fac'], ENT_QUOTES, $charset)."</label>
			</div>
		</div>

		<div class='row' id='adr_bib_Child' name='adr_bib_Child' style='display:none;'>
			<div class='colonne2'>
				<div class='colonne' style='margin-left:30px'>
					<textarea id='adr_liv' name='adr_liv' class='saisie-30emr' readonly='readonly' cols='50' rows='4' wrap='virtual'>!!adr_liv!!</textarea>&nbsp;
					<input type='hidden' id='id_adr_liv' name='id_adr_liv' value='!!id_adr_liv!!' />
				</div>
				<div class='colonne_suite'>
					<input type='button' tabindex='1' class='bouton_small' style='width:20px;' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=coord&caller=act_modif&param1=id_adr_liv&param2=adr_liv&id_bibli=!!id_bibli!!', 'selector'); \" />
					<input type='button' tabindex='1' class='bouton_small' style='width:20px;' value='".$msg['raz']."' onclick=\"document.getElementById('id_adr_liv').value='0';document.getElementById('adr_liv').value='';\" />
				</div>
			</div>
			<div class='colonne2'>
				<div class='colonne' style='margin-left:30px'>
					<textarea id='adr_fac' name='adr_fac' class='saisie-30emr' readonly='readonly' cols='50' rows='4' wrap='virtual'>!!adr_fac!!</textarea>&nbsp;
					<input type='hidden' id='id_adr_fac' name='id_adr_fac' value='!!id_adr_fac!!' />
				</div>
				<div class='colonne_suite'>
					<input type='button' tabindex='1' class='bouton_small' style='width:20px;' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=coord&caller=act_modif&param1=id_adr_fac&param2=adr_fac&id_bibli=!!id_bibli!!', 'selector'); \" />
					<input type='button' tabindex='1' class='bouton_small' style='width:20px;' value='".$msg['raz']."' onclick=\"document.getElementById('id_adr_fac').value='0';document.getElementById('adr_fac').value='';\" />
				</div>
			</div>
		</div>

		<div class='row'>
			<img id='comment_Img' src='".get_url_icon('plus.gif')."' class='img_plus' onclick=\"javascript:expandBase('comment_', true);\"/>
    		<label class='etiquette'>".htmlentities($msg['acquisition_commentaires'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='row' style='margin-left:30px'>
			<textarea  id='comment_Child' name='comment' tabindex='1' class='saisie-80em' style='display:none;' cols='62' rows='4' wrap='virtual'>!!comment!!</textarea>
		</div>

		<div class='row'>
			<img id='comment_i_Img' src='".get_url_icon('plus.gif')."' class='img_plus' onclick=\"javascript:expandBase('comment_i_', true);\"/>
    		<label class='etiquette'>".htmlentities($msg['acquisition_commentaires_i'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='row' style='margin-left:30px'>
			<textarea  id='comment_i_Child' name='comment_i' tabindex='1' class='saisie-80em' style='display:none;' cols='62' rows='4' wrap='virtual'>!!comment_i!!</textarea>
		</div>

		<div class='row'></div>
		<hr />

		<div class='row'>
			<div class='colonne3'>
				<table style='background-color:transparent;top:0px;'>
					<tr>
						<td style='width:50%' >
							<label class='etiquette'>".htmlentities($msg['acquisition_act_num_dev'], ENT_QUOTES, $charset)."</label>
						</td>
						<td >
							<span class='current'>!!lien_dev!!</span>
						</td>
					</tr>
				</table>
			</div>
			<div class='colonne3'>
				<table style='background-color:transparent;'>
					<tr>
						<td style='vertical-align:top'>
							<label class='etiquette'>".htmlentities($msg['acquisition_dev_ref_fou'], ENT_QUOTES, $charset)."</label>
						</td>
						<td style='vertical-align:top'>
							<input type='hidden' id='id_dev' name='id_dev' value='!!id_dev!!' />
							<input type='text' id='ref' name='ref' tabindex='1' class='saisie-1Oem' value='!!ref!!' />
						</td>
					</tr>
					<tr>
						<td style='vertical-align:top'>
							<label class='etiquette'>".htmlentities($msg['acquisition_fac_date_pay'], ENT_QUOTES, $charset)."</label>
						</td>
						<td style='vertical-align:top'><!-- sel_date_pay --></td>
					</tr>
					<tr>
						<td style='vertical-align:top'>
							<label class='etiquette'>".htmlentities($msg['acquisition_fac_num_pay'], ENT_QUOTES, $charset)."</label>
						</td>
						<td style='vertical-align:top'>
							<input type='text' id='num_pay' name='num_pay' tabindex='1' class='saisie-10em' value='!!num_pay!!' />
						</td>
					</tr>
				</table>
			</div>
			<div class='colonne3'>
				<table style='background-color:transparent;'>
					<tr>
						<td style='vertical-align:top; width:50%'>
							<label class='etiquette'>".htmlentities($msg['acquisition_cde_date_liv'], ENT_QUOTES, $charset)."</label>
						</td>
						<td style='vertical-align:top'>
							<!-- sel_date_liv -->
						</td>
					</tr>
				</table>
			</div>

			<input type='hidden' id='act_type' name='act_type' value='".TYP_ACT_CDE."' />
			<input type='hidden' id='id_cde' name='id_cde' value='!!id_cde!!' />
			<input type='hidden' id='gestion_tva' name='gestion_tva' value='".$acquisition_gestion_tva."' />

		</div>

		<div class='row'>
			<table class='act_cell' >
				<tbody id='act_tab' >
					<tr>
						<th style='width:3%' >
							<i class='fa fa-plus-square' onclick='expandAllCommentsRows();' style='cursor:pointer;'></i>
							&nbsp;
							<i class='fa fa-minus-square' onclick='collapseAllCommentsRows();' style='cursor:pointer;'></i>
						</th>
						<th style='width:10%'>".htmlentities($msg['acquisition_act_tab_code'], ENT_QUOTES, $charset)."</th>
						<th style='width:27%'>
								".htmlentities($msg['acquisition_act_tab_lib'], ENT_QUOTES, $charset)."
								<img src='".get_url_icon('aide.gif')."' onclick=\"showHelp(this);return(false);\" whatis='cde_saisie' helpdir='".$lang."' style='cursor: pointer' />
						</th>
						<th style='width:4%'>".htmlentities($msg['acquisition_act_tab_qte'], ENT_QUOTES, $charset)."</th>";
switch ($acquisition_gestion_tva) {
	case '1' :
		$modif_cde_form.= "
						<th style='width:6%'>".htmlentities($msg['acquisition_act_tab_priht'], ENT_QUOTES, $charset)." ".$msg['acquisition_act_tab_pri_exposant']."<br />".htmlentities($msg['acquisition_act_tab_prittc'], ENT_QUOTES, $charset)." ".$msg['acquisition_act_tab_pri_exposant']."</th>
						<th style='width:20%'>".htmlentities($msg['acquisition_act_tab_typ'], ENT_QUOTES, $charset)."<br />".htmlentities($msg['acquisition_tva'], ENT_QUOTES, $charset)." / ".htmlentities($msg['acquisition_remise'], ENT_QUOTES, $charset)."</th>";
		break;
	case '2' :
		$modif_cde_form.= "
						<th style='width:6%'>".htmlentities($msg['acquisition_act_tab_prittc'], ENT_QUOTES, $charset)." ".$msg['acquisition_act_tab_pri_exposant']."<br />".htmlentities($msg['acquisition_act_tab_priht'], ENT_QUOTES, $charset)." ".$msg['acquisition_act_tab_pri_exposant']."</th>
						<th style='width:20%'>".htmlentities($msg['acquisition_act_tab_typ'], ENT_QUOTES, $charset)."<br />".htmlentities($msg['acquisition_tva'], ENT_QUOTES, $charset)." / ".htmlentities($msg['acquisition_remise'], ENT_QUOTES, $charset)."</th>";
		break;
	default :
		$modif_cde_form.= "
						<th style='width:6%'>".htmlentities($msg['acquisition_act_tab_prittc'], ENT_QUOTES, $charset)." ".$msg['acquisition_act_tab_pri_exposant']."</th>
						<th style='width:20%'>".htmlentities($msg['acquisition_act_tab_typ'], ENT_QUOTES, $charset)."<br />".htmlentities($msg['acquisition_remise'], ENT_QUOTES, $charset)."</th>";
		break;
}
$modif_cde_form.="		<th style='width:12%'>".htmlentities($msg['acquisition_act_tab_bud'], ENT_QUOTES, $charset)."</th>
						<th style='width:12%'>".htmlentities($msg['acquisition_lgstat'], ENT_QUOTES, $charset)."</th>
						<th style='width:0px' ></th>
					</tr>
					<!-- lignes -->
				</tbody>
			</table>
		</div>

		<div class='row'>
			<div class='left' >
				<input type='button' id='bt_add_line' tabindex='1' class='bouton_small' value='".$msg['acquisition_act_add_lig']."' onclick=\"act_addLine();\" />
			</div>
		</div>
		<div class='row'>
			<div class='right'>
				<label class='etiquette'>".htmlentities($msg['acquisition_action_check_line'], ENT_QUOTES, $charset)."</label>
				<input type='button' tabindex='1' class='bouton_small' value='".$msg['acquisition_copy_chk_lig']."' onclick=\"act_copyLines();\" />
				<input type='button' tabindex='1' class='bouton_small' value='".$msg['acquisition_del_chk_lig']."' onclick=\"act_delLines();\" />
				<input type='button' tabindex='1' class='bouton_small' value='".$msg['acquisition_action_check_line_increase_rate']."' onclick=\"act_increaseRate();\" />
                <input type='text' id='increase_rate_percent' class='saisie-2em' value='".htmlentities($acquisition_increase_rate_percent, ENT_QUOTES, $charset)."' />".$msg['acquisition_action_check_line_increase_rate_percent']."
				<!-- sel_type_for_checked -->
				<!-- sel_budget_for_checked -->
				<input type='button' class='bouton_small' style='width:20px;' tabindex='1' value='+' onclick='act_switchCheck();' />
			</div>
		</div>
		<div class='row'>
			<div class='right'>
				<input type='button' tabindex='1' class='bouton_small' value='".$msg['acquisition_action_check_line_duplicate']."' onclick=\"act_line_action('duplicate');\" />
				<input type='button' tabindex='1' class='bouton_small' value='".$msg['acquisition_action_check_line_transfer']."' onclick=\"act_line_action('transfer');\" />
			</div>
		</div>
		<div class='row'></div>
		<hr />

		<div class='row'>
			<div class='left'>
				<input type='button' tabindex='1' class='bouton_small' value='".$msg['acquisition_calc']."' onclick=\"act_calc();\" />";
if ($acquisition_gestion_tva) $modif_cde_form.= "
				<label class='etiquette'>".htmlentities($msg['acquisition_total_ht'], ENT_QUOTES, $charset)."</label>
				<input type='text' id='tot_ht' name='tot_ht' class='saisie-10emd' style='text-align:right;' readonly='readonly' value='0.00' />
				<label class='etiquette'>".htmlentities($msg['acquisition_tva'], ENT_QUOTES, $charset)."</label>
				<input type='text' id='tot_tva' name='tot_tva' class='saisie-10emd' style='text-align:right;' readonly='readonly' value='0.00' />";

$modif_cde_form.= "
				<label class='etiquette'>".htmlentities($msg['acquisition_total_ttc'], ENT_QUOTES, $charset)."</label>
				<input type='text' id='tot_ttc' name='tot_ttc' class='saisie-10emd' style='text-align:right;' readonly='readonly' value='0.00' />
				<label class='etiquette'>".htmlentities($msg['acquisition_devise'], ENT_QUOTES, $charset)."</label>
				<input type='text' id='devise' name='devise' class='saisie-5em' style='text-align:right;' value='!!devise!!' />
			</div>
			<div class='right'>
				<label class='etiquette'>".htmlentities($msg['acquisition_tot_expl'], ENT_QUOTES, $charset)."</label>
				<input type='text' id='tot_expl' name='tot_expl' class='saisie-5emd' style='text-align:right;' readonly='readonly' value='0' />
			</div>
		</div>

		<div class='row'></div>

	</div>

	<div class='row'>
			<label class='etiquette'>".$msg['acquisition_act_tab_pri_exposant_label']."</label>
			<br /><label class='etiquette'>".$msg['acquisition_lig_frais_annexes']."</label>
	</div>
	<div class='row'>
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['76']."' onclick=\"document.location='./acquisition.php?categ=ach&sub=cmde&action=list&id_bibli=!!id_bibli!!&id_exercice=".$id_exercice."' \" />
			<!-- bouton_enr -->
			<!-- bouton_val -->
			<!-- bouton_dup -->
			<!-- bouton_imp -->
			<!-- bouton_audit -->
		</div>
		<div class='right'>
			<!-- bouton_sup -->
		</div>
	</div>

	<div class='row'></div>

</form>
<br /><br />
<script type='text/javascript' src='./javascript/tablist.js'></script>
<script type='text/javascript' src='./javascript/ajax.js'></script>
<script type='text/javascript'>
	var acquisition_gestion_tva=".$acquisition_gestion_tva.";
</script>
<script type='text/javascript' src='./javascript/actes.js'></script>
<script type='text/javascript'>

	document.getElementById('statut').value='!!statut!!';

	var msg_parcourir='".addslashes($msg['parcourir'])."';
	var msg_raz='".addslashes($msg['raz'])."';
	var msg_no_fou = '".addslashes($msg['acquisition_cde_fou_err'])."';
	var msg_act_vide='".addslashes($msg['acquisition_cde_vid'])."';
	var acquisition_budget = '".$acquisition_budget."';
	var acquisition_type_produit = '".$acquisition_type_produit."';
	var msg_no_bud = '".addslashes($msg['acquisition_act_bud_err'])."';
	var msg_no_typ = '".addslashes($msg['acquisition_act_typ_err'])."';
	var msg_acquisition_comment_lg='".addslashes($msg['acquisition_comment_lg'])."';
	var msg_acquisition_comment_lo='".addslashes($msg['acquisition_comment_lo'])."';
	var msg_acquisition_applicants='".addslashes($msg['acquisition_applicants'])."';
	var lgstat_sel=\"".lgstat::getHtmlSelect(array(0=>$deflt3lgstatcde), FALSE, array('id'=>'lg_statut[!!lig!!]', 'name'=>'lg_statut[!!lig!!]'))."\";

	var act_nblines='!!act_nblines!!';
	var act_curline='!!act_nblines!!';
	if(act_nblines>0) {
		act_calc();
	} else {
		act_addLine(!!id_cde!!);
	}
	ajax_parse_dom();
</script>
<!-- jscript -->";

$modif_cde_sel_typ_for_checked = "
		<input type='button' tabindex='1' class='bouton_small' value='".$msg['acquisition_act_apply_type_to_checked']."' onclick=\"act_applyTypeToChecked();\" />
		<input type='hidden' id='typ_for_checked' value='0' />
		<input type='hidden' id='rem_for_checked' value='0.00' />
		<input type='hidden' id='tva_for_checked' value='0.00' />
		<input type='text' id='lib_typ_for_checked' tabindex='1' completion='types_produits' linkfield='id_fou' autfield='typ_for_checked' autocomplete='off'  callback='callbackUpdateType' class='in_cell_ro' value='' />
		<input type='button' tabindex='1' id='sel_typ_for_checked' class='bouton_small' style='width:20px' value='".$msg['parcourir']."' onclick=\"act_getType(this);\" />
		<input type='button' tabindex='1' id='del_typ_for_checked' class='bouton_small' style='width:20px;' value='".$msg['raz']."' onclick=\"act_delType(this);\" />";


$modif_cde_sel_rub_for_checked = "
		<input type='button' tabindex='1' class='bouton_small' value='".$msg['acquisition_act_apply_budget_to_checked']."' onclick=\"act_applyBudgetToChecked();\" />
		<input type='hidden' id='rub_for_checked' value='0' />
		<input type='text' id='lib_rub_for_checked' tabindex='1' completion='rubriques' param1='!!id_bibli!!' param2='!!id_exer!!' autfield='rub_for_checked' autocomplete='off' class='in_cell_ro' value='' />
		<input type='button' id='sel_rub_for_checked' tabindex='1' class='bouton_small' style='width:20px;' value='".$msg['parcourir']."' onclick=\"act_getRubrique(this);\" />
		<input type='button' id='del_rub_for_checked' tabindex='1' class='bouton_small' style='width:20px;' value='".$msg['raz']."' onclick=\"act_delRubrique(this);\" />";


//	------------------------------------------------------------------------------
//	template de création/modification pour les lignes de commande
//	------------------------------------------------------------------------------
$modif_cde_row_form = "
<tr id='R_!!no!!' class='act_typ_lig_!!typ_lig!!'>
	<td style='overflow:visible; width:0px'>
		<img onclick=\"javascript:expandRow('C_!!no!!_', true);\"  src='".get_url_icon('plus.gif')."' name='C_!!no!!_Img' id='C_!!no!!_Img' class='act_cell_img_plus' />
	</td>
	<td>
		<input type='text' id='code[!!no!!]' name='code[!!no!!]' tabindex='1' class='in_cell' value='!!code!!' />
		<input type='button' tabindex='1' class='bouton_small' style='width:20px;' value='".$msg['parcourir']."' onclick=\"act_getCode(this);\" />
		<input type='button' tabindex='1' class='bouton_small' style='width:20px;' value='".$msg['raz']."' onclick=\"act_delCode(this);\" />
	</td>
	<td>
		<textarea id='lib[!!no!!]' name='lib[!!no!!]' tabindex='1' class='in_cell' rows='3' wrap='virtual'>!!lib!!</textarea>
	</td>
	<td>
		<input type='text' id='qte[!!no!!]' name='qte[!!no!!]' tabindex='1' class='in_cell_nb' value='!!qte!!' onchange='thresholds_notification();'/>
	</td>
	<td>
		<input type='text' id='prix[!!no!!]' name='prix[!!no!!]' tabindex='1' class='in_cell_nb' value='!!prix!!' !!convert_prix!!/>
		!!convert_ht_ttc!!
	</td>
	<td>
		<input type='hidden' id='typ[!!no!!]' name='typ[!!no!!]' value='!!typ!!' />
		<input type='text' id='lib_typ[!!no!!]' name='lib_typ[!!no!!]' tabindex='1' completion='types_produits' linkfield='id_fou' autfield='typ[!!no!!]' autocomplete='off'  callback='callBackTypeProduit' class='in_cell_ro' value='!!lib_typ!!' /><input type='button' tabindex='1' class='bouton_small' style='width:20px' value='".$msg['parcourir']."' onclick=\"act_getType(this);\" /><input type='button' tabindex='1' class='bouton_small' style='width:20px;' value='".$msg['raz']."' onclick=\"act_delType(this);\" />";
if ($acquisition_gestion_tva) {
	$modif_cde_row_form.= "&nbsp;<input type='text' id='tva[!!no!!]' name='tva[!!no!!]' tabindex='1' class='in_cell_nb' style='width:20%;' value='!!tva!!' !!onchange_tva!! />&nbsp;%";
}
$modif_cde_row_form.= "&nbsp;<input type='text' id='rem[!!no!!]' name='rem[!!no!!]' tabindex='1' class='in_cell_nb' style='width:20%;' value='!!rem!!' onchange='thresholds_notification();' />&nbsp;%
	</td>
	<td>
		<input type='hidden' id='rub[!!no!!]' name='rub[!!no!!]' value='!!rub!!' />
		<input type='text' id='lib_rub[!!no!!]' name='lib_rub[!!no!!]' tabindex='1' completion='rubriques' param1='!!id_bibli!!' param2='!!id_exer!!' autfield='rub[!!no!!]' autocomplete='off' class='in_cell_ro' value='!!lib_rub!!' /><input type='button' tabindex='1' class='bouton_small' style='width:20px;' value='".$msg['parcourir']."' onclick=\"act_getRubrique(this);\" /><input type='button' tabindex='1' class='bouton_small' style='width:20px;' value='".$msg['raz']."' onclick=\"act_delRubrique(this);\" />
		!!force_ht_ttc!!
	</td>
	<td>
		!!lgstat!!
	</td>
	<td style='overflow:visible; width:0px' >
		<input type='checkbox' id='chk[!!no!!]' name='chk[!!no!!]' tabindex='1' value='1' class='act_cell_chkbox2' />
		<input type='hidden' id='id_sug[!!no!!]' name='id_sug[!!no!!]' value='!!id_sug!!' />
		<input type='hidden' id='id_lig[!!no!!]' name='id_lig[!!no!!]' value='!!id_lig!!' />
		<input type='hidden' id='typ_lig[!!no!!]' name='typ_lig[!!no!!]' value='!!typ_lig!!' />
		<input type='hidden' id='id_prod[!!no!!]' name='id_prod[!!no!!]' value='!!id_prod!!' />
	</td>
</tr>
<tr id='C_!!no!!_Child' class='act_cell_comments' style='display:none;'>
	<td colspan='9'>
		<table>
			<tr>
				<td style='width:10%' >".htmlentities($msg['acquisition_comment_lg'],ENT_QUOTES,$charset)."</td>
				<td style='width:40%'>
					<textarea id='comment_lg[!!no!!]' name='comment_lg[!!no!!]' tabindex='1' class='in_cell' rows='1' wrap='virtual'>!!comment_lg!!</textarea>
				</td>
				<td style='width:10%'>".htmlentities($msg['acquisition_comment_lo'],ENT_QUOTES,$charset)."</td>
				<td style='width:40%'>
					<textarea id='comment_lo[!!no!!]' name='comment_lo[!!no!!]' tabindex='1' class='in_cell' rows='1' wrap='virtual'>!!comment_lo!!</textarea>
				</td>

			<tr />
			!!applicants_tr!!
		</table>
	</td>
</tr>
";

$first_applicant_line = "<div class='row'>

								<input id='C_!!no!!_empr_label_0' class='saisie-50emr' type='text' autocomplete='off' autfield='C_!!no!!_applicants_0' completion='empr' value='!!applicant_label!!' name='C_!!no!!_empr_label_0'>
								<input id='C_!!no!!_applicants_0' type='hidden' value='!!applicant_id!!' name='applicants[!!no!!][]'>

							<input class='bouton' type='button' value='...' title='".htmlentities($msg['grp_liste'],ENT_QUOTES,$charset)."' onclick='openPopUp(\"./select.php?what=emprunteur&caller=act_modif&param1=C_!!no!!_applicants_0&param2=C_!!no!!_empr_label_0\", \"selector\")'>
							<input class='bouton' type='button' onclick='this.form.C_!!no!!_empr_label_0.value=\"\"; this.form.C_!!no!!_applicants_0.value=\"0\"; ' value='X'>
							<input class='bouton' type='button' onclick='add_applicant_line(\"C_!!no!!_applicants_container\")' value='+'>
						</div>";


$others_applicants_line = "<div class='row'>

								<input id='C_!!no!!_empr_label_!!nb!!' class='saisie-50emr' type='text' autocomplete='off' autfield='C_!!no!!_applicants_!!nb!!' completion='empr' size='33' value='!!applicant_label!!' name='C_!!no!!_empr_label_!!nb!!'>
								<input id='C_!!no!!_applicants_!!nb!!' type='hidden' value='!!applicant_id!!' name='applicants[!!no!!][]'>

							<input class='bouton' type='button' value='...' title='".htmlentities($msg['grp_liste'],ENT_QUOTES,$charset)."' onclick='openPopUp(\"./select.php?what=emprunteur&caller=act_modif\", \"selector\")'>
							<input class='bouton' type='button' onclick='this.form.C_!!no!!_empr_label_!!nb!!.value=\"\"; this.form.C_!!no!!_applicants_!!nb!!.value=\"0\"; ' value='X'>
						</div>";

//Date paiement modifiable
$sel_date_pay_mod = "<input type='date' id='date_pay' name='date_pay' value='!!date_pay!!' />";

//Date livraison modifiable
$sel_date_liv_mod = "<input type='date' id='date_liv' name='date_liv' value='!!date_liv!!' />";
//Date livraison non modifiable
$sel_date_liv_fix = "<input type='hidden' id='date_liv' name='date_liv' value='!!date_liv!!' />!!date_liv_lib!!";


$bt_enr = "<input type='button' class='bouton' value='".$msg['77']."'
			onclick=\"
				r=act_verif();
				if (!r) return false;
				act_calc();
				document.forms['act_modif'].setAttribute('action', 'acquisition.php?categ=ach&sub=cmde&action=update');
				document.forms['act_modif'].submit();  \" />";

$bt_val = "<input type='button' class='bouton' value='".$msg['acquisition_act_bt_val']."'
			onclick=\"
				r=act_verif();
				if (!r) return false;
				act_calc();
				r=confirm('".addslashes($msg['acquisition_cde_val'])."');
				if (!r) return false;
				document.forms['act_modif'].setAttribute('action', 'acquisition.php?categ=ach&sub=cmde&action=valid');
				document.forms['act_modif'].submit(); \" />";

$bt_dup = "<input type='button' class='bouton' value='".$msg['acquisition_dup']."'
			onclick=\"document.forms['act_modif'].setAttribute('action', 'acquisition.php?categ=ach&sub=cmde&action=duplicate');
				document.forms['act_modif'].submit(); \" />";

$bt_sup = "<input type='button' class='bouton' value='".$msg['63']."'
			onclick=\"if (document.getElementById('id_cde').value == 0) {return false; }
				r = confirm('".addslashes($msg['acquisition_cde_sup'])."');
				if(r){
					document.forms['act_modif'].setAttribute('action', './acquisition.php?categ=ach&sub=cmde&action=delete');
					document.forms['act_modif'].submit();} \" />";

$bt_arc = "<input type='button' class='bouton' value='".addslashes($msg['acquisition_act_bt_arc'])."'
			onclick=\"document.forms['act_modif'].setAttribute('action', './acquisition.php?categ=ach&sub=cmde&action=arc');
				document.forms['act_modif'].submit(); \" />";

$bt_imp = "<input type='button' class='bouton' value='".$msg['imprimer']."' title='".$msg['imprimer']."' onclick=\"openPopUp('./pdf.php?pdfdoc=cmde&id_bibli=".$id_bibli."&id_cde=".$id_cde."' ,'print_PDF')\" />";


//	------------------------------------------------------------------------------
//	$valid_cde_form : template de visualisation pour les commandes validées non modifiables
//	------------------------------------------------------------------------------
$valid_cde_form = $modif_cde_duplicate_transfer_jscript."
<form class='form-".$current_module."' id='act_modif' name='act_modif' method='post' action=\"\">
	<h3>!!form_title!!</h3>
	<div class='row'></div>
	<!--    Contenu du form    -->
	<div class='form-contenu'>

		<div class='row'>
			<div class='colonne5'>
				<label class='etiquette'>".htmlentities($msg['acquisition_coord_lib'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='colonne_suite'>
				!!lib_bibli!!
				<input type='hidden' id='id_bibli' name='id_bibli' value='!!id_bibli!!' />
			</div>
		</div>

		<div class='row'>
			<div class='colonne5'>
				<label class='etiquette'>".htmlentities($msg['acquisition_budg_exer'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='colonne_suite'>
				!!lib_exer!!
				<input type='hidden' id='id_exer' name='id_exer' value='!!id_exer!!' />
			</div>
		</div>

		<div class='row'></div>
		<hr />

		<div class='row'>
			<div class='colonne60'>
				<div class='colonne3' >
					<label class='etiquette'>".htmlentities($msg['653'], ENT_QUOTES, $charset)."</label>
					&nbsp;
					!!date_cre!!
				</div>
				<div class='colonne3'>
					<label class='etiquette'>".htmlentities($msg['38'], ENT_QUOTES, $charset)."</label>
					&nbsp;
					!!numero!!
				</div>
				<div class='colonne3'>
					<label class='etiquette'>".$msg['acquisition_statut']."</label>
					&nbsp;
					<!-- sel_statut -->
				</div>
			</div>
			<div class='colonne40'>
				<div class='colonne3'>
		    		<label class='etiquette' >".htmlentities($msg['acquisition_ach_fou2'], ENT_QUOTES, $charset)."</label>&nbsp;
				</div>
				<div class='colonne_suite'>
					!!lib_fou!!
					<input type='hidden' id='id_fou' name='id_fou' value='!!id_fou!!' />
				</div>
			</div>
		</div>

		<div class='row'>
			<div class='colonne60'>
		    	<div class='colonne3'>
					<label class='etiquette'>".htmlentities($msg['acquisition_cde_nom'], ENT_QUOTES, $charset)."</label>
					&nbsp;
					!!nom_acte!!
				</div>
				<div class='colonne3'>
					<label class='etiquette'>!!date_valid_label!!</label>
					&nbsp;
					!!date_valid!!
				</div>
			</div>
			<div class='colonne40'>
				<img id='adr_fou_Img' name='adr_fou_Img' src='".get_url_icon('plus.gif')."' class='img_plus'  onclick=\"javascript:expandBase('adr_fou_', true);\"/>
		    	<label class='etiquette' >".htmlentities($msg['acquisition_adr_fou'], ENT_QUOTES, $charset)."</label>
			</div>
		</div>

		<div class='row' id='adr_fou_Child' style='display:none;'>
			<div class='colonne60'>&nbsp;</div>
			<div class='colonne_suite' style='margin-left:30px'>
				<textarea id='adr_fou' name='adr_fou' class='saisie-30emd' readonly='readonly' cols='50' rows='4' wrap='virtual'>!!adr_fou!!</textarea>
				<input type='hidden' id='id_adr_fou' name='id_adr_fou' value='!!id_adr_fou!!' />
			</div>
		</div>

		<div class='row'></div>
		<hr />

		<div class='row'>
			<div class='colonne2'>
				<img id='adr_bib_Img' src='".get_url_icon('plus.gif')."' class='img_plus' onclick=\"javascript:expandBase('adr_bib_', true);\" />
	    		<label class='etiquette'>".htmlentities($msg['acquisition_adr_liv'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='colonne2'>
	    		<label class='etiquette'>".htmlentities($msg['acquisition_adr_fac'], ENT_QUOTES, $charset)."</label>
			</div>
		</div>

		<div class='row' id='adr_bib_Child' name='adr_bib_Child' style='display:none;'>
			<div class='colonne2'>
				<div class='colonne' style='margin-left:30px' >
					<textarea  id='adr_liv' name='adr_liv' class='saisie-30emd' readonly='readonly' cols='50' rows='4' wrap='virtual'>!!adr_liv!!</textarea>
					<input type='hidden' id='id_adr_liv' name='id_adr_liv' value='!!id_adr_liv!!' />
				</div>
				<div class='colonne_suite'>&nbsp;</div>
			</div>
			<div class='colonne2'>
				<div class='colonne' style='margin-left:30px' >
					<textarea id='adr_fac' name='adr_fac'  class='saisie-30emd' readonly='readonly' cols='50' rows='4' wrap='virtual'>!!adr_fac!!</textarea>
					<input type='hidden' id='id_adr_fac' name='id_adr_fac' value='!!id_adr_fac!!' />
				</div>
				<div class='colonne_suite'>&nbsp;</div>
			</div>
		</div>

		<div class='row'>
			<img id='comment_Img' src='".get_url_icon('plus.gif')."' class='img_plus' onclick=\"javascript:expandBase('comment_', true);\"/>
    		<label class='etiquette'>".htmlentities($msg['acquisition_commentaires'], ENT_QUOTES, $charset)."</label>
		</div>

		<div class='row' style='margin-left:30px'>
			<textarea  id='comment_Child' name='comment' class='saisie-80em' style='display:none;' cols='62' rows='4' wrap='virtual' >!!comment!!</textarea>
		</div>

		<div class='row'>
			<img id='comment_i_Img' src='".get_url_icon('plus.gif')."' class='img_plus' onclick=\"javascript:expandBase('comment_i_', true);\"/>
    		<label class='etiquette'>".htmlentities($msg['acquisition_commentaires_i'], ENT_QUOTES, $charset)."</label>&nbsp;
		</div>

		<div class='row' style='margin-left:30px'>
			<textarea  id='comment_i_Child' name='comment_i' class='saisie-80emd' readonly='readonly' style='display:none;' cols='62' rows='4' wrap='virtual'>!!comment_i!!</textarea>
		</div>

		<div class='row'></div>
		<hr />

		<div class='row'>
			<div class='colonne3'>
				<table style='background-color:transparent;' >
					<tr>
						<td style='vertical-align:top; width:50%'>
							<label class='etiquette'>".htmlentities($msg['acquisition_act_num_dev'], ENT_QUOTES, $charset)."</label>
						</td>
						<td style='vertical-align:top'>
							<span class='current'>!!lien_dev!!</span>
						</td>
					</tr>
					<tr>
						<td style='vertical-align:top'>
							<label class='etiquette'>".htmlentities($msg['acquisition_liv_liees'], ENT_QUOTES, $charset)."</label>
							<span class='current'>!!liens_liv!!</span>
						</td>
						<td style='vertical-align:top'>
							<label class='etiquette'>".htmlentities($msg['acquisition_fac_liees'], ENT_QUOTES, $charset)."</label>
							<span class='current'>!!liens_fac!!</span>
						</td>
					</tr>
				</table>
			</div>
			<div class='colonne3'>
				<table style='background-color:transparent;'>
					<tr>
						<td style='vertical-align:top; width:50%'>
							<label class='etiquette'>".htmlentities($msg['acquisition_dev_ref_fou'], ENT_QUOTES, $charset)."</label>
						</td>
						<td style='vertical-align:top'>
							<input type='hidden' id='id_dev' name='id_dev' value='!!id_dev!!' />
							<input id='ref' name='ref' class='saisie-1Oem' type='text' value='!!ref!!' />
						</td>
					</tr>
					<tr>
						<td style='vertical-align:top'>
							<label class='etiquette'>".htmlentities($msg['acquisition_fac_date_pay'], ENT_QUOTES, $charset)."</label>
						</td>
						<td style='vertical-align:top'><!-- sel_date_pay --></td>
					</tr>
					<tr>
						<td style='vertical-align:top'>
							<label class='etiquette'>".htmlentities($msg['acquisition_fac_num_pay'], ENT_QUOTES, $charset)."</label>
						</td>
						<td style='vertical-align:top'>
							<input id='num_pay' name='num_pay' type='text' class='saisie-10em' value='!!num_pay!!' />
						</td>
					</tr>
				</table>
			</div>
			<div class='colonne3'>
				<table style='background-color:transparent;'>
					<tr>
						<td style='vertical-align:top; width:50%'>
							<label class='etiquette'>".htmlentities($msg['acquisition_cde_date_liv'], ENT_QUOTES, $charset)."</label>
						</td>
						<td style='vertical-align:top'>
							<!-- sel_date_liv -->
						</td>
					</tr>
				</table>
			</div>

			<input type='hidden' id='act_type' name='act_type' value='".TYP_ACT_CDE."' />
			<input type='hidden' id='id_cde' name='id_cde' value='!!id_cde!!' />
			<input type='hidden' id='gestion_tva' name='gestion_tva' value='".$acquisition_gestion_tva."' />
		</div>

		<div class='row'>
			<table class='act_cell' >
				<tbody id='act_tab'>
					<tr>
						<th style='width:3%' >
							<i class='fa fa-plus-square' onclick='expandAllCommentsRows();' style='cursor:pointer;'></i>
							&nbsp;
							<i class='fa fa-minus-square' onclick='collapseAllCommentsRows();' style='cursor:pointer;'></i>
						</th>
						<th style='width:10%'>".htmlentities($msg['acquisition_act_tab_code'], ENT_QUOTES, $charset)."</th>
						<th style='width:27%'>".htmlentities($msg['acquisition_act_tab_lib'], ENT_QUOTES, $charset)."</th>
						<th style='width:4%'>".htmlentities($msg['acquisition_act_tab_qte'], ENT_QUOTES, $charset)."<br />".htmlentities($msg['acquisition_act_tab_rec_par'], ENT_QUOTES, $charset)."</th>";
switch ($acquisition_gestion_tva) {
	case '1' :
		$valid_cde_form.= "
						<th style='width:6%'>".htmlentities($msg['acquisition_act_tab_priht'], ENT_QUOTES, $charset)."</th>
						<th style='width:20%'>".htmlentities($msg['acquisition_act_tab_typ'], ENT_QUOTES, $charset)."<br />".htmlentities($msg['acquisition_tva'], ENT_QUOTES, $charset)." / ".htmlentities($msg['acquisition_remise'], ENT_QUOTES, $charset)."</th>";
		break;
	case '2' :
		$valid_cde_form.= "
						<th style='width:6%'>".htmlentities($msg['acquisition_act_tab_prittc'], ENT_QUOTES, $charset)."</th>
						<th style='width:20%'>".htmlentities($msg['acquisition_act_tab_typ'], ENT_QUOTES, $charset)."<br />".htmlentities($msg['acquisition_tva'], ENT_QUOTES, $charset)." / ".htmlentities($msg['acquisition_remise'], ENT_QUOTES, $charset)."</th>";
		break;
	default :
		$valid_cde_form.= "
						<th style='width:6%'>".htmlentities($msg['acquisition_act_tab_prittc'], ENT_QUOTES, $charset)."</th>
						<th style='width:20%'>".htmlentities($msg['acquisition_act_tab_typ'], ENT_QUOTES, $charset)."<br />".htmlentities($msg['acquisition_remise'], ENT_QUOTES, $charset)."</th>";
		break;
}
$valid_cde_form.= "		<th style='width:20%'>".htmlentities($msg['acquisition_act_tab_bud'], ENT_QUOTES, $charset)."</th>
						<th style='width:10%'>".htmlentities($msg['acquisition_lgstat'], ENT_QUOTES, $charset)."</th>
						<th style='width:0px' ></th>
					</tr>
					<!-- lignes -->
				</tbody>
			</table>
		</div>
		<div class='row'>
			<div class='left' >
			</div>
			<div class='right'>
				<label class='etiquette'>".htmlentities($msg['acquisition_action_check_line'], ENT_QUOTES, $charset)."</label>
				<input type='button' tabindex='1' class='bouton_small' value='".$msg['acquisition_action_check_line_duplicate']."' onclick=\"act_line_action('duplicate');\" />
				<input type='button' class='bouton_small' style='width:20px;' tabindex='1' value='+' onclick='act_switchCheck();' />
			</div>
		</div>
		<div class='row'></div>
		<hr />
		<div class='row'>
			<div class='left'>";
if($acquisition_gestion_tva) $valid_cde_form.= "
				<label class='etiquette'>".htmlentities($msg['acquisition_total_ht'], ENT_QUOTES, $charset)."</label>
				<input type='text' id='tot_ht' name='tot_ht' class='saisie-10emd' style='text-align:right;' readonly='readonly' value='0.00' />
				<label class='etiquette'>".htmlentities($msg['acquisition_tva'], ENT_QUOTES, $charset)."</label>
				<input type='text' id='tot_tva' name='tot_tva' class='saisie-10emd' style='text-align:right;' readonly='readonly' value='' />";
$valid_cde_form.= "
				<label class='etiquette'>".htmlentities($msg['acquisition_total_ttc'], ENT_QUOTES, $charset)."</label>
				<input type='text' id='tot_ttc' name='tot_ttc' class='saisie-10emd' style='text-align:right;' readonly='readonly' value='' />
				<label class='etiquette'>".htmlentities($msg['acquisition_devise'], ENT_QUOTES, $charset)."</label>
				<input type='text' id='devise' name='devise' class='saisie-5emd' style='text-align:right;' readonly='readonly' value='!!devise!!' />
			</div>
			<div class='right'>
				<label class='etiquette'>".htmlentities($msg['acquisition_tot_expl'], ENT_QUOTES, $charset)."</label>
				<input type='text' id='tot_expl' name='tot_expl' class='saisie-5emd' style='text-align:right;' readonly='readonly' value='0' />
			</div>
		</div>

		<div class='row'></div>

	</div>

	<div class='row'>
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['76']."' onclick=\"document.location='./acquisition.php?categ=ach&sub=cmde&action=list&id_bibli=!!id_bibli!!' \" />
			<!-- bouton_enr_valid -->
			<!-- bouton_dup -->
			<!-- bouton_rec -->
			<!-- bouton_fac -->
			<!-- bouton_imp -->
			<!-- bouton_audit -->
		</div>
		<div class='right'>
			<!-- bouton_sol -->
			<!-- bouton_arc -->
		</div>
	</div>

	<div class='row'></div>

</form>
<br /><br />
<script type='text/javascript' src='./javascript/tablist.js'></script>
<script type='text/javascript' src='./javascript/actes.js'></script>
<script type='text/javascript' src='./javascript/ajax.js'></script>
<script type='text/javascript'>

	document.getElementById('statut').value='!!statut!!';

	var msg_parcourir='".addslashes($msg['parcourir'])."';
	var msg_raz='".addslashes($msg['raz'])."';
	var act_nblines='!!act_nblines!!';
	var act_curline='!!act_nblines!!';
	act_calc();
	ajax_parse_dom();
</script>
<!-- jscript -->";


$bt_enr_valid = "<input type='button' class='bouton' value='".$msg['77']."'
				onclick=\"document.forms['act_modif'].setAttribute('action', './acquisition.php?categ=ach&sub=cmde&action=update');
					document.forms['act_modif'].submit(); \" />";

$bt_rec = "<input type='button' class='bouton' value='".$msg['acquisition_cde_bt_rec']."'
				onclick=\"document.forms['act_modif'].setAttribute('action', './acquisition.php?categ=ach&sub=recept&action=from_cde');
					document.forms['act_modif'].submit(); \" />";

$bt_fac ="<input type='button' class='bouton' value='".$msg['acquisition_cde_bt_fac']."'
				onclick=\"document.forms['act_modif'].setAttribute('action', './acquisition.php?categ=ach&sub=fact&action=from_cde');
					document.forms['act_modif'].submit(); \" />";

$bt_sol ="<input type='button' class='bouton' value='".$msg['acquisition_cde_bt_sol']."'
			onclick=\"	r = confirm('".addslashes($msg['acquisition_cde_sol'])."');
						if(r) {
							document.forms['act_modif'].setAttribute('action', './acquisition.php?categ=ach&sub=cmde&action=sold');
							document.forms['act_modif'].submit(); } \" />";

$bt_audit = "<input type='button' class='bouton' value='".$msg['audit_button']."' onClick=\"openPopUp('./audit.php?type_obj=4&object_id=".$id_cde."', 'audit_popup')\" title='".$msg['audit_button']."' />";


//	------------------------------------------------------------------------------
//	template de visualisation pour les lignes de commande non modifiables
//	------------------------------------------------------------------------------
$valid_cde_row_form = "
<tr id='R_!!no!!'>
	<td style='overflow:visible; width:0px'>
		<img onclick=\"javascript:expandRow('C_!!no!!_', true);\"  src='".get_url_icon('plus.gif')."' name='C_!!no!!_Img' id='C_!!no!!_Img' class='act_cell_img_plus' />
	</td>
	<td >
		<div class='in_cell_ld' title='!!code!!' >!!code!!</div>
	</td>
	<td>
		<div class='in_cell_ld' >!!lib!!</div>
	</td>
	<td>
		<input type='text' id='qte[!!no!!]' title='!!qte!!' class='saisie-10emd' style='width:100%;text-align:right;' readonly='readonly' value='!!qte!!' />
		<div class='in_cell_rd' >(!!rec!!)</div>
	</td>
	<td>
		<input type='text' id='prix[!!no!!]' title='!!prix!!' class='saisie-10emd' style='width:100%;text-align:right;' readonly='readonly' value='!!prix!!' />
	</td>
	<td>
		<div class='in_cell_ld' title='!!lib_typ!!' >!!lib_typ!!</div>
";
if ($acquisition_gestion_tva) {
	$valid_cde_row_form.= "
		&nbsp;<input type='text' id='tva[!!no!!]' title='!!tva!! %' class='saisie-10emd' style='width:20%;text-align:right;' readonly='readonly' value='!!tva!!'/>&nbsp;%";
	}
$valid_cde_row_form.= "
		&nbsp;<input type='text' id='rem[!!no!!]' title='!!rem!! %' class='saisie-10emd' style='width:20%;text-align:right;margin-left:10px;' readonly='readonly' value='!!rem!!'  />&nbsp;%
	</td>
	<td>
		<div class='in_cell_ld' >!!lib_rub!!</div>
	</td>
	<td>
		!!lgstat!!
	</td>
	<td style='overflow:visible; width:0px' >
		<input type='checkbox' id='chk[!!no!!]' name='chk[!!no!!]' tabindex='1' value='1' class='act_cell_chkbox2' />
		<input type='hidden' id='id_lig[!!no!!]' name='id_lig[!!no!!]' value='!!id_lig!!' />
	</td>
</tr>
<tr id='C_!!no!!_Child' class='act_cell_comments' style='display:none;'>
	<td colspan='9'>
		<table>
			<tr>
				<td style='width:10%' >".htmlentities($msg['acquisition_comment_lg'],ENT_QUOTES,$charset)."</td>
				<td style='width:40%'>
					<textarea id='comment_lg[!!no!!]' name='comment_lg[!!no!!]' tabindex='1' class='in_cell' rows='1' wrap='virtual'>!!comment_lg!!</textarea>
				</td>
				<td style='width:10%'>".htmlentities($msg['acquisition_comment_lo'],ENT_QUOTES,$charset)."</td>
				<td style='width:40%'>
					<textarea id='comment_lo[!!no!!]' name='comment_lo[!!no!!]' tabindex='1' class='in_cell' rows='1' wrap='virtual'>!!comment_lo!!</textarea>
				</td>
			<tr />
			!!applicants_tr!!
		</table>
	</td>
</tr>
";

$applicants_common_tpl = "<tr !!applicants_visibility!!>
							<td colspan='2'>".htmlentities($msg['acquisition_applicants'],ENT_QUOTES,$charset)."</td>
							<td colspan='2' id='C_!!no!!_applicants_container'>
								!!std_applicants!!
							</td>
						</tr>";