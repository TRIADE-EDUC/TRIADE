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

  // Quelle page est en cours de traitement ?
  $isGlobalQuot = ($tcMenu==_MENU_PLG_QUOT_GBL);
  $isGlobalHebd = ($tcMenu==_MENU_PLG_HEBDO_GBL);
  $isGlobalMens = ($tcMenu==_MENU_PLG_MENS_GBL);
  $isDispoQuot = ($tcMenu==_MENU_DISP_QUOT);
  $isDispoHebd = ($tcMenu==_MENU_DISP_HEBDO);
  $isPlanningGlobal = ($isGlobalQuot || $isGlobalHebd || $isGlobalMens);
  $isDisponibilite = ($isDispoQuot || $isDispoHebd);
  // Generation de variables pour recuperer les noms complet du createur et du modificateur d'une note
  $NOM_UTIL_CREATEUR = str_replace("util_","t1.util_",$FORMAT_NOM_UTIL);
  $NOM_UTIL_MODIFICATEUR = str_replace("util_","t2.util_",$FORMAT_NOM_UTIL);
?>
  <SCRIPT language="JavaScript" type="text/javascript">
  <!--
<?php
  // Pour les plannings globaux
  if ($isPlanningGlobal) {
    // Pour les plannings globaux quotidiens et hebdomadaires
    if ($isGlobalQuot || $isGlobalHebd) {
?>
    // Changement Taille d'ecran
    function ChangeScreen() {
      var NVScreen = (window.innerWidth) ? window.innerWidth : window.document.body.offsetWidth;
      var OldScreen = '<?php echo $hdScreen; ?>';
      if (NVScreen!=OldScreen) {
        parent.window.frames['trash_<?php echo $sid; ?>'].window.location.href = "agenda_screen.php?sid=<?php echo $sid ?>&Screen="+NVScreen;
      }
    }

<?php
    } // FIN Pour les plannings globaux quotidiens et hebdomadaires
?>

    function mtc1(titre, texte) {
      if (sw!=2) {
        sw=0;
        width = 390;
        layerWrite("<TABLE width="+width+" border=0 cellpadding=0 cellspacing=1 class=\"infoBulle\"><TR><TD><TABLE width=\"100%\" border=0 cellpadding=0 cellspacing=0><TR><TD class=\"ibHeure\" height=\"13\">"+titre+"</TD></TR></TABLE><TABLE width=\"100%\" border=0 cellpadding=0 cellspacing=0><TR><TD class=\"ibTexte\" style=\"padding:0px;\">"+texte+"</TD></TR></TABLE></TD></TR></TABLE>", "infoBulle");
        disp();
      }
    }

    function stc1(titre, texte) {
      if (sw!=2) {
        sw=2;
        cnt=0;
        width = 390;
        layerWrite("<TABLE width="+width+" border=0 cellpadding=0 cellspacing=1 class=\"infoBulle\"><TR><TD><TABLE width=\"100%\" border=0 cellpadding=0 cellspacing=0><TR valign=\"top\"><TD class=\"ibHeure\" nowrap>"+titre+"</TD><TD align=\"right\" class=\"ibTitre\"><A href=\"/\" onClick=\"cClick(); return false;\"><IMG src=\"image/popup_close.gif\" width=\"13\" height=\"13\" alt=\"<?php echo trad("POPUP_FERMER"); ?>\" border=\"0\"></A></TD></TR></TABLE><TABLE width=\"100%\" border=0 cellpadding=0 cellspacing=0><TR><TD class=\"ibTexte\" style=\"padding:0px;\">"+texte+"</TD></TR></TABLE></TD></TR></TABLE>", "infoBulle");
        snow=0;
      }
    }

    // Affiche une note en modification planning global
    function affNoteG(_note,_decalhoraire) {
      var _options = "&ggr="+document.frmChoixGrp.ggr.value+"&ztActionGrp="+document.frmChoixGrp.ztActionGrp.value;
      window.location.href = "agenda.php?sid=<?php echo $sid; ?>&tcType=<?php echo _TYPE_NOTE; ?>&tcMenu=<?php echo $tcMenu; ?>&tcPlg=<?php echo $tcPlg; ?>&sd=<?php echo $sd; ?>&decalH="+ _decalhoraire+"&id="+ _note+_options;
    }

<?php
  } // FIN Pour les plannings globaux
?>
    // Creation d'une nouvelle note tenant compte des fuseaux horaires
    function nvNoteG(_sd,_hD,_hF,_User,_decalhoraire) {
      var _decalH = ((_decalhoraire!="") ? "&decalH="+_User : "");
      var _options = "&ggr="+document.frmChoixGrp.ggr.value+"&ztActionGrp="+document.frmChoixGrp.ztActionGrp.value;
      window.location.href = "agenda.php?sid=<?php echo $sid; ?>&tcType=<?php echo _TYPE_NOTE; ?>&sd="+_sd+"&hD="+_hD+"&hF="+_hF+"&sChoix="+_User+"&tcMenu=<?php echo $tcMenu; ?>&tcPlg=<?php echo $tcPlg; ?>"+_decalH+_options;
    }

    function genereListe(_liste, _tabTexte, _tabValue, _tailleTab) {
      for (var i=0; i<_tailleTab; i++)
        _liste.options[i]=new Option(_tabTexte[i], _tabValue[i]);
    }

    function bubbleSort(_tabText, _tabValue,_tailleTab) {
      var i,s;

      do {
        s=0;
        for (i=1; i<_tailleTab; i++)
          if (_tabText[i-1] > _tabText[i]) {
            y = _tabText[i-1];
            _tabText[i-1] = _tabText[i];
            _tabText[i] = y;
            y = _tabValue[i-1];
            _tabValue[i-1] = _tabValue[i];
            _tabValue[i] = y;
            s = 1;
          }
      } while (s);
    }

    function videListe(_liste) {
      var cpt = _liste.options.length;

      for(var i=0; i<cpt; i++) {
        _liste.options[0] = null;
      }
    }

    function selectUtil(_listeSource, _listeDest) {
      var i,j;
      var ok = false;
      var tabDestTexte = new Array();
      var tabDestValue = new Array();
      var tailleTabDest = 0;

      for (i=0; i<_listeDest.options.length; i++) {
        tabDestTexte[tailleTabDest]   = _listeDest.options[i].text;
        tabDestValue[tailleTabDest++] = _listeDest.options[i].value;
      }

      for (j=_listeSource.options.length-1; j>=0; j--) {
        if (_listeSource.options[j].selected) {
          ok = true;
          tabDestTexte[tailleTabDest]   = _listeSource.options[j].text;
          tabDestValue[tailleTabDest++] = _listeSource.options[j].value;
          _listeSource.options[j] = null;
        }
      }

      if (ok) {
        //Trie du tableau
        bubbleSort(tabDestTexte, tabDestValue, tailleTabDest);
        //Vide la liste destination
        videListe(_listeDest);
        //Recree la liste
        genereListe(_listeDest, tabDestTexte, tabDestValue, tailleTabDest);
      }
    }

    //Fonction pour selectionner tous les utilisateurs d'une liste source et les transferer dans une liste destination
    function selectAll(_listeSource, _listeDest) {
      for (var i=0; i<_listeSource.options.length; i++) {
        _listeSource.options[i].selected = true;
      }
      selectUtil(_listeSource, _listeDest);
    }

    function recupSelection(_liste, _champ) {
      _champ.value = "";
      for (var i=0; i<_liste.options.length; i++) {
        _champ.value += ((i) ? "," : "") + _liste.options[i].value;
      }
    }

    // Affiche ou cache le tableau de selection des utilisateurs
    function affOnglet(_onglet) {
      if (document.getElementById("div"+_onglet).style.display == "block") {
        document.getElementById("div"+_onglet).style.display = "none";
        document.getElementById("img"+_onglet).src='image/down.gif';
      } else {
        document.getElementById("div"+_onglet).style.display = "block";
        document.getElementById("img"+_onglet).src='image/up.gif';
      }
    }

    // Action sur le bouton 'Afficher'
    function saisieOK(theForm) {
      recupSelection(theForm.zlConsulte, theForm.sChoix);
      if (theForm.sChoix.value == "") {
        window.alert("<?php echo trad("PLGL_SELECT_PERSONNE"); ?>");
        theForm.zlUtilisateur.focus();
        return (false);
      }
<?php if ($isGlobalQuot || $isDisponibilite) { ?>
      if (theForm.zlHD.selectedIndex > theForm.zlHF.selectedIndex) {
        window.alert("<?php echo trad("DISPO_SELECT_HEURE");?>");
        theForm.zlHF.focus();
        return (false);
      }
<?php } ?>

      theForm.ztActionGrp.value="NvAff";
      theForm.submit();
      return (true);
    }

    // Action sur le bouton 'Sauvegarder et Afficher'
    function SauvOK(theForm) {
      recupSelection(theForm.zlConsulte, theForm.sChoix);
<?php if ($isGlobalQuot || $isDisponibilite) { ?>
      if (theForm.zlHD.selectedIndex > theForm.zlHF.selectedIndex) {
        window.alert("<?php echo trad("DISPO_SELECT_HEURE");?>");
        theForm.zlHF.focus();
        return (false);
      }
<?php } ?>

      theForm.ztActionGrp.value="SauvPref";
      theForm.action="agenda_traitement.php?sid=<?php echo $sid; ?>&tcMenu=<?php echo $tcMenu; ?>&tcPlg=<?php echo $tcPlg; ?>&sd=<?php echo $sd; ?>";
      theForm.submit();
      return (true);
    }

    var grpWin;
    // Action sur le bouton 'A' ou 'M' pour creer / modifier un groupe
    function AjoutGrp(theForm) {
      var _width = 320, _height = 120;
      var posX = (Math.max(screen.width,_width)-_width)/2;
      var posY = (Math.max(screen.height,_height)-_height)/2;
      var _position = (navigator.appVersion.match('MSIE')) ? ',top=' + posY + ',left=' + posX : ',screenY=' + posY + ',screenX=' + posX;
      recupSelection(theForm.zlConsulte, theForm.sChoix);
      theForm.target = 'AjoutGrp_<?php echo $sid; ?>';
      theForm.action = 'agenda_groupe_global.php?sid=<?php echo $sid; ?>&tcMenu=<?php echo $tcMenu; ?>&tcPlg=<?php echo $tcPlg; ?>&sd=<?php echo $sd; ?>&typegr=<?php echo ($tcMenu!=_MENU_DISP_HEBDO && $tcMenu!=_MENU_DISP_QUOT) ? "0" : "1"; ?>';
      grpWin = window.open('','AjoutGrp_<?php echo $sid; ?>','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0,resizable=1,width=' + _width + ',height=' + _height + _position);
      theForm.submit();
    }

    // Action sur le bouton 'E' pour enregistrer un groupe
    function SauvGrp(theForm) {
      theForm.ztActionGrp.value="SauvGrp";
      theForm.action="agenda_traitement.php?sid=<?php echo $sid; ?>&tcMenu=<?php echo $tcMenu; ?>&tcPlg=<?php echo $tcPlg; ?>&sd=<?php echo $sd; ?>";
      recupSelection(theForm.zlConsulte, theForm.sChoix);
      theForm.submit();
      return (true);
    }

    // Action sur le bouton 'S' pour supprimer un groupe
    function SupGrp(theForm) {
      theForm.ztActionGrp.value="SupGrp";
      theForm.action="agenda_traitement.php?sid=<?php echo $sid; ?>&tcMenu=<?php echo $tcMenu; ?>&tcPlg=<?php echo $tcPlg; ?>&sd=<?php echo $sd; ?>";
      theForm.submit();
      return (true);
    }

    // Choix d'un groupe depuis le tableau de selection des utilisateurs
    function changeGgr(theForm) {
      if (theForm.ggr.value == "0|0") {
        theForm.btAjoutGgr.value = "<?php echo trad("PLGL_BP_ADD"); ?>";
        theForm.btAjoutGgr.title = "<?php echo trad("PLGL_ADD"); ?>";
        selectAll(theForm.zlConsulte, theForm.zlUtilisateur);
        if (theForm.ggr1 != null) {
          theForm.ggr1.selectedIndex = 0;
        }
        return (false);
      }
      theForm.ztActionGrp.value="NvGr";
      theForm.submit();
      return (true);
    }

    // Choix d'un groupe depuis la liste du sous-menu
    function changeGgr1(theForm) {
      theForm.ggr.value = theForm.ggr1.value;
      changeGgr(theForm);
    }
  //-->
  </SCRIPT>

<?php
  // Liste d'acces rapide aux groupes
  $labelBouton = trad('PLGL_BP_ADD');
  $titleBouton = trad('PLGL_ADD');
  if ($ztActionGrp!="NvAff") {
    list ($grpg, $sChoix) = explode ('|', $ggr);
    $DB_CX->DbQuery("SELECT aff_user, aff_figer, aff_precision, aff_debut, aff_fin FROM ${PREFIX_TABLE}planning_affichage WHERE aff_util_id=".$idUser." AND aff_type=".(($isPlanningGlobal) ? "0" : "1")."");
    if ($enr=$DB_CX->DbNextRow()) {
      $ckAffCache = $enr['aff_user'];
      $ckAffGr = $enr['aff_figer'];
      $zlPrec = $enr['aff_precision'];
      $iHeureMin = $enr['aff_debut'];
      $iHeureMax = $enr['aff_fin'];
    }
  }
  if (($ztActionGrp!="NvAff" && $ztActionGrp!="NvGr") || $sChoix=="") {
    $DB_CX->DbQuery("SELECT ggr_id, ggr_liste, ggr_nom FROM ${PREFIX_TABLE}global_groupe WHERE ggr_util_id=".$idUser." AND ggr_aff='O' AND ggr_type=".(($isPlanningGlobal) ? "0" : "1")."");
    if ($DB_CX->DbNumRows()) {
      $grpg = $DB_CX->DbResult(0,0);
      $sChoix = $DB_CX->DbResult(0,1);
      $ztActionGrp = "NvGr";
      if ($DB_CX->DbResult(0,2)!="NoGroup") {
        $labelBouton = trad('PLGL_BP_MOD');
        $titleBouton = trad('PLGL_MOD');
      }
    }
  }
  elseif ($ztActionGrp=="NvGr" && $ggr!="0|0") {
    $labelBouton = trad('PLGL_BP_MOD');
    $titleBouton = trad('PLGL_MOD');
    $DB_CX->DbQuery("SELECT ggr_nom FROM ${PREFIX_TABLE}global_groupe WHERE ggr_id=".$grpg." AND ggr_util_id=".$idUser." AND ggr_nom='NoGroup' AND ggr_type=".(($isPlanningGlobal) ? "0" : "1")."");
    if ($DB_CX->DbNumRows()) {
      if ($DB_CX->DbResult(0,0)=="NoGroup") {
        $labelBouton = trad('PLGL_BP_ADD');
        $titleBouton = trad('PLGL_ADD');
      }
    }
  }
  $tChoix = explode (',', $sChoix);
  $LstUser = array();
  if ($droit_AGENDAS < _DROIT_AGENDA_PARTAGE) {
    $DB_CX->DbQuery("SELECT util_id FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$idUser);
  } else if ($droit_AGENDAS >= _DROIT_AGENDA_TOUS) {
    $DB_CX->DbQuery("SELECT util_id, CONCAT(".$FORMAT_NOM_UTIL.") AS nomUtil FROM ${PREFIX_TABLE}utilisateur LEFT JOIN ${PREFIX_TABLE}planning_affecte ON paf_util_id=util_id LEFT JOIN ${PREFIX_TABLE}planning_partage ON ppl_util_id=util_id WHERE (LENGTH(CONCAT(util_nom, util_prenom)) > 0)");
  } else {
    $DB_CX->DbQuery("SELECT util_id FROM ${PREFIX_TABLE}utilisateur LEFT JOIN ${PREFIX_TABLE}planning_affecte ON paf_util_id=util_id LEFT JOIN ${PREFIX_TABLE}planning_partage ON ppl_util_id=util_id WHERE util_id=".$idUser." OR (util_autorise_affect='1') OR (util_autorise_affect IN ('2','3') AND paf_consultant_id=".$idUser.") OR (util_partage_planning ='1') OR (util_partage_planning='2' AND ppl_consultant_id=".$idUser.")");
  }
  while ($enr=$DB_CX->DbNextRow())
    $LstUser[] = $enr['util_id'];
  $result = array_intersect ($LstUser, $tChoix);
  $sChoix = implode(",", $result);
?>
  <FORM action="?sid=<?php echo $sid; ?>&tcMenu=<?php echo $tcMenu; ?>&tcPlg=<?php echo $tcPlg; ?>&sd=<?php echo $sd; ?>" method="post" name="frmChoixGrp">
  <TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
  <TR>
<?php
  $DB_CX->DbQuery("SELECT ggr_id, ggr_liste FROM ${PREFIX_TABLE}global_groupe WHERE ggr_nom='NoGroup' and ggr_util_id=".$idUser." and ggr_type=".(($isPlanningGlobal) ? "0" : "1")."");
  if ($DB_CX->DbNumRows()) {
    $idNoGroup = $DB_CX->DbResult(0,0);
    $choixNoGroup = $DB_CX->DbResult(0,1);
  } else {
    $idNoGroup = "0";
    $choixNoGroup = "0";
  }
  $DB_CX->DbQuery("SELECT ggr_id, ggr_nom, ggr_liste FROM ${PREFIX_TABLE}global_groupe WHERE ggr_util_id=".$idUser." and ggr_type=".(($isPlanningGlobal) ? "0" : "1")." AND ggr_nom!='NoGroup' ORDER BY ggr_nom");
  if ($DB_CX->DbNumRows()) {
    echo ("    <TD class=\"sousMenu\" style=\"text-align:left;\" nowrap>&nbsp;<SELECT name=\"ggr1\" size=\"1\" onChange=\"javascript: changeGgr1(document.frmChoixGrp);\">
      <OPTION value=\"".$idNoGroup."|".$choixNoGroup."\">(".trad("PLGL_NO_GR").")</OPTION>\n");
    while ($enr = $DB_CX->DbNextRow()) {
      $selected = ($grpg==$enr['ggr_id']) ? " selected" : "";
      echo "      <OPTION value=\"".$enr['ggr_id']."|".$enr['ggr_liste']."\"".$selected.">".$enr['ggr_nom']."</OPTION>\n";
    }
    echo "    </SELECT></TD>\n";
  }
  echo "    <TD height=\"28\" class=\"sousMenu\" width=\"100%\" nowrap>".$titrePage."</TD>\n";
  // Lien pour l'impression dans les disponibilites hebdomadaires
  if ($isDispoHebd && !empty($sChoix))
    echo "    <TD align=\"right\" class=\"sousMenu\" style=\"text-align:right;\" nowrap>&nbsp;<A href=\"javascript: parent.imprime('".$tcMenu."','".$sd."','".urlencode($sChoix."|".$zlPrec."|".$zlHD."|".$zlHF."|".$debutSemaine."|".$finSemaine)."');\"><IMG src=\"image/impression.gif\" alt=\"".trad("DISPOH_IMPRIMER")."\" width=\"23\" height=\"21\" border=\"0\" align=\"absmiddle\"></A>&nbsp;</TD>\n";
  // Filtre couleur pour les plannings globaux
  if ($isPlanningGlobal) {
    //Si l'utilisateur a choisi une couleur de note on l'ajoute dans la clause WHERE de la recherche
    $whereCouleur = "";
    if ($FILTRE_COULEUR != "ALL" && !empty($FILTRE_COULEUR)) {
      $whereCouleur = ($FILTRE_COULEUR == $AgendaFondNotePerso) ? " AND (age_couleur='".$FILTRE_COULEUR."' OR age_couleur='')" : " AND age_couleur='".$FILTRE_COULEUR."'";
    }
    //Construction de la liste des couleurs/categories de notes
    echo "    <TD align=\"right\" class=\"sousMenu\" style=\"text-align:right;\" nowrap>";
    genereListeCouleur();
    echo "</TD>\n";
  }
?>
    <TD width="40" class="sousMenu" nowrap><A href="javascript: cClick(); affOnglet('Info');"><IMG src="image/choix.gif" width="16" height="16" border="0" align="absmiddle" title="<?php echo trad("PLGL_CHOIX_PL"); ?>">&nbsp;<IMG src="image/<?php echo (empty($sChoix) || $ckAffGr=="O") ? "up.gif" : "down.gif"; ?>" width="11" height="14" border="0" align="absmiddle" title="<?php echo trad("PLGL_CHOIX_PL"); ?>" id="imgInfo" ></A></TD>
  </TR>
  </TABLE>
  <BR>
<?php
  if (empty($sChoix) || $ckAffGr=="O")
    echo "  <DIV id=\"divInfo\" style=\"display: block\">\n";
  else
    echo "  <DIV id=\"divInfo\" style=\"display: none\">\n";
?>
  <TABLE cellspacing="0" width="575" cellpadding="0" border="0">
  <TR bgcolor="<?php echo $bgColor[1]; ?>">
    <TD align="center" class="tabIntitule"><span style="font-style: italic;"><?php echo trad("PLGL_LISTE_PERSONNES_1");?><br></span><span style="font-weight: normal;"><?php if ((($isDisponibilite && $ckAffCache=="O") || $isPlanningGlobal) && $droit_NOTES < _DROIT_NOTE_MODIF_CREATION)echo "<br>".trad("PLGL_LISTE_PERSONNES_2");?></span><span style="font-style: italic;font-weight: normal;"><br><?php if ((($isPlanningGlobal && $ckAffCache=="O") || $isDisponibilite) && $droit_AGENDAS < _DROIT_AGENDA_TOUS) echo trad("PLGL_LISTE_PERSONNES_3");?></span><BR></TD>
    <TD class="tabInput" colspan="2" width="436"><TABLE cellspacing="0" cellpadding="0" width="100%" border="0" align="center">
      <TR>
        <TH><?php echo trad("PLGL_PERSONNES_POSSIBLES");?></TH>
        <TH>&nbsp;</TH>
        <TH><?php echo trad("PLGL_PERSONNES_SELECTION");?></TH>
      </TR>
      <TR>
        <TD><SELECT name="zlUtilisateur" id="zlUtilisateur" size="8" multiple style="width:200px;">
<?php
  // Construction de la liste des utilisateurs selectionnables
  if ($droit_AGENDAS < _DROIT_AGENDA_PARTAGE) {
    $DB_CX->DbQuery("SELECT DISTINCT util_id, CONCAT(".$FORMAT_NOM_UTIL.") AS nomUtil FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$idUser);
  } elseif ($droit_AGENDAS >= _DROIT_AGENDA_TOUS) {
    $DB_CX->DbQuery("SELECT DISTINCT util_id, CONCAT(".$FORMAT_NOM_UTIL.") AS nomUtil FROM ${PREFIX_TABLE}utilisateur LEFT JOIN ${PREFIX_TABLE}planning_affecte ON paf_util_id=util_id WHERE (LENGTH(CONCAT(util_nom, util_prenom)) > 0) ORDER BY nomUtil");
  } else {
    if ($ckAffCache!="O") {
      if ($isPlanningGlobal) {
        // Liste des utilisateurs dont on peut consulter le planning
        $DB_CX->DbQuery("SELECT DISTINCT util_id, CONCAT(".$FORMAT_NOM_UTIL.") AS nomUtil FROM ${PREFIX_TABLE}utilisateur LEFT JOIN ${PREFIX_TABLE}planning_partage ON ppl_util_id=util_id WHERE util_id=".$idUser." OR (util_partage_planning='1') OR (util_partage_planning='2' AND ppl_consultant_id=".$idUser.") ORDER BY nomUtil");
      } else {
        // Liste des utilisateurs a qui l'on peut affecter une note
        $DB_CX->DbQuery("SELECT DISTINCT util_id, CONCAT(".$FORMAT_NOM_UTIL.") AS nomUtil FROM ${PREFIX_TABLE}utilisateur LEFT JOIN ${PREFIX_TABLE}planning_affecte ON paf_util_id=util_id WHERE util_id=".$idUser." OR (util_autorise_affect='1') OR (util_autorise_affect IN ('2','3') AND paf_consultant_id=".$idUser.") ORDER BY nomUtil");
      }
    } else {
      // Liste de tous les utilisateurs dont on a acces au planning (consultation et/ou affectation)
      $DB_CX->DbQuery("SELECT DISTINCT util_id, CONCAT(".$FORMAT_NOM_UTIL.") AS nomUtil FROM ${PREFIX_TABLE}utilisateur LEFT JOIN ${PREFIX_TABLE}planning_affecte ON paf_util_id=util_id LEFT JOIN ${PREFIX_TABLE}planning_partage ON ppl_util_id=util_id WHERE util_id=".$idUser." OR (util_autorise_affect='1') OR (util_autorise_affect IN ('2','3') AND paf_consultant_id=".$idUser.") OR (util_partage_planning ='1') OR (util_partage_planning='2' AND ppl_consultant_id=".$idUser.") ORDER BY nomUtil");
    }
  }
  $tabUtil = array();
  $result = array();
  $indT = 0;
  while ($enr=$DB_CX->DbNextRow()) {
    $tabUtil[0][$indT] = $enr['util_id'];
    $tabUtil[1][$indT] = $enr['nomUtil'];
    $indT++;
  }

  //Parcours des resultats et constitution de la liste des utilisateurs
  for ($j=0;$j<$indT;$j++) {
    $DB_CX->DbQuery("SELECT paf_consultant_id FROM ${PREFIX_TABLE}planning_affecte WHERE paf_util_id=".$tabUtil[0][$j]." AND paf_consultant_id=".$idUser);
    $tabUtil[2][$j] = ($DB_CX->DbNumRows()>0);
    $DB_CX->DbQuery("SELECT util_id FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$tabUtil[0][$j]." AND util_autorise_affect='1'");
    if ($DB_CX->DbNumRows()) {
      $tabUtil[2][$j] = true;
    }
    $DB_CX->DbQuery("SELECT ppl_consultant_id FROM ${PREFIX_TABLE}planning_partage WHERE ppl_util_id=".$tabUtil[0][$j]." AND ppl_consultant_id=".$idUser);
    $tabUtil[3][$j] = ($DB_CX->DbNumRows()>0);
    $DB_CX->DbQuery("SELECT util_id FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$tabUtil[0][$j]." AND util_partage_planning='1'");
    if ($DB_CX->DbNumRows()) {
      $tabUtil[3][$j] = true;
    }
  }

  // Liste des personnes selectionnees
  $aChoix = explode(",", $sChoix);

  for ($j=0; $j<$indT; $j++) {
    $selected = "";
    $AffUtil = "";
    if (!$tabUtil[2][$j] && $droit_NOTES < _DROIT_NOTE_MODIF_CREATION)
      $AffUtil = " (*)";
    if (!$tabUtil[3][$j] && $droit_AGENDAS < _DROIT_AGENDA_TOUS)
      $AffUtil .= " (1)";
    if ($AffUtil==" (*) (1)")
      $AffUtil = " (1*)";
    if ($tabUtil[0][$j]==$idUser)
      $AffUtil = "";
    // Recherche si l'utilisateur a ete selectionne
    for ($i=0; $i<count($aChoix) && empty($selected); $i++) {
      if ($aChoix[$i] == $tabUtil[0][$j]) {
        $result[] = $tabUtil[0][$j];
        $selected = " selected";
      }
    }
    echo "          <OPTION value=\"".$tabUtil[0][$j]."\"".$selected.">".htmlspecialchars($tabUtil[1][$j]).$AffUtil."</OPTION>\n";
  }
  $sChoix = implode(",", $result);
?>
        </SELECT></TD>
        <TD align="center" valign="middle"><TABLE border="0" cellpadding="0" cellspacing="0">
          <TR>
            <TD>&nbsp;<INPUT type="button" class="PickList" name="btSelect" id="btSelect" value="&#155;" title="<?php echo trad("PLGL_AJOUT_SELECTION");?>" onClick="javascript: selectUtil(document.frmChoixGrp.zlUtilisateur, document.frmChoixGrp.zlConsulte);">&nbsp;</TD>
          </TR>
          <TR>
            <TD height="7"></TD>
          </TR>
          <TR>
            <TD>&nbsp;<INPUT type="button" class="PickList" name="btSelect" id="btSelect" value="&#187;" title="<?php echo trad("PLGL_AJOUT_TOUS");?>" onClick="javascript: selectAll(document.frmChoixGrp.zlUtilisateur, document.frmChoixGrp.zlConsulte);">&nbsp;</TD>
          </TR>
          <TR>
            <TD height="7"></TD>
          </TR>
          <TR>
            <TD nowrap>&nbsp;<INPUT type="button" class="PickList" name="btDeselect" id="btDeselect" value="&#139;" title="<?php echo trad("PLGL_ENLEVE_SELECTION");?>" onClick="javascript: selectUtil(document.frmChoixGrp.zlConsulte, document.frmChoixGrp.zlUtilisateur);">&nbsp;</TD>
          </TR>
          <TR>
            <TD height="7"></TD>
          </TR>
          <TR>
            <TD nowrap>&nbsp;<INPUT type="button" class="PickList" name="btDeselect" id="btDeselect" value="&#171;" title="<?php echo trad("PLGL_ENLEVE_TOUS");?>" onClick="javascript: selectAll(document.frmChoixGrp.zlConsulte, document.frmChoixGrp.zlUtilisateur);">&nbsp;</TD>
          </TR>
        </TABLE></TD>
        <TD><SELECT name="zlConsulte" id="zlConsulte" size="8" multiple style="width:200px"></SELECT></TD>
      </TR>
    </TABLE><INPUT type="hidden" name="sChoix" value=""></TD>
  </TR>
  <TR bgcolor="<?php echo $bgColor[0]; ?>" height="21">
<?php
  // Recuperation des parametres de precision de l'utilisateur connecte
  if ($zlPrec!=4 && $zlPrec!=2) {
    $DB_CX->DbQuery("SELECT util_debut_journee, util_fin_journee, util_precision_planning FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$idUser);
    $iHeureMin = $DB_CX->DbResult(0,0);
    $iHeureMax = $DB_CX->DbResult(0,1);
    $zlPrec = $DB_CX->DbResult(0,2) * 2;
  }
  // Choix des horaires pour le planning global quotidien et les dispos
  if (!empty($sChoix)) {
    if ($isPlanningGlobal) {
      $iHeureMin = "24";
      $iHeureMax = "0";
    }
    $AzlPrec = "0";
    $SemaineTypeTotal="0000000";
    // Tableau contenant les id => nom des utilisateurs
    $aUtilPartage = array();
    // Tableau des heures de debut de journee pour chaque utilisateur
    $aHeureDebutJourneeUtil = array();
    $aHeureFinJourneeUtil = array();
    // Tableau contenant les infos de timezone
    $aUtiltzLibelle = array();
    $aUtiltzGmt = array();
    $aUtiltzDateEte = array();
    $aUtiltzHeureEte = array();
    $aUtiltzDateHiver = array();
    $aUtiltzHeureHiver = array();

    $DB_CX->DbQuery("SELECT util_id, CONCAT(".$FORMAT_NOM_UTIL.") AS nomUtil, util_debut_journee, util_fin_journee, util_precision_planning, util_semaine_type, tzn_libelle, tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver, util_format_heure FROM ${PREFIX_TABLE}utilisateur, ${PREFIX_TABLE}timezone WHERE util_id IN (".$sChoix.") AND tzn_zone=util_timezone ORDER BY nomUtil");
    while ($enr = $DB_CX->DbNextRow()) {
      // Recuperation des infos de timezone de l'utilisateur
      $aUtiltzLibelle[$enr['util_id']] = htmlentities($enr['tzn_libelle']);
      $aUtiltzGmt[$enr['util_id']] = $enr['tzn_gmt'];
      $aUtiltzDateEte[$enr['util_id']] = $enr['tzn_date_ete'];
      $aUtiltzHeureEte[$enr['util_id']] = $enr['tzn_heure_ete'];
      $aUtiltzDateHiver[$enr['util_id']] = $enr['tzn_date_hiver'];
      $aUtiltzHeureHiver[$enr['util_id']] = $enr['tzn_heure_hiver'];
      // Recuperation de l'heure de debut de journee minimale et de l'heure de fin de journee maximale selectionnees par l'utilisateur
      $aHeureDebutJourneeUtil[$enr['util_id']] = $enr['util_debut_journee'];
      $aHeureFinJourneeUtil[$enr['util_id']] = $enr['util_fin_journee'];
      // Convertion des heures dans le fuseau de l'utilisateur en cours
      if ($tzPartage!="O" || $isDisponibilite) {
        $hdeb = $aHeureDebutJourneeUtil[$enr['util_id']];
        $hfin = $aHeureFinJourneeUtil[$enr['util_id']];
        $gmt = $aUtiltzGmt[$enr['util_id']];
        $dEte = $aUtiltzDateEte[$enr['util_id']];
        $hEte = $aUtiltzHeureEte[$enr['util_id']];
        $dHiver = $aUtiltzDateHiver[$enr['util_id']];
        $hHiver = $aUtiltzHeureHiver[$enr['util_id']];
        $dateCrt = $anneeEnCours."-".$moisEnCours."-".$jourEnCours;
        list($hdeb,$hfin,$dCrt,$dMdf,$date) = decaleNote($gmt,$dEte,$hEte,$dHiver,$hHiver,$dateCrt,$dateCrt,$hdeb,$hfin,$dCrt,$dMdf,0,1);
        list($hdeb,$hfin,$dCrt,$dMdf,$date) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$dateCrt,$dateCrt,$hdeb,$hfin,$dCrt,$dMdf,0,0);
        $aHeureDebutJourneeUtil[$enr['util_id']] = $hdeb;
        $aHeureFinJourneeUtil[$enr['util_id']] = $hfin;
      }
      if ($isGlobalQuot || $isDisponibilite) {
        if ($isGlobalQuot) {
          $iHeureMin = min($aHeureDebutJourneeUtil[$enr['util_id']],$iHeureMin);
          $iHeureMax = max($aHeureFinJourneeUtil[$enr['util_id']],$iHeureMax);
        }
        $vSemaineTypeU = substr($enr['util_semaine_type'],6).substr($enr['util_semaine_type'],0,6); // Semaine type mappee au format PHP (L->D => D->S)
        if (substr($vSemaineTypeU,date("w",$sd),1)=="0") {
          // Journee hors profil semaine type => indisponibilite toute la journee
          $aHeureDebutJourneeUtil[$enr['util_id']] = $aHeureFinJourneeUtil[$enr['util_id']];
        }
      } elseif ($isGlobalHebd) {
        $iHeureMin = floor(min($aHeureDebutJourneeUtil[$enr['util_id']],$iHeureMin));
        $iHeureMax = ceil(max($aHeureFinJourneeUtil[$enr['util_id']],$iHeureMax));
        $SemaineType = bindec($enr['util_semaine_type']);
        $SemaineType |= bindec($SemaineTypeTotal);
        $SemaineTypeTotal = decbin($SemaineType);
        $vSemaineType[$enr['util_id']] = $enr['util_semaine_type']; // Semaine type mappee au format PHP (L->D => D->S)
      } elseif ($isGlobalMens) {
        $iHeureMin = floor(min($aHeureDebutJourneeUtil[$enr['util_id']],$iHeureMin));
        $iHeureMax = ceil(max($aHeureFinJourneeUtil[$enr['util_id']],$iHeureMax));
        $vSemaineType[$enr['util_id']] = substr($enr['util_semaine_type'],6).substr($enr['util_semaine_type'],0,6); // Semaine type mappee au format PHP (L->D => D->S)
      }
      $AzlPrec = max($enr['util_precision_planning'],$AzlPrec);
      // Info sur les utilisateurs selectionnes
      $aUtilPartage[$enr['util_id']] = $enr['nomUtil'];
    }

    if ($zlPrec=="")
      $zlPrec = $AzlPrec*2;

    // Recuperation de l'heure de debut de journee minimale et de l'heure de fin de journee maximale selectionnees par l'utilisateur
    if ($isGlobalQuot || $isGlobalMens) {
      if ($zlHD!="" && $ztActionGrp=="NvAff") {
        $iHeureMin = $zlHD;
      } else {
        $zlHD = $iHeureMin;
      }
      if ($zlHF!="" && $ztActionGrp=="NvAff") {
        $iHeureMax = $zlHF;
      } else {
        $zlHF = $iHeureMax;
      }
    } elseif ($isGlobalHebd) {
      if ($zlHD!="") {
        $iHeureMin = floor($zlHD);
      } else {
        $zlHD = floor($iHeureMin);
      }
      if ($zlHF!="") {
        $iHeureMax = ceil($zlHF);
      } else {
        $zlHF = ceil($iHeureMax);
      }
    } elseif ($isDisponibilite) {
      if ($zlHD!="") {
        $iHeureMin = $zlHD;
      } else {
        $zlHD = $iHeureMin;
      }
      if ($zlHF!="") {
        $iHeureMax = $zlHF;
      } else {
        $zlHF = $iHeureMax;
      }
    }
  }
  if ($isGlobalQuot || $isDisponibilite) {
?>
    <TD class="tabIntitule"><?php echo trad("PLGL_PRECISION_AFFICHAGE");?></TD>
    <TD class="tabInput" colspan="2"><SELECT name="zlPrec" size="1"><OPTION value="2"<?php if ($zlPrec!="4") echo " selected"; ?>><?php echo trad("PLGL_PRECISION_30MN");?></OPTION><OPTION value="4"<?php if ($zlPrec=="4") echo " selected"; ?>><?php echo trad("PLGL_PRECISION_15MN");?></OPTION></SELECT>
      &nbsp;<B><?php echo trad("PLGL_HEURE_DEBUT");?></B>&nbsp;<SELECT name="zlHD">
<?php
  if ($iHeureMin=="") {
    $iHeureMin = $zlHD;
  }
  for ($i=0; $i<24;$i=$i+0.5) {
    $selected = ($i == $iHeureMin) ? " selected" : "";
    echo "      <OPTION value=\"".$i."\"".$selected.">".afficheHeure($i,$i,$formatHeure)."</OPTION>\n";
  }
?>
      </SELECT>
      &nbsp;<B><?php echo trad("PLGL_HEURE_FIN");?></B>&nbsp;<SELECT name="zlHF">
<?php
  if ($iHeureMax=="") {
    $iHeureMax = $zlHF;
  }
  for ($i=0.5; $i<=24;$i=$i+0.5) {
    $selected = ($i == $iHeureMax) ? " selected" : "";
    echo "      <OPTION value=\"".$i."\"".$selected.">".afficheHeure($i,$i,$formatHeure)."</OPTION>\n";
  }
?>
    </SELECT></TD>
  </TR>
  <TR bgcolor="<?php echo $bgColor[1]; ?>" height="21">
<?php
  }
  // FIN Choix des horaires pour le planning global mensuel et les dispos
?>
    <TD class="tabIntitule"><?php echo trad('PLGL_CHOIX_GR'); ?></TD>
<?php
  echo ("    <TD class=\"tabInput\" width=\"370\" ><SELECT name=\"ggr\" size=\"1\" onChange=\"javascript: changeGgr(document.frmChoixGrp);\">
      <OPTION value=\"".$idNoGroup."|".$choixNoGroup."\">(".trad('PLGL_NO_GR').")</OPTION>\n");
  $DB_CX->DbQuery("SELECT ggr_id, ggr_nom, ggr_liste FROM ${PREFIX_TABLE}global_groupe WHERE ggr_util_id=".$idUser." and ggr_type=".(($isPlanningGlobal) ? "0" : "1")." ORDER BY ggr_nom");
  while ($enr = $DB_CX->DbNextRow()) {
    if ($enr['ggr_nom']!="NoGroup") {
      $selected = ($grpg == $enr['ggr_id']) ? " selected" : "";
      echo "      <OPTION value=\"".$enr['ggr_id']."|".$enr['ggr_liste']."\"".$selected.">".$enr['ggr_nom']."</OPTION>\n";
    }
  }
  echo ("    </SELECT></TD>
    <TD class=\"tabInput\" width=\"66\"  nowrap>&nbsp;<INPUT type=\"button\" class=\"bouton\" name=\"btModifGgr\" value=\"".trad('PLGL_BP_ENR')."\" title=\"".trad('PLGL_ENR')."\" style=\"width:16px\" onclick=\"javascript: SauvGrp(document.frmChoixGrp);\">&nbsp;<INPUT type=\"button\" class=\"bouton\" name=\"btAjoutGgr\" value=\"".$labelBouton."\" title=\"".$titleBouton."\" style=\"width:16px\" onclick=\"javascript: AjoutGrp(document.frmChoixGrp);\">&nbsp;<INPUT type=\"button\" class=\"bouton\" name=\"btSupprGgr\" value=\"".trad('PLGL_BP_SUP')."\" title=\"".trad('PLGL_SUP')."\" style=\"width:16px\" onclick=\"javascript: if (confirm('".trad('PLGL_CONF_SUP')."')) SupGrp(document.frmChoixGrp);\">&nbsp;</TD>\n");
?>
  </TR>
  </TABLE>
  <BR><INPUT class="bouton" type="button" value="<?php echo trad("PLGL_AFFICHER");?>" onClick="javascript: return saisieOK(document.frmChoixGrp);">&nbsp;&nbsp;&nbsp;&nbsp;
  <INPUT class="bouton" type="button" name="SauvParam" value="<?php echo trad("PLGL_SAUV"); ?>" title="" onClick="javascript: return SauvOK(document.frmChoixGrp);">
  &nbsp;&nbsp;&nbsp;<LABEL for="AffCache"><INPUT type="checkbox" name="ckAffCache" value="O" class="Case" id="AffCache" <?php if ($ckAffCache=="O") echo " checked"; ?>><FONT color="<?php echo $AgendaLegende; ?>">&nbsp;<?php echo ($isPlanningGlobal) ? trad("PLGL_CONSULT") : trad("PLGL_AFFECT"); ?></FONT></LABEL>
  &nbsp;&nbsp;&nbsp;&nbsp;<LABEL for="AffGr"><INPUT type="checkbox" name="ckAffGr" value="O" class="Case" id="AffGr" <?php if ($ckAffGr=="O") echo " checked"; ?>><FONT color="<?php echo $AgendaLegende; ?>">&nbsp;<?php echo trad('PLGL_FIGER'); ?></FONT></LABEL>
  <INPUT type="hidden" name="ztActionGrp" value="<?php echo $ztActionGrp; ?>">
  </DIV>
  </FORM>

