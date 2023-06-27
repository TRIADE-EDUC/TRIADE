<html>
<head>
<title>Interface d'administration du forum Triade</title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="../librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="../librairie_js/function.js"></script>
</head>
<body bgcolor="#ffffff">

<?php


// #############################################################################
// =============================================================================
// FouleTexte 1.5 - (c) 2000 Thierry Arsicaud (deltascripts@ifrance.com)
// =============================================================================
//
//
// *****************************************************************************
// Création du fichier "index.dat" s'il n'existe pas encore
// *****************************************************************************

global $repForum;

if (isset($_GET["repforum"])) {
	$repForum=$_GET["repforum"];
}
if (isset($_POST["repforum"])) {
	$repForum=$_POST["repforum"];
}

if ( ! file_exists("../data/forum") ) {
	@mkdir("../data/forum",0755);
	$text="<Files \"*\">\n";
	$text.="Order Deny,Allow\n";
	$text.="Deny from all\n";
	$text.="</Files>";
	$fp = fopen("../data/forum/.htaccess", "w");
	fwrite($fp,$text);
	fclose($fp);
}

$reperForum="../data/forum/$repForum";

if ( ! file_exists($reperForum) ) {
	@mkdir("$reperForum",0755);
	$text="<Files \"*\">\n";
	$text.="Order Deny,Allow\n";
	$text.="Deny from all\n";
	$text.="</Files>";
	$fp = fopen("${reperForum}/.htaccess", "w");
	fwrite($fp,$text);
	fclose($fp);
}


if (!file_exists("${reperForum}/index.dat")) {
  $crfic=fopen("${reperForum}/index.dat","w+");
  fputs($crfic,"Fichier Index. Ne pas éditer !");
  fclose($crfic);
}

if (isset($_POST["mdputil"])) { $mdputil=$_POST["mdputil"]; }
if (isset($_POST["idaction"])) {$idaction=$_POST["idaction"]; }
if (isset($_POST["pass"])) { $pass=$_POST["pass"]; }
if (isset($_POST["idmsgsup"])) { $idmsgsup=$_POST["idmsgsup"]; }
if (isset($_POST["rangsupmin"])) { $rangsupmin=$_POST["rangsupmin"]; }
if (isset($_POST["rangsupmax"])) { $rangsupmax=$_POST["rangsupmax"]; }


if(!isset($_POST["idaction"])) $idaction="";
if(!isset($_POST["pass"])) $pass="";


// #############################################################################
// *****************************************************************************
// Définition de diverses fonctions, utilisées par la suite dans le script
// *****************************************************************************
// =============================================================================
// Définition de la fonction ROT13, utilisée pour le codage/décodage du mot de passe
// =============================================================================

 function ROT13($chaine) {
  $chaine=strtolower($chaine);
  $chainecod="";
  $longueurchaine=strlen($chaine);
  for ($compt=0;$compt<$longueurchaine;$compt++) {
    $caract1=substr($chaine,$compt,1);
    $codecaract1=ord($caract1);
    if (($codecaract1>=97) and ($codecaract1<=122)) {
      if ($codecaract1<=109) {
        $codecaract2=$codecaract1+13;
      }
      else {
        $codecaract2=$codecaract1-13;
      }
    }
    else {
      $codecaract2=$codecaract1;
    }
    $caract2=chr($codecaract2);
    $chainecod=$chainecod.$caract2;
  }
  return($chainecod);
}

// =============================================================================
// Définition de la fonction "tabulation", utilisée pour matérialiser
// la hierarchie du forum
// =============================================================================

function tabulation($n=1) {
  $espacevide=(30*($n-1)+40);
  return($espacevide);
}

// =============================================================================
// Définition de la fonction ImprimFormInterrogMDP() qui imprime le formulaire
// correspondant à la demande "Veuillez entrer le mot de passe"
// =============================================================================

function ImprimFormInterrogMDP() {
  global $repForum;
  print("<center> \n");
  print("<form method=\"POST\" action=\"admin.php\"> \n");
  print("Mot de passe :<br> \n");
  print("<input type=\"password\" name=\"mdputil\" size=\"30\"><input type=\"hidden\" name=\"repforum\" value=\"$repForum\"><br>");
  print("<input type=\"hidden\" name=\"idaction\" value=\"verifMDP\"> \n");
  print("<br> \n");
  print("<input type=\"submit\" value=\"Envoyer\" name=\"A1\">");
  print("</form>");
  print("</center>");
}

// =============================================================================
// Définition de la fonction ImprimFormChoixMDP() qui imprime le formulaire correspondant
// à la demande "Veuillez choisir votre mot de passe"
// =============================================================================

function ImprimFormChoixMDP() {
  global $pass;  // en cas de demande de changement de mot de passe
  global $repForum;
  print("<center> \n");
  print("<form method=\"POST\" action=\"admin.php\"> \n");
  print("Choix du mot de passe : <br> \n");
  print("<input type=\"password\" name=\"mdputil1\" size=\"10\"><br> \n");
  print("Veuillez le ressaisir pour confirmation : <br> \n");
  print("<input type=\"password\" name=\"mdputil2\" size=\"10\"><br> \n");
  print("<input type=\"hidden\" name=\"pass\" value=\"$pass\"><input type=\"hidden\" name=\"repforum\" value=\"$repForum\"> \n");
  print("<input type=\"hidden\" name=\"idaction\" value=\"testChoixMDP\"> \n");
  print("<br><input type=\"submit\" value=\"Envoyer\" name=\"A1\">");
  print("</form>");
  print("</center>");
}
// #############################################################################
?>

<?php
// #############################################################################
// *****************************************************************************
// Récupération du mot de passe (si un mot de passe a déjà été choisi
// par l'administrateur du forum de discussion)
// *****************************************************************************

if(file_exists("../common/mdep.php")) {
  include("../common/mdep.php");
  // Note : le mot de passe est stocké dans la valeur "$MDP"
}
// #############################################################################
?>

<?php
// #############################################################################
// *****************************************************************************
// *****************************************************************************
// Modules de choix et de véfification du mot de passe
// *****************************************************************************
// *****************************************************************************

// *****************************************************************************
// MODULE idaction=""
// cas de figure où IDaction n'est pas renseigné (premier lancement du script) :
// Vérifie si un mot de passe a déjà été choisi
// - si non : imprime le formulaire de choix de mot de passe
// - si oui : imprime le formulaire d'interrogation de mot de passe
// *****************************************************************************

if ($idaction=="") {

  if(!file_exists("../common/mdep.php")) {
  // aucun mot de passe n'a été choisi
    print("<center> \n");
    print("<br> \n");
    print("<font size=\"+1\"><b>Bienvenue sur l'interface d'administration<br>du forum de discussion</b></font> \n");
    print("<br><br> \n");
    print("Veuillez choisir le mot de passe administrateur. <br><br> \n");
    print("Ce mot de passe vous sera demandé chaque fois<br>que vous souhaiterez accéder à cette interface, notamment<br> \n");
    print("si vous décidez de supprimer certains messages postés par les utilisateurs. <br><br> \n");
    print("</center> \n");

    ImprimFormChoixMDP();
  }

  else {
  // un mot de passe a déjà été choisi et enregistré
    print("<center> \n");
    print("<br> \n");
    print("<font size=\"+1\"><b>Bienvenue sur l'interface d'administration<br>du forum de discussion</b></font> \n");
    print("<br><br> \n");
    print("Veuillez vous identifier SVP. <br> \n");
    print("</center> \n");
    print("<br> \n");
    ImprimFormInterrogMDP();
  }
}

// *****************************************************************************
// Module idaction="testChoixMDP" :
// Vérifie si les deux valeurs "Mot de Passe"  entrées par l'utilisateur sont
// équivalentes
// - si non : envoie un message d'avertissement et imprime à nouveau
// le formulaire de choix du mot de passe
// - si oui : enregistre le mot de passe dans le fichier mdep.php et crée
// la variable "$pass" nécessaire pour accéder au menu des actions possibles
// *****************************************************************************

if ($idaction=="testChoixMDP") {

   // conversion des deux valeurs en minuscule
   $mdputil1=strtolower($mdputil1);
   $mdputil2=strtolower($mdputil2);

  if(file_exists("../common/mdep.php") and $pass!=ROT13($MDP)) {
    // protection contre des appels du script destinés à "faire sauter"
    // le mot de passe
    print("Erreur : Veuillez vous <a href=\"admin.php\">identifier</a> à nouveau.");
    exit;
  }

  if($mdputil1=="") {
    // cas de figure où l'utilisateur a validé le formulaire précédent
    // sans entrer de valeurs
    print("<br> \n");
    print("<center> \n");
    print("Veillez recommencer l'opération SVP.");
    print("</center> \n");
    ImprimFormChoixMDP();
    exit;
  }

  if ($mdputil1!=$mdputil2) {
  // cas de figure où les deux valeurs entrées par l'utilisateur ne coincident pas
    print("<center> \n");
    print("<br> \n");
    print("Les deux valeurs que vous avez entrées ne coincident pas. Veuiller recommencer SVP. <br> \n");
    print("</center>");
    ImprimFormChoixMDP();
  }

  else {
  // cas de figure où les deux valeurs entrées par l'utilisateur coincident

    // --- Enregistrement du mot de passe dans le fichier mdep.php
    $ficmdep=fopen("../common/mdep.php","w+");
    fputs($ficmdep,"<?php \n");
    fputs($ficmdep,"\$MDP=\"$mdputil1\"; \n");
    fputs($ficmdep,"?>");
    fclose($ficmdep);

    // Définition de la valeur "$pass", qui sera nécessaire pour les
    // opérations de configuration ou de suppression de messages
    // Note : "$pass" est produit à partir du mot de passe (codé)

    $MDP=$mdputil1;
    $MDPcode=ROT13($MDP);

    // --- Affichage d'un message de confirmation et impression
    // d'un bouton "Passage à la suite".

    print("<center> \n");
    print("<br> \n");
    print("Le mot de passe a bien été enregistré. <br> \n");
    print("<form method=\"POST\" action=\"admin.php\"> \n");
    print("<input type=\"hidden\" name=\"idaction\" value=\"menuGen\"> \n");
    print("<input type=\"hidden\" name=\"pass\" value=\"$MDPcode\"><input type=\"hidden\" name=\"repforum\" value=\"$repForum\"> \n");
    print("<input type=\"submit\" value=\"Passer à la suite\" name=\"A1\"> \n");
    print("</form> \n");
    print("</center> \n");
  }
}

// *****************************************************************************
// MODULE idaction="verifMDP" :
// Vérifie si le mot de passe entré par l'utilisateur est correct
// - si non : envoie un message d'avertissement et imprime à nouveau
// le formulaire d'interrogration du mot de passe
// - si oui : crée la variable "$pass" nécessaire pour accéder au menu
// des actions possibles
// *****************************************************************************

if ($idaction=="verifMDP") {

  // la valeur entrée par l'utilisateur est convertie en minuscules
  $mdputil=$_POST["mdputil"];
  $mdputil=strtolower($mdputil);

  if(crypt(md5($mdputil),"T2")!=$MDP) {
  // cas de figure où la valeur entrée par l'utilisateur n'est pas correcte
    print("<center> \n");
    print("<br> \n");
    print("Le mot de passe entré n'est pas valable. Veuillez à nouveau vous identifier.");
    print("</center> \n");
    ImprimFormInterrogMDP();
  }

  else {
  // cas de figure où la valeur entrée par l'utilisateur correspond au mot de passe
      $idaction="menuGen";
      $MDPcode=ROT13($MDP);
      $pass=$MDPcode;
  // -------------------------------------------
  // ------ On passe à la suite du script ------
  // -------------------------------------------
  }
}


// #############################################################################
// *****************************************************************************
// *****************************************************************************
// Modules correspondant aux fonctions d'administration
// *****************************************************************************
// *****************************************************************************

// *****************************************************************************
// Module idaction="menuGen" :
// Affiche le menu général des actions possibles
// *****************************************************************************

if ($idaction=="menuGen") {
  if ($pass!=ROT13($MDP)) {
    print("Erreur : Veuillez vous <a href=\"admin.php\">identifier</a> à nouveau.");
  }
  else {
    print("<center> \n");
    print("<br> \n");
    print("<b>MENU GENERAL :</b><br><br> \n");
    print("Vous pouvez accomplir les actions d'administration suivantes : <br><br> \n");
    print("<form method=\"POST\" action=\"admin.php\"><input type=\"hidden\" name=\"idaction\" value=\"menuSupMsgs\"><input type=\"hidden\" name=\"pass\" value=\"$pass\"><input type=\"hidden\" name=\"repforum\" value=\"$repForum\"><input type=\"submit\" value=\"1/  Supprimer des messages\" name=\"A1\">
	    <br /><br /><input type=\"button\" value=\"2/  Quitter ce module\" onclick='parent.window.close();'>
	    </form> \n");
//    print("<form method=\"POST\" action=\"admin.php\"><input type=\"hidden\" name=\"idaction\" value=\"menuChangeMDP\"><input type=\"hidden\" name=\"pass\" value=\"$pass\"><input type=\"submit\" value=\"2/  Changer le mot de passe\" name=\"A2\"></form> \n");
    print("</center> \n");
  }
}

// *****************************************************************************
// Module idaction="menuSupMsgs" :
// Affiche le menu "Suppression de messages"
// *****************************************************************************

if ($idaction=="menuSupMsgs") {
  if ($pass!=ROT13($MDP)) {
    print("Erreur : Veuillez vous <a href=\"admin.php\">identifier</a> à nouveau.");
  }
  else {

    // ==========================================================================
    // Lecture du fichier "index.dat" et stockage des données (identifiant,
    // niveau, date, nom et sujet) dans le tableau "$index"
    // ==========================================================================

    $tabindex=file("${reperForum}/index.dat");
    $nombremsgs=count($tabindex)-1;

    for($compt=1;$compt<=$nombremsgs;$compt++) {
      $index[$compt][1]=strtok($tabindex[$compt],"#"); // identifiant du message
      $index[$compt][2]=strtok("#");                   // niveau du message
      $chainetemp=strtok("#");                         // chaine date+nom+sujet
      $index[$compt][3]=strtok($chainetemp,"|");       // date
      $index[$compt][4]=strtok("|");                   // nom de l'auteur
      $index[$compt][5]=strtok("|");                   // sujet
    }

    // ==========================================================================
    // Cas de figure où aucun message n'a  encore été posté dans le forum de discussion
    // ==========================================================================

    if($nombremsgs<1) {
      print("<br> \n");
      print("<center> \n");
      print("Aucun message n'a été posté dans ce forum de discussion. <br> \n");
      print("<form method=\"POST\" action=\"admin.php\"><input type=\"hidden\" name=\"idaction\" value=\"menuGen\"><input type=\"hidden\" name=\"pass\" value=\"$pass\"><input type=\"hidden\" name=\"repforum\" value=\"$repForum\"><input type=\"submit\" value=\"Retour au menu général\" name=\"A1\"></form> \n");
      print("</center> \n");
      exit;
    }

    // ==========================================================================
    // Message d'explication destiné à l'utilisateur
    // ==========================================================================

    print("<br> \n");
    print("<center><b><font size=\"+1\">Suppression de messages</font></b></center><br> \n");

    print("<center>Le tableau suivant affiche la liste des messages postés dans le forum de discussion.<br> \n");
    print("Note : les sujets de discussion sont affichés ici <b>des plus anciens aux plus récents</b>.</center><br> \n");

    // ==========================================================================
    // Affichage des intitulés des messages dans un tableau (utilisant les
    // paramètres précisés plus haut)
    // ==========================================================================

    print("<table border=\"1\" align=\"center\"> \n");

    print("<tr><td bgcolor=\"#eeeeee\"><center><b>&nbsp;Ident.&nbsp;</b></center></td><td bgcolor=\"#eeeeee\"><b><center>INTITULE du message</center></b></td></tr> \n");

      for($compt=1;$compt<=$nombremsgs;$compt++) {

        // insertion d'un tableau à une ligne et deux colonnes
        // destiné à matérialiser la hierarche du forum

        print("<tr> \n");

        print("<td bgcolor=\"#eeeeee\"> \n");
        print("<center>".$index[$compt][1]."</center>");
        print("</td> \n");

        print("<td> \n");
        print("<table border=\"0\"> \n");
        print("<tr> \n");
        print("<td width=\"".tabulation($index[$compt][2]-1)."\"></td> \n");
        print("<td> \n");
        if($index[$compt][2]==1) {
          print("# \n");
        }
        else {
          print("> \n");
        }
        print("<b>".stripslashes(htmlentities(strip_tags($index[$compt][5])))."</b> - ");
        print("<b>".stripslashes(htmlentities(strip_tags($index[$compt][4])))."</b> (".$index[$compt][3].") <br> \n");
        print("</td> \n");
        print("</tr> \n");
        print("</table> \n");
        print("</td> \n");

        print("</tr> \n");
      }

    print("</table> \n");

    // ==========================================================================
    // Impression d'un message d'avertissement et du formulaire de saisie
    // d'identifiant de message à supprimer
    // ==========================================================================

    print("<br> \n");
    print("<table cellpadding=\"5\" border=\"1\" bgcolor=\"#fffff0\" align=\"center\"> \n");
    print("<tr><td align=\"center\"> \n");
    print("Pour supprimer un message du forum,<br> entrez ici son <b>numéro d'identification</b> : <br> \n");
    print("<form method=\"POST\" action=\"admin.php\"> \n");
    print("<input type=\"text\" name=\"idmsgsup\" size=\"5\"><br> \n");
    print("<input type=\"hidden\" name=\"idaction\" value=\"demandConfirmSuppMsg\"> \n");
    print("<input type=\"hidden\" name=\"pass\" value=\"$pass\"> \n");
    print("<input type=\"hidden\" name=\"repforum\" value=\"$repForum\"> \n");
    print("<br> \n");
    print("<input type=\"submit\" value=\"Envoyer\" name=\"A1\">");
    print("</form> \n");
    print("<b>Attention !</b> la suppression d'un message entraine automatiquement<br> la suppression des messages qui le suivent dans le fil de discussion<br>(même sujet de discussion). \n");
    print("</td></tr> \n");
    print("</table> \n");
    print("<br> \n");
    print("<center> \n");
    print("<form method=\"POST\" action=\"admin.php\"><input type=\"hidden\" name=\"idaction\" value=\"menuGen\"><input type=\"hidden\" name=\"pass\" value=\"$pass\"><input type=\"hidden\" name=\"repforum\" value=\"$repForum\"><input type=\"submit\" value=\"  Retour au menu général  \" name=\"A1\"></form> \n");
    print("</center> \n");
  }
}

// *****************************************************************************
// Module idaction="demandConfirmSuppMsg" :
// Affiche la page de demande de confirmation de suppression
// *****************************************************************************

if ($idaction=="demandConfirmSuppMsg") {
  if ($pass!=ROT13($MDP)) {
    print("Erreur : Veuillez vous <a href=\"admin.php\">identifier</a> à nouveau.");
  }
  else {

    // ==========================================================================
    // Lecture du fichier "index.dat" et stockage des données (identifiant,
    // niveau, date, nom et sujet) dans le tableau "$index"
    // ==========================================================================

    $tabindex=file("${reperForum}/index.dat");
    $nombremsgs=count($tabindex)-1;

    for($compt=1;$compt<=$nombremsgs;$compt++) {
      $index[$compt][1]=strtok($tabindex[$compt],"#"); // identifiant du message
      $index[$compt][2]=strtok("#");                   // niveau du message
      $chainetemp=strtok("#");                         // chaine date+nom+sujet
      $index[$compt][3]=strtok($chainetemp,"|");       // date
      $index[$compt][4]=strtok("|");                   // nom de l'auteur
      $index[$compt][5]=strtok("|");                   // sujet
    }


    // ==========================================================================
    // Cas de figure où l'utilisateur a entré une "valeur vide"
    // ==========================================================================

    if($idmsgsup=="") {
      print("<br> \n");
      print("<center> \n");
      print("Erreur ! Vous n'avez saisi aucune valeur. <br> \n");
      print("<form method=\"POST\" action=\"admin.php\"><input type=\"hidden\" name=\"idaction\" value=\"menuSupMsgs\"><input type=\"hidden\" name=\"pass\" value=\"$pass\"><input type=\"hidden\" name=\"repforum\" value=\"$repForum\"><input type=\"submit\" value=\"Retour au menu de suppression de messages\" name=\"A1\"></form> \n");
      print("</center> \n");
      exit;
    }

    // ==========================================================================
    // Cas de figure où le numéro de message à supprimer est aberrant
    // ==========================================================================

    if(($idmsgsup<0) or (!file_exists("${reperForum}/msg".$idmsgsup.".dat"))) {
      print("<br> \n");
      print("<center> \n");
      print("Erreur ! Ce message n'existe pas<br> ou a déjà été supprimé par l'administrateur du forum de discussion. <br> \n");
      print("<form method=\"POST\" action=\"admin.php\"><input type=\"hidden\" name=\"idaction\" value=\"menuSupMsgs\"><input type=\"hidden\" name=\"pass\" value=\"$pass\"><input type=\"hidden\" name=\"repforum\" value=\"$repForum\"><input type=\"submit\" value=\"Retour au menu de suppression de messages\" name=\"A1\"></form> \n");
      print("</center> \n");
      exit;
    }

    // ==========================================================================
    // Cas de figure où le message a déjà été supprimé par l'administrateur
    // ==========================================================================

    if(!file_exists("${reperForum}/msg".$idmsgsup.".dat")) {
      print("Ce message a déjà été supprimé par l'administrateur. <br> \n");
      print("<form method=\"POST\" action=\"admin.php\"><input type=\"hidden\" name=\"idaction\" value=\"menuSupMsgs\"><input type=\"hidden\" name=\"pass\" value=\"$pass\"><input type=\"hidden\" name=\"repforum\" value=\"$repForum\"><input type=\"submit\" value=\"Retour au menu de suppression de messages\" name=\"A1\"></form> \n");
      exit;
    }

    // ==========================================================================
    // Recherche du rang du premier message à supprimer
    // ==========================================================================

    $rangMsgSupP=1;

    while(@ $index[$rangMsgSupP][1]!=$idmsgsup) {
      $rangMsgSupP++;
    }

    // le rang du premier message à supprimer est stocké dans $rangMsgSupP

    // ==========================================================================
    // Recherche du rang du dernier message à supprimer
    // ==========================================================================

    $rangMsgSupD=$rangMsgSupP;

    while(@ $index[$rangMsgSupD+1][2]>$index[$rangMsgSupP][2]) {
      $rangMsgSupD++;
    }

    // le rang du dernier message à supprimer est stocké dans $rangMsgSupD

    // ==========================================================================
    // Affichage d'un message d'avertissement
    // ==========================================================================

    print("<br> \n");

    print("<table border=\1\" bgcolor=\"#fffff0\" align=\"center\" cellpadding=\"15\"> \n");
    print("<tr><td align=\"center\"> \n");
      print("<center><b>Vous êtes sur le point de supprimer le(s) message(s) suivant(s)</b> : </center><br> \n");

      // ==========================================================================
      // Affichage des intitulés des messages à supprimer dans un tableau
      // ==========================================================================

      print("<table border=\"1\" align=\"center\" bgcolor=\"#ffffff\"> \n");

      print("<tr><td bgcolor=\"#eeeeee\"><center><b>&nbsp;Ident.&nbsp;</b></center></td><td bgcolor=\"#eeeeee\"><b><center>INTITULE du message</center></b></td></tr> \n");

        for($compt=$rangMsgSupP;$compt<=$rangMsgSupD;$compt++) {

          // insertion d'un tableau à une ligne et deux colonnes
          // destiné à matérialiser la hierarche du forum

          print("<tr> \n");

          print("<td bgcolor=\"#eeeeee\"> \n");
          print("<center>".$index[$compt][1]."</center>");
          print("</td> \n");

          print("<td> \n");
          print("<table border=\"0\"> \n");
          print("<tr> \n");
          print("<td width=\"".tabulation($index[$compt][2]-1)."\"></td> \n");
          print("<td> \n");
          if($index[$compt][2]==1) {
            print("# \n");
          }
          else {
            print("> \n");
          }
          print("<b>".stripslashes(htmlentities(strip_tags($index[$compt][5])))."</b> - ");
          print("<b>".stripslashes(htmlentities(strip_tags($index[$compt][4])))."</b> (".$index[$compt][3].") <br> \n");
          print("</td> \n");
          print("</tr> \n");
          print("</table> \n");
          print("</td> \n");

          print("</tr> \n");
        }

      print("</table> \n");

      // ==========================================================================
      // Affichage de la demande de confirmation
      // ==========================================================================

      print("<center> \n");
      print("<form method=\"POST\" action=\"admin.php\"> \n");
      print("<input type=\"hidden\" name=\"idaction\" value=\"suppresMsgs\"> \n");
      print("<input type=\"hidden\" name=\"pass\" value=\"$pass\"><input type=\"hidden\" name=\"repforum\" value=\"$repForum\"> \n");
      print("<input type=\"hidden\" name=\"rangsupmin\" value=\"$rangMsgSupP\"> \n");
      print("<input type=\"hidden\" name=\"rangsupmax\" value=\"$rangMsgSupD\"> \n");
      print("<input type=\"submit\" value=\"Confirmer la suppression\" name=\"A1\"> \n");
      print("</form> \n");
      print("</center> \n");

    print("</td></tr> \n");
    print("</table> \n");

    print("<center> \n");
    print("<form method=\"POST\" action=\"admin.php\"><input type=\"hidden\" name=\"idaction\" value=\"menuGen\"><input type=\"hidden\" name=\"pass\" value=\"$pass\"><input type=\"hidden\" name=\"repforum\" value=\"$repForum\"><input type=\"submit\" value=\"Annuler (retour au menu général)\" name=\"A1\"></form> \n");
    print("</center> \n");
    print("</center> \n");

  }
}

// *****************************************************************************
// Module idaction="suppresMsgs" :
// Affiche la page de confirmation de suppression définitive de messages
// *****************************************************************************

if ($idaction=="suppresMsgs") {
  if ($pass!=ROT13($MDP)) {
    print("<br> \n");
    print("Erreur : Veuillez vous <a href=\"admin.php\">identifier</a> à nouveau.");
  }
  else {

    // ==========================================================================
    // Lecture du fichier "index.dat" et stockage des données dans le tableau
    // $index
    // ==========================================================================

      $tabindex=file("${reperForum}/index.dat");
      $nombremsgs=count($tabindex)-1;

      for($compt=1;$compt<=$nombremsgs;$compt++) {
        $index[$compt][1]=strtok($tabindex[$compt],"#"); // identifiant du message
        $index[$compt][2]=strtok("#");                   // niveau du message
        $chainetemp=strtok("#");                         // chaine date+nom+sujet
        $index[$compt][3]=strtok($chainetemp,"|");       // date
        $index[$compt][4]=strtok("|");                   // nom de l'auteur
        $index[$compt][5]=strtok("|");                   // sujet
     }

    // ==========================================================================
    // Suppression des fichiers msg__.dat
    // ==========================================================================

    if($rangsupmax>$nombremsgs) {
      print("<br> \n");
      print("<center> \n");
      print("Vous avez probablement tenté d'actualiser la page de confirmation de suppression de message. <br> \n");
      print("Cette opération n'a pas eu de conséquences dans ce cas précis.<br> \n");
      print("Elle aurait toutefois pu causer une erreur et endommager la structure du forum de discussion.<br><br> \n");
      print("<form method=\"POST\" action=\"admin.php\"><input type=\"hidden\" name=\"idaction\" value=\"menuGen\"><input type=\"hidden\" name=\"pass\" value=\"$pass\"><input type=\"hidden\" name=\"repforum\" value=\"$repForum\"><input type=\"submit\" value=\"Retour au menu général\" name=\"A1\"></form> \n");
      exit;
    }

    print("<br> \n");
    print("<center> \n");
    for($compt=$rangsupmin;$compt<=$rangsupmax;$compt++) {
      $testsup=unlink("${reperForum}/msg".$index[$compt][1].".dat");
      if($testsup) {
        print("Le message n° ".$index[$compt][1]." a bien été supprimé. <br> \n");
      }
      else {
       print("Impossible de supprimer le message n° ".$index[$compt][1].". <br> \n");
       print("<form method=\"POST\" action=\"admin.php\"><input type=\"hidden\" name=\"idaction\" value=\"menuGen\"><input type=\"hidden\" name=\"pass\" value=\"$pass\"><input type=\"hidden\" name=\"repforum\" value=\"$repForum\"><input type=\"submit\" value=\"Retour au menu général\" name=\"A1\"></form> \n");
       exit;
      }
    }
    print("</center> \n");

   // ==========================================================================
    // Recopie du fichier "index.dat", avec omission des lignes concernant les
    // messages à supprimer
    // ==========================================================================

    $ficindex=fopen("${reperForum}/index.dat","w+");

    fputs($ficindex,"Fichier Index. Ne pas éditer !\n");

    // --- recopie des premières lignes du fichier index.dat ---

    for($compt=1;$compt<=$rangsupmin-1;$compt++) {
      fputs($ficindex,$tabindex[$compt]);
    }

    // --- recopie des dernières lignes du fichier index.dat ---

    for($compt=$rangsupmax+1;$compt<=$nombremsgs;$compt++) {
      fputs($ficindex,$tabindex[$compt]);
    }

    fclose($ficindex);

    // ==========================================================================
    // Vérification du bon déroulement de l'opération de réécriture
    // du fichier "index.dat"
    // ==========================================================================

    $tabindverif=file("${reperForum}/index.dat");
    $nombremsgsnouv=count($tabindverif)-1;

    if($nombremsgsnouv!=(($nombremsgs-($rangsupmax-$rangsupmin+1)))) {
      print("Erreur durant l'opération de mise à jour du fichier index.<br> \n");
      print("La structure du forum a peut-être été endommagée.<br> \n");
      print("<form method=\"POST\" action=\"admin.php\"><input type=\"hidden\" name=\"idaction\" value=\"menuGen\"><input type=\"hidden\" name=\"pass\" value=\"$pass\"><input type=\"hidden\" name=\"repforum\" value=\"$repForum\"><input type=\"submit\" value=\"Annuler (retour au menu général)\" name=\"A1\"></form> \n");
      exit;
    }

    else {
      print("<center> \n");
      print("Mise à jour de l'index terminée.<br> \n");
      print("<br> \n");
      print("<form method=\"POST\" action=\"admin.php\"><input type=\"hidden\" name=\"idaction\" value=\"menuGen\"><input type=\"hidden\" name=\"pass\" value=\"$pass\"><input type=\"hidden\" name=\"repforum\" value=\"$repForum\"><input type=\"submit\" value=\"Retour au menu général\" name=\"A1\"></form> \n");
      print("<form method=\"POST\" action=\"admin.php\"><input type=\"hidden\" name=\"idaction\" value=\"menuSupMsgs\"><input type=\"hidden\" name=\"pass\" value=\"$pass\"><input type=\"hidden\" name=\"repforum\" value=\"$repForum\"><input type=\"submit\" value=\"Retour au menu de suppression de messages\" name=\"A1\"></form> \n");
      print("</center> \n");
    }

  }
}

// *****************************************************************************
// Module idaction="menuChangeMDP" :
// Affiche le menu permettant de changer le mot de passe
// *****************************************************************************

if ($idaction=="menuChangeMDP") {
  if ($pass!=ROT13($MDP)) {
    print("Erreur : Veuillez vous <a href=\"admin.php\">identifier</a> à nouveau.");
  }
  else {
    print("<center> \n");
    print("<br> \n");
    print("<b>Définition d'un nouveau mot de passe</b> <br> \n");
    ImprimFormChoixMDP();
    print("</center> \n");
  }
}

// #############################################################################
?>


</body>
</html>
