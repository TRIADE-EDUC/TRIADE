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
  ?> <SCRIPT> HelpPhenixCtx="{770FEB23-1593-43A7-8822-7313D62D86CC}.htm"; </SCRIPT> <?php
  // Mod Aide

  $id += 0;
  if (isset($ztNomGroupe) && !empty($ztNomGroupe)) {
    //Enregistrement d'un groupe depuis le popup
    if ($groupe != "0") {
      //UPDATE
      $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}favoris_groupe SET fgr_nom='".htmlspecialchars($ztNomGroupe)."' WHERE fgr_id=".$groupe);
      $labelBouton = trad("FAVORIS_BT_MODIFIER");
    } else {
      //INSERT
      $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}favoris_groupe (fgr_util_id, fgr_nom) VALUES (".$idUser.", '".htmlspecialchars($ztNomGroupe)."')");
      $groupe = $DB_CX->DbInsertID();
      $labelBouton = trad("FAVORIS_BT_MODIFIER");
    }
    if ($id)
      $titrePage = trad("FAVORIS_TITRE_MODIF");
    else
      $titrePage = trad("FAVORIS_TITRE_ENREG");
    //Recuperation des info deja saisies
    $nom         = htmlspecialchars(stripslashes($nom));
    $url         = htmlspecialchars(stripslashes($url));
    $commentaire = ($AUTORISE_HTML) ? stripslashes($commentaire) : htmlspecialchars(stripslashes($commentaire));
  } else {
    $action = "INSERT";
    $titrePage = trad("FAVORIS_TITRE_ENREG");
    $createur = $idUser;
    $groupe = 0;
    $labelBouton = trad("FAVORIS_BT_AJOUTER");
    if ($id) {
      // Edition d'un favori
      $DB_CX->DbQuery("SELECT fav_nom, fav_url, fav_commentaire, fav_partage, fav_util_id, fav_fgr_id FROM ${PREFIX_TABLE}favoris WHERE fav_id=".$id);
      if ($enr = $DB_CX->DbNextRow()) {
        $nom = $enr['fav_nom'];
        $url = $enr['fav_url'];
        $commentaire = $enr['fav_commentaire'];
        $partage = $enr['fav_partage'];
        $createur = $enr['fav_util_id'];
        $groupe = $enr['fav_fgr_id'];
        $labelBouton = trad("FAVORIS_BT_MODIFIER");
        $action = "UPDATE";
        $titrePage = trad("FAVORIS_TITRE_MODIF");
        if ($createur!=$idUser) {
          $titrePage .= " ".trad("FAVORIS_TITRE_PARTAGE");
        }
      } else  {
        $id = 0;
      }
    }
  }
?>
<!-- MODULE FAVORIS -->
  <SCRIPT language="JavaScript" type="text/javascript">
  <!--
    var grpWin;
    // Fenetre de gestion des groupes de favoris
    function ajoutGrp(theForm) {
      var _width = 320, _height = 90;
      var posX = (Math.max(screen.width,_width)-_width)/2;
      var posY = (Math.max(screen.height,_height)-_height)/2;
      var _position = (navigator.appVersion.match('MSIE')) ? ',top=' + posY + ',left=' + posX : ',screenY=' + posY + ',screenX=' + posX;

      theForm.target = 'ajoutGrp_<?php echo $sid; ?>';
      theForm.action = 'agenda_favoris_groupe.php?sid=<?php echo $sid; ?>&tcMenu=<?php echo $tcMenu; ?>&tcPlg=<?php echo $tcPlg; ?>&sd=<?php echo $sd; ?>';
      grpWin = window.open('','ajoutGrp_<?php echo $sid; ?>','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0,resizable=1,width=' + _width + ',height=' + _height + _position);
      PrepareSave()
      theForm.submit();
    }

    // Change le label du bouton de gestion des groupes
    function changeLabel(theForm) {
      theForm.btAjouter.value = (theForm.zlGroupe.value == "0") ? "<?php echo trad("FAVORIS_BT_AJOUTER");?>" : "<?php echo trad("FAVORIS_BT_MODIFIER");?>";
    }

    // Controle de saisie d'un favori
    function saisieOK(theForm) {
      if (trim(theForm.ztNom.value) == "") {
        window.alert("<?php echo trad("FAVORIS_JS_SAISIR_NOM");?>");
        theForm.ztNom.focus();
        return (false);
      }
      var _url = trim(theForm.ztURL.value);
      if (_url == "") {
        window.alert("<?php echo trad("FAVORIS_JS_SAISIR_URL");?>");
        theForm.ztURL.focus();
        return (false);
      }
      if (theForm.zlGroupe.selectedIndex == 0) {
        window.alert("<?php echo trad("FAVORIS_JS_SELECTIONNER_GROUPE");?>");
        theForm.zlGroupe.focus();
        return (false);
      }
<?php if (!$id) { ?>
      // Controle la validite d'une URL
      var _regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
      if (!_regexp.test(_url) && (_url.substr(0,5)!="http:") && (_url.substr(0,6)!="https:") && (_url.substr(0,4)!="ftp:")) {
        theForm.ztURL.value = "http://"+_url;
      }
<?php } ?>

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
  <FORM action="agenda_traitement.php?sid=<?php echo $sid; ?>&tcMenu=<?php echo $tcMenu; ?>&tcPlg=<?php echo $tcPlg; ?>&sd=<?php echo date("Y-n-j", $sd); ?>" method="post" name="FormFavoris">
    <INPUT type="hidden" name="id" value="<?php echo $id; ?>">
    <INPUT type="hidden" name="ztFrom" value="favoris">
    <INPUT type="hidden" name="ztAction" value="<?php echo $action; ?>">
    <INPUT type="hidden" name="ztCreateur" value="<?php echo $createur; ?>">
    <INPUT type="hidden" name="openFavGrp" value="<?php echo ($openFavGrp+0); ?>">
<?php if ($createur!=$idUser) { ?>
    <INPUT type="hidden" name="zlGroupe" value="<?php echo $groupe; ?>">
    <INPUT type="hidden" name="ckPartage" value="O">
<?php } ?>
    <TABLE width="560" border="0" cellspacing="0" cellpadding="0">
    <TR bgcolor="<?php echo $bgColor[1]; ?>" height="21">
      <TD class="tabIntitule" width="85" nowrap><?php echo trad("FAVORIS_LIB_NOM");?></TD>
      <TD class="tabInput" nowrap width="475"><INPUT type="text" class="Texte" name="ztNom" value="<?php echo htmlspecialchars(stripslashes($nom)); ?>" style="width:469px" size="90" maxlength="150"></TD>
    </TR>
    <TR bgcolor="<?php echo $bgColor[0]; ?>">
      <TD class="tabIntitule"><?php echo trad("FAVORIS_LIB_URL");?></TD>
      <TD class="tabInput" nowrap><TEXTAREA name="ztURL" cols="52" rows="2" wrap="soft" style="width:469px;"><?php echo htmlspecialchars(stripslashes($url)); ?></TEXTAREA></TD>
    </TR>
    <TR bgcolor="<?php echo $bgColor[1]; ?>">
      <TD class="tabIntitule"><?php echo trad("FAVORIS_LIB_COMMENTAIRE");?></TD>
      <TD class="tabInput" nowrap><?php genereTextArea("ztCommentaire",$commentaire,469,7); ?></TEXTAREA></TD>
    </TR>
<?php
  if ($createur==$idUser) {
    echo ("    <TR bgcolor=\"".$bgColor[0]."\" height=\"21\">
      <TD class=\"tabIntitule\">".trad("FAVORIS_LIB_GROUPE")."</TD>
      <TD class=\"tabInput\"><SELECT name=\"zlGroupe\" size=\"1\" onChange=\"javascript: changeLabel(document.FormFavoris);\">
        <OPTION value=\"0\">".trad("FAVORIS_NOUVEAU_GROUPE")."</OPTION>\n");
    $DB_CX->DbQuery("SELECT fgr_id, fgr_nom FROM ${PREFIX_TABLE}favoris_groupe WHERE fgr_util_id=".$idUser." ORDER BY fgr_nom");
    while ($enr = $DB_CX->DbNextRow()) {
      $selected = ($groupe == $enr['fgr_id']) ? " selected" : "";
      echo "        <OPTION value=\"".$enr['fgr_id']."\"".$selected.">".$enr['fgr_nom']."</OPTION>\n";
    }
    echo ("      </SELECT>&nbsp;&nbsp;<INPUT type=\"button\" class=\"bouton\" name=\"btAjouter\" value=\"".$labelBouton."\" onclick=\"javascript: ajoutGrp(document.FormFavoris);\"></TD>
    </TR>
    <TR bgcolor=\"".$bgColor[1]."\" height=\"21\">
      <TD class=\"tabIntitule\">".trad("FAVORIS_LIB_PARTAGE")."</TD>
      <TD class=\"tabInput\" nowrap><LABEL for=\"partageFav\"><INPUT type=\"checkbox\" name=\"ckPartage\" id=\"partageFav\" value=\"O\" class=\"Case\"".(($partage=='O') ? " checked" : "").">&nbsp;".trad("FAVORIS_COCHER_PARTAGE")."</LABEL></TD>
    </TR>\n");
  }
?>
    </TABLE>
    <BR><INPUT type="button" name="btEnregistre" value="<?php echo trad("FAVORIS_BT_ENREGISTRER");?>" onClick="javascript: return saisieOK(document.FormFavoris);" class="bouton">&nbsp;&nbsp;&nbsp;<INPUT type="button" name="btAnnule" value="<?php echo trad("FAVORIS_BT_ANNULER");?>" onclick="javascript: btAnnul();" class="bouton"><?php if ($action=="UPDATE" && $createur==$idUser) { ?>&nbsp;&nbsp;&nbsp;<INPUT type="button" name="btSupprime" value="<?php echo trad("FAVORIS_BT_SUPPRIMER");?>" onclick="javascript: if (confirm('<?php echo trad("FAVORIS_JS_CONFIRME_SUPPRIMER");?>')) { document.FormFavoris.ztAction.value='DELETE'; document.FormFavoris.submit(); }" class="Bouton"><?php } ?>
  </FORM>
  <BR>
  <FORM>
    <TABLE width="560" border="0" cellspacing="1" cellpadding="0" bgcolor="<?php echo $AgendaBordureTableau; ?>" style="border-collapse:separate;">
<?php
  //Liste des differents favoris
  $DB_CX->DbQuery("SELECT DISTINCT fav_id, fav_nom, fav_url, fav_commentaire, fav_util_id, fav_fgr_id, fgr_nom FROM ${PREFIX_TABLE}favoris, ${PREFIX_TABLE}favoris_groupe WHERE (fav_util_id=".$idUser." OR (fav_util_id!=".$idUser." AND fav_partage='O')) AND fgr_id=fav_fgr_id ORDER BY fgr_nom, fav_nom ASC");
  $index = 0;
  $idGroupeCrt = 0;
  $nomGroupeCrt = "";
  $borderTop = false;
  while ($enr = $DB_CX->DbNextRow()) {
    // Nom du groupe de favoris
    if ($nomGroupeCrt != $enr['fgr_nom']) {
      if ($idGroupeCrt>0) {
        // Ce n'est pas le premier enregistrement lu -> on ferme le tableau du groupe precedent
        echo ("      </TABLE></DIV></TD></TR>\n");
      }
      $idGroupeCrt = $enr['fav_fgr_id'];
      $nomGroupeCrt = $enr['fgr_nom'];
      $index = 0;
      // Pour la presentation, le premier favoris du groupe n'a pas de bordure "haute"
      $borderTop = false;
      if ($openFavGrp==$idGroupeCrt) {
        if (file_exists("skins/".$APPLI_STYLE."/collapse_fav.gif")) {
          $imgFav = "skins/".$APPLI_STYLE."/collapse_fav.gif";
          $pathFav = "skins/".$APPLI_STYLE."/";
        } else {
          $imgFav = "image/collapse_fav.gif";
          $pathFav = "image/";
        }
        $dispFav = "displayBlock";
      } else {
        if (file_exists("skins/".$APPLI_STYLE."/expand_fav.gif")) {
          $imgFav = "skins/".$APPLI_STYLE."/expand_fav.gif";
          $pathFav = "skins/".$APPLI_STYLE."/";
        } else {
          $imgFav = "image/expand_fav.gif";
          $pathFav = "image/";
        }
        $dispFav = "displayNone";
      }
      echo ("    <TR height=\"15\" onclick=\"javascript:affListe(document.FormFavoris['openFavGrp'],'favorisGrp','imageGrp','".$idGroupeCrt."','".$pathFav."');\" style=\"cursor:pointer;\"><TD width=\"100%\" bgcolor=\"".$AgendaFavorisFond."\"><TABLE cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" class=\"paddingDG3\"><TR><TD align=\"center\" width=\"100%\"><B><A class=\"MemoFavorisTitre\">".$enr['fgr_nom']."</A></B></TD><TD align=\"right\" nowrap><IMG id='imageGrp".$idGroupeCrt."' src=\"".$imgFav."\" width=\"7\" height=\"4\" alt=\"\" border=\"0\">&nbsp;</TD></TR></TABLE></TD></TR>
      <TR><TD colspan=\"2\"><DIV id=\"favorisGrp".$idGroupeCrt."\" class=\"".$dispFav."\"><TABLE cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" class=\"paddingDG3\">\n");
    }
    $index = 1 - $index;
    $lCommentaire = (!empty($enr['fav_commentaire'])) ? "<BR><BR><U>".trad("FAVORIS_LIB_COMMENTAIRE")."</U> :<BR>".nlTObr($enr['fav_commentaire']) : "";
    // Mise en forme de l'url sur plusieurs lignes
    for ($i=1;strlen(substr($enr['fav_url'],$i*80))>0;$i++) {
      $enr['fav_url'] = substr($enr['fav_url'],0,$i*80)."\n".substr($enr['fav_url'],$i*80);
    }
    echo ("    <TR bgcolor=\"".$bgColor[$index]."\">
      <TD width=\"420\"".(($borderTop) ? " class=\"bordT\"" : "")."><B>".$enr['fav_nom']."</B></TD>
      <TD width=\"50\"".(($borderTop) ? " class=\"bordT\"" : "")." rowspan=\"2\" nowrap>&nbsp;");
    if ($enr['fav_util_id']==$idUser || $MODIF_PARTAGE) { // Modif du favori
      echo "<INPUT type=\"button\" class=\"bouton\" name=\"btModif\" value=\"".trad("FAVORIS_BT_M")."\" title=\"".trad("FAVORIS_MODIFIER")."\" style=\"width:16px\" onclick=\"javascript: window.location.href='?id=".$enr['fav_id']."&tcType="._TYPE_FAVORIS."&sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."&openFavGrp=".$idGroupeCrt."';\">&nbsp;";
    }
    if ($enr['fav_util_id']==$idUser) { // Suppression du favori
      echo "<INPUT type=\"button\" class=\"bouton\" name=\"btSuppr\" value=\"".trad("FAVORIS_BT_S")."\" title=\"".trad("FAVORIS_BT_SUPPRIMER")."\" style=\"width:16px\" onclick=\"javascript: if (confirm('".trad("FAVORIS_JS_CONFIRME_SUPPRIMER")."')) window.location.href='agenda_traitement.php?ztFrom=favoris&ztAction=DELETE&id=".$enr['fav_id']."&sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".date("Y-n-j", $sd)."&openFavGrp=".$idGroupeCrt."';\">&nbsp;";
    }
    echo ("</TD>
    </TR>
    <TR bgcolor=\"".$bgColor[$index]."\">
      <TD>".nlTObr($enr['fav_url']).$lCommentaire."</TD>
    </TR>
    <TR bgcolor=\"".$bgColor[$index]."\">\n      <TD height=\"1\" colspan=\"2\"><IMG src=\"image/trans.gif\" height=\"1\" alt=\"\" border=\"0\"></TD>\n    </TR>\n");
    // Pour la presentation, les autres favoris du groupe ont une bordure "haute"
    $borderTop = true;
  }
  // On ferme le tableau du groupe precedent
  echo ("      </TABLE></DIV></TD></TR>\n");
?>
    </TABLE>
  </FORM>
<?php
  if (!$id) {
    echo ("  <SCRIPT type=\"text/javascript\">
  <!--
    document.FormFavoris.ztNom.focus();
  //-->
  </SCRIPT>\n");
  }
?>
<!-- FIN MODULE FAVORIS -->
