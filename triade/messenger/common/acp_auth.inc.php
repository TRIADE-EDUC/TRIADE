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


function random($nb_char) 
{
  $ret = "";
  $user_ramdom_key = "(aLABbC0cEd1[eDf2FghR3ij4kYXQl5Um-OPn6pVq7rJs8*tuW9I+vGw@xHTy&#)K]Z%§!M_S";
  srand((double)microtime()*time());
  for($i = 0; $i < $nb_char; $i++) 
  {
    $ret .= $user_ramdom_key[rand()%strlen($user_ramdom_key)];
  }
  return $ret;
} 


function chiffrer_pass($pass, $salt) 
{
  $pass_cr = sha1(sha1($pass . md5($salt)));
  //
  return $pass_cr;
} 


function f_acp_check_login($acp_login, $acp_pass)
{
  global $PREFIX_IM_TABLE, $id_connect;
  //
  //$retour = 'KO'; // par défaut
  $retour = 0; // par défaut
  //
  //if ( ($acp_login == "t") and ($acp_pass != "") ) $retour = "OK";
	//$acp_login = str_replace("'", "", $acp_login);
	$acp_login = f_clean_username($acp_login);
	if ($acp_login != '')
	{
    $requete  = " select ID_ADMIN, ADM_PASSWORD, ADM_SALT, ADM_LEVEL ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "ADM_ADMINACP ";
    $requete .= " WHERE ADM_USERNAME = '" . $acp_login . "' ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-P1d]", $requete);
    if ( mysqli_num_rows($result) == 1 )
    {
      list ($t_id, $t_pass, $t_salt, $admn_level) = mysqli_fetch_row ($result);
      if ($t_pass == sha1(sha1($acp_pass . md5($t_salt)))) 
      {
        $retour = $t_id; // "OK";
        //
        $requete  = " UPDATE " . $PREFIX_IM_TABLE . "ADM_ADMINACP ";
        $requete .= " SET ADM_DATE_LAST = CURDATE() ";
        $requete .= " WHERE ID_ADMIN = " . $t_id . " ";
        $requete .= " limit 1 ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-P1e]", $requete);
      }
      else
        $retour = -1;
    }
  }
  //
  return $retour;
}

?>