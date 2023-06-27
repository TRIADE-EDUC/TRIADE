<?php
  /**************************************************************************\
  * Phenix Agenda                                                            *
  * http://phenix.gapi.fr                                                    *
  * Written by    Stephane TEIL            <phenix-agenda@laposte.net>       *
  * Contributors  Christian AUDEON (Omega) <christian.audeon@gmail.com>      *
  *               Maxime CORMAU (MaxWho17) <maxwho17@free.fr>                *
  *               Mathieu RUE (Frognico)   <matt_rue@yahoo.fr>               *
  *               Bernard CHAIX (Berni69)  <ber123456@free.fr>               *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

// ----------------------------------------------------------------------------
// Parametres de l'application
// ----------------------------------------------------------------------------
   include_once("../../common/version.php");
  $APPLI_VERSION   = "5.51 - Triade ".VERSION ;
  $APPLI_LANGUE  = "fr"; // Langue par defaut de Phenix pour la page d'identification notamment

// ----------------------------------------------------------------------------
// Connexion a la base
// ----------------------------------------------------------------------------
  include_once("../../common/config.inc.php");
  include_once("../../librairie_php/lib_prefixe.php");
  $cfgHote=HOST; // Serveur MySQL
  $cfgUser=USER; // Utilisateur
  $cfgPass=PWD;  // Mot de passe
  $cfgBase=DB;   // Nom de la base
  $PREFIX_TABLE=$prefixe."px_"; 
  $CHEMIN_ABSOLU = false;
  if ($_GET['msg']!="6") {
    // Selon votre version de PHP, il vous faudra peut-etre indiquer ici le chemin absolu
    include("inc/db_class.inc.php");
  }

  define("_CONF_INC_LOADED",true);
?>
