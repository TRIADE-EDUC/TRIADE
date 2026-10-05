<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_coordonnees.tpl.php,v 1.5.18.1 2025/05/12 15:05:27 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) {
    die("no access");
}

global $msg, $charset;
global $jscript_common_selector_simple;

// Variables definies dans l'appel
global $param1, $param2;

// templates du sélecteur adresses

//-------------------------------------------
//	$sel_header : header
//-------------------------------------------
$sel_header = "
<div class='row'>
    <label class='etiquette'>".htmlentities($msg['acquisition_sel_coord'], ENT_QUOTES, $charset)."</label>
</div>
<div class='row'>&nbsp;</div>
<div class='row'>";

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------
$jscript = $jscript_common_selector_simple;
$jscript = str_replace('!!param1!!', $param1, $jscript);
$jscript = str_replace('!!param2!!', $param2, $jscript);
$jscript = str_replace('!!infield!!', '', $jscript);

//-------------------------------------------
//	$sel_footer : footer
//-------------------------------------------
$sel_footer = "
</div>";
