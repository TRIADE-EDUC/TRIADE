<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: receptions.tpl.php,v 1.16.8.1 2024/09/04 07:39:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $recept_search_form, $current_module, $msg, $charset, $recept_list_form, $recept_search_form_suite, $sel_date_form, $recept_hrow_form, $recept_row_form, $sel_fou_form, $sel_dem_form, $sel_rub_form, $bt_app, $bt_rel, $bt_chk, $link_not, $link_bull,$link_art, $link_sug, $bt_cat, $recept_cat_error_form, $pmb_numero_exemplaire_auto;

//	------------------------------------------------------------------------------
//	$recept_form : template de recherche pour les receptions
//	------------------------------------------------------------------------------

$recept_search_form = "
<form class='form-".$current_module."' id='recept_search_form' name='recept_search_form' method='post' action=\"\" >
	<h3>!!form_title!!</h3>
	<!--    Contenu du form    -->
	<div class='form-contenu'>

		<div class='row'>
			<div class='colonne2'>
				<label class='etiquette'>".htmlentities($msg['acquisition_coord_lib'], ENT_QUOTES, $charset)."</label>
				<br />
				<!-- sel_bibli -->
			</div>
			<div class='colonne2'>
				<label class='etiquette'>".htmlentities($msg['acquisition_budg_exer'], ENT_QUOTES, $charset)."</label>
				<br />
				<!-- sel_exer -->
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette'>".htmlentities($msg['acquisition_ach_fou'], ENT_QUOTES, $charset)."</label>
				<br />
				<span style='width: 267px;'>
					<input type='text' id='f_fou0' autfield='f_fou_code0' completion='fournisseur' class='saisie-20emr' value='!!f_fou!!' autocomplete='off' linkfield='id_bibli'/>
				</span>					
				<input type='hidden' id='f_fou_code0' name='f_fou_code[0]' value='!!f_fou_code!!' />
				<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=fournisseur&caller=recept_search_form&param1=f_fou_code0&param2=f_fou0&id_bibli='+this.form.id_bibli.value+'&deb_rech='+".pmb_escape()."(this.form.f_fou0.value), 'selector'); \" />
				<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.f_fou_code0.value=''; this.form.f_fou0.value='';  \" />
				<input type='button' onclick='add_fou();' value='+' class='bouton_small' />
				<input type='hidden' id='max_fou' value='!!max_fou!!' />
				<div id='add_fou' ><!-- sel_fou --></div>			
			</div>
			<div class='colonne3'>
				<label class='etiquette'>".htmlentities($msg['acquisition_ach_dem'], ENT_QUOTES, $charset)."</label>
				<br />
				<span style='width: 267px;'>
					<input type='text' id='f_dem0' autfield='f_dem_code0' completion='origine' class='saisie-20emr' value='!!f_dem!!' autocomplete='off' callback='after_dem' />
				</span>
				<input type='hidden' id='f_dem_code0' name='f_dem_code[0]' value='!!f_dem_code!!' />
				<input type='hidden' id='t_dem0' name='t_dem[0]' value='!!t_dem!!' />
				<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=origine&sub=empr&caller=recept_search_form&param1=f_dem_code0&param2=f_dem0&param3=t_dem0&deb_rech='+".pmb_escape()."(this.form.f_dem0.value), 'selector'); \" />
				<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.f_dem_code0.value=''; this.form.f_dem0.value=''; this.form.t_dem0.value=''; \" />				
				<input type='button' onclick='add_dem();' value='+' class='bouton_small' />
				<input type='hidden' id='max_dem' value='!!max_dem!!' />
				<div id='add_dem' ><!-- sel_dem --></div>			
			</div>
			<div class='colonne3'>
				<label class='etiquette'>".htmlentities($msg['acquisition_rub2'], ENT_QUOTES, $charset)."</label>
				<br />
				<span style='width: 267px;'>
					<input type='text' id='f_rub0' autfield='f_rub_code0' linkfield='id_exer' completion='rubrique' class='saisie-20emr' value='!!f_rub!!' autocomplete='off' />
				</span>
				<input type='hidden' id='f_rub_code0' name='f_rub_code[0]' value='!!f_rub_code!!' />
				<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=rubriques&caller=recept_search_form&param1=f_rub_code0&param2=f_rub0&id_bibli='+this.form.id_bibli.value+'&id_exer='+this.form.id_exer.value+'&deb_rech='+".pmb_escape()."(this.form.f_rub0.value), 'select_user', 400, 400, -2, -2, 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes'); \" />
				<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.f_rub_code0.value=''; this.form.f_rub0.value=''; \" />				
				<input type='button' onclick='add_rub();' value='+' class='bouton_small' />
				<input type='hidden' id='max_rub' value='!!max_rub!!' />
				<div id='add_rub' ><!-- sel_rub --></div>			
			</div>
		</div>

		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette' for='chk_cde' >".htmlentities($msg['acquisition_ach_cde2'], ENT_QUOTES, $charset)."</label>
				<input type='hidden' id='chk_dev' name='chk_dev' value='0' />
				<br />
				<input type='text' class='saisie-30em' id='cde_query' name='cde_query' value='!!cde_query!!' />
				<br />
				<!-- sel_date -->
			</div>
			<div class='colonne3'>
				<!-- categorie de commande a mettre ici -->
				<div class='row'>
					<!-- sel_categ -->
				</div>
			</div>
			<div class='colonne3'>
				<label class='etiquette'>".htmlentities($msg['acquisition_lgstat'], ENT_QUOTES, $charset)."</label>
				<br />
				&nbsp;<!-- sel_lgstat -->
			</div>
		</div>
		
		<div class='row'>
			<div class='colonne60'>
				<label class='etiquette'>".htmlentities($msg['global_search'], ENT_QUOTES, $charset)."</label>
				<br />
				<input type='text' id='all_query' name='all_query' class='saisie-80em' value='!!all_query!!'/>&nbsp;
			</div>
			<div class='colonne40'>
				<br />
				<input type='button' class='bouton' value='".$msg['actualiser']."' onclick=\"actualize(this.form);\"/>
			</div>
		</div>
		<input type='hidden' id='serialized_search' value='!!serialized_search!!' />
		<div class='row'></div>
";

//	------------------------------------------------------------------------------
//	$recept_form : template de liste pour les receptions
//	------------------------------------------------------------------------------
$recept_list_form= "		
		<hr />
		<div class='row'>
			<div class='left'>
				<label class='etiquette'>".htmlentities($msg['acquisition_recept2'], ENT_QUOTES, $charset)."</label>&nbsp;
				<input type='text' id='recept_query' name='recept_query' class='saisie-20em' value='!!recept_query!!' />&nbsp;
				<input type='submit' class='bouton' value='".$msg['acquisition_recept_bt_val']."' onclick=\" search_code(this, this.form.recept_query); return false; \"/>
				<input type='hidden' id='max_no' value='!!max_no!!' />
			</div>
		</div>

		<div class='row'></div>
		<hr />
		
		<!-- actes -->
			
		<div class='row'>
			<div class='left'>
				<label class='etiquette'>".htmlentities($msg['acquisition_recept_lgstat_mod'],ENT_QUOTES,$charset)."</label>
				<!-- sel_lgstat_all -->
			</div>
			<div class='right'><!-- bt_chk --></div>
		</div>
		<div class='row'>
			<div class='colonne2'>
				<label class='etiquette'>".htmlentities($msg['acquisition_comment_lg'],ENT_QUOTES,$charset)."</label>
				<br />
				<textarea id='comment_lg_all' name='comment_lg_all' tabindex='1' class='in_cell' rows='2' wrap='virtual'>!!comment_lg_all!!</textarea>
			</div>
			<div class='colonne2'>
				<label class='etiquette'>".htmlentities($msg['acquisition_comment_lo'],ENT_QUOTES,$charset)."</label>
				<br />
				<textarea id='comment_lo_all' name='comment_lo_all' tabindex='1' class='in_cell' rows='2' wrap='virtual'>!!comment_lo_all!!</textarea>
			</div>
		</div>
		
		<!--	boutons	-->
		<div class='row'>
			<div class='left'><!-- bt_app --></div>
			<div class='right'><!-- bt_rel --></div>
		</div>
		
		<div class='row'></div>
	</div>
</form>
<br />
";

$recept_search_form_suite= "
	</div>
</form>
<div id='att' style='z-Index:1000'></div>
<script type='text/javascript' src='javascript/ajax.js'></script>
<script type='text/javascript' src='javascript/tablist.js'></script>
<script type='text/javascript' src='javascript/receptions.js'></script>
<script type='text/javascript' >
	var msg_parcourir='".addslashes($msg['parcourir'])."'; 
	var msg_raz='".addslashes($msg['raz'])."'; 
	var msg_checkAll='".addslashes($msg['acquisition_recept_checkAll'])."';
	var msg_uncheckAll='".addslashes($msg['acquisition_recept_uncheckAll'])."';
	var option_num_auto='".$pmb_numero_exemplaire_auto."';
</script>
";

$sel_date_form[0] = "<label class='etiquette'>!!msg!!</label>"; 
$sel_date_form[1] = "<input type='date' id='date_inf' name='date_inf' value='!!date_inf!!' />";
$sel_date_form[2] = "<input type='date' id='date_sup' name='date_sup' value='!!date_sup!!' />";

$recept_hrow_form="
<div class='row'>
!!lib_acte!!
</div>
<div class='row'>
	<table class='act_cell' >
		<tbody id='act_tab' >
			<tr>
				<th width='3%' ></th>
				<th width='10%' title='".htmlentities($msg['acquisition_act_tab_code'], ENT_QUOTES, $charset)."' >".htmlentities($msg['acquisition_act_tab_code'], ENT_QUOTES, $charset)."</th>
				<th width='35%' title='".htmlentities($msg['acquisition_act_tab_lib'], ENT_QUOTES, $charset)."' >".htmlentities($msg['acquisition_act_tab_lib'], ENT_QUOTES, $charset)."</th>
				<th width='6%' title='".htmlentities($msg['acquisition_qte_cde'], ENT_QUOTES, $charset)."' >".htmlentities($msg['acquisition_qte_cde'], ENT_QUOTES, $charset)."</th>
				<th width='6%' title='".htmlentities($msg['acquisition_qte_liv'], ENT_QUOTES, $charset)."' >".htmlentities($msg['acquisition_qte_liv'], ENT_QUOTES, $charset)."</th>
				<th width='6%' title='".htmlentities($msg['acquisition_qte_sol'], ENT_QUOTES, $charset)."' >".htmlentities($msg['acquisition_qte_sol'], ENT_QUOTES, $charset)."</th>
				<th width='15%' title='".htmlentities($msg['acquisition_lgstat'], ENT_QUOTES, $charset)."' >".htmlentities($msg['acquisition_lgstat'], ENT_QUOTES, $charset)."</th>
				<th width='16%'>&nbsp;</th>
				<th width='3%' ></th>
			</tr>
			<!-- lignes -->
		</tbody>
	</table>
</div>
";


$recept_row_form= "
<tr id='R_!!no!!'>
	<td width='3%' style='overflow:visible;'>
		<img onclick=\"javascript:expandRow('D_!!no!!_', true);\"  src='".get_url_icon('plus.gif')."' name='D_!!no!!_Img' id='D_!!no!!_Img' class='act_cell_img_plus' />
	</td>
	<td>
		<input type='text' class='in_cell_ld' id='code[!!no!!]' name='code[]' value='!!code!!' />
	</td>
	<td>
		<div class='in_cell_ld' >!!lib!!</div>
	</td>
	<td>
		<div class='in_cell_rd' >!!qte_cde!!</div>
	</td>
	<td>
		<div class='in_cell_rd' id='qte_rec[!!no!!]' >!!qte_liv!!</div>
	</td>
	<td>
		<div class='in_cell_rd' id='qte_sol[!!no!!]' >!!qte_sol!!</div>
	</td>
	<td>
		!!lgstat!!
	</td>	
	<td>
		<div class='row'>
			<div class='left'>
				<!-- link_cat -->
				<!-- link_sug -->
				<!-- bt_cat -->
				</div>
			<div class='right'>
				<input type='button' id='bt_rec[!!no!!]' class='bouton_small' value='".htmlentities($msg['acquisition_recept2'],ENT_QUOTES,$charset)."' onclick=\"recept_openFrame(this,!!no!!);\" />
			</div>
		</div>
	</td>
	<td width='3%' style='overflow:visible;' >
		<input type='checkbox' id='chk[!!no!!]' name='chk[]' tabindex='1' value='!!no!!' class='act_cell_chkbox2' />
		<input type='hidden' id='date_last_relance[!!no!!]' name='date_last_relance[]' value='!!date_last_relance!!' />
        <input type='hidden' id='id_lig[!!no!!]' name='id_lig[!!no!!]' value='!!id_lig!!' /> 
<!--		<input type='hidden' id='id_sug[!!no!!]' name='id_sug[!!no!!]' value='!!id_sug!!' /> --> 
		<input type='hidden' id='typ_lig[!!no!!]' name='typ_lig[!!no!!]' value='!!typ_lig!!' /> 	
		<input type='hidden' id='id_prod[!!no!!]' name='id_prod[!!no!!]' value='!!id_prod!!' />
	</td>
</tr>	
<tr id='C_!!no!!_Child' class='act_cell_comments' >
	<td colspan='9'>
		<table>
			<tr>
				<td width='20%' >".htmlentities($msg['acquisition_comment_lg'],ENT_QUOTES,$charset)."
					<img style='padding-left:5px;cursor:pointer;width:10px;vertical-align:middle;' src='".get_url_icon('b_edit.png')."' onclick=\"recept_mod_comment('comment_lg_!!id_lig!!');\" >
					<img style='padding-left:5px;cursor:pointer;width:10px;vertical-align:middle;' src='".get_url_icon('cross.png')."' onclick=\"recept_del_comment('comment_lg_!!id_lig!!');\">
				</td>
				<td width='30%'>
					<div id='comment_lg_!!id_lig!!'>!!comment_lg!!</div>
					<div style='display:none;'>
						<textarea id='comment_lg_!!id_lig!!_mod' class='in_cell' rows='2' wrap='virtual' ></textarea>
						<input type='button' class='bouton_small' value='".$msg['76']."' onclick=\"recept_undo_comment('comment_lg_!!id_lig!!');\" />
						<input type='button' class='bouton_small' value='".$msg['77']."' onclick=\"recept_upd_comment('comment_lg_!!id_lig!!');\" />
					</div>
				</td>
				<td width='20%'>".htmlentities($msg['acquisition_comment_lo'],ENT_QUOTES,$charset)."
					<img style='padding-left:5px;cursor:pointer;width:10px;vertical-align:middle;' src='".get_url_icon('b_edit.png')."' onclick=\"recept_mod_comment('comment_lo_!!id_lig!!');\" >
					<img style='padding-left:5px;cursor:pointer;width:10px;vertical-align:middle;' src='".get_url_icon('cross.png')."' onclick=\"recept_del_comment('comment_lo_!!id_lig!!');\">
				</td>
				<td width='30%'>
					<div id='comment_lo_!!id_lig!!'>!!comment_lo!!</div>
					<div style='display:none;'>
						<textarea id='comment_lo_!!id_lig!!_mod' class='in_cell' rows='2' wrap='virtual' ></textarea>
						<input type='button' class='bouton_small' value='".$msg['76']."' onclick=\"recept_undo_comment('comment_lo_!!id_lig!!');\" />
						<input type='button' class='bouton_small' value='".$msg['77']."' onclick=\"recept_upd_comment('comment_lo_!!id_lig!!');\" />
					</div>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr id='D_!!no!!_Child' class='act_cell_reminders' style='display:none;'>
	<td colspan='9'>
		<table>
			<tr>
				<td width='10%'>!!nb_relances!!</td>
				<td width='90%'><!-- relances --></td>
			</tr>
		</table>	
</tr>
";

$sel_fou_form = "
<div class='row'>
	<span style='width: 267px;'>
		<input type='text' id='f_fou!!i!!' autfield='f_fou_code!!i!!' linkfield='id_bibli' completion='fournisseur' class='saisie-20emr' value='!!f_fou!!' autocomplete='off' />
	</span>					
	<input type='hidden' id='f_fou_code!!i!!' name='f_fou_code[!!i!!]' value='!!f_fou_code!!' />
	<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=fournisseur&caller=recept_search_form&param1=f_fou_code!!i!!&param2=f_fou!!i!!&id_bibli='+this.form.id_bibli.value+'&deb_rech='+".pmb_escape()."(this.form.f_fou!!i!!.value), 'selector'); \" />
	<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.f_fou_code!!i!!.value=''; this.form.f_fou!!i!!.value='';  \" />
</div>
";


$sel_dem_form="
<div class='row'>
	<span style='width: 267px;'>
		<input type='text' id='f_dem!!i!!' autfield='f_dem_code!!i!!' completion='origine' class='saisie-20emr' value='!!f_dem!!' autocomplete='off' callback='after_dem' />
	</span>
	<input type='hidden' id='f_dem_code!!i!!' name='f_dem_code[!!i!!]' value='!!f_dem_code!!' />
	<input type='hidden' id='t_dem!!i!!' name='t_dem[!!i!!]' value='!!t_dem!!' />
	<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=origine&sub=empr&caller=recept_search_form&param1=f_dem_code!!i!!&param2=f_dem!!i!!&param3=t_dem0&deb_rech='+".pmb_escape()."(this.form.f_dem0.value), 'selector'); \" />
	<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.f_dem_code!!i!!.value=''; this.form.f_dem!!i!!.value=''; this.form.t_dem!!i!!.value=''; \" />
</div>
";

$sel_rub_form="
<div class='row'>
	<span style='width: 267px;'>
		<input type='text' id='f_rub!!i!!' autfield='f_rub_code!!i!!' linkfield='id_exer' completion='rubrique' class='saisie-20emr' value='!!f_rub!!' autocomplete='off' callback='after_rub' />
	</span>
	<input type='hidden' id='f_rub_code!!i!!' name='f_rub_code[!!i!!]' value='!!f_rub_code!!' />
	<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=rubriques&caller=recept_search_form&param1=f_rub_code!!i!!&param2=f_rub!!i!!&param3=t_rub0&deb_rech='+".pmb_escape()."(this.form.f_rub!!i!!.value), 'selector'); \" />
	<input type='button' class='bouton_small' value='".$msg['raz']."' onclick=\"this.form.f_rub_code!!i!!.value=''; this.form.f_rub!!i!!.value=''; \" />
</div>
";

$bt_app ="<input type='button' id='bt_app' class='bouton' value='".$msg['acquisition_recept_bt_app']."' onclick=\"apply_changes(this.form);\" />";

$bt_rel ="<input type='button' id='bt_rel' class='bouton' value='".$msg['acquisition_recept_bt_rel']."' onclick=\"do_relances(this.form);\" />";

$bt_chk ="<input type='button' id='bt_chk' class='bouton' value='".$msg['acquisition_recept_checkAll']."' onclick=\"checkAll('recept_search_form', 'chk', check); return false;\" />";

$link_not = "<a href='./catalog.php?categ=isbd&id=!!id_prod!!' target='_blank' ><img border='0' class='align_middle' src='".get_url_icon('notice.gif')."' alt='".htmlentities($msg['acquisition_recept_view_not'],ENT_QUOTES, $charset)."' title='".htmlentities($msg['acquisition_recept_view_not'],ENT_QUOTES, $charset)."' /></a>";

$link_bull = "<a href='./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!id_prod!!' target='_blank' ><img border='0' class='align_middle' src='".get_url_icon('notice.gif')."' alt='".htmlentities($msg['acquisition_recept_view_bull'],ENT_QUOTES, $charset)."' title='".htmlentities($msg['acquisition_recept_view_bull'],ENT_QUOTES, $charset)."' /></a>";

$link_art = "<a href='./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!id_bull!!&art_to_show=!!id_prod!!#anchor_!!id_prod!!' target='_blank' ><img border='0' class='align_middle' src='".get_url_icon('notice.gif')."' alt='".htmlentities($msg['acquisition_recept_view_art'],ENT_QUOTES, $charset)."' title='".htmlentities($msg['acquisition_recept_view_art'],ENT_QUOTES, $charset)."' /></a>";

$link_sug = "<a href='./acquisition.php?categ=sug&action=modif&id_bibli=0&id_sug=!!id_sug!!' target='_blank' ><img border='0' class='align_middle' src='".get_url_icon('suggestion.png')."' alt='".htmlentities($msg['acquisition_recept_view_sug'],ENT_QUOTES, $charset)."' title='".htmlentities($msg['acquisition_recept_view_sug'],ENT_QUOTES, $charset)."' /></a>";

$bt_cat = "<input type='button' id='bt_cat' class='bouton_small' value='".$msg['acquisition_recept_cat']."' onclick=\"catalog(this.form);\" />
			<br /><input type='radio' name='cat_!!no!!' id='cat_not_!!no!!' value='0' checked='checked'/><label class='etiquette' for='cat_not_!!no!!' >".htmlentities($msg['acquisition_type_mono'],ENT_QUOTES, $charset)."</label>
			<br /><input type='radio' name='cat_!!no!!' id='cat_art_!!no!!' value='1' /><label class='etiquette' for='cat_art_!!no!!' >".htmlentities($msg['acquisition_type_art'],ENT_QUOTES, $charset)."</label>";

$bt_cat = "<input type='button' id='bt_cat' class='bouton_small' value='".$msg['acquisition_recept_cat']."' onclick=\"catalog(this.form,!!id_lig!!);\" />";


$recept_cat_error_form = "
<br />
<div class='erreur'>".htmlentities($msg[540],ENT_QUOTES, $charset)."</div>
<div class='row'>
	<div class='colonne10'>
		<img src='".get_url_icon('error.gif')."' class='align_left' />
	</div>
	<div class='colonne80'>
		<strong>".htmlentities($msg['gen_signature_erreur_similaire'], ENT_QUOTES,$charset)."</strong>
	</div>
</div>
<div class='row'>
	<form class='form-".$current_module."' name='recept_cat_error_form'  method='post' action='./acquisition.php?categ=ach&sub=recept&action=record' >
		<input type='hidden' name='serialized_post' value='!!serialized_post!!' />
		<input type='hidden' name='existant_notice_id' value='!!existant_notice_id!!' />
		<input type='hidden' name='existant_b_level' value='!!existant_b_level!!' />
		<input type='hidden' name='existant_h_level' value='!!existant_h_level!!' />
		<input type='hidden' name='signature' value='!!signature!!' />		
		<input type='hidden' name='id_lig' value='!!id_lig!!' />
		<input type='hidden' name='serialized_search' value='!!serialized_search!!' />
		<input type='button' name='existant' class='bouton' value='".htmlentities($msg['acquisition_recept_cat_exists'], ENT_QUOTES, $charset)."' 
			onClick=\"this.form.action = this.form.action+'&integre=existant';this.form.submit();\" />
		<input type='button' name='new' class='bouton'  value='".htmlentities($msg['acquisition_recept_cat_new'], ENT_QUOTES, $charset)."' 
			onClick=\"this.form.action = this.form.action+'&integre=new';this.form.submit();\" />
		<input type='button' name='undo' class='bouton'  value='".htmlentities($msg[76], ENT_QUOTES, $charset)."' 
			onClick=\"history.go(-1);\" />
	</form>
</div>
<div class='row'><!-- notice_display --></div>
<script type='text/javascript' src='./javascript/tablist.js'></script>
<script type='text/javascript'>
	document.getElementById('el!!existant_notice_id!!Child').setAttribute('startOpen','Yes');
	document.forms['recept_cat_error_form'].elements['existant'].focus();
</script>
";