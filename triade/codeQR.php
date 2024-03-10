<?php
session_start();
include_once("./librairie_php/lib_licence.php");
include_once("common/config.inc.php");
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
$cd=$_GET['cd'];
if (!is_numeric($cd)) $cd="0";
$data=recupInfoCompte($cd);
// id,nom,prenom,classe,compte_inactif
$idpers=$data[0][0];
$nom=$data[0][1];
$prenom=$data[0][2];
$idclasse=$data[0][3];
$classe=chercheClasse_nom($idclasse);
$compte_actif=($data[0][4] == 1) ? "<font color=red>Compte inactif</font>" : "<font color=green>Compte actif</font>";
$annee_scolaire=$data[0][5];
?>
<html>
<head>
   <meta name="MSSmartTagsPreventParsing" content="TRUE" />
   <meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />
   <meta http-equiv="pragma" content = "no-cache">
   <meta http-equiv="Cache" content="no store" />
   <meta http-equiv="expires" content = -1>
   <meta name="Copyright" content="Triade©, 2001" />
   <meta http-equiv="imagetoolbar" content="no" />
     <link rel="stylesheet" type="text/css" href="./librairie_css/css.css" media="screen" />
     <link rel="stylesheet" href="./librairie_css/modal-message.css" type="text/css" media="screen" >
     <link rel="shortcut icon" href="./favicon.ico" type="image/icon" />
   <title>Triade - Info QRCode </title>
</head>
<body>
<div class='cadre_central' style="width: 20em;
  border: 1px solid #333;
  border-radius: 10px;
  box-shadow: 5px 5px 5px #444;
  padding: 8px 12px;
  background-image: linear-gradient(180deg, #fff, #ddd 40%, #ccc);" 
>
<table border='0' width='100%'><tr><td width='50%'><font class='font1 shadow' ><b>Information Compte</b> - <?php print $compte_actif ?> </font></td></tr>
<tr><td><br>
<?php
print "<table>";
print "<tr><td align='center'><i>$annee_scolaire</i><br/><img src='image_trombi.php?idE=$idpers' /></td>";
print "<td valign='top' ><font class=T2>";
print "&nbsp;&nbsp;Nom : <b>$nom</b><br><br>";
print "&nbsp;&nbsp;Prénom : <b>$prenom</b><br><br>";
print "&nbsp;&nbsp;Classe : <i>$classe</i><br></font>";
print "</td></tr></table>";
?>
</td></tr></table>
</div>

</body>
</html>
