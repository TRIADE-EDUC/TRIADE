<?php
// +-------------------------------------------------+
// | PMB                                                                      |
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_notes.tpl.php,v 1.5 2017-10-19 14:42:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

// templates du sélecteur de notes

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

global $jscript_common_selector_simple;
global $jscript;

$jscript = $jscript_common_selector_simple;

$jscript .= "
<script type='text/javascript'>
	function copier_modele(location) {
		window.parent.location.href = location;
		closeCurrentEnv();
	}
</script>
";
