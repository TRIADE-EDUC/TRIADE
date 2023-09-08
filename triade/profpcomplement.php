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
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
if ($_SESSION["membre"] != "menuadmin") {
	verif_profp_eleve($_GET['eid'],$_SESSION["id_pers"],$_SESSION["membre"]);
}
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
	$cr=profPinfo($_POST["dateDebut"],$_POST["dateFin"],$_POST["commentaire"],$_SESSION["nom"],$_POST["idEleve"]);
	if($cr){
	      history_cmd($_SESSION["nom"],"Prof P.","enseignant");
	       //alertJs("Nouveau compte créé -- Service Triade");
	} else {
               error(0);
    }
}
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><B><?php print LANGPROFP5 ?> <font color=red><?php print recherche_eleve($idEleve);?></font></B></font></td></tr>
<tr id='cadreCentral0' >
<?php
if ($_SESSION["membre"] == "menuadmin") {
	$fichier="ficheeleve3.php?eid=";
}else{
	$fichier="profp3.php?eid=";
}
?>
<td colspan=2><br>&nbsp;&nbsp;<input type=button class=BUTTON value="<-- <?php print LANGPRECE ?>" onclick="open('<?php print $fichier.$_GET["eid"]?>','_parent','')"><br><br>
<form method=post onsubmit="return valideProfP()" name=formulaire>
<table bordercolor="#CCCC00"  width=60% align=center border=0 bgcolor="#FFFFFF">
<tr>
<td width=50% align=right  id='bordure'><font class='T2'><?php print LANGPROFP6 ?> :</font> </td>
<td id='bordure' ><input type=text name=dateDebut value="<?php print dateDMY()?>"  size=12 class=bouton2 readonly ></td>
</tr>
<tr>
<td  align=right id='bordure' ><font class='T2'><?php print LANGPROFP7 ?> :</font> </td>
<td  id='bordure'><input type=text name=dateFin size=12 readonly class=bouton2>
	<?php
	include_once("librairie_php/calendar.php");
	calendar('id1','document.formulaire.dateFin',$_SESSION["langue"],"0");
	?>
</td>
</tr>
<tr>
<td  colspan=2 align=left  id='bordure'><font class='T2'><?php print LANGASS27 ?> :</font> <br>
<textarea name="commentaire" cols=100% rows=8 ></textarea>
</td>
</tr>
<tr>
<td  colspan=2 align=center id='bordure'>
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
	profPsupp($_GET["supp"]);
}



$data=profPinfoAff($idEleve);
// id,dateDebut,dateFin,idEleve,commentaire,nomProf
for($i=0;$i<count($data);$i++) {
?>
	<tr><td id=bordure2 ><br />&nbsp;&nbsp;
	<?php print LANGPROFP6 ?><b><?php print dateForm($data[$i][1])?></b> <?php print LANGTE11 ?> <b><?php print dateForm($data[$i][2])?></b> &nbsp;&nbsp;&nbsp;[<a href="profpcomplement.php?supp=<?php print $data[$i][0]?>&eid=<?php print $idEleve?>" ><?php print LANGBT50 ?></a>]
	<br><br>
	&nbsp;<?php print $data[$i][4]?>

	<br>
	<div align=right><?php print ucwords(LANGABS34) ?> : <?php print $data[$i][5]?> &nbsp;&nbsp;</div>
	<br />
	</td>
	</tr>
<?php
}
?>

</table>
<br /><br />




</td></tr></table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION[membre] == "menuadmin") :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
print "</SCRIPT>";
else :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
print "</SCRIPT>";
top_d();
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
print "</SCRIPT>";
endif ;
?>

<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
