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
<?php 
include_once("./librairie_php/lib_licence.php"); 
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Gestion supplément aux titres</font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();

if ((isset($_GET["suppfichier"])) && (trim($_GET["suppfichier"]) != ""))  {
	@unlink("./data/parametrage/".$_GET["suppfichier"]);
	supp_parametrage_supplementtitre($_GET["suppfichier"]);
}

if (isset($_POST["createfile"])) {
	if (trim($_FILES['rtf']['tmp_name']) != "") {
		$fichier=$_FILES['rtf']['name'];
		$type=$_FILES['rtf']['type'];
		$tmp_name=$_FILES['rtf']['tmp_name'];
		$size=$_FILES['rtf']['size'];
		$key=md5(time());
	
		if ($size <= 8000000) {
			if  (($type == "application/octet-stream") || ($type == "application/msword")  || ($type == "application/rtf") || (preg_match('/\.rtf$/i',$fichier)) )  {
				@unlink("./data/parametrage/supplement_titre_$key.rtf");
				move_uploaded_file($tmp_name,"./data/parametrage/supplement_titre_$key.rtf");
				if (file_exists("./data/parametrage/supplement_titre_$key.rtf")) { 
					print "<br><center><font class='T2'>"."Fichier enregistré"."</font></center>";
					$libelle=preg_replace('/"/','',$_POST["libelle"]);
					enr_parametrage($libelle,"supplement_titre_$key.rtf","supplementautitre");
				}else{
					print "<br><center><font class='T2' id='color3' >"."Erreur d'enregistrement !!"."</font></center>";
				}
			}else{
				print "<br><center><font class='T2' id='color3' >Erreur : Fichier non reconnu !!</font></center>";
			}
		}else{
			print "<br><center><font class='T2' id='color3' >Erreur : Fichier suppérieur à 8 MO !!</font></center>";
		}
	}else{
		print "<br><center><font class='T2' id='color3' >Fichier NON enregistré !!</font></center>";

	}
}

?>
<br><br>
<form method='post' action='gestion_supplement_titre.php' enctype="multipart/form-data" >
<table align='center'>
<tr><td align='right'><font class='T2'>Libellé :</font></td><td><input type='text' name='libelle' size='30' maxlength='30' /></td></tr>
<tr><td height='10'></td></tr>
<tr><td align='right'><font class='T2'>Fichier "rtf" :</font></td><td><input type='file' name='rtf' /> <i>(max:8Mo)</i> </td></tr>
<tr><td height='20'></td></tr>
<tr><td colspan='2'>
<script language=JavaScript>buttonMagicRetour2('gestion_examen.php','_self',"<?php print LANGCIRCU14 ?>");</script>
<script language=JavaScript>buttonMagicSubmit3("<?php print LANGENR ?>","createfile",""); //text,nomInput</script>&nbsp;&nbsp;
</td></tr>
<tr><td height='10'></td></tr>
</table>
</form>
<br />
<hr>
<br /><br />
<table align='center' border='1' style='border-collapse: collapse;' >
<tr>
<td bgcolor='yellow'><font class='T2'>&nbsp;Libelle&nbsp;</font></td>
<td bgcolor='yellow'><font class='T2'>&nbsp;Action&nbsp;</font></td>
</tr>
<?php
$data=recupListeSupplementAuTitre();
//libelle,fichier
for($i=0;$i<count($data);$i++) {
	$libelle=$data[$i][0];
	$fichier="./data/parametrage/".$data[$i][1];
	print "<tr class=\"tabnormal\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\" ><td>";
	print "<font class='T2'>&nbsp;$libelle&nbsp;</font></td>";
	print "<td align='center'><a href='telecharger.php?fichier=$fichier&fichiername=$fichier'><img src='image/commun/download.png' border='0' title='Télécharger' /></a>";
	print "&nbsp;&nbsp;<a href='gestion_supplement_titre.php?suppfichier=".$data[$i][1]."'><img src='image/commun/trash.png' border='0' title='Supprimer' /></a>&nbsp;&nbsp;";		
	print "</td></tr>";

}
?>
</table>
<br><br>
<hr><br>
<ul>
<font class='T2'>
 <font class='shadow'><b>Configuration des mots clefs :</b></font><br><br>

 NBETUDIANTS => Nombre d'étudiants<br />
 HISTOETUDIANT => Parcours de l'étudiant<br />
 NOMETUDIANT => Nom de l'étudiant<br>
 PREETUDIANT => Prénom de l'étudiant<br>
 DATENAISETUDIANT => Date de naissance de l'étudiant<br>
 IDENTETUDIANT => Code d'identification de l'étudiant<br>
 NOMETABLISSEMENT => Nom de l'établissement de l'étudiant<br>
 DATEDUJOUR => Date du jour<br>
 LANGUEETUDIANT => La langue d'enseignement<br>
 NBRETUDIANTPA1 => Le nombre d'étudiants M4 et PREPA pour le titre 1 <br>
 NBRETUDIANTPA2 => Le nombre d'étudiants en première année pour le titre 2 <br>
 NBRETUDIANTPREPA => Le nombre d'étudiants en prépa  <br>
 NBRETUDIANTM4 => Le nombre d'étudiants en M4 pour le titre 1 <br>
 SPECIALISATION => Spécialisation de la classe <br>
 NOMDIRECTEUR => Nom du Directeur de l'établissement <br>
 NOMCLASSELONG => Nom de la classe au format long <br>

<br><br>
</ul>

</font>





<?php
Pgclose();
?>
<!-- // fin form -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
