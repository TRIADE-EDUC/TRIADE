<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: analytics_service_xiti.class.php,v 1.1 2021/07/21 09:45:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class analytics_service_xiti {
	
	public static function get_label() {
		return "Xiti";
	}
	
	public static function get_parameters_content_form($parameters=array()) {
		
		return '
		<div class="row">
			<label class="etiquette" for="analytics_service_parameters_userID">userID</label>
		</div>
		<div class="row">
			<input type="text" class="saisie-40em" id="analytics_service_parameters_userID" name="analytics_service_parameters[userID]" value="'.(!empty($parameters['userID']) ? $parameters['userID'] : '').'" />
		</div>';
		
	}
	
	public static function get_default_template() {
		return "
		<script type='text/javascript'>
		  	Xt_param = 's={{ userID }}&p=';
			try {Xt_r = top.document.referrer;}
			catch(e) {Xt_r = document.referrer; }
			Xt_h = new Date();
			Xt_i = '<img width=\"39\" height=\"25\" border=\"0\" alt=\"\" ';
			Xt_i += 'src=\"http://logv3.xiti.com/hit.xiti?'+Xt_param;
			Xt_i += '&hl='+Xt_h.getHours()+'x'+Xt_h.getMinutes()+'x'+Xt_h.getSeconds();
			if(parseFloat(navigator.appVersion)>=4) {Xt_s=screen;Xt_i+='&r='+Xt_s.width+'x'+Xt_s.height+'x'+Xt_s.pixelDepth+'x'+Xt_s.colorDepth;}
			document.write(Xt_i+'&ref='+Xt_r.replace(/[<>\"]/g, '').replace(/&/g, '$')+'\" title=\"Internet Audience\">');
		</script>
		";
	}
	
	public static function get_default_consent_template() {
		return "
		<script type='text/javascript'>
	        tarteaucitron.user.xitiId = '{{ userID }}';
	        tarteaucitron.user.xitiMore = function () { /* add here your optionnal xiti function */ };
	        (tarteaucitron.job = tarteaucitron.job || []).push('xiti');
        </script>";
	}
	
}