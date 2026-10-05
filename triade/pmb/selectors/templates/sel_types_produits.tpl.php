<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_types_produits.tpl.php,v 1.10 2019/08/29 10:05:39 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $param1, $param2, $param3, $param4, $param5, $sel_header, $msg, $charset, $jscript, $acquisition_gestion_tva, $callback, $sel_footer;

// templates du sélecteur adresses

//-------------------------------------------
//	$sel_header : header
//-------------------------------------------
$sel_header = "
<div class='row'>
	<label class='etiquette'>".htmlentities($msg['acquisition_sel_type'], ENT_QUOTES, $charset)."</label>
</div>
<div class='row'>&nbsp;</div>
<div class='row'>
";

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------
$jscript = "
<script type='text/javascript' src='./javascript/actes.js'></script>
<script type='text/javascript'>
<!--
function set_parent(f_caller, typ, lib_typ, rem, tva)
{
	set_parent_value(f_caller, '".$param1."', typ);
	set_parent_value(f_caller, '".$param2."', reverse_html_entities(lib_typ));
	set_parent_value(f_caller, '".$param3."', reverse_html_entities(rem));";
if ($acquisition_gestion_tva) {
	$jscript.= "set_parent_value(f_caller, '".$param4."', reverse_html_entities(tva));";
}
if ($acquisition_gestion_tva == 1) {
	$jscript.= "window.parent.document.getElementById('convert_ht_ttc_".$param5."').innerHTML=ht_to_ttc(window.parent.document.forms[f_caller].elements['prix[$param5]'].value,window.parent.document.forms[f_caller].elements['$param4'].value);";
} else if ($acquisition_gestion_tva == 2) {
 	$jscript.= "window.parent.document.getElementById('convert_ht_ttc_".$param5."').innerHTML=ttc_to_ht(window.parent.document.forms[f_caller].elements['prix[$param5]'].value,window.parent.document.forms[f_caller].elements['$param4'].value);";
}
if ($callback) {
	$jscript.= "window.parent.".$callback."();";
}
$jscript.= "closeCurrentEnv();
}
-->
</script>
";

//-------------------------------------------
//	$sel_footer : footer
//-------------------------------------------
$sel_footer = "
</div>
";
