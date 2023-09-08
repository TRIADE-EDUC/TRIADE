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

// Extraction depuis le fichier d'import
  function importOutlook($fileName,&$err) {
    global $DB_CX, $PREFIX_TABLE, $idUser;
    $nbAjout = 0;
    $fcontents = @file($fileName);
    $fic_name  = $_FILES['ztFile']['name'];
    $fic_ext = explode(".",$fic_name);
    if ((strtolower($fic_ext[1])!="csv") && (strtolower($fic_ext[1])!="txt")) {
       $err = "<P class=\"rouge\"><B>".trad("CALIMP_ECHEC_CSV_PV")."</B></P>";
    } else {
      //Creation du groupe "Import" s'il n'existe pas
      $DB_CX->DbQuery("SELECT cgr_id FROM ${PREFIX_TABLE}calepin_groupe WHERE cgr_nom='".trad("CALIMP_GROUPE_IMPORT")."' AND cgr_util_id=".$idUser);
      if ($DB_CX->DbNumRows())
        $grpID = $DB_CX->DbResult(0,0);
      else {
        $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}calepin_groupe (cgr_util_id, cgr_nom) VALUES (".$idUser.",'".trad("CALIMP_GROUPE_IMPORT")."')");
        $grpID = $DB_CX->DbInsertID();
      }
      $tabindex = explode(";",$fcontents[0]);
      $nbChamp = count($tabindex);
      for ($i=1;$i<count($fcontents);$i++) {
        // Initialisation des variables
        $soc=$nom=$prenom=$adresse=$ville=$cpostal=$pays=$note=$anniv=$tel_work=$tel_home=$tel_cell=$tel_fax=$email_perso=$email_boulot=$aim=$icq=$msn=$yahoo="";
        $tabCtt = explode(";",$fcontents[$i]);
        for ($j=0;$j<$nbChamp;$j++) {
          // Recup du NOM + Prenom
          if (rtrim($tabindex[$j])==trad("CALIMP_CSV_PV_NOM_COMPLET")) {
            list($prenom1,$nom1) = explode(" ",$tabCtt[$j]);
            if ($nom=="") $nom=$nom1;
            if ($prenom=="") $prenom=$prenom1;
          }
          // Recup du NOM + Prenom
          if (rtrim($tabindex[$j])==trad("CALIMP_CSV_PV_NOM")) {
            if ($nom=="") $nom = $tabCtt[$j];
          }
          // Recup du prenom
          if (rtrim($tabindex[$j])==trad("CALIMP_CSV_PV_PRENOM")) {
            if ($prenom=="") $prenom = $tabCtt[$j];
          }
          // Recup de la societe
          if (rtrim($tabindex[$j])==trad("CALIMP_CSV_PV_SOCIETE")) {
            $soc = $tabCtt[$j];
          }
          // Recup adresse
          if (rtrim($tabindex[$j])==trad("CALIMP_CSV_PV_ADRESSE")) {
            $adresse = $tabCtt[$j];
          }
          // Recup ville
          if (rtrim($tabindex[$j])==trad("CALIMP_CSV_PV_VILLE")) {
            $ville = $tabCtt[$j];
          }
          // Recup code postal
          if (rtrim($tabindex[$j])==trad("CALIMP_CSV_PV_CP")) {
            $cpostal = $tabCtt[$j];
          }
          // Recup code postal
          if (rtrim($tabindex[$j])==trad("CALIMP_CSV_PV_PAYS")) {
            $pays = $tabCtt[$j];
          }
          // Recup note
          if (rtrim($tabindex[$j])==trad("CALIMP_CSV_PV_DIVERS")) {
            $note = $tabCtt[$j];
          }
        // Recup Tel Work
          if (rtrim($tabindex[$j])==trad("CALIMP_CSV_PV_TEL_PRO")) {
            $tel_work = ereg_replace("[ .-]","",$tabCtt[$j]);
          }
          // Recup Tel HOME
          if (rtrim($tabindex[$j])==trad("CALIMP_CSV_PV_TEL")) {
            $tel_home = ereg_replace("[ .-]","",$tabCtt[$j]);
          }
          // Recup Tel CELL
          if (rtrim($tabindex[$j])==trad("CALIMP_CSV_PV_TEL_MOBILE")) {
            $tel_cell = ereg_replace("[ .-]","",$tabCtt[$j]);
          }
          // Recup Tel FAX
          if ((rtrim($tabindex[$j])==trad("CALIMP_CSV_PV_FAX")) && ($tel_fax=="")) {
            $tel_fax = ereg_replace("[ .-]","",$tabCtt[$j]);
          }
          // Recup Tel FAX
          if ((rtrim($tabindex[$j])==trad("CALIMP_CSV_PV_FAX_PRO")) && ($tel_fax=="")) {
            $tel_fax = ereg_replace("[ .-]","",$tabCtt[$j]);
          }
          // Recup Email perso
          if (rtrim($tabindex[$j])==trad("CALIMP_CSV_PV_EMAIL")) {
            $email_perso = $tabCtt[$j];
          }
        }
  //      echo $soc.','.$nom_c.','.$prenom_c.','.$adresse.','.$cpostal.','.$ville.','.$pays.','.$tel_home.','.$tel_work.','.$tel_cell.','.$tel_fax.','.$email_perso.','.$icq.','.$note.','.$anniv.','.$aim.','.$msn.','.$yahoo.','.$email_boulot.'<br>';
        // On protege les apostrophes
        $adresse = ereg_replace("'","\'",$adresse);
        $note = ereg_replace("'","\'",$note);
        $num_contact += 1;
        $nom_c = rtrim(strtoupper($nom));
        $prenom_c = rtrim(ucwords(strtolower($prenom)));
        // Enregistrement du contact dans la Base
        if ($nom!="") {
          $nbAjout++;
          $DB_CX->DbQuery("SELECT * FROM ${PREFIX_TABLE}calepin WHERE cal_nom LIKE '%$nom_c%' AND cal_prenom LIKE '%$prenom_c%' AND cal_util_id='$idUser'");
          if ($DB_CX->DbNumRows()) {
            $enr = $DB_CX->DbNextRow();
            $SQL = "cal_nom='".$nom_c."'";
            if ($enr['cal_societe']=="") $SQL .= ",cal_societe='".$soc."'";
            if ($enr['cal_nom']=="") $SQL .= ",cal_nom='".$nom_c."'";
            if ($enr['cal_prenom']=="") $SQL .= ",cal_prenom='".$prenom_c."'";
            if ($enr['cal_adresse']=="") $SQL .= ",cal_adresse='".$adresse."'";
            if ($enr['cal_cp']=="") $SQL .= ",cal_cp='".$cpostal."'";
            if ($enr['cal_ville']=="") $SQL .= ",cal_ville='".$ville."'";
            if ($enr['cal_pays']=="") $SQL .= ",cal_pays='".$pays."'";
            if ($enr['cal_domicile']=="") $SQL .= ",cal_domicile='".$tel_home."'";
            if ($enr['cal_travail']=="") $SQL .= ",cal_travail='".$tel_work."'";
            if ($enr['cal_portable']=="") $SQL .= ",cal_portable='".$tel_cell."'";
            if ($enr['cal_fax']=="") $SQL .= ",cal_fax='".$tel_fax."'";
            if ($enr['cal_email']=="") $SQL .= ",cal_email='".$email_perso."'";
            if ($enr['cal_icq']=="") $SQL .= ",cal_icq='".$icq."'";
            if ($enr['cal_note']=="") $SQL .= ",cal_note='".$note."'";
            if ($enr['cal_date_naissance']=="") $SQL .= ",cal_date_naissance='".$anniv."'";
            if ($enr['cal_aim']=="") $SQL .= ",cal_aim='".$aim."'";
            if ($enr['cal_msn']=="") $SQL .= ",cal_msn='".$msn."'";
            if ($enr['cal_yahoo']=="") $SQL .= ",cal_yahoo='".$yahoo."'";
            if ($enr['cal_emailpro']=="") $SQL .= ",cal_emailpro='".$email_boulot."'";
            $SQL .= " WHERE cal_nom='".$nom_c."' AND cal_prenom='".$prenom_c."' AND cal_util_id='".$idUser."'";
            $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}calepin SET $SQL");
          } else {
            $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}calepin (cal_societe,cal_nom,cal_prenom,cal_adresse,cal_cp,cal_ville,cal_pays,cal_domicile,cal_travail,cal_portable,cal_fax,cal_email,cal_icq,cal_util_id,cal_note,cal_date_naissance,cal_aim,cal_msn,cal_yahoo,cal_emailpro) VALUES ('$soc','$nom_c','$prenom_c','$adresse','$cpostal','$ville','$pays','$tel_home','$tel_work','$tel_cell','$tel_fax','$email_perso','$icq','$idUser','$note','$anniv','$aim','$msn','$yahoo','$email_boulot')");
            if ($DB_CX->DbAffectedRows()>0) {
              $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}calepin_appartient (cap_cal_id, cap_cgr_id) VALUES (".$DB_CX->DbInsertID().",".$grpID.")");
            }
          }
        } else
          $err_nom = 1;
      }

      if (($nbAjout) && ($err_nom==0))
        $err =  "<P class=\"vert\"><B>".sprintf(trad("CALIMP_MSG_IMPORT_OK"), $nbAjout)."</B></P>";
      elseif (($nbAjout) && ($err_nom==1))
        $err = "<P class=\"rouge\"><B>".sprintf(trad("CALIMP_MSG_IMPORT_OK"), $nbAjout)."</B>".trad("CALIMP_MSG_IMPORT_ERREUR")."</B></P>";
      else
        $err = "<P class=\"rouge\"><B>".trad("CALIMP_MSG_IMPORT_KO")."</B></P>";
    }
  }
//--------------------------------------------------

// Extraction depuis le fichier d'import
  function importVCard($fileName,&$err) {
    global $DB_CX, $PREFIX_TABLE, $idUser;
    $nbAjout = 0;
    $num_contact = 1;
    $fcontents = @file($fileName);
    $fic_name  = $_FILES['ztFile']['name'];
    $fic_ext = explode(".",$fic_name);
    if (strtolower($fic_ext[1])!="vcf") {
      $err = "<P class=\"rouge\"><B>".trad("CALIMP_ECHEC_VCARD")."</B></P>";
    } else {
      //Creation du groupe "Import" s'il n'existe pas
      $DB_CX->DbQuery("SELECT cgr_id FROM ${PREFIX_TABLE}calepin_groupe WHERE cgr_nom='".trad("CALIMP_GROUPE_IMPORT")."' AND cgr_util_id=".$idUser);
      if ($DB_CX->DbNumRows())
        $grpID = $DB_CX->DbResult(0,0);
      else {
        $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}calepin_groupe (cgr_util_id, cgr_nom) VALUES (".$idUser.",'".trad("CALIMP_GROUPE_IMPORT")."')");
        $grpID = $DB_CX->DbInsertID();
      }
      for ($i=0;$i<count($fcontents);$i++) {
        $tabCtt = explode(":",$fcontents[$i],2);
        if ($tabCtt[0]=="BEGIN") {
          //Si BEGIN:VCARD n'est pas dans le fichier c'est que le fichier n'est pas correct !!
          if (!eregi("VCARD",$tabCtt[1])) {
            $err = "<P class=\"rouge\"><B>".trad("CALIMP_ECHEC_PB_VCARD")."</B></P>";
            break;
          } else {
            // Initialisation des variables
            $soc=$nom=$prenom=$adresse=$ville=$cpostal=$pays=$note=$anniv=$tel_work=$tel_home=$tel_cell=$tel_fax=$email_perso=$email_boulot=$aim=$icq=$msn=$yahoo="";
          }
        }
        // Recup du NOM + Prenom
        if ($tabCtt[0]=="N") {
          list($nom,$prenom) = explode(";",$tabCtt[1]);
        }
        // Recup de la societe
        if ($tabCtt[0]=="ORG") {
          $soc = $tabCtt[1];
          if ($nom == "")
            $nom = $soc;
        }
        // Recup adresse
        if (eregi("ADR;HOME",$tabCtt[0])) {
          list(,,$adresse,$ville,,$cpostal,$pays) = explode(";",$tabCtt[1]);
          // \r pour le retour a la ligne
          $adresse = str_replace("=0D=0A","\r\n",$adresse);
        }
        // Recup note
        if (eregi("NOTE",$tabCtt[0])) {
          if ($note=="") $note = $tabCtt[1];
          else $note = $note."\r\n".$tabCtt[1];
          // "\r" pour le retour a la ligne
          $note = str_replace("=0D=0A","\r\n",$note);
        }
        // Recup TITRE -> on met dans note
        if ($tabCtt[0]=="TITLE") {
          if ($note=="") $note = trad("CALIMP_VCARD_TITRE")." : ".str_replace("=0D=0A","\r\n",$tabCtt[1]);
          else $note = $note."\r\n".trad("CALIMP_VCARD_TITRE")." : ".str_replace("=0D=0A","\r\n",$tabCtt[1]);
        }
        // Recup Date de naissance
        if ($tabCtt[0]=="BDAY") {
          $anniv = $tabCtt[1];
          $an = substr($anniv,0,4);
          $mois = substr($anniv,4,2);
          $jour = substr($anniv,6,2);
          $anniv = $an."-".$mois."-".$jour;
        }
        // Recup Tel Work
        if (($tabCtt[0]=="TEL;WORK") || (eregi("TEL;PREF;WORK",$tabCtt[0]))) {
          $tel_work = ereg_replace("[ .-]","",$tabCtt[1]);
        }
        // Recup Tel HOME
        if ($tabCtt[0]=="TEL;HOME") {
          $tel_home = ereg_replace("[ .-]","",$tabCtt[1]);
        }
        // Recup Tel CELL
        if ($tabCtt[0]=="TEL;CELL") {
          $tel_cell = ereg_replace("[ .-]","",$tabCtt[1]);
        }
        // Recup Tel FAX
        if (($tabCtt[0]=="TEL;FAX") || (eregi("TEL;WORK;FAX",$tabCtt[0]))) {
          $tel_fax = ereg_replace("[ .-]","",$tabCtt[1]);
        }
        // Recup Email perso
        if ($tabCtt[0]=="EMAIL") {
          if (empty($email_perso)) {
            $email_perso = $tabCtt[1];
          } else {
            $email_boulot = $tabCtt[1];
          }
        }
        // Recup AIM
        if ($tabCtt[0]=="X-PALM-IM;AIM") {
          $aim = $tabCtt[1];
        }
        // Recup ICQ
        if ($tabCtt[0]=="X-PALM-IM;ICQ") {
          $icq = $tabCtt[1];
        }
        // Recup msn
        if ($tabCtt[0]=="X-PALM-IM;MSN") {
          $msn = $tabCtt[1];
        }
        // Recup Yahoo
        if ($tabCtt[0]=="X-PALM-IM;Yahoo") {
          $yahoo = $tabCtt[1];
        }
        // Fin des infos du contact
        if ($tabCtt[0]=="END") {
          // On protege les apostrophes
          $adresse = ereg_replace("'","\'",$adresse);
          $note = ereg_replace("'","\'",$note);
          $num_contact += 1;
          $nom_c = rtrim(strtoupper($nom));
          $prenom_c = rtrim(ucwords(strtolower($prenom)));
          // Enregistrement du contact dans la Base
          if ($nom!="") {
            $nbAjout++;
            $DB_CX->DbQuery("SELECT * FROM ${PREFIX_TABLE}calepin WHERE cal_nom LIKE '%$nom_c%' AND cal_prenom LIKE '%$prenom_c%' AND cal_util_id='$idUser'");
            if ($DB_CX->DbNumRows()) {
              $enr = $DB_CX->DbNextRow();
              $SQL = "cal_nom='".$nom_c."'";
              if ($enr['cal_societe']=="") $SQL .= ",cal_societe='".$soc."'";
              if ($enr['cal_nom']=="") $SQL .= ",cal_nom='".$nom_c."'";
              if ($enr['cal_prenom']=="") $SQL .= ",cal_prenom='".$prenom_c."'";
              if ($enr['cal_adresse']=="") $SQL .= ",cal_adresse='".$adresse."'";
              if ($enr['cal_cp']=="") $SQL .= ",cal_cp='".$cpostal."'";
              if ($enr['cal_ville']=="") $SQL .= ",cal_ville='".$ville."'";
              if ($enr['cal_pays']=="") $SQL .= ",cal_pays='".$pays."'";
              if ($enr['cal_domicile']=="") $SQL .= ",cal_domicile='".$tel_home."'";
              if ($enr['cal_travail']=="") $SQL .= ",cal_travail='".$tel_work."'";
              if ($enr['cal_portable']=="") $SQL .= ",cal_portable='".$tel_cell."'";
              if ($enr['cal_fax']=="") $SQL .= ",cal_fax='".$tel_fax."'";
              if ($enr['cal_email']=="") $SQL .= ",cal_email='".$email_perso."'";
              if ($enr['cal_icq']=="") $SQL .= ",cal_icq='".$icq."'";
              if ($enr['cal_note']=="") $SQL .= ",cal_note='".$note."'";
              if ($enr['cal_date_naissance']=="") $SQL .= ",cal_date_naissance='".$anniv."'";
              if ($enr['cal_aim']=="") $SQL .= ",cal_aim='".$aim."'";
              if ($enr['cal_msn']=="") $SQL .= ",cal_msn='".$msn."'";
              if ($enr['cal_yahoo']=="") $SQL .= ",cal_yahoo='".$yahoo."'";
              if ($enr['cal_emailpro']=="") $SQL .= ",cal_emailpro='".$email_boulot."'";
              $SQL .= " WHERE cal_nom='".$nom_c."' AND cal_prenom='".$prenom_c."' AND cal_util_id='".$idUser."'";
              $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}calepin SET $SQL");
            } else {
              $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}calepin (cal_societe,cal_nom,cal_prenom,cal_adresse,cal_cp,cal_ville,cal_pays,cal_domicile,cal_travail,cal_portable,cal_fax,cal_email,cal_icq,cal_util_id,cal_note,cal_date_naissance,cal_aim,cal_msn,cal_yahoo,cal_emailpro) VALUES ('$soc','$nom_c','$prenom_c','$adresse','$cpostal','$ville','$pays','$tel_home','$tel_work','$tel_cell','$tel_fax','$email_perso','$icq','$idUser','$note','$anniv','$aim','$msn','$yahoo','$email_boulot')");
              if ($DB_CX->DbAffectedRows()>0) {
                $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}calepin_appartient (cap_cal_id, cap_cgr_id) VALUES (".$DB_CX->DbInsertID().",".$grpID.")");
              }
            }
          } else
            $err_nom = 1;
        }
        $nbChamp = count($tabCtt);
      }
      if (($nbAjout) && ($err_nom==0))
        $err =  "<P class=\"vert\"><B>".sprintf(trad("CALIMP_MSG_IMPORT_OK"), $nbAjout)."</B></P>";
      elseif (($nbAjout) && ($err_nom==1))
        $err = "<P class=\"rouge\"><B>".sprintf(trad("CALIMP_MSG_IMPORT_OK"), $nbAjout)."</B>".trad("CALIMP_MSG_IMPORT_ERREUR")."</B></P>";
      else
        $err = "<P class=\"rouge\"><B>".trad("CALIMP_MSG_IMPORT_KO")."</B></P>";
    }
  }
//--------------------------------------------------

// Extraction depuis le fichier d'import
  function importVCard_palmdesktop($fileName,&$err) {
    global $DB_CX, $PREFIX_TABLE, $idUser;
    $nbAjout = 0;
    $fcontents = @file($fileName);
    $fic_name  = $_FILES['ztFile']['name'];
    $fic_ext = explode(".",$fic_name);
    $num_contact = 1;
    if (strtolower($fic_ext[1])!="vcf") {
      $err = "<P class=\"rouge\"><B>".trad("CALIMP_ECHEC_VCARD")."</B></P>";
    } else {
      //Creation du groupe "Import" s'il n'existe pas
      $DB_CX->DbQuery("SELECT cgr_id FROM ${PREFIX_TABLE}calepin_groupe WHERE cgr_nom='".trad("CALIMP_GROUPE_IMPORT")."' AND cgr_util_id=".$idUser);
      if ($DB_CX->DbNumRows())
        $grpID = $DB_CX->DbResult(0,0);
      else {
        $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}calepin_groupe (cgr_util_id, cgr_nom) VALUES (".$idUser.",'".trad("CALIMP_GROUPE_IMPORT")."')");
        $grpID = $DB_CX->DbInsertID();
      }
      for ($i=0;$i<count($fcontents);$i++) {
        $tabCtt = explode(":",$fcontents[$i],2);
        if ($tabCtt[0]=="BEGIN") {
          //Si BEGIN:VCARD n'est pas dans le fichier c'est que le fichier n'est pas correct !!
          if (!eregi("VCARD",$tabCtt[1])) {
            $err = "<P class=\"rouge\"><B>".trad("CALIMP_ECHEC_PB_VCARD")."</B></P>";
            break;
          } else {
            // Initialisation des variables
            $soc=$nom=$prenom=$adresse=$ville=$cpostal=$pays=$note=$anniv=$tel_work=$tel_home=$tel_cell=$tel_fax=$email_perso=$email_boulot=$aim=$icq=$msn=$yahoo="";
          }
        }
        // Recup du NOM + Prenom
        if ($tabCtt[0]=="N") {
          list($nom,$prenom) = explode(";",$tabCtt[1]);
        }
        // Recup de la societe
        if ($tabCtt[0]=="ORG") {
          $soc = $tabCtt[1];
          if ($nom == "")
            $nom = $soc;
        }
        // Recup adresse
        if (eregi("ADR;HOME",$tabCtt[0])) {
          list(,,$adresse,$ville,,$cpostal,$pays) = explode(";",$tabCtt[1]);
          // \r pour le retour a la ligne
          $adresse = str_replace("=0D=0A","\r\n",$adresse);
        }
        // Recup note
        if (eregi("NOTE",$tabCtt[0])) {
          $note = $tabCtt[1];
          // \r pour le retour a la ligne
          $note = str_replace("=0D=0A","\r\n",$note);
        }
        // Recup Date de naissance
        if ($tabCtt[0]=="BDAY") {
          $anniv = $tabCtt[1];
          $an = substr($anniv,0,4);
          $mois = substr($anniv,4,2);
          $jour = substr($anniv,6,2);
          $anniv = $an."-".$mois."-".$jour;
        }
        // Recup Tel Work
        if ($tabCtt[0]=="TEL;WORK") {
          $tel_work = ereg_replace("[ .-]","",$tabCtt[1]);
        }
        // Recup Tel HOME
        if ($tabCtt[0]=="TEL;HOME") {
          $tel_home = ereg_replace("[ .-]","",$tabCtt[1]);
        }
        // Recup Tel CELL
        if ($tabCtt[0]=="TEL;CELL") {
          $tel_cell = ereg_replace("[ .-]","",$tabCtt[1]);
        }
        // Recup Tel FAX
        if ($tabCtt[0]=="TEL;FAX") {
          $tel_fax = ereg_replace("[ .-]","",$tabCtt[1]);
        }
        // Recup Email perso
        if ($tabCtt[0]=="EMAIL") {
          if (empty($email_perso)) {
            $email_perso = $tabCtt[1];
          } else {
            $email_boulot = $tabCtt[1];
          }
        }
        // Les Palm Desktop inverse ces 4 champs !!!
        // Recup AIM
        if ($tabCtt[0]=="X-PALM-IM;MSN") {
          $aim = $tabCtt[1];
        }
        // Recup ICQ
        if ($tabCtt[0]=="X-PALM-IM; ") {
          $icq = $tabCtt[1];
        }
        // Recup msn
        if ($tabCtt[0]=="X-PALM-IM;Yahoo") {
          $msn = $tabCtt[1];
        }
        // Recup Yahoo
        if ($tabCtt[0]=="X-PALM-IM;ICQ") {
          $yahoo = $tabCtt[1];
        }
        // Fin des infos du contact
        if ($tabCtt[0]=="END") {
          // On protege les apostrophes
          $adresse = ereg_replace("'","\'",$adresse);
          $note = ereg_replace("'","\'",$note);
          $num_contact += 1;
          $nom_c = rtrim(strtoupper($nom));
          $prenom_c = rtrim(ucwords(strtolower($prenom)));
          // Enregistrement du contact dans la Base
          if ($nom!="") {
            $nbAjout++;
            $DB_CX->DbQuery("SELECT * FROM ${PREFIX_TABLE}calepin WHERE cal_nom LIKE '%$nom_c%' AND cal_prenom LIKE '%$prenom_c%' AND cal_util_id='$idUser'");
            if ($DB_CX->DbNumRows()) {
              $enr = $DB_CX->DbNextRow();
              $SQL = "cal_nom='".$nom_c."'";
              if ($enr['cal_societe']=="") $SQL .= ",cal_societe='".$soc."'";
              if ($enr['cal_nom']=="") $SQL .= ",cal_nom='".$nom_c."'";
              if ($enr['cal_prenom']=="") $SQL .= ",cal_prenom='".$prenom_c."'";
              if ($enr['cal_adresse']=="") $SQL .= ",cal_adresse='".$adresse."'";
              if ($enr['cal_cp']=="") $SQL .= ",cal_cp='".$cpostal."'";
              if ($enr['cal_ville']=="") $SQL .= ",cal_ville='".$ville."'";
              if ($enr['cal_pays']=="") $SQL .= ",cal_pays='".$pays."'";
              if ($enr['cal_domicile']=="") $SQL .= ",cal_domicile='".$tel_home."'";
              if ($enr['cal_travail']=="") $SQL .= ",cal_travail='".$tel_work."'";
              if ($enr['cal_portable']=="") $SQL .= ",cal_portable='".$tel_cell."'";
              if ($enr['cal_fax']=="") $SQL .= ",cal_fax='".$tel_fax."'";
              if ($enr['cal_email']=="") $SQL .= ",cal_email='".$email_perso."'";
              if ($enr['cal_icq']=="") $SQL .= ",cal_icq='".$icq."'";
              if ($enr['cal_note']=="") $SQL .= ",cal_note='".$note."'";
              if ($enr['cal_date_naissance']=="") $SQL .= ",cal_date_naissance='".$anniv."'";
              if ($enr['cal_aim']=="") $SQL .= ",cal_aim='".$aim."'";
              if ($enr['cal_msn']=="") $SQL .= ",cal_msn='".$msn."'";
              if ($enr['cal_yahoo']=="") $SQL .= ",cal_yahoo='".$yahoo."'";
              if ($enr['cal_emailpro']=="") $SQL .= ",cal_emailpro='".$email_boulot."'";
              $SQL .= " WHERE cal_nom='".$nom_c."' AND cal_prenom='".$prenom_c."' AND cal_util_id='".$idUser."'";
              $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}calepin SET $SQL");
            } else {
              $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}calepin (cal_societe,cal_nom,cal_prenom,cal_adresse,cal_cp,cal_ville,cal_pays,cal_domicile,cal_travail,cal_portable,cal_fax,cal_email,cal_icq,cal_util_id,cal_note,cal_date_naissance,cal_aim,cal_msn,cal_yahoo,cal_emailpro) VALUES ('$soc','$nom_c','$prenom_c','$adresse','$cpostal','$ville','$pays','$tel_home','$tel_work','$tel_cell','$tel_fax','$email_perso','$icq','$idUser','$note','$anniv','$aim','$msn','$yahoo','$email_boulot')");
              if ($DB_CX->DbAffectedRows()>0) {
                $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}calepin_appartient (cap_cal_id, cap_cgr_id) VALUES (".$DB_CX->DbInsertID().",".$grpID.")");
              }
            }
          } else
            $err_nom = 1;
        }
        $nbChamp = count($tabCtt);
      }
      if (($nbAjout) && ($err_nom==0))
        $err =  "<P class=\"vert\"><B>".sprintf(trad("CALIMP_MSG_IMPORT_OK"), $nbAjout)."</B></P>";
      elseif (($nbAjout) && ($err_nom==1))
        $err = "<P class=\"rouge\"><B>".sprintf(trad("CALIMP_MSG_IMPORT_OK"), $nbAjout)."</B>".trad("CALIMP_MSG_IMPORT_ERREUR")."</B></P>";
      else
        $err = "<P class=\"rouge\"><B>".trad("CALIMP_MSG_IMPORT_KO")."</B></P>";
    }
  }
//--------------------------------------------------

// Extraction depuis le fichier d'import
  function importCsv($fileName,&$err) {
    global $DB_CX, $PREFIX_TABLE, $idUser;
    $nbAjout = 0;
    $fcontents = @file($fileName);
    $fic_name  = $_FILES['ztFile']['name'];
    $fic_ext = explode(".",$fic_name);
    if ((strtolower($fic_ext[1])!="csv")) {
      $err = "<P class=\"rouge\"><B>".trad("CALIMP_ECHEC_CSV_V")."</B></P>";
    } else {
      //Creation du groupe "Import" s'il n'existe pas
      $DB_CX->DbQuery("SELECT cgr_id FROM ${PREFIX_TABLE}calepin_groupe WHERE cgr_nom='".trad("CALIMP_GROUPE_IMPORT")."' AND cgr_util_id=".$idUser);
      if ($DB_CX->DbNumRows())
        $grpID = $DB_CX->DbResult(0,0);
      else {
        $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}calepin_groupe (cgr_util_id, cgr_nom) VALUES (".$idUser.",'".trad("CALIMP_GROUPE_IMPORT")."')");
        $grpID = $DB_CX->DbInsertID();
      }
      // on lit le premier caractere pour voir si ce n'est pas un guillemet
      // si c'est le cas, on estime que c'est un format Outlook 2003 sinon c'est du thunderbird
      $car1 = substr($fcontents[0], 0, 1);
      if ($car1=='"') {
        // Traitement fichier csv type outlook 2003
        // on transforme les ,,,, en ,"","","",
        $fcontents[0] = str_replace(",,",',"",',$fcontents[0]);
        $fcontents[0] = str_replace(",,",',"",',$fcontents[0]);
        $longueur = strlen($fcontents[0])-4;
        $tabindex = explode('","',substr($fcontents[0], 1, $longueur));
        $nbChamp = count($tabindex);
        for ($i=1;$i<count($fcontents);$i++) {
          // Initialisation des variables
          $soc=$nom=$prenom=$adresse=$ville=$cpostal=$pays=$note=$anniv=$tel_work=$tel_home=$tel_cell=$tel_fax=$email_perso=$email_boulot=$aim=$icq=$msn=$yahoo=$adresse_b=$cpostal_b=$ville_b=$pays_b="";
          // on transforme les ,,,, en ,"","","",
          $fcontents[$i] = str_replace(",,",',"",',$fcontents[$i]);
          $fcontents[$i] = str_replace(",,",',"",',$fcontents[$i]);
          $tabCtt = explode('","',$fcontents[$i]);
          for ($j=0;$j<$nbChamp;$j++) {
            // Recup du NOM + Prenom
            if (rtrim($tabindex[$j])==trad("CALIMP_CSV_V_NOM")) {
              $nom = $tabCtt[$j];
            }
            // Recup du prenom
            if (rtrim($tabindex[$j])==trad("CALIMP_CSV_V_PRENOM")) {
              $prenom = $tabCtt[$j];
            }
            // Recup de la societe
            if (rtrim($tabindex[$j])==trad("CALIMP_CSV_V_SOCIETE")) {
              $soc = $tabCtt[$j];
            }
            // Recup adresse
            if (rtrim($tabindex[$j])==trad("CALIMP_CSV_V_RUE")) {
              $adresse = $tabCtt[$j];
            }
            // Recup ville
            if (rtrim($tabindex[$j])==trad("CALIMP_CSV_V_VILLE")) {
              $ville = $tabCtt[$j];
            }
            // Recup code postal
            if (rtrim($tabindex[$j])==trad("CALIMP_CSV_V_CP")) {
              $cpostal = $tabCtt[$j];
            }
            // Recup code postal
            if (rtrim($tabindex[$j])==trad("CALIMP_CSV_V_PAYS")) {
              $pays = $tabCtt[$j];
            }
            // Recup adresse
            if (rtrim($tabindex[$j])==trad("CALIMP_CSV_V_ADRESSE_PRO")) {
              $adresse_b = $tabCtt[$j];
            }
            // Recup ville
            if (rtrim($tabindex[$j])==trad("CALIMP_CSV_V_VILLE_PRO")) {
              $ville_b = $tabCtt[$j];
            }
            // Recup code postal
            if (rtrim($tabindex[$j])==trad("CALIMP_CSV_V_CP_PRO")) {
              $cpostal_b = $tabCtt[$j];
            }
            // Recup code postal
            if (rtrim($tabindex[$j])==trad("CALIMP_CSV_V_PAYS_PRO")) {
              $pays_b = $tabCtt[$j];
            }
            // Recup note
            if (rtrim($tabindex[$j])==trad("CALIMP_CSV_V_DIVERS")) {
              $note = $tabCtt[$j];
            }
            // Recup Tel Work
            if (rtrim($tabindex[$j])==trad("CALIMP_CSV_V_TEL_PRO")) {
              $tel_work = ereg_replace("[ .-]","",$tabCtt[$j]);
            }
            // Recup Tel HOME
            if (rtrim($tabindex[$j])==trad("CALIMP_CSV_V_TEL")) {
              $tel_home = ereg_replace("[ .-]","",$tabCtt[$j]);
            }
            // Recup Tel CELL
            if (rtrim($tabindex[$j])==trad("CALIMP_CSV_V_TEL_MOBILE")) {
              $tel_cell = ereg_replace("[ .-]","",$tabCtt[$j]);
            }
            // Recup Tel FAX
            if ((rtrim($tabindex[$j])==trad("CALIMP_CSV_V_FAX")) && ($tel_fax=="")) {
              $tel_fax = ereg_replace("[ .-]","",$tabCtt[$j]);
            }
            // Recup Tel FAX
            if ((rtrim($tabindex[$j])==trad("CALIMP_CSV_V_FAX_PRO")) && ($tel_fax=="")) {
              $tel_fax = ereg_replace("[ .-]","",$tabCtt[$j]);
            }
            // Recup Email perso
            if (rtrim($tabindex[$j])==trad("CALIMP_CSV_V_EMAIL")) {
              $email_perso = $tabCtt[$j];
            }
          }
//      echo $soc.','.$nom_c.','.$prenom_c.','.$adresse.','.$cpostal.','.$ville.','.$pays.','.$tel_home.','.$tel_work.','.$tel_cell.','.$tel_fax.','.$email_perso.','.$icq.','.$note.','.$anniv.','.$aim.','.$msn.','.$yahoo.','.$email_boulot.'<br>';
          // On protege les apostrophes
          $adresse = ereg_replace("'","\'",$adresse);
          $note = ereg_replace("'","\'",$note);
          $num_contact += 1;
          $nom_c = rtrim(strtoupper($nom));
          $prenom_c = rtrim(ucwords(strtolower($prenom)));
          // Enregistrement du contact dans la Base
          if ($nom!="") {
            $nbAjout++;
            if ($adresse.$cpostal.$ville.$pays == "") {
              $adresse = $adresse_b;
              $cpostal = $cpostal_b;
              $ville = $ville_b;
              $pays = $pays_b;
            }
            $DB_CX->DbQuery("SELECT * FROM ${PREFIX_TABLE}calepin WHERE cal_nom LIKE '%$nom_c%' AND cal_prenom LIKE '%$prenom_c%' AND cal_util_id='$idUser'");
            if ($DB_CX->DbNumRows()) {
              $enr = $DB_CX->DbNextRow();
              $SQL = "cal_nom='".$nom_c."'";
              if ($enr['cal_societe']=="") $SQL .= ",cal_societe='".$soc."'";
              if ($enr['cal_nom']=="") $SQL .= ",cal_nom='".$nom_c."'";
              if ($enr['cal_prenom']=="") $SQL .= ",cal_prenom='".$prenom_c."'";
              if ($enr['cal_adresse']=="") $SQL .= ",cal_adresse='".$adresse."'";
              if ($enr['cal_cp']=="") $SQL .= ",cal_cp='".$cpostal."'";
              if ($enr['cal_ville']=="") $SQL .= ",cal_ville='".$ville."'";
              if ($enr['cal_pays']=="") $SQL .= ",cal_pays='".$pays."'";
              if ($enr['cal_domicile']=="") $SQL .= ",cal_domicile='".$tel_home."'";
              if ($enr['cal_travail']=="") $SQL .= ",cal_travail='".$tel_work."'";
              if ($enr['cal_portable']=="") $SQL .= ",cal_portable='".$tel_cell."'";
              if ($enr['cal_fax']=="") $SQL .= ",cal_fax='".$tel_fax."'";
              if ($enr['cal_email']=="") $SQL .= ",cal_email='".$email_perso."'";
              if ($enr['cal_icq']=="") $SQL .= ",cal_icq='".$icq."'";
              if ($enr['cal_note']=="") $SQL .= ",cal_note='".$note."'";
              if ($enr['cal_date_naissance']=="") $SQL .= ",cal_date_naissance='".$anniv."'";
              if ($enr['cal_aim']=="") $SQL .= ",cal_aim='".$aim."'";
              if ($enr['cal_msn']=="") $SQL .= ",cal_msn='".$msn."'";
              if ($enr['cal_yahoo']=="") $SQL .= ",cal_yahoo='".$yahoo."'";
              if ($enr['cal_emailpro']=="") $SQL .= ",cal_emailpro='".$email_boulot."'";
              $SQL .= " WHERE cal_nom='".$nom_c."' AND cal_prenom='".$prenom_c."' AND cal_util_id='".$idUser."'";
              $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}calepin SET $SQL");
            } else {
              $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}calepin (cal_societe,cal_nom,cal_prenom,cal_adresse,cal_cp,cal_ville,cal_pays,cal_domicile,cal_travail,cal_portable,cal_fax,cal_email,cal_icq,cal_util_id,cal_note,cal_date_naissance,cal_aim,cal_msn,cal_yahoo,cal_emailpro) VALUES ('$soc','$nom_c','$prenom_c','$adresse','$cpostal','$ville','$pays','$tel_home','$tel_work','$tel_cell','$tel_fax','$email_perso','$icq','$idUser','$note','$anniv','$aim','$msn','$yahoo','$email_boulot')");
              if ($DB_CX->DbAffectedRows()>0) {
                $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}calepin_appartient (cap_cal_id, cap_cgr_id) VALUES (".$DB_CX->DbInsertID().",".$grpID.")");
              }
            }
          } else
            $err_nom = 1;
        }
      } else {
        // Traitement fichier csv type thunderbird
        for ($i=0;$i<count($fcontents);$i++) {
          // Initialisation des variables !
          $soc=$nom=$prenom=$prenom_nom=$adresse=$adresse2l=$ville=$cpostal=$pays=$note=$anniv=$tel_work=$tel_home=$tel_cell=$tel_fax=$email_perso=$email_boulot="";
          $tabCtt = explode(",",$fcontents[$i]);
          list($prenom,$nom,$prenom_nom,,$email_perso,$email_boulot,$tel_work,$tel_home,$tel_fax,,$tel_cell,$adresse,$adresse2l,$ville,,$cpostal,$pays,,,,,,,,,$soc,,,,,,,,,,$note) = explode(",",$fcontents[$i]);
//echo $soc.','.$nom_c.','.$prenom_c.','.$adresse.','.$cpostal.','.$ville.','.$pays.','.$tel_home.','.$tel_work.','.$tel_cell.','.$tel_fax.','.$email_perso.','.$icq.','.$note.','.$anniv.','.$aim.','.$msn.','.$yahoo.','.$email_boulot.'<br>';
          if (($nom=="") && ($prenom=="") && ($prenom_nom!="")) {
            list($prenom,$nom) = explode(" ",$prenom_nom);
            if ($nom=="") {
              $nom=$prenom_nom;
            }
          }
          if (($nom=="") && ($prenom!="") && ($prenom_nom!="")) {
            list(,$nom) = explode(" ",$prenom_nom);
            if ($nom=="") {
              $nom=$prenom_nom;
            }
          }
          if (($nom=="") && ($prenom!="") && ($prenom_nom=="")) {
            $nom = $prenom;
          }
          if ($adresse2l!="") {
            $adresse = $adresse." ".$adresse2l;
          }

          // On protege les apostrophes
          $adresse = ereg_replace("'","\'",$adresse);
          $note = ereg_replace("'","\'",$note);
          $nom = rtrim(strtoupper($nom));
          $prenom = rtrim(ucwords(strtolower($prenom)));
          $nom_c = $nom;
          $prenom_c = $prenom;
          $tel_home = ereg_replace("[ .-]","",$tel_home);
          $tel_work = ereg_replace("[ .-]","",$tel_work);
          $tel_cell = ereg_replace("[ .-]","",$tel_cell);
          $tel_fax = ereg_replace("[ .-]","",$tel_fax);
          if (($nom=="") && ($soc!=""))
            $nom = $soc;
          // Enregistrement du contact dans la Base
          if ($nom!="") {
            $nbAjout++;
            $DB_CX->DbQuery("SELECT * FROM ${PREFIX_TABLE}calepin WHERE cal_nom LIKE '%$nom_c%' AND cal_prenom LIKE '%$prenom_c%' AND cal_util_id='$idUser'");
            if ($DB_CX->DbNumRows()) {
              $enr = $DB_CX->DbNextRow();
              $SQL = "cal_nom='".$nom_c."'";
              if ($enr['cal_societe']=="") $SQL .= ",cal_societe='".$soc."'";
              if ($enr['cal_nom']=="") $SQL .= ",cal_nom='".$nom_c."'";
              if ($enr['cal_prenom']=="") $SQL .= ",cal_prenom='".$prenom_c."'";
              if ($enr['cal_adresse']=="") $SQL .= ",cal_adresse='".$adresse."'";
              if ($enr['cal_cp']=="") $SQL .= ",cal_cp='".$cpostal."'";
              if ($enr['cal_ville']=="") $SQL .= ",cal_ville='".$ville."'";
              if ($enr['cal_pays']=="") $SQL .= ",cal_pays='".$pays."'";
              if ($enr['cal_domicile']=="") $SQL .= ",cal_domicile='".$tel_home."'";
              if ($enr['cal_travail']=="") $SQL .= ",cal_travail='".$tel_work."'";
              if ($enr['cal_portable']=="") $SQL .= ",cal_portable='".$tel_cell."'";
              if ($enr['cal_fax']=="") $SQL .= ",cal_fax='".$tel_fax."'";
              if ($enr['cal_email']=="") $SQL .= ",cal_email='".$email_perso."'";
              if ($enr['cal_icq']=="") $SQL .= ",cal_icq='".$icq."'";
              if ($enr['cal_note']=="") $SQL .= ",cal_note='".$note."'";
              if ($enr['cal_date_naissance']=="") $SQL .= ",cal_date_naissance='".$anniv."'";
              if ($enr['cal_aim']=="") $SQL .= ",cal_aim='".$aim."'";
              if ($enr['cal_msn']=="") $SQL .= ",cal_msn='".$msn."'";
              if ($enr['cal_yahoo']=="") $SQL .= ",cal_yahoo='".$yahoo."'";
              if ($enr['cal_emailpro']=="") $SQL .= ",cal_emailpro='".$email_boulot."'";
              $SQL .= " WHERE cal_nom='".$nom_c."' AND cal_prenom='".$prenom_c."' AND cal_util_id='".$idUser."'";
              $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}calepin SET $SQL");
            } else {
              $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}calepin (cal_societe,cal_nom,cal_prenom,cal_adresse,cal_cp,cal_ville,cal_pays,cal_domicile,cal_travail,cal_portable,cal_fax,cal_email,cal_icq,cal_util_id,cal_note,cal_date_naissance,cal_aim,cal_msn,cal_yahoo,cal_emailpro) VALUES ('$soc','$nom_c','$prenom_c','$adresse','$cpostal','$ville','$pays','$tel_home','$tel_work','$tel_cell','$tel_fax','$email_perso','$icq','$idUser','$note','$anniv','$aim','$msn','$yahoo','$email_boulot')");
              if ($DB_CX->DbAffectedRows()>0) {
                $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}calepin_appartient (cap_cal_id, cap_cgr_id) VALUES (".$DB_CX->DbInsertID().",".$grpID.")");
              }
            }
          } else
            $err_nom = 1;
        }
      }
      if (($nbAjout) && ($err_nom==0))
        $err =  "<P class=\"vert\"><B>".sprintf(trad("CALIMP_MSG_IMPORT_OK"), $nbAjout)."</B></P>";
      elseif (($nbAjout) && ($err_nom==1))
        $err = "<P class=\"rouge\"><B>".sprintf(trad("CALIMP_MSG_IMPORT_OK"), $nbAjout)."</B>".trad("CALIMP_MSG_IMPORT_ERREUR")."</B></P>";
      else
        $err = "<P class=\"rouge\"><B>".trad("CALIMP_MSG_IMPORT_KO")."</B></P>";
    }
  }
//--------------------------------------------------

// Extraction depuis le fichier d'import
  function importLdif($fileName,&$err) {
    global $DB_CX, $PREFIX_TABLE, $idUser;
    $nbAjout = 0;
    $fcontents = @file($fileName);
    $fic_name  = $_FILES['ztFile']['name'];
    $fic_ext = explode(".",$fic_name);
    $num_contact = 1;
    $debut=0;
    if (strtolower($fic_ext[1])!="ldif") {
      $err = "<P class=\"rouge\"><B>".trad("CALIMP_ECHEC_LDIF")."</B></P>";
    } else {
      //Creation du groupe "Import" s'il n'existe pas
      $DB_CX->DbQuery("SELECT cgr_id FROM ${PREFIX_TABLE}calepin_groupe WHERE cgr_nom='".trad("CALIMP_GROUPE_IMPORT")."' AND cgr_util_id=".$idUser);
      if ($DB_CX->DbNumRows())
        $grpID = $DB_CX->DbResult(0,0);
      else {
        $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}calepin_groupe (cgr_util_id, cgr_nom) VALUES (".$idUser.",'".trad("CALIMP_GROUPE_IMPORT")."')");
        $grpID = $DB_CX->DbInsertID();
      }
      // On commence a parcourir le fichier
      for ($i=0;$i<count($fcontents);$i++) {
        $tabCtt = explode(":",$fcontents[$i],2);
        if ($tabCtt[0]=="dn") {
          if (($debut=="1") && ($err_list!="1")) {
            // On a deja fait un tour, on peut sauvegarder le contact (si ce n'est pas une liste)
            // On protege les apostrophes
            $adresse = ereg_replace("'","\'",$adresse);
            $num_contact += 1;
            $nom_c = rtrim(strtoupper($nom));
            $prenom_c = rtrim(ucwords(strtolower($prenom)));
            if (($nom_c=="") && ($soc!=""))
              $nom_c = $soc;
            // Enregistrement du contact dans la Base
            if ($nom_c!="") {
              $nbAjout++;
              $DB_CX->DbQuery("SELECT * FROM ${PREFIX_TABLE}calepin WHERE cal_nom LIKE '%$nom_c%' AND cal_prenom LIKE '%$prenom_c%' AND cal_util_id='$idUser'");
              if ($DB_CX->DbNumRows()) {
                $enr = $DB_CX->DbNextRow();
                $SQL = "cal_nom='".$nom_c."'";
                if ($enr['cal_societe']=="") $SQL .= ",cal_societe='".$soc."'";
                if ($enr['cal_nom']=="") $SQL .= ",cal_nom='".$nom_c."'";
                if ($enr['cal_prenom']=="") $SQL .= ",cal_prenom='".$prenom_c."'";
                if ($enr['cal_adresse']=="") $SQL .= ",cal_adresse='".$adresse."'";
                if ($enr['cal_cp']=="") $SQL .= ",cal_cp='".$cpostal."'";
                if ($enr['cal_ville']=="") $SQL .= ",cal_ville='".$ville."'";
                if ($enr['cal_pays']=="") $SQL .= ",cal_pays='".$pays."'";
                if ($enr['cal_domicile']=="") $SQL .= ",cal_domicile='".$tel_home."'";
                if ($enr['cal_travail']=="") $SQL .= ",cal_travail='".$tel_work."'";
                if ($enr['cal_portable']=="") $SQL .= ",cal_portable='".$tel_cell."'";
                if ($enr['cal_fax']=="") $SQL .= ",cal_fax='".$tel_fax."'";
                if ($enr['cal_email']=="") $SQL .= ",cal_email='".$email_perso."'";
                if ($enr['cal_icq']=="") $SQL .= ",cal_icq='".$icq."'";
                if ($enr['cal_note']=="") $SQL .= ",cal_note='".$note."'";
                if ($enr['cal_date_naissance']=="") $SQL .= ",cal_date_naissance='".$anniv."'";
                if ($enr['cal_aim']=="") $SQL .= ",cal_aim='".$aim."'";
                if ($enr['cal_msn']=="") $SQL .= ",cal_msn='".$msn."'";
                if ($enr['cal_yahoo']=="") $SQL .= ",cal_yahoo='".$yahoo."'";
                if ($enr['cal_emailpro']=="") $SQL .= ",cal_emailpro='".$email_boulot."'";
                $SQL .= " WHERE cal_nom='".$nom_c."' AND cal_prenom='".$prenom_c."' AND cal_util_id='".$idUser."'";
                $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}calepin SET $SQL");
              } else {
                $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}calepin (cal_societe,cal_nom,cal_prenom,cal_adresse,cal_cp,cal_ville,cal_pays,cal_domicile,cal_travail,cal_portable,cal_fax,cal_email,cal_icq,cal_util_id,cal_note,cal_date_naissance,cal_aim,cal_msn,cal_yahoo,cal_emailpro) VALUES ('$soc','$nom_c','$prenom_c','$adresse','$cpostal','$ville','$pays','$tel_home','$tel_work','$tel_cell','$tel_fax','$email_perso','$icq','$idUser','$note','$anniv','$aim','$msn','$yahoo','$email_boulot')");
                if ($DB_CX->DbAffectedRows()>0) {
                  $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}calepin_appartient (cap_cal_id, cap_cgr_id) VALUES (".$DB_CX->DbInsertID().",".$grpID.")");
                }
              }
            } else
              $err_nom = 1;
          }
          // On lit un nouveau contact !
          $err_list = 0;
          // Initialisation des variables
          $soc=$nom=$prenom=$adresse=$ville=$cpostal=$pays=$note=$anniv=$tel_work=$tel_home=$tel_cell=$tel_fax=$email_perso=$email_boulot=$aim=$icq=$msn=$yahoo="";
        }
        if ($tabCtt[0]=="objectclass") {
          if (ltrim(strtoupper($tabCtt[1])=="groupOfNames")) {
            $err_list = 1 ;
          }
        }
        // Recup du Prenom
        if ($tabCtt[0]=="givenName") {
          $prenom = ltrim(ucwords(strtolower($tabCtt[1])));
        }
        // Recup du NOM
        if ($tabCtt[0]=="sn") {
          $nom = ltrim(strtoupper($tabCtt[1]));
        }
        // tentative de recup du nom si erreur ci dessus
        if ($tabCtt[0]=="cn") {
          if ($nom=="") {
            list(,$nom) = explode (" ",ucwords(ltrim(strtoupper($tabCtt[1]))));
          }
        }
        // Recup du Mail
        if ($tabCtt[0]=="mail") {
          $email_perso = ltrim($tabCtt[1]);
        }
        // Recup du second Mail
        if ($tabCtt[0]=="mozillaSecondEmail") {
          $email_boulot = ltrim($tabCtt[1]);
        }
        // Recup du Tel boulot
        if ($tabCtt[0]=="telephoneNumber") {
          $tel_work = ereg_replace("[ .-]","",$tabCtt[1]);
        }
        // Recup du Tel maison
        if ($tabCtt[0]=="homePhone") {
          $tel_home = ereg_replace("[ .-]","",$tabCtt[1]);
        }
        // Recup du Tel maison
        if ($tabCtt[0]=="facsimileTelephoneNumber") {
          $tel_fax = ereg_replace("[ .-]","",$tabCtt[1]);
        }
        // Recup du Tel portable
        if ($tabCtt[0]=="mobile") {
          $tel_cell = ereg_replace("[ .-]","",$tabCtt[1]);
        }
        // Recup Adresse
        if ($tabCtt[0]=="homePostalAddress") {
          $adresse = ltrim($tabCtt[1]);
        }
        // Recup Adresse 2emeligne
        if ($tabCtt[0]=="mozillaHomePostalAddress2") {
          $adresse = $adresse." ".ltrim($tabCtt[1]);
        }
        // Recup Ville
        if ($tabCtt[0]=="mozillaHomeLocalityName") {
          $ville = ltrim($tabCtt[1]);
        }

        // Recup Adresse code postal
        if ($tabCtt[0]=="mozillaHomePostalCode") {
          $cpostal = ltrim($tabCtt[1]);
        }
        // Recup Adresse 2emeligne
        if ($tabCtt[0]=="mozillaHomeCountryName") {
          $pays = ltrim($tabCtt[1]);
        }
        // Recup Adresse 2emeligne
        if ($tabCtt[0]=="o") {
          $soc = ltrim($tabCtt[1]);
        }
        $debut = 1;
      }
      if (($nbAjout) && ($err_nom==0))
        $err =  "<P class=\"vert\"><B>".sprintf(trad("CALIMP_MSG_IMPORT_OK"), $nbAjout)."</B></P>";
      elseif (($nbAjout) && ($err_nom==1))
        $err = "<P class=\"rouge\"><B>".sprintf(trad("CALIMP_MSG_IMPORT_OK"), $nbAjout)."</B>".trad("CALIMP_MSG_IMPORT_ERREUR")."</B></P>";
      else
        $err = "<P class=\"rouge\"><B>".trad("CALIMP_MSG_IMPORT_KO")."</B></P>";
    }
  }
//--------------------------------------------------


// Affiche le formulaire pour importer un carnet Outlook Express et autre
  function aff_import() {
    global $ztFile,$sid,$sd,$tcMenu,$tcPlg,$bgColor,$CalepinFondMessage,$AgendaBordureTableau;

    if (!empty($ztFile)) {
      if ($_POST['fic_type']=="outlook") {
        importOutlook($ztFile,$err);
      }
      if ($_POST['fic_type']=="vcard") {
        importVCard($ztFile,$err);
      }
      if ($_POST['fic_type']=="vcardpd") {
        importVCard_palmdesktop($ztFile,$err);
      }
      if ($_POST['fic_type']=="csv") {
        importCsv($ztFile,$err);
      }
      if ($_POST['fic_type']=="ldif") {
        importLdif($ztFile,$err);
      }
    }
    echo ("  <TR>
    <TD align=\"center\"><TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">
      <TR bgcolor=\"".$CalepinFondMessage."\">
        <TD align=\"left\" colspan=\"2\" class=\"bordTLRB\" style=\"padding-left:3px;padding-right:3px;\">".trad("CALIMP_INTRO_TITRE")." :<BR>
            <UL>
              <LI>".trad("CALIMP_INTRO_CSV_PV")."</LI>
              <LI>".trad("CALIMP_INTRO_VCARD")."</LI>
              <LI>".trad("CALIMP_INTRO_CSV_V")."</LI>
              <LI>".trad("CALIMP_INTRO_LDIF")."</LI>
          </UL></TD>
      </TR>
    </TABLE></TD>
  </TR>
  <TR>
    <TD align=\"center\"><BR><TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">
      <TR bgcolor=\"".$CalepinFondMessage."\">
        <TD align=\"left\" colspan=\"2\" class=\"bordTLRB\" style=\"padding-left:3px;padding-right:3px;\">".trad("CALIMP_LIB_IMPORT_DEPUIS")." :
        <SELECT name=\"fic_type_choix\" onchange=\"aff(this.options[this.selectedIndex].value);\" size=\"1\">
          <OPTION value=\"init\" selected>".trad("CALIMP_CHOIX_TYPE")."</OPTION>
          <OPTION value=\"outlook\">".trad("CALIMP_CHOIX_CSV_PV")."</OPTION>
          <OPTION value=\"vcard\">".trad("CALIMP_CHOIX_VCARD_STD")."</OPTION>
          <OPTION value=\"vcardpd\">".trad("CALIMP_CHOIX_VCARD_PALM")."</OPTION>
          <OPTION value=\"csv\">".trad("CALIMP_CHOIX_CSV_V")."</OPTION>
          <OPTION value=\"ldif\">".trad("CALIMP_CHOIX_LDIF")."</OPTION>
        </SELECT></TD>
      </TR>
    </TABLE></TD>
  </TR>\n");
// Message d'erreur !
    if ($err != "") {
      echo ("  <TR>
    <TD align=\"center\"><BR><TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">
      <TR bgcolor=\"".$CalepinFondMessage."\">
        <TD colspan=\"2\" align=\"center\" class=\"bordTLRB\">".$err."</TD>
      </TR>
      <TR>
        <TD colspan=\"2\" align=\"center\">&nbsp;</TD>
      </TR>
    </TABLE></TD>
  </TR>\n");
    }
// Presentation import de contacts
    echo ("  <TR>
    <TD align=\"center\"><DIV id=\"outlook\" style=\"position:relative; z-index:1; text-align:center; visibility:visible; display:none\">
      <FORM method=\"POST\" action=\"?sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."&tcType="._TYPE_IMPORT_CONTACT."\" enctype=\"multipart/form-data\">
      <BR><TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">
      <TR bgcolor=\"".$CalepinFondMessage."\">
        <TD align=\"left\" colspan=\"2\" class=\"bordTLRB\" style=\"padding-left:3px;padding-right:3px;\">".trad("CALIMP_PROC_CSV_PV_TITRE")." :
          <UL>
            <LI>".trad("CALIMP_PROC_CSV_PV_L1")."</LI>
            <LI>".trad("CALIMP_PROC_CSV_PV_L2")."</LI>
            <LI>".trad("CALIMP_PROC_CSV_PV_L3")."</LI>
            <LI>".trad("CALIMP_PROC_CSV_PV_L4")."</LI>
            <LI>".trad("CALIMP_PROC_CSV_PV_L5")."</LI>
        </UL></TD>
      </TR>
      <TR bgcolor=\"".$bgColor[1]."\">
        <TD width=\"80\" class=\"tabIntitule\">&nbsp;<B>".trad("CALIMP_LIB_FICHIER_CSV")."</B></TD>
        <TD width=\"385\" class=\"tabInput\"><INPUT type=\"file\" class=\"texte\" name=\"ztFile\" size=40></TD>
      </TR>
      </TABLE>
      <BR><INPUT type=\"submit\" class=\"bouton\" name=\"type\" value=\"".trad("CALIMP_BT_IMPORTER")."\">&nbsp;&nbsp;&nbsp;<INPUT type=\"button\" class=\"bouton\" name=\"btAnnule\" value=\"".trad("CALIMP_BT_ANNULER")."\" onclick=\"javascript: btAnnul();\"><INPUT type=\"hidden\" name=\"fic_type\" value=\"outlook\">
      </FORM>
    </DIV>\n");

// format VCard
    echo ("    <DIV id=\"vcard\" style=\"position:relative; z-index:1; text-align:center; visibility:visible; display:none\">
      <FORM method=\"POST\" action=\"?sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."&tcType="._TYPE_IMPORT_CONTACT."\" enctype=\"multipart/form-data\">
      <BR><TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">
      <TR bgcolor=\"".$CalepinFondMessage."\">
        <TD align=\"left\" colspan=\"2\" class=\"bordTLRB\" style=\"padding-left:3px;padding-right:3px;\">".trad("CALIMP_PROC_VCARD_STD_TITRE")." : <BR>
            <UL>
              <LI>".trad("CALIMP_PROC_VCARD_STD_L1")."</LI>
              <LI>".trad("CALIMP_PROC_VCARD_STD_L2")."</LI>
              <LI>".trad("CALIMP_PROC_VCARD_STD_L3")."</LI>
          </UL></TD>
      </TR>
      <TR bgcolor=\"".$bgColor[1]."\">
        <TD width=\"80\" class=\"tabIntitule\">&nbsp;<B>".trad("CALIMP_LIB_FICHIER_VCARD")."</B></TD>
        <TD width=\"385\" class=\"tabInput\"><INPUT type=\"file\" class=\"texte\" name=\"ztFile\" size=40></TD>
      </TR>
      </TABLE>
      <BR><INPUT type=\"submit\" class=\"bouton\" name=\"type\" value=\"".trad("CALIMP_BT_IMPORTER")."\">&nbsp;&nbsp;&nbsp;<INPUT type=\"button\" class=\"bouton\" name=\"btAnnule\" value=\"".trad("CALIMP_BT_ANNULER")."\" onclick=\"javascript: btAnnul();\"><INPUT type=\"hidden\" name=\"fic_type\" value=\"vcard\">
      </FORM>
    </DIV>\n");
// format VCard-PalmDesktop
    echo ("    <DIV id=\"vcardpd\" style=\"position:relative; z-index:1; text-align:center; visibility:visible; display:none\">
      <FORM method=\"POST\" action=\"?sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."&tcType="._TYPE_IMPORT_CONTACT."\" enctype=\"multipart/form-data\">
      <BR><TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">
      <TR bgcolor=\"".$CalepinFondMessage."\">
        <TD align=\"left\" colspan=\"2\" class=\"bordTLRB\" style=\"padding-left:3px;padding-right:3px;\">".trad("CALIMP_PROC_VCARD_PALM_TITRE")." : <BR>
            <UL>
              <LI>".trad("CALIMP_PROC_VCARD_PALM_L1")."</LI>
          </UL></TD>
      </TR>
      <TR bgcolor=\"".$bgColor[1]."\">
        <TD width=\"80\" class=\"tabIntitule\">&nbsp;<B>".trad("CALIMP_LIB_FICHIER_VCARD")."</B></TD>
        <TD width=\"385\" class=\"tabInput\"><INPUT type=\"file\" class=\"texte\" name=\"ztFile\" size=40></TD>
      </TR>
      </TABLE>
      <BR><INPUT type=\"submit\" class=\"bouton\" name=\"type\" value=\"".trad("CALIMP_BT_IMPORTER")."\">&nbsp;&nbsp;&nbsp;<INPUT type=\"button\" class=\"bouton\" name=\"btAnnule\" value=\"".trad("CALIMP_BT_ANNULER")."\" onclick=\"javascript: btAnnul();\"><INPUT type=\"hidden\" name=\"fic_type\" value=\"vcardpd\">
      </FORM>
    </DIV>\n");
// Format CSV (virgules)
    echo ("    <DIV id=\"csv\" style=\"position:relative; z-index:1; text-align:center; visibility:visible; display:none\">
      <FORM method=\"POST\" action=\"?sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."&tcType="._TYPE_IMPORT_CONTACT."\" enctype=\"multipart/form-data\">
      <BR><TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">
      <TR bgcolor=\"".$CalepinFondMessage."\">
        <TD align=\"left\" colspan=\"2\" class=\"bordTLRB\" style=\"padding-left:3px;padding-right:3px;\">".trad("CALIMP_PROC_CSV_V_TITRE")."<BR>
            <UL>
              <LI>".trad("CALIMP_PROC_CSV_V_L1")."</LI>
              <LI>".trad("CALIMP_PROC_CSV_V_L2")."</LI>
              <LI>".trad("CALIMP_PROC_CSV_V_L3")."</LI>
              <LI>".trad("CALIMP_PROC_CSV_V_L4")."</LI>
          </UL></TD>
      </TR>
      <TR bgcolor=\"".$bgColor[1]."\">
        <TD width=\"80\" class=\"tabIntitule\">&nbsp;<B>".trad("CALIMP_LIB_FICHIER_CSV")."</B></TD>
        <TD width=\"385\" class=\"tabInput\"><INPUT type=\"file\" class=\"texte\" name=\"ztFile\" size=40></TD>
      </TR>
      </TABLE>
      <BR><INPUT type=\"submit\" class=\"bouton\" name=\"type\" value=\"".trad("CALIMP_BT_IMPORTER")."\">&nbsp;&nbsp;&nbsp;<INPUT type=\"button\" class=\"bouton\" name=\"btAnnule\" value=\"".trad("CALIMP_BT_ANNULER")."\" onclick=\"javascript: btAnnul();\"><INPUT type=\"hidden\" name=\"fic_type\" value=\"csv\">
      </FORM>
    </DIV>\n");
// Format ldif
    echo ("    <DIV id=\"ldif\" style=\"position:relative; z-index:1; text-align:center; visibility:visible; display:none\">
      <FORM method=\"POST\" action=\"?sid=".$sid."&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."&tcType="._TYPE_IMPORT_CONTACT."\" enctype=\"multipart/form-data\">
      <BR><TABLE cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">
      <TR bgcolor=\"".$CalepinFondMessage."\">
        <TD align=\"left\" colspan=\"2\" class=\"bordTLRB\" style=\"padding-left:3px;padding-right:3px;\">".trad("CALIMP_PROC_LDIF_TITRE")."<BR>
            <UL>
              <LI>".trad("CALIMP_PROC_LDIF_L1")."</LI>
              <LI>".trad("CALIMP_PROC_LDIF_L2")."</LI>
              <LI>".trad("CALIMP_PROC_LDIF_L3")."</LI>
              <LI>".trad("CALIMP_PROC_LDIF_L4")."</LI>
          </UL></TD>
      </TR>
      <TR bgcolor=\"".$bgColor[1]."\">
        <TD width=\"80\" class=\"tabIntitule\">&nbsp;<B>".trad("CALIMP_LIB_FICHIER_LDIF")."</B></TD>
        <TD width=\"385\" class=\"tabInput\"><INPUT type=\"file\" class=\"texte\" name=\"ztFile\" size=40></TD>
      </TR>
      </TABLE>
      <BR><INPUT type=\"submit\" class=\"bouton\" name=\"type\" value=\"".trad("CALIMP_BT_IMPORTER")."\">&nbsp;&nbsp;&nbsp;<INPUT type=\"button\" class=\"bouton\" name=\"btAnnule\" value=\"".trad("CALIMP_BT_ANNULER")."\" onclick=\"javascript: btAnnul();\"><INPUT type=\"hidden\" name=\"fic_type\" value=\"ldif\">
      </FORM>
    </DIV></TD>
  </TR>\n");
  }
//--------------------------------------------------
?>
