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

  require("inc/nocache.inc.php");
  if (isset($_GET['sid']) || isset($HTTP_GET_VARS['sid'])) {
    include("inc/param.inc.php");
    include("inc/fonctions.inc.php");
    include("inc/html.inc.php");
  } else {
    include("inc/interdit.html");
    exit;
  }

  $idUser = Session_ok($sid);

  if ($idUser == -1) {
    include("inc/interdit.html");
    exit;
  }

  $Screen+=0;
  $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}sid SET sid_screen=".$Screen." WHERE sid_id='".$sid."'");
  if ($COOKIE_AUTH) { // MAJ du cookie d'identification
    if (!empty($_COOKIE) && isset($_COOKIE[$COOKIE_NOM]))
      $tabLog = explode(":",$_COOKIE[$COOKIE_NOM]);
    elseif (!empty($HTTP_COOKIE_VARS) && isset($HTTP_COOKIE_VARS[$COOKIE_NOM]))
      $tabLog = explode(":",$HTTP_COOKIE_VARS[$COOKIE_NOM]);
    if (@count($tabLog)>0) {
      setcookie($COOKIE_NOM, $tabLog[0].":".$tabLog[1].":".$tabLog[2].":".$Screen, time()+86400*$COOKIE_DUREE, "/", "", 0);
    }
  }
  // Fermeture BDD
  $DB_CX->DbDeconnect();
?>
<!DOCTYPE html public "-//w3c//dtd html 4.0 transitional//en">
<HTML>
<HEAD>
  <META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <TITLE></TITLE>
</HEAD>
<BODY>
  <SCRIPT language="JavaScript" type="text/javascript">
  <!--
    parent.window.frames['nav_<?php echo $sid; ?>'].document.location.reload();
  </SCRIPT>
</BODY>
</HTML>
