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

  if ($groupe != "0") {
    $DB_CX->DbQuery("SELECT cgr_pere_id, cgr_nom FROM ${PREFIX_TABLE}calepin_groupe WHERE cgr_id=".$groupe);
    $grpPere = $DB_CX->DbResult(0,0);
    $nomGroupe = $DB_CX->DbResult(0,1);
    $labelBouton = trad("CALGRP_BT_MODIFIER");
  }
  else {
    $grpPere = "0";
    $labelBouton = trad("CALGRP_BT_ENREGISTRER");
  }

  // Affiche la liste des groupes de contacts
  function aff_groupe($grpPere,$nivGrp,$grp,$nePasAff) {
    global $DB_CX, $PREFIX_TABLE, $idUser;
    $DB = new Db($DB_CX->ConnexionID);
    $DB->DbQuery("SELECT cgr_id, cgr_nom FROM ${PREFIX_TABLE}calepin_groupe WHERE cgr_id!=".$nePasAff." AND cgr_pere_id=".$grpPere." AND cgr_util_id=".$idUser." ORDER BY cgr_nom");
    $nivGrp++;
    while ($enr = $DB->DbNextRow()) {
      $selected = ($grp == $enr['cgr_id']) ? " selected" : "";
      echo "<OPTION value=\"".$enr['cgr_id']."\"".$selected.">";
      for ($i=0;$i<$nivGrp-1;$i++)
        echo "&nbsp;";//
      echo $enr['cgr_nom']."</OPTION>";
      aff_groupe($enr['cgr_id'],$nivGrp,$grp,$nePasAff);
    }
  }
?>
<!DOCTYPE html public "-//w3c//dtd html 4.0 transitional//en">
<HTML>
<HEAD>
  <META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <TITLE><?php echo trad("CALGRP_TITLE_GESTION");?></TITLE>
  <LINK rel="stylesheet" type="text/css" href="css/agenda_css.php?id=<?php echo $APPLI_STYLE; ?>">
  <SCRIPT language="JavaScript" type="text/javascript">
  <!--
    // Fonction trim javascript (suppression d'espaces avant et apres une chaine)
    function trim(chaine) {
      return chaine.replace(/^\s+/, "").replace(/\s+$/, "");
    }

    function restoreParam() {
      window.opener.document.forms['frmCalepin'].action="?sid=<?php echo $sid; ?>&tcMenu=<?php echo $tcMenu; ?>&tcPlg=<?php echo $tcPlg; ?>&sd=<?php echo $sd; ?>";
      window.opener.document.forms['frmCalepin'].target="_self";
    }

    function saisieOK(theForm) {
      if (trim(theForm.ztNom.value) == "") {
        window.alert("<?php echo trad("CALGRP_JS_SAISIR_NOM");?>");
        theForm.ztNom.focus();
        return (false);
      }

      theForm.submit();
      self.close();
      return (true);
    }
  //-->
  </SCRIPT>
</HEAD>
<BODY onLoad="javascript: window.focus(); document.Form1.ztNom.focus();" onUnload="javascript: restoreParam();" topmargin=7 style='<?php echo $AgendaCalepinGroupesFondImage; ?>'>
  <FORM name="Form1" method="post" action="agenda.php?sid=<?php echo $sid; ?>&tcMenu=<?php echo $tcMenu; ?>&tcPlg=<?php echo $tcPlg; ?>&sd=<?php echo $sd; ?>" target="nav_<?php echo $sid; ?>">
    <CENTER>
      <TABLE border="0" cellpadding="2" cellspacing="1" bgcolor="<?php echo $AgendaCalepinGroupesBords; ?>" style="border-collapse:separate;">
      <TR>
        <TD colspan="2" align="center" class="MenuOff"><B><?php echo trad("CALGRP_LIB_CRITERES");?></B></TD>
      </TR>
      <TR bgcolor="<?php echo $bgColor[1]; ?>">
        <TD nowrap><B><?php echo trad("CALGRP_LIB_PERE");?></B></TD>
        <TD><SELECT name="zlPere" size="1"><OPTION value="0"><?php echo trad("CALGRP_AUCUN_PERE");?></OPTION><?php aff_groupe(0,0,$grpPere,$groupe); ?></SELECT></TD>
      </TR>
      <TR bgcolor="<?php echo $bgColor[0]; ?>">
        <TD nowrap><B><?php echo trad("CALGRP_LIB_NOM");?></B></TD>
        <TD><INPUT type="text" class="Texte" name="ztNom" size="30" maxlength="100" value="<?php echo $nomGroupe; ?>"></TD>
      </TR>
      </TABLE>
      <BR><INPUT type="button" class="Bouton" value="<?php echo $labelBouton; ?>" name="btSubmit" onClick="javascript: return saisieOK(document.Form1);">&nbsp;&nbsp;&nbsp;<INPUT type="button" class="Bouton" value="<?php echo trad("CALGRP_BT_ANNULER");?>" name="btAnnuler" onclick="javascript: self.close();">
    </CENTER>
    <INPUT type="hidden" name="ztAction" value="M">
    <INPUT type="hidden" name="type2" value="<?php echo $type2; ?>">
    <INPUT type="hidden" name="id" value="<?php echo $id; ?>">
    <INPUT type="hidden" name="proprio" value="<?php echo $proprio; ?>">
    <INPUT type="hidden" name="societe" value="<?php echo htmlspecialchars(stripslashes($societe)); ?>">
    <INPUT type="hidden" name="nom" value="<?php echo htmlspecialchars(stripslashes($nom)); ?>">
    <INPUT type="hidden" name="prenom" value="<?php echo htmlspecialchars(stripslashes($prenom)); ?>">
    <TEXTAREA cols="29" rows="8" name="add" wrap="soft" style="visibility: hidden;"><?php echo htmlspecialchars(stripslashes($add)); ?></TEXTAREA>
    <INPUT type="hidden" name="cp" value="<?php echo htmlspecialchars(stripslashes($cp)); ?>">
    <INPUT type="hidden" name="ville" value="<?php echo htmlspecialchars(stripslashes($ville)); ?>">
    <INPUT type="hidden" name="pays" value="<?php echo htmlspecialchars(stripslashes($pays)); ?>">
    <INPUT type="hidden" name="domicile" value="<?php echo htmlspecialchars(stripslashes($domicile)); ?>">
    <INPUT type="hidden" name="travail" value="<?php echo htmlspecialchars(stripslashes($travail)); ?>">
    <INPUT type="hidden" name="portable" value="<?php echo htmlspecialchars(stripslashes($portable)); ?>">
    <INPUT type="hidden" name="fax" value="<?php echo htmlspecialchars(stripslashes($fax)); ?>">
    <INPUT type="hidden" name="email" value="<?php echo htmlspecialchars(stripslashes($email)); ?>">
    <INPUT type="hidden" name="emailpro" value="<?php echo htmlspecialchars(stripslashes($emailpro)); ?>">
    <INPUT type="hidden" name="icq" value="<?php echo htmlspecialchars(stripslashes($icq)); ?>">
    <INPUT type="hidden" name="aim" value="<?php echo htmlspecialchars(stripslashes($aim)); ?>">
    <INPUT type="hidden" name="msn" value="<?php echo htmlspecialchars(stripslashes($msn)); ?>">
    <INPUT type="hidden" name="yahoo" value="<?php echo htmlspecialchars(stripslashes($yahoo)); ?>">
    <INPUT type="hidden" name="naissance" value="<?php echo htmlspecialchars(stripslashes($naissance)); ?>">
    <TEXTAREA cols="29" rows="8" name="note" wrap="soft" style="visibility: hidden;"><?php echo htmlspecialchars(stripslashes($note)); ?></TEXTAREA>
    <INPUT type="hidden" name="groupe" value="<?php echo $groupe; ?>">
    <INPUT type="hidden" name="partage" value="<?php echo $partage; ?>">
    <INPUT type="hidden" name="siteweb" value="<?php echo htmlspecialchars(stripslashes($siteweb)); ?>">
  </FORM>
</BODY>
</HTML>
<?php
  // Fermeture BDD
  $DB_CX->DbDeconnect();
?>
