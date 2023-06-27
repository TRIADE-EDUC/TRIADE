<?php 	
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2010 THeUDS           **
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
if (isset($_POST['dest'])) $dest = $_POST['dest'];  else  $dest = "";
if (isset($_POST['lang'])) $lang = $_POST['lang'];  else  $lang = "";
if (isset($_POST['security_code'])) $security_code = $_POST['security_code'];  else  $security_code = "";
if (isset($_POST['sc'])) $sc = $_POST['sc'];  else  $sc = "";
//
if ($security_code == "")  exit("<font color='red'><center><br/><br/><B>Security code missing !");
if ($security_code != $sc) exit("<font color='red'><center><br/><br/><B>Security code error !");
//
define('INTRAMESSENGER',true);
require ("../common/sql.inc.php");  // pour send message alert !
require ("../common/functions.inc.php"); // pour send message alert !
require ("../common/config/config.inc.php");
require ("../distant/lang.inc.php"); // important !
#require ("lang.inc.php");
#require ("../common/menu.inc.php"); // après config.inc.php !
//
// not allowed to post avatars...
if (_PUBLIC_POST_AVATAR == "") die();
//
//
if ($_FILES['nom_du_fichier']['error']) 
{
  switch ($_FILES['nom_du_fichier']['error'])
  {
    case 1: // UPLOAD_ERR_INI_SIZE
      if ($lang == "FR")
        echo "Le fichier dépasse la limite autorisée par le serveur (fichier php.ini) (UPLOAD_ERR_INI_SIZE) !";
      else
        echo "The file exceeds the limit allowed by the server (file php.ini) (UPLOAD_ERR_INI_SIZE)!";
      break;
    case 2: // UPLOAD_ERR_FORM_SIZE
      if ($lang == "FR")
        echo "Le fichier dépasse la limite autorisée dans le formulaire HTML (UPLOAD_ERR_FORM_SIZE) !";
      else
        echo "The file exceeds the limit allowed in the HTML form (UPLOAD_ERR_FORM_SIZE)!";
      break;
    case 3: // UPLOAD_ERR_PARTIAL
      if ($lang == "FR")
        echo "L'envoi du fichier a été interrompu pendant le transfert (UPLOAD_ERR_PARTIAL) !";
      else
        echo "Sending the file has been interrupted during transfer (UPLOAD_ERR_PARTIAL)!";
      break;
    case 4: // UPLOAD_ERR_NO_FILE
      //echo "Le fichier que vous avez envoyé a une taille nulle (UPLOAD_ERR_NO_FILE) !";
      header("location:avatar.php?lang=" . $lang . "&");
      break;
  }
}
else 
{
  if (!is_dir($dest)) exit("Destination folder does not exist !");
  //
  if (!is_writable($dest)) exit("Cannot write in destination folder : chmod 775 or 777...");
  //
  if (is_readable($dest. $_FILES['nom_du_fichier']['name']))  exit("File already exist !");
  //
  if (is_readable("../distant/avatar/" . $_FILES['nom_du_fichier']['name']))  exit("File already exist.");
  //
  if (strlen($_FILES['nom_du_fichier']['name']) > 20) exit("Filename to long (20 characters max).");
  //
  if (_CENSOR_MESSAGES != "") 
  {
    if (is_readable("../common/config/censure.txt"))
    {
      require ("../common/words_filtering.inc.php");
      if ( textFilter($_FILES['nom_du_fichier']['name'], "../common/config/censure.txt") )  exit("Forbiden file name !");
    }
  }
  //
  if ( (isset($_FILES['nom_du_fichier']['name'])) and (isset($_FILES['nom_du_fichier']['type'])) )
  {
    if ( (exif_imagetype($_FILES['nom_du_fichier']['tmp_name']) == IMAGETYPE_GIF) or (exif_imagetype($_FILES['nom_du_fichier']['tmp_name']) == IMAGETYPE_JPEG) or (exif_imagetype($_FILES['nom_du_fichier']['tmp_name']) == IMAGETYPE_PNG)  or (exif_imagetype($_FILES['nom_du_fichier']['tmp_name']) == IMAGETYPE_BMP)  )
    {
      $size = getimagesize($_FILES['nom_du_fichier']['tmp_name']);
      if ( (intval($size[0]) < 30) or (intval($size[1]) < 30) or (intval($size[0]) > 150) or (intval($size[1]) > 150) )
        exit("Incorrect image size (30 => size <= 150) !");
      //
      move_uploaded_file($_FILES['nom_du_fichier']['tmp_name'], $dest . $_FILES['nom_du_fichier']['name']);
      write_log("log_upload_avatar", $_FILES['nom_du_fichier']['name']);
      //
      if (_SEND_ADMIN_ALERT != "")
      {
        $txt = $l_index_pending_avatars;
        if ($txt == "") $txt = "Pending avatar(s) waiting...";
        send_alert_message_to_admins($txt);
      }
      //
      header("location:avatar.php?lang=" . $lang . "&");
      break;
    }
    else
    {
      exit("This file is not a picture (jpg, png, gif, bmp) !");      
    }
  }
}
//
mysql_close($id_connect);
?>