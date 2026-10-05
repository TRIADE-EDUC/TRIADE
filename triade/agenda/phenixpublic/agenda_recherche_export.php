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

  //require("inc/nocache.inc.php");
  require("inc/html.inc.php");
  include("inc/param.inc.php");
  if (isset($sid)) {
    include("inc/fonctions.inc.php");
  } else {
    exit;
  }

  $idUser = Session_ok($sid);

  include("lang/$APPLI_LANGUE.php");

  if (!empty($sql)) {
    $contenu = "";
    $clot = "\"";   // cloture de champ
    $crlf = "\r\n"; // fin de ligne
    $header = "text/csv"; // header de transmission
    // separateur de champ
    switch ($ztFormatExport) {
      case "csv-excel":
        $sep = ";";   // csv-excel
        break;
      case "txt-tab":
        $sep = "\t";   // txt-tab
        $header = "text/plain";
        break;
      default:
        $sep = ",";   // csv
    }
    // extension de fichier
    $fileExt = substr($ztFormatExport,0,3);

    // Recuperation des infos de timezone de l'utilisateur
    $DB_CX->DbQuery("SELECT tzn_libelle, tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver, util_format_heure FROM ${PREFIX_TABLE}utilisateur, ${PREFIX_TABLE}timezone WHERE util_id=".$idUser." AND tzn_zone=util_timezone");
    $tzLibelle = htmlentities($DB_CX->DbResult(0,"tzn_libelle"));
    $tzGmt = $DB_CX->DbResult(0,"tzn_gmt");
    $tzDateEte = $DB_CX->DbResult(0,"tzn_date_ete");
    $tzHeureEte = $DB_CX->DbResult(0,"tzn_heure_ete");
    $tzDateHiver = $DB_CX->DbResult(0,"tzn_date_hiver");
    $tzHeureHiver = $DB_CX->DbResult(0,"tzn_heure_hiver");
    $formatHeure = $DB_CX->DbResult(0,"util_format_heure")==12 ? "h:ia" : "H:i";
    // Calcul des bascules ete/hiver pour la date et l'heure locale
    $tzEte = calculBasculeDST($tzDateEte,gmdate("Y"),$tzHeureEte,$tzGmt,0);
    $tzHiver = calculBasculeDST($tzDateHiver,gmdate("Y"),$tzHeureHiver,$tzGmt,1);

    // Ajustement de la date en fonction du timezone
    $decalageHoraire = calculDecalageH($tzGmt,$tzEte,$tzHiver,mktime(gmdate("H"),gmdate("i"),gmdate("s"),gmdate("n"),gmdate("j"),gmdate("Y")));
    $localTime = mktime(gmdate("H")+floor($decalageHoraire),gmdate("i")+($decalageHoraire*60)%60,gmdate("s"),gmdate("n"),gmdate("j"),gmdate("Y"));

    // Nom du fichier d'export
    $fileName = "Export_recherche_".$ztFormatExport."_".date("Ymd-His",$localTime).".".$fileExt;
    
    // On reconstruit la requete
    $sqlExport = "SELECT DATE_FORMAT(age_date,'%e/%c/%Y') AS ageDate,age_heure_debut,age_heure_fin,age_util_id,CONCAT(".$FORMAT_NOM_UTIL.") AS nomCreateur,aco_termine,age_libelle,age_id,age_nb_participant,age_createur_id,age_aty_id,age_date_creation,age_date_modif,age_lieu,age_cal_id,CONCAT(".$FORMAT_NOM_CONTACT.") AS nomContact,cal_util_id,cal_partage,age_detail FROM ${PREFIX_TABLE}agenda LEFT JOIN ${PREFIX_TABLE}calepin ON cal_id=age_cal_id, ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}utilisateur WHERE age_aty_id!=1 AND age_id=aco_age_id ".html_entity_decode($sql);
    
    // On recupere les infos en base
    $DB_CX->DbQuery(stripslashes($sqlExport));
    if ($DB_CX->DbNumRows()) {
      // On extrait les champs selectionnes
      $tabChampsExport = explode(" ", $ztChampsExport); // le js a transforme les + en espaces

      // On ecrit les entetes des champs
      foreach ($tabChpExpRch as $key=>$value) {
        if (in_array($key,$tabChampsExport)) {
          $contenu .= $clot.$value.$clot.$sep;
        }
      }
      // On enleve le dernier ; en trop et on revient a la ligne
      $contenu = substr($contenu,0,-strlen($sep)).$crlf;
      
      // On traite chaque ligne
      while ($enr = $DB_CX->DbNextRow()) {
        // Transformation de la date de debut de la note en timestamp PHP
        list($j,$m,$a) = explode("/",$enr['ageDate']);
        $tsNoteUTC = mktime(0,0,0,$m,$j,$a);
        //Decalage des notes en fonction du fuseau horaire
        // Extraction de la version de Phenix
        $version = preg_replace(array("/\./","'([a-iA-I])'e"),array("","ord(strtolower('\\1'))-96"),$APPLI_VERSION);
        if ($version<100) {
          $version *= 10;
        }
        if ($version<550) {
          list($enr['age_heure_debut'],$enr['age_heure_fin'],$dateCreation,$dateModif) = decaleNote($tzGmt,$tzEte,$tzHiver,0,date("Y-m-d",$tsNoteUTC),$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif']);
          $tsNote = $tsNoteUTC;
          // Date de creation apres decalage horaire
          $ageDateCrt = explode(" ",$enr['age_date_creation']);
          if ($ageDateCrt[1]!="00:00:00") {
            $dtCrt = explode("-",$ageDateCrt[0]);
            $hrCrt = explode(":",$ageDateCrt[1]);
            $decalHC = calculDecalageH($tzGmt,$tzEte,$tzHiver,mktime($hrCrt[0],$hrCrt[1],$hrCrt[2],$dtCrt[1],$dtCrt[2],$dtCrt[0]));
            $enr['age_date_creation'] = date("Y-m-d H:i:s",mktime($hrCrt[0]+floor($decalHC),$hrCrt[1]+($decalHC*60)%60,$hrCrt[2],$dtCrt[1],$dtCrt[2],$dtCrt[0]));
          }
          // Date de modification apres decalage horaire
          $ageDateMdf = explode(" ",$enr['age_date_modif']);
          if ($ageDateMdf[1]!="00:00:00") {
            $dtMdf = explode("-",$ageDateMdf[0]);
            $hrMdf = explode(":",$ageDateMdf[1]);
            $decalHM = calculDecalageH($tzGmt,$tzEte,$tzHiver,mktime($hrMdf[0],$hrMdf[1],$hrMdf[2],$dtMdf[1],$dtMdf[2],$dtMdf[0]));
            $enr['age_date_modif'] = date("Y-m-d H:i:s",mktime($hrMdf[0]+floor($decalHM),$hrMdf[1]+($decalHM*60)%60,$hrMdf[2],$dtMdf[1],$dtMdf[2],$dtMdf[0]));
          }
          // On elimine des incoherences sur les anciennes notes
          if ($enr['age_date_creation'] > $enr['age_date_modif']) {
            $enr['age_date_modif'] = $enr['age_date_creation'];
          }
        } else {
          list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif'],$dateNote) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$dateJour,date("Y-m-d",$tsNoteUTC),$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif'],1,0,1);
          $tabDate = explode("-",$dateNote);
          $tsNote = mktime(0,0,0,$tabDate[1],$tabDate[2],$tabDate[0]);
        }
        $hDeb = ($enr['age_aty_id']==2) ? afficheHeure(floor($enr['age_heure_debut']),$enr['age_heure_debut'],$formatHeure) : "00:00";
        $hFin = ($enr['age_aty_id']==2) ? afficheHeure(floor($enr['age_heure_fin']),$enr['age_heure_fin'],$formatHeure) : "23:59";
        $statut = ($enr['aco_termine'] == 1) ? trad("RECHEXP_NOTE_TERMINE") : trad("RECHEXP_NOTE_ACTIVE");
        // Formatage du detail
        $enr['age_detail'] = str_replace(chr(13).chr(10)," ",$enr['age_detail']);
        if ($zlCaractere!="all" && strlen($enr['age_detail']) > $zlCaractere) {
          $enr['age_detail'] = substr($enr['age_detail'],0,$zlCaractere)." ...";
        }

        // Composition de la ligne
        $ptr = 0; // initialisation du pointeur
        $contenu .= (in_array($ptr++,$tabChampsExport)) ? $clot.date("d/m/Y",$tsNote).$clot.$sep : "";  // date
        $contenu .= (in_array($ptr++,$tabChampsExport)) ? $clot.$hDeb.$clot.$sep : "";  // heure debut
        $contenu .= (in_array($ptr++,$tabChampsExport)) ? $clot.$hFin.$clot.$sep : "";  // heure fin
        $contenu .= (in_array($ptr++,$tabChampsExport)) ? $clot.str_replace('"','""',$enr['nomCreateur']).$clot.$sep : "";  // proprietaire
        $contenu .= (in_array($ptr++,$tabChampsExport)) ? $clot.$statut.$clot.$sep : "";  // statut
        $contenu .= (in_array($ptr++,$tabChampsExport)) ? $clot.str_replace('"','""',$enr['age_libelle']).$clot.$sep : "";  // libelle
        $contenu .= (in_array($ptr++,$tabChampsExport)) ? $clot.str_replace('"','""',$enr['age_lieu']).$clot.$sep : "";  // lieu
        $contenu .= (in_array($ptr++,$tabChampsExport)) ? $clot.str_replace('"','""',$enr['nomContact']).$clot.$sep : "";  // contact
        $contenu .= (in_array($ptr++,$tabChampsExport)) ? $clot.str_replace('"','""',strip_tags(html_entity_decode($enr['age_detail']))).$clot.$sep : "";  // detail
        $contenu .= (in_array($ptr++,$tabChampsExport)) ? $clot.$enr['age_date_creation'].$clot.$sep : "";  // date creation
        $contenu .= (in_array($ptr++,$tabChampsExport)) ? $clot.$enr['age_date_modif'].$clot.$sep : "";  // date modification
        // On enleve le dernier ; en trop et on revient a la ligne
        $contenu = substr($contenu,0,-strlen($sep)).$crlf;
      }
      
      // On envoi le fichier
      header("Content-Type: ".$header."; charset=iso-8859-1; header=present;");
      header("Content-Disposition: attachment; filename=\"".$fileName."\"; size=".strlen($contenu));
      echo $contenu;
    }
  }

  // Fermeture BDD
  $DB_CX->DbDeconnect();
?>
