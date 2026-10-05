<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_fcke_aff_outils.txt ?>
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
// Initialisation du style par defaut de l'application
@session_start();
if (!isset($APPLI_STYLE ))
  $APPLI_STYLE = "Petrole";
// ----------------------------------------------------------------------------
// GENERATION D'UN IDENTIFIANT DE SESSION
// ----------------------------------------------------------------------------
function SessionId($longueur, $idUser, $weekType, $hdScreen, $fromPPX) {
  global $DB_CX, $PREFIX_TABLE;

  $Pool = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
  $ok = false;

  // Generation d'un identifiant unique
  while (!$ok) {
    $sid = "";
    for ($index = 0; $index < $longueur; $index++)
      $sid .= substr($Pool, (mt_rand()%(strlen($Pool))), 1);
    $DB_CX->DbQuery("SELECT sid_util_id FROM ${PREFIX_TABLE}sid WHERE sid_id='".$sid."'");
    $ok = ($DB_CX->DbNumRows() == 0);
  }

  // Enregistrement de la session
  $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}sid (sid_id, sid_util_id, sid_admin_id, sid_last_maj, sid_session_id, sid_util_subst_id, sid_semaine_type, sid_filtre_couleur, sid_screen) VALUES ('".$sid."',".$idUser.",0,'".date("Y-m-d H:i:s", time())."','".@session_id()."',".$idUser.",'".$weekType."','ALL','".$hdScreen."')");

  // Transmission de l'identifiant genere
  return ($sid);
}
// ----------------------------------------------------------------------------


// ----------------------------------------------------------------------------
// VERIFICATION DE L'IDENTIFICATION
// ----------------------------------------------------------------------------
function Session_ok($idSession, $fromPPX = false) {
  global $DB_CX, $PREFIX_TABLE, $APPLI_STYLE, $DUREE_SESSION, $TELEPHONE_VF, $SEMAINE_TYPE, $tcMenu, $USER_SUBSTITUE, $FILTRE_COULEUR, $FORMAT_NOM_UTIL, $FORMAT_NOM_CONTACT, $MENU_DISPO, $NOTE_BARREE, $APPLI_LANGUE, $NOM_USER, $MENU_ONCLICK;
  global $idAdmin, $droit_PROFILS, $droit_AGENDAS, $droit_NOTES, $droit_ADMIN, $LANGUE_CFG, $AUTORISE_FCKE_CFG, $AUTORISE_FCKE, $FCKE_TOOLBAR_CFG, $FCKE_TOOLBAR, $hdScreen;

  // Mod fcke_aff_toolbar  
  global $FCKE_AFF_TOOLBAR;
  // Mod fcke_aff_toolbar  
  // On supprime les sessions de plus de 1 heure
  if (!$fromPPX) {
    $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}sid WHERE sid_last_maj < '".date("Y-m-d H:i:s", (time() - $DUREE_SESSION))."'");
  }
  // Retrait temporaire des droits d'administration
  $idAdmin = 0;

  // On recherche les sessions encore valides
  $DB_CX->DbQuery("SELECT util_id, util_interface, util_telephone_vf, util_planning, sid_util_subst_id, sid_semaine_type, sid_filtre_couleur, sid_screen, admin_id, util_format_nom, util_menu_dispo, util_note_barree, util_menuonclick, util_langue, CONCAT(util_nom,' ',util_prenom) AS nomUtil, util_fcke, util_fcke_toolbar, util_fcke_aff_toolbar, droit_profils, droit_agendas, droit_notes, droit_admin FROM ${PREFIX_TABLE}sid LEFT JOIN ${PREFIX_TABLE}admin ON admin_id=sid_admin_id, ${PREFIX_TABLE}utilisateur, ${PREFIX_TABLE}droit WHERE sid_id='".$idSession."' AND sid_session_id='".@session_id()."' AND sid_util_id = util_id AND droit_util_id=util_id");

  if ($DB_CX->DbNumRows() && $enr = $DB_CX->DbNextRow()) {
    // Recuperation des parametres de l'utilisateur connecte
    $idUser = $enr['util_id'];
    $idAdmin = $enr['admin_id'];
    if (file_exists("skins/".$enr['util_interface'].".php")) {
      $APPLI_STYLE = $enr['util_interface'];
    }
    $TELEPHONE_VF = ($enr['util_telephone_vf']=="O") ? true : false;
    $tcMenu = ($tcMenu=="") ? $enr['util_planning'] : $tcMenu;
    $USER_SUBSTITUE = $enr['sid_util_subst_id'];
    $SEMAINE_TYPE = $enr['sid_semaine_type'];
    $FILTRE_COULEUR = $enr['sid_filtre_couleur'];
    $FORMAT_NOM_UTIL = ($enr['util_format_nom']=="0") ? "util_nom, ' ', util_prenom" : "util_prenom, ' ', util_nom";
    $FORMAT_NOM_CONTACT = ($enr['util_format_nom']=="0") ? "cal_nom, ' ', cal_prenom" : "cal_prenom, ' ', cal_nom";
    $MENU_DISPO = $enr['util_menu_dispo'];
    $NOTE_BARREE = ($enr['util_note_barree']=="O") ? true : false;
    $MENU_ONCLICK = ($enr['util_menuonclick']=="O") ? true : false;
    $LANGUE_CFG = $APPLI_LANGUE;
    $APPLI_LANGUE = $enr['util_langue'];
    $NOM_USER = $enr['nomUtil'];
    $AUTORISE_FCKE_CFG = $AUTORISE_FCKE;
    $FCKE_TOOLBAR_CFG = $FCKE_TOOLBAR;
    if ($AUTORISE_FCKE==true) {
      $AUTORISE_FCKE = ($enr['util_fcke']=="O") ? true : false;
      if ($FCKE_TOOLBAR=="User") {
        $FCKE_TOOLBAR = $enr['util_fcke_toolbar'];
      }
    }
  // Mod fcke_aff_toolbar  
    $FCKE_AFF_TOOLBAR = ($enr['util_fcke_aff_toolbar']=="O") ? true : false;
  // Mod fcke_aff_toolbar  
    $droit_PROFILS = $enr['droit_profils'];
    $droit_AGENDAS = $enr['droit_agendas'];
    $droit_NOTES = $enr['droit_notes'];
    $droit_ADMIN = $enr['droit_admin'];
    $hdScreen = $enr['sid_screen'];
    // Controle de la validite de l'identifiant d'administrateur recupere
    if ($idAdmin) {
      $DB_CX->DbQuery("SELECT admin_id FROM ${PREFIX_TABLE}admin, ${PREFIX_TABLE}sid, ${PREFIX_TABLE}droit WHERE droit_util_id=$idUser AND droit_admin='O' AND sid_util_id=droit_util_id AND admin_id=sid_admin_id AND admin_id=".$idAdmin);
      if (!$DB_CX->DbNumRows()) {
        $idAdmin = 0;
        $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}sid SET sid_admin_id=0 WHERE sid_id='".$idSession."'");
      } elseif (!$fromPPX) {
        // Attribution des droits d'administration si applicable
        $droit_PROFILS = _DROIT_PROFIL_COMPLET;
        $droit_AGENDAS = _DROIT_AGENDA_TOUS;
        $droit_NOTES = _DROIT_NOTE_COMPLET;
      }
    }

    // Repositionnement des menus en fonction des droits
    if ($tcMenu==_MENU_PLG_QUOT_GBL && $droit_AGENDAS < _DROIT_AGENDA_PARTAGE) $tcMenu=_MENU_PLG_QUOT;
    if ($tcMenu==_MENU_PLG_HEBDO_GBL && $droit_AGENDAS < _DROIT_AGENDA_PARTAGE) $tcMenu=_MENU_PLG_HEBDO;
    if ($tcMenu==_MENU_PLG_MENS_GBL && $droit_AGENDAS < _DROIT_AGENDA_PARTAGE) $tcMenu=_MENU_PLG_MENSUEL;

    if (!$fromPPX) {
      // Verification de la substitution
      if ($droit_AGENDAS < _DROIT_AGENDA_TOUS) {
        $sql = "SELECT util_id FROM ${PREFIX_TABLE}utilisateur LEFT JOIN ${PREFIX_TABLE}planning_partage ON ppl_util_id=util_id WHERE (util_id=".$USER_SUBSTITUE." AND util_partage_planning='1') OR (util_id=".$USER_SUBSTITUE." AND util_partage_planning='2' AND ppl_consultant_id=".$idUser.")";
      } else {
        $sql = "SELECT util_id FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$USER_SUBSTITUE;
      }
      $DB_CX->DbQuery($sql);
      if ((!$DB_CX->DbNumRows())) {
        $USER_SUBSTITUE = $idUser;
        $majSubst = ", sid_util_subst_id=".$idUser;
      }
    }
    // Bail ok, on le renouvelle
    $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}sid SET sid_last_maj='".date("Y-m-d H:i:s", time())."'".$majSubst." WHERE sid_id='".$idSession."'");
  } else {
    (!$fromPPX) ? Header("location: deconnexion.php?msg=5") : formLog();
    exit;
  }

  return ($idUser);
}
// ----------------------------------------------------------------------------


// ----------------------------------------------------------------------------
// Retourne la liste des jours feries d'une annee passee en parametre
// Pour personnaliser cette liste ajouter ses jours dans le tableau de la ligne return...
// Respecter le format (j-m) pour les valeurs inferieures a 10 ( jour SANS zero devant - mois AVEC zero devant )
function getListeJourFerie($annee) {
  $Paques = $Ascension = $Pentecote = "";
  if (function_exists("easter_date")) {
    $jour = 3600*24;    //un jour en secondes
    $Paques = date("j-m", easter_date($annee)+$jour);
    $Ascension = date("j-m", easter_date($annee)+39*$jour);
    //if ($annee<2004)
    $Pentecote = date("j-m", easter_date($annee)+50*$jour);
  }
  return array($Paques,$Ascension,$Pentecote,"1-01","1-05","8-05","14-07","15-08","1-11","11-11","25-12");
}
// ----------------------------------------------------------------------------


//-------------------------------------------
function calcJour($place, $jour, $mois, $annee) {
  if ($place < 4) {
    //Premier a Quatrieme
    $debScan = 1+$place*7;
    $finScan = 8+$place*7;
    for ($i=$debScan;$i<$finScan;$i++)
      if ($jour == date("w",mktime(12,0,0,$mois,$i,$annee))) return $i;
  } else {
    //Dernier
    $debScan = date("t",mktime(12,0,0,$mois,1,$annee));
    $finScan = $debScan - 7;
    for ($i=$debScan;$i>$finScan;$i--)
      if ($jour == date("w",mktime(12,0,0,$mois,$i,$annee))) return $i;
  }
}
// ----------------------------------------------------------------------------


// ----------------------------------------------------------------------------
function signatureMail() {
  global $APPLI_VERSION;
  $signature = "\n\n<CENTER><HR size=\"1\" color=\"#000000\">";
  $signature .= "<FONT color=\"gray\" size=\"-1\">".sprintf(trad("FCT_MAIL_AUTO"),$APPLI_VERSION)."</FONT></CENTER>\n</BODY></HTML>\n";
  return $signature;
}
// ----------------------------------------------------------------------------


// ----------------------------------------------------------------------------
function envoiMail($nomEmetteur,$mailEmetteur,$destMail,$sujetMail,$corpsMail) {
  global $SMTP_SERVER, $SMTP_PORT, $SMTP_LOGIN, $SMTP_PASSWORD;
  global $classMailerLoaded,$classSMTPLoaded, $mailer;
  $envoiOK = false;
  $sujetMail = sprintf(trad("FCT_SUJET_MAIL"),$sujetMail);
  if (!$classMailerLoaded) {
    $mailer = new Mailer();
    $classMailerLoaded = true;
    if (!empty($SMTP_SERVER)) {
      $mailer->use_smtp($SMTP_SERVER, $SMTP_PORT, $SMTP_LOGIN, $SMTP_PASSWORD);
      $classSMTPLoaded = true;
    }
  } else {
    $mailer->clear_all();
  }
  $mailer->set_from($mailEmetteur, $nomEmetteur);
  for ($i=0;$i<count($destMail);$i++) {
    $mailer->set_address($destMail[$i]);
  }
  $mailer->set_format('html');
  $mailer->set_subject($sujetMail);
  $mailer->set_message($corpsMail);
  $envoiOK = $mailer->send();
  return $envoiOK;
}
// ----------------------------------------------------------------------------


// ----------------------------------------------------------------------------
function prefixeMot($premiereLettre, $prefixeVoyelle="d'", $prefixeConsonne="de ") {
  $tabVoyelle = array("a","e","i","o","u","y");
  $ok = false;
  for ($i=0;$i<count($tabVoyelle) && !$ok;$i++) {
    $ok = ($tabVoyelle[$i] == $premiereLettre);
  }
  return ($ok) ? $prefixeVoyelle : $prefixeConsonne;
}
// ----------------------------------------------------------------------------


// ----------------------------------------------------------------------------
// Permet de trouver le prochain indice de colonne ou la tranche horaire change
// de statut (occupe ou libre)
// Reinitialise dans le meme temps le tableau des tranches horaires
function colSpan($hDeb,$flag,$nbMax,$inHebdo) {
  global $aJournee, $iDureeJournee;
  for ($i=$hDeb;$i<$iDureeJournee;$i++) {
    if ($inHebdo) { // Dans le module hebdo on tient compte du nombre d'utilisateurs occupes
      if ((($flag==$nbMax || $flag==0) && $aJournee[$i][0]!=$flag) || (($flag>0 && $flag<$nbMax) && ($aJournee[$i][0]==0 || $aJournee[$i][0]==$nbMax)))
        return $i;
    } elseif ($aJournee[$i][0]!=$flag)
      return $i;
    $aJournee[$i][0]="0";
  }
  return $i;
}
// ----------------------------------------------------------------------------


// ----------------------------------------------------------------------------
  function calculAge($tabDate,$jourCrt) {
    $age = date("Y",$jourCrt)-$tabDate[0];
    if (date("md",$jourCrt)<($tabDate[1].$tabDate[2]))
      $age--;
    return $age;
  }
// ----------------------------------------------------------------------------


// ----------------------------------------------------------------------------
  function afficheAge($date,$jourCrt,$separateur="-") {
    $tabDate = explode($separateur,$date);
    $age = calculAge($tabDate,$jourCrt);
    $pluriel = ($age>1) ? trad("COMMUN_PLURIEL") : "";
    $text = ($age>0) ? sprintf(trad("COMMUN_AGE"),$age,$pluriel,$tabDate[0],$tabDate[1],$tabDate[2]) : trad("COMMUN_JOUR_NAISSANCE");
    return infoPopup($text);
  }
// ----------------------------------------------------------------------------


// ----------------------------------------------------------------------------
// Formate un numero de telephone pour l'affichage xx.xx.xx.xx.xx
  function telephoneVF($str) {
    global $TELEPHONE_VF;
    if ($TELEPHONE_VF) {
      $str = preg_replace( "/[^0-9+]+/","",stripslashes($str));
      for ($i=0; $i < strlen($str); $i+=2) {
        $temp .= (($i != 0) ? "." : "").@substr($str,$i,2);
      }
      $str = $temp;
    }
    return $str;
  }
// ----------------------------------------------------------------------------


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
      if ($hfin == 0) $hfin = 24;
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


// ----------------------------------------------------------------------------
// RECUPERATION DES COULEURS DE NOTE PERSONNALISEES EN BASE
// ----------------------------------------------------------------------------
function getListeCouleur() {
  global $DB_CX, $PREFIX_TABLE, $USER_SUBSTITUE;
  $DB_CX->DbQuery("SELECT * FROM ${PREFIX_TABLE}couleurs WHERE cou_util_id=0 OR cou_util_id=".$USER_SUBSTITUE." ORDER BY cou_libelle");
  $tabCouleur = Array();
  while($enr=$DB_CX->DbNextRow()) {
    $tabCouleur[$enr['cou_libelle']] = $enr['cou_couleur'];
  }
  return $tabCouleur;
}
// ----------------------------------------------------------------------------


// ----------------------------------------------------------------------------
// TENTATIVE D'INSERTION D'UN NOUVEAU PARAMETRE DE CONFIGURATION SINON MAJ
// ----------------------------------------------------------------------------
function insertOrUpdate($param,$valeur,$groupe) {
  global $DB_CX, $PREFIX_TABLE;
  $groupe += 0;
  if (!$DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}configuration VALUES ('$param', '$valeur', $groupe);")) {
    $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}configuration SET valeur='$valeur' WHERE param='$param' AND groupe=$groupe");
  }
}
// ----------------------------------------------------------------------------


// ----------------------------------------------------------------------------
// ATTRIBUTION DES DROITS SUR LES DIFFERENTES ACTIONS POSSIBLES SUR UNE NOTE
// ----------------------------------------------------------------------------
function attributDroits($enr, &$droitModifStatut, &$droitModifNotePerso, &$droitModifNoteAffectee, &$droitSuppOcc, &$droitSuppNoteCreee, &$droitSuppNoteAffectee, &$droitApprNote, $USER_SUBSTITUE, $AFFECTE_NOTE) {
  global $idUser, $droit_NOTES, $droit_AGENDAS, $AUTORISE_SUPPR;
  // Droit en modification du statut de la note
  // Conditions : etre sur son planning ET droitNotes>=_DROIT_NOTE_STANDARD_SANS_APPR
  //              OU
  //              pouvoir modifier le planning consulte ET etre le proprietaire de la note ET droitNOTES>=_DROIT_NOTE_STANDARD_SANS_APPR
  //              OU
  //              droitNOTES>=_DROIT_NOTE_MODIF_STATUT
  $droitModifStatut = (($USER_SUBSTITUE==$idUser and $droit_NOTES>=_DROIT_NOTE_STANDARD_SANS_APPR) or ($AFFECTE_NOTE && $enr['age_util_id']==$idUser and $droit_NOTES>=_DROIT_NOTE_STANDARD_SANS_APPR) or ($droit_NOTES >= _DROIT_NOTE_MODIF_STATUT));

  // Droit en modification sur une note personnelle
  // Conditions : etre sur son planning ET etre le proprietaire de la note ET droitNotes>=_DROIT_NOTE_STANDARD_SANS_APPR
  $droitModifNotePerso = ($USER_SUBSTITUE==$idUser and $enr['age_util_id']==$idUser and $droit_NOTES>=_DROIT_NOTE_STANDARD_SANS_APPR);

  // Droit en modification sur une note d'un planning consulte
  // Conditions : pouvoir modifier le planning consulte ET etre le proprietaire de la note ET droitNOTES>=_DROIT_NOTE_STANDARD_SANS_APPR
  //              OU
  //              droitNOTES>=_DROIT_NOTE_MODIF_CREATION
  $droitModifNoteAffectee = (($AFFECTE_NOTE && $enr['age_util_id']==$idUser && $droit_NOTES>=_DROIT_NOTE_STANDARD_SANS_APPR) or ($droit_NOTES >= _DROIT_NOTE_MODIF_CREATION and ($droit_AGENDAS >= _DROIT_AGENDA_TOUS or $AFFECTE_NOTE)));

  // Droit en suppression de l'occurrence
  // Conditions : etre sur son planning ET etre le proprietaire de la note ET droitNotes>=_DROIT_NOTE_STANDARD_SANS_APPR
  //              OU
  //              pouvoir modifier le planning consulte ET etre le proprietaire de la note ET droitNOTES>=_DROIT_NOTE_STANDARD_SANS_APPR
  //              OU
  //              droitNOTES>=_DROIT_NOTE_COMPLET
  $droitSuppOcc = (($USER_SUBSTITUE==$idUser and $enr['age_util_id']==$idUser and $droit_NOTES>=_DROIT_NOTE_STANDARD_SANS_APPR) or ($AFFECTE_NOTE && $enr['age_util_id']==$idUser && $droit_NOTES>=_DROIT_NOTE_STANDARD_SANS_APPR) or ($droit_NOTES >= _DROIT_NOTE_COMPLET and ($droit_AGENDAS >= _DROIT_AGENDA_TOUS or $AFFECTE_NOTE)));

  // Droit en suppression d'une note creee
  // Conditions : etre sur son planning ET etre le proprietaire de la note ET droitNotes>=_DROIT_NOTE_STANDARD_SANS_APPR
  //              OU
  //              pouvoir modifier le planning consulte ET etre le proprietaire de la note ET droitNOTES>=_DROIT_NOTE_STANDARD_SANS_APPR
  //              OU
  //              droitNOTES>=_DROIT_NOTE_COMPLET
  $droitSuppNoteCreee = (($USER_SUBSTITUE==$idUser and $enr['age_util_id']==$idUser and $droit_NOTES>=_DROIT_NOTE_STANDARD_SANS_APPR) or ($AFFECTE_NOTE && $enr['age_util_id']==$idUser && $droit_NOTES>=_DROIT_NOTE_STANDARD_SANS_APPR));

  // Droit en suppression d'une note affectee
  // Conditions : etre sur son planning ET parametre $AUTORISE_SUPPR à vrai ET droitNotes>_DROIT_NOTE_STANDARD_SANS_APPR
  $droitSuppNoteAffectee = (($USER_SUBSTITUE==$idUser and $AUTORISE_SUPPR and $droit_NOTES>_DROIT_NOTE_STANDARD_SANS_APPR) or ($droit_NOTES >= _DROIT_NOTE_COMPLET and ($droit_AGENDAS >= _DROIT_AGENDA_TOUS or $AFFECTE_NOTE)));

  // Droit en appropriation d'une note affectee
  // Conditions : etre sur son planning ET etre le seul destinataire de la note ET ne pas etre le proprietaire de la note ET droitNotes>=_DROIT_NOTE_STANDARD ET ne pas avoir un droit en modification sur toutes les notes
  $droitApprNote = ($USER_SUBSTITUE==$idUser and $enr['age_nb_participant']==1 and $enr['age_util_id']!=$idUser and $droit_NOTES>=_DROIT_NOTE_STANDARD and $droit_NOTES < _DROIT_NOTE_MODIF_CREATION);
}
// ----------------------------------------------------------------------------


// ----------------------------------------------------------------------------
// INSERTION DES OCCURENCES DES NOTES
// ----------------------------------------------------------------------------
function insertOccurrence() {
  global $tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$tsNote,$tsNoteUTC,$tsNow,$tsAlert,$zlR1,$zlHeureDebut,$zlHeureFin,$sql,$idParticipant,$DB_CX,$PREFIX_TABLE;
  // Conversion en utc en fonction du timezone
  $dateNote = date("Y-m-d",$tsNote);
  list($zlHeureDebutUTC,$zlHeureFinUTC,$dateCrtUTC,$dateModifUTC,$dateNoteUTC) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$dateJour,$dateNote,$zlHeureDebut,$zlHeureFin,$dateCrt,$dateModif,1,1);

  $endNote = ($tsNoteUTC > $tsNow) ? 0 : 1;
  $alert = ($tsNoteUTC > $tsAlert && $zlR1) ? 0 : 1;
  $DB_CX->DbQuery(str_replace(array("{theNewDate}","{theBeginHour}","{theEndHour}"),array($dateNoteUTC,$zlHeureDebutUTC,$zlHeureFinUTC),$sql));
  // Enregistrement des personnes concernees
  $ageID = $DB_CX->DbInsertID();
  for ($nb=0;$nb < count($idParticipant);$nb++)
    $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}agenda_concerne VALUES (".$ageID.",".$idParticipant[$nb].",".$alert.",".$endNote.")");
}
// ----------------------------------------------------------------------------


// ----------------------------------------------------------------------------
function get_moment() {
  $temps1 = explode( " ", microtime() );
  $temps2 = explode( ".", $temps1[0] );
  $temps2 = $temps1[1].".".$temps2[1];
  return $temps2;
}
// ----------------------------------------------------------------------------
function get_elapsed_time( $start, $end ) {
  return number_format( ( $end - $start ) * 1, 3, '.', ' ');
}
// ----------------------------------------------------------------------------

// ----------------------------------------------------------------------------
//Recuperation des donnees de formulaires et d'URL
// ----------------------------------------------------------------------------
function get_all_variables($array, &$target) {
  if (!is_array($array)) {
    return false;
  }
  $is_magic_quotes = get_magic_quotes_gpc();
  reset($array);
  while (list($key, $val) = each($array)) {
    if (is_array($val)) {
      pxExtract($val, $target[$key]);
    } else if (!$is_magic_quotes) {
      $target[$key] = addslashes(trim(stripTags($val)));
    } else {
      $target[$key] = trim(stripTags($val));
    }
  }
  reset($array);
  return true;
}
function display_variables() {
  global $bgColor, $AgendaBordureTableau;
  if (!empty($_POST) || !empty($_GET)) {
    $strout = "      <BR><TABLE align=\"center\" cellspacing=\"1\" bgcolor=\"".$AgendaBordureTableau."\" style=\"border-collapse:separate;\">";
    if (!empty($_GET)) {
      $strout .= "<TR bgcolor=\"".$bgColor[0]."\"><TH colspan=\"2\">Valeurs du GET</TH></TR>";
      $DEBUGTAB = array();
      get_all_variables($_GET, $DEBUGTAB);
      while (list($key, $val) = each($DEBUGTAB)) {
        $strout .= "<TR bgcolor=\"".$bgColor[1]."\"><TD><B>$key</B></TD><TD>$val</TD></TR>";
      }
    }
    if (!empty($_POST)) {
      $strout .= "<TR bgcolor=\"".$bgColor[0]."\"><TH colspan=\"2\">Valeurs du POST</TH></TR>";
      $DEBUGTAB = array();
      get_all_variables($_POST, $DEBUGTAB);
      while (list($key, $val) = each($DEBUGTAB)) {
        $strout .= "<TR bgcolor=\"".$bgColor[1]."\"><TD><B>$key</B></TD><TD>$val</TD></TR>";
      }
    }
    $strout .= "</TABLE>\n";
    echo $strout;
  }
}
// ----------------------------------------------------------------------------
?>
