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

  $majHebd+=0;

  $strout2 = ("<TABLE width=\"133\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"".$CalGaucheFond."\" style=\"border-collapse:separate;\"><TR bgcolor=\"".$CalGaucheTitreFond."\"><TD width=\"7\" height=\"17\"><IMG src=\"image/trans.gif\" width=\"5\"></TD><TD align=\"center\" colspan=\"7\" width=\"119\" height=\"17\" class=\"CalTitreSemaines\">".sprintf(trad("CALENDRIER_SEMAINES"), $crtYear)."</TD><TD width=\"7\" height=\"17\"><IMG src=\"image/trans.gif\" width=\"5\"></TD></TR><TR><TD bgcolor=\"".$CalLignesHMoisFond."\" colspan=\"9\" height=\"1\"><IMG src=\"image/trans.gif\" width=\"100%\" height=\"1\"></TD></TR><TR><TD colspan=\"9\" height=\"1\"><IMG src=\"image/trans.gif\" width=\"100%\" height=\"1\"></TD></TR>");

  // Jour de la semaine du 01/01/N
  $indexJourAn = date("w",mktime(0,0,0,1,1,$crtYear));
  if ($indexJourAn == 0) $indexJourAn = 7;
  $premierLundi = mktime(0,0,0,1,2-$indexJourAn,$crtYear);
  // Premiere et Derniere semaine de l'annee
  $firstWeek  = date("W",mktime(0,0,0,1,1,$crtYear));
  $lastWeek   = date("W",mktime(0,0,0,12,31,$crtYear));
  $semaineMax = ($lastWeek==1) ? date("W",mktime(0,0,0,12,24,$crtYear)) : $lastWeek;
  $cpt = 1;
  $idx = 0;
  for($j=1;$cpt<$semaineMax;$j++) {
    $strout2 .= "<TR><TD width=\"7\" height=\"17\"><IMG src=\"image/trans.gif\" width=\"5\"></TD>";
    for($i=1;$i<8;$i++) {
      if ($j==1 && $i==1 && $firstWeek!=1) {
        if ($debutSemaine == $premierLundi)
          $background = " width=\"15\" height=\"15\" bgcolor=\"".$CalJourSelection."\" class=\"CalFondJour\"";
        elseif ($debutSemaine == mktime(0,0,0,1,1,$crtYear))
          $background = " width=\"17\" height=\"17\" bgcolor=\"".$CalJourCourant."\"";
        else
          $background = " width=\"17\" height=\"17\"";
        $classCel   = "jMoisPrec";
        $tsSemaine  = $premierLundi;
        $numSemaine = $firstWeek;
      } else {
        $idxSemaine = mktime(0,0,0,1,(2-$indexJourAn)+($idx*7),$crtYear);
        if ($debutSemaine == $idxSemaine)
          $background = " width=\"15\" height=\"15\" bgcolor=\"".$CalJourSelection."\" class=\"CalFondJour\"";
        elseif ($idxSemaine == $tsSemaineCrt)
          $background = " width=\"17\" height=\"17\" bgcolor=\"".$CalJourCourant."\"";
        else
          $background = " width=\"17\" height=\"17\"";
        $tsSemaine = $idxSemaine;
        if ($cpt>$semaineMax) {
          $classCel   = "jMoisPrec";
          $numSemaine = ($cpt++)-$semaineMax;
        } else {
          $classCel   = "jMoisCrt";
          $numSemaine = $cpt++;
        }
      }
      $strout2 .= "<TD align=\"center\"".$background."><A href=\"javascript: affSemaine('".$tsSemaine."');\" class=\"".$classCel."\">".$numSemaine."</A></TD>";
      $idx++;
    }
    $strout2 .= "<TD width=\"7\" height=\"17\"><IMG src=\"image/trans.gif\" width=\"5\"></TD></TR>";
  }

  $strout2 .= "</TABLE>";

  // Si $majHebd vaut 0, on est dans l'ecriture normale du calendrier des semaines au chargement de la page dans agenda_calendrier
  if ($majHebd==0) {
    echo $strout2;
  }
  // Sinon on ne fait rien car cette page a ete appelee par agenda_calendrier_jours qui se charge du traitement de $strout2
  // pour mettre a jour le calendrier des semaines via une fonction JS depuis la frame cachee trash_sid
?>
