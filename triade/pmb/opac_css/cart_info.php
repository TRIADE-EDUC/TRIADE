<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart_info.php,v 1.109.4.2 2025/04/10 14:39:56 dgoron Exp $

global $msg, $charset, $class_path, $include_path, $lvl, $action;
global $opac_search_other_function;
global $opac_integrate_anonymous_cart;
global $opac_default_style, $css, $opac_accessibility;
global $location, $id;
global $plettreaut, $lcote, $dcote, $user_query;
global $opac_rgaa_active;

//Actions et affichage du résultat pour un panier de l'opac
$base_path=".";
require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

require_once($base_path."/classes/search.class.php");
require_once($class_path."/searcher.class.php");
require_once($class_path."/filter_results.class.php");
require_once($class_path."/cart.class.php");

// si paramétrage authentification particulière et pour le re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');

require_once($include_path."/templates/cart.tpl.php");

if($opac_search_other_function){
	require_once($include_path."/".$opac_search_other_function);
}

print "<!DOCTYPE html>
<html lang='".get_iso_lang_code()."'>
<head>
    <meta charset=\"".$charset."\" />
    <meta name='robots' content='noindex, nofollow'>
</head>
<body id='cart_info_body' class='cart_info_body'>
    <span id='cart_info_iframe_content' class='basket_is_not_empty'>";


print "<script>
		var msg_notice_title_basket = '".addslashes($msg["notice_title_basket"])."';
		var msg_record_display_add_to_cart = '".addslashes($msg["record_display_add_to_cart"])."';
		var msg_notice_title_basket_exist = '".addslashes($msg["notice_title_basket_exist"])."';
		var msg_notice_basket_remove = '".addslashes($msg["notice_basket_remove"])."';
		</script>";
print "<script src='".$include_path."/javascript/cart.js'></script>";

$cart_css = '';
if (file_exists($base_path.'/styles/'.$opac_default_style.'/cart.css')) {
	$cart_css = '<link rel="stylesheet" type="text/css" href="'.$base_path.'/styles/'.$opac_default_style.'/cart.css" />';
}
$vide_cache=filemtime("./styles/".$css."/".$css.".css");
print "<link rel=\"stylesheet\" href=\"./styles/".$css."/".$css.".css?".$vide_cache."\" />".$cart_css;
$cart_=(isset($_SESSION["cart"]) ? $_SESSION["cart"] : array());
if (!is_countable($cart_) || !count($cart_)) {
    $cart_=array();
}

//$id doit être addslasher car il est utilisé dans des requetes
//$id=stripslashes($id);// attention id peut etre du type es123 (recherche externe)
$location = intval($location);

if(!isset($id)) $id = 0;

// On évite les failles xss
if (strpos($id, "es") === 0) {
    // cas des recherche externe
    $idEs = substr($id, 2);
    $id = "es".intval($idEs);
} else {
    // sinon on caste en int
    $id = intval($id);
}

$message="";
if (($id)&&(!$lvl)) {
	if(!isset($action)) $action ='';
	switch($action) {
		case 'remove':
		    cart::remove($id);
			break;
		default:
		    $message = cart::add($id);
			break;
	}
} else if ($lvl) {
	$notices = '';
	$message = '';
	switch ($lvl) {
		case "section_see":
		    $message = cart::add_from_section($id, $location, $plettreaut, $dcote, $lcote);
			break;
		case "concept_see":
		    $message = cart::add_from_concept($id);
			break;
		case "listlecture":
		    $message = cart::add_from_liste_lecture($id);
			global $sub;
			if($sub == "consult") {
				print "<script>top.document.liste_lecture.action=\"index.php?lvl=show_list&sub=consultation&id_liste=".stripslashes($id)."\";top.document.liste_lecture.target=\"\"</script>";
			} else {
				print "<script>top.document.liste_lecture.action=\"index.php?lvl=show_list&sub=view&id_liste=".stripslashes($id)."\";top.document.liste_lecture.target=\"\"</script>";
			}
			break;
		default:
			// classes pour la gestion des sélecteurs
			require_once($class_path."/caddie/caddie_controller.class.php");
			caddie_controller::set_user_query(stripslashes($user_query));
			$message = caddie_controller::proceed($id);
			break;
	}
}else if(!$lvl && isset($notices) && $notices){
	cart::add_entities($notices);
}

print "<span class='img_basket'>
    <a href='index.php?lvl=show_cart' onClick=\"parent.document.location='index.php?lvl=show_cart'; return false;\">
    <img src='".get_url_icon("basket_small_20x20.png")."' alt='' style='vertical-align:center; border:0px'/>";
if ($opac_rgaa_active) {
    print "&nbsp;";
    if (count($cart_)) {
        print $message;
    }
    print "<span class='label_basket'>" . cart::get_display_label() . "</span>
    </a>
    </span>";
    
} else {
    // NON RGAA - on conserve la structure du texte en dehors du lien
    print "
    </a>
    </span>&nbsp;";
    if(count($cart_)) {
        print $message." <a href='index.php?lvl=show_cart' onClick=\"parent.document.location='index.php?lvl=show_cart'; return false;\">";
    }
    print "<span class='label_basket'>" . cart::get_display_label() . "</span>";
    if(count($cart_)) {
        print "</a>";
    }
}
print "</span>";
$_SESSION["cart"]=$cart_;

if (empty($cart_) || !count($cart_)) {
	print "<script>document.getElementById('cart_info_iframe_content').setAttribute('class', 'basket_is_empty');</script>";
}

// Compatibilite avec l'ancien mecanisme
if (!empty($_SESSION["pmbopac_fontSize"])) {
	$_SESSION["accessibility"] = $_SESSION["pmbopac_fontSize"];
	unset($_SESSION["pmbopac_fontSize"]);
}

if ($opac_accessibility && isset($_SESSION["accessibility"])) {
	print "
		<script src='{$include_path}/javascript/accessibility.js'></script>
		<input type=\"hidden\" id=\"opacAccessibility\" name=\"opacAccessibility\" value=\"$opac_accessibility\" />
		<script>
			accessibilitySetFontSize('{$_SESSION["accessibility"]}');
		</script>";
}
if($opac_integrate_anonymous_cart && isset($_SESSION['cart_anonymous'])){
	print cart::integrate_anonymous_cart();
}
print "</body>
</html>";