<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2012 THeUDS           **
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

define("_EXTERNAL_AUTHENTICATION_NAME", "LDAP");

function f_external_authentication($t_user, $t_pass)
{
  $t_verif_pass = "Ko";
  //
  require("../common/config/ldap.config.inc.php");
  //
  //if (extension_loaded('ldap'))
  if ( ($ldap_host != '') and ($ldap_basedn != '') and ($ldap_searchbasedn != '') and ($ldap_login  != '') and ($ldap_password !='') )  
  {
    $ldapconn = ldap_connect($ldap_host, $ldap_port);
    if ($ldapconn)    
    {
      // options pour rechercher d'en haut de l'arbre
      ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);  //Alors ca c est pour les ADs
      ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);   
      //
      // vu que je suis connecté, je bind avec mon user dedicated pour IntraMessenger (c'est IntraMessenger qui s'authentifie)
      $ldapbind = ldap_bind($ldapconn, "cn=" . $ldap_login . ", " . $ldap_basedn, $ldap_password);
      if ($ldapbind)  
      {
        // IM est authentifié, donc je cherche le user
        $sr = ldap_search($ldapconn,$ldap_searchbasedn , "samaccountname=" . $t_user);
        $info = ldap_get_entries($ldapconn, $sr);
        if ($info["count"] != 0) 
        {
          //on a trouvé le user alors testons si son pass est correct
          $ldapbind_user = ldap_bind($ldapconn,$info[0]["dn"],$t_pass);
          if ($ldapbind_user) 
          {
             //arrivé ici ça fonctionne, c'est donc le bon mot de passe
            $t_verif_pass = "OK";
          }
        }
      }
      else    
      {
        die("Could not connect to LDAP server");   // Connexion au serveur LDAP impossible
      }
      ldap_close($ldapconn);
    }
    else    
    {
       die("Could not connect to LDAP server");   // Connexion au serveur LDAP impossible
    }
  }
  //
  return $t_verif_pass;
}

/*
function f_external_authentication($t_user, $t_pass)
{
  $t_verif_pass = "Ko";
  //
  if (extension_loaded('ldap'))
  {
    require("../common/config/ldap.config.inc.php");
    if ( ($ldap_host != '') and ($ldap_basedn != '') )
    {
      if (intval($ldap_port > 0))
        $ldapconn = ldap_connect($ldap_host, $ldap_port);
      else
        $ldapconn = ldap_connect($ldap_host);
      //
      if ($ldapconn) 
      {
        $ldapbind = ldap_bind($ldapconn, "cn=" . $t_user . ", " . $ldap_basedn, $t_pass);
        if ($ldapbind) $t_verif_pass = "OK";
        ldap_close($ldapconn);
      }
      else
        die("Could not connect to LDAP server");   // Connexion au serveur LDAP impossible
    }
  }
  //
  return $t_verif_pass;
}
*/
?>