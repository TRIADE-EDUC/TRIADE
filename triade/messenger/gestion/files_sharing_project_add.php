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
if (isset($_POST['fpj_name'])) $fpj_name = trim($_POST['fpj_name']);  else $fpj_name = "";
if (isset($_POST['tri'])) $tri = $_POST['tri'];  else $tri = "";
if (isset($_POST['page'])) $page = $_POST['page']; else $page = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
//
$url = "list_files_projects.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&new_added=x&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ($fpj_name != "")
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    require ("../common/config/config.inc.php"); // important
    require ("../common/functions.inc.php");
    require ("../common/share_files.inc.php");
    //
    $fpj_name = f_clean_name($fpj_name);
    $folder =   f_clean_username($fpj_name);
    $folder =   f_DelSpecialChar($folder);
    $folder =   substr($folder, 0, 20);
    //
    if ($folder != "trash")
    {
      $requete  = " SELECT ID_PROJET, FPJ_NAME ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "FPJ_FILEPROJET ";
      $requete .= " WHERE FPJ_NAME like '" . $fpj_name . "' or FPJ_FOLDER like '" . $folder . "' ";
      $requete .= " limit 2 ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-N2a]", $requete);
      $nb_row = mysqli_num_rows($result);
      if ($nb_row <= 0)
      {
        $requete  = " insert into " . $PREFIX_IM_TABLE . "FPJ_FILEPROJET ";
        $requete .= " (FPJ_NAME, FPJ_FOLDER, FPJ_DATE_CREAT) ";
        $requete .= " values ('" . $fpj_name . "', '" . $folder . "', CURDATE())";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-N2b]", $requete);
        //
        //
        write_log("log_project_create", $fpj_name . ";" . $folder);
        //
        //
        sf_ftp_create_folder($folder);
      }
    }
    mysqli_close($id_connect);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>