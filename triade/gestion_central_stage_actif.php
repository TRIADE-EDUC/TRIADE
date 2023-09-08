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
<script language="JavaScript" src="./librairie_js/ajax_centralstage.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/scriptaculous.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Activation comme centrale des stages"?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
$data=visu_param();
// nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement,$anneeScolaire
for($i=0;$i<count($data);$i++) {
	$nom_etablissement=trim($data[$i][0]);
	$adresse=trim($data[$i][1]);
	$postal=trim($data[$i][2]);
	$ville=trim($data[$i][3]);
	$tel=trim($data[$i][4]);
	$mail=trim($data[$i][5]);
	$directeur_etablissement=trim($data[$i][6]);
	$urlsite=trim($data[$i][7]);
	$accademie=trim($data[$i][8]);
	$pays=trim($data[$i][9]);
	$departement=trim($data[$i][10]);
	$anneeScolaire=trim($data[$i][11]);
}
?>

<?php
$http=(HTTPS == "oui")?"https://":"http://";
?>


<br><br>
<form action='https://support.triade-educ.org/centralestage/activation.php' method='post' target="centralstage" name="formulaire" id="formulaire" >
<table border=0 align=center >

<tr> 
<td align=right><font class="T2"><?php print "Product-ID" ?> :</font></td>
<td align=left><input type=text name="productid" value="<?php print PRODUCTID ?>"  readonly='readonly' size='45'  /></td>
</tr>

<tr> 
<td align=right><font class="T2"><?php print "URL" ?> :</font></td>
<td align=left><input type=text name="url" value="<?php print $http.$_SERVER["SERVER_NAME"]."/".ECOLE."/" ?>"  size='45'/></td>
</tr>

<tr> 
<td align=right><font class="T2"><?php print "Etablissement" ?> :</font></td>
<td align=left><input type=text name="etablissement" value="<?php print $nom_etablissement ?>"  size='45'/></td>
</tr>

<tr> 
<td align=right><font class="T2"><?php print "Ville" ?> :</font></td>
<td align=left><input type=text name="ville" value="<?php print $ville ?>"  size='45'/></td>
</tr>

<tr> 
<td align=right><font class="T2"><?php print "Pays" ?> :</font></td>
<td align=left><input type=text name="pays" value="<?php print $pays ?>"  size='45'/></td>
</tr>

</table>
<input type="hidden" name="inc" value="<?php print GRAPH ?>" />
<input type="hidden" name="supp" id="supp" value="non" />
</form>


<table align=center >
<tr><td><script language=JavaScript>buttonMagicSubmit4("<?php print "Activer"?>","rien","ActiveCentraleStage()")</script></td><td><script language=JavaScript>buttonMagicSubmit4("<?php print "Désactiver"?>","rien","DesactiveCentraleStage();")</script>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
</table>
<br><br>
<font class="T1">ATTENTION : Désactiver supprime toutes les affiliations.</font>

</td></tr></table>
<br><br>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="105">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Etat de l'activation" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<iframe width='100%' height=100% src="https://support.triade-educ.org/centralestage/verifactivation.php?productid=<?php print PRODUCTID ?>&inc=<?php print GRAPH ?>" name='centralstage' MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no ></iframe> 


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
