<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: print_cart_tpl.tpl.php,v 1.9 2023/09/04 14:31:19 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $cart_tpl_content_js_form, $msg, $pmb_javascript_office_editor;

$cart_tpl_content_js_form= jscript_unload_question()."
	$pmb_javascript_office_editor
<script type='text/javascript'>
    pmb_include('./javascript/tinyMCE_interface.js');
    
	function test_form(form){
		if((form.f_name.value.length == 0) )		{
			alert('".$msg["admin_mailtpl_name_error"]."');
			return false;
		}
		unload_off();
		return true;
	}
</script>
";