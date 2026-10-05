<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_perio.tpl.php,v 1.7 2019/12/04 08:33:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

// templates du sélecteur periodiques

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

global $jscript;
global $jscript_common_selector_simple;

$jscript = $jscript_common_selector_simple;
$jscript .= "
		<script type='text/javascript'>
			function copier_modele(location){
				window.parent.location.href = location;
				closeCurrentEnv();
			}
		</script>";
