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
$option_show_col_hash = '1';
$option_show_col_auth = '1';
$option_show_col_size = '1';
//
if (isset($_GET['id_user_only'])) $id_user_only = intval($_GET['id_user_only']);  else  $id_user_only = 0;
if (isset($_GET['tri'])) $tri = $_GET['tri'];  else  $tri = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['page'])) $page = $_GET['page']; else $page = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_published_files);
require ("../common/menu.inc.php"); // après config.inc.php !
#require ("../common/share_files.inc.php"); // pour sf_remplir_medias()
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_options_backup_files_title . "</title>";
display_header();
echo '<META http-equiv="refresh" content="500;url="> ';
//echo "<link href='../common/styles/defil.css' rel='stylesheet' media='screen, print' type='text/css'/>";
echo "</head>";
echo "<body>";
//
display_menu();
//
//
if ($l_time_short_format_display == '') $l_time_short_format_display = $l_time_format_display;
//
if ($page == 'all')
  $nb_row_by_page = 1000;
else
{
  $nb_row_by_page = intval($nb_row_by_page);
  if ( ($nb_row_by_page < 15) or ($nb_row_by_page > 100) ) $nb_row_by_page = 15;
}
$page = intval($page);
if ($page < 1) $page = 1;
//
//
//
echo "<font face='verdana' size='2'>";
// echo $alpha_link;  // non plus bas !
//
//
if ( _BACKUP_FILES != '' )
{
  require ("../common/sql.inc.php");
  //
  $requete  = " SELECT FIB.ID_FILEBACKUP, FIB.FIB_NAME, FIB.FIB_SIZE, FIB.FIB_DATE_ADD, FIB.FIB_HASH, ";
  $requete .= " USR.USR_USERNAME, USR.USR_NICKNAME, FIB.ID_USER ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER USR, " . $PREFIX_IM_TABLE . "FIB_FILEBACKUP FIB ";
  $requete .= " WHERE FIB.ID_USER = USR.ID_USER ";
  $requete .= " and FIB.FIB_ONLINE = 'Y' ";
  if ($id_user_only > 0) $requete .= " and FIB.ID_USER = " . $id_user_only ;
  //
  switch ($tri)
  {
    //case "name" :
      //$requete .= "ORDER BY FIL_NAME ";
      //break;
    case "date_add" :
      $requete .= " ORDER BY FIB_DATE_ADD DESC, FIB_NAME ";
      break;
    case "auteur" :
      $requete .= " ORDER BY USR_USERNAME ";
      break;
    case "size" :
      $requete .= " ORDER BY FIB_SIZE DESC ";
      break;
    case "hash" :
      $requete .= " ORDER BY FIB_HASH, FIB_SIZE ";
      break;
    default :
      $requete .= " ORDER BY FIB_NAME ";
      break;
  }
  //
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-N1a]", $requete);
  $nb_row = mysqli_num_rows($result);
  if ( $nb_row > 30 )
    echo $alpha_link;
  else
    $alpha_link = "";
  //
  //echo "<TABLE cellspacing='3' cellpadding='0' BORDER='0'>"; // pour centrage en dessous du tableau (légende et choix colonnes)
  //echo "<TR><TD>";
  //
  //echo "<BR/>";
  // Page défilement :
  echo "<TABLE cellspacing='3' cellpadding='0' BORDER='0'>";
  if ($nb_row_by_page > 50)
  {
    echo "<TR><TD COLSPAN='2' ALIGN='RIGHT'>";
    display_nb_page($page, $nb_row_by_page, $nb_row, "&tri=" . $tri . "&only_status=" . $only_status . "&lang=" . $lang . "&'", "");
    echo "</TD></TR>";
  }
  echo "<TR><TD COLSPAN='2'>"; 
  //
  //
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<THEAD>";
  echo "<TR>";
    echo "<TH align=center COLSPAN='12' class='thHead'>";
    $title = $l_admin_options_backup_files_title . " </B> ";
    if ( $nb_row > 1 ) $title .= "&nbsp; <SMALL>(" . $nb_row . ")</SMALL>"; 
    echo "<font face='verdana' size='3'><b>&nbsp; " . $title . "&nbsp;</b></font></TH>";
  echo "</TR>";
  if ( $nb_row > 0 )
  {
    echo "<TR>";

      $link_col = "<A HREF='list_files_backup.php?tri=&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_share_files_col_name . "' class='cattitle' >" . $l_admin_share_files_col_name . "</A>";
      display_row_table($link_col, '');
      //
      if ($option_show_col_size != "") 
      {
        $link_col = "<A HREF='list_files_backup.php?tri=size&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_share_files_col_size . "' class='cattitle' >" . $l_admin_share_files_col_size . "</A>";
        display_row_table($link_col, '100');
      }
      //
      $link_col = "<A HREF='list_files_backup.php?tri=date_add&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_share_files_col_add . "' class='cattitle' >" . $l_admin_share_files_col_add . "</A>";
      display_row_table($link_col, '80');
      //
      if ($id_user_only <= 0) 
      {
        if ($option_show_col_auth != "") 
        {
          $link_col = "<A HREF='list_files_backup.php?tri=auteur&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_share_files_col_author . "' class='cattitle' >" . $l_admin_share_files_col_author . "</A>";
          display_row_table($link_col, '');
        }
      }
      //
      if ($option_show_col_hash != "")
      {
        $link_col = "<A HREF='list_files_backup.php?tri=hash&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_share_files_col_hash . "' class='cattitle' >" . $l_admin_share_files_col_hash . "</A>";
        display_row_table($link_col, '');
      }
      //
      display_row_table($l_admin_users_col_action, '');
    echo "</TR>";
    echo "</THEAD>";
    echo "<TFOOT>";
    echo "</TFOOT>";
    echo "\n";
    echo "<TBODY>";
    //
    $last_first_letter = "";
    $row_num = 0;
    $display_start = 0;
    $display_end = 0;
    $nb_page = 1;
    if ($nb_row > $nb_row_by_page)
    {
      $nb_page = ceil($nb_row / $nb_row_by_page);
      if ($page < 1) $page = 1;
      if ($page > $nb_page) $page = $nb_page;
      $display_start = ( ($page - 1) * $nb_row_by_page + 1);
      $display_end = ($display_start + $nb_row_by_page - 1);
      if ($display_end > $nb_row) $display_end = $nb_row;
    }
    while( list ($id_file, $fil_name, $fil_size, $fil_date_add, $fil_hash, $usrname_auth, $nickname_auth, $id_user_aut) = mysqli_fetch_row ($result) )
    {
      $row_num++;
      if (  ($display_start <= 0) or ($display_end <= 0) or ( ($row_num >= $display_start) and ($row_num <= $display_end) )  )
      {
        if ( ($nickname_auth != '') and (_ALLOW_UPPERCASE_SPACE_USERNAME != '') ) $usrname_auth = $nickname_auth;
        //if ($nom == 'HIDDEN') $nom = '';
        //
        echo "<TR>";
        //
        //
        echo "<TD align='left' class='row1'>";
          echo "<font face='verdana' size='2'>";
          echo "&nbsp;";
          echo $fil_name;
          echo "&nbsp;</font>";
        echo "</TD>";
        //
        if ($option_show_col_size != "") 
        {
          echo "<TD align='right' class='row2'>";
            echo "<font face='verdana' size='2'>";
            echo $fil_size . " " . $l_KB . "&nbsp;</font>";
          echo "</TD>";
        }
        //
        echo "<TD align='center' class='row2'>";
          if ($fil_date_add == '0000-00-00')
            $fil_date_add = 	'&nbsp;';
          else
            $fil_date_add = date($l_date_format_display, strtotime($fil_date_add));
          //
          echo "<font face='verdana' size='2'>";
          if ( $fil_date_add != date($l_date_format_display) )
            echo "<font color='gray'>";
          //
          echo $fil_date_add . "</font>";
        echo "</TD>";
        //
        if ($id_user_only <= 0) 
        {
          if ($option_show_col_auth != "") 
          {
            echo "<TD align='left' class='row2'>";
              echo "<font face='verdana' size='2'>";
              echo "&nbsp;". "<A HREF='user.php?id_user=" . $id_user_aut . "&lang=" . $lang . "&' alt='" . $l_clic_on_user . "' title='" . $l_clic_on_user . "' class='cattitle'>";
              echo $usrname_auth . "</A>&nbsp;</font>";
            echo "</TD>";
          }
        }
        //
        if ($option_show_col_hash != "")
        {
          echo "<TD align='left' class='row3'>";
            echo "<font face='verdana' size='1' color='gray'>";
            echo $fil_hash . "</font>";
          echo "</TD>";
        }
        //
        //
        echo "<FORM METHOD='POST' ACTION='files_backup_delete.php?'>";
          echo "<TD valign='MIDDLE' align='center' class='row2'>";
          echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_delete . "' class='liteoption' />";
          echo "<INPUT TYPE='hidden' name='id_file' value = '" . $id_file . "'/>";
          //echo "<INPUT TYPE='hidden' name='f_name' value = '" . base64_encode($fil_name) . "'/>";
          //echo "<INPUT TYPE='hidden' name='f_hash' value = '" . base64_encode($fil_hash) . "'/>";
          echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "'/>";
          echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
          echo "<INPUT TYPE='hidden' name='tri' value = '" . $tri . "'/>";
          echo "<INPUT TYPE='hidden' name='id_user_only' value = '" . $id_user_only . "' />";
          echo "<INPUT TYPE='hidden' name='source' value = '' />"; // vide, car page de retour par défaut.
          echo "</TD>";
        echo "</FORM>";
        echo "</TR>";
        echo "\n";
      }
    }
    echo "</TBODY>";
    echo "</TABLE>";
    echo "</TD></TR>";
    echo "<TR><TD>";
    //if ($nb_row > $nb_row_by_page)
    if ( ($nb_row > 15) and ($nb_row_by_page < 1000) )
    {
      echo "<font face='verdana' size='2'>";
      echo $l_rows_per_page . " : ";
      display_nb_row_page(15, $nb_row_by_page, "list_files_backup_nb_rows");
      echo " | ";
      display_nb_row_page(20, $nb_row_by_page, "list_files_backup_nb_rows");
      echo " | ";
      display_nb_row_page(25, $nb_row_by_page, "list_files_backup_nb_rows");
      echo " | ";
      display_nb_row_page(30, $nb_row_by_page, "list_files_backup_nb_rows");
      echo " | ";
      display_nb_row_page(50, $nb_row_by_page, "list_files_backup_nb_rows");
    }
    echo "</TD><TD ALIGN='RIGHT'>";
    display_nb_page($page, $nb_row_by_page, $nb_row, "&tri=" . $tri . "&id_user_only=" . $id_user_only . "&lang=" . $lang . "&'", "UP");
    echo "</TD></TR>";


    echo "</TD></TR>";
    echo "</TABLE>";

  }
  else
  {
    echo "<TR>";
    echo "<TD colspan='12' ALIGN='CENTER' class='row2'>";
      echo "<font face='verdana' size='2'>" . $l_admin_share_files_empty;
    echo "</TD>";
    echo "</TR>";
    echo "</TABLE>";

    echo "</TD></TR>";
    echo "</TABLE>";
  }
  //
  mysqli_close($id_connect);
  //
}
else
{
  echo "<BR/>";
  echo "<div class='warning'>";
  echo $l_admin_backup_files_cannot;
  echo "</div>";
}
display_menu_footer();
//
echo "</body></html>";
?>