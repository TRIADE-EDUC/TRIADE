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
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_stage.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1'><?php print LANGSTAGE49 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
validerequete("2");

if (isset($_GET["tri"])) {
	$data=listestageTri($_GET["tri"]);
	if (preg_match('/^idclasse/',$_GET["tri"])) { $imgClass="<img src='image/commun/za.png' border='0' />";	}
	if (preg_match('/^numstage/',$_GET["tri"])) { $imgNum="<img src='image/commun/za.png' border='0' />";	}
	if (preg_match('/^datedebut/',$_GET["tri"])) { $imgDate="<img src='image/commun/za.png' border='0' />";	}


}else{
	$imgClass="<img src='image/commun/za.png' border='0' />";
	$data=listestage();
}

print "<table width=100% border=1 bordercolor='#000000' >";
print "<tr bgcolor='yellow'  >";
print "<td ><font class=T2>&nbsp;<a href='gestion_stage_date_modif.php?tri=idclasse,numstage'>".LANGELE4."</a>&nbsp; $imgClass</font></td>";
print "<td width=60><font class=T2>&nbsp;<a href='gestion_stage_date_modif.php?tri=numstage'>".LANGSTAGE50."</a>&nbsp;$imgNum</font></td>";
print "<td> <font class=T2>&nbsp;<a href='gestion_stage_date_modif.php?tri=datedebut'>".LANGSTAGE51."</a>&nbsp; $imgDate </font></td>";
print "<td align=center width='1%'><font class=T2>&nbsp;".LANGPER30."&nbsp;</font></td>";
print "</tr>";
// idclasse,datedebut,datefin,numstage
for($i=0;$i<count($data);$i++) {
	print "<tr class='tabnormal' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\">";
	print "<td id=bordure><font class=T1>".trunchaine(chercheClasse_nom($data[$i][0]),15)."</font></td>";
	print "<td align=center id=bordure ><font class=T1>".$data[$i][3]."</font></td>";
	print "<td id=bordure ><font class=T1 id=bordure>".dateForm($data[$i][1])." au ".dateForm($data[$i][2])."</font></td>";
	print "<td width=5 id=bordure align='center' ><input type=button value='".strtolower(LANGPER30)."' onclick=\"open('gestion_stage_date_modif2.php?id=".$data[$i][4]."','_parent','');\"  STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'></td></tr>";

}
print "</table>";

?>


<!-- // fin  -->
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
// deconnexion en fin de fichier
	Pgclose();
?>
</BODY></HTML>
