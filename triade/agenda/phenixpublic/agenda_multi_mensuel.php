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

  $moisEnCours++;
  if ($moisEnCours == 13) {
    $moisEnCours = 1;
    $anneeEnCours ++;
  }
  $lienMois = "'".($moisEnCours)."','".$anneeEnCours."','2'";
  
  $Nvsd = mktime(0,0,0,$moisEnCours, $jourEnCours, $anneeEnCours);
  $jourEnCours  = date("d", $Nvsd);
  $moisEnCours  = date("m", $Nvsd);
  $anneeEnCours = date("Y", $Nvsd);

    // Recuperation des evenements personnalises a notifier dans le calendrier (sert aussi pour le planning mensuel global)
  $DB_CX->DbQuery("SELECT DISTINCT eve_date_debut, TO_DAYS(eve_date_fin)-TO_DAYS(eve_date_debut) AS duree, TO_DAYS(eve_date_debut)-TO_DAYS('$anneeEnCours-$moisEnCours-01') AS decalage, eve_couleur FROM ${PREFIX_TABLE}evenement WHERE (eve_date_debut LIKE '$anneeEnCours-$moisEnCours-%' OR (eve_date_debut<'$anneeEnCours-$moisEnCours-01' AND eve_date_fin>='$anneeEnCours-$moisEnCours-01'))".(($USER_SUBSTITUE==$idUser) ? " AND (eve_util_id=".$idUser." OR eve_partage='O')" : " AND eve_partage='O'"));
  unset ($tabEvenementDate);
  $tabEvenementDate = array();
  // Initialisation du tableau des couleurs des jours a vide
  $nbJourMois = date("t",$sd);
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
    if ($dureeEvt > ($nbJourMois-$jEvt)) { // La date de fin est posterieure au mois courant, donc il faut regulariser
      $dureeEvt = $nbJourMois-$jEvt;
    }
    if (empty($enr['eve_couleur']))
      $enr['eve_couleur'] = $CalJourEvenement;
    for ($i=0;$i<=$dureeEvt;$i++) {
      $tabEvenementDate[intval($jEvt+$i)] = $enr['eve_couleur'];
    }
  }

    $tabJourFerie = getListeJourFerie($anneeEnCours);


if ($nbJSelect) {
  echo "  <br>";
  echo ("  <TABLE cellspacing=\"0\" cellpadding=\"0\" width=\"99%\" border=\"0\">
  <TR>
    <TD></TD>
    <TD width=\"100%\" colspan=\"".$nbJSelect."\" height=\"18\" nowrap class=\"bordTLRB\" align=\"center\" bgcolor=\"".$AgendaTitreFond."\"><B><A href=\"javascript: affMois(".$lienMois.");\">".sprintf(trad("MENSUEL_MOIS_COURANT"), $tabMois[intval($moisEnCours)], $anneeEnCours)."</A></B></TD>
  </TR>
  <TR>
    <TD></TD>\n");
  $celSize = floor(100/$nbJSelect);
  for ($i=1; $i<8; $i++) {
    if (${"bt".$i}==1)
      echo "    <TD align=\"center\" width=\"".$celSize."%\" height=\"18\" class=\"bordTLRB\" style=\"background:".$AgendaTitre2Fond."\" id=\"colonne".$i."\"><B>".$tabJour[$i]."</B></TD>\n";
  }
  echo "  </TR>\n";

  $premierJour = date("w",mktime(12,0,0,$moisEnCours, 1, $anneeEnCours));
  if ($premierJour == 0)
    $premierJour = 7;

  // TimeStamp de la semaine a afficher
  $tsSemaine = mktime(12,0,0,$moisEnCours, 1-$premierJour+1, $anneeEnCours);
  //Index de la ligne pour le surlignage
  $iLigne = 1;
  echo "  <TR valign=\"top\" height=\"80\">\n    <TD valign=\"middle\" class=\"".(($tsSemaine==$tsSemaineCrt) ? "numWeekCrt" : "numWeek")."\" style=\"background:".(($tsSemaine==$tsSemaineCrt) ? $CalJourSelection : $AgendaTitre2Fond)."\" width=\"15\" id=\"ligne".$iLigne."\"><A href=\"javascript: affSemaine('".$tsSemaine."');\" class=\"AgendaTitreJours\">".date("W",$tsSemaine)."</A></TD>\n";
  $nbJour = 0;
  for ($i=1;$i<8;$i++) {
    if (${"bt".$i}!=1) {
      if ($i>=$premierJour)
        $nbJour++;
    } elseif ($i<$premierJour) {
      $tsJour = mktime(12,0,0,$moisEnCours, 1-$premierJour+$i, $anneeEnCours);
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
      $tsSemaine = mktime(12,0,0,$moisEnCours, $nbJour+1, $anneeEnCours);
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
          $tsJour = mktime(12,0,0,$moisEnCours, $nbJour, $anneeEnCours);
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
?>
<!-- FIN MODULE PLANNING MENSUEL -->
