<?php
 // +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_selector.php,v 1.8 2019-05-11 08:05:25 dgoron Exp $

//Gestion des options de type text
$base_path = "../..";
$base_auth = "CATALOGAGE_AUTH|ADMINISTRATION_AUTH";
$base_title = "";
include ($base_path."/includes/init.inc.php");

require_once ("$include_path/parser.inc.php");
require_once ("$include_path/fields.inc.php");
require_once("$class_path/authperso.class.php");

$options = stripslashes($options);

//Si enregistrer
if (isset($first) && $first == 1) {
	$param["FOR"] = "selector";
	$param["METHOD"][0]['value'] = stripslashes($METHOD);
	$param["DATA_TYPE"][0]['value'] = $DATA_TYPE;

	$options = array_to_xml($param, "OPTIONS");
	
	print"
	<script>
	opener.document.formulaire.".$name."_options.value='".str_replace("\n", "\\n", addslashes($options)) ."';
	opener.document.formulaire.".$name."_for.value='selector';
	self.close();
	</script>
	";
	
} else {
// Création formulaire
   $param = _parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$options, "OPTIONS");
	if ($param["FOR"] != "selector") {
		$param = array();
		$param["FOR"] = "selector";
	}
	
	if(isset($param["METHOD"]["0"]["value"]) && $param["METHOD"]["0"]["value"])$method_checked[$param["METHOD"]["0"]["value"]]="checked";
	else $method_checked[1]="checked";
	if(isset($param["DATA_TYPE"]["0"]["value"])) {
		$data_type_selected[$param["DATA_TYPE"]["0"]["value"]]="selected";
	} 
	
	$options_authperso='';
	$authpersos=authpersos::get_authpersos();
	foreach ($authpersos as $authperso){
		$options_authperso.="<option value='".($authperso['id'] + 1000)."' ".(isset($data_type_selected[($authperso['id'] + 1000)]) ? $data_type_selected[($authperso['id'] + 1000)] : '')." >".$authperso['name']."</option>";
	}
	
	//Formulaire	
	$form="
	<h3>".$msg['procs_options_param'].$name."</h3><hr />
	<form class='form-$current_module' name='formulaire' action='options_selector.php' method='post'>
	<h3>".$type_list[$type]."</h3>
	<div class='form-contenu'>
	<input type='hidden' name='first' value='1'>
	<input type='hidden' name='name' value='".htmlentities(	$name,ENT_QUOTES,$charset)."'>
	<table class='table-no-border' width=100%>	
		<tr><td>".$msg['parperso_include_option_methode']."</td><td>
		<table style='width:100%; vertical-align:center'>
			<tr><td class='center'>".$msg['parperso_include_option_selectors_id']."
			<br />
			<input type='radio' name='METHOD' value='1' ".(isset($method_checked[1]) ? $method_checked[1] : '').">
			</td>
			<td class='center'>".$msg['parperso_include_option_selectors_label']."
			<br />
			<input type='radio' name='METHOD' value='2' ".(isset($method_checked[2]) ? $method_checked[2] : '').">
			</td></tr>
		</table></td></tr>
	
		<tr><td>".$msg['include_option_type_donnees']."
		</td><td><select name='DATA_TYPE'>
			<option value='1' ".(isset($data_type_selected[1]) ? $data_type_selected[1] : '')." >".$msg['133']."</option>
			<option value='2' ".(isset($data_type_selected[2]) ? $data_type_selected[2] : '')." >".$msg['134']."</option>
			<option value='3' ".(isset($data_type_selected[3]) ? $data_type_selected[3] : '')." >".$msg['135']."</option>
			<option value='4' ".(isset($data_type_selected[4]) ? $data_type_selected[4] : '')." >".$msg['136']."</option>
			<option value='5' ".(isset($data_type_selected[5]) ? $data_type_selected[5] : '')." >".$msg['137']."</option>
			<option value='6' ".(isset($data_type_selected[6]) ? $data_type_selected[6] : '')." >".$msg['333']."</option>
			<option value='7' ".(isset($data_type_selected[7]) ? $data_type_selected[7] : '')." >".$msg['indexint_menu']."</option>
			<option value='8' ".(isset($data_type_selected[8]) ? $data_type_selected[8] : '')." >".$msg['titre_uniforme_search']."</option>
			<option value='9' ".(isset($data_type_selected[9]) ? $data_type_selected[9] : '')." >".$msg['skos_view_concepts_concepts']."</option>
			$options_authperso
		</select></td></tr>

	</table>
	</div>
	<input class='bouton' type='submit' value='".$msg[77]."'>
	</form>
	</body>
	</html>
	";
	print $form;
}

