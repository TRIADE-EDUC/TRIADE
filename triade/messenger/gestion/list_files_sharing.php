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
if (isset($_COOKIE['im_file_list_show_select_cols'])) $im_file_list_show_select_cols = $_COOKIE['im_file_list_show_select_cols'];  else  $im_file_list_show_select_cols = '1';
if (isset($_COOKIE['im_file_list_col_hash'])) $option_show_col_hash = $_COOKIE['im_file_list_col_hash'];  else  $option_show_col_hash = '0';
if (isset($_COOKIE['im_file_list_col_auth'])) $option_show_col_auth = $_COOKIE['im_file_list_col_auth'];  else  $option_show_col_auth = '1';
if (isset($_COOKIE['im_file_list_col_size'])) $option_show_col_size = $_COOKIE['im_file_list_col_size'];  else  $option_show_col_size = '1';
if (isset($_COOKIE['im_file_list_col_creat'])) $option_show_col_creat = $_COOKIE['im_file_list_col_creat'];  else  $option_show_col_creat = '1';
if (isset($_COOKIE['im_file_list_col_media'])) $option_show_col_media = $_COOKIE['im_file_list_col_media'];  else  $option_show_col_media = '0';
if (isset($_COOKIE['im_file_list_col_project'])) $option_show_col_project = $_COOKIE['im_file_list_col_project'];  else  $option_show_col_project = '1';
if (isset($_COOKIE['im_file_list_col_comment'])) $option_show_col_comment = $_COOKIE['im_file_list_col_comment'];  else  $option_show_col_comment = '1';
//if (isset($_COOKIE['im_file_list_col_XXX'])) $option_show_col_XXX = $_COOKIE['im_file_list_col_XXX'];  else  $option_show_col_XXX = '1';
if (intval($option_show_col_hash) <= 0) $option_show_col_hash = "";
if (intval($option_show_col_auth) <= 0) $option_show_col_auth = "";
if (intval($option_show_col_size) <= 0) $option_show_col_size = "";
if (intval($option_show_col_creat) <= 0) $option_show_col_creat = "";
if (intval($option_show_col_media) <= 0) $option_show_col_media = "";
if (intval($option_show_col_project) <= 0) $option_show_col_project = "";
if (intval($option_show_col_comment) <= 0) $option_show_col_comment = "";
//
//
if (isset($_GET['id_user_only'])) $id_user_only = intval($_GET['id_user_only']);  else  $id_user_only = 0;
if (isset($_GET['id_media_only'])) $id_media_only = intval($_GET['id_media_only']);  else  $id_media_only = 0;
if (isset($_GET['id_project_only'])) $id_project_only = intval($_GET['id_project_only']);  else  $id_project_only = 0;
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
require ("../common/share_files.inc.php"); // pour sf_remplir_medias()
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_share_files_title . "</title>";
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
  $requete  = " SELECT FIL.ID_FILE, FIL.FIL_NAME, FIL.FIL_SIZE, FIL.FIL_DATE, FIL.FIL_DATE_ADD, FIL.FIL_NB_DOWNLOAD, FIL.FIL_HASH, ";
  $requete .= " USR.USR_USERNAME, USR.USR_NICKNAME, FIL.ID_USER_AUT, FMD.FMD_NAME, FIL.ID_FILEMEDIA, FPJ.FPJ_NAME, FIL.ID_PROJET, ";
  $requete .= " FIL.ID_GROUP_DEST, FIL.FIL_RATING, FIL.FIL_COMPRESS, FIL.FIL_COMMENT ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER USR, " . $PREFIX_IM_TABLE . "FMD_FILEMEDIA FMD, " . $PREFIX_IM_TABLE . "FIL_FILE FIL ";
  $requete .= " LEFT JOIN " . $PREFIX_IM_TABLE . "FPJ_FILEPROJET AS FPJ ON ( FPJ.ID_PROJET = FIL.ID_PROJET ) ";
  $requete .= " WHERE FIL.ID_USER_AUT = USR.ID_USER ";
  $requete .= " and FIL.ID_FILEMEDIA = FMD.ID_FILEMEDIA ";
  $requete .= " and FIL.FIL_ONLINE = 'Y' ";
  $requete .= " and FIL.ID_USER_DEST is null ";
  if ($id_user_only > 0) $requete .= " and FIL.ID_USER_AUT = " . $id_user_only ;
  if ($id_media_only > 0) $requete .= " and FIL.ID_FILEMEDIA = " . $id_media_only ;
  if ($id_project_only > 0) $requete .= " and FIL.ID_PROJET = " . $id_project_only ;
  if ($tri == "projet") $requete .= " and FIL.ID_PROJET is not null ";
  //
  switch ($tri)
  {
    //case "name" :
      //$requete .= "ORDER BY FIL_NAME ";
      //break;
    case "date_add" :
      $requete .= " ORDER BY FIL_DATE_ADD DESC, FIL_NAME ";
      break;
    case "date_file" :
      $requete .= " ORDER BY FIL_DATE DESC, FIL_NAME";
      break;
    case "hash" :
      $requete .= " ORDER BY FIL_HASH, FIL_SIZE, FIL_DATE ";
      break;
    case "auteur" :
      $requete .= " ORDER BY USR_USERNAME ";
      break;
    case "size" :
      $requete .= " ORDER BY FIL_SIZE DESC ";
      break;
    case "media" : 
      $requete .= " ORDER BY FMD_NAME ";
      break;
    case "projet" : 
      $requete .= " ORDER BY FPJ_NAME ";
      break;
    case "download" :
      $requete .= " ORDER BY FIL_NB_DOWNLOAD DESC ";
      break;
    default :
      $requete .= " ORDER BY FIL_NAME ";
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
    $title = $l_admin_share_files_title . " </B> ";
    if ( $nb_row > 1 ) $title .= "&nbsp; <SMALL>(" . $nb_row . ")</SMALL>"; 
    echo "<font face='verdana' size='3'><b>&nbsp; " . $title . "&nbsp;</b></font></TH>";
  echo "</TR>";
  if ( $nb_row > 0 )
  {
    echo "<TR>";

      $link_col = "<A HREF='list_files_sharing.php?tri=&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_share_files_col_name . "' class='cattitle' >" . $l_admin_share_files_col_name . "</A>";
      display_row_table($link_col, '');
      //
      if ($option_show_col_size != "") 
      {
        $link_col = "<A HREF='list_files_sharing.php?tri=size&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_share_files_col_size . "' class='cattitle' >" . $l_admin_share_files_col_size . "</A>";
        display_row_table($link_col, '100');
      }
      //
      if ($option_show_col_creat != "")
      {
        $link_col = "<A HREF='list_files_sharing.php?tri=date_file&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_share_files_col_create . "' class='cattitle' >" . $l_admin_share_files_col_create . "</A>";
        display_row_table($link_col, '80');
      }
      //
      $link_col = "<A HREF='list_files_sharing.php?tri=date_add&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_share_files_col_add . "' class='cattitle' >" . $l_admin_share_files_col_add . "</A>";
      display_row_table($link_col, '80');
      //
      $link_col  = "<A HREF='list_files_sharing.php?tri=download&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_share_files_col_nb_download . "' class='cattitle' >";
      $link_col .= "<IMG SRC='" . _FOLDER_IMAGES . "menu_on_left_.png' width='16' height='16' border='0' ></A>"; // ALT='" . $l_language . "' TITLE='" . $l_language . "' 
      //$link_col .= "DL</A>";
      display_row_table($link_col, '');
      //
      if ($id_user_only <= 0) 
      {
        if ($option_show_col_auth != "") 
        {
          $link_col = "<A HREF='list_files_sharing.php?tri=auteur&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_share_files_col_author . "' class='cattitle' >" . $l_admin_share_files_col_author . "</A>";
          display_row_table($link_col, '');
        }
      }
      //
      if ($option_show_col_media <> '')
      {
        $link_col = "<A HREF='list_files_sharing.php?tri=media&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_share_file_media . "' class='cattitle' >" . $l_admin_share_file_media . "</A>";
        display_row_table($link_col, '');
      }
      
      if ($option_show_col_project != "")
      {
        $link_col  = "&nbsp;<A HREF='list_files_sharing.php?tri=projet&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_share_files_col_projet . "' class='cattitle' >" . $l_admin_share_files_col_projet . "</A>";
        $link_col .= "&nbsp;<A HREF='list_files_projects.php?lang=" . $lang . "&' TITLE='" . $l_admin_share_file_project_list . "' class='cattitle' >";
        $link_col .= "<IMG SRC='" . _FOLDER_IMAGES . "flag_language.png' width='16' height='16' border='0' ></A>&nbsp;"; // ALT='" . $l_language . "' TITLE='" . $l_language . "' 
        display_row_table($link_col, '');
      }
      //
      if ($option_show_col_comment != "") 
        display_row_table($l_admin_options_col_comment, '');
      //
      if ($option_show_col_hash != "")
      {
        $link_col = "<A HREF='list_files_sharing.php?tri=hash&page=" . $page . "&lang=" . $lang . "&' TITLE='" . $l_order_by . " " . $l_admin_share_files_col_hash . "' class='cattitle' >" . $l_admin_share_files_col_hash . "</A>";
        display_row_table($link_col, '');
      }
      //
      if (_SHARE_FILES_VOTE != "") 
      {
        display_row_table($l_admin_shoutbox_average, '');
      }
      display_row_table($l_admin_users_col_action, '');
    echo "</TR>";
    echo "</THEAD>";
    echo "<TFOOT>";
    //if ( ( mysqli_num_rows($result) > 1 ) and ( (_SHARE_FILES_NEED_APPROVAL != "") or (_SHARE_FILES_EXCHANGE_NEED_APPROVAL != "") ) )
    //if ( (_SHARE_FILES_NEED_APPROVAL != "") or (_SHARE_FILES_EXCHANGE_NEED_APPROVAL != "") )
    //if ( (_SHARE_FILES_NEED_APPROVAL != "") or (_SHARE_FILES_EXCHANGE_NEED_APPROVAL != "") or (_SHARE_FILES_TRASH != "") or (_SHARE_FILES_EXCHANGE_TRASH != "") )
    echo "<TR>";
    echo "<TD align='center' COLSPAN='12' class='catBottom'>";
    echo "<font face='verdana' size='2'>";
    if ( (_SHARE_FILES_NEED_APPROVAL != "") or (_SHARE_FILES_EXCHANGE_NEED_APPROVAL != "") or (_SHARE_FILES_TRASH != "") or (_SHARE_FILES_EXCHANGE_TRASH != "") )
    {
      if (_SHARE_FILES_NEED_APPROVAL != "")
        echo " &nbsp; <A HREF='list_files_sharing_pending.php?lang=" . $lang . "&'>" . $l_admin_share_file_pending . "</A> &nbsp; ";
      //
      if (_SHARE_FILES_EXCHANGE_NEED_APPROVAL != "")
        echo " &nbsp; <A HREF='list_files_exchanging_pending.php?lang=" . $lang . "&'>" . $l_admin_share_file_pending_exchange . "</A> &nbsp; ";
      //
      if (_SHARE_FILES_TRASH != "")
        echo " &nbsp; <A HREF='list_files_sharing_trash.php?lang=" . $lang . "&'>" . $l_admin_share_files_trash . "</A> &nbsp; ";
      //
      if (_SHARE_FILES_EXCHANGE_TRASH != "")
        echo " &nbsp; <A HREF='list_files_exchanging_trash.php?lang=" . $lang . "&'>" . $l_admin_share_files_trash_exchange . "</A> &nbsp; ";
    }
    else
    {
      echo "<font color='gray' size='1'>" . $l_admin_share_file_only_shared_files . "</font>";
    }
    echo "</TD>";
    echo "</TR>";
/*
    if ( (_SHARE_FILES_TRASH != "") or (_SHARE_FILES_EXCHANGE_TRASH != "") )
    {
      echo "<TR>";
      echo "<TD align='center' COLSPAN='12' class='row3'>";
      echo "<font face='verdana' size='2'>";
      if (_SHARE_FILES_TRASH != "")
        echo " &nbsp; <A HREF='list_files_sharing_trash.php?lang=" . $lang . "&'>" . $l_admin_share_files_trash . "</A> &nbsp; ";
      //
      if (_SHARE_FILES_EXCHANGE_TRASH != "")
        echo " &nbsp; <A HREF='list_files_exchanging_trash.php?lang=" . $lang . "&'>" . $l_admin_share_files_trash_exchange . "</A> &nbsp; ";
      //
      echo "</TD>";
      echo "</TR>";
    }
*/
    echo "</TFOOT>";
    echo "\n";
    echo "<TBODY>";
    //
    $last_first_letter = "";
    $row_num = 0;
    $display_start = 0;
    $display_end = 0;
    $nb_page = 1;
    $nb_files_no_protect = 0;
    if ($nb_row > $nb_row_by_page)
    {
      $nb_page = ceil($nb_row / $nb_row_by_page);
      if ($page < 1) $page = 1;
      if ($page > $nb_page) $page = $nb_page;
      $display_start = ( ($page - 1) * $nb_row_by_page + 1);
      $display_end = ($display_start + $nb_row_by_page - 1);
      if ($display_end > $nb_row) $display_end = $nb_row;
    }
    while( list ($id_file, $fil_name, $fil_size, $fil_date, $fil_date_add, $fil_nb_download, $fil_hash, $usrname_auth, $nickname_auth, $id_user_aut, $fmd_name, $id_filemedia, $fpj_name, $id_project, $id_group_dest, $fil_rating, $fil_compress, $fil_comment) = mysqli_fetch_row ($result) )
    {
      $row_num++;
      if ($fil_compress != "P") $nb_files_no_protect++;
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
          //if (_SHARE_FILES_FOLDER != "") echo "<A HREF='" . _SHARE_FILES_FOLDER . $fil_name . "' target='_blank'>";
          //if ( (_SHARE_FILES_FOLDER != "") XOR (_SHARE_FILES_FTP_PASSWORD != "") ) echo "<A HREF='files_sharing_download.php?file=". $fil_name . "&' target='_blank'>";
          if ( ($fil_compress == "") and ( (_SHARE_FILES_FOLDER != "") XOR (_SHARE_FILES_FTP_PASSWORD != "") ) ) 
          {
            echo "<A HREF='files_sharing_download.php?id_file=". $id_file . "&' target='_blank'>";
            echo $fil_name;
            echo "</A>";
          }
          else
          {
            if ($fil_compress == "P")
              echo "<IMG SRC='" . _FOLDER_IMAGES . "lock_on.png' ALT='" . $l_admin_share_file_protected_file . ": " . $l_admin_share_file_cannot_display . " (_SHARE_FILES_PROTECT)' TITLE='" . $l_admin_share_file_protected_file . ": " . $l_admin_share_file_cannot_display . " (_SHARE_FILES_PROTECT)' WIDTH='16' HEIGHT='16' BORDER='0' />";
            else
              echo "<IMG SRC='" . _FOLDER_IMAGES . "compress.png' ALT='" . $l_admin_share_file_compressed_file . ": " . $l_admin_share_file_cannot_display . " (_SHARE_FILES_COMPRESS)' TITLE='" . $l_admin_share_file_compressed_file . ": " . $l_admin_share_file_cannot_display . " (_SHARE_FILES_COMPRESS)' WIDTH='16' HEIGHT='16' BORDER='0' />";
            //
            echo $fil_name;
          }
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
        echo "<TD align='right' class='row2'>";
          echo "<font face='verdana' size='2'>&nbsp;";
          if ($fil_nb_download > 0)
          {
            echo $fil_nb_download . "&nbsp;</font>";
          }
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
        if ($option_show_col_media <> '')
        {
          echo "<TD align='left' class='row2'>";
            echo "<font face='verdana' size='2'>";
            echo "&nbsp;<A HREF='list_files_sharing.php?lang=" . $lang . "&id_project_only=" . $id_project_only . "&id_media_only=" . $id_filemedia . "&' >" . $fmd_name . "</A>&nbsp;"; // TITLE='" . $l_admin_share_file_media_files_only . "'
            echo "</font>";
          echo "</TD>";
        }
        //
        if ($option_show_col_project != "")
        {
          echo "<TD align='left' class='row2'>";
            echo "<font face='verdana' size='2'>";
            echo "&nbsp;<A HREF='list_files_sharing.php?lang=" . $lang . "&id_project_only=" . $id_project . "&id_media_only=" . $id_media_only . "&' TITLE='" . $l_admin_share_file_project_files_only . "'>" . $fpj_name . "</A>&nbsp;";
            echo "</font>";
          echo "</TD>";
        }
        //
        if ($option_show_col_comment != "")
        {
          echo "<FORM METHOD='POST' ACTION='files_sharing_update_comment.php?'>";
          echo "<TD valign='center' VALIGN='MIDDLE' class='row2'>";
          echo "<input type='text' name='fil_comment' maxlength='150' value='" . $fil_comment . "' size='20' class='post' />";
          echo " ";
          echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
          echo "<input type='hidden' name='id_file' value = '" . $id_file . "' />";
          echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
          echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "'/>";
          echo "<INPUT TYPE='hidden' name='tri' value = '" . $tri . "'/>";
          echo "<INPUT TYPE='hidden' name='id_user_only' value = '" . $id_user_only . "' />";
          echo "<INPUT TYPE='hidden' name='id_media_only' value = '" . $id_media_only . "' />";
          echo "<INPUT TYPE='hidden' name='id_project_only' value = '" . $id_project_only . "' />";
          echo "<INPUT TYPE='hidden' name='source' value = 'share_files' />";
          echo "</TD>";
          echo "</FORM>";
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
        if (_SHARE_FILES_VOTE != "") 
        {
          echo "<TD align='right' class='row2'>";
          echo "<font face=verdana size=2>";
          if ($fil_rating > 0) echo "<font color='green'>" . $fil_rating;
          if ($fil_rating < 0) echo "<font color='red'>" . $fil_rating;
          echo "&nbsp;";
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
          echo "<INPUT TYPE='hidden' name='tri' value = '" . $tri . "'/>";
          echo "<INPUT TYPE='hidden' name='id_user_only' value = '" . $id_user_only . "' />";
          echo "<INPUT TYPE='hidden' name='id_media_only' value = '" . $id_media_only . "' />";
          echo "<INPUT TYPE='hidden' name='id_project_only' value = '" . $id_project_only . "' />";
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
      display_nb_row_page(15, $nb_row_by_page, "list_files_sharing_nb_rows");
      echo " | ";
      display_nb_row_page(20, $nb_row_by_page, "list_files_sharing_nb_rows");
      echo " | ";
      display_nb_row_page(25, $nb_row_by_page, "list_files_sharing_nb_rows");
      echo " | ";
      display_nb_row_page(30, $nb_row_by_page, "list_files_sharing_nb_rows");
      echo " | ";
      display_nb_row_page(50, $nb_row_by_page, "list_files_sharing_nb_rows");
    }
    echo "</TD><TD ALIGN='RIGHT'>";
    display_nb_page($page, $nb_row_by_page, $nb_row, "&tri=" . $tri . "&id_user_only=" . $id_user_only . "&id_media_only=" . $id_media_only . "&id_project_only=" . $id_project_only . "&lang=" . $lang . "&'", "UP");
    echo "</TD></TR>";


    if ( (_SHARE_FILES_COMPRESS != "") and (_SHARE_FILES_PROTECT != "") )
    {
      if ( ($nb_row > 10) and ($nb_files_no_protect >= ($nb_row -2)) )
      {
        if (!is_readable("../distant/update/IM_Protect.zip")) 
        {
          echo "<TR><TD COLSPAN='2'>";
            echo "<div class='warning'>";
            echo $l_admin_share_file_cannot_protect . ": ";
            if ($lang == "FR")
              echo "<A HREF='http://www.intramessenger.com/faq/6/' target='_blank'>http://www.intramessenger.com/faq/6/</A>";
            else
              echo "<A HREF='http://www.intramessenger.com/faq/5/' target='_blank'>http://www.intramessenger.com/faq/5/</A>";
            echo "</div>";
          echo "</TD></TR>";
        }
      }
    }

    echo "<TR><TD></TD></TR>";
    echo "<TR><TD></TD></TR>";  // Espacement vertical
    
    
    echo "<TR><TD COLSPAN='2'>";

        echo "<TABLE WIDTH='100%' cellspacing='0' cellpadding='0' BORDER='0'>";
        echo "<TR><TD WITH='50%' VALIGN='TOP'>";
        
        //echo "";
          echo "<table cellspacing='1' cellpadding='1' class='forumline'>"; // width='650' 
          echo "<FORM METHOD='GET' name='formulaire_cookies' ACTION ='set_cookies.php?'>";
          echo "<TR>";
          echo "<TD class='catHead' align='center'>";
            echo "<FONT size='2'>&nbsp;<B>" . $l_display_col;

            if ($im_file_list_show_select_cols > 0)
            {
              echo "&nbsp;<A HREF='set_cookies.php?lang=" . $lang . "&page=" . $page . "&tri=" . $tri . "&action=list_files_sharing_show_select_cols&im_file_list_show_select_cols=0&'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "minimize.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
            }
            else
            {
              echo "&nbsp;<A HREF='set_cookies.php?lang=" . $lang . "&page=" . $page . "&tri=" . $tri . "&action=list_files_sharing_show_select_cols&im_file_list_show_select_cols=1&'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "maximize.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
            }

          echo "</TD>";
          echo "</TR>";
          //
          if ($im_file_list_show_select_cols > 0)
          {
            echo "<TR>";
            echo "<td class='row1'>";
            echo "<FONT size='2'>";

            echo "<INPUT name='option_show_col_size' id='option_show_col_size' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if ($option_show_col_size <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_size'>" . $l_admin_share_files_col_size . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_creat' id='option_show_col_creat' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if ($option_show_col_creat <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_creat'>" . $l_admin_share_files_col_create . "</label><BR/>\n";
            
            echo "<INPUT name='option_show_col_project' id='option_show_col_project' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if ($option_show_col_project <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_project'>" . $l_admin_share_files_col_projet . "</label><BR/>\n";
            
            echo "<INPUT name='option_show_col_auth' id='option_show_col_auth' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if ($option_show_col_auth <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_auth'>" . $l_admin_share_files_col_author . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_media' id='option_show_col_media' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if ($option_show_col_media <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_media'>" . $l_admin_share_file_media . "</label><BR/>\n";
            
            echo "<INPUT name='option_show_col_comment' id='option_show_col_comment' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if ($option_show_col_comment <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_comment'>" . $l_admin_options_col_comment . "</label><BR/>\n";

            echo "<INPUT name='option_show_col_hash' id='option_show_col_hash' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
            if ($option_show_col_hash <> '') echo "CHECKED";
            echo " />";
            echo "<label for='option_show_col_hash'>" . $l_admin_share_files_col_hash . "</label><BR/>\n";

            echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
            echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
            //echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
            echo "<input type='hidden' name='action' value = 'list_files_sharing' />"; // les paramètres de cette page, et y revenir ensuite
            echo "</TD>";
            echo "</TR>";
            echo "<TR>";
            echo "<TD ALIGN='CENTER' class='catBottom'>";
            echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_bt_update . "' />";
            echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
            echo "</TD>";
            echo "</TR>";
          }
          echo "</FORM>";
          echo "</TABLE>";
        



        echo "</TD><TD WITH='50%' ALIGN='RIGHT' VALIGN='TOP'>\n"; //-----------------------

            if ( ($id_project_only <= 0) or ($id_media_only <= 0) )
            {
              $requete  = " select FMD.ID_FILEMEDIA, FMD.FMD_NAME, count(FIL.ID_FILE) ";
              $requete .= " FROM " . $PREFIX_IM_TABLE . "FMD_FILEMEDIA FMD, " . $PREFIX_IM_TABLE . "FIL_FILE FIL ";
              $requete .= " WHERE FMD.ID_FILEMEDIA = FIL.ID_FILEMEDIA ";
              $requete .= " and FIL.FIL_ONLINE = 'Y' ";
              $requete .= " and FIL.ID_USER_DEST is null ";
              if ($id_project_only > 0) $requete .= " and FIL.ID_PROJET = " . $id_project_only ;
              $requete .= " GROUP BY ID_FILEMEDIA, FMD_NAME";
              $requete .= " order by FMD_NAME ";
              //
              $result = mysqli_query($id_connect, $requete);
              if (!$result) error_sql_log("[ERR-N1b]", $requete);
              if ( mysqli_num_rows($result) > 0 )
              {
                echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
                //echo "<TR><TD COLSPAN='4' ALIGN='CENTER' class='catHead'><B>" . $l_legende . " </B>(" . strtolower($l_admin_users_order_state) .") </TD></TR>";
                echo "<TR><TD COLSPAN='4' ALIGN='CENTER' class='catHead'><B>&nbsp;" . $l_admin_share_file_media . "&nbsp;</B>";
                echo "</TD></TR>";
                while( list ($id_media, $media_name, $nbre) = mysqli_fetch_row ($result) )
                {
                  echo "<TR>";
                  echo "<TD ALIGN='LEFT' class='row2'>";
                  echo "<font face='verdana' size='2'>";
                  echo "&nbsp;" . $media_name . "&nbsp;";
                  echo "</TD>";
                  //
                  echo "<TD ALIGN='RIGHT' class='row2'>";
                  echo "<font face='verdana' size='2'>";
                  echo "&nbsp;<A HREF='list_files_sharing.php?lang=".$lang . "&id_media_only=" . $id_media . "&id_project_only=" . $id_project_only . "&'>" . $nbre . "</A>&nbsp;";
                  echo "</TD>";
                  echo "</TR>";
                }
              }
              else
              {
                if ( ($id_project_only <= 0) and ($id_media_only <= 0) ) 
                {
                  sf_remplir_medias();
                }
              }
            }
            
            echo "</TABLE>";


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
    //
    if ( (_SHARE_FILES_NEED_APPROVAL != "") or (_SHARE_FILES_EXCHANGE_NEED_APPROVAL != "") or (_SHARE_FILES_TRASH != "") or (_SHARE_FILES_EXCHANGE_TRASH != "") )
    {
      echo "<br/>";
      //
      if (_SHARE_FILES_NEED_APPROVAL != "")
        echo " &nbsp; <A HREF='list_files_sharing_pending.php?lang=" . $lang . "&'>" . $l_admin_share_file_pending . "</A> &nbsp; ";
      //
      if (_SHARE_FILES_EXCHANGE_NEED_APPROVAL != "")
        echo " &nbsp; <A HREF='list_files_exchanging_pending.php?lang=" . $lang . "&'>" . $l_admin_share_file_pending_exchange . "</A> &nbsp; ";
      //
      echo "<br/>";
      if (_SHARE_FILES_TRASH != "")
        echo " &nbsp; <A HREF='list_files_sharing_trash.php?lang=" . $lang . "&'>" . $l_admin_share_files_trash . "</A> &nbsp; ";
      //
      if (_SHARE_FILES_EXCHANGE_TRASH != "")
        echo " &nbsp; <A HREF='list_files_exchanging_trash.php?lang=" . $lang . "&'>" . $l_admin_share_files_trash_exchange . "</A> &nbsp; ";
    }
  }
  //
  mysqli_close($id_connect);
  //
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