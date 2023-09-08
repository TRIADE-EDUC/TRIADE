<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart.tpl.php,v 1.3 2019-05-29 11:23:32 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $include_path;
global $cart_integrate_anonymous_on_confirm;

$cart_integrate_anonymous_on_confirm = "
<script type='text/javascript' src='".$include_path."/javascript/http_request.js'></script>
<script type='text/javascript'>
	window.addEventListener('load', function(){
		var cart_request= new http_request();
		if(confirm('!!cart_confirm_message!!')){
			cart_request.request('./ajax.php?module=ajax&categ=cart&action=!!cart_ajax_action!!', false, '', true, function(){
				window.location.reload();
			});
		}else{
			cart_request.request('./ajax.php?module=ajax&categ=cart&action=purge_cart', false, '', true, '');	
		}
	}, false);
</script>
";