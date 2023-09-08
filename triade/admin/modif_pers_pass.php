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
<?php include_once("../common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="../librairie_js/lib_css.js"></script>
<script language="JavaScript" src="../librairie_js/verif_creat.js"></script>
<title>Changement du mot de passe </title>
</head>
<body  id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<BR><center><font class=T2><?php print LANGMODIF16 ?></font></center>
<BR>
<table border="0" align="center" width="75%" bordercolor="#000000">
<tr><td>
<?php
include_once('../librairie_php/db_triade.php');
$cnx=cnx();
$id=$_GET["id"];
$type=$_GET["type"];
$nom=recherche_personne_nom($id,$type);
$prenom=recherche_personne_prenom($id,$type);
$affiche=1;
if(isset($_POST["create"])) {
	$cr=modif_pers_passe($_POST["id"],$_POST["pass"],$_POST["type"]);
	if ($cr) {
		$affiche=0;
		history_cmd("ADMIN ".$_SESSION["nom"],"MODIFICATION","password de ".ucwords($nom));
		?>
		<br><font class=T2><?php print LANGMODIF15 ?><b><?php print ucwords(strtolower($prenom));?>
		<?php print ucwords($nom);?></b> <?php print LANGMODIF15bis ?><br>
		</font>
<?php
	}else{
		$affiche1=affichageMessageSecuriteAdmin1();	
		alertJs($affiche1);
	}
}

if ($affiche) {
?>
<font class=T2>
<form method=post name=formulaire onsubmit="return validepass('Veuillez indiquer un mot de passe.')">
<?php print LANGNA1 ?> : <b><?php print ucwords($nom);?></b><br>
<br>
<?php print LANGNA2 ?> : <b><?php print ucwords(strtolower($prenom));?></b><br>
<br>
<?php print LANGPASS1 ?> : <input type=text name="pass" size=12 maxlength=50><br>
<br>
</font>
</tr></td></table>
<input type=hidden name="id" value="<?php print $id?>">
<input type=hidden name="type" value="<?php print $type?>">
<table align=center border=0 ><tr><td><script language=JavaScript>buttonMagicSubmit('<?php print LANGENR ?>','create') </script> <script language=JavaScript>buttonMagicFermeture(); //bouton de fermeture</script>&nbsp;&nbsp;</td></tr></table>
</form><BR>
<?php
}
?>
<br>
<table align=center border=0><tr><td><script language=JavaScript>buttonMagicFermeture(); //bouton de fermeture</script></td></tr></table>
</BODY></HTML>
<?php
Pgclose();
?>
