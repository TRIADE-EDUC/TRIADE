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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS422 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
     <!-- // fin  -->
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
$data=visu_param();
// nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite
for($i=0;$i<count($data);$i++) {
	$nom_etablissement=trim($data[$i][0]);
	$adresse=trim($data[$i][1]);
	$postal=trim($data[$i][2]);
	$ville=trim($data[$i][3]);
	$tel=trim($data[$i][4]);
	$mail=trim($data[$i][5]);
	$directeur_etablissement=trim($data[$i][6]);
	$urlsite=trim($data[$i][7]);
	if (!preg_match("/http:/",$urlsite)) {
		$urlsite="http://".$urlsite;	
	}
	$accademie=trim($data[$i][8]);
	$pays=trim($data[$i][9]);
}
Pgclose();
?>
<BR>
<TABLE border=0 align=center>

<tr>
<td align="right"><?php print LANGPARAM8?> :</td>
<td><b><?php print $nom_etablissement?></b></td>
</tr>

<tr>
<td align="right"><?php print LANGPARAM37?> :</td>
<td><?php print $accademie?></td>
</tr>
<tr>
<td align="right" valign="top"><?php print LANGPARAM9?>  :</td>
<td><?php print $adresse?></td>
</tr>

<tr>
<td align="right"><?php print LANGPARAM10?> :</td>
<td><?php print $postal?></td>
</tr>

<tr>
<td align="right"><?php print LANGPARAM11?> :</td>
<td><?php print $ville?></td>
</tr>

<tr>
<td align="right"><?php print LANGAGENDA73 ?> :</td>
<td><?php print $pays?></td>
</tr>

<tr>
<td align="right"><?php print LANGPARAM12?> :</td>
<td><?php print $tel?></td>
</tr>

<tr>
<td align="right"><?php print LANGPARAM13?> :</td>
<td><?php print $mail?></td>
</tr>

<tr>
<td align="right"><?php print LANGPARAM34 ?> :</td>
<td><a href="<?php print $urlsite?>" target="_blank"><?php print $urlsite?></a></td>
</tr>

</table>

<?php 
if ((LAN == "oui") && ($ville != "") && ($adresse != "") && ($pays != "")){
?>
<br /><center>
<iframe src="https://support.triade-educ.org/support/google-map-V3-triade.php?etablissement=<?php print  urlencode($nom_etablissement)?>&adresse=<?php print urlencode($adresse) ?>&ville=<?php print urlencode($ville) ?>&pays=<?php print urlencode($pays)?>&web=<?php print urlencode($urlsite) ?>" width=400 height=300 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no ></iframe >
</center>
<?php } ?>
<BR><br>
<!-- // fin  -->
</td></tr></table>

<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
