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
  ?> <SCRIPT> HelpPhenixCtx="{3C403BBF-5B44-4284-A17A-01E22D4B8F42}.htm"; </SCRIPT> <?php
  // Mod Aide
?>

<!-- MODULE DISPONIBILITE QUOTIDIENNE -->
<?php
  // Constitution du titre de la page
  $lienAvant = mktime(0,0,0,$moisEnCours,$jourEnCours-1,$anneeEnCours);
  $lienApres = mktime(0,0,0,$moisEnCours,$jourEnCours+1,$anneeEnCours);
  $titrePage = sprintf(trad("DISPOJ_DISPO_CONTACT"), $jourEnCours." ".$tabMois[intval($moisEnCours)]." ".$anneeEnCours);

  include("agenda_planning_groupe.php");

  // Si la liste contenant les identifiants des utilisateurs selectionnes n'est pas vide
  if (!empty($sChoix)) {
    // Info sur les utilisateurs selectionnes
    $DB_CX->DbQuery("SELECT util_id, util_semaine_type FROM ${PREFIX_TABLE}utilisateur WHERE util_id IN (".$sChoix.") ORDER BY nomUtil");
    while ($enr=$DB_CX->DbNextRow()) {
      $vSemaineType = substr($enr['util_semaine_type'],6).substr($enr['util_semaine_type'],0,6); // Semaine type mappee au format PHP (L->D => D->S)
      if (substr($vSemaineType,date("w",$sd),1)=="0") {
        // Journee hors profil semaine type => indisponibilite toute la journee
        $aHeureDebutJourneeUtil[$enr['util_id']] = $zlHF;
      }
    }
?>
  <BR>
  <TABLE border="0" cellspacing="0" cellpadding="0">
  <TR align="center">
    <TD>&nbsp;</TD>
<?php
    // Definition de la date courante
    $dateCrt = $anneeEnCours."-".$moisEnCours."-".$jourEnCours;
    // Calcul de la duree de la journee pour le nb de colonnes
    $iDureeJournee = ($iHeureMax-$iHeureMin)*$zlPrec;
    //Tableau des disponibilites 0->libre 1->Occupe 2->Hors Profil
    $aJournee = array();
    $sOutput = "";
    // Creation de la premiere ligne du tableau avec la plage horaire maximale des utilisateurs concernes
    for ($i=0;$i<$iDureeJournee;$i+=$zlPrec) {
      if (!$i && ($iHeureMin-floor($iHeureMin)>0)) {
        $tailleCell = ($iHeureMin-floor($iHeureMin))*$zlPrec;
        $i = $i - $tailleCell;
      }
      elseif ($i>=($iDureeJournee-$zlPrec) && ($iHeureMax-floor($iHeureMax)>0))
        $tailleCell = ($iHeureMax-floor($iHeureMax))*$zlPrec;
      else
        $tailleCell = $zlPrec;
      $iHeure = ($formatHeure=="H:i") ? ($iHeureMin+($i/$zlPrec)) : ((($iHeureMin+($i/$zlPrec))%12)==0 ? "12" : (($iHeureMin+($i/$zlPrec))%12));
      $sOutput .= "    <TD align=\"center\" class=\"jourPlanning\" colspan=\"".$tailleCell."\" width=\"".($tailleCell*15)."\" nowrap>".$iHeure.trad("DISPO_H")."</TD>\n";
    }
    $sOutput .= "  </TR>\n  <TR align=\"center\">\n    <TD>&nbsp;</TD>\n";
    for ($i=0;$i<$iDureeJournee;$i++) {
      //Initialisation du tableau de la journee a 0 (libre) et sans couleur par defaut
      $aJournee[$i][0]="0";
      $aJournee[$i][1]="";
      $sOutput .= "    <TD align=\"center\" class=\"jourPlanning\" width=\"15\" nowrap style=\"font-size:9px;\">".((($iHeureMin+($i/$zlPrec))*60)%60)."</TD>\n";
    }
    $sOutput .= "  </TR>\n";

    // Parcours du tableau d'utilisateur pour recuperer toutes les notes
    while (list($sUtilID,$sNomUtil)=each($aUtilPartage)) {
      // Pour chaque utilisateur on verifie si on a acces a son planning en modification
        $DB_CX->DbQuery("SELECT util_id FROM ${PREFIX_TABLE}utilisateur LEFT JOIN ${PREFIX_TABLE}planning_affecte ON paf_util_id=util_id WHERE util_id=".$sUtilID." AND (util_autorise_affect='1' OR (util_autorise_affect IN ('2','3') AND paf_consultant_id=".$idUser."))");
      if (($DB_CX->DbNumRows() && ($droit_NOTES < _DROIT_NOTE_MODIF_STATUT)) || ($droit_NOTES >= _DROIT_NOTE_MODIF_CREATION) || $sUtilID==$idUser) {
        $autoriseAffect = true;
      } else {
        $autoriseAffect = false;
      }
      $sOutput .= "  <TR height=\"17\">\n";
      // Pour chaque utilisateur on verifie si on a acces a son planning en consultation
      $DB_CX->DbQuery("SELECT util_id FROM ${PREFIX_TABLE}utilisateur LEFT JOIN ${PREFIX_TABLE}planning_partage ON ppl_util_id=util_id WHERE util_id=".$sUtilID." AND (util_partage_planning='1' OR (util_partage_planning='2' AND ppl_consultant_id=".$idUser."))");
      if (($DB_CX->DbNumRows() && ($droit_NOTES < _DROIT_NOTE_MODIF_STATUT)) || ($droit_NOTES >= _DROIT_NOTE_MODIF_CREATION) || $sUtilID==$idUser) {
        $sOutput .= "    <TD class=\"nomUtil\" style=\"padding-left:3px;padding-right:3px;\"><A href=\"javascript: substUser('".$sUtilID."');\" title=\"".trad("DISPOJ_PLANNING_UTIL")."\">".$sNomUtil."</A></TD>\n";
        $autoriseConsult = true;
      } else {
        $sOutput .= "    <TD class=\"nomUtil\" style=\"font-weight:normal;font-style:italic;padding-left:3px;padding-right:3px;\">".$sNomUtil."</TD>\n";
        $autoriseConsult = false;
      }

      // Recalcul des bascules ete/hiver en tenant compte de l'annee affichee
      $tzEte = calculBasculeDST($tzDateEte,$anneeEnCours,$tzHeureEte,$tzGmt,0);
      $tzHiver = calculBasculeDST($tzDateHiver,$anneeEnCours,$tzHeureHiver,$tzGmt,1);
      //Preparation au decalage horaire
      list($age_date,$age_dateAvant,$age_heure_debut,$age_heure_fin) = prepareDecalageH($tzGmt,$tzEte,$tzHiver,mktime(0,0,0,$moisEnCours,$jourEnCours,$anneeEnCours));
      // Recuperation des horaires des notes dans la table {PREFIX_TABLE}agenda_concerne
      $sql  = "SELECT age_heure_debut, age_heure_fin, age_aty_id, age_libelle, age_detail, age_couleur, age_util_id,";
      $sql .= "       age_createur_id, CONCAT(".$NOM_UTIL_CREATEUR.") AS nomCreateur, age_prive, age_date_creation,";
      $sql .= "       age_date_modif, age_modificateur_id, CONCAT(".$NOM_UTIL_MODIFICATEUR.") AS nomModificateur, age_id,";
      $sql .= "       age_lieu, CONCAT(".$FORMAT_NOM_CONTACT.") AS nomContact, age_date";
      $sql .= " FROM ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}agenda LEFT JOIN ${PREFIX_TABLE}calepin ON cal_id=age_cal_id, ${PREFIX_TABLE}utilisateur t1, ${PREFIX_TABLE}utilisateur t2";
      $sql .= " WHERE aco_util_id=".$sUtilID;
      $sql .= "  AND age_id=aco_age_id";
      $sql .= "  AND ($age_date='".$dateCrt."' OR ($age_dateAvant='".$dateCrt."' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin!=0 AND age_aty_id=2))";
      $sql .= "  AND age_disponibilite=0";
      $sql .= "  AND ((age_aty_id=2 AND $age_heure_debut=".$aHeureDebutJourneeUtil[$sUtilID];
      $sql .= "    OR ($age_heure_debut<".$aHeureDebutJourneeUtil[$sUtilID]." AND $age_heure_fin>".$aHeureDebutJourneeUtil[$sUtilID].")";
      $sql .= "    OR ($age_heure_debut>".$aHeureDebutJourneeUtil[$sUtilID]." AND $age_heure_debut<".$aHeureFinJourneeUtil[$sUtilID]."))";
      $sql .= "  OR age_aty_id=3)";
      $sql .= "  AND t1.util_id=age_createur_id AND t2.util_id=age_modificateur_id";
      $sql .= " ORDER BY age_aty_id DESC, age_date, age_heure_debut ASC, age_heure_fin DESC";
      $DB_CX->DbQuery($sql);

      // Remplissage du tableau de la journee a 1 (occupe) ou 2 (hors profil)
      while ($enr=$DB_CX->DbNextRow()) {
        //Decalage des notes en fonction du fuseau horaire
        list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['dateCreation'],$enr['dateModif']) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$dateCrt,$enr['age_date'],$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif']);
        // Ajustement des heures de debut et de fin si hors profil ou pour les notes couvrant toute une journee
        if ($enr['age_aty_id']==3) {
          $enr['age_heure_debut']=$aHeureDebutJourneeUtil[$sUtilID];
          $enr['age_heure_fin']=$aHeureFinJourneeUtil[$sUtilID];
        } else {
          $enr['age_heure_debut']=max($aHeureDebutJourneeUtil[$sUtilID],$enr['age_heure_debut']);
          $enr['age_heure_fin']=min($aHeureFinJourneeUtil[$sUtilID],$enr['age_heure_fin']);
        }
        //Propriete Privee ou Publique de la note
        if (!$autoriseConsult || ($sUtilID!=$idUser && $enr['age_util_id']!=$idUser && $enr['age_prive']==1)) {
          $enr['age_libelle'] = trad("COMMUN_OCCUPE");
          $enr['age_detail'] = ""; // Detail et info de creation non visible si note privee
          $enr['age_couleur'] = $PlanningNotePrivee; // Couleur de note non visible si note privee
          $enr['age_lieu'] = ""; // Emplacement non visible  si note privee
        } else {
          //Info sur le contact associe
          if (!empty($enr['nomContact'])) {
            $enr['age_detail'] = "<DIV style=\"background-color:".$AgendaContactPopup.";\">".trad("DISPOJ_CONTACT_ASSOCIE")." : <B>".$enr['nomContact']."</B></DIV>".$enr['age_detail'];
          }
          //Info sur la creation / modification de la note
          afficheInfoModifNote($enr, $sUtilID);
        }
        // Couleur de fond de la note si non definie dans la bdd
        if (empty($enr['age_couleur']))
          $enr['age_couleur'] = ($enr['age_util_id']==$sUtilID) ? $AgendaFondNotePerso : $AgendaFondNote;
        // Plage horaire de la note
        $plageNote = ($enr['age_aty_id']==2) ? afficheHeure(floor($enr['age_heure_debut']),$enr['age_heure_debut'],$formatHeure)."&rsaquo;".afficheHeure(floor($enr['age_heure_fin']),$enr['age_heure_fin'],$formatHeure) : trad("COMMUN_JOURNEE_ENTIERE");
        // Info a afficher dans le popup
        $libelleNote = htmlspecialchars($enr['age_libelle']).((!empty($enr['age_lieu'])) ? "<BR><I>(".$enr['age_lieu'].")</I>" : "");
        $detailNote = htmlspecialchars(nlTObr($enr['age_detail']));

        $noteCrt = 0;
        for ($i=$enr['age_heure_debut'];$i<$enr['age_heure_fin'];$i+=0.25) {
          // Si precision 30mn et note deja commence a etre affichee (pour afficher les notes commencant a la 15 ou 45eme mn) et si on est sur une tranche 15 ou 45
          if ($zlPrec==4 || ($zlPrec==2 && ($noteCrt!=$enr['age_id'] || ($noteCrt==$enr['age_id'] && (($i-floor($i)==0) || ($i-floor($i)==0.5)))))) {
            // Indice de la cellule du tableau a manipuler
            $iCell = ($i-$iHeureMin)*$zlPrec;
            // Saut de ligne si plusieurs notes sur la meme case
            $br = ($aJournee[$iCell][0] != "0") ? "<HR style='color:black;height:1px;'>" : "";
            // Contenu du popup
            $aJournee[$iCell][0] .= $br."<B>".$plageNote."</B>&nbsp;:&nbsp;".$libelleNote."<br>".$detailNote;
            // Couleur de la case
            $aJournee[$iCell][1] = $enr['age_couleur'];
            // Note en cours de lecture
            $noteCrt = $enr['age_id'];
          } else {
            $noteCrt = 0;
          }
        }
      }
      // Horaires hors profil
      for ($i=$iHeureMin;$i<$aHeureDebutJourneeUtil[$sUtilID];$i+=0.25)
        $aJournee[($i-$iHeureMin)*$zlPrec][0]="2";
      for ($i=$aHeureFinJourneeUtil[$sUtilID];$i<$iHeureMax;$i+=0.25)
        $aJournee[($i-$iHeureMin)*$zlPrec][0]="2";
      // Affichage de la journee de l'utilisateur
      for ($i=0;$i<$iDureeJournee;$i++) {
        if ($aJournee[$i][0]!="2" && $aJournee[$i][0]!="0") {
          $popup = " onmouseover=\"javascript: mtc('".trad("DISPOJ_DETAIL")."', '".addslashes(substr($aJournee[$i][0],1))."',390); return false;\" onmouseout=\"javascript: nd(); return true;\"";
          $sOutput .= "    <TD class=\"note\" style=\"cursor:auto; background-color:".$aJournee[$i][1].";\"".$popup.">&nbsp;</TD>"; //.$colspan
          // Reinitialisation du tableau pour le jour suivant
          $aJournee[$i][0]="0";
          $aJournee[$i][1]="";
        } else {
          $hFin = colSpan($i+1,$aJournee[$i][0],0,false);
          $colspan = (($hFin-$i)>1) ? " colspan=\"".($hFin-$i)."\"" : "";
          if ($aJournee[$i][0]=="2") {
            $sOutput .= "    <TD class=\"invalide\" style=\"cursor:auto;\"".$colspan.">&nbsp;</TD>";
          } elseif ($aJournee[$i][0]=="0" && $autoriseAffect==true) {
            $sOutput .= "    <TD class=\"libre\"".$colspan." onclick=\"javascript: nvNoteG('".$sd."','".(($i/$zlPrec)+$iHeureMin)."','".(($hFin/$zlPrec)+$iHeureMin)."','".$sChoix."','');\" title=\"".sprintf(trad("DISPOJ_CREER_NOTE"), afficheHeure(($i/$zlPrec)+$iHeureMin,($i/$zlPrec)+$iHeureMin,$formatHeure), afficheHeure(($hFin/$zlPrec)+$iHeureMin,($hFin/$zlPrec)+$iHeureMin,$formatHeure))."\">&nbsp;</TD>\n";
          } elseif ($aJournee[$i][0]=="0" && $autoriseAffect==false) {
            $sOutput .= "    <TD class=\"libreCons\"".$colspan.">&nbsp;</TD>\n";
          }
          // Reinitialisation du tableau pour le jour suivant
          $aJournee[$i][0]="0";
          $aJournee[$i][1]="";
          // On avance l'indice de la boucle
          $i = $hFin-1;
        }
      }
      $sOutput .= "  </TR>\n";
    }
    echo $sOutput;
?>
  </TABLE>
  <DIV class="timezone" style="text-align:center;"><?php echo sprintf(trad("COMMUN_FUSEAU_ACTUEL"), (($tzGmt<0) ? "-" : "+").afficheHeure(floor(abs($tzGmt)),abs($tzGmt)), $tzLibelle); ?></DIV>
  <BR>
  <TABLE border="0" cellspacing="0" cellpadding="0">
  <TR align="center">
    <TD class="note" width="60" style="cursor:auto;"><?php echo trad("COMMUN_OCCUPE");?></TD>
    <TD>&nbsp;&nbsp;</TD>
    <TD class="libre" width="60" style="cursor:auto;"><?php echo trad("DISPO_LIBRE");?></TD>
    <TD>&nbsp;&nbsp;</TD>
    <TD class="invalide" width="60" style="cursor:auto;"><?php echo trad("DISPOJ_HORS_PROFIL");?></TD>
  </TR>
  </TABLE>
<?php
  }
  // FIN SI la liste contenant les identifiants des utilisateurs selectionnes n'est pas vide
  $sChoix="";
?>
<!-- MODULE DISPONIBILITE QUOTIDIENNE -->
