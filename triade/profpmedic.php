<?php
session_start();
include_once("./common/config2.inc.php");
if (!isset($_SESSION['adminplusprofp'])) {
	if (PASSMODULEMEDICAL == "oui") {
		header("Location:base_de_donne_key.php?base=medic&eid=".$_GET['eid']);
		exit;
	}
}

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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onLoad="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
verif_profp_eleve($_GET["eid"],$_SESSION["id_pers"],$_SESSION["membre"]);
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>

<?php
// affichage de l'élève (lecture seule)
$idEleve=$_GET["eid"];
if (isset($_POST["create"])) {
	$idEleve=$_POST["idEleve"];
	profPmed(date("d/m/Y"),$_POST["commentaire"],$_SESSION["nom"],$_POST["idEleve"]);
}
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPROFP21 ?>  <font id="color2"><?php print couperchaine(recherche_eleve($idEleve),27);?></font></B></font>
</td>
</tr><tr id='cadreCentral0' >
<td colspan=2>
<br>
<?php
if ($_SESSION["membre"] == "menuprof") {
?>
	&nbsp;&nbsp;<input type=button class=BUTTON value="<-- <?php print LANGPRECE ?>" onClick="open('profp3.php?eid=<?php print $_GET["eid"]?>','_parent','')"><br><br>
<?php
}
	if (((defined("INFOMEDIC2")) && (INFOMEDIC2 == "oui")) || ($_SESSION["membre"] == "menuadmin")) {
?>
<form method="post">
<table bordercolor="#CCCC00"  width=95% align=center border=0 bgcolor="#FFFFFF">
<tr>
<td width=50% align=right id='bordure'><font class="T2 shadow" >Information du : </td><td id='bordure'> <?php print date("d/m/Y")?></font></td>
</tr>
<tr>
<td  colspan=2 align=left  id='bordure'><font class='shadow'>Commentaire : </font><br><br>
<textarea name="commentaire" cols=90 rows=8 ></textarea>
</td>
</tr>
<tr>
<td  colspan=2 align=center id='bordure'><br><br>
<input type=hidden name=idEleve value="<?php print $idEleve?>" >
<script language=JavaScript>buttonMagicSubmit("Enregistrer Information","create"); //text,nomInput</script>
<br><br>
</td>
</tr>
</table>
</form>
<br /><br />
<table bordercolor="#CCCC00"  width=95% align=center border=1 bgcolor="#FFFFFF" >

<?php
if (isset($_GET["supp"])) {
	profPmedsupp($_GET["supp"]);
}



$data=profPmedAff($idEleve);
// id,date,idEleve,nomProf,commentaire
for($i=0;$i<count($data);$i++) {
?>
	<tr><td id="bordure2" ><br />&nbsp;&nbsp;<font class="T2 shadow" >
	Information du </font><b><?php print $data[$i][1]?></b> &nbsp;&nbsp;&nbsp;[<a href="profpmedic.php?supp=<?php print $data[$i][0]?>&eid=<?php print $idEleve?>" >supprimer</a>]
	<br><br>
	&nbsp;<?php print stripslashes(strip_tags($data[$i][4]))?>

	<br>
	<div align=right>De : <?php print $data[$i][3]?> &nbsp;&nbsp;</div>
	<br />
	</td>
	</tr>
<?php
}
?>


</table>
<br /><br />
<?php
}
?>


</td></tr></table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION["membre"] == "menuadmin") :
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
