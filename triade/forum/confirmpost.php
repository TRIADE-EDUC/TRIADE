<?php
session_start();
error_reporting(0);

/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
// **************************************************************************
// Configuration de paramètres d'affichage des messages d'avertissement
// ou de confirmation :
// Modifiez les paramètres ci-dessous en n'oubliant pas de refermer
// les guillemets et le point virgule
// **************************************************************************

$policeText="Verdana";         // police de caractères utilisée pour l'affichage des messages de confirmation
$couleurPoliceText="#000000";  // couleur de la police de caractères

// #####################################################################
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="../librairie_js/acces.js"></script>
<script language="JavaScript" src="../librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="../librairie_js/function.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body  id='bodyforum'  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("../librairie_php/lib_licence_forum.php"); ?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="100%">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGFORUM15 ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign='top'>
<!-- // fin  -->


<?php

if ($_SESSION["membre"] == "menueleve") { if (ACCESFORUMELEVE == "non") { exit; } }
if ($_SESSION["membre"] == "menuprof") {  if (ACCESFORUMPROF == "non") { exit; } }
if ($_SESSION["membre"] == "menuparent") { if (ACCESFORUMPARENT == "non") { exit; } }

// #############################################################################
// *****************************************************************************
// Création du fichier "index.dat" s'il n'existe pas encore
// *****************************************************************************
$repforum="../data/forum/".$_SESSION["membre"];
if (!file_exists("${repforum}/index.dat")) {
  $crfic=fopen("${repforum}/index.dat","w+");
  fputs($crfic,"Fichier Index. Ne pas éditer !");
  fclose($crfic);
}

// *****************************************************************************
// Lecture du fichier index.dat et stockage des données
// dans le tableau "$index"
// *****************************************************************************

$ficindex=file("${repforum}/index.dat");
$nombremsgs=count($ficindex)-1;

for($compt=1;$compt<=$nombremsgs;$compt++) {
  $index[$compt][1]=strtok($ficindex[$compt],"#");  // identifiant du fichier
  $index[$compt][2]=strtok("#"); // niveau du fichier
  $index[$compt][3]=strtok("#"); // chaîne date+auteur+sujet
}
// #############################################################################
?>

<?php
// #############################################################################

// *********************************************************************
// Test : cas de figure où le message ne comprend pas de sujet,
// ou ne contient pas de texte
// *********************************************************************

$nom=$_POST["nom"];
$sujet=$_POST["sujet"];
$adel=$_POST["adel"];
$texte=$_POST["texte"];
$refer=$_POST["refer"];

if ((!$nom) and (!$adel) and (!$sujet) and (!$texte)) {
  print("<center> \n");
  print("<font face=\"$policeText\" color=\"$couleurPoliceText\" size=\"-1\"> \n");
  print LANGFORUM16. " <a href=\"post.php\">".LANGFORUM16bis."</a>.<br> \n";
  print("</font> \n");
  print("</center> \n");
}

elseif (!$texte) {
  print("<center> \n");
  print("<font face=\"$policeText\" color=\"$couleurPoliceText\" size=\"-1\"> \n");
  print LANGFORUM17." \n";
  print("</font> \n");
  print("</center> \n");
}

elseif (!$nom) {
  print("<center> \n");
  print("<font face=\"$policeText\" color=\"$couleurPoliceText\" size=\"-1\"> \n");
  print LANGFORUM18."\n";
  print("</font> \n");
  print("</center> \n");
}

// *********************************************************************
// Enregistrement du message
// *********************************************************************

else {

  // *********************************************************************
  // Détermination de l'identifiant du nouveau message (stocké dans
  // la variable "$IDnouvM"
  // *********************************************************************

  // création du tableau $tabidents, destiné à reccueillir les différentes
  // valeurs des identifiants des messages

  for($compt=1;$compt<=$nombremsgs;$compt++) {
    $tabidents[$compt]=intval($index[$compt][1]);
  }

  if(!isset($tabidents)) {
   $IDnouvM=1;
  }
  else {
    $IDnouvM=max($tabidents)+1;   // Identifiant du nouveau message
   }

  // suppression du tableau $tabidents

  unset($tabidents);

  // *********************************************************************
  // Détermination du rang et du niveau du nouveau message
  // en fonction de la valeur "$refer".
  // *********************************************************************
  // Cette opération s'effectue par l'intermédiaire de la fonction
  // determin($valeur) qui accepte en entrée la valeur $refer.
  // La fonction analyse le fichier index.dat, détermine le rang et
  // le niveau du message précédent ($rangMP,$niveauMP) et en déduit
  // les rangs et valeurs du nouveau message ($rangNM,$niveauNM).
  // Ces valeurs sont retournées sous la forme d'un tableau.
  // =====================================================================

  // ============================================
  // Définition de la fonction determin($valeur)
  // ============================================

  function determin($valeur) {

    global $index;
    global $nombremsgs;

    if($valeur) {
    // ========== cas de figure où $valeur a été défini ==========

      // recherche du rang et de du niveau du message précédent
      $compt=1;
      while($index[$compt][1]!=$valeur) {
        $compt++;
      }

      // le rang et le niveau du message précédent
      // sont stockés dans les valeurs $rangMP et $niveauMP

      $rangMP=$compt;
      $niveauMP=$index[$compt][2];

      // Détermination du rang du nouveau message

      $compt=$compt+1;
      while(@ $index[$compt][2]>$niveauMP) {
        $compt++;
      }

      // le rang et le niveau du nouveau message
      // sont stockés dans les valeurs $rangNM et $niveauMP

      $rangNM=$compt-1;
      $niveauNM=$niveauMP+1;

    }

    else {
   // ======= cas de figure où $valeur n'a pas été défini (nouveau message =======

      $rangNM=$nombremsgs;
      $niveauNM=1;
    }

  // === La fonction retourne un tableau à deux éléments (det[1] et det[2]), ===
  // ===      contenant respectivement les valeurs $rangNM et $niveauNM      ===

    $det[1]=$rangNM;
    $det[2]=$niveauNM;

    return($det);

  }

  // =============================================================
  // Application de la fonction et récupération du rang et du
  // niveau du nouveau message
  // =============================================================

  if(!isset($refer)) $refer="";

  $res=determin($refer);
  $RANGnouvM=$res[1];        // Rang du nouveau message
  $NIVEAUnouvM=$res[2];      // Niveau du nouveau message

  // ***************************************************************
  // définition d'éléments à intégrer dans les fichiers msgxx.dat
  //  et index.dat : date, champs vides, chaîne date+nom+sujet
  // ***************************************************************

  // ==== Définition de la date de rédaction du message ====

  //$tdate=getdate();
  //$jour=sprintf("%02.2d",$tdate["mday"])."/".sprintf("%02.2d",$tdate["mon"])."/".$tdate["year"];
  //$heure=sprintf("%02.2d",$tdate["hours"])."H".sprintf("%02.2d",$tdate["minutes"]);

  // ==== Définition de la date avec le Timezone ====
  include_once("../common/config2.inc.php");
  include_once("../librairie_php/timezone.php");
  $date=dateDMY().", ".dateHIS();

  // ===== Remplacement des champs vides par des ======
  // ===== informations du type "Pas de sujet"   ======

  if(!$refer) {
    $refer="n";
  }
  if(!$nom) {
    $nom="Pas de nom";
  }
  if ((!$adel) or (!ereg("^[^ ,;]+@[^ ,;]+[.][^ ,;]+$",$adel))) {
    $adel="noemail";
  }
  if (!$sujet) {
    $sujet="Pas de sujet";
  }

  // ***************************************************************
  // Nettoyage des variables $nom,$adel,$sujet et $texte, par
  // élimination des caractères /, | et #
  // ***************************************************************
  // -------------------------------------------------------------------
  // définition de la fonction stripSpeCar, destinée à éliminer
  // les caractères | et # susceptibles d'être postés par l'utilisateur
  // -------------------------------------------------------------------

  function stripSpeCar($chaine) {
    $chaine=str_replace("#","",$chaine);
    $chaine=str_replace("|","",$chaine);
  return($chaine);
  }

  // ===== Suppression des caractères # et | et des tags html =====

  $nom=trim(stripslashes(stripSpeCar($nom)));
  $adel=trim(stripslashes(stripSpeCar($adel)));
  $sujet=trim(stripslashes(stripSpeCar($sujet)));
  $texte=trim(stripslashes(stripSpeCar($texte)));

  // *********************************************************************
  // Création du nouveau fichier "msgxx.dat"
  // *********************************************************************
  // et écriture des données suivantes : identifiant message référant,
  // date, nom, adresse électronique, sujet, texte
  // *********************************************************************


  // ===== Définition du nom du fichier =====

  $nomfichier="${repforum}/msg".($IDnouvM).".dat";  //

  // ========== Ecriture du fichier ==========

  $fic=fopen($nomfichier,"w+");

  if(!$fic) {
    print("<center> \n");
    print("<font face=\"$policeText\" color=\"$couleurPoliceText\"> \n");
    print LANGFORUM19."\n";
    print("</font> \n");
    print("</center> \n");
  }

  else {
    fputs($fic,$refer."\n");  // identifiant du message référant - "N" par défaut (pour Nouveau)
    fputs($fic,$date."\n");   // date
    fputs($fic,$nom."\n");    // nom de l'auteur
    fputs($fic,$adel."\n");   // adresse électronique de l'auteur
    fputs($fic,$sujet."\n");  // sujet du message
    fputs($fic,$texte."\n");  // message proprement dit, entrecoupé d'éventuels
                                            // retours à la ligne
    fclose($fic);
  }

  // *********************************************************************
  // Mise à jour du fichier index.dat
  // Reproduction du fichier d'origine avec insertion
  // des informations relatives au nouveau message
  // *********************************************************************

  $find=fopen("${repforum}/index.dat","w+");

  if (!$find) {
    print("<font face=\"$policeText\" color=\"$couleurPoliceText\"> \n");
    print LANGFORUM20."\n";
    print LANGFORUM21."\n";
    print("</font> \n");
  }

  // ====== début de la reproduction du fichier d'origine ======

  else {
    fputs($find,"Fichier Index. Ne pas éditer !\n");

    // ====== recopie "brute" des premières lignes du fichier ======

    for($compt=1;$compt<=$RANGnouvM;$compt++) {
      fputs($find,$ficindex[$compt]);
    }

    // ====== insertion des informations relatives au nouveau ======
    // ====== message : identifiant ($idnm), niveau ($niveau) ======

    fputs($find,$IDnouvM."#"); // identifiant du message
    fputs($find,$NIVEAUnouvM."#"); // niveau du message
    fputs($find,$date."|".$nom."|".$sujet."|"."#"."\n"); // chaîne date|nom|sujet


    // ====== recopie "brute" des dernières lignes du fichier ======

    for($compt=$RANGnouvM+1;$compt<=$nombremsgs;$compt++) {
      fputs($find,$ficindex[$compt]);
    }

    // ====== fin de la reproduction du fichier d'origine ======

    fclose($find);

    // ====== Affichage d'un message de confirmation ======


    print("<center> \n");
    print "<BR>".LANGFORUM22."<br><br> \n";
    print("<center><a href=\"forum.php\">".LANGFORUM23."</a></center><br> \n");
    print("</center> \n");

  }
}
// #####################################################################
?>
</td></tr></table>
</BODY></HTML>
