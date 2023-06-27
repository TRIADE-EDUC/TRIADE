<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_DD.txt ?>
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

  if (isset($_GET['majJour']) || isset($HTTP_GET_VARS['majJour'])) {
    require("inc/nocache.inc.php");
    require("inc/html.inc.php");
    if (isset($_GET['sid']) || isset($HTTP_GET_VARS['sid'])) {
      include("inc/param.inc.php");
      include("inc/fonctions.inc.php");
    } else {
      include ("inc/interdit.html");
      exit;
    }

    $idUser = Session_ok($sid);

    if ($idUser == -1) {
      include ("inc/interdit.html");
      exit;
    }

    include("skins/$APPLI_STYLE.php");
    include("lang/$APPLI_LANGUE.php");

    $tcMenu=$menu;
    $majJour+=0;
    $majHebd+=0;
    // Si $majJour vaut 1 on actualise un calendrier qui ne correspond pas a celui de la date courante
    //   => pas d'affichage de la selection des mois ou des semaines dans le calendrier des jours
    // sinon $majJour vaut 2
    //   => l'utilisateur est de retour sur le calendrier initial donc on reaffiche les selections dans le calendrier des jours
    // Si $majHebd vaut 1 on actualise egalement le calendrier des semaines (fin de page)
    $jour = ($majJour==1) ? 1 : $jour;
    $sd_new = mktime(0,0,0,$mois, $jour, $annee);
    $jourEnCours  = date("d", $sd_new);
    $moisEnCours  = date("m", $sd_new);
    $anneeEnCours = date("Y", $sd_new);

    // Recuperation des evenements personnalises a notifier dans le calendrier (sert aussi pour le planning mensuel global)
    $DB_CX->DbQuery("SELECT DISTINCT eve_date_debut, TO_DAYS(eve_date_fin)-TO_DAYS(eve_date_debut) AS duree, TO_DAYS(eve_date_debut)-TO_DAYS('$anneeEnCours-$moisEnCours-01') AS decalage, eve_couleur FROM ${PREFIX_TABLE}evenement WHERE (eve_date_debut LIKE '$anneeEnCours-$moisEnCours-%' OR (eve_date_debut<'$anneeEnCours-$moisEnCours-01' AND eve_date_fin>='$anneeEnCours-$moisEnCours-01'))".(($USER_SUBSTITUE==$idUser) ? " AND (eve_util_id=".$idUser." OR eve_partage='O')" : " AND eve_partage='O'"));
    $tabEvenementDate = array();
    // Initialisation du tableau des couleurs des jours a vide
    $nbJourMois = date("t",$sd_new);
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

    // Recuperation des infos de timezone de l'utilisateur
    $DB_CX->DbQuery("SELECT tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver FROM ${PREFIX_TABLE}utilisateur t1, ${PREFIX_TABLE}utilisateur t2, ${PREFIX_TABLE}timezone WHERE t1.util_id=".$USER_SUBSTITUE." AND t2.util_id=".$idUser." AND ((tzn_zone=t1.util_timezone AND t2.util_timezone_partage='O') OR (tzn_zone=t2.util_timezone AND t2.util_timezone_partage='N'))");
    $tzGmt = $DB_CX->DbResult(0,"tzn_gmt");
    $tzDateEte = $DB_CX->DbResult(0,"tzn_date_ete");
    $tzHeureEte = $DB_CX->DbResult(0,"tzn_heure_ete");
    $tzDateHiver = $DB_CX->DbResult(0,"tzn_date_hiver");
    $tzHeureHiver = $DB_CX->DbResult(0,"tzn_heure_hiver");
    // Calcul des bascules ete/hiver pour la date et l'heure locale
    $tzEte = calculBasculeDST($tzDateEte,gmdate("Y"),$tzHeureEte,$tzGmt,0);
    $tzHiver = calculBasculeDST($tzDateHiver,gmdate("Y"),$tzHeureHiver,$tzGmt,1);
    // Ajustement de la date locale en fonction du timezone
    $decalageHoraire = calculDecalageH($tzGmt,$tzEte,$tzHiver,mktime(gmdate("H"),gmdate("i"),0,gmdate("n"),gmdate("j"),gmdate("Y")));
    $localTime = mktime(gmdate("H")+floor($decalageHoraire),gmdate("i")+($decalageHoraire*60)%60,gmdate("s"),gmdate("n"),gmdate("j"),gmdate("Y"));
    // Recalcul des bascules ete/hiver en tenant compte de la date choisie
    $tzEte = calculBasculeDST($tzDateEte,date("Y",$sd_new),$tzHeureEte,$tzGmt,0);
    $tzHiver = calculBasculeDST($tzDateHiver,date("Y",$sd_new),$tzHeureHiver,$tzGmt,1);
  } else {
    $majJour = 0;
  }

  // ----------------------------------------------------------------------------
  // Affichage d'une case du calendrier
  // ----------------------------------------------------------------------------
  function afficheJour($nbJour, $jourFerie, $jourSemaine) {
    global $CalJourSelection, $CalJourSelFerie, $CalJourFerie, $tcMenu, $inSemaine, $tabOccupe;
    global $premierJourSemaine, $nbJourMois, $jourEnCours, $tabJourFerie, $SEMAINE_CALENDRIER;
    global $tabEvenementDate, $CalJourEvenement, $CalJourCourant;
    global $strout, $majJour, $moisEnCours, $anneeEnCours, $sd, $tsAujourdhui;

    $classJour = (substr($SEMAINE_CALENDRIER,$jourSemaine-1,1)=="1") ? "jMoisCrt" : "jMoisCrtWE";
    $tsJour = mktime(0,0,0,$moisEnCours,$nbJour,$anneeEnCours);
    $numJour = ($tabOccupe[$nbJour]==1) ? "<A href=\"javascript: affJour('".$tsJour."');\" class=\"$classJour\"><B>".$nbJour."</B></A>" : "<A href=\"javascript: affJour('".$tsJour."');\" class=\"$classJour\">".$nbJour."</A>";
    // Si $majJour vaut 1 on actualise un calendrier qui ne correspond pas a celui de la date courante
    //   => pas d'affichage de la selection des mois ou des semaines
    if ($tcMenu==_MENU_PLG_QUOT || $tcMenu==_MENU_PLG_ANNUEL || $tcMenu==_MENU_PLG_QUOT_GBL || $tcMenu>_MENU_DISP_HEBDO || $majJour==1) {
      if ($tsJour == $sd) {
        $background = " width=\"15\" height=\"15\" bgcolor=\"".$CalJourSelection."\" class=\"CalFondJour\"";
      } else {
        // Coloration d'aujourd'hui
        if ($tsJour == $tsAujourdhui) {
          $background = " width=\"17\" height=\"17\" bgcolor=\"".$CalJourCourant."\"";
        }
        // Coloration des evenements
        if (!empty($tabEvenementDate[$nbJour])) {
          $background = " width=\"17\" height=\"17\" bgcolor=\"".$tabEvenementDate[$nbJour]."\"";
        }
        // Coloration des jours feries
        if (in_array($jourFerie,$tabJourFerie)) {
          $background = " width=\"17\" height=\"17\" bgcolor=\"".$CalJourFerie."\"";
        }
      }
    } else {
      // Coloration des jours feries
      if (in_array($jourFerie,$tabJourFerie)) {
        $fondJour = $CalJourFerie;
      } elseif (!empty($tabEvenementDate[$nbJour])) {
        $fondJour = $tabEvenementDate[$nbJour];
      } else {
        $fondJour = $CalJourSelection;
      }
      if ($tcMenu==_MENU_PLG_HEBDO || $tcMenu==_MENU_PLG_HEBDO_GBL || $tcMenu==_MENU_DISP_HEBDO) {
        if ($nbJour == $premierJourSemaine) {
          $inSemaine = true;
		      $background = " width=\"15\" height=\"15\" bgcolor=\"".$fondJour."\" class=\"CalFondDebutSemaine\"";
        } elseif ($jourSemaine == 7 && $inSemaine) {
          $inSemaine = false;
          $background = " width=\"15\" height=\"15\" bgcolor=\"".$fondJour."\" class=\"CalFondFinSemaine\"";
        } elseif($inSemaine) {
          $background = " width=\"15\" height=\"15\" bgcolor=\"".$fondJour."\" class=\"CalFondMilieuSemaine\"";
        } else {
          if (!empty($tabEvenementDate[$nbJour])) {
            $background = " width=\"17\" height=\"17\" bgcolor=\"".$tabEvenementDate[$nbJour]."\"";
          }
          //Coloration des jours feries
          if (in_array($jourFerie,$tabJourFerie)) {
            $background = " width=\"17\" height=\"17\" bgcolor=\"".$CalJourFerie."\"";
          }
        }
      } else {
        if ($nbJour == 1) {
          $background = ($jourSemaine == 7) ? "CalFondDebutMoisDimanche" : "CalFondDebutmois";
        } elseif ($nbJour == $nbJourMois) {
          $background = ($jourSemaine == 1) ? "CalFondFinMoisLundi" : "CalFondFinMois";
        } elseif ($nbJour < 8) {
          if ($jourSemaine == 1)
            $background = "CalFondDebutMois";
          else
            $background = ($jourSemaine == 7) ? "CalFondFinPremiereLigneMois" : "CalFondHautMois";
        } elseif ($nbJourMois-$nbJour < 7) {
          if ($jourSemaine == 1)
            $background = "CalFondDebutDernierLigneMois";
          else
            $background = ($jourSemaine == 7) ? "CalFondFinMois" : "CalFondBasMois";
        } elseif ($jourSemaine == 1) {
          $background = "CalFondDebutLigneMois";
        } elseif ($jourSemaine == 7) {
          $background = "CalFondFinLigneMois";
        }
        $background = " width=\"15\" height=\"15\" bgcolor=\"".$fondJour."\" class=\"".$background."\"";
      }
    }
    $strout .= "<TD align=\"center\"".$background.">".$numJour."</TD>";
  }



  // ----------------------------------------------------------------------------
  // Code de la page
  // ----------------------------------------------------------------------------
  // Info pour le calendrier permanent des semaines
  $indexJourCrt = date("w",$sd);
  if ($indexJourCrt == 0)
    $indexJourCrt = 7;
  $premierJourSemaine = $jourEnCours-$indexJourCrt+1;

  $premierJour = date("w",mktime(0,0,0,$moisEnCours, 1, $anneeEnCours));

  $tabJourFerie = getListeJourFerie($anneeEnCours);

  if ($premierJour == 0)
    $premierJour = 7;

  // Le calendrier journalier affiche les jours de la semaine type d'une couleur differente sauf si la semaine type est vide (0000000) ou complete (1111111) dans ces cas on affiche les week-end d'une couleur differente
  $SEMAINE_CALENDRIER = ($SEMAINE_TYPE!="1111111" && $SEMAINE_TYPE!="0000000") ? $SEMAINE_TYPE : "1111100";

  // Mod D&D
  $strout = ("<div id=\"calendar\"><TABLE width=\"133\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"".$CalGaucheFond."\" style=\"border-collapse:separate;\"><TR height=\"17\" bgcolor=\"".$CalGaucheTitreFond."\"><TD width=\"7\" height=\"17\"><IMG src=\"image/trans.gif\" width=\"5\"></TD>");
  // Fin Mod D&D

  for ($i=0; $i<7; $i++) {
    $couleurJour = (substr($SEMAINE_CALENDRIER,$i,1)=="1") ? $CalJour : $CalJourWE;
    $strout .= "<TD align=\"center\" width=\"17\" height=\"17\" style=\"color:$couleurJour\">".$tabJour2[$i]."</TD>";
  }

  $strout .= ("<TD width=\"7\" height=\"17\"><IMG src=\"image/trans.gif\" width=\"5\"></TD></TR><TR><TD bgcolor=\"".$CalLignesHMoisFond."\" colspan=\"9\" height=\"1\"><IMG src=\"image/trans.gif\" width=\"100%\" height=\"1\"></TD></TR><TR><TD colspan=\"9\" height=\"1\"><IMG src=\"image/trans.gif\" width=\"100%\" height=\"1\"></TD></TR><TR><TD width=\"7\" height=\"17\"><IMG src=\"image/trans.gif\" width=\"5\"></TD>");

  $inSemaine = false;
  $tsAujourdhui = mktime(0,0,0,date("n",$localTime),date("j",$localTime),date("Y",$localTime));
  if ($premierJour!=1) {
    $moisPrec = $moisEnCours - 1;
    $nbJourMoisPrec = ($moisPrec) ? date("t", mktime(0,0,0,$moisPrec,1,$anneeEnCours)) : date("t", mktime(0,0,0,12,1,$anneeEnCours-1));
    for($i=1;$i<$premierJour;$i++) {
      // S'il s'agit d'un planning hebdo (normal, global ou dispo)
      //    => on desactive l'affichage de la selection de la semaine si $majJour vaut 1
      //       car on actualise un calendrier qui ne correspond pas a celui de la date courante
      if (($tcMenu==_MENU_PLG_HEBDO || $tcMenu==_MENU_PLG_HEBDO_GBL || $tcMenu==_MENU_DISP_HEBDO) && $premierJourSemaine<1 && $majJour!=1) {
        if ($i == 1) {
          $inSemaine = true;
          $background = " width=\"15\" height=\"15\" bgcolor=\"".$CalJourSelection."\" class=\"CalFondDebutSemaine\"";
        } else {
          $background = " width=\"15\" height=\"15\" bgcolor=\"".$CalJourSelection."\" class=\"CalFondMilieuSemaine\"";
        }
      }
      $classJour = (substr($SEMAINE_CALENDRIER,$i-1,1)=="1") ? "jMoisPrec" : "jMoisPrecWE";
      $nbJour = $nbJourMoisPrec-$premierJour+$i+1;
      $tsJour = ($moisPrec) ? mktime(0,0,0,$moisPrec,$nbJour,$anneeEnCours) : mktime(0,0,0,12,$nbJour,$anneeEnCours-1);
      $strout .= "<TD align=\"center\"".$background."><A href=\"javascript: affJour('".$tsJour."');\" class=\"$classJour\">".$nbJour."</A></TD>";
    }
  }

  //Preparation au decalage horaire
  list($age_date,$age_dateAvant,$age_heure_debut,$age_heure_fin) = prepareDecalageH($tzGmt,$tzEte,$tzHiver,mktime(0,0,0,$moisEnCours,1,$anneeEnCours));

  // Recherche des jours du mois courant avec une note ou un anniversaire (agenda et calepin)
  $tabOccupe = array();
  $DB_CX->DbQuery("SELECT DISTINCT DATE_FORMAT(IF(age_aty_id=1,age_date,$age_date),'%e') AS jour FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne WHERE age_id=aco_age_id AND aco_util_id=".(($tcMenu<_MENU_CONTACT) ? $USER_SUBSTITUE : $idUser)." AND ($age_date LIKE '".$anneeEnCours."-".$moisEnCours."-%' OR (age_date LIKE '%-".$moisEnCours."-%' AND DATE_FORMAT(age_date,'%Y%m')<=".date("Ym",mktime(0,0,0,$moisEnCours, 1, $anneeEnCours))." AND age_aty_id=1))");
  while ($enr=$DB_CX->DbNextRow()) {
    $tabOccupe[$enr['jour']]=1;
  }
  // Recherche des notes a cheval
  $DB_CX->DbQuery("SELECT DISTINCT DATE_FORMAT($age_dateAvant,'%e') AS jour FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne WHERE age_id=aco_age_id AND aco_util_id=".(($tcMenu<_MENU_CONTACT) ? $USER_SUBSTITUE : $idUser)." AND ($age_dateAvant LIKE '".$anneeEnCours."-".$moisEnCours."-%' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin!=0 AND age_aty_id=2)");
  while ($enr=$DB_CX->DbNextRow()) {
    $tabOccupe[$enr['jour']]=1;
  }
  $DB_CX->DbQuery("SELECT DISTINCT DATE_FORMAT(cal_date_naissance,'%e') AS jour FROM ${PREFIX_TABLE}calepin WHERE (cal_util_id=".(($tcMenu<_MENU_CONTACT) ? $USER_SUBSTITUE : $idUser)." OR cal_partage='O') AND cal_date_naissance LIKE '%-".$moisEnCours."-%' AND DATE_FORMAT(cal_date_naissance,'%Y%m')<=".date("Ym",mktime(0,0,0,$moisEnCours, 1, $anneeEnCours)));
  while ($enr=$DB_CX->DbNextRow()) {
    $tabOccupe[$enr['jour']]=1;
  }

  $nbJour = 0;
  for($i=$premierJour;$i<8;$i++) {
    $jourFerie = (++$nbJour)."-".$moisEnCours;
    afficheJour($nbJour, $jourFerie, $i);
  }

  $strout .= ("<TD width=\"7\" height=\"17\"><IMG src=\"image/trans.gif\" width=\"5\"></TD></TR>");

  $cpt=1;
  $finDeMois = false;
  for($j=1;!$finDeMois;$j++) {
    if (checkdate($moisEnCours, $nbJour+1, $anneeEnCours)) {
      $strout .= "<TR><TD width=\"7\" height=\"17\"><IMG src=\"image/trans.gif\" width=\"5\"></TD>";
      for($i=1;$i<8;$i++) {
        if (checkdate($moisEnCours, ++$nbJour, $anneeEnCours)) {
          $jourFerie = $nbJour."-".$moisEnCours;
          afficheJour($nbJour, $jourFerie, $i);
        } else {
          $finDeMois = true;
          if ($i == 7 && $inSemaine) {
            $inSemaine = false;
            $background = " width=\"15\" height=\"15\" bgcolor=\"".$CalJourSelection."\" class=\"CalFondFinSemaine\"";
          } elseif ($inSemaine) {
            $background = " width=\"15\" height=\"15\" bgcolor=\"".$CalJourSelection."\" class=\"CalFondMilieuSemaine\"";
          } else {
            $background = " width=\"17\" height=\"17\" bgcolor=\"".$CalGaucheFond."\"";
          }
          $classJour = (substr($SEMAINE_CALENDRIER,$i-1,1)=="1") ? "jMoisPrec" : "jMoisPrecWE";
          $tsJour = mktime(0,0,0,$moisEnCours,$nbJour,$anneeEnCours);
          $strout .= "<TD align=\"center\"".$background."><A href=\"javascript: affJour('".$tsJour."');\" class=\"$classJour\">".($cpt++)."</A></TD>";
        }
      }
      $strout .= "<TD width=\"7\" height=\"17\"><IMG src=\"image/trans.gif\" width=\"5\"></TD></TR>";
    } else {
      $finDeMois = true;
    }
  }

  // Mod D&D
  $strout .= "</TABLE></div>";
  // Fin Mod D&D
  // Si $majJour vaut 0, on est dans l'ecriture normale du calendrier au chargement de la page dans agenda_calendrier
  if ($majJour==0) {
    echo $strout;
  }
  // Sinon on actualise le calendrier des jours et eventuellement des semaines via une fonction JS depuis la frame cachee trash_sid
  else {
    // Si $majHebd vaut 1 on actualise egalement le calendrier des semaines
    $strout2 = "";
    if ($majHebd==1) {
      // Ajustement de la date au 1er Janvier de la nouvelle annee pour afficher correctement la semaine 1
      $crtWeek = date("W",$sd_new);
      $crtYear = ($crtWeek==1 && $moisEnCours==12) ? $anneeEnCours+1 : $anneeEnCours;
      // On determine le TimeStamp du debut de la semaine selectionnee pour l'indiquer dans le calendrier
      $indexJourCrt = date("w",$sd);
      if ($indexJourCrt == 0)
        $indexJourCrt = 7;
      $debutSemaine = mktime(0,0,0,date("n",$sd),date("j",$sd)-$indexJourCrt+1,date("Y",$sd));
      // On determine le TimeStamp du debut de la semaine courante pour l'indiquer dans le calendrier
      $iJCrt = date("w",$localTime);
      if ($iJCrt == 0)
        $iJCrt = 7;
      $tsSemaineCrt = mktime(0,0,0,date("n",$localTime), date("j",$localTime)-$iJCrt+1, date("Y",$localTime));
      include("agenda_calendrier_semaines.php");
    }
    
    // Ecriture dans trash_sid de la fonction JS qui va actualiser le(s) calendrier(s) du menu de gauche
    echo ("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">

<HTML>
<HEAD>
  <META http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
  <META http-equiv=\"Cache-Control\" content=\"no-cache\">
  <META name=\"Author\" content=\"Stephane TEIL (phenix-agenda@laposte.net)\">
  <META name=\"robots\" content=\"noindex\">
  <LINK rel=\"stylesheet\" type=\"text/css\" href=\"css/agenda_css.php\">
  <SCRIPT type=\"text/javascript\">
  <!--
    ns4 = (document.layers) ? true : false;
    ope = (document.getElementById) ? true : false;
    ie4 = (document.all) ? true : false;
    function layerWrite(txt, _layer) {
      if (ie4 && !ope)
        parent.window.frames['nav_".$sid."'].document.all[_layer].innerHTML = txt;
      else if (ope)
        parent.window.frames['nav_".$sid."'].document.getElementById(_layer).innerHTML = txt;
      else if (ns4) {
        var lyr = parent.window.frames['nav_".$sid."'].document._layer.document;
        lyr.write(txt);
        lyr.close();
      }
    }
  //-->
  </SCRIPT>
</HEAD>

<BODY onload=\"javascript:layerWrite('".addslashes(htmlspecialchars($strout))."','calendrierDesJours');".((!empty($strout2)) ? "layerWrite('".addslashes(htmlspecialchars($strout2))."','calendrierDesSemaines');" : "")."\">
".$strout."
</BODY>
</HTML>");

  // Fermeture BDD
  $DB_CX->DbDeconnect();
  }
?>
