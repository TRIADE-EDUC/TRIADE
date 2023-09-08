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

  // ----------------------------------------------------------------------------
  // Affichage d'une case du calendrier
  // ----------------------------------------------------------------------------
  function afficheCase($nbJour, $jourFerie, $jourSemaine) {
    global $CalJourSelection, $CalJourFerie, $tabOccupe;
    global $jourEnCours, $moisEnCours, $anneeEnCours, $mois, $tabJourFerie;
    global $tabEvenementDate,$SEMAINE_CALENDRIER;

    $classJour = (substr($SEMAINE_CALENDRIER,$jourSemaine-1,1)=="1") ? "jMoisCrt" : "jMoisCrtWE";
    $tsJour = mktime(0,0,0,$moisEnCours, $nbJour, $anneeEnCours);
    $numJour = ($tabOccupe[$nbJour]==1) ? "<A href=\"javascript: affJour('".$tsJour."');\" class=\"$classJour\"><B>".$nbJour."</B></A>" : "<A href=\"javascript: affJour('".$tsJour."');\" class=\"$classJour\">".$nbJour."</A>";
    if ($jourEnCours == ($anneeEnCours.$moisEnCours.$nbJour))
      $background = " width=\"15\" height=\"15\" bgcolor=\"".$CalJourSelection."\" class=\"CalFondJour\"";
    else {
      if (!empty($tabEvenementDate[$nbJour])) {
        $background = " width=\"17\" height=\"17\" bgcolor=\"".$tabEvenementDate[$nbJour]."\"";
      }
      //Coloration des jours feries
      if (in_array($jourFerie,$tabJourFerie)) {
        $background = " width=\"17\" height=\"17\" bgcolor=\"".$CalJourFerie."\"";
      }
    }
    echo "        <TD".$background.">".$numJour."</TD>\n";
  }

  // ----------------------------------------------------------------------------
  // Affichage d'un calendrier en fonction du mois et de l'annee
  // ----------------------------------------------------------------------------
  function afficheCalendrier($moisCrt,$anneeCrt) {
    global $DB_CX,$PREFIX_TABLE,$USER_SUBSTITUE,$idUser,$nbJour,$tabOccupe,$SEMAINE_CALENDRIER;
    global $CalFond,$CalTitreFond,$AgendaBordureTableau,$tabJour2,$tabMois,$whereCouleur,$tabEvenementDate,$CalJourEvenement,$CalJour,$CalJourWE;
    global $tzGmt,$tzEte,$tzHiver;

    echo ("<TABLE width=\"150\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"".$CalFond."\" style=\"border: solid 1px ".$AgendaBordureTableau."; border-collapse:separate;\">
      <TR>
        <TD colspan=\"11\" height=\"18\" class=\"enteteTableau\"><A href=\"javascript: affMois('".floor($moisCrt)."','".$anneeCrt."','2');\" class=\"sousMenu\">".$tabMois[floor($moisCrt)]."</A></TD>
      </TR>
      <TR bgcolor=\"".$CalTitreFond."\" align=\"center\">
        <TD width=\"19\" colspan=\"2\" height=\"17\" valign=\"bottom\">Se</TD>
        <TD width=\"5\" height=\"17\"><IMG src=\"image/trans.gif\" width=\"5\"></TD>\n");
    // Intitule des colonnes (L,M,M,J,V,S,D)
    for ($i=0; $i<7; $i++) {
      $couleurJour = (substr($SEMAINE_CALENDRIER,$i,1)=="1") ? $CalJour : $CalJourWE;
      echo "        <TD width=\"17\" height=\"17\" style=\"color:$couleurJour\">".$tabJour2[$i]."</TD>\n";
    }

    $tsSemaine = mktime(0,0,0,$moisCrt, 1, $anneeCrt);
    echo ("        <TD width=\"5\" height=\"17\"><IMG src=\"image/trans.gif\" width=\"5\"></TD>
      </TR>
      <TR>
        <TD width=\"18\" bgcolor=\"".$CalTitreFond."\" height=\"1\"><IMG src=\"image/trans.gif\" width=\"18\" height=\"1\"></TD>
        <TD bgcolor=\"".$AgendaBordureTableau."\" colspan=\"10\" height=\"1\"><IMG src=\"image/trans.gif\" width=\"100%\" height=\"1\"></TD>
      </TR>
      <TR>
        <TD width=\"18\" height=\"1\" bgcolor=\"".$CalTitreFond."\"><IMG src=\"image/trans.gif\" width=\"17\" height=\"1\"></TD>
        <TD width=\"1\" height=\"1\" bgcolor=\"".$AgendaBordureTableau."\"><IMG src=\"image/trans.gif\" width=\"1\" height=\"1\"></TD>
        <TD colspan=\"9\" height=\"1\"><IMG src=\"image/trans.gif\" width=\"100%\" height=\"1\"></TD>
      </TR>
      <TR align=\"center\">
        <TD width=\"18\" height=\"17\" bgcolor=\"".$CalTitreFond."\"><A href=\"javascript: affSemaine('".$tsSemaine."');\" class=\"AgendaTitreJours\">".date("W",$tsSemaine)."</A></TD>
        <TD width=\"1\" height=\"1\" bgcolor=\"".$AgendaBordureTableau."\"><IMG src=\"image/trans.gif\" width=\"1\" height=\"1\"></TD>
        <TD width=\"5\" height=\"17\"><IMG src=\"image/trans.gif\" width=\"5\"></TD>\n");

    $premierJour = date("w",mktime(0,0,0,$moisCrt, 1, $anneeCrt));
    if ($premierJour == 0)
      $premierJour = 7;

    if ($premierJour!=1) {
      for($i=1;$i<$premierJour;$i++) {
        echo "        <TD width=\"17\" height=\"17\">&nbsp;</TD>\n";
      }
    }

    //Preparation au decalage horaire
    list($age_date,$age_dateAvant,$age_heure_debut,$age_heure_fin) = prepareDecalageH($tzGmt,$tzEte,$tzHiver,$tsSemaine);

    // Reinitialisation du tableau indiquant si un jour contient une note ou un anniversaire
    $tabOccupe = array();
    // Recherche des jours du mois courant avec une note ou un anniversaire (agenda et calepin)
    $DB_CX->DbQuery("SELECT DISTINCT DATE_FORMAT(IF(age_aty_id=1,age_date,$age_date),'%e') AS jour FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne WHERE age_id=aco_age_id AND aco_util_id=".$USER_SUBSTITUE." AND ($age_date LIKE '".$anneeCrt."-".$moisCrt."-%'".$whereCouleur." OR (age_date LIKE '%-".$moisCrt."-%' AND DATE_FORMAT(age_date,'%Y%m')<=".date("Ym",$tsSemaine)." AND age_aty_id=1))");
    while ($enr=$DB_CX->DbNextRow()) {
      $tabOccupe[$enr['jour']]=1;
    }
    // Recherche des notes a cheval
    $DB_CX->DbQuery("SELECT DISTINCT DATE_FORMAT($age_dateAvant,'%e') AS jour FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne WHERE age_id=aco_age_id AND aco_util_id=".$USER_SUBSTITUE." AND ($age_dateAvant LIKE '".$anneeCrt."-".$moisCrt."-%' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin!=0 AND age_aty_id=2)".$whereCouleur);
    while ($enr=$DB_CX->DbNextRow()) {
      $tabOccupe[$enr['jour']]=1;
    }
    $DB_CX->DbQuery("SELECT DISTINCT DATE_FORMAT(cal_date_naissance,'%e') AS jour FROM ${PREFIX_TABLE}calepin WHERE (cal_util_id=".$USER_SUBSTITUE." OR cal_partage='O') AND cal_date_naissance LIKE '%-".$moisCrt."-%' AND DATE_FORMAT(cal_date_naissance,'%Y%m')<=".date("Ym",$tsSemaine));
    while ($enr=$DB_CX->DbNextRow()) {
      $tabOccupe[$enr['jour']]=1;
    }

    // Recuperation des evenements personnalises a notifier dans le calendrier du mois
    $DB_CX->DbQuery("SELECT DISTINCT eve_date_debut, TO_DAYS(eve_date_fin)-TO_DAYS(eve_date_debut) AS duree, TO_DAYS(eve_date_debut)-TO_DAYS('$anneeCrt-$moisCrt-01') AS decalage, eve_couleur FROM ${PREFIX_TABLE}evenement WHERE (eve_date_debut LIKE '$anneeCrt-$moisCrt-%' OR (eve_date_debut<'$anneeCrt-$moisCrt-01' AND eve_date_fin>='$anneeCrt-$moisCrt-01'))".(($USER_SUBSTITUE==$idUser) ? " AND (eve_util_id=".$idUser." OR eve_partage='O')" : " AND eve_partage='O'"));
    $tabEvenementDate = array();
    // Initialisation du tableau des couleurs des jours a vide
    $nbJourMois = date("t",mktime(0,0,0,$moisCrt, 1, $anneeCrt));
    for ($i=1;$i<$nbJourMois;$i++) {
      $tabEvenementDate[$i] = "";
    }
    while ($enr = $DB_CX->DbNextRow()) {
      $dureeEvt = $enr['duree'];
      list($aEvt,$mEvt,$jEvt) = explode ("-",$enr['eve_date_debut']);
      if ($enr['decalage']<0) { // La date de debut est anterieure au mois courant donc il faut regulariser
        $jEvt=1;
        $dureeEvt = $dureeEvt+$enr['decalage']; // On additionne car $enr['decalage'] est negatif
      }
      if ($dureeEvt > ($nbJourMois-$jEvt)) { // La date de fin est posterieure au mois couront, donc il faut regulariser
        $dureeEvt = $nbJourMois-$jEvt;
      }
      if (empty($enr['eve_couleur']))
        $enr['eve_couleur'] = $CalJourEvenement;
      for ($i=0;$i<=$dureeEvt;$i++) {
        $tabEvenementDate[intval($jEvt+$i)] = $enr['eve_couleur'];
      }
    }

    $nbJour = 0;
    for($i=$premierJour;$i<8;$i++) {
      $jourFerie = (++$nbJour)."-".$moisCrt;
      afficheCase($nbJour, $jourFerie, $i);
    }

    echo ("        <TD width=\"5\" height=\"17\"><IMG src=\"image/trans.gif\" width=\"5\"></TD>
      </TR>\n");

    $finDeMois = false;
    for($j=1;!$finDeMois;$j++) {
      if (checkdate($moisCrt, $nbJour+1, $anneeCrt)) {
        $tsSemaine = mktime(0,0,0,$moisCrt, $nbJour+1, $anneeCrt);
        echo ("      <TR align=\"center\">
        <TD width=\"18\" height=\"17\" bgcolor=\"".$CalTitreFond."\"><A href=\"javascript: affSemaine('".$tsSemaine."');\" class=\"AgendaTitreJours\">".date("W",$tsSemaine)."</A></TD>
        <TD width=\"1\" height=\"1\" bgcolor=\"".$AgendaBordureTableau."\"><IMG src=\"image/trans.gif\" width=\"1\" height=\"1\"></TD>
        <TD width=\"5\" height=\"17\"><IMG src=\"image/trans.gif\" width=\"5\"></TD>\n");
        for($i=1;$i<8;$i++) {
          if (checkdate($moisCrt, ++$nbJour, $anneeCrt)) {
            $jourFerie = $nbJour."-".$moisCrt;
            afficheCase($nbJour, $jourFerie, $i);
          }
          else {
            $finDeMois = true;
            echo "        <TD width=\"17\" height=\"17\">&nbsp;</TD>\n";
          }
        }
        echo "        <TD width=\"5\" height=\"17\"><IMG src=\"image/trans.gif\" width=\"5\"></TD>\n";
        echo "      </TR>\n";
      }
      else {
        $finDeMois = true;
      }
    }
    echo ("      <TR>
        <TD width=\"18\" height=\"1\" bgcolor=\"".$CalTitreFond."\"><IMG src=\"image/trans.gif\" width=\"17\" height=\"1\"></TD>
        <TD width=\"1\" height=\"1\" bgcolor=\"".$AgendaBordureTableau."\"><IMG src=\"image/trans.gif\" width=\"1\" height=\"1\"></TD>
        <TD colspan=\"9\" height=\"1\"><IMG src=\"image/trans.gif\" width=\"100%\" height=\"1\"></TD>
      </TR>
    </TABLE>\n");
  }

  // ----------------------------------------------------------------------------
  // Code de la page
  // ----------------------------------------------------------------------------
?>
<!-- MODULE PLANNING ANNUEL -->
  <FORM action="<?php echo "?sid=".$sid."&tcMenu=".$tcMenu."&sd=".$sd; ?>" method="post">
  <TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
  <TR>
    <TD height="28" class="sousMenu" width="30%">&nbsp;</TD>
    <TD height="28" class="sousMenu" width="40%"><A href="javascript: affMois('1','<?php echo $anneeEnCours-1; ?>','<?php echo $tcMenu; ?>');" class="AgendaFleche"<?php echo infoPopup(trad("ANNUEL_ANNEE_PRECEDENTE")); ?>>&laquo;</A>&nbsp;&nbsp;<?php echo sprintf(trad("ANNUEL_ANNEE_COURANTE"), $anneeEnCours);?>&nbsp;&nbsp;<A href="javascript: affMois('1','<?php echo $anneeEnCours+1; ?>','<?php echo $tcMenu; ?>');" class="AgendaFleche"<?php echo infoPopup(trad("ANNUEL_ANNEE_SUIVANTE")); ?>>&raquo;</A></TD>
    <TD align="right" nowrap class="sousMenu" width="30%" style="text-align:right;"><?php genereListeCouleur(); ?>&nbsp;&nbsp;</TD>
  </TR>
  </TABLE>
  </FORM>
  <BR>
  <TABLE border="0" cellspacing="0" cellpadding="0">
  <TR align="center">
<?php
  //Si l'utilisateur a choisi une couleur de note on l'ajoute dans la clause WHERE de la recherche
  $whereCouleur = "";
  if ($FILTRE_COULEUR != "ALL" && !empty($FILTRE_COULEUR)) {
    $whereCouleur = ($FILTRE_COULEUR == $AgendaFondNotePerso) ? " AND (age_couleur='".$FILTRE_COULEUR."' OR age_couleur='')" : " AND age_couleur='".$FILTRE_COULEUR."'";
  }

  $jourEnCours = date("Ymj",$localTime);
  for ($mois=1; $mois<13; $mois++) {
    if ($mois==5 || $mois==9) {
      //Ligne de separation
      echo ("  </TR>\n  <TR>\n    <TD colspan=\"4\" height=\"24\">&nbsp;</TD>\n  </TR>\n  <TR align=\"center\">\n");
    }
    echo "    <TD width=\"174\" valign=\"top\" nowrap>";
    $moisEnCours = ($mois<10) ? "0".$mois : $mois;
    afficheCalendrier($moisEnCours,$anneeEnCours);
    echo ("</TD>\n");
  }
?>
  </TR>
  </TABLE>
<?php
    echo ("      <BR>
      <TABLE cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" border=\"0\">
      <TR align=\"center\" height=\"20\">
        <TD height=\"28\" align=\"center\"><TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
          <TR height=\"15\">
            <TD class=\"bordTLRB\" bgcolor=\"".$CalJourSelection."\" align=\"center\" width=\"110\" nowrap>".trad("ANNUEL_JOUR_COURANT")."</TD>
            <TD>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
            <TD class=\"bordTLRB\" bgcolor=\"".$CalJourFerie."\" align=\"center\" width=\"110\" nowrap>".trad("ANNUEL_JOUR_FERIE")."</TD>
            <TD>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
            <TD class=\"bordTLRB\" bgcolor=\"".$CalJourEvenement."\" align=\"center\" width=\"110\" nowrap>".trad("COMMUN_EVENEMENT")."</TD>
            <TD>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
            <TD class=\"bordTLRB\" bgcolor=\"".$PlanningJour."\" style=\"color:".$CalJourWE."\" align=\"center\" width=\"110\" nowrap>".(($SEMAINE_CALENDRIER=="1111100") ? trad("ANNUEL_WEEKEND") : trad("ANNUEL_HORS_SEMAINE"))."</TD>
          </TR>
        </TABLE></TD>
      </TR>
      </TABLE>\n");
?>
  <!-- FIN MODULE PLANNING ANNUEL -->
