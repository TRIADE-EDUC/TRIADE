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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGETUDE32 ?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td valign=top>
<!-- // fin  -->
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
validerequete("2");
$data=liste_etude_2($_GET["id"]);
//id,jour_semaine,heure,salle,pion,nom_etude,duree
?>
<table border="1" bordercolor="#000000" align="center" width="95%">
<?php
for($i=0;$i<count($data);$i++) {
	if (($data[$i][4] == "-1") || ($data[$i][4] == NULL )) {
		$pion="???";
	}else {
		$pion=$data[$i][4];
	}
?>
	<tr>
	<td bgcolor="#FFFFFF" bordercolor=#FFFFFF>
	<font class=T1>
	Etude : <b><?php print $data[$i][5] ?></b>
	En salle : <b><?php print $data[$i][3] ?></b> / Surveillant : <b><?php print $pion ?></b><br>
	Semaine : <b></b>
	Le : <b>
	<?php
	$liste=preg_replace('/\{/','',$data[$i][1]);
	$liste=preg_replace('/\}/','',$liste);
	$tab=explode(",", $liste);
	foreach($tab as $value) {
		print jourdesemaine($value).",";
	}

	?>
	<br>
	</b> à <b><?php print timeForm($data[$i][2]) ?></b> pendant <b>
	<?php
	if ($data[$i][6] == 0) {
		print "???";
	}else {
		print $data[$i][6];
	}
	?>
	</b>
	<br><br>
	</font>
	</td>
	</tr>


<?php
}
?>
</table>
<br><br>
<table width=95% bordercolor='#000000' border=1 align=center style="border-collapse: collapse;" >
<tr>
<td width=100  bgcolor=yellow><b>Nom</b></td>
<td width=100 bgcolor=yellow><b>Prénom</b></td>
<td width=50  bgcolor=yellow><b>Classe</b></td>
<td bgcolor=yellow><b>Commentaire</b></td><tr>
<?php
$data=liste_eleve_etude($_GET["id"]); //id_eleve,id_etude,information,auto_exit
for($i=0;$i<count($data);$i++) {
	print "<tr bgcolor='#FFFFFF' bordercolor='#FFFFFF'><td>".strtoupper(recherche_eleve_nom($data[$i][0]))."</td><td>".strtolower(recherche_eleve_prenom($data[$i][0]))."</td>";
?>
	<td ><?php $idclasse=chercheIdClasseDunEleve($data[$i][0]); $nomclasse=chercheClasse($idclasse); print $nomclasse[0][1]?> </td>
	<td><?php
		if (($data[$i][3] == 1) || ($data[$i][3] == "t")) {
			print "Autorisé à quitter cette étude.<br>";
		}
		 print  $data[$i][2]?></td>
</tr>
<?php } ?>

</table>
<br><br>
<table align=center><tr><td><script language=JavaScript>buttonMagicFermeture(); //bouton de fermeture</script></td></tr></table><br>
<!-- // fin  -->
</td></tr></table>
</BODY></HTML>