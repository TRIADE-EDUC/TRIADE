<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_origine.tpl.php,v 1.15 2020/08/13 07:00:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $param1, $param2, $param3, $param4, $param5, $param6, $callback, $filtre;
global $caller;
global $pmb_lecteurs_localises;
global $jscript, $sel_header, $sel_search_form, $sel_footer, $msg, $charset;

// templates du sélecteur origine

//-------------------------------------------
//	$sel_header : header
//-------------------------------------------

$sel_header = "
<ul class='sel_navbar' >";
	if($filtre == "ONLY_EMPR"){
		$sel_header .="
			<li id='sel_navbar_empr' !!is_current_empr!! ><a href=\"./select.php?what=origine&caller=$caller&dyn=$dyn&sub=empr&param1=$param1&param2=$param2&param3=$param3&param4=$param4&param5=$param5&param6=$param6&callback=$callback&filtre=$filtre&user_input=$user_input\" >".htmlentities($msg['selector_lib_empr'],ENT_QUOTES, $charset)."</a></li>
		";
	} elseif($filtre == "ONLY_USER"){
		$sel_header .="
			<li id='sel_navbar_user' !!is_current_user!! ><a href=\"./select.php?what=origine&caller=$caller&dyn=$dyn&sub=user&param1=$param1&param2=$param2&param3=$param3&param4=$param4&param5=$param5&param6=$param6&callback=$callback&filtre=$filtre&user_input=$user_input\" >".htmlentities($msg['selector_lib_user'],ENT_QUOTES, $charset)."</a></li>
		";
	} else{
		$sel_header .="
			<li id='sel_navbar_empr' !!is_current_empr!! ><a href=\"./select.php?what=origine&caller=$caller&dyn=$dyn&sub=empr&param1=$param1&param2=$param2&param3=$param3&param4=$param4&param5=$param5&param6=$param6&callback=$callback&user_input=$user_input\" >".htmlentities($msg['selector_lib_empr'],ENT_QUOTES, $charset)."</a></li>
			<li id='sel_navbar_user' !!is_current_user!! ><a href=\"./select.php?what=origine&caller=$caller&dyn=$dyn&sub=user&param1=$param1&param2=$param2&param3=$param3&param4=$param4&param5=$param5&param6=$param6&callback=$callback&user_input=$user_input\" >".htmlentities($msg['selector_lib_user'],ENT_QUOTES, $charset)."</a></li>
			<li id='sel_navbar_visitor' !!is_current_visitor!! ><a href=\"./select.php?what=origine&caller=$caller&dyn=$dyn&sub=visitor&param1=$param1&param2=$param2&param3=$param3&param4=$param4&param5=$param5&param6=$param6&callback=$callback&user_input=$user_input\" >".htmlentities($msg['selector_lib_visitor'],ENT_QUOTES, $charset)."</a></li>
	";
	}
$sel_header .= "	
</ul>
<div class='row'>
";


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
	function set_parent(f_caller, orig_value, lib_orig_value, typ_value, poi_value, loc )
	{
		try{
		  set_parent_value(f_caller, '".$param1."', orig_value);
		} catch(err){}
		try{
			set_parent_value(f_caller, '".$param2."', reverse_html_entities(lib_orig_value));
		} catch(err){}
		try{
			set_parent_value(f_caller, '".$param3."', typ_value);
		} catch(err){}
		try{
			set_parent_value(f_caller, '".$param4."', poi_value);
		} catch(err){}
		try{
			set_parent_value(f_caller, '".$param5."', poi_value);
		} catch(err){}
		try{
		   window.parent.document.getElementById('$param6').innerHTML = poi_value;
		} catch(err){}
		if(loc){
			try{
		   window.parent.document.getElementById('dmde_loc').value = loc;
			} catch(err){}
		}
	";
		
		if ($callback) $jscript.="\n	window.parent.".$callback."();\n";
		$jscript .= "	closeCurrentEnv();
	}
				
	-->
	</script>
	";
}

//-------------------------------------------
//	$sel_search_form : module de recherche
//-------------------------------------------
$sel_search_form ="
<script type='text/javascript'>
<!--
function test_form(form){
	if(form.f_user_input.value.length == 0){
		form.f_user_input.value = '*';
	}
	return true;
}
-->
</script>
<form name='search_form' method='post' action='$base_url'>";
if($pmb_lecteurs_localises)
	$sel_search_form .="<div>!!sel_loc!!</div>";
$sel_search_form .="
<input type='text' name='f_user_input' value=\"!!deb_rech!!\">
&nbsp;
<input type='submit' class='bouton_small' value='$msg[142]' onclick='return test_form(this.form)'>
</form>
<script type='text/javascript'>
<!--
	document.forms['search_form'].elements['f_user_input'].focus();
-->
</script>
<hr />
";

//-------------------------------------------
//	$sel_footer : footer
//-------------------------------------------
$sel_footer = "
</div>
";
