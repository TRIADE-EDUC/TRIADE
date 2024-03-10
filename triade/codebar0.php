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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCODEBAR1 ?> </font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
<table width='100%' border=1 bgcolor="#FFFFFF" bordercolor="#000000" >
<tr>
<td id='bordure' >
<form method=post onsubmit="return valide_consul_classe()" name="formulaire">
<font class=T2><?php print LANGELE4?> :</font> <select id="saisie_classe" name="saisie_classe">
<option id='select0' ><?php print LANGCHOIX?></option>
<?php
select_classe(); // creation des options
?>
</select> <BR><br />
<font class=T2><?php print LANGMESS221 ?></font> <select name="codebase">
<!-- <option value="codabar" id='select0' >CODABAR</option> -->
<option value="code39" id='select0' >code39</option>
<option value="qcode" id='select0' >Qcode</option>
<!-- <option value="EAN13-ISBN" id='select0' >EAN-13/ISBN</option> -->
</select>

<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","consult"); //text,nomInput</script>

</UL></UL></UL>

<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
</form>
</td>


<td id='bordure'>
<form method=post onsubmit="return valide_consul_membre()" name="formulaire1">
<font class=T2><?php print LANGMESS216 ?> :</font> <select id="membre" name="membre">
<option id='select0' ><?php print LANGCHOIX?></option>
<option id='select1' value="menuadmin" ><?php print "Direction"?></option>
<option id='select1' value="menuprof" ><?php print "Enseignant"?></option>
<option id='select1' value="menuscolaire" ><?php print "Vie Scolaire"?></option>
<option id='select1' value="menupersonnel" ><?php print "Personnel"?></option>
</select> <BR><br />
<font class=T2><?php print LANGMESS221?></font> <select name="codebase">
<!-- <option value="codabar" id='select0' >CODABAR</option> -->
<option value="code39" id='select0' >code39</option>
<option value="qcode" id='select0' >Qcode</option>
<!-- <option value="EAN13-ISBN" id='select0' >EAN-13/ISBN</option> -->
</select>

<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","consultmembre"); //text,nomInput</script>
</UL></UL></UL>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
</form>
</td>
</tr></table>
<?php if(isset($_POST["codebase"])) { ?>
		<br><center><?php print LANGCODEBAR4 ?> <b>code39</b>.</center>
<?php } ?>

<!-- // fin form -->
 </td></tr></table>

<?php
// affichage de la classe
if(isset($_POST["consult"])) {
	$saisie_classe=$_POST["saisie_classe"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);

	// ne fonctionne que si au moins 1 élève dans la classe
	// nom classe
	$cl=$data[0][0];
?>
	<BR><BR><BR>
	<table border="1" cellpadding="3" cellspacing="1" width="100%"  height="85">
	<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGELE4?> : <font id="color2"><B><?php print $cl?></font></font></td></tr>
	<?php
	if( count($data) <= 0 )	{
		print("<tr><td align=center valign=center>".LANGRECH1."</td></tr>");
	}else {
		history_cmd($_SESSION["nom"],"VISUALISA.","Code Barre");

	?>
	<tr><td>
	<iframe src="./codebar.php?idclasse=<?php print $saisie_classe?>&codebase=<?php print $_POST["codebase"]?>" width='100%' height='700' MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=yes name=codebar  ></iframe>
<?php
	}
	print "</td></tr></table>";
}

if(isset($_POST["consultmembre"])) {
	$membre=$_POST["membre"];
?>
	<BR><BR><BR>
	<table border="1" cellpadding="3" cellspacing="1" width="100%"  height="85">
	<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Membre" ?> : <font id="color2"><B><?php print renvoiMembreFormatePersonne($membre) ?></font></font></td></tr>
	<tr><td>
	<iframe src="./codebarmembre.php?membre=<?php print $membre?>&codebase=<?php print $_POST["codebase"]?>" width='100%' height='700' MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=yes name=codebar  ></iframe>
	</td></tr></table>
<?php  } ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
