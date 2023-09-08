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
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Changement de mot de passe </title>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<BR><center><font size=3><b><?php print PASSG8 ?></b></font></center>
<BR><BR>
<table border="0" align=center width=75% bordercolor="#000000">
<tr><td>
<?php
include_once('librairie_php/db_triade.php');
validerequete("2");
$cnx=cnx();
$ideleve=$_GET["ideleve"];
$nomeleve=recherche_eleve_nom($ideleve);
$prenomeleve=recherche_eleve_prenom($ideleve);
$affiche=1;

if (isset($_GET["p2"])) {
	$email=trim(recupEmail("menuparent",$ideleve,2));
}else{
	$email=trim(recupEmail("menuparent",$ideleve,1));
}

if(isset($_POST["create"])) {
	if ($_POST["P2"] == "P2") {
		$cr=modif_eleve_passe_parent2($ideleve,$_POST["pass"],'',$_POST["envoimail"],$_POST["email"]);
	}else{
		$cr=modif_eleve_passe($ideleve,$_POST["pass"],'',$_POST["envoimail"],$_POST["email"]);
	}
	if ($cr) {
	$affiche=0;
?>
	<br><font class=T2><?php print PASSG9 ?> <b><?php print ucwords(strtolower($prenomeleve));?>
	<?php print ucwords($nomeleve);?></b> <?php print PASSG9bis ?> (<?php if (isset($_GET['p2'])) { print "Pour le tuteur 2"; }else{ print "Pour le tuteur 1"; } ?>) <br>
	</font>
	<br />
	<br />
	<table align=center border=0><tr><td><script language=JavaScript>buttonMagicFermeture(); //bouton de fermeture</script></td></tr></table>
<?php
	}else{
		$affiche1=affichageMessageSecurite2();	
		alertJs($affiche1);
	}

}

if ($affiche) {

?>
<form method=post name=formulaire onsubmit="return validepass()">
<font class="T2">
<?php print LANGNA1 ?> : <b><?php print ucwords($nomeleve);?></b><br>
<br>
<?php print LANGNA2 ?> : <b><?php print ucwords(strtolower($prenomeleve));?></b><br>
<br>
<?php print ucfirst(LANGIMP58) ?> : <b><?php print $email ;?></b><br>
<br>
<?php print LANGTMESS504 ?> : <input type=checkbox name="envoimail" value='oui' <?php if (!ValideMail($email)) print "disabled='disabled'"; ?> /> <i>(oui)</i><br>
<br>
<?php print LANGPASS1 ?> : <input type=text name="pass" size=10 maxlength=50><br>
</font>
<br>
<br>
<input type="hidden" name="P2"  value="<?php print $_GET["p2"] ?>" >
<input type="hidden" name="email"  value="<?php print $email ?>" >
</tr></td></table>
<table align=center border=0><tr><td><script language=JavaScript>buttonMagicSubmit('<?php print LANGENR ?>','create') </script>
	<script language=JavaScript>buttonMagicFermeture(); //bouton de fermeture</script>&nbsp;&nbsp;
</td></tr></table>
</form>
<?php
}
?>
</BODY></HTML>
<?php
Pgclose();
?>
