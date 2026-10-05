<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5_Liste_des_libelles.txt ?>
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
  ?> <SCRIPT> HelpPhenixCtx="{85E07E51-421D-4EC2-BBFA-3808BBC52924}.htm"; </SCRIPT> <?php
  // Mod Aide

  $id += 0;
  $ztAction = "INSERT";
  $titrePage = trad("LIBELLE_TITRE_ENREG");
  $createur = $idUser;
  $duree = 0.25;
  if ($id) {
    // Edition d'un libelle
    $DB_CX->DbQuery("SELECT lib_nom, lib_duree, lib_couleur, lib_partage, lib_util_id, lib_detail FROM ${PREFIX_TABLE}libelle WHERE lib_id=".$id);
    if ($enr = $DB_CX->DbNextRow()) {
      $nom = $enr['lib_nom'];
      $duree = $enr['lib_duree'];
      $couleur = $enr['lib_couleur'];
      $ckPartage = $enr['lib_partage'];
      $createur = $enr['lib_util_id'];
      $detail = $enr['lib_detail'];
      $ztAction = "UPDATE";
      $titrePage = trad("LIBELLE_TITRE_MODIF");
      if ($createur!=$idUser) {
        $titrePage .= " ".trad("LIBELLE_TITRE_PARTAGE");
      }
    }
  }
?>
<!-- MODULE GESTION DES LIBELLES TYPES -->
  <SCRIPT language="JavaScript">
  <!--
    function saisieOK(theForm) {
      if (trim(theForm.ztLibelle.value) == "") {
        window.alert("<?php echo trad("LIBELLE_JS_SAISIR_INTITULE");?>");
        theForm.ztLibelle.focus();
        return (false);
      }

      PrepareSave();
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
  <FORM action="agenda_traitement.php" method="post" name="frmLibelle">
    <INPUT type="hidden" name="sid" value="<?php echo $sid; ?>">
    <INPUT type="hidden" name="sd" value="<?php echo date("Y-n-j", $sd); ?>">
    <INPUT type="hidden" name="id" value="<?php echo $id; ?>">
    <INPUT type="hidden" name="ztFrom" value="libelles">
    <INPUT type="hidden" name="ztAction" value="<?php echo $ztAction; ?>">
    <INPUT type="hidden" name="tcMenu" value="<?php echo $tcMenu; ?>">
    <INPUT type="hidden" name="tcPlg" value="<?php echo $tcPlg; ?>">
<?php if ($createur!=$idUser) { ?>
    <INPUT type="hidden" name="ckPartage" value="O">
<?php } ?>
  <TABLE cellspacing="0" cellpadding="0" width="550" border="0">
  <TR bgcolor="<?php echo $bgColor[1]; ?>" height="21">
    <TD class="tabIntitule" nowrap><?php echo trad("LIBELLE_LIB_CHOIX");?></TD>
    <TD class="tabInput" nowrap><SELECT name="zlLibelle" onchange="javascript: window.location.href='?sid=<?php echo $sid; ?>&tcType=<?php echo _TYPE_LIBELLE; ?>&tcMenu=<?php echo $tcMenu; ?>&tcPlg=<?php echo $tcPlg; ?>&sd=<?php echo $sd; ?>&id=' + this.value;">
      <OPTION value="0">-- <?php echo trad("LIBELLE_NOUVEAU_LIB");?> --</OPTION>
<?php
  $DB_CX->DbQuery("SELECT lib_id, lib_nom FROM ${PREFIX_TABLE}libelle WHERE lib_util_id=".$idUser.(($MODIF_PARTAGE) ? " OR (lib_util_id!=".$idUser." AND lib_partage='O')" : "")." ORDER BY lib_nom");
  while ($listLib = $DB_CX->DbNextRow()) {
    $selected = ($id == $listLib['lib_id']) ? " selected" : "";
    echo "      <OPTION value=\"".$listLib['lib_id']."\"".$selected.">".htmlspecialchars($listLib['lib_nom'])."</OPTION>\n";
  }
?>
      </SELECT></TD>
  </TR>
  <TR bgcolor="<?php echo $bgColor[0]; ?>" height="21">
    <TD class="tabIntitule" nowrap><?php echo trad("LIBELLE_LIB_INTITULE");?></TD>
    <TD class="tabInput" nowrap width="474"><INPUT type="text" class="Texte" name="ztLibelle" value="<?php echo htmlspecialchars($nom); ?>" style="width:469px" maxlength="150"></TD>
  </TR>
  <TR bgcolor="<?php echo $bgColor[1]; ?>">
    <TD class="tabIntitule" nowrap><?php echo trad("LIBELLE_LIB_DETAIL");?></TD>
    <TD class="tabInput" nowrap><?php genereTextArea("ztDetail",$detail,469,7); ?></TD>
  </TR>
  <TR bgcolor="<?php echo $bgColor[0]; ?>" height="21">
    <TD class="tabIntitule" nowrap><?php echo trad("LIBELLE_LIB_DUREE");?></TD>
    <TD class="tabInput" nowrap><SELECT name="zlDuree"<?php if ($duree==0) echo " disabled"; ?>>
<?php
    for ($i=0.25; $i<24;$i=$i+0.25) {
      $selected = ($i==$duree) ? " selected" : "";
      echo "          <OPTION value=\"".$i."\"".$selected.">".sprintf(trad("LIBELLE_MINUTE"), afficheHeure($i,$i))."</OPTION>\n";
    }
?>
    </SELECT>&nbsp;&nbsp;&nbsp;<LABEL for="allDay"><INPUT type="checkbox" name="ckJournee" id="allDay" class="case" value="1"<?php if ($duree==0) echo " checked"; ?> onclick="javascript: document.frmLibelle.zlDuree.disabled = (this.checked);">&nbsp;<?php echo trad("LIBELLE_JOURNEE_ENTIERE");?></LABEL></TD>
  </TR>
  <TR bgcolor="<?php echo $bgColor[1]; ?>" height="21">
    <TD class="tabIntitule" nowrap><?php echo trad("LIBELLE_LIB_COULEUR");?></TD>
    <TD class="tabInput"><?php
    //Recuperation des couleurs/categories de notes
    $tabTemp    = array(trad("COMMUN_COUL_DEFAUT") => $AgendaFondNotePerso);
    $tabCouleur = array_merge($tabTemp,getListeCouleur());

    //Construction de la liste des couleurs/categories de notes
    reset($tabCouleur);
    if (empty($couleur))
      $couleur = $AgendaFondNotePerso;
    echo "<SELECT name=\"zlCouleur\" style=\"background-color:".$couleur.";\" onchange=\"javascript: changeCouleurListe(this,document.frmLibelle.ztCouleur);\">\n";
    while (list($key, $val) = each($tabCouleur)) {
      $selected = ($val==$couleur) ? " selected" : "";
      echo "      <OPTION style=\"background-color:".$val.";\" value=\"".$val."\"".$selected.">".$key."</OPTION>\n";
    }
?>
    </SELECT>&nbsp;&nbsp;&nbsp;<INPUT type="text" name="ztCouleur" class="Texte" value="<?php echo trad("LIBELLE_APPARENCE");?>" style="background:<?php echo $couleur; ?>; text-align:center; font-weight:bold; height:17px;" size=25 readonly></TD>
  </TR>
<?php if ($createur==$idUser) { ?>
  <TR bgcolor="<?php echo $bgColor[0]; ?>" height="21">
    <TD class="tabIntitule" nowrap><?php echo trad("LIBELLE_LIB_PARTAGE");?></TD>
    <TD class="tabInput" nowrap><LABEL for="partageLib"><INPUT type="checkbox" name="ckPartage" id="partageLib" value="O" class="Case"<?php if ($ckPartage=='O') {echo " checked";} ?>>&nbsp;<?php echo trad("LIBELLE_COCHER_PARTAGE");?></LABEL></TD>
  </TR>
<?php } ?>
  </TABLE>
  <BR><INPUT type="button" name="btEnregistre" value="<?php echo trad("LIBELLE_BT_ENREGISTRER");?>" onClick="javascript: return saisieOK(document.frmLibelle);" class="bouton">&nbsp;&nbsp;&nbsp;<INPUT type="button" name="btAnnule" value="<?php echo trad("LIBELLE_BT_ANNULER");?>" onclick="javascript: btAnnul();" class="bouton"><?php if ($ztAction=="UPDATE" && $createur==$idUser) { ?>&nbsp;&nbsp;&nbsp;<INPUT type="button" name="btSupprime" value="<?php echo trad("LIBELLE_BT_SUPPRIMER");?>" onclick="javascript: if (confirm('<?php echo trad("LIBELLE_JS_CONFIRME_SUPPRIMER");?>')) { document.frmLibelle.ztAction.value='DELETE'; document.frmLibelle.submit(); }" class="bouton"><?php } ?>
  </FORM>
<?php
  // MOD Liste des libelles
  //Liste des differents libelles
  $DB_CX->DbQuery("SELECT lib_id, lib_nom, lib_detail, lib_duree, lib_couleur, lib_util_id, lib_partage FROM ${PREFIX_TABLE}libelle WHERE lib_util_id=".$idUser." OR lib_partage='O' ORDER BY lib_id");
  if ($DB_CX->DbNumRows()) {
    echo ("  <BR>
  <FORM>
    <TABLE width=\"570\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\" bgcolor=\"".$AgendaBordureTableau."\">
      <TR>
        <TD width=\"370\" class=\"enteteTableau\">".trad("LIBELLE_LIB_INTITULE")."</TD>
        <TD width=\"50\" class=\"enteteTableau\">".trad("LIBELLE_COL_DUREE")."</TD>
        <TD width=\"50\" class=\"enteteTableau\">".trad("LIBELLE_LIB_COULEUR")."</TD>
        <TD width=\"50\" class=\"enteteTableau\">".trad("LIBELLE_LIB_PARTAGE")."</TD>
        <TD width=\"50\" class=\"enteteTableau\">".trad("LIBELLE_COL_ACTION")."</TD>
      </TR>\n");
    $index = 0;
    while ($enr = $DB_CX->DbNextRow()) {
      $index = 1 - $index;
      echo ("    <TR bgcolor=\"".$bgColor[$index]."\" align=\"center\">
      <TD align=\"left\" nowrap>".$enr['lib_nom']."</TD>
      <TD>".(($enr['lib_duree'] > 0) ? sprintf(trad("LIBELLE_MINUTE"), afficheHeure($enr['lib_duree'],$enr['lib_duree'])) : trad("LIBELLE_JOURNEE"))."</TD>
      <TD bgcolor=\"".((!empty($enr['lib_couleur'])) ? $enr['lib_couleur'] : $AgendaFondNotePerso)."\">&nbsp;</TD>
      <TD nowrap>".(($enr['lib_partage'] == "O") ? "x" : "")."</TD>
      <TD align=\"left\" nowrap>&nbsp;");
      if ($enr['lib_util_id']==$idUser || $MODIF_PARTAGE) { // Modif du libelle
        echo "<INPUT type=\"button\" class=\"bouton\" name=\"btModif\" value=\"".trad("LIBELLE_M")."\" title=\"".trad("LIBELLE_BT_MODIFIER")."\" style=\"width:16px\" onclick=\"javascript: window.location.href='?id=".$enr['lib_id']."&tcType="._TYPE_LIBELLE."&sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."';\">&nbsp;";
      }
      if ($enr['lib_util_id']==$idUser) { // Suppression du libelle
        echo "<INPUT type=\"button\" class=\"bouton\" name=\"btSuppr\" value=\"".trad("LIBELLE_S")."\" title=\"".trad("LIBELLE_BT_SUPPRIMER")."\" style=\"width:16px\" onclick=\"javascript: if (confirm('".trad("LIBELLE_JS_CONFIRME_SUPPRIMER")."')) window.location.href='agenda_traitement.php?ztFrom=libelles&ztAction=DELETE&id=".$enr['lib_id']."&sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".date("Y-n-j", $sd)."';\">&nbsp;";
      }
      echo ("</TD>
    </TR>\n");
    }
    echo ("    </TABLE>
  </FORM>\n");
  }
  // Fin MOD Liste des libelles
  if (!$id) {
    echo ("  <SCRIPT type=\"text/javascript\">
  <!--
    document.frmLibelle.ztLibelle.focus();
  //-->
  </SCRIPT>\n");
  }
?>
<!-- FIN MODULE GESTION DES LIBELLES TYPES -->
