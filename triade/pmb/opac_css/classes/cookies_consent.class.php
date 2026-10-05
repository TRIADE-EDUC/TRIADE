<?php
use Pmb\Common\Helper\Portal;

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cookies_consent.class.php,v 1.9.4.1 2025/04/30 08:22:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/analytics_services/analytics_services.class.php");
require_once($include_path."/h2o/h2o.php");

class cookies_consent {

	public function __construct(){
	}
	
	protected static function get_orientation() {
		global $opac_cookies_consent_orientation;
		
		if($opac_cookies_consent_orientation) {
			if(in_array($opac_cookies_consent_orientation, array('top', 'middle', 'bottom', 'popup'))) {
				return $opac_cookies_consent_orientation;
			}
		}
		return "bottom";
	}
	
	public static function get_initialization() {
	    global $opac_cookies_consent_show_icon, $opac_url_more_about_cookies, $opac_cookies_consent_dsfr;
		
		return '
		<script>
	        tarteaucitron.init({
	    	  "privacyUrl": "", /* Privacy policy url */
	
	    	  "hashtag": "#PhpMyBibli-COOKIECONSENT", /* Open the panel with this hashtag */
	    	  "cookieName": "PhpMyBibli-COOKIECONSENT", /* Cookie name */
	    
	    	  "orientation": "'.static::get_orientation().'", /* Banner position (top - bottom - popup) */
	       
	          "groupServices": true, /* Group services by category */
	                           
	    	  "showAlertSmall": false, /* Show the small banner on bottom right */
	    	  "cookieslist": false, /* Show the cookie list */
				                           
	          "closePopup": false, /* Show a close X on the banner */
	
	          "showIcon": '.($opac_cookies_consent_show_icon ? 'true' : 'false').', /* Show cookie icon to manage cookies */
	          "iconSrc": "'.get_url_icon('cookie.png').'", /* Optionnal: URL or base64 encoded image */
	          "iconPosition": "BottomRight", /* BottomRight, BottomLeft, TopRight and TopLeft */
	
	    	  "adblocker": false, /* Show a Warning if an adblocker is detected */
	                           
	          "DenyAllCta" : true, /* Show the deny all button */
	          "AcceptAllCta" : true, /* Show the accept all button when highPrivacy on */
	          "highPrivacy": true, /* HIGHLY RECOMMANDED Disable auto consent */
	                           
	    	  "handleBrowserDNTRequest": false, /* If Do Not Track == 1, disallow all */
	
	    	  "removeCredit": true, /* Remove credit link */
	    	  "moreInfoLink": '.($opac_url_more_about_cookies ? 'true' : 'false').', /* Show more info link */
	
	          "useExternalCss": '.($opac_cookies_consent_dsfr ? 'true' : 'false').', /* If false, the tarteaucitron.css file will be loaded */
	          "useExternalJs": false, /* If false, the tarteaucitron.js file will be loaded */
				
	    	  //"cookieDomain": ".my-multisite-domaine.fr", /* Shared cookie for multisite */
	                          
	          "readmoreLink": "'.$opac_url_more_about_cookies.'", /* Change the default readmore link */
	
	          "mandatory": true, /* Show a message about mandatory cookies */
	        });
 
        </script>';
	}
	
	public static function get_display_custom_service($key, $name, $type) {
		global $opac_url_base, $opac_url_more_about_cookies;
		
		return '
		<script>
		tarteaucitron.services.'.$key.' = {
		  	"key": "'.$key.'",
			"type": "'.$type.'",
			"name": "'.$name.'",
			"needConsent": true,
			"cookies": [],
			"uri": "'.$opac_url_base.'",
			"readmoreLink": "'.$opac_url_more_about_cookies.'", // If you want to change readmore link
			"js": function () {
			    "use strict";
			    // When user allow cookie
		  	},
		  	"fallback": function () {
		    	"use strict";
		    	// when use deny cookie
		  	}
		};
		</script>
		<script>
        	(tarteaucitron.job = tarteaucitron.job || []).push("'.$key.'");
        </script>';
	}
	
	public static function get_display_custom_services() {
		global $msg, $pmb_logs_activate;
		
		$display = '';
		if($pmb_logs_activate) {
			$display .= static::get_display_custom_service('pmbstatopac', $msg['cookie_stats'], 'analytic');
		}
		return $display;
	}
	
	public static function get_display_service($name) {
		return '
		 <script>
        	(tarteaucitron.job = tarteaucitron.job || []).push("'.$name.'");
        </script>
		';
	}
	
	public static function get_display_services() {
		global $opac_script_analytics, $opac_show_social_network, $opac_param_social_network;
		
		$display = '';
		if ($opac_script_analytics) {
			if (strpos($opac_script_analytics, "google-analytics.com") !== false) {
				$display .= static::get_display_service('gajs');
			}
			if (strpos($opac_script_analytics, "googletagmanager.com") !== false) {
				$display .= static::get_display_service('gtag');
			}
			if (strpos($opac_script_analytics, "matomo") !== false) {
				$display .= static::get_display_service('matomo');
			}
			if (strpos($opac_script_analytics, "xiti") !== false) {
				$display .= static::get_display_service('xiti');
			}
		}
		if ($opac_show_social_network) {
			if(strpos($opac_param_social_network, 'addthis') !== false) {
				$display .= static::get_display_service('addthis');
			}
		}
		$display .= analytics_services::get_display_services();
		
		// Passage H2o
		return static::parse_H2o($display);
	}
	
	public static function parse_H2o($display) {
        $datas = [];
        $datas['get_vars'] = $_GET;
        $datas['post_vars'] = $_POST;
        $type_page = Portal::getTypePage();
        $type_page_label = Portal::getLabel($type_page);
        $subtype_page = Portal::getSubTypePage();
        $subtype_page_label = Portal::getLabel($subtype_page);
        $datas['page'] = [
            'title' => ($subtype_page_label ? $subtype_page_label : $type_page_label),
            'type_page' => $type_page_label,
            'subtype_page' => $subtype_page_label
        ];
        try{
            $html = H2o::parseString($display)->render($datas);
        }catch(Exception $e){
            $html = '<!-- '.$e->getMessage().' -->';
            $html .= '<div class="error_on_template" title="' . htmlspecialchars($e->getMessage(), ENT_QUOTES) . '">';
            $html .= '</div>';
        }
        return $html;
	}
	
	public static function is_opposed_service($name) {
		global $opac_cookie_consent;
		
		if($opac_cookie_consent == 2) {
			//On reste sur l'ancien fonctionnement
			if ($_COOKIE['PhpMyBibli-COOKIECONSENT'] == "false") {
				return true;
			}
		} else {
			//nouvelle gestion des cookies
			if (!empty($_COOKIE['PhpMyBibli-COOKIECONSENT']) && strpos($_COOKIE['PhpMyBibli-COOKIECONSENT'], "$name=false") !== false) {
				return true;
			}
		}
		return false;
	}
	
	public static function is_opposed_pmb_logs_service() {
		return static::is_opposed_service('pmbstatopac');
	}
	
	public static function is_opposed_addthis_service() {
		global $opac_cookie_consent;
		
		if($opac_cookie_consent == 2) {
			//On reste sur l'ancien fonctionnement
			if((!isset($_COOKIE['PhpMyBibli-COOKIECONSENT']) || $_COOKIE['PhpMyBibli-COOKIECONSENT'] == "" || $_COOKIE['PhpMyBibli-COOKIECONSENT'] == "false")) {
				return true;
			}
		} else {
			//nouvelle gestion des cookies			
			if((!isset($_COOKIE['PhpMyBibli-COOKIECONSENT']) || strpos($_COOKIE['PhpMyBibli-COOKIECONSENT'], "addthis") === false || strpos($_COOKIE['PhpMyBibli-COOKIECONSENT'], "addthis=wait") !== false || strpos($_COOKIE['PhpMyBibli-COOKIECONSENT'], "addthis=false") !== false)) {
				return true;
			}
		}
		return false;
	}
}


