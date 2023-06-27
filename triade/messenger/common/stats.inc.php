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

if ( !defined('INTRAMESSENGER') )
{
  exit;
}

// Si nouvelle date (donc à l'ajout de nouvelle ligne), on stocke le nombre d'utilisateurs à la dernière date (erreur de 1 max).
function stats_nb_user_last_date()
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $requete  = " SELECT max(STA_DATE) FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-M2a]", $requete);
  if ( mysqli_num_rows($result) == 1 ) 
  {
    list($dt) = mysqli_fetch_row ($result);
    if ($dt != "")
    {
      $dt = date("Ymd", strtotime($dt));
      //
      $requete  = " SELECT count(*) FROM " . $PREFIX_IM_TABLE . "USR_USER ";
      $requete .= " where USR_DATE_LAST = '" . $dt . "' ";
      $requete .= " or USR_DATE_LAST = CURDATE() "; // pour ceux déjà passés au lendemain (ajouté le 17/10/10).
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-M2b]", $requete);
      list($nb) = mysqli_fetch_row ($result);
      //
      if ( intval($nb) > 0 and ($dt != '') )
      {
        $requete  = " UPDATE " . $PREFIX_IM_TABLE . "STA_STATS ";
        $requete .= " SET STA_NB_USR = " . $nb;
        $requete .= " WHERE STA_DATE = '" . $dt . "' ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-M2c]", $requete);
      }
    }
  }
}


function stats_inc($type) // +1
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $champs = "";
  if ($type == "STA_NB_MSG") $champs = "STA_NB_MSG";
  if ($type == "STA_NB_CREAT") $champs = "STA_NB_CREAT";
  //if ($type == "STA_NB_SESSION") $champs = "STA_NB_SESSION";
  //if ($type == "") $champs = "";
  if ($champs != '')
  {
    $requete  = " SELECT " . $champs . " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
    $requete .= " WHERE STA_DATE = CURDATE() ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M2d]", $requete);
    $nb = 0;
    if ( mysqli_num_rows($result) == 1 ) 
    {
      list($nb) = mysqli_fetch_row ($result);
      $nb++;
      if (intval($nb) <= 0) $nb = 1;
      $requete  = " UPDATE " . $PREFIX_IM_TABLE . "STA_STATS ";
      $requete .= " SET " . $champs . " = " . $nb;
      $requete .= " WHERE STA_DATE = CURDATE() ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-M2e]", $requete);
    }
    else
    {
      // Si nouvelle date, on stocke le nombre d'utilisateurs à la dernière date.
      //
      stats_nb_user_last_date();
      //
      //
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "STA_STATS (STA_DATE, " . $champs . " ) ";
      $requete .= " VALUES ( CURDATE() , 1 ) " ;
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-M2f]", $requete);
    }
	}
}



function stats_max($type, $max) // >= max
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  $max = intval($max);
  $champs = "";
  //if ($type == "STA_NB_MSG") $champs = "STA_NB_MSG";
  //if ($type == "STA_NB_CREAT") $champs = "STA_NB_CREAT";
  if ($type == "STA_NB_SESSION") $champs = "STA_NB_SESSION";
  //if ($type == "") $champs = "";
  if ( ($champs != '') and ($max > 0) )
  {
    $requete  = " SELECT " . $champs . " FROM " . $PREFIX_IM_TABLE . "STA_STATS ";
    $requete .= " WHERE STA_DATE = CURDATE() ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-M2g]", $requete);
    if ( mysqli_num_rows($result) == 1 ) 
    {
      list($nb) = mysqli_fetch_row ($result);
      if ($max > $nb)
      {
        $requete  = " UPDATE " . $PREFIX_IM_TABLE . "STA_STATS ";
        $requete .= " SET " . $champs . " = " . $max;
        $requete .= " WHERE STA_DATE = CURDATE() ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-M2h]", $requete);
       }
    }
    else
    {
      // Si nouvelle date, on stocke le nombre d'utilisateurs à la dernière date.
      //
      stats_nb_user_last_date();
      //
      //
      //
      $requete  = " INSERT INTO " . $PREFIX_IM_TABLE . "STA_STATS (STA_DATE, " . $champs . " ) ";
      $requete .= " VALUES ( CURDATE() , " . $max . " ) " ;
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-M2i]", $requete);
    }
	}
}

?>