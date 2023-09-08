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
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_conference_title . "</title>";
display_header();
echo '<META http-equiv="refresh" content="120;url=" />';
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face=verdana size=2>";
if ( _ALLOW_CONFERENCE != '' )
{
  //
  require ("../common/sql.inc.php");
  //
  echo "<font face=verdana size=2>";
  //
  $requete1  = " select CNF.ID_CONFERENCE, USR.USR_USERNAME, USR.USR_NICKNAME, CNF.CNF_DATE_CREAT, CNF.CNF_TIME_CREAT ";
  $requete1 .= " FROM " . $PREFIX_IM_TABLE . "CNF_CONFERENCE CNF, " . $PREFIX_IM_TABLE . "USR_USER USR ";
  $requete1 .= " WHERE CNF.ID_USER = USR.ID_USER ";
  $requete1 .= " ORDER BY CNF.ID_CONFERENCE ";
  $result1 = mysqli_query($id_connect, $requete1);
  if (!$result1) error_sql_log("[ERR-A5a]", $requete1);
  echo "<BR/>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<THEAD>";
  echo "<TR>";
    echo "<TH align='center' COLSPAN='4' class='thHead'>";
    echo "<font face=verdana size=3><b>" . $l_admin_conference_title . " </B></font></TH>";
  echo "</TR>";
  //
  if ( mysqli_num_rows($result1) > 0 )
  {
    echo "<TR>";
      display_row_table($l_admin_conference_col_creator, '');
      display_row_table($l_admin_users_col_creat, '');
      display_row_table($l_admin_conference_col_partaker, '250');
      display_row_table($l_admin_users_col_action, '');
    echo "</TR>";
    echo "</THEAD>";
    echo "<TFOOT>";
    echo "</TFOOT>";
    //
    echo "<TBODY>";
    //
    while( list ($id_conf, $usercreat, $nickcreat, $dt_creat, $tm_creat) = mysqli_fetch_row ($result1) )
    {
      if ( ($nickcreat != '') and (_ALLOW_UPPERCASE_SPACE_USERNAME != '') ) $usercreat = $nickcreat;
      //
      $requete2  = " select USR.USR_USERNAME, USR.USR_NICKNAME, USC.USC_ACTIVE, USR.ID_USER ";
      $requete2 .= " FROM " . $PREFIX_IM_TABLE . "USC_USERCONF USC, " . $PREFIX_IM_TABLE . "USR_USER USR ";
      $requete2 .= " WHERE USR.ID_USER = USC.ID_USER ";
      $requete2 .= " AND USC.ID_CONFERENCE = " . $id_conf; 
      $requete2 .= " ORDER BY USR.USR_USERNAME "; 
      $result2 = mysqli_query($id_connect, $requete2);
      if (!$result2) error_sql_log("[ERR-A5b]", $requete2);
      if ( mysqli_num_rows($result2) > 0 )
      {
        echo "<TR>";
        $num_lig = 0;
        while( list ($user, $nickname, $actif, $id_user) = mysqli_fetch_row ($result2) )
        {
          if ( ($nickname != '') and (_ALLOW_UPPERCASE_SPACE_USERNAME != '') ) $user = $nickname;
          $num_lig++;
          if ($num_lig == 1)
          {
            echo "<TD class='row1'>";
            echo "<font face='verdana' size='2'>";
            echo "&nbsp;" . $usercreat . "&nbsp;";
          }
          else
            echo "<TD class='row3'> ";
          //
          echo "</TD>";
          // --------------------
          //
          if ($num_lig == 1)
          {
            echo "<TD class='row1' align='center'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            if ($dt_creat == '0000-00-00')
              $datcreat = 	'&nbsp;';
            else
              $datcreat = date($l_date_format_display, strtotime($dt_creat));
            //
            if ( $datcreat != date($l_date_format_display) )
              echo "<font color='gray'>";
            //
            echo $datcreat . " " . substr($tm_creat, 0, 5) . "&nbsp;</font>";
          }
          else
            echo "<TD class='row3'> ";
          //
          echo "</TD>";
          // --------------------
          //
          echo "<TD class='row1'>";
          echo "<font face='verdana' size='2'>";
          //if ($user == $usercreat) echo "<B>";
          if (intval($actif == 0)) echo "<I>";
          echo "&nbsp;" . $user . "<BR/>";
          if (intval($actif == 0)) echo "</I>";
          echo "</TD>";
          // --------------------
          //
          echo "<FORM METHOD='POST' ACTION='conference_delete_user.php?'>";
          //echo "<FORM METHOD='POST' ACTION='conference_delete.php?'>";
          echo "<TD valign='bottom' align='center' class='row2'>";
            echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_delete . "' class='liteoption' />";
            echo "<input type='hidden' name='id_user' value = '" . $id_user . "' />";
            echo "<input type='hidden' name='id_conf' value = '" . $id_conf . "' />";
            echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
            echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
          echo "</TD>";
          echo "</FORM>";
          // --------------------
          //
          echo "</TR>";
          echo "\n";
        }
        echo "<TR>";
        echo "<TD  class='row2' COLSPAN='5'> </TD>";
        echo "</TR>";
      }
      else
      {
        $requete2  = " delete FROM " . $PREFIX_IM_TABLE . "USC_USERCONF ";
        $requete2 .= " WHERE ID_CONFERENCE = " . $id_conf; 
        $result2 = mysqli_query($id_connect, $requete2);
        if (!$result2) error_sql_log("[ERR-A5c]", $requete2);
        //
        $requete2  = " delete FROM " . $PREFIX_IM_TABLE . "CNF_CONFERENCE ";
        $requete2 .= " WHERE ID_CONFERENCE = " . $id_conf; 
        $result2 = mysqli_query($id_connect, $requete2);
        if (!$result2) error_sql_log("[ERR-A5d]", $requete2);
      }
    }
    echo "</TBODY>";
    //
    echo "</TABLE>";
  }
  else
  {
    echo "<TR>";
    echo "<TD colspan='4' ALIGN='CENTER' class='row2'>";
      echo "<font face='verdana' size='2'>" . $l_admin_conference_no_conference;
    echo "</TD>";
    echo "</TR>";
    echo "</TABLE>";
    //
    $requete2  = " delete FROM " . $PREFIX_IM_TABLE . "USC_USERCONF ";
    $result2 = mysqli_query($id_connect, $requete2);
    if (!$result2) error_sql_log("[ERR-A5e]", $requete2);
    //
    $requete2  = " delete FROM " . $PREFIX_IM_TABLE . "CNF_CONFERENCE ";
    $result2 = mysqli_query($id_connect, $requete2);
    if (!$result2) error_sql_log("[ERR-A5f]", $requete2);
  }
	//
  mysqli_close($id_connect);
}
else
{
  echo "<BR/>";
  echo "<div class='warning'>";
  echo $l_admin_conference_cannot_use_1;
  echo "</div>";
}
//
display_menu_footer();
//
echo "</body></html>";
?>