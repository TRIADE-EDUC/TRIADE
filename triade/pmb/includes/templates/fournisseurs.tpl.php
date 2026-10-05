<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fournisseurs.tpl.php,v 1.30.8.1 2025/05/28 06:20:16 dgoron Exp $


if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $id_bibli, $coord_content_form2, $current_module, $msg, $charset, $cond_form, $frame, $bt_add, $rem_form, $bt_sup, $histrel_form, $histrel_hrow_form, $acquisition_gestion_tva, $acquisition_gestion_tva;

//	------------------------------------------------------------------------------
//	$coord_content_form2 : template form edition des coordonnées des fournisseurs 
//	------------------------------------------------------------------------------

$coord_content_form2 = "
<div class='row'>
	<div class='colonne'>
		<div class='colonne5' >			
			<label class='etiquette'>".htmlentities($msg['acquisition_coord_lib'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='colonne5'>
			!!lib_bibli!!
		</div>
		<div class='colonne_suite'>
			<!-- chk_all_etablissements -->
		</div>
	</div>
</div>
<div class='row'>
	<hr />
</div>	
<div class='row'>
		<label class='etiquette' for='raison'>".htmlentities($msg['acquisition_raison_soc'],ENT_QUOTES,$charset)."</label>
</div>
<div class='row'>
	<input type=text id='raison' name='raison' value=\"!!raison!!\" class='saisie-50em' />
</div>
<div class='row'>
		<label class='etiquette' for='num_cp'>".htmlentities($msg['acquisition_num_cp_client'],ENT_QUOTES,$charset)."</label>
</div>
<div class='row'>
	<input type=text id='num_cp' name='num_cp' value=\"!!num_cp!!\" class='saisie-25em' />
</div>
<div class= 'row'>
	!!contact!!
</div>
<hr />
<div class='row'>
	<label class='etiquette' for='comment'>".htmlentities($msg['acquisition_commentaires'],ENT_QUOTES,$charset)."</label>
</div>
<div class='row'>
	<textarea id='comment' name='comment' class='saisie-80em' cols='62' rows='6' wrap='virtual'>!!commentaires!!</textarea>
</div>
<div class= 'colonne2'>
	<div class='row'>
		<label class='etiquette' for='siret'>".htmlentities($msg['acquisition_siret'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='row'>
		<input type='text' id='siret' name='siret' value='!!siret!!' class='saisie-50em' />
	</div>
</div>

<div class='colonne_suite'>
	<div class='row'>
		<label class='etiquette' for='rcs'>".htmlentities($msg['acquisition_rcs'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='row'>
		<input type='text' id='rcs' name='rcs' value='!!rcs!!' class='saisie-50em' />
	</div>
</div>

<div class= 'colonne2'>
	<div class='row'>
		<label class='etiquette' for='naf'>".htmlentities($msg['acquisition_naf'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='row'>
		<input type='text' id='naf' name='naf' value='!!naf!!' class='saisie-50em' />
	</div>
</div>

<div class='colonne_suite'>
	<div class='row'>
		<label class='etiquette' for='tva' >".htmlentities($msg['acquisition_tva'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='row'>
		<input type='text' id='tva' name='tva' value='!!tva!!' class='saisie-50em' />
	</div>
</div>
<div class='row'>
	<label class='etiquette' for='site_web'>".htmlentities($msg['acquisition_site_web'],ENT_QUOTES,$charset)."</label>
</div>
<div class='row'>
	<input type='text' id='site_web' name='site_web' value='!!site_web!!' class='saisie-50em' />
</div>
";

//	------------------------------------------------------------------------------
//	$cond_form : template form liste des conditions commerciales  
//	------------------------------------------------------------------------------

$cond_form = "
<form class='form-".$current_module."' id='condform' name='condform' method='post' action=\"./acquisition.php?categ=ach&sub=fourn&action=updatecond&id=!!id!!\">
<h3>!!form_title!!</h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<div class='colonne2'>
			<div class='colonne2' >			
				<label class='etiquette'>".htmlentities($msg['acquisition_coord_lib'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='colonne_suite'>
				!!lib_bibli!!
			</div>
		</div>
	</div>
	<div class='row'>
		<hr />
	</div>	
	<div class='row'>
		<div class='colonne2'>
			<div class='colonne2' >			
				<label class='etiquette'>".htmlentities($msg['acquisition_ach_fou2'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='colonne_suite'>
				!!raison!!
			</div>
		</div>
	</div>
	<div class='row'>&nbsp;
	</div>	

	<div class='row'>
		<div class='colonne2'>
			<label class='etiquette'>".htmlentities($msg['acquisition_fou_paie'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='colonne_suite'>
		</div>
	</div>
	<div class='row'>
		<div class='colonne2'>
			<!-- paiements -->
		</div>
		<div class='colonne_suite'>
		</div>
	</div>
	<div class='row'>&nbsp;</div>
	<div class='row'><hr /></div>
	<!-- frame -->
	<!-- bt_add -->
</div>	
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onclick=\"document.location='./acquisition.php?categ=ach&sub=fourn'\" />&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' />
	</div>
	<div class='row'></div>
</div>
</form>
<div class='row'></div>
";


//	------------------------------------------------------------------------------
//	$frame : trame de visualisation des offres produits 
//	------------------------------------------------------------------------------

$frame = "
<table>
	<tr>
		<th>".htmlentities($msg['acquisition_type_prod'],ENT_QUOTES,$charset)."</th>
		<th>".htmlentities($msg['acquisition_remise'],ENT_QUOTES,$charset)."</th>
	</tr>
	<!-- frames_rows -->
</table>
";

$bt_add = "<input type='button' class='bouton' value='".htmlentities($msg['acquisition_rem_bt_add'],ENT_QUOTES,$charset)."' 
			onclick=\"document.forms['condform'].setAttribute('action', './acquisition.php?categ=ach&sub=fourn&action=modrem&id=!!id_fou!!&id_prod=0');
						document.forms['condform'].submit();\" />";


//	------------------------------------------------------------------------------
//	$rem_content_form : contenu du formulaire des offres de remises par type de produits
//	------------------------------------------------------------------------------

$rem_content_form = "
<div class='row'>
	<div class='colonne2'>
		<div class='colonne2' >
			<label class='etiquette'>".htmlentities($msg['acquisition_coord_lib'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='colonne_suite'>
			!!lib_bibli!!
		</div>
	</div>
</div>
<div class='row'>
	<hr />
</div>
<div class='row'>
	<div class='colonne2'>
		<div class='colonne2' >
			<label class='etiquette'>".htmlentities($msg['acquisition_ach_fou2'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='colonne_suite'>
			!!raison!!
		</div>
	</div>
</div>
<div class='row'>&nbsp;
</div>
<div class='row'>
	<label class='etiquette'>".htmlentities($msg['acquisition_type_prod'], ENT_QUOTES, $charset)."</label>
</div>
<div class='row'>
	<input type='hidden' id='id_prod' name='id_prod' value='!!id_prod!!' />
	!!lib_prod!!
</div>
<div class='row'>
	<label for='rem'>".htmlentities($msg['acquisition_remise'], ENT_QUOTES, $charset)."</label>
</div>
<div class='row'>
	<input type='text' id='rem' name='rem' class='saisie-10em' style='text-align:right' value='!!rem!!' />&nbsp;%
</div>
<div class='row'>
	<label for='comment'>".htmlentities($msg['acquisition_commentaires'],ENT_QUOTES,$charset)."</label>
</div>
<div class='row'>
	<textarea id='comment' class='saisie-80em' name='comment' cols='62' rows='6' wrap='virtual'>!!commentaires!!</textarea>
</div>
<div class='row'>&nbsp;</div>";


$histrel_form ="
<form class='form-".$current_module."' id='histrel_form' name='histrel_condform' method='post' action=\"\">
<h3>!!form_title!!</h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<div class='colonne2'>
			<div class='colonne2' >			
				<label class='etiquette'>".htmlentities($msg['acquisition_coord_lib'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='colonne_suite'>
				!!lib_bibli!!
			</div>
		</div>
	</div>
	<div class='row'>
		<div class='colonne2'>
			<div class='colonne2' >			
				<label class='etiquette'>".htmlentities($msg['acquisition_ach_fou2'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='colonne_suite'>
				!!raison!!
			</div>
		</div>
	</div>
	
	<br /><hr /><br />

	<!-- relances -->
	
</div>
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=\"".$msg['654']."\" onclick=\"document.location='./acquisition.php?categ=ach&sub=fourn&action=list'\" />&nbsp;
	</div>
	<div class='right'>
		<input class='bouton' type='button' value=\"".$msg['acquisition_hist_rel_del']."\" onclick=\"confirmation_delete();\" />&nbsp;
	</div>
</div>
<div class='row'></div>
</form>
<div class='row'></div>
<br />
<script type='text/javascript'>
function confirmation_delete() {
	result = confirm(\"".addslashes($msg['acquisition_hist_rel_del_conf'])."\");
	if(result) document.location = './acquisition.php?categ=ach&sub=fourn&action=deletehistrel&id=!!id!!' ;
}
</script> ";

$histrel_hrow_form = "
<div class='row'>
!!lib_rel!!
</div>
<div class='row'>		
	<table class='act_cell' >
		<tbody id='act_tab'>
			<tr>
				<th width='8%'>".htmlentities($msg['38'], ENT_QUOTES, $charset)."</th>
				<th width='8%'>".htmlentities($msg['acquisition_act_tab_code'], ENT_QUOTES, $charset)."</th>
				<th width='28%'>".htmlentities($msg['acquisition_act_tab_lib'], ENT_QUOTES, $charset)."</th>
				<th width='4%'>".htmlentities($msg['acquisition_act_tab_qte'], ENT_QUOTES, $charset)."</th>";
switch ($acquisition_gestion_tva) {
	case '1' :
		$histrel_hrow_form.= "
				<th width='6%'>".htmlentities($msg['acquisition_act_tab_priht'], ENT_QUOTES, $charset)."</th>
				<th width='20%'>".htmlentities($msg['acquisition_act_tab_typ'], ENT_QUOTES, $charset)."<br />".htmlentities($msg['acquisition_tva'], ENT_QUOTES, $charset)." / ".htmlentities($msg['acquisition_remise'], ENT_QUOTES, $charset)."</th>";
		break;
	case '2' :
		$histrel_hrow_form.= "
				<th width='6%'>".htmlentities($msg['acquisition_act_tab_prittc'], ENT_QUOTES, $charset)."</th>
				<th width='20%'>".htmlentities($msg['acquisition_act_tab_typ'], ENT_QUOTES, $charset)."<br />".htmlentities($msg['acquisition_tva'], ENT_QUOTES, $charset)." / ".htmlentities($msg['acquisition_remise'], ENT_QUOTES, $charset)."</th>";
		break;	
	default :
		$histrel_hrow_form.= "
				<th width='6%'>".htmlentities($msg['acquisition_act_tab_prittc'], ENT_QUOTES, $charset)."</th>
				<th width='20%'>".htmlentities($msg['acquisition_act_tab_typ'], ENT_QUOTES, $charset)."<br />".htmlentities($msg['acquisition_remise'], ENT_QUOTES, $charset)."</th>";
		break;
}
$histrel_hrow_form.= "		
				<th width='16%'>".htmlentities($msg['acquisition_act_tab_bud'], ENT_QUOTES, $charset)."</th>
				<th width='10%'>".htmlentities($msg['acquisition_lgstat'], ENT_QUOTES, $charset)."</th>
			</tr>
			<!-- lignes -->
		</tbody>
	</table>
</div>

";

$histrel_row_form = "
<tr id='R_!!no!!'>
	<td >
		<div class='in_cell_ld' title='!!numero!!' >!!numero!!</div>
	</td>
	<td >
		<div class='in_cell_ld' title='!!code!!' >!!code!!</div>
	</td>
	<td>
		<div class='in_cell_ld' >!!lib!!</div>
	</td>
	<td>
		<input type='text' id='qte[!!no!!]' title='!!qte!!' class='saisie-10emd' style='width:100%;text-align:right;' readonly='readonly' value='!!qte!!' />
	</td>
	<td>
		<input type='text' id='prix[!!no!!]' title='!!prix!!' class='saisie-10emd' style='width:100%;text-align:right;' readonly='readonly' value='!!prix!!' />
	</td>
	<td>
		<div class='in_cell_ld' title='!!lib_typ!!' >!!lib_typ!!</div>		
";
if ($acquisition_gestion_tva) {
	$histrel_row_form.= "
		&nbsp;<input type='text' id='tva[!!no!!]' title='!!tva!! %' class='saisie-10emd' style='width:20%;text-align:right;' readonly='readonly' value='!!tva!!'/>&nbsp;%";
	}
$histrel_row_form.= "
		&nbsp;<input type='text' id='rem[!!no!!]' title='!!rem!! %'class='saisie-10emd' style='width:20%;text-align:right;margin-left:10px;' readonly='readonly' value='!!rem!!'  />&nbsp;%
	</td>
	<td>
		<div class='in_cell_ld' >!!lib_rub!!</div>
	</td>
	<td>
		!!lgstat!!
	</td>	
	<input type='hidden' id='id_lig[!!no!!]' name='id_lig[!!no!!]' value='!!id_lig!!' /> 
</tr>
<tr id='C_!!no!!_Child' class='act_cell_comments' >
	<td colspan='9'>
		<table>
			<tr>
				<td width='20%' >".htmlentities($msg['acquisition_comment_lg'],ENT_QUOTES,$charset)."
				</td>
				<td width='30%'>
					<div id='comment_lg_!!id_lig!!'>!!comment_lg!!</div>
				</td>
				<td width='20%'>".htmlentities($msg['acquisition_comment_lo'],ENT_QUOTES,$charset)."
				</td>
				<td width='30%'>
					<div id='comment_lo_!!id_lig!!'>!!comment_lo!!</div>
				</td>
			</tr>
		</table>
	</td>
</tr>
";