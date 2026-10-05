<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facette.tpl.php,v 1.16.4.1 2025/02/24 15:36:46 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $tpl_js_form_facette, $tpl_form_facette_authperso_selector, $pmb_opac_view_activate, $msg, $current_module, $categ, $charset;

$tpl_js_form_facette= "
<script type='text/javascript' src='./javascript/http_request.js'></script>
<script type='text/javascript'>
    function load_subfields(id_ss_champs){
		var xhr_object=  new http_request();					
		xhr_object.request('./ajax.php?module=$current_module&categ=$categ&sub=lst_!!sub!!&type=!!type!!',true,\"list_crit=\"+document.getElementById('list_crit').value+\"&sub_field=\"+id_ss_champs+\"&authperso_id=\"+(document.getElementById('list_authperso') ? document.getElementById('list_authperso').value : 0),'true',cback,0,0)
	}
	
	function cback(response){						
		var div = document.getElementById('liste2');
		div.innerHTML = response;
	}
	
	function load_authperso_fields(id_champs){
		var xhr_object=  new http_request();					
		xhr_object.request('./ajax.php?module=$current_module&categ=$categ&sub=lst_fields_!!sub!!&type=!!type!!',true,\"authperso_id=\"+document.getElementById('list_authperso').value+\"&facet_field=\"+id_champs,'true',authpersoCallBack,0,0)
	}
	
	function authpersoCallBack(response){
		var div = document.getElementById('list_fields');
		div.innerHTML = response;
	}
</script>
";

$tpl_form_facette_authperso_selector = "
    <div class='row'>
		<label for='list_authperso'>".htmlentities($msg['authperso'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='row'>
        <select name='authperso_id' id='list_authperso' onchange='load_authperso_fields(0)'>
		  !!authperso_options!!
        </select>
	</div>
";