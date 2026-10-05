<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abts.tpl.php,v 1.6 2017/10/19 14:42:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

global $dyn;
global $jscript;

if ($dyn==1) {
$jscript = "
	<script type='text/javascript'>
	<!--
	function set_parent(f_caller, id_value, libelle_value,callback, flag_circlist_info)	{		
		if(callback)
			window.parent[callback](id_value,libelle_value,flag_circlist_info);
		closeCurrentEnv();
	}
	-->
	</script>
";
}
