<?php
 // +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_html.php,v 1.5 2017-02-24 15:34:34 dgoron Exp $

//Gestion des options de type commentaire
$base_path = "../..";
$base_auth = "CATALOGAGE_AUTH|ADMINISTRATION_AUTH";
$base_title = "";
include ($base_path."/includes/init.inc.php");

require_once ("$include_path/parser.inc.php");
require_once ("$include_path/fields_empr.inc.php");

if(!isset($first)) $first = '';

$options = stripslashes($options);

//Si enregistrer
if ($first == 1) {
	$param["FOR"] = "html";
	$param['HEIGHT'][0]['value'] = stripslashes($HEIGHT*1);
	$param['WIDTH'][0]['value'] = stripslashes($WIDTH*1);
	$param['REPEATABLE'][0]['value'] = $REPEATABLE ? 1 : 0;
	$param['HTMLEDITOR'][0]['value'] = $HTMLEDITOR ? 1 : 0;
	
	$options = array_to_xml($param, "OPTIONS");
	?> 
	<script>
	opener.document.formulaire.<?php  echo $name; ?>_options.value="<?php  echo str_replace("\n", "\\n", addslashes($options));?> ";
	opener.document.formulaire.<?php  echo $name; ?>_for.value="html";
	self.close();
	</script>
	<?php
	 } else {
	?> 
	<h3><?php  echo $msg['procs_options_param'].$name;
	?> </h3><hr />
	
	
	<?php
	if($options){
		$param = _parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$options, "OPTIONS");
	}
	if (!isset($param["FOR"]) || $param["FOR"] != "html") {
		$param = array();
		$param["FOR"] = "html";
		$param['HEIGHT'][0]['value'] = '';
		$param['WIDTH'][0]['value'] = '';
		$param['REPEATABLE'][0]['value'] = '';
	}
	
	//Formulaire
	?> 
	
	<form class='form-<?php echo $current_module ?>' name="formulaire" action="options_html.php" method="post">
	<h3><?php  echo $type_list_empr[$type];
	?> </h3>
	<div class='form-contenu'>
	<input type="hidden" name="first" value="1">
	<input type="hidden" name="name" value="<?php  echo htmlentities(
		$name,
		ENT_QUOTES,
		$charset);
	?>">
	<table class='table-no-border' width=100%>
	<tr><td><?php  echo $msg["persofield_htmlheight"];
	?> </td><td><input type="text" class="saisie-10em" name="HEIGHT" value="<?php  echo htmlentities(
		$param['HEIGHT'][0]['value'],
		ENT_QUOTES,
		$charset);
	?>"></td></tr>
	<tr><td><?php  echo $msg["persofield_htmlwidth"];
	?> </td><td><input type="text" class="saisie-10em" name="WIDTH" value="<?php  echo htmlentities(
		$param['WIDTH'][0]['value'],
		ENT_QUOTES,
		$charset);
	?>"></td></tr>
	<tr><td><?php  echo $msg["persofield_textrepeat"];
	?> </td><td><input type="checkbox" name="REPEATABLE" <?php  echo $param['REPEATABLE'][0]['value'] ? ' checked ' : "";
	?>></td></tr>
	<tr><td><?php  echo $msg["persofield_usehtmleditor"];
	?> </td><td><input type="checkbox" name="HTMLEDITOR" <?php  echo $param['HTMLEDITOR'][0]['value'] ? ' checked ' : "";
	?>></td></tr>
	</table>
	</div>
	<input class="bouton" type="submit" value="<?php  echo $msg[77];
	?>">
	</form>
	<?php
	 }
?>
</body>
</html>