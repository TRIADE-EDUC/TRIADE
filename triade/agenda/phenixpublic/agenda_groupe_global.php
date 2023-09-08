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
    include ("inc/interdit.html");
    exit;
  }

  $idUser = Session_ok($sid);

  if ($idUser == -1) {
    include ("inc/interdit.html");
    exit;
  }

  include("skins/$APPLI_STYLE.php");
  include("lang/$APPLI_LANGUE.php");

  list ($grpg, $GrChoix) = explode ('|', $ggr);
  if ($grpg != "0") {
    if ($utilgr=="O") {
      $DB_CX->DbQuery("SELECT gr_util_nom FROM ${PREFIX_TABLE}groupe_util WHERE gr_util_id=".$grpg);
    } else {
      $DB_CX->DbQuery("SELECT ggr_nom FROM ${PREFIX_TABLE}global_groupe WHERE ggr_id=".$grpg);
    }
    $ggr_nom = $DB_CX->DbResult(0,0);
    if ($ggr_nom!="NoGroup"){
      $labelBouton = trad("GRPGL_BT_MODIFIER");
      $ztActionGrp = "ModifGgg";
    } else {
      $labelBouton = trad("GRPGL_BT_ENREGISTRER");
      $ztActionGrp = "AjoutGgg";
      $ggr_nom = "";
    }
  } else {
    $labelBouton = trad("GRPGL_BT_ENREGISTRER");
    $ztActionGrp = "AjoutGgg";
    $ggr_nom = "";
  }


?>
<!DOCTYPE html public "-//w3c//dtd html 4.0 transitional//en">
<HTML>
<HEAD>
  <META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <TITLE><?php echo trad("GRPGL_TITLE_GESTION");?></TITLE>
  <LINK rel="stylesheet" type="text/css" href="css/agenda_css.php?id=<?php echo $APPLI_STYLE; ?>">
  <SCRIPT language="JavaScript" type="text/javascript">
  <!--
    function restoreParam() {
      window.opener.document.forms['frmChoixGrp'].action="?sid=<?php echo $sid; ?>&tcMenu=<?php echo $tcMenu; ?>&tcPlg=<?php echo $tcPlg; ?>&sd=<?php echo $sd; ?>";
      window.opener.document.forms['frmChoixGrp'].target="_self";
    }

    function saisieOK(theForm) {
      if (trim(theForm.ztNom.value) == "") {
        window.alert("<?php echo trad("GRPGL_JS_SAISIR_NOM");?>");
        theForm.ztNom.focus();
        return (false);
      }

      theForm.submit();
      self.close();
      return (true);
    }

    // Fonction trim javascript (suppression d'espaces avant et apres une chaine)
    function trim(chaine) {
      return chaine.replace(/^\s+/, "").replace(/\s+$/, "");
    }
  //-->
  </SCRIPT>
</HEAD>
<BODY onLoad="javascript: window.focus(); document.Form1.ztNom.focus();" onUnload="javascript: restoreParam();" topmargin=7 style='<?php echo $AgendaCalepinGroupesFondImage; ?>'>
  <FORM name="Form1" method="post" action="agenda_traitement.php?sid=<?php echo $sid; ?>&tcMenu=<?php echo $tcMenu; ?>&tcPlg=<?php echo $tcPlg; ?>&sd=<?php echo $sd; ?>&sChoix=<?php echo $sChoix; ?>&ztActionGrp=<?php echo $ztActionGrp; ?>" target="nav_<?php echo $sid; ?>">
    <CENTER>
      <TABLE border="0" cellpadding="2" cellspacing="1" bgcolor="<?php echo $AgendaFavorisGroupesBords; ?>" style="border-collapse:separate;">
      <TR>
<?php if ($utilgr=="O") { ?>
        <TD colspan="2" align="center" class="MenuOff"><B><?php echo trad("ADMIN_GROUPE");?></B></TD>
<?php } else { ?>
        <TD colspan="2" align="center" class="MenuOff"><B><?php echo trad("GRPGL_LIB_GROUPE");?></B></TD>
<?php } ?>
      </TR>
      <TR bgcolor="<?php echo $bgColor[0]; ?>">
        <TD nowrap><B><?php echo trad("GRPGL_LIB_NOM");?></B></TD>
        <TD><INPUT type="text" class="Texte" name="ztNom" size="30" maxlength="100" value="<?php echo $ggr_nom; ?>"></TD>
      </TR>
      </TABLE>
      <BR>
      <INPUT type="button" class="Bouton" value="<?php echo $labelBouton; ?>" name="btSubmit" onClick="javascript: return saisieOK(document.Form1);">&nbsp;&nbsp;&nbsp;
      <INPUT type="button" class="Bouton" value="<?php echo trad("GRPGL_BT_ANNULER");?>" name="btAnnuler" onclick="javascript: self.close();">
      <INPUT type="hidden" name="grpg" value="<?php echo $grpg; ?>">
      <INPUT type="hidden" name="typegr" value="<?php echo $typegr; ?>">
      <INPUT type="hidden" name="utilgr" value="<?php echo $utilgr; ?>">
    </CENTER>
  </FORM>
</BODY>
</HTML>
