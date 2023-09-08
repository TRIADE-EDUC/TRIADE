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
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php");
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/menu.inc.php"); // après config.inc.php !
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_servers_title . "</title>";
display_header();
if (_SERVERS_STATUS == "") // not allowed to display...
{
  echo '<META http-equiv="refresh" content="0;url=../"> ';
  die();
}
echo '<META http-equiv="refresh" content="400;url="> ';
//echo "<link href='../common/styles/defil.css' rel='stylesheet' media='screen, print' type='text/css'/>";
echo "</head>";
echo "<body background='" . _FOLDER_IMAGES . f_background_image_color() . "background.jpg'>";
//
echo "<font face=verdana size=2>";
//
echo "<CENTER>";
echo "<SMALL><SMALL><BR/></SMALL></SMALL>";
if ($lang != 'FR') echo " <A HREF='?lang=FR&' TITLE='Français'><IMG SRC='../images/flags/fr.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'EN') echo " <A HREF='?lang=EN&' TITLE='English'><IMG SRC='../images/flags/us.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'IT') echo " <A HREF='?lang=IT&' TITLE='Italian'><IMG SRC='../images/flags/it.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'ES') echo " <A HREF='?lang=ES&' TITLE='Spanish'><IMG SRC='../images/flags/es.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'PT') echo " <A HREF='?lang=PT&' TITLE='Portuguese'><IMG SRC='../images/flags/pt.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'RO') echo " <A HREF='?lang=RO&' TITLE='Romana'><IMG SRC='../images/flags/ro.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'DE') echo " <A HREF='?lang=DE&' TITLE='German'><IMG SRC='../images/flags/de.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'NL') echo " <A HREF='?lang=NL&' TITLE='Netherlands'><IMG SRC='../images/flags/nl.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
echo "<SMALL><SMALL><BR/></SMALL></SMALL>";
if ($l_time_short_format_display == '') $l_time_short_format_display = $l_time_format_display;
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
  $requete  = " SELECT ID_SERVER, SRV_NAME, SRV_STATE, SRV_STATE_COMMENT, SRV_STATE_DATE, SRV_STATE_TIME ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "SRV_SERVERSTATE ";
  $requete .= " ORDER BY UPPER(SRV_NAME) ";
  //
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-W1z]", $requete);
  //
  echo "<BR/>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<THEAD>";
  echo "<TR>";
    echo "<TH align='center' COLSPAN='5' class='thHead'>";
    echo "<font face=verdana size=3>&nbsp;<b>" . $l_admin_servers_list . " </B>&nbsp;</font></TH>";
  echo "</TR>";
  if ( mysqli_num_rows($result) > 0 )
  {
    echo "<TR>";
      display_row_table("&nbsp;" . $l_admin_users_col_etat . "&nbsp;", '');
      display_row_table("&nbsp;" . $l_admin_servers_col_server . "&nbsp;", '');
      display_row_table("&nbsp;" . $l_admin_session_col_time . "&nbsp;", '');
      display_row_table("&nbsp;" . $l_admin_options_col_comment . "&nbsp;", '');
      //echo "<TD align='center' COLSPAN='2' class='catHead'> <font face='verdana' size='2'><b>" . $l_admin_users_col_action . "</b></font> </TD>\n";
    echo "</TR>";
    echo "</THEAD>";
    echo "<TFOOT>";
    // Dernière ligne : trier.
    echo "<TR>";
      echo "<TD align='center' COLSPAN='5' class='catBottom'>";
        //echo "<font face=verdana size=2>";
        //echo "<A HREF='server_status_adding.php?lang=" . $lang . "&'>" . $l_admin_servers_creat . "</A>";
      echo "</TD>";
    echo "</TR>";
    echo "</TFOOT>";
    echo "<TBODY>";
    //
    $last_first_letter_group = "";
    $last_first_letter_user = "";
    $last_user = "";
    $last_group = "";
    while( list ($id_srv, $srv_name, $srv_status, $srv_comment, $srv_date, $srv_time) = mysqli_fetch_row ($result) )
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
      if ($srv_status == 2)
        echo "<TD valign='center' class='row2'>";
      else
        echo "<TD valign='center' class='row1'>";
      //
      echo "&nbsp;" . $srv_name . "&nbsp;";
      echo "</TD>";
      //
      if ($srv_status == 2)
      {
        echo "<TD class='row2'>&nbsp;";
        echo "</TD>";
      }
      else
      {
        echo "<TD align='center' class='row1'>&nbsp;";
          if ($srv_date != '0000-00-00') 
          {
            $srv_date = date($l_date_format_display, strtotime($srv_date));
            if ( $srv_date != date($l_date_format_display) )
              echo "<font color='gray'>" . $srv_date . "</font>";
            else
            {
              $srv_time = date($l_time_short_format_display, strtotime($srv_time));
              echo $srv_time;
            }
          }
        echo "</TD>";
      }
      //
      if ($srv_status == 2)
        echo "<TD valign='center' class='row2'>";
      else
        echo "<TD valign='center' class='row1'>";
      //
      echo "&nbsp;" . $srv_comment . "&nbsp;";
      echo "</TD>";
      //
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
  echo $l_admin_servers_cannot . "<BR/>";
}
//
echo "</body></html>";
?>