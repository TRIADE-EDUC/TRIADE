<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5_Scission_de_note_recurente.txt ?>
<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5.5_Aide.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_Meteo_today_ico.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_Impression_Note.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_horoscope_hebdo.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_DD.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5.5_Emplacement_Plus.txt ?>
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
  ?> <SCRIPT> HelpPhenixCtx="{E78236EE-3D7C-4A38-86DF-B9C6A8993542}.htm"; </SCRIPT> <?php
  // Mod Aide
  // Mod D&D
  $DB_CX->DbQuery("SELECT util_dd FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$idUser);
  $util_dd = $DB_CX->DbResult(0,0);  
  if (!$callByDDUpdate) {
    // D&D : est ce que l'utilisateur a le droit de modifier l'agenda en cours
    function affDD($note_type) {
	global $util_dd;	
    $dd_ico = "";
    if ($util_dd==1) $dd_ico = "&nbsp;<IMG style=\"cursor:pointer;\" src=\"image/move.gif\" width=\"12\" height=\"12\" border=\"0\" align=\"absmiddle\" title=\"".trad("COMMUN_DD_DEPLACE_NOTE")."\">";
    return $dd_ico;
    }
    if (($util_dd==1) and ((($USER_SUBSTITUE==$idUser || $AFFECTE_NOTE) and ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR)) or ($droit_NOTES >= _DROIT_NOTE_MODIF_CREATION)))
    {
     $note_infos = array();
      ?>
      <SCRIPT type="text/javascript" src="inc/prototype.js"></SCRIPT>
      <SCRIPT type="text/javascript" src="inc/rico.js"></SCRIPT>
      <?php
    }
  }
  // Fin Mod D&D

  if (intval($moisEnCours)==1) {
    $lienAvant = "'12','".($anneeEnCours-1)."','2'";
    $lienApres = "'".($moisEnCours+1)."','".$anneeEnCours."','2'";
  } elseif (intval($moisEnCours)==12) {
    $lienAvant = "'".($moisEnCours-1)."','".$anneeEnCours."','2'";
    $lienApres = "'1','".($anneeEnCours+1)."','2'";
  } else {
    $lienAvant = "'".($moisEnCours-1)."','".$anneeEnCours."','2'";
    $lienApres = "'".($moisEnCours+1)."','".$anneeEnCours."','2'";
  }
?>

<!-- MODULE PLANNING MENSUEL -->
<?php
  // Mod D&D
  if (!$callByDDUpdate) {
?>
<script language="javascript">
<!--
  var oldColorWeek = '';
  var oldColorJour = '';
  var oldColorCell = '';
  var newColor = '<?php echo $AgendaLigneHover; ?>';
  function swapColor(pCell, pLigne, pColonne, pMouseOver) {
    var trLigne = document.getElementById('ligne' + pLigne);
    var cellJour = document.getElementById('colonne' + pColonne);
    if (pMouseOver) {
      //sauver couleur debut ligne + colonne + cellule
      oldColorWeek = trLigne.style.backgroundColor;
      oldColorCell = pCell.style.backgroundColor;
      oldColorJour = cellJour.style.backgroundColor;
      //Nouvelle couleur
      trLigne.style.backgroundColor=newColor;
      cellJour.style.backgroundColor=newColor;
      pCell.style.backgroundColor=newColor;
    } else {
      //restauration des couleurs
      trLigne.style.backgroundColor=oldColorWeek;
      cellJour.style.backgroundColor=oldColorJour;
      pCell.style.backgroundColor=oldColorCell;
    }
  }
//-->
</script>
  <FORM action="<?php echo "?sid=".$sid."&tcMenu=".$tcMenu."&sd=".$sd; ?>" method="post">
  <TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
  <TR>
    <TD width="100%" height="28" nowrap class="sousMenu" style="font-size:10px;"><LABEL for="lundi"><INPUT type="checkbox" name="bt1" value="1"<?php if ($bt1==1) echo " checked"; ?> class="case" id="lundi">&nbsp;<?php echo trad("COMMUN_LUNDI");?></LABEL>&nbsp;&nbsp;
      <LABEL for="mardi"><INPUT type="checkbox" name="bt2" value="1"<?php if ($bt2==1) echo " checked"; ?> class="case" id="mardi">&nbsp;<?php echo trad("COMMUN_MARDI");?></LABEL>&nbsp;&nbsp;
      <LABEL for="mercredi"><INPUT type="checkbox" name="bt3" value="1"<?php if ($bt3==1) echo " checked"; ?> class="case" id="mercredi">&nbsp;<?php echo trad("COMMUN_MERCREDI");?></LABEL>&nbsp;&nbsp;
      <LABEL for="jeudi"><INPUT type="checkbox" name="bt4" value="1"<?php if ($bt4==1) echo " checked"; ?> class="case" id="jeudi">&nbsp;<?php echo trad("COMMUN_JEUDI");?></LABEL>&nbsp;&nbsp;
      <LABEL for="vendredi"><INPUT type="checkbox" name="bt5" value="1"<?php if ($bt5==1) echo " checked"; ?> class="case" id="vendredi">&nbsp;<?php echo trad("COMMUN_VENDREDI");?></LABEL>&nbsp;&nbsp;
      <LABEL for="samedi"><INPUT type="checkbox" name="bt6" value="1"<?php if ($bt6==1) echo " checked"; ?> class="case" id="samedi">&nbsp;<?php echo trad("COMMUN_SAMEDI");?></LABEL>&nbsp;&nbsp;
    <LABEL for="dimanche"><INPUT type="checkbox" name="bt7" value="1"<?php if ($bt7==1) echo " checked"; ?> class="case" id="dimanche">&nbsp;<?php echo trad("COMMUN_DIMANCHE");?></LABEL>&nbsp;&nbsp;&nbsp;</TD>
    <TD align="right" nowrap class="sousMenu" style="text-align:right;"><?php genereListeCouleur(); ?>&nbsp;&nbsp;<?php
  if ($nbJSelect)
    echo "&nbsp;<A href=\"javascript: parent.imprime('".$tcMenu."','".$sd."','".urlencode(str_replace("#","!",$FILTRE_COULEUR))."');\"><IMG src=\"image/impression.gif\" width=\"23\" height=\"21\" border=\"0\" align=\"absmiddle\" title=\"".trad("MENSUEL_IMPRIMER")."\"></A>&nbsp;&nbsp;";
?>
    </TD>
  </TR>
  </TABLE>
  </FORM>
  <BR>
<?php // MOD D&D ?>
  <div id="tableau">
<?php
  }
  // Fin Mod D&D
  if ($nbJSelect) {
  //Si l'utilisateur a choisi une couleur de note on l'ajoute dans la clause WHERE de la recherche
  $whereCouleur = "";
  if ($FILTRE_COULEUR != "ALL" && !empty($FILTRE_COULEUR)) {
    $whereCouleur = ($FILTRE_COULEUR == $AgendaFondNotePerso) ? " AND (age_couleur='".$FILTRE_COULEUR."' OR age_couleur='')" : " AND age_couleur='".$FILTRE_COULEUR."'";
  }

  function afficheCase($leJour, $nbJour, $mPrec, $jLien, $iLigne, $iColonne) {
    global $DB_CX, $PREFIX_TABLE, $MODIF_PARTAGE, $AUTORISE_SUPPR, $USER_SUBSTITUE, $AFFECTE_NOTE, $NOM_UTIL_CREATEUR, $NOM_UTIL_MODIFICATEUR, $FORMAT_NOM_CONTACT, $NOTE_BARREE, $FORMAT_NOM_CONTACT;
    global $idUser, $sid, $sd, $tcMenu, $AgendaFondNotePerso, $AgendaFondNote, $AgendaTexteTitrePopup, $moisEnCours, $anneeEnCours;
    global $tabJourFerie, $whereCouleur, $CalJourFerie, $bgColor, $CalJourSelection, $CalFond, $PlanningNotePrivee;
    global $tabEvenementDate, $CalJourEvenement, $AgendaContactPopup;
    global $tzGmt, $tzDateEte, $tzHeureEte, $tzDateHiver, $tzHeureHiver, $localTime, $formatHeure;
    global $droit_PROFILS, $droit_AGENDAS, $droit_NOTES;
    // MOD Scission de note
    global $AUTORISE_SCISSION;
    // Fin MOD Scission de note
	// Mod D&D
	global $note_infos,$util_dd;
	// Fin Mod D&D
    // On regarde si le jour a afficher appartient au mois courant
    if ($mPrec==0) {
      $tsJour = mktime(0,0,0,$moisEnCours, $nbJour, $anneeEnCours);
      //Coloration des jours feries
      if (in_array(date("j-m",$tsJour),$tabJourFerie)) {
        $styl = "mensFerie";
        $bkColor = $CalJourFerie;
      } elseif (!empty($tabEvenementDate[$nbJour+0])) {
        $styl = "mensEvenement"; $bkColor = $CalJourEvenement;
      } else {
        $styl = "mensNote"; $bkColor = $bgColor[0];
      }
      if ($leJour==date("Y-m-d",$localTime)) {
        $classCel = "mensJour";
        $bgColorCell = $CalJourSelection;
      } else {
        $classCel = $styl;
        $bgColorCell = $bkColor;
      }
      $styleLien = "><B>".$nbJour."</B>";
    } else {
      $tsJour = mktime(0,0,0,$moisEnCours+$mPrec, $nbJour, $anneeEnCours);
      if ($leJour==date("Y-m-d",$localTime)) {
        $classCel = "mensJour";
        $bgColorCell = $CalJourSelection;
      } else {
        $classCel = "mensPrec";
        $bgColorCell = $CalFond;
      }
      $styleLien = " class=\"jMoisPrec\">".$nbJour;
    }
    $ligneEvenement = $ligneAnniv = $ligneNote = "";
    // MOD meteo
    $ligneEvenement .= aff_meteo_j(date("M j",$tsJour))."&nbsp;";
   // fin mod meteo
    // MOD horoscope
    $ligneEvenement .= aff_horoscope(date("M j",$tsJour))."&nbsp;";
    // fin mod horoscope
    // Savoir si on affiche ou non le lien pour creer une nouvelle note
    // Mod D&D
    $opt = $leJour;	
	if ($util_dd==1) $dd_script = "<script>dndMgr.registerDropZone( new Rico.Dropzone('droponme".$iLigne.$iColonne."','".$opt."') );</script>";
	// Fin Mod D&D
    $lienAjout = ((($USER_SUBSTITUE==$idUser || $AFFECTE_NOTE) and ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR)) or ($droit_NOTES >= _DROIT_NOTE_MODIF_CREATION)) ? "&nbsp;<A href=\"javascript: nvNote('".$tsJour."','','');\"><IMG src=\"image/ajout_note.gif\" width=\"13\" height=\"15\" border=\"0\" align=\"top\" vspace=\"1\" hspace=\"1\" title=\"".trad("MENSUEL_AJOUT_NOTE_J")."\"></A>" : "";
    // Evenements du jour
    $DB_CX->DbQuery("SELECT DISTINCT eve_id, eve_libelle, eve_util_id, eve_type, DATE_FORMAT(eve_date_debut,'%d/%m/%Y') AS dateDebut, DATE_FORMAT(eve_date_fin,'%d/%m/%Y') AS dateFin FROM ${PREFIX_TABLE}evenement WHERE DATE_FORMAT(eve_date_debut,'%Y%m%d')<='".date("Ymd",$tsJour)."' AND DATE_FORMAT(eve_date_fin,'%Y%m%d')>='".date("Ymd",$tsJour)."'".(($USER_SUBSTITUE==$idUser) ? " AND (eve_util_id=".$idUser." OR eve_partage='O')" : " AND eve_partage='O'"));
    while ($enr = $DB_CX->DbNextRow()) {
      $dureeEvent = ($enr['dateDebut']!=$enr['dateFin']) ? "<BR>".sprintf(trad("COMMUN_DUREE_EVENEMENT"), $enr['dateDebut'], $enr['dateFin']) : "";
      $lienModif = (($MODIF_PARTAGE || $enr['eve_util_id']==$idUser) && ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR)) ? " href=\"javascript: affEvent('".$enr['eve_id']."')\"" : "";
      $ligneEvenement .= "<A".$lienModif."><IMG src=\"image/evenement/evenement".$enr['eve_type'].".gif\" width=\"15\" height=\"15\" border=\"0\" align=\"absmiddle\" vspace=\"1\"".infoPopup($enr['eve_libelle'].$dureeEvent)."></A>&nbsp;";
    }
    // Anniversaire(s) du calepin (y compris les contacts partages)
    $DB_CX->DbQuery("SELECT DISTINCT cal_id,CONCAT(".$FORMAT_NOM_CONTACT.") AS nomContact,cal_util_id,cal_partage,cal_date_naissance FROM ${PREFIX_TABLE}calepin WHERE (cal_util_id=".$USER_SUBSTITUE." OR cal_partage='O') AND cal_date_naissance LIKE '%".substr($leJour,4)."' AND DATE_FORMAT(cal_date_naissance,'%Y%m%d')<=".date("Ymd",$tsJour));
    while ($enr = $DB_CX->DbNextRow()) {
      $tabDate = explode("-",$enr['cal_date_naissance']);
      $infoAge = afficheAge($enr['cal_date_naissance'],$tsJour);
      $ligneAnniv .= ($enr['cal_util_id']==$idUser || ($enr['cal_partage']=='O' && $MODIF_PARTAGE)) ? "<A href=\"?ztAction=M&id=".$enr['cal_id']."&sid=".$sid."&tcMenu="._MENU_CONTACT."&tcPlg=".$tcMenu."&sd=".$sd."\"".$infoAge.">".$enr['nomContact']."</A>/" : "<A".$infoAge.">".$enr['nomContact']."</A>/";
    }
    // Anniversaire(s) et note(s) du jour
    // Recalcul des bascules ete/hiver en tenant compte de l'annee affichee
    $tzEte = calculBasculeDST($tzDateEte,date("Y",$tsJour),$tzHeureEte,$tzGmt,0);
    $tzHiver = calculBasculeDST($tzDateHiver,date("Y",$tsJour),$tzHeureHiver,$tzGmt,1);
    //Preparation au decalage horaire
    list($age_date,$age_dateAvant,$age_heure_debut,$age_heure_fin) = prepareDecalageH($tzGmt,$tzEte,$tzHiver,$tsJour);
    $DB_CX->DbQuery("SELECT age_id,age_aty_id,age_heure_debut,age_heure_fin,age_libelle,age_ape_id,age_util_id,CONCAT(".$NOM_UTIL_CREATEUR.") AS nomCreateur,age_detail,aco_termine,age_prive,age_couleur,age_rappel,age_rappel_coeff,age_mere_id,age_nb_participant,age_createur_id,age_date,age_date_creation,age_date_modif,age_modificateur_id,CONCAT(".$NOM_UTIL_MODIFICATEUR.") AS nomModificateur,age_lieu,CONCAT(".$FORMAT_NOM_CONTACT.") AS nomContact,cal_id,cal_util_id,cal_partage,cal_societe,cal_adresse,cal_cp,cal_ville,cal_pays,cal_domicile,cal_travail,cal_portable,cal_fax,cal_email,cal_emailpro FROM ${PREFIX_TABLE}agenda LEFT JOIN ${PREFIX_TABLE}calepin ON cal_id=age_cal_id, ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}utilisateur t1, ${PREFIX_TABLE}utilisateur t2 WHERE age_id=aco_age_id AND aco_util_id=".$USER_SUBSTITUE." AND (((($age_date='".$leJour."' OR ($age_dateAvant='".$leJour."' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin!=0)) AND age_aty_id IN (2,3))".$whereCouleur.") OR (age_date LIKE '%".substr($leJour,4)."' AND DATE_FORMAT(age_date,'%Y%m%d')<=".date("Ymd",$tsJour)." AND age_aty_id=1)) AND t1.util_id=age_createur_id AND t2.util_id=age_modificateur_id ORDER BY age_aty_id DESC, age_date, age_heure_debut ASC");
    if ($DB_CX->DbNumRows()) {
      while ($enr = $DB_CX->DbNextRow()) {
        //Recuperation des droits de l'utilisateur sur la note
        attributDroits($enr, $droitModifStatut, $droitModifNotePerso, $droitModifNoteAffectee, $droitSuppOcc, $droitSuppNoteCreee, $droitSuppNoteAffectee, $droitApprNote, $USER_SUBSTITUE, $AFFECTE_NOTE);
        //Decalage des notes en fonction du fuseau horaire
        list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['dateCreation'],$enr['dateModif']) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$leJour,$enr['age_date'],$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif']);
        //Mod D&D : info pour creer les script drag&drop par rapport aux id des notes de l'utilisateur en cours
        $note_infos[$enr['age_id']] = $enr['age_date'];
        // Fin mod D&D
        $infoContact = "";
        //Stockage des infos relatives aux anniversaires
        if ($enr['age_aty_id']==1) {
          $infoAge = afficheAge($enr['age_date'],$tsJour);
          $ligneAnniv .= ($USER_SUBSTITUE==$idUser) ? "<A href=\"javascript: affAnniv('".$enr['age_id']."');\"".$infoAge.">".$enr['age_libelle']."</A>/" : "<A".$infoAge.">".$enr['age_libelle']."</A>/";
        } else {
          //Propriete Privee ou Publique de la note
          if ($USER_SUBSTITUE!=$idUser && $enr['age_util_id']!=$idUser && $enr['age_prive']==1) {
            $enr['age_libelle'] = trad("COMMUN_OCCUPE");
            $enr['age_detail'] = "<P class=\"infoDate\">".trad("COMMUN_NOTE_PRIVEE")."</P>"; // Detail et info de creation non visible si note privee
            $enr['age_couleur'] = $PlanningNotePrivee; // Couleur de note non visible si note privee
            $enr['age_lieu'] = ""; // Emplacement non visible si note privee
            $notePrive = true;
          } else {
            $notePrive = false;
            //Info sur la creation / modification de la note
            afficheInfoModifNote($enr, $USER_SUBSTITUE);
          }
          //Plage horaire de la note
          $debutNote = afficheHeure(floor($enr['age_heure_debut']),$enr['age_heure_debut'],$formatHeure);
          $finNote = afficheHeure(floor($enr['age_heure_fin']),$enr['age_heure_fin'],$formatHeure);
          //Info a afficher dans le popup
          $libelleNote = " <A style='font-weight:normal;color:".$AgendaTexteTitrePopup."'>".htmlspecialchars($enr['age_libelle']).((!empty($enr['age_lieu'])) ? "<BR><I>(".htmlspecialchars($enr['age_lieu']).")</I>" : "")."</A>";
          $detailNote = htmlspecialchars(nlTObr($enr['age_detail']));
          //Couleur de fond de la note si non definie dans la bdd
          if (empty($enr['age_couleur']))
            $enr['age_couleur'] = ($enr['age_util_id']==$USER_SUBSTITUE) ? $AgendaFondNotePerso : $AgendaFondNote;
          // Droit en modification sur la note
          $lien = ($droitModifNotePerso || ($droitModifNoteAffectee && !$notePrive)) ? " href=\"javascript: affNote('".$enr['age_id']."')\"" : "";
          //Propriete Active ou Terminee de la note
          if ($enr['aco_termine'] == 1) {
            $styleNote = ($NOTE_BARREE) ? "line-through" : "none";
            $imgTemoin = "puce_ok.gif";
          } else {
            $styleNote = "none";
            $imgTemoin = "puce_ko.gif";
          }
          //Correction id pour les notes a cheval
          $doubleNote = "";
          if ($enr['age_heure_fin']==24) $doubleNote = "a";
          if ($enr['age_heure_debut']==0) $doubleNote = "b";
          //Stockage des infos relatives aux notes
		  // Mod D&D
		  if ($droitModifNotePerso || ($droitModifNoteAffectee && !$notePrive))
			$ligneNote .= "<DIV id=\"dragme".$enr['age_id']."\" style=\"padding:1px;background-color:".$enr['age_couleur']."\">";
          else
            $ligneNote .= "<DIV style=\"padding:1px;background-color:".$enr['age_couleur']."\">";
		  // Fin Mod D&D	
          // Droit en modification du statut de la note
          if ($droitModifStatut && !$notePrive) {
            $ligneNote .= "<A href=\"/\" onclick=\"javascript: parent.termineNote('".$enr['age_id'].$doubleNote."',".(($NOTE_BARREE) ? "true" : "false")."); return false;\"><IMG src=\"image/".$imgTemoin."\" width=\"6\" height=\"6\" border=\"0\" id=\"t".$enr['age_id'].$doubleNote."\" hspace=\"2\" alt=\"".trad("COMMUN_CHANGER_STATUT")."\"></A>";
          } else {
            $ligneNote .= "<IMG src=\"image/".$imgTemoin."\" width=\"6\" height=\"6\" border=\"0\" alt=\"\" hspace=\"2\">";
          }
          //Mod Emplacement Plus
          $DB = new Db($DB_CX->ConnexionID);
          $DB->DbQuery("SELECT empl_type FROM ${PREFIX_TABLE}emplacement WHERE empl_nom='".$enr['age_lieu']."'");
          if ($empl = $DB->DbNextRow()) $emplType = $empl['empl_type'];
          else $emplType = 0;
           if ($emplType!=0)
             $ligneNote .= " <IMG src=\"image/emplacement/puce_".$emplType.".gif"."\" width=\"6\" height=\"6\" border=\"0\" title=\"Lieu: ".$enr['age_lieu']."\" alt=\"\" hspace=\"1\">";
          //Fin Mod Emplacement Plus
          $ligneNote .= "<A".$lien." id=\"n".$enr['age_id'].$doubleNote."\" style=\"text-decoration: ".$styleNote.";\"";
          //Distinction entre les notes couvrant toute une journee et les autres
          if ($enr['age_aty_id']==2) {
            $ligneNote .= " onmouseover=\"javascript: dtc('".addslashes($debutNote."&rsaquo;".$finNote)."','".addslashes($libelleNote)."','".addslashes($detailNote)."'); return false;\" onmouseout=\"javascript: nd(); return true;\">";
            $ligneNote .= $debutNote."&rsaquo;";
            $ligneNote .= $enr['age_libelle']."</A>";
          } elseif ($enr['age_aty_id']==3) {
            $ligneNote .= " onmouseover=\"javascript: dtc('".trad("COMMUN_JOURNEE_ENTIERE")."','".addslashes($libelleNote)."','".addslashes($detailNote)."'); return false;\" onmouseout=\"javascript: nd(); return true;\">";
            $ligneNote .= $enr['age_libelle']."</A>";
          }
          // Options possibles sur la note (rappel, suppression)
          if ($notePrive==false) {
            // Indique si un rappel a ete programme (si le statut de la note n'est pas "terminee")
            if ($enr['aco_termine']!=1 && $enr['age_rappel']>0) {
              $rappel = trad("COMMUN_RAPPEL")." <B>".$enr['age_rappel'];
              if ($enr['age_rappel_coeff']==1)
                $rappel .= " ".trad("COMMUN_MINUTE");
              elseif ($enr['age_rappel_coeff']==60)
                $rappel .= " ".trad("COMMUN_HEURE");
              else
                $rappel .= " ".trad("COMMUN_JOUR");
              $rappel .= "</B> ".trad("COMMUN_AVANCE");
              $ligneNote .= "&nbsp;<IMG src=\"image/rappel.gif\" border=\"0\" align=\"absmiddle\"".infoPopup($rappel).">";
            }
            if ($enr['age_ape_id']!=1) {
              // MOD Scission de note
              if (($droitModifNotePerso || ($droitModifNoteAffectee && !$notePrive)) && $AUTORISE_SCISSION) {
                if ($enr['age_mere_id'])
                  $ligneNote .= "&nbsp;<A href=\"javascript: scindeOcc('".$enr['age_id']."');\"><IMG src=\"image/scinder.gif\" width=\"11\" height=\"13\" border=\"0\" align=\"absmiddle\" title=\"".trad("COMMUN_SCISSION_NOTE")."\"></A> ";
              }
              // Fin MOD Scission de note
              // Droit en suppression de l'occurrence
              if ($droitSuppOcc) {
                $ligneNote .= "&nbsp;<A href=\"javascript: supprOcc('".$enr['age_id']."','0');\"><IMG src=\"image/recurrent.gif\" width=\"13\" height=\"11\" border=\"0\" align=\"absmiddle\" title=\"".trad("COMMUN_SUPPR_OCCURENCE")."\"></A>";
              } else {
                $ligneNote .= "&nbsp;<IMG src=\"image/recurrent.gif\" border=\"0\" align=\"absmiddle\" title=\"".trad("COMMUN_NOTE_RECURRENTE")."\">";
              }
            }
            // Droit en suppression d'une note creee
            if ($droitSuppNoteCreee) {
              $ligneNote .= "&nbsp;<A href=\"javascript: supprOcc('".(($enr['age_mere_id']) ? $enr['age_mere_id'] : $enr['age_id'])."','1');\"><IMG src=\"image/suppr.gif\" width=\"12\" height=\"12\" border=\"0\" align=\"absmiddle\" title=\"".trad("COMMUN_SUPPR_NOTE")."\"></A>";
            }
            // Droit en suppression d'une note affectee
            elseif ($droitSuppNoteAffectee) {
              $ligneNote .= "&nbsp;<A href=\"javascript: supprOcc('".$enr['age_id']."','2');\"><IMG src=\"image/suppr.gif\" width=\"12\" height=\"12\" border=\"0\" align=\"absmiddle\" title=\"".trad("COMMUN_SUPPR_NOTE")."\"></A>";
            }
            // Info du contact associe (et lien eventuel vers la fiche contact) selon les droits
            $ligneNote .= getInfoContactAssocie($enr,$droit_NOTES);
	// Mod Ajout Impression Note
	$ligneNote .= "&nbsp;<A href=\"javascript:parent.imprNote('".$enr[0]."')\">";
	$ligneNote .= "<IMG title=\"".trad("IMPRESSION_NOTE")."\" src=\"image/ticket.gif\" border=\"0\" align=\"absmiddle\"></A>";
	// Fin Mod Ajout Impression Note
			// Mod D&D
			if ($droitModifNotePerso || ($droitModifNoteAffectee && !$notePrive))
	          $ligneNote .= affDD($enr['age_aty_id']);
			// Fin Mod D&D
          }
          // Droit en appropriation d'une note affectee
          $ligneNote .= ($droitApprNote) ? "&nbsp;<A href=\"javascript: apprNote('".$enr['age_id']."');\"><IMG src=\"image/appropriation.gif\" border=\"0\" align=\"absmiddle\" title=\"".trad("COMMUN_APPROPRIATION")."\"></A></DIV>" : "</DIV>";
        }
      }
      if (!empty($ligneAnniv)|| !empty($ligneEvenement)) {
        $ligneEvenement  = "<TD width=\"100%\">".$ligneEvenement;
        $ligneEvenement .= (!empty($ligneAnniv)) ? "[".substr($ligneAnniv,0,strlen($ligneAnniv)-1)."]" : "";
        $ligneEvenement .= "</TD>";;
      }
	  // Mod D&D
      echo "    <TD id=\"".$iLigne.$iColonne."\" class=\"".$classCel."\" style=\"background:".$bgColorCell."\" onmouseover=\"javascript: swapColor(this,".$iLigne.",".$iColonne.",true);\" onmouseout=\"javascript: swapColor(this,".$iLigne.",".$iColonne.",false);\"><div id=\"droponme".$iLigne.$iColonne."\"><TABLE cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" border=\"0\"><TR align=\"right\" valign=\"top\">".$ligneEvenement."<TD nowrap>&nbsp;<A href=\"javascript: affJour('".$tsJour."')\"".$styleLien."</A>";
      echo $lienAjout."</TD></TR></TABLE>".$ligneNote."</div>".$dd_script."</TD>\n";
	  // Fin Mod D&D			
    }
    else {
      if (!empty($ligneAnniv) || !empty($ligneEvenement)) {
        $ligneEvenement .= (!empty($ligneAnniv)) ? "[".substr($ligneAnniv,0,strlen($ligneAnniv)-1)."]" : "";
		// Mod D&D
        echo "    <TD id=\"".$iLigne.$iColonne."\" class=\"".$classCel."\" style=\"background:".$bgColorCell."\" onmouseover=\"javascript: swapColor(this,".$iLigne.",".$iColonne.",true);\" onmouseout=\"javascript: swapColor(this,".$iLigne.",".$iColonne.",false);\"><div id=\"droponme".$iLigne.$iColonne."\"><TABLE cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" border=\"0\" align=\"right\"><TR align=\"right\" valign=\"top\"><TD width=\"100%\">".$ligneEvenement."</TD><TD nowrap>&nbsp;<A href=\"javascript: affJour('".$tsJour."')\"".$styleLien."</A>".$lienAjout."</TD></TR></TABLE><DIV style=\"height:17;\"></DIV></div>".$dd_script."</TD>\n";
		// Fin Mod D&D
      }
      else {
	    // Mod D&D
        echo "    <TD id=\"".$iLigne.$iColonne."\" class=\"".$classCel."\" style=\"background:".$bgColorCell."\" align=\"right\" valign=\"top\" onmouseover=\"javascript: swapColor(this,".$iLigne.",".$iColonne.",true);\" onmouseout=\"javascript: swapColor(this,".$iLigne.",".$iColonne.",false);\"><div id=\"droponme".$iLigne.$iColonne."\"><A href=\"javascript: affJour('".$tsJour."')\"".$styleLien."</A>".$lienAjout."</div>".$dd_script."</TD>\n";
		// Fin Mod D&D	
		}
    }
  }

  echo ("  <TABLE cellspacing=\"0\" cellpadding=\"0\" width=\"99%\" border=\"0\">
  <TR>
    <TD></TD>
    <TD width=\"100%\" colspan=\"".$nbJSelect."\" height=\"18\" nowrap class=\"bordTLRB\" align=\"center\" bgcolor=\"".$AgendaTitreFond."\"><B><A href=\"javascript: affMois(".$lienAvant.");\" class=\"AgendaFleche\"".infoPopup(trad("MENSUEL_MOIS_PRECEDENT")).">&laquo;</A>&nbsp;&nbsp;".sprintf(trad("MENSUEL_MOIS_COURANT"), $tabMois[intval($moisEnCours)], $anneeEnCours)."&nbsp;&nbsp;<A href=\"javascript: affMois(".$lienApres.");\" class=\"AgendaFleche\"".infoPopup(trad("MENSUEL_MOIS_SUIVANT")).">&raquo;</A></B></TD>
  </TR>
  <TR>
    <TD></TD>\n");
  $celSize = floor(100/$nbJSelect);
  for ($i=1; $i<8; $i++) {
    if (${"bt".$i}==1)
      echo "    <TD align=\"center\" width=\"".$celSize."%\" height=\"18\" class=\"bordTLRB\" style=\"background:".$AgendaTitre2Fond."\" id=\"colonne".$i."\"><B>".$tabJour[$i]."</B></TD>\n";
  }
  echo "  </TR>\n";

  $premierJour = date("w",mktime(0,0,0,$moisEnCours, 1, $anneeEnCours));
  if ($premierJour == 0)
    $premierJour = 7;

  // TimeStamp de la semaine a afficher
  $tsSemaine = mktime(0,0,0,$moisEnCours, 1-$premierJour+1, $anneeEnCours);
  //Index de la ligne pour le surlignage
  $iLigne = 1;
  echo "  <TR valign=\"top\" height=\"80\">\n    <TD valign=\"middle\" class=\"".(($tsSemaine==$tsSemaineCrt) ? "numWeekCrt" : "numWeek")."\" style=\"background:".(($tsSemaine==$tsSemaineCrt) ? $CalJourSelection : $AgendaTitre2Fond)."\" width=\"15\" id=\"ligne".$iLigne."\"><A href=\"javascript: affSemaine('".$tsSemaine."');\" class=\"AgendaTitreJours\">".date("W",$tsSemaine)."</A></TD>\n";
  $nbJour = 0;
  for ($i=1;$i<8;$i++) {
    if (${"bt".$i}!=1) {
      if ($i>=$premierJour)
        $nbJour++;
    } elseif ($i<$premierJour) {
      $tsJour = mktime(0,0,0,$moisEnCours, 1-$premierJour+$i, $anneeEnCours);
      afficheCase(date("Y-m-d",$tsJour), date("j",$tsJour), -1, ($i-$premierJour+1), $iLigne, $i);
    } else {
      $leJour = (++$nbJour < 10) ? $anneeEnCours."-".$moisEnCours."-0".$nbJour : $anneeEnCours."-".$moisEnCours."-".$nbJour;
      afficheCase($leJour, $nbJour, 0, $nbJour, $iLigne, $i);
    }
  }
  echo "  </TR>\n";
  $finDeMois = false;
  for ($j=1;!$finDeMois;$j++) {
    if (checkdate($moisEnCours, $nbJour+1, $anneeEnCours)) {
      $tsSemaine = mktime(0,0,0,$moisEnCours, $nbJour+1, $anneeEnCours);
      echo "  <TR valign=\"top\" height=\"80\">\n    <TD valign=\"middle\" class=\"".(($tsSemaine==$tsSemaineCrt) ? "numWeekCrt" : "numWeek")."\" style=\"background:".(($tsSemaine==$tsSemaineCrt) ? $CalJourSelection : $AgendaTitre2Fond)."\" width=\"15\" id=\"ligne".(++$iLigne)."\"><A href=\"javascript: affSemaine('".$tsSemaine."');\" class=\"AgendaTitreJours\">".date("W",$tsSemaine)."</A></TD>\n";
      for ($i=1;$i<8;$i++) {
        if (${"bt".$i}!=1)
          $nbJour++;
        elseif (checkdate($moisEnCours, ++$nbJour, $anneeEnCours)) {
          $leJour = ($nbJour < 10) ? $anneeEnCours."-".$moisEnCours."-0".$nbJour : $anneeEnCours."-".$moisEnCours."-".$nbJour;
          afficheCase($leJour, $nbJour, 0, $nbJour, $iLigne, $i);
        }
        else {
          $finDeMois = true;
          $tsJour = mktime(0,0,0,$moisEnCours, $nbJour, $anneeEnCours);
          afficheCase(date("Y-m-d",$tsJour), date("j",$tsJour), 1, $nbJour, $iLigne, $i);
        }
      }
      echo "  </TR>\n";
    }
    else {
      $finDeMois = true;
    }
  }
  echo "  </TABLE>\n";
  echo "  <DIV class=\"timezone\">".sprintf(trad("COMMUN_FUSEAU_ACTUEL"), (($tzGmt<0) ? "-" : "+").afficheHeure(floor(abs($tzGmt)),abs($tzGmt)), $tzLibelle)."</DIV>\n";
}
  // Mod D&D
  if (($util_dd==1) and ((($USER_SUBSTITUE==$idUser || $AFFECTE_NOTE) and ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR)) or ($droit_NOTES >= _DROIT_NOTE_MODIF_CREATION))) {
  //print_r($note_infos);
    foreach ($note_infos as $uid => $value) {
	  // $note_infos[$uid] contient la date de la note
      $opt2 = $sid.";".$uid.";".$note_infos[$uid].";".$idUser.";".$USER_SUBSTITUE.";".$nbJSelect.";".$anneeEnCours.";".$moisEnCours.";".$debutSemaine.";".$finSemaine.";".$premierJourSemaine.";".$bt1.";".$bt2.";".$bt3.";".$bt4.";".$bt5.";".$bt6.";".$bt7.";0;".$AFFECTE_NOTE.";mensuel;".$APPLI_LANGUE.";".$tzGmt.";".$tzEte.";".$tzHiver.";".$droit_NOTES.";".$formatHeure.";".html_entity_decode($tzLibelle).";".$SEMAINE_TYPE.";".$jourEnCours.";".$localTime.";".$tcMenu.";".$indexJourCrt.";".$sd.";".$NOTE_BARREE.";".$tzDateEte.";".$tzHeureEte.";".$tzDateHiver.";".$tzHeureHiver;	  
      // Script drag&drop de chaque note
      echo "<script>dndMgr.registerDraggable( new Rico.Draggable('test-rico-dnd','dragme".$uid."','".$opt2."') );</script>";
    }
  }
  if (!$callByDDUpdate) {
?>
</div> <!-- Fin DIV D&D -->
<!-- FIN MODULE PLANNING MENSUEL -->
<?php
  }
  // Fin Mod D&D
?>
