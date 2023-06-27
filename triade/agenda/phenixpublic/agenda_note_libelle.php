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

  include("lang/$APPLI_LANGUE.php");

  $DB_CX->DbQuery("SELECT lib_detail FROM ${PREFIX_TABLE}libelle WHERE lib_id=".$id);
  if ($enr = $DB_CX->DbNextRow()) {
    $detail = str_replace(chr(13),"",str_replace(chr(10),"\\n",addslashes($enr['lib_detail'])));
    if ($AUTORISE_HTML && $AUTORISE_FCKE) {
      // Si l'editeur HTML est actif, les retours a la ligne doivent etre des <br>
      $detail = str_replace("\\n","<br>", $detail);
    }
  }

  // Fermeture BDD
  $DB_CX->DbDeconnect();
?>
<!DOCTYPE html public "-//w3c//dtd html 4.0 transitional//en">
<HTML>
<HEAD>
  <META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <TITLE><?php echo trad("NOTE_LIBELLE_TITRE");?></TITLE>
  <LINK rel="stylesheet" type="text/css" href="css/agenda_css.php?id=<?php echo $APPLI_STYLE; ?>">
  <SCRIPT language="JavaScript" type="text/javascript">
  <!--
    // Verifie le detail de la note
    var StkDetail = parent.window.frames['nav_<?php echo $sid; ?>'].document.Form1.ztDetail.value;
    // Recopie dans le detail de la note
    parent.window.frames['nav_<?php echo $sid; ?>'].document.Form1.ztDetail.value = StkDetail+"<?php echo $detail; ?>";
    parent.window.frames['nav_<?php echo $sid; ?>'].ToggleFck();
  </SCRIPT>
</HEAD>
<BODY></BODY>
</HTML>
