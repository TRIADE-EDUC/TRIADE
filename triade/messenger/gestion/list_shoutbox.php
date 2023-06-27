<?php 	
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2015 THeUDS           **
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
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['page'])) $page = $_GET['page']; else $page = "";
if (isset($_GET['id_grp'])) $id_grp = intval($_GET['id_grp']); else $id_grp = "";
if (isset($_GET['id_user'])) $id_user = intval($_GET['id_user']); else $id_user = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_shoutbox);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_options_shoutbox_title_short . "</title>";
display_header();
echo '<META http-equiv="refresh" content="60;url=" />';
if ( (_SHOUTBOX != "") and (_SHOUTBOX_PUBLIC != "") ) echo '<link rel="alternate" title="test RSS" type="application/rss+xml" href="../' . _PUBLIC_FOLDER . '/rss/shoutbox.xml">';
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face=verdana size=2>";
if ( _SHOUTBOX != '' )
{
  require ("../common/sql.inc.php");
  require ("../common/shoutbox.inc.php");
  //
  // Ménage avant affichage :
  //shoutbox_remove_old_msg(); (maintenant dans shoutbox_send.inc.php)
  //
  if ($id_user > 0) $id_grp = 0;
  //
  //if ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '' ) xor ( _GROUP_FOR_SBX_AND_ADMIN_MSG != '') )
  if ( ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '' ) or ( _SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '' ) ) xor ( _GROUP_FOR_SBX_AND_ADMIN_MSG != '') )
  {
    if (strval($id_grp) == "")   // strval absolument !!!    car    ->     if ($id_grp == "")  ->  ne fonctionne pas !!!
    {
      $nbre0 = 0;
      $requete  = " SELECT count(ID_SHOUT) "; // GRP.GRP_PRIVATE, 
      $requete .= " FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
      $requete .= " WHERE ID_GROUP_DEST = 0 ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-V1c]", $requete);
      if ( mysqli_num_rows($result) == 1)
      {
        list ($nbre0) = mysqli_fetch_row ($result);
      }
      //
      $requete  = " SELECT GRP.ID_GROUP, GRP.GRP_NAME, GRP.GRP_SHOUTBOX, GRP.GRP_SBX_NEED_APPROVAL, count(SBX.ID_SHOUT) "; // GRP.GRP_PRIVATE, 
      $requete .= " FROM " . $PREFIX_IM_TABLE . "GRP_GROUP GRP ";
      $requete .= " LEFT JOIN " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX SBX ON SBX.ID_GROUP_DEST = GRP.ID_GROUP ";
      $requete .= " WHERE SBX.ID_GROUP_DEST > 0 ";
      $requete .= " GROUP BY ID_GROUP ";
      $requete .= " ORDER BY UPPER(GRP_NAME) ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-V1d]", $requete);
      if ( mysqli_num_rows($result) > 0 )
      {
        echo "<BR/>";
        echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
        echo "<THEAD>";
        echo "<TR>";
          echo "<TH align='center' COLSPAN='4' class='thHead'>";
          echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_options_shoutbox_title_short . "&nbsp;</B></font>";
          if (_SHOUTBOX_PUBLIC != "") 
          {
            echo "&nbsp; &nbsp; <A href='../" . _PUBLIC_FOLDER . "/rss/" . "shoutbox.xml' target='_blank'>";
            echo "<IMG SRC='" . _FOLDER_IMAGES . "rss.png' WIDTH='16' HEIGHT='16' ALT='RSS' TITLE='RSS' border='0' />";
            echo "</A>";
          }
          //echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_options_shoutbox_title_long . "&nbsp;</B></font></TH>";
          echo "</TH>";
        echo "</TR>";
        echo "<TR>";
          //display_row_table("&nbsp;" . $l_admin_group_order_group . "&nbsp;", '');
          echo "<TD COLSPAN='2' class='catHead'></TD>";
// l_index_shoutbox_nb_msg_wait (l_index_shoutbox_pending  )
          display_row_table("&nbsp;" . $l_admin_stats_col_nb_msg . "&nbsp;", '');
          //display_row_table("&nbsp;" . $l_admin_options_shoutbox_title_short . "&nbsp;", '');
          display_row_table($l_admin_users_col_action, '');
        echo "</TR>";
        echo "</THEAD>";
        echo "<TBODY>";
        //
        // la shoutbox group=0 :
        echo "<TR>";
          echo "<TD class='row1' COLSPAN='2'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            echo $l_admin_options_shoutbox_title_long . "</A>&nbsp;";
          echo "</TD>";
          echo "<TD class='row1' align='RIGHT'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            echo $nbre0 . "&nbsp;";
          echo "</TD>";
          echo "<TD class='row1' align='CENTER'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            echo "<A HREF='list_shoutbox.php?id_grp=0&lang=" . $lang . "&' alt='" . $l_display . "' title='" . $l_display . "' class='cattitle'>";
            echo $l_display . "</A>&nbsp;";
          echo "</TD>";
          //
        echo "</TR>";
        
        echo "<TR>";
          //echo "<TD class='row3' COLSPAN='4'>";
          echo "<TD class='catBottom' COLSPAN='4'>";
          echo "</TD>";
        echo "</TR>";
        
        echo "\n";
        echo "<TR>";
          echo "<TH align='center' COLSPAN='4' class='thHead'>";
          echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_options_shoutbox_title_short . " - " . $l_admin_group_title . "&nbsp;</B></font></TH>";
        echo "</TR>";
        echo "<TR>";
          display_row_table("&nbsp;" . $l_admin_group_col_group . "&nbsp;", '');
          display_row_table("&nbsp;" . $l_admin_options_shoutbox_title_short . "&nbsp;", '');
          display_row_table("&nbsp;" . $l_admin_stats_col_nb_msg . "&nbsp;", '');
          display_row_table($l_admin_users_col_action, '');
        echo "</TR>";
        //
        // Les shoutboxs des groupes ( > 0 ) :
        while( list ($id_group, $group, $grp_shoutbox_allowed, $grp_shoutbox_need_approval, $nbre) = mysqli_fetch_row ($result) )
        {
          echo "<TR>";
          //
          // COl group
          //
          echo "<TD class='row1'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            //echo "<A HREF='list_shoutbox.php?id_grp=" . $id_group . "&lang=" . $lang . "&' alt='" . $l_display . "' title='" . $l_display . "' class='cattitle'>";
            echo "<A HREF='list_group_members.php?id_group=" . $id_group . "&lang=" . $lang . "&' alt='" . $l_admin_group_members . "' title='" . $l_admin_group_members . "' class='cattitle'>";
            echo $group . "</A>&nbsp;";
          echo "</TD>";
          //
          // COl shoutbox
          //
          echo "<TD class='row1' align='CENTER'>";
            echo "<A HREF='list_group.php?lang=" . $lang . "&'>";
            if ($grp_shoutbox_allowed > 0)
            {
              if ($grp_shoutbox_need_approval > 0)
                echo "<IMG SRC='" . _FOLDER_IMAGES . "shoutbox_approval.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_options_shoutbox_need_approval . "' TITLE='" . $l_admin_options_shoutbox_need_approval . "' border='0' />";
              else
                echo "<IMG SRC='" . _FOLDER_IMAGES . "shoutbox_no_approval.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_check_on . "' TITLE='" . $l_admin_check_on . "' border='0' />";
            }
            else
            {
              echo "<IMG SRC='" . _FOLDER_IMAGES . "shoutbox_disabled.png' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_check_off . "' TITLE='" . $l_admin_check_off . "' border='0' />";
            }
            echo "</A>";
            //
            if (_SHOUTBOX_PUBLIC != "") 
            {
              $chemin = "../" . _PUBLIC_FOLDER . "/rss/" . "shoutbox" ;
              if ($id_group > 0) $chemin .= f_encode64($id_group);
              $chemin .= ".xml";
              echo "&nbsp; &nbsp; <A href='" . $chemin . "' target='_blank'>";
              echo "<IMG SRC='" . _FOLDER_IMAGES . "rss.png' WIDTH='16' HEIGHT='16' ALT='RSS' TITLE='RSS' border='0' />";
              echo "</A>";
            }

          echo "</TD>";
          //
          // COl nbre
          //
          echo "<TD class='row1' align='RIGHT'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            echo $nbre . "&nbsp;";
          echo "</TD>";
          //
          // COl action
          //
          echo "<TD class='row1' align='CENTER'>";
            echo "<font face='verdana' size='2'>&nbsp;";
            echo "<A HREF='list_shoutbox.php?id_grp=" . $id_group . "&lang=" . $lang . "&' alt='" . $l_display . "' title='" . $l_display . "' class='cattitle'>";
            echo $l_display . "</A>&nbsp;";
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
        $id_grp = 0;
    }
  }
  else
    $id_grp = 0;
  //
  //
  if (strval($id_grp) != "") // strval absolument !!!    car    ->     if ($id_grp != "")  ->  ne fonctionne pas !!!
    require("list_shoutbox.inc.php");
	//
  mysqli_close($id_connect);
}
else
{
  echo "<BR/>";
  echo "<div class='warning'>";
  echo $l_admin_shoutbox_cannot;
  echo "</div>";
}
//
display_menu_footer();
//
echo "</body></html>";
?>