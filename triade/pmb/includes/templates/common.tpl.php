<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: common.tpl.php,v 1.209 2024/04/02 10:59:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $pmb_recherche_ajax_mode, $fiches_active, $cms_active, $pmb_scan_request_activate, $semantic_active, $acquisition_active, $demandes_active, $dsi_active, $pmb_show_help;
global $pmb_extension_tab, $current, $frbr_active, $modelling_active, $param_chat_activate, $class_path, $pmb_default_style_addon, $css_addon, $std_header, $charset, $msg, $stylesheet;
global $base_path, $src_maps_dojo, $pmb_map_activate, $pmb_map_base_layer_type, $javascript_path, $base_use_dojo, $pmb_dojo_gestion_style, $lang, $base_title, $base_noheader;
global $base_nobody, $base_nochat, $selector_header, $selector_header_no_cache, $extra2, $menu_bar, $notification_empty, $notification_icon;
global $dashboard_module_name, $dashboard_class_name, $extra, $request_uri, $doc_params_explode, $doc_params;
global $pos, $script_name, $pmb_opac_url, $pmb_show_rtl, $timeout_start_alert, $categ, $url_active, $presence_chaine, $extra_info, $footer, $begin_result_liste, $affich_tris_result_liste;
global $sort, $expand_result, $end_result_list, $cms_dojo_plugins_editor, $affich_external_tris_result_liste, $affich_authorities_tris_result_liste;
global $affich_authorities_popup_tris_result_liste, $current_module;

require_once $class_path."/html_helper.class.php";

if(!isset($pmb_recherche_ajax_mode)) $pmb_recherche_ajax_mode = 0;
if(!isset($fiches_active)) $fiches_active = 0;
if(!isset($cms_active)) $cms_active = 0;
if(!isset($pmb_scan_request_activate)) $pmb_scan_request_activate = 0;
if(!isset($semantic_active)) $semantic_active = 0;
if(!isset($acquisition_active)) $acquisition_active = 0;
if(!isset($demandes_active)) $demandes_active = 0;
if(!isset($dsi_active)) $dsi_active = 0;
if(!isset($pmb_show_help)) $pmb_show_help = 0;
if(!isset($pmb_extension_tab)) $pmb_extension_tab = 0;
if(!isset($current)) $current = '';
if(!isset($frbr_active)) $frbr_active = 0;
if(!isset($modelling_active)) $modelling_active = 0;
if(!isset($param_chat_activate)) $param_chat_activate = 0;
if(!isset($stylesheet)) $stylesheet = '';

if (isset($pmb_default_style_addon) && $pmb_default_style_addon) {
    $css_addon = "
		<style type='text/css'>
			".$pmb_default_style_addon."
		</style>";
} else {
    $css_addon = "";
}

//	----------------------------------
// $std_header : template header standard
// attention : il n'y a plus le <body> : est envoyé par le fichier init.inc.php, c'est bien un header
$std_header = "<!DOCTYPE html>
<html lang='".get_iso_lang_code()."'>
<head>
	<meta charset=\"".$charset."\" />
    <title>
      $msg[1001]
    </title>
	<meta name='author' content='PMB Group' />
	<meta name='description' content='Logiciel libre de gestion de médiathèque' />
	<meta name='keywords' content='logiciel, gestion, bibliothèque, médiathèque, libre, free, software, mysql, php, linux, windows, mac' />
	<!--<meta http-equiv='Pragma' content='no-cache' />
	<meta http-equiv='Cache-Control' content='no-cache' />-->
	";
      $std_header.= HtmlHelper::getInstance()->getStyle($stylesheet);
      $std_header.= $css_addon;
      $std_header.="
	<link rel=\"SHORTCUT ICON\" href=\"".$base_path."/images/favicon.ico\" />
	<script src=\"".$base_path."/javascript/popup.js\" type=\"text/javascript\"></script>
	<script src=\"".$base_path."/javascript/drag_n_drop.js\" type=\"text/javascript\"></script>
	<script src=\"".$base_path."/javascript/handle_drop.js\" type=\"text/javascript\"></script>
	<script src=\"".$base_path."/javascript/element_drop.js\" type=\"text/javascript\"></script>
	<script src=\"".$base_path."/javascript/cart_div.js\" type=\"text/javascript\"></script>
	<script src=\"".$base_path."/javascript/misc.js\" type=\"text/javascript\"></script>
	<script src=\"".$base_path."/javascript/http_request.js\" type=\"text/javascript\"></script>
	<script type=\"text/javascript\">
		var base_path='".$base_path."';
		var pmb_img_minus = '".get_url_icon('minus.gif')."';
		var pmb_img_plus = '".get_url_icon('plus.gif')."';
		var pmb_img_patience = '".get_url_icon('patience.gif')."';
	</script>
	<script src='".$base_path."/javascript/tablist.js' type=\"text/javascript\"></script>
	<script src='".$base_path."/javascript/sorttable.js' type='text/javascript'></script>
	<script src='".$base_path."/javascript/templates.js' type='text/javascript'></script>
	<script type=\"text/javascript\">
		function keep_context(myObject,methodName){
			return function(){
				return myObject[methodName]();
			}
		}
		// Fonction a utilisier pour l'encodage des URLs en javascript
		function encode_URL(data){
			var docCharSet = document.characterSet ? document.characterSet : document.charset;
			if(docCharSet == \"UTF-8\"){
				return encodeURIComponent(data);
			}else{
				return escape(data);
			}
		}
	</script>
";
      
      if ($pmb_scan_request_activate) {
          $std_header.="
	<script type='text/javascript' src='".$base_path."/javascript/scan_requests.js'></script>";
      }
      $src_maps_dojo = '';
      if($pmb_map_activate){
          switch($pmb_map_base_layer_type){
              case "GOOGLE" :
                  $std_header.="<script src='http://maps.google.com/maps/api/js?v=3&amp;sensor=false'></script>";
                  break;
          }
          $std_header.="<link rel='stylesheet' type='text/css' href='".$javascript_path."/openlayers/theme/default/style.css'/>";
          $std_header.="<script type='text/javascript' src='".$javascript_path."/openlayers/lib/OpenLayers.js'></script>";
          $std_header.="<script type='text/javascript' src='".$javascript_path."/html2canvas.js'></script>";
          $src_maps_dojo.= "<script type='text/javascript' src='".$base_path."/javascript/dojo/dojo/pmbmaps.js'></script>";
      }
      
      
      if(isset($base_use_dojo)){
          if ($param_chat_activate) {
              $std_header.="<script type='text/javascript' src='".$base_path."/javascript/chat_dragable.js'></script>";
          }
          
          global $messages;
          $array_message_retourne = array();
          foreach ($messages->table_js as $group => $msgs) {
              foreach ($msgs as $key => $value) {
                  $array_message_retourne[] = array(
                      'code' => $key,
                      'message' => $value,
                      'group' => $group
                  );
              }
          }
          $json_message_retourne = encoding_normalize::json_encode($array_message_retourne);
          
          $std_header.="
		<link rel='stylesheet' type='text/css' href='".$base_path."/javascript/dojo/dijit/themes/".$pmb_dojo_gestion_style."/".$pmb_dojo_gestion_style.".css' />
		<script type='text/javascript'>
			var dojoConfig = {
				parseOnLoad: true,
				locale: '".str_replace("_","-",strtolower($lang))."',
				isDebug: false,
				usePlainJson: true,
				packages: [{
					name: 'pmbBase',
					location:'../../..'
				},{
					name: 'd3',
					location:'../../d3'
				},{
					name: 'ace',
					location:'../../ace'
				}],
				deps: ['apps/pmb/MessagesStore', 'apps/pmb/AceManager', 'dgrowl/dGrowl', 'dojo/ready', 'apps/pmb/IndexationInfos', 'apps/pmb/ImagesStore'],
				callback:function(MessagesStore, AceManager, dGrowl, ready, IndexationInfos, ImagesStore){
					window.pmbDojo = {};
					pmbDojo.uploadMaxFileSize = ".(get_upload_max_filesize()/1024).",
					pmbDojo.messages = new MessagesStore({url : '$base_path/ajax.php?module=ajax&categ=messages', directInit : false, messages : $json_message_retourne});
					pmbDojo.images = new ImagesStore({url:'".$base_path."/ajax.php?module=ajax&categ=images', directInit:false});
					pmbDojo.aceManager = new AceManager();
					ready(function(){
                        require(['apps/chat/ChatController'], function(ChatController){
                            " . ($param_chat_activate && ($base_title!= 'Selection' && (!isset($base_noheader) || !$base_noheader) && (!isset($base_nobody) || !$base_nobody) && (!isset($base_nochat) || !$base_nochat)) ? "new ChatController();" : "") . "
                        });
						new dGrowl({'channels':[{'name':'service','pos':1},{'name':'info','pos':2},{'name':'error', 'pos':3}]});
						new IndexationInfos();
                        
					});
				},
	        };
		</script>
		<script type='text/javascript' src='".$base_path."/javascript/dojo/dojo/dojo.js'></script>";
          
          $std_header.=$src_maps_dojo;
          
          $std_header.="<link href='".$base_path."/javascript/dojo/dojox/editor/plugins/resources/editorPlugins.css' type='text/css' rel='stylesheet' />
		<link href='".$base_path."/javascript/dojo/dojox/editor/plugins/resources/css/InsertEntity.css' type='text/css' rel='stylesheet' />
		<link href='".$base_path."/javascript/dojo/dojox/editor/plugins/resources/css/PasteFromWord.css' type='text/css' rel='stylesheet' />
		<link href='".$base_path."/javascript/dojo/dojox/editor/plugins/resources/css/InsertAnchor.css' type='text/css' rel='stylesheet' />
		<link href='".$base_path."/javascript/dojo/dojox/editor/plugins/resources/css/LocalImage.css' type='text/css' rel='stylesheet' />
		<link href='".$base_path."/javascript/dojo/dojox/form/resources/FileUploader.css' type='text/css' rel='stylesheet' />
		<link href='".$base_path."/javascript/dojo/dgrowl/dGrowl.css' type='text/css' rel='stylesheet' />
		<script type='text/javascript'>
			dojo.require('dijit.Editor');
			dojo.require('dijit._editor.plugins.LinkDialog');
			dojo.require('dijit._editor.plugins.FontChoice');
			dojo.require('dijit._editor.plugins.TextColor');
			dojo.require('dijit._editor.plugins.FullScreen');
			dojo.require('dijit._editor.plugins.ViewSource');
			dojo.require('dojox.editor.plugins.InsertEntity');
			dojo.require('dojox.editor.plugins.TablePlugins');
			dojo.require('dojox.editor.plugins.ResizeTableColumn');
			dojo.require('dojox.editor.plugins.PasteFromWord');
			dojo.require('dojox.editor.plugins.InsertAnchor');
			dojo.require('dojox.editor.plugins.Blockquote');
			dojo.require('dojox.editor.plugins.LocalImage');
		</script>
	";
      }
      if (function_exists("auto_hide_getprefs")) $std_header.=auto_hide_getprefs()."\n";
      $std_header.="
		<script type='text/javascript' src='".$javascript_path."/pmbtoolkit.js'></script>
		<script type='text/javascript' src='".$javascript_path."/notification.js'></script>";
      $std_header.="	</head>";
      
      //	----------------------------------
      // $selector_header : template header selecteur
      $selector_header = "<!DOCTYPE html>
<html lang='".get_iso_lang_code()."'>
<head>
	<meta charset=\"".$charset."\" />
  	<meta name='author' content='PMB Group' />
	<meta name='description' content='Logiciel libre de gestion de médiathèque' />
	<meta name='keywords' content='logiciel, gestion, bibliothèque, médiathèque, libre, free, software, mysql, php, linux, windows, mac' />
  	<script type=\"text/javascript\">
		var base_path='".$base_path."';
		var pmb_img_minus = '".get_url_icon('minus.gif')."';
		var pmb_img_plus = '".get_url_icon('plus.gif')."';
		var pmb_img_patience = '".get_url_icon('patience.gif')."';
	</script>
    <title>
      PMB-Selector
    </title>";
      $selector_header.= HtmlHelper::getInstance()->getStyle($stylesheet);
      $selector_header.= $css_addon;
      $src_maps_dojo = '';
      if($pmb_map_activate){
          switch($pmb_map_base_layer_type){
              case "GOOGLE" :
                  $std_header.="<script src='http://maps.google.com/maps/api/js?v=3&amp;sensor=false'></script>";
                  break;
          }
          $selector_header.="<link rel='stylesheet' type='text/css' href='".$javascript_path."/openlayers/theme/default/style.css'/>";
          $selector_header.="<script type='text/javascript' src='".$javascript_path."/openlayers/lib/OpenLayers.js'></script>";
          $selector_header.="<script type='text/javascript' src='".$javascript_path."/html2canvas.js'></script>";
          $src_maps_dojo.= "<script type='text/javascript' src='".$base_path."/javascript/dojo/dojo/pmbmaps.js'></script>";
      }
      
      
      if(isset($base_use_dojo)){
          $selector_header.="
		<link rel='stylesheet' type='text/css' href='".$base_path."/javascript/dojo/dijit/themes/".$pmb_dojo_gestion_style."/".$pmb_dojo_gestion_style.".css' />
		<script type='text/javascript'>
			var dojoConfig = {
				parseOnLoad: true,
				locale: '".str_replace("_","-",strtolower($lang))."',
				isDebug: false,
				usePlainJson: true,
				packages: [{
					name: 'pmbBase',
					location:'../../..'
				}],
				deps: ['apps/pmb/MessagesStore', 'apps/pmb/AceManager', 'dgrowl/dGrowl', 'dojo/ready', 'apps/pmb/ImagesStore'],
				callback:function(MessagesStore, AceManager, dGrowl, ready, ImagesStore){
					window.pmbDojo = {};
					pmbDojo.uploadMaxFileSize = ".(get_upload_max_filesize()/1024).",
					pmbDojo.messages = new MessagesStore({url:'".$base_path."/ajax.php?module=ajax&categ=messages', directInit:false});
					pmbDojo.images = new ImagesStore({url:'".$base_path."/ajax.php?module=ajax&categ=images', directInit:false});
					pmbDojo.aceManager = new AceManager();
					ready(function(){
						new dGrowl({'channels':[{'name':'info','pos':2},{'name':'error', 'pos':3},{'name':'service','pos':1}]});
					});
				},
	        };
		</script>
		<script type='text/javascript' src='".$base_path."/javascript/dojo/dojo/dojo.js'></script>";
          
          $selector_header.=$src_maps_dojo;
          
          $selector_header.="<link href='".$base_path."/javascript/dojo/dojox/editor/plugins/resources/editorPlugins.css' type='text/css' rel='stylesheet' />
		<link href='".$base_path."/javascript/dojo/dojox/editor/plugins/resources/css/InsertEntity.css' type='text/css' rel='stylesheet' />
		<link href='".$base_path."/javascript/dojo/dojox/editor/plugins/resources/css/PasteFromWord.css' type='text/css' rel='stylesheet' />
		<link href='".$base_path."/javascript/dojo/dojox/editor/plugins/resources/css/InsertAnchor.css' type='text/css' rel='stylesheet' />
		<link href='".$base_path."/javascript/dojo/dojox/editor/plugins/resources/css/LocalImage.css' type='text/css' rel='stylesheet' />
		<link href='".$base_path."/javascript/dojo/dojox/form/resources/FileUploader.css' type='text/css' rel='stylesheet' />
		<link href='".$base_path."/javascript/dojo/dgrowl/dGrowl.css' type='text/css' rel='stylesheet' />
		<script type='text/javascript'>
			dojo.require('dijit.Editor');
			dojo.require('dijit._editor.plugins.LinkDialog');
			dojo.require('dijit._editor.plugins.FontChoice');
			dojo.require('dijit._editor.plugins.TextColor');
			dojo.require('dijit._editor.plugins.FullScreen');
			dojo.require('dijit._editor.plugins.ViewSource');
			dojo.require('dojox.editor.plugins.InsertEntity');
			dojo.require('dojox.editor.plugins.TablePlugins');
			dojo.require('dojox.editor.plugins.ResizeTableColumn');
			dojo.require('dojox.editor.plugins.PasteFromWord');
			dojo.require('dojox.editor.plugins.InsertAnchor');
			dojo.require('dojox.editor.plugins.Blockquote');
			dojo.require('dojox.editor.plugins.LocalImage');
		</script>
	";
      }
      $selector_header.="  </head>
  </head>
  <body>
";
      
      //	----------------------------------
      // $selector_header_no_cache : template header selecteur (no cache)
      $selector_header_no_cache = "<!DOCTYPE html>
<html lang='".get_iso_lang_code()."'>
<head>
	<meta charset=\"".$charset."\" />
    <title>
      PMB-selector
    </title>
	<meta name='author' content='PMB Group' />
	<meta name='description' content='Logiciel libre de gestion de médiathèque' />
	<meta name='keywords' content='logiciel, gestion, bibliothèque, médiathèque, libre, free, software, mysql, php, linux, windows, mac' />
	<!--<meta http-equiv='Pragma' content='no-cache'>
    <meta http-equiv='Cache-Control' content='no-cache'>-->
	<script type=\"text/javascript\">
		var base_path='".$base_path."';
		var pmb_img_minus = '".get_url_icon('minus.gif')."';
		var pmb_img_plus = '".get_url_icon('plus.gif')."';
		var pmb_img_patience = '".get_url_icon('patience.gif')."';
	</script>";
      $selector_header_no_cache.= HtmlHelper::getInstance()->getStyle($stylesheet);
      $selector_header_no_cache.= $css_addon;
      $selector_header_no_cache.="
  </head>
  <body>
";
      
      
      //	----------------------------------
      // $extra2 : template extra2
      
      $extra2 = "
<!--	Extra2		-->
<div id='extra2'>
	!!notification_icon!!
</div>
";
      
      $notification_empty=get_url_icon('notification_empty.png');
      $notification_icon = "
		<div class='notification' id='notification' title='".$msg['empty_notification']."'>
			<img src='".$notification_empty."' alt='".$msg['empty_notification']."'>
		</div>";
      
      //chargement du tableau de board du module...
      $dashboard_module_name = substr($current,0,strpos($current,"."));
      $dashboard_class_name = '';
      if(file_exists($class_path."/dashboard/dashboard_module_".$dashboard_module_name.".class.php")){
      	//on récupère la classe;
      	require_once($class_path."/dashboard/dashboard_module_".$dashboard_module_name.".class.php");
      	//Dans certains cas, l'affichage change...
      	switch($dashboard_module_name){
      		case "dashboard" :
      			//dans le tableau de bord, on n'affiche rien en notification...
      			$extra2 = str_replace("!!notification_icon!!","",$extra2);
      			break;
      		default :
      			$extra2 = str_replace("!!notification_icon!!",$notification_icon,$extra2);
      			break;
      	}
      }else{
      	$extra2 = str_replace("!!notification_icon!!","",$extra2);
      }
      
//	----------------------------------
// $menu_bar : template menu bar
//	Générer le $menu_bar selon les droits...
//	Par défaut : la page d'accueil.
require_once($class_path."/list/modules/list_modules_ui.class.php");
$menu_bar = list_modules_ui::get_instance()->get_display();
      
if(!isset($extra)) $extra = '';
if (defined('SESSrights') && SESSrights & CATALOGAGE_AUTH) {
  $extra.="<iframe id='history' style='display:none;'></iframe>";
}
$extra.="
<div id='extra'>
<span id=\"keystatus\">&nbsp;</span>&nbsp;&nbsp;&nbsp;";
if (defined('SESSrights') && SESSrights & CATALOGAGE_AUTH) {
  $extra.="<a class=\"icon_history\" href=\"#\" onClick=\"document.getElementById('history').style.display=''; document.getElementById('history').src='./history.php'; return false;\" title=\"".htmlentities($msg["menu_bar_title_histo"], ENT_QUOTES, $charset)."\">
    <img src='".get_url_icon('historique.gif')."' class='align_middle' style='margin:0px 3px' alt='' />
    <span class='visually-hidden'>".htmlentities($msg["menu_bar_title_histo"], ENT_QUOTES, $charset)."</span>
    </a>";
}
          
//affichage du lien d'aide, c'est un "?" pour l'instant
if ($pmb_show_help) {
  // remplacement de !!help_link!! par le lien correspondant
  $request_uri  = $_SERVER["REQUEST_URI"];
  $doc_params_explode = explode("?", $request_uri);
  if(isset($doc_params_explode[1])) {
      $doc_params = $doc_params_explode[1];
  } else {
      $doc_params = '';
  }

  $pos = strrpos($doc_params_explode[0], "/") + 1;
  $script_name = substr($doc_params_explode[0], $pos);
  
  // On évite la vulnérabilités xss
  $help_url = sprintf("./doc/index.php?script_name=%s", urlencode($script_name));
  if (!empty($doc_params)) {       	
  	$help_url = "{$help_url}&" . htmlentities($doc_params, ENT_QUOTES, $charset);
  }
  $help_url .= "&lang={$lang}";
  
  $extra .= '<a class="icon_help" href="'. $help_url .'" title="'.$msg['1900'].'" target="_blank" >';
  $extra .= "<img src='".get_url_icon('aide.gif')."' class='align_middle' style='margin:0px 3px' alt='' />";
  $extra .= "<span class='visually-hidden'>".htmlentities($msg['1900'], ENT_QUOTES, $charset)."</span>";
  $extra .= "</a>";
}
if (defined('SESSrights') && SESSrights & PREF_AUTH) {
    $extra .="<a class=\"icon_param\" href='./account.php' accesskey='$msg[2006]' title=\"{$msg[934]} ".SESSlogin."\">
    <img src='".get_url_icon('parametres.gif')."' class='align_middle' style='margin:0px 3px' alt='' />
    <span class='visually-hidden'>".htmlentities($msg["934"], ENT_QUOTES, $charset)."</span>
    </a>";
}
$extra .="<a class=\"icon_opac\" title='$msg[1027]' href='".$pmb_opac_url."index.php?database=".LOCATION."' target='_opac_' accesskey='$msg[2007]'>
    <img src='".get_url_icon('opac2.gif')."' class='align_middle' style='margin:0px 3px' alt='' />
    <span class='visually-hidden'>".htmlentities($msg["1027"], ENT_QUOTES, $charset)."</span>
    </a>";
              
if (defined('SESSrights') && SESSrights & SAUV_AUTH) {
  $extra .="<a class=\"icon_sauv\" title='$msg[sauv_shortcuts_title]' href='#' onClick='openPopUp(\"./admin/sauvegarde/launch.php\",\"sauv_launch\",600,500,-2,-2,\"menubar=no,scrollbars=yes\"); w.focus(); return false;'>
    <img src='".get_url_icon('sauv.gif')."' class='align_middle' style='margin:0px 3px' alt='' />
    <span class='visually-hidden'>".htmlentities($msg["sauv_shortcuts_title"], ENT_QUOTES, $charset)."</span>
    </a>";
}
if ($pmb_show_rtl) {
  $extra .= "<a title='".$msg['rtl']."' href='#' onclick=\"setActiveStyleSheet('lefttoright'); window.location.reload(false); return false;\">
    <img src='".get_url_icon('rtl.gif')."' class='align_middle' style='margin:0px 3px' alt='' />
    <span class='visually-hidden'>".htmlentities($msg["rtl"], ENT_QUOTES, $charset)."</span>
    </a>";
  $extra .= "<a title='".$msg['ltr']."' href='#' onclick=\"setActiveStyleSheet('righttoleft'); window.location.reload(false); return false;\"><img src='".get_url_icon('ltr.gif')."' class='align_middle' style='margin:0px 3px' alt='' /></a>";
}
                  
$extra .= "<a class=\"icon_quit\" title='$msg[747] : ".LOCATION."' href='./logout.php' accesskey='$msg[2008]'>
    <img src='".get_url_icon('close.png')."' class='align_middle' style='margin:0px 3px' alt='' />
    <span class='visually-hidden'>".htmlentities($msg["747"], ENT_QUOTES, $charset)."</span>
    </a>";
                  
                  $extra .= "</div>";
                  
                  $timeout_start_alert = 5000; // 5s pour déclancher la requette des alertes / tableau de bord
                  if(isset($categ) && (($categ=='pret') || $categ=='retour')){
                      $timeout_start_alert = 30000; // 30s pour les phases de prêt / retour
                  }
                  // Récupération de l'url active et test de présence sur la chaine cir.php'
                  $url_active = $_SERVER['PHP_SELF'];
                  $presence_chaine = strpos($url_active,'circ.php');
                  
                  // Masquage de l'iframe d'alerte dans le cas
                  // ou l'onglet courant est circulation et utilisateur en circulation restreinte'
                  if ( !function_exists("auto_hide_getprefs") || ((defined('SESSrights') && SESSrights & RESTRICTCIRC_AUTH) && ($categ!="pret") && ($categ!="pretrestrict") &&  ($presence_chaine != false))) {
                      $extra_info = '';
                  } else {
                      
                      $extra_info ="<iframe frameborder='0' scrolling='auto' name='alerte' id='alerte' class='$current_module'></iframe>";
                      
                      $extra_info="<script type=\"text/javascript\">
                      
		window.onfocus = function() {alert_focus_active = 1;}
		window.onblur = function() {alert_focus_active = 0;}
		
		function get_alert() {
			if(!document.getElementById('div_alert')) return;
			if(!session_active) return;
			if(alert_focus_active) {
				var req = new http_request();
				req.request('$base_path/ajax.php?module=ajax&categ=alert&current_alert=$current_module',0,'',1,get_alert_callback,'');
			}
			setTimeout(get_alert,120000);
		}
		
		function get_alert_callback(text ) {
			var struct = eval('('+text+')');
			if(struct.state != 1 ){
				session_active=0;
				return;
			}
			session_active=1;
			var div_alert = document.getElementById('div_alert');
			//si les notifications sont en fonctionnement, on appelle le callback des alertes...
			if(typeof(notif) == 'object'){
				notif.check_new_alert(struct);
			}
			div_alert.innerHTML = struct.separator+struct.html;
		}
		session_active=1;
		addLoadEvent(function() {
			alert_focus_active = 1;
			setTimeout(get_alert, ".$timeout_start_alert.");
		});
	</script>";
                  }
                  if($dashboard_class_name) {
                      $extra_info.="<script type=\"text/javascript\">
                      
		function get_dashboard() {
			if(!document.getElementById('notification_zone')) return;
			var req = new http_request();
			req.request('$base_path/ajax.php?module=ajax&categ=dashboard&current_dashboard=$current',0,'',1,get_dashboard_callback,'');
		}
		
		function get_dashboard_callback(text ) {
			var struct = eval('('+text+')');
			if(struct.state != 1 ){
				return;
			}
			var div_notifications = document.getElementById('notifications');
			div_notifications.innerHTML = struct.html_notifications;
		}
		addLoadEvent(function() {
			setTimeout(get_dashboard, ".$timeout_start_alert.");
		});
			    
	</script>";
                  }
                  
                  //	----------------------------------
                  // $footer : template footer standard
                  $footer = "
<div id='footer'>
	<div class='row'>
                      
	</div>
</div>
<script type=\"text/javascript\">
	if (init_drag && ((typeof no_init_drag=='undefined') || (no_init_drag==false)) ) init_drag();
	menuAutoHide();
</script>
  </body>
</html>
";
                  
                  /* listes dépliables et tris */
                  // ici, templates de gestion des listes dépliables et tris en résultat de recherche catalogage ou autres
                  if($pmb_recherche_ajax_mode){
                      $begin_result_liste = "
<script type=\"text/javascript\" src=\"".$javascript_path."/tablist.js\"></script>
<span class='item-expand'>
".get_expandCollapseAll_ajax_buttons()."
</span>
";
                  }else{
                      $begin_result_liste = "
<script type=\"text/javascript\" src=\"".$javascript_path."/tablist.js\"></script>
<span class='item-expand'>
".get_expandCollapseAll_buttons()."
</span>
";
                  }
                  
                  $affich_tris_result_liste = "<a href='#' class='sort' onClick=\"document.getElementById('history').src='./sort.php?action=0&module=$current_module'; document.getElementById('history').style.display='';return false;\" alt=\"".$msg['tris_dispos']."\" title=\"".$msg['tris_dispos']."\"><img src='".get_url_icon('orderby_az.gif')."' class='align_middle' style='margin:0px 3px'></a>";
                  $affich_external_tris_result_liste = "<a href='#' class='sort' onClick=\"document.getElementById('history').src='./sort.php?action=0&caller=external&type_tri=external&module=$current_module'; document.getElementById('history').style.display='';return false;\" alt=\"".$msg['tris_dispos']."\" title=\"".$msg['tris_dispos']."\"><img src='".get_url_icon('orderby_az.gif')."' class='align_middle' style='margin:0px 3px'></a>";
                  if (isset($_SESSION["tri"]) && $_SESSION["tri"]) {
                      require_once($class_path."/sort.class.php");
                      $sort = new sort("notices","base");
                      $affich_tris_result_liste .= $msg['tri_par']." ".$sort->descriptionTriParId($_SESSION["tri"]);
                      $affich_external_tris_result_liste .= $msg['tri_par']." ".$sort->descriptionTriParId($_SESSION["tri"]);
                  }
                  $affich_authorities_tris_result_liste = "<a href='#' class='sort' onClick=\"document.getElementById('history').src='./sort.php?action=0&type_tri=!!entity_type!!&module=$current_module!!sort_params!!'; document.getElementById('history').style.display='';return false;\" alt=\"".$msg['tris_dispos']."\" title=\"".$msg['tris_dispos']."\"><img src='".get_url_icon('orderby_az.gif')."' class='align_middle' style='margin:0px 3px'></a>";
                  $affich_authorities_popup_tris_result_liste = "<a href='#' id='iframeSort' class='sort' alt=\"".$msg['tris_dispos']."\" title=\"".$msg['tris_dispos']."\">
                                                                    <img src='".get_url_icon('orderby_az.gif')."' class='align_middle' style='margin:0px 3px'>
                                                                  </a>
                                                                    <script>
                                                                        if (!window.sortIframe) {
                                                                            require([
                                                                                'dojo/ready', 
                                                                                'dojo/topic',
                                                                                'dojo/dom',
                                                                                'apps/pmb/SortIframe'
                                                                            ], function(ready, topic, dom, SortIframe){
                                                                                ready(function(){
                                                                                    // le current_module est récupérer en JS
                                                                                    window.sortIframe = new SortIframe({
                                                                                        btnNode: dom.byId('iframeSort'),
                                                                                        sortLink: './sort.php?action=0&type_tri=!!entity_type!!&popup=1&base_noheader=1',
                                                                                        msgSuppr: '" . $msg['tri_confirm_supp'] . "'
                                                                                    })
                                                                               });
                                                                            });
                                                                        } else {
                                                                            // Page reload ou nouvelle recherche
                                                                            var btnNode = document.getElementById('iframeSort');
                                                                            if (btnNode) {
				                                                                window.sortIframe.parse(btnNode);
                                                                            }
                                                                        }
                                                                    </script>";
                  
                  $expand_result="
<script type=\"text/javascript\" src=\"./javascript/tablist.js\"></script>
";
                  
                  $end_result_list = "
";
                  
                  
                  /* /listes dépliables et tris */
                  
                  /* Editeur HTML DOJO */
                  $cms_dojo_plugins_editor=
                  " data-dojo-props=\"extraPlugins:[
			{name: 'pastefromword', width: '400px', height: '200px'},
			{name: 'dojox.editor.plugins.TablePlugins', command: 'insertTable'},
		    {name: 'dojox.editor.plugins.TablePlugins', command: 'modifyTable'},
		    {name: 'dojox.editor.plugins.TablePlugins', command: 'InsertTableRowBefore'},
		    {name: 'dojox.editor.plugins.TablePlugins', command: 'InsertTableRowAfter'},
		    {name: 'dojox.editor.plugins.TablePlugins', command: 'insertTableColumnBefore'},
		    {name: 'dojox.editor.plugins.TablePlugins', command: 'insertTableColumnAfter'},
		    {name: 'dojox.editor.plugins.TablePlugins', command: 'deleteTableRow'},
		    {name: 'dojox.editor.plugins.TablePlugins', command: 'deleteTableColumn'},
		    {name: 'dojox.editor.plugins.TablePlugins', command: 'colorTableCell'},
		    {name: 'dojox.editor.plugins.TablePlugins', command: 'tableContextMenu'},
		    {name: 'dojox.editor.plugins.TablePlugins', command: 'ResizeTableColumn'},
			{name: 'fontName', plainText: true},
			{name: 'fontSize', plainText: true},
			{name: 'formatBlock', plainText: true},
			'foreColor','hiliteColor',
			'createLink','insertanchor', 'unlink', 'insertImage',
			'fullscreen',
			'viewsource'
                      
		]\"	";