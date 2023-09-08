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
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr bgcolor="#666666"><td height="2"><b><font  color="#FFFFFF"> <?php print LANGDISC1?> <?php print $_GET["date"] ?> <?php print LANGTE11 ?> <?php print $_GET["dateFin"] ?></font></b></td></tr>
<tr bgcolor="#CCCCCC">
<td >
<table border="1" bordercolor="#000000" width="100%">
<tr>
<TR>
<TD width=5% bgcolor='yellow'><b>&nbsp;<?php print LANGELE4 ?>&nbsp;</b></TD>
<TD width=10% bgcolor='yellow'><b>&nbsp;<?php print LANGDISC11Ter ?>&nbsp;</b></TD>
<TD width=10% bgcolor='yellow'><b>&nbsp;<?php print "Date et ".ucwords(LANGDISC12) ?>&nbsp;</b></TD>
<TD width=5% bgcolor='yellow' align=center ><b>&nbsp;<?php print LANGDISC16 ?>&nbsp;</b></TD>
<TD width=25% align=center bgcolor='yellow'><b>&nbsp;<?php print LANGABS12 ?>&nbsp;</b></TD>
<TD width=15% align=center bgcolor='yellow'><b>&nbsp;<?php print "*".LANGDISC17 ?>.&nbsp;</b></TD>
</TR>
<?php
$data=recherche_retenue_du_jour_2bis($_GET["date"],$_GET["dateFin"],$_GET['tri']);
// id_elev,date_de_la_retenue,heure_de_la_retenue,date_de_saisie,origi_saisie,id_category,retenue_effectuer,motif,attribuer_par,signature_parent,duree_retenu
for($i=0;$i<count($data);$i++) {
	$classe=chercheClasse(chercheIdClasseDunEleve($data[$i][0]));
?>
	<TR>
	<td bgcolor='#FFFFFF' valign=top><?php print $classe[0][1]?></td>
	<td bgcolor='#FFFFFF' valign=top><?php print ucwords(recherche_eleve_nom($data[$i][0]))?> <?php print ucwords(recherche_eleve_prenom($data[$i][0]))?></td>
	<td bgcolor='#FFFFFF' valign=top>En retenu le <?php print dateForm($data[$i][1])?><br> à <?php print timeForm($data[$i][2])?><br>durant <?php print timeForm($data[$i][10])?> heure(s) </td>
	<td bgcolor='#FFFFFF' valign=top align=center><input type=checkbox 
		<?php
		if (($data[$i][6] == t ) || ($data[$i][6] == 1 )) {
			$checked="checked='checked'";
		} else {
			$checked=" ";
		} 
	print $checked ;
	$message1=$data[$i][7];
	$message2=$data[$i][11];
	$fait=$data[$i][12];
		?>
	></td>
	<td bgcolor='#FFFFFF' valign=top ><u><?php print LANGPARENT15."</u> : ".$message1 ?><br /><u>Description des faits</u> : <?php print $fait ?> <br /> <u>Devoir à faire</u> : <?php print $message2 ?>
</td>
	<td bgcolor='#FFFFFF' valign=top >
   	D:<b><?php print cherchetel($data[$i][0])?></B><BR>
        P:<b><?php print cherchetelpere($data[$i][0])?></b><BR>
   	M:<b><?php print cherchetelmere($data[$i][0])?></b><br>
	Portable 1 : <b><?php print cherchetelportable1($data[$i][0])?> </b><BR>
	Portable 2 : <b><?php print cherchetelportable2($data[$i][0])?> </b><BR>
	Email : <b><?php print cherchemail($data[$i][0])?> </b> <BR>
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
