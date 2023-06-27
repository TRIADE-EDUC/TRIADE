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
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
</head>
<body>
<?php 
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr bgcolor="#666666"><td height="2"><b><font  color="#FFFFFF"><?php print LANGPARENT15bis." ".$_GET["debut"]." au ".$_GET["fin"]?></font></b></td></tr>
<tr bgcolor="#CCCCCC">
<td >
<table border="1" bordercolor="#000000" width="100%">
<tr>
<TD  bgcolor='yellow' ><b>&nbsp;<?php print LANGNA1 ?>&nbsp;&nbsp;<?php print LANGNA2 ?>&nbsp;</b></TD>
<TD width=5%  bgcolor='yellow'><b>&nbsp;<?php print LANGELE4 ?>&nbsp;</b></TD>
<TD width=30  bgcolor='yellow'><b>&nbsp;<?php print "Attribué&nbsp;par" ?>&nbsp;</b></TD>
<TD width=25%  bgcolor='yellow' align=center><b>&nbsp;<?php print LANGABS12." / Devoir " ?>&nbsp;</b></TD>
<TD width=15%  bgcolor='yellow' align=center><b>&nbsp;<?php print "*".LANGDISC17 ?>.&nbsp;</b></TD>
<?php
//$data=recherche_sanction_du_jour();
$date=$_GET["debut"];
$dateFin=$_GET["fin"];
$tri=$_GET["tri"];
$data=recherche_sanction_du_jour_2bis($date,$dateFin,$tri);
// id,id_eleve,motif,id_category,date_saisie,origin_saisie,enr_en_retenue,signature_parent,attribuer_par,devoir_a_faire,description_fait
for($i=0;$i<count($data);$i++) {
	$classe=chercheClasse(chercheIdClasseDunEleve($data[$i][1]));
	$fait=$data[$i][10];
?>
	<TR>
	<td bgcolor='#FFFFFF' valign=top  width=15%><?php print ucwords(recherche_eleve_nom($data[$i][1]))?> <?php print ucwords(recherche_eleve_prenom($data[$i][1]))?></td>
	<td bgcolor='#FFFFFF' valign=top  width=15%><?php print $classe[0][1]?></td>
	<td bgcolor='#FFFFFF' valign=top width=15% ><?php print $data[$i][8] ?></td>
	<td bgcolor='#FFFFFF' valign=top>
					 <u>Catégorie</u> : <?php print rechercheCategory($data[$i][3]) ?> <br> 
					 <u>Sanction</u> : <?php print $data[$i][2]?> <br> 
					 <u>Description des faits</u> : <?php print $fait?> <br> 
					 <u>Devoir à faire</u> : <?php print $data[$i][9]?> </td>
	<td bgcolor='#FFFFFF' valign=top >
   	D:<b><?php print cherchetel($data[$i][1])?></B><BR>
    	P:<b><?php print cherchetelpere($data[$i][1])?></b><BR>
	M:<b><?php print cherchetelmere($data[$i][1])?></b><BR>
	Portable 1 : <b><?php print cherchetelportable1($data[$i][1])?> </b><BR>
	Portable 2 : <b><?php print cherchetelportable2($data[$i][1])?> </b><BR>
	Email : <b><?php print cherchemail($data[$i][1])?> </b> <BR>
	</td>
	</TR>
<?php
}
print "</table>";
?>
<BR><BR>
<?php
print LANGDISC15;
// deconnexion en fin de fichier
Pgclose();
?>
<script language=Javascript>
window.print();
</script>
</BODY></HTML>
