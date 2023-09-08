<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_RSS_Reader.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5.5_Emplacement_Plus.txt ?>
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

//Gestion des rapports d'erreurs

  error_reporting(E_ALL ^ E_NOTICE);

  if (!isset($_SERVER) && isset($HTTP_SERVER_VARS)) {
    $_POST   = $HTTP_POST_VARS;
    $_GET    = $HTTP_GET_VARS;
    $_FILES  = $HTTP_POST_FILES;
    $_SERVER = $HTTP_SERVER_VARS;
  }

  include ("conf.inc.php");

  if ($_GET['msg']!="6") {
    $DB_CX->DbQuery("SELECT param, valeur FROM ${PREFIX_TABLE}configuration WHERE groupe=0");
    while ($enr = $DB_CX->DbNextRow()) {
      if ($enr['valeur']=="OUI") {
        ${$enr['param']} = true;
      } elseif ($enr['valeur']=="NON") {
        ${$enr['param']} = false;
      } else {
        ${$enr['param']} = $enr['valeur'];
      }
    }
  }

  function pxExtract($array, &$target) {
    if (!is_array($array)) {
      return false;
    }
    $is_magic_quotes = get_magic_quotes_gpc();
    reset($array);
    while (list($key, $value) = each($array)) {
      if (is_array($value)) {
        pxExtract($value, $target[$key]);
      } else if (!$is_magic_quotes) {
        $target[$key] = addslashes(trim(stripTags($value)));
      } else {
        $target[$key] = trim(stripTags($value));
      }
    }
    reset($array);
    return true;
  }

// ----------------------------------------------------------------------------
//Recuperation des donnees de formulaires
// ----------------------------------------------------------------------------
  if (!empty($_GET)) {
    pxExtract($_GET, $GLOBALS);
  }

  if (!empty($_POST)) {
    pxExtract($_POST, $GLOBALS);
  }

  if (!empty($_FILES)) {
    while (list($name, $value) = each($_FILES)) {
      $$name = $value['tmp_name'];
      ${$name . '_name'} = $value['name'];
    }
  }

// ----------------------------------------------------------------------------
//Autorise ou non les balises HTML
// ----------------------------------------------------------------------------
  function stripTags($str) {
    global $AUTORISE_HTML;
    return ($AUTORISE_HTML) ? $str : strip_tags($str);
  }

// ----------------------------------------------------------------------------
//Gestion des erreurs de la BDD
// ----------------------------------------------------------------------------
  function serveurDown() {
    echo("<HTML>
  <HEAD><link rel=\"stylesheet\" type=\"text/css\" href=\"css/agenda_css.php\"></HEAD>
  <BODY onload=\"javascript:window.location.href='index.php?msg=6';\">
  </BODY>
</HTML>");
    exit;
  }

// ----------------------------------------------------------------------------
//Constantes pour la gestion des droits et les menus
// ----------------------------------------------------------------------------
// profil
  define("_DROIT_PROFIL_RIEN",0);                 // Aucun Acces
  define("_DROIT_PROFIL_PARAM_BASE",10);          // Acces parametres de base
  define("_DROIT_PROFIL_PARAM_PARTAGE",20);       // Acces parametres de base et partages
  define("_DROIT_PROFIL_AUTRE_PARAM_BASE",30);    // Acces autres agendas parametres de base
  define("_DROIT_PROFIL_AUTRE_PARAM_PARTAGE",40); // Acces autres agendas parametres de base et partages
  define("_DROIT_PROFIL_COMPLET",50);             // Acces complet aux autres agendas
// agenda
  define("_DROIT_AGENDA_SEUL",0);                 // Acces a son propre agenda
  define("_DROIT_AGENDA_PARTAGE",10);             // Acces standard
  define("_DROIT_AGENDA_TOUS",20);                // Acces a tous
// note
  define("_DROIT_NOTE_CONSULT_SEUL",0);           // Acces en consultation uniquement
  define("_DROIT_NOTE_CONSULT_RECHERCHE",5);      // Acces en consultation avec recherche
  define("_DROIT_NOTE_STANDARD_SANS_APPR",10);    // Acces standard sans appropriation
  define("_DROIT_NOTE_STANDARD",15);              // Acces standard
  define("_DROIT_NOTE_MODIF_STATUT",20);          // Acces standard avec modification du statut des notes
  define("_DROIT_NOTE_MODIF_CREATION",30);        // Acces en modification et creation (sans suppression)
  define("_DROIT_NOTE_COMPLET",40);               // Acces complet
// menu
  define("_MENU_PLG_QUOT",0);                     // Menu planning quotidien
  define("_MENU_PLG_HEBDO",1);                    // Menu planning hebdomadaire
  define("_MENU_PLG_MENSUEL",2);                  // Menu planning mensuel
  define("_MENU_PLG_ANNUEL",3);                   // Menu planning annuel
  define("_MENU_PLG_MENS_GBL",4);                 // Menu planning mensuel global
  define("_MENU_PLG_HEBDO_GBL",5);                // Menu planning hebdomadaire global
  define("_MENU_PLG_QUOT_GBL",6);                 // Menu planning quotidien global
  define("_MENU_DISP_HEBDO",8);                   // Menu disponibilite hebdomadaire
  define("_MENU_DISP_QUOT",9);                    // Menu disponibilite quotidien
  define("_MENU_RECHERCHE",10);                   // Menu recherche
  define("_MENU_CONTACT",11);                     // Menu contact
  define("_MENU_PROFIL",13);                      // Menu profil
  define("_MENU_NOTE_IMPORT",16);                 // Menu import note
  define("_MENU_NOTE_EXPORT",17);                 // Menu export note
  define("_MENU_ADMIN",20);                       // Menu administration
// type
  define("_TYPE_ANNIV",1);                        // Gestion anniversaire
  define("_TYPE_NOTE",2);                         // Gestion note
  define("_TYPE_CONTACT",3);                      // Gestion contact
  define("_TYPE_IMPORT_CONTACT",4);               // Import contacts
  define("_TYPE_EVENEMENT",5);                    // Gestion evenement
  define("_TYPE_MEMO",8);                         // Gestion memo
  define("_TYPE_LIBELLE",9);                      // Gestion libelle
  define("_TYPE_FAVORIS",10);                     // Gestion favoris
  // Mod RSS Reader
  define("_TYPE_RSS_READER",101);                 // Gestion RSS Reader
  // Fin mod
  //Mod Emplacement Plus
  define("_TYPE_EMPL",11);                         // Gestion emplacement
  //Fin Mod Emplacement Plus
// gestion des rappels
  define('_RAPPEL_NOTE', 1);
  define('_RAPPEL_ANNIV', 2);
  define('_RAPPEL_ANNIV_CONTACT', 3);
?>
