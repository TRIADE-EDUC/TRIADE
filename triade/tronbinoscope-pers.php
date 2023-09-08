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
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();

if (isset($_POST["create"])) {
	$photo=$_FILES['photo']['name'];
	$type=$_FILES['photo']['type'];
	$tmp_name=$_FILES['photo']['tmp_name'];
	$size=$_FILES['photo']['size'];

	$taille = getimagesize($tmp_name);
	if ((!empty($photo)) &&  ($size <= 2000000) &&  ($taille[0] <= 96) && ($taille[1] <= 96)   ) {
		$type=str_replace("image/","",$type);
		$type=str_replace("pjpeg","jpg",$type);
		$type=str_replace("jpeg","jpg",$type);
		$type=str_replace("x-png","png",$type);
		if (verifImageJpg($type))  {
			$nomphoto=$_POST["idpers"].".$type";
			move_uploaded_file($tmp_name,"data/image_pers/$nomphoto");
			history_cmd($_SESSION["nom"],"PHOTO","AJOUT $nomphoto");
			modif_photo_pers($nomphoto,$_POST["idpers"]);
			print "<script>alert(\"Photo Enregistré \\n\\n L'Equipe Triade\");</script>";
		 }else{
			print "<script>alert(\"".LANGTRONBI3." \\n\\n L'Equipe Triade\");</script>";
		 }
	} else {
		print "<script>alert(\"".LANGTRONBI4." \\n\\n L'Equipe Triade\");</script>";
	}
}


?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTRONBI11?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<!-- // debut form  -->
<form method="POST">
<blockquote><BR>
<font class="T2"><?php print "Modifier la photo de " ?></font> : <select id="saisie_classe" name="saisie_pers">
<option STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<optgroup label="Direction" />
<?php select_personne('ADM'); ?>
<optgroup label="Vie Scolaire" />
<?php select_personne('MVS'); ?>
<optgroup label="Enseignant" />
<?php select_personne('ENS'); ?>
<optgroup label="Personnel" />
<?php select_personne('PER'); ?>
</select> <BR>
<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","consult"); //text,nomInput</script>
</UL></UL></UL>
</blockquote>
</form>
<?php
// affichage de la classe
if(isset($_POST["consult"])) {
?>
	<br /><br /><hr>
	<form method="post" ENCTYPE="multipart/form-data">
	<br>
	<table border=0 width=100%>
	<tr>
	<?php if (isset($_POST["saisie_pers"])) { $idpers=$_POST["saisie_pers"]; } ?>
	<td align=center><img src="image_trombi.php?idP=<?php print $idpers?>" /></td>
	<td width=65%>
	<font class="T2">
	<?php print LANGNA1 ?> : <b><?php print recherche_personne_nom($idpers,"XXX"); ?></b> <br><br>
	<?php print LANGNA2?> : <b><?php print recherche_personne_prenom($idpers,"XXX"); ?></b> <br><br>
	<br><br>
	</font>
	<br>
	<tr><td colspan=2 align=center><br> <?php print $text1?> <?php print LANGTRONBI7 ?> : <input type="file" name="photo" size=30 > <br> <?php print LANGLOGO3?> </td></tr>
<tr><td colspan=2 align=center><br>
<table align=center><tr><td><br>
<script language=JavaScript>buttonMagicSubmit('<?php print LANGBT46?>','create'); //text,nomInput</script>&nbsp;&nbsp;
</td></tr></table>
</td></tr></table>
<input type="hidden" name="idpers" value="<?php print $idpers?>" >
</form>
<?php
}
?>


<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>

<!-- // fin form -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
