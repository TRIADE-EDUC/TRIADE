<?php
  /**************************************************************************\
  * Phenix Agenda                                                            *
  * http://phenix.gapi.fr                                                    *
  * Written by Stephane TEIL <phenix-agenda@laposte.net>                     *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

// ----------------------------------------------------------------------------

// CE PROGRAMME EST RATTACHE AU MOD IMPRESSION D UNE NOTE
//

// Affiche un popup avec la note à imprimer
// puis lance l'impression

// ----------------------------------------------------------------------------


  require("inc/nocache.inc.php");
  require("inc/html.inc.php");
  include("inc/param.inc.php");

  if (isset($sid)) {
    include("inc/fonctions.inc.php");
  }
  else {
    exit;
  }

$idUser = Session_ok($sid);

include_once("../../common/config.inc.php");
$prefixe=PREFIXE;
$DB_CX->DbQuery("SELECT nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement,annee_scolaire FROM ${prefixe}info_ecole");
$nom_ecole = $DB_CX->DbResult(0,0);
$adresse = $DB_CX->DbResult(0,1);
$postal = $DB_CX->DbResult(0,2);
$ville = $DB_CX->DbResult(0,3);
$tel = $DB_CX->DbResult(0,4);
$mail = $DB_CX->DbResult(0,5);

// Init Variables
$soc_name="$nom_ecole";
$addr="$adresse";
$addr2="";
$cp="$postal";
$ville="$ville";
$tel="$tel";
$fax="";
$mail="$mail";




// Recuperation user
$DB_CX->DbQuery("SELECT CONCAT(".$FORMAT_NOM_UTIL."), util_email  FROM ${PREFIX_TABLE}utilisateur WHERE util_id=".$USER_SUBSTITUE);
$nomUtil = $DB_CX->DbResult(0,0);
$mailUtil = $DB_CX->DbResult(0,"util_email");

// Recuperation note res= array de $res[0] pour age_date
$sql  = "SELECT  UNIX_TIMESTAMP(age_date), age_heure_debut, age_libelle, age_detail ";
$sql .= " FROM ${PREFIX_TABLE}agenda";
$sql .= " WHERE age_id='".$idAge."'";
$DB_CX->DbQuery($sql);
$res = $DB_CX->DbNextRow();

//****************************
// Recuperation des infos de timezone de l'utilisateur
$DB_CX->DbQuery("SELECT tzn_libelle, tzn_gmt, tzn_date_ete, tzn_heure_ete, tzn_date_hiver, tzn_heure_hiver, t1.util_format_heure FROM ${PREFIX_TABLE}utilisateur t1, ${PREFIX_TABLE}utilisateur t2, ${PREFIX_TABLE}timezone WHERE t1.util_id=".$USER_SUBSTITUE." AND t2.util_id=".$idUser." AND ((tzn_zone=t1.util_timezone AND t2.util_timezone_partage='O') OR (tzn_zone=t2.util_timezone AND t2.util_timezone_partage='N'))");
$tzGmt = $DB_CX->DbResult(0,1);
$tzEte = calculBasculeDST($DB_CX->DbResult(0,2),gmdate("Y"),$DB_CX->DbResult(0,3),$tzGmt,0);
$tzHiver = calculBasculeDST($DB_CX->DbResult(0,4),gmdate("Y"),$DB_CX->DbResult(0,5),$tzGmt,1);

// Ajustement de la date en fonction du timezone
$decalageHoraire = calculDecalageH($tzGmt,$tzEte,$tzHiver,mktime(gmdate("H"),gmdate("i"),0,gmdate("n"),gmdate("j"),gmdate("Y")));
$Heure1  = afficheHeure(floor($res[1]),$res[1],"H");
$Minute1 = afficheHeure(floor($res[1]),$res[1],"i");
$DateNote = mktime($Heure1+$decalageHoraire, $Minute1, 0, date("m", $res[0]), date("d", $res[0]), date("y", $res[0]));
//****************************

// Fermeture BDD
$DB_CX->DbDeconnect();
?>

<!DOCTYPE html public "-//w3c//dtd html 4.0 transitional//en">
<HTML>
<HEAD>
  <META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <META http-equiv="Cache-Control" content="no-cache">
  <META name="robots" content="noindex">
  <TITLE>Agenda <?php echo $APPLI_VERSION; ?></TITLE>
</HEAD>

<BODY onLoad="javascript: window.focus(); window.print();" topmargin=0 leftmargin=1 marginwidth=1 marginheight=0>

<?php
echo "<br><h3>$soc_name</h3>";
echo "$addr<br>$addr2<br>$cp $ville<br><br>";
echo "T&eacute;l. : $tel<br>";
echo "Fax. : $fax<br><br>";
echo "E-mail : ".((!empty($mailUtil)) ? $mailUtil : $mail)."<br>";
?>

<br>
<br>
<hr style="width: 100%; height: 1px;">
<div style="text-align: center;"><br><big><big><big>FICHE RENDEZ-VOUS</big></big></big><br></div>
<br><hr style="width: 100%; height: 1px;"><br><br>
Madame, Monsieur,<br>
<br>
Votre prochain rendez-vous<br>
<br>
<table style="text-align: left; margin-left: auto; margin-right: auto;" width="80%" align="center" border="0" cellpadding="2" cellspacing="2">
    <tr>
      <td style="vertical-align: top;">- est fix&eacute; au</td>
      <td style="font-weight: bold;"><?php Echo date("d/m/Y",$DateNote)." &agrave ".date("H:i",$DateNote); ?><br><br>
      </td>
    </tr>
    <tr>
      <td style="vertical-align: top;">- avec</td>
      <td style="font-weight: bold;"><?php echo "$nomUtil"; ?><br><br>
      </td>
    </tr>
    <tr>
      <td style="vertical-align: top;">- et a pour objet</td>
      <td>

<?php
//Retour à la ligne et suppression des lignes vides dans l'affichage du détail de la note
$tabDetail = explode(chr(13),$res[3]);
$res[3] = "";
for ($nb=0;$nb<count($tabDetail);$nb++)
  {
      $tabDetail[$nb]=trim($tabDetail[$nb]);
      if (!empty($tabDetail[$nb]))$res[3] .= "<BR>".$tabDetail[$nb];
  }

echo "<b>$res[2]</b>$res[3]";
?>

   </td>
    </tr>
</table>
<br><br>En cas d'impossibilit&eacute;, merci de nous contacter au <b><?php echo "$tel"; ?></b><br><br>
</body>
</html> 
