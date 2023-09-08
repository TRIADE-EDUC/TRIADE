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
if ( (!isset($_GET['ps'])) ) die();
//
$pass = base64_decode($_REQUEST['ps']);
//
if ($pass != "")
{
  if (_MAINTENANCE_MODE != '')
    die(">1#NotPublic#A###");
  //
  if (is_readable("../admin/index.php")) 
    die(">1#NotPublic#B###"); // configuration non terminée.
  //
  if ( ($pass == _IM_ADDRESS_BOOK_PASSWORD) and (_IM_ADDRESS_BOOK_PASSWORD != '') )
  {
    // and (_FORCE_UPDATE_BY_SERVER == "") and (_FORCE_UPDATE_BY_INTERNET !=  "")
    if ( (_PASSWORD_FOR_PRIVATE_SERVER == '') and (_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER != '') and (_IM_ADDRESS_BOOK_PASSWORD != '') 
          and (_ENTERPRISE_SERVER == "") and (_PENDING_USER_ON_COMPUTER_CHANGE == "") and (_HISTORY_MESSAGES_ON_ACP == "") )
    {
      require ("../common/constant.inc.php");
      require ("../common/sql.inc.php");
      //
      $requete = "select count(*) ";
      $requete .= "FROM " . $PREFIX_IM_TABLE . "SES_SESSION ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-100a]", $requete);
      list ($nb_ses) = mysqli_fetch_row ($result);
      //
      //
      $requete = "select count(*) ";
      $requete .= "FROM " . $PREFIX_IM_TABLE . "USR_USER ";
      //$requete .= "WHERE ( (USR_CHECK <> 'WAIT' and USR_CHECK <> '') or USR_STATUS = 1 )  ";
      $requete .= "WHERE USR_STATUS = 1 ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-100b]", $requete);
      list ($nb_user) = mysqli_fetch_row ($result);
      //
      mysqli_close($id_connect);
      //
      echo ">1#" . $nb_user . "#" . $nb_ses . "#" . _SPECIAL_MODE_OPEN_COMMUNITY . "#" . _MAX_NB_USER . "#" . _MAX_NB_SESSION . "#";
      echo _MAX_NB_CONTACT_BY_USER . "#" . _PENDING_NEW_AUTO_ADDED_USER . "#" . _LANG . "#" . _SERVER_VERSION .  "#";
      echo f_encode64(_EXTERNAL_AUTHENTICATION) . "#" . "#" . "#" . "#####";
    }
    else
      echo ">1#NotPublic#C###";
  }
}
?>