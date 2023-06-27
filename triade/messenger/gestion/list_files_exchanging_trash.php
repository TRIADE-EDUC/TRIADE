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
if (isset($_COOKIE['im_file_list_col_hash'])) $option_show_col_hash = $_COOKIE['im_file_list_col_hash'];  else  $option_show_col_hash = '0';
if (isset($_COOKIE['im_file_list_col_auth'])) $option_show_col_auth = $_COOKIE['im_file_list_col_auth'];  else  $option_show_col_auth = '1';
if (isset($_COOKIE['im_file_list_col_size'])) $option_show_col_size = $_COOKIE['im_file_list_col_size'];  else  $option_show_col_size = '1';
if (isset($_COOKIE['im_file_list_col_creat'])) $option_show_col_creat = $_COOKIE['im_file_list_col_creat'];  else  $option_show_col_creat = '1';
if (isset($_COOKIE['im_file_list_col_media'])) $option_show_col_media = $_COOKIE['im_file_list_col_media'];  else  $option_show_col_media = '0';
if (isset($_COOKIE['im_file_list_col_project'])) $option_show_col_project = $_COOKIE['im_file_list_col_project'];  else  $option_show_col_project = '1';
if (isset($_COOKIE['im_file_list_col_comment'])) $option_show_col_comment = $_COOKIE['im_file_list_col_comment'];  else  $option_show_col_comment = '1';
if (intval($option_show_col_hash) <= 0) $option_show_col_hash = "";
if (intval($option_show_col_auth) <= 0) $option_show_col_auth = "";
if (intval($option_show_col_size) <= 0) $option_show_col_size = "";
if (intval($option_show_col_creat) <= 0) $option_show_col_creat = "";
if (intval($option_show_col_media) <= 0) $option_show_col_media = "";
if (intval($option_show_col_project) <= 0) $option_show_col_project = "";
if (intval($option_show_col_comment) <= 0) $option_show_col_comment = "";
//
//
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
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_share_files_trash_exchange . "</title>";
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
if ( _SHARE_FILES != '' )
{
  require ("../common/sql.inc.php");
  //
  $requete  = " SELECT FIL.ID_FILE, FIL.FIL_NAME, FIL.FIL_SIZE, FIL.FIL_DATE, FIL.FIL_DATE_ADD, FIL.FIL_DATE_TRASH, FIL.FIL_HASH, ";
  $requete .= " USR.USR_USERNAME, USR.USR_NICKNAME, US2.USR_USERNAME, US2.USR_NICKNAME, FIL.ID_USER_AUT, FIL.ID_USER_DEST, ";
  $requete .= " FMD.FMD_NAME, FIL.FIL_COMPRESS, FIL.FIL_COMMENT ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER USR, " . $PREFIX_IM_TABLE . "USR_USER US2, " . $PREFIX_IM_TABLE . "FMD_FILEMEDIA FMD, " . $PREFIX_IM_TABLE . "FIL_FILE FIL ";
  $requete .= " WHERE FIL.ID_USER_AUT = USR.ID_USER ";
  $requete .= " and FIL.ID_USER_DEST = US2.ID_USER ";
  $requete .= " and FIL.ID_FILEMEDIA = FMD.ID_FILEMEDIA ";
  $requete .= " and FIL.FIL_ONLINE = 'T' ";
  $requete .= " and FIL.ID_USER_DEST is not null ";
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
    $title = $l_admin_share_files_trash_exchange . " </B> ";
    if ( $nb_row > 1 ) $title .= "&nbsp; <SMALL>(" . $nb_row . ")</SMALL>"; 
    echo "<font face='verdana' size='3'><b>&nbsp; " . $title . "</b>&nbsp;</font></TH>";
  echo "</TR>";
  if ( $nb_row > 0 )
  {
    echo "<TR>";
      display_row_table($l_admin_share_files_col_name, '');
      if ($option_show_col_size != "") display_row_table($l_admin_share_files_col_size, '100');
      if ($option_show_col_creat != "") display_row_table($l_admin_share_files_col_create, '80');
      display_row_table($l_admin_share_files_col_add, '80');
      display_row_table($l_admin_share_files_col_removal, '80');
      display_row_table($l_admin_share_files_col_author, '');
      display_row_table($l_admin_share_files_col_recipient, '');
      //if ($option_show_col_media != "") display_row_table($l_admin_share_file_media, '');
      if ($option_show_col_comment != "") display_row_table($l_admin_options_col_comment, '');
      if ($option_show_col_hash != "") display_row_table($l_admin_share_files_col_hash, '');
      display_row_table($l_admin_users_col_action, '');
    echo "</TR>";
    echo "</THEAD>";
    /*
    echo "<TFOOT>";
    //if ( ( mysqli_num_rows($result) > 1 ) and ( (_SHARE_FILES_NEED_APPROVAL != "") or (_SHARE_FILES_EXCHANGE_NEED_APPROVAL != "") ) )
    if ( (_SHARE_FILES_NEED_APPROVAL != "") or (_SHARE_FILES_EXCHANGE_NEED_APPROVAL != "") )
    {
      echo "<TR>";
      echo "<TD align='center' COLSPAN='9' class='catBottom'>";
      echo "<font face='verdana' size='2'><B>";
      echo "<A HREF='list_files_sharing_pending.php?lang=" . $lang . "&'>" . $l_index_share_file_pending . "</A></B>";
      echo "</TD>";
      echo "</TR>";
    }
    echo "</TFOOT>";
    */
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
    while( list ($id_file, $fil_name, $fil_size, $fil_date, $fil_date_add, $fil_date_trash, $fil_hash, $usrname_auth, $nickname_auth, $usrname_dest, $nickname_dest, $id_user_aut, $id_user_dest, $fmd_name, $fil_compress, $fil_comment) = mysqli_fetch_row ($result) )
    {
      $row_num++;
      if (  ($display_start <= 0) or ($display_end <= 0) or ( ($row_num >= $display_start) and ($row_num <= $display_end) )  )
      {
        if (_ALLOW_UPPERCASE_SPACE_USERNAME != '')
        {
          if ($nickname_auth != '') $usrname_auth = $nickname_auth;
          if ($nickname_dest != '') $usrname_dest = $nickname_dest;
        }
        //if ($nom == 'HIDDEN') $nom = '';
        //
        echo "<TR>";
        //
        //
        echo "<TD align='left' class='row1'>";
          echo "<font face='verdana' size='2'>";
          echo "&nbsp;";
          //if (_SHARE_FILES_FOLDER != "") echo "<A HREF='" . _SHARE_FILES_FOLDER . $fil_name . "' target='_blank'>";
          //if (_SHARE_FILES_FOLDER != "") echo "<A HREF='files_sharing_download.php?file=". $fil_name . "&' target='_blank'>";
          if ( ($fil_compress == "") and ( (_SHARE_FILES_FOLDER != "") XOR (_SHARE_FILES_FTP_PASSWORD != "") ) ) 
            echo "<A HREF='files_sharing_download.php?id_file=". $id_file . "&' target='_blank'>";
          else
          {
            if ($fil_compress == "P")
              echo "<IMG SRC='" . _FOLDER_IMAGES . "lock_on.png' ALT='" . $l_admin_share_file_protected_file . ": " . $l_admin_share_file_cannot_display . " (_SHARE_FILES_PROTECT)' TITLE='" . $l_admin_share_file_protected_file . ": " . $l_admin_share_file_cannot_display . " (_SHARE_FILES_PROTECT)' WIDTH='16' HEIGHT='16' BORDER='0' />";
            else
              echo "<IMG SRC='" . _FOLDER_IMAGES . "compress.png' ALT='" . $l_admin_share_file_compressed_file . ": " . $l_admin_share_file_cannot_display . " (_SHARE_FILES_COMPRESS)' TITLE='" . $l_admin_share_file_compressed_file . ": " . $l_admin_share_file_cannot_display . " (_SHARE_FILES_COMPRESS)' WIDTH='16' HEIGHT='16' BORDER='0' />";
          }
          //
          echo $fil_name;
          if ( ($fil_compress == "") and ( (_SHARE_FILES_FOLDER != "") XOR (_SHARE_FILES_FTP_PASSWORD != "") ) ) echo "</A>";
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
        if ($option_show_col_creat != "")
        {
          echo "<TD align='center' class='row2'>";
            if ($fil_date == '0000-00-00')
              $fil_date = 	'&nbsp;';
            else
              $fil_date = date($l_date_format_display, strtotime($fil_date));
            //
            echo "<font face='verdana' size='2'>";
            if ( $fil_date != date($l_date_format_display) )
              echo "<font color='gray'>";
            //
            echo $fil_date . "</font>";
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
        echo "<TD align='center' class='row2'>";
          if ($fil_date_trash == '0000-00-00')
            $fil_date_trash = 	'&nbsp;';
          else
            $fil_date_trash = date($l_date_format_display, strtotime($fil_date_trash));
          //
          echo "<font face='verdana' size='2'>";
          if ( $fil_date_trash != date($l_date_format_display) )
            echo "<font color='gray'>";
          //
          echo $fil_date_trash . "</font>";
        echo "</TD>";
        //
        echo "<TD align='left' class='row2'>";
          echo "<font face='verdana' size='2'>";
          echo "&nbsp;". "<A HREF='user.php?id_user=" . $id_user_aut . "&lang=" . $lang . "&' alt='" . $l_clic_on_user . "' title='" . $l_clic_on_user . "' class='cattitle'>";
          echo $usrname_auth . "</A>&nbsp;</font>";
        echo "</TD>";
        //
        echo "<TD align='left' class='row2'>";
          echo "<font face='verdana' size='2'>";
          echo "&nbsp;". "<A HREF='user.php?id_user=" . $id_user_aut . "&lang=" . $lang . "&' alt='" . $l_clic_on_user . "' title='" . $l_clic_on_user . "' class='cattitle'>";
          echo $usrname_dest . "</A>&nbsp;</font>";
        echo "</TD>";
        //
        if ($option_show_col_comment != "")
        {
          echo "<TD align='left' class='row2'>";
            echo "<font face='verdana' size='2'>";
            echo $fil_comment . "</font>";
          echo "</TD>";
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
        echo "<FORM METHOD='POST' ACTION='files_sharing_delete.php?'>";
          echo "<TD valign='MIDDLE' align='center' class='row2'>";
          echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_delete . "' class='liteoption' />";
          echo "<INPUT TYPE='hidden' name='id_file' value = '" . $id_file . "'/>";
          //echo "<INPUT TYPE='hidden' name='f_name' value = '" . base64_encode($fil_name) . "'/>";
          //echo "<INPUT TYPE='hidden' name='f_hash' value = '" . base64_encode($fil_hash) . "'/>";
          echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "'/>";
          echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
          echo "<INPUT TYPE='hidden' name='source' value = 'share_files_exch_trash' />";
          echo "</TD>";
        echo "</FORM>";
        //
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
      display_nb_row_page(15, $nb_row_by_page, "list_files_exchanging_trash_nb_rows");
      echo " | ";
      display_nb_row_page(20, $nb_row_by_page, "list_files_exchanging_trash_nb_rows");
      echo " | ";
      display_nb_row_page(25, $nb_row_by_page, "list_files_exchanging_trash_nb_rows");
      echo " | ";
      display_nb_row_page(30, $nb_row_by_page, "list_files_exchanging_trash_nb_rows");
      echo " | ";
      display_nb_row_page(50, $nb_row_by_page, "list_files_exchanging_trash_nb_rows");
    }
    echo "</TD><TD ALIGN='RIGHT'>";
    display_nb_page($page, $nb_row_by_page, $nb_row, "&tri=" . $tri . "&lang=" . $lang . "&'", "UP");
    echo "</TD></TR>";



    echo "<TR><TD></TD></TR>";
    echo "<TR><TD></TD></TR>";  // Espacement vertical
    
    
    echo "<TR><TD COLSPAN='2'>";

        echo "<TABLE WIDTH='100%' cellspacing='0' cellpadding='0' BORDER='0'>";
        echo "<TR><TD WITH='50%' VALIGN='TOP'>";
        
        
        

        echo "</TD><TD WITH='50%' ALIGN='RIGHT' VALIGN='TOP'>\n";


      echo "</TD></TR>";
      echo "</TABLE>";


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
    echo "<BR/>";
  }
  //
  mysqli_close($id_connect);
  //
  echo "<font face='verdana' size='2'>";
  echo " &nbsp; <A HREF='list_files_sharing_trash.php?lang=" . $lang . "&'>" . $l_admin_share_files_trash . "</A> &nbsp; ";
}
else
{
  echo "<BR/>";
  echo "<div class='warning'>";
  echo $l_admin_share_files_cannot;
  echo "</div>";
}
display_menu_footer();
//
echo "</body></html>";
?>