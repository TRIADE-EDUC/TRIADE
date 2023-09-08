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
//
require ("../common/display_errors.inc.php"); 
//
if (isset($_GET['ban'])) $ban = strtolower($_GET['ban']);  else  $ban = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_REQUEST['onglet'])) $onglet = $_REQUEST['onglet']; else $onglet = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_banned);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
//
switch ($ban)
{
  case "ip" : // ip address
    $title = $l_admin_ban_ip;
    $add_button = $l_admin_ban_add_ip;
    $ban_type = "I";
    $onglet = "1";
    break;
  case "pc" : // computer
    $title = $l_admin_ban_pc;
    $add_button = $l_admin_ban_add_pc;
    $ban_type = "P";
    $onglet = "2";
    break;
  default : // user
    $title = $l_admin_ban_users;
    $add_button = $l_admin_ban_add_user;
    $ban_type = "U";
    $onglet = "3";
    break; 
}
//
echo "<title>[IM] " . $title . "</title>";
display_header();
echo "<link href='../common/styles/onglets.css' rel='stylesheet' media='screen, print' type='text/css'/>";
//echo '<META http-equiv="refresh" content="120;url="> ';
?>

<script type="text/javascript">
function show_only(tabonglet, onglet)
{
  document.getElementById('tab_1').style.display="none";
  document.getElementById('tab_2').style.display="none";
  document.getElementById('tab_3').style.display="none";
  document.getElementById('onglet_1').className="onglet";
  document.getElementById('onglet_2').className="onglet";
  document.getElementById('onglet_3').className="onglet";
  //
  document.getElementById(tabonglet).style.display="block";
  document.getElementById(onglet).className="onglet-actif";
  //document.getElementById(onglet).style.display=document.getElementById(onglet).style.display=="none"?"block":"none";
} 

<?php
echo '</script>';
echo "</head>";
switch ($onglet)
{
  case "2" :
    echo "<body onLoad=\"show_only('tab_2', 'onglet_2');\">";
    break;
  case "3" :
    echo "<body onLoad=\"show_only('tab_3', 'onglet_3');\">";
    break;
  default :
    echo "<body onLoad=\"show_only('tab_1', 'onglet_1');\">";
    break; 
}
//
display_menu();
//
echo "<font face=verdana size=2>";
//
require ("../common/sql.inc.php");
//
//
echo "<div class='menu_onglet'>";
echo "<a class='onglet' id='onglet_1' href='#' onclick=\"show_only('tab_1', 'onglet_1');\" TITLE=''>" . $l_admin_log_ban_ip_address . "</a> &nbsp;";
echo "<a class='onglet' id='onglet_2' href='#' onclick=\"show_only('tab_2', 'onglet_2');\" TITLE=''>" . $l_admin_ban_pc . "</a> &nbsp;";
echo "<a class='onglet' id='onglet_3' href='#' onclick=\"show_only('tab_3', 'onglet_3');\" TITLE=''>" . $l_admin_ban_users . "</a> &nbsp;";
echo "<BR/>";
//echo "<div class='spacer'></div>";
echo "</div>";



function aff_tableau_onglet($ban_type)
{
  GLOBAL $PREFIX_IM_TABLE, $id_connect, $lang, $l_admin_bt_delete, $l_menu_list;
  //
  $ban = "";
  if ($ban_type == "I") $ban = "ip";
  if ($ban_type == "P") $ban = "pc";
  //
  $requete  = " SELECT BAN_VALUE ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "BAN_BANNED ";
  $requete .= " WHERE BAN_TYPE = '" . $ban_type . "' ";
  $requete .= " ORDER BY UPPER(BAN_VALUE) ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-L6a]", $requete);
  if ( mysqli_num_rows($result) > 0 )
  {
    echo "<BR/>";
    echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
    echo "<THEAD>";
    echo "<TR>";
      echo "<TH align='center' COLSPAN='10' class='thHead'>";
      echo "<font face=verdana size=3><b>" . $l_menu_list . " </B></font></TH>";
    echo "</TR>";
    echo "</THEAD>";
    echo "<TBODY>";
    //
    //
    $num_col = 0;
    echo "<TR>";
    while( list ($tban) = mysqli_fetch_row ($result) )
    {
      if ($num_col > 2) 
      {
        echo "</TR>\n";
        echo "<TR>";
        $num_col = 0;
      }
      $num_col++;
      //echo "<TR>";
      echo "<TD class='row1'><font face=verdana size=2>&nbsp;" . $tban . "&nbsp;</TD>";
      //
      echo "<TD valign='bottom' align='center' class='row1'>";
      //echo "<A HREF='user_delete.php?id_user=" . $id_user . "&tri=" . $tri . "&page=" . $page . "&lang=" . $lang . "' title='" . $l_admin_bt_delete . "'>";
      echo " <A HREF='ban_delete.php?ban_type=" . $ban_type . "&ban_value=" . $tban . "&ban=" . $ban . "&lang=" . $lang . "&' title='" . $l_admin_bt_delete . "'>";
      echo "<IMG SRC='" . _FOLDER_IMAGES . "b_drop.png' ALT='" . $l_admin_bt_delete . "' TITLE='" . $l_admin_bt_delete . "' WIDTH='16' HEIGHT='16' BORDER='0'></A>";
      echo " </TD>";
      echo " <TD class='row2'>";
      echo "<font face=verdana size=1>&nbsp;";
      echo " </TD>";
  /*
      echo "<FORM METHOD='POST' ACTION='ban_delete.php?'>";
      echo "<TD valign='bottom' align='center' class='row2'>";
        echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_delete . "' class='liteoption' />";
        echo "<input type='hidden' name='ban' value = '" . $ban . "' />";
        echo "<input type='hidden' name='ban_type' value = '" . $ban_type . "' />";
        echo "<input type='hidden' name='ban_value' value = '" . $tban . "' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
      echo "</TD>";
      echo "</FORM>";
      */
      //echo "</TR>\n";
    }
    echo "</TBODY>";
    //
    echo "</TABLE>";
  }
}


function display_file($file)
{
  $handle = fopen($file, "r");
  if ($handle) 
  {
    echo "<table width='250' cellspacing='1' cellpadding='1' class='forumline'>";
    echo "<TR>";
    echo "<TH class='thHead' colspan='6' align='center'>";
    echo $file;
    echo "</TH>";
    echo "</TR>\n";
    echo "<TR>";
    $num_col = 0;
    while (!feof($handle)) 
    {
      $buffer = fgets($handle, 4096);
      $buffer = trim($buffer);
      if (strlen($buffer) > 1)
      {
        if ($num_col > 5) 
        {
          echo "</TR>\n";
          echo "<TR>";
          $num_col = 0;
        }
        $num_col++;
        echo "<TD class='row2'><font face='verdana' size='2'>";
        echo $buffer;
        echo "</TD>";
      }
    }
    echo "</TR>";
    fclose($handle);
    echo "</TABLE>";
  }
}



$folder = "../common/config/";



#
##
###
#######>------------------------------------------------ TAB 1 ------------------------------------------------
###
##
#

echo "<div id='tab_1' style='display:none' ";
//if ($javactif != '') echo "style='display:none'";
echo ">";

//echo "<BR/>";
echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='700'>";
echo "<TR>";
echo "<TH align='center' COLSPAN='4' class='thHead'>";
echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_ban_ip . "&nbsp;</b></font>";
echo "</TH>";
echo "</TR>";

echo "<TR>";
echo "<TD align='center' COLSPAN='4' class='catHead'>";
echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_ban_add_ip . "&nbsp;</b></font>";
echo "</TD>";
echo "</TR>";
echo "<TR>";

echo "<FORM METHOD='POST' ACTION='ban_add.php?'>";
echo "<TD class='row2' width='20%'>&nbsp;";
echo "</TD>";
echo "<TD class='row1' align='center'>";
echo "<font face=verdana size=2>";
echo "<input type='text' name='ban_value' maxlength='50' value='' size='35' class='post' />";
echo " &nbsp; ";
//echo "</TD>";
//echo "<TD class='row1' align='center'>";
echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_add . "' class='liteoption' />";
echo "<input type='hidden' name='ban' value = 'ip' />";
echo "<input type='hidden' name='ban_type' value = 'I' />";
echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
echo "</TD>";
echo "<TD class='row2' width='20%'>&nbsp;";
echo "</TD>";
echo "</TR>";
echo "</FORM>";

echo "</TABLE>";

aff_tableau_onglet('I');

//if ( ($ban_type == "I") and (is_readable($folder . "ban_ip.txt")) )  
if (is_readable($folder . "ban_ip.txt"))
{
  $l_admin_ban_dont_need_file = str_replace("zzz", "<I>" . $folder . "ban_ip.txt</I>", $l_admin_ban_dont_need_file);
  echo "<FORM METHOD='POST' ACTION='ban_import_file.php?'>";
  echo "<BR/><BR/><div class='notice'><FONT COLOR='RED'>" . $l_admin_ban_dont_need_file . "</font>";
  echo "<BR/><BR/><INPUT TYPE='submit' VALUE = '" . $l_admin_ban_import_delete . "' class='liteoption' />";
  echo "<input type='hidden' name='ban' value = '" . $ban . "' />";
  echo "<input type='hidden' name='ban_type' value = '" . $ban_type . "' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
  echo "</div>";
  echo "</FORM>";
  display_file($folder . "ban_ip.txt");
}

echo "</div>";



#
##
###
#######>------------------------------------------------ TAB 2 ------------------------------------------------
###
##
#

echo "<div id='tab_2' style='display:none' ";
//if ($javactif != '') echo "style='display:none'";
echo ">";

//echo "<BR/>";
echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='700'>";
echo "<TR>";
echo "<TH align='center' COLSPAN='3' class='thHead'>";
echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_ban_pc . "&nbsp;</b></font>";
echo "</TH>";
echo "</TR>";

echo "<TR>";
echo "<TD align='center' COLSPAN='3' class='catHead'>";
echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_ban_add_pc . "&nbsp;</b></font>";
echo "</TD>";
echo "</TR>";
echo "<TR>";

echo "<FORM METHOD='POST' ACTION='ban_add.php?'>";
echo "<TD class='row2' width='20%'>&nbsp;";
echo "</TD>";
echo "<TD class='row1' align='center'>";
echo "<font face=verdana size=2>";
echo "<input type='text' name='ban_value' maxlength='50' value='' size='35' class='post' />";
echo " &nbsp; ";
//echo "</TD>";
//echo "<TD class='row1' align='center'>";
echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_add . "' class='liteoption' />";
echo "<input type='hidden' name='ban' value = 'pc' />";
echo "<input type='hidden' name='ban_type' value = 'P' />";
echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
echo "</TD>";
echo "<TD class='row2' width='20%'>&nbsp;";
echo "</TD>";
echo "</TR>";
echo "</FORM>";

echo "<TR>";
echo "<TD align='left' COLSPAN='9' class='row2' >"; //  'style:'
  echo "<font face=verdana size=2>";
  echo "";
  echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_user_pc_ban.png' ALT='" . $l_admin_users_ban_pc . "' TITLE='" . $l_admin_users_ban_pc . "' WIDTH='32' HEIGHT='32' ALIGN='LEFT' BORDER='0'>";
  echo $l_admin_users_how_to_ban_pc;
echo "</TD>";
echo "</TR>";

echo "</TABLE>";

aff_tableau_onglet('P');

echo "</div>";




#
##
###
#######>------------------------------------------------ TAB 3 ------------------------------------------------
###
##
#

echo "<div id='tab_3' style='display:none' ";
//if ($javactif != '') echo "style='display:none'";
echo ">";

//echo "<BR/>";
echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='700'>";
echo "<TR>";
echo "<TH align='center' COLSPAN='3' class='thHead'>";
echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_ban_users . "&nbsp;</b></font>";
echo "</TH>";
echo "</TR>";

echo "<TR>";
echo "<TD align='center' COLSPAN='3' class='catHead'>";
echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_ban_add_user . "&nbsp;</b></font>";
echo "</TD>";
echo "</TR>";
echo "<TR>";

echo "<FORM METHOD='POST' ACTION='ban_add.php?'>";
echo "<TD class='row2' width='20%'>&nbsp;";
echo "</TD>";
echo "<TD class='row1' align='center'>";
echo "<font face=verdana size=2>";
echo "<input type='text' name='ban_value' maxlength='50' value='' size='35' class='post' />";
echo " &nbsp; ";
//echo "</TD>";
//echo "<TD class='row1' align='center'>";
echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_add . "' class='liteoption' />";
echo "<input type='hidden' name='ban' value = '' />";
echo "<input type='hidden' name='ban_type' value = 'U' />";
echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
echo "</TD>";
echo "<TD class='row2' width='20%'>&nbsp;";
echo "</TD>";
echo "</TR>";
echo "</FORM>";

echo "</TABLE>";

aff_tableau_onglet('U');

//if ( ($ban_type == "U") and (is_readable($folder . "ban_nickname.txt")) )
if (is_readable($folder . "ban_nickname.txt"))
{
  $l_admin_ban_dont_need_file = str_replace("zzz", "<I>" . $folder . "ban_nickname.txt</I>", $l_admin_ban_dont_need_file);
  echo "<FORM METHOD='POST' ACTION='ban_import_file.php?'>";
  echo "<BR/><BR/><div class='notice'><FONT COLOR='RED'>" . $l_admin_ban_dont_need_file . "</font>";
  echo "<BR/><BR/><INPUT TYPE='submit' VALUE = '" . $l_admin_ban_import_delete . "' class='liteoption' />";
  echo "<input type='hidden' name='ban' value = '" . $ban . "' />";
  echo "<input type='hidden' name='ban_type' value = '" . $ban_type . "' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
  echo "</div>";
  echo "</FORM>";
  display_file($folder . "ban_nickname.txt");
}


echo "</div>";


//
mysqli_close($id_connect);
//
//

//
display_menu_footer();
//
echo "</body></html>";
?>