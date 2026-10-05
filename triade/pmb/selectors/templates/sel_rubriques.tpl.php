<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_rubriques.tpl.php,v 1.8 2017/01/19 10:25:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

// templates du sélecteur adresses

//-------------------------------------------
//	$sel_header : header
//-------------------------------------------
$sel_header = "
<div class='row'>
	<label class='etiquette'>".htmlentities($msg['acquisition_sel_rub'], ENT_QUOTES, $charset)."</label>
</div>
<div class='row'></div>
";

//-------------------------------------------
//	$sel_search : search
//-------------------------------------------
$sel_search="<div class='row'>
	<form class='form-$current_module' id='form_query' name='form_query' method='post' action='!!action_url!!' onSubmit='return test_form(this)'>
		<div class='row' >
			<input type='text' id='elt_query' name='elt_query' value='!!elt_query!!' class='saisie-30em'/>
			<input type='button' class='bouton_small' value='X' onclick=\"document.forms['form_query'].elt_query.value=''; return false;\"/>
			<input type='submit' class='bouton_small' value='$msg[142]' />
		</div>
	</form>
</div>
<script type='text/javascript'>

	function test_form(form) {
		if (form.elt_query.value.length == 0) {
			form.elt_query.value='*';
			return true;
		}
		return true;
	}
	document.forms['form_query'].elements['elt_query'].focus();
</script>";

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
</div>
";
