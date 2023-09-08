<?php
header('Content-Type: text/html; charset=ISO-8859-15');
// Récupèration des variables passées dans l'url via AJAX
//$ztAction = $_GET['ztAction'];
$ztFrom = "note";
$aff_mensuel = 0;
$opt = $_GET['opt'];
$opt2 = $_GET['opt2'];
$opt3 = $_GET['opt3'];  // MOVE ou COPY
if ($opt2!="") list($sid,$idAge,$duree,$idUser,$USER_SUBSTITUE,$nbJSelect,$anneeEnCours,$moisEnCours,$debutSemaine,$finSemaine,$premierJourSemaine,$bt1,$bt2,$bt3,$bt4,$bt5,$bt6,$bt7,$pos,$AFFECTE_NOTE,$page,$APPLI_LANGUE,$tzGmt,$tzEte,$tzHiver,$droit_NOTES,$formatHeure,$tzLibelle,$SEMAINE_TYPE,$jourEnCours,$localTime,$tcMenu,$indexJourCrt,$sd,$NOTE_BARREE,$tzDateEte,$tzHeureEte,$tzDateHiver,$tzHeureHiver) = explode(";",$opt2);
if (($page == "mensuel") && ($opt!="")) $ztDate = $opt; 
if (($page != "mensuel") && ($opt!="")) {
  list($a,$b,$date_timestamp,$zlHeureDebut) = explode(";",$opt);
  $ztDate = date("Y-m-d",$date_timestamp);
  $ztDateNote = date("d-m-Y",$date_timestamp);
  $zlHeureDebut=str_replace(",",".",$zlHeureDebut);
  $duree=str_replace(",",".",$duree); 
  $zlHeureFin = $zlHeureDebut + $duree;  
} 
if ($opt3 == "cal") $page = "cal";
  include("inc/param.inc.php");
  //include("inc/conf.inc.php");  
  if (isset($sid)) {
    include("inc/fonctions.inc.php");
    $start_time=get_moment();
  } else {
    Header("location: deconnexion.php?msg=5");
    exit;
  }
  include("lang/$APPLI_LANGUE.php");

$tabJourFerie = getListeJourFerie($anneeEnCours);
list($cel_ligne) = explode(";",$opt); // si $cel_ligne = -1 c'est une note qui prend toute la journée, il ne faut pas toucher aux horaires
  
// On recharge la fonction d'affichage du D&D
function affDD($note_type) {
  $dd_ico = "";
  $dd_ico = "&nbsp;<IMG style=\"cursor:pointer;\" src=\"image/move.gif\" width=\"12\" height=\"12\" border=\"0\" align=\"absmiddle\" title=\"".trad("COMMUN_DD_DEPLACE_NOTE")."\">";
  return $dd_ico;
}

if ($page != "cal") {
// Mise à jour de la note après déplacement ou copie
if (($page!="mensuel") && ($cel_ligne != -1)) {
    $tabDate = explode("-",$ztDateNote);
    $ztDateForm = $tabDate[2]."-".$tabDate[1]."-".$tabDate[0];
    // Conversion en utc en fonction du timezone
    $decalageHoraire = calculDecalageH($tzGmt,$tzEte,$tzHiver,mktime(12,0,0,$tabDate[1],$tabDate[0],$tabDate[2]));
    if ($decalageHoraire!=0) {
      // Calcule heure de debut en utc
      $tzNote = mktime(floor($zlHeureDebut)-floor($decalageHoraire), ($zlHeureDebut*60)%60-($decalageHoraire*60)%60, 0, $tabDate[1], $tabDate[0], $tabDate[2]);
      $ztDateNoteD = date("d/m/Y",$tzNote);
      $zlHeureDebut = date("H",$tzNote).".".date("i",$tzNote)*100/60;
      // Calcule heure de fin en utc
      $tzNoteF = mktime(floor($zlHeureFin)-floor($decalageHoraire), ($zlHeureFin*60)%60-($decalageHoraire*60)%60, 0, $tabDate[1], $tabDate[0], $tabDate[2]);
      $ztDateNoteF = date("d/m/Y",$tzNoteF);
      $zlHeureFin = date("H",$tzNoteF).".".date("i",$tzNoteF)*100/60;
      if ($zlHeureFin == "00.00") $zlHeureFin = "24.00";
      $tabDate = explode("/",$ztDateNoteD);
    }
    $tzDst = calculDecalageH($tzGmt,$tzEte,$tzHiver,mktime(12,0,0,$tabDate[1],$tabDate[0],$tabDate[2])) - $tzGmt;	
    $ztDate = $tabDate[2]."-".$tabDate[1]."-".$tabDate[0];
} 

    $DB_CX->DbQuery("SELECT * FROM ${PREFIX_TABLE}agenda WHERE age_id=".$idAge);
    $enr = $DB_CX->DbNextRow();
    $date_note = $enr[age_date];
if ($page=="quot") $ztDate = $date_note;	
    $age_mere_id = $enr[age_mere_id];
    list($a_note,$m_note,$j_note) = explode("-",$date_note);
    $a_note = substr($a_note,2,2);
    $date_cur = $a_note." ".$m_note." ".$j_note;
    $date_cur = mktime(0, 0, 0, $m_note, $j_note, $a_note);
    list($a_new_note,$m_new_note,$j_new_note) = explode("-",$ztDate);
    $a_new_note = substr($a_new_note,2,2);
    $date_new = mktime(0, 0, 0, $m_new_note, $j_new_note, $a_new_note);
    $date_dif = floor(($date_new - $date_cur) / 86400);

if (($page=="mensuel") || ($cel_ligne == -1)) {	
 // On gère le décalage des jours si la note est enregistré le jour précédent en GMT0 (dans la bdd)
 list($tabDate[2],$tabDate[1],$tabDate[0]) = explode("-",$ztDate);
 $decalageHoraire = calculDecalageH($tzGmt,$tzEte,$tzHiver,mktime(12,0,0,$tabDate[1],$tabDate[0],$tabDate[2]));
 if (($enr[age_heure_debut]+$decalageHoraire) >= 24) {
   list($tmp_a,$tmp_m,$tmp_j) = explode("-",$ztDate); 
   $ztDate = date("Y-m-d", (mktime(0, 0, 0, $tmp_m, $tmp_j, $tmp_a)-1440));
 }
}
if ($opt3=="move") {
    $sql = "UPDATE ${PREFIX_TABLE}agenda ";
    $sql .= "SET age_date='".$ztDate."',";
	$sql .= " age_modificateur_id='".$idUser."',";
	$sql .= " age_date_modif='".gmdate("Y-m-d H:i:s", time())."'";
if (($page!="mensuel") && ($cel_ligne != -1)) {	
    $sql .= ", age_heure_debut=".$zlHeureDebut.",";
    $sql .= " age_heure_fin=".$zlHeureFin;
}	
    $sql .= " WHERE age_id=".$idAge;
    $DB_CX->DbQuery($sql);
}

if ($opt3=="copy") {
    $enr['age_libelle'] = addslashes($enr['age_libelle']);
    $enr['age_lieu'] = addslashes($enr['age_lieu']);
    $enr['age_detail'] = addslashes($enr['age_detail']);
    $sql = "INSERT INTO ${PREFIX_TABLE}agenda ";
    $sql .= "(age_util_id,age_aty_id,age_date,age_heure_debut,age_heure_fin,age_plage,age_plage_duree,age_libelle,age_detail,age_lieu,age_rappel,age_rappel_coeff,age_email,age_prive,age_couleur,age_createur_id,age_date_creation,age_cal_id,age_date_modif,age_modificateur_id)";
if (($page!="mensuel") && ($cel_ligne != -1))
    $sql .= " VALUES ('".$enr[age_util_id]."','".$enr[age_aty_id]."','".$ztDate."','".$zlHeureDebut."','".$zlHeureFin."','".$enr[age_plage]."','".$enr[age_plage_duree]."','".$enr[age_libelle]."','".$enr[age_detail]."','".$enr[age_lieu]."','".$enr[age_rappel]."','".$enr[age_rappel_coeff]."','".$enr[age_email]."','".$enr[age_prive]."','".$enr[age_couleur]."','".$idUser."','".gmdate("Y-m-d H:i:s", time())."','".$enr['age_cal_id']."','".gmdate("Y-m-d H:i:s", time())."','".$idUser."')";
else 	
    $sql .= " VALUES ('".$enr[age_util_id]."','".$enr[age_aty_id]."','".$ztDate."','".$enr[age_heure_debut]."','".$enr[age_heure_fin]."','".$enr[age_plage]."','".$enr[age_plage_duree]."','".$enr[age_libelle]."','".$enr[age_detail]."','".$enr[age_lieu]."','".$enr[age_rappel]."','".$enr[age_rappel_coeff]."','".$enr[age_email]."','".$enr[age_prive]."','".$enr[age_couleur]."','".$idUser."','".gmdate("Y-m-d H:i:s", time())."','".$enr['age_cal_id']."','".gmdate("Y-m-d H:i:s", time())."','".$idUser."')";

    $DB_CX->DbQuery($sql);
    $idAge = $DB_CX->DbInsertID();
    $idAge_init = $enr[age_id];
	$requete = mysql_query("SELECT * FROM ${PREFIX_TABLE}agenda_concerne WHERE aco_age_id=".$idAge_init);
	while($enr = mysql_fetch_array($requete)) {
      // Enregistrement des personnes concernees
      $DB_CX->DbQuery("INSERT INTO ${PREFIX_TABLE}agenda_concerne (aco_age_id,aco_util_id) VALUES (".$idAge.",".$enr[aco_util_id].")");
    }
}

if ($opt3=="move") {
    if ($age_mere_id != 0) $idAgeMere = $age_mere_id;
    else $idAgeMere = $idAge;
    $i=0;
    $DB_CX->DbQuery("SELECT age_id,age_date FROM ${PREFIX_TABLE}agenda WHERE (age_mere_id=".$idAgeMere." OR age_id=".$idAgeMere.") AND age_id NOT LIKE ".$idAge);
     while ($enr = $DB_CX->DbNextRow())
      {
        $idAge_en_cours = $enr[0];
        $date_note = $enr[1];
        list($a_note,$m_note,$j_note) = explode("-",$date_note);
        $a_note = substr($a_note,2,2);
        $date_cur = $a_note." ".$m_note." ".$j_note;
        $date_cur = mktime(0, 0, 0, $m_note, $j_note, $a_note);
        $date_new = $date_cur + 86400*$date_dif;
        $date_new = date("Y-m-d",$date_new);
        //echo "Date rec en cours : ".$date_new;
        $mod_date[$i]['id'] = $idAge_en_cours;
        $mod_date[$i]['date'] = $date_new;
        //echo $mod_date[$i]['id']." : ".$mod_date[$i]['date']."   ";
        $i=$i+1;
      }

for ($j=0;$j<$i;$j=$j+1)
  {
   $date_new = $mod_date[$j]['date'];
   $idAge_en_cours = $mod_date[$j]['id'];
   $sql = "UPDATE ${PREFIX_TABLE}agenda ";
   $sql .= "SET age_date='".$date_new."'";
if (($page!="mensuel") && ($cel_ligne != -1)) {   
   $sql .= ", age_heure_debut=".$zlHeureDebut.",";
   $sql .= " age_heure_fin=".$zlHeureFin;
}   
   $sql .= " WHERE age_id=".$idAge_en_cours;
   $DB_CX->DbQuery($sql);
  }
}
} // Fin de la maj de la note (affichage quot,hebdo ou mensuel)
 
// On récupère quelques variables de l'utilisateur perdues au passage
  include("inc/html.inc.php");
  $DB_CX->DbQuery("SELECT util_interface, util_format_nom FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$idUser);
  $APPLI_STYLE = $DB_CX->DbResult(0,0);
  if ($DB_CX->DbResult(0,1) == 0) {
    $FORMAT_NOM_UTIL = "util_nom, ' ', util_prenom"; 
    $NOM_UTIL_CREATEUR = "t1.util_nom, ' ', t1.util_prenom";
	$NOM_UTIL_MODIFICATEUR = "t2.util_nom, ' ', t2.util_prenom";
	$FORMAT_NOM_CONTACT = "cal_nom, ' ', cal_prenom";
  }	
  else {
    $FORMAT_NOM_UTIL = "util_prenom, ' ', util_nom";
    $NOM_UTIL_CREATEUR = "t1.util_prenom, ' ', t1.util_nom";
	$NOM_UTIL_MODIFICATEUR = "t2.util_prenom, ' ', t2.util_nom";
	$FORMAT_NOM_CONTACT = "cal_prenom, ' ', cal_nom";
  }
  include("skins/$APPLI_STYLE.php");
  include("lang/$APPLI_LANGUE.php");  
  
// definition de la variable d'appel
$callByDDUpdate=true;

if ( file_exists('agenda_meteo.php')) include('agenda_meteo.php');
if ( file_exists('agenda_horoscope.php')) include('agenda_horoscope.php');

if ($page=="hebdo") {  
// ************************************************************
// Copier/coller de agenda_hebdo.php sauf la partie bandeau choix des jour de la semaine et <div id="tableau">
// ************************************************************
  include("agenda_hebdomadaire.php");
}

/****************************************************************************************************************/
if ($page=="quot") {  
/****************************************************************************************************************/
  include("agenda_quotidien.php");
}

/****************************************************************************************************************/
if ($page=="mensuel") {  
/****************************************************************************************************************/
  include("agenda_mensuel.php");
}

/****************************************************************************************************************/
if ($page=="cal") {  
/****************************************************************************************************************/
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

  $moisAvant = ($moisEnCours != 1) ? ($moisEnCours-1)."','".$anneeEnCours : "12','".($anneeEnCours-1);
  $moisApres = ($moisEnCours != 12) ? ($moisEnCours+1)."','".$anneeEnCours : "1','".($anneeEnCours+1);
  $anneeAvant = $moisEnCours."','".($anneeEnCours-1);
  $anneeApres = $moisEnCours."','".($anneeEnCours+1);

  $premierJour = date("w",mktime(12,0,0,$moisEnCours, 1, $anneeEnCours));

  $tabJourFerie = getListeJourFerie($anneeEnCours);

  if ($premierJour == 0)
    $premierJour = 7;

  // Le calendrier journalier affiche les jours de la semaine type d'une couleur differente sauf si la semaine type est vide (0000000) ou complete (1111111) dans ces cas on affiche les week-end d'une couleur differente
  $SEMAINE_CALENDRIER = ($SEMAINE_TYPE!="1111111" && $SEMAINE_TYPE!="0000000") ? $SEMAINE_TYPE : "1111100";

    // Recuperation des evenements personnalises a notifier dans le calendrier (sert aussi pour le planning mensuel global)
    $DB_CX->DbQuery("SELECT DISTINCT eve_date_debut, TO_DAYS(eve_date_fin)-TO_DAYS(eve_date_debut) AS duree, TO_DAYS(eve_date_debut)-TO_DAYS('$anneeEnCours-$moisEnCours-01') AS decalage, eve_couleur FROM ${PREFIX_TABLE}evenement WHERE (eve_date_debut LIKE '$anneeEnCours-$moisEnCours-%' OR (eve_date_debut<'$anneeEnCours-$moisEnCours-01' AND eve_date_fin>='$anneeEnCours-$moisEnCours-01'))".(($USER_SUBSTITUE==$idUser) ? " AND (eve_util_id=".$idUser." OR eve_partage='O')" : " AND eve_partage='O'"));
    $tabEvenementDate = array();

    // Initialisation du tableau des couleurs des jours a vide
    $nbJourMois = date("t",$sd_new);
    for ($i=1;$i<$nbJourMois;$i++) {
      $tabEvenementDate[$i] = "";
    }
    while ($enr = $DB_CX->DbNextRow()) {
      $dureeEvt = $enr['duree'];
      list($aEvt,$mEvt,$jEvt) = explode ("-",$enr['eve_date_debut']);
      if ($enr['decalage']<0) { // La date de debut est anterieure au mois courant donc il faut regulariser
        $jEvt=1;
        $dureeEvt = $dureeEvt+$enr['decalage']; // On additionne car $enr['decalage'] est negatif
      }
      if ($dureeEvt > ($nbJourMois-$jEvt)) { // La date de fin est posterieure au mois courant, donc il faut regulariser
        $dureeEvt = $nbJourMois-$jEvt;
      }
      if (empty($enr['eve_couleur']))
        $enr['eve_couleur'] = $CalJourEvenement;
      for ($i=0;$i<=$dureeEvt;$i++) {
        $tabEvenementDate[intval($jEvt+$i)] = $enr['eve_couleur'];
      }
    }  
  
  include("agenda_calendrier_jours.php"); 
?>

<!-- FIN MODULE CALENDRIER -->

<?php
} // Fin update Calendrier de gauche
?>
