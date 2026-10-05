<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: analytics_service_gtag.class.php,v 1.1 2021/07/21 09:45:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class analytics_service_gtag {
	
	public static function get_label() {
		return "Google Analytics";
	}
	
	public static function get_parameters_content_form($parameters=array()) {
		
		return '
		<div class="row">
			<label class="etiquette" for="analytics_service_parameters_gtagUa">gtagUa</label>
		</div>
		<div class="row">
			<input type="text" class="saisie-40em" id="analytics_service_parameters_gtagUa" name="analytics_service_parameters[gtagUa]" value="'.(!empty($parameters['gtagUa']) ? $parameters['gtagUa'] : '').'" />
		</div>';
		
	}
	
	public static function get_default_template() {
		return "
		<script async src='https://www.googletagmanager.com/gtag/js?id={{ gtagUa }}'></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());
		
		  gtag('config', '{{ gtagUa }}');
		
		  // your optionnal gtag()
		</script>
		";
	}
	
	public static function get_default_consent_template() {
		return "
		<script type='text/javascript'>
	        tarteaucitron.user.gtagUa = '{{ gtagUa }}';
	        // tarteaucitron.user.gtagCrossdomain = ['example.com', 'example2.com'];
	        tarteaucitron.user.gtagMore = function () { /* add here your optionnal gtag() */ };
	        (tarteaucitron.job = tarteaucitron.job || []).push('gtag');
        </script>";
	}
	
}