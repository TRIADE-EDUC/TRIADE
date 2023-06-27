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
  require("inc/html.inc.php");
  if (isset($_GET['sid']) || isset($HTTP_GET_VARS['sid'])) {
    include("inc/param.inc.php");
    include("inc/fonctions.inc.php");
  } else {
    include("inc/interdit.html");
    exit;
  }

  $idUser = Session_ok($sid);

  if ($idUser == -1) {
    include("inc/interdit.html");
    exit;
  }

  include("skins/$APPLI_STYLE.php");
  include("lang/$APPLI_LANGUE.php");

  if ($zlGroupe != "0") {
    $DB_CX->DbQuery("SELECT fgr_nom FROM ${PREFIX_TABLE}favoris_groupe WHERE fgr_id=".$zlGroupe);
    $nomGroupe = $DB_CX->DbResult(0,0);
    $labelBouton = trad("FAVGRP_BT_MODIFIER");
  } else {
    $labelBouton = trad("FAVGRP_BT_ENREGISTRER");
  }
?>
<!DOCTYPE html public "-//w3c//dtd html 4.0 transitional//en">
<HTML>
<HEAD>
  <META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <TITLE><?php echo trad("FAVGRP_TITLE_GESTION");?></TITLE>
  <LINK rel="stylesheet" type="text/css" href="css/agenda_css.php?id=<?php echo $APPLI_STYLE; ?>">
  <SCRIPT language="JavaScript" type="text/javascript">
  <!--
    // Fonction trim javascript (suppression d'espaces avant et apres une chaine)
    function trim(chaine) {
      return chaine.replace(/^\s+/, "").replace(/\s+$/, "");
    }

    function restoreParam() {
      window.opener.document.forms['FormFavoris'].action="?sid=<?php echo $sid; ?>&tcMenu=<?php echo $tcMenu; ?>&tcPlg=<?php echo $tcPlg; ?>&sd=<?php echo $sd; ?>";
      window.opener.document.forms['FormFavoris'].target="_self";
    }

    function saisieOK(theForm) {
      if (trim(theForm.ztNomGroupe.value) == "") {
        window.alert("<?php echo trad("FAVGRP_JS_SAISIR_NOM");?>");
        theForm.ztNomGroupe.focus();
        return (false);
      }

      theForm.submit();
      self.close();
      return (true);
    }
  //-->
  </SCRIPT>
</HEAD>
<BODY onLoad="javascript: window.focus(); document.Form1.ztNomGroupe.focus();" onUnload="javascript: restoreParam();" topmargin=7 style='<?php echo $AgendaFavorisGroupesFondImage; ?>'>
  <FORM name="Form1" method="post" action="agenda.php?sid=<?php echo $sid; ?>&tcType=<?php echo _TYPE_FAVORIS; ?>&tcMenu=<?php echo $tcMenu; ?>&tcPlg=<?php echo $tcPlg; ?>&sd=<?php echo $sd; ?>" target="nav_<?php echo $sid; ?>">
    <CENTER>
      <TABLE border="0" cellpadding="2" cellspacing="1" bgcolor="<?php echo $AgendaFavorisGroupesBords; ?>" style="border-collapse:separate;">
      <TR>
        <TD colspan="2" align="center" class="MenuOff"><B><?php echo trad("FAVGRP_LIB_CRITERES");?></B></TD>
      </TR>
      <TR bgcolor="<?php echo $bgColor[0]; ?>">
        <TD nowrap><B><?php echo trad("FAVGRP_LIB_NOM");?></B></TD>
        <TD><INPUT type="text" class="Texte" name="ztNomGroupe" size="30" maxlength="100" value="<?php echo $nomGroupe; ?>"></TD>
      </TR>
      </TABLE>
      <BR><INPUT type="button" class="Bouton" value="<?php echo $labelBouton; ?>" name="btSubmit" onClick="javascript: return saisieOK(document.Form1);">&nbsp;&nbsp;&nbsp;<INPUT type="button" class="Bouton" value="<?php echo trad("FAVGRP_BT_ANNULER");?>" name="btAnnuler" onclick="javascript: self.close();">
    </CENTER>
    <INPUT type="hidden" name="action" value="<?php echo $ztAction; ?>">
    <INPUT type="hidden" name="id" value="<?php echo $id; ?>">
    <INPUT type="hidden" name="createur" value="<?php echo $ztCreateur; ?>">
    <INPUT type="hidden" name="nom" value="<?php echo htmlspecialchars(stripslashes($ztNom)); ?>">
    <TEXTAREA cols="29" rows="8" name="url" wrap="soft" style="visibility: hidden;"><?php echo htmlspecialchars(stripslashes($ztURL)); ?></TEXTAREA>
    <TEXTAREA cols="29" rows="8" name="commentaire" wrap="soft" style="visibility: hidden;"><?php echo htmlspecialchars(stripslashes($ztCommentaire)); ?></TEXTAREA>
    <INPUT type="hidden" name="groupe" value="<?php echo $zlGroupe; ?>">
    <INPUT type="hidden" name="partage" value="<?php echo $ckPartage; ?>">
    <INPUT type="hidden" name="openFavGrp" value="<?php echo $openFavGrp; ?>">
  </FORM>
</BODY>
</HTML>
<?php
  // Fermeture BDD
  $DB_CX->DbDeconnect();
?>
