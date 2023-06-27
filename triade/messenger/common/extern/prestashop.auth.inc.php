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

define("_EXTERNAL_AUTHENTICATION_NAME", "Prestashop");

function f_external_authentication($t_user, $t_pass)
{
  GLOBAL $id_connect;
  //
  $t_verif_pass = "Ko";
  $try_to_hack = "";
  //
  // login format is : name.firstname (nom.prenom)
  // login est au format nom.prenom : on le décompose
  $t = strpos($t_user, ".");
  if ( (strval($t) > 1) and (defined("_COOKIE_KEY_")) )
  {
    $nom = substr($t_user, 0, $t);
    $prenom = substr($t_user, $t+1, strlen($t_user) -$t);
    $nom = trim($nom);
    $prenom = trim($prenom);
    $nom = str_replace('_',' ',$nom);
    $prenom = str_replace('_',' ',$prenom);
    if ( ($nom != "") and ($prenom != "") )
    {
      $passcr = md5(_COOKIE_KEY_ . $t_pass);
      //
      require("../common/config/extern.config.inc.php");
      if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
      {
        // Si Prestashop n'est pas sur le même serveur ou la même base de donnée.
        mysqli_close($id_connect);
        require("extern.sql.inc.php");
        $id_connect = $id_connect_extern;
      }
      //
      // ---- Employee ----
      //
      $requete  = " select LOWER(lastname), LOWER(firstname), passwd FROM " . $extern_prefix . "employee ";
      $requete .= " WHERE LOWER(lastname) = '" . $t_user . "' ";
      $requete .= " and LOWER(firstname) = '" . $prenom . "' ";
      $requete .= " and active = 1 ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-T84a]", $requete);
      if ( mysqli_num_rows($result) == 1 )
      {
        list ($nom_extern, $prenom_extern, $pass_extern) = mysqli_fetch_row ($result);
        if ( ($nom_extern == $nom) and ($prenom_extern == $prenom) )
        {
          if ($pass_extern == $passcr) 
            $t_verif_pass = "OK";
          else
            $try_to_hack = "!!!";
        }
      }
      //
      if ($try_to_hack == "")
      {
        //
        // ---- Customers ----
        //
        $requete  = " select LOWER(lastname), LOWER(firstname), passwd FROM " . $extern_prefix . "customer ";
        $requete .= " WHERE LOWER(lastname) = '" . $t_user . "' ";
        $requete .= " and LOWER(firstname) = '" . $prenom . "' ";
        $requete .= " and active = 1 ";
        $requete .= " and deleted = 0 ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-T84b]", $requete);
        if ( mysqli_num_rows($result) > 0 )
        {
          while( list ($nom_extern, $prenom_extern, $pass_extern) = mysqli_fetchi_row ($result) )
          {
            if ( ($nom_extern == $nom) and ($prenom_extern == $prenom) and ($pass_extern == $passcr) )
              $t_verif_pass = "OK";
            //
          }
        }
      }
      //
      if ( ($extern_dbhost != '') and ($extern_database != '') and ($extern_dbuname != '') )
      {
        mysqli_close($id_connect_extern);
        require("sql.2.inc.php");
      }
    }
  }
  //
  return $t_verif_pass;
}
?>