<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart.tpl.php,v 1.4 2023/08/17 09:47:52 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $include_path;
global $cart_integrate_anonymous_on_confirm;

$cart_integrate_anonymous_on_confirm = "
<script src='".$include_path."/javascript/http_request.js'></script>
<script>
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