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
  ?> <SCRIPT> HelpPhenixCtx="{E8CA709C-9AFA-4BC2-BAFF-CEEFBCB3FD06}.htm"; </SCRIPT> <?php
  // Mod Aide

  $ztAction = "INSERT";
  $titrePage = trad("EVENEMENT_TITRE_ENREG");
  $createur = $idUser;
  $rdType = 0;
  if ($id) {
    $DB_CX->DbQuery("SELECT DATE_FORMAT(eve_date_debut,'%d/%m/%Y') AS dateDebut, DATE_FORMAT(eve_date_fin,'%d/%m/%Y') AS dateFin, DATE_FORMAT(eve_date_debut,'%Y') AS anneeEvt, eve_libelle, eve_partage, eve_util_id, eve_type, eve_couleur FROM ${PREFIX_TABLE}evenement WHERE eve_id=".$id." AND (eve_util_id=".$idUser." OR eve_partage='O')");
    if ($enr = $DB_CX->DbNextRow()) {
      $dateDebut = $enr['dateDebut'];
      $dateFin = $enr['dateFin'];
      $libelle = $enr['eve_libelle'];
      $ckPartage = $enr['eve_partage'];
      $createur = $enr['eve_util_id'];
      $rdType = $enr['eve_type'];
      $couleur = $enr['eve_couleur'];
      $openEvtAnnee = $enr['anneeEvt'];
      $ztAction = "UPDATE";
      $titrePage = trad("EVENEMENT_TITRE_MODIF");
      if ($createur!=$idUser) {
        $titrePage .= " ".trad("EVENEMENT_EVE_PARTAGE");
      }
    } else  {
      $id = 0;
    }
  }
?>
<!-- MODULE GESTION DES EVENEMENTS -->
<?php include("inc/checkdate.js.php"); ?>
  <SCRIPT language="JavaScript">
  <!--
<?php if ($ztAction == "UPDATE") { ?>
    function dupEvent(theForm) {
      theForm.idEvt.value="";
      theForm.ztAction.value="INSERT";
      if (theForm.btSupprime) {
        theForm.btSupprime.disabled=true;
      }
      theForm.btDuplique.disabled=true;
      if (ie4) {
        document.all["titrePage"].innerHTML = "<?php echo trad("EVENEMENT_TITRE_ENREG");?>";
      } else if (ope) {
        document.getElementById("titrePage").innerHTML = "<?php echo trad("EVENEMENT_TITRE_ENREG");?>";
      } else if (ns4) {
        var lyr = document.titrePage.document;
        lyr.write("<?php echo trad("EVENEMENT_TITRE_ENREG");?>");
        lyr.close();
      }
    }

<?php } ?>
    function saisieOK(theForm) {
      if (trim(theForm.ztLibelle.value) == "") {
        window.alert("<?php echo trad("EVENEMENT_JS_SAISIR_LIBELLE");?>");
        theForm.ztLibelle.focus();
        return (false);
      }
      if (theForm.ztDateDebut.value == "") {
        window.alert("<?php echo trad("EVENEMENT_JS_SAISIR_DATE");?>");
        theForm.ztDateDebut.focus();
        return (false);
      }
      if (!chk_date_format(theForm.ztDateDebut) || !chk_date_format(theForm.ztDateFin))
        return (false);
      // Verifier que la date de fin est bien posterieure a la date de debut
      if (theForm.ztDateFin.value != "") {
        if (evalDate(theForm.ztDateFin.value)<evalDate(theForm.ztDateDebut.value)) {
          window.alert("<?php echo trad("EVENEMENT_JS_DATE_POST");?>");
          return (false);
        }
      }
      theForm.submit();
      return (true);
    }
  //-->
  </SCRIPT>
  <TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
  <TR>
    <TD height="28" class="sousMenu"><DIV id="titrePage"><?php echo $titrePage; ?></DIV></TD>
  </TR>
  </TABLE>
  <BR>
  <FORM action="agenda_traitement.php" method="post" name="Form1">
    <INPUT type="hidden" name="sid" value="<?php echo $sid; ?>">
    <INPUT type="hidden" name="idEvt" value="<?php echo $id; ?>">
    <INPUT type="hidden" name="ztAction" value="<?php echo $ztAction; ?>">
    <INPUT type="hidden" name="ztFrom" value="evenement">
    <INPUT type="hidden" name="tcMenu" value="<?php echo $tcMenu; ?>">
    <INPUT type="hidden" name="tcPlg" value="<?php echo $tcPlg; ?>">
    <INPUT type="hidden" name="sd" value="<?php echo date("Y-n-j", $sd); ?>">
    <INPUT type="hidden" name="openEvtAnnee" value="<?php echo ($openEvtAnnee+0); ?>">
<?php if ($createur!=$idUser) { ?>
    <INPUT type="hidden" name="ckPartage" value="O">
<?php } ?>
    <TABLE cellspacing="0" cellpadding="0" width="500" border="0">
    <TR bgcolor="<?php echo $bgColor[1]; ?>" height="21">
      <TD class="tabIntitule" width="120"><?php echo trad("EVENEMENT_LIB_LIBELLE");?></TD>
      <TD class="tabInput" nowrap width="380"><INPUT type="text" class="Texte" name="ztLibelle" value="<?php echo htmlspecialchars($libelle); ?>" size="50" maxlength="100"></TD>
    </TR>
    <TR bgcolor="<?php echo $bgColor[0]; ?>" height="21">
      <TD class="tabIntitule" nowrap><?php echo trad("EVENEMENT_LIB_DATE_DEBUT");?></TD>
      <TD class="tabInput"><INPUT type="text" class="texte" name="ztDateDebut" id="ztDateDebut" value="<?php echo $dateDebut; ?>" size=12 maxlength=10 title="<?php echo trad("EVENEMENT_FORMAT_DATE");?>" onKeyPress="return onlyChar(event);">&nbsp;<INPUT type="button" id="btCal1" value="..." class="Picklist" style="height:16px" title="<?php echo trad("EVENEMENT_AFFICHE_CALENDRIER");?>">&nbsp;&nbsp;<I>(<?php echo trad("EVENEMENT_FORMAT_DATE");?>)</I></TD>
    </TR>
    <TR bgcolor="<?php echo $bgColor[1]; ?>" height="21">
      <TD class="tabIntitule" nowrap><?php echo trad("EVENEMENT_LIB_DATE_FIN");?></TD>
      <TD class="tabInput"><INPUT type="text" class="texte" name="ztDateFin" id="ztDateFin" value="<?php echo $dateFin; ?>" size=12 maxlength=10 title="<?php echo trad("EVENEMENT_FORMAT_DATE");?>" onKeyPress="return onlyChar(event);">&nbsp;<INPUT type="button" id="btCal2" value="..." class="Picklist" style="height:16px" title="<?php echo trad("EVENEMENT_AFFICHE_CALENDRIER");?>">&nbsp;&nbsp;<I>(<?php echo trad("EVENEMENT_FORMAT_DATE");?>)</I></TD>
    </TR>
    <TR bgcolor="<?php echo $bgColor[0]; ?>" height="21">
      <TD class="tabIntitule" nowrap><?php echo trad("EVENEMENT_LIB_ICONE");?></TD>
      <TD class="tabInput"><?php
  for ($i=0;$i<10;$i++) {
    $selected = ($rdType==$i) ? " checked" : "";
    echo "<INPUT type=\"radio\" name=\"rdType\" value=\"$i\" class=\"Case\"".$selected."><IMG src=\"image/evenement/evenement".$i.".gif\" border=\"0\" alt=\"\" align=\"absmiddle\" onclick=\"javascript: document.Form1.rdType[".$i."].checked=true;\">&nbsp;";
  }
?></TD>
      </TR>
    <TR bgcolor="<?php echo $bgColor[1]; ?>" height="21">
      <TD class="tabIntitule" nowrap><?php echo trad("EVENEMENT_LIB_COULEUR");?></TD>
      <TD class="tabInput"><INPUT type="hidden" name="ztCouleur" value="<?php echo $couleur; ?>"><INPUT type="text" name="ztApercu" class="Texte" value="" style="background:<?php echo (!empty($couleur)) ? $couleur : $CalJourEvenement; ?>;" size="25" readonly tabindex="1000">&nbsp;<INPUT type="button" id="btCouleur" value="..." class="Picklist" style="height:16px" title="<?php echo trad("EVENEMENT_AFFICHE_PALETTE");?>" onclick="javascript: showPalette(document.Form1.ztCouleur.value, '<?php echo $CalJourEvenement; ?>','<?php echo trad("EVENEMENT_POPUP_TITRE");?>','<?php echo trad("EVENEMENT_POPUP_FERMER");?>','<?php echo trad("EVENEMENT_POPUP_SELECT");?>');">&nbsp;<INPUT type="button" id="btCouleur" value="<?php echo trad("EVENEMENT_BT_PAR_DEFAUT");?>" class="Bouton" style="height:16px;" onclick="javascript: document.Form1.ztCouleur.value=''; document.Form1.ztApercu.style.backgroundColor='<?php echo $CalJourEvenement; ?>';"></TD>
    </TR>
<?php if ($createur==$idUser) { ?>
    <TR bgcolor="<?php echo $bgColor[0]; ?>" height="21">
      <TD class="tabIntitule"><?php echo trad("EVENEMENT_PARTAGE");?></TD>
      <TD class="tabInput" nowrap><LABEL for="partageFav"><INPUT type="checkbox" name="ckPartage" id="partageFav" value="O" class="Case"<?php if ($ckPartage=='O') {echo " checked";} ?>>&nbsp;<?php echo trad("EVENEMENT_COCHER_PARTAGE");?></LABEL></TD>
    </TR>
<?php } ?>
    </TABLE>
    <BR><INPUT type="button" name="btEnregistre" value="<?php echo trad("EVENEMENT_BT_ENREGISTRER");?>" onClick="javascript: return saisieOK(document.Form1);" class="bouton">&nbsp;&nbsp;&nbsp;<INPUT type="button" name="btAnnule" value="<?php echo trad("EVENEMENT_BT_ANNULER");?>" onclick="javascript: btAnnul();" class="bouton"><?php if ($ztAction == "UPDATE" && $createur==$idUser) { ?>&nbsp;&nbsp;&nbsp;<INPUT type="button" name="btSupprime" value="<?php echo trad("EVENEMENT_BT_SUPPRIMER");?>" onclick="javascript: if (confirm('<?php echo trad("EVENEMENT_JS_CONFIRME_SUPPRIMER");?>')) { document.Form1.ztAction.value='DELETE'; document.Form1.submit(); }" class="bouton"><?php } if ($ztAction == "UPDATE") { ?>&nbsp;&nbsp;&nbsp;<INPUT type="button" name="btDuplique" value="<?php echo trad("EVENEMENT_BT_DUPLIQUER");?>" onclick="javascript: dupEvent(document.Form1);" class="Bouton"><?php } ?>
  </FORM>
  <BR>
<?php
  //Liste des differents evenements auxquels l'utilisateur a acces
  $DB_CX->DbQuery("SELECT eve_id, DATE_FORMAT(eve_date_debut,'%d/%m/%Y') AS dateDebut, DATE_FORMAT(eve_date_fin,'%d/%m/%Y') AS dateFin, DATE_FORMAT(eve_date_debut,'%Y') AS anneeEvt, eve_libelle, eve_util_id, eve_type, eve_couleur FROM ${PREFIX_TABLE}evenement WHERE eve_util_id=".$idUser." OR eve_partage='O' ORDER BY eve_date_debut, eve_libelle");
  if ($DB_CX->DbNumRows()) {
    echo ("  <FORM>
    <TABLE width=\"500\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\" bgcolor=\"".$AgendaBordureTableau."\" style=\"border-collapse:separate;\">\n");
    $index = 0;
    $anneeCrt = "";
    while ($enr = $DB_CX->DbNextRow()) {
      if ($anneeCrt!=$enr['anneeEvt']) {
        if ($anneeCrt!="") {
          // Ce n'est pas le premier enregistrement lu -> on ferme le tableau de l'annee precedente
          echo ("      </TABLE></DIV></TD></TR>\n");
        }
        $anneeCrt = $enr['anneeEvt'];
        $index = 0;
        if ($openEvtAnnee==$anneeCrt) {
          if (file_exists("skins/".$APPLI_STYLE."/collapse_fav.gif")) {
            $imgEvt = "skins/".$APPLI_STYLE."/collapse_fav.gif";
            $pathEvt = "skins/".$APPLI_STYLE."/";
          } else {
            $imgEvt = "image/collapse_fav.gif";
            $pathEvt = "image/";
          }
          $dispEvt = "displayBlock";
        } else {
          if (file_exists("skins/".$APPLI_STYLE."/expand_fav.gif")) {
            $imgEvt = "skins/".$APPLI_STYLE."/expand_fav.gif";
            $pathEvt = "skins/".$APPLI_STYLE."/";
          } else {
            $imgEvt = "image/expand_fav.gif";
            $pathEvt = "image/";
          }
          $dispEvt = "displayNone";
        }
        // On reecrit l'entete du tableau
        echo ("    <TR height=\"15\" onclick=\"javascript:affListe(document.Form1['openEvtAnnee'],'favorisGrp','imageGrp','".$anneeCrt."','".$pathEvt."');\" style=\"cursor:pointer;\"><TD width=\"100%\" bgcolor=\"".$AgendaFavorisFond."\"><TABLE cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" class=\"paddingDG3\"><TR><TD align=\"center\" width=\"100%\"><B><A class=\"MemoFavorisTitre\">".sprintf(trad("EVENEMENT_ANNEE"), $enr['anneeEvt'])."</A></B></TD><TD align=\"right\" nowrap><IMG id='imageGrp".$anneeCrt."' src=\"".$imgEvt."\" width=\"7\" height=\"4\" alt=\"\" border=\"0\">&nbsp;</TD></TR></TABLE></TD></TR>
      <TR><TD colspan=\"2\"><DIV id=\"favorisGrp".$anneeCrt."\" class=\"".$dispEvt."\"><TABLE cellspacing=\"1\" cellpadding=\"2\" width=\"100%\">
    <TR>
      <TD width=\"75\" class=\"enteteTableau\">".trad("EVENEMENT_DEBUT")."</TD>
      <TD width=\"75\" class=\"enteteTableau\">".trad("EVENEMENT_FIN")."</TD>
      <TD width=\"40\" class=\"enteteTableau\">".trad("EVENEMENT_TYPE")."</TD>
      <TD width=\"260\" class=\"enteteTableau\">".trad("EVENEMENT_LIBELLE")."</TD>
      <TD width=\"50\" class=\"enteteTableau\">".trad("EVENEMENT_ACTION")."</TD>
    </TR>\n");
      }
      $index = 1 - $index;
      echo ("    <TR bgcolor=\"".$bgColor[$index]."\" align=\"center\">
      <TD nowrap>".$enr['dateDebut']."</TD>
      <TD nowrap>".$enr['dateFin']."</TD>
      <TD bgcolor=\"".((!empty($enr['eve_couleur'])) ? $enr['eve_couleur'] : $CalJourEvenement)."\"><IMG src=\"image/evenement/evenement".$enr['eve_type'].".gif\" border=\"0\" alt=\"\"></TD>
      <TD align=\"left\"><B>".$enr['eve_libelle']."</B>&nbsp;</TD>
      <TD align=\"left\" nowrap>&nbsp;");
      if ($enr['eve_util_id']==$idUser || $MODIF_PARTAGE) { // Modif de l'evenement
        echo "<INPUT type=\"button\" class=\"bouton\" name=\"btModif\" value=\"".trad("EVENEMENT_BT_M")."\" title=\"".trad("EVENEMENT_MODIFIER")."\" style=\"width:16px\" onclick=\"javascript: window.location.href='?sid=".$sid."&tcType="._TYPE_EVENEMENT."&id=".$enr['eve_id']."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."';\">&nbsp;";
      }
      if (($enr['eve_util_id']==$idUser) || (($enr['eve_util_id']=="0") && $idAdmin)){ // Suppression de l'evenement
        echo "<INPUT type=\"button\" class=\"bouton\" name=\"btSuppr\" value=\"".trad("EVENEMENT_BT_S")."\" title=\"".trad("EVENEMENT_BT_SUPPRIMER")."\" style=\"width:16px\" onclick=\"javascript: if (confirm('".trad("EVENEMENT_JS_CONFIRME_SUPPRIMER")."')) window.location.href='agenda_traitement.php?ztFrom=evenement&ztAction=DELETE&idEvt=".$enr['eve_id']."&sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".date("Y-n-j", $sd)."';\">&nbsp;";
      }
      echo ("</TD>
    </TR>\n");
    }
    // On ferme le tableau de l'annee precedente
    echo ("      </TABLE></DIV></TD></TR>
    </TABLE>
  </FORM>\n");
  }
?>
  <SCRIPT type="text/javascript">
  <!--
    Calendar.setup( {
      inputField : "ztDateDebut",    // ID of the input field
      ifFormat   : "%d/%m/%Y",  // the date format
      button     : "btCal1"      // ID of the button
    } );
    Calendar.setup( {
      inputField : "ztDateFin",    // ID of the input field
      ifFormat   : "%d/%m/%Y",  // the date format
      button     : "btCal2"      // ID of the button
    } );
    document.Form1.ztLibelle.focus();
  //-->
  </SCRIPT>
<!-- FIN MODULE GESTION DES EVENEMENTS -->
