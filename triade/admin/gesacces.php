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
error_reporting(0);
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade_admin.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade</title>
</head>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%"  height="85" bgcolor="#0B3A0C">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Contrôle d'accès</font></b></td></tr>
<tr id="cadreCentral0" ><td > <p align="left"><font color="#000000">
<!-- // debut de la saisie -->
<?php
if (isset($_POST["rien"])) {
	if (isset($_POST["acceseleve"])) {
		if ($_POST["acceseleve"] == 1) {
			touch("../data/parametrage/noacces.eleve");
		}else{
			@unlink("../data/parametrage/noacces.eleve");
		}

	}else{
		@unlink("../data/parametrage/noacces.eleve");
	}

	if (isset($_POST["accesparent"])) {
		if ($_POST["accesparent"] == 1) {
			touch("../data/parametrage/noacces.parent");
		}else{
			@unlink("../data/parametrage/noacces.parent");
		}
	}else{
		@unlink("../data/parametrage/noacces.parent");
	}
}

if (file_exists("../data/parametrage/noacces.parent")) {
	$chekparentnon="checked='checked'";
	$chekparentoui="";
}else{
	$chekparentoui="checked='checked'";
	$chekparentnon="";
}

if (file_exists("../data/parametrage/noacces.eleve")) {
	$chekelevenon="checked='checked'";
	$chekeleveoui="";
}else{
	$chekeleveoui="checked='checked'";
	$chekelevenon="";
}


if (isset($_POST["justifie"])) {
	if (trim($_POST["com"]) == "") {
		@unlink("../data/parametrage/acces.commentaire");
	}
	$text=$_POST["com"];
	$text=nl2br($text);
	$text=htmlentities($text);
	$fp=fopen("../data/parametrage/acces.commentaire","w");
	fwrite($fp,"$text");
	fclose($fp);
}


if (file_exists("../data/parametrage/acces.commentaire")) {
	$fp=fopen("../data/parametrage/acces.commentaire","r");
	$donne=fread($fp,9000000);
	$donne=preg_replace("/\&lt;br \/&gt;/","",$donne);
	fclose($fp);
}
?>
<br>
<ul>
<form method="post" >
<font class=T2>
Accès Elève : oui <input type=radio name="acceseleve"  value="0" <?php print $chekeleveoui ?> > non <input type=radio name="acceseleve"  value="1" <?php print $chekelevenon ?> ><br><br>
Accès Parent : oui <input type=radio name="accesparent"  value="0" <?php print $chekparentoui ?> > non <input type=radio name="accesparent"  value="1" <?php print $chekparentnon ?> >
<br><br>
<table><tr><td><script language=JavaScript>buttonMagicSubmit("Valider","rien"); //text,nomInput</script></td></tr></table>
</font>
</form>
</ul>
<hr>
<ul>
<br>
<form method="post" >

<font class=T2>Commentaire de justification : </font><br>
<textarea name="com" cols=80 rows=8><?php print stripslashes($donne) ?></textarea>
<br><br>
<table><tr><td><script language=JavaScript>buttonMagicSubmit("Valider","justifie"); //text,nomInput</script></td></tr></table>

</form>


</ul>




</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
