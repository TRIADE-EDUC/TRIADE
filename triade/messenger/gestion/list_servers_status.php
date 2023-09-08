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
require ("../common/display_errors.inc.php"); 
//
if (isset($_COOKIE['im_nb_row_by_page'])) $nb_row_by_page = $_COOKIE['im_nb_row_by_page'];  else  $nb_row_by_page = '15';
//
if (isset($_GET['only_pending'])) $only_pending = $_GET['only_pending'];  else  $only_pending = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_servers_status);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_servers_title . "</title>";
display_header();
echo '<META http-equiv="refresh" content="120;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face=verdana size=2>";
if ( _SERVERS_STATUS != '')
{
  $img_status_0 = "bt_red.gif";
  $img_status_1 = "bt_yellow.gif";
  $img_status_2 = "bt_green.gif";
  //$img_status_0 = "state_away2.png";
  //$img_status_1 = "state_away.png";
  //$img_status_2 = "state_on.png";
  //
  require ("../common/sql.inc.php");
  //
  $requete  = " SELECT ID_SERVER, SRV_NAME, SRV_STATE, SRV_IP_ADDRESS, SRV_STATE_COMMENT ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "SRV_SERVERSTATE ";
  $requete .= " ORDER BY UPPER(SRV_NAME) ";
  //
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-W1a]", $requete);
  //
  echo "<BR/>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<THEAD>";
  echo "<TR>";
    echo "<TH align='center' COLSPAN='6' class='thHead'>";
    echo "<font face=verdana size=3><b>" . $l_admin_servers_list . " </B></font></TH>";
  echo "</TR>";
  //
  if ( mysqli_num_rows($result) > 0 )
  {
    echo "<TR>";
      display_row_table($l_admin_users_col_etat, '');
      display_row_table("&nbsp;" . $l_admin_servers_col_server . "&nbsp;", '');
      display_row_table("&nbsp;" . $l_admin_options_col_comment . "&nbsp;", '');
      display_row_table("&nbsp;" . $l_admin_session_col_ip . "&nbsp;", '');
      //display_row_table($l_admin_users_col_action, '');
      echo "<TD align='center' COLSPAN='2' class='catHead'> <font face='verdana' size='2'><b>" . $l_admin_users_col_action . "</b></font> </TD>\n";
    echo "</TR>";
    echo "</THEAD>";
    echo "<TFOOT>";
    // Dernière ligne : trier.
    echo "<TR>";
      echo "<TD align='center' COLSPAN='6' class='catBottom'>";
        if ( mysqli_num_rows($result) < 20 )
        {
          echo "<font face=verdana size=2>";
          echo "<A HREF='server_status_adding.php?lang=" . $lang . "&'>" . $l_admin_servers_creat . "</A>";
        }
      echo "</TD>";
    echo "</TR>";
    echo "</TFOOT>";
    echo "<TBODY>";
    //
    $last_first_letter_group = "";
    $last_first_letter_user = "";
    $last_user = "";
    $last_group = "";
    while( list ($id_srv, $srv_name, $srv_status, $srv_ip, $srv_comment) = mysqli_fetch_row ($result) )
    {
      echo "<TR>";
      //
      //
      // --------------------------------------------------------------------
      //
      //
      // Col STATUS
      //
      echo "<TD class='row1' align='center'>";
      switch ($srv_status)
      {
        case "1" : // 
          echo "<IMG SRC='" . _FOLDER_IMAGES . $img_status_1 . "' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_servers_status_1 . "' TITLE='" . $l_admin_servers_status_1 . "' border='0' />";
          break;
        case "2" : // 
          echo "<IMG SRC='" . _FOLDER_IMAGES . $img_status_2 . "' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_servers_status_2 . "' TITLE='" . $l_admin_servers_status_2 . "' border='0' />";
          break;
        default : // 0
          echo "<IMG SRC='" . _FOLDER_IMAGES . $img_status_0 . "' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_servers_status_0 . "' TITLE='" . $l_admin_servers_status_0 . "' border='0' />";
          break;
      }
      echo "</TD>";
      //
      // Col name
      //
      echo "<FORM METHOD='POST' ACTION='server_update_name.php?'>";
      echo "<TD valign='center' VALIGN='MIDDLE' class='row1'>";
      echo "<input type='text' name='srv_name' maxlength='60' value='" . $srv_name . "' size='30' class='post' />";
      echo " ";
      echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
      echo "<input type='hidden' name='id_srv' value = '" . $id_srv . "' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
      echo "</TD>";
      echo "</FORM>";
      //
      // Col comment
      //
      echo "<FORM METHOD='POST' ACTION='server_update_comment.php?'>";
      echo "<TD valign='center' VALIGN='MIDDLE' class='row1'>";
      echo "<input type='text' name='srv_comment' maxlength='150' value='" . $srv_comment . "' size='25' class='post' />";
      echo " ";
      echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
      echo "<input type='hidden' name='id_srv' value = '" . $id_srv . "' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
      echo "</TD>";
      echo "</FORM>";
      //
      // Col IP
      //
      echo "<FORM METHOD='POST' ACTION='server_update_ip.php?'>";
      echo "<TD valign='center' VALIGN='MIDDLE' class='row2'>";
      echo "<input type='text' name='srv_ip' maxlength='23' value='" . $srv_ip . "' size='20' class='post' />";
      echo " ";
      echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
      echo "<input type='hidden' name='id_srv' value = '" . $id_srv . "' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
      echo "</TD>";
      echo "</FORM>";
      //
      //
      // Col action
      echo "<TD valign='bottom' align='left' class='row1'>&nbsp;";
        //
        if ($srv_status <> 2)
        {
          $t = $l_admin_bt_update . " " . $l_admin_users_order_state . " : " . $l_admin_servers_status_2;
          echo "<A HREF='server_update_status.php?id_srv=" . $id_srv . "&status=2&srvname=" . $srv_name . "&lang=" . $lang . "&' class='cattitle'>";
          echo "<IMG SRC='" . _FOLDER_IMAGES . $img_status_2 . "' WIDTH='16' HEIGHT='16' ALT='" . $t . "' TITLE='" . $t . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
        }
        //
        if ($srv_status <> 1)
        {
          $t = $l_admin_bt_update . " " . $l_admin_users_order_state . " : " . $l_admin_servers_status_1;
          echo "<A HREF='server_update_status.php?id_srv=" . $id_srv . "&status=1&srvname=" . $srv_name . "&lang=" . $lang . "&' class='cattitle'>";
          echo "<IMG SRC='" . _FOLDER_IMAGES . $img_status_1 . "' WIDTH='16' HEIGHT='16' ALT='" . $t . "' TITLE='" . $t . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
        }
        //
        if ($srv_status <> 0)
        {
          $t = $l_admin_bt_update . " " . $l_admin_users_order_state . " : " . $l_admin_servers_status_0;
          echo "<A HREF='server_update_status.php?id_srv=" . $id_srv . "&status=0&srvname=" . $srv_name . "&lang=" . $lang . "&' class='cattitle'>";
          echo "<IMG SRC='" . _FOLDER_IMAGES . $img_status_0 . "' WIDTH='16' HEIGHT='16' ALT='" . $t . "' TITLE='" . $t . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
        }
      echo "</TD>";
        //
      echo "<TD valign='bottom' align='left' class='row2'>&nbsp;";
        echo "<A HREF='server_status_deleting.php?id_srv=" . $id_srv . "&srv_name=" . $srv_name . "&lang=" . $lang . "&' class='cattitle'>";
        echo "<IMG SRC='" . _FOLDER_IMAGES . "b_drop.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_bt_delete . "' TITLE='" . $l_admin_bt_delete . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
      echo "</TD>";
      //
      echo "</TR>";
      echo "\n";
    }
    echo "</TBODY>";
    //
    echo "</TABLE>";
  }
  else
  {
    echo "<TR>";
    echo "<TD colspan='5' ALIGN='CENTER' class='row2'>";
      echo "<font face='verdana' size='2'>" . $l_admin_servers_list_empty;
    echo "</TD>";
    echo "</TR>";
    echo "<TR>";

    echo "<FORM METHOD='POST' ACTION='server_status_adding.php?'>";
    echo "<TD valign='bottom' align='center' class='row2'>";
      echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_servers_creat . "' class='liteoption' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
    echo "</TD>";
    echo "</FORM>";

    echo "</TR>";
    echo "</TABLE>";
  }
	//
  mysqli_close($id_connect);
  //
  echo "<BR/>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<TR><TD COLSPAN='2' ALIGN='CENTER' class='catHead'><B>" . $l_legende . "</B></TD></TR>";

  echo "</TR><TR><TD ALIGN='CENTER' WIDTH='25' class='row1'>";
  echo "<IMG SRC='" . _FOLDER_IMAGES . $img_status_2 . "' WIDTH='16' HEIGHT='16'>";
  echo "</TD><TD class='row3'><font face=verdana size=2>&nbsp;" . $l_admin_servers_status_2 . "&nbsp;"; // $l_admin_users_col_etat . " : " . 
  echo "</TD>";

  echo "</TD></TR><TR><TD ALIGN='CENTER' class='row1'>";
  echo "<IMG SRC='" . _FOLDER_IMAGES . $img_status_1 . "' WIDTH='16' HEIGHT='16'>";
  echo "</TD><TD class='row3'><font face=verdana size=2>&nbsp;" . $l_admin_servers_status_1 . "&nbsp;";
  echo "</TD>";
  //
  echo "</TD></TR><TR><TD ALIGN='CENTER' class='row1'>";
  echo "<IMG SRC='" . _FOLDER_IMAGES . $img_status_0 . "' WIDTH='16' HEIGHT='16'>";
  echo "</TD><TD class='row3'><font face=verdana size=2>&nbsp;" . $l_admin_servers_status_0 . "&nbsp;";
  echo "</TD></TR>";
  echo "</TABLE>";
}
else
{
  echo "<BR/>";
  echo "<div class='warning'>";
  echo $l_admin_servers_cannot . "<BR/>";
  echo "</div>";
}
//
display_menu_footer();
//
echo "</body></html>";
?>