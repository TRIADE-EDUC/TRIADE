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
if (isset($_GET['id_projet'])) $id_projet = intval($_GET['id_projet']);  else $id_projet = 0;
if (isset($_GET['status'])) $status = $_GET['status'];  else $status = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
//
$url = "list_files_projects.php?lang=" . $lang . "&";
//
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ($id_projet > 0) and (!preg_match("#[^0-9]#", $id_projet)) and ( ($status == 'open') or ($status == 'close') ) )
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    //require ("../common/functions.inc.php");
    //
    $requete  = " update " . $PREFIX_IM_TABLE . "FPJ_FILEPROJET ";
    if ($status == 'open')
      $requete .= " set FPJ_DATE_CLOSE = '0000-00-00', FPJ_DATE_CREAT = CURDATE() ";
    else
      $requete .= " set FPJ_DATE_CLOSE = CURDATE() ";
    //
    $requete .= " where ID_PROJET = " . $id_projet;
    $requete .= " LIMIT 1 "; // (to protect)
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-N2g]", $requete);
    //
    mysqli_close($id_connect);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>