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
if (isset($_POST['id_projet'])) $id_projet = intval($_POST['id_projet']);  else $id_projet = 0;
if (isset($_POST['fpj_name'])) $fpj_name = trim($_POST['fpj_name']);  else $fpj_name = "";
if (isset($_POST['tri'])) $tri = $_POST['tri'];  else $tri = "";
if (isset($_POST['page'])) $page = $_POST['page']; else $page = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
//
$url = "list_files_projects.php?tri=" . $tri . "&lang=" . $lang . "&page=" . $page . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ($id_projet > 0)
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    require ("../common/functions.inc.php");
    //
    $fpj_name = f_clean_name($fpj_name);
    $nb_row = 0;
    if ($fpj_name != "")
    {
      $requete  = " SELECT ID_PROJET, FPJ_NAME ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "FPJ_FILEPROJET ";
      $requete .= " WHERE ID_PROJET <> " . $id_projet;
      $requete .= " and FPJ_NAME like '" . $fpj_name . "' ";
      $requete .= " limit 2 ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-N2c]", $requete);
      $nb_row = mysqli_num_rows($result);
    }
    if ($nb_row <= 0)
    {
      $requete  = " update " . $PREFIX_IM_TABLE . "FPJ_FILEPROJET ";
      $requete .= " set FPJ_NAME = '" . $fpj_name . "' ";
      $requete .= " where ID_PROJET = " . $id_projet;
      $requete .= " LIMIT 1 "; // (to protect)
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-N2d]", $requete);
      //
      write_log("log_project_update_name", $fpj_name . ";");
    }
    mysqli_close($id_connect);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>