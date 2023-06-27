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

// On regarde si les fichiers de config sont deja charges
if (!defined("_CONF_INC_LOADED")) {
  include("inc/param.inc.php");
  include("inc/fonctions.inc.php");
  include("inc/html.inc.php");
  include("lang/$APPLI_LANGUE.php");
}

// Pre-controle sur l'ID fourni
if (!isset($id) || empty($id) || strlen($id)!=32) {
  header('HTTP/1.1 403 Forbidden');
  exit;
}

// On recupere l'identifiant et le mot de passe de l'utilisateur identifie par l'ID passe en parametre dans l'URL
$DB_CX->DbQuery("SELECT util_login, util_passwd FROM ${PREFIX_TABLE}utilisateur WHERE util_url_export='".$id."'");
$login_util = $DB_CX->DbResult(0,0);
$passwd_util = $DB_CX->DbResult(0,1);
// Si l'identifiant ou le mot de passe est vierge on refuse la synchro
if (empty($login_util) || empty($passwd_util)) {
  header('HTTP/1.1 403 Forbidden');
  exit;
}

// On recupere les variables serveur
$TYPE_REQUETE = basename($_SERVER['REQUEST_METHOD']);
$SET_HTACCES = isset($_SERVER['REMOTE_USER']);
$SET_IIS = isset($_SERVER['HTTP_AUTHORIZATION']);
$IIS_AUTH = $_SERVER['HTTP_AUTHORIZATION'];
$SET_LOGIN = isset($_SERVER['PHP_AUTH_USER']);
$LOGIN_USER = $_SERVER['PHP_AUTH_USER'];
$LOGIN_PW = $_SERVER['PHP_AUTH_PW'];

// On verifie si l'acces au script est protege, sinon on demande l'authentification
if (!$SET_HTACCES) {
  // Si le serveur est IIS, on recupere l'authentification
  if ($SET_IIS)
    list($LOGIN_USER, $LOGIN_PW) = explode(':', base64_decode(substr($IIS_AUTH, 6)));
  // Si l'authentification est annulee ou est incorrecte
  if (!$SET_LOGIN || $LOGIN_USER != $login_util || md5($LOGIN_PW) != $passwd_util) {
    header('WWW-Authenticate: Basic realm="Phenix-synchronisation"');
    header('HTTP/1.1 401 Unauthorized');
    exit;
  }
}

// On aiguille en fonction du type de requete
switch ($TYPE_REQUETE) {
  // Lecture de l'agenda
  case "GET":
    $zlTypeFichier = "icsURL";
    header('HTTP/1.1 200 OK');
    include("agenda_note_export.php");
    break;
  // Ecriture de l'agenda
  case "PUT":
    $ImportSunbird="OK";
    include("agenda_note_import.php");
    break;
}
?>
