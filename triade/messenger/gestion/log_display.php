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
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
if (isset($_POST['action'])) $action = $_POST['action']; else $action = "";
if (isset($_POST['folder'])) $folder_log = base64_decode($_POST['folder']); else $folder_log = "";
if (isset($_POST['title'])) $title = base64_decode($_POST['title']); else $title = "";
//
$repertoire  = getcwd() . "/"; 
if ( ($action == "") or (substr_count($repertoire, "/admin_demo/") > 0) or (substr_count($repertoire, "\admin_demo/") > 0) )
{
  header("location:log.php?lang=" . $lang . "&");  
  break;
}
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_log_title . "</title>";
display_header();
//echo '<META http-equiv="refresh" content="40"; url=""> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
//
if ($folder_log == "")
{
  $folder_log = "../distant/log/";
}
if ($action == "log_upload_avatar") $folder_log = "../" . _PUBLIC_FOLDER . "/log/";
//
$array_file_content = array();
$file_content = '';
$fic = $action . ".txt";
$handle = fopen($folder_log . $fic, "r");
if ($handle) 
{
  while (!feof($handle)) 
  {
    $file_content .= fgets($handle, 4096);
  }
  fclose($handle);
  $array_file_content = explode("\n", $file_content);
  //
  $nb_lines = count($array_file_content);
  if ($nb_lines > 0)
  {
    echo "<table width='100%' class='forumline' cellspacing='1' cellpadding='1' BGCOLOR='#CEDCEC'>";
    echo "<TR>";
    echo "<TH colspan='10' class='thHead'>";
    echo "<FONT size='3'>";
    echo $title;
    echo "</TH>";
    echo "</TR>\n";
    for ($j = $nb_lines; $j > 0; $j--)
    {
      $buffer = trim($array_file_content[$j - 1]);
      if (substr($buffer, strlen($buffer) , 1) != ";") $buffer .= ";";
      if (strlen($buffer) > 10) 
      {
        $n = substr_count($buffer, ";");
        if ($n > 1)
        {
          $p = explode(";", $buffer);
          echo "<TR>";
          for ($i = 0; $i < $n; $i++) 
          {
            if (strlen($p[$i]) == 1) 
            {
              if (ord($p[$i]) < 32) $p[$i] = "";
            }
            if (strlen($p[$i]) > 0) 
            {
              if ( ($action == "log_send_message") and ($i == 2) )
              {
                $p[$i] = base64_decode($p[$i]);
              }
              //
              echo "<TD class='row2'><font size='1'>";
              $p[$i] =   str_replace("%20", " ", $p[$i]);
              echo $p[$i];
              echo "</TD>";
            }
          }
          echo "</TR>\n";
        }
      }
    }
    echo "</TABLE>";
  }
  
}
echo "<BR/>";
//
//
display_menu_footer();

echo "</body></html>";
?>