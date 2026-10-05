<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_pricing_system.tpl.php,v 1.5 2023/07/05 15:32:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $rent_pricing_system_js_content_form_tpl, $msg;

$rent_pricing_system_js_content_form_tpl = "
<script src='javascript/pricing_systems.js'></script>
<script type='text/javascript'>
	function test_form(form) {
		if(form.elements['pricing_system_label'].value.replace(/^\s+|\s+$/g, '').length == 0) {
			alert('".addslashes($msg['pricing_system_label_mandatory'])."');
			return false;
		}
		if(!parseInt(form.elements['pricing_system_exercices'].value)) {
			alert('".addslashes($msg['pricing_system_exercices_mandatory'])."');
			return false;
		}
		return true;
	}
</script>
";