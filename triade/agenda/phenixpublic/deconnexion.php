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

  // Destruction des informations sur la session
  @session_start();
  @session_destroy();

  require("inc/nocache.inc.php");

  if (isset($_GET['sid']) || isset($HTTP_GET_VARS['sid'])) {
    // Destruction de la session
    include("inc/param.inc.php");
    $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}sid WHERE sid_id='".$sid."'");
    // Fermeture BDD
    $DB_CX->DbDeconnect();
    // Destruction du cookie
    setcookie($COOKIE_NOM,'',time()-31536000,"/","",0);
  }

  // Deconnexion geree par l'appli
  if (isset($_GET['msg']))
    $msg = $_GET['msg'];
  elseif (isset($HTTP_GET_VARS['msg']))
    $msg = $HTTP_GET_VARS['msg'];
  else
    $msg = 5;
?>
<HTML>
<HEAD><LINK rel="stylesheet" type="text/css" href="css/agenda_css.php"></HEAD>
<BODY onload="javascript: window.parent.location.href='index.php?msg=<?php echo $msg ?>';"></BODY>
</HTML>
