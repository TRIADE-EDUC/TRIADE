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
if (isset($_POST['id_file'])) $id_file = intval($_POST['id_file']);  else $id_file = 0;
if (isset($_POST['fil_comment'])) $fil_comment = $_POST['fil_comment']; else $fil_comment = "";
if (isset($_POST['page'])) $page = intval($_POST['page']);  else $page = 0;
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
if (isset($_POST['tri'])) $tri = $_POST['tri']; else $tri = "";
if (isset($_POST['source'])) $source = $_POST['source']; else $source = "";
if (isset($_POST['id_user_only'])) $id_user_only = intval($_POST['id_user_only']); else $id_user_only = "";
if (isset($_POST['id_media_only'])) $id_media_only = intval($_POST['id_media_only']); else $id_media_only = "";
if (isset($_POST['id_project_only'])) $id_project_only = intval($_POST['id_project_only']); else $id_project_only = "";
//
$fic_dest = "list_files_sharing";
if ($source == "share_files_pending") $fic_dest = "list_files_sharing_pending";
if ($source == "share_files_exch_pending") $fic_dest = "list_files_exchanging_pending";
if ($source == "share_files_alert") $fic_dest = "list_files_sharing_alert";
$url = $fic_dest . ".php?lang=" . $lang . "&page=" . $page . "&tri=" . $tri . "&id_user_only=" . $id_user_only . "&id_media_only=" . $id_media_only . "&id_project_only=" . $id_project_only . "&";
//
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ($id_file > 0) and (!preg_match("#[^0-9]#", $id_file)) )
  {
    define('INTRAMESSENGER',true);
    //
    require ("../common/sql.inc.php");
    //
    $fil_comment = trim($fil_comment);
    $fil_comment = str_replace("'", " ", $fil_comment);
    //
    $requete  = " update " . $PREFIX_IM_TABLE . "FIL_FILE ";
    $requete .= " set FIL_COMMENT = '" . $fil_comment . "' ";
    $requete .= " where ID_FILE = " . $id_file;
    $requete .= " limit 1 ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-N2e]", $requete);
    //
    mysqli_close($id_connect);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>