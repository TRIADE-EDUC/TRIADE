<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: common.tpl.php,v 1.182 2019-05-27 14:55:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $pmb_recherche_ajax_mode, $fiches_active, $cms_active, $pmb_scan_request_activate, $semantic_active, $acquisition_active, $demandes_active, $dsi_active, $pmb_show_help;
global $pmb_extension_tab, $current, $frbr_active, $modelling_active, $param_chat_activate, $class_path, $pmb_default_style_addon, $css_addon, $std_header, $charset, $msg, $stylesheet;
global $base_path, $src_maps_dojo, $pmb_map_activate, $pmb_map_base_layer_type, $javascript_path, $base_use_dojo, $pmb_dojo_gestion_style, $lang, $base_title, $base_noheader;
global $base_nobody, $base_nochat, $selector_header, $selector_header_no_cache, $extra2, $menu_bar, $dash_icon_path, $notification_empty, $notification_icon, $notification_zone;
global $dashboard_module_name, $dashboard_class_name, $dash, $styles_path, $notif_icon_path, $pmb_dashboard_quick_params_activate, $extra, $request_uri, $doc_params_explode, $doc_params;
global $pos, $script_name, $pmb_opac_url, $pmb_show_rtl, $timeout_start_alert, $categ, $url_active, $presence_chaine, $extra_info, $footer, $begin_result_liste, $affich_tris_result_liste;
global $sort, $expand_result, $end_result_list, $cms_dojo_plugins_editor;

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

require_once($class_path."/sort.class.php");

function link_styles($style) {
    // où $rep = répertoire de stockage des feuilles
    
    global $feuilles_style_deja_lu;
    if ($feuilles_style_deja_lu) return $feuilles_style_deja_lu ;
    
    // mise en forme du répertoire
    global $styles_path;
    global $charset;
    
    if($styles_path) $rep = $styles_path;
    else $rep = './styles/';
    
    if(!preg_match('/\/$/', $rep)) $rep .= '/';
    
    /** classement des feuilles de style communes **/
    $feuilles_style="";
    $handle = @opendir($rep."common");
    $css_filenames = array();
    if($handle) {
        while($css = readdir($handle)) {
            if(is_file($rep."common/".$css) && preg_match('/css$/', $css)) {
                $css_filenames[] = $css;
            }
        }
        closedir($handle);
    }
    //Tri alpha
    sort($css_filenames);
    
    foreach($css_filenames as $css_file){
        $feuilles_style.="\n\t<link rel='stylesheet' type='text/css' href='".$rep."common/".$css_file."' title='lefttoright' />";
    }
    /** fin classement des feuilles de style communes **/
    
    
    /** classement des fichiers javascript communs**/
    //Un peu de JS à la rigueur, on inclut tout dans l'ordre alpha
    $jsfiles = array();
    $handle = @opendir($rep."common/javascript");
    if($handle) {
        while($js = readdir($handle)) {
            $jsfiles[] = $js;
        }
        closedir($handle);
    }
    sort($jsfiles);
    
    foreach($jsfiles as $js) {
        if(is_file($rep."common/javascript/".$js) && preg_match('/js$/', $js)) {
            $vide_cache=@filemtime($rep."common/javascript/".$js);
            $feuilles_style.="\n\t<script type='text/javascript' src='".$rep."common/javascript/".$js."?".$vide_cache."' ></script>";
        }
    }
    /** fin classement des fichiers javascript communs**/
    
    
    /** classement des feuilles de style issues des thèmes **/
    $handle = @opendir($rep.$style);
    if(!$handle) {
        $result = array();
        return $result;
    }
    $css_style_filenames = array();
    while($css = readdir($handle)) {
        if(is_file($rep.$style."/".$css) && preg_match('/css$/', $css)) {
            $css_style_filenames[] = $css;
            
        }
    }
    closedir($handle);
    
    sort($css_style_filenames);
    foreach($css_style_filenames as $css_style_filename){
        $feuilles_style.="\n\t<link rel='stylesheet' type='text/css' href='".$rep.$style."/".$css_style_filename."' title='lefttoright' />";
    }
    /** fin classement des feuilles de style issues des thèmes **/
    
    /** classement des fichiers javascript issus des thèmes **/
    //Un peu de JS à la rigueur, on inclut tout dans l'ordre alpha
    $jsfiles = array();
    $handle = @opendir($rep.$style."/javascript");
    if($handle) {
        while($js = readdir($handle)) {
            $jsfiles[] = $js;
        }
        closedir($handle);
    }
    sort($jsfiles);
    foreach($jsfiles as $js) {
        if(is_file($rep.$style."/javascript/".$js) && preg_match('/js$/', $js)) {
            $vide_cache=@filemtime($rep.$style."/javascript/".$js);
            $feuilles_style.="\n\t<script type='text/javascript' src='".$rep.$style."/javascript/".$js."?".$vide_cache."' ></script>";
        }
    }
    /** fin classement des fichiers javascript issus des thèmes **/
    
    
    // RTL / LTR
    global $pmb_show_rtl;
    if ($pmb_show_rtl) {
        $handlertl = @opendir($rep.$style."/rtl/");
        if($handlertl) {
            while($css = readdir($handlertl)) {
                if(is_file($rep.$style."/rtl/".$css) && preg_match('/css$/', $css)) {
                    $result[] = $css;
                    $feuilles_style.="\n\t<link rel='alternate stylesheet' type='text/css' href='".$rep.$style."/rtl/".$css."' title='righttoleft' />";
                }
            }
            $feuilles_style.="\n\t<script type='text/javascript' src='./javascript/styleswitcher.js'></script>";
            closedir($handlertl);
        }
    }
    $feuilles_style_deja_lu = $feuilles_style;
    return $feuilles_style;
}

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
      $std_header.= link_styles($stylesheet);
      $std_header.= $css_addon;
      $std_header.="
	<link rel=\"SHORTCUT ICON\" href=\"images/favicon.ico\" />
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
				}],
				deps: ['apps/pmb/MessagesStore', 'apps/pmb/AceManager', 'dgrowl/dGrowl', 'dojo/ready', 'apps/pmb/IndexationInfos', 'apps/pmb/ImagesStore'],
				callback:function(MessagesStore, AceManager, dGrowl, ready, IndexationInfos, ImagesStore){
					window.pmbDojo = {};
					pmbDojo.uploadMaxFileSize = ".(get_upload_max_filesize()/1024).",
					pmbDojo.messages = new MessagesStore({url:'".$base_path."/ajax.php?module=ajax&categ=messages', directInit:false});
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
      $selector_header.= link_styles($stylesheet); //"    <link rel='stylesheet' type='text/css' href='./styles/$stylesheet'>";
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
      $selector_header_no_cache.= link_styles($stylesheet);
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
      
      //	----------------------------------
      // $menu_bar : template menu bar
      //	Générer le $menu_bar selon les droits...
      //	Par défaut : la page d'accueil.
      
      $menu_bar = "
<!--	Menu bar	-->
!!notification_zone!!
<div id='navbar'>
<h3><span>$msg[1913]</span></h3>
	<ul>
";
      
      $menu_bar = $menu_bar."\n<li id='navbar-dashboard' ";
      $dash_icon_path = get_url_icon('dashboard.png');
      if ("$current" == "dashboard.php"){
          $menu_bar = $menu_bar." class='current'><a class='current' ";
      }else $menu_bar = $menu_bar."><a ";
      $menu_bar.= "title='".$msg['dashboard']."' href='./dashboard.php?categ=' accesskey='$msg[2001]'><img title='".$msg['dashboard']."' alt='".$msg['dashboard']."' src='".$dash_icon_path."'/></a></li>";
      
      //	L'utilisateur fait la CIRCULATION ?
      if (defined('SESSrights') && SESSrights & CIRCULATION_AUTH) {
          $menu_bar = $menu_bar."\n<li id='navbar-circ' ";
          if ("$current" == "circ.php"){
              $menu_bar = $menu_bar." class='current'><a class='current' ";
          }else $menu_bar = $menu_bar."><a ";
          $menu_bar.= "title='$msg[742]' href='./circ.php?categ=' accesskey='$msg[2001]'>$msg[5]</a></li>";
      }
      
      //	L'utilisateur fait le CATALOGAGE ?
      if (defined('SESSrights') && SESSrights & CATALOGAGE_AUTH) {
          $menu_bar = $menu_bar."\n<li id='navbar-catalog'";
          if ("$current" == "catalog.php") $menu_bar = $menu_bar." class='current'><a class='current' ";
          else $menu_bar = $menu_bar."><a ";
          $menu_bar.= "title='$msg[743]' href='./catalog.php' accesskey='$msg[2002]'>$msg[6]</a></li>";
      }
      
      //	L'utilisateur fait les AUTORITÉS ?
      if (defined('SESSrights') && SESSrights & AUTORITES_AUTH) {
          $menu_bar = $menu_bar."\n<li id='navbar-autorites'";
          if ("$current" == "autorites.php") $menu_bar = $menu_bar." class='current'><a class='current' ";
          else $menu_bar = $menu_bar."><a ";
          $menu_bar.= "title='$msg[744]' href='./autorites.php?categ=search' accesskey='$msg[2003]'>$msg[132]</a></li>";
      }
      
      //	L'utilisateur fait l'ÉDITIONS ?
      if (defined('SESSrights') && SESSrights & EDIT_AUTH) {
          $menu_bar = $menu_bar."\n<li id='navbar-edit'";
          if ("$current" == "edit.php") $menu_bar = $menu_bar." class='current'><a class='current' ";
          else $menu_bar = $menu_bar."><a ";
          $menu_bar.= "title='$msg[745]' href='./edit.php?categ=procs' accesskey='$msg[2004]'>$msg[1100]</a></li>";
      }
      
      //	L'utilisateur fait la DSI ?
      if ($dsi_active && (defined('SESSrights') && SESSrights & DSI_AUTH)) {
          $menu_bar = $menu_bar."\n<li id='navbar-dsi'";
          if ("$current" == "dsi.php") $menu_bar = $menu_bar." class='current'><a class='current' ";
          else $menu_bar = $menu_bar."><a ";
          $menu_bar.= "title='".htmlentities($msg['dsi_menu_title'],ENT_QUOTES, $charset)."' href='./dsi.php' >$msg[dsi_menu]</a></li>";
      }
      
      //	L'utilisateur fait l'ACQUISITION ?
      if ($acquisition_active && (defined('SESSrights') && SESSrights & ACQUISITION_AUTH)) {
          $menu_bar = $menu_bar."\n<li id='navbar-acquisition'";
          if ("$current" == "acquisition.php") $menu_bar = $menu_bar." class='current'><a class='current' ";
          else $menu_bar = $menu_bar."><a ";
          $menu_bar.= "title='".htmlentities($msg['acquisition_menu_title'],ENT_QUOTES, $charset)."' href='./acquisition.php' >$msg[acquisition_menu]</a></li>";
      }
      
      //	L'utilisateur accède aux extensions ?
      if ($pmb_extension_tab && (defined('SESSrights') && SESSrights & EXTENSIONS_AUTH)) {
          $menu_bar = $menu_bar."\n<li id='navbar-extensions'";
          if ("$current" == "extensions.php") $menu_bar = $menu_bar." class='current'><a class='current' ";
          else $menu_bar = $menu_bar."><a ";
          $menu_bar.= "title='".htmlentities($msg['extensions_menu_title'],ENT_QUOTES, $charset)."' href='./extensions.php' >$msg[extensions_menu]</a></li>";
      }
      
      //	L'utilisateur fait les DEMANDES ?
      if ($demandes_active && (defined('SESSrights') && SESSrights & DEMANDES_AUTH)) {
          $menu_bar = $menu_bar."\n<li id='navbar-demandes'";
          if ("$current" == "demandes.php") $menu_bar = $menu_bar." class='current'><a class='current' ";
          else $menu_bar = $menu_bar."><a ";
          $menu_bar.= "title='".htmlentities($msg['demandes_menu_title'],ENT_QUOTES, $charset)."' href='./demandes.php' >$msg[demandes_menu]</a></li>";
      }
      
      //	L'utilisateur fait l'onglet FICHES ?
      if ($fiches_active && (defined('SESSrights') && SESSrights & FICHES_AUTH)) {
          $menu_bar = $menu_bar."\n<li id='navbar-fichier'";
          if ("$current" == "fichier.php") $menu_bar = $menu_bar." class='current'><a class='current' ";
          else $menu_bar = $menu_bar."><a ";
          $menu_bar.= "title='".htmlentities($msg['onglet_fichier'],ENT_QUOTES, $charset)."' href='./fichier.php' >".$msg['onglet_fichier']."</a></li>";
      }
      
      //	L'utilisateur fait l'onglet SEMANTIC ?
      if ($semantic_active==true && ((defined('SESSrights') && SESSrights & SEMANTIC_AUTH))) {
          $menu_bar.= "\n<li id='navbar-semantic'";
          if ("$current" == "semantic.php") $menu_bar.= " class='current'><a class='current' ";
          else $menu_bar.= "><a ";
          $menu_bar.= "title='".htmlentities($msg['semantic_onglet_title'],ENT_QUOTES, $charset)."' href='./semantic.php' >".$msg['semantic_onglet_title']."</a></li>";
      }
      
      //	L'utilisateur fait l'onglet CMS ?
      if (defined('SESSrights') && SESSrights & CMS_AUTH) {
          $menu_bar = $menu_bar."\n<li id='navbar-cms'";
          if ("$current" == "cms.php") $menu_bar = $menu_bar." class='current'><a class='current' ";
          else $menu_bar = $menu_bar."><a ";
          $menu_bar.= "title='".htmlentities($msg['cms_onglet_title'],ENT_QUOTES, $charset)."' href='".($cms_active ? "./cms.php?categ=editorial&sub=list" : "./cms.php?categ=frbr_pages&sub=list")."' >".$msg['cms_onglet_title']."</a></li>";
      }
      
      //	L'utilisateur fait l'onglet FRBR ?
      if ($frbr_active==true && defined('SESSrights') && SESSrights & FRBR_AUTH) {
          $menu_bar = $menu_bar."\n<li id='navbar-frbr'";
          if ("$current" == "frbr.php") $menu_bar = $menu_bar." class='current'><a class='current' ";
          else $menu_bar = $menu_bar."><a ";
          $menu_bar.= "title='".htmlentities($msg['frbr'],ENT_QUOTES, $charset)."' href='./frbr.php' >".$msg['frbr']."</a></li>";
      }
      
      //	L'utilisateur fait l'onglet modélisation ?
      if ($modelling_active==true && defined('SESSrights') && SESSrights & MODELLING_AUTH) {
          $menu_bar = $menu_bar."\n<li id='navbar-modelling'";
          if ("$current" == "modelling.php") $menu_bar = $menu_bar." class='current'><a class='current' ";
          else $menu_bar = $menu_bar."><a ";
          $menu_bar.= "title='".htmlentities($msg['modelling'],ENT_QUOTES, $charset)."' href='modelling.php' >".$msg['modelling']."</a></li>";
      }
      //	L'utilisateur fait l'ADMINISTRATION ?
      if (defined('SESSrights') && SESSrights & ADMINISTRATION_AUTH) {
          $menu_bar = $menu_bar."\n<li id='navbar-admin'";
          if ("$current" == "admin.php") $menu_bar = $menu_bar." class='current'><a class='current' ";
          else $menu_bar = $menu_bar."><a ";
          $menu_bar.= "title='$msg[746]' href='./admin.php?categ=' accesskey='$msg[2005]'>$msg[7]</a></li>";
      }
      
      $menu_bar = $menu_bar."
	</ul>
</div>";
      
      $notification_empty=get_url_icon('notification_empty.png');
      $notification_icon = "
		<div class='notification' id='notification'>
			<img src='".$notification_empty."' title='".$msg['empty_notification']."' alt='".$msg['empty_notification']."'>
		</div>";
      $notification_zone = "
		<div id='notification_zone'>
			<div class='row ui-flex ui-flex-between '>
				<div class='ui-flex-grow'>
					!!visits_statistics!!
					<div class='row' id='plugins'>!!plugins!!</div>
					<div class='row' id='quick_actions'>!!quick_actions!!</div>
					<div class='row' id='indexation_infos'></div>
				</div>
				<div class='ui-flex-shrink' id='alert_zone'></div>
			</div>
			<div class='row' id='notifications'></div>
		</div>";
      
      //chargement du tableau de board du module...
      $dashboard_module_name = substr($current,0,strpos($current,"."));
      $dashboard_class_name = '';
      if(file_exists($class_path."/dashboard/dashboard_module_".$dashboard_module_name.".class.php")){
          //on récupère la classe;
          require_once($class_path."/dashboard/dashboard_module_".$dashboard_module_name.".class.php");
          $dashboard_class_name = "dashboard_module_".$dashboard_module_name;
          $dash = new $dashboard_class_name();
          //Dans certains cas, l'affichage change...
          switch($dashboard_module_name){
              case "dashboard" :
                  //dans le tableau de bord, on n'affiche rien en notification...
                  $menu_bar = str_replace("!!notification_zone!!","",$menu_bar);
                  $extra2 = str_replace("!!notification_icon!!","",$extra2);
                  break;
              default :
                  if(file_exists($styles_path."/".$stylesheet."/images/notification_new.png")){
                      $notif_icon_path = $styles_path."/".$stylesheet."/images";
                  }else{
                      $notif_icon_path = "./images";
                  }
                  $notification_zone.="
			<script type='text/javascript'>var notif = new notification('".$dashboard_module_name."','".addslashes($msg['empty_notification'])."','".addslashes($msg['new_notification'])."','".$notif_icon_path."/notification_new.png','".$notif_icon_path."/notification_empty.png')</script>";
                  
                  $menu_bar = str_replace("!!notification_zone!!",$notification_zone,$menu_bar);
                  $extra2 = str_replace("!!notification_icon!!",$notification_icon,$extra2);
                  $menu_bar = str_replace("!!visits_statistics!!", $dash->get_visits_statistics_form(), $menu_bar);
                  $menu_bar = str_replace("!!plugins!!", $dash->get_plugins_form(), $menu_bar);
                  $menu_bar = str_replace("!!quick_actions!!", ($pmb_dashboard_quick_params_activate?$dash->get_quick_params_form():''), $menu_bar);
                  break;
          }
      }else{
          $menu_bar = str_replace("!!notification_zone!!","",$menu_bar);
          $extra2 = str_replace("!!notification_icon!!","",$extra2);
      }
      
      if(!isset($extra)) $extra = '';
      if (defined('SESSrights') && SESSrights & CATALOGAGE_AUTH) {
          $extra.="<iframe id='history' style='display:none;'></iframe>";
      }
      $extra.="
<div id='extra'>
<span id=\"keystatus\">&nbsp;</span>&nbsp;&nbsp;&nbsp;";
      if (defined('SESSrights') && SESSrights & CATALOGAGE_AUTH)
          $extra.="<a class=\"icon_history\" href=\"#\" onClick=\"document.getElementById('history').style.display=''; document.getElementById('history').src='./history.php'; return false;\" alt=\"".$msg["menu_bar_title_histo"]."\" title=\"".$msg["menu_bar_title_histo"]."\"><img src='".get_url_icon('historique.gif')."' class='align_middle' hspace='3' alt='' /></a>";
          
          
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
              $script_name=substr($doc_params_explode[0],$pos);
              $extra .= '<a class="icon_help" href="./doc/index.php?script_name='.$script_name.'&'.$doc_params.'&lang='.$lang.'" alt="'.$msg['1900'].'" title="'.$msg['1900'].'" target="__blank" >';
              $extra .= "<img src='".get_url_icon('aide.gif')."' class='align_middle' hspace='3' alt='' /></a>";
          }
          if (defined('SESSrights') && SESSrights & PREF_AUTH)
              $extra .="<a class=\"icon_param\" href='./account.php' accesskey='$msg[2006]' alt=\"${msg[934]} ".SESSlogin."\" title=\"${msg[934]} ".SESSlogin."\"><img src='".get_url_icon('parametres.gif')."' class='align_middle' hspace='3' alt='' /></a>";
              
              $extra .="<a class=\"icon_opac\" title='$msg[1027]' href='".$pmb_opac_url."index.php?database=".LOCATION."' target='_opac_' accesskey='$msg[2007]'><img src='".get_url_icon('opac2.gif')."' class='align_middle' hspace='3' alt='' /></a>";
              
              if (defined('SESSrights') && SESSrights & SAUV_AUTH)
                  $extra .="<a class=\"icon_sauv\" title='$msg[sauv_shortcuts_title]' href='#' onClick='openPopUp(\"./admin/sauvegarde/launch.php\",\"sauv_launch\",600,500,-2,-2,\"menubar=no,scrollbars=yes\"); w.focus(); return false;'><img src='".get_url_icon('sauv.gif')."' class='align_middle' hspace='3' alt='' /></a>";
                  
                  if ($pmb_show_rtl) {
                      $extra .= "<a title='".$msg['rtl']."' href='#' onclick=\"setActiveStyleSheet('lefttoright'); window.location.reload(false); return false;\"><img src='".get_url_icon('rtl.gif')."' class='align_middle' hspace='3' alt='' /></a>";
                      $extra .= "<a title='".$msg['ltr']."' href='#' onclick=\"setActiveStyleSheet('righttoleft'); window.location.reload(false); return false;\"><img src='".get_url_icon('ltr.gif')."' class='align_middle' hspace='3' alt='' /></a>";
                  }
                  
                  $extra .= "<a class=\"icon_quit\" title='$msg[747] : ".LOCATION."' href='./logout.php' accesskey='$msg[2008]'><img src='".get_url_icon('close.png')."' class='align_middle' hspace='3' alt='' /></a>";
                  
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
<a href=\"javascript:expandAll_ajax()\"><img src='".get_url_icon('expand_all.gif')."' border='0' id=\"expandall\"></a>
<a href=\"javascript:collapseAll()\"><img src='".get_url_icon('collapse_all.gif')."' border='0' id=\"collapseall\"></a>
</span>
";
                  }else{
                      $begin_result_liste = "
<script type=\"text/javascript\" src=\"".$javascript_path."/tablist.js\"></script>
<span class='item-expand'>
<a href=\"javascript:expandAll()\"><img src='".get_url_icon('expand_all.gif')."' border='0' id=\"expandall\"></a>
<a href=\"javascript:collapseAll()\"><img src='".get_url_icon('collapse_all.gif')."' border='0' id=\"collapseall\"></a>
</span>
";
                  }
                  
                  $affich_tris_result_liste = "<a href=# onClick=\"document.getElementById('history').src='./sort.php?action=0'; document.getElementById('history').style.display='';return false;\" alt=\"".$msg['tris_dispos']."\" title=\"".$msg['tris_dispos']."\"><img src='".get_url_icon('orderby_az.gif')."' class='align_middle' hspace='3'></a>";
                  
                  if (isset($_SESSION["tri"]) && $_SESSION["tri"]) {
                      $sort = new sort("notices","base");
                      $affich_tris_result_liste .= $msg['tri_par']." ".$sort->descriptionTriParId($_SESSION["tri"]);
                  }
                  
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