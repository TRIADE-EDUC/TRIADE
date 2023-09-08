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

  include("inc/param.inc.php");
  include("inc/html.inc.php");
  include("lang/$APPLI_LANGUE.php");
// ----------------------------------------------------------------------------
// INITIALISATION DE L'AFFICHAGE
// ----------------------------------------------------------------------------
  $v += 0;
  $NOM_PAGE = basename($_SERVER['PHP_SELF']);
// ----------------------------------------------------------------------------
// FORMULAIRE D'IDENTIFICATION
// ----------------------------------------------------------------------------
  function formLog() {
    global $APPLI_VERSION, $NOM_PAGE;
    echo "<html><head><title>".trad("TIMODE_TITRE")."</title></head>";
    echo "<body text=\"#000000\" vlink=\"#800000\" link=\"#800000\" bgcolor=\"#FFFFFF\">";
    echo "<center><form method=\"post\" action=\"".$NOM_PAGE."\" target=\"_self\">";
    echo trad("TIMODE_IDENTIFIANT")."<br><input type=\"text\" name=\"log\" accesskey=\"7\" size=\"10\"><br>";
    echo trad("TIMODE_MOT_DE_PASSE")."<br><input type=\"text\" name=\"pwd\" accesskey=\"9\" size=\"10\">";
    echo "<br><input type=\"submit\" name=\"btOK\" value=\"".trad("TIMODE_BT_OK")."\" accesskey=\"4\"> <input type=\"reset\" name=\"btRaz\" value=\"".trad("TIMODE_BT_RAZ")."\" accesskey=\"6\"></form>";
    echo "<hr size=\"1\"><font color=\"#FF9200\">".sprintf(trad("TIMODE_VERSION_PHENIX"), $APPLI_VERSION)."</font><br>".trad("TIMODE_COPYRIGHT")."</center></body></html>";
  }
// ----------------------------------------------------------------------------
// GENERATION D'UN IDENTIFIANT DE SESSION
// ----------------------------------------------------------------------------
  function SessionId($longueur, $idUser) {
    global $DB_CX, $PREFIX_TABLE;
    $Pool  = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $ok = false;
    // Generation d'un identifiant unique
    while (!$ok) {
      $sid = "";
      for ($index = 0; $index < $longueur; $index++)
        $sid .= substr($Pool, (mt_rand()%(strlen($Pool))), 1);
      $DB_CX->DbQuery("SELECT sid_util_id FROM ${PREFIX_TABLE}sid WHERE sid_id='".$sid."'");
      $ok = ($DB_CX->DbNumRows()==0);
    }
    // Enregistrement de la session
    $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}sid (sid_id, sid_util_id, sid_admin_id, sid_last_maj, sid_session_id, sid_util_subst_id, sid_semaine_type, sid_filtre_couleur) VALUES ('".$sid."',".$idUser.",0,'".date("Y-m-d H:i:s", time())."','',0,'','ALL')");
    return ($sid);
  }
// ----------------------------------------------------------------------------
// VERIFICATION DE L'IDENTIFICATION
// ----------------------------------------------------------------------------
  function Session_ok($idSession) {
    global $DB_CX, $PREFIX_TABLE, $NOM_USER, $APPLI_LANGUE;
    global $droit_PROFILS, $droit_AGENDAS, $droit_NOTES;
    // On recherche les sessions encore valides
    $DB_CX->DbQuery("SELECT util_id, CONCAT(util_nom,' ',util_prenom), util_langue FROM ${PREFIX_TABLE}sid, ${PREFIX_TABLE}utilisateur WHERE sid_id='".$idSession."' AND sid_util_id = util_id AND sid_session_id=''");
    if ($DB_CX->DbNumRows()) {
      // Recuperation de l'uid et du style
      $idUser = $DB_CX->DbResult(0,0);
      $NOM_USER = $DB_CX->DbResult(0,1);
      $APPLI_LANGUE = $DB_CX->DbResult(0,2);
      // Bail ok, on le renouvelle
      $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}sid SET sid_last_maj='".date("Y-m-d H:i:s", time())."' WHERE sid_id='".$idSession."'");

      $DB_CX->DbQuery("SELECT droit_profils, droit_agendas, droit_notes FROM ${PREFIX_TABLE}droit WHERE droit_util_id=".$idUser."");
      if ($DB_CX->DbNumRows()) {
        $droit_PROFILS = $DB_CX->DbResult(0,0);
        $droit_AGENDAS = $DB_CX->DbResult(0,1);
        $droit_NOTES = $DB_CX->DbResult(0,2);
      } else {
        $droit_PROFILS = _DROIT_PROFIL_RIEN;
        $droit_AGENDAS = _DROIT_AGENDA_SEUL;
        $droit_NOTES = _DROIT_NOTE_CONSULT_SEUL;
      }
    } else {
      $droit_PROFILS = _DROIT_PROFIL_RIEN;
      $droit_AGENDAS = _DROIT_AGENDA_SEUL;
      $droit_NOTES = _DROIT_NOTE_CONSULT_SEUL;
      formLog();
      exit;
    }
    return ($idUser);
  }
// ----------------------------------------------------------------------------
// CALCUL DE LA DATE DE BASCULEMENT ETE/HIVER
// ----------------------------------------------------------------------------
  function calculBasculeDST($bascule,$annee,$heure,$gmt,$dst) {
    if (!empty($bascule)) {
      $tabJour = array('Sun'=>0, 'Mon'=>1, 'Tue'=>2, 'Wed'=>3, 'Thu'=>4, 'Fri'=>5, 'Sat'=>6);
      $tabMois = array('Jan'=>1, 'Feb'=>2, 'Mar'=>3, 'Apr'=>4, 'May'=>5, 'Jun'=>6, 'Jul'=>7, 'Aug'=>8, 'Sep'=>9, 'Oct'=>10, 'Nov'=>11, 'Dec'=>12);
      list($mois,$jour) = explode(" ",$bascule);
      if (preg_match('/^last(\w+)/',$jour,$res)) {
        // recherche du dernier jour
        $jourSemaine = $tabJour[$res[1]];
        $dernDate = gmmktime(0,0,0,$tabMois[$mois]+1,0,$annee);
        $dernJour = gmdate('w',$dernDate);
        if ($dernJour >= $jourSemaine) {
          $jour = gmdate('d',($dernDate-(($dernJour-$jourSemaine)*60*60*24)));
        } else {
          $jour = gmdate('d',($derndate-((7+$dernJour-$jourSemaine)*60*60*24)));
        }
      } elseif (preg_match('/^first(\w+)/',$jour,$res)) {
        // recherche du premier jour
        $jourSemaine = $tabJour[$res[1]];
        $premDate = gmmktime(0,0,0,$tabMois[$mois],1,$annee);
        $premJour = gmdate('w',$premDate);
        if ($premJour <= $jourSemaine) {
          $jour = gmdate('d',($premDate+(($jourSemaine-$premJour)*60*60*24)));
        } else {
          $jour = gmdate('d',($premDate+((7+$jourSemaine-$premJour)*60*60*24)));
        }
      } elseif (preg_match('/^(\w+)>=(\d+)/',$jour,$res)) {
        // recherche du jour superieur a
        $jourSemaine = $tabJour[$res[1]];
        $supDate = gmmktime(0,0,0,$tabMois[$mois],$res[2],$annee);
        $supJour = gmdate('w',$supDate);
        if ($supJour <= $jourSemaine) {
          $jour = gmdate('d',($supDate+(($jourSemaine-$supJour)*60*60*24)));
        } else {
          $jour = gmdate('d',($supDate+((7+$jourSemaine-$supJour)*60*60*24)));
        }
      } elseif (preg_match('/^(\w+)<=(\d+)/',$jour,$res)) {
        // recherche du jour inferieur a
        $jourSemaine = $tabJour[$res[1]];
        $infDate = gmmktime(0,0,0,$tabMois[$mois],$res[2],$annee);
        $infJour = gmdate('w',$infDate);
        if ($infJour >= $jourSemaine) {
          $jour = gmdate('d',($infDate-(($infJour-$jourSemaine)*60*60*24)));
        } else {
          $jour = gmdate('d',($infdate-((7+$infJour-$jourSemaine)*60*60*24)));
        }
      }
      $heures = 0;
      $minutes = 0;
      if (preg_match('/(\d+):(\d+)(\w?)/',$heure,$res)) {
        // recherche de l'heure de basculement
        $heures = $res[1];
        $minutes = $res[2];
        if ($res[3]!='u') {
          $heures -= (floor($gmt) + $dst);
          $minute -= (($gmt*60)%60 + $dst);
        }
      }
      $date = mktime($heures,$minutes,0,$tabMois[$mois],$jour,$annee);
    }
    return $date;
  }
// ----------------------------------------------------------------------------
// CALCUL DU DECALAGE HORAIRE POUR LA DATE EN COURS
// ----------------------------------------------------------------------------
  function calculDecalageH($gmt,$ete,$hiver,$date) {
    $dst = 0;
    if (!empty($ete) && !empty($hiver)) {
      // On choisi le test en fonction de l'hemisphere
      if ($hiver > $ete) {  // hemisphere nord
        if ($date >= $ete && $date < $hiver) {$dst = 1;}
      } else {  // hemisphere sud
        if ($date >= $ete || $date < $hiver) {$dst = 1;}
      }
    }
    return $gmt + $dst;
  }
// ----------------------------------------------------------------------------
// DETECTION DES NOTES CONCERNES PAR LA BASCULE ETE/HIVER
// ----------------------------------------------------------------------------
  function detectBascule($gmt,$dEte,$hEte,$dHiver,$hHiver,$date,$heure,$toUTC) {
    $tabDate = explode("-",$date);
    $tzEte = calculBasculeDST($dEte,$tabDate[0],$hEte,$gmt,0);
    $tzHiver = calculBasculeDST($dHiver,$tabDate[0],$hHiver,$gmt,1);
    $hBascule = 0;
    $regul = false;
    if ($toUTC && !empty($dEte)) {
      // calcul de la bascule ete/hiver
      preg_match('/(\d+):(\d+)(\w?)/',$hEte,$res);
      $hbEte = $res[1];
      $mbEte = $res[2];
      if ($res[3]!='u') {
        $hbEte -= floor($gmt);
        $mbEte -= (($gmt*60)%60);
      }
      $basculeEte = $hbEte.".".sprintf("%02d",round($mbEte*100/60));
      // calcul de la bascule hiver/ete
      preg_match('/(\d+):(\d+)(\w?)/',$hHiver,$res);
      $hbHiver = $res[1];
      $mbHiver = $res[2];
      if ($res[3]!='u') {
        $hbHiver -= (floor($gmt) + 1);
        $mbHiver -= (($gmt*60)%60 + 1);
      }
      $basculeHiver = $hbHiver.".".sprintf("%02d",round($mbHiver*100/60));
      // on regarde si on est le jour de la bascule
      $bascule = "";
      if ($date==date("Y-m-d",$tzEte)) $bascule = $basculeEte;
      if ($date==date("Y-m-d",$tzHiver)) $bascule = $basculeHiver;
      if (!empty($bascule)) {
        if ($heure >= ($bascule + $gmt) && $heure < ($bascule + $gmt + 1)) {
          $heure = $bascule + $gmt + 1;
          $regul = true;
        }
        $hBascule = $gmt;
      }
    }
    return array($tzEte,$tzHiver,$hBascule,$heure,$regul);
  }
// ----------------------------------------------------------------------------
// DECALAGE DES NOTES EN FONCTION DU FUSEAU HORAIRE, DEPUIS OU VERS UTC
// ----------------------------------------------------------------------------
  function decaleNote($gmt,$dEte,$hEte,$dHiver,$hHiver,$dateJour,$dateNote,$hdeb,$hfin,$dateCrt,$dateModif,$nolimit=false,$toUTC=false,$brut=false) {
    $tabDate = explode("-",$dateNote);
    // Detection du jour de bascule
    list($tzEte,$tzHiver,$hBascule,$hdeb,$reguldeb) = detectBascule($gmt,$dEte,$hEte,$dHiver,$hHiver,$dateNote,$hdeb,$toUTC);
    list($tzEte,$tzHiver,$hBascule,$hfin,$regulfin) = detectBascule($gmt,$dEte,$hEte,$dHiver,$hHiver,$dateNote,$hfin,$toUTC);
    // s'il y a regul et superposition, on decale
    if (($reguldeb || $regulfin) && $hdeb==$hfin) $hfin +=0.25;
    // Conversion en fonction du timezone
    $decalHD = calculDecalageH($gmt,$tzEte,$tzHiver,mktime(floor($hdeb)-floor($hBascule),($hdeb*60)%60-($hBascule*60)%60,0,$tabDate[1],$tabDate[2],$tabDate[0]));
    $decalHF = calculDecalageH($gmt,$tzEte,$tzHiver,mktime(floor($hfin)-floor($hBascule),($hfin*60)%60-($hBascule*60)%60,0,$tabDate[1],$tabDate[2]+(($hdeb>=$hfin)?1:0),$tabDate[0]));
    // On decale vers utc
    if ($toUTC) {
      $hdeb -= $decalHD;
      $hfin -= $decalHF;
    } else {  // ou bien depuis
      $hdeb += $decalHD;
      $hfin += $decalHF;
    }
    // On normalise les heures et la date
    if ($hdeb < 0) {
      $hdeb += 24;
      if ($toUTC || $nolimit) $dateNote = date("Y-m-d",mktime(12,0,0,$tabDate[1],$tabDate[2]-1,$tabDate[0]));
    }
    if ($hfin < 0) $hfin += 24;
    if ($hdeb >= 24) {
      $hdeb -= 24;
      if ($toUTC || $nolimit) $dateNote = date("Y-m-d",mktime(12,0,0,$tabDate[1],$tabDate[2]+1,$tabDate[0]));
    }
    if ($hfin >= 24) $hfin -= 24;
    // Regularisation pour les notes a cheval sur deux jours
    if (!$nolimit) {
      if ($hdeb >= $hfin) {
        if ($gmt >= 0) {
          if ($dateNote < $dateJour) $hdeb = 0;
          if ($dateNote == $dateJour) $hfin = 24;
        } else {
          if ($dateNote > $dateJour) $hfin = 24;
          if ($dateNote == $dateJour) $hdeb = 0;
        }
      }
    }
    // On s'occupe de l'heure de creation
    $ageDateCrt = explode(" ",$dateCrt);
    if ($ageDateCrt[1]!="00:00:00" && !empty($ageDateCrt[1])) {
      $dtCrt = explode("-",$ageDateCrt[0]);
      $hrCrt = explode(":",$ageDateCrt[1]);
      list($tzEte,$tzHiver,$hBascule,$hCrt,$regul) = detectBascule($gmt,$dEte,$hEte,$dHiver,$hHiver,$ageDateCrt[0],$hrCrt[0].".".sprintf("%02d",round($hrCrt[1]*100/60)),$toUTC);
      $hrCrt[0] = floor($hCrt-$hBascule);
      $hrCrt[1] = round((($hCrt-$hBascule)*60)%60);
      $decalHC = calculDecalageH($gmt,$tzEte,$tzHiver,mktime($hrCrt[0],$hrCrt[1],$hrCrt[2],$dtCrt[1],$dtCrt[2],$dtCrt[0]));
      if ($toUTC) $decalHC = -$decalHC;
      $dateCrt = date("Y-m-d H:i:s",mktime($hrCrt[0]+floor($decalHC),$hrCrt[1]+($decalHC*60)%60,$hrCrt[2],$dtCrt[1],$dtCrt[2],$dtCrt[0]));
    }
    // On renvoi les donnees brutes ou on formate
    if ($brut) {
      $dateCrtOK = $dateCrt;
    } else {
      $dateCrtOK = strftime(trad("COMMUN_FORMAT_DATE_CREATION"),strtotime($dateCrt));
    }
    // On s'occupe de l'heure de modification
    $ageDateMdf = explode(" ",$dateModif);
    if ($ageDateMdf[1]!="00:00:00" && !empty($ageDateMdf[1])) {
      $dtMdf = explode("-",$ageDateMdf[0]);
      $hrMdf = explode(":",$ageDateMdf[1]);
      list($tzEte,$tzHiver,$hBascule,$hMdf,$regul) = detectBascule($gmt,$dEte,$hEte,$dHiver,$hHiver,$ageDateMdf[0],$hrMdf[0].".".sprintf("%02d",round($hrMdf[1]*100/60)),$toUTC);
      $hrMdf[0] = floor($hMdf-$hBascule);
      $hrMdf[1] = round((($hMdf-$hBascule)*60)%60);
      $decalHM = calculDecalageH($gmt,$tzEte,$tzHiver,mktime($hrMdf[0],$hrMdf[1],$hrMdf[2],$dtMdf[1],$dtMdf[2],$dtMdf[0]));
      if ($toUTC) $decalHM = -$decalHM;
      $dateModif = date("Y-m-d H:i:s",mktime($hrMdf[0]+floor($decalHM),$hrMdf[1]+($decalHM*60)%60,$hrMdf[2],$dtMdf[1],$dtMdf[2],$dtMdf[0]));
    }
    // On renvoi les donnees brutes ou on formate
    if ($brut) {
      $dateModifOK = $dateModif;
    } else {
      $dateModifOK = strftime(trad("COMMUN_FORMAT_DATE_CREATION"),strtotime($dateModif));
    }
    // On elimine des incoherences sur les anciennes notes
    if ($dateCrt > $dateModif) {
      $dateModifOK = $dateCrtOK;
    }
    if ($toUTC || $nolimit) {
      return array($hdeb,$hfin,$dateCrtOK,$dateModifOK,$dateNote);
    } else {
      return array($hdeb,$hfin,$dateCrtOK,$dateModifOK);
    }
  }
// ----------------------------------------------------------------------------
// PREPARATION AU DECALAGE HORAIRE POUR LES REQUETES SQL
// ----------------------------------------------------------------------------
  function prepareDecalageH($gmt,$ete,$hiver,$date) {
    if ($date==$ete || $date==$hiver) $date -= 3600;  // cas particulier
    $decalageHoraire = calculDecalageH($gmt,$ete,$hiver,$date);
    $age_date = "DATE_FORMAT((age_date + INTERVAL (age_heure_debut+".$decalageHoraire.")*60 MINUTE),'%Y-%m-%d')";
    $age_dateAvant = "DATE_FORMAT((age_date + INTERVAL (age_heure_debut+".($decalageHoraire+24).")*60 MINUTE),'%Y-%m-%d')";
    $basculeEte = date("G",$ete).".".sprintf("%02d",round(date("i",$ete)*100/60));
    $basculeHiver = date("G",$hiver).".".sprintf("%02d",round(date("i",$hiver)*100/60));
    $age_heure_debut = "((age_heure_debut+".($decalageHoraire+24).")-FLOOR((age_heure_debut+".($decalageHoraire+24).")/24)*24)";
    $age_heure_debut .= " + IF(age_date='".date("Y-m-d",$ete)."' AND age_heure_debut>=".$basculeEte.",1,0)";
    $age_heure_debut .= " - IF(age_date='".date("Y-m-d",$hiver)."' AND age_heure_debut>=".$basculeHiver.",1,0)";
    $age_heure_fin = "((age_heure_fin+".($decalageHoraire+24).")-FLOOR((age_heure_fin+".($decalageHoraire+24).")/24)*24)";
    $age_heure_fin .= " + IF(age_date='".date("Y-m-d",$ete)."' AND age_heure_fin>=".$basculeEte.",1,0)";
    $age_heure_fin .= " - IF(age_date='".date("Y-m-d",$hiver)."' AND age_heure_fin>=".$basculeHiver.",1,0)";
    return array($age_date,$age_dateAvant,$age_heure_debut,$age_heure_fin);
  }
// ----------------------------------------------------------------------------
// IDENTIFICATION DE L'UTILISATEUR
// ----------------------------------------------------------------------------
  if (!isset($sid)) {
    // Cryptage du mot de passe
    $ztPasswd = md5(trim($pwd));
    // Recherche de l'utilisateur correspondant
    $DB_CX->DbQuery("SELECT util_id FROM ${PREFIX_TABLE}utilisateur WHERE util_login='".$log."' AND util_passwd='".$ztPasswd."'");

    if ($DB_CX->DbNumRows()) {
      // L'utilisateur existe
      $idUser = $DB_CX->DbResult(0,0);
      // On genere un nouveau sid
      mt_srand((double)microtime()*1000000);
      $sid = SessionId(8, $idUser);
    } else {
      // L'utilisateur n'existe pas
      // Fermeture BDD
      $DB_CX->DbDeconnect();
      formLog();
      exit;
    }
  }

  $idUser = Session_ok($sid);
  // Ecrase la selection de la langue par defaut par le choix de l'utilisateur
  @include("lang/$APPLI_LANGUE.php");

// ----------------------------------------------------------------------------
// RECUPERATION ET FORMATAGE DE LA DATE A TRAITER
// ----------------------------------------------------------------------------
  // Recuperation des infos de timezone de l'utilisateur
  $DB_CX->DbQuery("SELECT tzn_libelle, tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver, util_format_heure FROM ${PREFIX_TABLE}utilisateur, ${PREFIX_TABLE}timezone WHERE util_id=".$idUser." AND tzn_zone=util_timezone");
  $tzLibelle = htmlentities($DB_CX->DbResult(0,0));
  $tzGmt = $DB_CX->DbResult(0,"tzn_gmt");
  $tzDateEte = $DB_CX->DbResult(0,"tzn_date_ete");
  $tzHeureEte = $DB_CX->DbResult(0,"tzn_heure_ete");
  $tzDateHiver = $DB_CX->DbResult(0,"tzn_date_hiver");
  $tzHeureHiver = $DB_CX->DbResult(0,"tzn_heure_hiver");
  // Calcul des bascules ete/hiver pour la date et l'heure locale
  $tzEte = calculBasculeDST($tzDateEte,gmdate("Y"),$tzHeureEte,$tzGmt,0);
  $tzHiver = calculBasculeDST($tzDateHiver,gmdate("Y"),$tzHeureHiver,$tzGmt,1);
  $formatHeure = $DB_CX->DbResult(0,"util_format_heure")==12 ? "h:ia" : "H:i";

  // Ajustement de la date en fonction du timezone
  $decalageHoraire = calculDecalageH($tzGmt,$tzEte,$tzHiver,mktime(gmdate("H"),gmdate("i"),0,gmdate("n"),gmdate("j"),gmdate("Y")));
  $localTime = mktime(gmdate("H")+floor($decalageHoraire),gmdate("i")+($decalageHoraire*60)%60,gmdate("s"),gmdate("n"),gmdate("j"),gmdate("Y"));

  $sd += 0;
  // Recalcul des bascules ete/hiver en tenant compte de la date affichee
  $sdAnnee = (!$sd) ? ((!isset($annee)) ? gmdate("Y") : $annee) : date("Y", $sd);
  $tzEte = calculBasculeDST($tzDateEte,$sdAnnee,$tzHeureEte,$tzGmt,0);
  $tzHiver = calculBasculeDST($tzDateHiver,$sdAnnee,$tzHeureHiver,$tzGmt,1);

  if (!$sd) {
    if (!isset($jour))  $jour = ((!empty($mois)) ? 1 : date("j",$localTime));
    if (!isset($mois))  $mois = date("n",$localTime);
    if (!isset($annee)) $annee = date("Y",$localTime);
    $sd = mktime(0,0,0,$mois, $jour, $annee);
  }
  $jourEnCours  = date("d", $sd);
  $moisEnCours  = date("m", $sd);
  $anneeEnCours = date("Y", $sd);
// ----------------------------------------------------------------------------
// ENTETE HTML COMMUNE
// ----------------------------------------------------------------------------
  echo ("<html>
<head>
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
  <title>".trad("TIMODE_TITRE")."</title>
  <base target=\"_self\">
</head>
<body text=\"#000000\" vlink=\"#800000\" link=\"#800000\" bgcolor=\"#FFFFFF\" leftmargin=\"1\" topmargin=\"2\" rightmargin=\"1\" bottommargin=\"0\" marginwidth=\"1\" marginheight=\"2\">
<table width=\"100%\"><tr><td bgcolor=\"#FFB655\" align=\"center\">".(($v>4) ? sprintf(trad("TIMODE_CALEPIN_DE"), $NOM_USER) : sprintf(trad("TIMODE_AGENDA_DE"), $NOM_USER))."</td></tr></table>");
// ----------------------------------------------------------------------------
// FORMULAIRE DE RECHERCHE DANS LE CALEPIN
// ----------------------------------------------------------------------------
  if ($v==5) {
    echo "<form method=\"post\" action=\"".$NOM_PAGE."\">";
    echo "<input type=\"hidden\" name=\"sid\" value=\"".$sid."\"><input type=\"hidden\" name=\"v\" value=\"6\">";
    echo "<table align=\"center\"><tr><td>".trad("TIMODE_LIB_RECHERCHE_NOM")."<br><input type=\"text\" name=\"nom\" accesskey=\"4\" size=\"10\" value=\"\"> <input type=\"submit\" name=\"btOK\" value=\"".trad("TIMODE_BT_OK")."\" accesskey=\"6\"></td></tr></table></form>";
    echo "<hr size=\"1\"><center>&#59109; <a href=\"".$NOM_PAGE."?sid=".$sid."\" accesskey=\"4\">".trad("TIMODE_MENU_AGENDA")."</a> - <a href=\"".$NOM_PAGE."?sid=".$sid."&v=2\" accesskey=\"5\">".trad("TIMODE_MENU_NOTE")."</a> &#59110;</center>";
  }
// ----------------------------------------------------------------------------
// RESULTAT DE LA RECHERCHE DANS LE CALEPIN
// ----------------------------------------------------------------------------
  elseif ($v==6) {
    if (trim($nom)=="")
      echo "<center><font color=\"FF0000\">".trad("TIMODE_SAISIR_BRIBE")."</font></center><hr size=\"1\">";
    else {
      $strOutput = "";
      $DB_CX->DbQuery("SELECT DISTINCT cal_id, CONCAT(cal_nom,' ',cal_prenom) AS nomContact, cal_domicile, cal_travail, cal_portable, cal_email, cal_emailpro FROM ${PREFIX_TABLE}calepin WHERE (LOWER(cal_nom) LIKE LOWER('%".trim($nom)."%') OR LOWER(cal_prenom) LIKE LOWER('%".trim($nom)."%')) AND (cal_util_id=".$idUser." OR (cal_util_id!=".$idUser." AND cal_partage='O')) ORDER BY cal_nom ASC, cal_prenom ASC, cal_societe ASC");
      if ($DB_CX->DbNumRows()) {
        echo "<table width=\"100%\"><tr><td bgcolor=\"#6D92AA\" align=\"center\">".sprintf(trad("TIMODE_NB_REPONSES"), $DB_CX->DbNumRows(), (($DB_CX->DbNumRows()>1)?trad("COMMUN_PLURIEL"):""))."</td></tr></table>";
        while ($enr = $DB_CX->DbNextRow()) {
          $strOutput .= "<font color=\"4924FF\">".trim($enr['nomContact'])."</font>";
          if ($enr['cal_domicile']!="") {
            $enr['cal_domicile'] = preg_replace( "/[^0-9+]+/","",$enr['cal_domicile']);
            $strOutput .= "<br>".trad("TIMODE_LIB_TEL_DOMICILE")."<a href=\"tel:".$enr['cal_domicile']."\">".$enr['cal_domicile']."</a>";
          }
          if ($enr['cal_travail']!="") {
            $enr['cal_travail'] = preg_replace( "/[^0-9+]+/","",$enr['cal_travail']);
            $strOutput .= "<br>".trad("TIMODE_LIB_TEL_TRAVAIL")."<a href=\"tel:".$enr['cal_travail']."\">".$enr['cal_travail']."</a>";
          }
          if ($enr['cal_portable']!="") {
            $enr['cal_portable'] = preg_replace( "/[^0-9+]+/","",$enr['cal_portable']);
            $strOutput .= "<br>".trad("TIMODE_LIB_TEL_PORTABLE")."<a href=\"tel:".$enr['cal_portable']."\">".$enr['cal_portable']."</a>";
          }
          if ($enr['cal_email']!="")
            $strOutput .= "<br>&#59091; <a href=\"mailto:".$enr['cal_email']."\">".$enr['cal_email']."</a>";
          if ($enr['cal_emailpro']!="")
            $strOutput .= "<br>&#59091; <a href=\"mailto:".$enr['cal_emailpro']."\">".$enr['cal_emailpro']."</a>";
          $strOutput .= "<br><center><a href=\"".$NOM_PAGE."?sid=".$sid."&v=7&id=".$enr['cal_id']."&nom=".urlencode(stripslashes($nom))."\">".trad("TIMODE_BT_DETAIL")."</a></center><hr size=\"1\">";
        }
      }
      else
        $strOutput = "<center><font color=\"#FF0000\">".trad("TIMODE_AUCUN_CONTACT")."</font></center><hr size=\"1\">";
    }
    echo $strOutput."<center>&#59111; <a href=\"".$NOM_PAGE."?sid=".$sid."&v=5\" accesskey=\"6\">".trad("TIMODE_BT_AUTRE_RECHERCHE")."</a><br>&#59109; <a href=\"".$NOM_PAGE."?sid=".$sid."\" accesskey=\"4\">".trad("TIMODE_MENU_AGENDA")."</a>  - <a href=\"".$NOM_PAGE."?sid=".$sid."&v=2\" accesskey=\"5\">".trad("TIMODE_MENU_NOTE")."</a> &#59110;</center>";
  }
// ----------------------------------------------------------------------------
// DETAIL D'UN CONTACT
// ----------------------------------------------------------------------------
  elseif ($v==7) {
    $strOutput = "";
    $DB_CX->DbQuery("SELECT * FROM ${PREFIX_TABLE}calepin WHERE cal_id=".$id);
    if ($DB_CX->DbNumRows()) {
      $enr = $DB_CX->DbNextRow();
      if (!empty($enr['cal_societe']))   // Societe
        $strOutput .= $enr['cal_societe']."<br>";
      if (!empty($enr['cal_nom']) || !empty($enr['cal_prenom']))  // Nom et Prenom
        $strOutput .= "<font color=\"4924FF\">".trim($enr['cal_nom']." ".$enr['cal_prenom'])."</font>";
      if (!empty($enr['cal_adresse']))   // Adresse
        $strOutput .= "<br>".str_replace(chr(13),"",str_replace(chr(10),"<br>",$enr['cal_adresse']));
      if (!empty($enr['cal_cp']) || !empty($enr['cal_ville']))  // Code postal et Ville
        $strOutput .= "<br>".trim($enr['cal_cp']." ".$enr['cal_ville']);
      if (!empty($enr['cal_pays']))   // Pays
        $strOutput .= "<br>".$enr['cal_pays'];
      if (!empty($enr['cal_date_naissance']) && $enr['cal_date_naissance']!="0000-00-00") { // Age
        $tabDate = explode("-",$enr['cal_date_naissance']);
        $age=date("Y")-$tabDate[0];
        if (date("md")<($tabDate[1].$tabDate[2]))
          $age--;
        $strOutput .= "<br><br>&#59014;&nbsp;".$tabDate[2]."/".$tabDate[1]."/".$tabDate[0]." (".$age." ans)";
      }
      $strOutput .= "<br>";
      if (!empty($enr['cal_domicile'])) {  // Telephone domicile
        $enr['cal_domicile'] = preg_replace( "/[^0-9+]+/","",$enr['cal_domicile']);
        $strOutput .= "<br>".trad("TIMODE_LIB_TEL_DOMICILE")."<a href=\"tel:".$enr['cal_domicile']."\">".$enr['cal_domicile']."</a>";
      }
      if (!empty($enr['cal_travail'])) {  // Telephone professionnel
        $enr['cal_travail'] = preg_replace( "/[^0-9+]+/","",$enr['cal_travail']);
        $strOutput .= "<br>".trad("TIMODE_LIB_TEL_TRAVAIL")."<a href=\"tel:".$enr['cal_travail']."\">".$enr['cal_travail']."</a>";
      }
      if (!empty($enr['cal_portable'])) { // Portable
        $enr['cal_portable'] = preg_replace( "/[^0-9+]+/","",$enr['cal_portable']);
        $strOutput .= "<br>".trad("TIMODE_LIB_TEL_PORTABLE")."<a href=\"tel:".$enr['cal_portable']."\">".$enr['cal_portable']."</a>";
      }
      if (!empty($enr['cal_fax']))   // Fax
        $strOutput .= "<br>".trad("TIMODE_LIB_FAX").preg_replace( "/[^0-9+]+/","",$enr['cal_fax']);
      $strOutput .= "<br>";
      if (!empty($enr['cal_email']))  // Adresse Email
        $strOutput .= "<br>&#59091; <a href=\"mailto:".$enr['cal_email']."\">".$enr['cal_email']."</a>";
      if (!empty($enr['cal_emailpro']))  // Adresse Email Pro
        $strOutput .= "<br>&#59091; <a href=\"mailto:".$enr['cal_emailpro']."\">".$enr['cal_emailpro']."</a>";
      $strOutput .= "<br>";
      if (!empty($enr['cal_icq']))  // ICQ
        $strOutput .= "<br>".trad("TIMODE_LIB_ICQ").$enr['cal_icq'];
      if (!empty($enr['cal_aim']))  // AIM
        $strOutput .= "<br>".trad("TIMODE_LIB_AIM").$enr['cal_aim'];
      if (!empty($enr['cal_msn']))  // MSN
        $strOutput .= "<br>".trad("TIMODE_LIB_MSN")."<a href=\"mailto:".$enr['cal_msn']."\">".$enr['cal_msn']."</a>";
      if (!empty($enr['cal_yahoo']))  // YAHOO
        $strOutput .= "<br>".trad("TIMODE_LIB_YAHOO")."".$enr['cal_yahoo'];
      if (!empty($enr['cal_note']))  // Commentaire
        $strOutput .= "<br><br>".str_replace(chr(13),"",str_replace(chr(10),"<br>",$enr['cal_note']));
      while (substr($strOutput,-4)=="<br>")
        $strOutput = substr($strOutput,0,strlen($strOutput)-4);
      $strOutput .= "<hr size=\"1\">";
    }
    else
      $strOutput = "<font color=\"#FF0000\">".trad("TIMODE_AUCUN_CONTACT")."</font><hr size=\"1\">";
    // Retour vers l'agenda si affichage d'un contact associe a une note, sinon retour vers la recherche
    $lienRetour = ($f == "note") ? "&v=1&sd=$sd" : "&v=6&nom=".urlencode(stripslashes($nom));
    echo $strOutput."<center>&#59110; <a href=\"".$NOM_PAGE."?sid=".$sid.$lienRetour."\" accesskey=\"5\">".trad("TIMODE_BT_RETOUR")."</a></center>";
  }
// ----------------------------------------------------------------------------
// MODULE CREATION/MODIFICATION DE NOTE
// ----------------------------------------------------------------------------
  elseif ($v==2) {
    $DB_CX->DbQuery("SELECT util_debut_journee FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$idUser);
    //Pour une nouvelle note, on se positionne en debut de journee du profil
    $enr['ageDate'] = date("d/m/Y",$sd);
    $enr['age_heure_debut'] = $DB_CX->DbResult(0,0);
    $enr['age_heure_fin'] = $DB_CX->DbResult(0,0)+0.25;
    $url = "?sid=".$sid."&sd=".$sd."&v=3&ztAction=INSERT";
    //Recuperation des informations sur la note pour une modification
    if ($id) {
      $DB_CX->DbQuery("SELECT age_id, age_libelle, age_detail, age_date, age_heure_debut, age_heure_fin, age_prive, age_rappel, age_rappel_coeff, age_aty_id, age_date_creation, age_date_modif FROM ${PREFIX_TABLE}agenda WHERE age_id=".$id." AND age_util_id=".$idUser." AND age_aty_id!=1");
      if ($enr = $DB_CX->DbNextRow()) {
        $tabDate = explode("-",$enr['age_date']);
        // Decalage de la note
        list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['dateCreation'],$enr['dateModif'],$enr['ageDate']) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$enr['age_date'],$enr['age_date'],$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif'],1);
        if ($enr['age_heure_debut'] > $enr['age_heure_fin'] && $enr['age_heure_fin'] == 0) $enr['age_heure_fin'] = "24.00";
        $tabDate = explode("-",$enr['ageDate']);
        $enr['ageDate'] = date("d/m/Y",mktime(0,0,0,$tabDate[1],$tabDate[2],$tabDate[0]));
        $ztAction = "UPDATE";
        $url = "?sid=".$sid."&sd=".$sd."&v=3&ztAction=UPDATE&id=".$enr['age_id']."";
      }
    }
    echo "<form method=\"post\" action=\"".$NOM_PAGE.$url."\" target=\"_self\">";
    echo trad("TIMODE_LIB_LIBELLE")."<br><input type=\"text\" name=\"ztLibelle\" size=\"15\" value=\"".htmlspecialchars($enr['age_libelle'])."\"><br>";
    echo trad("TIMODE_LIB_DETAIL")."<br><input type=\"text\" name=\"ztDetail\" size=\"15\" value=\"".htmlspecialchars($enr['age_detail'])."\"><br>";
    echo trad("TIMODE_LIB_DATE")."<br><input type=\"text\" name=\"ztDate\" size=\"10\" value=\"".$enr['ageDate']."\"><br>";
    echo "<input type=\"checkbox\" name=\"ckTypeNote\" value=\"3\"".(($enr['age_aty_id']==3) ? " checked" : "").">&nbsp;".trad("COMMUN_JOURNEE_ENTIERE")."<br>";
    echo trad("TIMODE_HEURE_DEBUT")."<br><select name=\"zlHeureDebut\">";
    for ($i=0; $i<24;$i=$i+0.25) {
      $selected = ($i == $enr['age_heure_debut']) ? " selected" : "";
      echo "<option value=\"".$i."\"".$selected.">".afficheHeure($i,$i,$formatHeure)."</option>";
    }
    echo "</select><br>";
    echo trad("TIMODE_HEURE_FIN")."<br><select name=\"zlHeureFin\">";
    for ($i=0.25; $i<=24;$i=$i+0.25) {
      $selected = ($i == $enr['age_heure_fin']) ? " selected" : "";
      echo "<option value=\"".$i."\"".$selected.">".afficheHeure($i,$i,$formatHeure)."</option>";
    }
    echo "</select><br>";
    echo trad("TIMODE_LIB_PARTAGE")."<br><select name=\"zlPartage\">";
    echo "<option value=\"0\"".(($enr['age_prive']!=1) ? " selected" : "").">".trad("TIMODE_PUBLIQUE")."</option>";
    echo "<option value=\"1\"".(($enr['age_prive']==1) ? " selected" : "").">".trad("TIMODE_PRIVEE")."</option>";
    echo "</select><br>";
    echo trad("TIMODE_LIB_RAPPEL")."<br><select name=\"zlR1\">";
    for ($i=0;$i<60;$i++) {
      $selected = ($enr['age_rappel']==$i) ? " selected" : "";
      echo "<option value=\"".$i."\"".$selected.">".$i."</option>";
    }
    echo "</select> <select name=\"zlR2\">";
    echo "<option value=\"1\"".(($enr['age_rappel_coeff']==1) ? " selected" : "").">".trad("COMMUN_MINUTE")."</option>";
    echo "<option value=\"60\"".(($enr['age_rappel_coeff']==60) ? " selected" : "").">".trad("COMMUN_HEURE")."</option>";
    echo "<option value=\"1440\"".(($enr['age_rappel_coeff']==1440) ? " selected" : "").">".trad("COMMUN_JOUR")."</option>";
    echo "</select>";
    echo "<br><input type=\"submit\" name=\"btOK\" value=\"".trad("TIMODE_BT_OK")."\" accesskey=\"4\"> <input type=\"reset\" name=\"btRaz\" value=\"".trad("TIMODE_BT_RAZ")."\" accesskey=\"6\">";
    echo "  <hr size=\"1\"><center><a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."\" accesskey=\"4\">".trad("TIMODE_MENU_AGENDA")."</a> &#59109;</center>";
  }
// ----------------------------------------------------------------------------
// MODULE ENREGISTREMENT D'UNE NOTE
// ----------------------------------------------------------------------------
  elseif ($v==3) {
    $tabDate = explode("/",$ztDate);
    //Si la date saisie est erronee, on enregistre a la date du jour
    if (!checkdate($tabDate[1],$tabDate[0],$tabDate[2])) {
      $tabDate[0]=date("j",$localTime);
      $tabDate[1]=date("n",$localTime);
      $tabDate[2]=date("Y",$localTime);
    }
    $sd = mktime(0,0,0,$tabDate[1],$tabDate[0],$tabDate[2]);

    // Contre-mesure de certains providers
    $zlHeureDebut=str_replace(",",".",$zlHeureDebut);
    $zlHeureFin=str_replace(",",".",$zlHeureFin);

    // Conversion en utc en fonction du timezone
    $dateNote = $tabDate[2]."-".$tabDate[1]."-".$tabDate[0];
    list($zlHeureDebutUTC,$zlHeureFinUTC,$dateCrtUTC,$dateModifUTC,$dateNoteUTC) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$dateJour,$dateNote,$zlHeureDebut,$zlHeureFin,$dateCrt,$dateModif,1,1);
    $dateUTC = explode("-",$dateNoteUTC);
    $tabDateUTC = array($dateUTC[2],$dateUTC[1],$dateUTC[0]);

    $ztDateUTC = $tabDateUTC[2]."-".$tabDateUTC[1]."-".$tabDateUTC[0];
    $hNoteUTC = floor($zlHeureDebutUTC);
    $mNoteUTC = ($zlHeureDebutUTC*60)%60;
    if (!$zlR1)
      $zlR2=1;
    if ($ckTypeNote!=3)
      $ckTypeNote=2;
    $tsNow = mktime(gmdate("H"),gmdate("i"),0,gmdate("n"),gmdate("j"),gmdate("Y"));
    $tsAlert = mktime(gmdate("H"),gmdate("i")+($zlR1*$zlR2),0,gmdate("n"),gmdate("j"),gmdate("Y"));
    $tsNoteUTC = mktime($hNoteUTC,$mNoteUTC,0,$tabDateUTC[1],$tabDateUTC[0],$tabDateUTC[2]);
    $endNote = ($tsNoteUTC > $tsNow) ? 0 : 1;
    $alert = ($tsNoteUTC > $tsAlert && $zlR1) ? 0 : 1;
    $ztLibelle = trim($ztLibelle);
    $ztDetail = trim($ztDetail);

    if ($ztAction == "INSERT") {
      $dateCreation = gmdate("Y-m-d H:i:s", time());
      $sql = "INSERT INTO ${PREFIX_TABLE}agenda (age_util_id,age_aty_id,age_date,age_heure_debut,age_heure_fin, age_libelle, age_detail, age_rappel, age_rappel_coeff, age_prive, age_createur_id, age_date_creation, age_modificateur_id, age_date_modif) ";
      $sql .= "VALUES (".$idUser.",".$ckTypeNote.",'".$ztDateUTC."',".$zlHeureDebutUTC.",".$zlHeureFinUTC.",'".$ztLibelle."','".$ztDetail."',".$zlR1.",".$zlR2.",".$zlPartage.",".$idUser.", '".$dateCreation."',".$idUser.", '".$dateCreation."')";
      $DB_CX->DbQuery($sql);
      $idAge = $DB_CX->DbInsertID();
      $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}agenda_concerne VALUES (".$idAge.",".$idUser.",".$alert.",".$endNote.")");
    }
    elseif ($ztAction == "UPDATE" && $id) {
      $sql = "UPDATE ${PREFIX_TABLE}agenda ";
      $sql .= "SET age_aty_id=".$ckTypeNote.",";
      $sql .= " age_date='".$ztDateUTC."',";
      $sql .= " age_heure_debut=".$zlHeureDebutUTC.",";
      $sql .= " age_heure_fin=".$zlHeureFinUTC.",";
      $sql .= " age_libelle='".$ztLibelle."',";
      $sql .= " age_detail='".$ztDetail."',";
      $sql .= " age_rappel=".$zlR1.",";
      $sql .= " age_rappel_coeff=".$zlR2.",";
      $sql .= " age_prive=".$zlPartage.", ";
      $sql .= " age_date_modif='".gmdate("Y-m-d H:i:s", time())."',";
      $sql .= " age_modificateur_id=".$idUser." ";
      $sql .= "WHERE age_id=".$id." AND age_util_id=".$idUser;
      $DB_CX->DbQuery($sql);
    }
    //Renvoi vers l'affichage du detail de la journee
    $v=1;
  }
// ----------------------------------------------------------------------------
// MODULE SUPPRESSION D'UNE NOTE
// ----------------------------------------------------------------------------
  elseif ($v==4) {
    echo "<center>";
    if ($id && $ztAction!="DELETE" && $flag) {
      switch ($flag) {
        case 1 : echo trad("TIMODE_JS_SUPPR_NOTE")."<br>&#59106; <a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&id=".$id."&v=4&ztAction=DELETE&flag=1\" accesskey=\"1\">".strtoupper(trad("COMMUN_OUI"))."</a> - <a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&v=1\" accesskey=\"3\">".strtoupper(trad("COMMUN_NON"))."</a> &#59108;"; break;
        case 2 : echo trad("TIMODE_JS_SUPPR_NOTE_AFFECTEE")."<br>&#59106; <a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&id=".$id."&v=4&ztAction=DELETE&flag=2\" accesskey=\"1\">".strtoupper(trad("COMMUN_OUI"))."</a> - <a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&v=1\" accesskey=\"3\">".strtoupper(trad("COMMUN_NON"))."</a> &#59108;"; break;
        case 3 : echo trad("TIMODE_JS_SUPPR_OCCURENCE")."<br>&#59106; <a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&id=".$id."&v=4&ztAction=DELETE&flag=3\" accesskey=\"1\">".strtoupper(trad("COMMUN_OUI"))."</a> - <a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&v=1\" accesskey=\"3\">".strtoupper(trad("COMMUN_NON"))."</a> &#59108;"; break;
        default : ; break;
      }
    }
    elseif ($id && $ztAction=="DELETE" && $flag) {
      if ($flag == 2 && $AUTORISE_SUPPR) {
        //Suppression d'une note affectee
        $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda_concerne WHERE aco_age_id=".$id." AND aco_util_id=".$idUser);
        $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}information WHERE info_age_id=".$id." AND info_destinataire_id=".$idUser);
        //Recherche s'il reste des personnes concernees par cette note
        $DB_CX->DbQuery("SELECT aco_util_id FROM ${PREFIX_TABLE}agenda_concerne WHERE aco_age_id=".$id);
        //si NON : on efface la note
        if (!$DB_CX->DbNumRows())
          $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda WHERE age_id=".$id);
      }
      elseif ($flag == 1) {
        //Suppression de la totalite d'une note par son auteur
        $DB_CX->DbQuery("SELECT DISTINCT age_id FROM ${PREFIX_TABLE}agenda WHERE (age_id=".$id." OR age_mere_id=".$id.") AND age_util_id=".$idUser);
        $liste = "0";
        while ($enr = $DB_CX->DbNextRow())
          $liste .= ",".$enr['age_id'];
        $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda WHERE age_id IN (".$liste.")");
        $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda_concerne WHERE aco_age_id IN (".$liste.")");
        $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}information WHERE info_age_id IN (".$liste.")");
      }
      elseif ($flag == 3) {
        //Suppression d'une occurrence d'une note par son auteur
        $DB_CX->DbQuery("SELECT MIN(age_id) FROM ${PREFIX_TABLE}agenda WHERE age_mere_id=".$id." AND age_util_id=".$idUser);
        $newId = $DB_CX->DbResult(0,0) + 0;
        if ($newId) {
          $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}agenda SET age_mere_id=".$newId." WHERE age_mere_id=".$id);
          $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}agenda SET age_mere_id=0 WHERE age_id=".$newId);
        }
        $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda WHERE age_id=".$id." AND age_util_id=".$idUser);
        if ($DB_CX->DbAffectedRows()>0) {
          $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda_concerne WHERE aco_age_id=".$id);
          $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}information WHERE info_age_id=".$id);
        }
      }
      //Renvoi vers l'affichage du detail de la journee
      $v=1;
    }
  }
// ----------------------------------------------------------------------------
// DETAIL D'UN JOUR
// ----------------------------------------------------------------------------
  if ($v==1) {
    //Preparation au decalage horaire
    list($age_date,$age_dateAvant,$age_heure_debut,$age_heure_fin) = prepareDecalageH($tzGmt,$tzEte,$tzHiver,mktime(0,0,0,$moisEnCours,$jourEnCours,$anneeEnCours));

    $DB_CX->DbQuery("SELECT age_id,age_aty_id,age_heure_debut,age_heure_fin,age_libelle,age_ape_id,age_util_id,age_detail,cal_id,CONCAT(cal_prenom,' ',cal_nom) AS nomContact,cal_util_id,cal_partage,age_date,age_date_creation,age_date_modif FROM ${PREFIX_TABLE}agenda LEFT JOIN ${PREFIX_TABLE}calepin ON cal_id=age_cal_id, ${PREFIX_TABLE}agenda_concerne WHERE age_id=aco_age_id AND aco_util_id=".$idUser." AND ($age_date='".date("Y-m-d",$sd)."' OR ($age_dateAvant='".date("Y-m-d",$sd)."' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin!=0 AND age_aty_id=2) OR (age_date LIKE '%".date("m-d",$sd)."' AND age_aty_id=1)) ORDER BY age_aty_id DESC, age_date, age_heure_debut ASC");
    $ligneAnniv = $ligneNote = "";
    for ($j=0;$j<$DB_CX->DbNumRows();$j++) {
      $enr = $DB_CX->DbNextRow();
      //Decalage des notes en fonction du fuseau horaire
      list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['dateCreation']) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,date("Y-m-d",$sd),$enr['age_date'],$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif']);
      //Stockage des infos relatives aux anniversaires
      if ($enr['age_aty_id']==1) {
        $ligneAnniv .= $enr['age_libelle']." / ";
      }
      //Stockage des infos relatives aux notes
      else {
        //Plage horaire de la note
        $plageNote = ($enr['age_aty_id']==2) ? afficheHeure(floor($enr['age_heure_debut']),$enr['age_heure_debut'],$formatHeure)."-".afficheHeure(floor($enr['age_heure_fin']),$enr['age_heure_fin'],$formatHeure) : trad("COMMUN_JOURNEE_ENTIERE");
        //Droit en modification et en suppression
        $droitModif=$droitSuppr="";
        if ($enr['age_ape_id']!=1 && $enr['age_util_id']==$idUser && ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR))
          $droitSuppr=" <a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&id=".$enr['age_id']."&v=4&flag=3\">".trad("TIMODE_BT_OCCURENCE")."</a>";

        if ($enr['age_util_id']==$idUser && ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR)) {
          $droitModif=" <a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&id=".$enr['age_id']."&v=2\">".trad("TIMODE_BT_MODIFIER")."</a>";
          $droitSuppr.=" <a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&id=".$enr['age_id']."&v=4&flag=1\">".trad("TIMODE_BT_SUPPRIMER")."</a>";
        } elseif ($AUTORISE_SUPPR && ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR)) {
          $droitSuppr.=" <a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&id=".$enr['age_id']."&v=4&flag=2\">".trad("TIMODE_BT_SUPPRIMER")."</a>";
        }
        $ligneNote .= "<font color=\"#4924FF\">".$plageNote."</font>".$droitModif.$droitSuppr."<br>".$enr['age_libelle'];
        //Info sur le contact associe
        if (($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR) && !empty($enr['cal_id'])) {
          $enr['age_detail'] = trad("TIMODE_LIB_CONTACT_ASSOCIE")."<b>".$lienContact."</b>".chr(13).$enr['age_detail'];
        }
        // Info sur le detail de la note
        $tabDetail = explode(chr(13),$enr['age_detail']);
        if (count($tabDetail) > 0) {
          $ligneNote .= "<font color=\"444444\">";
          for ($nb=0;$nb<count($tabDetail);$nb++) {
            $tabDetail[$nb]=trim($tabDetail[$nb]);
            if (!empty($tabDetail[$nb]))
              $ligneNote .= "<br>&nbsp; ".$tabDetail[$nb];
          }
          $ligneNote .= "</font>";
        }
        $ligneNote .= " <br><br> ";
      }
    }
    echo "<table width=\"100%\"><tr><td bgcolor=\"#6D92AA\" align=\"center\">".$tabJour[date("w",$sd)]." ".date("d/m/y",$sd)."</td></tr></table>";
    // Anniversaire(s) du calepin (y compris les contacts partages)
    $DB_CX->DbQuery("SELECT CONCAT(cal_prenom,' ',cal_nom) AS nomContact FROM ${PREFIX_TABLE}calepin WHERE (cal_util_id=".$idUser." OR cal_partage='O') AND cal_date_naissance LIKE '%".date("m-d",$sd)."'");
    while ($enr = $DB_CX->DbNextRow())
      $ligneAnniv .= $enr['nomContact']." / ";
    if (!empty($ligneAnniv))
      echo "<table width=\"100%\"><tr><td bgcolor=\"#B6DBFF\">&#59014;&nbsp; ".substr($ligneAnniv,0,strlen($ligneAnniv)-3)."</td></tr></table>";
    if (!empty($ligneNote))
      echo substr($ligneNote,0,strlen($ligneNote)-10);
    $navig = "";
    // Recuperation du jour precedent ayant une note
    $DB_CX->DbQuery("SELECT DATE_FORMAT(IF($age_dateAvant<'".date("Y-m-d",$sd)."' AND $age_heure_debut>$age_heure_fin AND $age_heure_fin!=0,$age_dateAvant,$age_date),'%e/%c/%Y') AS ageDate FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne WHERE age_id=aco_age_id AND aco_util_id=".$idUser." AND $age_date<'".date("Y-m-d",$sd)."' AND age_aty_id!=1 ORDER BY age_date DESC LIMIT 0,1");
    if ($DB_CX->DbNumRows()) {
      // Transformation de la date de debut de la note en timestamp PHP
      list($j,$m,$a) = explode("/",$DB_CX->DbResult(0,0));
      $tsNote = mktime(0,0,0,$m,$j,$a);
      $navig = "&#59106; <a href=\"".$NOM_PAGE."?sid=".$sid."&v=1&sd=".$tsNote."\" accesskey=\"1\">".trad("TIMODE_BT_PRECEDENT")."</a>";
    }
    //Recuperation du jour suivant ayant une note
    $DB_CX->DbQuery("SELECT DATE_FORMAT(IF($age_date='".date("Y-m-d",$sd)."' AND $age_heure_debut>$age_heure_fin AND $age_heure_fin!=0,$age_dateAvant,$age_date),'%e/%c/%Y') AS ageDate FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne WHERE age_id=aco_age_id AND aco_util_id=".$idUser." AND ($age_date>'".date("Y-m-d",$sd)."' OR ($age_date='".date("Y-m-d",$sd)."' AND $age_heure_debut>$age_heure_fin AND $age_heure_fin!=0)) AND age_aty_id!=1 ORDER BY age_date LIMIT 0,1");
    if ($DB_CX->DbNumRows()) {
      // Transformation de la date de debut de la note en timestamp PHP
      list($j,$m,$a) = explode("/",$DB_CX->DbResult(0,0));
      $tsNote = mktime(0,0,0,$m,$j,$a);
      if (!empty($navig))
        $navig .= " - ";
      $navig .= "<a href=\"".$NOM_PAGE."?sid=".$sid."&v=1&sd=".$tsNote."\" accesskey=\"2\">".trad("TIMODE_BT_SUIVANT")."</a> &#59107;";
    }
    if (!empty($navig))
      $navig .= "<br>";
    if ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR) {
      echo "  <hr size=\"1\"><center>".$navig."&#59109; <a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."\" accesskey=\"4\">".trad("TIMODE_MENU_AGENDA")."</a> - <a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&v=2\" accesskey=\"5\">".trad("TIMODE_MENU_NOTE")."</a> &#59110;</center>";
    } else {
      echo "  <hr size=\"1\"><center>".$navig."&#59109; <a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."\" accesskey=\"4\">".trad("TIMODE_MENU_AGENDA")."</a>";
    }
  }
// ----------------------------------------------------------------------------
// MODULE CALENDRIER
// ----------------------------------------------------------------------------
  elseif (!$v) {
    // Affichage d'une case du calendrier
    function afficheJour($nbJour, $leJour) {
      global $NOM_PAGE, $sid, $tabOccupe, $moisEnCours, $anneeEnCours;
      global $tzGmt,$tzEte,$tzHiver,$localTime;
      $numJour = ($tabOccupe[$nbJour]==1) ? "<a href=\"".$NOM_PAGE."?sid=".$sid."&v=1&sd=".mktime(0,0,0,intval($moisEnCours),$nbJour,$anneeEnCours)."\"><font color=\"#800000\">".$nbJour."</font></a>" : "<font color=\"#4924FF\">".$nbJour."</font>";
      echo "          <td".(($leJour==date("Y-m-d",$localTime))?" bgcolor=\"#99CCCC\"":"").">".$numJour."</td>\n";
    }
    // Menu de navigation entre les differents mois
    $moisAvant  = $NOM_PAGE."?sid=".$sid."&sd=".(($moisEnCours != 1) ? mktime(0,0,0,($moisEnCours-1),1,$anneeEnCours) : mktime(0,0,0,12,1,($anneeEnCours-1)));
    $moisApres  = $NOM_PAGE."?sid=".$sid."&sd=".(($moisEnCours != 12) ? mktime(0,0,0,($moisEnCours+1),1,$anneeEnCours) : mktime(0,0,0,1,1,($anneeEnCours+1)));
    $anneeAvant = $NOM_PAGE."?sid=".$sid."&sd=".mktime(0,0,0,$moisEnCours,1,($anneeEnCours-1));
    $anneeApres = $NOM_PAGE."?sid=".$sid."&sd=".mktime(0,0,0,$moisEnCours,1,($anneeEnCours+1));
    echo ("  <table width=\"119\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
    <tr>
      <td><table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" border=\"0\">
        <tr bgcolor=\"#6D92AA\">
          <td align=\"right\" width=\"18\" nowrap><a href=\"".$anneeAvant."\"><img src=\"image/timode/anneeprec.gif\" alt=\"".trad("TIMODE_ANNEE_PREC")."\" width=\"8\" height=\"10\" border=\"0\"></a><a href=\"".$moisAvant."\"><img src=\"image/timode/moisprec.gif\" alt=\"".trad("TIMODE_MOIS_PREC")."\" width=\"9\" height=\"10\" border=\"0\"></a></td>
          <td align=\"center\" width=\"100%\" nowrap>".$tabMois2[$moisEnCours*1]." ".$anneeEnCours."</td>
          <td align=\"left\" width=\"18\" nowrap><a href=\"".$moisApres."\"><img src=\"image/timode/moissuiv.gif\" alt=\"".trad("TIMODE_MOIS_SUIV")."\" width=\"9\" height=\"10\" border=\"0\"></a><a href=\"".$anneeApres."\"><img src=\"image/timode/anneesuiv.gif\" alt=\"".trad("TIMODE_ANNEE_SUIV")."\" width=\"8\" height=\"10\" border=\"0\"></a></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#E1EDF7\">
        <tr align=\"center\" bgcolor=\"#B6DBFF\">\n");

    // Ligne des jours de la semaine
    for ($i=0; $i<7; $i++)
      echo "          <td width=\"17\">".$tabJour2[$i]."</td>\n";

    echo ("        </tr>
        <tr align=\"center\">\n");

    // Affichage des jours du mois precedent
    $premierJour = date("w",mktime(0,0,0,$moisEnCours, 1, $anneeEnCours));
    if ($premierJour == 0)
      $premierJour = 7;
    for($i=1;$i<$premierJour;$i++) {
      echo "          <td>&nbsp;</td>\n";
    }

    //Preparation au decalage horaire
    list($age_date,$age_dateAvant,$age_heure_debut,$age_heure_fin) = prepareDecalageH($tzGmt,$tzEte,$tzHiver,mktime(0,0,0,$moisEnCours,$jourEnCours,$anneeEnCours));

    // Recherche des jours du mois courant avec une note ou un anniversaire (agenda et calepin)
    $tabOccupe = array();
    $DB_CX->DbQuery("SELECT DISTINCT DATE_FORMAT(IF(age_aty_id=1,age_date,$age_date),'%e') AS jour FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne WHERE age_id=aco_age_id AND aco_util_id=".$idUser." AND ($age_date LIKE '".$anneeEnCours."-".$moisEnCours."-%' OR (age_date LIKE '%-".$moisEnCours."-%' AND age_aty_id=1))");
    while ($enr=$DB_CX->DbNextRow()) {
      $tabOccupe[$enr['jour']]=1;
    }
    // Recherche des notes a cheval
    $DB_CX->DbQuery("SELECT DISTINCT DATE_FORMAT($age_dateAvant,'%e') AS jour FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne WHERE age_id=aco_age_id AND aco_util_id=".$idUser." AND ($age_dateAvant LIKE '".$anneeEnCours."-".$moisEnCours."-%' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin!=0 AND age_aty_id=2)");
    while ($enr=$DB_CX->DbNextRow()) {
      $tabOccupe[$enr['jour']]=1;
    }
    $DB_CX->DbQuery("SELECT DISTINCT DATE_FORMAT(cal_date_naissance,'%e') AS jour FROM ${PREFIX_TABLE}calepin WHERE (cal_util_id=".$idUser." OR cal_partage='O') AND cal_date_naissance LIKE '%-".$moisEnCours."-%'");
    while ($enr=$DB_CX->DbNextRow()) {
      $tabOccupe[$enr['jour']]=1;
    }

    // Affichage de la premiere ligne des jours du mois courant
    $nbJour = 0;
    for($i=$premierJour;$i<8;$i++) {
      $leJour = (++$nbJour < 10) ? $anneeEnCours."-".$moisEnCours."-0".$nbJour : $anneeEnCours."-".$moisEnCours."-".$nbJour;
      afficheJour($nbJour, $leJour);
    }

    echo "        </tr>\n";

    // Affichage du reste du mois courant
    $cpt=1;
    $finDeMois = false;
    for($j=1;!$finDeMois;$j++) {
      if (checkdate($moisEnCours, $nbJour+1, $anneeEnCours)) {
        echo "        <tr align=\"center\">\n";
        for($i=1;$i<8;$i++) {
          if (checkdate($moisEnCours, ++$nbJour, $anneeEnCours)) {
            $leJour = ($nbJour < 10) ? $anneeEnCours."-".$moisEnCours."-0".$nbJour : $anneeEnCours."-".$moisEnCours."-".$nbJour;
            afficheJour($nbJour, $leJour);
          }
          else {
            $finDeMois = true;
            echo "          <td>&nbsp;</td>\n";
          }
        }
        echo "        </tr>\n";
      }
      else {
        $finDeMois = true;
      }
    }

    echo ("      </table></td>
    </tr>
  </table>\n");

    // Note du jour
    if ($tabOccupe[date("j",$sd)]!=1)
      echo "  <center><font color=\"green\">".trad("TIMODE_PAS_DE_NOTE")."</font></center>\n";

    // Lien vers le calepin et vers la creation d'une note
    if ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR){
      echo "  <hr size=\"1\"><center>&#59110; <a href=\"".$NOM_PAGE."?sid=".$sid."&v=2\" accesskey=\"5\">".trad("TIMODE_MENU_NOTE")."</a> - <a href=\"".$NOM_PAGE."?sid=".$sid."&v=5\" accesskey=\"6\">".trad("TIMODE_MENU_CALEPIN")."</a> &#59111;</center>\n";
    } else {
      echo "  \n";
    }
  }

  // Fermeture BDD
  $DB_CX->DbDeconnect();

// ----------------------------------------------------------------------------
// PIED DE PAGE HTML COMMUN
// ----------------------------------------------------------------------------
  echo ("  <hr size=\"1\"><center><a href=\"mailto:phenix-agenda@laposte.net\"><font color=\"#FF9200\">".sprintf(trad("TIMODE_VERSION_PHENIX"), $APPLI_VERSION)."</font></a></center>
</body>
</html>");
?>
