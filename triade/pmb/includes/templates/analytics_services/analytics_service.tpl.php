<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: analytics_service.tpl.php,v 1.3 2023/07/07 14:54:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg;
global $analytics_service_calculate_button;

$analytics_service_calculate_button = "
<div class='row'>&nbsp;</div>
<div class='row' id='analytics_service_calculate'>
	<button data-dojo-type='dijit/form/Button'>
	".$msg['analytics_service_calculate_template']."
		<script type='dojo/on' data-dojo-event='click'>
			require(['dojo/request/xhr', 'dojo/dom-form', 'dojo/topic'], function(xhr, domForm, topic){
				xhr.post('./ajax.php?module=admin&categ=opac&sub=analytics_services&action=get_templates',
				 	{
						handleAs: 'json',
						data: domForm.toObject('analytics_service_form')
					}
				).then(function(response){
					if(response) {
						if(document.getElementById('analytics_service_template')) {
							document.getElementById('analytics_service_template').innerHTML = response['template'];
						}
						if(document.getElementById('analytics_service_consent_template')) {
							document.getElementById('analytics_service_consent_template').innerHTML = response['consent_template'];
						}
					}
				});
			});
		</script>
	</button>
</div>";
