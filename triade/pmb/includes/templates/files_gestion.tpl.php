<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: files_gestion.tpl.php,v 1.8 2022/06/17 15:06:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $files_gestion_js_script_list;

$files_gestion_js_script_list = "
<script>
var flag_mouseover_info_div = false;
		
function show_div_img(event,img) {
	if (flag_mouseover_info_div == true) {
		return true;
	}	
	
	flag_mouseover_info_div = true;
	var pos=getCoordinate(event);
	posxdown=pos[0];
	posydown=pos[1];
	var pannel=document.createElement('div');
	pannel.setAttribute('id','div_img');
	pannel.style.top=(posydown-50)+'px';
	pannel.style.left=posxdown+'px';
	pannel.style.border='#000000 solid 1px';
	pannel.style.position='absolute';
	pannel.style.background='#FFFFFF';
	
	pannel.style.zIndex=1500;
	document.body.appendChild(pannel);
	pannel.innerHTML='<img src=\"' + img + '\" alt=\"\" />';
	
	return true;
}
		
function hide_div_img() {
	var pannel=document.getElementById('div_img');
	if (pannel) {
		pannel.parentNode.removeChild(pannel);
		flag_mouseover_info_div = false;
	}
}
</script>
";
