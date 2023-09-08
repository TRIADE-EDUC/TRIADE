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

  $id += 0;
  $ztAction = "INSERT";
  $titrePage = trad("EMPL_TITRE_ENREG");
  $createur = $idUser;
  if ($id) {
    //Edition d'un emplacement
    $DB_CX->DbQuery("SELECT empl_nom, empl_partage, empl_type, empl_util_id FROM ${PREFIX_TABLE}emplacement WHERE empl_id=".$id);
    if ($enr = $DB_CX->DbNextRow()) {
      $nom = $enr['empl_nom'];
      $ckPartLieu = $enr['empl_partage'];
      $rdType = $enr['empl_type'];
      $createur = $enr['empl_util_id'];
      $ztAction = "UPDATE";
      $titrePage = trad("EMPL_TITRE_MODIF");
      if ($createur!=$idUser) {
        $titrePage .= " ".trad("LIBELLE_TITRE_PARTAGE");
      }
    }
  }
?>
<!-- MODULE GESTION DES EMPLACEMENTS TYPES -->
  <SCRIPT language="JavaScript">
  <!--
    function saisieOK(theForm) {
      if (trim(theForm.ztLieu.value) == "") {
        window.alert("<?php echo trad("LIBELLE_JS_SAISIR_INTITULE");?>");
        theForm.ztLieu.focus();
        return (false);
      }

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
  <FORM action="agenda_traitement.php" method="post" name="frmEmplacement">
    <INPUT type="hidden" name="sid" value="<?php echo $sid; ?>">
    <INPUT type="hidden" name="sd" value="<?php echo gmdate("Y-n-j", $sd); ?>">
    <INPUT type="hidden" name="id" value="<?php echo $id; ?>">
    <INPUT type="hidden" name="ztFrom" value="emplacement">
    <INPUT type="hidden" name="ztAction" value="<?php echo $ztAction; ?>">
    <INPUT type="hidden" name="tcMenu" value="<?php echo $tcMenu; ?>">
    <INPUT type="hidden" name="tcPlg" value="<?php echo $tcPlg; ?>">
<?php if ($createur!=$idUser) { ?>
    <INPUT type="hidden" name="ckPartLieu" value="O">
<?php } ?>
  <TABLE cellspacing="0" cellpadding="0" width="550" border="0">
  <TR bgcolor="<?php echo $bgColor[1]; ?>" height="21">
    <TD class="tabIntitule" nowrap><?php echo trad("EMPL_LIB_CHOIX");?></TD>
    <TD class="tabInput" nowrap><SELECT name="zlLieu" onchange="javascript: window.location.href='?sid=<?php echo $sid; ?>&tcType=<?php echo _TYPE_EMPL; ?>&tcMenu=<?php echo $tcMenu; ?>&tcPlg=<?php echo $tcPlg; ?>&sd=<?php echo $sd; ?>&id=' + this.value;">
      <OPTION value="0">-- <?php echo trad("EMPL_NOUVEAU_LIB");?> --</OPTION>
<?php
  $l_Req = "SELECT empl_id, empl_nom FROM ${PREFIX_TABLE}emplacement WHERE empl_util_id=".$idUser.(($MODIF_PARTAGE) ? " OR (empl_util_id!=".$idUser." AND empl_partage='O')" : "")." ORDER BY empl_nom";
  $DB_CX->DbQuery($l_Req);
  while ($listEmpl = $DB_CX->DbNextRow()) {
    $selected = ($id == $listEmpl['empl_id']) ? " selected" : "";
    echo "      <OPTION value=\"".$listEmpl['empl_id']."\"".$selected.">".htmlspecialchars($listEmpl['empl_nom'])."</OPTION>\n";
  }
?>
      </SELECT></TD>
  </TR>
  <TR bgcolor="<?php echo $bgColor[0]; ?>" height="21">
    <TD class="tabIntitule" nowrap><?php echo trad("EMPL_LIB_INTITULE");?></TD>
    <TD class="tabInput" nowrap width="474"><INPUT type="text" class="Texte" name="ztLieu" value="<?php echo htmlspecialchars($nom); ?>" style="width:469px" maxlength="150"></TD>
  </TR>
  <TR bgcolor="<?php echo $bgColor[0]; ?>" height="21">
     <TD class="tabIntitule" nowrap><?php echo trad("EMPL_LIB_ICONE");?></TD>
    <TD class="tabInput">
      <?php
        for ($i=0;$i<15;$i++) {
          $selected = ($rdType==$i) ? " checked" : "";
          echo "<INPUT type=\"radio\" name=\"rdType\" value=\"$i\" class=\"Case\"".$selected."><IMG src=\"image/emplacement/puce_".$i.".gif\" border=\"0\" alt=\"\" align=\"absmiddle\" onclick=\"javascript: this.rdType[".$i."].checked=true;\">";
        }
      ?>
    </TD>
  </TR>

<!--  -->
<?php if ($createur==$idUser) { ?>
  <TR bgcolor="<?php echo $bgColor[0]; ?>" height="21">
    <TD class="tabIntitule" nowrap><?php echo trad("EMPL_LIB_PARTAGE");?></TD>
    <TD class="tabInput" nowrap><LABEL for="partageLieu"><INPUT type="checkbox" name="ckPartLieu" id="partageLieu" value="O" class="Case"<?php if ($ckPartLieu=='O') {echo " checked";} ?>>&nbsp;<?php echo trad("EMPL_COCHER_PARTAGE");?></LABEL></TD>
  </TR>
<?php } ?>
  </TABLE>
  <BR><INPUT type="button" name="btEnregistre" value="<?php echo trad("EMPL_BT_ENREGISTRER");?>" onClick="javascript: return saisieOK(document.frmEmplacement);" class="bouton">&nbsp;&nbsp;&nbsp;<INPUT type="button" name="btAnnule" value="<?php echo trad("EMPL_BT_ANNULER");?>" onclick="javascript: btAnnul();" class="bouton"><?php if ($ztAction=="UPDATE" && $createur==$idUser) { ?>&nbsp;&nbsp;&nbsp;<INPUT type="button" name="btSupprime" value="<?php echo trad("EMPL_BT_SUPPRIMER");?>" onclick="javascript: if (confirm('<?php echo trad("EMPL_JS_CONFIRME_SUPPRIMER");?>')) { document.frmEmplacement.ztAction.value='DELETE'; document.frmEmplacement.submit(); }" class="bouton"><?php } ?>
  </FORM>
<?php

  if (!$id) {
    echo ("  <SCRIPT type=\"text/javascript\">
  <!--
    document.frmEmplacement.ztLieu.focus();
  //-->
  </SCRIPT>\n");
  }
?>
<!-- FIN MODULE GESTION DES EMPLACEMENTS TYPES -->
