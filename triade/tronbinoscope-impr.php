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
<meta name="Copyright" content="Triade�, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Trombinoscope</title>
</head>
<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php
// connexion
include("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();

// nom classe
$saisie_classe=$_GET["idclasse"];
$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);

// ne fonctionne que si au moins 1 �l�ve dans la classe
// nom classe
$cl=$data[0][0];

?>
<center>
<br />
<font class=T2><?php print LANGELE4 ?> : <strong><?php print $cl?></strong></font> </td>
</center>
<br /><br />
<table border="0" width="100%" align=center>
<?php
if( count($data) <= 0 ) {
	print("<tr><td align=center valign=center>".LANGRECH1."</td></tr>");
}else {
?>
<tr>
<?php
$nbp=1;
for($i=0;$i<count($data);$i++) {
?>

	<td align=center valign=bottom>
		<img src="image_trombi.php?idE=<?php print $data[$i][1]?>"<br><br>
		<?php print strtoupper($data[$i][2])?>
		<?php print ucwords($data[$i][3])?>

	</td>
<?php

	if ($nbp == 5) { print "</tr><tr><td colspan=5>&nbsp;</td></tr><tr><td colspan=5>&nbsp;</td></tr>"; $nbp=0; }
	$nbp++;
}
print "</tr></table>";
}
?>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
<script type='text/javascript'>window.print();</script>
</BODY>
</HTML>
