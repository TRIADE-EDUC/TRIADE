<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: thumbnail.tpl.php,v 1.6.14.1.2.2 2025/02/28 14:05:08 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) {
    die("no access");
}

global $pmb_curl_timeout, $js_function_chklnk_tpl, $pmb_url_base;

$js_function_chklnk_tpl = "
<script type='text/javascript'>
var tabTokens_thumbnail = !!tokens_csrf!!;
function chklnk_f_thumbnail_url(element){
	if(element.value != ''){
		var url=element.value;
		var thisRegex = new RegExp('^[a-zA-Z0-9_]+\.php','g');
		var flagPhp=false;
		if(thisRegex.test(url)){
			url = '".(isset($pmb_url_base) ? $pmb_url_base : '')."/'+url;
			flagPhp=true;
		}
		var wait = document.createElement('img');
		wait.setAttribute('src','".get_url_icon('patience.gif')."');
		wait.setAttribute('align','top');
		while(document.getElementById('f_thumbnail_check').firstChild){
			document.getElementById('f_thumbnail_check').removeChild(document.getElementById('f_thumbnail_check').firstChild);
		}
		document.getElementById('f_thumbnail_check').appendChild(wait);
		var csrf_token = tabTokens_thumbnail[0];
		tabTokens_thumbnail.splice(0, 1);
		var testlink = encodeURIComponent(url);
		var req = new XMLHttpRequest();
		req.open('GET', './ajax.php?module=ajax&categ=chklnk&timeout=".$pmb_curl_timeout."&link='+testlink+'&csrf_token='+csrf_token, true);
		req.onreadystatechange = function (aEvt) {
			if (req.readyState == 4) {
				if(req.status == 200){
					var img = document.createElement('img');
					var src='';
					var type_status=req.responseText.substr(0,1);
					if(type_status == '2' || type_status == '3'){
						if(!flagPhp){
							if((element.value.substr(0,7) != 'http://') && (element.value.substr(0,8) != 'https://')) element.value = 'http://'+element.value;
						}
						//impec, on print un petit message de confirmation
						src = '".get_url_icon('tick.gif')."';
					}else{
					//problème...
						src = '".get_url_icon('error.png')."';
						img.setAttribute('style','height:1.5em;');
					}
					img.setAttribute('src',src);
					img.setAttribute('align','top');
					while(document.getElementById('f_thumbnail_check').firstChild){
						document.getElementById('f_thumbnail_check').removeChild(document.getElementById('f_thumbnail_check').firstChild);
					}
					document.getElementById('f_thumbnail_check').appendChild(img);
				} else {
					var img = document.createElement('img');
					var src='';
					//problème...
					src = '".get_url_icon('error.png')."';
					img.setAttribute('style','height:1.5em;');
					img.setAttribute('src',src);
					img.setAttribute('align','top');
					while(document.getElementById('f_thumbnail_check').firstChild){
						document.getElementById('f_thumbnail_check').removeChild(document.getElementById('f_thumbnail_check').firstChild);
					}
					document.getElementById('f_thumbnail_check').appendChild(img);
				}
			}
		};
		req.send(null);
	}
}
</script>";
