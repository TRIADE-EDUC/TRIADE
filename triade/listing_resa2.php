<?php
session_start();
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if (($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"resaressource") == 0) ) {
	PgClose();
	header("Location: accespersonneldenied.php?titre=Module Gestion des ressources.");	
}
if ($_SESSION["membre"] != "menupersonnel") { validerequete("2"); }
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

$type=$_POST["type"];
if ($type == "salle") { $type="de salle"; $type2="Salle"; }
if ($type == "equip") { $type="d'équipement"; $type2="Equipement";  }
?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr bgcolor="#666666">
<td height="2"> <b><font  color="#FFFFFF">Listing des réservations <?php print $type ?>
   &nbsp;du  <?php print $_POST["saisie_date_debut"]  ?> au <?php print $_POST["saisie_date_fin"]  ?> </font></b></td>
</tr>
<tr bgcolor="#CCCCCC">
<td valign=top>
     <!-- // fin  -->
<?php
// affichage de la liste d élèves trouvées
$idclasse=$_POST["saisie_classe"];

$data=affResaEquip($_POST["type"],dateFormBase($_POST["saisie_date_debut"]),dateFormBase($_POST["saisie_date_fin"]));
// n.id,n.idmatos,n.idqui,n.quand,n.heure_depart,n.heure_fin,n.info,n.valider,m.type,m.id //  resa_matos m, resa_liste n 
if (count($data) <= 0) {
        print("<BR><center>"."Aucune réservation pour cette période"."<BR><BR></center>");
} else {
?>
	<table border="1" bordercolor="#000000" width="100%">
	<tr>
	<TD bgcolor=yellow width=20%>&nbsp;<B><?php print $type2 ?></B></TD>
	<TD bgcolor=yellow width=20%>&nbsp;<b>Réservé le</B></TD>
	<TD bgcolor=yellow width=20%>&nbsp;<b>Par</b></TD>
	<TD bgcolor=yellow  align=center >&nbsp;<B>Information</b></TD>
	<TD bgcolor=yellow width=5 align=center >&nbsp;<B>Accepté</b>&nbsp;</TD>

	<?php
	for($i=0;$i<count($data);$i++) {
		print "<tr>";
		print "<td bgcolor=#FFFFFF>&nbsp;".recherche_equip($data[$i][1])."</td>";
		print "<td bgcolor=#FFFFFF>&nbsp; ".dateForm($data[$i][3])." de ".$data[$i][4]." à ".$data[$i][5]."</td>";
		print "<td bgcolor=#FFFFFF>&nbsp; ".recherche_personne($data[$i][2])."</td>";
		print "<td bgcolor=#FFFFFF>&nbsp; ".$data[$i][6]."</td>";
		print "<td bgcolor=#FFFFFF align=center >&nbsp; "; if ($data[$i][7] == 1) { print "oui" ; }else{ print "???"; } ; print "</td>";
		print "</tr>";
	}
	print "</table>";
}
?>
<BR>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY></HTML>
