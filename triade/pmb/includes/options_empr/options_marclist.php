<?php
 // +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_marclist.php,v 1.8 2019-03-25 13:22:58 dgoron Exp $

//Gestion des options de type text
$base_path = "../..";
$base_auth = "CATALOGAGE_AUTH|ADMINISTRATION_AUTH";
$base_title = "";
include ($base_path."/includes/init.inc.php");

require_once ("$include_path/parser.inc.php");
require_once("$include_path/fields_empr.inc.php");

if(!isset($first)) $first = '';

$options = stripslashes($options);

//Si enregistrer
if ($first == 1) {
	$param["FOR"] = "marclist";
	$param["METHOD"][0]['value'] = stripslashes($METHOD);
	$param["DATA_TYPE"][0]['value'] = $DATA_TYPE;
	$param["METHOD_SORT_VALUE"][0]['value'] = stripslashes($METHOD_SORT_VALUE);
	$param["METHOD_SORT_ASC"][0]['value'] = stripslashes($METHOD_SORT_ASC);
	if ($MULTIPLE=="yes")
		$param['MULTIPLE'][0]['value']="yes";
	else
		$param['MULTIPLE'][0]['value']="no";
	if ($AUTORITE=="yes")
		$param['AUTORITE'][0]['value']="yes";
	else
		$param['AUTORITE'][0]['value']="no";
	$param['UNSELECT_ITEM'][0]['VALUE']=stripslashes($UNSELECT_ITEM_VALUE);
	$param['UNSELECT_ITEM'][0]['value']="<![CDATA[".stripslashes($UNSELECT_ITEM_LIB)."]]>";
	$param["DEFAULT_VALUE"][0]['value'] = stripslashes($DEFAULT_VAULE);
	
	$options = array_to_xml($param, "OPTIONS");
	
	print"
	<script>
	opener.document.formulaire.".$name."_options.value='".str_replace("\n", "\\n", addslashes($options)) ."';
	opener.document.formulaire.".$name."_for.value='marclist';
	self.close();
	</script>
	";
	
} else {
// Création formulaire
	if($options){
		$param=_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$options,"OPTIONS");
	}
	if (!isset($param["FOR"]) || $param["FOR"] != "marclist") {
		$param = array();
		$param["FOR"] = "marclist";
		$param['MULTIPLE'][0]['value'] = '';
		$param['AUTORITE'][0]['value'] = '';
		$param['UNSELECT_ITEM'][0]['VALUE'] = '';
		$param['UNSELECT_ITEM'][0]['value'] = '';
		$param['DEFAULT_VALUE'][0]['value'] = '';
		$param["METHOD"]["0"]["value"] = '';
		$param["METHOD_SORT_VALUE"]["0"]["value"] = '';
		$param["METHOD_SORT_ASC"]["0"]["value"] = '';
	}
	
	$MULTIPLE=$param['MULTIPLE'][0]['value'];
	$AUTORITE=$param['AUTORITE'][0]['value'];
	$UNSELECT_ITEM_VALUE=$param['UNSELECT_ITEM'][0]['VALUE'];
	$UNSELECT_ITEM_LIB=$param['UNSELECT_ITEM'][0]['value'];
	$DEFAULT_VALUE = $param['DEFAULT_VALUE'][0]['value'];
	
	if($param["METHOD"]["0"]["value"])$method_checked[$param["METHOD"]["0"]["value"]]="checked";
	else $method_checked[1]="checked";
	$data_type_selected[$param["DATA_TYPE"]["0"]["value"]]="selected"; 
	
	if($param["METHOD_SORT_VALUE"]["0"]["value"])$method_sort_value_checked[$param["METHOD_SORT_VALUE"]["0"]["value"]]="checked";
	else $method_sort_value_checked[2]="checked";
	if($param["METHOD_SORT_ASC"]["0"]["value"])$method_sort_asc_checked[$param["METHOD_SORT_ASC"]["0"]["value"]]="checked";
	else $method_sort_asc_checked[1]="checked";
	
	$multiple_checked="";
	if ($MULTIPLE=="yes") $multiple_checked= "checked";
	$autorite_checked="";
	if ($AUTORITE=="yes") $autorite_checked= "checked";
		
	//Formulaire	
	$form="
	<h3>".$msg['procs_options_param'].$name."</h3><hr />
	<form class='form-$current_module' name='formulaire' action='options_marclist.php' method='post'>
	<h3>".$type_list[$type]."</h3>
	<div class='form-contenu'>
	<input type='hidden' name='first' value='1'>
	<input type='hidden' name='name' value='".htmlentities(	$name,ENT_QUOTES,$charset)."'>
	<table class='table-no-border' width=100%>	
		<tr><td>".$msg['parperso_include_option_methode']."</td><td>
		<table style='width:100%;vertical-align:center'>
			<tr><td class='center'>".ucfirst($msg['parperso_include_option_selectors_code'])."
			<br />
			<input type='radio' name='METHOD' value='1' ".(isset($method_checked[1]) ? $method_checked[1] : '').">
			</td>
			<td class='center'>".ucfirst($msg['parperso_include_option_selectors_label'])."
			<br />
			<input type='radio' name='METHOD' value='2' ".(isset($method_checked[2]) ? $method_checked[2] : '').">
			</td></tr>
		</table></td></tr>
	
		<tr><td>".$msg['include_option_type_donnees']."
		</td>
		<td>
		<select name='DATA_TYPE'>
			<option value='country' ".(isset($data_type_selected["country"]) ? $data_type_selected["country"] : '')." >".$msg['parperso_marclist_option_country']."</option>
			<option value='lang' ".(isset($data_type_selected["lang"]) ? $data_type_selected["lang"] : '')." >".$msg['parperso_marclist_option_lang']."</option>
			<option value='doctype' ".(isset($data_type_selected["doctype"]) ? $data_type_selected["doctype"] : '')." >".$msg['parperso_marclist_option_doctype']."</option>
			<option value='function' ".(isset($data_type_selected["function"]) ? $data_type_selected["function"] : '')." >".$msg['parperso_marclist_option_function']."</option>
			<option value='section_995' ".(isset($data_type_selected["section_995"]) ? $data_type_selected["section_995"] : '')." >".$msg['parperso_marclist_option_section_995']."</option>
			<option value='typdoc_995' ".(isset($data_type_selected["typdoc_995"]) ? $data_type_selected["typdoc_995"] : '')." >".$msg['parperso_marclist_option_typdoc_995']."</option>
			<option value='codstatdoc_995' ".(isset($data_type_selected["codstatdoc_995"]) ? $data_type_selected["codstatdoc_995"] : '')." >".$msg['parperso_marclist_option_codstatdoc_995']."</option>
			<option value='nivbiblio' ".(isset($data_type_selected["nivbiblio"]) ? $data_type_selected["nivbiblio"] : '')." >".$msg['parperso_marclist_option_nivbiblio']."</option>
			<option value='music_form' ".(isset($data_type_selected["music_form"]) ? $data_type_selected["music_form"] : '')." >".$msg['parperso_marclist_option_music_form']."</option>
			<option value='music_key' ".(isset($data_type_selected["music_key"]) ? $data_type_selected["music_key"] : '')." >".$msg['parperso_marclist_option_music_key']."</option>
		</select>
		</td>
		</tr>
		<tr><td>".$msg['parperso_include_option_sort_methode']."</td><td>
		<table style='width:100%;vertical-align:center'>
			<tr><td class='center'>".ucfirst($msg['parperso_include_option_sort_selectors_code'])."
			<br />
			<input type='radio' name='METHOD_SORT_VALUE' value='1' ".(isset($method_sort_value_checked[1]) ? $method_sort_value_checked[1] : '').">
			</td>
			<td class='center'>".ucfirst($msg['parperso_include_option_sort_selectors_label'])."
			<br />
			<input type='radio' name='METHOD_SORT_VALUE' value='2' ".(isset($method_sort_value_checked[2]) ? $method_sort_value_checked[2] : '').">
			</td class='center'>
			<td>".ucfirst($msg['parperso_options_list_order'])."
			<br />
			<input type='radio' name='METHOD_SORT_VALUE' value='3' ".(isset($method_sort_value_checked[3]) ? $method_sort_value_checked[3] : '').">
			</td></tr>
			<tr><td class='center'>".ucfirst($msg['parperso_include_option_sort_selectors_asc'])."
			<br />
			<input type='radio' name='METHOD_SORT_ASC' value='1' ".(isset($method_sort_asc_checked[1]) ? $method_sort_asc_checked[1] : '').">
			</td>
			<td class='center'>".ucfirst($msg['parperso_include_option_sort_selectors_desc'])."
			<br />
			<input type='radio' name='METHOD_SORT_ASC' value='2' ".(isset($method_sort_asc_checked[2]) ? $method_sort_asc_checked[2] : '').">
			</td>
			<td></td></tr>
		</table></td></tr>
		<tr>
			<td>".$msg["procs_options_liste_multi"]."</td>
			<td><input type='checkbox' value='yes' name='MULTIPLE' $multiple_checked></td>
		</tr>
		<tr>
			<td>".$msg['pprocs_options_liste_authorities']."</td>
			<td><input type='checkbox' value='yes' name='AUTORITE' $autorite_checked></td>
		</tr>
		<tr>
			<td>".$msg['procs_options_choix_vide']."</td>
			<td>".$msg['procs_options_value']." : <input type='text' size='5' name='UNSELECT_ITEM_VALUE' value='".htmlentities($UNSELECT_ITEM_VALUE,ENT_QUOTES,$charset)."'>&nbsp;".$msg['procs_options_label']." : <input type='text' name='UNSELECT_ITEM_LIB' value='".htmlentities($UNSELECT_ITEM_LIB,ENT_QUOTES,$charset)."'></td>
		</tr>
	</table>
	
	</div>
	<input class='bouton' type='submit' value='".$msg[77]."'>
	</form>
	</body>
	</html>
	";
	print $form;
}

