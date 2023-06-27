<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E.
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) S.A.R.L. T.R.I.A.D.E. 
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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>

<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();

$taille=2000000;
$taille2="2Mo";

include_once("librairie_php/lib_get_init.php");
include_once("common/config6.inc.php");

if (MAXUPLOAD == "oui") {
	$id=php_ini_get("safe_mode");
	if ($id != 1) {
		set_time_limit(600); // en secondes
		$taille=8000000;
		$taille2="8Mo";
	}
}

if (php_module_load("SQLite") != 1) {
	$erreurSQLITE="<font class='T2' color='red'>". LANGEDT10." </font> ";
	$disabled="disabled='diseabled'";

}

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCARNET63 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<br />
<form method="post" name="formulaire" ENCTYPE="multipart/form-data" >
<ul>
<table>
<tr><td><font class="T2"> <?php print LANGCARNET64 ?> :</font></td>
    <td><input type=file name="fichier"  > <A href='#' onMouseOver="AffBulle3('Information','./image/commun/info.jpg','<font face=Verdana size=1><B><font color=red><?php print LANGEDT1?></font></B><?php print LANGEDT1bis." <b>$taille2</b> . </font>" ?> '); window.status=''; return true;" onMouseOut='HideBulle()'><img src='./image/help.gif' align=center width='15' height='15'  border=0></A> </td>
</tr></table>	
</ul>
<UL><UL><UL><script language=JavaScript>buttonMagicRetour2("carnet_admin.php","_parent","<?php print LANGCIRCU14?>");</script>
<script language=JavaScript>buttonMagicSubmit3("<?php print LANGAGENDA86 ?>","create","<?php print $disabled ?>");</script></UL></UL></UL><br><br>
<?php brmozilla($_SESSION["navigateur"]); ?>
</form>
<?php 
print $erreurSQLITE; 
if (isset($_POST["create"])) {
	$fichier=$_FILES['fichier']['name'];
	$type=$_FILES['fichier']['type'];
	$tmp_name=$_FILES['fichier']['tmp_name'];
	$size=$_FILES['fichier']['size'];
	if ( (!empty($fichier)) &&  ($size <= $taille) &&  ($type == "text/xml") ) {
		@unlink("data/fichier_ASCII/cds.xml");
		move_uploaded_file($tmp_name,"data/fichier_ASCII/cds.xml"); ?>
		</td></tr></table><br /><br />
		<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
		<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCARNET63 ?></font></b></td></tr>
		<tr id='cadreCentral0' ><td >
<?php
		$xml = simplexml_load_file("data/fichier_ASCII/cds.xml");
		$cnx=cnx();
		foreach ($xml->PARAMETRAGE as $PARAM) {
			$versionTRIADE=$PARAM->VERSION_TRIADE;
			$versionPATCH=$PARAM->VERSION_PATCH;
			$versionXMLCDS=$PARAM->VERSION_XML_CDS;
			$dateCreationXML=$PARAM->DATE_CREATION_XML;
		}
		foreach ($xml->LES_CARNETS->UN_CARNET as $UN_CARNET) {
			$cr=create_carnet(accent_export($UN_CARNET->NOM_CARNET),$UN_CARNET->CODE_LETTRE,$UN_CARNET->CODE_CHIFFRE,$UN_CARNET->CODE_COULEUR,$UN_CARNET->CODE_NOTE,'',$UN_CARNET->NB_PERIODE);
			$nom_carnet=accent_export($UN_CARNET->NOM_CARNET);
		}
		if ($cr == -1) {
			print "<br /><ul><font class='T2'>".LANGCARNET66."</font></ul>";
		}else{
			$idcarnet=chercheIdCarnet($nom_carnet);
			foreach ($xml->LES_COMPETENCES->UNE_COMPETENCE as $UNE_COMPETENCE) {
				$nomCompetence=accent_export($UNE_COMPETENCE->NOM_COMPETENCE);
				$ordre=$UNE_COMPETENCE->ORDRE;
				$idcompetence=enr_competence_import($idcarnet,$nomCompetence,$ordre);
				foreach ($UNE_COMPETENCE->DES_DESCRIPTIFS->UN_DESCRIPTIF as $UN_DESCRIPTIF) {
					$descriptif=accent_export($UN_DESCRIPTIF->LIBELLE);
					$titre=accent_export($UN_DESCRIPTIF->TITRE);
					$ordre=$UN_DESCRIPTIF->ORDRE;
				
					enr_descriptif_import($idcarnet,$idcompetence,$titre,$descriptif,$ordre);
				}

			}
		Pgclose();
		unlink("data/fichier_ASCII/cds.xml");
		print "<center><font class='T2'>".LANGCARNET63."</font><br /><br />";
 		} 
	}
}
?>
</td></tr></table>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION["membre"] == "menuadmin") {
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
            print "</SCRIPT>";
       }else{
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
            print "</SCRIPT>";

       }
?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
