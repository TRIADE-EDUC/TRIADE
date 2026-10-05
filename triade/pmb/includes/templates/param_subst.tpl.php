<?php
// +-------------------------------------------------+
// Â© 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: param_subst.tpl.php,v 1.6 2024/01/17 08:09:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $tpl_param_table, $msg, $tpl_param_table_line, $tpl_param_subst_table, $tpl_param_subst_table_line, $tpl_param_subst_form, $current_module;

//*******************************************************************
// Définition des templates pour les listes en edition
//*******************************************************************
$tpl_param_table = "
	<table>	
		<tr>	
			<th>".$msg[1603]."</th>
			<th>".$msg[1604]."</th>
			<th>".$msg['param_explication']."</th>
		</tr>	
		!!table_lines!!
	</table>	
";
$tpl_param_table_line = "
		<tr class='!!odd_even!!' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='!!odd_even!!'\" onmousedown=\"document.location='!!link_edit!!'\" style=\"cursor: pointer;\">
			<td style='vertical-align:top'>!!name!!</td>
			<td class='ligne_data'>!!value!!</td>	
			<td style='vertical-align:top'>!!comment!!</td>
		</tr>	
";

$tpl_param_subst_table = "
	<table>	
		<tr>	
			<th>".$msg[1603]."</th>
			<th>".$msg[1604]."</th>
			<th>".$msg["param_subst_origin_value_title"]."</th>
			<th>".$msg['param_explication']."</th>
			<th>".$msg['param_subst_to_origin']."</th>
		</tr>	
		!!subst_table_lines!!
	</table>	
";
$tpl_param_subst_table_line = "
		<tr class='!!odd_even!!' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='!!odd_even!!'\" onmousedown=\"document.location='!!link_edit!!'\" style=\"!!style!!\">
			<td style='vertical-align:top'>!!name!!</td>
			<td class='ligne_data'>!!value!!</td>
			<td class='ligne_data'>!!origin_value!!</td>	
			<td style='vertical-align:top'>!!comment!!</td>	
			<td style='vertical-align:top'><input type='button' class='bouton_small' value='X' onclick=\"document.location='!!link_suppr!!'\" /></td>
		</tr>	
";

$tpl_param_subst_form = "
<form class='form-$current_module' name='paramform' method='post' action='!!link_save!!'>
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<div class='form-contenu'>
	<div class='row'>
		<div class='colonne5 align_right'>
				<label class='etiquette'>$msg[1602] &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				!!type_param!! <input type='hidden' name='form_type_param' value='!!type_param!!' />
				</div>
		</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='colonne5 align_right'>
				<label class='etiquette'>$msg[1603] &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				!!sstype_param!! <input type='hidden' name='form_sstype_param' value='!!sstype_param!!' />
				</div>
		</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='colonne5 align_right'>
				<label class='etiquette' for='form_valeur_param'>$msg[1604] &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				<textarea id='form_valeur_param' name='form_valeur_param' rows='10' cols='90' wrap='virtual'>!!valeur_param!!</textarea>
				</div>
		</div>
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='colonne5 align_right'>
				<label class='etiquette' for='comment_param'>".$msg['param_explication']." &nbsp;</label>
				</div>
		<div class='colonne_suite'>
				<textarea id='comment_param' name='comment_param' rows='10' cols='90' wrap='virtual'>!!comment_param!!</textarea>
				</div>
		</div>
	<div class='row'> </div>
	</div>
	<div class='row'>
		<input class='bouton' type='button' value=' $msg[76] ' !!link_annuler!!>
		<input class='bouton' type='submit' value=' $msg[77] ' />
		<input type='hidden' class='text' name='form_id_param' value='!!id_param!!' readonly />
			</div>
</form>
<script type='text/javascript'>document.forms['paramform'].elements['form_valeur_param'].focus();</script>
";