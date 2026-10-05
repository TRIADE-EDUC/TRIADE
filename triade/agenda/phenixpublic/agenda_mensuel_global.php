<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5_Scission_de_note_recurente.txt ?>
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
  *                                                             MOD by dJuL  *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
  // Mod Aide
  // Fichier d'aide contextuel
  ?> <SCRIPT> HelpPhenixCtx="{64302D5C-A631-4D23-9EF4-6FDAA525E58B}.htm"; </SCRIPT> <?php
  // Mod Aide
?>

<!-- MODULE PLANNING MENSUEL GLOBAL -->
<?php
  // Constitution du titre de la page
  $lienAvant = mktime(0,0,0,$moisEnCours-1,$jourEnCours,$anneeEnCours);
  $lienApres = mktime(0,0,0,$moisEnCours+1,$jourEnCours,$anneeEnCours);
  $titrePage = "<B><A href=\"javascript: affMois('".date("n",$lienAvant)."','".date("Y",$lienAvant)."','4');\" class=\"sousMenu\"".infoPopup("<B>".$tabMois[date("n",$lienAvant)]." ".date("Y",$lienAvant)."</B>").">&laquo;</A>&nbsp;&nbsp;".sprintf(trad("PLGL_PERIODE"), $tabMois[$moisEnCours+0], $anneeEnCours)."&nbsp;&nbsp;<A href=\"javascript: affMois('".date("n",$lienApres)."','".date("Y",$lienApres)."','4');\" class=\"sousMenu\"".infoPopup("<B>".$tabMois[date("n",$lienApres)]." ".date("Y",$lienApres)."</B>").">&raquo;</A></B>";

  // Hauteur d'une case
  $hdiv = 10;

  include("agenda_planning_groupe.php");

  // Si la liste contenant les identifiants des utilisateurs selectionnes n'est pas vide
  if (!empty($sChoix)) {
    function colSpanH($hDeb,$iDureeJournee) {
      for ($i=$hDeb;$i<=$iDureeJournee;$i++) {
        return $i;
      }
      return $i;
    }
    //Fonction gerant l'affichage des cases du tableau
    function afficheCase($leJour, $nbJour, $sUtilID, $tzPartage, $aHeureDebutJourneeUtil ,$aHeureFinJourneeUtil) {
      global $DB_CX, $PREFIX_TABLE, $NOM_UTIL_CREATEUR, $NOM_UTIL_MODIFICATEUR, $FORMAT_NOM_CONTACT, $idUser, $moisEnCours, $anneeEnCours, $tabAffichage, $_SERVER;
      global $AgendaFondNotePerso, $AgendaFondNote, $AgendaTexteTitrePopup, $PlanningNotePrivee, $AgendaContactPopup,$hdiv, $bgColor, $PlNote, $PfNote;
      global $droit_NOTES, $AUTORISE_SUPPR, $PlanningNotePrivee, $aAutoAffect, $iHeureMax, $iHeureMin, $autoriseConsult, $whereCouleur, $AgendaBordureTableau, $PlanningInvalideTexte, $PlanningPartiel, $hauteurMax;
      global $tzGmt, $tzEte, $tzDateEte, $tzHeureEte, $tzHiver, $tzDateHiver, $tzHeureHiver, $aUtiltzGmt, $aUtiltzDateEte, $aUtiltzHeureEte, $aUtiltzDateHiver, $aUtiltzHeureHiver, $formatHeure, $localTime;
      global $CalJourFerie, $CalJourSelection , $CalJourEvenement;

      // MOD Scission de note
      global $AUTORISE_SCISSION;
      // Fin MOD Scission de note
      $iDureeJournee = $iHeureMax-$iHeureMin;
      $classCel = ($leJour==date("Y-m-d",$localTime)) ? "mensJour" : "mensNote";
      $tsJour = mktime(0,0,0,$moisEnCours, $nbJour, $anneeEnCours);
      $AffJour = ($nbJour < 10) ? "0".$nbJour."-".$moisEnCours."-".$anneeEnCours : $nbJour."-".$moisEnCours."-".$anneeEnCours;
      //Preparation au decalage horaire
      list($age_date,$age_dateAvant,$age_heure_debut,$age_heure_fin) = prepareDecalageH($tzGmt,$tzEte,$tzHiver,$tsJour);

      $sql  = "SELECT age_heure_debut, age_heure_fin, age_aty_id, age_libelle, age_detail, age_couleur, age_util_id,";
      $sql .= "       age_createur_id, CONCAT(".$NOM_UTIL_CREATEUR.") AS nomCreateur, age_prive, age_date_creation,";
      $sql .= "       age_date_modif, age_modificateur_id, CONCAT(".$NOM_UTIL_MODIFICATEUR.") AS nomModificateur, age_id,";
      $sql .= "       age_lieu, CONCAT(".$FORMAT_NOM_CONTACT.") AS nomContact, age_date, age_rappel,";
      $sql .= "       age_rappel_coeff, age_mere_id, age_ape_id, cal_id, aco_termine, cal_partage, age_nb_participant";
      $sql .= " FROM ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}agenda LEFT JOIN ${PREFIX_TABLE}calepin ON cal_id=age_cal_id, ${PREFIX_TABLE}utilisateur t1, ${PREFIX_TABLE}utilisateur t2";
      $sql .= " WHERE aco_util_id=".$sUtilID;
      $sql .= "  AND age_id=aco_age_id";
      $sql .= "  AND ($age_date='".$leJour."' OR ($age_dateAvant='".$leJour."' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin!=0))";
      $sql .= "  AND age_disponibilite=0";
      $sql .= "  AND t1.util_id=age_createur_id AND t2.util_id=age_modificateur_id";
      $sql .= $whereCouleur;
      $sql .= " ORDER BY age_aty_id DESC, age_date, age_heure_debut ASC, age_heure_fin DESC";
      $DB_CX->DbQuery($sql);

      $sOutput="";
      for ($i=0;$i<=$iDureeJournee;$i++) {
        //Initialisation du tableau de la journee a 0 (libre) et sans couleur par defaut
        $aJournee[$i][0]="0";
        $aJournee[$i][1]="";
        $aJournee[$i][2]="&nbsp;";
        $aJournee[$i][3]="";
        $aJournee[$i][4]=false;
        $aJournee[$i][5]=1;
      }
      while ($enr=$DB_CX->DbNextRow()) {
        $AutoAffectT=(($aAutoAffect[$sUtilID]==1) ? true : false);
        attributDroits($enr, $droitModifStatut, $droitModifNotePerso, $droitModifNoteAffectee, $droitSuppOcc, $droitSuppNoteCreee, $droitSuppNoteAffectee, $droitApprNote, $sUtilID, $AutoAffectT);
        //Decalage des notes en fonction du fuseau horaire
        if ($tzPartage=="O") {
          list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['dateCreation'],$enr['dateModif']) = decaleNote($aUtiltzGmt[$sUtilID],$aUtiltzDateEte[$sUtilID],$aUtiltzHeureEte[$sUtilID],$aUtiltzDateHiver[$sUtilID],$aUtiltzHeureHiver[$sUtilID],$leJour,$enr['age_date'],$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif']);
        } else {
          list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['dateCreation'],$enr['dateModif']) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$leJour,$enr['age_date'],$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif']);
        }
        // Ajustement des heures de debut et de fin si hors profil ou pour les notes couvrant toute une journee
        $notePrive = false;
        if ($enr['age_aty_id']==3) {
          $enr['age_heure_debut'] = $aHeureDebutJourneeUtil;
          $enr['age_heure_fin'] = $aHeureFinJourneeUtil;
          $finr = $enr['age_heure_fin'];
          $hprf0 = false;
          $hprf1 = false;
        } elseif ($enr['age_heure_debut']<$iHeureMax && $enr['age_heure_fin']>$iHeureMin) {
          $debr = $enr['age_heure_debut'];
          $enr['age_heure_debut'] = max($aHeureDebutJourneeUtil,$enr['age_heure_debut']);
          $finr = $enr['age_heure_fin'];
          $enr['age_heure_fin'] = min($aHeureFinJourneeUtil,$enr['age_heure_fin']);
          if ($finr>$enr['age_heure_fin'] || $debr>$enr['age_heure_fin']) {
            $enr['age_heure_debut'] = min($debr,$iHeureMax-1);
            $enr['age_heure_fin'] = min($finr,$iHeureMax);
            $hprf0 = true;
            $PfNote = true;
          } else
            $hprf0 = false;
          if ($finr<$enr['age_heure_debut'] || $debr<$enr['age_heure_debut']) {
            $enr['age_heure_fin'] = max($enr['age_heure_debut'],$finr);
            $enr['age_heure_debut'] = max($iHeureMin,$debr);
            $hprf1 = true;
            $PfNote = true;
          } else
            $hprf1 = false;
        }
        //Propriete Privee ou Publique de la note
        if (!$autoriseConsult || ($sUtilID!=$idUser && $enr['age_util_id']!=$idUser && $enr['age_prive']==1)) {
          $enr['age_libelle'] = "&nbsp;".trad("COMMUN_OCCUPE");
          $enr['age_detail'] = "&nbsp;"; // Detail et info de creation non visible si note privee
          $enr['age_couleur'] = $PlanningNotePrivee; // Couleur de note non visible si note privee
          $enr['age_lieu'] = ""; // Emplacement non visible  si note privee
          $notePrive = true;
        } else {
          //Info sur le contact associe
          if (!empty($enr['nomContact'])) {
            if ($sUtilID==$idUser || $enr['cal_partage'] =='O')
              $enr['age_detail'] = "<DIV style=\"background-color:".$AgendaContactPopup.";\"><A href=\"javascript: affContact('".$enr['cal_id']."');\"><IMG src=\"image/contact.gif\" width=\"10\" height=\"11\" border=\"0\" align=\"absmiddle\" title=\"".trad("PLGL_INFO_ASSOCIE")."\"></A> : <B>".$enr['nomContact']."</B></DIV>".$enr['age_detail'];
            else
              $enr['age_detail'] = "<DIV style=\"background-color:".$AgendaContactPopup.";\"><IMG src=\"image/contact.gif\" width=\"10\" height=\"11\" border=\"0\" align=\"absmiddle\" title=\"".trad("PLGL_INFO_ASSOCIE")."\"></DIV>".$enr['age_detail'];
          }
          //Info sur la creation / modification de la note
          afficheInfoModifNote($enr, $sUtilID);
        }
        // Couleur de fond de la note si non definie dans la bdd
        if (empty($enr['age_couleur']))
          $enr['age_couleur'] = ($enr['age_util_id']==$sUtilID) ? $AgendaFondNotePerso : $AgendaFondNote;
        // Plage horaire de la note
        $plageNote = ($enr['age_aty_id']==2) ? afficheHeure(floor($debr),$debr,$formatHeure)."&rsaquo;".afficheHeure(floor($finr),$finr,$formatHeure) : trad('COMMUN_JOURNEE_ENTIERE');
        // Info a afficher dans le popup
        $libelleNote = htmlspecialchars($enr['age_libelle']).((!empty($enr['age_lieu'])) ? "<BR><I>(".htmlspecialchars($enr['age_lieu']).")</I>" : "");
        $detailNote = htmlspecialchars(nlTObr($enr['age_detail']));
        $noteCrt = 0;
        $noteCpt = 0;
        $olibelleNote = "";
        $odetailNote = "";
        $oiCell = "";
        if (!$JourType) {
          for ($i=floor($enr['age_heure_debut']); $i<min($finr,$enr['age_heure_fin']); $i+=0.25) {
            // Si precision 30mn et note deja commence a etre affichee (pour afficher les notes commencant a la 15 ou 45eme mn) et si on est sur une tranche 15 ou 45
            if ($noteCrt!=$enr['age_id'] || ($noteCrt==$enr['age_id'] && (($i-floor($i)==0) || ($i-floor($i)==0.25) || ($i-floor($i)==0.5) || ($i-floor($i)==0.75)))) {
              // Indice de la cellule du tableau a manipuler
              $iCell = floor($i-floor($iHeureMin));
              if ($oiCell != $iCell) {
                $olibelleNote = "";
                $odetailNote = "";
              }
              if ($olibelleNote != $libelleNote && $odetailNote != $detailNote) {
                $aJournee[$iCell][3] = $enr[age_id];
                // Saut de ligne si plusieurs notes sur la meme case
                $aJournee[$iCell][2] = ($aJournee[$iCell][0] != "0") ? "*" : "&nbsp;";
                if ($aJournee[$iCell][2]=="*") {
                  $PlColor = true;
                  $PlNote = true;
                  $aJournee[$iCell][5]++;
                  if ($aJournee[$iCell][5]<10)
                    $aJournee[$iCell][2] = $aJournee[$iCell][5];
                } else
                  $PlColor = false;
                if (($hprf0 == true || $hprf1 == true) && $PlColor==true)
                  $aJournee[$iCell][2] = "H&nbsp;*";
                if (($hprf0 == true || $hprf1 == true) && $PlColor==false)
                  $aJournee[$iCell][2] = "H";
                // Contenu du popup
                if (($sUtilID==$idUser) || (!$notePrive && ($AutoAffectT || ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR)))) {
                  $lien = "";
                  if ($enr['aco_termine'] == 1) {
                    $styleNote = ($NOTE_BARREE) ? "line-through" : "none";
                    $TitleTemoin = trad("AGENDA_LEGENDE_NOTE_TERMINEE");
                    $imgTemoin = "puce_ok.gif";
                  } else {
                    $styleNote = "none";
                    $TitleTemoin = trad("AGENDA_LEGENDE_NOTE_ACTIVE");
                    $imgTemoin = "puce_ko.gif";
                  }
                  $puce = "&nbsp;<IMG src='image/".$imgTemoin."' width='6' height='6' border='0' title='".$TitleTemoin."'>&nbsp;";
                  // Indique si un rappel a ete programme (si le statut de la note n'est pas "terminee")
                  if ($enr['aco_termine']!=1 && $enr['age_rappel']>0) {
                    $rappel = trad('COMMUN_RAPPEL')." ".$enr['age_rappel']." ";
                    if ($enr['age_rappel_coeff']==1)
                      $rappel .= trad('COMMUN_MINUTE');
                    elseif ($enr['age_rappel_coeff']==60)
                      $rappel .= trad('COMMUN_HEURE');
                    else
                      $rappel .= trad('COMMUN_JOUR');
                    $rappel .= " ".trad('COMMUN_AVANCE');
                    $lien .= "<IMG src='image/rappel.gif' border='0' align='absmiddle' title='".$rappel."'>&nbsp;";
                  }
                  // Modification d'une note
                  $lien .= ($droitModifNotePerso || $droitModifNoteAffectee) ? "<A href='javascript: affNoteG(".$aJournee[$iCell][3].",".$sUtilID.");'><IMG src='image/modif.gif' width='13' height='13' border='0' vspace='1' align='absmiddle' title='".trad('PLGL_MOD_NOTE')."'></A>" : "";
                  // Appropriation d'une note
                  $lien .= ($droitApprNote) ? "<A href='javascript: apprNote(".$enr['age_id'].");'><IMG src='image/appropriation.gif' border='0' align='absmiddle' title='".trad('PLGL_APPROPRIATION')."'></A>" : "";
                  // Suppression d'une note
                  if ($enr['age_ape_id']!=1) {
                    // MOD Scission de note
                    if (($droitModifNotePerso || ($droitModifNoteAffectee && !$notePrive)) && $AUTORISE_SCISSION) {
                      if ($enr['age_mere_id'])
                        $lien .= "&nbsp;<A href='javascript: scindeOcc(".$enr['age_id'].");'><IMG src='image/scinder.gif' width='11' height='13' border='0' align='absmiddle' title='".trad('COMMUN_SCISSION_NOTE')."'></A>";
                    }
                    // Fin MOD Scission de note
                    if ($droitSuppNoteCreee)
                      $lien .= "&nbsp;<A href='javascript: supprOcc(".$enr['age_id'].",0);'><IMG src='image/recurrent.gif' width='13' height='11' border='0' align='absmiddle' title='".trad('COMMUN_SUPPR_OCCURENCE')."'></A>";
                    else
                      $lien .= "&nbsp;<IMG src='image/recurrent.gif' width='13' height='11' border='0' align='absmiddle' title='".trad('COMMUN_NOTE_RECURRENTE')."'></A>";
                    }
                  // Suppression d'une occurrence
                  if ($droitSuppNoteCreee)
                    $lien .= "&nbsp;<A href='javascript: supprOcc(".(($enr['age_mere_id']) ? $enr['age_mere_id'] : $enr['age_id']).",1);'><IMG src='image/suppr.gif' width='12' height='12' border='0' align='absmiddle' title='".trad('COMMUN_SUPPR_NOTE')."'></A>";
                  elseif ($droitSuppNoteAffectee)
                    $lien .= "&nbsp;<A href='javascript: supprOcc(".$enr['age_id'].",2);'><IMG src='image/suppr.gif' width='12' height='12' border='0' align='absmiddle' title='".trad('COMMUN_SUPPR_NOTE')."'></A>";
                  $aJournee[$iCell][4] = (($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR) ? false : true);
                } else {
                  $aJournee[$iCell][4]=true;
                  $lien = "";
                }
                $aJournee[$iCell][0] .= "<TABLE width='100%' style='border-top: solid 1px ".$AgendaBordureTableau."; border-bottom:solid 1px ".$AgendaBordureTableau.";' cellpadding=0 cellspacing=0><TR style='background-color:".$enr['age_couleur']."' class='PopUpGbl'><TD height='13' valign='top' nowrap><B>".$plageNote."</B>&nbsp;:".$puce."</TD><TD width='100%'><span style='text-decoration: ".$styleNote.";'><A style='font-weight:normal'>".$libelleNote."</SPAN></TD><TD nowrap>".$lien."</TD></TR></TABLE><TABLE width='100%' border=0 cellpadding=2 cellspacing=0><TR><TD class='ibTexte'>".$detailNote."</TD></TR></TABLE>";
                // Couleur de la case
                $olibelleNote = $libelleNote;
                $odetailNote = $detailNote;
                $oiCell = $iCell;
                $aJournee[$iCell][1] = $enr['age_couleur'];
                if ($PlColor==true)
                  $aJournee[$iCell][1] = $PlanningPartiel;
                if ($hprf0 == true || $hprf1 == true)
                  $aJournee[$iCell][1] = $AgendaBordureTableau;
                // Note en cours de lecture
                $noteCrt = $enr['age_id'];
              }
            } else {
              $noteCrt = 0;
            }
          }
        }
      }
      // Affichage de la journee de l'utilisateur
      for ($i=($iHeureMin-1); $i<floor($aHeureDebutJourneeUtil); $i++)
        if ($aJournee[floor($i-$iHeureMin)][0]=="0")
          $aJournee[floor($i-$iHeureMin)][0] = "2";
      for ($i=floor($aHeureFinJourneeUtil+0.75); $i<=ceil($iHeureMax); $i++)
        if ($aJournee[floor($i-$iHeureMin)][0]=="0")
          $aJournee[floor($i-$iHeureMin)][0] = "2";
      $Class="";
      for ($i=0;$i<$iDureeJournee;$i++) {
        if (($sUtilID==$idUser && !$aJournee[$i][4]) || (!$aJournee[$i][4] && (($aAutoAffect[$sUtilID]==1) || ($droit_NOTES >= _DROIT_NOTE_MODIF_CREATION and $droit_AGENDAS >= _DROIT_AGENDA_TOUS)))) {
          $lienNote = " onclick=\"javascript: stc1('".trad('PLGL_LEGENDE_DETAIL')."','".addslashes(substr($aJournee[$i][0],1))."'); return false;\"";
          $cursor = "pointer ";
        } else {
          $lienNote = "";
          $cursor = "default";
        }
        if ($aJournee[$i][0]!="0") {
          $BordCouleur = ($aJournee[$i][0]=="2") ? $AgendaBordureTableau : $aJournee[$i][1];
          if (($aJournee[$i][0]==$aJournee[$i+1][0]) or ($i==$iDureeJournee-1))  {
            if (($i==0) or ($aJournee[$i][0]==$OldaJournee) or ($Class == "border-bottom:solid 1px ".$AgendaBordureTableau.";")) {
              $Class = "border-bottom:solid 1px ".$BordCouleur.";";
              $Class1 = "border-top:solid 1px ".$BordCouleur.";";
            } else {
              $Class = "border-bottom:solid 1px ".$BordCouleur.";";
              $Class1 = "border-top:solid 1px ".$AgendaBordureTableau.";";
            }
          } else {
            if (($i==0) or ($aJournee[$i][0]==$OldaJournee) or ($Class == "border-bottom:solid 1px ".$AgendaBordureTableau.";")) {
              $Class = "border-bottom:solid 1px ".$AgendaBordureTableau.";";
              $Class1 = "border-top:solid 1px ".$BordCouleur.";";
            } else {
              $Class = "border-bottom:solid 1px ".$AgendaBordureTableau.";";
              $Class1 = "border-top:solid 1px ".$AgendaBordureTableau.";";
            }
          }
        }
        $AffTxt = (($aJournee[$i][0]!=$OldaJournee) ? true : false);
        $OldaJournee = $aJournee[$i][0];
        if ($aJournee[$i][0]!="2" && $aJournee[$i][0]!="0") {
          $popup = $lienNote." onmouseover=\"javascript: mtc1('".trad('PLGL_LEGENDE_DETAIL')."','".addslashes(substr($aJournee[$i][0],1))."'); return false;\" onmouseout=\"javascript: nd(); return true;\"";
          $sOutput .= "    <DIV style=\"".$Class.$Class1." cursor:".$cursor."; text-align:left; vertical-align:top; height:".$hdiv."px; font-size:8px; font-weight:normal; color:".$PlanningInvalideTexte."; background-color:".$aJournee[$i][1].";\"".$popup.">".(!$AffTxt ? "" : "<IMG align='abmiddle' src='image/suivant_n.gif'>".$aJournee[$i][2])."</DIV>"; //.$colspan
        } elseif ($aJournee[$i][0]=="2") {
          if ($aJournee[$i][2]=="H") {
            $popup = $lienNote." onmouseover=\"javascript: mtc1('".trad('PLGL_LEGENDE_DETAIL')."','".addslashes(substr($aJournee[$i][0],1))."'); return false;\" onmouseout=\"javascript: nd(); return true;\"";
            $sOutput .= "    <DIV style=\"".$Class.$Class1." cursor:default; text-align:left; height:".$hdiv."px; font-size: 8px; color:".$PlanningInvalideTexte."; background-color:".$AgendaBordureTableau.";\"".$popup."><IMG align='abmiddle' src='image/suivant_n.gif'>&nbsp;".$aJournee[$i][2]."</DIV>"; //.$colspan
          } else {
            $sOutput .= "    <DIV style=\"".$Class.$Class1." cursor:default; height:".$hdiv."px; font-size:8px; font-weight:normal; color:".$PlanningInvalideTexte."; background-color:".$AgendaBordureTableau.";\">&nbsp;</DIV>\n";
          }
        } else {
          $hFin = colSpanH($i+1,$iDureeJournee);
          $BordCouleur = ($classCel == "mensJour") ? $CalJourSelection : $bgColor[$i%2];
          $Class = ($hFin<=$iDureeJournee) ? " border-bottom:solid 1px ".$BordCouleur.";" : "border-bottom:solid 1px ".$AgendaBordureTableau.";";
          $Class1 = ($hFin<=$iDureeJournee) ? "border-top:solid 1px ".$BordCouleur.";" : "border-top:solid 1px ".$BordCouleur.";";
          if ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR && (($sUtilID==$idUser) || (($aAutoAffect[$sUtilID]==1) || ($droit_NOTES >= _DROIT_NOTE_MODIF_CREATION and $droit_AGENDAS >= _DROIT_AGENDA_TOUS)))) {
            $cursor = "pointer ";
            $sd1 = mktime(0,0,0,$moisEnCours,$premierJourSemaine+$Jsem,$anneeEnCours);
            $T_Note=(floor($i+$iHeureMin) < $aHeureDebutJourneeUtil ? $aHeureDebutJourneeUtil : floor($i+$iHeureMin));
            $sOutput .= "    <DIV style=\"".$Class.$Class1."cursor:".$cursor."; height:".$hdiv."px; font-size:8px; font-weight:normal; background-color:".$BordCouleur.";\" onclick=\"javascript: nvNoteG('".$tsJour."','".$T_Note."','','".$sUtilID."','".(($tzPartage=="O") ? $sUtilID : "")."');\" title=\"".sprintf(trad("PLGL_CREER_NOTE"), afficheHeure($T_Note,$T_Note))."\">&nbsp;</DIV>\n";
          } else {
            $lienNote = "";
            $cursor = "default";
            $sOutput .= "    <DIV style=\"".$Class.$Class1."cursor:".$cursor."; height:".$hdiv."px; font-size:8px; font-weight:normal; background-color:".$BordCouleur."\">&nbsp;</DIV>\n";
          }
          // On avance l'indice de la boucle
          $i = $hFin-1;
        }
        // Reinitialisation du tableau pour le jour suivant
        $aJournee[$i][0] = "0";
        $aJournee[$i][1] = "";
        $aJournee[$i][2] = "&nbsp;";
        $aJournee[$i][3] = "";
        $aJournee[$i][4] = false;
        $aJournee[$i][5] = 1;
      }
      $tabAffichage[$nbJour][0] = "    <TD class=\"".$classCel."\">".$sOutput."</TD>\n";
      $tabAffichage[$nbJour][1] = ($iDureeJournee)*$hdiv;
      //FIN Fonction gerant l'affichage des cases du tableau
    }

    //Affichage du planning global mensuel
    echo "  <BR><TABLE cellspacing=\"0\" cellpadding=\"0\" width=\"99%\" border=\"0\">\n";
    //On mappe la semaine type (transformee dans agenda_calendrier)  au format PHP (Dim->Sam)
    $SEMAINE_CALENDRIER = substr($SEMAINE_CALENDRIER,6).substr($SEMAINE_CALENDRIER,0,6);
    // Affichage des utilisateurs selectionnes
    $NblignUtil = 0;

    while (list($sId,$sNom)=each($aUtilPartage)) {
      if (($NblignUtil % 10)==0) {
        if ($NblignUtil==0)
          echo "<TR>\n    <TD height=\"18\">&nbsp;</TD><TD height=\"18\">&nbsp;</TD>\n";
        else
          echo  "<TR>\n    <TD style=\"background-color:".$PlanningJour."; border-left:solid 1px ".$AgendaBordureTableau.";\">&nbsp;</TD><TD style=\"background-color:".$PlanningJour.";\">&nbsp;</TD>\n";
       //Entete du tableau
        for ($i=1; $i<=$nbJourMois; $i++) {
          $ferie = $i."-".$moisEnCours;
          //Coloration des jours feries
          if (in_array($ferie,$tabJourFerie)) {
            $classCel2 = "mensFerie";
          } elseif (!empty($tabEvenementDate[$i])) {
            $classCel2 = "mensEvenement";
          } else {
            $classCel2 = "mensJour";
          }

          if ($anneeEnCours."-".$moisEnCours."-".$i == date("Y-m-j",$localTime))
            $classCel2 = "mensJour";

          $tsJour = mktime(0,0,0,$moisEnCours, $i, $anneeEnCours);
          $idxJour = date("w",$tsJour);
          if (substr($SEMAINE_CALENDRIER,$idxJour,1)=="0") {
            $could = $CalJourWE;
          } else {
            $could = $AgendaTexte;
          }
          if ($i < 10)
            $i = "0".$i;
          echo "    <TD align=\"center\" width=\"3%\" height=\"18\" class=\"".$classCel2."\"><A href=\"javascript: affSemaine('".$tsJour."');\" title=\"".trad("PLGL_AFF_SEM")."\"><FONT color=\"".$could."\">".$tabJour3[$idxJour]."</FONT></A><BR><A href=\"javascript: affJour('".$tsJour."');\" title=\"".trad("PLGL_AFF_JOUR")."\"><FONT color=\"".$could."\"><B>".$i."</B></FONT></A></TD>\n";
        }
        echo "  </TR>\n";
        // Affichage des utilisateurs selectionnes
      }
      $NblignUtil++;

      // Pour chaque utilisateur on verifie si on a acces a son planning en modification
      $DB_CX->DbQuery("SELECT util_id FROM ${PREFIX_TABLE}utilisateur LEFT JOIN ${PREFIX_TABLE}planning_affecte ON paf_util_id=util_id WHERE util_id=".$sId." AND (util_autorise_affect='1' OR (util_autorise_affect IN ('2','3') AND paf_consultant_id=".$idUser."))");
      if (($DB_CX->DbNumRows() && ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR)) || ($droit_NOTES >= _DROIT_NOTE_MODIF_CREATION and $droit_AGENDAS >= _DROIT_AGENDA_TOUS) || $sId==$idUser) {
        $autoriseAffect = true;
        $aAutoAffect[$sId] = 1;
      } else {
        $autoriseAffect = false;
        $aAutoAffect[$sId] = 0;
      }
      if ($tzPartage=="O")
        $styleUTC = "<BR><SPAN style=\"font-weight:normal;font-style:normal;\"><SMALL>".sprintf(trad("PLGL_FUSEAU_MENSUEL"), (($aUtiltzGmt[$sId]<0) ? "-" : "+").afficheHeure(floor(abs($aUtiltzGmt[$sId])),abs($aUtiltzGmt[$sId])), $aUtiltzLibelle[$sId])."</SMALL></SPAN>";
      else
        $styleUTC = "";
      // Pour chaque utilisateur on verifie si on a acces a son planning en consultation
      $DB_CX->DbQuery("SELECT util_id FROM ${PREFIX_TABLE}utilisateur LEFT JOIN ${PREFIX_TABLE}planning_partage ON ppl_util_id=util_id WHERE util_id=".$sId." AND (util_partage_planning='1' OR (util_partage_planning='2' AND ppl_consultant_id=".$idUser."))");
      if (($DB_CX->DbNumRows() && ($droit_AGENDAS < _DROIT_AGENDA_TOUS)) || ($droit_AGENDAS >= _DROIT_AGENDA_TOUS) || $sId==$idUser) {
        $output = "    <TD class=\"nomUtil\" style=\"padding-left:3px;padding-right:3px;\" height=\"43\" valign=\"middle\"><A href=\"javascript: substUser('".$sId."');\" title=\"".trad("PLGL_PLANNING_UTIL")."\">".$sNom."</A>".$styleUTC."</TD>\n";
        $autoriseConsult = true;
      } else {
        $output = "    <TD class=\"nomUtil\" style=\"font-weight:normal;font-style:italic;padding-left:3px;padding-right:3px;\" height=\"43\" valign=\"middle\">".$sNom.$styleUTC."</TD>\n";
        $autoriseConsult = false;
      }

      if ($autoriseConsult || ($ckAffCache=="O")) {
        echo "  <TR>\n";
        echo $output;
        // Variable pour stocker la hauteur de la case la plus importante
        for ($i=1; $i<=$nbJourMois; $i++) {
          $leJour = ($i < 10) ? $anneeEnCours."-".$moisEnCours."-0".$i : $anneeEnCours."-".$moisEnCours."-".$i;
          // Journee hors profil semaine type => indisponibilite toute la journee
          if (substr($vSemaineType[$sId],date("w",mktime(0,0,0,$moisEnCours, $i, $anneeEnCours)),1)=="0") {
            $aHeureDebutJourneeUtilTmp = $aHeureFinJourneeUtil[$sId];
          } else {
            $aHeureDebutJourneeUtilTmp = $aHeureDebutJourneeUtil[$sId];
          }
          afficheCase($leJour, $i, $sId, $tzPartage, $aHeureDebutJourneeUtilTmp, $aHeureFinJourneeUtil[$sId]);
        }
        $sOutput="";
        for ($j=0;$j<($iHeureMax-$iHeureMin);$j++) {
          $heure = strval(floor($iHeureMin+$j));
          if (($formatHeure == "h:ia" and $heure > 12))
            $heure=$heure-12;
            $sOutput .= "    <DIV style=\"text-align:right; border-top:solid 1px ".$bgColor[$j%2]."; border-bottom:solid 1px ".$bgColor[$j%2]."; cursor:default; height:".$hdiv."px; font-size:8px; font-weight:normal; background:".$bgColor[$j%2].";\">&nbsp;".$heure."&nbsp;</DIV>\n";
        }

        echo "    <TD class=\"nomUtil\">".$sOutput."    </TD>\n";

        for ($j=1; $j<=$nbJourMois; $j++) {
          // On recupere la hauteur de la case courante stockee dans le tableau
          $hauteurCase = -$tabAffichage[$j][1];
          // On determine la hauteur du dernier DIV en fonction de la hauteur de la plus grande case (si applicable)
          echo str_replace("{hauteurMax}",$hauteurCase,$tabAffichage[$j][0]);
        }
        echo "  </TR>\n";
        // Reinitialisation du tableau pour l'utilisateur suivant
        unset($tabAffichage);
      }
    }
    echo "  </TABLE>\n";
    if ($tzPartage=="O")
      echo "  <DIV class=\"timezone\">".trad("PLGL_FUSEAU_ACTUEL")."</DIV>\n";
    else
      echo "  <DIV class=\"timezone\">".sprintf(trad("COMMUN_FUSEAU_ACTUEL"), (($tzGmt<0) ? "-" : "+").afficheHeure(floor(abs($tzGmt)),abs($tzGmt)), $tzLibelle)."</DIV>\n";
    echo ("  <BR>
  <TABLE cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" border=\"0\">
  <TR align=\"center\" height=\"20\">
    <TD height=\"28\" align=\"center\"><TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
      <TR>
        <TD height=\"4\"><IMG src=\"image/trans.gif\" width=\"1\" height=\"4\" alt=\"\" border=\"0\"></TD>
      </TR>
      <TR>
        <TD class=\"bordTLRB\" bgcolor=\"".$CalJourSelection."\" align=\"center\" width=\"90\" nowrap>".trad("PLGL_JOUR_COURANT")."</TD>
        <TD>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
        <TD class=\"bordTLRB\" bgcolor=\"".$CalJourFerie."\" align=\"center\" width=\"90\" nowrap>".trad("PLGL_JOUR_FERIE")."</TD>
        <TD>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
        <TD class=\"bordTLRB\" bgcolor=\"".$CalJourEvenement."\" align=\"center\" width=\"90\" nowrap>".trad("COMMUN_EVENEMENT")."</TD>
        <TD>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
        <TD class=\"bordTLRB\" bgcolor=\"".$PlanningJour."\" style=\"color:".$CalJourWE."\" align=\"center\" width=\"110\" nowrap>".(($SEMAINE_CALENDRIER=="0111110") ? "".trad("PLGL_WEEKEND")."" : "".trad("PLGL_HORS_SEMAINE")."")."</TD>");
    if ($PlNote==true) {
      echo ("        <TD>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
        <TD class=\"bordTLRB\" style=\"cursor:default; color:".$PlanningInvalideTexte."; background-color:".$PlanningPartiel.";\" align=\"center\" width=\"110\" nowrap>".trad('PLGL_PL_NOTES')."");
    }
    if ($PfNote==true) {
      echo ("        <TD>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
        <TD class=\"bordTLRB\" style=\"cursor:default; color:".$PlanningInvalideTexte."; background-color:".$AgendaBordureTableau.";\" align=\"center\" width=\"130\" nowrap>".trad('PLGL_HPROFIL_N')."</TD>");
    } else {
      echo ("        <TD>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
       <TD class=\"bordTLRB\" style=\"cursor:default; color:".$PlanningInvalideTexte."; background-color:".$AgendaBordureTableau.";\" align=\"center\" width=\"130\" nowrap>".trad('PLGL_HPROFIL')."</TD>");
    }
    echo ("         <TD>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
        <TD class=\"legendeBis\"><IMG src=\"image/puce_ko.gif\" width=\"6\" height=\"6\" alt=\"\" border=\"0\" align=\"absmiddle\">&nbsp;".trad("AGENDA_LEGENDE_NOTE_ACTIVE")."</TD>
        <TD>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
        <TD class=\"legendeBis\"><IMG src=\"image/puce_ok.gif\" width=\"6\" height=\"6\" alt=\"\" border=\"0\" align=\"absmiddle\">&nbsp;".trad("AGENDA_LEGENDE_NOTE_TERMINEE")."</TD>
      </TR>
    </TABLE></TD>
  </TR>
  </TABLE>\n");
?>
      <BR>
      <BR>
      <TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
      <TR align="center">
        <TD height="20" colspan="2" class="legende">&nbsp;<IMG src="image/rappel.gif" alt="" border="0" align="absmiddle">&nbsp;<?php echo trad('PLGL_LEGENDE_NOTE_RAPPEL'); ?>&nbsp;&nbsp;&nbsp;<IMG src="image/modif.gif" width="13" height="13" alt="" border="0" align="absmiddle">&nbsp;<?php echo trad('PLGL_LEGENDE_MOD_NOTE'); ?>&nbsp;&nbsp;&nbsp;<IMG src="image/recurrent.gif" width="13" height="11" alt="" border="0" align="absmiddle">&nbsp;<?php echo trad('PLGL_LEGENDE_SUP_OCCURENCE'); ?>&nbsp;&nbsp;&nbsp;<IMG src="image/suppr.gif" alt="" width="12" height="12" border="0" align="absmiddle">&nbsp;<?php echo trad('PLGL_LEGENDE_SUP_NOTE'); ?>&nbsp;&nbsp;&nbsp;<IMG src="image/appropriation.gif" alt="" width="13" height="11" border="0" align="absmiddle">&nbsp;<?php echo trad('PLGL_LEGENDE_APPROPRIER_NOTE'); ?>&nbsp;&nbsp;<IMG src="image/contact.gif" alt="" width="10" height="11" border="0" align="absmiddle">&nbsp;<?php echo trad('PLGL_LEGENDE_CONTACT_ASSOCIE'); ?></TD>
      </TR>
    </TABLE>
  <BR>
<?php
  }
  // FIN SI la liste contenant les identifiants des utilisateurs selectionnes n'est pas vide
  $sChoix="";
?>
<!-- FIN MODULE PLANNING MENSUEL GLOBAL -->
