<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5.5_Aide.txt ?>
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
  // Mod Aide
  // Fichier d'aide contextuel
  ?> <SCRIPT> HelpPhenixCtx="{F2E5B6A9-A9AF-4AC9-A74A-82B31A27F429}.htm"; </SCRIPT> <?php
  // Mod Aide

  $ztAction = "INSERT";
  $titrePage = trad("ANNIV_TITRE_ENREG");
  if ($id) {
    $DB_CX->DbQuery("SELECT age_id, DATE_FORMAT(age_date,'%d/%m/%Y') AS ageDate, age_libelle FROM ${PREFIX_TABLE}agenda WHERE age_id=".$id." AND age_util_id=".$idUser." AND age_aty_id=1");
    if ($enr = $DB_CX->DbNextRow()) {
      $ztAction = "UPDATE";
      $titrePage = trad("ANNIV_TITRE_MODIF");
    } else  {
      $id = 0;
    }
  }
?>
<!-- MODULE GESTION DES ANNIVERSAIRES -->
<?php include("inc/checkdate.js.php"); ?>
  <SCRIPT language="JavaScript">
  <!--
    function saisieOK(theForm) {
      if (trim(theForm.ztLibelle.value) == "") {
        window.alert("<?php echo trad("ANNIV_JS_SAISIR_NOM");?>");
        theForm.ztLibelle.focus();
        return (false);
      }
      if (theForm.ztDate.value == "") {
        window.alert("<?php echo trad("ANNIV_JS_SAISIR_DATE");?>");
        theForm.ztDate.focus();
        return (false);
      }
      if (!chk_date_format(theForm.ztDate))
        return (false);

      theForm.submit();
      return (true);
    }
  //-->
  </SCRIPT>
  <TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
  <TR>
    <TD height="28" class="sousMenu"><?php echo $titrePage; ?></TD>
  </TR>
  </TABLE>
  <BR>
  <FORM action="agenda_traitement.php" method="post" name="Form1">
    <INPUT type="hidden" name="sid" value="<?php echo $sid; ?>">
    <INPUT type="hidden" name="idAge" value="<?php echo $enr['age_id']; ?>">
    <INPUT type="hidden" name="ztAction" value="<?php echo $ztAction; ?>">
    <INPUT type="hidden" name="ztFrom" value="anniv">
    <INPUT type="hidden" name="tcMenu" value="<?php echo $tcMenu; ?>">
    <INPUT type="hidden" name="tcPlg" value="<?php echo $tcPlg; ?>">
    <INPUT type="hidden" name="sd" value="<?php echo date("Y-n-j", $sd); ?>">
    <TABLE cellspacing="0" cellpadding="0" width="465" border="0">
    <TR bgcolor="<?php echo $bgColor[1]; ?>" height="21">
      <TD class="tabIntitule" width="120"><?php echo trad("ANNIV_LIB_ANNIVERSAIRE_DE");?></TD>
      <TD class="tabInput" nowrap width="345"><INPUT type="text" class="Texte" name="ztLibelle" value="<?php echo htmlspecialchars($enr['age_libelle']); ?>" size="50" maxlength="150"></TD>
    </TR>
    <TR bgcolor="<?php echo $bgColor[0]; ?>" height="21">
      <TD class="tabIntitule" nowrap><?php echo trad("ANNIV_LIB_DATE_NAISSANCE");?></TD>
      <TD class="tabInput"><INPUT type="text" class="texte" name="ztDate" id="ztDate" value="<?php echo $enr['ageDate']; ?>" size=12 maxlength=10 title="<?php echo trad("ANNIV_FORMAT_DATE");?>" onKeyPress="return onlyChar(event);">&nbsp;<INPUT type="button" id="btCal" value="..." class="Picklist" style="height:16px" title="<?php echo trad("ANNIV_AFFICHE_CALENDRIER");?>">&nbsp;&nbsp;<I>(<?php echo trad("ANNIV_FORMAT_DATE");?>)</I></TD>
    </TR>
    </TABLE>
    <BR><INPUT type="button" name="btEnregistre" value="<?php echo trad("ANNIV_BT_ENREGISTRER");?>" onClick="javascript: return saisieOK(document.Form1);" class="bouton">&nbsp;&nbsp;&nbsp;<INPUT type="button" name="btAnnule" value="<?php echo trad("ANNIV_BT_ANNULER");?>" onclick="javascript: btAnnul();" class="bouton"><?php if ($ztAction == "UPDATE") { ?>&nbsp;&nbsp;&nbsp;<INPUT type="button" name="btSupprime" value="<?php echo trad("ANNIV_BT_SUPPRIMER");?>" onclick="javascript: if (confirm('<?php echo trad("ANNIV_JS_CONFIRME_SUPPRIMER");?>')) { document.Form1.ztAction.value='DELETE'; document.Form1.submit(); }" class="bouton"><?php } ?>
  </FORM>
<?php
  //Liste des differents anniversaire de l'utilisateur
  $DB_CX->DbQuery("SELECT age_id, DATE_FORMAT(age_date,'%d/%m/%Y') AS ageDate, age_libelle FROM ${PREFIX_TABLE}agenda WHERE age_util_id=".$idUser." AND age_aty_id=1 ORDER BY age_libelle");
  if ($DB_CX->DbNumRows()) {
    echo ("  <BR>
  <FORM>
    <TABLE width=\"465\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\" bgcolor=\"".$AgendaBordureTableau."\" style=\"border-collapse:separate;\">
    <TR>
      <TD class=\"enteteTableau\" nowrap>".trad("ANNIV_COL_NOM")."</TD>
      <TD class=\"enteteTableau\" nowrap>".trad("ANNIV_COL_DATE")."</TD>
      <TD class=\"enteteTableau\" nowrap>".trad("ANNIV_COL_ACTION")."</TD>
    </TR>\n");
    $index = 0;
    while ($enr = $DB_CX->DbNextRow()) {
      $index = 1 - $index;
      echo ("    <TR bgcolor=\"".$bgColor[$index]."\">
      <TD width=\"100%\" nowrap><B>".$enr['age_libelle']."</B>&nbsp;&nbsp;</TD>
      <TD nowrap>".$enr['ageDate']."</TD>
      <TD width=\"50\" nowrap>&nbsp;<INPUT type=\"button\" class=\"bouton\" name=\"btModif\" value=\"".trad("ANNIV_BT_M")."\" title=\"".trad("ANNIV_MODIFIER")."\" style=\"width:16px\" onclick=\"javascript: window.location.href='?sid=".$sid."&tcType="._TYPE_ANNIV."&id=".$enr['age_id']."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."';\">&nbsp;<INPUT type=\"button\" class=\"bouton\" name=\"btSuppr\" value=\"".trad("ANNIV_BT_S")."\" title=\"".trad("ANNIV_BT_SUPPRIMER")."\" style=\"width:16px\" onclick=\"javascript: if (confirm('".trad("ANNIV_JS_CONFIRME_SUPPRIMER")."')) window.location.href='agenda_traitement.php?ztFrom=anniv&ztAction=DELETE&idAge=".$enr['age_id']."&sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".date("Y-n-j", $sd)."';\"></TD>
    </TR>\n");
    }
    echo ("    </TABLE>
  </FORM>\n");
  }
?>
  <SCRIPT type="text/javascript">
  <!--
    Calendar.setup( {
      inputField : "ztDate",    // ID of the input field
      ifFormat   : "%d/%m/%Y",  // the date format
      button     : "btCal"      // ID of the button
    } );
    document.Form1.ztLibelle.focus();
  //-->
  </SCRIPT>
<!-- FIN MODULE GESTION DES ANNIVERSAIRES -->
