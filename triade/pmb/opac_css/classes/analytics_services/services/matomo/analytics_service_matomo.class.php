<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: analytics_service_matomo.class.php,v 1.2 2023/08/17 09:47:56 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class analytics_service_matomo {

	public static function get_label() {
		return "Matomo";
	}

	public static function get_parameters_content_form($parameters=array()) {

		return '
		<div class="row">
			<label class="etiquette" for="analytics_service_parameters_site_url">site_url</label>
		</div>
		<div class="row">
			<input type="text" class="saisie-40em" id="analytics_service_parameters_site_url" name="analytics_service_parameters[site_url]" value="'.(!empty($parameters['site_url']) ? $parameters['site_url'] : '').'" />
		</div>
		<div class="row">
			<label class="etiquette" for="analytics_service_parameters_site_id">site_id</label>
		</div>
		<div class="row">
			<input type="text" class="saisie-40em" id="analytics_service_parameters_site_id" name="analytics_service_parameters[site_id]" value="'.(!empty($parameters['site_id']) ? $parameters['site_id'] : '').'" />
		</div>';

	}

	public static function get_default_template() {
		return "
		<script>
		  var _paq = _paq || [];
		  _paq.push(['trackPageView']);
		  _paq.push(['enableLinkTracking']);
		  (function() {
		    var u='{{ site_url }}';
		    _paq.push(['setTrackerUrl', u+'piwik.php']);
		    _paq.push(['setSiteId', {{ site_id }}]);
		    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
		    g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
		  })();
		</script>
		";
	}

	public static function get_default_consent_template() {
		return "
		<script>
	        tarteaucitron.user.matomoId = {{ site_id }};
	        (tarteaucitron.job = tarteaucitron.job || []).push('matomo');
        </script>";
	}



}