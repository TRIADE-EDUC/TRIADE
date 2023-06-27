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
  include_once("../../common/config.inc.php");
  include_once("../../common/config2.inc.php");

  if (AGENDAPDA != "oui") {
	exit;
  }




  include("inc/param.inc.php");
  include("inc/html.inc.php");
  include("inc/fonctions.inc.php");
  include("lang/$APPLI_LANGUE.php");
// ----------------------------------------------------------------------------
// INITIALISATION DE L'AFFICHAGE
// ----------------------------------------------------------------------------
  $v += 0;
  $NOM_PAGE = basename($_SERVER['PHP_SELF']);
  // Variables servant a definir les couleurs utilisees dans le module Pocket PheniX
  // Communs
  $PPX_COULEUR_FOND_PAGE = "#FFFFFF";
  $PPX_TAILLE_TEXTE = "10";
  $PPX_COULEUR_TEXTE = "#000000";
  $PPX_COULEUR_LIEN = "#990000";
  $PPX_COULEUR_LIEN_SURVOL = "#999999";
  $PPX_APPARENCE_LIEN = "none";               // underline / none
  $PPX_APPARENCE_LIEN_SURVOL = "none";        // underline / none
  $PPX_COULEUR_BORDURE_TABLEAU = "#000000";
  $PPX_TAILLE_TABLEAU = "240";                // 100% ou une taille en pixels
  $PPX_COULEUR_FOND_COMMUN = "#FFFFFF";
  $PPX_COULEUR_MESSAGE_INFO = "#FF0000";
  $PPX_COULEUR_FOND_ACTION = "#F1F1F1";
  // Menu
  $PPX_COULEUR_FOND_TITRE = "#FF6600";
  $PPX_COULEUR_TEXTE_TITRE = "#000000";
  $PPX_COULEUR_FOND_MENU = "#FF9900";
  $PPX_COULEUR_FOND_MENU_ACTIF = "#FF6600";
  // Agenda
  $PPX_COULEUR_LIGNE_MOIS = "#FFCC33";
  $PPX_COULEUR_TEXTE_MOIS = "#000000";
  $PPX_COULEUR_LIGNE_JOUR_SEMAINE = "#FFFF33";
  $PPX_COULEUR_FOND_CALENDRIER = "#EEEEEE";
  $PPX_COULEUR_FOND_COLONNE_SEMAINE = "#FFFF33";
  $PPX_COULEUR_FOND_JOUR_COURANT = "#FFCC88";
  $PPX_COULEUR_SEMAINE_TYPE = "#444444";
  $PPX_COULEUR_HORS_SEMAINE_TYPE = "#EE0000";
  $PPX_COULEUR_MOIS_PREC = "#FFFFB4";
  $PPX_COULEUR_LIGNE_AUJOURDHUI = "#FFFF33";
  $PPX_COULEUR_LIGNE_NAVIGATION_JOUR = "#FFCC33";
  $PPX_COULEUR_LIGNE_ANNIVERSAIRE = "#FFFF33";
  $PPX_COULEUR_NOTE_PERSO = "#FFFFB4";
  $PPX_COULEUR_NOTE_AFFECTEE = "#A0E0DF";
  $PPX_COULEUR_HORAIRE_NOTE = "#003366";
  $PPX_COULEUR_DETAIL_NOTE = "#444444";
  // Calepin
  $PPX_COULEUR_ALPHABET = "#FFCC33";
  $PPX_COULEUR_ALPHABET_ACTIF = "#FF6600";
  $PPX_COULEUR_NB_RESULTAT = "#FFFF33";
  $PPX_COULEUR_NOM_CONTACT = "#4924FF";
  $PPX_COULEUR_LIGNE = array("#FFFFFF","#FFFFB4");
  // Pied de page
  $PPX_COULEUR_FOND_COPYRIGHT = "#FF9900";
  $PPX_COULEUR_NOM_APPLI = "#FFFFFF";
// ----------------------------------------------------------------------------
// FORMULAIRE D'IDENTIFICATION
// ----------------------------------------------------------------------------
  function formLog() {

    $nom=utf8_decode($_GET["nom"]);
    $prenom=utf8_decode($_GET["prenom"]);
    $mdp=$_GET["pass"];
    $membre=$_GET["membre"];

    global $APPLI_VERSION, $NOM_PAGE;
    echo "<html><head><title>".trad("PPX_TITRE")."-TRIADE</title></head>";
    echo "<body text=\"$PPX_COULEUR_TEXTE\" vlink=\"$PPX_COULEUR_LIEN\" link=\"$PPX_COULEUR_LIEN\" bgcolor=\"$PPX_COULEUR_FOND_PAGE\">";
    echo "<center><form method=\"post\" action=\"".$NOM_PAGE."\" target=\"_self\" name='formulaire'>";
    echo "Nom <br> <input type=\"text\" name=\"nom\" size=\"10\" value=\"$nom\" >";
    echo "<br>Prénom <br> <input type=\"text\" name=\"prenom\" value=\"$prenom\" size=\"10\">";
    echo "<br>".trad("PPX_MOT_DE_PASSE")."<br><input type=\"text\" name=\"pwd\" size=\"10\" value=\"$mdp\" >";
    if ($membre == "direction") {
	$selecteddir="selected='selected'";
    }
    if ($membre == "enseignant") {
	$selectedens="selected='selected'";
    }
    if ($membre == "viescolaire") {
	$selectedmvs="selected='selected'";
    }

    echo "<br>Membre <br> <select name=\"membre\"><option value='ADM' $selecteddir >Direction</option><option value='ENS' $selectedens >Enseignant</option><option value='MVS' $selectedmvs >Vie Scolaire</option></select><br>";
    echo "<br><input type=\"submit\" name=\"btOK\" value=\"".trad("PPX_BT_OK")."\"> <input type=\"reset\" name=\"btRaz\" value=\"".trad("PPX_BT_RAZ")."\"></form>";
    echo "<hr size=\"1\"><font color=\"$PPX_COULEUR_NOM_APPLI\">".sprintf(trad("PPX_VERSION_PHENIX"), $APPLI_VERSION)."</font><br>".trad("PPX_COPYRIGHT")."</center></body></html>";
  }
// ----------------------------------------------------------------------------
// IDENTIFICATION DE L'UTILISATEUR
// ----------------------------------------------------------------------------
  if ( (isset($btOK)) && (!isset($sid)) ) {


    // Cryptage du mot de passe
    // $ztPasswd = md5($pwd);
	  // Recherche de l'utilisateur correspondant
	  //
    
    global $gestionMDP;
    $gestionMDP=GESTIONMDP;

function cryptage($mdp) {
	global $gestionMDP;
	if ($gestionMDP == "MD5") { 
		$mdp=md5($mdp); 
	}else{
		$mdp=crypt(md5($mdp),"T2");
	}
	return $mdp;
}


    $prefixe=PREFIXE;
    $pwd=cryptage($pwd);

    $DB_CX->DbQuery("SELECT pers_id FROM ${prefixe}personnel WHERE lower(trim(nom))='$nom' AND  lower(trim(prenom))='$prenom' AND mdp='$pwd' AND
    	     type_pers='$membre' AND offline = '0'");
    if (!$DB_CX->DbNumRows()) {
	  Header("location: ppx.php?error=1");
	  exit;
    }
    $idpers = $DB_CX->DbResult(0,0);

    if ($membre == "ADM") { $membre="menuadmin"; }
    if ($membre == "ENS") { $membre="menuprof"; }
    if ($membre == "MVS") { $membre="menuadmin"; }
    if ($membre == "PER") { $membre="menupersonnel"; }
    if ($membre == "TUT") { $membre="menututeur"; }
    if ($membre == "PAR") { $membre="menuparent"; }
    if ($membre == "ELE") { $membre="menueleve"; }


    $DB_CX->DbQuery("SELECT idtriade,idphenix,membre FROM ${PREFIX_TABLE}tria2phenix WHERE idtriade='$idpers' AND membre='$membre'");
    if (!$DB_CX->DbNumRows()) {
	  Header("location: ppx.php?error=1");
	  exit;
    }

    $idpers = $DB_CX->DbResult(0,0);
    $membre = $DB_CX->DbResult(0,2);
    $log="$membre$idpers";
 

    $DB_CX->DbQuery("SELECT util_id, util_semaine_type FROM ${PREFIX_TABLE}utilisateur WHERE util_login='".$log."'");
    
  
    if ($DB_CX->DbNumRows()) {
	    // L'utilisateur existe

	   $idUser = $DB_CX->DbResult(0,0);
	
      $SEMAINE_TYPE = $DB_CX->DbResult(0,1);
      $SEMAINE_TYPE = ($SEMAINE_TYPE!="0000000") ? $SEMAINE_TYPE : "1111100";
      // On genere un nouveau sid
      mt_srand((double)microtime()*1000000);
      $sid = SessionId(8, $idUser, $SEMAINE_TYPE, 0, true);
    } else {
      // L'utilisateur n'existe pas
      // Fermeture BDD
      $DB_CX->DbDeconnect();
      formLog();
      exit;
    }
  }
  
  

  if (isset($_GET["error"])) {
	 $DB_CX->DbDeconnect();
         formLog();
	 exit;
  }



$idUser = Session_ok($sid, true);

// Ecrase la selection de la langue par defaut par le choix de l'utilisateur
  @include("lang/$APPLI_LANGUE.php");  
  

// ----------------------------------------------------------------------------
// RECUPERATION ET FORMATAGE DE LA DATE A TRAITER
// ----------------------------------------------------------------------------
  // Recuperation des infos de timezone de l'utilisateur
  $DB_CX->DbQuery("SELECT tzn_libelle, tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver, util_format_heure FROM ${PREFIX_TABLE}utilisateur, ${PREFIX_TABLE}timezone WHERE util_id=".$idUser." AND tzn_zone=util_timezone");
  $tzLibelle = htmlentities($DB_CX->DbResult(0,"tzn_libelle"));
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
  $closeForm = false;
// ----------------------------------------------------------------------------
// ENTETE HTML COMMUNE
// ----------------------------------------------------------------------------
  echo ("<html>
<head>
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
  <title>".trad("PPX_TITRE")."</title>
  <base target=\"_self\">
  <style type=\"text/css\">
  <!--
    BODY {
      FONT-FAMILY: Verdana, Arial, Tahoma;
      FONT-SIZE: ".$PPX_TAILLE_TEXTE."px;
      FONT-WEIGHT: normal;
      COLOR: $PPX_COULEUR_TEXTE;
      BACKGROUND-COLOR: $PPX_COULEUR_FOND_PAGE;
    }
    A, A:link, A:visited  {
      COLOR: $PPX_COULEUR_LIEN;
      TEXT-DECORATION: $PPX_APPARENCE_LIEN;
    }
    A:hover  {
      COLOR: $PPX_COULEUR_LIEN_SURVOL;
      TEXT-DECORATION: $PPX_APPARENCE_LIEN_SURVOL;
    }
    TABLE  {
      FONT-FAMILY: Verdana, Arial, Tahoma;
      FONT-SIZE: ".$PPX_TAILLE_TEXTE."px;
      COLOR: $PPX_COULEUR_TEXTE;
      BORDER-COLLAPSE: separate;
    }
    FORM {
      PADDING: 0px;
      MARGIN: 0px;
    }
    INPUT, SELECT, TEXTAREA {
      FONT-SIZE: ".$PPX_TAILLE_TEXTE."px;
    }
  -->
  </style>
</head>
<body text=\"$PPX_COULEUR_TEXTE\" vlink=\"$PPX_COULEUR_LIEN\" link=\"$PPX_COULEUR_LIEN\" bgcolor=\"$PPX_COULEUR_FOND_PAGE\" leftmargin=\"0\" topmargin=\"0\" rightmargin=\"0\" bottommargin=\"0\" marginwidth=\"0\" marginheight=\"0\">
<table width=\"$PPX_TAILLE_TABLEAU\" cellspacing=\"1\" cellpadding=\"0\" bgcolor=\"$PPX_COULEUR_BORDURE_TABLEAU\" align=\"center\"><tr align=\"center\" bgcolor=\"$PPX_COULEUR_FOND_TITRE\"><td colspan=\"4\"><font color=\"$PPX_COULEUR_TEXTE_TITRE\">".(($v>9) ? sprintf(trad("PPX_CALEPIN_DE"), $NOM_USER) : sprintf(trad("PPX_AGENDA_DE"), $NOM_USER))."</font></td></tr>\n");

  if ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR) {
    echo ("<tr align=\"center\" bgcolor=\"$PPX_COULEUR_FOND_MENU\">
<td width=\"25%\"".(($v<10 && $v!=2) ? " bgcolor=\"$PPX_COULEUR_FOND_MENU_ACTIF\"" : "")."><a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."\">".trad("PPX_MENU_AGENDA")."</a></td>
<td width=\"25%\"".(($v==2) ? " bgcolor=\"$PPX_COULEUR_FOND_MENU_ACTIF\"" : "")."><a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&v=2\">".trad("PPX_MENU_NOTE")."</a></td>
<td width=\"25%\"".(($v>9 && $v!=13) ? " bgcolor=\"$PPX_COULEUR_FOND_MENU_ACTIF\"" : "")."><a href=\"".$NOM_PAGE."?sid=".$sid."&v=10\">".trad("PPX_MENU_CALEPIN")."</a></td>
<td width=\"25%\"".(($v==13) ? " bgcolor=\"$PPX_COULEUR_FOND_MENU_ACTIF\"" : "")."><a href=\"".$NOM_PAGE."?sid=".$sid."&v=13\">".trad("PPX_MENU_CONTACT")."</a></td>
</tr>\n");
  } else {
    echo ("<tr align=\"center\" bgcolor=\"$PPX_COULEUR_FOND_MENU\">
<td width=\"25%\"".(($v<10 && $v!=2) ? " bgcolor=\"$PPX_COULEUR_FOND_MENU_ACTIF\"" : "")."><a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."\">".trad("PPX_MENU_AGENDA")."</a></td>
</tr>\n");
  }

// ----------------------------------------------------------------------------
// SELECTION DES CONTACTS PAR LA PREMIERE LETTRE DU NOM
// ----------------------------------------------------------------------------
  if ($v>9) {
    // Stockage des criteres de recherche
    $critRecherche = (!empty($lettre)) ? "&lettre=".$lettre : ((!empty($nom)) ? "&nom=".urlencode(stripslashes($nom)) : "");
    echo "<tr bgcolor=\"$PPX_COULEUR_ALPHABET\"><td colspan=\"4\">";
    echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\"><tr>";
    $A = ord("A");
    for ($i=$A;$i<$A+26;$i++) {
      if ($lettre==chr($i)) {
        $bgCoul = " bgcolor=\"$PPX_COULEUR_ALPHABET_ACTIF\" style=\"";
        if ($i>$A)
          $bgCoul .= "border-left:solid 1px $PPX_COULEUR_BORDURE_TABLEAU;";
        if ($i<$A+25)
          $bgCoul .= "border-right:solid 1px $PPX_COULEUR_BORDURE_TABLEAU;";
        $bgCoul .= "\"";
        $lienLettre = "<b>".chr($i)."</b>";
      } else {
        $bgCoul = "";
        $lienLettre = chr($i);
      }
      echo "<td align=\"center\"".$bgCoul."><a href=\"".$NOM_PAGE."?sid=".$sid."&v=11&lettre=".chr($i)."\">".$lienLettre."</a></td>";
    }
    echo "</tr></table></td></tr>";
  }
// ----------------------------------------------------------------------------
// MODULE ENREGISTREMENT D'UN CONTACT
// ----------------------------------------------------------------------------
  if ($v==14) {
    if (!empty($ztNom)) {
      $ztNom    = strtoupper($ztNom);
      $ztPrenom = ucwords(strtolower($ztPrenom));
      $ztVille  = ucwords(strtolower($ztVille));
      $ztPays   = ucwords(strtolower($ztPays));
      //Si la date de naissance saisie est erronee, on l'efface
      if (!empty($ztNaissance)) {
        list($D,$M,$Y) = explode("/",$ztNaissance);
        $ztNaissance = (@checkdate($M,$D,$Y)) ? "$Y-$M-$D" : "";
      }
      if (empty($ztICQ)) { $ztICQ = 0; }
      if ($ztPartage != "O") { $ztPartage = "N"; }

      $lettre = strtoupper($ztNom[0]);
      $critRecherche = "&lettre=".$lettre;

      if ($ztAction=="INSERT") {
        $sql  = "INSERT INTO ${PREFIX_TABLE}calepin (cal_societe,cal_nom,cal_prenom,cal_adresse,cal_cp,cal_ville,cal_pays,cal_domicile,cal_travail,cal_portable,cal_fax,cal_email,cal_emailpro,cal_icq,cal_aim,cal_msn,cal_yahoo,cal_date_naissance,cal_note,cal_partage,cal_util_id) ";
        $sql .= "VALUES ('".$ztSociete."','".$ztNom."','".$ztPrenom."','".$ztAdresse."','".$ztCP."','".$ztVille."','".$ztPays."','".$ztDomicile."','".$ztTravail."','".$ztPortable."','".$ztFax."','".$ztEmail."','".$ztEmailPro."',".$ztICQ.",'".$ztAIM."','".$ztMSN."','".$ztYahoo."','".$ztNaissance."','".$ztNote."','".$ztPartage."',".$idUser.")";
        if ($DB_CX->DbQuery($sql) && $DB_CX->DbAffectedRows()) {
          $id = $DB_CX->DbInsertID();
          $DB_CX->DbQuery("SELECT cgr_id FROM ${PREFIX_TABLE}calepin_groupe WHERE cgr_util_id=".$idUser." AND (cgr_nom='".htmlentities(trad("COMMUN_NON_CLASSE"))."' OR cgr_nom='".trad("COMMUN_NON_CLASSE")."')");
          if ($DB_CX->DbNumRows()) {
            $groupe = $DB_CX->DbResult(0,0);
          } else {
            $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}calepin_groupe (cgr_util_id, cgr_nom) VALUES (".$idUser.", '".trad("COMMUN_NON_CLASSE")."')");
            $groupe = $DB_CX->DbInsertID();
          }
          $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}calepin_appartient (cap_cgr_id, cap_cal_id) VALUES ('".$groupe."',".$id.")");
        }
      } elseif ($ztAction=="UPDATE" && $id) {
        $sql = "UPDATE ${PREFIX_TABLE}calepin ";
        $sql .= "SET cal_societe='".$ztSociete."', cal_nom='".$ztNom."', cal_prenom='".$ztPrenom."',";
        $sql .= " cal_adresse='".$ztAdresse."', cal_cp='".$ztCP."', cal_ville='".$ztVille."',";
        $sql .= " cal_pays='".$ztPays."', cal_domicile='".$ztDomicile."', cal_travail='".$ztTravail."',";
        $sql .= " cal_portable='".$ztPortable."', cal_fax='".$ztFax."', cal_email='".$ztEmail."',";
        $sql .= " cal_emailpro='".$ztEmailPro."', cal_icq=".$ztICQ.", cal_aim='".$ztAIM."',";
        $sql .= " cal_msn='".$ztMSN."', cal_yahoo='".$ztYahoo."', cal_date_naissance='".$ztNaissance."',";
        $sql .= " cal_note='".$ztNote."', cal_partage='".$ztPartage."' ";
        $sql .= "WHERE cal_id=".$id.(($MODIF_PARTAGE) ? "" : " AND cal_util_id=".$idUser);
        $DB_CX->DbQuery($sql);
      }
      //Renvoi vers l'affichage du detail du contact
      $v=12;
    } else {
      $v=10;
    }
  }
// ----------------------------------------------------------------------------
// MODULE SUPPRESSION D'UN CONTACT
// ----------------------------------------------------------------------------
  elseif ($v==15) {
    if ($id && $confirm!="1") {
      $closeForm = true;
      echo "<tr align=\"center\" bgcolor=\"$PPX_COULEUR_FOND_COMMUN\"><td colspan=\"4\"><form>";
      echo trad("PPX_JS_SUPPR_CONTACT")."<br><input type=\"button\" value=\"".strtoupper(trad("COMMUN_OUI"))."\" onclick=\"javascript: document.location.href='".$NOM_PAGE."?sid=".$sid."&id=".$id."&v=15&confirm=1".$critRecherche."'\"> <input type=\"button\" value=\"".strtoupper(trad("COMMUN_NON"))."\" onclick=\"javascript: document.location.href='".$NOM_PAGE."?sid=".$sid."&v=12&id=".$id.$critRecherche."'\">";
    } elseif ($id && $confirm=="1") {
      if ($DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}calepin WHERE cal_id=".$id.(($MODIF_PARTAGE) ? "" : " AND cal_util_id=".$idUser)) && $DB_CX->DbAffectedRows()>0) {
        $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}calepin_appartient WHERE cap_cal_id=".$id);
        $v=10;
      } else {
        $v=12;
      }
    } else {
      $v=10;
    }
  }
// ----------------------------------------------------------------------------
// FORMULAIRE DE RECHERCHE DANS LE CALEPIN
// ----------------------------------------------------------------------------
  if ($v==10) {
    echo "<tr bgcolor=\"$PPX_COULEUR_FOND_COMMUN\"><td colspan=\"4\" align=\"center\"><form method=\"post\" action=\"".$NOM_PAGE."?sid=".$sid."&v=11\">";
    echo "<table><tr><td align=\"center\">".trad("PPX_LIB_RECHERCHE_NOM")."<br><input type=\"text\" name=\"nom\" size=\"15\" value=\"\"> <input type=\"submit\" name=\"btOK\" value=\"".trad("PPX_BT_OK")."\"></td></tr></table></form>";
    echo "<br><font color=\"$PPX_COULEUR_MESSAGE_INFO\">".trad("PPX_SAISIR_NOM")."</font><br>";
  }
// ----------------------------------------------------------------------------
// RESULTAT DE LA RECHERCHE DANS LE CALEPIN
// ----------------------------------------------------------------------------
  elseif ($v==11) {
    $strOutput = "";
    if (empty($lettre) && empty($nom))
      echo "<tr bgcolor=\"$PPX_COULEUR_FOND_COMMUN\"><td colspan=\"4\" align=\"center\"><br><font color=\"$PPX_COULEUR_MESSAGE_INFO\">".trad("PPX_SAISIR_BRIBE")."</font><br>&nbsp;</td></tr>";
    else {
      $clauseWhere = (!empty($lettre)) ? "LOWER(cal_nom) LIKE LOWER('".$lettre."%')" : "(LOWER(cal_nom) LIKE LOWER('%".$nom."%') OR LOWER(cal_prenom) LIKE LOWER('%".$nom."%'))";
      $DB_CX->DbQuery("SELECT DISTINCT cal_id, CONCAT(cal_nom,' ',cal_prenom) AS nomContact, cal_domicile, cal_travail, cal_portable, cal_fax, cal_email, cal_emailpro FROM ${PREFIX_TABLE}calepin WHERE ".$clauseWhere." AND (cal_util_id=".$idUser." OR cal_partage='O') ORDER BY cal_nom ASC, cal_prenom ASC, cal_societe ASC");
      if ($DB_CX->DbNumRows()) {
        echo "<tr bgcolor=\"$PPX_COULEUR_NB_RESULTAT\" align=\"center\"><td colspan=\"4\">".sprintf(trad("PPX_NB_REPONSES"), $DB_CX->DbNumRows(), (($DB_CX->DbNumRows()>1)?trad("COMMUN_PLURIEL"):""))."</td></tr>";
        $index = 1;
        while ($enr = $DB_CX->DbNextRow()) {
          $index = 1 - $index;
          $strOutput .= "<tr bgcolor=\"".$PPX_COULEUR_LIGNE[$index]."\"><td colspan=\"4\" style=\"padding:2px;\"><a href=\"".$NOM_PAGE."?sid=".$sid."&v=12&id=".$enr['cal_id'].$critRecherche."\"><b>".trim($enr['nomContact'])."</b></a>";
          if ($enr['cal_domicile']!="") {
            $enr['cal_domicile'] = preg_replace( "/[^0-9+]+/","",$enr['cal_domicile']);
            $strOutput .= "<br><IMG src=\"image/calepin/telephone.gif\" border=0 width=18 height=14 vspace=1 align=\"absmiddle\">&nbsp;".$enr['cal_domicile'];
          }
          if ($enr['cal_travail']!="") {
            $enr['cal_travail'] = preg_replace( "/[^0-9+]+/","",$enr['cal_travail']);
            $strOutput .= "<br><IMG src=\"image/calepin/telephone2.gif\" border=0 width=18 height=14 vspace=1 align=\"absmiddle\">&nbsp;".$enr['cal_travail'];
          }
          if ($enr['cal_portable']!="") {
            $enr['cal_portable'] = preg_replace( "/[^0-9+]+/","",$enr['cal_portable']);
            $strOutput .= "<br><IMG src=\"image/calepin/portable.gif\" border=0 width=18 height=16 vspace=1 align=\"absmiddle\">&nbsp;".$enr['cal_portable'];
          }
          if ($enr['cal_fax']!="") {
            $enr['cal_fax'] = preg_replace( "/[^0-9+]+/","",$enr['cal_fax']);
            $strOutput .= "<br><IMG src=\"image/calepin/fax.gif\" border=0 width=16 height=15 vspace=1 hspace=1 align=\"absmiddle\">&nbsp;".$enr['cal_fax'];
          }
          if ($enr['cal_email']!="")
            $strOutput .= "<br><IMG src=\"image/calepin/email.gif\" border=0 width=18 height=16 vspace=1 align=\"absmiddle\">&nbsp;<a href=\"mailto:".$enr['cal_email']."\">".$enr['cal_email']."</a>";
          if ($enr['cal_emailpro']!="")
            $strOutput .= "<br><IMG src=\"image/calepin/email.gif\" border=0 width=18 height=16 vspace=1 align=\"absmiddle\">&nbsp;<a href=\"mailto:".$enr['cal_emailpro']."\">".$enr['cal_emailpro']."</a>";
          $strOutput .= "</td></tr>";
        }
      }
      else
        $strOutput = "<tr bgcolor=\"$PPX_COULEUR_FOND_COMMUN\"><td colspan=\"4\" align=\"center\"><br><font color=\"$PPX_COULEUR_MESSAGE_INFO\">".trad("PPX_AUCUN_CONTACT")."</font><br>&nbsp;</td></tr>";
    }
    $closeForm = true;
    echo $strOutput."<tr bgcolor=\"$PPX_COULEUR_FOND_ACTION\"><td colspan=\"4\" align=\"center\"><form><br><input type=\"button\" value=\"".trad("PPX_BT_AUTRE_RECHERCHE")."\" onclick=\"document.location.href='".$NOM_PAGE."?sid=".$sid."&v=10'\"><br>";
  }
// ----------------------------------------------------------------------------
// DETAIL D'UN CONTACT
// ----------------------------------------------------------------------------
  elseif ($v==12) {
    $closeForm = true;
    $DB_CX->DbQuery("SELECT * FROM ${PREFIX_TABLE}calepin WHERE cal_id=".$id);
    if ($enr = $DB_CX->DbNextRow()) {
      $strOutput = "<tr bgcolor=\"$PPX_COULEUR_FOND_COMMUN\"><td colspan=\"4\">";
      if (!empty($enr['cal_societe']))   // Societe
        $strOutput .= "<i>".$enr['cal_societe']."</i><br>";
      if (!empty($enr['cal_nom']) || !empty($enr['cal_prenom']))  // Nom et Prenom
        $strOutput .= "<font color=\"$PPX_COULEUR_NOM_CONTACT\"><b>".trim($enr['cal_nom']." ".$enr['cal_prenom'])."</b></font>";
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
        $strOutput .= "<br><br>".sprintf(trad("PPX_AGE"), $age)." (".$tabDate[2]."/".$tabDate[1]."/".$tabDate[0].")";
      }
      $strOutput .= "<br>";
      if (!empty($enr['cal_domicile'])) {  // Telephone domicile
        $enr['cal_domicile'] = preg_replace( "/[^0-9+]+/","",$enr['cal_domicile']);
        $strOutput .= "<br><IMG src=\"image/calepin/telephone.gif\" border=0 width=18 height=14 vspace=1 align=\"absmiddle\">&nbsp;".$enr['cal_domicile'];
      }
      if (!empty($enr['cal_travail'])) {  // Telephone professionnel
        $enr['cal_travail'] = preg_replace( "/[^0-9+]+/","",$enr['cal_travail']);
        $strOutput .= "<br><IMG src=\"image/calepin/telephone2.gif\" border=0 width=18 height=14 vspace=1 align=\"absmiddle\">&nbsp;".$enr['cal_travail'];
      }
      if (!empty($enr['cal_portable'])) { // Portable
        $enr['cal_portable'] = preg_replace( "/[^0-9+]+/","",$enr['cal_portable']);
        $strOutput .= "<br><IMG src=\"image/calepin/portable.gif\" border=0 width=18 height=16 vspace=1 align=\"absmiddle\">&nbsp;".$enr['cal_portable'];
      }
      if (!empty($enr['cal_fax'])) {   // Fax
        $enr['cal_fax'] = preg_replace( "/[^0-9+]+/","",$enr['cal_fax']);
        $strOutput .= "<br><IMG src=\"image/calepin/fax.gif\" border=0 width=16 height=15 vspace=1 hspace=1 align=\"absmiddle\">&nbsp;".$enr['cal_fax'];
      }
      while (substr($strOutput,-4)=="<br>")
        $strOutput = substr($strOutput,0,strlen($strOutput)-4);
      $strOutput .= "<br>";
      if (!empty($enr['cal_email']))  // Adresse Email
        $strOutput .= "<br><IMG src=\"image/calepin/email.gif\" border=0 width=18 height=16 vspace=1 align=\"absmiddle\">&nbsp;<a href=\"mailto:".$enr['cal_email']."\">".$enr['cal_email']."</a>";
      if (!empty($enr['cal_emailpro']))  // Adresse Email Pro
        $strOutput .= "<br><IMG src=\"image/calepin/email.gif\" border=0 width=18 height=16 vspace=1 align=\"absmiddle\">&nbsp;<a href=\"mailto:".$enr['cal_emailpro']."\">".$enr['cal_emailpro']."</a>";
      $strOutput .= "<br>";
      if (!empty($enr['cal_icq']))  // ICQ
        $strOutput .= "<br><IMG src=\"image/calepin/icq.gif\" border=0 align=\"absmiddle\">&nbsp;".$enr['cal_icq'];
      if (!empty($enr['cal_aim']))  // AIM
        $strOutput .= "<br><IMG src=\"image/calepin/aim.gif\" border=0 vspace=1 hspace=1 align=\"absmiddle\">&nbsp;".$enr['cal_aim'];
      if (!empty($enr['cal_msn']))  // MSN
        $strOutput .= "<br><IMG src=\"image/calepin/msn.gif\" border=0 vspace=1 hspace=1 align=\"absmiddle\">&nbsp;<a href=\"mailto:".$enr['cal_msn']."\">".$enr['cal_msn']."</a>";
      if (!empty($enr['cal_yahoo']))  // YAHOO
        $strOutput .= "<br><IMG src=\"image/calepin/yahoo.gif\" border=0 vspace=1 hspace=1 align=\"absmiddle\">&nbsp;".$enr['cal_yahoo'];
      while (substr($strOutput,-4)=="<br>")
        $strOutput = substr($strOutput,0,strlen($strOutput)-4);
      if (!empty($enr['cal_note']))  // Commentaire
        $strOutput .= "<br><br><u>".trad("PPX_COMMENTAIRE")."</u> :<br>".str_replace(chr(13),"",str_replace(chr(10),"<br>",$enr['cal_note']));
      while (substr($strOutput,-4)=="<br>")
        $strOutput = substr($strOutput,0,strlen($strOutput)-4);
      $strOutput .= "<br>&nbsp;</td></tr><tr bgcolor=\"$PPX_COULEUR_FOND_ACTION\"><td colspan=\"4\" align=\"center\"><form><br>";
      // Droits en modification et suppression
      if ($enr['cal_util_id']==$idUser) {
        $strOutput.="<input type=\"button\" value=\"".trad("PPX_BT_MODIFIER")."\" onclick=\"javascript: document.location.href='".$NOM_PAGE."?sid=".$sid."&v=13&id=".$id.$critRecherche."'\"> <input type=\"button\" value=\"".trad("PPX_BT_SUPPRIMER")."\" onclick=\"javascript: document.location.href='".$NOM_PAGE."?sid=".$sid."&v=15&id=".$id.$critRecherche."'\"> ";
      } elseif ($MODIF_PARTAGE) {
        $strOutput.="<input type=\"button\" value=\"".trad("PPX_BT_MODIFIER")."\" onclick=\"javascript: document.location.href='".$NOM_PAGE."?sid=".$sid."&v=13&id=".$id.$critRecherche."'\"> ";
      }
    } else {
      $strOutput = "<tr bgcolor=\"$PPX_COULEUR_FOND_COMMUN\"><td colspan=\"4\" align=\"center\"><form><br><font color=\"$PPX_COULEUR_MESSAGE_INFO\">".trad("PPX_AUCUN_CONTACT")."</font><br><br>";
    }
    // Retour vers l'agenda si affichage d'un contact associe a une note, sinon retour vers la recherche
    $lienRetour = ($f=="note") ? "&v=1&sd=".$sd : "&v=11".$critRecherche;
    echo $strOutput."<input type=\"button\" value=\"".trad("PPX_BT_RETOUR")."\" onclick=\"javascript: document.location.href='".$NOM_PAGE."?sid=".$sid.$lienRetour."'\">";
  }
// ----------------------------------------------------------------------------
// MODULE CREATION/MODIFICATION D'UN CONTACT
// ----------------------------------------------------------------------------
  elseif ($v==13) {
    // Transforme une date aaaa-mm-jj en jj/mm/aaaa
    function dateVF($dateUS) {
      if (!empty($dateUS) && $dateUS!="0000-00-00") {
        list($Y,$M,$D) = explode("-",$dateUS);
        $dateFR = "$D/$M/$Y";
      }
      return $dateFR;
    }
    //----------------------------------
    $url = "?sid=".$sid."&v=14&ztAction=INSERT";
    $proprio = $idUser;
    //Recuperation des informations sur la note pour une modification
    if ($id) {
      $DB_CX->DbQuery("SELECT * FROM ${PREFIX_TABLE}calepin WHERE cal_id=".$id.(($MODIF_PARTAGE) ? "" : " AND cal_util_id=".$idUser));
      if ($enr = $DB_CX->DbNextRow()) {
        $ztAction = "UPDATE";
        $url = "?sid=".$sid."&v=14&ztAction=UPDATE&id=".$enr['cal_id']."";
        $proprio = $enr['cal_util_id'];
      } else {
        $id = 0;
      }
    }
    $closeForm = true;
    echo "<tr bgcolor=\"$PPX_COULEUR_FOND_COMMUN\"><td colspan=\"4\"><form method=\"post\" action=\"".$NOM_PAGE.$url.$critRecherche."\" target=\"_self\" name=\"frmContact\">";
    echo "<b>".trad("PPX_LIB_SOCIETE")."</b><br><input type=\"text\" name=\"ztSociete\" size=\"30\" maxlength=\"50\" value=\"".htmlspecialchars($enr['cal_societe'])."\" style=\"width:230px;\"><br>";
    echo "<b>".trad("PPX_LIB_NOM")."</b><br><input type=\"text\" name=\"ztNom\" size=\"30\" maxlength=\"50\" value=\"".htmlspecialchars($enr['cal_nom'])."\" style=\"width:230px;\"><br>";
    echo "<b>".trad("PPX_LIB_PRENOM")."</b><br><input type=\"text\" name=\"ztPrenom\" size=\"30\" maxlength=\"30\" value=\"".htmlspecialchars($enr['cal_prenom'])."\" style=\"width:230px;\"><br>";
    echo "<b>".trad("PPX_LIB_ADRESSE")."</b><br><textarea name=\"ztAdresse\" cols=\"26\" rows=\"3\" wrap=\"soft\" style=\"width:230px;\">".htmlspecialchars($enr['cal_adresse'])."</textarea><br>";
    echo "<b>".trad("PPX_LIB_CP")."</b><br><input type=\"text\" name=\"ztCP\" size=\"6\" maxlength=\"10\" value=\"".htmlspecialchars($enr['cal_cp'])."\"><br>";
    echo "<b>".trad("PPX_LIB_VILLE")."</b><br><input type=\"text\" name=\"ztVille\" size=\"30\" maxlength=\"100\" value=\"".htmlspecialchars($enr['cal_ville'])."\" style=\"width:230px;\"><br>";
    echo "<b>".trad("PPX_LIB_PAYS")."</b><br><input type=\"text\" name=\"ztPays\" size=\"30\" maxlength=\"100\" value=\"".htmlspecialchars($enr['cal_pays'])."\" style=\"width:230px;\"><br>";
    echo "<br><b>".trad("PPX_LIB_TEL_DOMICILE")."</b><br><input type=\"text\" name=\"ztDomicile\" size=\"15\" maxlength=\"20\" value=\"".htmlspecialchars($enr['cal_domicile'])."\"><br>";
    echo "<b>".trad("PPX_LIB_TEL_TRAVAIL")."</b><br><input type=\"text\" name=\"ztTravail\" size=\"15\" maxlength=\"20\" value=\"".htmlspecialchars($enr['cal_travail'])."\"><br>";
    echo "<b>".trad("PPX_LIB_TEL_PORTABLE")."</b><br><input type=\"text\" name=\"ztPortable\" size=\"15\" maxlength=\"20\" value=\"".htmlspecialchars($enr['cal_portable'])."\"><br>";
    echo "<b>".trad("PPX_LIB_FAX")."</b><br><input type=\"text\" name=\"ztFax\" size=\"15\" maxlength=\"20\" value=\"".htmlspecialchars($enr['cal_fax'])."\"><br>";
    echo "<br><b>".trad("PPX_LIB_EMAIL")."</b><br><input type=\"text\" name=\"ztEmail\" size=\"30\" maxlength=\"50\" value=\"".htmlspecialchars($enr['cal_email'])."\" style=\"width:230px;\"><br>";
    echo "<b>".trad("PPX_LIB_EMAIL_PRO")."</b><br><input type=\"text\" name=\"ztEmailPro\" size=\"30\" maxlength=\"50\" value=\"".htmlspecialchars($enr['cal_emailpro'])."\" style=\"width:230px;\"><br>";
    echo "<br><b>".trad("PPX_LIB_ICQ")."</b><br><input type=\"text\" name=\"ztICQ\" size=\"12\" maxlength=\"15\" value=\"".(($enr['cal_icq']>0)?$enr['cal_icq']:"")."\"><br>";
    echo "<b>".trad("PPX_LIB_AIM")."</b><br><input type=\"text\" name=\"ztAIM\" size=\"30\" maxlength=\"50\" value=\"".htmlspecialchars($enr['cal_aim'])."\" style=\"width:230px;\"><br>";
    echo "<b>".trad("PPX_LIB_MSN")."</b><br><input type=\"text\" name=\"ztMSN\" size=\"30\" maxlength=\"50\" value=\"".htmlspecialchars($enr['cal_msn'])."\" style=\"width:230px;\"><br>";
    echo "<b>".trad("PPX_LIB_YAHOO")."</b><br><input type=\"text\" name=\"ztYahoo\" size=\"30\" maxlength=\"50\" value=\"".htmlspecialchars($enr['cal_yahoo'])."\" style=\"width:230px;\"><br>";
    echo "<br><b>".trad("PPX_LIB_DATE_NAISSANCE")."</b><br><input type=\"text\" name=\"ztNaissance\" size=\"10\" maxlength=\"10\" value=\"".dateVF($enr['cal_date_naissance'])."\"><br>";
    echo "<b>".trad("PPX_LIB_DIVERS")."</b><br><textarea name=\"ztNote\" cols=\"26\" rows=\"3\" wrap=\"soft\" style=\"width:230px;\">".htmlspecialchars($enr['cal_note'])."</textarea>";
    if ($idUser==$proprio) {
      echo "<br><br><b>".trad("PPX_LIB_PARTAGE")."</b><br><input type=\"checkbox\" name=\"ztPartage\" value=\"O\"".(($enr['cal_partage']=="O")?" checked":"").">&nbsp;".trad("PPX_COCHER_PARTAGE");
    } elseif ($id) {
      echo "<input type=\"hidden\" name=\"ztPartage\" value=\"O\">";
    }
    $lienRetour = (($id) ? "&v=12&id=".$id : "&v=10").$critRecherche;
    echo "<br>&nbsp;</td></tr><tr bgcolor=\"$PPX_COULEUR_FOND_ACTION\"><td colspan=\"4\" align=\"center\"><br><input type=\"submit\" name=\"btOK\" value=\"".trad("PPX_BT_VALIDER")."\"> <input type=\"reset\" name=\"btRaz\" value=\"".trad("PPX_BT_RAZ")."\"> <input type=\"button\" value=\"".trad("PPX_BT_RETOUR")."\" onclick=\"javascript: document.location.href='".$NOM_PAGE."?sid=".$sid.$lienRetour."'\"><br>";
  }
// ----------------------------------------------------------------------------
// MODULE CREATION/MODIFICATION D'UNE NOTE
// ----------------------------------------------------------------------------
  elseif ($v==2) {
    $DB_CX->DbQuery("SELECT util_debut_journee, util_duree_note FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$idUser);
    $debutJournee = $DB_CX->DbResult(0,0);
    $dureeNote = $DB_CX->DbResult(0,1);
    //Pour une nouvelle note, on se positionne en debut de journee du profil
    $decalageHoraire = calculDecalageH($tzGmt,$tzEte,$tzHiver,mktime(0,0,0,$moisEnCours,$jourEnCours,$anneeEnCours));
    $enr['ageDate'] = date("d/m/Y",$sd);
    $enr['age_heure_debut'] = (isset($hD) && !empty($hD)) ? $hD : $debutJournee;
    $enr['age_heure_fin'] = (isset($hF) && !empty($hF)) ? $hF : $enr['age_heure_debut']+(0.25*$dureeNote);
    $url = "?sid=".$sid."&sd=".$sd."&v=3&ztAction=INSERT";
    //Recuperation des informations sur la note pour une modification
    if ($id) {
      $DB_CX->DbQuery("SELECT age_id, age_libelle, age_detail, age_date, age_heure_debut, age_heure_fin, age_prive, age_rappel, age_rappel_coeff, age_aty_id, age_disponibilite, age_lieu, age_couleur, age_cal_id FROM ${PREFIX_TABLE}agenda WHERE age_id=".$id." AND age_util_id=".$idUser." AND age_aty_id!=1");
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
    $closeForm = true;
    echo "<tr bgcolor=\"$PPX_COULEUR_FOND_COMMUN\"><td colspan=\"4\"><form method=\"post\" action=\"".$NOM_PAGE.$url."\" target=\"_self\" name=\"frmNote\">";
    echo "<b>".trad("PPX_LIB_LIBELLE")."</b><br><input type=\"text\" name=\"ztLibelle\" size=\"30\" value=\"".htmlspecialchars($enr['age_libelle'])."\" style=\"width:230px;\"><br>";
    echo "<b>".trad("PPX_LIB_EMPLACEMENT")."</b><br><input type=\"text\" name=\"ztLieu\" size=\"30\" value=\"".htmlspecialchars($enr['age_lieu'])."\" style=\"width:230px;\"><br>";
    echo "<b>".trad("PPX_LIB_DETAIL")."</b><br><textarea name=\"ztDetail\" cols=\"26\" rows=\"3\" wrap=\"soft\" style=\"width:230px;\">".htmlspecialchars($enr['age_detail'])."</textarea><br>";
    echo "<br><b>".trad("PPX_LIB_DATE")."</b><br><input type=\"text\" name=\"ztDate\" size=\"10\" value=\"".$enr['ageDate']."\"><br>";
    echo "<br><b>".trad("PPX_LIB_HORAIRES")."</b><br>".trad("PPX_HEURE_DEBUT")." <select name=\"zlHeureDebut\"".(($enr['age_aty_id']==3) ? " disabled" : "").">";
    for ($i=0; $i<24;$i=$i+0.25) {
      $selected = ($i==$enr['age_heure_debut']) ? " selected" : "";
      echo "<option value=\"".$i."\"".$selected.">".afficheHeure($i,$i,$formatHeure)."</option>";
    }
    echo "</select>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;".trad("PPX_HEURE_FIN")." <select name=\"zlHeureFin\"".(($enr['age_aty_id']==3) ? " disabled" : "").">";
    for ($i=0.25; $i<=24;$i=$i+0.25) {
      $selected = ($i==$enr['age_heure_fin']) ? " selected" : "";
      echo "<option value=\"".$i."\"".$selected.">".afficheHeure($i,$i,$formatHeure)."</option>";
    }
    echo "</select><br>";
    echo trad("PPX_LIB_JOURNEE_ENTIERE")." <input type=\"checkbox\" name=\"ckTypeNote\" value=\"3\"".(($enr['age_aty_id']==3) ? " checked" : "")." onclick=\"javascript: document.frmNote.zlHeureDebut.disabled=this.checked; document.frmNote.zlHeureFin.disabled=this.checked;\"><br><br>";
    echo "<b>".trad("PPX_LIB_COULEUR")."</b><br><select name=\"zlCouleur\">";
    $tabTemp    = array(trad("COMMUN_COUL_DEFAUT") => "");
    $tabCouleur = array_merge($tabTemp,getListeCouleur());
    reset($tabCouleur);
    while (list($key, $val) = each($tabCouleur)) {
      $selected = ($val==$enr['age_couleur']) ? " selected" : "";
      echo "<option".((!empty($val)) ? " style=\"background-color:".$val.";\"" : "")." value=\"".$val."\"".$selected.">".$key."</option>";
    }
    echo "</select><br><br>";
    echo "<b>".trad("PPX_LIB_PARTAGE")."</b><br><select name=\"zlPartage\">";
    echo "<option value=\"0\"".(($enr['age_prive']!=1) ? " selected" : "").">".trad("PPX_PUBLIQUE")."</option>";
    echo "<option value=\"1\"".(($enr['age_prive']==1) ? " selected" : "").">".trad("PPX_PRIVEE")."</option>";
    echo "</select><br><br>";
    echo "<b>".trad("PPX_LIB_DISPO")."</b><br><select name=\"zlDispo\">";
    echo "<option value=\"0\"".(($enr['age_disponibilite']!=1) ? " selected" : "").">".trad("COMMUN_OCCUPE")."</option>";
    echo "<option value=\"1\"".(($enr['age_disponibilite']==1) ? " selected" : "").">".trad("PPX_LIBRE")."</option>";
    echo "</select><br><br>";
    echo "<b>".trad("COMMUN_LIB_RAPPEL")."</b><br><select name=\"zlR1\">";
    for ($i=0;$i<60;$i++) {
      $selected = ($enr['age_rappel']==$i) ? " selected" : "";
      echo "<option value=\"".$i."\"".$selected.">".$i."</option>";
    }
    echo "</select> <select name=\"zlR2\">";
    echo "<option value=\"1\"".(($enr['age_rappel_coeff']==1) ? " selected" : "").">".trad("COMMUN_MINUTE")."</option>";
    echo "<option value=\"60\"".(($enr['age_rappel_coeff']==60) ? " selected" : "").">".trad("COMMUN_HEURE")."</option>";
    echo "<option value=\"1440\"".(($enr['age_rappel_coeff']==1440) ? " selected" : "").">".trad("COMMUN_JOUR")."</option>";
    echo "</select>";
    // Recuperation des contacts de l'utilisateur et ceux qui sont partages
    $DB_CX->DbQuery("SELECT DISTINCT cal_id, LTRIM(CONCAT(cal_nom,' ', cal_prenom)) AS nomContact FROM ${PREFIX_TABLE}calepin WHERE cal_util_id=".$idUser." OR cal_partage='O' ORDER BY nomContact");
    // Le choix du contact n'est pas affiche si le calepin est vide
    if ($DB_CX->DbNumRows()) {
      echo "<br><br><b>".trad("PPX_LIB_CONTACT_ASSOCIE")."</b><br><select name=\"zlContactAssocie\">";
      echo "<option value=\"0\">".trad("PPX_LIB_CONTACT_AUCUN")."</option>";
      $lettreCrt = "";
      while ($cal = $DB_CX->DbNextRow()) {
        // Premiere lettre
        if ($lettreCrt!=substr($cal['nomContact'],0,1)) {
          if ($lettreCrt!="") {
            echo "      </optgroup>\n";
          }
          $lettreCrt = substr($cal['nomContact'],0,1);
          echo "      <optgroup label=\"".htmlspecialchars($lettreCrt)."\">\n";
        }
        $selected = ($cal['cal_id']==$enr['age_cal_id']) ? " selected" : "";
        echo "        <option value=\"".$cal['cal_id']."\"".$selected.">".htmlspecialchars($cal['nomContact'])."</OPTION>\n";
      }
      echo "      </optgroup>\n";
      echo "</select>";
    }
    echo "<br>&nbsp;</td></tr><tr bgcolor=\"$PPX_COULEUR_FOND_ACTION\"><td colspan=\"4\" align=\"center\"><br><input type=\"submit\" name=\"btOK\" value=\"".trad("PPX_BT_VALIDER")."\"> <input type=\"reset\" name=\"btRaz\" value=\"".trad("PPX_BT_RAZ")."\"> <input type=\"button\" value=\"".trad("PPX_BT_RETOUR")."\" onclick=\"javascript: document.location.href='".$NOM_PAGE."?sid=".$sid."&v=1&sd=".$sd."'\"><br>";
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
    //Type de la note
    if ($ckTypeNote!=3) {
      $ckTypeNote=2;
    } else {
      $DB_CX->DbQuery("SELECT util_debut_journee,util_fin_journee FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$idUser);
      //Pour une note sur toute la journee, on positionne la note sur les horaires du profil
      $zlHeureDebut = $DB_CX->DbResult(0,0);
      $zlHeureFin = $DB_CX->DbResult(0,1);
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

    $tsNow = mktime(gmdate("H"),gmdate("i"),gmdate("s"),gmdate("n"),gmdate("j"),gmdate("Y"));
    $tsAlert = mktime(gmdate("H"),gmdate("i")+($zlR1*$zlR2),gmdate("s"),gmdate("n"),gmdate("j"),gmdate("Y"));
    $tsNoteUTC = mktime($hNoteUTC,$mNoteUTC,0,$tabDateUTC[1],$tabDateUTC[0],$tabDateUTC[2]);
    $endNote = ($tsNoteUTC > $tsNow) ? 0 : 1;
    $alert = ($tsNoteUTC > $tsAlert && $zlR1) ? 0 : 1;
    $zlContactAssocie += 0;

    if (!empty($ztLibelle) && $ztAction=="INSERT") {
      $dateCreation = gmdate("Y-m-d H:i:s", time());
      $sql = "INSERT INTO ${PREFIX_TABLE}agenda (age_util_id,age_aty_id,age_date,age_heure_debut,age_heure_fin, age_libelle, age_detail, age_rappel, age_rappel_coeff, age_prive, age_disponibilite, age_lieu, age_couleur, age_cal_id, age_createur_id, age_date_creation, age_modificateur_id, age_date_modif) ";
      $sql .= "VALUES (".$idUser.",".$ckTypeNote.",'".$ztDateUTC."',".$zlHeureDebutUTC.",".$zlHeureFinUTC.",'".$ztLibelle."','".$ztDetail."',".$zlR1.",".$zlR2.",".$zlPartage.",".$zlDispo.",'".$ztLieu."','".$zlCouleur."',".$zlContactAssocie.",".$idUser.", '".$dateCreation."',".$idUser.", '".$dateCreation."')";
      $DB_CX->DbQuery($sql);
      $idAge = $DB_CX->DbInsertID();
      $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}agenda_concerne VALUES (".$idAge.",".$idUser.",".$alert.",".$endNote.")");
    } elseif (!empty($ztLibelle) && $ztAction=="UPDATE" && $id) {
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
      $sql .= " age_disponibilite=".$zlDispo.", ";
      $sql .= " age_lieu='".$ztLieu."', ";
      $sql .= " age_cal_id=".$zlContactAssocie.", ";
      $sql .= " age_couleur='".$zlCouleur."', ";
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
    if ($id && $confirm!="1" && $flag) {
      echo "<tr align=\"center\" bgcolor=\"$PPX_COULEUR_FOND_COMMUN\"><td colspan=\"4\">";
      switch ($flag) {
        case 1 : $closeForm = true; echo sprintf(trad("PPX_JS_SUPPR_NOTE"), "<img src=\"image/recurrent.gif\" aligne=\"middle\" border=\"0\">")."<br><input type=\"button\" value=\"".strtoupper(trad("COMMUN_OUI"))."\" onclick=\"javascript: document.location.href='".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&id=".$id."&v=4&confirm=1&flag=1'\"> <input type=\"button\" value=\"".strtoupper(trad("COMMUN_NON"))."\" onclick=\"javascript: document.location.href='".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&v=1'\"><br>"; break;
        case 2 : $closeForm = true; echo trad("PPX_JS_SUPPR_NOTE_AFFECTEE")."<br><input type=\"button\" value=\"".strtoupper(trad("COMMUN_OUI"))."\" onclick=\"javascript: document.location.href='".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&id=".$id."&v=4&confirm=1&flag=2'\"> <input type=\"button\" value=\"".strtoupper(trad("COMMUN_NON"))."\" onclick=\"javascript: document.location.href='".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&v=1'\"><br>"; break;
        case 3 : $closeForm = true; echo sprintf(trad("PPX_JS_SUPPR_OCCURENCE"), "<img src=\"image/suppr.gif\" border=\"0\" align=\"middle\">")."<br><input type=\"button\" value=\"".strtoupper(trad("COMMUN_OUI"))."\" onclick=\"javascript: document.location.href='".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&id=".$id."&v=4&confirm=1&flag=3'\"> <input type=\"button\" value=\"".strtoupper(trad("COMMUN_NON"))."\" onclick=\"javascript: document.location.href='".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&v=1'\"><br>"; break;
        default : break;
      }
    }
    elseif ($id && $confirm=="1" && $flag) {
      if ($flag==2 && $AUTORISE_SUPPR) {
        //Suppression d'une note affectee
        $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda_concerne WHERE aco_age_id=".$id." AND aco_util_id=".$idUser);
        $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}information WHERE info_age_id=".$id." AND info_destinataire_id=".$idUser);
        //Recherche s'il reste des personnes concernees par cette note
        $DB_CX->DbQuery("SELECT aco_util_id FROM ${PREFIX_TABLE}agenda_concerne WHERE aco_age_id=".$id);
        //si NON : on efface la note
        if (!$DB_CX->DbNumRows()) {
          $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda WHERE age_id=".$id);
        } else {
          //si OUI : on reajuste le nombre de participant (pour l'appropriation)
          $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}agenda SET age_nb_participant = ".$DB_CX->DbNumRows()." WHERE age_id=".$idAge);
        }
      } elseif ($flag==1) {
        //Suppression de la totalite d'une note par son auteur
        $DB_CX->DbQuery("SELECT DISTINCT age_id FROM ${PREFIX_TABLE}agenda WHERE (age_id=".$id." OR age_mere_id=".$id.") AND age_util_id=".$idUser);
        $liste = "0";
        while ($enr = $DB_CX->DbNextRow())
          $liste .= ",".$enr['age_id'];
        $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda WHERE age_id IN (".$liste.")");
        $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}agenda_concerne WHERE aco_age_id IN (".$liste.")");
        $DB_CX->DbQuery("DELETE FROM ${PREFIX_TABLE}information WHERE info_age_id IN (".$liste.")");
      } elseif ($flag==3) {
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
// MODULE CHANGEMENT D'ETAT D'UNE NOTE
// ----------------------------------------------------------------------------
  elseif ($v==5) {
    if ($id) {
      $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}agenda_concerne SET aco_termine= 1-aco_termine WHERE aco_age_id=".$id." AND aco_util_id=".$idUser);
    }
    //Renvoi vers l'affichage du detail de la journee
    $v=1;
  }
// ----------------------------------------------------------------------------
// MODULE APPROPRIATION D'UNE NOTE
// ----------------------------------------------------------------------------
  elseif ($v==6) {
    if ($id && $confirm!="1") {
      $closeForm = true;
      echo "<tr align=\"center\" bgcolor=\"$PPX_COULEUR_FOND_COMMUN\"><td colspan=\"4\"><form>";
      echo trad("PPX_JS_APPROPRIER")."<br><input type=\"button\" value=\"".strtoupper(trad("COMMUN_OUI"))."\" onclick=\"javascript: document.location.href='".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&id=".$id."&v=6&confirm=1'\"> <input type=\"button\" value=\"".strtoupper(trad("COMMUN_NON"))."\" onclick=\"javascript: document.location.href='".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&v=1'\">";
    } elseif ($id && $confirm=="1") {
      $DB_CX->DbQuery("UPDATE ${PREFIX_TABLE}agenda SET age_util_id=".$idUser." WHERE age_id=".$id);
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

    $DB_CX->DbQuery("SELECT age_id,age_aty_id,age_heure_debut,age_heure_fin,age_libelle,age_ape_id,age_util_id,age_detail,age_lieu,age_couleur,age_nb_participant,age_date,age_date_creation,age_date_modif,aco_termine,cal_id,CONCAT(cal_prenom,' ',cal_nom) AS nomContact,cal_util_id,cal_partage FROM ${PREFIX_TABLE}agenda LEFT JOIN ${PREFIX_TABLE}calepin ON cal_id=age_cal_id, ${PREFIX_TABLE}agenda_concerne WHERE age_id=aco_age_id AND aco_util_id=".$idUser." AND ($age_date='".date("Y-m-d",$sd)."' OR ($age_dateAvant='".date("Y-m-d",$sd)."' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin!=0 AND age_aty_id=2) OR (age_date LIKE '%".date("m-d",$sd)."' AND age_aty_id=1)) ORDER BY age_aty_id DESC, age_date, age_heure_debut ASC");
    $ligneAnniv = $ligneNote = "";
    for ($j=0;$j<$DB_CX->DbNumRows();$j++) {
      $enr = $DB_CX->DbNextRow();
      //Decalage des notes en fonction du fuseau horaire
      list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['dateCreation'],$enr['dateModif']) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,date("Y-m-d",$sd),$enr['age_date'],$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif']);
      //Stockage des infos relatives aux anniversaires
      if ($enr['age_aty_id']==1) {
        $ligneAnniv .= $enr['age_libelle']." / ";
      }
      //Stockage des infos relatives aux notes
      else {
        //Propriete Active ou Terminee de la note
        $imgTemoin = ($enr['aco_termine']==1) ? "puce_ok.gif" : "puce_ko.gif";
        $lienStatut = (($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR) and ($enr['age_util_id']==$idUser)) ? "<a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&id=".$enr['age_id']."&v=5\"><img src=\"image/".$imgTemoin."\" width=\"6\" height=\"6\" border=\"0\" align=\"middle\"></a> " : "<img src=\"image/".$imgTemoin."\" width=\"6\" height=\"6\" border=\"0\" align=\"middle\"> ";
        //Plage horaire de la note
        $plageNote = ($enr['age_aty_id']==2) ? afficheHeure(floor($enr['age_heure_debut']),$enr['age_heure_debut'],$formatHeure)."&nbsp;-&nbsp;".afficheHeure(floor($enr['age_heure_fin']),$enr['age_heure_fin'],$formatHeure) : trad("COMMUN_JOURNEE_ENTIERE");
        //Droit en modification et en suppression
        $lienModif = $enr['age_libelle'].((!empty($enr['age_lieu'])) ? " <i>(".$enr['age_lieu'].")</i>" : "");
        $sOption = "";
        if ($enr['age_ape_id']!=1) {
          if ($enr['age_util_id']==$idUser && ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR)) {
            $sOption = "&nbsp;<a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&id=".$enr['age_id']."&v=4&flag=3\"><img src=\"image/recurrent.gif\" border=\"0\" align=\"absmiddle\"></a>";
          }else{
            $sOption = "<img src=\"image/recurrent.gif\" border=\"0\" align=\"absmiddle\">";
          }
        }
        if ($enr['age_util_id']==$idUser) {
          $lienModif = ($enr['age_util_id']==$idUser && ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR)) ? "<a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&id=".$enr['age_id']."&v=2\">".$enr['age_libelle']."</a>".((!empty($enr['age_lieu'])) ? " <i>(".$enr['age_lieu'].")</i>" : "") : $enr['age_libelle'].((!empty($enr['age_lieu'])) ? " <i>(".$enr['age_lieu'].")</i>" : "");
          $sOption .= ($enr['age_util_id']==$idUser && ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR)) ? "&nbsp;<a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&id=".$enr['age_id']."&v=4&flag=1\"><img src=\"image/suppr.gif\" border=\"0\" align=\"absmiddle\"></a>" : "";
        } elseif ($AUTORISE_SUPPR) {
          $sOption .= ($enr['age_util_id']==$idUser && ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR)) ? "&nbsp;<a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&id=".$enr['age_id']."&v=4&flag=2\"><img src=\"image/suppr.gif\" border=\"0\" align=\"absmiddle\"></a>" : "";
        }
        //Appropriation d'une note affectee
        if ($enr['age_util_id']!=$idUser && ($droit_NOTES >= _DROIT_NOTE_STANDARD)) {
          $sOption .= "&nbsp;<a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&id=".$enr['age_id']."&v=6\"><img src=\"image/appropriation.gif\" border=\"0\" align=\"absmiddle\"></a>";
        }
        //Couleur de fond de la note si non definie dans la bdd
        if (empty($enr['age_couleur'])) {
          $enr['age_couleur'] = ($enr['age_util_id']==$idUser) ? $PPX_COULEUR_NOTE_PERSO : $PPX_COULEUR_NOTE_AFFECTEE;
        }

        $ligneNote .= "<tr bgcolor=\"".$enr['age_couleur']."\"><td colspan=\"4\" style=\"padding:2px;\"><font color=\"$PPX_COULEUR_HORAIRE_NOTE\"><b>".$plageNote."</b></font>".$sOption."<br>".$lienStatut.$lienModif;
        //Info sur le contact associe
        if (!empty($enr['cal_id'])) {
          $lienContact = (($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR) && ($enr['cal_util_id']==$idUser || ($enr['cal_partage']=='O' && $MODIF_PARTAGE))) ? "<a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&id=".$enr['cal_id']."&v=12&f=note\">".htmlspecialchars($enr['nomContact'])."</a>" : htmlspecialchars($enr['nomContact']);
          $enr['age_detail'] = "<img src=\"image/contact.gif\" align=\"absmiddle\">&nbsp;<b>".$lienContact."</b>".chr(13).$enr['age_detail'];
        }
        // Info sur le detail de la note
        $tabDetail = explode(chr(13),$enr['age_detail']);
        if (count($tabDetail) > 0) {
          $ligneNote .= "<font color=\"$PPX_COULEUR_DETAIL_NOTE\"><i>";
          for ($nb=0;$nb<count($tabDetail);$nb++) {
            $tabDetail[$nb]=trim($tabDetail[$nb]);
            if (!empty($tabDetail[$nb]))
              $ligneNote .= "<br>&nbsp; ".$tabDetail[$nb];
          }
          $ligneNote .= "</i></font>";
        }
        $ligneNote .= "</td></tr>";
      }
    }
    // Recuperation du jour precedent ayant une note
    $jourAvant = "";
    $DB_CX->DbQuery("SELECT DATE_FORMAT(IF($age_dateAvant<'".date("Y-m-d",$sd)."' AND $age_heure_debut>$age_heure_fin AND $age_heure_fin!=0,$age_dateAvant,$age_date),'%e/%c/%Y') AS ageDate FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne WHERE age_id=aco_age_id AND aco_util_id=".$idUser." AND $age_date<'".date("Y-m-d",$sd)."' AND age_aty_id!=1 ORDER BY age_date DESC LIMIT 0,1");
    if ($DB_CX->DbNumRows()) {
      // Transformation de la date de debut de la note en timestamp PHP
      list($j,$m,$a) = explode("/",$DB_CX->DbResult(0,0));
      $tsNote = mktime(0,0,0,$m,$j,$a);
      $jourAvant = "<a href=\"".$NOM_PAGE."?sid=".$sid."&v=1&sd=".$tsNote."\"><img src=\"image/timode/anneeprec.gif\" alt=\"\" width=\"8\" height=\"10\" border=\"0\" align=\"absmiddle\"></a>&nbsp;";
    }
    //Recuperation du jour suivant ayant une note
    $jourApres = "";
    $DB_CX->DbQuery("SELECT DATE_FORMAT(IF($age_date='".date("Y-m-d",$sd)."' AND $age_heure_debut>$age_heure_fin AND $age_heure_fin!=0,$age_dateAvant,$age_date),'%e/%c/%Y') AS ageDate FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne WHERE age_id=aco_age_id AND aco_util_id=".$idUser." AND ($age_date>'".date("Y-m-d",$sd)."' OR ($age_date='".date("Y-m-d",$sd)."' AND $age_heure_debut>$age_heure_fin AND $age_heure_fin!=0)) AND age_aty_id!=1 ORDER BY age_date LIMIT 0,1");
    if ($DB_CX->DbNumRows()) {
      // Transformation de la date de debut de la note en timestamp PHP
      list($j,$m,$a) = explode("/",$DB_CX->DbResult(0,0));
      $tsNote = mktime(0,0,0,$m,$j,$a);
      $jourApres = "&nbsp;<a href=\"".$NOM_PAGE."?sid=".$sid."&v=1&sd=".$tsNote."\"><img src=\"image/timode/anneesuiv.gif\" alt=\"\" width=\"8\" height=\"10\" border=\"0\" align=\"absmiddle\"></a>";
    }
    if ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR) {
      echo "<tr bgcolor=\"$PPX_COULEUR_LIGNE_NAVIGATION_JOUR\" align=\"center\"><td colspan=\"4\">".$jourAvant."<a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&v=2\">".$tabJour[date("w",$sd)]." ".date("d/m/Y",$sd)."</a> [".sprintf(trad("PPX_SEMAINE"), date("W",$sd))."]".$jourApres."</td></tr>";
    } else {
      echo "<tr bgcolor=\"$PPX_COULEUR_LIGNE_NAVIGATION_JOUR\" align=\"center\"><td colspan=\"4\">".$jourAvant.$tabJour[date("w",$sd)]." ".date("d/m/Y",$sd)." [".sprintf(trad("PPX_SEMAINE"), date("W",$sd))."]".$jourApres."</td></tr>";
    }
    // Anniversaire(s) du calepin (y compris les contacts partages)
    $DB_CX->DbQuery("SELECT cal_id, CONCAT(cal_prenom,' ',cal_nom) AS nomContact FROM ${PREFIX_TABLE}calepin WHERE (cal_util_id=".$idUser." OR cal_partage='O') AND cal_date_naissance LIKE '%".date("m-d",$sd)."'");
    while ($enr = $DB_CX->DbNextRow())
      $ligneAnniv .= ($droit_NOTES >= _DROIT_NOTE_STANDARD_SANS_APPR) ? "<a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".$sd."&id=".$enr['cal_id']."&v=12&f=note\">".$enr['nomContact']."</a> / " : "";
    if (!empty($ligneAnniv)) {
      echo "<tr bgcolor=\"$PPX_COULEUR_LIGNE_ANNIVERSAIRE\"><td colspan=\"4\">".trad("COMMUN_ANNIVERSAIRE")." : ".substr($ligneAnniv,0,strlen($ligneAnniv)-3)."</td></tr>";
    }
    if (!empty($ligneNote)) {
      echo substr($ligneNote,0,strlen($ligneNote)-10);
    } else {
      echo "<tr bgcolor=\"$PPX_COULEUR_FOND_COMMUN\"><td colspan=\"4\" align=\"center\"><br><font color=\"$PPX_COULEUR_MESSAGE_INFO\">".trad("PPX_AUCUNE_NOTE")."</font><br>";
    }
  }
// ----------------------------------------------------------------------------
// MODULE AFFICHAGE HEBDOMADAIRE
// ----------------------------------------------------------------------------
  elseif ($v==7) {
    // Calcul du premier jour de la semaine
    $indexJourCrt = date("w",$sd);
    if ($indexJourCrt == 0)
      $indexJourCrt = 7;
    $premierJourSemaine = $jourEnCours-$indexJourCrt+1;
    $debutSemaine = mktime(0,0,0,$moisEnCours,$premierJourSemaine,$anneeEnCours);
    $finSemaine   = mktime(0,0,0,$moisEnCours,$premierJourSemaine+6,$anneeEnCours);

    // Menu de navigation entre les differentes semaines
    $semaineAvant  = $NOM_PAGE."?sid=".$sid."&v=7&sd=".mktime(0,0,0,$moisEnCours,$premierJourSemaine-7,$anneeEnCours);
    $semaineApres  = $NOM_PAGE."?sid=".$sid."&v=7&sd=".mktime(0,0,0,$moisEnCours,$premierJourSemaine+7,$anneeEnCours);
    echo ("<tr align=\"center\" bgcolor=\"$PPX_COULEUR_FOND_COMMUN\"><td colspan=\"4\">
    <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
    <tr>
      <td bgcolor=\"$PPX_COULEUR_LIGNE_NAVIGATION_JOUR\">
      <table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" border=\"0\">
        <tr>
          <td align=\"right\" width=\"5%\" nowrap><a href=\"".$semaineAvant."\"><img src=\"image/timode/anneeprec.gif\" alt=\"\" width=\"8\" height=\"10\" border=\"0\"></a></td>
          <td align=\"center\" width=\"90%\" nowrap><font color=\"$PPX_COULEUR_TEXTE_MOIS\"><a href=\"".$NOM_PAGE."?sid=".$sid."&sd=".mktime(0,0,0,$moisEnCours,1,$anneeEnCours)."\">".$tabMois[$moisEnCours*1]." ".$anneeEnCours."</a> [".sprintf(trad("PPX_SEMAINE"), date("W",$debutSemaine))."]</font></td>
          <td align=\"left\" width=\"5%\" nowrap><a href=\"".$semaineApres."\"><img src=\"image/timode/anneesuiv.gif\" alt=\"\" width=\"8\" height=\"10\" border=\"0\"></a></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"$PPX_COULEUR_FOND_CALENDRIER\">
        <tr align=\"center\" bgcolor=\"$PPX_COULEUR_LIGNE_JOUR_SEMAINE\"><td style=\"border-top:solid 1px $PPX_COULEUR_BORDURE_TABLEAU;\">&nbsp;</td>");

    // Ligne des jours de la semaine
    $largeurCellule = floor(95/substr_count($SEMAINE_TYPE,"1"));
    for ($j=0; $j<7; $j++) {
      if (substr($SEMAINE_TYPE,$j,1)=="1") {
        $leJour = mktime(0,0,0,$moisEnCours,$premierJourSemaine+$j,$anneeEnCours);
        echo "<td width=\"".$largeurCellule."%\" style=\"".((date("Ymd",$leJour)==date("Ymd",$localTime))?"background:".$PPX_COULEUR_FOND_JOUR_COURANT.";":"")."border-top:solid 1px $PPX_COULEUR_BORDURE_TABLEAU;border-left:solid 1px $PPX_COULEUR_BORDURE_TABLEAU;\"><a href=\"".$NOM_PAGE."?sid=".$sid."&v=1&sd=".$leJour."\"><i>".$tabJour2[$j]."</i> ".date("d",$leJour)."</font></td>";
      }
    }

    echo "</tr>\n";

    //Parametres de la journee choisis par l'utilisateur
    $DB_CX->DbQuery("SELECT util_debut_journee, util_fin_journee, util_precision_planning FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$idUser);
    $debutJournee = $DB_CX->DbResult(0,0);
    $finJournee   = $DB_CX->DbResult(0,1);
    $precisionAff = 2*$DB_CX->DbResult(0,2);

    //Preparation au decalage horaire
    list($age_date,$age_dateAvant,$age_heure_debut,$age_heure_fin) = prepareDecalageH($tzGmt,$tzEte,$tzHiver,mktime(0,0,0,$moisEnCours,$jourEnCours,$anneeEnCours));
    $datePJSemM1=date("Y-m-d",mktime(0,0,0,$moisEnCours,$premierJourSemaine-1,$anneeEnCours));
    $datePJSemP7=date("Y-m-d",mktime(0,0,0,$moisEnCours,$premierJourSemaine+7,$anneeEnCours));

    //Heure de debut et de fin en fonction des notes hors profil
    $DB_CX->DbQuery("SELECT MIN($age_heure_debut), MAX($age_heure_fin), MAX(IF($age_dateAvant>'$datePJSemM1' AND $age_dateAvant<'$datePJSemP7' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin!=0,1,0)), MAX(IF($age_date>'$datePJSemM1' AND $age_date<'$datePJSemP7' AND $age_heure_debut>=$age_heure_fin,1,0)) FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne WHERE age_id=aco_age_id AND aco_util_id=".$idUser." AND (($age_date>'$datePJSemM1' AND $age_date<'$datePJSemP7') OR ($age_dateAvant>'$datePJSemM1' AND $age_dateAvant<'$datePJSemP7' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin!=0)) AND age_aty_id=2");
    if ($DB_CX->DbResult(0,0)!=NULL) {
      $debutJournee = min($debutJournee,$DB_CX->DbResult(0,0));
      $finJournee   = max($finJournee,$DB_CX->DbResult(0,1));
      if ($DB_CX->DbResult(0,2)) $debutJournee = 0;
      if ($DB_CX->DbResult(0,3)) $finJournee = 24;
    }
    // Regularisation pour travailler en heure pleine
    $debutJournee = floor($debutJournee);
    $finJournee   = floor($finJournee+0.75);
    $dureeJournee = ($finJournee-$debutJournee)*$precisionAff;

    // Affichage des notes pour la semaine courante
    $tabOccupe = array();
    for ($j=0; $j<7; $j++) {
      $leJour = mktime(0,0,0,$moisEnCours,$premierJourSemaine+$j,$anneeEnCours);
      // Recalcul des bascules ete/hiver en tenant compte de l'annee affichee
      $tzEte = calculBasculeDST($tzDateEte,date("Y",$leJour),$tzHeureEte,$tzGmt,0);
      $tzHiver = calculBasculeDST($tzDateHiver,date("Y",$leJour),$tzHeureHiver,$tzGmt,1);
      //Preparation au decalage horaire
      list($age_date,$age_dateAvant,$age_heure_debut,$age_heure_fin) = prepareDecalageH($tzGmt,$tzEte,$tzHiver,$leJour);
      $DB_CX->DbQuery("SELECT age_aty_id,age_heure_debut,age_heure_fin,age_util_id,age_couleur,age_date,age_date_creation,age_date_modif FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne WHERE age_id=aco_age_id AND aco_util_id=".$idUser." AND (($age_date='".date("Y-m-d",$leJour)."' OR ($age_dateAvant='".date("Y-m-d",$leJour)."' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin!=0 AND age_aty_id=2)) OR (age_date LIKE '%".date("m-d",$leJour)."' AND DATE_FORMAT(age_date,'%Y%m%d')<=".date("Ymd",$leJour)." AND age_aty_id=1)) ORDER BY age_aty_id DESC, age_date, age_heure_debut");
      while ($enr = $DB_CX->DbNextRow()) {
        //Decalage des notes en fonction du fuseau horaire
        list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['dateCreation'],$enr['dateModif']) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,date("Y-m-d",$leJour),$enr['age_date'],$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif']);
        if ($enr['age_aty_id']==2) {
          $hDeb = ($precisionAff==2 && (($enr['age_heure_debut']-floor($enr['age_heure_debut'])==0.25) || ($enr['age_heure_debut']-floor($enr['age_heure_debut'])==0.75))) ? $enr['age_heure_debut']-0.25 : $enr['age_heure_debut'];
          $hFin = ($precisionAff==2 && (($enr['age_heure_fin']-floor($enr['age_heure_fin'])==0.25) || ($enr['age_heure_fin']-floor($enr['age_heure_fin'])==0.75))) ? $enr['age_heure_fin']+0.25 : $enr['age_heure_fin'];
        } elseif ($enr['age_aty_id']==3) {
          // note couvrant toute la journee
          $hDeb = 0;
          $hFin = 24;
        }
        $hDeb = max($hDeb,$debutJournee);
        $hFin = min($hFin,$finJournee);
        if (empty($enr['age_couleur'])) {
          $enr['age_couleur'] = ($enr['age_util_id']==$idUser) ? $PPX_COULEUR_NOTE_PERSO : $PPX_COULEUR_NOTE_AFFECTEE;
        }
        for ($t=$hDeb; $t<$hFin; $t+=(1/$precisionAff)) {
          $iMat = floor(($t-$debutJournee)*$precisionAff);
          $tabOccupe[$iMat][$j] = $enr['age_couleur'];
        }
      }
    }
    //Affichage du tableau
    $largeurNote = floor(228*$largeurCellule/100);
    for ($i=0; $i<$dureeJournee; $i++) {
      if (!($i%$precisionAff)) {
        echo "        <tr align=\"center\"><td rowspan=\"".$precisionAff."\" width=\"5%\" bgcolor=\"$PPX_COULEUR_FOND_COLONNE_SEMAINE\" style=\"border-top:solid 1px $PPX_COULEUR_BORDURE_TABLEAU;\" align=\"right\">".($debutJournee+($i/$precisionAff))."</td>";
        $borderTop = "border-top:solid 1px $PPX_COULEUR_BORDURE_TABLEAU;";
      } else {
        $borderTop = "";
      }
      for ($j=0; $j<7; $j++) {
        if (substr($SEMAINE_TYPE,$j,1)=="1") {
          $leJour = mktime(0,0,0,$moisEnCours,$premierJourSemaine+$j,$anneeEnCours);
          if (!empty($tabOccupe[$i][$j])) {
            $couleurNote = $tabOccupe[$i][$j];
            $accesNote = "<a href=\"".$NOM_PAGE."?sid=".$sid."&v=1&sd=".$leJour."\"><img src=\"image/trans.gif\" alt=\"\" width=\"".$largeurNote."\" height=\"".ceil(($PPX_TAILLE_TEXTE/$precisionAff)+2)."\" border=\"0\"></a>";
          } else {
            $couleurNote = $PPX_COULEUR_FOND_COMMUN;
            $accesNote = "<a href=\"".$NOM_PAGE."?sid=".$sid."&v=2&sd=".$leJour."&hD=".($debutJournee+($i/$precisionAff))."\"><img src=\"image/trans.gif\" alt=\"\" width=\"".$largeurNote."\" height=\"".ceil(($PPX_TAILLE_TEXTE/$precisionAff)+2)."\" border=\"0\"></a>";
          }
          echo "<td width=\"".$largeurCellule."%\" bgcolor=\"".$couleurNote."\" style=\"".$borderTop."border-left:solid 1px $PPX_COULEUR_BORDURE_TABLEAU;\">".$accesNote."</td>";
        }
      }
      echo "</tr>\n";
    }

    echo ("      </table></td>
    </tr>
  </table>");
  }
// ----------------------------------------------------------------------------
// MODULE CALENDRIER
// ----------------------------------------------------------------------------
  elseif (!$v) {
    // Affichage d'une case du calendrier
    function afficheJour($nbJour, $leJour, $jourSemaine) {
      global $NOM_PAGE, $sid, $tabOccupe, $moisEnCours, $anneeEnCours, $SEMAINE_TYPE;
      global $PPX_COULEUR_SEMAINE_TYPE,$PPX_COULEUR_HORS_SEMAINE_TYPE,$PPX_COULEUR_FOND_JOUR_COURANT;
      global $tzGmt,$tzEte,$tzHiver,$localTime;
      $couleurJour = (substr($SEMAINE_TYPE,$jourSemaine-1,1)=="1") ? $PPX_COULEUR_SEMAINE_TYPE : $PPX_COULEUR_HORS_SEMAINE_TYPE;
      $numJour = ($tabOccupe[$nbJour]==1) ? "<b>".$nbJour."</b>" : $nbJour;
      echo "<td height=\"19\"".(($leJour==date("Y-m-d",$localTime)) ? " bgcolor=\"$PPX_COULEUR_FOND_JOUR_COURANT\"" : "")."><a href=\"".$NOM_PAGE."?sid=".$sid."&v=1&sd=".mktime(0,0,0,intval($moisEnCours),$nbJour,$anneeEnCours)."\"><font color=\"".$couleurJour."\">".$numJour."</font></a></td>";
    }
    // Menu de navigation entre les differents mois
    $moisAvant  = $NOM_PAGE."?sid=".$sid."&sd=".(($moisEnCours != 1) ? mktime(0,0,0,($moisEnCours-1),1,$anneeEnCours) : mktime(0,0,0,12,1,($anneeEnCours-1)));
    $moisApres  = $NOM_PAGE."?sid=".$sid."&sd=".(($moisEnCours != 12) ? mktime(0,0,0,($moisEnCours+1),1,$anneeEnCours) : mktime(0,0,0,1,1,($anneeEnCours+1)));
    $anneeAvant = $NOM_PAGE."?sid=".$sid."&sd=".mktime(0,0,0,$moisEnCours,1,($anneeEnCours-1));
    $anneeApres = $NOM_PAGE."?sid=".$sid."&sd=".mktime(0,0,0,$moisEnCours,1,($anneeEnCours+1));
    echo ("<tr align=\"center\" bgcolor=\"$PPX_COULEUR_FOND_COMMUN\"><td colspan=\"4\"><br>
    <table width=\"158\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"border:solid 1px $PPX_COULEUR_BORDURE_TABLEAU;\">
    <tr>
      <td bgcolor=\"$PPX_COULEUR_LIGNE_MOIS\"><table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" border=\"0\">
        <tr>
          <td align=\"right\" width=\"19\" height=\"19\" nowrap><a href=\"".$anneeAvant."\"><img src=\"image/timode/anneeprec.gif\" alt=\"\" width=\"8\" height=\"10\" border=\"0\"></a><a href=\"".$moisAvant."\"><img src=\"image/timode/moisprec.gif\" alt=\"\" width=\"9\" height=\"10\" border=\"0\"></a></td>
          <td align=\"center\" width=\"120\" height=\"19\" nowrap><font color=\"$PPX_COULEUR_TEXTE_MOIS\"><b>".$tabMois[$moisEnCours*1]." ".$anneeEnCours."</b></font></td>
          <td align=\"left\" width=\"19\" height=\"19\" nowrap><a href=\"".$moisApres."\"><img src=\"image/timode/moissuiv.gif\" alt=\"\" width=\"9\" height=\"10\" border=\"0\"></a><a href=\"".$anneeApres."\"><img src=\"image/timode/anneesuiv.gif\" alt=\"\" width=\"8\" height=\"10\" border=\"0\"></a></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"$PPX_COULEUR_FOND_CALENDRIER\">
        <tr align=\"center\" bgcolor=\"$PPX_COULEUR_LIGNE_JOUR_SEMAINE\"><td width=\"25\" height=\"19\" style=\"border-bottom:solid 1px $PPX_COULEUR_BORDURE_TABLEAU;border-right:solid 1px $PPX_COULEUR_BORDURE_TABLEAU;\">".trad("PPX_CAL_SEMAINE")."</td>");

    // Ligne des jours de la semaine
    for ($i=0; $i<7; $i++) {
      $couleurJour = (substr($SEMAINE_TYPE,$i,1)=="1") ? $PPX_COULEUR_SEMAINE_TYPE : $PPX_COULEUR_HORS_SEMAINE_TYPE;
      echo "<td width=\"19\" height=\"19\" style=\"border-bottom:solid 1px $PPX_COULEUR_BORDURE_TABLEAU;\"><font color=\"".$couleurJour."\">".$tabJour2[$i]."</font></td>";
    }

    echo ("</tr>\n        <tr align=\"center\"><td width=\"25\" height=\"19\" align=\"right\" bgcolor=\"$PPX_COULEUR_FOND_COLONNE_SEMAINE\" style=\"border-right:solid 1px $PPX_COULEUR_BORDURE_TABLEAU;\"><a href=\"".$NOM_PAGE."?sid=".$sid."&v=7&sd=".mktime(0,0,0,$moisEnCours, 1, $anneeEnCours)."\">".(date("W",mktime(0,0,0,$moisEnCours, 1, $anneeEnCours))+0)."</a>&nbsp;</td>");

    // Affichage des jours du mois precedent
    $moisPrec = $moisEnCours - 1;
    $nbJourMoisPrec = ($moisPrec) ? date("t", mktime(0,0,0,$moisPrec,1,$anneeEnCours)) : date("t", mktime(0,0,0,12,1,$anneeEnCours-1));
    $premierJour = date("w",mktime(0,0,0,$moisEnCours, 1, $anneeEnCours));
    if ($premierJour==0)
      $premierJour = 7;
    for($i=1;$i<$premierJour;$i++) {
      $couleurJour = (substr($SEMAINE_TYPE,$i,1)=="1") ? $PPX_COULEUR_SEMAINE_TYPE : $PPX_COULEUR_HORS_SEMAINE_TYPE;
      echo "<td height=\"19\" bgcolor=\"$PPX_COULEUR_MOIS_PREC\"><font color=\"".$couleurJour."\" style=\"font-size:".($PPX_TAILLE_TEXTE-2)."px\"><i>".($nbJourMoisPrec-$premierJour+$i+1)."</i></font></td>";
    }

    //Preparation au decalage horaire
    list($age_date,$age_dateAvant,$age_heure_debut,$age_heure_fin) = prepareDecalageH($tzGmt,$tzEte,$tzHiver,mktime(0,0,0,$moisEnCours,1,$anneeEnCours));

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
      afficheJour($nbJour, $leJour, $i);
    }

    echo "</tr>\n";

    // Affichage du reste du mois courant
    $cpt=1;
    $finDeMois = false;
    for($j=1;!$finDeMois;$j++) {
      if (checkdate($moisEnCours, $nbJour+1, $anneeEnCours)) {
        echo "        <tr align=\"center\"><td width=\"25\" height=\"19\" align=\"right\" bgcolor=\"$PPX_COULEUR_FOND_COLONNE_SEMAINE\" style=\"border-right:solid 1px $PPX_COULEUR_BORDURE_TABLEAU;\"><a href=\"".$NOM_PAGE."?sid=".$sid."&v=7&sd=".mktime(0,0,0,$moisEnCours, $nbJour+1, $anneeEnCours)."\">".(date("W",mktime(0,0,0,$moisEnCours, $nbJour+1, $anneeEnCours))+0)."</a>&nbsp;</td>";
        for($i=1;$i<8;$i++) {
          if (checkdate($moisEnCours, ++$nbJour, $anneeEnCours)) {
            $leJour = ($nbJour < 10) ? $anneeEnCours."-".$moisEnCours."-0".$nbJour : $anneeEnCours."-".$moisEnCours."-".$nbJour;
            afficheJour($nbJour, $leJour, $i);
          } else {
            $finDeMois = true;
            $couleurJour = (substr($SEMAINE_TYPE,$i-1,1)=="1") ? $PPX_COULEUR_SEMAINE_TYPE : $PPX_COULEUR_HORS_SEMAINE_TYPE;
            echo "<td height=\"19\" bgcolor=\"$PPX_COULEUR_MOIS_PREC\"><font color=\"".$couleurJour."\" style=\"font-size:".($PPX_TAILLE_TEXTE-2)."px\"><i>".($cpt++)."</i></font></td>";
          }
        }
        echo "</tr>\n";
      } else {
        $finDeMois = true;
      }
    }

    echo ("        <tr align=\"center\" bgcolor=\"$PPX_COULEUR_LIGNE_AUJOURDHUI\"><td colspan=\"8\" height=\"18\" style=\"border-top:solid 1px $PPX_COULEUR_BORDURE_TABLEAU;\"><a href=\"".$NOM_PAGE."?sid=".$sid."&v=1&sd=".mktime(0,0,0,date("n",$localTime), date("j",$localTime), date("Y",$localTime))."\">".trad("PPX_AUJOURDHUI").": ".date("d/m/Y",$localTime)."</a></td></tr>
      </table></td>
    </tr>
  </table>");
  }

  // Fermeture BDD
  $DB_CX->DbDeconnect();

// ----------------------------------------------------------------------------
// PIED DE PAGE HTML COMMUN
// ----------------------------------------------------------------------------
  if ($closeForm) {
    echo "</form>";
  }
  echo ((($v==7) ? "" : "&nbsp;")."</td></tr><tr align=\"center\" bgcolor=\"$PPX_COULEUR_FOND_COPYRIGHT\"><td colspan=\"4\"><font color=\"$PPX_COULEUR_NOM_APPLI\">".sprintf(trad("PPX_VERSION_PHENIX"), $APPLI_VERSION)."</font></td></tr></table>
</body>
</html>");
?>
