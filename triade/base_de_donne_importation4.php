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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" onunload="attente_close()" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
@unlink("./data/fichier_ASCII/import.csv");
@unlink("./data/fichier_ASCII/import.txt");
if (empty($_SESSION["adminplus"])) {
        print "<script>";
        print "location.href='./base_de_donne_importation.php'";
        print "</script>";
	exit;
}
?>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
<?php  $today= dateDMY();  ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript"<?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE22?></font></b></td></tr>
<tr id='cadreCentral0' >
<td ><!-- // fin  -->
<br />
<form method=post  action='./base_de_donne_importation5.php' name=formulaire ENCTYPE="multipart/form-data">
<br>
&nbsp;&nbsp;<font class="T2"><?php print LANGBULL3?> : </font><select name="annee_scolaire" size="1">
<?php
filtreAnneeScolaireSelectFutur(); // creation des options
?>
 </select><br><br><br>
<font class="T2">&nbsp;&nbsp;<?php print LANGIMP39?> :</font> <input type="file" name="fichier" size=20 >
<br />
<br />
<ul>
<?php print LANGBASE41?> : <input type=checkbox name="vide_eleve" value="oui" > (<?php print LANGOUI ?>)
<br />

<?php
$annee=date("Y")-1;
$annee=$annee."-".date("Y");
if (file_exists("./data/archive/$annee.sqlite")) {
	print "<font color=red>Attention la suppression des l'élèves, supprimera toutes les archives !!</font>";
}
?>
<br />
<br><br>
<script language=JavaScript>buttonMagicSubmit2("<?php print LANGBT23?>","rien","<?php print LANGBT5?>"); //text,nomInput</script>
<!-- <input type=submit onclick="attente()" value="Envoyer le fichier">-->
</ul>
<br />
<br />
</form>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
</BODY></HTML>
