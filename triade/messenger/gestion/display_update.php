<?php 	
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2015 THeUDS           **
 **  Web:            http://www.theuds.com            **
 **                  http://www.intramessenger.net    **
 **  Licence :       GPL (GNU Public License)         **
 **  http://opensource.org/licenses/gpl-license.php   **
 *******************************************************/

/*******************************************************
 **       This file is part of IntraMessenger-server  **
 **                                                   **
 **  IntraMessenger is a free software.               **
 **  IntraMessenger is distributed in the hope that   **
 **  it will be useful, but WITHOUT ANY WARRANTY.     **
 *******************************************************/
//
if (isset($_POST['lang'])) $lang = $_POST['lang'];  else  $lang = "";
if (isset($_POST['style'])) $style = $_POST['style'];  else  $style = "";
if (isset($_POST['position_menu'])) $position_menu = $_POST['position_menu'];  else  $position_menu = "";
if (isset($_POST['full_menu'])) $full_menu = $_POST['full_menu'];  else  $full_menu = "";
if (isset($_POST['back_color'])) $back_color = $_POST['back_color'];  else  $back_color = "";
if (isset($_POST['option_charset'])) $option_charset = $_POST['option_charset'];  else  $option_charset = "";
//
//--------------------------------------------------------------
// Afficher le menu à droite ou en haut
//--------------------------------------------------------------
switch ($position_menu)
{
  case "T" :
    $position_menu = "TOP";
    break;
  case "R" :
    $position_menu = "RIGHT";
    break;
  default : // L
    $position_menu = "LEFT";
    break;
}
//  
setcookie("im_position_menu", $position_menu, mktime(0,0,0,12,31,2030));
//
//--------------------------------------------------------------
// Activer menus en entier, ou juste ce qui est nécessaire.
//--------------------------------------------------------------
if ($full_menu == "1") 
  $full_menu = "X";
else
  $full_menu = "";
//  
setcookie("im_full_menu", $full_menu, mktime(0,0,0,12,31,2030));
//
//--------------------------------------------------------------
// Activer le style sélectionné
//--------------------------------------------------------------
setcookie("im_style_css", $style, mktime(0,0,0,12,31,2030));
//
//--------------------------------------------------------------
// Activer la couleur de fond sélectionnée
//--------------------------------------------------------------
setcookie("im_background_color", $back_color, mktime(0,0,0,12,31,2030));
//
//--------------------------------------------------------------
// Choix du charset
//--------------------------------------------------------------
setcookie("im_charset", $option_charset, mktime(0,0,0,12,31,2030));
//
//
//--------------------------------------------------------------
header("location:display_updating.php?lang=" . $lang . "&");
die();
?>