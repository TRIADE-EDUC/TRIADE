<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_empr.tpl.php,v 1.17 2020/08/13 07:00:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $dyn, $param1, $param2, $infield, $jscript, $auto_submit, $jscript_common_selector;
//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------
if ($dyn==2) {
	//inspiré de $jscript_common_selector
	$jscript = $jscript_common_selector;
	$jscript = str_replace('!!param1!!', $param1, $jscript);
	$jscript = str_replace('!!param2!!', $param2, $jscript);
} else {
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
}