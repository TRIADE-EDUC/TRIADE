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
if (isset($_POST['id_gp'])) $id_gp = intval($_POST['id_gp']);  else $id_gp = 0;
if (isset($_POST['tri'])) $tri = $_POST['tri'];  else $tri = "";
if (isset($_POST['lang'])) $lang = $_POST['lang']; else $lang = "";
//
//
$url = "list_group.php?tri=" . $tri . "&lang=" . $lang . "&";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") == 0) and (substr_count($repertoire, "\admin_demo/") == 0) ) 
{
  if ( ($id_gp > 0) and (!preg_match("#[^0-9]#", $id_gp)) )
  {
    define('INTRAMESSENGER',true);
    require ("../common/sql.inc.php");
    //
    // Si plus personne (existant encore) dans le groupe :
    $requete  = " SELECT count(*) ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "GRP_GROUP GRP, " . $PREFIX_IM_TABLE . "USG_USERGRP USG, " . $PREFIX_IM_TABLE . "USR_USER USR ";
    $requete .= " WHERE GRP.ID_GROUP = USG.ID_GROUP ";
    $requete .= " AND USG.ID_USER = USR.ID_USER ";
    $requete .= " AND GRP.ID_GROUP = " . $id_gp;
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-D5a]", $requete);
    list ($nb_user) = mysqli_fetch_row ($result);
    if (intval($nb_user) == 0)
    {
      // on supprime les messages de la shoutbox envoyés à ce groupe :
      $requete  = " delete from " . $PREFIX_IM_TABLE . "SBX_SHOUTBOX ";
      $requete .= " where ID_GROUP_DEST = " . $id_gp;
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-D5c]", $requete);
      //
      // on supprime le groupe :
      $requete  = " delete from " . $PREFIX_IM_TABLE . "GRP_GROUP ";
      $requete .= " where ID_GROUP = " . $id_gp;
      $requete .= " LIMIT 1 "; // (to protect)
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-D5b]", $requete);
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