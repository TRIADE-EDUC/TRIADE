<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_MemoProgress_plus.txt ?>
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

  $moisAvant = ($moisEnCours != 1) ? ($moisEnCours-1)."','".$anneeEnCours : "12','".($anneeEnCours-1);
  $moisApres = ($moisEnCours != 12) ? ($moisEnCours+1)."','".$anneeEnCours : "1','".($anneeEnCours+1);
  $anneeAvant = $moisEnCours."','".($anneeEnCours-1);
  $anneeApres = $moisEnCours."','".($anneeEnCours+1);

  $premierJour = date("w",mktime(0,0,0,$moisEnCours, 1, $anneeEnCours));

  $tabJourFerie = getListeJourFerie($anneeEnCours);

  if ($premierJour == 0)
    $premierJour = 7;

  // Le calendrier journalier affiche les jours de la semaine type d'une couleur differente sauf si la semaine type est vide (0000000) ou complete (1111111) dans ces cas on affiche les week-end d'une couleur differente
  $SEMAINE_CALENDRIER = ($SEMAINE_TYPE!="1111111" && $SEMAINE_TYPE!="0000000") ? $SEMAINE_TYPE : "1111100";
?>
<!-- MODULE CALENDRIER -->
  <DIV id="sel_mois" style="z-index:100;top:89px;left:<?php echo ($RELOAD_CALENDAR) ? "11" : "6"; ?>px;position:absolute;background:<?php echo $ListeChoixFond; ?>;border: 1px solid <?php echo $AgendaBordureTableau; ?>;display:none;" onmouseover="javascript:clearTimeout(window.div_timer);" onmouseout="javascript:retardeLancement('cacheListe()',100);"></DIV>
  <DIV id="sel_annee" style="z-index:100;top:89px;left:<?php echo ($RELOAD_CALENDAR) ? "81" : "70"; ?>px;position:absolute;background:<?php echo $ListeChoixFond; ?>;border: 1px solid <?php echo $AgendaBordureTableau; ?>;display:none;" onmouseover="javascript:clearTimeout(window.div_timer);" onmouseout="javascript:retardeLancement('cacheListe()',100);"></DIV>
  <TABLE width="133" border="0" cellspacing="0" cellpadding="0" bgcolor="<?php echo $CalFondBandeauGauche; ?>">
  <TR>
    <TD><FORM name="calForm"><TABLE cellspacing="0" cellpadding="0" width="133" border="0" bgcolor="<?php echo $CalMoisNavigationCelFond; ?>">
      <TR align="center">
        <TD width="0%" align="left" nowrap height="28"><A href="javascript: affMois('<?php echo $moisAvant; ?>','<?php echo $tcMenu; ?>');" class="calFlecheAnnee" title="<?php echo trad("CALENDRIER_MOIS_PREC"); ?>">&laquo;</A></TD>
        <TD><INPUT type="text" name="ztCalMois2" value="<?php echo $tabMois[intval($moisEnCours)]; ?>" class="CalTexte" style="width:57px; cursor:pointer;" readonly onClick="javascript:affMois(document.calForm['ztCalMois'].value,document.calForm['ztCalAnnee'].value,'<?php echo ($tcPlg==4||$tcPlg==5||$tcPlg==6) ? "4" : "2"; ?>');" onmouseover="javascript:this.style.color='<?php echo $CalMoisNavigationTexteHover; ?>';retardeLancement('showListe(\'sel_mois\',\'ztCalMois\')',500);" onmouseout="javascript:this.style.color='<?php echo $CalMoisNavigationTexte; ?>';retardeLancement('cacheListe()',100);"><INPUT type="hidden" name="ztCalMois" value="<?php echo intval($moisEnCours); ?>"></TD>
        <TD><INPUT type="text" name="ztCalAnnee" value="<?php echo $anneeEnCours; ?>" class="CalTexte" style="width:32px; cursor:pointer;" readonly onClick="javascript:affMois(document.calForm['ztCalMois'].value,document.calForm['ztCalAnnee'].value,'3');" onmouseover="javascript:this.style.color='<?php echo $CalMoisNavigationTexteHover; ?>';retardeLancement('showListe(\'sel_annee\',\'ztCalAnnee\')',500);" onmouseout="javascript:this.style.color='<?php echo $CalMoisNavigationTexte; ?>';retardeLancement('cacheListe()',100);"></TD>
<?php
  // Si le rechargement automatique des calendriers n'est pas actif, on affiche le bouton OK
  if (!$RELOAD_CALENDAR) {
?>
        <TD width="0%" nowrap><A href="javascript: affCalSelect();" class="calAllerDate" title="<?php echo trad("CALENDRIER_ALLER_DATE"); ?>">ok</A></TD>
<?php
  }
?>
        <TD width="0%" align="right" nowrap><A href="javascript: affMois('<?php echo $moisApres; ?>','<?php echo $tcMenu; ?>');" class="calFlecheAnnee" title="<?php echo trad("CALENDRIER_MOIS_SUIV"); ?>">&raquo;</A></TD>
      </TR>
    </TABLE></FORM></TD>
  </TR>
  <TR>
    <TD><DIV id="calendrierDesJours"><?php include("agenda_calendrier_jours.php"); ?></DIV></TD>
  </TR>
  <SCRIPT language="javascript" src="inc/liveclock.js.php?id=<?php echo $APPLI_STYLE; ?>&frmHrs=<?php echo urlencode($formatHeure); ?>"></SCRIPT>
<?php
  //Ajustement de la date au 1er Janvier pour afficher correctement la semaine 1
  $crtWeek = date("W",$sd);
  $crtYear = ($crtWeek==1 && $moisEnCours==12) ? $anneeEnCours+1 : $anneeEnCours;
?>
  <TR>
    <TD><DIV id="calendrierDesSemaines"><?php include("agenda_calendrier_semaines.php"); ?></DIV></TD>
  </TR>
  <TR><TD height="1" bgcolor="<?php echo $CalGaucheFond; ?>"><IMG src="image/trans.gif" alt="" width="1" height="1" border="0"></TD></TR>
  <TR><TD height="1" bgcolor="<?php echo $CalLignesHMoisFond; ?>"><IMG src="image/trans.gif" height="1"></TD></TR>
<?php
  if ($droit_ADMIN=="O" && $idAdmin) {
?>
  <TR><TD height="25" bgcolor="<?php echo $CalFondExport; ?>" align="center" valign="middle"><A href="javascript: nvlVu('<?php echo _MENU_ADMIN; ?>');" title="<?php echo trad('CALENDRIER_ADMIN'); ?>"><IMG src="image/droits.gif" width="15" height="15" alt="" border="0" align="absmiddle">&nbsp;<FONT color="#FF0000"><B><?php echo trad('CALENDRIER_ADMIN'); ?></B></FONT></A></TD></TR>
  <TR><TD height="1" bgcolor="<?php echo $CalLignesHMoisFond; ?>"><IMG src="image/trans.gif" height="1"></TD></TR>
<?php
  }
?>
  <TR>
    <TD align="center" bgcolor="<?php echo $CalFondQuitter; ?>"><BR><BR><A href="deconnexion.php?sid=<?php echo $sid; ?>" class="btnQuitter"><IMG src="image/quitter.gif" width="18" height="18" alt="" border="0" align="absmiddle">&nbsp;<B><?php echo trad("CALENDRIER_DECONNEXION"); ?></B></A><BR><BR><BR></TD>
  </TR>
<?php
  if ((($USER_SUBSTITUE==$idUser) and ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR)) or ($droit_NOTES >= _DROIT_NOTE_MODIF_CREATION)) {
    //Recuperation des memos de l'utilisateur
	//Mod MemoProgress
	$DB_CX->DbQuery("SELECT DISTINCT mem_id, mem_titre, mem_contenu, mem_progress, mem_pcent, mem_util_id FROM ${PREFIX_TABLE}memo WHERE (mem_util_id=".$idUser." OR (mem_util_id!=".$idUser." AND mem_partage='O')) ORDER BY mem_id ASC");
	//fin Mod MemoProgress
    $afficheFinMenu = false;
    if ($DB_CX->DbNumRows()) {
      $afficheFinMenu = true;
      echo ("  <TR>
    <TD><TABLE cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" border=\"0\" class=\"paddingDG3\">
      <TR><TD bgcolor=\"".$CalMemoFavoris."\" height=\"15\" align=\"center\"><A href=\"javascript: nvType('"._TYPE_MEMO."');\" class=\"MemoFavorisTitre\"><B>".trad("CALENDRIER_MEMOS")."</B></A></TD></TR>\n");
      $index = 0;
      while ($enr=$DB_CX->DbNextRow()) {
        $index = 1-$index;
	//Mod MemoProgress
	$barre = nlTObr($enr['mem_pcent'])*0.2;
	$barre2 = 20-$barre;
	if ($enr['mem_progress']=="O") {
	  $textbarre="<img src='image/barre-vert.gif' width='".$barre."' height='3px' style='border:0;'><img src='image/barre-rouge.gif' width='".$barre2."' height='3px' style='border:0;'>&nbsp;&nbsp;";
  } else {
    $textbarre="";
  }
	//fin Mod MemoProgress
        $lien = ($enr['mem_util_id']==$idUser || $MODIF_PARTAGE) ? " href=\"?id=".$enr['mem_id']."&sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&tcType="._TYPE_MEMO."&sd=".$sd."\"" : "";
        echo ("      <TR bgcolor=\"".$bgColor[$index]."\">
<TD width=\"100%\">
<A".$lien." class=\"MemoFavorisTexte\" onmouseover=\"mtc('".addslashes(htmlspecialchars(nlTObr($enr['mem_titre'])))."','".addslashes(htmlspecialchars(nlTObr($enr['mem_contenu'])))."&nbsp;',330); return false;\" onmouseout=\"nd(); return true;\">".$textbarre.$enr['mem_titre']."</A></TD>
      </TR>\n");
      }
      echo ("    </TABLE></TD>
  </TR>\n");
    }
    //Recuperation des favoris de l'utilisateur (y compris partages) classes par groupe
    $DB_CX->DbQuery("SELECT DISTINCT fav_nom, fav_url, fav_commentaire, fgr_nom FROM ${PREFIX_TABLE}favoris, ${PREFIX_TABLE}favoris_groupe WHERE (fav_util_id=".$idUser." OR (fav_util_id!=".$idUser." AND fav_partage='O')) AND fgr_id=fav_fgr_id ORDER BY fgr_nom, fav_nom ASC");
    if ($DB_CX->DbNumRows()) {
      $afficheFinMenu = true;
      echo ("  <TR>
    <TD><TABLE cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" border=\"0\">
      <TR><TD colspan=\"2\" bgcolor=\"".$CalMemoFavoris."\" height=\"15\" align=\"center\"><A href=\"javascript: nvType('"._TYPE_FAVORIS."');\" class=\"MemoFavorisTitre\"><B>".trad("CALENDRIER_FAVORIS")."</B></A></TD></TR>\n");
      $index = 0;
      $nomGroupeCrt = "";
      $idxGrp = 1;
      while ($enr=$DB_CX->DbNextRow()) {
        // Nom du groupe de favoris
        if ($nomGroupeCrt != $enr['fgr_nom']) {
          if (!empty($nomGroupeCrt)) {
            // Ce n'est pas le premier enregistrement lu -> on ferme le tableau du groupe precedent
            echo ("      </TABLE></DIV></TD></TR>\n");
          }
          echo ("      <TR><TD bgcolor=\"".$CalMemoFavoris."\" height=\"1\" colspan=\"2\"><IMG src=\"image/trans.gif\" width=\"100%\" height=\"1\"></TD></TR>
      <TR bgcolor=\"".$CalMemoFavorisFond."\" class=\"CalFavorisGroupe\" height=\"15\" onclick=\"javascript:classToggle(document.getElementById('favGrp".$idxGrp."'),'displayNone','displayBlock',document.getElementById('imgGrp".$idxGrp."'),'image/')\" style=\"cursor:pointer;\"><TD width=\"100%\"><B>".$enr['fgr_nom']."</B></TD><TD align=\"right\" nowrap><IMG id='imgGrp".$idxGrp."' src=\"image/expand1.gif\" width=\"7\" height=\"4\" alt=\"\" border=\"0\"></TD></TR>
      <TR><TD bgcolor=\"".$CalLignesHMoisFond."\" height=\"1\" colspan=\"2\"><IMG src=\"image/trans.gif\" width=\"100%\" height=\"1\"></TD></TR>
      <TR><TD colspan=\"2\"><DIV id=\"favGrp".($idxGrp++)."\" class=\"displayNone\"><TABLE cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" class=\"paddingDG3\">\n");
          $nomGroupeCrt = $enr['fgr_nom'];
          $index = 0;
        }
        $index = 1-$index;
        $infoLien = (!empty($enr['fav_commentaire'])) ? infoPopup(htmlspecialchars(nlTObr($enr['fav_commentaire']))) : "";
        echo ("      <TR bgcolor=\"".$bgColor[$index]."\">
        <TD width=\"100%\"><A href=\"".$enr['fav_url']."\" class=\"MemoFavorisTexte\" target=\"_blank\"".$infoLien.">".htmlspecialchars($enr['fav_nom'])."</A></TD>
      </TR>\n");
      }
      // On ferme le tableau du groupe precedent
      echo ("      </TABLE></DIV></TD></TR>\n");
      echo ("    </TABLE></TD>
  </TR>\n");
    }
  }
  if ($afficheFinMenu)
    echo "  <TR><TD height=\"1\" bgcolor=\"".$CalLignesHMoisFond."\"><IMG src=\"image/trans.gif\" height=\"1\"></TD></TR>\n";
?>
  </TABLE>
<!-- FIN MODULE CALENDRIER -->
