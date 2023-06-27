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
//
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php");
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_avatar_title . "</title>";
display_header();
if (_PUBLIC_POST_AVATAR == "") // not allowed to post avatars...
{
  echo '<META http-equiv="refresh" content="0;url=../"> ';
  die();
}
//echo '<META http-equiv="refresh" content="40"; url=""> ';
echo "</head>";
echo "<body background='" . _FOLDER_IMAGES . f_background_image_color() . "background.jpg'>";
//
//display_menu();
//
$repert = "upload/";
//
// 
echo "<CENTER>";
echo "<SMALL><SMALL><BR/></SMALL></SMALL>";
if ($lang != 'FR') echo " <A HREF='?lang=FR&' TITLE='Français'><IMG SRC='../images/flags/fr.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'EN') echo " <A HREF='?lang=EN&' TITLE='English'><IMG SRC='../images/flags/us.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'IT') echo " <A HREF='?lang=IT&' TITLE='Italian'><IMG SRC='../images/flags/it.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'ES') echo " <A HREF='?lang=ES&' TITLE='Spanish'><IMG SRC='../images/flags/es.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'PT') echo " <A HREF='?lang=PT&' TITLE='Portuguese'><IMG SRC='../images/flags/pt.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'BR') echo " <A HREF='?lang=BR&' TITLE='Portuguese'><IMG SRC='../images/flags/br.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'RO') echo " <A HREF='?lang=RO&' TITLE='Romana'><IMG SRC='../images/flags/ro.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'DE') echo " <A HREF='?lang=DE&' TITLE='German'><IMG SRC='../images/flags/de.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
if ($lang != 'NL') echo " <A HREF='?lang=NL&' TITLE='Netherlands'><IMG SRC='../images/flags/nl.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
echo "<BR/>";
echo "<SMALL><SMALL><BR/></SMALL></SMALL>";
//
//
echo "<table width='650' class='forumline' cellspacing='1' cellpadding='1'>";
echo "<TR>";
echo "<TH class='thHead'>";
echo "<FONT size='3'>";
echo $l_admin_avatar_title_2;
echo "</TH>";
echo "</TR>\n";
echo "<FORM method='POST' ENCTYPE='multipart/form-data' name='form_send_image' action='avatar_upload.php'>";
echo "<TR>";
echo "<TD ALIGN='LEFT' class='row2'>";
echo "<INPUT type='hidden' name='MAX_FILE_SIZE'  VALUE='200000'>";
echo "<INPUT type='file' name='nom_du_fichier' size='80' class='post'>";  // mainoption post
echo "&nbsp; &nbsp; ";
//echo "<INPUT type='submit' value='" . $l_admin_mess_bt_send . "' class='liteoption'>";
echo "</TD>";
echo "</TR>";
echo "<TR>";
echo "<TD ALIGN='LEFT' class='row1'>";
$aleat = rand (5678, 9587);
echo "<font name='verdana' size='2'>";
echo $l_captcha . " : ";
echo "<font name='times new roman' size=2><I>" . $aleat . "</I></font> ==> ";
echo "<INPUT type='input' name='security_code' size='4' maxlength='4' class='post'>";
echo "</TD>";
echo "</TR>";
/*
echo "<TR>";
echo "<TD class='row2'>";
echo "</TD>";
echo "</TR>";
echo "<TR>";
*/
echo "<TD ALIGN='center' class='catBottom'>";
echo "<INPUT type='hidden' name='dest'  VALUE='" . $repert . "'>";
echo "<INPUT type='hidden' name='lang'  VALUE='" . $lang . "'>";
echo "<INPUT type='hidden' name='sc'  VALUE='" . $aleat . "'>";
echo "<INPUT type='submit' value='" . $l_admin_mess_bt_send . "' class='liteoption'>";
echo "</TD>";
echo "</TR>";
echo "</FORM>";
echo "</TABLE>";
echo "<BR/>";
//
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
      if ( (!is_dir($file)) and (strlen($file) <= 20) and ( (strpos($ext, ".png")) or (strpos($ext, ".gif")) or (strpos($ext, ".jpg")) or (strpos($ext, ".jpeg")) ) )
      {
        $tab_files[] = $file;
      }
    }
  }
  closedir($rep);
  //
  $nb_avatars = 0;
  if (!empty($tab_files))
  {
    echo "<table width='650' class='forumline' cellspacing='1' cellpadding='1'>";
    echo "<TR>";
    echo "<TH colspan='6' class='thHead'>";
    echo "<FONT size='3'>";
    echo $l_admin_avatar_title_5;
    echo "</TH>";
    echo "</TR>\n";
    echo "<TR>";
    sort($tab_files);// pour le tri croissant, rsort() pour le tri décroissant
    $num_col = 0;
    foreach($tab_files as $file) 
    {
        $nb_avatars++;
        if ($num_col > 5) 
        {
          echo "</TR>\n";
          echo "<TR>";
          $num_col = 0;
        }
        $num_col++;
        echo "<TD class='row2' ALIGN='CENTER'>";
        echo "<IMG SRC='" . $repert . $file . "' alt='" . $file . "' title='" . $file . "' ><BR/>";
        //echo "<INPUT name='avatar' TYPE='radio' VALUE='" . $file . "' class='genmed' />";
        echo "<font face='verdana' size='1'>" . $file . "</font>";
        //echo "</select>";
        echo "</TD>\n";
    }
    echo "</TR>";
  }
  echo "</TABLE>";
}
echo "<BR/>";
//
//display_menu_footer();

echo "</body></html>";
?>