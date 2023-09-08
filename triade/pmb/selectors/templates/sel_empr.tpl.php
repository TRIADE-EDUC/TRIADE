<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_empr.tpl.php,v 1.16 2019-06-06 13:42:32 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $param1, $param2, $infield, $jscript, $auto_submit;
//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------
$jscript = "
<script type='text/javascript'>
<!--
function set_parent(f_caller, id_value, libelle_value,callback){
	set_parent_value(f_caller, '".$param1."', id_value);
	set_parent_value(f_caller, '".$param2."', reverse_html_entities(libelle_value));
	if(callback)
		window.parent[callback]('$infield');";
if (isset($auto_submit) && $auto_submit=='YES') $jscript .= "	window.parent.document.forms[f_caller].submit();";
$jscript .= "	closeCurrentEnv();
}
-->
</script>
";
