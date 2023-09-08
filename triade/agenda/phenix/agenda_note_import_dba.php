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

  // Extraction depuis le fichier d'import (Import PalmDesktop  DBA)
  // Tester avec la version 4.1.4

  //Lecture String
  function ReadChaine ($Texte,$Pos) {
    list(,$StringLength) = unpack ('C', substr ($Texte, $Pos, 1));
    $Pos++;
    if ($StringLength == 255) {
      list(,$StringLength) = unpack ('S', substr ($Texte, $Pos, 2));;
    $Pos+=2;
    }
    return array ($Pos + $StringLength,substr ($Texte, $Pos, $StringLength));
  }

  //Lecture Long
  function ReadLong ($Texte,$Pos) {
    list(,$LongVal) = unpack("V",substr($Texte,$Pos,4));
    return array ($Pos+4,$LongVal);
  }

  //Lecture Short
  function ReadShort ($Texte,$Pos) {
    list(,$ShortVal) = unpack("S",substr($Texte,$Pos,2));
    return array ($Pos+2,$ShortVal);
  }

  //Lecture Chaine
  function ReadChar ($Texte,$Pos,$Nb,$aff=false) {
    return array ($Pos + $Nb,substr ($Texte, $Pos, $Nb));
  }


  function importDBA($fileName,&$err) {
    global $DB_CX, $PREFIX_TABLE, $idUser;
    global $zlPeriodicite,$zlCouleur,$rdA,$zlA1,$zlA2,$zlA3,$zlA4,$zlA5,$rdPlage,$ztP,$zlP1,$zlP2,$zlP3,$rdQ,$ztQ,$zlH,$ztH,$rdM,$zlM1,$zlM2,$zlM3,$ztM,$ztDate,$ckTypeNote,$idAge,$ztParticipant,$ztLibelle,$ztLieu,$ztDetail,$rdPrive,$zlHeureDebut,$zlHeureFin,$age_date_create,$vSemaineType,$rdRappel,$zlR1,$zlR2,$ckEmail,$rdDispo,$zlContactAssocie;
    global $tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$tzEte,$tzHiver;

    if (file_exists($fileName)) {
      $err = "";
      $nbNote = $nbImportOK = $nbImportKO = 0;
      // Lecture du fichier
      $DatebookFile = @file_get_contents($fileName);
      $fic_name  = $_FILES['ztFile']['name'];
      $fic_ext = explode(".",$fic_name);
      if ((strtolower($fic_ext[1])!="dba") and (strtolower($fic_ext[1])!="dat")) {
        $err = "<P class=\"rouge\"><B>".trad("NOTEIMP_ECHEC_DBA")."</B></P>";
      } else {

        $DatebookIndex=0;
        // Tag initial du fichier
        list($DatebookIndex,$TagId) = ReadLong ($DatebookFile,$DatebookIndex);
        // Type du Fichier
        list($DatebookIndex,$DatebookFileType)=ReadChaine ($DatebookFile,$DatebookIndex);
        // Nom et chemin du Fichier
        $DatebookIndex = 52;
        list($DatebookIndex,$DatebookFileName)=ReadChaine ($DatebookFile,$DatebookIndex);
        $DatebookIndex += 43;
        // Nombre de catégorie
        list($DatebookIndex,$NewCatId) = ReadLong ($DatebookFile,$DatebookIndex);
        list($DatebookIndex,$NbCat) = ReadLong ($DatebookFile,$DatebookIndex);
        // Lecture des catégories
        for ($i = 0; $i < $NbCat; $i++) {
          list($DatebookIndex,$NumCat) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$ColorCat) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$FlagCat) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$LTextCat[$NumCat]) = ReadChaine ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$STextCat) = ReadChaine ($DatebookFile,$DatebookIndex);
        }
          list($DatebookIndex,$ResourceID) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$FieldsPerRow) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$RecordIdPos) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$RdStatusPos) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$RdPlcPos) = ReadLong ($DatebookFile,$DatebookIndex);
        // Lecture de la liste de champ
        list($DatebookIndex,$FieldCount) = ReadShort ($DatebookFile,$DatebookIndex);
        for ($i=0; $i<$FieldCount; $i++)
             {list($DatebookIndex,$Poub) = ReadShort ($DatebookFile,$DatebookIndex);}

        // Nombre d'entree à lire
        list($DatebookIndex,$NbEntrees) = ReadLong ($DatebookFile,$DatebookIndex);
        $NbEntrees = $NbEntrees / $FieldCount;

        // Lecture des entrees
        for ($nbNote=0; $nbNote<$NbEntrees; $nbNote++) {
            // Initialisation des variables
            $rep_info=$note_summary=$note_description=$note_location=$note_class=$note_couleur=$note_rappel=$note_dispo=$note_rrule=$note_dtstart=$note_dtend=$age_date_create=$note_contact="";
            $note_categorie=$note_alarm=$note_statut=$note_jour=$note_statut=$note_alarm=$note_alarm_duree=$note_alarm_unit=$note_date_exclusion=$note_repete_mode=$note_repete_type="";
            $note_repete_date_fin=$note_repete_j_semaine=$note_repete_index_jour=$note_repete_masque_jour=$note_repete_index_semaine=$note_repete_numero_jour=$note_repete_index_mois="";
            $zlPeriodicite=$rdQ=$ztQ=$zlH=$ztH=$rdM=$zlM1=$zlM2=$zlM3=$ztM=$rdA=$zlA1=$zlA2=$zlA3=$zlA4=$zlA5=$rdPlage=$ztP=$zlP1=$zlP2=$zlP3="";
            $idAge=$zlCouleur=$ztDate=$ckTypeNote=$ztParticipant=$ztLibelle=$ztLieu=$ztDetail=$rdPrive=$zlHeureDebut=$zlHeureFin=$vSemaineType=$rdRappel=$zlR1=$zlR2=$ckEmail=$rdDispo=$zlContactAssocie="";

          list($DatebookIndex,$Type) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$Poub) = ReadLong ($DatebookFile,$DatebookIndex);
          // numero d'ID
          list($DatebookIndex,$Type) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$note_uid) = ReadLong ($DatebookFile,$DatebookIndex);
          // Statut
          list($DatebookIndex,$Type) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$note_statut) = ReadLong ($DatebookFile,$DatebookIndex);
          for ($i=0; $i<4; $i++) {
            list($DatebookIndex,$Poub) = ReadLong ($DatebookFile,$DatebookIndex);
          }
          // numero de categorie
          list($DatebookIndex,$Type) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$note_categorie) = ReadLong ($DatebookFile,$DatebookIndex);
          $note_couleur=$LTextCat[$note_categorie];
          //Note Privee
          list($DatebookIndex,$Type) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$note_class) = ReadLong ($DatebookFile,$DatebookIndex);
          for ($i=0; $i<8; $i++) {
            list($DatebookIndex,$Poub) = ReadLong ($DatebookFile,$DatebookIndex);
          }
          list($DatebookIndex,$Type) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$Poub) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$Poub)=ReadChaine ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$Type) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$Poub) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$Poub)=ReadChaine ($DatebookFile,$DatebookIndex);
          // StartTime
          list($DatebookIndex,$Type) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$note_dtstart) = ReadLong ($DatebookFile,$DatebookIndex);
          // EndTime
          list($DatebookIndex,$Type) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$note_dtend) = ReadLong ($DatebookFile,$DatebookIndex);
          // Titre
          list($DatebookIndex,$Type) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$Poub) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$note_summary)=ReadChaine ($DatebookFile,$DatebookIndex);
          $note_summary = str_replace("'","\'",$note_summary);
          $note_summary = str_replace("\n"," ",$note_summary);
          list($DatebookIndex,$Poub) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$Poub) = ReadLong ($DatebookFile,$DatebookIndex);
          // Note
          list($DatebookIndex,$Type) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$Poub) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$note_description)=ReadChaine ($DatebookFile,$DatebookIndex);
          $note_description = str_replace("'","\'",$note_description);
          $note_description = str_replace("\n","\r\n",$note_description);
          // Toute la journee
          list($DatebookIndex,$Type) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$note_jour) = ReadLong ($DatebookFile,$DatebookIndex);
          // Alarm
          list($DatebookIndex,$Type) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$note_alarm) = ReadLong ($DatebookFile,$DatebookIndex);
          // Duree alarme
          list($DatebookIndex,$Type) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$note_alarm_duree) = ReadLong ($DatebookFile,$DatebookIndex);
          // Unite alarme (0 Minutes; 1 Heures; 2 Jours)
          list($DatebookIndex,$Type) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$note_alarm_unit) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$Type) = ReadLong ($DatebookFile,$DatebookIndex);
          // Dates d'exclusion
          list($DatebookIndex,$Exclusion) = ReadShort ($DatebookFile,$DatebookIndex);
          for ($i=0; $i<$Exclusion; $i++) {
            list($DatebookIndex,$note_date_exclusion) = ReadLong ($DatebookFile,$DatebookIndex);
          }
          list($DatebookIndex,$EventFlag) = ReadShort ($DatebookFile,$DatebookIndex);
          if ($EventFlag == 65535) {
            list($DatebookIndex,$Poub) = ReadShort ($DatebookFile,$DatebookIndex);
            list($DatebookIndex,$Lenght_note_repete_mode) = ReadShort ($DatebookFile,$DatebookIndex);
            // Mode de repetition (CDayName;CDateOfMonth;CDateOfYear)
            list($DatebookIndex,$note_repete_mode)=ReadChar ($DatebookFile,$DatebookIndex,$Lenght_note_repete_mode);
          }
          if ($EventFlag != 0) {
            // Type de repetition (1 = Daily;2 = Weekly;3 = MonthlybyDay;4 = MonthlybyDate;5 = YearlybyDate;6 = YearlybyDay)<br>";
            list($DatebookIndex,$note_repete_type) = ReadLong ($DatebookFile,$DatebookIndex);
            // Intervalle de repetition
            list($DatebookIndex,$note_repete_Interval) = ReadLong ($DatebookFile,$DatebookIndex);
            // Date de fin
            list($DatebookIndex,$note_repete_date_fin) = ReadLong ($DatebookFile,$DatebookIndex);
            // Premier jour de la semaine
            list($DatebookIndex,$note_repete_j_semaine) = ReadLong ($DatebookFile,$DatebookIndex);
            if ($note_repete_type == 1 || $note_repete_type == 2 || $note_repete_type == 3) {
             // Index de jour
              list($DatebookIndex,$note_repete_index_jour) = ReadLong ($DatebookFile,$DatebookIndex);
            }
            if ($note_repete_type == 2) {
              // Masque de jour
              list($DatebookIndex,$note_repete_masque_jour) = ReadChar ($DatebookFile,$DatebookIndex,1);
            }
            if ($note_repete_type == 3) {
              // Index de semaine
              list($DatebookIndex,$note_repete_index_semaine) = ReadLong ($DatebookFile,$DatebookIndex);
            }
            if ($note_repete_type == 4 || $note_repete_type == 5) {
              // Numero du jour
              list($DatebookIndex,$note_repete_numero_jour) = ReadLong ($DatebookFile,$DatebookIndex);
            }
            if ($note_repete_type == 5) {
              // Index de mois
              list($DatebookIndex,$note_repete_index_mois) = ReadLong ($DatebookFile,$DatebookIndex);
            }
          }

          list($DatebookIndex,$Type) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$Poub) = ReadLong ($DatebookFile,$DatebookIndex);
          // Lieu
          list($DatebookIndex,$note_location)=ReadChaine ($DatebookFile,$DatebookIndex);
          $note_location = str_replace("'","\'",$note_location);
          $note_location = str_replace("\n"," ",$note_location);
          for ($i=0; $i<2; $i++) {
            list($DatebookIndex,$Poub) = ReadLong ($DatebookFile,$DatebookIndex);
          }
          list($DatebookIndex,$Type) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$Poub) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$Poub)=ReadChaine ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$Type) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$Poub) = ReadLong ($DatebookFile,$DatebookIndex);
          list($DatebookIndex,$Poub)=ReadChaine ($DatebookFile,$DatebookIndex);

          if ((dechex($note_statut & 128) != 80) && (dechex($note_statut & 4)!= 4)) {
            // on traite la note
            // On met la date au bon format pour pouvoir les comparer
            $heure_deb_tmp=date("H.i",$note_dtstart);
            $heure_deb_h=date("H",$note_dtstart);
            $heure_deb_tmp_m=date("i",$note_dtstart);
            $an_debut = date("Y",$note_dtstart);
            $mois_debut = date("m",$note_dtstart);
            $jour_debut = date("d",$note_dtstart);
            $date_debut = $an_debut."-".$mois_debut."-".$jour_debut;
            $heure_fin_tmp=date("H.i",$note_dtend);
            $heure_fin_h=date("H",$note_dtend);
            $heure_fin_tmp_m=date("i",$note_dtend);
            $an_fin = date("Y",$note_dtend);
            $mois_fin = date("m",$note_dtend);
            $jour_fin = date("d",$note_dtend);
            $date_fin = $an_fin."-".$mois_fin."-".$jour_fin;
            // minute de debut au format /100
            if (($heure_deb_tmp_m>=0) && ($heure_deb_tmp_m<15)) $heure_deb_m = "00";
            if (($heure_deb_tmp_m>=15) && ($heure_deb_tmp_m<30)) $heure_deb_m = "25";
            if (($heure_deb_tmp_m>=30) && ($heure_deb_tmp_m<45)) $heure_deb_m = "50";
            if (($heure_deb_tmp_m>=45) && ($heure_deb_tmp_m<=59)) $heure_deb_m = "75";
            // minute de fin au format /100
            if (($heure_fin_tmp_m>=0) && ($heure_fin_tmp_m<15)) $heure_fin_m = "00";
            if (($heure_fin_tmp_m>=15) && ($heure_fin_tmp_m<30)) $heure_fin_m = "25";
            if (($heure_fin_tmp_m>=30) && ($heure_fin_tmp_m<45)) $heure_fin_m = "50";
            if (($heure_fin_tmp_m>=45) && ($heure_fin_tmp_m<=59)) $heure_fin_m = "75";
            // On reforme les heures de deb et fin de la note
            $heure_deb = $heure_deb_h.".".$heure_deb_m;
            $heure_fin = $heure_fin_h.".".$heure_fin_m;
            //Decalage des notes en fonction du fuseau horaire en utc
            list($heure_deb,$heure_fin,$dtCrt,$dtMdf,$date_debut) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$dateJour,$date_debut,$heure_deb,$heure_fin,$dtCrt,$dtMdf,1,1,1);

            list($jour_dem_deb,$mois_dem_deb,$an_dem_deb) = explode("/",$_POST['ztDateDeb']);
            $date_dem_deb = $an_dem_deb."-".$mois_dem_deb."-".$jour_dem_deb;
            list($jour_dem_fin,$mois_dem_fin,$an_dem_fin) = explode("/",$_POST['ztDateFin']);
            $date_dem_fin = $an_dem_fin."-".$mois_dem_fin."-".$jour_dem_fin;
            if ((($heure_fin_tmp =="") && ($heure_deb_tmp =="")) || ($note_jour==1)) {
              // Jours consecutifs il faut creer une repetition avec le nombre de jours
              $heure_deb = "00.00";
              $heure_fin = "23.45";
              $ckTypeNote = 3;
            }
            // Date de creation de la note
            $age_date_create = gmdate("Y-m-d")." ".gmdate('H').":".gmdate('i').":".gmdate('s');
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
                if ($note_couleur!="")
                {
                  $DB_CX->DbQuery("SELECT cou_couleur FROM ${PREFIX_TABLE}couleurs WHERE cou_libelle ='$note_couleur'");
                  if ($DB_CX->DbNumRows()) {
                    $zlCouleur = $DB_CX->DbResult(0,0);
                  }
                }
                // rappel
                $rdRappel = ($note_alarm == 1 ) ? 2 : 1;
                $zlR1=$note_alarm_duree;
                $zlR2= ($note_alarm_unit==2 ) ? 1440 : (($note_alarm_unit==1 ) ? 60 : 1);
                $ckEmail=0;
                // disponibilite
                $rdDispo = 0;
                $ztLibelle = substr($note_summary,0,230);
                $ztLieu = substr($note_location,0,230);
                $ztDetail = $note_description;
                $ztDate = $jour_debut."/".$mois_debut."/".$an_debut;
                $zlHeureDebut = $heure_deb;
                $zlHeureFin = $heure_fin;
                $rdPrive = ($note_class == 1) ? 1 : 0;
                $zlContactAssocie = $note_contact + 0;
                $importValide = true; // Determine en fin de traitement si la note peut etre enregistree ou pas
                // Gestion Repetition
                if ($note_repete_type == "") {
                  // Pas de repetition
                  $zlPeriodicite = 1;
                } else {
                  //On reinitialise les variables de repetitions
                  $vFreqType=$vFreq=$vCount=$vUntil=$vInterval="";
                  $aByDay=$aByMonth=$aByMonthDay=array();
                  // On recupere le type de repetition
                  $vFreq = $note_repete_type;
                  if (($note_repete_type>0) || ($note_repete_type<6)) {
                    // -------------------------------- //
                    //    TRAITEMENT DES REPETITIONS    //
                    // -------------------------------- //
                    // Fin apres le
                    $rdPlage = 2;
                    $zlP1 = date("d",$note_repete_date_fin); // jour
                    $zlP2 = date("m",$note_repete_date_fin); //mois
                    $zlP3 = date("Y",$note_repete_date_fin); //annee
                    // ** REPETITION QUOTIDIENNE ** //
                    if ($vFreq=="1") {
                      $vFreqType = trad("NOTEIMP_QUOTIDIENNE");
                      $zlPeriodicite = 2;
                      $rdQ = 1;
                      $ztQ = $note_repete_Interval; // tous les X jours
                      if ($ztQ==0) {
                        // Format de repetition quotidienne non valide,  on transforme en tous les jours
                        $ztQ=1;
                      }
                    }
                    // ** REPETITION HEBDOMADAIRE ** //
                    elseif ($vFreq=="2") {
                      $vFreqType = trad("NOTEIMP_HEBDOMADAIRE");
                      if ($note_repete_masque_jour!="") {
                        $zlPeriodicite = 3;
                        $ztH = $note_repete_Interval; // toutes les X semaines
                        if ($ztH==0) {
                          // Format de repetition hebdomadaire non valide, on transforme en toutes les semaines
                          $ztH=1;
                        }
                        // On construit une chaine "1001100" (Dimanche a Samedi) qui sera interpretee comme la semaine type de l'utilisateur a l'enregistrement
                        $vSemaineType=strrev(substr("0000000".decbin(ord($note_repete_masque_jour)),-7));
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
                    elseif ($vFreq==3 || $vFreq==4) {
                      $vFreqType = trad("NOTEIMP_MENSUELLE");
                      $zlPeriodicite = 4;
                      $ztM = $note_repete_Interval; // tous les X mois
                      if ($ztM==0) {
                        // Format de repetition mensuelle non valide,  on transforme en tous les mois
                        $ztM=1;
                      }
                      if ($vFreq==4) { // Tous les X( date du jour) de chaque mois => ex Le 4 de chaque mois
                        $rdM = 1;
                        if ($note_repete_numero_jour!="") {
                          $zlM1 = $note_repete_numero_jour; // Si plusieurs jours on ne gere que le premier
                        } else {
                          // Il n'y a aucun jour de precise pour les repetitions
                          // On prend donc la jour de debut de la note "dtstart"
                          $zlM1 = $date_debut_jour + 0;
                        }
                      } elseif ($vFreq==3) { // Tous les premiers...dernier / nom du jour (MO,TU,WE,TH,FR,SA)  du mois => ex Le troisieme mardi du mois
                        $rdM = 2;
                        // Recuperationdu rang (premier...dernier)
                        $zlM2=$note_repete_index_semaine;
                        // On cherche le numero du jour 0:dimanche -> 6:samedi
                        $zlM3 = $note_repete_index_jour;
                      } else {
                        $vFreqType = trad("NOTEIMP_INCONNUE");
                        // La note est enregistree sans repetition
                        $zlPeriodicite = 1;
                      }
                    }
                    // ** REPETITION ANNUELLE ** //
                    elseif ($vFreq==5) {
                      $vFreqType = trad("NOTEIMP_ANNUELLE");
                      $zlPeriodicite = 5;
                      $rdA = 1;
                      $zlA1 = $note_repete_numero_jour; // jour de l'evenement a repeter
                      $zlA2 = $note_repete_index_mois+1; // mois de l'evenement a repeter
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

?>
