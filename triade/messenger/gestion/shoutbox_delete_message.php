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
if (isset($_POST['id_shout'])) $id_shout = intval($_POST['id_shout']);  else $id_shout = 0;
if (isset($_POST['id_grp'])) $id_grp = intval($_POST['id_grp']);  else $id_grp = 0;
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
//
$url = "list_shoutbox.php?lang=" . $lang . "&id_grp=" . $id_grp . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ($id_shout > 0) and (!preg_match("#[^0-9]#", $id_shout)) )
  {
    define('INTRAMESSENGER',true);
    //
    require ("../common/sql.inc.php");
    require ("../common/shoutbox.inc.php");
    require ("../common/functions.inc.php");
    //
    // auteur du message
    $requete  = " select ID_USER_AUT, SBX_TEXT ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
    $requete .= " WHERE ID_SHOUT = " . $id_shout;
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-V2a]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($id_aut, $txt) = mysqli_fetch_row ($result);
      //
      $requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE ";
      $requete .= " WHERE ID_SHOUT = " . $id_shout;
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-V2c]", $requete);
      //
      $requete  = " DELETE FROM " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
      $requete .= " WHERE ID_SHOUT = " . $id_shout;
      $requete .= " LIMIT 1 "; // (to protect)
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-V2b]", $requete);
      //
      stats_sbx_add_reject_msg($id_aut);
      //
      $user = f_get_username_of_id($id_aut);
      $txt = f_decode64_wd($txt);
      write_log("shoutbox_delete_message", $user . ";" . $txt);
    }
    //
    mysqli_close($id_connect);
  }
  //
  header("location:" . $url);
}
else
  require("redirect_acp_demo.inc.php");
?>