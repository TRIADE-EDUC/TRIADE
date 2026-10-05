<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  - 
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
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_stage.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1'><?php print LANGSTAGE49 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();
validerequete("2");
if (isset($_GET["id"])) {
	if ($_GET["id"] == "tous") {
		$cr=stage_date_supp_tous();
		if($cr == 1){
			history_cmd($_SESSION["nom"],"SUPPRESSION","des dates de stage");
		}
	}else{
		$cr=stage_date_supp($_GET["id"]);
		if($cr == 1){
			history_cmd($_SESSION["nom"],"SUPPRESSION","date de stage");
			alertJs(LANGSTAGE57);
		}else{
			alertJs("Suppression Impossible \\n \\n Cette référence de date est actuellement affecté à un ou plusieurs élèves.");
		}
	}
}
$data=listestage();
print "<br>&nbsp;&nbsp;<font class='T2'>Suppression de toutes les dates non affectées : <input type=button value='".LANGBT50."' onclick=\"open('gestion_stage_date_supp.php?id=tous','_parent','');\" STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;' /></font><br><br>";

print "<table class='cc-data-table'>";
print "<thead><tr class='cc-thead-row'>";
print "<th class='cc-th'>".LANGELE4."</th>";
print "<th class='cc-th' style='width:5%'>".LANGSTAGE50." N°</th>";
print "<th class='cc-th'>".LANGSTAGE51."</th>";
print "<th class='cc-th cc-th-center'>".LANGBT50."</th>";
print "</tr></thead><tbody>";
// idclasse,datedebut,datefin,numstage,id
for($i=0;$i<countTriade($data);$i++) {
	print "<tr class='cc-tr-data'>";
	$nomclasse=chercheClasse_nom($data[$i][0]);
	print "<td id=bordure align=left><font class=T1>".trunchaine($nomclasse,15)."</font></td>";
	print "<td id=bordure align=center><font class=T1>".$data[$i][3]."</font></td>";
	print "<td id=bordure ><font class=T1>".dateForm($data[$i][1])." au ".dateForm($data[$i][2])."</font></td>";
	print "<td id=bordure width=5><input type=button value='".LANGBT50."' onclick=\"open('gestion_stage_date_supp.php?id=".$data[$i][4]."','_parent','');\" STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'></td>";
	print "</tr>";
}
print "</tbody></table>";
?>
<!-- // fin  -->
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
// deconnexion en fin de fichier
	Pgclose();
?>
</BODY></HTML>
