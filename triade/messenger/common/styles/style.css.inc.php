<?php
//
define('_FOLDER_IMAGES', '../common/styles/default/images/'); 
//
$file_style_css = "";
$style_css_list = array();
//
$style_css_list[] = "subSilverPlus.css";
$style_css_list[] = "subSilver.css";
$style_css_list[] = "saphir.css";
$style_css_list[] = "BlueDim.css";
$style_css_list[] = "Igloo.css";
$style_css_list[] = "jasidogdotcom.css";
$style_css_list[] = "SoftBlue.css";
$style_css_list[] = "SoftBlueV2.css";
//
$style_css_list[] = "olympus.css";
$style_css_list[] = "newacts.css";
$style_css_list[] = "SwiftBlue.css";
$style_css_list[] = "BlueIce.css";
$style_css_list[] = "mxSilver.css";
$style_css_list[] = "radio.css";
$style_css_list[] = "Macinscott.css";
//
#$style_css_list[] = "Avalanche.css";
#$style_css_list[] = "MorpheusXBlue.css";
#$style_css_list[] = "creamywhite.css";
#$style_css_list[] = "Xbox.css";
#$style_css_list[] = "ExFlame.css";
//
//if (file_exists("../common/config/style.config.inc.php")) include("../common/config/style.config.inc.php");
if (isset($_COOKIE['im_style_css'])) $file_style_css = $_COOKIE['im_style_css'];  else  $file_style_css = "";
//
if ($file_style_css == "") $file_style_css = "subSilverPlus.css";
if (!in_array($file_style_css, $style_css_list)) $file_style_css = "subSilverPlus.css";
if (!file_exists("../common/styles/" . $file_style_css)) $file_style_css = "subSilverPlus.css";
//
?>