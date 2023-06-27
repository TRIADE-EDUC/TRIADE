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

  require("inc/nocache.inc.php");
  require("inc/html.inc.php");
  include("inc/param.inc.php");
  if (isset($sid)) {
    include("inc/fonctions.inc.php");
  } else {
    exit;
  }

  $idUser = Session_ok($sid);

  include("skins/$APPLI_STYLE.php");
  include("lang/$APPLI_LANGUE.php");

  $bgColor[0]="#FFFFFF";
  $bgColor[1]="#F7F7EF";

  $vu += 0;
?>
<!DOCTYPE html public "-//w3c//dtd html 4.0 transitional//en">
<HTML>
<HEAD>
  <META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <META http-equiv="Cache-Control" content="no-cache">
  <META name="Author" content="Stephane TEIL (phenix-agenda@laposte.net)">
  <META name="robots" content="noindex">
  <TITLE><?php echo sprintf(trad("IMPRESSION_VERSION_PHENIX"), $APPLI_VERSION); ?></TITLE>
  <STYLE type="text/css">
  <!--
    BODY {
      FONT-FAMILY: Verdana, Arial, Tahoma;
      FONT-SIZE: 11px;
      FONT-WEIGHT: normal;
      COLOR: #000000;
      BACKGROUND-COLOR: #FFFFFF;
    }

    TABLE  {
      FONT-FAMILY: Verdana, Arial, Tahoma;
      FONT-SIZE: 10px;
      COLOR: #000000;
      BORDER-COLLAPSE: collapse;
    }
    .borderAll {
      COLOR: #000000;
      BORDER: solid 1px #000000;
      PADDING-TOP: 3px;
      TEXT-ALIGN: center;
    }
    TD.borderNone {
      COLOR: #000000;
      PADDING-TOP: 3px;
      TEXT-ALIGN: center;
      BORDER-TOP: solid 1px #000000;
      BORDER-BOTTOM: solid 1px #000000;
    }
    TD.borderNote {
      COLOR: #000000;
      BORDER-LEFT: solid 1px #000000;
      BORDER-RIGHT: solid 1px #000000;
      BORDER-BOTTOM: solid 1px #000000;
      PADDING: 2px;
      PADDING-TOP: 3px;
    }
    TD.borderNotePerso {
      COLOR: #000000;
      BORDER-LEFT: solid 1px #000000;
      BORDER-RIGHT: solid 1px #000000;
      BORDER-BOTTOM: solid 1px #000000;
      PADDING: 2px;
      PADDING-TOP: 3px;
    }
    TD.mensNote {
      COLOR: #000000;
      BORDER: solid 1px #000000;
    }
    TD.mensFerie {
      COLOR: #000000;
      BORDER: solid 1px #000000;
      BACKGROUND-COLOR: <?php echo $CalJourFerie; ?>;
    }
    TD.mensVide {
      COLOR: #000000;
      BORDER: solid 1px #000000;
    }
    TD.mensPrec {
      COLOR: #000000;
      BORDER: solid 1px #000000;
      BACKGROUND-COLOR: #E9EEF3;
    }
    TD.numWeek {
      COLOR: #000000;
      FONT-WEIGHT: bold;
      BORDER: solid 1px #000000;
      BACKGROUND-COLOR: <?php echo $bgColor[1]; ?>;
      PADDING: 2px;
      TEXT-ALIGN: center;
    }
    H4 {
      FONT-SIZE: 15px;
    }
    TD.lettre {
      HEIGHT: 30px;
      WIDTH: 30px;
      BACKGROUND-COLOR: #000000;
      COLOR: #FFFFFF;
      FONT-SIZE: 15px;
      FONT-WEIGHT: bold;
      TEXT-ALIGN: center;
      VERTICAL-ALIGN: middle;
    }
    TD.nomCtt {
      FONT-WEIGHT: bold;
      BACKGROUND-COLOR: #BBBBBB;
    }
  -->
  </STYLE>
</HEAD>

<BODY onLoad="javascript: window.focus();" topmargin=0 leftmargin=1 marginwidth=1 marginheight=0>
<?php
  // Recuperation des infos de timezone de l'utilisateur
  $DB_CX->DbQuery("SELECT tzn_libelle, tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver, t2.util_format_heure, t2.util_timezone_partage FROM ${PREFIX_TABLE}utilisateur t1, ${PREFIX_TABLE}utilisateur t2, ${PREFIX_TABLE}timezone WHERE t1.util_id=".$USER_SUBSTITUE." AND t2.util_id=".$idUser." AND ((tzn_zone=t1.util_timezone AND t2.util_timezone_partage='O') OR (tzn_zone=t2.util_timezone AND t2.util_timezone_partage='N'))");
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
  $tzPartage = $DB_CX->DbResult(0,"util_timezone_partage");

  // Ajustement de la date en fonction du timezone
  $decalageHoraire = calculDecalageH($tzGmt,$tzEte,$tzHiver,mktime(gmdate("H"),gmdate("i"),0,gmdate("n"),gmdate("j"),gmdate("Y")));
  $localTime = mktime(gmdate("H")+floor($decalageHoraire),gmdate("i")+($decalageHoraire*60)%60,gmdate("s"),gmdate("n"),gmdate("j"),gmdate("Y"));

  // Recalcul des bascules ete/hiver en tenant compte de la date affichee
  $sdAnnee = (!$sd) ? ((!isset($annee)) ? gmdate("Y") : $annee) : date("Y", $sd);
  $tzEte = calculBasculeDST($tzDateEte,$sdAnnee,$tzHeureEte,$tzGmt,0);
  $tzHiver = calculBasculeDST($tzDateHiver,$sdAnnee,$tzHeureHiver,$tzGmt,1);

  $jourEnCours  = @date("d", $sd);
  $moisEnCours  = @date("m", $sd);
  $anneeEnCours = @date("Y", $sd);
  $DB_CX->DbQuery("SELECT CONCAT(".$FORMAT_NOM_UTIL.") FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$USER_SUBSTITUE);
  $nomUtil = $DB_CX->DbResult(0,0);

//---------------------------------------------------------
//                    PLANNING QUOTIDIEN
//---------------------------------------------------------
if (!$vu) {
  /****************************************************************************************************************/
  function getInfoNote(&$enr) {
    global $classNote,$plageNote,$noteRec;
    global $USER_SUBSTITUE,$idUser,$AgendaFondNotePerso,$AgendaFondNote,$PlanningNotePrivee;
    global $formatHeure;
    //Propriete Privee ou Publique de la note
    if ($USER_SUBSTITUE!=$idUser && $enr['age_util_id']!=$idUser && $enr['age_prive']==1) {
      $enr['age_libelle'] = trad("COMMUN_OCCUPE");
      $enr['age_detail'] = ""; // Detail non visible si note privee
      $enr['age_couleur'] = $PlanningNotePrivee; // Couleur de note non visible si note privee
      $enr['age_lieu'] = ""; // Emplacement non visible  si note privee
      $notePrive = true;
    }
    else {
      $notePrive = false;
      //Info sur le contact associe
      if (!empty($enr['nomContact'])) {
        $enr['age_detail'] = trad("IMPRESSION_CONTACT_ASSOCIE")." : <B>".$enr['nomContact']."</B>".chr(13).$enr['age_detail'];
      }
      //Retour a la ligne et suppression des lignes vides dans l'affichage du detail de la note
      $tabDetail = explode(chr(13),$enr['age_detail']);
      $enr['age_detail'] = "";
      for ($nb=0;$nb<count($tabDetail);$nb++) {
        $tabDetail[$nb]=trim($tabDetail[$nb]);
        if (!empty($tabDetail[$nb]))
          $enr['age_detail'] .= "<BR>".$tabDetail[$nb];
      }
    }
    //Couleur de fond de la note si non definie dans la bdd
    if (empty($enr['age_couleur']))
      $enr['age_couleur'] = ($enr['age_util_id']==$USER_SUBSTITUE) ? $AgendaFondNotePerso : $AgendaFondNote;
    //Propriete de la note
    if ($enr['age_util_id']==$idUser && $USER_SUBSTITUE==$idUser) {
      $classNote = "borderNotePerso";
    }
    else {
      $classNote = ($enr['age_util_id']==$USER_SUBSTITUE) ? "borderNotePerso" : "borderNote";
    }
    //Plage horaire de la note
    $plageNote = ($enr['age_aty_id']==2) ? afficheHeure(floor($enr['age_heure_debut']),$enr['age_heure_debut'],$formatHeure)."&rsaquo;".afficheHeure(floor($enr['age_heure_fin']),$enr['age_heure_fin'],$formatHeure) : trad("COMMUN_JOURNEE_ENTIERE");
    //Recursivite de la note
    $noteRec = ($enr['age_ape_id']!=1 && $notePrive==false) ? "&nbsp;<IMG src=\"image/recurrent.gif\" border=\"0\" align=\"absmiddle\">" : "";
  }
  /****************************************************************************************************************/
  $dateCrt = $anneeEnCours."-".$moisEnCours."-".$jourEnCours;
  $ligneAnniv = $premiereLettre = "";
  // Anniversaire(s) de l'agenda
  $DB_CX->DbQuery("SELECT age_libelle FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne WHERE age_id=aco_age_id AND aco_util_id=".$USER_SUBSTITUE." AND age_date LIKE '%".substr($dateCrt,4)."' AND DATE_FORMAT(age_date,'%Y%m%d')<=".date("Ymd",$sd)." AND age_aty_id=1");
  while ($enr = $DB_CX->DbNextRow()) {
    if (empty($premiereLettre))
      $premiereLettre = strtolower(substr($enr['age_libelle'],0,1));
    $ligneAnniv .= $enr['age_libelle']." / ";
  }
  // Anniversaire(s) du calepin (y compris les contacts partages)
  $DB_CX->DbQuery("SELECT CONCAT(".$FORMAT_NOM_CONTACT.") AS nomContact FROM ${PREFIX_TABLE}calepin WHERE (cal_util_id=".$USER_SUBSTITUE." OR cal_partage='O') AND cal_date_naissance LIKE '%".substr($dateCrt,4)."' AND DATE_FORMAT(cal_date_naissance,'%Y%m%d')<=".date("Ymd",$sd));
  while ($enr = $DB_CX->DbNextRow()) {
    if (empty($premiereLettre))
      $premiereLettre = strtolower(substr($enr['nomContact'],0,1));
    $ligneAnniv .= $enr['nomContact']." / ";
  }
  if (!empty($ligneAnniv)) {
    $genre = prefixeMot($premiereLettre,trad("COMMUN_PREFIXE_D"),trad("COMMUN_PREFIXE_DE"))."<B>";
    $ligneAnniv = "<CENTER>".sprintf(trad("IMPRESSION_ANNIVERSAIRE_DE"), $genre.substr($ligneAnniv,0,strlen($ligneAnniv)-3))."</B></CENTER><BR>";
  }
?>
<!-- PLANNING QUOTIDIEN -->
  <H4 align="center"><?php echo sprintf(trad("IMPRESSION_AGENDA_DE"), prefixeMot(strtolower(substr($nomUtil,0,1)),trad("COMMUN_PREFIXE_D"),trad("COMMUN_PREFIXE_DE")).$nomUtil); ?><BR><?php echo sprintf(trad("IMPRESSION_JOURNEE_DU"), $tabJour[date("w",$sd)]." ".$jourEnCours." ".strtolower($tabMois[date("n",$sd)])." ".$anneeEnCours); ?></H4>
<?php echo $ligneAnniv; ?>
  <TABLE cellspacing="0" cellpadding="4" width="100%" border="0">
  <TR>
    <TD><TABLE cellspacing="0" cellpadding="0" width="100%" border="0" style="border: 1px solid #000000;">
<?php
  //Si l'utilisateur a choisi une couleur de note on l'ajoute dans la clause WHERE de la recherche
  $whereCouleur = "";
  $FILTRE_COULEUR = str_replace("!","#",urldecode($extra));
  if ($FILTRE_COULEUR != "ALL" && !empty($FILTRE_COULEUR)) {
    $whereCouleur = ($FILTRE_COULEUR == $AgendaFondNotePerso) ? " AND (age_couleur='".$FILTRE_COULEUR."' OR age_couleur='')" : " AND age_couleur='".$FILTRE_COULEUR."'";
  }

  //Parametres de la journee choisis par l'utilisateur
  $DB_CX->DbQuery("SELECT util_debut_journee, util_fin_journee FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$USER_SUBSTITUE);
  $debutJournee = $DB_CX->DbResult(0,0);
  $finJournee   = $DB_CX->DbResult(0,1);
  //Decalage des bornes de la journee en fonction du timezone
  if ($tzPartage!="O") {
    // Recuperation des infos de timezone de l'utilisateur
    $DB_CX->DbQuery("SELECT tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver, util_format_heure FROM ${PREFIX_TABLE}utilisateur, ${PREFIX_TABLE}timezone WHERE util_id=".$USER_SUBSTITUE." AND tzn_zone=util_timezone");
    $tzUtilGmt = $DB_CX->DbResult(0,"tzn_gmt");
    $tzUtilDateEte = $DB_CX->DbResult(0,"tzn_date_ete");
    $tzUtilHeureEte = $DB_CX->DbResult(0,"tzn_heure_ete");
    $tzUtilDateHiver = $DB_CX->DbResult(0,"tzn_date_hiver");
    $tzUtilHeureHiver = $DB_CX->DbResult(0,"tzn_heure_hiver");
    $dateCrt = $anneeEnCours."-".$moisEnCours."-".$jourEnCours;
    // Passage d'un fuseau a l'autre via utc
    list($debutJournee,$finJournee,$dCrt,$dMdf,$date) = decaleNote($tzUtilGmt,$tzUtilDateEte,$tzUtilHeureEte,$tzUtilDateHiver,$tzUtilHeureHiver,$dateCrt,$dateCrt,$debutJournee,$finJournee,$dCrt,$dMdf,0,1);
    list($debutJournee,$finJournee,$dCrt,$dMdf,$date) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$dateCrt,$dateCrt,$debutJournee,$finJournee,$dCrt,$dMdf,0,0);
  }

  //Preparation au decalage horaire
  list($age_date,$age_dateAvant,$age_heure_debut,$age_heure_fin) = prepareDecalageH($tzGmt,$tzEte,$tzHiver,mktime(0,0,0,$moisEnCours,$jourEnCours,$anneeEnCours));

  //Heure de debut et de fin en fonction des notes non affichees
  $DB_CX->DbQuery("SELECT MIN($age_heure_debut), MAX($age_heure_fin), MAX(IF($age_dateAvant='".$dateCrt."' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin!=0,1,0)), MAX(IF($age_date='".$dateCrt."' AND $age_heure_debut>=$age_heure_fin,1,0)) FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne WHERE age_id=aco_age_id AND aco_util_id=".$USER_SUBSTITUE." AND ($age_date='".$dateCrt."' OR ($age_dateAvant='".$dateCrt."' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin!=0)) AND age_aty_id=2".$whereCouleur);
  if ($DB_CX->DbResult(0,0)!=NULL) {
    $debutJournee = min($debutJournee,$DB_CX->DbResult(0,0));
    if (($debutJournee-floor($debutJournee)==0.25) || ($debutJournee-floor($debutJournee)==0.75))
      $debutJournee -= 0.25;
    $finJournee = max($finJournee,$DB_CX->DbResult(0,1));
    if (($finJournee-floor($finJournee)==0.25) || ($finJournee-floor($finJournee)==0.75))
      $finJournee += 0.25;
    if ($DB_CX->DbResult(0,2)) $debutJournee = 0;
    if ($DB_CX->DbResult(0,3)) $finJournee = 24;
  }

  $dureeJournee = ($finJournee-$debutJournee)*2;

  //Nb maxi de notes en meme temps
  $maxNote = 1;
  $tabIdNoteMultiple = array();
  for ($hCrt=$debutJournee;$hCrt<$finJournee;$hCrt=$hCrt+0.5) {
    $DB_CX->DbQuery("SELECT DISTINCT(age_id) FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne WHERE age_id=aco_age_id AND aco_util_id=".$USER_SUBSTITUE." AND ($age_date='".$dateCrt."' OR ($age_dateAvant='".$dateCrt."' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin>$hCrt)) AND ((($age_heure_debut<=".$hCrt." OR ($age_dateAvant='".$dateCrt."' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin!=0)) AND ($age_heure_fin>".$hCrt." OR $age_heure_debut>=$age_heure_fin)) OR (($age_heure_debut<=".($hCrt+0.25)." OR ($age_dateAvant='".$dateCrt."' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin!=0)) AND ($age_heure_fin>".($hCrt+0.25)." OR $age_heure_debut>=$age_heure_fin))) AND age_aty_id=2".$whereCouleur);
    if ($DB_CX->DbNumRows() > 1) {
      while ($enr = $DB_CX->DbNextRow()) {
        if (!in_array($enr['age_id'], $tabIdNoteMultiple))
          $tabIdNoteMultiple[] = $enr['age_id'];
      }
      if ($DB_CX->DbNumRows() > $maxNote)
        $maxNote = $DB_CX->DbNumRows();
    }
  }
  // Definit la taille du colspan qui sera applique lorsqu'il n'y a pas de chevauchement de note
  $colspanSize = $maxNote;

  $widthCell = round(100/$maxNote++);

  //Initialisation de la matrice d'affichage
  for ($i=0;$i<$dureeJournee;$i++) {
    for ($j=0;$j<$maxNote;$j++) {
      $matAff[$i][$j] = ($j) ? "        <TD class=\"borderNone\"><IMG src=\"image/trans.gif\"></TD>\n" : "borderAll";
      $tabCol[$j] = 0;
    }
  }

  // Index et tableau pour stocker les evenements et notes globales
  $iGlb = 0;
  $aGlobale = array();

  // Evenement(s) du jour (affiche(s) comme des notes globales)
  $DB_CX->DbQuery("SELECT DISTINCT eve_libelle, eve_couleur FROM ${PREFIX_TABLE}evenement WHERE DATE_FORMAT(eve_date_debut,'%Y%m%d')<='".date("Ymd",$sd)."' AND DATE_FORMAT(eve_date_fin,'%Y%m%d')>='".date("Ymd",$sd)."'".(($USER_SUBSTITUE==$idUser) ? " AND (eve_util_id=".$idUser." OR eve_partage='O')" : " AND eve_partage='O'"));
  while ($enr = $DB_CX->DbNextRow()) {
    // Couleur de l'evenement
    if (empty($enr['eve_couleur']))
      $enr['eve_couleur'] = $CalJourEvenement;
    //Stockage des infos relatives a l'evenement
    $aGlobale[$iGlb]  = "      <TR>\n        <TD colspan=\"".($maxNote+1)."\" class=\"borderNotePerso\" bgcolor=\"".$enr['eve_couleur']."\">";
    $aGlobale[$iGlb++] .= "[".trad("COMMUN_EVENEMENT")."]&nbsp;<B>".$enr['eve_libelle']."</B></TD>\n      </TR>\n";
  }

  //Lecture des notes couvrant la totalite d'une journee
  $DB_CX->DbQuery("SELECT age_heure_debut,age_heure_fin,age_ape_id,age_libelle,age_detail,age_util_id,age_prive,age_couleur,age_aty_id,age_lieu,CONCAT(".$FORMAT_NOM_CONTACT.") AS nomContact FROM ${PREFIX_TABLE}agenda LEFT JOIN ${PREFIX_TABLE}calepin ON cal_id=age_cal_id, ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}utilisateur WHERE age_id=aco_age_id AND aco_util_id=".$USER_SUBSTITUE." AND age_date='".$dateCrt."' AND age_aty_id=3 AND util_id=age_util_id".$whereCouleur." ORDER BY age_heure_debut ASC");
  while ($enr = $DB_CX->DbNextRow()) {
    //Formatage des informations sur la note
    getInfoNote($enr);
    //Stockage des infos relatives aux notes couvrant la totalite d'une journee
    $aGlobale[$iGlb]  = "      <TR>\n        <TD colspan=\"".($maxNote+1)."\" class=\"".$classNote."\" bgcolor=\"".$enr['age_couleur']."\">";
    $aGlobale[$iGlb++] .= "[".$plageNote."]&nbsp;<B>".$enr['age_libelle']."</B>".((!empty($enr['age_lieu'])) ? " <I>(".$enr['age_lieu'].")</I>" : "").$noteRec.$enr['age_detail']."</TD>\n      </TR>\n";
  }

  //Lecture des notes de la journee
  for ($hCrt=$debutJournee;$hCrt<$finJournee;$hCrt=$hCrt+0.5) {
    $iMat = ($hCrt-$debutJournee)*2;
    $DB_CX->DbQuery("SELECT age_heure_debut,age_heure_fin,age_ape_id,age_libelle,age_detail,age_util_id,age_prive,age_couleur,age_aty_id,age_lieu,CONCAT(".$FORMAT_NOM_CONTACT.") AS nomContact,age_id,age_date,age_date_creation,age_date_modif FROM ${PREFIX_TABLE}agenda LEFT JOIN ${PREFIX_TABLE}calepin ON cal_id=age_cal_id, ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}utilisateur WHERE age_id=aco_age_id AND aco_util_id=".$USER_SUBSTITUE." AND ($age_date='".$dateCrt."' OR ($age_dateAvant='".$dateCrt."' AND $age_heure_debut>=$age_heure_fin AND $hCrt=0 AND $age_heure_fin!=0)) AND ($age_heure_debut=".$hCrt." OR $age_heure_debut=".($hCrt+0.25)." OR ($age_dateAvant='".$dateCrt."' AND $age_heure_debut>=$age_heure_fin AND $hCrt=0)) AND age_aty_id=2 AND util_id=age_util_id".$whereCouleur." ORDER BY age_date, age_heure_debut ASC");
    while ($enr = $DB_CX->DbNextRow()) {
      //Decalage des notes en fonction du fuseau horaire
      list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['dateCreation'],$enr['dateModif']) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$dateCrt,$enr['age_date'],$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif']);
      //Formatage des informations sur la note
      getInfoNote($enr);
      //Modification des horaires de la note en fonction de la precision d'affichage de l'utilisateur
      $hDebut = $hCrt;
      $hFin = (($enr['age_heure_fin']-floor($enr['age_heure_fin'])==0.25) || ($enr['age_heure_fin']-floor($enr['age_heure_fin'])==0.75)) ? $enr['age_heure_fin']+0.25 : $enr['age_heure_fin'];
      $hFin = min($hFin,$finJournee);
      $duree = ($hFin-$hDebut)*2;

      //Position dans la matrice d'affichage
      $colToUse = 0;
      for ($i=1;$i<$maxNote && !$colToUse;$i++) {
        if ($tabCol[$i]<=$hDebut) {
          $colToUse = $i;
          $tabCol[$i] = $hFin;
        }
      }

      //Stockage des informations sur la note
      if (in_array($enr['age_id'],$tabIdNoteMultiple)) {
        $colspanCell = " width=\"".$widthCell."%\"";
      } else {
        $colspanCell = " colspan=\"".$colspanSize."\" width=\"".($widthCell * ($maxNote-1))."%\"";
        // Effacement des cellules adjascentes en cas de non chevauchement
        for ($i=0;$i<$duree;$i++) {
          for ($j=2;$j<$maxNote;$j++) {
            $matAff[$iMat+$i][$j] = "";
          }
        }
      }
      $matAff[$iMat][$colToUse] = "        <TD".$colspanCell." rowspan=\"".$duree."\" class=\"".$classNote."\" bgcolor=\"".$enr['age_couleur']."\">";
      $matAff[$iMat][$colToUse] .= "[".$plageNote."]&nbsp;<B>".$enr['age_libelle']."</B>".((!empty($enr['age_lieu'])) ? " <I>(".$enr['age_lieu'].")</I>" : "").$noteRec.$enr['age_detail']."</TD>\n";
      //Correction de l'affichage pour les notes sur plusieurs lignes
      for ($i=1;$i<$duree-1;$i++) {
        $matAff[$iMat+$i][$colToUse] = "";
      }
      if ($duree>1) {
        $matAff[$iMat+$duree-1][$colToUse] = "";
      }
    }
  }

  //Affichage des notes couvrant toute la journee
  for ($i=0;$i<count($aGlobale);$i++)
    echo $aGlobale[$i];
  //Affichage du tableau
  $index = 0;
  for ($i=0;$i<$dureeJournee;$i++) {
    $index=1-$index;
    echo "      <TR bgcolor=\"".$bgColor[$index]."\" height=\"19\" valign=\"top\">\n";
    for ($j=0;$j<$maxNote+1;$j++) {
      echo ($j) ? $matAff[$i][$j] : "        <TD width=\"40\" nowrap class=\"".$matAff[$i][0]."\">".afficheHeure($debutJournee+($i/2),$debutJournee+($i/2),$formatHeure)."</TD>\n";
    }
    echo "      </TR>\n";
  }
?>
    </TABLE></TD>
  </TR>
  </TABLE>
<!-- FIN PLANNING QUOTIDIEN -->
<?php
}
//---------------------------------------------------------
//                  PLANNING HEBDOMADAIRE
//---------------------------------------------------------
elseif ($vu==_MENU_PLG_HEBDO) {
  $tabJourFerie = getListeJourFerie($anneeEnCours);
  $indexJourCrt = date("w",$sd);
  if ($indexJourCrt == 0)
    $indexJourCrt = 7;
  $premierJourSemaine = $jourEnCours-$indexJourCrt+1;

  $debutSemaine = mktime(0,0,0,$moisEnCours,$premierJourSemaine,$anneeEnCours);
  $finSemaine   = mktime(0,0,0,$moisEnCours,$premierJourSemaine+6,$anneeEnCours);

  //Si l'utilisateur a choisi une couleur de note on l'ajoute dans la clause WHERE de la recherche
  $whereCouleur = "";
  $FILTRE_COULEUR = str_replace("!","#",urldecode($extra));
  if ($FILTRE_COULEUR != "ALL" && !empty($FILTRE_COULEUR)) {
    $whereCouleur = ($FILTRE_COULEUR == $AgendaFondNotePerso) ? " AND (age_couleur='".$FILTRE_COULEUR."' OR age_couleur='')" : " AND age_couleur='".$FILTRE_COULEUR."'";
  }
?>
<!-- PLANNING HEBDOMADAIRE -->
  <H4 align="center"><?php echo sprintf(trad("IMPRESSION_AGENDA_DE"), prefixeMot(strtolower(substr($nomUtil,0,1)),trad("COMMUN_PREFIXE_D"),trad("COMMUN_PREFIXE_DE")).$nomUtil); ?><BR><?php echo sprintf(trad("IMPRESSION_SEMAINE_DU"), date("W",$debutSemaine), date("d",$debutSemaine)." ".strtolower($tabMois[date("n",$debutSemaine)])." ".date("Y",$debutSemaine), date("d",$finSemaine)." ".strtolower($tabMois[date("n",$finSemaine)])." ".date("Y",$finSemaine)); ?></H4>
  <TABLE cellspacing="1" cellpadding="0" width="100%" border="0" bgcolor="#000000" style="border-collapse:separate;">
<?php
  $index = 0;
  $nbJSelect=0;

  for ($i=0;$i<7;$i++) {
    if (substr($SEMAINE_TYPE,$i,1)==1) {
      $index=1-$index;
      $leJour = mktime(0,0,0,$moisEnCours,$premierJourSemaine+$i,$anneeEnCours);
      // Recalcul des bascules ete/hiver en tenant compte de l'annee affichee
      $tzEte = calculBasculeDST($tzDateEte,date("Y",$leJour),$tzHeureEte,$tzGmt,0);
      $tzHiver = calculBasculeDST($tzDateHiver,date("Y",$leJour),$tzHeureHiver,$tzGmt,1);
      //Preparation au decalage horaire
      list($age_date,$age_dateAvant,$age_heure_debut,$age_heure_fin) = prepareDecalageH($tzGmt,$tzEte,$tzHiver,$leJour);
      $DB_CX->DbQuery("SELECT age_aty_id,age_heure_debut,age_heure_fin,age_libelle,age_ape_id,age_detail,age_prive,age_couleur,age_util_id,age_lieu,CONCAT(".$FORMAT_NOM_CONTACT.") AS nomContact,age_date,age_date_creation,age_date_modif FROM ${PREFIX_TABLE}agenda LEFT JOIN ${PREFIX_TABLE}calepin ON cal_id=age_cal_id, ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}utilisateur WHERE age_id=aco_age_id AND aco_util_id=".$USER_SUBSTITUE." AND ((($age_date='".date("Y-m-d",$leJour)."' OR ($age_dateAvant='".date("Y-m-d",$leJour)."' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin!=0 AND age_aty_id=2))".$whereCouleur.") OR (age_date LIKE '%".date("m-d",$leJour)."' AND DATE_FORMAT(age_date,'%Y%m%d')<=".date("Ymd",$leJour)." AND age_aty_id=1)) AND util_id=age_util_id ORDER BY age_aty_id DESC, age_date, age_heure_debut ASC");
      $ligneAnniv = $ligneNote = $premiereLettre = "";
      for ($j=0;$j<$DB_CX->DbNumRows();$j++) {
        $enr = $DB_CX->DbNextRow();
        //Decalage des notes en fonction du fuseau horaire
        list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['dateCreation'],$enr['dateModif']) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,date("Y-m-d",$leJour),$enr['age_date'],$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif']);
        if ($enr['age_aty_id']==1) { //Stockage des infos relatives aux anniversaires
          if (empty($premiereLettre))
            $premiereLettre = strtolower(substr($enr['age_libelle'],0,1));
          $ligneAnniv .= $enr['age_libelle']." / ";
        } else { //Stockage des infos relatives aux notes
          if ($USER_SUBSTITUE!=$idUser && $enr['age_util_id']!=$idUser && $enr['age_prive']==1) {
            $enr['age_libelle'] = trad("COMMUN_OCCUPE");
            $enr['age_detail'] = ""; // Detail non visible si note privee
            $enr['age_couleur'] = $PlanningNotePrivee; // Couleur de note non visible si note privee
            $enr['age_lieu'] = ""; // Emplacement non visible  si note privee
            $notePrive = true;
          } else
            $notePrive = false;
            //Info sur le contact associe
            if (!empty($enr['nomContact'])) {
              $enr['age_detail'] = trad("IMPRESSION_CONTACT_ASSOCIE")." : <B>".$enr['nomContact']."</B>".chr(13).$enr['age_detail'];
            }
          //Couleur de fond de la note si non definie dans la bdd
          if (empty($enr['age_couleur']))
            $enr['age_couleur'] = ($enr['age_util_id']==$USER_SUBSTITUE) ? $AgendaFondNotePerso : $AgendaFondNote;
          $plageNote = ($enr['age_aty_id']==2) ? afficheHeure(floor($enr['age_heure_debut']),$enr['age_heure_debut'],$formatHeure)."&rsaquo;".afficheHeure(floor($enr['age_heure_fin']),$enr['age_heure_fin'],$formatHeure) : trad("COMMUN_JOURNEE_ENTIERE");
          $ligneNote .= "<DIV style=\"background-color:".$enr['age_couleur'].";\">&nbsp;[".$plageNote."]&nbsp;<B>".$enr['age_libelle']."</B>".((!empty($enr['age_lieu'])) ? " <I>(".$enr['age_lieu'].")</I>" : "");
          if ($enr['age_ape_id']!=1 && $notePrive==false) {
            $ligneNote .= "&nbsp;<IMG src=\"image/recurrent.gif\" border=\"0\" align=\"absmiddle\">";
          }
          $tabDetail = explode(chr(13),$enr['age_detail']);
          for ($nb=0;$nb<count($tabDetail);$nb++) {
            $tabDetail[$nb]=trim($tabDetail[$nb]);
            if (!empty($tabDetail[$nb]))
              $ligneNote .= "<BR>&nbsp;&nbsp;&nbsp;&nbsp;".$tabDetail[$nb];
          }
          $ligneNote .= "</DIV>";
        }
      }

      // Evenement(s) du jour (affiche(s) comme des notes globales)
      $DB_CX->DbQuery("SELECT DISTINCT eve_libelle, eve_couleur FROM ${PREFIX_TABLE}evenement WHERE DATE_FORMAT(eve_date_debut,'%Y%m%d')<='".date("Ymd",$leJour)."' AND DATE_FORMAT(eve_date_fin,'%Y%m%d')>='".date("Ymd",$leJour)."'".(($USER_SUBSTITUE==$idUser) ? " AND (eve_util_id=".$idUser." OR eve_partage='O')" : " AND eve_partage='O'"));
      while ($enr = $DB_CX->DbNextRow()) {
        // Couleur de l'evenement
        if (empty($enr['eve_couleur']))
          $enr['eve_couleur'] = $CalJourEvenement;
        //Stockage des infos relatives a l'evenement au debut de la liste
        $ligneNote = "<DIV style=\"background-color:".$enr['eve_couleur'].";\">&nbsp;[".trad("COMMUN_EVENEMENT")."]&nbsp;<B>".$enr['eve_libelle']."</B></DIV>".$ligneNote;
      }

      // Anniversaire(s) du calepin (y compris les contacts partages)
      $DB_CX->DbQuery("SELECT CONCAT(".$FORMAT_NOM_CONTACT.") AS nomContact FROM ${PREFIX_TABLE}calepin WHERE (cal_util_id=".$USER_SUBSTITUE." OR cal_partage='O') AND cal_date_naissance LIKE '%".date("m-d",$leJour)."' AND DATE_FORMAT(cal_date_naissance,'%Y%m%d')<=".date("Ymd",$leJour));
      while ($enr = $DB_CX->DbNextRow()) {
        if (empty($premiereLettre))
          $premiereLettre = strtolower(substr($enr['nomContact'],0,1));
        $ligneAnniv .= $enr['nomContact']." / ";
      }
      // Format du tableau si anniversaire du jour a afficher
      if (!empty($ligneAnniv)) {
        $rowspan = 2;
        $hauteur = 65;
        $genre = prefixeMot($premiereLettre,trad("COMMUN_PREFIXE_D"),trad("COMMUN_PREFIXE_DE"))."<B>";
        $ligneAnniv = ("    <TD height=\"15\" bgcolor=\"#E9EEF3\">&nbsp;".sprintf(trad("IMPRESSION_ANNIVERSAIRE_DE"), $genre.substr($ligneAnniv,0,strlen($ligneAnniv)-3))."</B></TD>
  </TR>
  <TR bgcolor=\"".$bgColor[$index]."\" valign=\"top\">\n");
      }
      else {
        $rowspan = 1;
        $hauteur = 80;
      }
      //Coloration des jours feries
      if (in_array(date("j-m",$leJour),$tabJourFerie)) {
        $bgColorLigne = $CalJourFerie;
      } else {
        $bgColorLigne = $bgColor[$index];
      }
?>
  <TR bgcolor="<?php echo $bgColorLigne; ?>" valign="top">
    <TD width="70" height="80" rowspan="<?php echo $rowspan; ?>" align="center" valign="middle"><B><?php echo $tabJour[date("w",$leJour)]."<BR>".date("d/m",mktime(0,0,0,$moisEnCours,$premierJourSemaine+$i,$anneeEnCours)); ?></B></TD>
<?php echo $ligneAnniv; ?>
    <TD height="<?php echo $hauteur; ?>"><?php echo $ligneNote; ?></TD>
  </TR>
<?php
    }
  }
?>
  </TABLE>
<!-- FIN PLANNING HEBDOMADAIRE -->
<?php
}
//---------------------------------------------------------
//                     PLANNING MENSUEL
//---------------------------------------------------------
elseif ($vu==_MENU_PLG_MENSUEL) {
  $tabJourFerie = getListeJourFerie($anneeEnCours);

  //Si l'utilisateur a choisi une couleur de note on l'ajoute dans la clause WHERE de la recherche
  $whereCouleur = "";
  $FILTRE_COULEUR = str_replace("!","#",urldecode($extra));
  if ($FILTRE_COULEUR != "ALL" && !empty($FILTRE_COULEUR)) {
    $whereCouleur = ($FILTRE_COULEUR == $AgendaFondNotePerso) ? " AND (age_couleur='".$FILTRE_COULEUR."' OR age_couleur='')" : " AND age_couleur='".$FILTRE_COULEUR."'";
  }

  function afficheJour($leJour, $nbJour, $mPrec) {
    global $DB_CX, $PREFIX_TABLE, $USER_SUBSTITUE, $idUser, $AgendaFondNotePerso, $AgendaFondNote, $moisEnCours, $anneeEnCours, $PlanningNotePrivee;
    global $FORMAT_NOM_CONTACT, $whereCouleur, $tabJourFerie, $CalJourEvenement;
    global $tzGmt, $tzDateEte, $tzHeureEte, $tzDateHiver, $tzHeureHiver, $formatHeure;
    // On regarde si le jour a afficher appartient au mois courant
    if ($mPrec==0) {
      $tsJour = mktime(0,0,0,$moisEnCours, $nbJour, $anneeEnCours);
      //Coloration des jours feries
      if (in_array(date("j-m",$tsJour),$tabJourFerie)) {
        $classCel = "mensFerie";
      } else {
        $classCel = "mensNote";
      }
      $styleLien = "<B>".$nbJour."</B>";
    } else {
      $classCel = "mensPrec";
      $styleLien = "<FONT color=\"#98B1C8\">".$nbJour."</FONT>";
      $tsJour = mktime(0,0,0,$moisEnCours+$mPrec, $nbJour, $anneeEnCours);
    }
    $ligneAnniv = $plageNote = "";
    // Evenement(s) du jour (affiche(s) comme des notes globales)
    $DB_CX->DbQuery("SELECT DISTINCT eve_libelle, eve_couleur FROM ${PREFIX_TABLE}evenement WHERE DATE_FORMAT(eve_date_debut,'%Y%m%d')<='".date("Ymd",$tsJour)."' AND DATE_FORMAT(eve_date_fin,'%Y%m%d')>='".date("Ymd",$tsJour)."'".(($USER_SUBSTITUE==$idUser) ? " AND (eve_util_id=".$idUser." OR eve_partage='O')" : " AND eve_partage='O'"));
    while ($enr = $DB_CX->DbNextRow()) {
      // Couleur de l'evenement
      if (empty($enr['eve_couleur']))
        $enr['eve_couleur'] = $CalJourEvenement;
      //Stockage des infos relatives a l'evenement
      $plageNote .= "<DIV style=\"padding:1px;background-color:".$enr['eve_couleur'].";\">".trad("COMMUN_EVENEMENT")."&rsaquo;".$enr['eve_libelle']."</DIV>";
    }
    // Anniversaire(s) du calepin (y compris les contacts partages)
    $DB_CX->DbQuery("SELECT CONCAT(".$FORMAT_NOM_CONTACT.") AS nomContact FROM ${PREFIX_TABLE}calepin WHERE (cal_util_id=".$USER_SUBSTITUE." OR cal_partage='O') AND cal_date_naissance LIKE '%".substr($leJour,4)."' AND DATE_FORMAT(cal_date_naissance,'%Y%m%d')<=".date("Ymd",$tsJour));
    while ($enr = $DB_CX->DbNextRow())
      $ligneAnniv .= $enr['nomContact']."/";
    // Recalcul des bascules ete/hiver en tenant compte de l'annee affichee
    $tzEte = calculBasculeDST($tzDateEte,date("Y",$tsJour),$tzHeureEte,$tzGmt,0);
    $tzHiver = calculBasculeDST($tzDateHiver,date("Y",$tsJour),$tzHeureHiver,$tzGmt,1);
    //Preparation au decalage horaire
    list($age_date,$age_dateAvant,$age_heure_debut,$age_heure_fin) = prepareDecalageH($tzGmt,$tzEte,$tzHiver,$tsJour);
    $DB_CX->DbQuery("SELECT age_aty_id,age_heure_debut,age_heure_fin,age_libelle,age_prive,age_couleur,age_util_id,age_lieu,age_date,age_date_creation,age_date_modif FROM ${PREFIX_TABLE}agenda, ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}utilisateur WHERE age_id=aco_age_id AND aco_util_id=".$USER_SUBSTITUE." AND ((($age_date='".$leJour."' OR ($age_dateAvant='".$leJour."' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin!=0 AND age_aty_id=2))".$whereCouleur.") OR (age_date LIKE '%".substr($leJour,4)."' AND DATE_FORMAT(age_date,'%Y%m%d')<=".date("Ymd",$tsJour)." AND age_aty_id=1)) AND util_id=age_util_id ORDER BY age_aty_id DESC, age_date, age_heure_debut ASC");
    if ($DB_CX->DbNumRows()) {
      while ($enr = $DB_CX->DbNextRow()) {
        //Decalage des notes en fonction du fuseau horaire
        list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['dateCreation'],$enr['dateModif']) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,$leJour,$enr['age_date'],$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif']);
        if ($enr['age_aty_id']==1) { //Stockage des infos relatives aux anniversaires
          $ligneAnniv .= $enr['age_libelle']."/";
        } else { //Stockage des infos relatives aux notes
          //Propriete Privee ou Publique de la note
          if ($USER_SUBSTITUE!=$idUser && $enr['age_util_id']!=$idUser && $enr['age_prive']==1) {
            $enr['age_libelle'] = trad("COMMUN_OCCUPE");
            $enr['age_couleur'] = $PlanningNotePrivee; // Couleur de note non visible si note privee
            $enr['age_lieu'] = ""; // Emplacement non visible  si note privee
          }
          //Couleur de fond de la note si non definie dans la bdd
          if (empty($enr['age_couleur']))
            $enr['age_couleur'] = ($enr['age_util_id']==$USER_SUBSTITUE) ? $AgendaFondNotePerso : $AgendaFondNote;
          $plageNote .= "<DIV style=\"padding:1px;background-color:".$enr['age_couleur'].";\">".(($enr['age_aty_id']==2) ? afficheHeure(floor($enr['age_heure_debut']),$enr['age_heure_debut'],$formatHeure)."-".afficheHeure(floor($enr['age_heure_fin']),$enr['age_heure_fin'],$formatHeure) : trad("COMMUN_JOURNEE_ENTIERE"))."&rsaquo;".$enr['age_libelle'].((!empty($enr['age_lieu'])) ? " <I>(".$enr['age_lieu'].")</I>" : "")."</DIV>";
        }
      }
      if (!empty($ligneAnniv))
        $ligneAnniv = "<TD width=\"100%\">[".substr($ligneAnniv,0,strlen($ligneAnniv)-1)."]&nbsp;</TD>";
      echo "    <TD class=\"".$classCel."\"><TABLE cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" border=\"0\"><TR align=\"right\" valign=\"top\">".$ligneAnniv."<TD>".$styleLien."</TD></TR></TABLE>".$plageNote."</TD>\n";
    } elseif (!empty($ligneAnniv) || !empty($plageNote)) {
        $ligneAnniv = (!empty($ligneAnniv)) ? "[".substr($ligneAnniv,0,strlen($ligneAnniv)-1)."]" : "";
        echo "    <TD class=\"".$classCel."\"><TABLE cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" border=\"0\"><TR align=\"right\" valign=\"top\"><TD width=\"100%\">".$ligneAnniv."&nbsp;</TD><TD>".$styleLien."</TD></TR></TABLE>".$plageNote."</TD>";
    } else {
        echo "    <TD class=\"".$classCel."\" align=\"right\">".$styleLien."</TD>\n";
    }
  }
?>
<!-- PLANNING MENSUEL -->
  <H4 align="center"><?php echo sprintf(trad("IMPRESSION_AGENDA_DE"), prefixeMot(strtolower(substr($nomUtil,0,1)),trad("COMMUN_PREFIXE_D"),trad("COMMUN_PREFIXE_DE")).$nomUtil); ?><BR><?php echo $tabMois[date("n",$sd)]." ".date("Y",$sd); ?></H4>
  <TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
<?php
  echo "  <TR bgcolor=\"".$bgColor[1]."\">\n    <TD bgcolor=\"#FFFFFF\"></TD>\n";
  $nbJSelect=0;
  for ($i=1; $i<8; $i++) {
    ${"bt".$i} = substr($SEMAINE_TYPE,$i-1,1);
    if (${"bt".$i}==1)
      $nbJSelect++;
  }
  $celSize = floor(100/$nbJSelect);
  for ($i=1; $i<8; $i++)
    if (${"bt".$i}==1)
      echo "    <TD align=\"center\" width=\"".$celSize."%\" height=\"18\" class=\"mensVide\"><B>".$tabJour[$i]."</B></TD>\n";
  echo "  </TR>\n";

  $premierJour = date("w",mktime(0,0,0,$moisEnCours, 1, $anneeEnCours));
  if ($premierJour == 0)
    $premierJour = 7;

  echo "  <TR valign=\"top\" height=\"80\">\n    <TD valign=\"middle\" class=\"numWeek\">".date("W",mktime(0,0,0,$moisEnCours, 1, $anneeEnCours))."</TD>\n";
  $nbJour = 0;
  for ($i=1;$i<8;$i++) {
    if (${"bt".$i}!=1) {
      if ($i>=$premierJour)
        $nbJour++;
    } elseif ($i<$premierJour) {
      $tsJour = mktime(0,0,0,$moisEnCours, 1-$premierJour+$i, $anneeEnCours);
      afficheJour(date("Y-m-d",$tsJour), date("j",$tsJour), -1);
    } else {
      $leJour = (++$nbJour < 10) ? $anneeEnCours."-".$moisEnCours."-0".$nbJour : $anneeEnCours."-".$moisEnCours."-".$nbJour;
      afficheJour($leJour, $nbJour, 0);
    }
  }
  echo "  </TR>\n";
  $finDeMois = false;
  for ($j=1;!$finDeMois;$j++) {
    if (checkdate($moisEnCours, $nbJour+1, $anneeEnCours)) {
      echo "  <TR valign=\"top\" height=\"80\">\n    <TD valign=\"middle\" class=\"numWeek\">".date("W",mktime(0,0,0,$moisEnCours, $nbJour+1, $anneeEnCours))."</TD>\n";
      for($i=1;$i<8;$i++) {
        if (${"bt".$i}!=1)
          $nbJour++;
        elseif (checkdate($moisEnCours, ++$nbJour, $anneeEnCours)) {
          $leJour = ($nbJour < 10) ? $anneeEnCours."-".$moisEnCours."-0".$nbJour : $anneeEnCours."-".$moisEnCours."-".$nbJour;
          afficheJour($leJour, $nbJour, 0);
        }
        else {
          $finDeMois = true;
          $tsJour = mktime(0,0,0,$moisEnCours, $nbJour, $anneeEnCours);
          afficheJour(date("Y-m-d",$tsJour), date("j",$tsJour), 1);
        }
      }
      echo "  </TR>\n";
    } else {
      $finDeMois = true;
    }
  }
?>
  </TABLE>
<!-- FIN PLANNING MENSUEL -->
<?php
}
//---------------------------------------------------------
//                DISPONIBILITE HEBDOMADAIRE
//---------------------------------------------------------
elseif ($vu==_MENU_DISP_HEBDO) {
  $aVar = explode("|",$extra);
  $sChoix = $aVar[0];
  $zlPrec = $aVar[1];
  $iHeureMin = $aVar[2];
  $iHeureMax = $aVar[3];
  $moisEnCours = date("m",$aVar[4]);
  $premierJourSemaine = date("d",$aVar[4]);
  $anneeEnCours = date("Y",$aVar[4]);

  $sOutput = ("<!-- DISPONIBILITE HEBDOMADAIRE -->
  <H4 align=\"center\">".sprintf(trad("IMPRESSION_AGENDA_DE"), prefixeMot(strtolower(substr($nomUtil,0,1)),trad("COMMUN_PREFIXE_D"),trad("COMMUN_PREFIXE_DE")).$nomUtil)."<BR>".sprintf(trad("IMPRESSION_SEMAINE_DU"), date("W",$aVar[4]), $premierJourSemaine." ".strtolower($tabMois[$moisEnCours+0])." ".$anneeEnCours, date("d",$aVar[5])." ".strtolower($tabMois[date("n",$aVar[5])])." ".date("Y",$aVar[5]))."</H4>\n");
  $sOutput .= "  <P align=\"center\"><B><U>".trad("IMPRESSION_DISPO_TITRE")."</U> :</B><BR>";
  // Info sur les utilisateurs selectionnes
  $DB_CX->DbQuery("SELECT util_id, CONCAT(".$FORMAT_NOM_UTIL.") AS nomUtil, util_debut_journee, util_fin_journee, util_semaine_type FROM ${PREFIX_TABLE}utilisateur WHERE util_id IN (".$sChoix.") ORDER BY nomUtil");
  // Tableau contenant les id => horaires (case 1 et 2) et semaine type de l'utilisateur (case 3)
  $aUtil = array();
  //Compteur pour savoir combien d'utilisateur ont ete selectionnes
  $nbUtilSelectionne=0;
  while ($enr=$DB_CX->DbNextRow()) {
    $sOutput .= $enr['nomUtil']."<BR>";
    $aUtil[$enr['util_id']][0] = max($enr['util_debut_journee'],$iHeureMin);
    $aUtil[$enr['util_id']][1] = min($enr['util_fin_journee'],$iHeureMax);
    $aUtil[$enr['util_id']][2] = substr($enr['util_semaine_type'],6).substr($enr['util_semaine_type'],0,6); // Semaine type mappee au format PHP (L->D => D->S)
    $nbUtilSelectionne++;
  }
  $sOutput .= "</P>\n";
  // Calcul de la duree de la journee pour le nb de colonnes
  $iDureeJournee = ($iHeureMax-$iHeureMin)*$zlPrec;
  //Tableau des disponibilites 0->libre 1->Occupe
  $aJournee = array();
  for ($i=0;$i<$iDureeJournee;$i++) {
    //Initialisation du tableau de la journee a 0 (libre)
    $aJournee[$i][0]=0;
  }

  // Parcours des jours de la semaine
  $sOutput .= "  <TABLE border=\"0\" cellspacing=\"1\" cellpadding=\"4\" align=\"center\" bgcolor=\"#000000\" style=\"border-collapse:separate;\">\n";
  $index = 0;
  for ($j=0;$j<7;$j++) {
    $index=1-$index;
    $leJour = mktime(0,0,0,$moisEnCours,$premierJourSemaine+$j,$anneeEnCours);
    $sOutput .= "  <TR align=\"center\" valign=\"middle\" bgcolor=\"".$bgColor[$index]."\">\n";
    $sOutput .= "    <TD class=\"mois\"><B>&nbsp;".$tabJour[date("w",$leJour)]."&nbsp;<BR>".date("d/m/y",mktime(0,0,0,$moisEnCours,$premierJourSemaine+$j,$anneeEnCours))."</B></TD>\n";
    $sOutput .= "    <TD class=\"libre\">";

    // On commence par positionner les indisponibilites des utilisateurs en fonction de leur horaires et de leur semaine type
    while (list($sUtilID,$aInfoUtil)=each($aUtil)) {
      // Semaine type
      if (substr($aInfoUtil[2],date("w",$leJour),1)=="0") {
      // Journee hors profil semaine type => indisponibilite toute la journee
        for ($i=0;$i<$iDureeJournee;$i++) {
          $aJournee[$i][0] = 1; // On specifie que la plage horaire est occupee
        }
      } else {
        // Debut de journee
        for ($i=$iHeureMin;$i<$aInfoUtil[0];$i+=(1/$zlPrec)) {
          $aJournee[($i-$iHeureMin)*$zlPrec][0] = 1; // On specifie que la plage horaire est occupee
        }
        // Fin de journee
        for ($i=$aInfoUtil[1];$i<$iHeureMax;$i+=(1/$zlPrec)) {
          $aJournee[($i-$iHeureMin)*$zlPrec][0] = 1; // On specifie que la plage horaire est occupee
        }
      }
    }
    // On se repositionne au debut du tableau
    reset($aUtil);
    // Recalcul des bascules ete/hiver en tenant compte de l'annee affichee
    $tzEte = calculBasculeDST($tzDateEte,date("Y",$leJour),$tzHeureEte,$tzGmt,0);
    $tzHiver = calculBasculeDST($tzDateHiver,date("Y",$leJour),$tzHeureHiver,$tzGmt,1);
    //Preparation au decalage horaire
    list($age_date,$age_dateAvant,$age_heure_debut,$age_heure_fin) = prepareDecalageH($tzGmt,$tzEte,$tzHiver,$leJour);
    // Recuperation des horaires des notes dans la table agenda_concerne
    $sql  = "SELECT age_heure_debut, age_heure_fin, age_aty_id, age_date, age_date_creation, age_date_modif";
    $sql .= " FROM ${PREFIX_TABLE}agenda_concerne, ${PREFIX_TABLE}agenda";
    $sql .= " WHERE aco_util_id IN (".$sChoix.")";
    $sql .= "  AND age_id=aco_age_id";
    $sql .= "  AND ($age_date='".date("Y-m-d",$leJour)."' OR ($age_dateAvant='".date("Y-m-d",$leJour)."' AND $age_heure_debut>=$age_heure_fin AND $age_heure_fin!=0 AND age_aty_id=2))";
    $sql .= "  AND age_disponibilite=0";
    $sql .= "  AND ($age_heure_debut=".$iHeureMin;
    $sql .= "  OR ($age_heure_debut<".$iHeureMin." AND $age_heure_fin>".$iHeureMin.")";
    $sql .= "  OR ($age_heure_debut>".$iHeureMin." AND $age_heure_debut<".$iHeureMax."))";
    $sql .= " ORDER BY age_date, age_heure_debut ASC, age_heure_fin DESC";
    $DB_CX->DbQuery($sql);
    // Remplissage du tableau de la journee a 1 (occupe)
    while ($enr=$DB_CX->DbNextRow()) {
      //Decalage des notes en fonction du fuseau horaire
      list($enr['age_heure_debut'],$enr['age_heure_fin'],$enr['dateCreation'],$enr['dateModif']) = decaleNote($tzGmt,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver,date("Y-m-d",$leJour),$enr['age_date'],$enr['age_heure_debut'],$enr['age_heure_fin'],$enr['age_date_creation'],$enr['age_date_modif']);
      // Ajustement des heures de debut et de fin si hors profil ou pour les notes couvrant toute une journee
      if ($enr['age_aty_id']==3) {
        $enr['age_heure_debut']=$iHeureMin;
        $enr['age_heure_fin']=$iHeureMax;
      } else {
        $enr['age_heure_debut']=max($enr['age_heure_debut'],$iHeureMin);
        $enr['age_heure_fin']=min($enr['age_heure_fin'],$iHeureMax);
      }
      for ($i=$enr['age_heure_debut'];$i<$enr['age_heure_fin'];$i+=0.25)
        $aJournee[($i-$iHeureMin)*$zlPrec][0] = 1;
    }
    // Flag pour savoir si on a trouve une plage libre
    $plageLibre = false;
    for ($i=0;$i<$iDureeJournee;$i++) {
      $hFin = colSpan($i+1,$aJournee[$i][0],$nbUtilSelectionne,true);
      if ($aJournee[$i][0]==0) {
        $sOutput .= sprintf(trad("IMPRESSION_DISPO_ENTRE"), afficheHeure(($i/$zlPrec)+$iHeureMin,($i/$zlPrec)+$iHeureMin,$formatHeure), afficheHeure(($hFin/$zlPrec)+$iHeureMin,($hFin/$zlPrec)+$iHeureMin,$formatHeure))."<BR>";
        $plageLibre = true;
      }
      // Reinitialisation du tableau pour le jour suivant
      $aJournee[$i][0]=0;
      // On avance l'indice de la boucle
      $i = $hFin-1;
    }
    if (!$plageLibre) {
      $sOutput .= trad("IMPRESSION_DISPO_AUCUNE");
    }
    $sOutput .= "</TD>\n  </TR>\n";
  }
  echo $sOutput."  </TABLE>\n<DIV class=\"timezone\" style=\"text-align:center;\">".sprintf(trad("COMMUN_FUSEAU_ACTUEL"), (($tzGmt<0) ? "-" : "+").afficheHeure(floor(abs($tzGmt)),abs($tzGmt)), $tzLibelle)."</DIV>\n<!-- FIN DISPONIBILITE HEBDOMADAIRE -->\n";
}
//---------------------------------------------------------
//                         CALEPIN
//---------------------------------------------------------
elseif ($vu==_MENU_CONTACT) {
?>
<!-- CALEPIN -->
  <H4 align="center"><?php echo sprintf(trad("IMPRESSION_CALEPIN_DE"), prefixeMot(strtolower(substr($nomUtil,0,1)),trad("COMMUN_PREFIXE_D"),trad("COMMUN_PREFIXE_DE")).$nomUtil); ?></H4>
  <TABLE cellspacing="0" cellpadding="0" width="100%" border="0">
<?php
  $strOutput = "";
  $index = 0;
  if ($extra!="ALL") { // Affichage du resultat d'une recherche
    $DB_CX->DbQuery(stripslashes($extra));
    while ($enr = $DB_CX->DbNextRow()) {
      $index = 1 - $index;
      $strOutput .= "  <TR bgcolor=\"".$bgColor[$index]."\" valign=\"top\">\n";
      $strOutput .= "    <TD width=\"2\" rowspan=\"3\" style=\"border-top:solid 1px #000000;border-left:solid 1px #000000;border-bottom:solid 1px #000000;\"><IMG src=\"image/trans.gif\" width=\"2\" height=\"1\" alt=\"\" style=\"border:0px\"></TD>\n";
      $strOutput .= "    <TD width=\"100%\" colspan=3 style=\"border-top:solid 1px #000000;\"><I>".$enr['cal_societe']."</I>&nbsp;&nbsp;</TD>\n";
      $strOutput .= "    <TD width=\"2\" rowspan=\"3\" style=\"border-top:solid 1px #000000;border-right:solid 1px #000000;border-bottom:solid 1px #000000;\"><IMG src=\"image/trans.gif\" width=\"2\" height=\"1\" alt=\"\" style=\"border:0px\"></TD>\n";
      $strOutput .= "  </TR>\n";
      $strOutput .= "  <TR bgcolor=\"".$bgColor[$index]."\" valign=\"top\">\n";
      $strOutput .= "    <TD width=\"40%\">";
      if (!empty($enr['cal_nom']) || !empty($enr['cal_prenom']))  // Nom et Prenom
        $strOutput .= "<B>".trim($enr['cal_nom']." ".$enr['cal_prenom'])."</B>&nbsp;&nbsp;<BR>";
      if (!empty($enr['cal_adresse']))   // Adresse
        $strOutput .= nlTObr($enr['cal_adresse'],"&nbsp;&nbsp;")."&nbsp;&nbsp;<BR>";
      if (!empty($enr['cal_cp']) || !empty($enr['cal_ville']))  // Code postal et Ville
        $strOutput .= "<BR>".trim($enr['cal_cp']." ".$enr['cal_ville'])."&nbsp;&nbsp;";
      if (!empty($enr['cal_pays']))   // Pays
        $strOutput .= "<BR>".$enr['cal_pays']."&nbsp;&nbsp;";
      if (!empty($enr['cal_date_naissance']) && $enr['cal_date_naissance']!="0000-00-00") { // Age
        $tabDate = explode("-",$enr['cal_date_naissance']);
        $age = calculAge($tabDate,$sd);
        $pluriel = ($age>1) ? trad("COMMUN_PLURIEL") : "";
        $strOutput .= "<BR><BR>".(($age>0) ? sprintf(trad("COMMUN_AGE"),$age,$pluriel,$tabDate[0],$tabDate[1],$tabDate[2]) : sprintf(trad("COMMUN_DATE_NAISSANCE"),$tabDate[0],$tabDate[1],$tabDate[2]))."&nbsp;&nbsp;";
      }
      $strOutput .= "</TD>\n";
      $strOutput .= "    <TD width=\"36%\">";
      $col1 = $col2 = "";
      if (!empty($enr['cal_domicile'])) {   // Telephone domicile
        $col1 .= trad("IMPRESSION_CALEPIN_DOMICILE")." :&nbsp;<BR>";
        $col2 .= telephoneVF($enr['cal_domicile'])."<BR>";
      }
      if (!empty($enr['cal_travail'])) {   // Telephone professionnel
        $col1 .= trad("IMPRESSION_CALEPIN_TRAVAIL")." :&nbsp;<BR>";
        $col2 .= telephoneVF($enr['cal_travail'])."<BR>";
      }
      if (!empty($enr['cal_portable'])) {  // Portable
        $col1 .= trad("IMPRESSION_CALEPIN_PORTABLE")." :&nbsp;<BR>";
        $col2 .= telephoneVF($enr['cal_portable'])."<BR>";
      }
      if (!empty($enr['cal_fax'])) {  // Fax
        $col1 .= trad("IMPRESSION_CALEPIN_FAX")." :&nbsp;<BR>";
        $col2 .= telephoneVF($enr['cal_fax'])."<BR>";
      }
      if (!empty($enr['cal_email'])) {  // Adresse Email
        $col1 .= trad("IMPRESSION_CALEPIN_EMAIL")." :&nbsp;<BR>";
        $col2 .= $enr['cal_email']."<BR>";
      }
      if (!empty($enr['cal_emailpro'])) {  // Adresse Email Professionnelle
        $col1 .= trad("IMPRESSION_CALEPIN_EMAIL_PRO")." :&nbsp;";
        $col2 .= $enr['cal_emailpro'];
      }
      $strOutput .= (!empty($col1)) ? "<TABLE cellspacing=\"0\" cellpadding=\"0\" width=\"0%\" border=\"0\"><TR><TD nowrap>".$col1."</TD><TD>".$col2."</TD></TR></TABLE>" : "&nbsp;";
      $strOutput .= "</TD>\n";
      $strOutput .= "    <TD width=\"24%\">";
      $col1 = $col2 = "";
      if (!empty($enr['cal_icq'])) {  // ICQ
        $col1 .= trad("IMPRESSION_CALEPIN_ICQ")." :&nbsp;<BR>";
        $col2 .= $enr['cal_icq']."<BR>";
      }
      if (!empty($enr['cal_aim'])) {  // AIM
        $col1 .= trad("IMPRESSION_CALEPIN_AIM")." :&nbsp;<BR>";
        $col2 .= $enr['cal_aim']."<BR>";
      }
      if (!empty($enr['cal_msn'])) {  // MSN
        $col1 .= trad("IMPRESSION_CALEPIN_MSN")." :&nbsp;<BR>";
        $col2 .= $enr['cal_msn']."<BR>";
      }
      if (!empty($enr['cal_yahoo'])) {  // YAHOO
        $col1 .= trad("IMPRESSION_CALEPIN_YAHOO")." :&nbsp;";
        $col2 .= $enr['cal_yahoo'];
      }
      $strOutput .= (!empty($col1)) ? "<TABLE cellspacing=\"0\" cellpadding=\"0\" width=\"0%\" border=\"0\"><TR><TD nowrap>".$col1."</TD><TD>".$col2."</TD></TR></TABLE>" : "&nbsp;";
      $strOutput .= "</TD>\n";
      $strOutput .= "  </TR>\n";
      $strOutput .= "  <TR bgcolor=\"".$bgColor[$index]."\" valign=\"top\">\n";
      $strOutput .= "    <TD width=\"100%\" colspan=\"3\" style=\"border-bottom:solid 1px #000000;\">";
      $strOutput .= (!empty($enr['cal_note'])) ? "&nbsp;<BR><U>".trad("IMPRESSION_CALEPIN_COMMENTAIRE")."</U> :<BR>".nlTObr($enr['cal_note'])."<BR>&nbsp;" : "&nbsp;";
      $strOutput .= "</TD>\n";
      $strOutput .= "  </TR>\n";
    }
  } else { // Affichage de l'integralite des contacts au format porte-cartes format A4 - Portrait - 80 lignes - 2 colones
    $DB_CX->DbQuery("SELECT ${PREFIX_TABLE}calepin.*, cgr_id, cgr_nom FROM ${PREFIX_TABLE}calepin, ${PREFIX_TABLE}calepin_appartient, ${PREFIX_TABLE}calepin_groupe WHERE (cal_util_id=".$idUser." AND cap_cal_id=cal_id AND cgr_id=cap_cgr_id) OR (cal_util_id!=".$idUser." AND cal_partage='O' AND cap_cal_id=cal_id AND cgr_id=cap_cgr_id) ORDER BY cal_nom ASC, cal_prenom ASC, cal_societe ASC");
    $lettreCrt = "";              // Lettre de l'alphabet en cours de traitement
    $nbCol   = 0;                 // Flag (0,1) pour savoir dans quelle colonne on se situe (gauche ou droite)
    $newCol  = false;             // Flag pour indiquer si on doit changer de colonne ou de page
    $nbLigneTitre = 3;            // Correspond a l'espace occupe par le titre sur la premiere page (Calepin de xxx)
    $nbLigneLettre = 3;           // Correspond a l'espace occupe par l'affichage de la lettre courante
    $nbLigneCol = $nbLigneTitre;  // Stocke le nombre de ligne de la colonne courante (initialise a la valeur du titre de la page)
    $nbLigneParColonne = 80;      // Nombre de ligne maxi pour une colonne
    //$nbPage  = 1;                 // Numero de la page
    $strOutput = ("  <TR valign=\"top\">\n    <TD>");
    while ($enr = $DB_CX->DbNextRow()) {
      // On commence par regarder si on a un changement de lettre
      $nomCtt = trim($enr['cal_nom']." ".$enr['cal_prenom']);
      if ($lettreCrt!=substr($nomCtt,0,1)) {
        $lettreCrt = substr($nomCtt,0,1);
        $blocLettre = "<TABLE style=\"margin-bottom:6px;\"><TR><TD class=\"lettre\">".$lettreCrt."</TD></TR></TABLE>";
        $nbLigneContact = $nbLigneLettre;
        $newLettre = true;
      } else {
        $nbLigneContact = 0;
        $newLettre = false;
      }
      // Stockage des informations du contact
      $blocContact = "<TABLE cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" border=\"0\">\n";
      if (!empty($nomCtt)) { // Nom et Prenom
        $nbLigneContact++;
        $blocContact .= "      <TR><TD class=\"nomCtt\">".$nomCtt."</TD></TR>\n";
      }
      if (!empty($enr['cal_societe'])) {  // Societe
        $nbLigneContact++;
        $blocContact .= "      <TR><TD>".trad("IMPRESSION_CALEPIN_SOCIETE")." : <I>".$enr['cal_societe']."</I></TD></TR>\n";
      }
      if (!empty($enr['cal_adresse'])) { // Adresse
        $enr['cal_adresse'] = nlToBR($enr['cal_adresse']);
        // Compter le nombre de <br> dans la chaine
        $tabLigne = explode("<BR>",$enr['cal_adresse']);
        $nbLigneContact += count($tabLigne);
        $blocContact .= "      <TR><TD>".$enr['cal_adresse']."</TD></TR>\n";
      }
      if (!empty($enr['cal_cp']) || !empty($enr['cal_ville']) || !empty($enr['cal_pays'])) { // Code postal Ville et (Pays)
        $nbLigneContact++;
        $blocContact .= "      <TR><TD>".trim($enr['cal_cp']." ".$enr['cal_ville'].((!empty($enr['cal_pays'])) ? " <I>(".$enr['cal_pays'].")</I>" : ""))."</TD></TR>\n";
      }
      if (!empty($enr['cal_domicile']))  { // Telephone domicile
        $nbLigneContact++;
        $blocContact .= "      <TR><TD>".trad("IMPRESSION_CALEPIN_DOMICILE")." : ".telephoneVF($enr['cal_domicile'])."</TD></TR>\n";
      }
      if (!empty($enr['cal_travail'])) {  // Telephone professionnel
        $nbLigneContact++;
        $blocContact .= "      <TR><TD>".trad("IMPRESSION_CALEPIN_TRAVAIL")." : ".telephoneVF($enr['cal_travail'])."</TD></TR>\n";
      }
      if (!empty($enr['cal_portable'])) { // Portable
        $nbLigneContact++;
        $blocContact .= "      <TR><TD>".trad("IMPRESSION_CALEPIN_PORTABLE")." : ".telephoneVF($enr['cal_portable'])."</TD></TR>\n";
      }
      if (!empty($enr['cal_fax'])) { // Fax
        $nbLigneContact++;
        $blocContact .= "      <TR><TD>".trad("IMPRESSION_CALEPIN_FAX")." : ".telephoneVF($enr['cal_fax'])."</TD></TR>\n";
      }
      if (!empty($enr['cal_email'])) { // Adresse Email
        $nbLigneContact++;
        $blocContact .= "      <TR><TD>".trad("IMPRESSION_CALEPIN_EMAIL")." : ".$enr['cal_email']."</TD></TR>\n";
      }
      if (!empty($enr['cal_emailpro'])) { // Adresse Email Professionnelle
        $nbLigneContact++;
        $blocContact .= "      <TR><TD>".trad("IMPRESSION_CALEPIN_EMAIL_PRO")." : ".$enr['cal_emailpro']."</TD></TR>\n";
      }
      $nbLigneContact++;
      $blocContact .= "      <TR><TD>&nbsp;</TD></TR></TABLE>";

      // Changement de colonne ou saut de page avec reprise de l'entete toutes les $nbLigneParColonne lignes (format A4 - Portrait)
      if (($nbLigneCol+$nbLigneContact) > $nbLigneParColonne) {
        $newCol = true;
        $nbCol = 1 - $nbCol;
        if (!$nbCol && !$newLettre) { // Si on change de page ($nbCol = 0 => colonne de gauche) on reaffiche la lettre courante
          $newLettre = true;
          $nbLigneContact += $nbLigneLettre;
        }
      }

      // Changement de colonne (ou de page) eventuel
      if ($newCol) {
        $strOutput .= "</TD>\n";
        if (!$nbCol) { // On revient a la colonne de gauche => saut de page
          /*$br = "";
          $nbLigneMax = max($nbLigneCol, $nbLigneCol1);
          for ($i=$nbLigneMax;$i<($nbLigneParColonne-1);$i++)
            $br .= "<BR>";
          $strOutput .= ("  </TR>
  <TR><TD colspan=3 align=\"center\">".$br."Page ".($nbPage++)."</TD></TR>
  <TR valign=\"top\" style=\"page-break-before: always\">\n");*/
          $strOutput .= ("  </TR>
  <TR valign=\"top\" style=\"page-break-before: always\">\n");
        } else { // Colonne de separation entre les 2 colonnes de donnees
          $strOutput .= "    <TD width=\"20\" nowrap>&nbsp;</TD>\n";
          //$nbLigneCol1 = $nbLigneCol;
        }
        $strOutput .= "    <TD>";
        $nbLigneCol = 0 + $nbLigneTitre;
        $nbLigneTitre = 0; // On n'a plus besoin de s'occuper du titre sur les autres pages
      }

      $newCol = false;
      $nbLigneCol += $nbLigneContact;
      $strOutput .= (($newLettre) ? $blocLettre : "").$blocContact;
    }
    $strOutput .= ("</TD>
  </TR>\n");
  }
  echo $strOutput;
?>
  </TABLE>
<!-- FIN CALEPIN -->
<?php
}
  // Fermeture BDD
  $DB_CX->DbDeconnect();
?>
  <TABLE border="0" cellspacing="0" cellpadding="0" align="center"><TR><TD style="color:#C0C0C0; font-size:9px;"><?php echo sprintf(trad("IMPRESSION_VERSION_PHENIX_LIEN"), $APPLI_VERSION); ?></TD></TR></TABLE>
</BODY>
</HTML>
