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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
include_once("librairie_php/recupnoteperiode.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE33?></font></b></td></tr>
<tr id='cadreCentral0' ><td ><br><br>
<?php
// recupe du nom de la classe
$data=chercheClasse($_POST["idclasse"]);
$classeNom=$data[0][1];
$classeNomLong=$data[0][2];
$classeNom=preg_replace('/ /','_',$classeNom);
$idClasse=$_POST["idclasse"];
$fic="classe".$idclasse;

if (is_dir("./data/parametrage/certificat_${classeNom}")) {
	nettoyage_repertoire("./data/parametrage/certificat_${classeNom}");
}else{
	mkdir("./data/parametrage/certificat_${classeNom}");
}

$num_certif=$_POST["num_certif"];
$eleveT=recupEleve($idClasse);      // nom,prenom,lv1,lv2,elev_id,date_naissance,lieu_naissance,adr1,code_post_adr1,commune_adr1,telephone, numero_eleve
for($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
	$nomEleve=trim(strtoupper($eleveT[$j][0]));
	$prenomEleve=trim(ucwords(strtolower($eleveT[$j][1])));
	$dateNaissanceEleve=dateForm($eleveT[$j][5]);
	$adresseEleve=$eleveT[$j][7];
	$CodePostalEleve=$eleveT[$j][8];
	$VilleEleve=$eleveT[$j][9];
	$LieuDeNaissance=$eleveT[$j][6];
	$Nationalite=rechercheNationaliteEleve($eleveT[$j][6]);

	$fic="./data/parametrage/certificat_${classeNom}/certificat_eleve_".$nomEleve.".rtf";
	@unlink("$fic");

	$TempFilename="./data/parametrage/certificat$num_certif.rtf";
	$fichier=fopen($TempFilename,"r");
	$longueur=9000000;
	$data=fread($fichier,$longueur);
	fclose($fichier);
	$datedujour=dateDMY();
	$paramScolaire=visu_param(); // nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement,annee_scolaire
	$anneeScolaire=$paramScolaire[0][11];

	$data=preg_replace("/NomEleve/",$nomEleve,$data);
	$data=preg_replace("/PrenomEleve/",$prenomEleve,$data);
	$data=preg_replace("/ClasseEleveLong/",$classeNomLong,$data);
	$data=preg_replace("/ClasseEleve/",$classeNom,$data);
	$data=preg_replace("/DateNaissanceEleve/","$dateNaissanceEleve",$data);
	$data=preg_replace("/AdresseEleve/","$AdresseEleve",$data);
	$data=preg_replace("/CodePostalEleve/","$CodePostalEleve",$data);
	$data=preg_replace("/VilleEleve/",ucwords($VilleEleve),$data);
	$data=preg_replace("/LieuDeNaissance/",ucwords($LieuDeNaissance),$data);
	$data=preg_replace("/DateDuJour/",$datedujour,$data);
	$data=preg_replace("/AnneeScolaire/",$anneeScolaire,$data);
	$data=preg_replace("/Nationalite/",$Nationalite,$data);

	$fichier=fopen("$fic","a");
	fwrite($fichier,$data);
	fclose($fichier);
}

include_once('./librairie_php/pclzip.lib.php');
@unlink("./data/parametrage/certificat_${classeNom}".'.zip');
$archive = new PclZip("./data/parametrage/certificat_${classeNom}".'.zip');
$archive->create("./data/parametrage/certificat_${classeNom}",PCLZIP_OPT_REMOVE_ALL_PATH);

nettoyage_repertoire("./data/parametrage/certificat_${classeNom}");
rmdir("./data/parametrage/certificat_${classeNom}");
?>
<ul>
<font class=T2>Récupération des certificats au format ZIP : <br></font><br />
<input type=button value="Télécharger"  class=button onclick="open('telecharger.php?fichier=<?php print "./data/parametrage/certificat_${classeNom}".'.zip' ?>','_blank','');" />
<br /><br /></ul>
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
