<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: common.class.php,v 1.19.2.1 2024/05/22 08:00:07 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Common\Helper\Portal;

class common {

	protected static $levels = [];

	/**
	 * Retourne les metadonnees pour securiser la page web
	 *
	 * @return string
	 */
	public static function get_securities_metadata() {
		global $opac_content_security_policy;

		$opac_content_security_policy = str_replace('"', "'", $opac_content_security_policy ?? "");
		$opac_content_security_policy = trim($opac_content_security_policy);

		return '<meta http-equiv="Content-Security-Policy" content="'. $opac_content_security_policy .'">';
	}

	public static function get_metadata() {
		global $msg, $charset, $lvl, $opac_biblio_name;
		global $opac_meta_author, $opac_meta_keywords, $opac_meta_description;

		$metadata = common::get_securities_metadata();
		$metadata .= "
			<meta charset=\"".$charset."\" />
			<meta name=\"author\" content=\"".($opac_meta_author?htmlentities($opac_meta_author,ENT_QUOTES,$charset):"PMB Group")."\" />

			<meta name=\"keywords\" content=\"".($opac_meta_keywords?htmlentities($opac_meta_keywords,ENT_QUOTES,$charset):$msg['opac_keywords'])."\" />
			<meta name=\"description\" content=\"".($opac_meta_description?htmlentities($opac_meta_description,ENT_QUOTES,$charset):$msg['opac_title']." $opac_biblio_name.")."\" />";

		switch ($lvl) {
			case 'contribution_area':
				$metadata .= "<meta name='robots' content='all' />";
				break;
			case 'show_cart':
				$metadata .= "<meta name='robots' content='noindex, nofollow' />";
				break;
			default :
				$metadata .= "<meta name='robots' content='all' />";
				break;
		}

		$metadata.="
			<!--IE et son enfer de compatibilité-->
			<meta http-equiv='X-UA-Compatible' content='IE=Edge' />
			<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, maximum-scale=3\" />";
		return $metadata;
	}

	public static function get_formatted_page_title($title) {
	    global $msg, $charset;
	    global $opac_rgaa_active;
	    global $opac_biblio_name;

	    if($opac_rgaa_active && !empty($msg['rgaa_title_format'])) {
	        $page_title = $msg['rgaa_title_format'];
	        $page_title = str_replace('!!page_title!!', html_entity_decode(strip_tags($title), ENT_QUOTES, $charset), $page_title);
	        $page_title = str_replace('!!biblio_name!!', $opac_biblio_name, $page_title);
	        return $page_title;
	    } else {
	        return html_entity_decode(strip_tags($title), ENT_QUOTES, $charset).' '.$opac_biblio_name;
	    }
	}

	public static function get_html_title() {
		global $msg, $lvl;
		global $opac_rgaa_active;

		switch ($lvl) {
			case 'contribution_area':
				return static::get_formatted_page_title($msg['empr_menu_contribution_area']);
			default :
			    if($opac_rgaa_active) {
			        $subTypePage = Portal::getSubTypePage();
			        $label = Portal::getLabel($subTypePage);
			        if(empty($label)) {
			            $typePage = Portal::getTypePage();
			            $label = Portal::getLabel($typePage);
			        }
			        if(!empty($label)) {
			            return static::get_formatted_page_title($label);
			        }
			    }
			    return static::get_formatted_page_title($msg['opac_title']);
		}
	}

	public static function format_hidden_title($title='') {
	    global $charset;
	    global $opac_rgaa_active;

	    if($opac_rgaa_active) {
	        if(!empty($title)) {
    	        $level = count(static::$levels)+1;
    	        if($level == 1) {
    	            static::$levels[$level] = $title;
    	            return "<h1 class='visually-hidden'>".htmlentities($title, ENT_QUOTES, $charset)."</h1>";
    	        } else {
    	            return "<h2 class='visually-hidden'>".htmlentities($title, ENT_QUOTES, $charset)."</h2>";
    	        }
	        } else {
	            $subTypePage = Portal::getSubTypePage();
	            $label = Portal::getLabel($subTypePage);
	            if(empty($label)) {
	                $typePage = Portal::getTypePage();
	                $label = Portal::getLabel($typePage);
	            }
	            if(!empty($label)) {
	                $level = count(static::$levels)+1;
	                static::$levels[$level] = $label;
	                return "<h1 class='visually-hidden'>".htmlentities($label, ENT_QUOTES, $charset)."</h1>";
	            }
	        }
	    }
	    return '';
	}

	protected static function get_formatted_attributes_title() {
        $typePage = Portal::getTypePage();
        $typePage = intval($typePage);
        switch ($typePage) {
            case 3: // Résultats notices
            case 4: // Résultats autorités
                return "class='searchResult-title'";
            case 8:
                return "class='title_basket'";
            case 9: // Historique
                return "class='title_history'";
            case 31: // Formulaire de contact
                return "class='contact_form_title'";
        }
        return '';
	}

	protected static function get_formatted_attributes_span_title($type='') {
	    switch ($type) {
	        case 'categories':
	            return "id='titre_categories'";
	        default:
	            $typePage = Portal::getTypePage();
	            $typePage = intval($typePage);
	            switch ($typePage) {
	                case 3: // Résultats notices
	                case 4: // Résultats autorités
	                    if($type == 'equation') {
	                        return "class='searchResult-equation'";
	                    }
	            }
	    }
	    return '';
	}

	protected static function get_formatted_title($title, $allow_html=false, $type='', $level=1) {
	    global $charset;

	    $formatted_attributes_title = static::get_formatted_attributes_title();
	    $formatted_attributes_span_title = static::get_formatted_attributes_span_title($type);
	    return "<h".$level." ".$formatted_attributes_title."><span ".$formatted_attributes_span_title.">".($allow_html ? $title : htmlentities($title, ENT_QUOTES, $charset))."</span></h".$level.">";
	}

	public static function format_title($title, $allow_html=false, $type='', $header_title=false) {
	    global $charset;
	    global $opac_rgaa_active;

	    if($opac_rgaa_active) {
	        $level = count(static::$levels)+1;
	        if($level == 1) {
	           static::$levels[$level] = $title;
	           return static::get_formatted_title($title, $allow_html, $type, $level);
	        } else {
	            return static::format_subtitle($title, $allow_html, $type);
	        }
	    } else {
	        return static::get_formatted_title($title, $allow_html, $type, 3);
	    }
	}

	public static function format_subtitle($title, $allow_html=false, $type='') {
	    return static::get_formatted_title($title, $allow_html, $type, 2);
	}

	public static function get_levels() {
	    return static::$levels;
	}

	public static function add_level($title) {
	    $level = count(static::$levels)+1;
	    if($level == 1) {
	        static::$levels[$level] = $title;
	    }
	}


	public static function get_dojo_configuration() {
		global $javascript_path, $lang, $messages_last_modified;

		return "
		<link rel='stylesheet' type='text/css' href='".$javascript_path."/dojo/dijit/themes/tundra/tundra.css' />
		<script>
			var dojoConfig = {
				parseOnLoad: true,
				locale: '".str_replace("_","-",strtolower($lang))."',
				isDebug: false,
				usePlainJson: true,
				packages: [{
						name: 'pmbBase',
						location:'../../../..'
					},{
						name: 'd3',
						location:'../../d3'
					}],
				deps: ['apps/pmb/MessagesStore', 'dgrowl/dGrowl', 'dojo/ready', 'apps/pmb/ImagesStore'],
				callback:function(MessagesStore, dGrowl, ready, ImagesStore){
					window.pmbDojo = {};
					pmbDojo.messages = new MessagesStore({url:'./ajax.php?module=ajax&categ=messages', directInit:false, lastModified:'".$messages_last_modified."'});
					pmbDojo.images = new ImagesStore({url:'./ajax.php?module=ajax&categ=images', directInit:false});
					ready(function(){
						new dGrowl({'channels':[{'name':'info','pos':2},{'name':'error', 'pos':1}]});
					});

				},
			};
		</script>
		<script src='".$javascript_path."/dojo/dojo/dojo.js'></script>
		<script>
		dojo.addOnLoad(function () {
			// Ajout du theme Dojo
			dojo.addClass(dojo.body(),'tundra');
		})
		</script>
		";
	}

	public static function get_script_analytics() {
		global $msg, $include_path;
		global $opac_script_analytics, $opac_cookies_consent, $opac_url_more_about_cookies, $opac_show_social_network, $pmb_logs_activate;
		global $opac_cookies_consent_dsfr;
		
		$script_analytics = '';
		if($opac_cookies_consent == 2) {
			//On reste sur l'ancien fonctionnement
			if (!isset($_COOKIE['PhpMyBibli-COOKIECONSENT']) || !$_COOKIE['PhpMyBibli-COOKIECONSENT']) {
				if ($opac_cookies_consent && ($opac_script_analytics || $opac_show_social_network || $pmb_logs_activate)) {
					$script_analytics .= "
					<script>
						var msg_script_analytics_content = '".addslashes($msg["script_analytics_content"])."';
						var msg_script_analytics_inform_ask_opposite = '".addslashes($msg["script_analytics_inform_ask_opposite"])."';
						var msg_script_analytics_inform_ask_accept = '".addslashes($msg["script_analytics_inform_ask_accept"])."';
			            var msg_script_analytics_button_dnt_confirm = '".addslashes($msg["script_analytics_button_dnt_confirm"])."';
					";
					if ($opac_url_more_about_cookies) {
						$script_analytics .= "
						var script_analytics_content_link_more = '".$opac_url_more_about_cookies."';
						var script_analytics_content_link_more_msg = '".addslashes($msg["script_analytics_content_link_more"])."';";
					} else {
						$script_analytics .= "	var script_analytics_content_link_more = '';
						var script_analytics_content_link_more_msg = '';";
					}
					$script_analytics .= "
					</script>
					<script src='".$include_path."/javascript/script_analytics.js'></script>
					<script>
						scriptAnalytics.CookieConsent.start();
					</script>
					";
				}
			}
			if (!empty($_COOKIE['PhpMyBibli-COOKIECONSENT']) && $_COOKIE['PhpMyBibli-COOKIECONSENT'] != "false") {
				if ($opac_script_analytics) {
					eval("\$opac_script_analytics=\"".str_replace("\"","\\\"",$opac_script_analytics)."\";");
					$script_analytics .= $opac_script_analytics;
				}
			}
		} elseif ($opac_cookies_consent) {
			//nouvelle gestion des cookies
			//on commence par la surcharge des messages
			//utile pour la substitution et pour le charset ISO-8859-1
			$script_analytics .= '
			<script>
				var tarteaucitron_messages = pmbDojo.messages.getMessages("tarteaucitron");
				if(tarteaucitron_messages.length) {
					tarteaucitronCustomText = {};
					tarteaucitron_messages.forEach(function(message) {
						if(parseInt(message.code.indexOf(":")) !== -1) {
							let tarteaucitron_messages_group = message.code.split(":");
							if(typeof tarteaucitronCustomText[tarteaucitron_messages_group[0]] == "undefined") {
								tarteaucitronCustomText[tarteaucitron_messages_group[0]] = {};
							}
							tarteaucitronCustomText[tarteaucitron_messages_group[0]][tarteaucitron_messages_group[1]] = message.message;
						} else {
							tarteaucitronCustomText[message.code] = message.message;
						}
					});
				}
			</script>
			';
			if ($opac_cookies_consent_dsfr) {
			    $script_analytics .= '<script src="'.$include_path.'/javascript/dsfr-tarteaucitron/tarteaucitron.js"></script>';
			} else {
			    $script_analytics .= '<script src="'.$include_path.'/javascript/tarteaucitron/tarteaucitron.js"></script>';
			}
			$script_analytics .= cookies_consent::get_initialization();
			$script_analytics .= cookies_consent::get_display_custom_services();
			$script_analytics .= cookies_consent::get_display_services();
			if ($opac_script_analytics) {
				eval("\$opac_script_analytics=\"".str_replace("\"","\\\"",$opac_script_analytics)."\";");
				$script_analytics .= $opac_script_analytics;
			}
		}
		return $script_analytics;
	}

	public static function get_js_function_encode_url() {
		return "<script>
			// Fonction a utiliser pour l'encodage des URLs en javascript
			function encode_URL(data){
				var docCharSet = document.characterSet ? document.characterSet : document.charset;
				if(docCharSet == \"UTF-8\"){
	    			return encodeURIComponent(data);
	    		}else{
	    			return escape(data);
	    		}
	    	}
	    </script>";
	}

	public static function get_js_script_social_network() {
		global $opac_cookies_consent, $opac_show_social_network, $opac_param_social_network;

		$js_script = '';

		//Opposition à l'utilisation des cookies, désactivation des partages sur les réseaux sociaux
		if ($opac_cookies_consent && cookies_consent::is_opposed_addthis_service()) {
			$opac_show_social_network = 0;
		}

		$js_script .= "<script>
			var opac_show_social_network =$opac_show_social_network;
		</script>";

		if (!empty($opac_show_social_network) && !empty($opac_param_social_network)) {
			$addThisParams = json_decode($opac_param_social_network);
			//ra-4d9b1e202c30dea1
			if (!empty($addThisParams->addthis_share)) {
				$js_script .= "<script>var addthis_share = ".json_encode($addThisParams->addthis_share).";</script>";
			}
			$js_script .= "<script>var addthis_config = ".json_encode($addThisParams->addthis_config).";</script>
				<script src='https://s7.addthis.com/js/$addThisParams->version/addthis_widget.js#pubid=$addThisParams->token'></script>";
		}
		return $js_script;
	}

	public static function get_js_function_record_display() {
		return "
		<script>
		function findNoticeElement(id){
			var ul=null;
			//cas des notices classiques
			var domNotice = document.getElementById('el'+id+'Child');
			//notice_display
			if(!domNotice) domNotice = document.getElementById('notice');
			if(domNotice){
				var uls = domNotice.getElementsByTagName('ul');
				for (var i=0 ; i<uls.length ; i++){
					if(uls[i].getAttribute('id') == 'onglets_isbd_public'+id){
						var ul = uls[i];
						break;
					}
				}
			} else{
				var li = document.getElementById('onglet_isbd'+id);
				if(!li) var li = document.getElementById('onglet_public'+id);
				if(!li) var li = document.getElementById('onglet_detail'+id);
				if(li) var ul = li.parentNode;
			}
			return ul;
		}
		function show_what(quoi, id) {
			switch(quoi){
				case 'EXPL_LOC' :
					document.getElementById('div_expl_loc' + id).style.display = 'block';
					document.getElementById('div_expl' + id).style.display = 'none';
					document.getElementById('onglet_expl' + id).className = 'isbd_public_inactive';
					document.getElementById('onglet_expl_loc' + id).className = 'isbd_public_active';
					break;
				case 'EXPL' :
					document.getElementById('div_expl_loc' + id).style.display = 'none';
					document.getElementById('div_expl' + id).style.display = 'block';
					document.getElementById('onglet_expl' + id).className = 'isbd_public_active';
					document.getElementById('onglet_expl_loc' + id).className = 'isbd_public_inactive';
					break;
				default :
					quoi= quoi.toLowerCase();
					var ul = findNoticeElement(id);
					if (ul) {
						var items  = ul.getElementsByTagName('li');
						for (var i=0 ; i<items.length ; i++){
							if(items[i].getAttribute('id')){
								if(items[i].getAttribute('id') == 'onglet_'+quoi+id){
									items[i].className = 'isbd_public_active';
									document.getElementById('div_'+quoi+id).style.display = 'block';
								}else{
									if(items[i].className != 'onglet_tags' && items[i].className != 'onglet_avis' && items[i].className != 'onglet_sugg' && items[i].className != 'onglet_basket' && items[i].className != 'onglet_liste_lecture'){
										items[i].className = 'isbd_public_inactive';
										document.getElementById(items[i].getAttribute('id').replace('onglet','div')).style.display = 'none';
									}
								}
							}
						}
					}
					break;
			}
		}
		</script>";
	}
}
