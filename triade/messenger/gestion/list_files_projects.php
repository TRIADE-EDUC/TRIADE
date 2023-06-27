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
//
if (isset($_GET['tri'])) $tri = $_GET['tri'];  else  $tri = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['page'])) $page = $_GET['page']; else $page = "";
if (isset($_GET['new_added'])) $new_added = $_GET['new_added']; else $new_added = "";
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
echo "<title>[IM] " . $l_admin_options_share_files . " - " . $l_admin_share_file_project_list . "</title>";
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
  $requete  = " SELECT ID_PROJET, FPJ_NAME, FPJ_FOLDER, FPJ_DATE_CREAT, FPJ_DATE_END, FPJ_DATE_CLOSE ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "FPJ_FILEPROJET ";
  $requete .= " order by FPJ_NAME ";
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
    display_nb_page($page, $nb_row_by_page, $nb_row, "&tri=" . $tri . "&lang=" . $lang . "&'", "");
    echo "</TD></TR>";
  }
  echo "<TR><TD COLSPAN='2'>"; 
  //
  //
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<THEAD>";
  echo "<TR>";
    echo "<TH align=center COLSPAN='6' class='thHead'>";
    $title = $l_admin_share_file_project_list . " </B> ";
    if ( $nb_row > 1 ) $title .= "&nbsp; <SMALL>(" . $nb_row . ")</SMALL>"; 
    echo "<font face='verdana' size='3'><b>&nbsp; " . $title . "&nbsp;</b></font></TH>";
  echo "</TR>";
  if ( $nb_row > 0 )
  {
    echo "<TR>";
      display_row_table($l_admin_share_files_col_projet, '');
      display_row_table("&nbsp;" . $l_admin_share_file_project_subfolder . "&nbsp;", '');
      display_row_table($l_admin_share_files_col_create, '80');
      #display_row_table($l_admin_share_file_project_col_end, '80');
      display_row_table($l_admin_share_file_project_col_closing, '80');
      display_row_table($l_admin_users_col_action, '');
    echo "</TR>";
    echo "</THEAD>";
    echo "<TFOOT>";
      echo "<TR>";
        echo "<TD align=center COLSPAN='6' class='row3'>";
        echo "<font face='verdana' size='1' color='gray'>" . $l_admin_share_file_project_close_empty;
        echo "</TD>";
      echo "</TR>";
      if ($new_added != "")
      {
        echo "<TR>";
          echo "<TD align=center COLSPAN='6' class='catBottom'>";
          echo "<font face='verdana' size='1' color='red'>" . $l_admin_share_file_project_subfolder_must_exist;
          echo "</TD>";
        echo "</TR>";
      }
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
    while( list ($id_projet, $fpj_name, $fpj_folder, $fpj_date_creat, $fpj_date_end, $fpj_date_close) = mysqli_fetch_row ($result) )
    {
      $row_num++;
      if (  ($display_start <= 0) or ($display_end <= 0) or ( ($row_num >= $display_start) and ($row_num <= $display_end) )  )
      {
        $projet_is_open = "X";
        if ( ($fpj_date_close != '0000-00-00') or ($fpj_name == "") )  $projet_is_open = "";
        //
        echo "<TR>";
        //
        echo "<FORM METHOD='POST' ACTION='files_sharing_project_update_name.php?'>";
        //echo "<TD valign='center' VALIGN='MIDDLE' class='row1'>";
        echo "<TD valign='center' VALIGN='MIDDLE' ";
          if ($projet_is_open == "") 
            echo "class='row3'>";  
          else 
            echo "class='row1'>";
        //
        echo "<input type='text' name='fpj_name' maxlength='50' value='" . $fpj_name . "' size='25' class='post' />";
        echo " ";
        //echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_update . "' class='liteoption' />";
        echo "<INPUT TYPE='image' SRC='" . _FOLDER_IMAGES . "b_save.png' VALUE = '" . $l_admin_bt_update . "' ALT='" . $l_admin_bt_update . "' TITLE='" . $l_admin_bt_update . "' WIDTH='16' HEIGHT='16' />";
        echo "<input type='hidden' name='id_projet' value = '" . $id_projet . "' />";
        echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
        echo "<INPUT TYPE='hidden' name='tri' value = '" . $tri . "' />";
        echo "</TD>";
        echo "</FORM>";
        //
        //echo "<TD align='left' class='row1'>";
        echo "<TD align='left' ";
          if ($projet_is_open == "") 
            echo "class='row3'>";  
          else 
            echo "class='row1'>";
          //
          echo "<font face='verdana' size='2'>&nbsp;";
          echo $fpj_folder . "&nbsp;</font>";
        echo "</TD>";
        //
        //echo "<TD align='center' class='row2'>";
        echo "<TD align='center' ";
          if ($projet_is_open == "") 
            echo "class='row3'>";  
          else 
            echo "class='row2'>";
          //
          if ($fpj_date_creat == '0000-00-00')
            $fpj_date_creat = 	'&nbsp;';
          else
            $fpj_date_creat = date($l_date_format_display, strtotime($fpj_date_creat));
          //
          echo "<font face='verdana' size='2'>";
          if ( $fpj_date_creat != date($l_date_format_display) )
            echo "<font color='gray'>";
          //
          if ($projet_is_open == "") echo "<I>";
          echo $fpj_date_creat . "</font>";
        echo "</TD>";
        //
/*
        //echo "<TD align='center' class='row2'>";
        echo "<TD align='center' ";
          if ($projet_is_open == "") 
            echo "class='row3'>";  
          else 
            echo "class='row2'>";
          //
          if ($fpj_date_end == '0000-00-00')
            $fpj_date_end = 	'&nbsp;';
          else
            $fpj_date_end = date($l_date_format_display, strtotime($fpj_date_end));
          //
          echo "<font face='verdana' size='2'>";
          if ( $fpj_date_end != date($l_date_format_display) )
            echo "<font color='gray'>";
          //
          echo $fpj_date_end . "</font>";
        echo "</TD>";
*/
        //
        //echo "<TD align='center' class='row2'>";
        echo "<TD align='center' ";
          if ($projet_is_open == "") 
            echo "class='row3'>";  
          else 
            echo "class='row2'>";
          //
          if ($fpj_date_close == '0000-00-00')
            $fpj_date_close = '&nbsp;';
          else
          {
            $fpj_date_close = date($l_date_format_display, strtotime($fpj_date_close));
            echo "<font color='red'>";
          }
          echo "<font face='verdana' size='2'>";
          //
          if ($projet_is_open == "") echo "<I>";
          echo $fpj_date_close . "</font>";
        echo "</TD>";
        //
        //echo "<TD align='center' class='row2'>";
        echo "<TD align='center' ";
          if ($projet_is_open == "") 
            echo "class='row3'>";  
          else 
            echo "class='row2'>";
          //
          if ($fpj_name == "")
            echo "&nbsp;";
          else
          {
            //if ($fpj_date_close == '0000-00-00')
            if ($fpj_date_close == '&nbsp;')
            {
              echo "<A HREF='files_sharing_project_update_status.php?id_projet=" . $id_projet . "&status=close&lang=" . $lang . "&'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "b_lock.png' WIDTH='16' HEIGHT='16' ALT='" . $l_menu_group_add_member . "' TITLE='" . $l_menu_group_add_member . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
            }
            else
            {
              echo "<A HREF='files_sharing_project_update_status.php?id_projet=" . $id_projet . "&status=open&lang=" . $lang . "&'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "b_back.gif' WIDTH='16' HEIGHT='16' ALT='" . $l_menu_group_add_member . "' TITLE='" . $l_menu_group_add_member . "' border='0'></A>&nbsp;"; // ALIGN='BASELINE'
            }
          }
        echo "</TD>";
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
      display_nb_row_page(15, $nb_row_by_page, "list_files_projects_nb_rows");
      echo " | ";
      display_nb_row_page(20, $nb_row_by_page, "list_files_projects_nb_rows");
      echo " | ";
      display_nb_row_page(25, $nb_row_by_page, "list_files_projects_nb_rows");
      echo " | ";
      display_nb_row_page(30, $nb_row_by_page, "list_files_projects_nb_rows");
      echo " | ";
      display_nb_row_page(50, $nb_row_by_page, "list_files_projects_nb_rows");
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
    echo "<TD colspan='6' ALIGN='CENTER' class='row2'>";
      echo "<font face='verdana' size='2'>" . $l_admin_share_file_project_empty;
    echo "</TD>";
    echo "</TR>";
    echo "</TABLE>";

    echo "</TD></TR>";
    echo "</TABLE>";
  }
  //
  mysqli_close($id_connect);
  //
  //
  echo "<BR/>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<TR>";
  echo "<TD align='center' COLSPAN='3' class='catHead'>";
  echo "<font face=verdana size=3><b>" . $l_admin_share_file_project_add_new . "</b></font>";
  echo "</TD>";
  echo "</TR>";
  //
  echo "<TR>";
  echo "<FORM METHOD='POST' ACTION='files_sharing_project_add.php?'>";
  echo "<TD class='row2'>";
  echo "<font face=verdana size=2>";
  echo $l_admin_share_files_col_projet . " : <input type='text' name='fpj_name' maxlength='50' value='' size='25' class='post' />";
  //echo "<BR/>";
  echo "</TD><TD class='row2'>";
  //echo "<font face=verdana size=2>";
  //echo $l_admin_users_order_function . " : <input type='text' name='nom' maxlength='40' size='20' class='post' />";
  echo "</TD><TD class='row2'>";
  //echo " ";
  echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_add . "' class='liteoption' />";
  echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
  echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
  echo "</TD></TR>";
  echo "</FORM>";
  //
  echo "</TABLE>";
  //echo "<font face='verdana' size='2'>";
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