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
if (isset($_GET['sxd'])) $sxd = $_GET['sxd']; else $sxd = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_save_title . "</title>";
display_header();
//echo '<META http-equiv="refresh" content="60;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face=verdana size=2>";
echo "<BR/>";
//
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{

  $use_ext_backup = "";
  $ext_backup_folder = "../common/library/sypex_dumper/sxd/";
  if ( ($sxd == "") and (is_readable($ext_backup_folder . "index.php")) and (is_readable($ext_backup_folder . "auth_intramessenger.php")) and (is_readable($ext_backup_folder . "backup/index.php")) )
  {
    if ( (!is_writeable($ext_backup_folder . "ses.php")) or (!is_writeable($ext_backup_folder . "cfg.php")) )
    {
      echo $l_admin_save_cannot_use . " <I>Sypex Dumper</I><BR/>";
      if (!is_writeable($ext_backup_folder . "ses.php")) echo "<I>" . $ext_backup_folder . "ses.php" . "</I> : <FONT COLOR='RED'><B> " . $l_admin_check_not_writeable . " !</B></FONT> (chmod)<BR/>";
      if (!is_writeable($ext_backup_folder . "cfg.php")) echo "<I>" . $ext_backup_folder . "cfg.php" . "</I> : <FONT COLOR='RED'><B> " . $l_admin_check_not_writeable . " !</B></FONT> (chmod)<BR/>";
      echo "<BR/>";
    }
    else
      $use_ext_backup = "X";
  }
  if ($use_ext_backup != "")
  {
    echo "<B><A HREF='?lang=" . $lang . "&sxd=no&'>" . $l_admin_save_do_not_use . " <I>Sypex Dumper</I></A></B><BR/><BR/>";
    echo "<IFRAME SRC='" . $ext_backup_folder . "' WIDTH='584' HEIGHT='480' VALIGN='TOP' FRAMEBORDER='0' title='Sypex Dumper' name='Sypex Dumper'></IFRAME>";
  } // 461
  else
  {
    echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
    echo "<TR>";
      echo "<TD align='center' COLSPAN='3' class='catHead'>";
      echo "&nbsp;<font face=verdana size=3><b>" . $l_admin_save_title . "</b></font>&nbsp;";
      echo "</TD>";
    echo "</TR>";


    echo "<TR>";
      echo "<FORM METHOD='POST' ACTION='save_db.php?'>";
      echo "<TD class='row2' align='center'>";
      echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_save_bt_now . "' class='mainoption' />";
      echo "<INPUT TYPE='hidden' name='action' value = 'save' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
      echo "<INPUT TYPE='hidden' name='sxd' value = '" . $sxd . "' />";
      echo "</TD>";
    echo "</TR>";
    echo "</FORM>";

    echo "</TABLE>";
    //

    function format_size($bytes)
    {
      $kb = $bytes / 1024;

      if ( $kb < 1024 )
      {
        return round( $kb) .' KB';
      }

      $mb = $kb / 1024;

      if ( $mb < 1024 )
      {
        return round( $mb, 1 ) .' MB';
      }
    }

    function select_file($filename, $folder)
    {
      GLOBAL $l_date_format_display, $l_time_format_display;
      //
      $file = $folder . $filename;
      if (is_readable($file))
      {
        $sof = filesize($file);
        if ($sof > 10)
        {
          echo "<TR>";
          echo "<TD class='row1'>";
          echo "<font face='verdana' size='2'>";
          echo "<INPUT name='file' TYPE='radio' VALUE='" . $filename . "' class='genmed' />";
          echo $filename;
          echo "</TD>";
          echo "<TD class='row1' align='CENTER'>";
          echo "<font face='verdana' size='2'>" . date($l_date_format_display . " - ". $l_time_format_display, filemtime($file)) . "";
          echo "</TD>";
          echo "<TD class='row1' align='RIGHT'>";
          //echo "<font face='verdana' size='2'>" . round($sof / 1024) . " KB";
          echo "<font face='verdana' size='2'>" . format_size($sof);
          echo "</TD>";
          echo "</TR>";
        }
      }
    }
    echo "<BR/>";
    $repert = "save/";
    //
    if (is_dir($repert)) 
    {
      $rep = opendir($repert);
      $tab_files = array(); // on déclare le tableau contenant le nom des fichiers
      while ($file = readdir($rep))
      {
        if ($file != ".." && $file != "." && $file !="" ) // .inc.php && strpos(strtolower($file), ".*") 
        {
          $ext = strtolower(substr($file,-5));
          if ( (!is_dir($file)) and (strlen($file) > 30) and (strpos($ext, ".sql")) )
          {
            $tab_files[] = $file;
          }
        }
      }
      closedir($rep);
      //
      if (!empty($tab_files))
      {
        echo "<table width='650' class='forumline' cellspacing='1' cellpadding='1'>";
        echo "<TR>";
        echo "<TH colspan='3' class='thHead'>";
        echo "<FONT size='3'>";
    //    if (_MAINTENANCE_MODE != '')
    //      echo $l_admin_save_selet_to_restore;
    //    else
          echo $l_admin_save_list;
        echo "</TH>";
        echo "</TR>\n";
        echo "<FORM METHOD='POST' name='formulaire' ACTION ='save_delete.php?'>";
        rsort($tab_files); // pour le tri décroissant, sort() pour le tri croissant
        foreach($tab_files as $file) 
        {
          select_file($file, $repert);
        }
        echo "<TR>";
        echo "<TD colspan='3' ALIGN='CENTER' class='catBottom'>";
    //    if (_MAINTENANCE_MODE != '')
    //    echo "<INPUT class='liteoption' TYPE='submit' VALUE ='" . $l_admin_save_bt_restore . "' />";
        echo "<INPUT class='liteoption' TYPE='submit' VALUE ='" . $l_admin_bt_delete . "' />";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
        echo "<INPUT TYPE='hidden' name='sxd' value = '" . $sxd . "' />";
        echo "</TD>";
        echo "</TR>";
        echo "</FORM>";
        //
        echo "</TABLE>";
        echo "<BR/>";
    //    if (_MAINTENANCE_MODE == '')
    //      echo "<B>" . $l_admin_save_not_in_maintenance . "</B>";
      }
    }
  }
}
else
  echo "Not in demo version...";
//
display_menu_footer();
//
echo "</body></html>";
?>