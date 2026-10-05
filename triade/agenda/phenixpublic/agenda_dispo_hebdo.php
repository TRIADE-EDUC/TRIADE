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

<!-- MODULE DISPONIBILITE HEBDOMADAIRE -->
<?php
  // Constitution du titre de la page
  $lienAvant = mktime(0,0,0,$moisEnCours,$premierJourSemaine-7,$anneeEnCours);
  $lienApres = mktime(0,0,0,$moisEnCours,$premierJourSemaine+7,$anneeEnCours);
  $titrePage = "<A href=\"javascript: affSemaine('".$lienAvant."');\" class=\"sousMenu\"".infoPopup(trad("DISPOH_SEMAINE_PRECEDENTE")).">&laquo;</A>&nbsp;&nbsp;".sprintf(trad("DISPOH_SEMAINE_COURANTE"), date("d",$debutSemaine)." ".strtolower($tabMois[date("n",$debutSemaine)])." ".date("Y",$debutSemaine), date("d",$finSemaine)." ".strtolower($tabMois[date("n",$finSemaine)])." ".date("Y",$finSemaine))."&nbsp;&nbsp;<A href=\"javascript: affSemaine('".$lienApres."');\" class=\"sousMenu\"".infoPopup(trad("DISPOH_SEMAINE_SUIVANTE")).">&raquo;</A>";

  include("agenda_planning_groupe.php");

  // Si la liste contenant les identifiants des utilisateurs selectionnes n'est pas vide
  if (!empty($sChoix)) {
    // Info sur les utilisateurs selectionnes
    //Recuperer les horaires de debut et fin de journee de chaque utilisateur pour les mettre a "occupe" s'ils ne correspondent pas a la plage horaire choisie
    //Recuperer la semaine type des utilisateurs pour les rendre indisponibles les jours a 0
    $DB_CX->DbQuery("SELECT util_id, util_semaine_type FROM ${PREFIX_TABLE}utilisateur WHERE util_id IN (".$sChoix.") ORDER BY util_id");
    // Tableau contenant les id => horaires (case 1 et 2) et semaine type de l'utilisateur (case 3)
    $aUtil = array();
    while ($enr=$DB_CX->DbNextRow()) {
      $aUtil[$enr['util_id']][0] = max($aHeureDebutJourneeUtil[$enr['util_id']],$iHeureMin);
      $aUtil[$enr['util_id']][1] = min($aHeureFinJourneeUtil[$enr['util_id']],$iHeureMax);
      $aUtil[$enr['util_id']][2] = substr($enr['util_semaine_type'],6).substr($enr['util_semaine_type'],0,6); // Semaine type mappee au format PHP (L->D => D->S)
    }
?>
  <BR>
  <TABLE border="0" cellspacing="0" cellpadding="0">
  <TR align="center">
    <TD>&nbsp;</TD>
<?php
    // Calcul de la duree de la journee pour le nb de colonnes
    $iDureeJournee = ($iHeureMax-$iHeureMin)*$zlPrec;
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
      $sOutput .= "    <TD align=\"center\" class=\"jourPlanning\" width=\"15\" nowrap style=\"font-size:9px;\">".((($iHeureMin+($i/$zlPrec))*60)%60)."</TD>\n";
    }
    $sOutput .= "  </TR>\n";

    // Parcours des jours de la semaine
    for ($j=0;$j<7;$j++) {
      $leJour = mktime(0,0,0,$moisEnCours,$premierJourSemaine+$j,$anneeEnCours);
      $sOutput .= "  <TR align=\"center\" valign=\"middle\">\n";
      $sOutput .= "    <TD class=\"nomUtil\"><A href=\"javascript: affJour('".$leJour."');\" title=\"".trad("DISPOH_DETAIL_UTIL")."\"><B>&nbsp;".$tabJour[date("w",$leJour)]."&nbsp;<BR>".date("d/m",mktime(0,0,0,$moisEnCours,$premierJourSemaine+$j,$anneeEnCours))."</B></A></TD>\n";

      // On commence par positionner les indisponibilites des utilisateurs en fonction de leur horaires et de leur semaine type
      while (list($sUtilID,$aInfoUtil)=each($aUtil)) {
        // Semaine type
        if (substr($aInfoUtil[2],date("w",$leJour),1)=="0") {
        // Journee hors profil semaine type => indisponibilite toute la journee
          for ($i=0;$i<$iDureeJournee;$i++) {
            $aJournee[$i][0] += 1; // On incremente le nombre d'utilisateur qui sont occupes sur cette plage horaire
            $aJournee[$i][$sUtilID] = 1; // On "marque" cette plage comme traitee pour cet utilisateur
          }
        } else {
          // Debut de journee
          for ($i=$iHeureMin;$i<$aInfoUtil[0];$i+=(1/$zlPrec)) {
            $aJournee[($i-$iHeureMin)*$zlPrec][0] += 1; // On incremente le nombre d'utilisateur qui sont occupes sur cette plage horaire
            $aJournee[($i-$iHeureMin)*$zlPrec][$sUtilID] = 1; // On "marque" cette plage comme traitee pour cet utilisateur
          }
          // Fin de journee
          for ($i=$aInfoUtil[1];$i<$iHeureMax;$i+=(1/$zlPrec)) {
            $aJournee[($i-$iHeureMin)*$zlPrec][0] += 1; // On incremente le nombre d'utilisateur qui sont occupes sur cette plage horaire
            $aJournee[($i-$iHeureMin)*$zlPrec][$sUtilID] = 1; // On "marque" cette plage comme traitee pour cet utilisateur
          }
        }
      }
      // On se repositionne au debut du tableau
      reset($aUtil);
      // Recalcul des bascules ete/hiver en tenant compte de l'annee affichee
      $tzEte = calculBasculeDST($tzDateEte,date("Y",$leJour),$tzHeureEte,$tzGmt,0);
      $tzHiver = calculBasculeDST($tzDateHiver,date("Y",$leJour),$tzHeureHiver,$tzGmt,1);
      //Preparation au decalage horaire
      list($age_date,$age_dateAvant,$age_heure_debut,$age_heure_fin) = prepareDecalageH($tzGmt,$tzEte,$tzHiver,$leJour);
      // Recuperation des horaires des notes dans la table agenda_concerne
      $sql  = "SELECT age_heure_debut, age_heure_fin, age_aty_id, aco_util_id, age_date, age_date_creation, age_date_modif";
      $sql .= " FROM ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}agenda";
      $sql .= " WHERE aco_util_id IN (".$sChoix.")";
      $sql .= "  AND age_id=aco_age_id";
      $sql .= "  AND ($age_date='".date("Y-m-d",$leJour)."' OR ($age_dateAvant='".date("Y-m-d",$leJour)."' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin!=0 AND age_aty_id=2))";
      $sql .= "  AND age_disponibilite=0";
      $sql .= "  AND ((age_aty_id=2 AND $age_heure_debut=".$iHeureMin;
      $sql .= "    OR ($age_heure_debut<".$iHeureMin." AND $age_heure_fin>".$iHeureMin.")";
      $sql .= "    OR ($age_heure_debut>".$iHeureMin." AND $age_heure_debut<".$iHeureMax."))";
      $sql .= "  OR age_aty_id=3)";
      $sql .= " ORDER BY age_date, age_heure_debut ASC, age_heure_fin DESC";
      $DB_CX->DbQuery($sql);

      // Remplissage du tableau de la journee a 1 (occupe)
      while ($enr=$DB_CX->DbNextRow()) {
        //Decalage des notes en fonction du fuseau horaire
        list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['dateCreation'],$enr['dateModif']) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,date("Y-m-d",$leJour),$enr['age_date'],$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif']);
        // Ajustement des heures de debut et de fin si hors profil ou pour les notes couvrant toute une journee
        if ($enr['age_aty_id']==3) {
          $enr['age_heure_debut']=$iHeureMin;
          $enr['age_heure_fin']=$iHeureMax;
        } else {
          $enr['age_heure_debut']=max($enr['age_heure_debut'],$iHeureMin);
          $enr['age_heure_fin']=min($enr['age_heure_fin'],$iHeureMax);
        }
        for ($i=$enr['age_heure_debut'];$i<$enr['age_heure_fin'];$i+=(1/$zlPrec)) {
          //Matrice des disponibilites 0->libre 1->Occupe
          if ($aJournee[($i-$iHeureMin)*$zlPrec][$enr['aco_util_id']]!=1) { // Si on n'a pas deja specifie cette plage horaire comme occupe pour cet utilisateur
            $aJournee[($i-$iHeureMin)*$zlPrec][0] += 1; // On incremente le nombre d'utilisateur qui sont occupes sur cette plage horaire
            $aJournee[($i-$iHeureMin)*$zlPrec][$enr['aco_util_id']] = 1; // On "marque" cette plage comme traitee pour cet utilisateur
          }
        }
      }
      for ($i=0;$i<$iDureeJournee;$i++) {
        $hFin = colSpan($i+1,$aJournee[$i][0],count($aChoix),true);
        $colspan = ($hFin-$i>1) ? " colspan=\"".($hFin-$i)."\"" : "";
        if ($aJournee[$i][0]>0) { // Si la plage horaire est occupee
          if ($aJournee[$i][0]==count($aChoix)) // Si tous les utilisateurs sont occupes sur cette plage horaire
            $sOutput .= "    <TD class=\"note\" style=\"cursor:auto;\"".$colspan.">&nbsp;</TD>\n";
          else
            $sOutput .= "    <TD class=\"partiel\" style=\"cursor:auto;\"".$colspan.">&nbsp;</TD>\n";
        } else {
          $sOutput .= "    <TD class=\"libre\"".$colspan." onclick=\"javascript: nvNoteG('".$leJour."','".(($i/$zlPrec)+$iHeureMin)."','".(($hFin/$zlPrec)+$iHeureMin)."','".$sChoix."','');\" title=\"".sprintf(trad("DISPOH_CREER_NOTE"), afficheHeure(($i/$zlPrec)+$iHeureMin,($i/$zlPrec)+$iHeureMin,$formatHeure), afficheHeure(($hFin/$zlPrec)+$iHeureMin,($hFin/$zlPrec)+$iHeureMin,$formatHeure))."\">&nbsp;</TD>\n";
        }
        // On avance l'indice de la boucle pour tenir compte du colspan
        $i = $hFin-1;
      }
      $sOutput .= "  </TR>\n";
      // Reinitialisation du tableau pour le jour suivant
      unset($aJournee);
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
    <TD class="partiel" width="60" style="cursor:auto;"><?php echo trad("DISPOH_PARTIEL");?></TD>
    <TD>&nbsp;&nbsp;</TD>
    <TD class="libre" width="60" style="cursor:auto;"><?php echo trad("DISPO_LIBRE");?></TD>
  </TR>
  </TABLE>
<?php
  }
  // FIN SI la liste contenant les identifiants des utilisateurs selectionnes n'est pas vide
  $sChoix="";
?>
<!-- FIN MODULE DISPONIBILITE HEBDOMADAIRE -->
