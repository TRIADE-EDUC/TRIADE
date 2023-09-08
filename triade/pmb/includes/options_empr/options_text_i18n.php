<?php
 // +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_text_i18n.php,v 1.6 2019-03-25 13:28:03 dgoron Exp $

//Gestion des options de type text_i18n
$base_path = "../..";
$base_auth = "CATALOGAGE_AUTH|ADMINISTRATION_AUTH";
$base_title = "";
$base_use_dojo=1;
include ($base_path."/includes/init.inc.php");

require_once ("$include_path/parser.inc.php");
require_once ("$include_path/fields_empr.inc.php");

if(!isset($first)) $first = '';

$options = stripslashes($options);

//Si enregistrer
if ($first == 1) {
	$param["FOR"] = "text_i18n";
	$param['SIZE'][0]['value'] = stripslashes($SIZE*1);
	$param['MAXSIZE'][0]['value'] = stripslashes($MAXSIZE*1);
	$param['REPEATABLE'][0]['value'] = $REPEATABLE ? 1 : 0;
	$param['ISHTML'][0]['value'] = $ISHTML ? 1 : 0;
	$param['DEFAULT_LANG'][0]['value']=stripslashes($DEFAULT_LANG);
	
	$options = array_to_xml($param, "OPTIONS");
	?> 
	<script>
	opener.document.formulaire.<?php  echo $name; ?>_options.value="<?php  echo str_replace("\n", "\\n", addslashes($options));?> ";
	opener.document.formulaire.<?php  echo $name; ?>_for.value="text_i18n";
	self.close();
	</script>
	<?php
	 } else {
	?> 
	<h3><?php  echo $msg['procs_options_param'].$name;
	?> </h3><hr />
	
	<?php
	if (!$options) $options = "<OPTIONS></OPTIONS>";
	 $param = _parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$options, "OPTIONS");
	if (!isset($param["FOR"]) || $param["FOR"] != "text_i18n") {
		$param = array();
		$param["FOR"] = "text_i18n";
		$param['SIZE'][0]['value'] = '50';
		$param['MAXSIZE'][0]['value'] = '255';
		$param['REPEATABLE'][0]['value'] = '';
		$param['ISHTML'][0]['value'] = '';
		$param['DEFAULT_LANG'][0]['value'] = '';
	}
	if (!isset($langue_doc) || !count($langue_doc)) {
		$langue_doc = new marc_list('lang');
		$langue_doc = $langue_doc->table;
	}
	//Formulaire
	?> 
	
	<form class='form-<?php echo $current_module ?>' name="formulaire" action="options_text_i18n.php" method="post">
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
	<tr><td><?php  echo $msg['procs_options_text_taille'];
	?> </td><td><input class='saisie-10em' type="text" name="SIZE" value="<?php  echo htmlentities(
		$param['SIZE'][0]['value'],
		ENT_QUOTES,
		$charset);
	?>"></td></tr>
	<tr><td><?php  echo $msg['procs_options_text_max']."<br /><span style='font-size: 0.8em'>".$msg['procs_options_text_max_help']."</span>";
	?> </td><td><input type="text" class='saisie-10em' name="MAXSIZE" value="<?php  echo htmlentities(
		$param['MAXSIZE'][0]['value'],
		ENT_QUOTES,
		$charset);
	?>"></td></tr>
	<tr><td><?php  echo $msg['persofield_textrepeat'];
	?> </td><td><input type="checkbox" name="REPEATABLE" <?php  echo $param['REPEATABLE'][0]['value'] ? ' checked ' : "";
	?>></td></tr>
	<tr><td><?php  echo $msg['persofield_textishtml'];
	?> </td><td><input type="checkbox" name="ISHTML" <?php  echo $param['ISHTML'][0]['value'] ? ' checked ' : "";
	?>></td></tr>
	</table>
	<h3><?php echo $msg["procs_options_lang_options"];
		?></h3>
	<table class='table-no-border' width=100%>
	<tr><td><?php  echo $msg["proc_options_default_value"];
	?> </td><td>
		<input type="hidden" id="DEFAULT_LANG" name="DEFAULT_LANG" value="<?php  echo htmlentities($param['DEFAULT_LANG'][0]['value'],ENT_QUOTES,$charset);?>" />
		<input type="text" id="DEFAULT_LANG_LABEL" name="DEFAULT_LANG_LABEL" class="saisie-20emr" value="<?php  echo htmlentities($langue_doc[$param['DEFAULT_LANG'][0]['value']],ENT_QUOTES,$charset);
	?>">
		<input type="button" class="bouton" value="..." onclick="openPopUp('<?php echo $base_path; ?>/select.php?what=lang&caller=formulaire&p1=DEFAULT_LANG&p2=DEFAULT_LANG_LABEL', 'selector')" />
		<input type="button" class="bouton" value="X" onclick="this.form.DEFAULT_LANG.value='';this.form.DEFAULT_LANG_LABEL.value='';" />
		</td></tr>
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