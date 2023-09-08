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
if ( (!isset($_GET['u'])) or (!isset($_GET['id'])) or (!isset($_GET['at'])) or (!isset($_GET['ip'])) ) die();
//
$id_user =	  intval(f_decode64_wd($_GET['u']));
$id_user = 		(intval($id_user) - intval($action));
$ip = 			  f_decode64_wd($_GET['ip']);
$version =    intval($_GET['v']);
$id_conf = 		intval($_GET['id']);
$accord = 		$_GET['at'];
if (isset($_GET['s'])) $id_session = intval(f_decode64_wd($_GET['s'])); else $id_session = "";
if (isset($_GET['sc'])) $session_chk = f_decode64_wd($_GET['sc']); else $session_chk = "";
//
if (preg_match("#[^0-9]#", $id_user)) $id_user = "";
if (preg_match("#[^0-9]#", $id_conf)) $id_conf = "";
//
if ( ($id_user > 0) and ($id_conf > 0) )
{
  require ("../common/acces.inc.php");
  f_verif_ip($ip);
  //
  require ("../common/sql.inc.php");
  require ("../common/sessions.inc.php");
  //
  if (f_check_session_id_user($id_user, $session_chk, $action) != 'OK')  die(">F51#KO#1#"); // 1:session non ouverte.
  //
  //
  $t_allow_conference = _ALLOW_CONFERENCE;
  if (_ROLES_TO_OVERRIDE_PERMISSIONS != "")
  {
    require ("../common/roles.inc.php");
    $id_role = f_role_of_user($id_user);
    //
    if ($id_role > 0)
    {
      $t_allow_conference = f_role_permission($id_role, "ALLOW_CONFERENCE", _ALLOW_CONFERENCE);
    }
  }
  //
  if ($t_allow_conference  == '')
    die(">F51#KO#5#"); // 5:non autorisé
  //
  if (intval($id_conf) > 0)
  {
    $requete  = " select ID_CONFERENCE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "CNF_CONFERENCE ";
    $requete .= " WHERE ID_CONFERENCE = " . $id_conf . " ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-61a]", $requete);
    if ( mysqli_num_rows($result) == 1 ) 
    {
      // On supprime la présence dans d'éventuelles autres conférences
      $requete  = " delete from " . $PREFIX_IM_TABLE . "USC_USERCONF ";
      $requete .= " WHERE ID_CONFERENCE <> " . $id_conf;
      $requete .= " AND ID_USER = " . $id_user . " ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-61b]", $requete);
      //
      $requete  = " select CNF.ID_CONFERENCE ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "CNF_CONFERENCE CNF, " . $PREFIX_IM_TABLE . "USC_USERCONF USC ";
      $requete .= " WHERE CNF.ID_CONFERENCE = USC.ID_CONFERENCE ";
      $requete .= " AND USC.ID_USER = " . $id_user . " ";
      //$requete .= " AND USC_ACTIVE = 0 "; // en attente de validation
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-61c]", $requete);
      if ( mysqli_num_rows($result) == 1 ) // normalement pas plus...
      {
        //
        if ($accord == "NO") // rejet
        {
          $requete  = " delete from " . $PREFIX_IM_TABLE . "USC_USERCONF ";
          $requete .= " WHERE ID_CONFERENCE = " . $id_conf;
          $requete .= " AND ID_USER = " . $id_user . " ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-61d]", $requete);
        }
        else
        {
          $requete  = " update " . $PREFIX_IM_TABLE . "USC_USERCONF ";
          $requete .= " SET USC_ACTIVE = 1 "; // VALIDE 
          $requete .= " WHERE ID_CONFERENCE = " . $id_conf;
          $requete .= " AND ID_USER = " . $id_user . " ";
          $requete .= " AND USC_ACTIVE = 0 "; // en attente de validation
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-61e]", $requete);
        }
        //
        echo ">F51#OK####"; // conférence bien acceptée/refusée.
      }
      else
      {
        $requete  = "INSERT INTO " . $PREFIX_IM_TABLE . "USC_USERCONF (ID_CONFERENCE, ID_USER, USC_ACTIVE) ";
        $requete .= "VALUES (" . $id_conf . ", " . $id_user . " , 1 ) ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-61f]", $requete);
        //
        echo ">F51#OK####"; // conférence bien acceptée.
      }
    }
    else
      echo ">F51#KO######"; // conférence bien créée
    //
  }
  else
    die(">F51#KO####");
  //
  mysqli_close($id_connect);
}
?>