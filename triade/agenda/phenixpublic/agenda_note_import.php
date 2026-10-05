<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5_import_ch_coul.txt ?>
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
  ?> <SCRIPT> HelpPhenixCtx="{F868D6BB-FC1B-4AF6-B027-B6B3893C4F35}.htm"; </SCRIPT> <?php
  // Mod Aide

  // Extraction depuis une requete de publication iCal
  if (!empty($id)) {
    // On definie les bornes d'import
    $_POST['ztDateDeb'] = date("d")."/".date("m")."/".(date("Y")-5);
    $_POST['ztDateFin'] = date("d")."/".date("m")."/".(date("Y")+5);
    // On definie le nom du fichier a partir de l'ID
    $fileName = $id.".ics";
    // On recupere les evenements postes
    $data = "";
    if ($flux = fopen('php://input','r')) {
      while (!@feof($flux)) {
        $data .= fgets($flux,4096);
      }
      @fclose($flux);
    }
    // On recupere l'identifiant utilisateur a partir de l'ID passe en parametre dans l'URL
    $DB_CX->DbQuery("SELECT util_id FROM ${PREFIX_TABLE}utilisateur WHERE util_url_export='".$id."'");
    $idUser = $DB_CX->DbResult(0,0) + 0;
    // On lance l'import et on envoi l'entete en fonction du resultat
    if (!empty($idUser) && !empty($data)) {
      // Recuperation des infos de timezone de l'utilisateur
      $DB_CX->DbQuery("SELECT tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver, tzn_zone FROM ${PREFIX_TABLE}utilisateur, ${PREFIX_TABLE}timezone WHERE util_id=".$idUser." AND tzn_zone=util_timezone");
      $tzGmt = $DB_CX->DbResult(0,"tzn_gmt");
      $tzDateEte = $DB_CX->DbResult(0,"tzn_date_ete");
      $tzHeureEte = $DB_CX->DbResult(0,"tzn_heure_ete");
      $tzDateHiver = $DB_CX->DbResult(0,"tzn_date_hiver");
      $tzHeureHiver = $DB_CX->DbResult(0,"tzn_heure_hiver");
      if ($ImportSunbird=="OK") $ImportSunbird=true; else $ImportSunbird=false;
      // Lancement de l'import
      importICS($fileName,$err,$data);
    }
    if (empty($err))
      header('HTTP/1.1 200 OK');
    else
      header('HTTP/1.1 400 Bad Request');
    exit;
  }

  // Extraction depuis le fichier d'import iCal
  function importICS($fileName,&$err,$data="") {
    global $DB_CX, $PREFIX_TABLE, $idUser,$ImportSunbird;
    global $zlPeriodicite,$zlCouleur,$rdA,$zlA1,$zlA2,$zlA3,$zlA4,$zlA5,$rdPlage,$ztP,$zlP1,$zlP2,$zlP3,$rdQ,$ztQ,$zlH,$ztH,$rdM,$zlM1,$zlM2,$zlM3,$ztM,$ztDate,$ckTypeNote,$ztParticipant,$ztLibelle,$ztLieu,$ztDetail,$rdPrive,$zlHeureDebut,$zlHeureFin,$age_date_create,$age_date_modif,$vSemaineType,$rdRappel,$zlR1,$zlR2,$ckEmail,$rdDispo,$zlContactAssocie,$idAge;
    global $tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$tzEte,$tzHiver;

    if (file_exists($fileName) || !empty($data)) {
      // Tableau pour recontituer la semaine type de l'utilisateur en repetition hebdomadaire
      $tabJourUS = array("SU","MO","TU","WE","TH","FR","SA");
      // Tableau pour recuperer les jours du parametre "BYDAY" dans les repetitions
      $jour = array("SU"=>0,"MO"=>1,"TU"=>2,"WE"=>3,"TH"=>4,"FR"=>5,"SA"=>6);
      $err = "";
      $nbNote = $nbImportOK = $nbImportKO = 0;

      if (!empty($data)) {
        // Lecture des donnees
        $fcontents = explode("\n",$data);
        $fic_name  = $fileName;
      } else {
        // Lecture du fichier
        $fcontents = @file($fileName);
        $fic_name  = $_FILES['ztFile']['name'];
      }
      $fic_ext = explode(".",$fic_name);
      if (strtolower($fic_ext[1])!="ics") {
        $err = "<P class=\"rouge\"><B>".trad("NOTEIMP_ECHEC_ICAL")."</B></P>";
      } else {
        $note_GLOB_TZ="";
        unset ($note_uid);
        $note_uid_Tbl = array();
        for ($ligne_fic=0;$ligne_fic<=count($fcontents);$ligne_fic++) {
          $tabCtt = explode(":",trim($fcontents[$ligne_fic]),2);
          if (trim(($tabCtt[0])=="BEGIN") && (trim($tabCtt[1])=="VEVENT")) {
            $nbNote++;
            // Initialisation des variables
            $rep_info=$note_uid=$note_summary=$note_description=$note_location=$note_class=$note_couleur=$note_rappel=$note_dispo=$note_rrule=$note_dtstart=$note_dtend=$note_duration=$age_date_create=$age_date_modif=$note_contact=$note_categorie=$note_alarm="";
            $zlPeriodicite=$rdQ=$ztQ=$zlH=$ztH=$rdM=$zlM1=$zlM2=$zlM3=$ztM=$rdA=$zlA1=$zlA2=$zlA3=$zlA4=$zlA5=$rdPlage=$ztP=$zlP1=$zlP2=$zlP3="";
            $ztDate=$ckTypeNote=$ztParticipant=$ztLibelle=$ztLieu=$ztDetail=$rdPrive=$zlHeureDebut=$zlHeureFin=$vSemaineType=$rdRappel=$zlR1=$zlR2=$ckEmail=$rdDispo=$zlContactAssocie=$idAge="";
          }
          if ($nbNote>0) {
            // on traite la note
            if (eregi("UID",$tabCtt[0])) {
              $note_uid = trim($tabCtt[1]);
            }
            elseif (eregi("SUMMARY",$tabCtt[0])) {
              $note_summary = str_replace("'","\'",trim($tabCtt[1]));
              $note_summary = str_replace("\n"," ",$note_summary);
              $note_summary = utf8_decode($note_summary);
            }
            elseif (eregi("DESCRIPTION",$tabCtt[0])) {
              if (!eregi("Mozilla Alarm:",$tabCtt[1])) {
                $note_description = str_replace("'","\'",trim($tabCtt[1]));
                $note_description = str_replace("\n","\r\n",$note_description);
                $note_description = utf8_decode($note_description);
              }
            }
            elseif (eregi("LOCATION",$tabCtt[0])) {
              $note_location = str_replace("'","\'",trim($tabCtt[1]));
              $note_location = str_replace("\n"," ",$note_location);
              $note_location = utf8_decode($note_location);
            }
            elseif (eregi("DTSTAMP",$tabCtt[0])) {
              $age_date_create=trim($tabCtt[1]);
            }
            elseif (eregi("LAST-MODIFIED",$tabCtt[0])) {
              $age_date_modif=trim($tabCtt[1]);
            }
            elseif (eregi("CLASS",$tabCtt[0])) {
              $note_class = trim($tabCtt[1]);
            }
            elseif (eregi("X-PHENIX-AGENDA-TYPE",$tabCtt[0])) {
              $ckTypeNote = trim($tabCtt[1]);
            }
            elseif (eregi("X-PHENIX-AGENDA-COLOR",$tabCtt[0])) {
              $note_couleur = str_replace("'","\'",trim($tabCtt[1]));
            }
            elseif (eregi("X-PHENIX-AGENDA-RAPPEL",$tabCtt[0])) {
              $note_rappel = trim($tabCtt[1]);
            }
            elseif (eregi("X-PHENIX-AGENDA-DISPO",$tabCtt[0])) {
              $note_dispo = trim($tabCtt[1]);
            }
            elseif (eregi("X-PHENIX-AGENDA-CONTACT",$tabCtt[0])) {
              $note_contact = trim($tabCtt[1]);
            }
            elseif (eregi("TRIGGER",$tabCtt[0])) {
              $tabCtt[0]="";
              $note_alarm = trim($tabCtt[1]);
            }
            elseif (eregi("DURATION",$tabCtt[0])) {
              $note_duration = trim($tabCtt[1]);
            }
            elseif (eregi("RRULE",$tabCtt[0])) {
              $note_rrule = trim($tabCtt[1]);
            }
            elseif (eregi("CATEGORIES",$tabCtt[0])) {
              $note_categorie = trim($tabCtt[1]);
            }
            elseif (eregi("DTSTART",$tabCtt[0])) {
              $note_dtstart = trim($tabCtt[1]);
              if (eregi("TZID",$tabCtt[0])) {
                $tab_TZ = explode("/",$tabCtt[0]);
                $note_TZ = array_pop($tab_TZ);
                $note_TZ = array_pop($tab_TZ)."/".$note_TZ;
                $note_TZ = array_pop(explode("=",$note_TZ));
              } else {
                $note_TZ=$note_GLOB_TZ;
              }
            }
            elseif (eregi("DTEND",$tabCtt[0])) {
              $note_dtend = trim($tabCtt[1]);
            }
            elseif ($tabCtt[1]=="") {
              if ($last_obj=="DESCRIPTION") {
                $note_description .= str_replace("'","\'",trim($tabCtt[0]));
                $note_description = str_replace("\n","\r\n",$note_description);
                $note_description = utf8_decode($note_description);
              }
              elseif ($last_obj=="LOCATION") {
                $note_location .= str_replace("'","\'",trim($tabCtt[0]));
                $note_location = str_replace("\n"," ",$note_location);
                $note_location = utf8_decode($note_location);
              }
              elseif ($last_obj=="SUMMARY") {
                $note_summary .= str_replace("'","\'",trim($tabCtt[0]));
                $note_summary = str_replace("\n"," ",$note_summary);
                $note_summary = utf8_decode($note_summary);
              }
            }
            $last_obj = $tabCtt[0];
          }
          if (eregi("TZID",$tabCtt[0])) {
            $tab_GLOB_TZ = explode("/",$tabCtt[0]);
            $note_GLOB_TZ = array_pop($tab_GLOB_TZ);
            $note_GLOB_TZ = array_pop($tab_GLOB_TZ)."/".$note_GLOB_TZ;
          }

          if (trim(($tabCtt[0])=="END") && (trim($tabCtt[1])=="VEVENT")) {
            // On met la date au bon format pour pouvoir les comparer
            list($date_debut,$heure_deb_tmp) = explode("T",$note_dtstart);
            $an_debut = substr($date_debut,0,4);
            $mois_debut = substr($date_debut,4,2);
            $jour_debut = substr($date_debut,6,2);
            $date_debut = $an_debut."-".$mois_debut."-".$jour_debut;
            list($date_fin,$heure_fin_tmp) = explode("T",$note_dtend);
            $an_fin = substr($date_fin,0,4);
            $mois_fin = substr($date_fin,4,2);
            $jour_fin = substr($date_fin,6,2);
            $date_fin = $an_fin."-".$mois_fin."-".$jour_fin;
            list($jour_dem_deb,$mois_dem_deb,$an_dem_deb) = explode("/",$_POST['ztDateDeb']);
            $date_dem_deb = $an_dem_deb."-".$mois_dem_deb."-".$jour_dem_deb;
            list($jour_dem_fin,$mois_dem_fin,$an_dem_fin) = explode("/",$_POST['ztDateFin']);
            $date_dem_fin = $an_dem_fin."-".$mois_dem_fin."-".$jour_dem_fin;
            $time_local_start = $time_local_end = "";
            $time_local_start = substr($note_dtstart, -1);
            $time_local_end = substr($note_dtend, -1);
            if (($heure_fin_tmp =="") && ($heure_deb_tmp =="") && ($note_duration=="")) {
              // Jours consecutifs il faut creer une repetition avec le nombre de jours
              $time_local_start = $time_local_end = "";
              $heure_deb_tmp = "000000";
              $heure_fin_tmp = "234500";
              $ckTypeNote = 3;
            }
            if (($heure_fin_tmp =="") && ($heure_deb_tmp !="") && ($note_duration!="")) {
              // On regarde la duree inscrite dans le champ DURATION
              // puis on l'ajoute a l'heure de depart pour avoir le DTEND (heure de fin de l'evenement)
              $substate = "duration";
              $durH = $durM = $m_fin_tmp = $h_fin_tmp = 0;
              if ( preg_match ( "/PT.*([0-9]+)H/", $note_duration, $submatch ) )
                $durH = $submatch[1];
              if ( preg_match ( "/PT.*([0-9][0-9]+)H/", $note_duration, $submatch ) )
                $durH = $submatch[1];
              if ( preg_match ( "/PT.*([0-9]+)M/", $note_duration, $submatch ) )
                $durM = $submatch[1];
              if ( preg_match ( "/PT.*([0-9][0-9]+)M/", $note_duration, $submatch ) )
                $durM = $submatch[1];
              $h_fin_tmp = substr($heure_deb_tmp,0,2) + $durH;
              $m_fin_tmp = substr($heure_deb_tmp,2,2) + $durM;
              if ($m_fin_tmp>=60) {
                $m_fin_tmp = $m_fin_tmp - 60;
                $h_fin_tmp = $h_fin_tmp + 1;
              }
              if ($m_fin_tmp=="0") $m_fin_tmp = "00";
              $heure_fin_tmp = $h_fin_tmp.$m_fin_tmp;
            }
            $heure_deb_h = substr($heure_deb_tmp,0,2);
            $heure_fin_h = substr($heure_fin_tmp,0,2);
            // Date de creation de la note
            if ($age_date_create!="") {
              $dcreate_t_local = "";
              $dcreate_t_local = substr($age_date_create, -1);
              $date_create = substr($age_date_create,0,4);
              $mois_create = substr($age_date_create,4,2);
              $jour_create = substr($age_date_create,6,2);
              $heure_create = substr($age_date_create,9,2);
              $min_create = substr($age_date_create,11,2);
              $sec_create = substr($age_date_create,13,2);
              $age_date_create = $date_create."-".$mois_create."-".$jour_create." ".$heure_create.":".$min_create.":".$sec_create;
            } else {
              $age_date_create = gmdate("Y-m-d")." ".gmdate('H').":".gmdate('i').":".gmdate('s');
            }
            // Date de modification de la note
            if (($age_date_modif!="") || ($age_date_modif!="00000000T000000Z")) {
              $dmodif_t_local = "";
              $dmodif_t_local = substr($age_date_modif, -1);
              $date_modif = substr($age_date_modif,0,4);
              $mois_modif = substr($age_date_modif,4,2);
              $jour_modif = substr($age_date_modif,6,2);
              $heure_modif = substr($age_date_modif,9,2);
              $min_modif = substr($age_date_modif,11,2);
              $sec_modif = substr($age_date_modif,13,2);
              $age_date_modif = $date_modif."-".$mois_modif."-".$jour_modif." ".$heure_modif.":".$min_modif.":".$sec_modif;
            } else {
              $age_date_modif = $age_date_create;
            }
            // minute de debut au format /100
            if ((substr($heure_deb_tmp,2,2)>=0) && (substr($heure_deb_tmp,2,2)<15)) $heure_deb_m = "00";
            if ((substr($heure_deb_tmp,2,2)>=15) && (substr($heure_deb_tmp,2,2)<30)) $heure_deb_m = "25";
            if ((substr($heure_deb_tmp,2,2)>=30) && (substr($heure_deb_tmp,2,2)<45)) $heure_deb_m = "50";
            if ((substr($heure_deb_tmp,2,2)>=45) && (substr($heure_deb_tmp,2,2)<=59)) $heure_deb_m = "75";
            // minute de fin au format /100
            if ((substr($heure_fin_tmp,2,2)>=0) && (substr($heure_fin_tmp,2,2)<15)) $heure_fin_m = "00";
            if ((substr($heure_fin_tmp,2,2)>=15) && (substr($heure_fin_tmp,2,2)<30)) $heure_fin_m = "25";
            if ((substr($heure_fin_tmp,2,2)>=30) && (substr($heure_fin_tmp,2,2)<45)) $heure_fin_m = "50";
            if ((substr($heure_fin_tmp,2,2)>=45) && (substr($heure_fin_tmp,2,2)<=59)) $heure_fin_m = "75";
            // On reforme les heures de deb et fin de la note
            $heure_deb = $heure_deb_h.".".$heure_deb_m;
            $heure_fin = $heure_fin_h.".".$heure_fin_m;
            $ztDate = date("d/m/Y",mktime(12,0,0,$mois_debut,$jour_debut,$an_debut));
            // Si le fuseau est fourni, on converti en utc
            if ($note_TZ != "") {
              // Recuperation des infos de timezone
              $DB_CX->DbQuery("SELECT tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver FROM ${PREFIX_TABLE}timezone WHERE tzn_zone='".$note_TZ."'");
              if ($DB_CX->DbNumRows()) {
                $tzGmt_N = $DB_CX->DbResult(0,"tzn_gmt");
                $tzDateEte_N = $DB_CX->DbResult(0,"tzn_date_ete");
                $tzHeureEte_N = $DB_CX->DbResult(0,"tzn_heure_ete");
                $tzDateHiver_N = $DB_CX->DbResult(0,"tzn_date_hiver");
                $tzHeureHiver_N = $DB_CX->DbResult(0,"tzn_heure_hiver");
                //Decalage des notes en fonction du fuseau horaire
                list($heure_deb,$heure_fin,$dtCrt,$dtMdf,$ztDate) = decaleNote($tzGmt_N,$tzDateEte_N,$tzHeureEte_N,$tzDateHiver_N,$tzHeureHiver_N,$dateJour,$date_debut,$heure_deb,$heure_fin,$dtCrt,$dtMdf,1,1,1);
                $tabDate = explode("-",$ztDate);
                $ztDate = $tabDate[2]."/".$tabDate[1]."/".$tabDate[0];
              }
            }
            // Recuperation des bornes d'import choisis
            // Conversion de la date de debut en UTC
            list($j_deb,$m_deb,$a_deb) = explode("/",$_POST['ztDateDeb']);
            $d_deb = $a_deb."-".$m_deb."-".$j_deb;
            $h_deb = "00.00";
            list($tzEteD,$tzHiverD,$hBascule,$h_deb,$regul) = detectBascule($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$d_deb,$h_deb,1);
            $decalHD = calculDecalageH($tzGmt,$tzEteD,$tzHiverD,mktime(floor($h_deb-$hBascule),(($h_deb-$hBascule)*60)%60,0,$m_deb,$j_deb,$a_deb));
            $date_dem_deb = mktime(floor($h_deb-$decalHD),(($h_deb-$decalHD)*60)%60,0,$m_deb,$j_deb,$a_deb);
            $date_dem_deb = date("Y-m-d H",$date_dem_deb).".".sprintf("%02d",round(date("i",$date_dem_deb)*100/60));
            // Conversion de la date de fin en UTC
            list($j_fin,$m_fin,$a_fin) = explode("/",$_POST['ztDateFin']);
            $d_fin = $a_fin."-".$m_fin."-".$j_fin;
            $h_fin = "24.00";
            list($tzEteF,$tzHiverF,$hBascule,$h_fin,$regul) = detectBascule($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$d_fin,$h_fin,1);
            $decalHF = calculDecalageH($tzGmt,$tzEteF,$tzHiverF,mktime(floor($h_fin-$hBascule),(($h_fin-$hBascule)*60)%60,0,$m_fin,$j_fin,$a_fin));
            $date_dem_fin = mktime(floor($h_fin-$decalHF),(($h_fin-$decalHF)*60)%60,0,$m_fin,$j_fin,$a_fin);
            $date_dem_fin = date("Y-m-d H",$date_dem_fin).".".sprintf("%02d",round(date("i",$date_dem_fin)*100/60));

            if ((($date_debut." ".$heure_deb)>=$date_dem_deb) && (($date_debut." ".$heure_deb)<=$date_dem_fin)) {
              // On traite l'ajout si l'heure et bien comprise entre les 2 dates entrees dans le formulaire.
              $importSuite = true;
              $DB_CX->DbQuery("SELECT age_id, age_date_modif, age_ape_id FROM ${PREFIX_TABLE}agenda WHERE age_date='".substr($date_debut,0,10)."' AND age_heure_debut=$heure_deb AND age_heure_fin=$heure_fin AND age_libelle='$note_summary' AND age_util_id='$idUser'");
              if ($DB_CX->DbNumRows()) {
                if (empty($data)){
                  $err =  "<P class=\"vert\"><B>".trad("NOTEIMP_MSG_IMPORT_ERREUR")."</B></P>";
                  $importValide = false;
                  $importSuite = false;
                } else {
                  $old_age_id = $DB_CX->DbResult(0,0);
                  $old_age_date_modif = $DB_CX->DbResult(0,1);
                  $old_Periodicite = $DB_CX->DbResult(0,2);
                  $note_uid=$old_age_id;
                  $note_uid_Tbl[]=$note_uid;               
                  if (substr($old_age_date_modif,0,-2) == substr($age_date_modif,0,-2)) {
                    // On ne fait rien car la note existe deja
                    $importValide = false;
                    $importSuite = false;
                  }
                } 
              }
              if ($importSuite == true) {
                if (!empty($data)) {
                  $idAge = $note_uid + 0;
                  // Si la note existe.
                  $DB_CX->DbQuery("SELECT age_id FROM ${PREFIX_TABLE}agenda WHERE age_id=".$idAge);
                  if (!$DB_CX->DbNumRows()) {
                    $idAge = "";
                  }
               }
                $ztParticipant = $idUser;
                // couleur
//                $zlCouleur = $note_couleur;
                if ($note_couleur=="") {
                  $DB_CX->DbQuery("SELECT cou_couleur FROM ${PREFIX_TABLE}couleurs WHERE cou_libelle ='$note_categorie'");
                  if ($DB_CX->DbNumRows()) {
                    $zlCouleur = $DB_CX->DbResult(0,0);
                  }
                }
                // rappel
                $rdRappel = (!empty($note_rappel)) ? 2 : 1;
                list($zlR1, $zlR2, $ckEmail) = explode(" ",$note_rappel);
                if (($rdRappel == 1) and (substr($note_alarm, 0, 1) == "-")) {
                  $D_alarm = substr($note_alarm, 0, -1);
                  $T_alarm = substr($note_alarm, -1);
                  $rdRappel = 2;
                  $ckEmail = 0;

                  if ($T_alarm == "M")
                    { $zlR1 = substr($D_alarm, 3); $zlR2 = 1; }
                  elseif ($T_alarm == "H")
                    { $zlR1 = substr($D_alarm, 3); $zlR2 = 60; }
                  elseif ($T_alarm == "D")
                    { $zlR1 = substr($D_alarm, 2); $zlR2 = 1440; }
                  elseif ($T_alarm == "W")
                    { $zlR1 = substr($D_alarm, 2); $zlR2 = 10080; }
                  else
                    { $rdRappel = 1; }
                }
                // disponibilite
                $rdDispo = ($note_dispo=="1") ? 1 : 0;
                $ztLibelle = substr($note_summary,0,230);
                $ztLieu = substr($note_location,0,230);
                $ztDetail = $note_description;
                $zlHeureDebut = $heure_deb;
                $zlHeureFin = $heure_fin;
                $rdPrive = ($note_class=="PRIVATE") ? 1 : 0;
                $zlContactAssocie = $note_contact + 0;
                $importValide = true; // Determine en fin de traitement si la note peut etre enregistree ou pas
                // Gestion Repetition
                if ($note_rrule=="") {
                  // Pas de repetition
                  if ($old_Periodicite>1)
                    $zlPeriodicite = 10;
                  else
                    $zlPeriodicite = 1;
                } else {
                  //On reinitialise les variables de repetitions
                  $vFreqType=$vFreq=$vCount=$vUntil=$vInterval=$vBySetPos="";
                  $aByDay=$aByMonth=$aByMonthDay=array();
                  //On recupere les parametres de repetition
                  $paramRepet = explode(";",$note_rrule);
                  for ($i=0;$i<count($paramRepet);$i++) {
                    list($cle,$valeur) = explode("=",$paramRepet[$i]);
                    switch (trim($cle)) {
                      case "FREQ" :
                        $vFreq = trim($valeur);
                        break;
                      case "COUNT" :
                        $vCount = trim($valeur) + 0;
                        break;
                      case "UNTIL" :
                        $vUntil = trim($valeur);
                        break;
                      case "INTERVAL" :
                        $vInterval = trim($valeur) + 0;
                        break;
                      case "BYDAY" :
                        $aByDay = explode(",",trim($valeur));
                        break;
                      case "BYMONTH" : // Si plusieurs mois on ne gere que le premier
                        $aByMonth = explode(",",trim($valeur));
                        break;
                      case "BYMONTHDAY" : // Si plusieurs jours on ne gere que le premier
                        $aByMonthDay = explode(",",trim($valeur));
                        break;
                      case "BYSETPOS" :
                        $vBySetPos = trim($valeur) + 0;
                        break;
                    }
                  }
                  // -------------------------------- //
                  //    TRAITEMENT DES REPETITIONS    //
                  // -------------------------------- //
                  if (!empty($vFreq)) {
                    // Fin de la repetition
                    if (!empty($vUntil)) {
                      // Fin apres le
                      $rdPlage = 2;
                      $zlP1 = substr($vUntil,6,2); // jour
                      $zlP2 = substr($vUntil,4,2); //mois
                      $zlP3 = substr($vUntil,0,4); //annee
                    } else {
                      // Occurrences
                      $rdPlage = 1;
                      $ztP = ($vCount>0) ? $vCount : 1; // Si pas d'occurrence on transforme en 1 fois
                    }
                    // ** REPETITION QUOTIDIENNE ** //
                    if ($vFreq=="DAILY") {
                      $vFreqType = trad("NOTEIMP_QUOTIDIENNE");
                      $zlPeriodicite = 2;
                      // On ne gere que le cas Tous les X jours dans Phenix
                      $rdQ = 1;
                      $ztQ = ($vInterval>0) ? $vInterval : 1; // Si pas d'interval on transforme en tous les jours
                      for ($i=0;$i<count($tabJourUS);$i++) {
                        $trouve = false;
                        for ($j=0;$j<count($aByDay) && !$trouve;$j++) {
                          if (substr($aByDay[$j],-2) == $tabJourUS[$i]) { // On enleve eventuellement les informations sur la frequence de repetition => style -1WE devient WE
                            $trouve = true;
                          }
                        }
                        $vSemaineType .= ($trouve) ? "1" : "0";
                      }
                      if ($vSemaineType=="0111110") {
                        // Cas particulier de la selection uniquement des jours ouvrables (Lundi a Vendredi) qui est gere par PHENIX dans la periodicite quotidienne
                        $rdQ = 2;
                      }
                    }
                    // ** REPETITION HEBDOMADAIRE ** //
                    elseif ($vFreq=="WEEKLY") {
                      $vFreqType = trad("NOTEIMP_HEBDOMADAIRE");
                      $ztH = ($vInterval>0) ? $vInterval : 1; // Si pas d'interval on transforme en toutes les semaines
                      if (count($aByDay)>0) {
                        $zlPeriodicite = 3;
                        // En fonction de aByDay[], on construit une chaine "1001100" (Dimanche a Samedi) qui sera interpretee comme la semaine type de l'utilisateur a l'enregistrement
                        for ($i=0;$i<count($tabJourUS);$i++) {
                          $trouve = false;
                          for ($j=0;$j<count($aByDay) && !$trouve;$j++) {
                            if (substr($aByDay[$j],-2) == $tabJourUS[$i]) { // On enleve eventuellement les informations sur la frequence de repetition => style -1WE devient WE
                              $trouve = true;
                            }
                          }
                          $vSemaineType .= ($trouve) ? "1" : "0";
                        }
                        if ($vSemaineType=="0111110") {
                          // Cas particulier de la selection uniquement des jours ouvrables (Lundi a Vendredi) qui est gere par PHENIX dans la periodicite quotidienne
                          $zlPeriodicite = 2;
                          $rdQ = 2;
                        }
                      } else {
                        // Si pas aByDay[], on transforme tous les 7 * interval jours
                        $vFreqType = trad("NOTEIMP_QUOTIDIENNE");
                        $zlPeriodicite = 2;
                        $rdQ = 1;
                        $ztQ = (7 * $ztH);
                      }
                    }
                    // ** REPETITION MENSUELLE ** //
                    elseif ($vFreq=="MONTHLY") {
                      $vFreqType = trad("NOTEIMP_MENSUELLE");
                      $zlPeriodicite = 4;
                      $ztM = ($vInterval>0) ? $vInterval : 1; // Si pas d'interval on transforme en tous les mois
                      if (count($aByMonthDay)>0) {
                        // Tous les X( date du jour) de chaque mois => ex Le 4 de chaque mois
                        $rdM = 1;
                        $zlM1 = $aByMonthDay[0]; // Si plusieurs jours on ne gere que le premier
                      } elseif (count($aByDay)>0) {
                        // Tous les premiers...dernier / nom du jour (MO,TU,WE,TH,FR,SA)  du mois => ex Le troisieme mardi du mois
                        $rdM = 2;
                        // Si $aByDay contient plusieurs jours, on ne traite que le premier
                        if (strlen($aByDay[0])>2) {
                          // Recuperation du rang (premier...dernier)
                          $vRang = substr($aByDay[0],0,strlen($aByDay[0])-2);
                          if ($vRang>0) {
                            $zlM2 = ($vRang-1); // 1er a 4eme "xJour" du mois
                          } else {
                            $zlM2 = 4; // Dernier "xJour" du mois
                          }
                        } elseif (!empty($vBySetPos)) {
                          // C'est le cas du rang du BYSETPOS
                          if ($vBySetPos>0) {
                            $zlM2 = ($vBySetPos-1); // 1er a 4eme "xJour" du mois
                          } else {
                            $zlM2 = 4; // Dernier "xJour" du mois
                          }
                        } else {
                          $vFreqType = trad("NOTEIMP_INCONNUE");
                          // La note est enregistree sans repetition
                          $zlPeriodicite = 1;
                        }
                        $zlM3 = $jour[substr($aByDay[0],-2)]; // On recupere le jour
                      }
                    }
                    // ** REPETITION ANNUELLE ** //
                    elseif ($vFreq=="YEARLY") {
                      $vFreqType = trad("NOTEIMP_ANNUELLE");
                      $zlPeriodicite = 5;
                      if (count($aByMonthDay)>0 && count($aByMonth)>0) {
                        // Tous les X( date du jour) de Y mois => ex Tous les 4 Novembre
                        $rdA = 1;
                        $zlA1 = $aByMonthDay[0]; // Si plusieurs jours on ne gere que le premier
                        $zlA2 = $aByMonth[0]; // Si plusieurs mois on ne gere que le premier
                      } elseif (count($aByDay)>0 && count($aByMonth)>0) {
                        // Tous les premiers...dernier xJour de chaque xMois => ex Le premier jeudi de Novembre
                        $rdA = 2;
                        // Si $aByDay contient plusieurs jours, on ne traite que le premier
                        if (strlen($aByDay[0])>2) {
                          // Recuperation du rang (premier...dernier)
                          $vRang = substr($aByDay[0],0,strlen($aByDay[0])-2);
                          if ($vRang>0) {
                            $zlA3 = ($vRang-1); // 1er a 4eme "xJour" du mois
                          } else {
                            $zlA3 = 4; // Dernier "xJour" du mois
                          }
                        } elseif (!empty($vBySetPos)) {
                          // C'est le cas du rang du BYSETPOS
                          if ($vBySetPos>0) {
                            $zlA3 = ($vBySetPos-1); // 1er a 4eme "xJour" du mois
                          } else {
                            $zlA3 = 4; // Dernier "xJour" du mois
                          }
                        } else {
                          $vFreqType = trad("NOTEIMP_INCONNUE");
                          // La note est enregistree sans repetition
                          $zlPeriodicite = 1;
                        }
                        $zlA4 = $jour[substr($aByDay[0],-2)]; // Si plusieurs jours on ne gere que le premier
                        $zlA5 = $aByMonth[0]; // Si plusieurs mois on ne gere que le premier
                      } elseif (count($aByMonthDay)==0 && count($aByMonth)==0 && count($aByDay)==0) {
                        // C'est une repetition annuelle sur le jour de la note
                        $rdA = 1;
                        $zlA1 = $jour_debut; // jour de l'evenement a repeter
                        $zlA2 = $mois_debut; // mois de l'evenement a repeter
                      } else {
                        $vFreqType = trad("NOTEIMP_INCONNUE");
                        // La note est enregistree sans repetition
                        $zlPeriodicite = 1;
                      }
                    }
                    // ** REPETITION NON TRAITEE DANS PHENIX ** //
                    else {
                      $vFreqType = trad("NOTEIMP_INCONNUE");
                      // La note est enregistree sans repetition
                      $zlPeriodicite = 1;
                    }
                    $rep_info = " (".trad("NOTEIMP_REPETITION")." : ".$vFreqType.")";
                  }
                }
              }
              //Decalage des notes en fonction du fuseau horaire
              list($heure_deb_loc,$heure_fin_loc,$dtCrt,$dtMdf,$date_deb_loc) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$dateJour,$date_debut,$heure_deb,$heure_fin,$dtCrt,$dtMdf,1,0,1);
              $heure_deb_loc = floor($heure_deb_loc).".".(($heure_deb_loc*60)%60);
              $heure_fin_loc = floor($heure_fin_loc).".".(($heure_fin_loc*60)%60);
              $tabDate = explode("-",$date_deb_loc);
              $date_deb_loc = $tabDate[2]."/".$tabDate[1]."/".$tabDate[0];
              // Generation du rapport et Enregistrement de la note si import valide et non valide
              $note_uid_Tbl[]=addReport($importValide, floor($heure_deb_loc).trad("NOTEIMP_H").(substr($heure_deb_loc*100,-2)), floor($heure_fin_loc).trad("NOTEIMP_H").(substr($heure_fin_loc*100,-2)).$rep_info, $date_deb_loc, $note_summary);
            }
          }
        }
        if ($ImportSunbird==true) {
          unset ($Sql_uid_Tbl);
          $DB_CX->DbQuery("SELECT age_id FROM ${PREFIX_TABLE}agenda WHERE age_aty_id>1 AND age_mere_id=0 AND age_util_id='$idUser'");
          while ($enr = $DB_CX->DbNextRow()) {
            $Sql_uid_Tbl[] = $enr['age_id'];
          }
          $liste_note_uid = implode(",", array_diff($Sql_uid_Tbl, $note_uid_Tbl));
          if ($liste_note_uid!="") {
            unset ($Sql_uid_Tbl);
            $DB_CX->DbQuery("SELECT age_id FROM ${PREFIX_TABLE}agenda WHERE age_mere_id IN (".$liste_note_uid.")");
            while ($enr = $DB_CX->DbNextRow()) {
              $Sql_uid_Tbl[] = $enr['age_id'];
            }
            if (isset($Sql_uid_Tbl)) $liste_note_uid_mere = implode(",", $Sql_uid_Tbl);
            if (isset($liste_note_uid_mere)) $Ch_liste=$liste_note_uid.",".$liste_note_uid_mere; else $Ch_liste=$liste_note_uid;
            $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda WHERE age_id IN (".$Ch_liste.")");
            $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda_concerne WHERE aco_age_id IN (".$Ch_liste.")");
          }
        }    
      }
    }
  }
  //--------------------------------------------------------------------------------

  // Extraction depuis le fichier d'import (Import VCal standar .vcs  PalmDesktop)
  function importVCAL($fileName,&$err) {
    global $DB_CX, $PREFIX_TABLE, $idUser;
    global $zlPeriodicite,$zlCouleur,$rdA,$zlA1,$zlA2,$zlA3,$zlA4,$zlA5,$rdPlage,$ztP,$zlP1,$zlP2,$zlP3,$rdQ,$ztQ,$zlH,$ztH,$rdM,$zlM1,$zlM2,$zlM3,$ztM,$ztDate,$ckTypeNote,$idAge,$ztParticipant,$ztLibelle,$ztLieu,$ztDetail,$rdPrive,$zlHeureDebut,$zlHeureFin,$age_date_create,$age_date_modif,$vSemaineType,$rdRappel,$zlR1,$zlR2,$ckEmail,$rdDispo,$zlContactAssocie;
    global $tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$tzEte,$tzHiver;

    if (file_exists($fileName)) {
      // Tableau pour recontituer la semaine type de l'utilisateur en repetition hebdomadaire
      $tabJourUS = array("SU","MO","TU","WE","TH","FR","SA");
      // Tableau pour recuperer les jours du parametre "BYDAY" dans les repetitions OBLIGER DE METTRE DES "" POUR LES VALEURS A CAUSE D'UN BUG DANS LES REPETITIONS HEBDO
      $jour = array("SU"=>"0","MO"=>"1","TU"=>"2","WE"=>"3","TH"=>"4","FR"=>"5","SA"=>"6");
      $err = "";
      $nbNote = $nbImportOK = $nbImportKO = 0;

      // Lecture du fichier
      $fcontents = @file($fileName);
      $fic_name  = $_FILES['ztFile']['name'];
      $fic_ext = explode(".",$fic_name);
      if (strtolower($fic_ext[1])!="vcs") {
        $err = "<P class=\"rouge\"><B>".trad("NOTEIMP_ECHEC_VCAL")."</B></P>";
      } else {
        for ($ligne_fic=0;$ligne_fic<count($fcontents);$ligne_fic++) {
          $tabCtt = explode(":",trim($fcontents[$ligne_fic]),2);
          if (trim(($tabCtt[0])=="BEGIN") && (trim($tabCtt[1])=="VEVENT")) {
            $nbNote++;
            // Initialisation des variables
            $rep_info=$note_summary=$note_description=$note_location=$note_class=$note_couleur=$note_rappel=$note_dispo=$note_rrule=$note_dtstart=$note_dtend=$age_date_create=$age_date_modif=$note_contact=$note_categorie=$note_alarm="";
            $zlPeriodicite=$rdQ=$ztQ=$zlH=$ztH=$rdM=$zlM1=$zlM2=$zlM3=$ztM=$rdA=$zlA1=$zlA2=$zlA3=$zlA4=$zlA5=$rdPlage=$ztP=$zlP1=$zlP2=$zlP3="";
            $idAge=$ztDate=$ckTypeNote=$ztParticipant=$ztLibelle=$ztLieu=$ztDetail=$rdPrive=$zlHeureDebut=$zlHeureFin=$vSemaineType=$rdRappel=$zlR1=$zlR2=$ckEmail=$rdDispo=$zlContactAssocie="";
          }
          if ($nbNote>0) {
            // on traite la note
            if (eregi("SUMMARY",$tabCtt[0])) {
              $note_summary = str_replace("'","\'",trim($tabCtt[1]));
            }
            elseif (eregi("DESCRIPTION",$tabCtt[0])) {
              $note_description = str_replace("'","\'",trim($tabCtt[1]));
              $note_description = str_replace("=0D=0A","\r\n",$note_description);
            }
            elseif (eregi("LOCATION",$tabCtt[0])) {
              $note_location = str_replace("'","\'",trim($tabCtt[1]));
            }
            elseif (eregi("CLASS",$tabCtt[0])) {
              $note_class = trim($tabCtt[1]);
            }
            elseif (eregi("X-PHENIX-AGENDA-TYPE",$tabCtt[0])) {
              $ckTypeNote = trim($tabCtt[1]);
            }
            elseif (eregi("X-PHENIX-AGENDA-COLOR",$tabCtt[0])) {
              $note_couleur = str_replace("'","\'",trim($tabCtt[1]));
            }
            elseif (eregi("X-PHENIX-AGENDA-RAPPEL",$tabCtt[0])) {
              $note_rappel = trim($tabCtt[1]);
            }
            elseif (eregi("X-PHENIX-AGENDA-DISPO",$tabCtt[0])) {
              $note_dispo = trim($tabCtt[1]);
            }
            elseif (eregi("X-PHENIX-AGENDA-CONTACT",$tabCtt[0])) {
              $note_contact = trim($tabCtt[1]);
            }
            elseif (eregi("DCREATED",$tabCtt[0])) {
              $age_date_create= trim($tabCtt[1]);
            }
            elseif (eregi("LAST-MODIFIED",$tabCtt[0])) {
              $age_date_modif=trim($tabCtt[1]);
            }
            elseif (eregi("RRULE",$tabCtt[0])) {
              $note_rrule = trim($tabCtt[1]);
            }
            elseif (eregi("CATEGORIES",$tabCtt[0])) {
              $note_categorie = trim($tabCtt[1]);
            }
            elseif (eregi("DALARM",$tabCtt[0])) {
              $note_alarm = trim($tabCtt[1]);
            }
            elseif (eregi("DTSTART",$tabCtt[0])) {
              $note_dtstart = trim($tabCtt[1]);
            }
            elseif (eregi("DTEND",$tabCtt[0])) {
              $note_dtend = trim($tabCtt[1]);
            }
          }
          if (trim(($tabCtt[0])=="END") && (trim($tabCtt[1])=="VEVENT")) {
            // On met la date au bon format pour pouvoir les comparer
            list($date_debut,$heure_deb_tmp) = explode("T",$note_dtstart);
            $an_debut = substr($date_debut,0,4);
            $mois_debut = substr($date_debut,4,2);
            $jour_debut = substr($date_debut,6,2);
            $date_debut = $an_debut."-".$mois_debut."-".$jour_debut;
            list($date_fin,$heure_fin_tmp) = explode("T",$note_dtend);
            $an_fin = substr($date_fin,0,4);
            $mois_fin = substr($date_fin,4,2);
            $jour_fin = substr($date_fin,6,2);
            $date_fin = $an_fin."-".$mois_fin."-".$jour_fin;
            list($jour_dem_deb,$mois_dem_deb,$an_dem_deb) = explode("/",$_POST['ztDateDeb']);
            $date_dem_deb = $an_dem_deb."-".$mois_dem_deb."-".$jour_dem_deb;
            list($jour_dem_fin,$mois_dem_fin,$an_dem_fin) = explode("/",$_POST['ztDateFin']);
            $date_dem_fin = $an_dem_fin."-".$mois_dem_fin."-".$jour_dem_fin;
            if (($heure_fin_tmp =="") && ($heure_deb_tmp =="")) {
              // Jours consecutifs il faut creer une repetition avec le nombre de jours
              $time_local_start = $time_local_end = "";
              $heure_deb_tmp = "000000";
              $heure_fin_tmp = "234500";
              $ckTypeNote = 3;
              $note_rrule = "FREQ=DAILY;UNTIL=".$an_fin.$mois_fin.$jour_fin.";INTERVAL=1";
            }
            $time_local_start = $time_local_end = "";
            $time_local_start = substr($note_dtstart, -1);
            $time_local_end = substr($note_dtend, -1);
            $heure_deb_h = substr($heure_deb_tmp,0,2);
            $heure_fin_h = substr($heure_fin_tmp,0,2);
            // Date de creation de la note
            if ($age_date_create!="") {
              $dcreate_t_local = "";
              $dcreate_t_local = substr($age_date_create, -1);
              $date_create = substr($age_date_create,0,4);
              $mois_create = substr($age_date_create,4,2);
              $jour_create = substr($age_date_create,6,2);
              $heure_create = substr($age_date_create,9,2);
              $min_create = substr($age_date_create,11,2);
              $sec_create = substr($age_date_create,13,2);
              $age_date_create = $date_create."-".$mois_create."-".$jour_create." ".$heure_create.":".$min_create.":".$sec_create;
            } else {
              $age_date_create = gmdate("Y-m-d")." ".gmdate('H').":".gmdate('i').":".gmdate('s');
            }
            // Date de modification de la note
            if (($age_date_modif!="") || ($age_date_modif!="00000000T000000Z")) {
              $dmodif_t_local = "";
              $dmodif_t_local = substr($age_date_modif, -1);
              $date_modif = substr($age_date_modif,0,4);
              $mois_modif = substr($age_date_modif,4,2);
              $jour_modif = substr($age_date_modif,6,2);
              $heure_modif = substr($age_date_modif,9,2);
              $min_modif = substr($age_date_modif,11,2);
              $sec_modif = substr($age_date_modif,13,2);
              $age_date_modif = $date_modif."-".$mois_modif."-".$jour_modif." ".$heure_modif.":".$min_modif.":".$sec_modif;
            } else {
              $age_date_modif = $age_date_create;
            }
            // minute de debut au format /100
            if ((substr($heure_deb_tmp,2,2)>=0) && (substr($heure_deb_tmp,2,2)<15)) $heure_deb_m = "00";
            if ((substr($heure_deb_tmp,2,2)>=15) && (substr($heure_deb_tmp,2,2)<30)) $heure_deb_m = "25";
            if ((substr($heure_deb_tmp,2,2)>=30) && (substr($heure_deb_tmp,2,2)<45)) $heure_deb_m = "50";
            if ((substr($heure_deb_tmp,2,2)>=45) && (substr($heure_deb_tmp,2,2)<=59)) $heure_deb_m = "75";
            // minute de fin au format /100
            if ((substr($heure_fin_tmp,2,2)>=0) && (substr($heure_fin_tmp,2,2)<15)) $heure_fin_m = "00";
            if ((substr($heure_fin_tmp,2,2)>=15) && (substr($heure_fin_tmp,2,2)<30)) $heure_fin_m = "25";
            if ((substr($heure_fin_tmp,2,2)>=30) && (substr($heure_fin_tmp,2,2)<45)) $heure_fin_m = "50";
            if ((substr($heure_fin_tmp,2,2)>=45) && (substr($heure_fin_tmp,2,2)<=59)) $heure_fin_m = "75";
            // On reforme les heures de deb et fin de la note
            $heure_deb = $heure_deb_h.".".$heure_deb_m;
            $heure_fin = $heure_fin_h.".".$heure_fin_m;

            // Recuperation des bornes d'import choisis
            // Conversion de la date de debut en UTC
            list($j_deb,$m_deb,$a_deb) = explode("/",$_POST['ztDateDeb']);
            $d_deb = $a_deb."-".$m_deb."-".$j_deb;
            $h_deb = "00.00";
            list($tzEteD,$tzHiverD,$hBascule,$h_deb,$regul) = detectBascule($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$d_deb,$h_deb,1);
            $decalHD = calculDecalageH($tzGmt,$tzEteD,$tzHiverD,mktime(floor($h_deb-$hBascule),(($h_deb-$hBascule)*60)%60,0,$m_deb,$j_deb,$a_deb));
            $date_dem_deb = mktime(floor($h_deb-$decalHD),(($h_deb-$decalHD)*60)%60,0,$m_deb,$j_deb,$a_deb);
            $date_dem_deb = date("Y-m-d H",$date_dem_deb).".".sprintf("%02d",round(date("i",$date_dem_deb)*100/60));
            // Conversion de la date de fin en UTC
            list($j_fin,$m_fin,$a_fin) = explode("/",$_POST['ztDateFin']);
            $d_fin = $a_fin."-".$m_fin."-".$j_fin;
            $h_fin = "24.00";
            list($tzEteF,$tzHiverF,$hBascule,$h_fin,$regul) = detectBascule($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$d_fin,$h_fin,1);
            $decalHF = calculDecalageH($tzGmt,$tzEteF,$tzHiverF,mktime(floor($h_fin-$hBascule),(($h_fin-$hBascule)*60)%60,0,$m_fin,$j_fin,$a_fin));
            $date_dem_fin = mktime(floor($h_fin-$decalHF),(($h_fin-$decalHF)*60)%60,0,$m_fin,$j_fin,$a_fin);
            $date_dem_fin = date("Y-m-d H",$date_dem_fin).".".sprintf("%02d",round(date("i",$date_dem_fin)*100/60));

            if ((($date_debut." ".$heure_deb)>=$date_dem_deb) && (($date_debut." ".$heure_deb)<=$date_dem_fin)) {
              // On traite l'ajout si l'heure et bien comprise entre les 2 dates entrees dans le formulaire.
              $DB_CX->DbQuery("SELECT age_id FROM ${PREFIX_TABLE}agenda WHERE age_date='$date_debut' AND age_heure_debut=$heure_deb AND age_heure_fin=$heure_fin AND age_libelle='$note_summary' AND age_util_id='$idUser'");
              if ($DB_CX->DbNumRows()) {
                // On ne fait rien car la note existe deja
                $err =  "<P class=\"vert\"><B>".trad("NOTEIMP_MSG_IMPORT_ERREUR")."</B></P>";
                $importValide = false;
              } else {
                $ztParticipant = $idUser;
                // couleur
//                $zlCouleur = $note_couleur;
                if ($note_couleur=="") {
                  $DB_CX->DbQuery("SELECT cou_couleur FROM ${PREFIX_TABLE}couleurs WHERE cou_libelle ='$note_categorie'");
                  if ($DB_CX->DbNumRows()) {
                    $zlCouleur = $DB_CX->DbResult(0,0);
                  }
                }
                // rappel
                $rdRappel = (!empty($note_rappel)) ? 2 : 1;
                list($zlR1, $zlR2, $ckEmail) = explode(" ",$note_rappel);
                if (($rdRappel == 1) and ($note_alarm != "")) {
                  $rdRappel = 2;
                  $ckEmail = 0;
                  $depart_alarme = substr($note_dtstart,0,4)."-".substr($note_dtstart,4,2)."-".substr($note_dtstart,6,2)." ".substr($note_dtstart,9,2).":".substr($note_dtstart,11,2).":".substr($note_dtstart,13,2);
                  $fin_alarme = substr($note_alarm,0,4)."-".substr($note_alarm,4,2)."-".substr($note_alarm,6,2)." ".substr($note_alarm,9,2).":".substr($note_alarm,11,2).":".substr($note_alarm,13,2);
                  $duree_alarm = (strtotime(substr($depart_alarme, 0, -1))-strtotime(substr($fin_alarme, 0, -1)))/60;
                  if (($duree_alarm >= 1440) and (fmod( $duree_alarm, 1440 )==0))
                    { $zlR1 = ($duree_alarm / 1440); $zlR2 = 1440; }
                  elseif (($duree_alarm >= 60) and (fmod( $duree_alarm, 60 )==0))
                    { $zlR1 = ($duree_alarm /60); $zlR2 = 60; }
                  else
                    { $zlR1 = $duree_alarm; $zlR2 = 1; }
                }
                // disponibilite
                $rdDispo = ($note_dispo=="1") ? 1 : 0;
                $ztLibelle = substr($note_summary,0,230);
                $ztLieu = substr($note_location,0,230);
                $ztDetail = $note_description;
                $ztDate = $jour_debut."/".$mois_debut."/".$an_debut;
                $zlHeureDebut = $heure_deb;
                $zlHeureFin = $heure_fin;
                $rdPrive = ($note_class=="PRIVATE") ? 1 : 0;
                $zlContactAssocie = $note_contact + 0;
                $importValide = true; // Determine en fin de traitement si la note peut etre enregistree ou pas
                // Gestion Repetition
                if ($note_rrule=="") {
                  // Pas de repetition
                  $zlPeriodicite = 1;
                } else {
                  //On reinitialise les variables de repetitions
                  $vFreqType=$vFreq=$vCount=$vUntil=$vInterval="";
                  $aByDay=$aByMonth=$aByMonthDay=array();
                  // On recupere le type de repetition
                  $vFreq = @substr($note_rrule,0,1);
                  if (($vFreq=="D") || ($vFreq=="W") || ($vFreq=="M") || ($vFreq=="Y")) {
                    // C'est un type VCal !! tout est ok
                    $rule_options = explode(" ",$note_rrule);
                    $nb_options = count($rule_options);
                    // -------------------------------- //
                    //    TRAITEMENT DES REPETITIONS    //
                    // -------------------------------- //
                    // On recherche si c'est un COUNT ou un UNTIL
                    $derniereOption = $rule_options[$nb_options-1];
                    $nbOptionsALire = $nb_options-1; // Par defaut on considere que le dernier parametre concerne la periodicite
                    if (substr($derniereOption,0,1)=="#") {
                      if (substr($derniereOption,1,1)=="0") {
                        // #0 => C'est un UNTIL infini, PHENIX ne le gerant pas on fixe ARBITRAIREMENT la date de fin au 31/12/annee courante + 10 ans
                        $vUntil = (gmdate("Y")+10)."1231";
                      } else {
                        // #n => C'est un COUNT
                        $vCount = substr($derniereOption,1,strlen($derniereOption)-1) + 0;
                      }
                    } elseif (strlen($derniereOption)>=8) {
                      // La derniere option contient la date de fin au format aaaammjj...
                      $vUntil = substr($derniereOption,0,8);
                    } else {
                      // La derniere option n'est pas une date ou un nombre d'occurrence  => C'est un UNTIL infini, PHENIX ne le gerant pas on fixe ARBITRAIREMENT la date de fin au 31/12/annee courante + 10 ans
                      $vUntil = (gmdate("Y")+10)."1231";
                      $nbOptionsALire += 1; // Comme la derniere option n'est pas un parametre de periodicite, il va falloir l'interpreter (utile en periodicite hebdo pour la gestion de la pseudo semaine type)
                    }
                    // Fin de la repetition
                    if (!empty($vUntil) && strlen($vUntil)==8) {
                      // Fin apres le
                      $rdPlage = 2;
                      $zlP1 = substr($vUntil,6,2); // jour
                      $zlP2 = substr($vUntil,4,2); //mois
                      $zlP3 = substr($vUntil,0,4); //annee
                    } else {
                      // Occurrences
                      $rdPlage = 1;
                      $ztP = ($vCount>0) ? $vCount : 1; // Si pas d'occurrence on transforme en 1 fois
                    }

                    // on explose la date de debut en : an, mois, jour
                    list($date_debut_an,$date_debut_mois,$date_debut_jour) = explode("-",$date_debut);

                    // ** REPETITION QUOTIDIENNE ** //
                    if ($vFreq=="D") {
                      $vFreqType = trad("NOTEIMP_QUOTIDIENNE");
                      $zlPeriodicite = 2;
                      $rdQ = 1;
                      $ztQ = substr($rule_options[0],1,strlen($rule_options[0])-1) + 0; // tous les X jours
                      if ($ztQ==0) {
                        // Format de repetition quotidienne non valide,  on transforme en tous les jours
                        $ztQ=1;
                      }
                    }
                    // ** REPETITION HEBDOMADAIRE ** //
                    elseif ($vFreq=="W") {
                      $vFreqType = trad("NOTEIMP_HEBDOMADAIRE");
                      // On stocke les parametres VALIDES sur les jours de repetitions dans un tableau (on exclu le premier et eventuellement le dernier parametre en fonction de $nbOptionsALire ET on exclu egalement tous les parametres de repetitions du style  2+ ou autres)
                      for ($i=1;$i<$nbOptionsALire;$i++) {
                        if ($jour[$rule_options[$i]]!="") { // C'est a cause de ce test qu'il a fallu mettre les valeurs entre "" dans $jour[] car sinon la valeur 0 etait interpretee comme une chaine vide !!!
                          $aByDay[] = $rule_options[$i];
                        }
                      }
                      if (count($aByDay)>0) {
                        $zlPeriodicite = 3;
                        $ztH = substr($rule_options[0],1,strlen($rule_options[0])-1) + 0; // toutes les X semaines
                        if ($ztH==0) {
                          // Format de repetition hebdomadaire non valide, on transforme en toutes les semaines
                          $ztH=1;
                        }
                        // En fonction de aByDay[], on construit une chaine "1001100" (Dimanche a Samedi) qui sera interpretee comme la semaine type de l'utilisateur a l'enregistrement
                        for ($i=0;$i<count($tabJourUS);$i++) {
                          $trouve = false;
                          for ($j=0;$j<count($aByDay) && !$trouve;$j++) {
                            if ($aByDay[$j] == $tabJourUS[$i]) {
                              $trouve = true;
                            }
                          }
                          $vSemaineType .= ($trouve) ? "1" : "0";
                        }
                        if ($vSemaineType=="0111110") {
                          // Cas particulier de la selection uniquement des jours ouvrables (Lundi a Vendredi) qui est gere par PHENIX
                          $zlPeriodicite = 2;
                          $rdQ = 2;
                        }
                      } else {
                        $vFreqType = trad("NOTEIMP_INCONNUE");
                        // La note est enregistree sans repetition
                        $zlPeriodicite = 1;
                      }
                    }
                   // ** REPETITION MENSUELLE ** //
                    elseif ($vFreq=="M") {
                      $vFreqType = trad("NOTEIMP_MENSUELLE");
                      $zlPeriodicite = 4;
                      $ztM = substr($rule_options[0],2,strlen($rule_options[0])-2) + 0; // tous les X mois
                      if ($ztM==0) {
                        // Format de repetition mensuelle non valide,  on transforme en tous les mois
                        $ztM=1;
                      }

                      // On cherche s'il y a un rang ou non
                      $m_rang = substr($rule_options[0],1,1);

                      if ($m_rang=="D") { // Every month on the 7th for 12 months: MD1 7 #12
                        // Tous les X( date du jour) de chaque mois => ex Le 4 de chaque mois
                        $rdM = 1;
                        if ($nbOptionsALire>1) {
                          $zlM1 = $rule_options[1]; // Si plusieurs jours on ne gere que le premier
                        } else {
                          // Il n'y a aucun jour de precise pour les repetitions
                          // On prend donc la jour de debut de la note "dtstart"
                          $zlM1 = $date_debut_jour + 0;
                        }
                      } elseif ($m_rang=="P") { //Every six months on the first Monday of the month for 24 months: MP6 1+ MO #2
                        // Tous les premiers...dernier / nom du jour (MO,TU,WE,TH,FR,SA)  du mois => ex Le troisieme mardi du mois
                        $rdM = 2;
                        // Recuperationdu rang (premier...dernier)
                        $vRang = substr($rule_options[1],0,strlen($rule_options[1])-2);
                        $jRang = substr($rule_options[1],0,1); // Premier caractere donne le rang du jour (1=premier...4=quatrieme, (5 et +)=dernier
                        $signeRang = substr($rule_options[1],strlen($rule_options[1])-1); // Dernier caractere donne le signe + ou -
                        if ($signeRang=="+") {
                          if ($jRang<5) {
                            $zlM2 = max(($jRang-1),0); // 1er a 4eme "xJour" du mois
                          } else {
                            $zlM2 = 4; // Dernier "xJour" du mois
                          }
                        } else {
                          // On procede a la recherche a l'envers, depuis la fin du mois
                          if ($jRang==1) $zlM2=4;     // Dernier
                          elseif ($jRang==2) $zlM2=3;
                          elseif ($jRang==3) $zlM2=2;
                          elseif ($jRang==4) $zlM2=1;
                          else $zlM2=0;                // Premier
                        }
                        // On cherche le numero du jour 0:dimanche -> 6:samedi
                        $zlM3 = $jour[$rule_options[2]];
                      } else {
                        $vFreqType = trad("NOTEIMP_INCONNUE");
                        // La note est enregistree sans repetition
                        $zlPeriodicite = 1;
                      }
                    }
                    // ** REPETITION ANNUELLE ** //
                    elseif ($vFreq=="Y") {
                      $vFreqType = trad("NOTEIMP_ANNUELLE");
                      $zlPeriodicite = 5;
                      $rdA = 1;
                      $zlA1 = $date_debut_jour; // jour de l'evenement a repeter
                      $zlA2 = $date_debut_mois; // mois de l'evenement a repeter
                    }
                  }
                  // ** REPETITION NON TRAITEE DANS PHENIX ** //
                  else {
                    $vFreqType = trad("NOTEIMP_INCONNUE");
                    // La note est enregistree sans repetition
                    $zlPeriodicite = 1;
                  }
                  $rep_info = " (".trad("NOTEIMP_REPETITION")." : ".$vFreqType.")";
                }
              }
              //Decalage des notes en fonction du fuseau horaire
              list($heure_deb_loc,$heure_fin_loc,$dtCrt,$dtMdf,$date_deb_loc) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$dateJour,$date_debut,$heure_deb,$heure_fin,$dtCrt,$dtMdf,1,0,1);
              $heure_deb_loc = floor($heure_deb_loc).".".(($heure_deb_loc*60)%60);
              $heure_fin_loc = floor($heure_fin_loc).".".(($heure_fin_loc*60)%60);
              $tabDate = explode("-",$date_deb_loc);
              $date_deb_loc = $tabDate[2]."/".$tabDate[1]."/".$tabDate[0];
              // Generation du rapport et Enregistrement de la note si import valide
              addReport($importValide, floor($heure_deb_loc).trad("NOTEIMP_H").(substr($heure_deb_loc*100,-2)), floor($heure_fin_loc).trad("NOTEIMP_H").(substr($heure_fin_loc*100,-2)).$rep_info, $date_deb_loc, $note_summary);
            }
          }
        }
      }
    }
  }
  //--------------------------------------------------------------------------------

  // Extraction depuis le fichier d'import (Import PalmDesktop DBA)
  include "agenda_note_import_dba.php";

  // Extraction depuis le fichier d'import CSV (Outlook, les repetitions ne sont pas gerees !!)
  function importCSV($fileName,&$err) {
    global $DB_CX, $PREFIX_TABLE, $idUser;
    global $zlPeriodicite,$zlCouleur,$rdA,$zlA1,$zlA2,$ztP,$zlP1,$zlP2,$zlP3,$rdQ,$ztQ,$zlH,$ztH,$rdM,$zlM1,$zlM2,$zlM3,$ztDate,$ckTypeNote,$idAge,$ztParticipant,$ztLibelle,$ztDetail,$rdPrive,$zlHeureDebut,$zlHeureFin,$age_date_create,$age_date_modif;
    global $tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$tzEte,$tzHiver;
    $age_date_create="";

    if (file_exists($fileName)) {
      $err = "";
      $nbNote = $ajout_en_cours = $ajout_ok = $nb_champ_cours = 0;
      $fcontents = @file($fileName);
      $fic_name  = $_FILES['ztFile']['name'];
      $fic_ext = explode(".",$fic_name);
      if (strtolower($fic_ext[1])!="csv" && strtolower($fic_ext[1])!="txt") {
        $err = "<P class=\"rouge\"><B>".trad("NOTEIMP_ECHEC_CSV")."</B></P>";
      } else {
        $tabindex = explode(",",$fcontents[0]);
        $nbChamp = count($tabindex);
        for ($i=1;$i<=count($fcontents);$i++) {
          // Initialisation des variables
          if (($ajout_en_cours==0) && ($ajout_ok==0)) {
            $nbNote = $nbNote + 1;
            $note_summary = "";
            $note_description = "";
            $note_location = "";
            $zlPeriodicite=$rdA=$zlA1=$zlA2=$ztP=$zlP1=$zlP2=$zlP3=$rdQ=$ztQ=$zlH=$ztH=$rdM=$zlM1=$zlM2=$zlM3=$ztDate=$ckTypeNote=$idAge=$ztParticipant=$ztLibelle=$ztDetail=$rdPrive=$zlHeureDebut=$zlHeureFin="";
          }
          $tabCtt = explode(",",$fcontents[$i]);
          if (count($tabCtt)==$nbChamp) { // c'est une note sans commentaire
            $ajout_en_cours = 1;
            for ($j=0;$j<$nbChamp;$j++) {  // On regarde tous les champs
              // Recup du titre
              if ($tabindex[$j]=='"'.trad("NOTEIMP_CHAMP_OBJET").'"') {
                $ztLibelle = str_replace('"','',$tabCtt[$j]);
                $ztLibelle = str_replace("'","\'",$ztLibelle);
              }
              // Recup de la date de debut ou heure de debut (merci Microsoft pour les noms pourris !!)
              if ($tabindex[$j]=='"'.trad("NOTEIMP_CHAMP_DEBUT").'"') {
                $pos = strpos($tabCtt[$j], "/");
                if ($pos === false) {
                  // c'est l'heure de debut
                  list($heure_deb_h,$heure_deb_m_tmp,$heure_deb_sec) = explode(':',str_replace('"','',$tabCtt[$j]));
                  // minute de debut au format /100
                  if (($heure_deb_m_tmp>=0) && ($heure_deb_m_tmp<15)) $heure_deb_m = "00";
                  if (($heure_deb_m_tmp>=15) && ($heure_deb_m_tmp<30)) $heure_deb_m = "25";
                  if (($heure_deb_m_tmp>=30) && ($heure_deb_m_tmp<45)) $heure_deb_m = "50";
                  if (($heure_deb_m_tmp>=45) && ($heure_deb_m_tmp<=59)) $heure_deb_m = "75";
                  // On reforme les heures de deb et fin de la note au format Phenix
                  $zlHeureDebut = $heure_deb_h.".".$heure_deb_m;
                } else {
                  // C'est la date de debut de l'evenement
                  list($jour_debut,$mois_debut,$an_debut) = explode("/",str_replace('"','',$tabCtt[$j]));
                  if (strlen($mois_debut)==1) $mois_debut = "0".$mois_debut;
                  if (strlen($jour_debut)==1) $jour_debut = "0".$jour_debut;
                  // On reforme la date au format Phenix
                  $Date_note = $jour_debut."/".$mois_debut."/".$an_debut;
                  $date_debut = $an_debut."-".$mois_debut."-".$jour_debut;
                }
              }
              // Recup de la date de fin ou heure de fin
              if ($tabindex[$j]=='"'.trad("NOTEIMP_CHAMP_FIN").'"') {
                $pos = strpos($tabCtt[$j], "/");
                if ($pos === false) {
                  // c'est l'heure de fin
                  list($heure_fin_h,$heure_fin_m_tmp,$heure_fin_sec) = explode(':',str_replace('"','',$tabCtt[$j]));
                  // minute de debut au format /100
                  if (($heure_fin_m_tmp>=0) && ($heure_fin_m_tmp<15)) $heure_fin_m = "00";
                  if (($heure_fin_m_tmp>=15) && ($heure_fin_m_tmp<30)) $heure_fin_m = "25";
                  if (($heure_fin_m_tmp>=30) && ($heure_fin_m_tmp<45)) $heure_fin_m = "50";
                  if (($heure_fin_m_tmp>=45) && ($heure_fin_m_tmp<=59)) $heure_fin_m = "75";
                  // On reforme les heures de fin et fin de la note
                  $zlHeureFin = $heure_fin_h.".".$heure_fin_m;
                }
                // On ne s'occupe pas de la date de fin...on prend la meme que la date du debut dans tous les cas
              }
              // Recup Prive/Public
              if (trim($tabindex[$j])=='"'.trad("NOTEIMP_CHAMP_PRIVE").'"') {
                if (trim(str_replace('"','',$tabCtt[$j]))==trad("NOTEIMP_CHAMP_PRIVE_FAUX")) $rdPrive = 0;
                else $rdPrive = 1;
              }
              $ajout_ok = 1;
            } // fin boucle : regarde tous les champs
          } else { // C'est une note avec commentaires ou c'est une ligne de commentaire
            $ajout_en_cours = 1;
            for ($j=0;$j<count($tabCtt);$j++) {
              if ((substr($fcontents[$i],0,1)=='"') && (substr($fcontents[$i],1,1)!='"') && (substr($fcontents[$i],1,1)!=",")) {
                // C'est le debut de la note !
                // Recup du titre
                if ($tabindex[$j]=='"'.trad("NOTEIMP_CHAMP_OBJET").'"') {
                  $ztLibelle = str_replace('"','',$tabCtt[$j]);
                  $ztLibelle = str_replace("'","\'",$ztLibelle);
                }
                // Recup de la date de debut ou heure de debut (merci Microsoft pour les noms pourris !!)
                if ($tabindex[$j]=='"'.trad("NOTEIMP_CHAMP_DEBUT").'"') {
                  $pos = strpos($tabCtt[$j], "/");
                  if ($pos === false) {
                    // c'est l'heure de debut
                    list($heure_deb_h,$heure_deb_m_tmp,$heure_deb_sec) = explode(':',str_replace('"','',$tabCtt[$j]));
                    // minute de debut au format /100
                    if (($heure_deb_m_tmp>=0) && ($heure_deb_m_tmp<15)) $heure_deb_m = "00";
                    if (($heure_deb_m_tmp>=15) && ($heure_deb_m_tmp<30)) $heure_deb_m = "25";
                    if (($heure_deb_m_tmp>=30) && ($heure_deb_m_tmp<45)) $heure_deb_m = "50";
                    if (($heure_deb_m_tmp>=45) && ($heure_deb_m_tmp<=59)) $heure_deb_m = "75";
                    // On reforme les heures de deb et fin de la note au format Phenix
                    $zlHeureDebut = $heure_deb_h.".".$heure_deb_m;
                  } else {
                    // C'est la date de debut de l'evenement
                    list($jour_debut,$mois_debut,$an_debut) = explode("/",str_replace('"','',$tabCtt[$j]));
                    if (strlen($mois_debut)==1) $mois_debut = "0".$mois_debut;
                    if (strlen($jour_debut)==1) $jour_debut = "0".$jour_debut;
                    // On reforme la date au format Phenix
                    $Date_note = $jour_debut."/".$mois_debut."/".$an_debut;
                    $date_debut = $an_debut."-".$mois_debut."-".$jour_debut;
                  }
                }
                // Recup de la date de fin ou heure de fin
                if ($tabindex[$j]=='"'.trad("NOTEIMP_CHAMP_FIN").'"') {
                  $pos = strpos($tabCtt[$j], "/");
                  if ($pos === false) {
                    // c'est l'heure de fin
                    list($heure_fin_h,$heure_fin_m_tmp,$heure_fin_sec) = explode(':',str_replace('"','',$tabCtt[$j]));
                    // minute de debut au format /100
                    if (($heure_fin_m_tmp>=0) && ($heure_fin_m_tmp<15)) $heure_fin_m = "00";
                    if (($heure_fin_m_tmp>=15) && ($heure_fin_m_tmp<30)) $heure_fin_m = "25";
                    if (($heure_fin_m_tmp>=30) && ($heure_fin_m_tmp<45)) $heure_fin_m = "50";
                    if (($heure_fin_m_tmp>=45) && ($heure_fin_m_tmp<=59)) $heure_fin_m = "75";
                    // On reforme les heures de fin et fin de la note
                    $zlHeureFin = $heure_fin_h.".".$heure_fin_m;
                  }
                  // On ne s'occupe pas de la date de fin...on prend la meme que la date du debut dans tous les cas
                }
                // Recup description
                if ($tabindex[$j]=='"'.trad("NOTEIMP_CHAMP_DESCRIPTION").'"') {
                  $ztDetail = str_replace('"','',$tabCtt[$j]);
                }
                $num_champ_cours = count($tabCtt)-1;
              }
              if (count($tabCtt)==1) {
                // c'est un commentaire
                $ztDetail .= str_replace('""','"',$tabCtt[$j]);
              }
              if ((substr(trim($fcontents[$i]),strlen(trim($fcontents[$i]))-1,1)=='"') && (substr(trim($fcontents[$i]),strlen(trim($fcontents[$i]))-2,1))!='"') {
                // On regarde le dernier et l'avant dernier caractere de la ligne
                // C'est la fin de la note !
                // Recup description
                if ($tabindex[$j+$num_champ_cours]=='"'.trad("NOTEIMP_CHAMP_DESCRIPTION").'"') {
                  $ztDetail .= str_replace('"','',$tabCtt[$j]);
                  $ztDetail = str_replace("'","\'",$ztDetail);
                }
                // Recup Prive/Public
                if (trim($tabindex[$j+$num_champ_cours])=='"'.trad("NOTEIMP_CHAMP_PRIVE").'"') {
                  if (trim(str_replace('"','',$tabCtt[$j]))==trad("NOTEIMP_CHAMP_PRIVE_FAUX")) $rdPrive = 0;
                  else $rdPrive = 1;
                }
                $ajout_ok = 1;
              }
            } // fin boucle : regarde tous les champs
          }
          // Insertion dans la base !
          if ($ajout_ok==1) {
            list($jour_dem_deb,$mois_dem_deb,$an_dem_deb) = explode("/",$_POST['ztDateDeb']);
            $date_dem_deb = $an_dem_deb."-".$mois_dem_deb."-".$jour_dem_deb;
            list($jour_dem_fin,$mois_dem_fin,$an_dem_fin) = explode("/",$_POST['ztDateFin']);
            $date_dem_fin = $an_dem_fin."-".$mois_dem_fin."-".$jour_dem_fin;
            if (($date_debut>=$date_dem_deb) && ($date_debut<=$date_dem_fin)) {
              $id_age="";
              //Decalage des notes en fonction du fuseau horaire en utc
              list($heure_deb_utc,$heure_fin_utc,$dtCrt,$dtMdf,$date_deb_utc) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$dateJour,$date_debut,$zlHeureDebut,$zlHeureFin,$dtCrt,$dtMdf,1,1,1);
              $tabDate = explode("-",$date_deb_utc);
              $date_deb_utc = mktime(12,0,0,$tabDate[1],$tabDate[2],$tabDate[0]);

              // On regarde si la note existe deja
              $DB_CX->DbQuery("SELECT age_id FROM ${PREFIX_TABLE}agenda WHERE age_date='".date("Y-m-d",$date_deb_utc)."' AND age_heure_debut=$heure_deb_utc AND age_heure_fin=$heure_fin_utc AND age_libelle='$ztLibelle' AND age_util_id='$idUser'");
              if ($DB_CX->DbNumRows()) {
                $id_age = $DB_CX->DbResult(0,0);
                // On ne fait rien ! car la note existe
                $err =  "<P class=\"vert\"><B>".trad("NOTEIMP_MSG_IMPORT_ERREUR")."</B></P>";
                // Generation du rapport d'erreur
                addReport(false, $heure_deb_h.trad("NOTEIMP_H").$heure_deb_m_tmp, $heure_fin_h.trad("NOTEIMP_H").$heure_fin_m_tmp, $Date_note, str_replace("\'","'",$ztLibelle));
              } else {
                // Pas de repetition -> ajout de la note sans repet
                $zlPeriodicite = 1;
                $ckTypeNote = 2;
                $idAge = $id_age;
                $ztParticipant = $idUser;
                $ztDate = date("d/m/Y",$date_deb_utc);
                $zlHeureDebut = $heure_deb_utc;
                $zlHeureFin = $heure_fin_utc;
//                $zlCouleur = "";
                // date de creation de la note : Aujourd'hui
                $age_date_create = gmdate("Y-m-d")." ".gmdate('H').":".gmdate('i').":".gmdate('s');
                // date de modification de la note : Aujourd'hui
                $age_date_modif = gmdate("Y-m-d")." ".gmdate('H').":".gmdate('i').":".gmdate('s');
                // Generation du rapport et Enregistrement de la note si import valide
                addReport(true, $heure_deb_h."h".$heure_deb_m_tmp, $heure_fin_h."h".$heure_fin_m_tmp, $Date_note, str_replace("\'","'",$ztLibelle));
              }
              $ajout_en_cours = 0;
              $ajout_ok = 0;
              $num_champ_cours = 0 ;
            }
          }
        } // boucle pour regarder chaque ligne
      } // fin else traitement du fichier
    } //Fin le fichier existe
  }
  //--------------------------------------------------------------------------------
?>

<!-- DEBUT MODULE IMPORT NOTES -->
  <STYLE type="text/css">@import url(css/calendar_css.php?id=<?php echo $APPLI_STYLE; ?>);</STYLE>
  <SCRIPT type="text/javascript" src="inc/calendar.js"></SCRIPT>
<?php
  include("inc/calendar-setup.js.php");
  include("inc/checkdate.js.php");
?>
  <TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
  <TR>
    <TD height="28" class="sousMenu"><?php echo trad("NOTEIMP_TITRE_IMPORT");?></TD>
  </TR>
  </TABLE><BR>

<?php
  $j = date("d",$localTime);
  $m = date("m",$localTime);
  $an_deb = date("Y",$localTime)-5;
  $an_fin = date("Y",$localTime)+5;
  $date_deb = $j."/".$m."/".$an_deb;
  $date_fin = $j."/".$m."/".$an_fin;

  // Initialisation des variables globales a toutes les fonctions
  $nbImportOK = $nbImportKO = 0;
  $aDetailImportOK = $aDetailImportKO = array();

  if (!empty($ztFile)) {
    if ($zlTypeFichier=="vcal") {
      importVCAL($ztFile,$err);
    } elseif ($zlTypeFichier=="ics") {
      importICS($ztFile,$err);
    } elseif ($zlTypeFichier=="csv") {
      importCSV($ztFile,$err);
    } elseif ($zlTypeFichier=="dba") {
      importDBA($ztFile,$err);
    }
    // Rapport
    if ($err=="") {
      if (($nbImportOK+$nbImportKO)>0) {
        $err = "<P class=\"vert\"><B>".trad("NOTEIMP_MSG_LECTURE_OK")." : ".sprintf(trad("NOTEIMP_MSG_IMPORT_OK"), ($nbImportOK+$nbImportKO))."</B></P>";
      } else {
        $err = "<P class=\"rouge\"><B>".trad("NOTEIMP_MSG_LECTURE_OK")." : ".trad("NOTEIMP_MSG_IMPORT_KO")."</B></P>";
      }
    }
  }

  // Message d'erreur !
  if ($err != "") {
    echo ("  <TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"600\" align=\"center\">
  <TR bgcolor=\"".$CalepinFondMessage."\">
    <TD colspan=\"2\" align=\"center\" class=\"bordTLRB\">".$err."</TD>
  </TR>
  </TABLE><BR>\n\n");
  }
?>
  <FORM name="FormImport" method="POST" action="agenda.php" enctype="multipart/form-data">
    <INPUT type="hidden" name="sid" value="<?php echo $sid; ?>">
    <INPUT type="hidden" name="tcPlg" value="<?php echo $tcPlg; ?>">
    <INPUT type="hidden" name="tcMenu" value="<?php echo $tcMenu; ?>">
    <INPUT type="hidden" name="tcType" value="<?php echo $tcType; ?>">
    <INPUT type="hidden" name="sd" value="<?php echo $sd; ?>">
    <TABLE cellspacing="0" cellpadding="0" width="600" border="0">
    <TR>
      <TD align="center"><TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
        <TR bgcolor="<?php echo $CalepinFondMessage; ?>">
          <TD align="left" colspan="2" class="bordTLRB" style="padding-left:3px;padding-right:3px;">
            <?php echo trad("NOTEIMP_INTRO_TITRE");?>
            <UL>
              <LI><?php echo trad("NOTEIMP_INTRO_ICAL");?></LI>
              <LI><?php echo trad("NOTEIMP_INTRO_VCAL");?></LI>
              <LI><?php echo trad("NOTEIMP_INTRO_DBA");?></LI>
              <LI><?php echo trad("NOTEIMP_INTRO_CSV");?></LI>
            </UL>
            <U><?php echo trad("NOTEIMP_INTRO_PERIODE");?></U> :<BR><BR>
            <TABLE width="100%">
            <TR>
              <TD nowrap><B><?php echo trad("NOTEIMP_LIB_DATE_DEBUT");?> :</B>&nbsp;</TD>
              <TD><INPUT type="text" class="Texte" name="ztDateDeb" id="ztDateDeb" size=12 maxlength=10 value="<?php echo $date_deb; ?>" title="<?php echo trad("NOTEIMP_FORMAT_DATE");?>" onKeyPress="return onlyChar(event);">&nbsp;<INPUT type="button" id="btCal" value="..." class="Picklist" style="height:16px" title="<?php echo trad("NOTEIMP_AFFICHE_CALENDRIER");?>"></TD>
              <TD rowspan="2" align="center"><?php echo trad("NOTEIMP_INCHANGER_DATES");?></TD>
            </TR><TR>
              <TD><B><?php echo trad("NOTEIMP_LIB_DATE_FIN");?> :</B></TD>
              <TD><INPUT type="text" class="Texte" name="ztDateFin" id="ztDateFin" size=12 maxlength=10 value="<?php echo $date_fin; ?>" title="<?php echo trad("NOTEIMP_FORMAT_DATE");?>" onKeyPress="return onlyChar(event);">&nbsp;<INPUT type="button" id="btCal2" value="..." class="Picklist" style="height:16px" title="<?php echo trad("NOTEIMP_AFFICHE_CALENDRIER");?>"></TD>
            </TR>
            <TR>
              <TD colspan="3"><BR><BR><U><?php echo trad("MODIMPCL_CH_COULEUR");?></U> :<BR><BR>
              </TD>
            </TR>
            <TR>
              <TD colspan="2" nowrap><B><?php echo trad("MODIMPCL_CH_CL");?></B>&nbsp;&nbsp;
                <SELECT name="zlCouleur" style="background-color:<?php echo $enr['age_couleur'];?>;" onchange="javascript: changeCouleurListe(this,document.FormImport.ztCouleur);">
                <?php
                  $tabTemp    = array(trad("COMMUN_COUL_DEFAUT") => $AgendaFondNotePerso);
                  $tabCouleur = array_merge($tabTemp,getListeCouleur());
                  reset($tabCouleur);
                  while (list($key, $val) = each($tabCouleur)) {
                    $selected = ($val==$enr['age_couleur']) ? " selected" : "";
                    echo "      <OPTION style=\"background-color:".$val.";\" value=\"".$val."\"".$selected.">".$key."</OPTION>\n";
                  }
                ?>
                </SELECT>
              </TD>
             </TR>
            </TABLE><BR>
          </TD>
        </TR>
        <TR bgcolor="<?php echo $bgColor[1]; ?>">
          <TD width="55" class="tabIntitule">&nbsp;<B><?php echo trad("NOTEIMP_LIB_FICHIER");?> :</B></TD>
          <TD width="545" class="tabInput"><SELECT name="zlTypeFichier" size="1">
            <OPTION value="init" selected><?php echo trad("NOTEIMP_CHOIX_TYPE");?></OPTION>
            <OPTION value="ics"><?php echo trad("NOTEIMP_CHOIX_ICAL");?></OPTION>
            <OPTION value="vcal"><?php echo trad("NOTEIMP_CHOIX_VCAL");?></OPTION>
            <OPTION value="dba"><?php echo trad("NOTEIMP_CHOIX_DBA");?></OPTION>
            <OPTION value="csv"><?php echo trad("NOTEIMP_CHOIX_CSV");?></OPTION>
          </SELECT>&nbsp;<INPUT type="file" class="texte" name="ztFile" size="20"></TD>
        </TR>
        <TR>
          <TD colspan="2" align="center"><BR><INPUT type="button" class="bouton" name="btImporter" value="<?php echo trad("NOTEIMP_BT_IMPORTER");?>" title="<?php echo trad("NOTEIMP_BT_IMPORTER");?>" style="width:65px;" onclick="javascript:if (document.forms.FormImport.ztFile.value=='') alert('<?php echo trad("NOTEIMP_JS_CHOIX_FICHIER");?>'); else {if (document.forms.FormImport.zlTypeFichier.value=='init') alert('<?php echo trad("NOTEIMP_JS_CHOIX_TYPE");?>'); else document.forms.FormImport.submit();}">
          &nbsp;<INPUT type="button" class="bouton" name="btAnnule" value="<?php echo trad("NOTEIMP_BT_ANNULER");?>" onclick="javascript: btAnnul();"></TD>
        </TR>
        </TABLE>
<?php
  if (($nbImportOK+$nbImportKO)>0) {
    echo ("        <BR><BR>
        <TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"600\" align=\"center\">
        <TR bgcolor=\"#FFFFFF\">
          <TD colspan=\"2\" align=\"center\" class=\"bordTLRB\"><B>".trad("NOTEIMP_DETAIL_IMPORTS")." (".$nbImportOK."/".($nbImportOK+$nbImportKO).") :</B></TD>
        </TR>
        </TABLE>
        <TEXTAREA name=\"importOK\" rows=\"10\" style=\"width:600px;\" wrap=\"off\" readonly>");
    for ($i=1;$i<=$nbImportOK;$i++) {
      echo $aDetailImportOK[$i.'date_debut']." : ".$aDetailImportOK[$i.'libelle']." ".sprintf(trad("NOTEIMP_PLAGE"), $aDetailImportOK[$i.'heure_deb'], $aDetailImportOK[$i.'heure_fin'])."\r\n";
    }
    echo ("</TEXTAREA>
        <BR><BR>
        <TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"600\" align=\"center\">
        <TR bgcolor=\"#FFFFFF\">
          <TD colspan=\"2\" align=\"center\" class=\"bordTLRB\"><B>".trad("NOTEIMP_DETAIL_REJETS")." (".$nbImportKO."/".($nbImportOK+$nbImportKO).") :</B></TD>
        </TR>
        </TABLE>
        <TEXTAREA name=\"importKO\" rows=\"10\" style=\"width:600px;\" wrap=\"off\" readonly>");
    for ($i=1;$i<=$nbImportKO;$i++) {
      echo $aDetailImportKO[$i.'date_debut']." : ".$aDetailImportKO[$i.'libelle']." ".sprintf(trad("NOTEIMP_PLAGE"), $aDetailImportKO[$i.'heure_deb'], $aDetailImportKO[$i.'heure_fin'])."\r\n";
    }
    echo "</TEXTAREA>\n";
  }
?>
      </TD>
    </TR>
    </TABLE>
  </FORM>

  <SCRIPT type="text/javascript">
  <!--
    Calendar.setup( {
      inputField : "ztDateDeb",    // ID of the input field
      ifFormat   : "%d/%m/%Y",  // the date format
      button     : "btCal"      // ID of the button
    } );

    Calendar.setup( {
      inputField : "ztDateFin",    // ID of the input field
      ifFormat   : "%d/%m/%Y",  // the date format
      button     : "btCal2"      // ID of the button
    } );
  //-->
  </SCRIPT>
<!-- FIN MODULE IMPORT NOTES -->
<?php
  // Fonction gerant le suivi de l'import pour le rapport presente a l'utilisateur
  function addReport($importValide, $noteHeureDebut, $noteHeureFin, $noteDateDebut, $noteLibelle) {
    global $nbImportOK, $nbImportKO, $aDetailImportOK, $aDetailImportKO;
    if ($importValide) {
      // L'import est valide
      $nbImportOK++;
      $aDetailImportOK[$nbImportOK.'heure_deb'] = $noteHeureDebut;
      $aDetailImportOK[$nbImportOK.'heure_fin'] = $noteHeureFin;
      $aDetailImportOK[$nbImportOK.'date_debut'] = $noteDateDebut;
      $aDetailImportOK[$nbImportOK.'libelle'] = stripslashes($noteLibelle);
      // On declenche l'enregistrement de la note
      return import_cal();
    } else {
      // L'import n'est pas valide
      $nbImportKO++;
      $aDetailImportKO[$nbImportKO.'heure_deb'] = $noteHeureDebut;
      $aDetailImportKO[$nbImportKO.'heure_fin'] = $noteHeureFin;
      $aDetailImportKO[$nbImportKO.'date_debut'] = $noteDateDebut;
      $aDetailImportKO[$nbImportKO.'libelle'] = stripslashes($noteLibelle);
    }
  }
  //--------------------------------------------------------------------------------


  // Fonction d'insertion / modification des notes
  function import_cal(){
    global $DB_CX, $PREFIX_TABLE, $idUser;
    global $zlPeriodicite,$zlCouleur,$rdA,$zlA1,$zlA2,$zlA3,$zlA4,$zlA5,$rdPlage,$ztP,$zlP1,$zlP2,$zlP3,$rdQ,$ztQ,$zlH,$ztH,$rdM,$zlM1,$zlM2,$zlM3,$ztM,$ztDate,$ckTypeNote,$idAge,$tcMenu,$ztParticipant,$ztLibelle,$ztLieu,$ztDetail,$rdPrive,$zlHeureDebut,$zlHeureFin,$age_date_create,$age_date_modif,$vSemaineType,$rdRappel,$zlR1,$zlR2,$ckEmail,$rdDispo,$zlContactAssocie;
    global $tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$tzEte,$tzHiver;
    global $tsNote,$tsNoteUTC,$tsNow,$tsAlert,$sql,$idParticipant;

    $tabDateUTC = explode("/",$ztDate);
    $ztDateUTC = $tabDateUTC[2]."-".$tabDateUTC[1]."-".$tabDateUTC[0];

    // Conversion en Local en fonction du timezone
    $zlHeureDebutUTC = $zlHeureDebut;
    $zlHeureFinUTC = $zlHeureFin;
    list($zlHeureDebut,$zlHeureFin,$dateCrt,$dateModif,$dateNote) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$dateJour,$ztDateUTC,$zlHeureDebutUTC,$zlHeureFinUTC,$dateCrtUTC,$dateModifUTC,1,0,0);
    $date = explode("-",$dateNote);
    $tabDate = array($date[2],$date[1],$date[0]);

    $hNote = floor($zlHeureDebut);
    $mNote = ($zlHeureDebut*60)%60;
    $ztDate = $tabDate[2]."-".$tabDate[1]."-".$tabDate[0];
    $zlPeriodicite += 0;
    $zlContactAssocie += 0;
    $periode1 = $periode2 = $periode3 = $periode4 = 0;
    switch ($zlPeriodicite) {
      case 2 :
        if ($rdQ == 1) {
          $ztQ = $periode2 = (floor($ztQ)>0) ? floor($ztQ) : 1;
        } else {
          $rdQ = 2;
        }
        $periode1 = $rdQ;
        break;
      case 3 :
        $ztH = $periode1 = (floor($ztH)>0) ? floor($ztH) : 1;
        // Creation d'un tableau des jours de la semaine au format PHP ie. du Dimanche(0) au Samedi(6)
        $aSemaineType = array();
        for($i=0;$i<7;$i++) {
          $aSemaineType[$i] = substr($vSemaineType,$i,1);
        }
        // Stockage de la semaine type qui est utilisee pour creer la note
        $periode2 = $vSemaineType + 0; // On transforme la chaine en entier pour enlever les 0 devants
        break;
      case 4 :
        if ($rdM == 1) {
          $periode2 = $zlM1;
        } else {
          $rdM = 2; $periode2 = $zlM2; $periode3 = $zlM3;
        }
        $periode1 = $rdM;
        $ztM = $periode4 = (floor($ztM)>0) ? floor($ztM) : 1;
        break;
      case 5 :
        if ($rdA == 1) {
          $periode2 = $zlA1; $periode3 = $zlA2;
        } else {
          $rdA = 2; $periode2 = $zlA3; $periode3 = $zlA4; $periode4 = $zlA5;
        }
        $periode1 = $rdA;
        break;
      case 10 : break;
      default : $zlPeriodicite = 1;
    }
    if ($rdPlage == 2 && $zlPeriodicite > 1) {
      $nbOccurrence = 0;
      if (!checkdate($zlP2,$zlP1,$zlP3))
        $zlP1 = date("t", mktime(0,0,0,$zlP2,1,$zlP3));
      $dateMax = mktime($hNote,$mNote,0,$zlP2,$zlP1,$zlP3);
    } elseif ($zlPeriodicite > 1) {
      $ztP += 0;
      $rdPlage = 1;
      $nbOccurrence = min($ztP,99);
      $dateMax = 0;
    } else {
      $rdPlage = 1;
      $nbOccurrence = 10;
      $dateMax = 0;
    }
    if ($rdRappel != 2) {
      $zlR1 = 0;
      $zlR2 = 1;
      $ckEmail = 0;
    } elseif ($ckEmail != 1)
      $ckEmail = 0;
    if ($rdPrive != 1)
      $rdPrive = 0;
    if ($rdDispo != 1)
      $rdDispo = 0;
    if ($ckTypeNote!=3)
      $ckTypeNote=2;
    $hNoteUTC = floor($zlHeureDebutUTC);
    $mNoteUTC = ($zlHeureDebutUTC*60)%60;
    $tsNow = mktime(gmdate("H"),gmdate("i"),gmdate("s"),gmdate("n"),gmdate("j"),gmdate("Y"));
    $tsAlert = mktime(gmdate("H"),gmdate("i")+($zlR1*$zlR2),gmdate("s"),gmdate("n"),gmdate("j"),gmdate("Y"));
    $tsNoteUTC = mktime($hNoteUTC,$mNoteUTC,0,$tabDateUTC[1],$tabDateUTC[0],$tabDateUTC[2]);
    $endNote = ($tsNoteUTC > $tsNow) ? 0 : 1;
    $alert = ($tsNoteUTC > $tsAlert && $zlR1) ? 0 : 1;
    //Liste des personnes concernees
    $idParticipant = explode("+", $ztParticipant);

    if ($idAge) {
      $edit = ($zlPeriodicite > 1) ? "all" : "occ";
      if ($zlPeriodicite == 10)
        $zlPeriodicite = 1;
      $liste = "0";
      if ($edit!="occ") {
        $DB_CX->DbQuery("SELECT DISTINCT age_id, age_date_creation FROM ${PREFIX_TABLE}agenda WHERE age_mere_id=".$idAge);
        while ($enr = $DB_CX->DbNextRow()) {
          $liste .= ",".$enr['age_id'];
          $age_date_create = $enr['age_date_creation'];
        }
        $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda WHERE age_id IN (".$liste.")");
      }
      $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda_concerne WHERE aco_age_id IN (".$liste.",".$idAge.")");

      $sql = "UPDATE ${PREFIX_TABLE}agenda ";
      $sql .= "SET age_aty_id=".$ckTypeNote.",";
      $sql .= " age_date='".$ztDateUTC."',";
      $sql .= " age_heure_debut=".$zlHeureDebutUTC.",";
      $sql .= " age_heure_fin=".$zlHeureFinUTC.",";
      if ($edit!="occ") {
        $sql .= " age_ape_id=".$zlPeriodicite.",";
        $sql .= " age_periode1=".$periode1.",";
        $sql .= " age_periode2=".$periode2.",";
        $sql .= " age_periode3=".$periode3.",";
        $sql .= " age_periode4=".$periode4.",";
        $sql .= " age_plage=".$rdPlage.",";
        $sql .= " age_plage_duree=".($nbOccurrence + $dateMax).",";
      }
      $sql .= " age_libelle='".$ztLibelle."',";
      $sql .= " age_detail='".$ztDetail."',";
      $sql .= " age_rappel=".$zlR1.",";
      $sql .= " age_rappel_coeff=".$zlR2.",";
      $sql .= " age_email=".$ckEmail.",";
      //$sql .= " age_email_contact=".$ckEmailContact.",";
      $sql .= " age_prive=".$rdPrive.",";
      $sql .= " age_couleur='".$zlCouleur."',";
      $sql .= " age_nb_participant=".count($idParticipant).",";
      $sql .= " age_disponibilite=".$rdDispo.",";
      $sql .= " age_date_modif='".$age_date_modif."',";
      $sql .= " age_modificateur_id=".$idUser.",";
      $sql .= " age_lieu='".$ztLieu."',";
      $sql .= " age_cal_id=".$zlContactAssocie." ";
      $sql .= "WHERE age_id=".$idAge." AND age_util_id=".$idUser;
      $DB_CX->DbQuery($sql);
    } else {
      $sql = "INSERT INTO ${PREFIX_TABLE}agenda (age_mere_id,age_util_id,age_aty_id,age_date,age_heure_debut,age_heure_fin,age_ape_id, age_periode1, age_periode2, age_periode3, age_periode4, age_plage, age_plage_duree, age_libelle, age_detail, age_rappel, age_rappel_coeff, age_email, age_prive, age_couleur, age_nb_participant, age_createur_id, age_disponibilite, age_date_creation, age_date_modif, age_modificateur_id, age_lieu, age_cal_id) ";
      $sql .= "VALUES (0,".$idUser.",".$ckTypeNote.",'".$ztDateUTC."',".$zlHeureDebutUTC.",".$zlHeureFinUTC.",".$zlPeriodicite.",".$periode1.",".$periode2.",".$periode3.",".$periode4.",".$rdPlage.",".($nbOccurrence + $dateMax).",'".$ztLibelle."','".$ztDetail."',".$zlR1.",".$zlR2.",".$ckEmail.",".$rdPrive.",'".$zlCouleur."',".count($idParticipant).",".$idUser.",".$rdDispo.",'".$age_date_create."','".$age_date_modif."',".$idUser.",'".$ztLieu."',".$zlContactAssocie.")";
      $DB_CX->DbQuery($sql);
      $idAge = $DB_CX->DbInsertID();
    }

    // Enregistrement des personnes concernees
    for ($nb=0;$nb < count($idParticipant);$nb++)
      $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}agenda_concerne VALUES (".$idAge.",".$idParticipant[$nb].",".$alert.",".$endNote.")");

    if ($idAge) {
      // Requete generique
      $sql = "INSERT INTO ${PREFIX_TABLE}agenda (age_mere_id,age_util_id,age_aty_id,age_date,age_heure_debut,age_heure_fin,age_ape_id, age_periode1, age_periode2, age_periode3, age_periode4, age_plage, age_plage_duree, age_libelle, age_detail, age_rappel, age_rappel_coeff, age_email, age_prive, age_couleur, age_nb_participant, age_createur_id, age_disponibilite, age_date_creation, age_date_modif, age_modificateur_id, age_lieu, age_cal_id) ";
      $sql .= "VALUES (".$idAge.",".$idUser.",".$ckTypeNote.",'{theNewDate}',{theBeginHour},{theEndHour},".$zlPeriodicite.",".$periode1.",".$periode2.",".$periode3.",".$periode4.",".$rdPlage.",".($nbOccurrence + $dateMax).",'".$ztLibelle."','".$ztDetail."', ".$zlR1.",".$zlR2.",".$ckEmail.",".$rdPrive.",'".$zlCouleur."',".count($idParticipant).",".$idUser.",".$rdDispo.",'".$age_date_create."','".$age_date_modif."',".$idUser.",'".$ztLieu."',".$zlContactAssocie.")";
      if ($rdPlage == 1) {
        // Repetition en nombre d'occurrence
        switch ($zlPeriodicite) {
          case 2 : // Quotidienne
            if ($rdQ == 1) {
              for ($i=1;$i<$nbOccurrence;$i++) {
                $tsNote = mktime($hNote,$mNote,0,$tabDate[1],$tabDate[0]+($i*$ztQ),$tabDate[2]);
                // On calcule en utc pour la detection terminee et alerte
                $tsNoteUTC = mktime($hNoteUTC,$mNoteUTC,0,$tabDateUTC[1],$tabDateUTC[0]+($i*$ztQ),$tabDateUTC[2]);
                insertOccurrence();
              }
            } else {
              for ($i=1;$i<$nbOccurrence;$i++) {
                $tsNote = mktime($hNote,$mNote,0,$tabDate[1],$tabDate[0]+$i,$tabDate[2]);
                if (date("w",$tsNote)!=0 && date("w",$tsNote)!=6) {
                  // On calcule en utc pour la detection terminee et alerte
                  $tsNoteUTC = mktime($hNoteUTC,$mNoteUTC,0,$tabDateUTC[1],$tabDateUTC[0]+$i,$tabDateUTC[2]);
                  insertOccurrence();
                } else
                  $nbOccurrence++;
              }
            }
            break;
          case 3 : // Hebdomadaire
            $i=1; $nbAjout = 1;
            while ($nbAjout<$nbOccurrence) {
              $tsNote = mktime($hNote,$mNote,0,$tabDate[1],$tabDate[0]+$i,$tabDate[2]);
              if (date("w",$tsNote)==1 && $ztH>1) { // Les lundi on verifie les sauts de semaine
                $i = $i+(7*($ztH-1));
                $tsNote = mktime($hNote,$mNote,0,$tabDate[1],$tabDate[0]+$i,$tabDate[2]);
              }
              if ($aSemaineType[date("w",$tsNote)]==1) {
                // On calcule en utc pour la detection terminee et alerte
                $tsNoteUTC = mktime($hNoteUTC,$mNoteUTC,0,$tabDateUTC[1],$tabDateUTC[0]+$i,$tabDateUTC[2]);
                insertOccurrence();
                $nbAjout++;
              }
              $i++;
            }
            break;
          case 4 : // Mensuelle
            for ($i=1;$i<$nbOccurrence;$i++) {
              $jSelect = ($rdM == 1) ? $zlM1 : calcJour($zlM2,$zlM3,$tabDate[1]+($ztM*$i),$tabDate[2]);
              $tsNote = mktime($hNote,$mNote,0,$tabDate[1]+($ztM*$i),$jSelect,$tabDate[2]);
              // On calcule en utc pour la detection terminee et alerte
              $tsNoteUTC = mktime($hNoteUTC,$mNoteUTC,0,$tabDateUTC[1]+($ztM*$i),$jSelect,$tabDateUTC[2]);
              insertOccurrence();
            }
            break;
          case 5 : // Annuelle
            for ($i=1;$i<$nbOccurrence;$i++) {
              if ($rdA == 1) {
                $jSelect = $zlA1;
                if (!checkdate($zlA2,$jSelect,$tabDate[2]+$i))
                  $jSelect = date("t", mktime(0,0,0,$zlA2,1,$tabDate[2]+$i));
                $tsNote = mktime($hNote,$mNote,0,$zlA2,$jSelect,$tabDate[2]+$i);
                // On calcule en utc pour la detection terminee et alerte
                if (!checkdate($zlA2,$jSelect,$tabDateUTC[2]+$i))
                  $jSelect = date("t", mktime(0,0,0,$zlA2,1,$tabDateUTC[2]+$i));
                $tsNoteUTC = mktime($hNoteUTC,$mNoteUTC,0,$zlA2,$jSelect,$tabDateUTC[2]+$i);
              } else {
                $tsNote = mktime($hNote,$mNote,0,$zlA5,calcJour($zlA3,$zlA4,$zlA5,$tabDate[2]+$i),$tabDate[2]+$i);
                // On calcule en utc pour la detection terminee et alerte
                $tsNoteUTC = mktime($hNoteUTC,$mNoteUTC,0,$zlA5,calcJour($zlA3,$zlA4,$zlA5,$tabDateUTC[2]+$i),$tabDateUTC[2]+$i);
              }
              insertOccurrence();
            }
            break;
        }
      } else {
        // Repetition avec une date de fin
        $i = 1;
        switch ($zlPeriodicite) {
          case 2 : // Quotidienne
            if ($rdQ == 1) {
              $tsNote = mktime($hNote,$mNote,0,$tabDate[1],$tabDate[0]+($ztQ*$i++),$tabDate[2]);
              while ($tsNote <= $dateMax) {
                // On calcule en utc pour la detection terminee et alerte
                $tsNoteUTC = mktime($hNoteUTC,$mNoteUTC,0,$tabDateUTC[1],$tabDateUTC[0]+($ztQ*($i-1)),$tabDateUTC[2]);
                insertOccurrence();
                $tsNote = mktime($hNote,$mNote,0,$tabDate[1],$tabDate[0]+($ztQ*$i++),$tabDate[2]);
              }
            } else {
              $tsNote = mktime($hNote,$mNote,0,$tabDate[1],$tabDate[0]+($i++),$tabDate[2]);
              while ($tsNote <= $dateMax) {
                if (date("w",$tsNote)!=0 && date("w",$tsNote)!=6) {
                  // On calcule en utc pour la detection terminee et alerte
                  $tsNoteUTC = mktime($hNoteUTC,$mNoteUTC,0,$tabDateUTC[1],$tabDateUTC[0]+($i-1),$tabDateUTC[2]);
                  insertOccurrence();
                }
                $tsNote = mktime($hNote,$mNote,0,$tabDate[1],$tabDate[0]+($i++),$tabDate[2]);
              }
            }
            break;
          case 3 : // Hebdomadaire
            $tsNote = mktime($hNote,$mNote,0,$tabDate[1],$tabDate[0]+$i,$tabDate[2]);
            $stop = false;
            while ($tsNote <= $dateMax) {
              if (date("w",$tsNote)==1 && $ztH>1) { // Les lundi on verifie les sauts de semaine
                $i = $i+(7*($ztH-1));
                $tsNote = mktime($hNote,$mNote,0,$tabDate[1],$tabDate[0]+$i,$tabDate[2]);
                $stop = ($tsNote > $dateMax);
              }
              if (!$stop) {
                if ($aSemaineType[date("w",$tsNote)]==1) {
                  // On calcule en utc pour la detection terminee et alerte
                  $tsNoteUTC = mktime($hNoteUTC,$mNoteUTC,0,$tabDateUTC[1],$tabDateUTC[0]+$i,$tabDateUTC[2]);
                  insertOccurrence();
                }
                $tsNote = mktime($hNote,$mNote,0,$tabDate[1],$tabDate[0]+(++$i),$tabDate[2]);
              }
            }
            break;
          case 4 : // Mensuelle
            $jSelect = ($rdM == 1) ? $zlM1 : calcJour($zlM2,$zlM3,$tabDate[1]+$ztM,$tabDate[2]);
            $tsNote = mktime($hNote,$mNote,0,$tabDate[1]+$ztM,$jSelect,$tabDate[2]);
            while ($tsNote <= $dateMax) {
              // On calcule en utc pour la detection terminee et alerte
              $tsNoteUTC = mktime($hNoteUTC,$mNoteUTC,0,$tabDateUTC[1]+($ztM*$i),$jSelect,$tabDateUTC[2]);
              insertOccurrence();
              $i++;
              $jSelect = ($rdM == 1) ? $zlM1 : calcJour($zlM2,$zlM3,$tabDate[1]+($ztM*$i),$tabDate[2]);
              $tsNote = mktime($hNote,$mNote,0,$tabDate[1]+($ztM*$i),$jSelect,$tabDate[2]);
            }
            break;
          case 5 : // Annuelle
            if ($rdA == 1) {
              $jSelect = $zlA1;
              if (!checkdate($zlA2,$jSelect,$tabDate[2]+$i))
                $jSelect = date("t", mktime(0,0,0,$zlA2,1,$tabDate[2]+$i));
              $tsNote = mktime($hNote,$mNote,0,$zlA2,$jSelect,$tabDate[2]+($i++));
            } else
              $tsNote = mktime($hNote,$mNote,0,$zlA5,calcJour($zlA3,$zlA4,$zlA5,$tabDate[2]+$i),$tabDate[2]+($i++));
            while ($tsNote <= $dateMax) {
              // On calcule en utc pour la detection terminee et alerte
              if ($rdA == 1) {
                $jSelect = $zlA1;
                if (!checkdate($zlA2,$jSelect,$tabDateUTC[2]+($i-1)))
                  $jSelect = date("t", mktime(0,0,0,$zlA2,1,$tabDateUTC[2]+($i-1)));
                $tsNote = mktime($hNoteUTC,$mNoteUTC,0,$zlA2,$jSelect,$tabDateUTC[2]+($i-1));
              } else
                $tsNoteUTC = mktime($hNoteUTC,$mNoteUTC,0,$zlA5,calcJour($zlA3,$zlA4,$zlA5,$tabDateUTC[2]+$i),$tabDateUTC[2]+($i-1));
              insertOccurrence();
              if ($rdA == 1) {
                $jSelect = $zlA1;
                if (!checkdate($zlA2,$jSelect,$tabDate[2]+$i))
                  $jSelect = date("t", mktime(0,0,0,$zlA2,1,$tabDate[2]+$i));
                $tsNote = mktime($hNote,$mNote,0,$zlA2,$jSelect,$tabDate[2]+($i++));
              } else
                $tsNote = mktime($hNote,$mNote,0,$zlA5,calcJour($zlA3,$zlA4,$zlA5,$tabDate[2]+$i),$tabDate[2]+($i++));
            }
            break;
        }
      }
    }
    return $idAge;
  }
  //--------------------------------------------------------------------------------
?>
