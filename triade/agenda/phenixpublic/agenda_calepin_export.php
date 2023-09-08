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
  include("inc/fonctions.inc.php");
  include("inc/html.inc.php");
  $idUser = Session_ok($sid);
  include("lang/$APPLI_LANGUE.php");

  $fic = $_GET['fictype'];

  if (strtolower($fic)=="vcard-palm") {
    $fileName = "Export_contacts_VCard_Palm_Desktop".date("Ymd-His").".vcf";

  //  $DB_CX->DbQuery("SELECT ${PREFIX_TABLE}calepin.*, cgr_nom FROM ${PREFIX_TABLE}calepin, ${PREFIX_TABLE}calepin_appartient, ${PREFIX_TABLE}calepin_groupe WHERE cal_util_id=".$idUser." AND cap_cal_id=cal_id AND cgr_id=cap_cgr_id ORDER BY cal_nom ASC, cal_prenom ASC, cal_societe ASC");
    $DB_CX->DbQuery(stripslashes($sql));
    if ($DB_CX->DbNumRows()) {
      $contenu = "";
      while ($enr=$DB_CX->DbNextRow()) {
        $data = "BEGIN:VCARD\r\nVERSION:2.1\r\n";
        $data .= "N:".rtrim($enr['cal_nom']).";".rtrim($enr['cal_prenom'])."\r\n";
        $data .= "FN:".rtrim($enr['cal_prenom'])." ".rtrim($enr['cal_nom'])."\r\n";
        if (!empty($enr['cal_societe']))
          $data .= "ORG:".rtrim($enr['cal_societe'])."\r\n";
        $data .= "ADR;HOME;ENCODING=QUOTED-PRINTABLE:;;".str_replace("\r\n","=0D=0A",$enr['cal_adresse']).";".rtrim($enr['cal_ville']).";;".rtrim($enr['cal_cp']);
        if (!empty($enr['cal_pays']))
          $data .= ";".rtrim($enr['cal_pays']);
        $data .= "\r\n";
        if (!empty($enr['cal_note']))
          $data .= "NOTE;ENCODING=QUOTED-PRINTABLE:".str_replace("\r\n","=0D=0A",$enr['cal_note'])."\r\n";
        if (!empty($enr['cal_date_naissance']) && $enr['cal_date_naissance']!="0000-00-00")
          $data .= "BDAY:".str_replace("-","",rtrim($enr['cal_date_naissance']))."\r\n";
        if (!empty($enr['cal_travail']))
          $data .= "TEL;WORK:".rtrim($enr['cal_travail'])."\r\n";
        if (!empty($enr['cal_domicile']))
          $data .= "TEL;HOME:".rtrim($enr['cal_domicile'])."\r\n";
        if (!empty($enr['cal_portable']))
          $data .= "TEL;CELL:".rtrim($enr['cal_portable'])."\r\n";
        if (!empty($enr['cal_fax']))
          $data .= "TEL;FAX:".rtrim($enr['cal_fax'])."\r\n";
        if (!empty($enr['cal_email']))
          $data .= "EMAIL:".rtrim($enr['cal_email'])."\r\n";
        if (!empty($enr['cal_emailpro']))
          $data .= "EMAIL:".rtrim($enr['cal_emailpro'])."\r\n";
        if (!empty($enr['cal_icq']))
          $data .= "X-PALM-IM; :".$enr['cal_icq']."\r\n";
        if (!empty($enr['cal_aim']))
          $data .= "X-PALM-IM;MSN:".$enr['cal_aim']."\r\n";
        if (!empty($enr['cal_msn']))
          $data .= "X-PALM-IM;Yahoo:".$enr['cal_msn']."\r\n";
        if (!empty($enr['cal_yahoo']))
          $data .= "X-PALM-IM;ICQ:".$enr['cal_yahoo']."\r\n";
        $data .= "END:VCARD\r\n\r\n";
        $contenu .= $data;
      }
    } else {
      $contenu = trad("CALEXP_ECHEC_EXPORT")."\r\n$sql";
    }
  }

  elseif (strtolower($fic)=="vcard") {
    $fileName = "Export_contacts_VCard_".date("Ymd-His").".vcf";

  //  $DB_CX->DbQuery("SELECT ${PREFIX_TABLE}calepin.*, cgr_nom FROM ${PREFIX_TABLE}calepin, ${PREFIX_TABLE}calepin_appartient, ${PREFIX_TABLE}calepin_groupe WHERE cal_util_id=".$idUser." AND cap_cal_id=cal_id AND cgr_id=cap_cgr_id ORDER BY cal_nom ASC, cal_prenom ASC, cal_societe ASC");
    $DB_CX->DbQuery(stripslashes($sql));
    if ($DB_CX->DbNumRows()) {
      $contenu = "";
      while ($enr=$DB_CX->DbNextRow()) {
        $data = "BEGIN:VCARD\r\nVERSION:2.1\r\n";
        $data .= "N:".rtrim($enr['cal_nom']).";".rtrim($enr['cal_prenom'])."\r\n";
        $data .= "FN:".rtrim($enr['cal_prenom'])." ".rtrim($enr['cal_nom'])."\r\n";
        if (!empty($enr['cal_societe']))
          $data .= "ORG:".rtrim($enr['cal_societe'])."\r\n";
        $data .= "ADR;HOME;ENCODING=QUOTED-PRINTABLE:;;".str_replace("\r\n","=0D=0A",$enr['cal_adresse']).";".rtrim($enr['cal_ville']).";;".rtrim($enr['cal_cp']);
        if (!empty($enr['cal_pays']))
          $data .= ";".rtrim($enr['cal_pays']);
        $data .= "\r\n";
        if (!empty($enr['cal_note']))
          $data .= "NOTE;ENCODING=QUOTED-PRINTABLE:".str_replace("\r\n","=0D=0A",$enr['cal_note'])."\r\n";
        if (!empty($enr['cal_date_naissance']) && $enr['cal_date_naissance']!="0000-00-00")
          $data .= "BDAY:".str_replace("-","",rtrim($enr['cal_date_naissance']))."\r\n";
        if (!empty($enr['cal_travail']))
          $data .= "TEL;WORK:".rtrim($enr['cal_travail'])."\r\n";
        if (!empty($enr['cal_domicile']))
          $data .= "TEL;HOME:".rtrim($enr['cal_domicile'])."\r\n";
        if (!empty($enr['cal_portable']))
          $data .= "TEL;CELL:".rtrim($enr['cal_portable'])."\r\n";
        if (!empty($enr['cal_fax']))
          $data .= "TEL;FAX:".rtrim($enr['cal_fax'])."\r\n";
        if (!empty($enr['cal_email']))
          $data .= "EMAIL:".rtrim($enr['cal_email'])."\r\n";
        if (!empty($enr['cal_emailpro']))
          $data .= "EMAIL:".rtrim($enr['cal_emailpro'])."\r\n";
        if (!empty($enr['cal_aim']))
          $data .= "X-PALM-IM;AIM:".rtrim($enr['cal_aim'])."\r\n";
        if (!empty($enr['cal_msn']))
          $data .= "X-PALM-IM;MSN:".rtrim($enr['cal_msn'])."\r\n";
        if (!empty($enr['cal_yahoo']))
          $data .= "X-PALM-IM;Yahoo:".rtrim($enr['cal_yahoo'])."\r\n";
        if (!empty($enr['cal_icq']))
          $data .= "X-PALM-IM;ICQ:".rtrim($enr['cal_icq'])."\r\n";
        $data .= "END:VCARD\r\n\r\n";
        $contenu .= $data;
      }
    } else {
      $contenu = trad("CALEXP_ECHEC_EXPORT")."\r\n$sql";
    }
  }

  elseif (strtolower($fic)=="csvv") {
    $fileName = "Export_contacts_csvcsv(delimiteur-virgule)_".date("Ymd-His").".csv";

  //  $DB_CX->DbQuery("SELECT ${PREFIX_TABLE}calepin.*, cgr_nom FROM ${PREFIX_TABLE}calepin, ${PREFIX_TABLE}calepin_appartient, ${PREFIX_TABLE}calepin_groupe WHERE cal_util_id=".$idUser." AND cap_cal_id=cal_id AND cgr_id=cap_cgr_id ORDER BY cal_nom ASC, cal_prenom ASC, cal_societe ASC");
    $DB_CX->DbQuery(stripslashes($sql));
    if ($DB_CX->DbNumRows()) {
      $contenu = "";
      $contenu = '"'.trad("CALEXP_CSV_V_PRENOM").'","'.trad("CALEXP_CSV_V_NOM").',"'.trad("CALEXP_CSV_V_EMAIL").'","'.trad("CALEXP_CSV_V_EMAIL_PRO").'","'.trad("CALEXP_CSV_V_ADRESSE").'","'.trad("CALEXP_CSV_V_VILLE").'","'.trad("CALEXP_CSV_V_CP").'","'.trad("CALEXP_CSV_V_PAYS").'","'.trad("CALEXP_CSV_V_TEL").'","'.trad("CALEXP_CSV_V_TEL_MOBILE").'","'.trad("CALEXP_CSV_V_TEL_PRO").'","'.trad("CALEXP_CSV_V_FAX").'","'.trad("CALEXP_CSV_V_SOCIETE").'","'.trad("CALEXP_CSV_V_DIVERS").'"';
      $contenu .= "\r\n";
      while ($enr=$DB_CX->DbNextRow()) {
        // Prenom,NOM
        $data = '"'.rtrim($enr['cal_prenom']).'","'.rtrim($enr['cal_nom'].', '.rtrim($enr['cal_prenom']).'"');
        if (!empty($enr['cal_email']))
          $data .= ',"'.rtrim($enr['cal_email']).'"';
        else $data .= ',';
        if (!empty($enr['cal_emailpro']))
          $data .= ',"'.rtrim($enr['cal_emailpro']).'"';
        else $data .= ',';
        if (!empty($enr['cal_adresse']))
          $data .= ',"'.str_replace("\r\n"," ",$enr['cal_adresse']).'"';
        else $data .= ',';
        if (!empty($enr['cal_ville']))
          $data .= ',"'.rtrim($enr['cal_ville']).'"';
        else $data .= ',';
        if (!empty($enr['cal_cp']))
          $data .= ',"'.rtrim($enr['cal_cp']).'"';
        else $data .= ',';
        if (!empty($enr['cal_pays']))
          $data .= ',"'.rtrim($enr['cal_pays']).'"';
        else $data .= ',';
        if (!empty($enr['cal_domicile']))
          $data .= ',"'.rtrim($enr['cal_domicile']).'"';
        else $data .= ',';
        if (!empty($enr['cal_portable']))
          $data .= ',"'.rtrim($enr['cal_portable']).'"';
        else $data .= ',';
        if (!empty($enr['cal_travail']))
          $data .= ',"'.rtrim($enr['cal_travail']).'"';
        else $data .= ',';
        if (!empty($enr['cal_fax']))
          $data .= ',"'.rtrim($enr['cal_fax']).'"';
        else $data .= ',';
        if (!empty($enr['cal_societe']))
          $data .= ',"'.rtrim($enr['cal_societe']).'"';
        else $data .= ',';
        if (!empty($enr['cal_note']))
          $data .= ",\"".str_replace("\r\n"," ",$enr['cal_note']);
        $data .= "\r\n";
  /*
        if (!empty($enr['cal_note']))
          $data .= "NOTE;ENCODING=QUOTED-PRINTABLE:".str_replace("\r\n","=0D=0A",$enr['cal_note'])."\r\n";
        if (!empty($enr['cal_date_naissance']) && $enr['cal_date_naissance']!="0000-00-00")
          $data .= "BDAY:".str_replace("-","",rtrim($enr['cal_date_naissance']))."\r\n";
        if (!empty($enr['cal_aim']))
          $data .= "X-PALM-IM;AIM:".rtrim($enr['cal_aim'])."\r\n";
        if (!empty($enr['cal_msn']))
          $data .= "X-PALM-IM;MSN:".rtrim($enr['cal_msn'])."\r\n";
        if (!empty($enr['cal_yahoo']))
          $data .= "X-PALM-IM;Yahoo:".rtrim($enr['cal_yahoo'])."\r\n";
        if (!empty($enr['cal_icq']))
          $data .= "X-PALM-IM;ICQ:".rtrim($enr['cal_icq'])."\r\n";
        $data .= "END:VCARD\r\n\r\n";
  */
        $contenu .= $data;
      }
    } else {
      $contenu = trad("CALEXP_ECHEC_EXPORT")."\r\n$sql";
    }
  }

  elseif (strtolower($fic)=="csvpv") {
    $fileName = "Export_contacts_csv(delimiteur-point_virgule)_".date("Ymd-His").".csv";
    $DB_CX->DbQuery(stripslashes($sql));
    if ($DB_CX->DbNumRows()) {
      $contenu = "";
      $contenu = trad("CALEXP_CSV_PV_PRENOM").";".trad("CALEXP_CSV_PV_NOM").";".trad("CALEXP_CSV_PV_PRENOM_2").";".trad("CALEXP_CSV_PV_NOM_COMPLET").";".trad("CALEXP_CSV_PV_SURNOM").";".trad("CALEXP_CSV_PV_EMAIL").";".trad("CALEXP_CSV_PV_ADRESSE").";".trad("CALEXP_CSV_PV_VILLE").";".trad("CALEXP_CSV_PV_CP").";".trad("CALEXP_CSV_PV_DEPARTEMENT").";".trad("CALEXP_CSV_PV_PAYS").";".trad("CALEXP_CSV_PV_TEL").";".trad("CALEXP_CSV_PV_TEL_MOBILE").";".trad("CALEXP_CSV_PV_ADRESSE_PRO").";".trad("CALEXP_CSV_PV_VILLE_PRO").";".trad("CALEXP_CSV_PV_CP_PRO").";".trad("CALEXP_CSV_PV_DEPARTEMENT_PRO").";".trad("CALEXP_CSV_PV_PAYS_PRO").";".trad("CALEXP_CSV_PV_SITE_WEB").";".trad("CALEXP_CSV_PV_TEL_PRO").";".trad("CALEXP_CSV_PV_FAX").";".trad("CALEXP_CSV_PV_RADIO").";".trad("CALEXP_CSV_PV_SOCIETE").";".trad("CALEXP_CSV_PV_FONCTION").";".trad("CALEXP_CSV_PV_SERVICE").";".trad("CALEXP_CSV_PV_LIEU").";".trad("CALEXP_CSV_PV_DIVERS")."\r\n";
      while ($enr=$DB_CX->DbNextRow()) {
        // Prenom,NOM
        $data = rtrim($enr['cal_prenom']).";".rtrim($enr['cal_nom']).";";
        // ,Prenom NOM,pseudo=vide
        $data .= ";".rtrim($enr['cal_prenom'])." ".rtrim($enr['cal_nom']).";";
        if ((!empty($enr['cal_email'])) && (empty($enr['cal_emailpro'])))
          $data .= ";".rtrim($enr['cal_email']);
        elseif ((empty($enr['cal_email'])) && (!empty($enr['cal_emailpro'])))
          $data .= ";".rtrim($enr['cal_emailpro']);
        elseif ((!empty($enr['cal_email'])) && (!empty($enr['cal_emailpro'])))
          $data .= ";".rtrim($enr['cal_emailpro']);
        else $data .= ";";
        if (!empty($enr['cal_adresse']))
          $data .= ";".str_replace("\r\n"," ",$enr['cal_adresse']);
        else $data .= ";";
        if (!empty($enr['cal_ville']))
          $data .= ";".rtrim($enr['cal_ville']);
        else $data .= ";";
        if (!empty($enr['cal_cp']))
          $data .= ";".rtrim($enr['cal_cp']).";";
        else $data .= ";;";
        if (!empty($enr['cal_pays']))
          $data .= ";".rtrim($enr['cal_pays']);
        else $data .= ";";
        if (!empty($enr['cal_domicile']))
          $data .= ";".rtrim($enr['cal_domicile']);
        else $data .= ";";
        if (!empty($enr['cal_portable']))
          $data .= ";".rtrim($enr['cal_portable']);
        else $data .= ";";
        $data .= ";;;;;;";
        if (!empty($enr['cal_travail']))
          $data .= ";".rtrim($enr['cal_travail']);
        else $data .= ";";
        if (!empty($enr['cal_fax']))
          $data .= ";".rtrim($enr['cal_fax']).";";
        else $data .= ";;";
        if (!empty($enr['cal_societe']))
          $data .= ";".rtrim($enr['cal_societe']);
        else $data .= ";";
        $data .= ";;;";
        if (!empty($enr['cal_note']))
          $data .= ";".str_replace("\r\n"," ",$enr['cal_note'])."\r\n";
        else $data .= ";\r\n";
        $contenu .= $data;
      }
    } else {
      $contenu = trad("CALEXP_ECHEC_EXPORT")."\r\n$sql";
    }
  }

  elseif (strtolower($fic)=="ldif") {
    $fileName = "Export_contacts_ldif_".date("Ymd-His").".ldif";
    $deb=0;
  //  $DB_CX->DbQuery("SELECT ${PREFIX_TABLE}calepin.*, cgr_nom FROM ${PREFIX_TABLE}calepin, ${PREFIX_TABLE}calepin_appartient, ${PREFIX_TABLE}calepin_groupe WHERE cal_util_id=".$idUser." AND cap_cal_id=cal_id AND cgr_id=cap_cgr_id ORDER BY cal_nom ASC, cal_prenom ASC, cal_societe ASC");
    $DB_CX->DbQuery(stripslashes($sql));
    if ($DB_CX->DbNumRows()) {
      $contenu = "";
      while ($enr=$DB_CX->DbNextRow()) {
        if ((!empty($enr['cal_nom'])) && (!empty($enr['cal_prenom']))) {
          $pren_nom = $enr['cal_prenom']." ".$enr['cal_nom'];
        } elseif ((!empty($enr['cal_nom'])) && (empty($enr['cal_prenom']))) {
          $pren_nom = $enr['cal_nom'];
        } elseif ((empty($enr['cal_nom'])) && (!empty($enr['cal_prenom']))) {
          $pren_nom = $enr['cal_prenom'];
        } else
          $pren_nom = "";
        if ((!empty($enr['cal_email'])) && (!empty($enr['cal_emailpro']))) {
          $mail1 = $enr['cal_email'];
        } elseif ((!empty($enr['cal_email'])) && (empty($enr['cal_emailpro']))) {
          $mail1 = $enr['cal_email'];
        } elseif ((empty($enr['cal_email'])) && (!empty($enr['cal_emailpro']))) {
          $mail1 = $enr['cal_emailpro'];
        } else
          $mail1 = "";
        if ($deb!=0) {
          if ($mail1!="")
            $data = "\r\ndn: cn=".$pren_nom.",mail=".rtrim($mail1)."\r\n";
          else
            $data = "\r\ndn: cn=".$pren_nom."\r\n";
        } else {
          if ($mail1!="")
            $data = "dn: cn=".$pren_nom.",mail=".rtrim($mail1)."\r\n";
          else
            $data = "dn: cn=".$pren_nom."\r\n";
        }
        $data .= "objectclass: top\r\n";
        $data .= "objectclass: person\r\n";
        $data .= "objectclass: organizationalPerson\r\n";
        $data .= "objectclass: inetOrgPerson\r\n";
        $data .= "objectclass: mozillaAbPersonObsolete\r\n";
        $data .= "givenName:".rtrim($enr['cal_prenom'])."\r\n";
        $data .= "sn:".rtrim($enr['cal_nom'])."\r\n";
        $data .= "cn:".rtrim($enr['cal_prenom'])." ".rtrim($enr['cal_nom'])."\r\n";
        if ($mail1!="") $data .= "mail:".rtrim($mail1)."\r\n";
        if ((!empty($enr['cal_email'])) && (!empty($enr['cal_emailpro']))) {
          $mail2 = $enr['cal_emailpro'];
          $data .= "mozillaSecondEmail:".rtrim($mail2)."\r\n";
        }
        if (!empty($enr['cal_travail']))
          $data .= "telephoneNumber:".rtrim($enr['cal_travail'])."\r\n";
        if (!empty($enr['cal_domicile']))
          $data .= "homePhone:".rtrim($enr['cal_domicile'])."\r\n";
        if (!empty($enr['cal_fax']))
          $data .= "facsimileTelephoneNumber:".rtrim($enr['cal_fax'])."\r\n";
        if (!empty($enr['cal_portable']))
          $data .= "mobile:".rtrim($enr['cal_portable'])."\r\n";
        if (!empty($enr['cal_adresse']))
          $data .= "homePostalAddress:".str_replace(" ","=0D=0A",$enr['cal_adresse'])."\r\n";
        if (!empty($enr['cal_ville']))
          $data .= "mozillaHomeLocalityName:".rtrim($enr['cal_ville'])."\r\n";
        if (!empty($enr['cal_cp']))
          $data .= "mozillaHomePostalCode:".rtrim($enr['cal_cp'])."\r\n";
        if (!empty($enr['cal_pays']))
          $data .= "mozillaHomeCountryName:".rtrim($enr['cal_pays'])."\r\n";
        if (!empty($enr['cal_societe']))
          $data .= "o:".rtrim($enr['cal_societe'])."\r\n";
        $contenu .= $data;
        $deb=1;
      }
    } else {
      $contenu = trad("CALEXP_ECHEC_EXPORT")."\r\n$sql";
    }
  }

  header('Content-Type: text/calendar');
  header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
  if (preg_match('@MSIE ([0-9].[0-9]{1,2})@', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
    header('Content-Disposition: inline; filename="'.$fileName.'"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
  } else {
    header('Content-Disposition: attachment; filename="'.$fileName.'"');
    header('Pragma: no-cache');
  }
  echo $contenu;
?>
