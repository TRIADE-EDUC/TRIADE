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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("2");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Export des données vers Photographe de France"?></font></b></td></tr>
<tr id='cadreCentral0'  >
<td valign="top">
<?php
if (LAN == "oui") {
	if (isset($_POST["create"])) {
		$cnx=cnx();
		
		$url=$_SERVER["SERVER_NAME"];
		if (file_exists("./common/config-pdf.php")) {
			include_once("./common/config-pdf.php");
			$ref=IDETABLISSEMENT;
		}else{
			$chaine=dateYMDHMS().$url;
			$ref=md5($chaine);
		}
	

		$idEtablissement=$ref;
		$urlServeur=$url;

		$identPDF=$_POST["identPDF"];
		$email=$_POST["email"];

		@unlink("./common/config-pdf.php");
		$texte="<?php\n";
		$texte.='define("IDETABLISSEMENT","'.$idEtablissement.'");'."\n";
		$texte.="?>";
		$fichier=fopen("./common/config-pdf.php","a");
       	        fwrite($fichier,$texte);
       	        fclose($fichier);

		$url="http://wellphoto.triade-educ.com/import_liste.php";
		print "<iframe width='10' height='10' name='TRIADE-PDF' src='vide.html' style='visibility:hidden' ></iframe>";
		print "<form method='post' action='${url}' target='TRIADE-PDF' name='formulaire'>";
		print "<textarea name='triade-list' style='visibility:hidden'   >"; 
?>
<TRIADE2PDF>
	<PARAMETRAGE>
		<VERSION_TRIADE><?php print VERSION ?></VERSION_TRIADE>
		<VERSION_PATCH><?php print VERSIONPATCH ?></VERSION_PATCH>
		<VERSION_XML_PDF><?php print "1.0" ?></VERSION_XML_PDF>
		<DATE_CREATION_XML><?php print dateDMY2() ?></DATE_CREATION_XML>
	</PARAMETRAGE>
	<INFO>
		<IDPHOTOGRAPHE><?php print $identPDF ?></IDPHOTOGRAPHE>
		<IDETABLISSEMENT><?php print $idEtablissement ?></IDETABLISSEMENT>
		<URLETABLISSEMENT><?php print $urlServeur ?></URLETABLISSEMENT>
		<INFOUSER><?php print $email ?></INFOUSER>
	</INFO>
<?php
$data=affEleve(); //elev_id, nom, prenom, classe
print("\t".'<LES_ELEVES>'."\n");
for($i=0;$i<count($data);$i++) {
	$id=$data[$i][0];
	$nom=$data[$i][1];
	$prenom=$data[$i][2];
	$idclasse=$data[$i][3];
	$classe=chercheClasse_nom($idclasse);
	print("\t\t".'<UN_ELEVE>'."\n");
	print("\t\t\t".'<ID>E_'.$id."</ID>\n");
	print("\t\t\t".'<NOM>'.$nom."</NOM>\n");
	print("\t\t\t".'<PRENOM>'.$prenom."</PRENOM>\n");
	print("\t\t\t".'<CLASSE>'.$classe."</CLASSE>\n");
	print("\t\t".'</UN_ELEVE>'."\n");
}
	print("\t".'</LES_ELEVES>'."\n");
	
print("\t".'<LES_PERSONNELS>'."\n");

$data=affPers("ADM"); // pers_id, civ, nom, prenom
for($i=0;$i<count($data);$i++) {
	$id=$data[$i][0];
	$civ=civ($data[$i][1]);
	$nom=$data[$i][2];
	$prenom=$data[$i][3];
	$type="Direction";
	print("\t\t".'<UNE_PERSONNE>'."\n");
	print("\t\t\t".'<ID>P_'.$id."</ID>\n");
	print("\t\t\t".'<CIV>'.$civ."</CIV>\n");
	print("\t\t\t".'<NOM>'.$nom."</NOM>\n");
	print("\t\t\t".'<PRENOM>'.$prenom."</PRENOM>\n");
	print("\t\t\t".'<TYPE>'.$type."</TYPE>\n");
	print("\t\t".'</UNE_PERSONNE>'."\n");
}

$data=affPers("MVS"); // pers_id, civ, nom, prenom
for($i=0;$i<count($data);$i++) {
	$id=$data[$i][0];
	$civ=civ($data[$i][1]);
	$nom=$data[$i][2];
	$prenom=$data[$i][3];
	$type="Vie Scolaire";
	print("\t\t".'<UNE_PERSONNE>'."\n");
	print("\t\t\t".'<ID>P_'.$id."</ID>\n");
	print("\t\t\t".'<CIV>'.$civ."</CIV>\n");
	print("\t\t\t".'<NOM>'.$nom."</NOM>\n");
	print("\t\t\t".'<PRENOM>'.$prenom."</PRENOM>\n");
	print("\t\t\t".'<TYPE>'.$type."</TYPE>\n");
	print("\t\t".'</UNE_PERSONNE>'."\n");
}

$data=affPers("ENS"); // pers_id, civ, nom, prenom
for($i=0;$i<count($data);$i++) {
	$id=$data[$i][0];
	$civ=civ($data[$i][1]);
	$nom=$data[$i][2];
	$prenom=$data[$i][3];
	$type="Enseignant(e)";
	print("\t\t".'<UNE_PERSONNE>'."\n");
	print("\t\t\t".'<ID>P_'.$id."</ID>\n");
	print("\t\t\t".'<CIV>'.$civ."</CIV>\n");
	print("\t\t\t".'<NOM>'.$nom."</NOM>\n");
	print("\t\t\t".'<PRENOM>'.$prenom."</PRENOM>\n");
	print("\t\t\t".'<TYPE>'.$type."</TYPE>\n");
	print("\t\t".'</UNE_PERSONNE>'."\n");
}
print("\t".'</LES_PERSONNELS>'."\n");

	print("</TRIADE2PDF>");
	print "</textarea>";
	
		print "</form>";
		print "<script>document.formulaire.submit()</script>";
		print "<center><font class=T2>INFORMATIONS ENVOYEE(S)";
		print "</font></center><br /><br />";
		history_cmd($_SESSION["nom"],"TRIADE2PDF","Envoi information P.d.F.");
		Pgclose();
	}else{
		print "<center><font class='T2' >INFORMATIONS NON ENVOYEE(S)</font></center>";
	}
}else{
	print "<br><center><font class=T2>".ERREUR1."</font> <br><br> <i>".ERREUR3."</i></center>";
}



?>



</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
