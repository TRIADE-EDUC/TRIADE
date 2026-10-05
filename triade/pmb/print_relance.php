<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: print_relance.php,v 1.2 2022/07/28 12:35:46 jparis Exp $

//Ajout aux maniers

$base_path = ".";
$base_auth = "CATALOGAGE_AUTH";
$base_title = "\$msg[abts_gestion_retard_print_relance]";
require_once ("$base_path/includes/init.inc.php"); 

$rel_id_list=explode(",",$sel_relance);
$nb=count($rel_id_list)-1;
if ($action=="print_prepare") {
	print "<script type='text/javascript' src='./javascript/tablist.js'></script>";
	print "<h3>".$msg["abts_gestion_retard_print_relance"]."</h3>\n";
	print "<form name='print_options' action='./catalog/serials/abts_retard/get_relance.php' method='post'>";	
	print "
		<b>".sprintf($msg["abts_gestion_retard_print_info"],$nb)."</b><br />	<hr />	
		<input type='hidden' name='sel_relance' value='$sel_relance' />
		&nbsp;".$msg["abts_gestion_retard_print_type"]."<br />	
		<input type='radio' readonly name='print_mode' value='1'/>&nbsp;".$msg["abts_gestion_retard_print_type_pdf"]."<br />
		<input type='radio' name='print_mode' value='0' checked/>&nbsp;".$msg["abts_gestion_retard_print_type_rtf"]."<br />	
			<hr />	
		<input type='submit' value='".$msg["abts_gestion_retard_print_relance"]."' class='bouton'/>&nbsp;
		<input type='button' value='".$msg["print_cancel"]."' class='bouton' onClick='self.close();'/>
		</form>";
}
print $footer;
html_builder();
