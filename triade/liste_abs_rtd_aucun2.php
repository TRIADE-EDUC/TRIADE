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
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
</head>
<body>
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr bgcolor="#666666">
<td height="2"> <b><font  color="#FFFFFF">List des relevés de non absence, ni retard du    
    <?php print $_POST["saisie_date_debut"]  ?> <?php print LANGABS19 ?> <?php print $_POST["saisie_date_fin"]  ?> </font></b></td>
</tr>
<tr bgcolor="#CCCCCC">
<td valign=top>
     <!-- // fin  -->
	<table border="1" bordercolor="#000000" width="100%"  style="border-collapse: collapse;" >
	<tr>
	<TD bgcolor=yellow width=20%><B>&nbsp;<?php print "Classe" ?></B></TD>
	<TD bgcolor=yellow width=20%><b>&nbsp;<?php print "Matière" ?></B></TD>
	<TD bgcolor=yellow width=20%><b>&nbsp;<?php print "Date - Heure" ?></b></TD>
	<TD bgcolor=yellow width=20%><b>&nbsp;<?php print "Enseignant" ?></b></TD>
	</tr>
<?php
	$data=recup_abs_rtd_aucun2($_POST["saisie_date_debut"],$_POST["saisie_date_fin"]); //id,classe,date,heure,matiere,enseignante
	for($i=0;$i<count($data);$i++) {
?>
	<tr>
	<TD bgcolor=#FFFFFF >&nbsp;<?php print $data[$i][1] ?></TD>
	<TD bgcolor=#FFFFFF >&nbsp;<?php print $data[$i][4] ?></TD>
	<TD bgcolor=#FFFFFF >&nbsp;<?php print dateForm($data[$i][2])." - ".$data[$i][3] ?></TD>
	<TD bgcolor=#FFFFFF >&nbsp;<?php print $data[$i][5] ?></TD>
	</tr>
	
<?php

	}	
Pgclose();
?>
</BODY></HTML>
