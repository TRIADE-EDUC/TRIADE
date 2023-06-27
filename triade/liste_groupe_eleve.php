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
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Groupe </title>
</head>
<body id='bodyfond2'>
<?php include("./librairie_php/lib_licence.php"); ?>

<center>
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();


$gid=$_GET["gid"];

$sql="SELECT libelle,liste_elev FROM ${prefixe}groupes WHERE group_id='$gid'";
$res=execSql($sql);
$data=chargeMat($res);
$nomgrp=$data[0][0];
$liste_eleves=preg_replace('/\{/',"",$data[0][1]);
$liste_eleves=preg_replace('/\}/',"",$liste_eleves);

$sql=<<<EOF
SELECT nom,prenom,libelle FROM ${prefixe}eleves, ${prefixe}classes where classe=code_class AND elev_id IN ($liste_eleves) ORDER BY nom 
EOF;
$res=execSql($sql);
$data=chargeMat($res);
?>

<font class=T2>
<?php print LANGGRP23?> <font color="red"><?php print $nomgrp?></font></p>
</font>
<table border="1" width=80% bordercolor="#000000" style="border-collapse: collapse;" >
<TR>
<TD bgcolor="#FFFFFF" ><B><?php print LANGEL1?></B></TD>
<TD bgcolor="#FFFFFF" ><B><?php print LANGEL2?></B></TD>
<TD bgcolor="#FFFFFF" align=center><B><?php print LANGEL3?></B></TD>
</tr>
<?php
// debut for
for($i=0;$i<count($data);$i++) {
?>
<tr>
	<td bgcolor="#FFFFFF"><?php print ucwords($data[$i][0])?></td>
	<td bgcolor="#FFFFFF"><?php print ucwords($data[$i][1])?></td>
	<td align=center bgcolor="#FFFFFF"><?php print $data[$i][2]?></td>
</tr>
<?php
} // fin for
?>
</table>
<BR><BR>
<table align=center><tr><td><script language=JavaScript>buttonMagicFermeture()</script></td></tr></table>
</center>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</body>
</HTML>
