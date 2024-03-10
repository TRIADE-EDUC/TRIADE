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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<title>Triade</title>
<?php include("./librairie_php/lib_licence.php"); ?>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/valide.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Vérification - Listing des fichiers</font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<br>
<div id="infopatch" align="center"></div>
<?php
if (file_exists("../common/config-md5.php")) {
	include("../common/config-md5.php");
}else{
	define("VERSIONMD5","000");
}
include_once("./librairie_php/db_triade_admin.php");

if (LAN == "oui") {
	$cnx=cnx();
?>
	
<script language="JavaScript" src="https://support.triade-educ.net/support/version-patch.php?v=<?php print VERSIONPATCH ?>"></script>
<script language="JavaScript" src="https://support.triade-educ.org/support/version-md5.php?v=<?php print VERSIONMD5 ?>"></script>
<ul>
<font class='T2'>Liste des fichiers non conformes : </font>
<br><br>
<form method="post" action="listchecksum2.php" onSubmit="return valideMail()" name="formulaire" >
<?php
$data=listeFichierMd5();
print "<textarea cols=60 rows=20  STYLE=\"font-family: Arial;font-size:12px;background-color:#FCE4BA;\" name='liste' readonly='readonly'>";
for($i=0;$i<count($data);$i++) {
	$sum=$data[$i][0];
	$fichier=trim($data[$i][2]);
	print "$fichier;\n";
}
print "</textarea>";

?>
<br /><br />
<?php 
if (!file_exists("../../../common/lib_acces_inc.php")) { ?>
<script language=JavaScript>buttonMagicSubmit("Récupération du patch","envoi"); //text,nomInput</script>
<?php }else{ ?>
<i>La gestion de votre Triade est sous le contrôle de nos équipes.</i> 

<?php } ?>
</form>
<br /><br />
<?php
}else{
	print "<br><center><font class=T2>".ERREUR1."</font> <br><br> <i>".ERREUR2."</i></center>";
}
?>
<!-- // fin de la saisie -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
<?php Pgclose() ?>
<script language="JavaScript">
if (update == 1) { 
	document.getElementById("infopatch").innerHTML="<font color=red><b>ATTENTION, VOUS DEVEZ PATCHER VOTRE TRIADE,<br /> AVANT D'EFFECTUER CETTE OPERATION, <br />PATCH DISPONIBLE : <a href='update.php'><font color=red>MODULE \"Triade update\"</font></a></b></font>"; 
	document.formulaire.envoi.disabled=true;
}else{
	if (updatemd5 == 1) {
		document.getElementById("infopatch").innerHTML="<font color=red><b>ATTENTION, VOUS DEVEZ INSTALLER LE PATCH MD5,<br /> AVANT D'EFFECTUER CETTE OPERATION, <br />PATCH DISPONIBLE : <a href='https://support.triade-educ.org/support/recupFichierMd5.php' target='_blank' ><font color=red>MODULE \"Triade MD5 \"</font></a></b></font>"; 
		document.formulaire.envoi.disabled=true;
	}
}

</script>


</html>
