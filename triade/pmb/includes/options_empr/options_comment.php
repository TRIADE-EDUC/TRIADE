<?php
 // +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_comment.php,v 1.15 2018-06-21 16:09:19 dgoron Exp $

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
	$param["FOR"] = "comment";
	$param['COLS'][0]['value'] = stripslashes($COLS*1);
	$param['ROWS'][0]['value'] = stripslashes($ROWS*1);
	$param['MAXSIZE'][0]['value'] = stripslashes($MAXSIZE*1);
	$param['REPEATABLE'][0]['value'] = $REPEATABLE ? 1 : 0;
	$param['ISHTML'][0]['value'] = $ISHTML ? 1 : 0;

	$options = array_to_xml($param, "OPTIONS");
	?> 
	<script>
	opener.document.formulaire.<?php  echo $name; ?>_options.value="<?php  echo str_replace("\n", "\\n", addslashes($options));?> ";
	opener.document.formulaire.<?php  echo $name; ?>_for.value="comment";
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
	if (!isset($param["FOR"]) || $param["FOR"] != "comment") {
		$param = array();
		$param["FOR"] = "comment";
		$param['COLS'][0]['value'] = '50';
		$param['ROWS'][0]['value'] = '5';
		$param['MAXSIZE'][0]['value'] = '255';
		$param['REPEATABLE'][0]['value'] = '';
		$param['ISHTML'][0]['value'] = '';
	}
	
	//Formulaire
	?> 
	
	<form class='form-<?php echo $current_module ?>' name="formulaire" action="options_comment.php" method="post">
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
	<tr><td><?php echo $msg["parperso_options_comment_larg"]; ?></td><td><input class='saisie-10em' type="text" name="COLS" value="<?php  echo htmlentities(
		$param['COLS'][0]['value'],
		ENT_QUOTES,
		$charset);
	?>"></td></tr>
	<tr><td><?php echo $msg["parperso_options_comment_lines"]; ?> </td><td><input class='saisie-10em' type="text" name="ROWS" value="<?php  echo htmlentities(
		$param['ROWS'][0]['value'],
		ENT_QUOTES,
		$charset);
	?>"></td></tr>
	<tr><td><?php  echo $msg["procs_options_text_max"]."<br /><span style='font-size: 0.8em'>".$msg['procs_options_text_max_help']."</span>";
	?> </td><td><input type="text" class='saisie-10em' name="MAXSIZE" value="<?php  echo htmlentities(
		$param['MAXSIZE'][0]['value'],
		ENT_QUOTES,
		$charset);
	?>"></td></tr>
	<tr><td><?php  echo $msg["persofield_textrepeat"];
	?> </td><td><input type="checkbox" name="REPEATABLE" <?php  echo $param['REPEATABLE'][0]['value'] ? ' checked ' : "";
	?>></td></tr>
	<tr><td><?php  echo $msg["persofield_textishtml"];
	?> </td><td><input type="checkbox" name="ISHTML" <?php  echo $param['ISHTML'][0]['value'] ? ' checked ' : "";
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