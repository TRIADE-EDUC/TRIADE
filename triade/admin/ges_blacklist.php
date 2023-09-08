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
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Gestion Black-List</font></b></td></tr>
<tr id='cadreCentral0' >
<td valign=top>
<img src="../image/commun/security.gif" align="left" />
&nbsp;&nbsp;<i>Voici la liste des personnes qui ont tenté d'accéder à un service non autorisé.
<b>Tentative de piratage !!</b> (Le ou les comptes ci-dessous sont bloqués.) </i> <br><br>
<!-- // debut de la saisie -->
<table width=100% bgcolor="#FFFFFF" border=1 bordercolor="#000000"  cellpadding=1 cellspacing=1 >
<?php
$cnx=cnx();

if (isset($_GET["supp"])) {
	blacklistsupp($_GET["supp"]);
}

// id,nom,prenom,date,ip,nb_tentative
$data=listeblacklistetotal();
//id,nom,prenom,date,ip,nb_tentative,fichier,membre
for($i=0;$i<count($data);$i++) {

	if ($data[$i][7] == "menuprof") { $membre="Compte Enseignant"; }
	if ($data[$i][7] == "menuscolaire") { $membre="Compte Vie scolaire"; }
	if ($data[$i][7] == "menueleve") { $membre="Compte Elève"; }
	if ($data[$i][7] == "menuadmin") { $membre="Compte Direction"; }
	if ($data[$i][7] == "menuparent") { $membre="Compte Parent"; }
	
print "<tr bordercolor='#FFFFFF' class=\"tabnormal\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\">";
print "<td>&nbsp;<font class=T1 color=red>".strtoupper($data[$i][1])."";
print "&nbsp;".ucwords($data[$i][2])."</font> ($membre) <br> ";
print "&nbsp;".$data[$i][5]." tentative(s) - ";
print "&nbsp; dernière tentative le ".dateForm($data[$i][3])."<br>";
print "&nbsp; IP : ".$data[$i][4]." - Tentative d'accès via le fichier :<br><i>&nbsp;".$data[$i][6]."</i><br>";
print "&nbsp;<input type=button value='supprimer' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;' onclick=\"open('ges_blacklist.php?supp=".$data[$i][0]."','_parent','')\" >";
print "</td></tr>";
}
?>
</table>
<!-- // fin de la saisie -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
