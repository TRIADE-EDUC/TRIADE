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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
if ($_SESSION["membre"] == "menupersonnel") {
        if (!verifDroit($_SESSION["id_pers"],"edt")) {
                accesNonReserveFen();
                exit;
        }
}else{
	validerequete("2");
}
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGEDT9 ?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td >
<br />
<!-- // fin  -->
<?php
if (isset($_POST["supp"])) {
	$dateDebut=dateFormBase($_POST["datedebutEDT"]);
	$dateFin=dateFormBase($_POST["datefinEDT"]);

	$dateD=preg_replace('/-/','',$dateDebut);
	$dateF=preg_replace('/-/','',$dateFin);

	if ((trim($_POST["datedebutEDT"]) != "" ) && (trim($_POST["datedebutEDT"]) != "" )) {

		if ($dateF < $dateD) {
			print "<center><font id='color2' class='T2'><b>ERREUR SUR LES DATES !! </b></font></center><br><br>";
		}else{
			print "<center><font id='color2' class='T2'><b>Suppression effectuée.</b></font></center><br><br>";
			suppPeriodeEdt($dateDebut,$dateFin);	
		}
	}
}
?>
<table border=0 align='center' >
<form action='gestion_suppr_edt.php' method='post' name="form2" >
<td align=right><font class="T2"><?php print "Période à supprimer sur l'EDT" ?> :</td><td>
du <input type="text" name="datedebutEDT" value="<?php print $_POST["datedebutEDT"] ?>"  onclick="this.value=''" size=12 class="bouton2" onKeyPress="onlyChar(event)" />&nbsp;<?php
include_once("librairie_php/calendar.php");
calendar("id111","document.form2.datedebutEDT",$_SESSION["langue"],"0","0");
?>&nbsp;<br>au&nbsp;<input type="text" name="datefinEDT" value="<?php print $_POST["datefinEDT"] ?>"  onclick="this.value=''" size=12 class="bouton2" onKeyPress="onlyChar(event)" />&nbsp;<?php
calendar("id222","document.form2.datefinEDT",$_SESSION["langue"],"0","0");
?> </font></td></tr>
<tr><td height=20 id='bordure' ></td></tr>
<tr><td align=center valign='bottom' ><script language=JavaScript> buttonMagicSubmit3("<?php print "Valider la suppression" ?>","supp","")</script></td></tr>
</form>
</table>
<br>


<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php PgClose();  ?>
</BODY></HTML>
