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
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return verifcommun()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Planification d'un stage" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td valign=top>
<br>
<?php
include_once("./librairie_php/db_triade.php");
include_once("librairie_php/calendar.php");
$cnx=cnx();
$date=dateDMY();
if (isset($_POST["create"])) {
	if ($_POST["id"] != "") {
		$cr=modifDateStageCentral($_POST["periode1"],$_POST["periode2"],$_POST["nomdustage"],$_POST["id"]);
		if ($cr) {
			print "<center><font class='T2' id='color3' >Stage modifié</font></center><br>";
		}
	}else{
		$cr=creationDateStageCentral($_POST["periode1"],$_POST["periode2"],$_POST["nomdustage"]);
		if ($cr) {
			print "<center><font class='T2' id='color3' >Stage enregistré</font></center><br>";
		}
	}
}

if (isset($_GET["idmodif"])) { 
	$data=infoDateStageCentral($_GET["idmodif"]); 
	$id=$data[0][0];
	$datedebut=dateForm($data[0][1]);
	$datefin=dateForm($data[0][2]);
	$libelle=$data[0][3];
}

?>
<br>
<form method="post" name="formulaire" action='gestion_central_stage_planification.php' >
<table width="100%" border="0" align="center" >

<tr>
<td align="right" ><font class="T2"><?php print "Nom du stage " ?> :</font></td>
<td><input type=text name='nomdustage' size=32 class="bouton2" maxlength='50' value="<?php print $libelle ?>" /></td>
</tr>

<tr>
<td align="right" ><font class="T2"><?php print "Période demandée" ?> :</font></td>
<td><input type=text name='periode1' size=12 class="bouton2" onKeyPress="onlyChar(event)" value="<?php print $datedebut ?>" >
<?php
calendarDim("id2","document.formulaire.periode1",$_SESSION["langue"],"1");
?>
au <input type=text name='periode2' size=12 class="bouton2" onKeyPress="onlyChar(event)" value="<?php print $datefin ?>" >
<?php
calendarDim("id3","document.formulaire.periode2",$_SESSION["langue"],"1");
?>
</td>
</tr>
</table>

<ul><ul>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","create"); //text,nomInput</script>
<script language=JavaScript>buttonMagicRetour("gestion_central_stage.php","_parent"); //text,nomInput</script>
</ul></ul>
</ul></ul>
<BR><br>
</ul>
<input type='hidden' name='id' value="<?php print $id ?>" />
</form></table>

<!-- // fin  -->
<br><br>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Planification des stages" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td valign=top>
<table width='100%' border='1' style='border-collapse: collapse;' >
<tr>
<td bgcolor='yellow' align='center'><font class='T1'>Libelle</font></td>
<td bgcolor='yellow' align='center' width='5%'><font class='T1'>&nbsp;Date&nbsp;de&nbsp;début&nbsp;</font></td>
<td bgcolor='yellow' align='center' width='5%'><font class='T1'>&nbsp;Date&nbsp;de&nbsp;fin&nbsp;</font></td>
<td bgcolor='yellow' align='center' width='5%'><font class='T1'>&nbsp;Action&nbsp;</font></td>
</tr>
<?php 

if (isset($_GET["idsupp"])) { suppDateStageCentral($_GET["idsupp"]); }

$data=listeDateStageCentral();  //id,datedebut,datefin,nomstage
for ($i=0;$i<count($data);$i++) {
	print "<tr>";
	print "<td>&nbsp;".$data[$i][3]."</td>";
	print "<td>&nbsp;".dateForm($data[$i][1])."</td>";
	print "<td>&nbsp;".dateForm($data[$i][2])."</td>";
	print "<td>&nbsp;<a href='gestion_central_stage_planification.php?idmodif=".$data[$i][0]."'><img src='image/commun/editer.gif' border='0'/></a>&nbsp;";
	print "<a href='gestion_central_stage_planification.php?idsupp=".$data[$i][0]."'><img src='image/commun/trash.png' border='0'/></a>";
	print "&nbsp;</td>";
	print "</tr>";
}
?>

</table>
</td></tr></table>



<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION["membre"] == "menuadmin") :
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
<?php  Pgclose(); ?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
