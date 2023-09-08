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
if ( !defined('INTRAMESSENGER') )
{
  exit;
}
//
if ($page == 'all')
  $nb_row_by_page = 1000;
else
{
  if (intval($nb_row_by_page) < 15) $nb_row_by_page = 15;
  if ( ($nb_row_by_page <> 15) and ($nb_row_by_page <> 20) and ($nb_row_by_page <> 30) and ($nb_row_by_page <> 40) and ($nb_row_by_page <> 50) and ($nb_row_by_page <> 100) )  $nb_row_by_page = 20;
}
$page = intval($page);
if ($page < 1) $page = 1;
//
$requete  = " select ID_SHOUT, ID_GROUP_DEST, SBX_DISPLAY, ID_USER_AUT, SBX_TIME, SBX_DATE, SBX_RATING, SBX_TEXT";
$requete .= " FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
$requete .= " WHERE SBX_DISPLAY < 1 ";
if ($id_user > 0)
  $requete .= " and ID_USER_AUT = " . $id_user;
else
  $requete .= " and ID_GROUP_DEST = " . $id_grp;
//$requete .= " ORDER BY SBX_DATE, SBX_TIME ";
$requete .= " ORDER BY ID_SHOUT DESC ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-V1a]", $requete);
//
if ( mysqli_num_rows($result) > 0 )
{
  echo "<BR/>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' WIDTH='95%'>";
  echo "<THEAD>";
  echo "<TR>";
    echo "<TH align='center' COLSPAN='5' class='thHead'>";
    echo "<font face=verdana size=3><b>" . $l_admin_options_shoutbox_title_short . " - " . $l_admin_users_info_wait_valid . "</B></font></TH>";
  echo "</TR>";
  echo "<TR>";
    if (strlen($l_time_short_format_display) == 3)
      display_row_table($l_admin_users_col_creat, '145');
    else
      display_row_table($l_admin_users_col_creat, '165');
    //
    display_row_table($l_admin_conference_col_creator, '120');
    display_row_table($l_admin_mess_message, '');
    display_row_table($l_admin_users_col_action, '90');
  echo "</TR>";
  echo "</THEAD>";
  echo "\n";
  //echo "<TFOOT>";
  //echo "</TFOOT>";
  echo "<TBODY>";
  //
  $id_shout_max = 0;
  while( list ($id_shout, $id_group_dest, $s_display, $id_aut, $s_time, $s_date, $rating, $txt) = mysqli_fetch_row ($result) )
  {
    if ($id_shout > $id_shout_max) $id_shout_max = $id_shout;
    $s_date = date($l_date_format_display, strtotime($s_date));
    $s_time = date($l_time_short_format_display, strtotime($s_time));
    //$username = f_get_username_of_id($id_aut);
    $username = f_get_username_nickname_of_id($id_aut); // affichage avec majuscules et espaces
    echo "<TR>";
    //
    echo "<TD class='row3' ALIGN='CENTER'>";
    echo "<font face='verdana' size='2'>";
    if ( $s_date != date($l_date_format_display) ) echo "<font color='gray'>";
    //echo "&nbsp;" . $s_date . "-" . $s_time . "&nbsp;";
    echo $s_date . "-" . $s_time;
    echo "</TD>";
    //
    // --------------------
    //
    echo "<TD class='row2'>";
    echo "<font face='verdana' size='2'>&nbsp;";
    //echo "&nbsp;" . $username . "&nbsp;";
    echo "<A HREF='user.php?id_user=" . $id_aut . "&lang=" . $lang . "&' alt='" . $l_clic_on_user . "' title='" . $l_clic_on_user . "' class='cattitle'>";
    echo $username . "</A>&nbsp;";
    echo "</TD>";
    //
    // --------------------
    //
    echo "<TD class='row2'><I>";
    echo "<font face='verdana' size='2'>";
    echo "&nbsp;" . f_decode64_wd($txt) . "&nbsp;";
    echo "</TD>";
    //
    // --------------------
    //
    echo "<FORM METHOD='POST' ACTION='shoutbox_delete_message.php?'>";
      echo "<TD valign='MIDDLE' align='center' class='row2'>";
      echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_delete . "' class='liteoption' />";
      echo "<input type='hidden' name='id_grp' value = '" . $id_grp . "' />";
      echo "<input type='hidden' name='id_shout' value = '" . $id_shout . "' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
      echo "</TD>";
    echo "</FORM>";
    // --------------------
    //
    echo "</TR>";
    echo "\n";
  }
  echo "</TBODY>";
  echo "<TR>";
    echo "<TD align='center' COLSPAN='4' class='catHead'>";
      echo "<FORM METHOD='POST' ACTION='shoutbox_valid_messages.php?'>";
      echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_shoutbox_valid_messages . "' class='liteoption' />";
      echo "<input type='hidden' name='id_grp' value = '" . $id_grp . "' />";
      echo "<input type='hidden' name='id_shout_max' value = '" . $id_shout_max . "' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
      echo "</FORM>";
    echo "</TD>";
  echo "</TR>";
  //
  echo "</TABLE>";
}
//
//
//
//
$requete  = " select ID_SHOUT, ID_GROUP_DEST, SBX_DISPLAY, ID_USER_AUT, SBX_TIME, SBX_DATE, SBX_RATING, SBX_TEXT";
$requete .= " FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
$requete .= " WHERE SBX_DISPLAY > 0 ";
if ($id_user > 0)
  $requete .= " and ID_USER_AUT = " . $id_user;
else
  $requete .= " and ID_GROUP_DEST = " . $id_grp;
//$requete .= " ORDER BY SBX_DATE, SBX_TIME ";
$requete .= " ORDER BY ID_SHOUT DESC ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-V1b]", $requete);
$nb_row = mysqli_num_rows($result);
echo "<BR/>";
//
//
// Page défilement :
echo "<TABLE cellspacing='3' cellpadding='0' BORDER='0' WIDTH='96%'>";
if ($nb_row_by_page > 50)
{
  echo "<TR><TD COLSPAN='2' ALIGN='RIGHT'>";
  display_nb_page($page, $nb_row_by_page, $nb_row, "&lang=" . $lang . "&id_grp=" . $id_grp . "&id_user=" . $id_user . "&'", "");
  echo "</TD></TR>";
}
echo "<TR><TD COLSPAN='2'>"; //
//
//
echo "<TABLE cellspacing='1' cellpadding='1'  WIDTH='100%' class='forumline' >"; // WIDTH='95%'
echo "<THEAD>";
echo "<TR>";
  echo "<TH align='center' COLSPAN='5' class='thHead'>";
  echo "<font face=verdana size=3><b>" . $l_admin_options_shoutbox_title_long . " </B></font>";
  $chemin = "../" . _PUBLIC_FOLDER . "/rss/" . "shoutbox" ;
  if ($id_grp > 0) $chemin .= f_encode64($id_grp);
  $chemin .= ".xml";
  if ( _SHOUTBOX_PUBLIC != '') echo "<A HREF='" . $chemin . "'><img src='" . _FOLDER_IMAGES . "rss.png' ALT='RSS' TITLE='RSS' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
echo "</TH>";
echo "</TR>";
//
if ( mysqli_num_rows($result) > 0 )
{
  echo "<TR>";
    if (strlen($l_time_short_format_display) == 3)
      display_row_table($l_admin_users_col_creat, '145');
    else
      display_row_table($l_admin_users_col_creat, '165');
    //
    display_row_table($l_admin_conference_col_creator, '120');
    display_row_table($l_admin_mess_message, '');
    display_row_table("&nbsp;" . $l_admin_shoutbox_average . "&nbsp;", '20');
    display_row_table($l_admin_users_col_action, '90');
  echo "</TR>";
  echo "</THEAD>";
  echo "<TFOOT>";
  echo "</TFOOT>";
  //
  echo "<TBODY>";
  //
  $row_num = 0;
  $display_start = 0;
  $display_end = 0;
  if ($nb_row > $nb_row_by_page)
  {
    $nb_page = ceil($nb_row / $nb_row_by_page);
    if ($page < 1) $page = 1;
    if ($page > $nb_page) $page = $nb_page;
    $display_start = ( ($page - 1) * $nb_row_by_page + 1);
    $display_end = ($display_start + $nb_row_by_page - 1);
    if ($display_end > $nb_row) $display_end = $nb_row;
  }
  while( list ($id_shout, $id_group_dest, $s_display, $id_aut, $s_time, $s_date, $rating, $txt) = mysqli_fetch_row ($result) )
  {
    $row_num++;
    if (  ($display_start <= 0) or ($display_end <= 0) or ( ($row_num >= $display_start) and ($row_num <= $display_end) )  )
    {
      $s_date = date($l_date_format_display, strtotime($s_date));
      $s_time = date($l_time_short_format_display, strtotime($s_time));
      //$username = f_get_username_of_id($id_aut);
      $username = f_get_username_nickname_of_id($id_aut); // affichage avec majuscules et espaces
      echo "<TR>";
      //
      echo "<TD class='row2' ALIGN='CENTER'>";
      echo "<font face='verdana' size='2'>";
      if ( $s_date != date($l_date_format_display) ) echo "<font color='gray'>";
      //echo "&nbsp;" . $s_date . " - " . $s_time . "&nbsp;";
      echo $s_date . " - " . $s_time;
      echo "</TD>";
      //
      // --------------------
      //
      echo "<TD class='row1'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      //echo "&nbsp;" . $username . "&nbsp;";
      echo "<A HREF='user.php?id_user=" . $id_aut . "&lang=" . $lang . "&' alt='" . $l_clic_on_user . "' title='" . $l_clic_on_user . "' class='cattitle'>";
      echo $username . "</A>&nbsp;";
      echo "</TD>";
      //
      // --------------------
      //
      echo "<TD class='row1'>";
      echo "<font face='verdana' size='2'>";
      echo "&nbsp;" . f_decode64_wd($txt) . "&nbsp;";
      echo "</TD>";
      //
      // --------------------
      //
      echo "<TD class='row2' ALIGN='CENTER'>";
      echo "<font face='verdana' size='2'>";
      if (intval($rating) > 0) 
      {
        echo "<IMG SRC='" . _FOLDER_IMAGES . "flag-green.png' WIDTH='16' HEIGHT='16' ALT='+" . $rating . "' TITLE='+" . $rating . "'>";
        echo "<font color='green'>&nbsp;+" . $rating . "</font>";
      }
      if (intval($rating) < 0) 
      {
        echo "<IMG SRC='" . _FOLDER_IMAGES . "flag-red.png' WIDTH='16' HEIGHT='16' ALT='" . $rating . "' TITLE='" . $rating . "'>";
        echo "<font color='red'>&nbsp;" . $rating . "</font>";
      }
      echo "</TD>";
      //
      // --------------------
      //
      echo "<FORM METHOD='POST' ACTION='shoutbox_delete_message.php?'>";
        echo "<TD valign='bottom' align='center' class='row2'>";
        echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_delete . "' class='liteoption' />";
        echo "<input type='hidden' name='id_grp' value = '" . $id_grp . "' />";
        echo "<input type='hidden' name='id_shout' value = '" . $id_shout . "' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
        echo "</TD>";
      echo "</FORM>";
      // --------------------
      //
      echo "</TR>";
      echo "\n";
    }
  }
  echo "</TBODY>";
  //
  echo "</TABLE>";
  //
  //
  // Défilement
  //
  echo "</TD></TR>";
  echo "<TR><TD>";
  echo "</TD><TD ALIGN='RIGHT'>";
  display_nb_page($page, $nb_row_by_page, $nb_row, "&lang=" . $lang . "&id_grp=" . $id_grp . "&id_user=" . $id_user . "&'", "UP");
  echo "</TD></TR>";
  echo "</TABLE>";
}
else
{
  echo "<TR>";
  echo "<TD colspan='4' ALIGN='CENTER' class='row2'>";
    echo "<font face='verdana' size='2'>" . $l_admin_shoutbox_empty;
  echo "</TD>";
  echo "</TR>";
  //
  echo "<FORM METHOD='GET' ACTION ='list_shoutbox.php?'>";
  echo "<TR>";
  echo "<TD colspan='4' ALIGN='CENTER' class='catBottom'>";
  echo "<INPUT class='liteoption' TYPE='submit' VALUE ='" . $l_admin_mess_bt_refresh . "' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
  echo "<INPUT TYPE='hidden' name='id_grp' value = '" . $id_grp . "' />";
  echo "</TD>";
  echo "</FORM>";
  echo "</TR>";
  //
  echo "</TABLE>";
  
  echo "</TABLE>";
}

?>