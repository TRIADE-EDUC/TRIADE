<?php
 // +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_date_flottante.php,v 1.1 2018-03-02 15:37:18 ngantier Exp $

//Gestion des options de type intervalle de date
$base_path = "../..";
$base_auth = "CATALOGAGE_AUTH|ADMINISTRATION_AUTH";
$base_title = "";

include ($base_path."/includes/init.inc.php");
require_once("$include_path/fields_empr.inc.php");
require_once ("$include_path/parser.inc.php");

if(!isset($first)) $first = '';

$options = stripslashes($options);

if ($first == 1) {
	$param["FOR"]="date_flot";
	if ($DEFAULT_TODAY) {
		$param["DEFAULT_TODAY"][0]["value"]="yes";
	} else {
		$param["DEFAULT_TODAY"][0]["value"]="";
	}
	$param['REPEATABLE'][0]['value'] = ($REPEATABLE ? 1 : 0);
	$param['DURATION'][0]['value'] = stripslashes($DURATION*1);
	$param['DURATION_D_M_Y'][0]['value'] = stripslashes($DURATION_D_M_Y*1);
	$options = array_to_xml($param, "OPTIONS");
?> 
<script>
opener.document.formulaire.<?php  echo $name; ?>_options.value="<?php  echo str_replace("\n", "\\n", addslashes($options));?> ";
opener.document.formulaire.<?php  echo $name; ?>_for.value="date_flot";
//alert("<?php echo $msg["proc_param_date_options"]; ?>")
self.close();
</script>
<?php 
} else {
	if($options){
		$param = _parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$options, "OPTIONS");
	}
	if (!isset($param["FOR"]) || $param["FOR"] != "date_flot") {
		$param = array();
		$param["FOR"] = "date_flot";
		$param["DEFAULT_TODAY"][0]["value"] = 'yes';
		$param['REPEATABLE'][0]['value'] = '';
		$param['DURATION'][0]['value'] = 1;
		$param['DURATION_D_M_Y'][0]['value'] = 0;
	}
?>
	<form class='form-<?php echo $current_module ?>' name="formulaire" action="options_date_flottante.php" method="post">
		<h3><?php  echo $type_list_empr[$type];?> </h3>
		<div class='form-contenu'>
			<input type="hidden" name="first" value="1">
			<input type="hidden" name="name" value="<?php  echo htmlentities($name,ENT_QUOTES,$charset);?>">
			<!-- Formulaire -->
			<table class='table-no-border' width=100%>
				<tr>
					<td><?php echo $msg["parperso_default_today"]; ?> </td>
					<td><input type="checkbox" name="DEFAULT_TODAY" value="yes" <?php if ($param["DEFAULT_TODAY"][0]["value"]=="yes") echo "checked"; ?>></td>
				</tr>
				<tr style="display:none;"><!-- To do -->
					<td><?php  echo $msg['persofield_textrepeat'];?> </td>
					<td><input type="checkbox" name="REPEATABLE" <?php  echo $param['REPEATABLE'][0]['value'] ? ' checked ' : "";?>></td>
				</tr>
				<tr>
					<td><?php  echo $msg['parperso_option_duration'];?> </td>
					<td>
						<input type="text" class='saisie-10em' name="DURATION" value="<?php  echo htmlentities($param['DURATION'][0]['value'], ENT_QUOTES, $charset);?>">
						<select name="DURATION_D_M_Y"><?php
							print "
 							<option value='0' ".(!$param['DURATION_D_M_Y'][0]['value'] ? ' selected ' : '').">".$msg['parperso_option_duration_d']."</option>							
							<option value='1' ".($param['DURATION_D_M_Y'][0]['value']==1 ? ' selected ' : '').">".$msg['parperso_option_duration_m']."</option>
							<option value='2' ".($param['DURATION_D_M_Y'][0]['value']==2 ? ' selected ' : '').">".$msg['parperso_option_duration_y']."</option>";								
							?>
						</select>					
					</td>
				</tr>				
			</table>
		</div>
		<input class="bouton" type="submit" value="<?php  echo $msg[77];?>">
	</form>
<?php
}
?>
</body>
</html>