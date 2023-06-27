<?php


// === Paramètres d'affichage du Tableau ===
$largeurTableau="90%";                 // Largeur du tableau
$couleurBordureTableau="#000000";      // Couleur de la bordure du tableau
$couleurFondEnteteTableau="#FFEF39";   // Couleur de fond de l'entête du tableau
$policeEnteteTableau="Verdana";        // Police de caractères utilisée dans l'entête du tableau
$couleurPoliceEnteteTableau="#000000"; // Couleur de la police de caractères utilisée dans l'entête du tableau
$couleurFondInt1Tableau="#EBFFFD";     // Couleur1 de fond des lignes d'affichage des intitulés de messages (en alternance)
$couleurFondInt2Tableau="#FFFBEB";     // Couleur2 de fond des lignes d'affichage des intitulés de messages (en alternance)
$policeIntTableau="Arial";             // Police de caractères utilisées pour l'affichage des intitulés de messages
$couleurPoliceIntTableau="#000000";    // Couleur de la police de caractères utilisée pour l'affichage des intitulés de messages

$NombreMsgParPage=30;              // Nombre maximum de messages à afficher par page
$NombreMaxPages=1;                // Nombre maximum de pages de messages susceptibles d'être affichées

// === Mise en valeur des n derniers messages postés ===

$nombreNouveauxMessagesSignales=5;           // Nombre de messages récents à signaler, en mettant leur date en surbrillance (la valeur 0 est possible)
$couleurNouveauxMessagesSignales="#000080";  // Couleur de la date des messages récents à signaler


// ###########################################################################
// *****************************************************************************
// Création du fichier "index.dat" s'il n'existe pas encore
// *****************************************************************************
$repforum="../data/forum/".$_SESSION["membre"];

if (!file_exists("${repforum}/index.dat")) {
  $crfic=fopen("${repforum}/index.dat","w+");
  fputs($crfic,"Fichier Index. Ne pas éditer !");
  fclose($crfic);
}

// **************************************************************************
// Lecture du fichier "index.dat" et stockage des données (identifiant,
// niveau, date, nom et sujet) dans le tableau "$index"
// **************************************************************************

$tabindex=file("${repforum}/index.dat");
$nombremsgs=count($tabindex)-1;

for($compt=1;$compt<=$nombremsgs;$compt++) {
  $index[$compt][1]=strtok($tabindex[$compt],"#"); // identifiant du message
  $index[$compt][2]=strtok("#");                   // niveau du message
  $chainetemp=strtok("#");                         // chaine date+nom+sujet
  $index[$compt][3]=strtok($chainetemp,"|");       // date
  $index[$compt][4]=strtok("|");                   // nom de l'auteur
  $index[$compt][5]=strtok("|");                   // sujet
}

// ###########################################################################
// =======================================================================
// Définition de fonctions utiles pour l'affichage des résultats dans un tableau
// =======================================================================
// =======================================================================
// Définition de la fonction couleuralt, qui alterne les couleurs
// d'affichage des lignes du tableau
// =======================================================================

function couleuralt() {
  global $couleurFondInt1Tableau;
  global $couleurFondInt2Tableau;
  static $numligne;
  if(!isset($numligne)) $numligne=0;
  if ($numligne%2=="1") {
    $numligne=$numligne+1;
    return($couleurFondInt1Tableau);
  }
  else {
    $numligne=$numligne+1;
    return($couleurFondInt2Tableau);
  }
}

// =======================================================================
// Définition de la fonction "tabulation", utilisée pour matérialiser
// la hierarchie du forum
// =======================================================================

function tabulation($n=1) {
  $espacevide=(30*($n-1)+40);
  return($espacevide);
}


// **************************************************************************
// Cas de figure où aucun message n'a  encore été posté dans le forum de discussion
// **************************************************************************

if($nombremsgs<1) {
  print("<center> \n");
  print("<font face=\"$policeEnteteTableau\" size=\"-1\"> \n");
  print LANGFORUM2." <br> \n";
  print LANGFORUM3." <a href=\"forumposter.php\"><b>".LANGFORUM3bis."</b></a> ".LANGFORUM3ter.". \n";
  print("</font> \n");
  print("</center> \n");
}

// **************************************************************************
// Cas de figure où des messages ont été postés dans le forum de discussion :
// Affichage des intitulés des messages dans un tableau (utilisant les
// paramètres précisés plus haut)
// **************************************************************************

else {

  // ===================================================================
  // Détermination de l'identifiant du dernier message posté

  // création du tableau $tabidents, destiné à reccueillir les différentes
  // valeurs des identifiants des messages

  for($compt=1;$compt<=$nombremsgs;$compt++) {
    $tabidents[$compt]=intval($index[$compt][1]);
  }

  // Tri du tableau dans l'ordre inverse des valeurs
  rsort($tabidents);

  // Détermination des valeurs minimum et maximum des identifiants à signaler
  @ $limMaxDerMessa=$tabidents[0];

  if($nombremsgs<=$nombreNouveauxMessagesSignales) {
    @ $limMinDerMessa=$tabidents[$nombremsgs-1];
  }
  else {
    $limMinDerMessa=$tabidents[$nombreNouveauxMessagesSignales-1];
  }

  if($nombremsgs==1) {  // Cas de figure où un seul message a été posté
    $limMaxDerMessa=1;
    $limMinDerMessa=1;
  }

  // suppression du tableau $tabidents
  unset($tabidents);

  // **************************************************************************
  // Prise en compte de la valeur $p, sensée indiquer le numéro de la page
  // à afficher - Détermination des rangs min. et max. des messages à afficher
  // dans la page
  // **************************************************************************

  if(@ !$p) $p=1;
  $rangPMax=$nombremsgs-(($p-1)*$NombreMsgParPage);
  $rangPMin=max($nombremsgs-($p*$NombreMsgParPage)+1,1);

  // **************************************************************************
  // Note : l'option "chrono" des versions précédentes de FouleTexte n'est
  // désormais plus disponible : les messages sont maintenant automatiquement
  // affichés des plus récents aux plus anciens
  // **************************************************************************

  print("<center> \n");
  print("<font face=\"$policeEnteteTableau\" size=\"-1\"> \n");
  print("&gt;&nbsp;<a href=\"forumposter.php\">".LANGFORUM4."</a>&nbsp;&lt;<br> \n");    print("</font> \n");
  print("</center> \n");
  print("<br> \n");

  print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"$largeurTableau\" align=\"center\" bgcolor=\"$couleurBordureTableau\"> \n");
  print("<tr><td> \n");

    print("<table border=\"0\" width=\"100%\" cellspacing=\"1\" align=\"center\" cellpadding=\"5\"> \n");

    for($rangC=$rangPMax;$rangC>=$rangPMin;$rangC--) {    //  *** Définition du premier curseur ($rangC), progressant par incrémentation négative ***
      if($index[$rangC][2]==1) {                   // Cas de figure où le rang du message rencontré vaut 1

        print("<tr> \n");
        print("<td bgcolor=\"".couleuralt()."\"> \n");
        // insertion d'un tableau à une ligne et deux colonnes
        // destiné à matérialiser la hierarche du forum
          print("<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"> \n");
          print("<tr> \n");
          print("<td width=\"".tabulation($index[$rangC][2]-1)."\"></td> \n");
          print("<td> \n");
          print("<img src='../image/on1.gif' width='8' height='8' align=center > \n");
          print("<a href=\"lire.php?msg=".$index[$rangC][1]."\">".stripslashes(htmlentities(strip_tags($index[$rangC][5])))."</a> - ");
          if(($nombreNouveauxMessagesSignales>0) and ($index[$rangC][1]>=$limMinDerMessa) and ($index[$rangC][1]<=$limMaxDerMessa)) {
            print("".stripslashes(htmlentities(strip_tags($index[$rangC][4])))." (".$index[$rangC][3].")  \n");
          }
          else {
            print("".stripslashes(htmlentities(strip_tags($index[$rangC][4])))." (".$index[$rangC][3].")  \n");
          }
          print("</td> \n");
          print("</tr> \n");
          print("</table> \n");
        print("</td> \n");
        print("</tr> \n");

        $rangP=$rangC+1;                 //  *** Définition du second curseur ($rangP), progressant par incrémentation positive ***
        while(@ $index[$rangP][2]>1) {     //  Cas de figure où le rang du message rencontré est supérieur à 1
          print("<tr> \n");
          print("<td bgcolor=\"".couleuralt()."\"> \n");
          // insertion d'un tableau à une ligne et deux colonnes
          // destiné à matérialiser la hierarche du forum
            print("<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"> \n");
            print("<tr> \n");
            print("<td width=\"".tabulation($index[$rangP][2]-1)."\"></td> \n");
            print("<td> \n");
            print("<font face=\"$policeIntTableau\" color=\"$couleurPoliceIntTableau\">&gt;</font> \n");
            print("<a href=\"lire.php?msg=".$index[$rangP][1]."\">".stripslashes(htmlentities(strip_tags($index[$rangP][5])))."</a> - ");
            if(($nombreNouveauxMessagesSignales>0) and ($index[$rangP][1]>=$limMinDerMessa) and ($index[$rangP][1]<=$limMaxDerMessa)) {
              print("".stripslashes(htmlentities(strip_tags($index[$rangP][4])))." (".$index[$rangP][3].")   \n");
            }
            else {
              print("".stripslashes(htmlentities(strip_tags($index[$rangP][4])))." (".$index[$rangP][3].")   \n");
            }
            print("</td> \n");
            print("</tr> \n");
            print("</table> \n");
          print("</td> \n");
          print("</tr> \n");

          $rangP++;                    // Incrémentation de $rangP
        }

      }
    }

    print("</table> \n");

  print("</td></tr> \n");
  print("</table>");

  print("<br> \n");



}


// ###########################################################################
?>
