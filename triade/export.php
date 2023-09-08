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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'.js'?>"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
<?php  $today= date ("j M, Y");  ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>
<?php top_h(); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'1.js'?>"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS234 ?>  </font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<br />
<ul>
<font class=T2><b><?php print LANGMESS235 ?></b></font>
<br />
<br />
<font class=T2>
<img src="./image/commun/on1.gif" width="8" height="8"> <a href="./export_eleve.php"><?php print ucfirst(INTITULEELEVE) ?> (format excel)</A> <br />
<br />
<img src="./image/commun/on1.gif" width="8" height="8"> <a href="./export_personnel.php"><?php print LANGMESS236 ?> (format excel)</A>&nbsp;&nbsp;<i>(Ens.,Vie scolaire,Dir.,etc...)</i> <br />
</ul>
<br>
<ul>
<font class=T2><b><?php print LANGMESS237 ?></b></font>
<br />
<br />
<?php
if (isset($_POST["savestructure"])) {

	$structure=stripslashes($_POST["structure"]);
	$nom_structure=$_POST["nom_structure"];
	$libelle="##struct##$nom_structure";

	$data=aff_enr_parametrage($libelle);
	if (count($data) > 0) { 
		print "<form method='post' action='export.php' >";
		print "<input type=hidden name='structure' value='$structure' />";
		print "<font class=T2 color='red' >".LANGTMESS431." : </font>";
		print "<input type=text name='nom_structure' value='$nom_structure' /> ";
		print "<input type=submit value='".VALIDE."' name='savestructure' class='bouton2' />";
		print "</form>";
	}else{
		enr_parametrage($libelle,$structure);
	}
}

if (isset($_GET['supp'])) { supp_parametrage('##struct##'.$_GET['supp']); }

print "<table width='90%'>";
$data=aff_structure("##struct##");
for($i=0;$i<count($data);$i++) {
	$libelle=$data[$i][0];
//	$structure=unserialize($data[$i][1]);
	$libelle=preg_replace('/##struct##/','',$libelle);
	print "<tr><td><img src='./image/commun/on1.gif' width='8' height='8'> ".LANGTMESS432." : <a href='export_eleve_3.php?libelle=$libelle' ><span id='disp$i'>$libelle</span></a></td><td><a href='./export.php?supp=$libelle' title=\"$libelle\" onmouseover=\"document.getElementById('disp$i').style.cssText='color:red;font-weight:bold;'\"  onmouseout=\"document.getElementById('disp$i').style.cssText='color:black;' \" ><img src='image/commun/trash.png' border='0' align='center'/></a></td></tr>";
}
?>


</table>
<br><br>
<!-- // fin  -->
</td></tr></table>
<BR>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'2.js'?>"> </SCRIPT>
<SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>
</BODY></HTML>
