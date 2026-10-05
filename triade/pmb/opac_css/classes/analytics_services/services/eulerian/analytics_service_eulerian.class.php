<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: analytics_service_eulerian.class.php,v 1.1.4.2 2025/05/06 14:39:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class analytics_service_eulerian {
	
	public static function get_label() {
		return "Eulerian";
	}
	
	public static function get_parameters_content_form($parameters=array()) {
		
		return '
		<div class="row">
			<label class="etiquette" for="analytics_service_parameters_domain">domain</label>
		</div>
		<div class="row">
			<input type="text" class="saisie-40em" id="analytics_service_parameters_domain" name="analytics_service_parameters[domain]" value="'.(!empty($parameters['domain']) ? $parameters['domain'] : '').'" />
		</div>';

	}
	
	public static function get_default_template() {
		return "
        <!-- Tag standard - Eulerian Analytics  -->
        <script>
            (function(e,a){var i=e.length,y=5381,k='script',s=window,v=document,o=v.createElement(k);for(;i;){i-=1;y=(y*33)^e.charCodeAt(i)}y='_EA_'+(y>>>=0);(function(e,a,s,y){s[a]=s[a]||function(){(s[y]=s[y]||[]).push(arguments);s[y].eah=e;};}(e,a,s,y));i=new Date/1E7|0;o.ea=y;y=i%26;o.async=1;o.src='//'+e+'/'+String.fromCharCode(97+y,122-y,65+y)+(i%1E3)+'.js?2';s=v.getElementsByTagName(k)[0];s.parentNode.insertBefore(o,s);})
            ('{{ domain }}','EA_push');
        </script>
        <script>
            (function() {
                window.EA_datalayer = [];
                
                //window.EA_datalayer.push('uid', '{% if session.vars.id_empr %}{{session_vars.empr_login}}{% endif %}');
                window.EA_datalayer.push('pagegroup', '{{page.type_page | escape}} | {{page.subtype_page | escape}}');
                let h1 = document.querySelector('h1');
	           	if (h1) {
                    window.EA_datalayer.push('path', h1.innerText);
                    window.EA_datalayer.push('page_title', h1.innerText);
                } else {
                    window.EA_datalayer.push('path', '{{page.title | escape}}');
                    window.EA_datalayer.push('page_title', '{{page.title | escape}}');
                }
                window.EA_push(window.EA_datalayer);
                {% if post_vars.user_query %}
                    window.EA_datalayer.push('isearchengine', '{{ post_vars.user_query | escape }}');
                    window.EA_datalayer.push('isearchresults', '0');
                {% endif %}
                //tracking des liens et des boutons
                analyticsSerciceAddEvents('EA_push');
            })();
        </script>
		";
	}
	
	public static function get_default_consent_template() {
		return "
		<script>
	        tarteaucitron.services.eulerian = {
    	        'key': 'eulerian',
    	        'type': 'analytic',
    	        'name': 'Eulerian Analytics',
    	        'needConsent': true,
    	        'cookies': ['etuix'],
    	        'uri' : '{{ domain }}',
    	        'js': function () {
    	        'use strict';
    	        (function(x,w){ if (!x._ld){ x._ld = 1;
    	        let ff = function() { if(x._f){x._f('tac',tarteaucitron,1)} };
    	        w.__eaGenericCmpApi = function(f) { x._f = f; ff(); };
    	        w.addEventListener('tac.close_alert', ff);
    	        w.addEventListener('tac.close_panel', ff);
    	        }})(this,window);
    	        },
    	        'fallback': function () { this.js(); }
    	    };
            (tarteaucitron.job = tarteaucitron.job || []).push('eulerian');
        </script>";
	}
}