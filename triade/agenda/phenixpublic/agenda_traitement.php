<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5_Couleur_par_defaut.txt ?>
<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5_Scission_de_note_recurente.txt ?>
<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5.5_Copie_note_par_mail.txt ?>
<?php // Mod applied : 2008-11-02 * Mod_Phenix_V5_Rappel_Sonore.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_Meteo_today_ico.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_Menu_Note.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_MemoProgress_plus.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_horoscope_hebdo.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_fcke_aff_outils.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5_DD.txt ?>
<?php // Mod applied : 2008-08-04 * Mod_Phenix_V5.5_Emplacement_Plus.txt ?>
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
  if (isset($sid)) {
    include("inc/fonctions.inc.php");
    include('inc/class.mailer.php');
    include("inc/html.inc.php");
    $classMailerLoaded = $classSMTPLoaded = false;
  } else {
    Header("location: deconnexion.php?msg=5");
    exit;
  }

  $idUser = Session_ok($sid);

  include("lang/$APPLI_LANGUE.php");

  $idAge += 0;

  $sTmp = "";

/*--------------------------------------------
              GESTION DES NOTES
--------------------------------------------*/
if ($ztFrom == "note") {
  if ($tcMenu>=_MENU_DISP_HEBDO)
    $tcMenu = $tcPlg;
  if ($ztAction == "INSERT" || $ztAction == "UPDATE") {
    $tabDate = explode("/",$ztDateNote);
    $ztDateForm = $tabDate[2]."-".$tabDate[1]."-".$tabDate[0];

    // Contre-mesure de certains providers
    $zlHeureDebut=str_replace(",",".",$zlHeureDebut);
    $zlHeureFin=str_replace(",",".",$zlHeureFin);

    // Conversion en utc en fonction du timezone
    $dateNote = $ztDateForm;
    list($zlHeureDebutUTC,$zlHeureFinUTC,$dateCrtUTC,$dateModifUTC,$dateNoteUTC) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$dateJour,$dateNote,$zlHeureDebut,$zlHeureFin,$dateCrt,$dateModif,1,1);
    $dateUTC = explode("-",$dateNoteUTC);
    $tabDateUTC = array($dateUTC[2],$dateUTC[1],$dateUTC[0]);

    $hNote = floor($zlHeureDebut);
    $mNote = ($zlHeureDebut*60)%60;
    $ztDateUTC = $tabDateUTC[2]."-".$tabDateUTC[1]."-".$tabDateUTC[0];
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
        //Stockage de la semaine type au format PHP qui est utilisee pour creer la note
        $periode2 = "";
        for ($i=0;$i<7;$i++) {
          $aSemaineType[$i] = (!$i) ? $bt7 + 0 : ${"bt".$i} + 0;
          $periode2 .= $aSemaineType[$i];
        }
        $periode2 += 0; // On retransforme la chaine en entier pour enlever les 0 devants
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
      default : $zlPeriodicite = 1;
    }
    if ($rdPlage == 2 && $zlPeriodicite > 1) {
      $nbOccurrence = 0;
      list($zlP1,$zlP2,$zlP3) = explode("/",$ztDateFin);
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
      $ckEmailContact = 0;
    } else {
      if ($ckEmail != 1) {
        $ckEmail = 0;
      }
      if ($ckEmailContact != 1 || $zlContactAssocie == "0") {
        $ckEmailContact = 0;
      }
    }
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
    $dateCreation = gmdate("Y-m-d H:i:s", time());
    //Liste des personnes concernees
    $idParticipant = explode("+", $ztParticipant);
  }

  // Recuperation pour les alertes par mail du nom et de l'adresse mail de l'utilisateur courant
  $DB_CX->DbQuery("SELECT CONCAT(".$FORMAT_NOM_UTIL."), util_email FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$idUser);
  $sNomExpediteur = $DB_CX->DbResult(0,0);
  $sMailExpediteur = $DB_CX->DbResult(0,1);
  // MOD Copie note par mail
  $tabEmailCopie = explode(";", $ztEmailCopie);
  // Fin MOD Copie note par mail

  // Verification de la superposition des notes
  if ($ztAction == "INSERT" || ($ztAction == "UPDATE" && $idAge)) {
    // Calcul des bascules ete/hiver pour la date et l'heure locale
    $tzEte = calculBasculeDST($tzDateEte,gmdate("Y"),$tzHeureEte,$tzGmt,0);
    $tzHiver = calculBasculeDST($tzDateHiver,gmdate("Y"),$tzHeureHiver,$tzGmt,1);
    //Preparation au decalage horaire
    list($age_date,$age_dateAvant,$age_heure_debut,$age_heure_fin) = prepareDecalageH($tzGmt,$tzEte,$tzHiver,$tsNoteUTC);
    // Test de l'existence d'une note pour la plage horaire concernee
    // pour les personnes concernees par la note (autre que le createur)
    $sql  = "SELECT DISTINCT(aco_util_id) AS acoUtilId FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne ";
    $sql .= "WHERE aco_age_id=age_id AND aco_util_id!=".$idUser." AND age_aty_id=2 AND age_id!=".$idAge;
    $sql .= " AND (($age_date='".$ztDateForm."' AND (($age_heure_debut<=".$zlHeureDebut." AND $age_heure_fin>".$zlHeureDebut.")";
    $sql .= " OR ($age_heure_debut>=".$zlHeureDebut." AND $age_heure_fin<=".$zlHeureFin." AND $age_heure_debut<=$age_heure_fin)";
    $sql .= " OR ($age_heure_fin>=".$zlHeureFin." AND $age_heure_debut<".$zlHeureFin.")))";
    $sql .= " OR ($age_dateAvant='".$ztDateForm."' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin!=0";
    $sql .= " AND $age_heure_fin>".$zlHeureDebut.")) ORDER BY aco_util_id";
    $DB_CX->DbQuery($sql);
    $noteUser="";
    //Trie du tableau par ordre croissant
    //Pour ensuite faire avancer l'indice de depart de la boucle FOR
    //en fonction du dernier participant trouve
    sort($idParticipant);
    reset($idParticipant);
    $iDepart=0;
    $cpTour=0;
    while ($enr = $DB_CX->DbNextRow()) {
      $ok=false;
      for ($nb=$iDepart;$nb<count($idParticipant) && !$ok;$nb++) {
        if ($idParticipant[$nb]==$enr['acoUtilId']) {
          $noteUser .= ",".$enr['acoUtilId'];
          $ok = true;
        }
        $cpTour++;
      }
      $iDepart = ($ok) ? $nb : $iDepart;
    }
    // Liste des utilisateurs concernes par la superposition de note a ajouter dans l'url en bas de page
    $sTmp = (!empty($noteUser)) ? "&lSup=".substr($noteUser,1) : "";
  }

  // Recuperation des informations lors de la suppression et la mise a jour
  // Permet ainsi l'envoi de mail pour toute suppression d'une note aux personnes concernees
  // Et aussi en cas de modification de la note et de suppression d'une personne concernee
  if (($ztAction == "DELETE" || $ztAction == "UPDATE") && $idAge) {
    // Construction de la liste des personnes qui ETAIENT concernees par l'ANCIENNE note
    // On ne retient que les utilisateurs (autres que l'auteur) qui ont choisi d'etre informe par email
    $DB_CX->DbQuery("SELECT util_id, util_email, tzn_libelle, tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver FROM ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}utilisateur, ${PREFIX_TABLE}timezone WHERE aco_age_id=".$idAge." AND aco_util_id!=".$idUser." AND util_id=aco_util_id AND util_alert_affect='O' AND tzn_zone=util_timezone");
    $aTabConcerne = array();
    $sOldDestMail = array();
    $sSupDestMail = array();
    $tabOldDestMail = array();
    $tabSupDestMail = array();
    // Tableaux des timezones
    $sDestTzLibelle = array();
    $sDestTzGmt = array();
    $sDestTzDateEte = array();
    $sDestTzHeureEte = array();
    $sDestTzDateHiver = array();
    $sDestTzHeureHiver = array();
    while ($enr = $DB_CX->DbNextRow()) {
      if (!empty($enr['util_email'])) {
        $aTabConcerne[$enr['util_id']] = $enr['util_email'];
        $sOldDestMail[$enr['util_id']] = $enr['util_email'];
        // Recuperation des infos de timezone
        $sDestTzLibelle[$enr['util_id']] = $enr['tzn_libelle'];
        $sDestTzGmt[$enr['util_id']] = $enr['tzn_gmt'];
        $sDestTzDateEte[$enr['util_id']] = $enr['tzn_date_ete'];
        $sDestTzHeureEte[$enr['util_id']] = $enr['tzn_heure_ete'];
        $sDestTzDateHiver[$enr['util_id']] = $enr['tzn_date_hiver'];
        $sDestTzHeureHiver[$enr['util_id']] = $enr['tzn_heure_hiver'];
      }
    }
    if ($ztAction == "UPDATE") {
      // Construction de la liste des personnes qui SONT concernees par la NOUVELLE note
      // On ne retient que les utilisateurs (autres que l'auteur) qui ont choisi d'etre informe par email
      $aTabNvConcerne = array();
      for ($nb=0;$nb < count($idParticipant);$nb++) {
        if ($idParticipant[$nb]!=$idUser) {
          $DB_CX->DbQuery("SELECT util_email, tzn_libelle, tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver FROM ${PREFIX_TABLE}utilisateur, ${PREFIX_TABLE}timezone WHERE util_id=".$idParticipant[$nb]." AND util_alert_affect='O' AND tzn_zone=util_timezone");
          // Test pour savoir si cet utilisateur a renseigne son adresse email
          if ($enr = $DB_CX->DbNextRow()) {
            if (!empty($enr['util_email'])) {
              $aTabNvConcerne[$idParticipant[$nb]] = $enr['util_email'];
              // Recuperation des infos de timezone
              $sDestTzLibelle[$idParticipant[$nb]] = $enr['tzn_libelle'];
              $sDestTzGmt[$idParticipant[$nb]] = $enr['tzn_gmt'];
              $sDestTzDateEte[$idParticipant[$nb]] = $enr['tzn_date_ete'];
              $sDestTzHeureEte[$idParticipant[$nb]] = $enr['tzn_heure_ete'];
              $sDestTzDateHiver[$idParticipant[$nb]] = $enr['tzn_date_hiver'];
              $sDestTzHeureHiver[$idParticipant[$nb]] = $enr['tzn_heure_hiver'];
            }
          }
        }
      }
      // Construction de la liste des personnes qui NE SONT PLUS concernees par la NOUVELLE note
      // Permet d'envoyer un mail de suppression si une personne est retiree de la note
      $aTmp = array_diff($aTabConcerne, $aTabNvConcerne);
      // Concatenation des emails des personnes de la liste ci-dessus
      while(list($sCle,$sValeur)=each($aTmp)) {
        $sSupDestMail[$sCle] = $sValeur;
      }
    }
    // Recuperation des informations de la note avant la suppression
    $DB_CX->DbQuery("SELECT age_date, age_heure_debut, age_libelle, age_lieu, age_detail, age_email_copie FROM ${PREFIX_TABLE}agenda WHERE age_id=".$idAge);
    $sDate = $DB_CX->DbResult(0,0);
    $sHeureNoteSupp = $DB_CX->DbResult(0,1);
    $sLibelle = $DB_CX->DbResult(0,2);
    $sLieu = $DB_CX->DbResult(0,3);
    $sDetail = $DB_CX->DbResult(0,4);
    // MOD Copie note par mail
    $tabSupEmailCopie = explode(";", $DB_CX->DbResult(0,"age_email_copie"));
    // Fin MOD Copie note par mail
    // Construction du sujet du mail
    $sSujet = trad("TRAITEMENT_NOTIF_SUPP");
    // Construction du corps du mail
    // On trouve la liste des timezones disctincts
    $aDestListTZ = array_unique($sDestTzLibelle);
    foreach ($aDestListTZ as $key=>$libTZ) {
      // Pour chaque TZ on recupere les id des utilisateurs
      $listID = array_keys($sDestTzLibelle,$libTZ);
      foreach ($listID as $idUtil) {
        // Pour chaque liste on genere le tableau des emails
        if (array_key_exists($idUtil,$sOldDestMail)) {
          $tabOldDestMail[$key][] = $sOldDestMail[$idUtil];
        }
        if (array_key_exists($idUtil,$sSupDestMail)) {
          $tabSupDestMail[$key][] = $sSupDestMail[$idUtil];
        }
      }
      // On genere le corps specifique au fuseau en court
      list($tHeureNoteSupp[$key],$thrfin,$tdtCrt,$tdtMdf,$tDate[$key]) = decaleNote($sDestTzGmt[$key],$sDestTzDateEte[$key],$sDestTzHeureEte[$key],$sDestTzDateHiver[$key],$sDestTzHeureHiver[$key],$dateJour,$sDate,$sHeureNoteSupp,$hrfin,$dtCrt,$dtMdf,1);
      // Formatage de l'heure et la date de la note pour affichage dans le mail
      $tHeureNoteSupp[$key] = afficheHeure(floor($tHeureNoteSupp[$key]), $tHeureNoteSupp[$key], "H\hi");
      $aDate = explode("-",$tDate[$key]);
      $tDate[$key] = $aDate[2]."/".$aDate[1]."/".$aDate[0];

      if ($flag != 2) {
        // L'auteur d'une note la supprime -> on informe les utilisateurs concernes
        $aCorps[$key] = nl2br("<HTML><BODY>".sprintf(trad("TRAITEMENT_SUPP_J"),$sNomExpediteur,$tDate[$key],$tHeureNoteSupp[$key])."\n\n<U>".trad("TRAITEMENT_LIBELLE")."</U>:&nbsp;".$sLibelle."\n".((!empty($sLieu)) ? "<U>".trad("TRAITEMENT_EMPLACEMENT")."</U>:&nbsp;".$sLieu."\n" : "").((!empty($sDetail)) ? "<U>".trad("TRAITEMENT_DETAIL")."</U>:&nbsp;".$sDetail : "").signatureMail());
      } elseif ($flag == 2 && $AUTORISE_SUPPR) {
        // Un utilisateur supprime une note qui lui avait ete affectee -> on informe le createur de la note
        $aCorps[$key] = nl2br("<HTML><BODY>".sprintf(trad("TRAITEMENT_SUPP_AFFECT"),$sNomExpediteur,$tDate[$key],$tHeureNoteSupp[$key])."\n\n<U>".trad("TRAITEMENT_LIBELLE")."</U>:&nbsp;".$sLibelle."\n".((!empty($sLieu)) ? "<U>".trad("TRAITEMENT_EMPLACEMENT")."</U>:&nbsp;".$sLieu."\n" : "").((!empty($sDetail)) ? "<U>".trad("TRAITEMENT_DETAIL")."</U>:&nbsp;".$sDetail : "").signatureMail());
      }
    }
    if ($flag == 2 && $AUTORISE_SUPPR) {
      // Un utilisateur supprime une note qui lui avait ete affectee -> on informe le createur de la note
      $DB_CX->DbQuery("SELECT util_email, tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}utilisateur, ${PREFIX_TABLE}timezone WHERE age_id=".$idAge." AND util_id=age_util_id AND tzn_zone=util_timezone");
      $sAuteurNote = array();
      if ($enr = $DB_CX->DbNextRow()) {
        if ($enr['util_email']!="") {
          $sAuteurNote[] = $enr['util_email'];
          // Recuperation des infos de timezone
          $sAuteurTzGmt = $enr['tzn_gmt'];
          $sAuteurTzDateEte = $enr['tzn_date_ete'];
          $sAuteurTzHeureEte = $enr['tzn_heure_ete'];
          $sAuteurTzDateHiver = $enr['tzn_date_hiver'];
          $sAuteurTzHeureHiver = $enr['tzn_heure_hiver'];
          // On genere le corps specifique au fuseau de l'auteur
          list($tHeureNoteSupp,$thrfin,$tdtCrt,$tdtMdf,$tDate) = decaleNote($sAuteurTzGmt,$sAuteurTzDateEte,$sAuteurTzHeureEte,$sAuteurTzDateHiver,$sAuteurTzHeureHiver,$dateJour,$sDate,$sHeureNoteSupp,$hrfin,$dtCrt,$dtMdf,1);
          // Formatage de l'heure et la date de la note pour affichage dans le mail
          $tHeureNoteSupp = afficheHeure(floor($tHeureNoteSupp), $tHeureNoteSupp, "H\hi");
          $aDate = explode("-",$tDate);
          $tDate = $aDate[2]."/".$aDate[1]."/".$aDate[0];
          $sCorps = nl2br("<HTML><BODY>".sprintf(trad("TRAITEMENT_SUPP_AFFECT"),$sNomExpediteur,$tDate,$tHeureNoteSupp)."\n\n<U>".trad("TRAITEMENT_LIBELLE")."</U>:&nbsp;".$sLibelle."\n".((!empty($sLieu)) ? "<U>".trad("TRAITEMENT_EMPLACEMENT")."</U>:&nbsp;".$sLieu."\n" : "").((!empty($sDetail)) ? "<U>".trad("TRAITEMENT_DETAIL")."</U>:&nbsp;".$sDetail : "").signatureMail());
        }
      }
    }
  }

  if ($ztAction == "INSERT") {
    $sd = $ztDateForm;
    $sql = "INSERT INTO ${PREFIX_TABLE}agenda (age_mere_id,age_util_id,age_aty_id,age_date,age_heure_debut,age_heure_fin,age_ape_id, age_periode1, age_periode2, age_periode3, age_periode4, age_plage, age_plage_duree, age_libelle, age_detail, age_rappel, age_rappel_coeff, age_email, age_prive, age_couleur, age_nb_participant, age_createur_id, age_disponibilite, age_date_creation, age_date_modif, age_modificateur_id, age_lieu, age_cal_id, age_email_contact, age_email_copie) ";
    $sql .= "VALUES (0,".$idUser.",".$ckTypeNote.",'".$ztDateUTC."',".$zlHeureDebutUTC.",".$zlHeureFinUTC.",".$zlPeriodicite.",".$periode1.",".$periode2.",".$periode3.",".$periode4.",".$rdPlage.",".($nbOccurrence + $dateMax).",'".$ztLibelle."','".$ztDetail."',".$zlR1.",".$zlR2.",".$ckEmail.",".$rdPrive.",'".$zlCouleur."',".count($idParticipant).",".$idUser.",".$rdDispo.",'".$dateCreation."','".$dateCreation."',".$idUser.",'".$ztLieu."',".$zlContactAssocie.",".$ckEmailContact.",'".$ztEmailCopie."')";
    $DB_CX->DbQuery($sql);
    $idAge = $DB_CX->DbInsertID();

    // Enregistrement des personnes concernees
    for ($nb=0;$nb < count($idParticipant);$nb++)
      $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}agenda_concerne VALUES (".$idAge.",".$idParticipant[$nb].",".$alert.",".$endNote.")");
    $msg=8;

    //Si l'utilisateur a clique sur le bouton Recommencer,
    //on enregistre les parametres pour qu'il soit renvoye vers la page de creation de note
    if ($ztRecommence == "OUI") {
      $sTmp .= "&tcType="._TYPE_NOTE."&tcPlg=".$tcMenu;
    }
  }

  elseif ($ztAction == "UPDATE" && $idAge) {
    if ($zlPeriodicite == 1)
      $sd = $ztDateForm;
    $liste = "0";
    if ($edit!="occ") {
      // Modification de la note mere -> on supprime toutes les occurences pour les recreer
      $DB_CX->DbQuery("SELECT DISTINCT age_id FROM ${PREFIX_TABLE}agenda WHERE age_mere_id=".$idAge);
      while ($enr = $DB_CX->DbNextRow()) {
        $liste .= ",".$enr['age_id'];
      }
      $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda WHERE age_id IN (".$liste.")");
      // Information sur la note mere a conserver dans les occurences recrees
      $DB_CX->DbQuery("SELECT age_date_creation FROM ${PREFIX_TABLE}agenda WHERE age_id=".$idAge);
      if ($enr = $DB_CX->DbNextRow()) {
        $dateCreation = $enr['age_date_creation'];
      }
    }
    $DB_CX->DbQuery("SELECT paf_util_id FROM ${PREFIX_TABLE}planning_affecte WHERE paf_consultant_id=".$idUser);
    $utilAffecte = array();
    while ($enr = $DB_CX->DbNextRow()) {
      $utilAffecte[] = $enr['paf_util_id'];
    }
    $DB_CX->DbQuery("SELECT DISTINCT aco_util_id FROM ${PREFIX_TABLE}agenda_concerne WHERE aco_age_id IN (".$liste.",".$idAge.") AND aco_util_id NOT IN (".implode(",", $utilAffecte).") AND aco_util_id!=".$idUser);
    while ($enr = $DB_CX->DbNextRow()) {
      $idParticipant[] = $enr['aco_util_id'];
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
    $sql .= " age_email_contact=".$ckEmailContact.",";
    // MOD Copie note par mail
    $sql .= " age_email_copie='".$ztEmailCopie."',";
    // Fin MOD Copie note par mail
    $sql .= " age_prive=".$rdPrive.",";
    $sql .= " age_couleur='".$zlCouleur."',";
    $sql .= " age_nb_participant=".count($idParticipant).",";
    $sql .= " age_disponibilite=".$rdDispo.",";
    $sql .= " age_date_modif='".gmdate("Y-m-d H:i:s", time())."',";
    $sql .= " age_modificateur_id=".$idUser.",";
    $sql .= " age_lieu='".$ztLieu."',";
    $sql .= " age_cal_id=".$zlContactAssocie." ";
    $sql .= "WHERE age_id=".$idAge;
    if ($droit_NOTES < _DROIT_NOTE_MODIF_CREATION)
      $sql .= " AND age_util_id=".$idUser;
    $DB_CX->DbQuery($sql);
    $msg=9;

    // Enregistrement des personnes concernees
    for ($nb=0;$nb < count($idParticipant);$nb++)
      $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}agenda_concerne VALUES (".$idAge.",".$idParticipant[$nb].",".$alert.",".$endNote.")");
  }

  elseif ($ztAction == "DELETE" && $idAge) {
    $flag += 0;
    // MOD Copie note par mail
    $copieMailSujet = trad("TRAITEMENT_NOTIF_SUPP");
    $tHeureNoteSupp = afficheHeure(floor($sHeureNoteSupp), $sHeureNoteSupp, "H\hi");
    $aDate = explode("-",$sDate);
    $sDate = $aDate[2]."/".$aDate[1]."/".$aDate[0];
    $copieMailCorps = nl2br("<HTML><BODY>".sprintf(trad("TRAITEMENT_SUPP_J"),$sNomExpediteur,$sDate,$tHeureNoteSupp)."\n\n<U>".trad("TRAITEMENT_LIBELLE")."</U>:&nbsp;".$sLibelle."\n".((!empty($sLieu)) ? "<U>".trad("TRAITEMENT_EMPLACEMENT")."</U>:&nbsp;".$sLieu."\n" : "").((!empty($sDetail)) ? "<U>".trad("TRAITEMENT_DETAIL")."</U>:&nbsp;".$sDetail : "").signatureMail());
    // Fin MOD Copie note par mail
    if ($flag == 2 && $AUTORISE_SUPPR) {
      //Suppression d'une note affectee
      $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda_concerne WHERE aco_age_id=".$idAge." AND aco_util_id=".$idUser);
      $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}information WHERE info_age_id=".$idAge." AND info_destinataire_id=".$idUser);
      //Recherche s'il reste des personnes concernees par cette note
      $DB_CX->DbQuery("SELECT aco_util_id FROM ${PREFIX_TABLE}agenda_concerne WHERE aco_age_id=".$idAge);
      //si NON : on efface la note
      if (!$DB_CX->DbNumRows()) {
        $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda WHERE age_id=".$idAge);
      } else {
        //si OUI : on reajuste le nombre de participant (pour l'appropriation)
        $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}agenda SET age_nb_participant = ".$DB_CX->DbNumRows()." WHERE age_id=".$idAge);
      }

      //On informe l'auteur de la note
      if (count($sAuteurNote)>0) {
        envoiMail($sNomExpediteur, $sMailExpediteur, $sAuteurNote, $sSujet, $sCorps);
      }
    } elseif ($flag == 1) {
      //Suppression de la totalite d'une note par son auteur
      $DB_CX->DbQuery("SELECT DISTINCT age_id FROM ${PREFIX_TABLE}agenda WHERE (age_id=".$idAge." OR age_mere_id=".$idAge.")".(($droit_NOTES < _DROIT_NOTE_COMPLET) ? " AND age_util_id=".$idUser :""));
      $liste = "0";
      while ($enr = $DB_CX->DbNextRow())
        $liste .= ",".$enr['age_id'];
      $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda WHERE age_id IN (".$liste.")");
      $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda_concerne WHERE aco_age_id IN (".$liste.")");
      //On informe les personnes concernees que l'auteur vient de supprimer la note qu'il avait cree
      if ($DB_CX->DbAffectedRows()>0 && count($tabOldDestMail)>0) {
        // On distingue chaque fuseau pour l'envoi
        foreach ($aDestListTZ as $key=>$libTZ) {
          envoiMail($sNomExpediteur, $sMailExpediteur, $tabOldDestMail[$key], $sSujet, $aCorps[$key]);
        }
      }
      $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}information WHERE info_age_id IN (".$liste.")");
      $msg=10;
      // MOD Copie note par mail
      if (count($tabSupEmailCopie)>0) {
        envoiMail($sNomExpediteur, $sMailExpediteur, $tabSupEmailCopie, $copieMailSujet, $copieMailCorps);
      }
      // Fin MOD Copie note par mail
    } else {
      //Suppression d'une occurrence d'une note par son auteur
      $DB_CX->DbQuery("SELECT MIN(age_id) FROM ${PREFIX_TABLE}agenda WHERE age_mere_id=".$idAge.(($droit_NOTES >= _DROIT_NOTE_COMPLET) ? " AND age_util_id=".$idUser :""));
      $newIdAge = $DB_CX->DbResult(0,0) + 0;
      if ($newIdAge) {
        $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}agenda SET age_mere_id=".$newIdAge." WHERE age_mere_id=".$idAge);
        $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}agenda SET age_mere_id=0 WHERE age_id=".$newIdAge);
      }
      $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda WHERE age_id=".$idAge.(($droit_NOTES < _DROIT_NOTE_COMPLET) ? " AND age_util_id=".$idUser :""));
      if ($DB_CX->DbAffectedRows()>0) {
        $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda_concerne WHERE aco_age_id=".$idAge);
       //On informe les personnes concernees que l'auteur vient de supprimer une occurrence d'une note qu'il avait cree
        if ($DB_CX->DbAffectedRows()>0 && count($tabOldDestMail)>0) {
          // On distingue chaque fuseau pour l'envoi
          foreach ($aDestListTZ as $key=>$libTZ) {
            envoiMail($sNomExpediteur, $sMailExpediteur, $tabOldDestMail[$key], $sSujet, $aCorps[$key]);
          }
        }
        $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}information WHERE info_age_id=".$idAge);
      }
      $msg=11;
      // MOD Copie note par mail
      if (count($tabSupEmailCopie)>0) {
        envoiMail($sNomExpediteur, $sMailExpediteur, $tabSupEmailCopie, $copieMailSujet, $copieMailCorps);
      }
      // Fin MOD Copie note par mail
    }
    $idAge = 0;
  }

  elseif ($ztAction == "APPROPRIATION" && $idAge) {
    // On recherche si c'est une note recurrente pour s'approprier toute la serie
    $DB_CX->DbQuery("SELECT age_mere_id FROM ${PREFIX_TABLE}agenda WHERE age_id=".$idAge);
    $idAgeMere = $DB_CX->DbResult(0,0) + 0;
    if ($idAgeMere) {
      $idAge = $idAgeMere;
    }
    $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}agenda SET age_util_id=".$idUser." WHERE (age_id=".$idAge." OR age_mere_id=".$idAge.")");
    $idAge = 0;
  }
  // MOD Scission de note
  elseif ($ztAction == "DIVIDE" && $idAge) {
    // Recuperation des infos sur la note mere et la fin des occurrences
    $DB_CX->DbQuery("SELECT age_mere_id,age_date,age_heure_debut,age_plage,age_plage_duree FROM ${PREFIX_TABLE}agenda WHERE age_id=".$idAge);
    $enr = $DB_CX->DbNextRow();
    $idAgeMere = $enr['age_mere_id'];
    $ageDate = $enr['age_date'];
    $zlHeureDebut = $enr['age_heure_debut'];
    $rdPlage = $enr['age_plage'];
    $nbOccurrence = $enr['age_plage_duree'];
    $hNote = floor($zlHeureDebut);
    $mNote = ($zlHeureDebut*60)%60;
    list($zlP3,$zlP2,$zlP1) = explode("-",$ageDate);
    $dateAge = mktime($hNote,$mNote,0,$zlP2,$zlP1,$zlP3);
    // Recuperation de la date de la note mere
    $DB_CX->DbQuery("SELECT age_date FROM ${PREFIX_TABLE}agenda WHERE age_id=".$idAgeMere);
    $ageDateMere = $DB_CX->DbResult(0,"age_date");
    // Decoupage
    if ($rdPlage==1) {
      // Repetition avec nombre d'occurrences
      // On trouve le nombre d'occurrences precedent la note de scission
      $DB_CX->DbQuery("SELECT COUNT(*) FROM ${PREFIX_TABLE}agenda WHERE (age_id=".$idAgeMere." OR age_mere_id=".$idAgeMere.") AND age_date<'".$ageDate."'");
      $nbOccG1 = $DB_CX->DbResult(0,0);
      $nbOccG2 = $nbOccurrence-$nbOccG1;
      $dateMaxG1 = 0;
      $dateMaxG2 = 0;
    } else {
      // Repetition avec une date de fin
      // On trouve l'occurrence precedent la note de scission
      $DB_CX->DbQuery("SELECT age_date FROM ${PREFIX_TABLE}agenda WHERE (age_id=".$idAgeMere." OR age_mere_id=".$idAgeMere.") AND age_date<'".$ageDate."' ORDER BY age_date DESC LIMIT 0,1");
      $ageDateFinG1 = $DB_CX->DbResult(0,"age_date");
      list($zlP3,$zlP2,$zlP1) = explode("-",$ageDateFinG1);
      $nbOccG1 = ($ageDateFinG1==$ageDateMere) ? 1 : 0;
      $nbOccG2 = ($nbOccurrence==$dateAge) ? 1 : 0;
      $dateMaxG1 = mktime($hNote,$mNote,0,$zlP2,$zlP1,$zlP3);
      $dateMaxG2 = $nbOccurrence;
    }
    // On redefini le nombre ou la date de fin des occurrences du premier groupe de note
    if ($nbOccG1==1) {
      // Si une seule note se trouve dans le premier groupe, on enleve la periodicite
      $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}agenda SET age_mere_id=0, age_ape_id=1, age_periode1=0, age_periode2=0, age_periode3=0, age_periode4=0, age_plage=1, age_plage_duree=10 WHERE age_id=".$idAgeMere);
    } else {
      $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}agenda SET age_plage_duree=".($nbOccG1+$dateMaxG1)." WHERE (age_id=".$idAgeMere." OR age_mere_id=".$idAgeMere.") AND age_date<'".$ageDate."'");
    }
    // On redefini le nombre d'occurrences du second groupe de note
    if ($nbOccG2==1) {
      // Si une seule note se trouve dans le second groupe, on enleve la periodicite
      $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}agenda SET age_mere_id=0, age_ape_id=1, age_periode1=0, age_periode2=0, age_periode3=0, age_periode4=0, age_plage=1, age_plage_duree=10 WHERE age_id=".$idAge);
    } else {
      // On defini la note de scission en note mere
      $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}agenda SET age_mere_id=0, age_plage_duree=".($nbOccG2+$dateMaxG2)." WHERE age_id=".$idAge);
      // On redefini le nombre d'occurrences du second groupe de note et on les rattache a la note de scission
      $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}agenda SET age_mere_id=".$idAge.", age_plage_duree=".($nbOccG2+$dateMaxG2)." WHERE age_mere_id=".$idAgeMere." AND age_date>='".$ageDate."'");
    }
    $idAge = 0;
  }
  // Fin MOD Scission de note
  if ($idAge) {
    // Information par mail des personnes a qui on a affecte une note
    $DB_CX->DbQuery("SELECT util_email, tzn_libelle, tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver FROM ${PREFIX_TABLE}utilisateur, ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}timezone WHERE aco_age_id=".$idAge." AND util_id=aco_util_id AND util_id!=".$idUser." AND util_alert_affect='O' AND tzn_zone=util_timezone");
    if ($DB_CX->DbNumRows()) {
      $destMail = array();
      while ($enr = $DB_CX->DbNextRow()) {
        if (!empty($enr['util_email'])) {
          $destMail[$enr['util_id']] = $enr['util_email'];
          // Recuperation des infos de timezone
          $destMailTzLibelle[$enr['util_id']] = $enr['tzn_libelle'];
          $destMailTzGmt[$enr['util_id']] = $enr['tzn_gmt'];
          $destMailTzDateEte[$enr['util_id']] = $enr['tzn_date_ete'];
          $destMailTzHeureEte[$enr['util_id']] = $enr['tzn_heure_ete'];
          $destMailTzDateHiver[$enr['util_id']] = $enr['tzn_date_hiver'];
          $destMailTzHeureHiver[$enr['util_id']] = $enr['tzn_heure_hiver'];
        }
      }
      // On trouve la liste des timezones disctincts
      $destMailListTZ = array_unique($destMailTzLibelle);
      foreach ($destMailListTZ as $key=>$libTZ) {
        // Pour chaque TZ on recupere les id des utilisateurs
        $listID = array_keys($destMailTzLibelle,$libTZ);
        foreach ($listID as $idUtil) {
          // Pour chaque liste on genere le tableau des emails
          $tabDestMail[$key][] = $destMail[$idUtil];
        }
        // On genere le corps specifique au fuseau en court
        list($sHeureNote[$key],$thrfin,$tdtCrt,$tdtMdf,$tDate[$key]) = decaleNote($destMailTzGmt[$key],$destMailTzDateEte[$key],$destMailTzHeureEte[$key],$destMailTzDateHiver[$key],$destMailTzHeureHiver[$key],$dateJour,$dateNoteUTC,$zlHeureDebutUTC,$hrfin,$dtCrt,$dtMdf,1);
        $aDate = explode("-",$tDate[$key]);
        $tsNoteMail = mktime(floor($sHeureNote[$key]),($sHeureNote[$key]*60)%60,0,$aDate[1],$aDate[2],$aDate[0]);

        $corpsMail  = nl2br("<HTML><BODY>".sprintf(trad("TRAITEMENT_CREER_NOTE"),$sNomExpediteur).(($zlPeriodicite>1) ? trad("TRAITEMENT_RECURRENTE") : trad("TRAITEMENT_JOURNEE"))." <B>".date(trad("TRAITEMENT_FORMAT_DATE"),$tsNoteMail)."</B>\n\n<U>".trad("TRAITEMENT_LIBELLE")."</U>:&nbsp;".stripslashes($ztLibelle)."\n".((!empty($ztLieu)) ? "<U>".trad("TRAITEMENT_EMPLACEMENT")."</U>:&nbsp;".stripslashes($ztLieu)."\n" : "").((!empty($ztDetail)) ? "<U>".trad("TRAITEMENT_DETAIL")."</U>:&nbsp;".stripslashes($ztDetail) : "").signatureMail());
        envoiMail($sNomExpediteur, $sMailExpediteur, $tabDestMail[$key], trad("TRAITEMENT_NOTIF_AJOUT"), $corpsMail);
      }
    }
    // MOD Copie note par mail
    if (count($tabEmailCopie)>0) {
      $copieMailSujet = trad("TRAITEMENT_NOTIF_AJOUT");
      $tsNoteMail = mktime($hNote,$mNote,0,$tabDate[1],$tabDate[0],$tabDate[2]);
      $copieMailCorps = nl2br("<HTML><BODY>".sprintf(trad("TRAITEMENT_CREER_NOTE"),$sNomExpediteur).(($zlPeriodicite>1) ? trad("TRAITEMENT_RECURRENTE") : trad("TRAITEMENT_JOURNEE"))." <B>".date(trad("TRAITEMENT_FORMAT_DATE"),$tsNoteMail)."</B>\n\n<U>".trad("TRAITEMENT_LIBELLE")."</U>:&nbsp;".stripslashes($ztLibelle)."\n".((!empty($ztLieu)) ? "<U>".trad("TRAITEMENT_EMPLACEMENT")."</U>:&nbsp;".stripslashes($ztLieu)."\n" : "").((!empty($ztDetail)) ? "<U>".trad("TRAITEMENT_DETAIL")."</U>:&nbsp;".stripslashes($ztDetail) : "").signatureMail());
      envoiMail($sNomExpediteur, $sMailExpediteur, $tabEmailCopie, $copieMailSujet, $copieMailCorps);
    }
    // Fin MOD Copie note par mail
    // Si la liste des destinataires non conserves est non nulle alors on les avertit
    if (count($tabSupDestMail)>0) {
      // On distingue chaque fuseau pour l'envoi
      foreach ($aDestListTZ as $key=>$libTZ) {
        envoiMail($sNomExpediteur, $sMailExpediteur, $tabSupDestMail[$key], $sSujet, $aCorps[$key]);
      }
    }
    // Requete generique
    $sql = "INSERT INTO ${PREFIX_TABLE}agenda (age_mere_id,age_util_id,age_aty_id,age_date,age_heure_debut,age_heure_fin,age_ape_id, age_periode1, age_periode2, age_periode3, age_periode4, age_plage, age_plage_duree, age_libelle, age_detail, age_rappel, age_rappel_coeff, age_email, age_prive, age_couleur, age_nb_participant, age_createur_id, age_disponibilite, age_date_creation, age_date_modif, age_modificateur_id, age_lieu, age_cal_id, age_email_contact, age_email_copie) ";
    $sql .= "VALUES (".$idAge.",".$idUser.",".$ckTypeNote.",'{theNewDate}',{theBeginHour},{theEndHour},".$zlPeriodicite.",".$periode1.",".$periode2.",".$periode3.",".$periode4.",".$rdPlage.",".($nbOccurrence + $dateMax).",'".$ztLibelle."','".$ztDetail."', ".$zlR1.",".$zlR2.",".$ckEmail.",".$rdPrive.",'".$zlCouleur."',".count($idParticipant).",".$idUser.",".$rdDispo.",'".$dateCreation."','".gmdate("Y-m-d H:i:s", time())."',".$idUser.",'".$ztLieu."',".$zlContactAssocie.",".$ckEmailContact.",'".$ztEmailCopie."')";
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
            if ($rdM == 1) {
              $jSelect = $zlM1;
            } elseif ($zlM3 == 9) { // le dernier jour du mois
              $jSelect = date("t",mktime($hNote,$mNote,0,$tabDate[1]+($ztM*$i),1,$tabDate[2]));
            } else {
              $jSelect = calcJour($zlM2,$zlM3,$tabDate[1]+($ztM*$i),$tabDate[2]);
            }
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
          if ($rdM == 1) {
            $jSelect = $zlM1;
          } elseif ($zlM3 == 9) { // le dernier jour du mois
            $jSelect = date("t",mktime($hNote,$mNote,0,$tabDate[1]+$ztM,1,$tabDate[2]));
          } else {
            $jSelect = calcJour($zlM2,$zlM3,$tabDate[1]+$ztM,$tabDate[2]);
          }
          $tsNote = mktime($hNote,$mNote,0,$tabDate[1]+$ztM,$jSelect,$tabDate[2]);
          while ($tsNote <= $dateMax) {
            // On calcule en utc pour la detection terminee et alerte
            $tsNoteUTC = mktime($hNoteUTC,$mNoteUTC,0,$tabDateUTC[1]+($ztM*$i),$jSelect,$tabDateUTC[2]);
            insertOccurrence();
            $i++;
            if ($rdM == 1) {
              $jSelect = $zlM1;
            } elseif ($zlM3 == 9) { // le dernier jour du mois
              $jSelect = date("t",mktime($hNote,$mNote,0,$tabDate[1]+($ztM*$i),1,$tabDate[2]));
            } else {
              $jSelect = calcJour($zlM2,$zlM3,$tabDate[1]+($ztM*$i),$tabDate[2]);
            }
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
}


/*--------------------------------------------
           GESTION DES ANNIVERSAIRES
--------------------------------------------*/
elseif ($ztFrom == "anniv") {
  if ($tcMenu>=_MENU_DISP_HEBDO)
    $tcMenu = _MENU_PLG_QUOT;
  if ($ztAction != "DELETE") {
    $tabDate = explode("/",$ztDate);
    $dateAnnivOK = false;
    if (checkdate($tabDate[1],$tabDate[0],$tabDate[2])) {
      $ztDate = $tabDate[2]."-".$tabDate[1]."-".$tabDate[0];
      $dateAnnivOK = true;
    } else {
      $msg=16;
      $sTmp .= "&tcType="._TYPE_ANNIV;
    }
  }
  if ($ztAction == "INSERT" && $dateAnnivOK) {
    $sd = date("Y")."-".$tabDate[1]."-".$tabDate[0];
    $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}agenda (age_util_id,age_aty_id,age_date,age_libelle,age_createur_id,age_date_creation,age_modificateur_id,age_date_modif) VALUES (".$idUser.",1,'".$ztDate."','".$ztLibelle."',".$idUser.",'".gmdate("Y-m-d H:i:s", time())."',".$idUser.",'".gmdate("Y-m-d H:i:s", time())."')");
    $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}agenda_concerne VALUES (".$DB_CX->DbInsertID().",".$idUser.",1,0)");
    $msg=12;
  }

  elseif ($ztAction == "UPDATE" && $idAge && $dateAnnivOK) {
    $sd = date("Y")."-".$tabDate[1]."-".$tabDate[0];
    $sql = "UPDATE ${PREFIX_TABLE}agenda ";
    $sql .= "SET age_date='".$ztDate."',";
    $sql .= " age_libelle='".$ztLibelle."',";
    $sql .= " age_date_modif='".gmdate("Y-m-d H:i:s", time())."',";
    $sql .= " age_modificateur_id=".$idUser." ";
    $sql .= "WHERE age_id=".$idAge." AND age_util_id=".$idUser;
    $DB_CX->DbQuery($sql);
    $msg=13;
  }

  elseif ($ztAction == "DELETE" && $idAge) {
    $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda WHERE age_id=".$idAge." AND age_util_id=".$idUser);
    $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda_concerne WHERE aco_age_id=".$idAge." AND aco_util_id=".$idUser);
    $msg=14;
  }
}


/*--------------------------------------------
           GESTION DES EVENEMENTS
--------------------------------------------*/
elseif ($ztFrom == "evenement") {
  $sTmp .= "&tcType="._TYPE_EVENEMENT;
  if ($ztAction != "DELETE") {
    if ($ckPartage!="O")
      $ckPartage = "N";
    list($jDeb,$mDeb,$aDeb) = explode("/",$ztDateDebut);
    if (empty($ztDateFin)) { // Si la date de fin n'est pas renseignee -> on prend la date de debut
      $ztDateFin = $ztDateDebut;
    }
    list($jFin,$mFin,$aFin) = explode("/",$ztDateFin);
    $eventOK = false;
    if (checkdate($mDeb,$jDeb,$aDeb) && checkdate($mFin,$jFin,$aFin) && !empty($ztLibelle)) {
      $ztDateDebut = $aDeb."-".$mDeb."-".$jDeb;
      $ztDateFin = $aFin."-".$mFin."-".$jFin;
      $openEvtAnnee = $aDeb;
      $eventOK = true;
    } else {
      $msg=16;
    }
  }
  if ($ztAction == "INSERT" && $eventOK) {
    $sd = $ztDateDebut;
    $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}evenement (eve_date_debut,eve_date_fin,eve_libelle,eve_type,eve_couleur,eve_util_id,eve_partage) VALUES ('".$ztDateDebut."','".$ztDateFin."','".$ztLibelle."',".$rdType.",'".$ztCouleur."',".$idUser.",'".$ckPartage."');");
    $msg=18;
  }

  elseif ($ztAction == "UPDATE" && $idEvt && $eventOK) {
    $sd = $ztDateDebut;
    $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}evenement  SET eve_date_debut='".$ztDateDebut."', eve_date_fin='".$ztDateFin."', eve_libelle='".$ztLibelle."', eve_type=".$rdType.", eve_couleur='".$ztCouleur."', eve_partage='".$ckPartage."' WHERE eve_id=".$idEvt.(($MODIF_PARTAGE) ? "" : " AND eve_util_id=".$idUser));
    $msg=19;
  }

  elseif ($ztAction == "DELETE" && $idEvt) {
    $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}evenement WHERE eve_id=".$idEvt.(($MODIF_PARTAGE) ? "" : " AND eve_util_id=".$idUser));
    $msg=20;
  }
  $sTmp .= "&tcType="._TYPE_EVENEMENT."&openEvtAnnee=".$openEvtAnnee;
}


/*--------------------------------------------
               GESTION DES MEMOS
--------------------------------------------*/
elseif ($ztFrom == "memo") {
  $sTmp .= "&tcType="._TYPE_MEMO;
  if ($ckProgress != "O")
    $ckProgress = "N";
  if ($ckPartage != "O")
    $ckPartage = "N";
  if ($ztAction == "INSERT" && !empty($ztTitre)) {
	//Mod MemoProgress
	$date_actuelle=date("d/m/Y");
    $heure_actuelle=date("H:i");
	if ($zlUtilisateur != $idUser) {
		$DB_CX->DbQuery("SELECT util_nom,util_prenom FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$idUser);
		$origine_memo=$DB_CX->DbNextRow();
		$DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}memo (mem_titre, mem_date, mem_contenu, mem_util_id, mem_partage, mem_progress) VALUES ('".trim($ztTitre)."  de $origine_memo[1] $origine_memo[0]','$date_actuelle à $heure_actuelle','".trim($ztContenu)."',".$zlUtilisateur.",'".$ckPartage."','".$ckProgress."')");
		} else {
			$DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}memo (mem_titre, mem_date, mem_contenu, mem_util_id, mem_partage, mem_progress) VALUES ('".trim($ztTitre)."  ','$date_actuelle à $heure_actuelle','".trim($ztContenu)."',".$zlUtilisateur.",'".$ckPartage."','".$ckProgress."')"); 
		}
	//fin Mod MemoProgress
  }

  elseif ($ztAction == "UPDATE" && $id && !empty($ztTitre)) {
    $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}memo SET mem_titre='".$ztTitre."', mem_contenu='".$ztContenu."', mem_partage='".$ckPartage."', mem_progress='".$ckProgress."' WHERE mem_id=".$id.(($MODIF_PARTAGE) ? "" : " AND mem_util_id=".$idUser));
  }

  elseif ($ztAction == "DELETE" && $id) {
    $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}memo WHERE mem_id=".$id.(($MODIF_PARTAGE) ? "" : " AND mem_util_id=".$idUser));
  }
}


/*--------------------------------------------
              GESTION DES LIBELLES
--------------------------------------------*/
elseif ($ztFrom == "libelles") {
  $sTmp .= "&tcType="._TYPE_LIBELLE;
  if ($ckPartage != "O")
    $ckPartage = "N";
  if ($ckJournee == "1")
    $zlDuree = "0";
  if ($ztAction == "INSERT" && !empty($ztLibelle)) {
    $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}libelle (lib_nom,lib_duree,lib_couleur,lib_util_id, lib_partage, lib_detail) VALUES ('".$ztLibelle."',".$zlDuree.",'".$zlCouleur."',".$idUser.",'".$ckPartage."','".$ztDetail."')");
  } elseif ($ztAction == "UPDATE" && $id && !empty($ztLibelle)) {
    $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}libelle SET lib_nom='".$ztLibelle."',lib_duree=".$zlDuree.",lib_couleur='".$zlCouleur."',lib_partage='".$ckPartage."',lib_detail='".$ztDetail."' WHERE lib_id=".$id.(($MODIF_PARTAGE) ? "" : " AND lib_util_id=".$idUser));
  } elseif ($ztAction == "DELETE" && $id) {
    $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}libelle WHERE lib_id=".$id.(($MODIF_PARTAGE) ? "" : " AND lib_util_id=".$idUser));
  }
}


/*--------------------------------------------
              GESTION DES FAVORIS
--------------------------------------------*/
elseif ($ztFrom == "favoris") {
  $zlGroupe += 0;
  if ($ckPartage!="O")
    $ckPartage = "N";
  if ($ztAction == "INSERT") {
    $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}favoris (fav_nom, fav_url, fav_commentaire, fav_util_id, fav_fgr_id, fav_partage) VALUES ('".$ztNom."','".$ztURL."','".$ztCommentaire."',".$idUser.",".$zlGroupe.",'".$ckPartage."')");
    $openFavGrp = $zlGroupe;
  } elseif ($ztAction == "UPDATE" && $id) {
    $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}favoris SET fav_nom='".$ztNom."', fav_url='".$ztURL."', fav_commentaire='".$ztCommentaire."', fav_fgr_id=".$zlGroupe.", fav_partage='".$ckPartage."' WHERE fav_id=".$id.(($MODIF_PARTAGE) ? "" : " AND fav_util_id=".$idUser));
    $openFavGrp = $zlGroupe;
  } elseif ($ztAction == "DELETE" && $id) {
    $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}favoris WHERE fav_id=".$id.(($MODIF_PARTAGE) ? "" : " AND fav_util_id=".$idUser));
  }
  $sTmp .= "&tcType="._TYPE_FAVORIS."&openFavGrp=".$openFavGrp;
}


/*--------------------------------------------
            GESTION DES ACHEVEMENTS
--------------------------------------------*/
elseif ($ztAction == "TERMINE" && $idAge) {
  $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}agenda_concerne SET aco_termine= 1-aco_termine WHERE aco_age_id=".$idAge." AND aco_util_id=".$USER_SUBSTITUE);

  if ($comp == 1) {
    Header("location: blank.html");
    exit;
  } else {
    $DB_CX->DbQuery("SELECT age_date FROM ${PREFIX_TABLE}agenda WHERE age_id=".$idAge);
    $sd = $DB_CX->DbResult(0,0);
  }
}


/*--------------------------------------------
               GESTION DU PROFIL
--------------------------------------------*/
elseif ($ztFrom == "profil" && $ztAction == "UPDATE") {
  // Recuperation des Saisies
  if ($rdTelephone!="N")
    $rdTelephone = "O";
  $zlPlanning += 0;
  if (($droit_PROFILS >= _DROIT_PROFIL_AUTRE_PARAM_PARTAGE) or (($droit_PROFILS >= _DROIT_PROFIL_PARAM_PARTAGE) and ($idUser==$USER_SUBSTITUE))) {
    if ($rdPartage=="2" && empty($ztPartage) && empty($ztPrtGroupe))
      $rdPartage = "0";
    $rdPartage += 0;
    if (($zlAffectation=="2" && empty($ztPartage) && empty($ztPrtGroupe)) || ($zlAffectation=="3" && empty($ztAffecte) && empty($ztAffGroupe)))
      $zlAffectation = "0";
    $zlAffectation += 0;
  }
  if ($ckAlertEmail!="O")
    $ckAlertEmail = "N";
  // Mod Son
  if ($ckRappelSon!="O")
    $ckRappelSon = "N";
  // Mod Son
  elseif ($ztEmail=="" || !$zlAffectation)
    $ckAlertEmail="N";
  if ($zlPrecision!="2")
    $zlPrecision = "1";
  $SEMAINE_TYPE= "";
  for ($i=1; $i<8; $i++)
    $SEMAINE_TYPE .= ${"bt".$i} + 0;
  if ($rdRappel != 2) {
    $zlRappelDelai = 0;
    $zlRappelType  = 1;
    $ckRappelEmail = 0;
  } elseif ($ckRappelEmail != 1)
    $ckRappelEmail = 0;
  if ($zlFormatNom!="1")
    $zlFormatNom = "0";
  if ($zlMenuDispo!="9")
    $zlMenuDispo = "8";
  if ($ztCodeURL=="")
    $ztCodeURL = md5(uniqid(rand()));
  if ($rdBarree!="N")
    $rdBarree = "O";
  if ($rdOnClick!="N")
    $rdOnClick = "O";
  if ($rdRappelAnniv != 2) {
    $zlRappelAnniv = 0;
    $zlRappelAnnivCoeff = 1440;
    $ckAnnivEmail = 0;
  } elseif ($ckAnnivEmail != 1)
    $ckAnnivEmail = 0;
  if ($ckFuseauPartage!="O")
    $ckFuseauPartage = "N";
  if ($zlFCKE!="O")
    $zlFCKE = "N";
    //  MODS menu note
    if ($rdMenuNote!="O")
      $rdMenuNote = "N";
    //  MODS menu note

  // Verifie si le login choisi n'est pas deja utilise
  $DB_CX->DbQuery("SELECT util_id FROM ${PREFIX_TABLE}utilisateur WHERE util_login='".$ztLogin."' AND util_id!=".$USER_SUBSTITUE);
  if (!$DB_CX->DbNumRows()) {
    $passOK = true;
    if (!empty($ztPasswdMD5)) {
      // Verification de l'ancien mot de passe
      $DB_CX->DbQuery("SELECT util_passwd FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$USER_SUBSTITUE);
      $verif_pwd = $DB_CX->DbResult(0,0);
      if ($ztOldPasswdMD5 != $verif_pwd) {
        // Mot de passe invalide
        $passOK = false;
        $tcMenu = _MENU_PROFIL;
        $msg = 4;
      } else
        $sqlPasswd = ", util_passwd='".$ztPasswdMD5."'";
    } elseif ($COOKIE_AUTH) {
      // On recupere le mot de passe dans le cookie
      if (!empty($_COOKIE) && isset($_COOKIE[$COOKIE_NOM]))
        $tabLog = explode(":",$_COOKIE[$COOKIE_NOM]);
      elseif (!empty($HTTP_COOKIE_VARS) && isset($HTTP_COOKIE_VARS[$COOKIE_NOM]))
        $tabLog = explode(":",$HTTP_COOKIE_VARS[$COOKIE_NOM]);
      $ztPasswdMD5 = (get_magic_quotes_gpc()) ? stripslashes($tabLog[1]) : $tabLog[1];
    }
    if ($passOK) {
      if (($droit_PROFILS >= _DROIT_PROFIL_AUTRE_PARAM_PARTAGE) or (($droit_PROFILS >= _DROIT_PROFIL_PARAM_PARTAGE) and ($idUser==$USER_SUBSTITUE))) {
      // Partage du planning en consultation
      $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}planning_partage WHERE ppl_util_id=".$USER_SUBSTITUE);
      if ($rdPartage==2) {// Si partage selectif uniquement
        $tabPartage = explode("+", $ztPartage);
        for ($i=0;$i<count($tabPartage);$i++) {
          if ($tabPartage[$i]!="0") {
            $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}planning_partage VALUES ('".$USER_SUBSTITUE."','".$tabPartage[$i]."','0')");
          }
        }

        $tabPrtPartage = explode("+", $ztPrtGroupe);
        for ($ij=0;$ij<count($tabPrtPartage);$ij++) {
          list ($grpg, $ztPrtGroupe) = explode ('|', $tabPrtPartage[$ij]);
          $PrtGroupe = explode(",", $ztPrtGroupe);
          for ($i=0;$i<count($PrtGroupe);$i++) {
            if ($PrtGroupe[$i]!="0") $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}planning_partage VALUES ('".$USER_SUBSTITUE."','".$PrtGroupe[$i]."','".$grpg."')");
          }
        }
      }

      // Partage du planning en modification
      $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}planning_affecte WHERE paf_util_id=".$USER_SUBSTITUE);
      if ($zlAffectation==3) {// Si affectation selective uniquement
        $tabAffecte = explode("+", $ztAffecte);
        for ($i=0;$i<count($tabAffecte);$i++)
          if ($tabAffecte[$i]!="0") $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}planning_affecte VALUES ('".$USER_SUBSTITUE."','".$tabAffecte[$i]."','0')");
        $tabAffPartage = explode("+", $ztAffGroupe);
        for ($ij=0;$ij<count($tabAffPartage);$ij++) {
          list ($grpg, $ztAffGroupe) = explode ('|', $tabAffPartage[$ij]);
          $AffGroupe = explode(",", $ztAffGroupe);
          for ($i=0;$i<count($AffGroupe);$i++) {
            if ($AffGroupe[$i]!="0") $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}planning_affecte VALUES ('".$USER_SUBSTITUE."','".$AffGroupe[$i]."','".$grpg."')");
          }
        }
      } elseif ($zlAffectation==2) {// Si consultation basee sur la liste du partage
        if ($rdPartage!=2)
          $zlAffectation=$rdPartage;
        else {
          for ($i=0;$i<count($tabPartage);$i++)
            if ($tabPartage[$i]!="0") {
              $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}planning_affecte VALUES ('".$USER_SUBSTITUE."','".$tabPartage[$i]."','0')");
            }
            for ($ij=0;$ij<count($tabPrtPartage);$ij++) {
              list ($grpg, $ztPrtGroupe) = explode ('|', $tabPrtPartage[$ij]);
              $PrtGroupe = explode(",", $ztPrtGroupe);
              for ($i=0;$i<count($PrtGroupe);$i++) {
                if ($PrtGroupe[$i]!="0") $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}planning_affecte VALUES ('".$USER_SUBSTITUE."','".$PrtGroupe[$i]."','".$grpg."')");
              }
            }
          }
        }
      }
      // Verifie si le code pour l'export URL n'est pas deja utilise
      $DB_CX->DbQuery("SELECT util_id FROM ${PREFIX_TABLE}utilisateur WHERE util_url_export='".$ztCodeURL."' AND util_id!=".$USER_SUBSTITUE);
      if (!$DB_CX->DbNumRows()) {
        $sql = "UPDATE ${PREFIX_TABLE}utilisateur SET";
        $sql .= " util_nom='".(($AUTO_UPPERCASE == true) ? strtoupper($ztNom) : ucfirst(strtolower($ztNom)))."',";
        $sql .= " util_prenom='".ucfirst($ztPrenom)."',";
        $sql .= " util_login='".$ztLogin."',";
        $sql .= " util_interface='".$zlInterface."',";
        $sql .= " util_debut_journee='".$zlHeureDebut."',";
        $sql .= " util_fin_journee='".$zlHeureFin."',";
        $sql .= " util_telephone_vf='".$rdTelephone."',";
        $sql .= " util_planning=".$zlPlanning.",";
        if (($droit_PROFILS >= _DROIT_PROFIL_AUTRE_PARAM_PARTAGE) or (($droit_PROFILS >= _DROIT_PROFIL_PARAM_PARTAGE) and ($idUser==$USER_SUBSTITUE))){
          $sql .= " util_partage_planning='".$rdPartage."',";
        }
        $sql .= " util_email=LOWER('".$ztEmail."'),";
        if (($droit_PROFILS >= _DROIT_PROFIL_AUTRE_PARAM_PARTAGE) or (($droit_PROFILS >= _DROIT_PROFIL_PARAM_PARTAGE) and ($idUser==$USER_SUBSTITUE))){
          $sql .= " util_autorise_affect='".$zlAffectation."',";
        }
        $sql .= " util_alert_affect='".$ckAlertEmail."',";
        $sql .= " util_precision_planning='".$zlPrecision."',";
        $sql .= " util_semaine_type='".$SEMAINE_TYPE."',";
        $sql .= " util_duree_note='".$zlDureeNote."',";
        $sql .= " util_rappel_delai=".$zlRappelDelai.",";
        $sql .= " util_rappel_type=".$zlRappelType.",";
        $sql .= " util_rappel_email=".$ckRappelEmail.",";
        // Mod Son
        $sql .= " util_rappel_son='".$ckRappelSon."',";
        $sql .= " util_choix_son='".$zlSon."',";
        // Mod Son
        $sql .= " util_format_nom='".$zlFormatNom."',";
        $sql .= " util_menu_dispo='".$zlMenuDispo."',";
        $sql .= " util_url_export='".$ztCodeURL."',";
        $sql .= " util_note_barree='".$rdBarree."',";
        // MOD Couleur par defaut
        $sql .= " util_couleur='".$zlCouleur."',";
        // Fin MOD Couleur par defaut
        $sql .= " util_menuonclick='".$rdOnClick."',";
        $sql .= " util_rappel_anniv=".$zlRappelAnniv.",";
        $sql .= " util_rappel_anniv_coeff=".$zlRappelAnnivCoeff.",";
        $sql .= " util_rappel_anniv_email=".$ckAnnivEmail.",";
        $sql .= " util_langue='".$zlLangue."',";
        $sql .= " util_fcke='".$zlFCKE."',";
        $sql .= " util_fcke_toolbar='".$zlFCKEbar."',";
        //  Mod fcke_aff_toolbar
        $sql .= " util_fcke_aff_toolbar='".$zlFCKEbar_aff."',";
        //  Mod fcke_aff_toolbar
        $sql .= " util_timezone='".$zlFuseauHoraire."',";
        $sql .= " util_timezone_partage='".$ckFuseauPartage."',";
        // MOD meteo
    if ($ztMeteoCode!="") $ztMeteo=$ztMeteoCode.";".$ztMeteoActif;
        $sql .= " util_meteo_code='".$ztMeteo."',";
        // fin mod meteo
        // MOD horoscope v1.1
        $sql .= " util_horo='".$ztHoroscope."',";
        // fin mod horoscope
		// MOD D&D
		$sql .= " util_dd='".$ztDDActif."',";
		// fin mod D&D
          //  MODS menu note
          $sql .= " util_menu_note='".$rdMenuNote."',";   
          //  MODS menu note
        $sql .= " util_format_heure='".$zlFormatHeure."'";
        $sql .= $sqlPasswd." WHERE util_id=".$USER_SUBSTITUE;
        $DB_CX->DbQuery($sql);

        if ($droit_PROFILS >= _DROIT_PROFIL_COMPLET or $idAdmin!=0) {
          if ($droit_Aff_Login!="1")
            $droit_Aff_Login = "0";
          if ($droit_Aff_MDP!="1")
            $droit_Aff_MDP = "0";
          if ($droit_Aff_THEME!="1")
            $droit_Aff_THEME = "0";
          $droit_Aff= $droit_Aff_Login.$droit_Aff_MDP.$droit_Aff_THEME;
          $sql = "UPDATE ${PREFIX_TABLE}droit SET";
          $sql .= " droit_profils=".$zlAMProfils.",";
          $sql .= " droit_agendas=".$zlAMAgendas.",";
          $sql .= " droit_notes=".$zlAMNotes.",";
          $sql .= " droit_aff='".$droit_Aff."'";
          $sql .= " WHERE droit_util_id=".$USER_SUBSTITUE;
          $DB_CX->DbQuery($sql);
        }
        // MAJ de la semaine type de l'utilisateur dans la table des sessions
        $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}sid SET sid_semaine_type='".$SEMAINE_TYPE."' WHERE sid_id='".$sid."'");
        $tcMenu = $tcPlg;
        $msg = 7;
        // MAJ du cookie d'identification
        if (($COOKIE_AUTH) && ($idUser == $USER_SUBSTITUE))
          setcookie($COOKIE_NOM, $ztLogin.":".$ztPasswdMD5.":".$tabLog[2].":".$hdScreen, time()+86400*$COOKIE_DUREE, "/", "", 0);

        if ($zlFuseauHoraireValid=="OUI") {
          // Timezone d'origine
          $DB_CX->DbQuery("SELECT tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver FROM ${PREFIX_TABLE}timezone WHERE tzn_zone='".$zlFuseauHoraireORG."'");
          $tzOrgGmt = $DB_CX->DbResult(0,"tzn_gmt");
          $tzOrgDateEte = $DB_CX->DbResult(0,"tzn_date_ete");
          $tzOrgHeureEte = $DB_CX->DbResult(0,"tzn_heure_ete");
          $tzOrgDateHiver = $DB_CX->DbResult(0,"tzn_date_hiver");
          $tzOrgHeureHiver = $DB_CX->DbResult(0,"tzn_heure_hiver");
          // Timezone desire
          $DB_CX->DbQuery("SELECT tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver FROM ${PREFIX_TABLE}timezone WHERE tzn_zone='".$zlFuseauHoraire."'");
          $tzChgGmt = $DB_CX->DbResult(0,"tzn_gmt");
          $tzChgDateEte = $DB_CX->DbResult(0,"tzn_date_ete");
          $tzChgHeureEte = $DB_CX->DbResult(0,"tzn_heure_ete");
          $tzChgDateHiver = $DB_CX->DbResult(0,"tzn_date_hiver");
          $tzChgHeureHiver = $DB_CX->DbResult(0,"tzn_heure_hiver");
          // Creation d'une nouvelle instance pour l'execution de requetes en boucle
          $DB = new Db($DB_CX->ConnexionID);
          // Calcul du decalage et mise a jour des notes
          $DB_CX->DbQuery("SELECT age_id, age_aty_id, age_date, age_heure_debut, age_heure_fin, age_date_creation, age_date_modif FROM ${PREFIX_TABLE}agenda WHERE age_util_id=".$USER_SUBSTITUE);
          while ($enr = $DB_CX->DbNextRow()) {
            list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif'],$enr['age_date']) = decaleNote($tzOrgGmt,$tzOrgDateEte,$tzOrgHeureEte,$tzOrgDateHiver,$tzOrgHeureHiver,$dateJour,$enr['age_date'],$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif'],1,0,1);
            list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif'],$enr['age_date']) = decaleNote($tzChgGmt,$tzChgDateEte,$tzChgHeureEte,$tzChgDateHiver,$tzChgHeureHiver,$dateJour,$enr['age_date'],$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif'],1,1,1);
            // Mise a jour des dates et heures de notes (pour les anniversaires uniquement la date de creation)
            if ($enr['age_aty_id']!=1) {
              $DB->DbQuery("UPDATE ${PREFIX_TABLE}agenda SET age_date='".$enr['age_date']."', age_heure_debut=".$enr['age_heure_debut'].", age_heure_fin=".$enr['age_heure_fin'].", age_date_creation='".$enr['age_date_creation']."', age_date_modif='".$enr['age_date_modif']."' WHERE age_id=".$enr['age_id']);
            } else {
              $DB->DbQuery("UPDATE ${PREFIX_TABLE}agenda SET age_date_creation='".$enr['age_date_creation']."' WHERE age_id=".$enr['age_id']);
            }
          }
        }
      } else {
        // Code export URL deja utilise
        $tcMenu = _MENU_PROFIL;
        $msg = 17;
      }
    }
  } else {
    // Login deja utilise
    $tcMenu = _MENU_PROFIL;
    $msg = 3;
  }
}


/*--------------------------------------------
          SUBSTITUTION D'UTILISATEUR
--------------------------------------------*/
elseif ($ztAction == "SUBST" && $suid) {
  $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}sid SET sid_util_subst_id=".$suid." WHERE sid_id='".$sid."'");
  // Si on accede a la substitution depuis la page des disponibilites, on redirige vers le planning quotidien
  if ($tcMenu==_MENU_DISP_QUOT)
    $tcMenu=_MENU_PLG_QUOT;
}


/*--------------------------------------------
              GESTION DES GROUPES
--------------------------------------------*/
elseif ($ztActionGrp == "SauvPref") {
  // Renseignement du type de groupe
  if ($tcMenu==_MENU_PLG_MENS_GBL || $tcMenu==_MENU_PLG_HEBDO_GBL || $tcMenu==_MENU_PLG_QUOT_GBL) {
    // Si l'on vient d'un planning global
    $typeGroupe = "0";
  } elseif ($tcMenu==_MENU_DISP_HEBDO || $tcMenu==_MENU_DISP_QUOT) {
    // Si l'on vient des disponibilites
    $typeGroupe = "1";
  }
  if (empty($sChoix)) {
    $sChoix = "0";
  }
  // Recuperation du groupe selectionne dans la liste ggr
  list ($grpID, $grpChoix) = explode ('|', $ggr);
  // Si la selection il n'y a pas d'identifiant de groupe renseigne
  if (!$grpID) {
    // On recherche s'il existe un groupe 'NoGroup' en base
    $DB_CX->DbQuery("SELECT ggr_id FROM ${PREFIX_TABLE}global_groupe WHERE ggr_nom='NoGroup' and ggr_util_id=".$idUser." AND ggr_type=".$typeGroupe);
    if ($DB_CX->DbNumRows()) {
      // Si OUI on recupere son identifiant
      $grpID = $DB_CX->DbResult(0,0);
    } else {
      // Si NON on le cree et on recupere son identifiant
      $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}global_groupe (ggr_util_id,ggr_nom,ggr_liste,ggr_aff,ggr_type) VALUES (".$idUser.",'NoGroup','".$sChoix."','O',".$typeGroupe.")");
      $grpID = $DB_CX->DbInsertID();
    }
  }
  // On met a jour le groupe (existant ou cree) avec la liste d'utilisateur selectionnee et on active son affichage
  $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}global_groupe SET ggr_liste='".$sChoix."', ggr_aff='O' WHERE ggr_id=".$grpID." AND ggr_type=".$typeGroupe);
  // On desactive l'affichage des autres groupes de l'utilisateur
  $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}global_groupe SET ggr_aff='N' WHERE ggr_id!=".$grpID." AND ggr_util_id=".$idUser." AND ggr_type=".$typeGroupe);

  // Enregistrement des options d'affichage
  // Precision / Heure debut / Heure fin
  $zlPrec+=0;
  $zlHD+=0;
  $zlHF+=0;
  // S'il existe des informations de precisions d'affichage, on les met a jour (si applicable)
  $infoPrecision = ($zlPrec) ? ", aff_precision='".$zlPrec."', aff_debut=".$zlHD.", aff_fin=".$zlHF : "";
  // Choix 'Figer la vue'
  if ($ckAffGr!="O")
    $ckAffGr="N";
  // Choix 'Afficher non consultable ou non affectable' selon le cas
  if ($ckAffCache!="O")
    $ckAffCache="N";
  // Recherche s'il existe deja des preferences d'affichage pour cet utilisateur
  $DB_CX->DbQuery("SELECT aff_util_id FROM ${PREFIX_TABLE}planning_affichage WHERE aff_util_id=".$idUser." AND aff_type=".$typeGroupe);
  if ($DB_CX->DbNumRows()) {
    // Si OUI on les met a jour
    $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}planning_affichage SET aff_figer='".$ckAffGr."', aff_user='".$ckAffCache."'".$infoPrecision." WHERE aff_util_id=".$idUser." AND aff_type=".$typeGroupe);
  } else {
    // Si NON on les cree
    $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}planning_affichage (aff_util_id,aff_type,aff_figer,aff_user,aff_precision,aff_debut,aff_fin) VALUES (".$idUser.",".$typeGroupe.",'".$ckAffGr."','".$ckAffCache."','".$zlPrec."',".$zlHD.",".$zlHF.")");
  }

  $msg = 21;
  $url = "&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."&msg=".$msg;
  $RetConsul = true;
}

elseif ($ztActionGrp == "SauvGrp") {
  if ($utilgr!="O") {
    list ($grpg, $GrChoix) = explode ('|', $ggr);
    $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}global_groupe SET ggr_liste='".$sChoix."' WHERE ggr_id=".$grpg."");
    $msg = 21;
    $url="&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."&msg=".$msg."&ggr=".$grpg."|".$sChoix."&ztActionGrp=NvGr";
    $RetConsul=true;
  } else {
    list ($grpg, $GrChoix) = explode ('|', $ggr);
    $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}groupe_util SET gr_util_liste='".$sChoix."' WHERE gr_util_id=".$grpg."");

    $tChoix= explode (',', $sChoix);
    $TabPartage= array();
    $DB_CX->DbQuery("SELECT DISTINCT ppl_util_id FROM ${PREFIX_TABLE}planning_partage WHERE ppl_gr=".$grpg."");
    while ($enr = $DB_CX->DbNextRow()) {
      $TabPartage[]=$enr['ppl_util_id'];
    }
    $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}planning_partage WHERE ppl_gr=".$grpg."");
    for ($j=0; $j<count($TabPartage); $j++) {
      for ($i=0; $i<count($tChoix); $i++) {
        $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}planning_partage VALUES ('".$TabPartage[$j]."','".$tChoix[$i]."','".$grpg."')");
      }
    }
    $TabAffecte= array();
    $DB_CX->DbQuery("SELECT DISTINCT paf_util_id FROM ${PREFIX_TABLE}planning_affecte WHERE paf_gr=".$grpg."");
    while ($enr = $DB_CX->DbNextRow()) {
      $TabAffecte[]=$enr['paf_util_id'];
    }
    $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}planning_affecte WHERE paf_gr=".$grpg."");
    for ($j=0; $j<count($TabAffecte); $j++) {
      for ($i=0; $i<count($tChoix); $i++) {
        $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}planning_affecte VALUES ('".$TabAffecte[$j]."','".$tChoix[$i]."','".$grpg."')");
      }
    }
    $msg = 21;
    $url = "&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."&msg=".$msg."&ggr=".$grpg."|".$sChoix."&ztActionGrp=NvGr&groupe=1";
    $RetConsul = true;
  }
}

elseif ($ztActionGrp == "SupGrp") {
  if ($utilgr!="O") {
    list ($grpg, $GrChoix) = explode ('|', $ggr);
    $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}global_groupe WHERE ggr_id=".$grpg."");
    $msg = 21;
    $url="&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."&msg=".$msg;
    $RetConsul=true;
  } else {
    list ($grpg, $GrChoix) = explode ('|', $ggr);
    $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}groupe_util WHERE gr_util_id=".$grpg."");
    $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}planning_affecte WHERE paf_gr=".$grpg."");
    $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}planning_partage WHERE ppl_gr=".$grpg."");
    $msg = 21;
    $url = "&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."&msg=".$msg."&groupe=1";
    $RetConsul = true;
  }
}

elseif ($ztActionGrp == "AjoutGgg") {
  if ($utilgr!="O") {
    $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}global_groupe VALUES ('','".$idUser."','".$ztNom."','".$sChoix."','N','".$typegr."')");
    $grpg = $DB_CX->DbInsertID();
    $msg = 21;
    $url="&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."&msg=".$msg."&ggr=".$grpg."|".$sChoix."&ztActionGrp=NvGr";
    $RetConsul=true;
  } else {
    $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}groupe_util VALUES ('','".$ztNom."','".$sChoix."')");
    $grpg = $DB_CX->DbInsertID();
    $msg = 21;
    $url = "&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."&msg=".$msg."&ggr=".$grpg."|".$sChoix."&ztActionGrp=NvGr&groupe=1&ztNom=".$ztNom;
    $RetConsul = true;
  }
}

elseif ($ztActionGrp == "ModifGgg") {
  if ($utilgr!="O") {
    $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}global_groupe SET ggr_nom='".$ztNom."', ggr_liste='".$sChoix."' WHERE ggr_id=".$grpg."");
    $msg = 21;
    $url="&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."&msg=".$msg."&ggr=".$grpg."|".$sChoix."&ztActionGrp=NvGr";
    $RetConsul=true;
  } else {
    $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}groupe_util SET gr_util_nom='".$ztNom."', gr_util_liste='".$sChoix."' WHERE gr_util_id=".$grpg."");
    $msg = 21;
    $url = "&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."&msg=".$msg."&ggr=".$grpg."|".$sChoix."&ztActionGrp=NvGr&groupe=1";
    $RetConsul = true;

    $tChoix= explode (',', $sChoix);
    $TabPartage= array();
    $DB_CX->DbQuery("SELECT DISTINCT ppl_util_id FROM ${PREFIX_TABLE}planning_partage WHERE ppl_gr=".$grpg."");
    while ($enr = $DB_CX->DbNextRow()) {
      $TabPartage[]=$enr['ppl_util_id'];
    }
    $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}planning_partage WHERE ppl_gr=".$grpg."");
    for ($j=0; $j<count($TabPartage); $j++) {
      for ($i=0; $i<count($tChoix); $i++) {
        $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}planning_partage VALUES ('".$TabPartage[$j]."','".$tChoix[$i]."','".$grpg."')");
      }
    }
    $TabAffecte= array();
    $DB_CX->DbQuery("SELECT DISTINCT paf_util_id FROM ${PREFIX_TABLE}planning_affecte WHERE paf_gr=".$grpg."");
    while ($enr = $DB_CX->DbNextRow()) {
      $TabAffecte[]=$enr['paf_util_id'];
    }
    $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}planning_affecte WHERE paf_gr=".$grpg."");
    for ($j=0; $j<count($TabAffecte); $j++) {
      for ($i=0; $i<count($tChoix); $i++) {
        $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}planning_affecte VALUES ('".$TabAffecte[$j]."','".$tChoix[$i]."','".$grpg."')");
      }
    }
    $msg = 21;
    $url = "&tcMenu=".$tcMenu."&tcPlg=".$tcPlg."&sd=".$sd."&msg=".$msg."&ggr=".$grpg."|".$sChoix."&ztActionGrp=NvGr&groupe=1";
    $RetConsul = true;
  }
}


/*--------------------------------------------
    DECONNEXION DU COMPTE D'ADMINISTRATION
--------------------------------------------*/
elseif ($ztDiscon == "Admin") {
  $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}sid SET sid_admin_id=0 WHERE sid_id='".$sid."'");
  $idAdmin = 0;
  $url = "&tcMenu=".$tcPlg;
  $RetConsul = true;
}

/*--------------------------------------------
              GESTION DES EMPLACEMENTS
--------------------------------------------*/
elseif ($ztFrom == "emplacement") {
  $sTmp .= "&tcType="._TYPE_EMPL;
  if ($ckPartLieu != "O")
    $ckPartLieu = "N";
  if ($ztAction == "INSERT" && !empty($ztLieu)) {
    $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}emplacement (empl_nom, empl_util_id, empl_type, empl_partage) VALUES ('".$ztLieu."',".$idUser.",".$rdType.",'".$ckPartLieu."')");
  } elseif ($ztAction == "UPDATE" && $id && !empty($ztLieu)) {
    $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}emplacement SET empl_nom='".$ztLieu."', empl_type=".$rdType.", empl_partage='".$ckPartLieu."' WHERE empl_id=".$id.(($MODIF_PARTAGE) ? "" : " AND empl_util_id=".$idUser));
  } elseif ($ztAction == "DELETE" && $id) {
    $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}emplacement WHERE empl_id=".$id.(($MODIF_PARTAGE) ? "" : " AND empl_util_id=".$idUser));
  }
}
  // Fermeture BDD
  $DB_CX->DbDeconnect();

  $tabDate = explode("-",$sd);
  if (!empty($tcPlg))
    $sTmp .= "&tcPlg=".$tcPlg;
  if (!$RetConsul) {
    $tsjour = mktime(0,0,0,intval($tabDate[1]),intval($tabDate[2]),$tabDate[0]);
    $url = $sTmp."&tcMenu=".(($ztAction == "UPDATE" && $RetProfil == "profil") ? _MENU_PROFIL : $tcMenu)."&sd=".$tsjour."&msg=".$msg;
    if (!empty($ggr)) {  // si on est en edition de note depuis les plannings globaux
      $url .= "&ggr=".$ggr."&ztActionGrp=".$ztActionGrp;
    }
    if (!empty($selOnglet)) {  // si on passe en substitution sur la page de profil
      $url .= "&selOnglet=".$selOnglet;
    }
  }
  if ($classSMTPLoaded)
    $mailer->smtp->quit();

  Header("location: agenda.php?sid=".$sid.$url);
?>
