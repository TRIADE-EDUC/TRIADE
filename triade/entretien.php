<?php
session_start();
include_once("./common/config2.inc.php");
if ($_SESSION["adminplus"] != "suppreme") {
	if (defined("PASSMODULEINDIVIDUEL")) {
		if (PASSMODULEINDIVIDUEL == "oui") {
			header("Location:base_de_donne_key.php?key=passmoduleindividuel");
		}
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
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type='text/javascript' src="./librairie_php/server.php?client=Util,main,dispatcher,httpclient,request,json,loading,iframe"></script>
<script type='text/javascript' src="./librairie_php/auto_server.php?client=all&stub=livesearch"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<?php include("./librairie_php/googleanalyse.php"); ?>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if ($_SESSION["membre"] == "menupersonnel") {
	if (!verifDroit($_SESSION["id_pers"],"entretien")) {
		accesNonReserveFen();
		exit;
	}
}else{
	validerequete("2");
}
include_once("./librairie_php/ajax.php");
ajax_js();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print LANGMESS369 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<!-- // debut form  -->
<form method=post onsubmit="return valide_recherche_eleve()" name="formulaire">
<blockquote><BR>
<table border=0 cellspacing=0><tr><td style="padding-top:0px;" nowrap>
<font class="T2"><?php print LANGABS3?> : </font><input type="text" name="saisie_nom_eleve" size="20" id="search" autocomplete="off" onkeyup="searchRequest(this,'eleve','target','formulaire','saisie_nom_eleve')"   style="width:15em" />
</td></tr><tr><td style="padding-top:0px;"><div id="target" style="width:16em" ></div></td></tr>
</table><div style="position:relative">
<UL><UL><UL><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT39?>","create"); //text,nomInput</script></UL></UL></UL>
</div>
 </blockquote>
 <?php brmozilla($_SESSION["navigateur"]);?>
 <?php brmozilla($_SESSION["navigateur"]);?>
 </form>
 <!-- // fin form -->
 </td></tr></table>
<br /><br />
<?php
if (defined("PASSMODULEINDIVIDUEL")) {
	if (PASSMODULEINDIVIDUEL == "oui") {
		if (empty($_SESSION["adminplus"])) {
			print "<script>";
			print "location.href='./base_de_donne_key.php?key=passmoduleindividuel'";
			print "</script>";
		}
	}
}

//alertJs(empty($create));
// affichage de la liste d élèves trouvés
if(isset($_POST["saisie_nom_eleve"]))
{
$saisie_nom_eleve=trim($_POST["saisie_nom_eleve"]);
$motif=strtolower($saisie_nom_eleve);
$sql=<<<EOF

SELECT c.libelle,e.nom,e.prenom,e.elev_id
FROM ${prefixe}eleves e, ${prefixe}classes c
WHERE lower(e.nom) LIKE '%$motif%'
AND c.code_class = e.classe
ORDER BY c.libelle, e.nom, e.prenom

EOF;
$res=execSql($sql);
$data=chargeMat($res);

?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#CCCCCC" >
<tr id='coulBar0' ><td height="2" colspan=3><b><font   id='menumodule1'>
<?php print LANGRECH2?> : <font id="color2"><B><?php print ucwords(stripslashes($motif))?></font>
</font></td>
</tr>
<?php

if( count($data) <= 0 )
	{
	print("<tr><td align=center valign=center>".LANGRECH3."</td></tr>");
	}
else {
?>
<tr bgcolor="#FFFFFF"><td><b><?php print ucwords(LANGIMP10)?></b></td><td><B><?php print LANGIMP8?></B></td><td><B><?php print LANGIMP9?></B></td></tr>
<?php
for($i=0;$i<count($data);$i++)
	{
	?>
	<tr>
	<td bgcolor="#FFFFFF"><?php print $data[$i][0]?></td>
	<td bgcolor="#FFFFFF"><a style="text-decoration:underline" href="entretien2.php?eid=<?php print $data[$i][3]?>" onMouseOver="AffBulle('<font face=Verdana size=1><?php print "Accès journal d\'entretien"?></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><?php print strtoupper($data[$i][1])?></a></td>
	<td bgcolor="#FFFFFF"><?php print ucwords($data[$i][2])?></td>
	</tr>
	<?php
	}
}

?>
</table>
<script type="text/JavaScript">InitBulle('#000000','#CCCCFF','red',1);</script>
<br><br>
<?php
}
?>


<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGMESS370 ?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td>
<!-- // fin  -->
<form method="POST" onsubmit="return verifAccesFiche2()" name="formulaire2" action="entretien_classe.php">
<br />
<ul>
<font class=T2><?php print LANGPROFG ?> :</font>
<select name="sClasseGrp" size="1" >
<option id='select0' ><?php print LANGCHOIX?></option>
<?php select_classe(); // creation des options ?>
</select>
<br /><br /><br>
<UL><UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT31 ?>","rien"); //text,nomInput</script>
<br><br></UL></UL></UL></UL></UL>
</form>
<!-- // fin  -->
</td></tr></table>
<br><br>



<form method=post onsubmit="return valide_consul_classe1()" name="formulaire1">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print  LANGMESS371 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<blockquote><BR>
<font class=T2><?php print LANGPROFG?> :</font> <select id="saisie_classe" name="saisie_classe">
<option id='select0' ><?php print LANGCHOIX?></option>
<?php
select_classe(); // creation des options
?>
</select> <BR><br>
<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","consult");</script>
<?php brmozilla($_SESSION["navigateur"]);?>
<?php brmozilla($_SESSION["navigateur"]);?>
<?php 
$data=recupListPedago(); // p.nom,p.prenom,p.civ,t.heuredebut,t.heurefin,t.nomclasse
?>
</ul></ul></ul>
<br /><br /><br />

<table border='1' width='90%' style="border-collapse: collapse;" >
<tr>
<td bgcolor="yellow"><font class='T1'><?php print LANGMESS372 ?></font></td>
<td bgcolor="yellow"><font class='T1'><?php print LANGMESS373 ?></font></td>
</tr>
<?php 
for ($i=0;$i<=count($data);$i++) {
	$cumulHeure=cumulEntretienPedago($data[$i][6]);
	if (is_numeric($data[$i][2])) {
		print "<tr bgcolor='#FFFFFF'><td valign='top'><font class='T1'>&nbsp;".civ($data[$i][2])." ".$data[$i][1]."</font></td>";
		print "<td><font class='T1'>".$cumulHeure."</font></td></tr>";
	}
}
?>
</table>
<br /><br />
</td></tr></table>
<!-- // fin form -->
<?php
// affichage de la classe
if(isset($_POST["consult"])) { 
	$saisie_classe=$_POST["saisie_classe"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);
	$cl=$data[0][0];
?>
	<BR><BR><BR>
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" >
	<tr id='coulBar0' ><td height="2" colspan="3"><b><font   id='menumodule1' >
	<?php print LANGELE4?> : <font id="color2"><b><?php print $cl?></b></font>&nbsp;&nbsp; <?php print LANGCOM3 ?><font id="color2"><b><?php print count($data) ?></b></font></font></td>
	</tr>
<?php 
	if( count($data) <= 0 ) {
		print("<tr><td align=center valign=center id='cadreCentral0'><font class=T2>".LANGRECH1."</font></td></tr>");
	} else {
?>
		<tr ><td bgcolor="yellow" width="50%" >&nbsp;<B><?php print ucwords(LANGIMP8)?>&nbsp;<?php print ucwords(LANGIMP9)?></B></td>
		<td bgcolor="yellow" ><b><?php print ucwords("Cumul")?></b></td></tr>
<?php
		for($i=0;$i<count($data);$i++) {
			$eid=$data[$i][1];

	?>
	<tr  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
	<td><?php print strtoupper($data[$i][2])?> <?php print trunchaine(ucwords($data[$i][3]),30)?></td>
	<td>
	<?php
			$cumulHeure=cumulEntretien2($eid);
			$cumulTotal+=cumulEntretien22($eid,$cl);
			print $cumulHeure;
	?>

	</td>
	</tr>
	<?php
	}
	}
	print "<tr><td align='center' bgcolor='#FFFFF' colspan='2'> Soit : ".timeForm(convert_sec($cumulTotal))." entretien en $cl</td></tr>";
print "</table>";
}


?>
</form>


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
