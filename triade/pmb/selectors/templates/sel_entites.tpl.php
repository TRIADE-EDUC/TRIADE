<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_entites.tpl.php,v 1.5 2017/01/19 10:25:18 dgoron Exp $


if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

//-------------------------------------------
//	$sel_header : header
//-------------------------------------------
$sel_header = "
<div class='row'>
	<label for='titre_select_entites' class='etiquette'>$msg[344]</label>
	</div>
<div class='row'>
";

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------
$jscript = $jscript_common_selector_simple;
$jscript = str_replace('!!param1!!', $param1, $jscript);
$jscript = str_replace('!!param2!!', $param2, $jscript);
$jscript = str_replace('!!infield!!', '', $jscript);

//-------------------------------------------
//	$sel_search_form : module de recherche
//-------------------------------------------
$sel_search_form ="
<form name='search_form' method='post' action='$base_url'>
<input type='text' name='f_user_input' value=\"!!deb_rech!!\">
&nbsp;
<input type='submit' class='bouton_small' value='$msg[142]' />
</form>
<script type='text/javascript'>
<!--
	document.forms['search_form'].elements['f_user_input'].focus();
-->
</script>
!!bouton_ajouter!!
";

//-------------------------------------------
//	$sel_footer : footer
//-------------------------------------------
$sel_footer = "
</div>
";
