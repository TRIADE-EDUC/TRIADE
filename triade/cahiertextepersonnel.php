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
<?php 
include("./librairie_php/lib_licence.php"); 
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Cahier de textes" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
<?php
$visu=0;
if ($_SESSION["membre"] == "menupersonnel") {
	if (verifDroit($_SESSION["id_pers"],"cahiertextRead")){
		$visu=1;
	}
}
if ($visu == 0) {
	accesNonReserve();
	print "<br><br>";
}else{ 
?>
	<center><br>
	<form method='post' action="cahiertextpersonnel2.php"  name="formulaire1" 
	onsubmit="return valide_choix_pers('<?php print " un enseignant" ?>')" >
	<font class="T2"><?php print "Nom de l'enseignant" ?>  :</font> <select name="saisie_prof">
        <option   STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
	<?php
	$idpers=$_SESSION["id_pers"];
	$sql="SELECT libelle,text,idclasse,info FROM ${prefixe}parametrage WHERE libelle='permModCdT_$idpers'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$nomgrp="permModCdT_$idpers";
	$liste=preg_replace('/\{/',"",$data[0][1]);
	$liste=preg_replace('/\}/',"",$liste);
	unset($data);
	if ($liste != "") {
		$sql="SELECT nom,prenom,civ,pers_id FROM ${prefixe}personnel where pers_id IN ($liste)";
		$res=execSql($sql);
		$data=chargeMat($res);
	}
	for($i=0;$i<count($data);$i++) {
		print "<option value='".$data[$i][3]."' id='select1' >".ucwords($data[$i][0])." ".ucwords($data[$i][1]). "</option>";
	}
	?>
	</select>
	<BR><br>
	<UL><UL><UL>
	<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","consult"); //text,nomInput</script>
	<br><br>
	</UL></UL></UL>
	<input type="hidden" value="<?php print dateDMY2() ?>" name="iddate" />
	<input type="hidden" value="1" name="devoirvisu" />
	</form>
	</center>
<?php } ?>

<!-- // fin form -->
 </td></tr></table>

   <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;
?>

<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
