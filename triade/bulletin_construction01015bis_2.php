<?php
session_start();
error_reporting(0);
include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET);
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
include_once("./librairie_php/lib_licence.php");
include_once("./common/config.inc.php");
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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();"  >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="100%">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Mise en place des matières par groupement"?> </font></b></td></tr>
<tr  id='cadreCentral0' >
<td valign='top'>
<!-- // fin  --><br> <br>
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
validerequete("menuadmin");

if (isset($_POST["saisie_classe"])) { $idclasse=$_POST["saisie_classe"]; $nbgroupement=$_POST["nbgroupement"]; }
if (isset($_GET["idclasse"]))  { $idclasse=$_GET["idclasse"]; $nbgroupement=$_GET["nbgroupement"]; }

?>
<form method='post' name="formulaire" action="bulletin_construction01015bis_3.php" >
<ul>
<input type='hidden'  name="saisie_classe" value="<?php print $idclasse ?>" />
<input type='hidden'  name="nbgroupement" value="<?php print $nbgroupement ?>" />
<?php 
if (isset($_GET["supp"])){ supp_parametrage_bulletin($_GET["supp"],$_GET["idclasse"]);}


for ($i=1;$i<=$nbgroupement; $i++) { 
	$data=aff_grp_bull_leap("bulletinLeap_$i",$idclasse);
	$nomdugroupe=$data[0][2];
?>
	<font class='T2'> Nom du <?php print $i ?> groupement : </font> <input type="text" name="label[]" maxlength='30' size='30' value="<?php print $nomdugroupe ?>" />&nbsp;&nbsp;<img src="image/commun/trash.png" title="Supprimer" onclick="open('bulletin_construction01015bis_2.php?supp=<?php print "bulletinLeap_$i" ?>&idclasse=<?php print $idclasse ?>&nbgroupement=<?php print $nbgroupement ?>','_self','')"  align="center" /> <br><br><br>
<?php } ?>

<br><br><input type="submit" value="<?php print "Suivant" ?> -->"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" >
</form>
</ul>
</BODY></HTML>
