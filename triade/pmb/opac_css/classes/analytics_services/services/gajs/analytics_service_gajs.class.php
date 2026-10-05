<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: analytics_service_gajs.class.php,v 1.2 2023/08/17 09:47:56 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class analytics_service_gajs {

	public static function get_label() {
		return "Google Analytics";
	}

	public static function get_parameters_content_form($parameters=array()) {

		return '
		<div class="row">
			<label class="etiquette" for="analytics_service_parameters_gajsUa">gajsUa</label>
		</div>
		<div class="row">
			<input type="text" class="saisie-40em" id="analytics_service_parameters_gajsUa" name="analytics_service_parameters[gajsUa]" value="'.(!empty($parameters['gajsUa']) ? $parameters['gajsUa'] : '').'" />
		</div>';

	}

	public static function get_default_template() {
		return "
		<script>
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', '{{ gajsUa }}']);
			_gaq.push(['_trackPageview']);

			(function() {
			    var ga = document.createElement('script');
			    ga.async = true;
			    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			    var s = document.getElementsByTagName('script')[0];
			    s.parentNode.insertBefore(ga, s);
			})();

			// your optionnal _ga.push()
		</script>
		";
	}

	public static function get_default_consent_template() {
		return "
		<script>
	        tarteaucitron.user.gajsUa = '{{ gajsUa }}';
	        tarteaucitron.user.gajsMore = function () { /* add here your optionnal _ga.push() */ };
	        (tarteaucitron.job = tarteaucitron.job || []).push('gajs');
        </script>";
	}
}