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
if (isset($_COOKIE['im_no_avatar_images_filter'])) $option_no_avatar_images_filter = $_COOKIE['im_no_avatar_images_filter'];  else  $option_no_avatar_images_filter = '1';
//
if (intval($option_no_avatar_images_filter) <= 0) $option_no_avatar_images_filter = "";
//
if (isset($_GET['id_user_select'])) $id_user_select = intval($_GET['id_user_select']);  else  $id_user_select = 0;
if (isset($_GET['avatar'])) $avatar = $_GET['avatar'];  else  $avatar = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_avatars);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_avatar_title . "</title>";
display_header();
//echo '<META http-equiv="refresh" content="40"; url=""> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
$username = "";
if ($id_user_select > 0)
{
  require ("../common/sql.inc.php");
  $username = f_get_username_of_id($id_user_select);
  if ($username == "") $id_user_select = 0;
  //
  mysql_close($id_connect);
}
//
//
if ($id_user_select <= 0)
{
  //
  // -------------------------------------------------------------
  //  Parcours du répertoire des avatars en attente de validation
  // -------------------------------------------------------------
  //
  $repert = "../" . _PUBLIC_FOLDER . "/upload/";
  //
  if (is_dir($repert)) 
  {
    $rep = opendir($repert);
    $tab_files = array(); // on déclare le tableau contenant le nom des fichiers
    $tab_files_reject = array(); // on déclare le tableau contenant le nom des fichiers rejetés
    while ($file = readdir($rep))
    {
      if ( ($file != "..") and ($file != ".") and ($file != "") and (!is_dir($file)) ) // .inc.php && strpos(strtolower($file), ".*") 
      {
        //$ext = strtolower(substr($file,-5));
        //$ext = strtolower(array_pop(explode('.', $file)));
        $ext = explode('.', $file);
        $ext = array_pop($ext);
        $ext = strtolower($ext);
        $tmime_ok = false;
        if ($option_no_avatar_images_filter <> '')
        {
          $tmime = mime_content_type($repert . $file);
          if (substr($tmime, 0, 6) == "image/") $tmime_ok = true;
        }
        else
          $tmime_ok = true;
        //
        //if ( ($tmime_ok == true) and (strpos(".png.gif.jpg.jpeg", $ext)) )
        if (strpos(".png.gif.jpg.jpeg", $ext))
        {
          if ($tmime_ok == true)
          {
            $size = getimagesize($repert . $file);
            if ( (strlen($file) <= 20) and (intval($size[0]) >= 30) and (intval($size[1]) >= 30) and (intval($size[0]) <= 150) and (intval($size[1]) <= 150) )
            {
              $tab_files[] = $file;
            }
            else
              $tab_files_reject[] = $file;
          }
          else
            $tab_files_reject[] = $file;
        }
      }
    }
    closedir($rep);
    //
    // --------------------
    //  AVATARS EN ATTENTE
    // --------------------
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
      //
      //echo "<FORM METHOD='POST' name='formulaire' ACTION ='avatar_valid.php?'>";
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
          //echo "<font face='verdana' size='1'>" . $file . "</font>";
          //echo "</select>";
          //
          echo "<A HREF='avatar_valid.php?avatar=" . $file . "&lang=" . $lang . "&' title='" . $l_admin_bt_allow . "'>";
          echo "<IMG SRC='" . _FOLDER_IMAGES . "b_ok_2.png' ALT='" . $l_admin_bt_allow . "' TITLE='" . $l_admin_bt_allow . "' WIDTH='16' HEIGHT='16' BORDER='0'></A>";
          //
          echo " &nbsp; &nbsp; ";
          //
          echo "<A HREF='avatar_deny.php?avatar=" . base64_encode($file) . "&lang=" . $lang . "&' title='" . $l_admin_bt_delete . "'>";
          echo "<IMG SRC='" . _FOLDER_IMAGES . "b_drop.png' ALT='" . $l_admin_bt_delete . "' TITLE='" . $l_admin_bt_delete . "' WIDTH='16' HEIGHT='16' BORDER='0'></A>";

          echo "</TD>\n";
      }
      echo "</TR>";
      /*
      echo "<TR>";
      echo "<TD colspan='6' ALIGN='CENTER' class='catBottom'>";
      echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_bt_allow . "' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
      echo "</TD>";
      echo "</TR>";
      echo "</FORM>";
      */
      echo "</TABLE>";
      echo "<BR/>";
    }
    //
    // -----------------------
    //  AVATARS INNACEPTABLES
    // -----------------------
    //
    if (!empty($tab_files_reject))
    {
      echo "<table width='650' class='forumline' cellspacing='1' cellpadding='1'>";
      echo "<TR>";
      echo "<TH colspan='6' class='thHead'>";
      echo "<FONT size='3'>";
      echo $l_admin_avatar_title_6;
      echo "</TH>";
      echo "</TR>\n";
      //
      //echo "<FORM METHOD='POST' name='formulaire' ACTION ='avatar_valid.php?'>";
      echo "<TR>";
      sort($tab_files_reject);// pour le tri croissant, rsort() pour le tri décroissant
      $num_col = 0;
      foreach($tab_files_reject as $file) 
      {
          if ($num_col > 5) 
          {
            echo "</TR>\n";
            echo "<TR>";
            $num_col = 0;
          }
          $num_col++;
          echo "<TD class='row2' ALIGN='CENTER'>";
          echo "<IMG SRC='" . $repert . $file . "' alt='" . $file . "' title='" . $file . "' ><BR/>";
          //
          echo "<A HREF='avatar_deny.php?avatar=" . base64_encode($file) . "&lang=" . $lang . "&' title='" . $l_admin_bt_delete . "'>";
          echo "<IMG SRC='" . _FOLDER_IMAGES . "b_drop.png' ALT='" . $l_admin_bt_delete . "' TITLE='" . $l_admin_bt_delete . "' WIDTH='16' HEIGHT='16' BORDER='0'></A>";

          echo "</TD>\n";
      }
      echo "</TR>";
      echo "</FORM>";
      /*
      echo "<TR>";
      echo "<TD colspan='6' ALIGN='CENTER' class='catBottom'>";
      echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_bt_delete . "' />";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
      echo "</TD>";
      echo "</TR>";
      echo "</FORM>";
      */
      echo "</TABLE>";
      echo "<BR/>";
    }
  }
}
//
// ---------------------------------------------
//  Parcours du répertoire des avatars diffusés
// ---------------------------------------------
//
$repert = "../distant/avatar/";
//
if (is_dir($repert)) 
{
  $rep = opendir($repert);
  $tab_files = array(); // on déclare le tableau contenant le nom des fichiers
  $tab_files_reject = array(); // on déclare le tableau contenant le nom des fichiers rejetés
  while ($file = readdir($rep))
  {
    if ( ($file != "..") and ($file != ".") and ($file !="") and (!is_dir($file)) ) // .inc.php && strpos(strtolower($file), ".*") 
    {
      //$ext = strtolower(substr($file,-5));
      //$ext = strtolower(array_pop(explode('.', $file)));
      $ext = explode('.', $file);
      $ext = array_pop($ext);
      $ext = strtolower($ext);
      $tmime_ok = false;
      if ($option_no_avatar_images_filter <> '')
      {
        $tmime = mime_content_type($repert . $file);
        if (substr($tmime, 0, 6) == "image/") $tmime_ok = true;
      }
      else
        $tmime_ok = true;
      //
      if ( ($tmime_ok == true) and (strpos(".png.gif.jpg.jpeg", $ext)) )
      {
        $size = getimagesize($repert. $file);
        if ( (strlen($file) <= 20) and (intval($size[0]) >= 30) and (intval($size[1]) >= 30) and (intval($size[0]) <= 150) and (intval($size[1]) <= 150) )
        {
          $tab_files[] = $file;
        }
        else
          $tab_files_reject[] = $file;
      }
    }
  }
  closedir($rep);
}
//
//
if ($id_user_select > 0)
{
  echo "<font face='arial,verdana' size='5'><B>";
  echo $username . "</B></font><BR/>";
  if ($avatar == "") $avatar = $username . ".jpg";
  if (is_readable($repert . $avatar))
  {
    echo "<IMG SRC='" . $repert . $avatar . "' /><BR/>";
  }
  echo "<SMALL><SMALL><BR/></SMALL></SMALL>";
}

//
// -------------------
//  ENVOYER UN AVATAR
// -------------------
//
echo "<table width='650' class='forumline' cellspacing='1' cellpadding='1'>";
echo "<TR>";
echo "<TH colspan='6' class='thHead'>";
echo "<FONT size='3'>";
echo $l_admin_avatar_title_2;
echo "</TH>";
echo "</TR>\n";
echo "<FORM method='POST' ENCTYPE='multipart/form-data' name='form_send_image' action='avatar_upload.php'>";
echo "<TR>";
echo "<TD colspan='6' ALIGN='LEFT' class='row1'>";
echo "<INPUT type='hidden' name='MAX_FILE_SIZE'  VALUE='200000'>";
echo "<INPUT type='hidden' name='dest'  VALUE='" . $repert . "'>";
echo "<INPUT type='hidden' name='lang'  VALUE='" . $lang . "'>";
if ($id_user_select > 0)
{
  echo "<INPUT type='hidden' name='id_user_select'  VALUE='" . $id_user_select . "'>";
  echo "<INPUT type='hidden' name='username'  VALUE='" . $username . "'>";
}
if (strpos(" " . $_SERVER["HTTP_USER_AGENT"], "Windows") > 0)
  echo "<INPUT type='file' name='nom_du_fichier' size='80' class='post'>";  // mainoption post
else
  echo "<INPUT type='file' name='nom_du_fichier' size='62' class='post'>";  // mainoption post
//
echo "&nbsp; &nbsp; ";
echo "<INPUT type='submit' value='" . $l_admin_mess_bt_send . "' class='liteoption'>";
echo "</TD>";
echo "</TR>";
echo "</FORM>";
//
//if ($nb_avatars < 15)
if (count($tab_files) < 15)
{
  echo "<TR>";
  echo "<TD align='center' COLSPAN='6' class='catBottom'>";
  echo "<font face='verdana' size='2'>";
  echo "<A HREF='http://www.theuds.com/download.php?soft=avatar-im&site=4&lang=" . $lang . "&from=" . $_SERVER['SERVER_NAME'] . "&' target='_blank'>" . $l_admin_avatar_bt_download . "</A>";  echo "</TD>";
  echo "</TR>";
}
//
echo "</TABLE>";
echo "</BR>";
//

// 
echo "<table width='650' class='forumline' cellspacing='1' cellpadding='1'>";
echo "<TR>";
echo "<TH colspan='6' class='thHead'>";
echo "<FONT size='3'>";
if ($id_user_select > 0)
  echo $l_admin_avatar_title_2;
else
  echo $l_admin_avatar_title_4;
echo "</TH>";
echo "</TR>\n";
//
// -----------------
//  AVATARS VALIDES
// -----------------
//
$nb_avatars = 0;
if (!empty($tab_files))
{
  if ($id_user_select > 0)
    echo "<FORM METHOD='POST' name='formulaire' ACTION ='avatar_change.php?'>";
  else
    echo "<FORM METHOD='POST' name='formulaire' ACTION ='avatar_delete.php?'>";
  //
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
			echo "<INPUT name='avatar' TYPE='radio' VALUE='" . $file . "' id='" . $file . "' class='genmed' />";
			echo "<font face='verdana' size='1'><label for='" . $file . "'>" . $file . "</label></font>";
			echo "</select>";
			echo "</TD>\n";
  }
  echo "</TR>";
  //
  echo "<TR>";
  echo "<TD colspan='6' ALIGN='CENTER' class='catBottom'>";
  if ($id_user_select > 0)
  {
    echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_bt_update . "' />";
    echo "<INPUT TYPE='hidden' name='id_user_select' value = '" . $id_user_select . "' />";
  }
  else
  {
    echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_bt_delete . "' />";
  }
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
  echo "</TD>";
  echo "</TR>";
  echo "</FORM>";
}
else
{
    echo "<TR>";
    echo "<TD colspan='6' ALIGN='CENTER' class='row2'>";
		echo "<font face='verdana' size='2'>";
    echo $l_admin_avatar_info_1 . " : " . $repert . "</font>";
    echo "</TD>";
    echo "</TR>";
}
echo "</TABLE>";
echo "<BR/>";
//echo "<P id=pending>";
//
//
// -----------------------------------------------
//  AVATARS INNACEPTABLES (uploadés manuellement)
// -----------------------------------------------
//
if (!empty($tab_files_reject))
{
  echo "<table width='650' class='forumline' cellspacing='1' cellpadding='1'>";
  echo "<TR>";
  echo "<TH colspan='6' class='thHead'>";
  echo "<FONT size='3'>";
  echo $l_admin_avatar_title_6;
  echo "</TH>";
  echo "</TR>\n";
  //
  //echo "<FORM METHOD='POST' name='formulaire' ACTION ='avatar_valid.php?'>";
  echo "<TR>";
  sort($tab_files_reject);// pour le tri croissant, rsort() pour le tri décroissant
  $num_col = 0;
  foreach($tab_files_reject as $file) 
  {
      if ($num_col > 5) 
      {
        echo "</TR>\n";
        echo "<TR>";
        $num_col = 0;
      }
      $num_col++;
      echo "<TD class='row2' ALIGN='CENTER'>";
      echo "<IMG SRC='" . $repert . $file . "' alt='" . $file . "' title='" . $file . "' ><BR/>";
      //
      echo "<A HREF='avatar_delete.php?avatar=" . $file . "&lang=" . $lang . "&' title='" . $l_admin_bt_delete . "'>";
      echo "<IMG SRC='" . _FOLDER_IMAGES . "b_drop.png' ALT='" . $l_admin_bt_delete . "' TITLE='" . $l_admin_bt_delete . "' WIDTH='16' HEIGHT='16' BORDER='0'></A>";

      echo "</TD>\n";
  }
  echo "</TR>";
  //echo "</FORM>";
  /*
  echo "<TR>";
  echo "<TD colspan='6' ALIGN='CENTER' class='catBottom'>";
  echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_bt_delete . "' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
  echo "</TD>";
  echo "</TR>";
  echo "</FORM>";
  */
  echo "</TABLE>";
  echo "<BR/>";
}
//
echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<FORM METHOD='GET' name='formulaire_cookies' ACTION ='set_cookies.php?'>";
  echo "<TR><TD COLSPAN='2' ALIGN='CENTER' class='catHead'>";
  echo "<B>" . $l_admin_display_title . "</B> (.png .gif .jpg .jpeg)</TD></TR>";
  //echo "<TR><TD COLSPAN='2' class='row3'>";
  echo "</TD></TR>";
  echo "<TR><TD COLSPAN='2' class='row1'>";
    echo "<font face='verdana' size='2'>";
    echo "<INPUT name='im_no_avatar_images_filter' id='im_no_avatar_images_filter' TYPE='CHECKBOX' VALUE='2' class='genmed' ";
    if ($option_no_avatar_images_filter <> '') echo "CHECKED";
    echo " />";
    echo "<label for='im_no_avatar_images_filter'>" . $l_admin_avatar_images_filter . "</label>&nbsp;"; //"<BR/>\n";
  echo "</TD></TR>";
  echo "<TR><TD COLSPAN='2' ALIGN='CENTER' class='catBottom'>";
  echo "<input type='hidden' name='action' value = 'avatar_changing' />"; // les paramètres de cette page, et y revenir ensuite
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
  echo "<INPUT class='liteoption' TYPE='submit' VALUE ='" . $l_admin_bt_update . "' />";
  echo "</TD>";
  echo "</TR>";
  echo "</FORM>";
echo "</TABLE>";
//
//
display_menu_footer();

echo "</body></html>";
?>