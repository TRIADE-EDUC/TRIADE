<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET 
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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="185">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print " Export des entreprises à la centrale des stages"?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>

<?php
include_once('./librairie_php/db_triade.php');
validerequete("menuadmin");

include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(0);
}

$cnx=cnx();

$fichier="./data/triade2centralestage.xml";
@unlink($fichier);

$xml=fopen($fichier,"a+");
fwrite($xml,'<?xml version="1.0" encoding="ISO-8859-1"?>'."\n");
fwrite($xml,'<TRIADE2CENTRALESTAGE>'."\n");


fwrite($xml,"\t".'<PARAMETRAGE>'."\n");
fwrite($xml,"\t\t".'<VERSION_TRIADE>'.VERSION.'</VERSION_TRIADE>'."\n");
fwrite($xml,"\t\t".'<VERSION_PATCH>'.VERSIONPATCH.'</VERSION_PATCH>'."\n");
fwrite($xml,"\t\t".'<VERSION_XML_CENTRALE_STAGE>'."1.0".'</VERSION_XML_CENTRALE_STAGE>'."\n");
fwrite($xml,"\t\t".'<DATE_CREATION_XML>'.dateDMY2().'</DATE_CREATION_XML>'."\n");
fwrite($xml,"\t".'</PARAMETRAGE>'."\n");

$cnx=cnx();
$data=listingEntreprise(); // nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus,bonus,contact_fonction,pays_ent
fwrite($xml,"\t".'<LES_ENTREPRISES>'."\n");
for($i=0;$i<count($data);$i++) {

	$nom=$data[$i][0];
	$contact=$data[$i][1];
	$adresse=$data[$i][2];
	$code_p=$data[$i][3];
	$ville=$data[$i][4];
	$secteur_ac=$data[$i][5];
	$activite_prin=$data[$i][6];
	$tel=$data[$i][7];
	$fax=$data[$i][8];
	$email=$data[$i][9];
	$info_plus=$data[$i][10];
	$bonus=$data[$i][11];
	$contact_fonction=$data[$i][12];
	$pays_ent=$data[$i][13];


	fwrite($xml,"\t\t".'<UNE_ENTREPRISE>'."\n");
	fwrite($xml,"\t\t\t".'<NOM>'.$nom."</NOM>\n");
	fwrite($xml,"\t\t\t".'<CONTACT>'.$contact."</CONTACT>\n");
	fwrite($xml,"\t\t\t".'<ADRESSE>'.$adresse."</ADRESSE>\n");
	fwrite($xml,"\t\t\t".'<CODE_POSTALE>'.$code_p."</CODE_POSTALE>\n");
	fwrite($xml,"\t\t\t".'<VILLE>'.$ville."</VILLE>\n");
	fwrite($xml,"\t\t\t".'<PAYS>'.$pays_ent."</PAYS>\n");
	fwrite($xml,"\t\t\t".'<SECTEUR_ACTIVITE>'.$secteur_ac."</SECTEUR_ACTIVITE>\n");
	fwrite($xml,"\t\t\t".'<ACTIVITE_PRINCIPALE>'.$activite_prin."</ACTIVITE_PRINCIPALE>\n");
	fwrite($xml,"\t\t\t".'<TEL>'.$tel."</TEL>\n");
	fwrite($xml,"\t\t\t".'<FAX>'.$fax."</FAX>\n");
	fwrite($xml,"\t\t\t".'<EMAIL>'.$email."</EMAIL>\n");
	fwrite($xml,"\t\t\t".'<INFO>'.$info_plus."</INFO>\n");
	fwrite($xml,"\t\t\t".'<BONUS>'.$bonus."</BONUS>\n");
	fwrite($xml,"\t\t\t".'<CONTACT_FONCTION>'.$contact_fonction."</CONTACT_FONCTION>\n");
	fwrite($xml,"\t\t".'</UNE_ENTREPRISE>'."\n");
}
fwrite($xml,"\t".'</LES_ENTREPRISES>'."\n");
fwrite($xml,'</TRIADE2CENTRALESTAGE>'."\n");
fclose($xml);


Pgclose();

?>


<font class="T2">
&nbsp;&nbsp;<img src="./image/on1.gif" align='center' width='8' height='8' /> <a href='telecharger.php?fichier=<?php print $fichier ?>'>Récupérer le fichier<b>cliquez ici</b></a>
</font>


<!-- // fin  -->
</td></tr></table>


<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
            print "</SCRIPT>";

       endif ;
?>
</BODY></HTML>
