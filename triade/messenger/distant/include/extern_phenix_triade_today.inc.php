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
//
if ( (!isset($_GET['iu'])) or (!isset($_GET['ip'])) or (!isset($_GET['v'])) ) die();
//
$id_user =	  intval(f_decode64_wd($_GET['iu']));
$id_user = 		(intval($id_user) - intval($action));
$ip = 			  f_decode64_wd($_GET['ip']);
$version =	  intval($_GET['v']);
$dec =        intval($_GET['dec']); 
if (isset($_GET['s'])) $id_session = intval(f_decode64_wd($_GET['s'])); else $id_session = "";
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
//
//$dec = 1;  // pour forcer l'heure d'été (to force daylight saving).
//
if ( ($id_user > 0) and ($ip != "") )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  //
  require("../common/config/extern/triade.config.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die("KO#1#"); 
  //
  //
  $requete  = " select USR_TRIADE_PHENIX ";
  $requete .= " from " . $PREFIX_IM_TABLE . "USR_USER ";
  $requete .= " where ID_USER = " . $id_user;
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-91a]", $requete);
  if ( mysqli_num_rows($result) == 1 )
  list ($user_phenix_triade) = mysqli_fetch_row ($result);
  //
  //
  function DisplayTime($hr, $gmt, $dec) 
  {
    $hor = explode ('.', $hr);
    return date("H:i",mktime($hor[0] + strval($gmt) + strval($dec),($hor[1] / 100 * 60)%60,0,1,1,2000)); 
  }
  //
  //
  if ( (isset($phenix_include_in_triade)) and (isset($phenix_table_prefix)) )
  {
    if ($phenix_include_in_triade != "") $PREFIX_TABLE = $phenix_table_prefix;
  }
  //
  #if ( ($user_phenix_triade != "") and (_AUTHENTICATION_ON_TRIADE != '') )
  if ( ($user_phenix_triade != "") and (_EXTERNAL_AUTHENTICATION == "triade") )
  {
    $idUserPhenix = 0;
    if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
    {
      // Si phenix n'est pas sur le même serveur ou la même base de donnée.
      mysqli_close($id_connect);
      require("../common/extern/extern.sql.inc.php");
      $id_connect = $id_connect_extern;
    }
    $requete  = " select util_id, util_login, tzn_gmt FROM " . $PREFIX_TABLE . "utilisateur , " . $PREFIX_TABLE . "timezone ";
    $requete .= " WHERE util_timezone = tzn_zone ";
    $requete .= " and LOWER(util_login) = '" . $user_phenix_triade . "' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-91b]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($idUserPhenix, $login_phenix, $gmt_user) = mysqli_fetch_row ($result);
      if ($login_phenix != $user_phenix_triade) $idUserPhenix = 0;
    }
    //
    if (intval($idUserPhenix) > 0)
    {
      $requete  = " SELECT age_id, age_heure_debut, age_heure_fin, age_libelle, age_lieu, age_rappel, age_prive "; // age_detail, age_date_creation, age_ape_id, age_util_id, age_mere_id, age_createur_id,
      $requete .= " FROM " . $PREFIX_TABLE . "agenda, " . $PREFIX_TABLE . "agenda_concerne, " . $PREFIX_TABLE . "utilisateur ";
      $requete .= " WHERE age_id = aco_age_id AND aco_util_id = " . $idUserPhenix   ; 
      //$requete .= " and age_date = '" . date("Ymd") . "' ";
      $requete .= " and age_date = '" . date("Y-m-d") . "' ";
      $requete .= " AND util_id = age_createur_id ";
      $requete .= " AND age_rappel > 0 ";
      $requete .= " AND aco_termine = 0 ";
      $requete .= " ORDER BY age_heure_debut ASC";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) 
        error_sql_log("[ERR-91c]", $requete);
      else
      {
        if ( mysqli_num_rows($result) > 0 )
        {
          while( list ($age_id, $age_heure_debut, $age_heure_fin, $age_libelle, $age_lieu, $age_rappel, $age_prive) = mysqli_fetch_row ($result) )
          {
            $age_heure_debut = DisplayTime($age_heure_debut, $gmt_user, $dec);
            $age_heure_fin = DisplayTime($age_heure_fin, $gmt_user, $dec);
            //
            echo ">F70#" . $age_id . "#" . $age_heure_debut . "#" . $age_heure_fin . "#" . addslashes($age_libelle) . "#" . addslashes($age_lieu) . "#" . $age_rappel . "#" . $age_prive . "#" . "<BR>" . "#|"; // séparateur de ligne : '|' (pipe).
          }
        }
        else
          echo("KO#7#");
      }
    }
    else
      die("KO#5#"); // user nom trouvé
  }
  else
    echo("KO#6#"); // user nom trouvé
  //
  mysqli_close($id_connect);
}
?>