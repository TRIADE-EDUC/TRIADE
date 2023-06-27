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
if (isset($_POST['dest'])) $dest = $_POST['dest'];  else  $dest = "";
if (isset($_POST['id_user_select'])) $id_user_select = intval($_POST['id_user_select']);  else  $id_user_select = 0;
if (isset($_POST['username'])) $username = $_POST['username'];  else  $username = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ! function_exists( 'exif_imagetype' ) ) 
  {
    function exif_imagetype ( $filename ) 
    {
      if ( ( list($width, $height, $type, $attr) = getimagesize( $filename ) ) !== false ) 
      {
        return $type;
      }
      return false;
    }
  }
  //
  if ($_FILES['nom_du_fichier']['error']) 
  {
    switch ($_FILES['nom_du_fichier']['error'])
    {
      case 1: // UPLOAD_ERR_INI_SIZE
        echo"Le fichier dépasse la limite autorisée par le serveur (fichier php.ini) (UPLOAD_ERR_INI_SIZE) !";
        break;
      case 2: // UPLOAD_ERR_FORM_SIZE
        echo "Le fichier dépasse la limite autorisée dans le formulaire HTML (UPLOAD_ERR_FORM_SIZE) !";
        break;
      case 3: // UPLOAD_ERR_PARTIAL
        echo "L'envoi du fichier a été interrompu pendant le transfert (UPLOAD_ERR_PARTIAL) !";
        break;
      case 4: // UPLOAD_ERR_NO_FILE
        echo "Le fichier que vous avez envoyé a une taille nulle (UPLOAD_ERR_NO_FILE) !";
        break;
    }
  }
  else 
  {
    if (!is_dir($dest)) exit("Destination folder does not exist.");
    //
    if (!is_writable($dest)) exit("Cannot write in destination folder : chmod 775 or 777...");
    //
    //if ((isset($_FILES['nom_du_fichier']['fichier'])&&($_FILES['nom_du_fichier']['error'] == UPLOAD_ERR_OK)) {
    if ( (isset($_FILES['nom_du_fichier']['name'])) and (isset($_FILES['nom_du_fichier']['type'])) )
    {
      if ( (exif_imagetype($_FILES['nom_du_fichier']['tmp_name']) == IMAGETYPE_GIF) or (exif_imagetype($_FILES['nom_du_fichier']['tmp_name']) == IMAGETYPE_JPEG) or (exif_imagetype($_FILES['nom_du_fichier']['tmp_name']) == IMAGETYPE_PNG)  or (exif_imagetype($_FILES['nom_du_fichier']['tmp_name']) == IMAGETYPE_BMP)  )
      {
        $size = getimagesize($_FILES['nom_du_fichier']['tmp_name']);
        if ( (intval($size[0]) < 30) or (intval($size[1]) < 30) or (intval($size[0]) > 150) or (intval($size[1]) > 150) )
          exit("Incorrect image size !");
        //
        move_uploaded_file($_FILES['nom_du_fichier']['tmp_name'], $dest . $_FILES['nom_du_fichier']['name']);
        header("location:avatar_changing.php?id_user_select=" . $id_user_select . "&username=" . $username . "&avatar=&lang=" . $lang . "&");
        break;
      }
      else
      {
        exit("This file is not a picture (gif, jpg, png, bmp) !");      
      }
    }
  }
}
?>