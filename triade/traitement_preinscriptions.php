<?php
session_start();
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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>

<?php
session_start();		
include ("./librairie_php/fonction.inc.php");
$id=db_connect() or die ("<br>Acces a la base de donne cagt impossible");

$email = trim($_POST['mail']);			
$compteur = trim($_POST['compteur']);
$export = trim($_POST['Exporter']);

if ($export == 'Exporter')
  {
  $sqlexport = "SELECT elev_id,nom,prenom,classe,lv1,lv2,regime,date_naissance,lieu_naissance,nationalite,passwd,passwd_eleve,civ_1,nomtuteur,prenomtuteur,adr1,code_post_adr1,commune_adr1,tel_port_1,civ_2,nom_resp_2,prenom_resp_2,adr2,code_post_adr2,commune_adr2,tel_port_2,telephone,profession_pere,tel_prof_pere,profession_mere,tel_prof_mere,nom_etablissement,numero_etablissement,code_postal_etablissement,commune_etablissement,numero_eleve,photo,email,email_eleve,email_resp_2,class_ant,annee_ant,tel_eleve,sexe,option2 FROM preinscription_eleves WHERE decision = 'Accepté';";
  $res = mysql_query($sqlexport);
  $cpt=0;
  while($data = mysql_fetch_assoc($res))
  {  
  $contenu_fichier = $data['nom'].';'.$data['prenom'].';'.$data['classe'].';'.$data['regime'].';'.$data['date_naissance'].';'.$data['lieu_naissance'].';'.$data['nationalite'].';'.$data['civ_1'].';'.$data['nomtuteur'].';'.$data['prenomtuteur'].';'.$data['adr1'].';'.$data['code_post_adr1'].';'.$data['commune_adr1'].';'.$data['tel_port_1'].';'.$data['civ_2'].';'.$data['nom_resp_2'].';'.$data['prenom_resp_2'].';'.$data['adr2'].';'.$data['code_post_adr2'].';'.$data['commune_adr2'].';'.$data['tel_port_2'].';'.$data['telephone'].';'.$data['tel_eleve'].';'.$data['profession_pere'].';'.$data['tel_prof_pere'].';'.$data['profession_mere'].';'.$data['tel_prof_mere'].';'.$data['nom_etablissement'].';'.$data['numero_etablissement'].';'.$data['lv1'].';'.$data['lv2'].';'.$data['option2'].';'.$data['numero_eleve'].';'.$data['passwd'].';'.$data['email'].';'.$data['email_eleve'].';'.$data['class_ant'].';'.$data['annee_ant'].';'.$data['passwd_eleve'];
  $file=fopen("eleves$cpt.txt","a+");
  fwrite($file,$contenu_fichier."\n");
  fclose($file);
  $cpt++;
  }


  }
else
  {
for($j=0;$j<=$compteur;$j++)
  {
  $refuser = trim($_POST["refuser$j"]);
  $accepter = trim($_POST["accepter$j"]);
  $enattente = trim($_POST["enattente$j"]);
  $email_eleve2 = trim($_POST["$j"]);
  if ($refuser!='')
    {
    $sqlinsert = "UPDATE preinscription_eleves SET decision = 'Refusé' WHERE email_eleve= '$email_eleve2'";
    mysql_query($sqlinsert);
    }
  if ($accepter!='')
    {
    $sqlinsert = "UPDATE preinscription_eleves SET decision = 'Accepté' WHERE email_eleve= '$email_eleve2'";
    mysql_query($sqlinsert);
    } 
  if ($enattente!='')
    {
    $sqlinsert = "UPDATE preinscription_eleves SET decision = 'En attente' WHERE email_eleve= '$email_eleve2'";
    mysql_query($sqlinsert);
    }
  }

$sql = "SELECT elev_id,nom,prenom,classe,lv1,lv2,regime,date_naissance,lieu_naissance,nationalite,passwd,passwd_eleve,civ_1,nomtuteur,prenomtuteur,adr1,code_post_adr1,commune_adr1,tel_port_1,civ_2,nom_resp_2,prenom_resp_2,adr2,code_post_adr2,commune_adr2,tel_port_2,telephone,profession_pere,tel_prof_pere,profession_mere,tel_prof_mere,nom_etablissement,numero_etablissement,code_postal_etablissement,commune_etablissement,numero_eleve,photo,email,email_eleve,email_resp_2,class_ant,annee_ant,tel_eleve,sexe,option2,decision FROM preinscription_eleves;";
$res = mysql_query($sql); 
  }  
mysql_close();
$_SESSION['email_login'] = $data['email_eleve'];



?>

<form method="POST" action="./traitement_preinscriptions.php">

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><!--td height="2" colspan=2 --><b><font   id='menumodule1' >
Informations sur l'élève</B></font>
	<!--/td-->
</tr>
<td id='cadreCentral0'  colspan=12><br>&nbsp;&nbsp;<br><br></td>
<?php
$i=0;
while($data = mysql_fetch_assoc($res))
  {
$email_eleve = $data['email_eleve'];
		echo "<tr><td bgcolor=#FFFFFF align=right><B>Nom :</B> </td>";
		echo "<td bgcolor=#FFFFFF>";
		echo $data['nom'].' '.$data['prenom'];
    echo "</td><td bgcolor=#FFFFFF align=right><B>Classe :</B> </td>";
		echo "<td bgcolor=#FFFFFF>";
		echo $data['classe'];
		echo "</td><td bgcolor=#FFFFFF align=right><B>Décision</B> </td>";
		echo "<td bgcolor=#FFFFFF>";
		echo $data['decision'];
		//echo "</td><td bgcolor=#FFFFFF width=40% align=right><B>Accepter</B> </td>";
		echo "<td bgcolor=#FFFFFF>";
		echo '<input type=submit value=Accepter name=accepter'.$i.'></td>';
		//echo "</td><td bgcolor=#FFFFFF width=40% align=right><B>Refuser</B> </td>";
		echo "<td bgcolor=#FFFFFF>";
		echo '<input type=submit value=Refuser name=refuser'.$i.'></td>';
		echo "<td bgcolor=#FFFFFF>";
		echo '<input type=submit value=En_attente name=enattente'.$i.'></td>';
		echo '<input type=hidden name='.$i.' value='.$email_eleve.'>';
		//echo ;
		echo "</td></tr>";
		$i++;
 }
$i--;
echo '<input type=hidden name=compteur value='.$i.'>';
?>


<br><br>
<input type=submit value=Exporter name=Exporter>	
</td></tr>
</form>
<!-- // fin  -->
</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
